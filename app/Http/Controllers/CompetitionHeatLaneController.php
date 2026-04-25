<?php

namespace App\Http\Controllers;

use App\Enums\CompetitionTeamEntryStatus;
use App\Enums\RoundTypeEnum;
use App\Models\Competition;
use App\Models\CompetitionEvent;
use App\Models\CompetitionHeat;
use App\Models\CompetitionHeatLane;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class CompetitionHeatLaneController extends Controller
{
    public function partialReload(Competition $competition, Request $r){
        $event_id = $r->input('event_id');
        $event = !$event_id
        ? $competition->events->first()
        : CompetitionEvent::find($event_id);
        $event->load(['heats.heatLanes.entry.athlete.club']);

        $selectEvents = $competition->events;
        $totalLanes = $event->competitionSession->pool->total_lanes;
        $totalEntries = $event->entries->where('status', CompetitionTeamEntryStatus::Active->value)->count();

        // Group heats by round_type
        $heatsByRound = $event->heats->groupBy('round_type');

        return view('pages.competition.tabs.heats', compact(
            'competition',
            'event',
            'selectEvents',
            'totalLanes',
            'totalEntries',
            'heatsByRound'
        ));
    }
    public function generateHeat(Competition $competition){
        $events = $competition->events;
        $roundType = 'PRELIM';

        $result = [];
        foreach ($events as $index => $event) {
           $result[$index]['competition_event_id'] = $event->id;
           $result[$index]['heats'] = $this->generateHeatByEvent($event);
        }

        if(empty($result)){
            return response()->json([
                'status' => false,
                'message' => 'Tidak ditemukan data entry yang aktif'
            ]);
        }

        foreach ($result as $res) {
            if(!$res['heats']) continue;
            foreach ($res['heats'] as $heat) {
                $heatId = CompetitionHeat::create([
                    'competition_event_id' => $res['competition_event_id'],
                    'heat_number' => $heat['heat_number'],
                    'round_type' => $roundType
                ])->id;
                foreach($heat['lanes'] as $index => $lane){
                    CompetitionHeatLane::create([
                        'competition_heat_id' => $heatId,
                        'competition_entry_id' => $lane['entry_id'],
                        'lane_number' => $lane['lane_number'],
                        'lane_order' => $index+1,
                    ]);
                }
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'Heat dan lane berhasil dibuat'
        ]);
    }
    public function generateHeatByEvent(CompetitionEvent $event){
        $totalLanes = $event->competitionSession->pool->total_lanes;

        $entries = $event->entries()
                ->where('status', CompetitionTeamEntryStatus::Active->value)
                ->get();
        if ($entries->isEmpty()) return;

        $orderedEntry = $entries->sortByDesc(function ($e) {
            if (!$e->seed_time) return PHP_INT_MAX; // null = paling lambat

            [$minute, $second] = explode(':', $e->seed_time);
            return ((int) $minute * 60) + (float) $second;
        })->values();

        $totalEntry = $orderedEntry->count();
        $sisa = $totalEntry % $totalLanes;

        if($totalEntry <= $totalLanes){
            $assignEntryToHeat = collect([$orderedEntry]);
        }else if ($sisa > 0 && $sisa <= (int) floor($totalLanes / 2)) {
            // Jika sisa ≤ setengah lane, redistribusi 2 heat pertama
            $twoHeatTotal  = $sisa + $totalLanes; // gabung 2 heat pertama
            $firstCount    = (int) floor($twoHeatTotal / 2);
            $secondCount   = (int) ceil($twoHeatTotal / 2);

            $firstHeat     = $orderedEntry->slice(0, $firstCount)->values();
            $secondHeat    = $orderedEntry->slice($firstCount, $secondCount)->values();
            $restOfHeats   = $orderedEntry->slice($firstCount + $secondCount)->chunk($totalLanes);

            $assignEntryToHeat = collect([$firstHeat, $secondHeat])->merge($restOfHeats);
        } else {
            // Sisa cukup banyak, heat pertama pakai sisa
            $firstHeat         = $orderedEntry->slice(0, $sisa ?: $totalLanes)->values();
            $restOfHeats       = $orderedEntry->slice($sisa ?: $totalLanes)->chunk($totalLanes);
            $assignEntryToHeat = collect([$firstHeat])->merge($restOfHeats);
        }

        $lanes = $this->generateSnakeLanes($totalLanes);


        $arrHeats = [];
        foreach($assignEntryToHeat as $heatIndex => $entry){
            $arrHeats[$heatIndex]['heat_number'] = $heatIndex + 1;
            $entry = $entry->sortBy(function ($e) {
                if (!$e->seed_time) return PHP_INT_MAX;
                [$minute, $second] = explode(':', $e->seed_time);
                return ((int)$minute * 60) + (float)$second;
            })->values();

            foreach ($entry->values() as $laneIndex => $item) {
                $lane = $lanes[$laneIndex];
                $arrHeats[$heatIndex]['lanes'][$laneIndex]['lane_number'] = $lane;
                $arrHeats[$heatIndex]['lanes'][$laneIndex]['entry_id'] = $item->id;
                $arrHeats[$heatIndex]['lanes'][$laneIndex]['seed_time'] = $item->seed_time;
            }
        }
        return $arrHeats;
    }

    private function generateSnakeLanes(int $totalLanes): array{
        $lanes = [];
        $mid   = (int) floor(($totalLanes + 1) / 2); // 8→4, 6→3...
        if ($totalLanes % 2 === 0) {
            $mid = $totalLanes / 2; // 8→4, 6→3
        }
        $left = $mid;
        $right = $mid + 1;

        $lanes[] = $mid;

        while (count($lanes) < $totalLanes) {
            if ($right <= $totalLanes) { $lanes[] = $right++; }
            if ($left > 1)             { $lanes[] = --$left;  }
        }
        return $lanes;
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

        // Hapus heats lama jika ada
        foreach ($event->heats as $heat) {
            $heat->heatLanes()->delete();
        }
        $event->heats()->delete();

        // Ambil semua entry aktif, sort by seed_time (NT paling belakang)
        $entries = $event->entries()
            ->where('status', CompetitionTeamEntryStatus::Active->value)
            ->whereHas('competitionTeam', fn($q) => $q->where('status', 'active'))
            ->orderByRaw("CASE WHEN seed_time IS NULL THEN 1 ELSE 0 END")
            ->orderBy('seed_time')
            ->get();

        if ($entries->isEmpty()) return;

        // Generate hanya untuk ronde pertama (penyisihan/final)
        // Ronde berikutnya diisi via "Promosi Atlet"
        $firstRound = $request->rounds[0];
        $usedLanes  = min($firstRound['lanes'], $totalLanes);

        $activeLanes = $this->getActiveLanes($usedLanes, $totalLanes);
        $laneOrder   = $this->getCircleSeedOrder($activeLanes);

        $totalHeats = (int) ceil($entries->count() / $usedLanes);

        // Distribute atlet — terkencang di heat terakhir
        $chunks = $entries->reverse()->chunk($usedLanes)->values()->reverse()->values();

        foreach ($chunks as $heatIndex => $chunk) {
            $heat = CompetitionHeat::create([
                'competition_event_id' => $event->id,
                'heat_number'          => $heatIndex + 1,
                'round_type'           => $firstRound['type'],
                'used_lanes'           => $usedLanes,
            ]);

            foreach ($chunk->values() as $pos => $entry) {
                CompetitionHeatLane::create([
                    'competition_heat_id'  => $heat->id,
                    'competition_entry_id' => $entry->id,
                    'lane_number'          => $laneOrder[$pos] ?? ($pos + 1),
                    'lane_order'           => $pos + 1,
                    'seed_time'            => $entry->seed_time,
                ]);
            }
        }

        // Simpan konfigurasi round berikutnya ke session/cache
        // agar saat "Promosi Atlet" diklik, sistem tahu konfigurasinya
        cache()->put(
            "heat_config_{$event->id}",
            $request->rounds,
            now()->addHours(24)
        );

        return response()->json([
            'status' => true,
            'messsage' => 'Sukses generate seri'
        ]);
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
        $mid   = (int) floor(count($sorted) / 2);
        $order = [];
        $l = $mid - 1;
        $r = $mid;

        if (count($sorted) % 2 === 0) {
            // $order[] = $sorted[$r++];
            // $order[] = $sorted[$l--];
            $order[] = $sorted[$l--];
            $order[] = $sorted[$r++];
        } else {
            $order[] = $sorted[$mid];
            $r = $mid + 1;
            $l = $mid - 1;
        }

        while ($r < count($sorted) || $l >= 0) {
            if ($r < count($sorted)) $order[] = $sorted[$r++];
            if ($l >= 0)             $order[] = $sorted[$l--];
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
}
