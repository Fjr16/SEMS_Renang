<?php

namespace App\Http\Controllers;

use App\Enums\CompetitionResultStatus;
use App\Enums\CompetitionTeamEntryStatus;
use App\Enums\CompetitionTeamStatus;
use App\Enums\RecordTypeEnum;
use App\Enums\RoundTypeEnum;
use App\Models\Competition;
use App\Models\CompetitionEvent;
use App\Models\CompetitionHeat;
use App\Models\CompetitionHeatLane;
use App\Models\CompetitionResult;
use App\Models\EventResult;
use App\Models\EventRoundConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class CompetitionHeatLaneController extends Controller
{
    public function partialReload(Competition $competition, Request $r){
        $event_id = $r->input('event_id');
        $event = !$event_id
        ? $competition->events->first()
        : CompetitionEvent::find($event_id);

        if ($event) {
            $event->load(['heats.heatLanes.entry.athlete.club']);
        }

        $selectEvents = $competition?->events;
        $totalLanes = $event?->competitionSession?->pool->total_lanes;
        $totalEntries = $event?->entries()
                        ->where('status', CompetitionTeamEntryStatus::Active->value)
                        ->whereHas('competitionTeam', function($row){
                            return $row->where('status', CompetitionTeamStatus::Active->value);
                        })
                        ->count();

        // Group heats by round_type
        $heatsByRound = $event?->heats?->groupBy('round_type');
        $roundConfig = EventRoundConfig::select('competition_event_id', 'round_type', 'used_lanes', 'qualify_count')
        ->where('competition_event_id', $event?->id)
        ->orderBy('order')
        ->get()
        ->keyBy('round_type');

        return view('pages.competition.tabs.heats', compact(
            'competition',
            'event',
            'selectEvents',
            'totalLanes',
            'totalEntries',
            'heatsByRound',
            'roundConfig'
        ));
    }
    public function generate(Competition $competition, Request $request)
    {
        $request->validate([
            'event_id'       => 'required|exists:competition_events,id',
            'rounds'         => 'required|array',
            'rounds.*.type'  => ['required', new Enum(RoundTypeEnum::class)],
            'rounds.*.lanes' => 'required|integer|min:1',
            'rounds.*.lolos' => 'nullable|integer|min:1',
        ]);

        $event      = CompetitionEvent::findOrFail($request->event_id);
        $totalLanes = $event->competitionSession->pool->total_lanes ?? 8;

        // Ambil semua entry aktif, sort by seed_time (NT paling belakang)
        $entries = $event->entries()
            ->where('status', CompetitionTeamEntryStatus::Active->value)
            ->whereHas('competitionTeam', fn($q) => $q->where('status', CompetitionTeamStatus::Active->value))
            ->get();

        if ($entries->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Data entri atlet tidak ditemukan'
            ]);
        };

        try {
                //code...
            $orderedEntry = $entries->sortBy(fn ($e) => $this->swimTimeToCs($e->seed_time))->values();

            // Generate hanya untuk ronde pertama (penyisihan/final)
            // Ronde berikutnya diisi via "Promosi Atlet"
            $firstRound = $request->rounds[0];
            $usedLanes  = min($firstRound['lanes'], $totalLanes);

            $activeLanes = $this->getActiveLanes($usedLanes, $totalLanes);
            $laneOrder   = $this->getCircleSeedOrder($activeLanes);

            // Distribute atlet — terkencang di heat terakhir
            $chunks = $orderedEntry->chunk($usedLanes)->values();

            $lastHeatChunk = $chunks->last();
            $secondLastIndex = $chunks->count() - 2;
            // $threshold = (int) ceil($usedLanes / 2);
            $threshold = 3;

            if($lastHeatChunk->count() < $threshold && $chunks->count() > 1){
                $secondLastChunk = $chunks->get($secondLastIndex);

                if($secondLastChunk){
                    $combined = $secondLastChunk->merge($lastHeatChunk);
                    $half = (int) ($combined->count() - 3);

                    $newSecondLast = $combined->slice(0,$half)->values();
                    $newLast = $combined->slice($half)->values();

                    $lastIndex = $chunks->count()-1;

                    $chunks = $chunks->map(function($chunk, $index) use ($newSecondLast, $newLast, $secondLastIndex, $lastIndex){
                        if($index === $secondLastIndex) return $newSecondLast;
                        if($index === $lastIndex) return $newLast;
                        return $chunk;
                    })->values();
                }
            }

            DB::beginTransaction();
            // Hapus heats lama jika ada
            foreach ($event->heats as $heat) {
                $heat->heatLanes()->delete();
            }
            $event->heats()->delete();
            // end Hapus heats lama jika ada

            foreach ($chunks as $heatIndex => $chunk) {
                $heat = CompetitionHeat::create([
                    'competition_event_id' => $event->id,
                    'heat_number'          => $chunks->count() - $heatIndex,
                    'round_type'           => $firstRound['type'],
                ]);

                foreach ($chunk->values() as $lane => $entry) {
                    CompetitionHeatLane::create([
                        'competition_heat_id'  => $heat->id,
                        'competition_entry_id' => $entry->id,
                        'lane_number'          => $laneOrder[$lane],
                        'lane_order'           => $lane + 1,
                        'seed_time'            => $entry->seed_time,
                    ]);
                }
            }

            // Simpan konfigurasi round berikutnya ke db
            // agar saat "Promosi Atlet" diklik, sistem tahu konfigurasinya
            $incomingTypes = collect($request->rounds)->pluck('type');
            EventRoundConfig::where('competition_event_id', $event->id)
                ->whereNotIn('round_type', $incomingTypes)
                ->delete();

            foreach ($request->rounds as $orderNumber => $round) {
                $usedLaneBaseRound = min($round['lanes'], $totalLanes);
                EventRoundConfig::updateOrCreate(
                    [
                        'competition_event_id' => $event->id,
                        'round_type' => $round['type'],
                    ],
                    [
                        'used_lanes' => $usedLaneBaseRound,
                        'qualify_count' => $round['lolos'] ?? null,
                        'order' => $orderNumber+1
                    ]
                );
            }
            // end Simpan konfigurasi round berikutnya ke db

            DB::commit();

            return response()->json([
                'status' => true,
                'messsage' => 'Sukses generate seri'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'messsage' => substr($th->getMessage(),0,150)
            ]);
        }
    }

    private function getActiveLanes(int $used, int $total): array
    {
        $start = (int) floor(($total - $used) / 2) + 1;
        return range($start, $start + $used - 1);
    }

    // untuk generate per heat saja
    private function getCircleSeedOrder(array $activeLanes): array
    {
        $sorted = $activeLanes;
        sort($sorted);
        $total  = count($sorted);
        $order  = [];
        $l      = (int) floor(($total - 1) / 2);
        $r      = (int) floor($total / 2);

        if ($total % 2 !== 0) {
            $order[] = $sorted[$l];
            $l--;
            $r++;
            while ($l >= 0 || $r < $total) {
                if ($r < $total)  $order[] = $sorted[$r++];
                if ($l >= 0)      $order[] = $sorted[$l--];
            }
        } else {
            while ($l >= 0 || $r < $total) {
                if ($l >= 0)      $order[] = $sorted[$l--];
                if ($r < $total)  $order[] = $sorted[$r++];
            }
        }

        return $order;
    }

    public function resetByEvent(Competition $competition, Request $request)
    {
        try {
            $request->validate([
                'event_id' => 'required|exists:competition_events,id',
            ]);

            $event = CompetitionEvent::findOrFail($request->event_id);

            if(!$event || $event->heats->isEmpty()){
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal Reset Seri, tidak ada seri yang ditemukan'
                ]);
            }

            // Hapus heats lama jika ada
            foreach ($event->heats as $heat) {
                $heat->heatLanes()->delete();
            }
            $event->heats()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil reset seri'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(),0,150)
            ]);
        }
    }

    public function saveResult(Request $req, Competition $comptetition){
        $validators = Validator::make($req->all(), [
            '*.lane_id' => 'required|exists:competition_heat_lanes,id',
            '*.swim_time' => ['nullable', 'regex:/^\d{2}:\d{2}\.\d{2}$/'],
            '*.status' => ['required', new Enum(CompetitionResultStatus::class)],
            '*.rank_heat' => 'nullable',
            '*.record_types' => 'nullable|array',
            '*.record_types.*' => ['nullable', new Enum(RecordTypeEnum::class)]
        ], [
            '*.swim_time.regex' => 'Format waktu renang / waktu finish tidak sesuai'
        ]);

        if($validators->fails()){
            return response()->json([
                'status' => false,
                'message' => $validators->errors()->first()
            ]);
        }

        try {
            DB::beginTransaction();
            foreach ($req->all() as $key => $row) {
                $item = CompetitionHeatLane::find($row['lane_id']);
                $item->swim_time = $row['swim_time'] ?? null;
                $item->status = $row['status'];
                $item->rank_in_heat = $row['rank_heat'] ?? null;
                $item->record_type = !empty($row['record_types']) ? implode(',' , array_filter($row['record_types'])) : null;
                $item->save();

                // input juga ke event result sebagai tabel rekap akhir per event
                if($row->last()){
                    $payload = [
                        'competition_event_id' => $item->heat->event->id,
                        'round_type' => $item->heat->round_type,
                    ];
                    $this->updateEventResult($payload);
                }
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil input hasil'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150)
            ]);
        }

    }
    public function promoteAthletes(Request $req, Competition $comptetition){
        $validators = Validator::make($req->all(), [
            'competition_event_id' => 'required|exists:competition_events,id',
            'round_type' => ['required', new Enum(RoundTypeEnum::class)],
        ]);

        if($validators->fails()){
            return response()->json([
                'status' => false,
                'message' => $validators->errors()->first()
            ]);
        }

        try {
            $roundType = $req->round_type;
            $event = CompetitionEvent::with(['configs','heats'])
                    ->findOrFail($req->competition_event_id);
            $totalLanes = $event->competitionSession->pool->total_lanes ?? 8;

            $roundDest = $event->configs
                        ->where('round_type', $roundType)
                        ->first();

            if (!$roundDest) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Konfigurasi ronde tujuan tidak ditemukan.',
                ]);
            }

            $roundBefore = $event->configs
                        ->where('order', (int) $roundDest->order - 1)
                        ->first();

            if (!$roundBefore) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Konfigurasi ronde sebelumnya tidak ditemukan.',
                ]);
            }

            $rbType = $roundBefore->round_type;
            $rbLolos = $roundBefore->qualify_count;

            if(!$this->checkResults($req->competition_event_id, $rbType)){
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal, karena hasil ronde sebelumnya belum lengkap'
                ]);
            }

            $heatIds = $event->heats
                        ->where('round_type', $rbType)
                        ->pluck('id');

            $results = CompetitionHeatLane::whereIn('competition_heat_id', $heatIds)
            ->where('status', CompetitionResultStatus::valid->value)
            ->whereNotNull('swim_time')
            ->get();

            if ($results->isEmpty()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Tidak ada hasil valid dari ronde sebelumnya.',
                ]);
            }

            $ranked = $results
                        ->sortBy(fn ($e) => $this->swimTimeToCs($e->swim_time))
                        ->values();

            if ($rbLolos === null) { // Tidak ada kuota → semua atlet valid lolos
                $qualifiers = $ranked;
            } else {
                $cutoffLane = $ranked->get($rbLolos - 1);

                if (!$cutoffLane) { // Jumlah atlet valid < kuota → semua lolos
                    $qualifiers = $ranked;
                } else {
                    $cutoffCs = $this->swimTimeToCs($cutoffLane->swim_time);
                    // Loloskan semua atlet dengan waktu <= cutoff (tie ikut lolos)
                    $qualifiers = $ranked
                                    ->filter(fn($lane) => $this->swimTimeToCs($lane->swim_time) <= $cutoffCs)
                                    ->values();
                }
            }

            if ($qualifiers->isEmpty()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Tidak ada atlet yang memenuhi syarat untuk dipromosikan.',
                ]);
            }

            $usedLanes = min(($roundDest->used_lanes ?? $totalLanes), $totalLanes);
            $totalHeats  = (int) ceil($qualifiers->count() / $usedLanes);
            // $rdLolos = $roundDest->qualify_count;

            // ── Distribusi zigzag ke heat (standar FINA) ──────────────────
            $heats = $this->distributeZigzag($qualifiers->all(), $totalHeats);

            // ── Lane assignment (circle seeding, sama seperti prelim) ─────
            $activeLanes = $this->getActiveLanes($usedLanes, $totalLanes);
            $laneOrder   = $this->getCircleSeedOrder($activeLanes);

            // ── Hapus heat lama di ronde tujuan jika ada ──────────────────
            $existingHeats = $event->heats->where('round_type', $roundType);
            foreach ($existingHeats as $heat) {
                $heat->heatLanes()->delete();
            }
            $event->heats()->where('round_type', $roundType)->delete();

            DB::beginTransaction();
            foreach ($heats as $heatNumber => $lanes) {
                $heat = CompetitionHeat::create([
                    'competition_event_id' => $event->id,
                    'heat_number'          => $heatNumber,
                    'round_type'           => $roundType,
                ]);

                $lanesSorted = collect($lanes)
                                ->sortBy(fn($lane) => $this->swimTimeToCs($lane->swim_time))
                                ->values();

                foreach ($lanesSorted as $laneIdx => $prevLane) {
                    CompetitionHeatLane::create([
                        'competition_heat_id'  => $heat->id,
                        'competition_entry_id' => $prevLane->competition_entry_id,
                        'lane_number'          => $laneOrder[$laneIdx] ?? ($laneIdx + 1),
                        'lane_order'           => $laneIdx + 1,
                        // swim_time ronde sebelumnya jadi seed_time ronde berikutnya
                        // 'seed_time'            => $prevLane->swim_time,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Berhasil promosi atlet'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150)
            ]);
        }

    }

    private function checkResults($event_id, $round){
        $isValid = CompetitionHeat::where('competition_event_id', $event_id)
        ->where('round_type', $round)
        ->whereHas('heatLanes', function($q){
            $q->where(function($qq) {
                $qq->where('status', 'valid')
                ->whereNull('swim_time');
            })
            ->orWhere(function($qqq) {
                $qqq->whereNotIn('status', ['dq', 'dnf', 'dns', 'valid'])
                    ->orWhereNull('status');
            });
        })
        ->count();

        return $isValid === 0 ? true : false;
    }

    private function swimTimeToCs(?string $time): int{
        // detik ke centi detik
        if (!$time) return PHP_INT_MAX;

        if (str_contains($time, ':')) {
            [$min, $rest] = explode(':', $time, 2);
            [$sec, $cs]   = array_pad(explode('.', $rest, 2), 2, '0');
        } else {
            [$sec, $cs]   = array_pad(explode('.', $time, 2), 2, '0');
            $min          = 0;
        }

        $cs = substr(str_pad($cs, 2, '0'), 0, 2);

        return ((int)$min * 60 * 100)
            + ((int)$sec * 100)
            + (int)$cs;
    }

    private function updateEventResult(array $payload){
        $data = CompetitionHeatLane::query()
        ->from('competition_heat_lanes as chl')
        ->select(
            'chl.id',
            'chl.competition_entry_id',
            'chl.swim_time',
            'ch.round_type',
            'ch.heat_number',
            'ch.competition_event_id',
        )
        ->leftJoin('competition_heats as ch', 'chl.competition_heat_id', '=', 'ch.id')
        ->where('chl.status', 'valid')
        ->where('ch.competition_event_id', $payload['competition_event_id'])
        ->where('ch.round_type', $payload['round_type'])
        ->get();

        $fixed = $data
                ->map(function($item) {
                    $item->time_in_cs = $this->swimTimeToCs($item->swim_time);
                    return $item;
                })
                ->sortBy('time_in_cs')
                ->values()
                ->map(function($item, $index){
                    return [
                        'competition_event_id' => $item->competition_event_id,
                        'competition_entry_id' => $item->competition_entry_id,
                        'round_type'           => $item->round_type,
                        'rank_overral'         => $index+1,
                        'time_in_cs'           => $item->time_in_cs,
                        'time_in_display'      => $item->swim_time,
                        'points'               => null,
                    ];
                })->toArray();

        EventResult::upsert(
            $fixed,
            ['competition_event_id', 'competition_entry_id', 'round_type'],
            ['rank_overral', 'time_in_cs', 'time_in_display', 'points']
        );
    }

    // ============================================================
    // HELPER: distribusi zigzag atlet ke heat (standar FINA)
    // Contoh 9 atlet, 3 heat:
    //   rank 1 → heat 3, rank 2 → heat 2, rank 3 → heat 1
    //   rank 4 → heat 3, rank 5 → heat 2, rank 6 → heat 1
    //   rank 7 → heat 3, rank 8 → heat 2, rank 9 → heat 1
    // ============================================================
    private function distributeZigzag(array $qualifiers, int $totalHeats): array
    {
        $heats = array_fill(1, $totalHeats, []);

        foreach ($qualifiers as $index => $lane) {
            $posInCycle = $index % $totalHeats;
            $heatNumber = $totalHeats - $posInCycle;

            $heats[$heatNumber][] = $lane;
        }

        return $heats;
    }
}
