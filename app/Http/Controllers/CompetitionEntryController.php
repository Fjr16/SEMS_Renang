<?php

namespace App\Http\Controllers;

use App\Enums\CompetitionStatus;
use App\Enums\CompetitionTeamEntryStatus;
use App\Enums\CompetitionTeamPaymentStatus;
use App\Enums\CompetitionTeamStatus;
use App\Enums\EventType;
use App\Models\Athlete;
use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionEntry;
use App\Models\CompetitionEntryRelayMember;
use App\Models\CompetitionEvent;
use App\Models\CompetitionTeam;
use App\Models\CompetitionTeamOfficial;
use App\Models\Official;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompetitionEntryController extends Controller
{
    public function index(){
        Carbon::setLocale('id');
        $q = request('q');
        $stts = request('status', null);

        $query = Competition::query()
        ->with(['venue', 'organization'])
        ->when($stts, function($q) use ($stts){
            $q->where('status', $stts);
        })
        ->when($q, function($qq) use ($q){
            $qq->where(function($subQ) use ($q){
                $subQ->where('name', 'LIKE', '%'.$q.'%')
                    ->orWhereHas('organization', function($organization) use ($q){
                        $organization->where('name', 'LIKE', '%'.$q.'%');
                    })
                    ->orWhereHas('venue', function($venue) use ($q){
                        $venue->where('name', 'like', "%{$q}%");
                    });
            });
        })
        ->orderBy('created_at', 'desc');
        $data = $query->paginate(21)->withQueryString();

        $compClass = CompetitionStatus::class;

        if(request()->ajax()){
            return view('pages.club.registrations.partials.cards', compact('data', 'compClass'))->render();
        }

        $team_id = auth()->user()->club_id ?? null;

        // Hitung counts per status
        $counts = CompetitionTeam::query()
            ->where('team_id', $team_id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Pastikan semua status ada (default 0)
        foreach (CompetitionTeamStatus::cases() as $case) {
            $counts[$case->value] = $counts[$case->value] ?? 0;
        }

        $entries = CompetitionTeam::query()
        ->where('team_id', $team_id)
        ->with([
            'competitionEntries',
            'competitionEntries.competitionEntryRelayMembers'
        ])
        ->orderBy('created_at','desc')
        ->paginate(21)
        ->withQueryString();

        // $events_count = $entries

        return view('pages.club.registrations.index',compact('data', 'compClass', 'entries', 'counts'));
    }

    public function create(Competition $competition){
        Carbon::setLocale('id');
        $events = $competition->events;
        $club = Auth::user()->club;
        $athletes = $club->athletes;
        $officials = $club->officials;
        $existingTeam = CompetitionTeam::where('competition_id', $competition->id)
        ->where('team_id', auth()->user()->club_id)
        ->with([
            'competitionEntries' => function($q) {
                $q->with([
                    'competitionEntryRelayMembers' => function($q) {
                        $q->where('status', 'active')->orderBy('leg_order');
                    },
                    'athlete',
                ]);
            },
            'competitionTeamOfficials',
        ])
        ->first();

        // Format existing data untuk JS
        $existingJS = null;
        if ($existingTeam) {
            $existingJS = [
                'team_id' => $existingTeam->team_id,
                'officials' => $existingTeam->competitionTeamOfficials->map(fn($o) => [
                    'id'   => $o->official_id,
                    'role' => $o->role_override ?? $o->official->role,
                ])->values(),
                'competitionEntries' => $existingTeam->competitionEntries
                    ->whereIn('status', [
                        CompetitionTeamEntryStatus::Pending->value,
                        CompetitionTeamEntryStatus::Active->value
                    ])
                    ->groupBy('competition_event_id')
                    ->map(function($eventEntries, $eventId) {
                        $first   = $eventEntries->first();
                        $isRelay = $first->is_relay;

                        if ($isRelay) {
                            return [
                                'event_id'        => $eventId,
                                'is_relay'        => true,
                                'team_entry_time' => $first->entry_time,
                                'athletes'        => $first->competitionEntryRelayMembers->map(fn($m) => [
                                    'id'        => $m->athlete_id,
                                    'leg_order' => $m->leg_order,
                                ])->values(),
                            ];
                        }

                        return [
                            'event_id'  => $eventId,
                            'is_relay'  => false,
                            'athletes'  => $eventEntries->map(fn($e) => [
                                'id'         => $e->athlete_id,
                                'entry_time' => $e->entry_time,
                            ])->values(),
                        ];
                    })->values(),
            ];
        }
        return view('pages.club.registrations.create', [
            'comp' => $competition,
            'events' => $events,
            'club' => $club,
            'athletes' => $athletes,
            'officials' => $officials,
            'existingJS' => $existingJS
        ]);
    }

    public function indexGuest(){
        $q = request('q');
        $stts = request('status', null);

        $query = Competition::query()
        ->with(['venue', 'organization'])
        ->when($stts, function($q) use ($stts){
            $q->where('status', $stts);
        })
        ->when($q, function($qq) use ($q){
            $qq->where(function($subQ) use ($q){
                $subQ->where('name', 'LIKE', '%'.$q.'%')
                    ->orWhereHas('organization', function($organization) use ($q){
                        $organization->where('name', 'LIKE', '%'.$q.'%');
                    })
                    ->orWhereHas('venue', function($venue) use ($q){
                        $venue->where('name', 'like', "%{$q}%");
                    });
            });
        })
        ->orderBy('created_at', 'desc');
        $data = $query->paginate(21)->withQueryString();

        $compClass = CompetitionStatus::class;

        if(request()->ajax()){
            return view('pages.club.registrations.partials.cards', compact('data', 'compClass'))->render();
        }

        $accessType = 'Guest';
        return view('pages.club.registrations.index',compact('data', 'compClass', 'data'));
    }

    public function store(Request $r){
        $validators = Validator::make($r->all(), [
            'team_id' => ['required', 'integer', 'exists:clubs,id'],
            'competition_id' => ['required', 'integer', 'exists:competitions,id'],
            'entries' => ['required', 'array', 'min:1'],
            'entries.*.athletes' => ['required', 'array', 'min:1'],
            'entries.*.athletes.*' => ['integer', 'exists:athletes,id'],
            'entries.*.entry_times' => ['nullable', 'array'],
            'entries.*.entry_times.*' => ['nullable', 'regex:/^\d{2}:\d{2}\.\d{2}$/'],
            'entries.*.team_entry_time' => ['nullable', 'regex:/^\d{2}:\d{2}\.\d{2}$/'],
            'entries.*.relay_orders' => ['nullable', 'array'],
            'entries.*.relay_orders.*' => ['nullable', 'integer', 'min:1'],
            'comp_officials' => ['nullable', 'array'],
            'comp_officials.*.id' => ['required', 'integer', 'exists:officials,id'],
            'comp_officials.*.role' => ['nullable', 'string', 'max:50'],
        ]);

        if ($validators->fails()) {
            return response()->json([
                'message' => 'Data Tidak Valid',
                'errors'  => $validators->errors(),
            ], 422);
        }

        try {
            $errors     = [];
            $comp       = Competition::find($r->competition_id);
            $entries    = $r->entries ?? [];
            $officials  = $r->comp_officials ?? [];
            $team = Club::find($r->team_id);

            if (!$team || $team->id !== auth()->user()->club_id) {
                return response()->json([
                    'message' => 'Data Tidak Valid',
                    'errors'  => ['Team tidak valid atau bukan milik klub Anda.'],
                ], 422);
            }

            foreach ($entries as $eventId => $entry) {
                $event = CompetitionEvent::where('id', $eventId)
                    ->whereHas('competitionSession', function($query) use ($comp){
                        return $query->where('competition_id', $comp->id);
                    })
                    ->with('ageGroup')
                    ->first();

                if (!$event) {
                    $errors[] = "Event ID {$eventId} tidak valid atau bukan bagian dari kompetisi ini.";
                    continue;
                }

                $athleteIds = $entry['athletes'] ?? [];
                $isRelay    = $event->event_type === EventType::estafet->value;
                $maxRelay   = $event->max_relay_athletes;

                // Cek atlet milik klub ini
                $validAthletes = Athlete::whereIn('id', $athleteIds)
                    ->where('club_id', $team->id)
                    ->where('status', 'active')
                    ->get()
                    ->keyBy('id');

                foreach ($athleteIds as $athId) {
                    if (!$validAthletes->has($athId)) {
                        $errors[] = "Atlet ID {$athId} tidak valid atau bukan milik klub Anda.";
                        continue;
                    }

                    $athlete = $validAthletes->get($athId);

                    // Validasi kelompok umur
                    if ($event->ageGroup && $event->ageGroup->label !== 'UMUM') {
                        $minDob = Carbon::parse($comp->start_date)->subYears($event->ageGroup->max_age)->format('Y-m-d');
                        $maxDob = Carbon::parse($comp->start_date)->subYears($event->ageGroup->min_age)->format('Y-m-d');

                        if ($athlete->bod < $minDob || $athlete->bod > $maxDob) {
                            $errors[] = "Atlet {$athlete->name} tidak sesuai kelompok umur event";
                        }
                    }

                    // Validasi gender event
                    if ($event->gender && $event->gender !== 'mixed' && $athlete->gender !== $event->gender) {
                        $errors[] = "Atlet {$athlete->name} tidak sesuai jenis kelamin event";
                    }
                }

                // Validasi khusus estafet
                if ($isRelay) {
                    // cek max atlet
                    if (count($athleteIds) > $maxRelay) {
                        $errors[] = "Event {$event->event_number}: jumlah atlet melebihi batas estafet ({$maxRelay}).";
                    }

                    // cek relay_orders
                    $relayOrders = $entry['relay_orders'] ?? [];

                    // semua atlet harus punya urutan
                    foreach ($athleteIds as $athId) {
                        if (empty($relayOrders[$athId]) || $relayOrders[$athId] == 0) {
                            $errors[] = "Event {$event->event_number}: atlet ID {$athId} belum memiliki urutan estafet.";
                        }
                    }

                    // urutan tidak boleh duplikat
                    $orderValues = array_values($relayOrders);
                    if (count($orderValues) !== count(array_unique($orderValues))) {
                        $errors[] = "Event {$event->event_number}: terdapat urutan estafet yang duplikat.";
                    }
                }
            }
            // ── 3. Validasi official ──────────────────────────────────────────────
            foreach ($officials as $ofc) {
                $valid = Official::where('id', $ofc['id'])
                    ->where('club_id', $team->id)
                    ->exists();

                if (!$valid) {
                    $errors[] = "Official ID {$ofc['id']} tidak valid atau bukan milik klub Anda.";
                }
            }

            $message = 'Data tidak valid';
            $oldEntry = CompetitionTeam::where('competition_id', $comp->id)
                    ->where('team_id', $team->id)
                    ->first();
            // Cek masa registrasi //atau gunakan status saja, sehingga jika registration end date telah lewat namun stts masih open bisa update data
            if (now()->toDateString() > $comp->registration_end) {
                $message = 'Gagal Simpan Entry';
                $errors[] = "Masa registrasi telah ditutup";
            }
            // ditolak tidak di larang update karena memikirkan kemungkinan untuk perbaikan data agar diterima
            if ($oldEntry?->status == CompetitionTeamStatus::Disqualified->value || $oldEntry?->status == CompetitionTeamStatus::Withdrawn->value) {
                $message = 'Gagal Update Entry';
                $errors[] = "Team anda telah ". CompetitionTeamStatus::from($oldEntry->status)->label() .", silahkan hubungi panitia kompetisi";
            }

            if (!empty($errors)) {
                return response()->json([
                    'message' => $message,
                    'errors'  => $errors,
                ], 422);
            }


            // ── 5. Simpan data ────────────────────────────────────────────────────
            DB::transaction(function () use ($comp, $entries, $officials, $team) {
                $existingActiveRegistration = CompetitionTeam::where('competition_id', $comp->id)
                    ->where('team_id', $team->id)
                    ->where('status', CompetitionTeamStatus::Active->value)
                    ->first();

                // Tambah atlet ya
                // event baru Ya
                // Ganti anggota relay Ya
                // CompetitionTeam->status == active ya
                // Update entry time saja❌ Tidak perlu, panitia yang urus seed time
                // Cek apakah ada perubahan yang perlu verifikasi ulang
                $needsReview = $this->needReviewCheck($existingActiveRegistration, $entries);

                // Upsert CompetitionTeam
                $item = CompetitionTeam::updateOrCreate(
                    [
                        'competition_id' => $comp->id,
                        'team_id'        => $team->id,
                    ],
                    [
                        'status' => $needsReview ? CompetitionTeamStatus::Pending->value : ($existingActiveRegistration?->status ?? CompetitionTeamStatus::Pending->value),
                        'payment_status' => $needsReview ? CompetitionTeamPaymentStatus::Unpaid->value : ($existingActiveRegistration?->status ?? CompetitionTeamPaymentStatus::Unpaid->value),
                    ]
                );

                // Upsert officials
                $incomingOfficialIds = collect($officials)->pluck('id')->toArray();
                // delete official yang tidak ada di list baru
                CompetitionTeamOfficial::where('competition_team_id', $item->id)
                    ->whereNotIn('official_id', $incomingOfficialIds)
                    ->delete();

                foreach ($officials as $ofc) {
                    $ofcrole = Official::find($ofc['id'])->role ?? null;
                    CompetitionTeamOfficial::updateOrCreate(
                        [
                            'competition_team_id' => $item->id,
                            'official_id'    => $ofc['id'],
                        ],
                        [
                            'role_override'    =>$ofc['role'] ?? $ofcrole,
                        ]
                    );
                }

                // simpan atau update registrasi per event
                foreach ($entries as $eventId => $entry) {
                    $event = CompetitionEvent::find($eventId);
                    $isRelay =  $event->event_type === EventType::estafet->value;

                    $athleteIds  = $entry['athletes'] ?? [];
                    $teamEntryTime  = $entry['team_entry_time'] ?? null;
                    $entryTimes  = $entry['entry_times'] ?? [];
                    $relayOrders = $entry['relay_orders'] ?? [];

                    // jika status entry diskulfikasi maka abaikan perubahan atau penambahan, pengurangan data terhadap entry ini
                    $checkDiskualifikasi = CompetitionEntry::where('competition_team_id', $item->id)
                            ->where('competition_event_id', $eventId)
                            ->where('status', CompetitionTeamEntryStatus::Disqualified->value)
                            ->exists();
                    if($checkDiskualifikasi) continue;

                    if($isRelay){
                        $existingEntry = CompetitionEntry::where('competition_team_id', $item->id)
                                ->where('competition_event_id', $eventId)
                                ->first();

                        $entryStatus = $existingEntry ? $existingEntry->status : CompetitionTeamEntryStatus::Pending->value;

                        // Jika ada anggota baru → pending
                        $existingMemberIds = $existingEntry
                            ? $existingEntry->competitionEntryRelayMembers->where('status', 'active')->pluck('athlete_id')->toArray()
                            : [];

                        $newMembers = array_diff($athleteIds, $existingMemberIds);
                        if (!empty($newMembers)) $entryStatus = CompetitionTeamEntryStatus::Pending->value;

                        // jika status entry sebelumnya adalah scratch, withdrawn, atau active, maka jika ada perubahan pada data entry,
                        // dianggap sebagai pengjuan ulang entry yang perlu di review oleh panitia kembali, makanya status entry menjadi pending
                        $temp = CompetitionEntry::updateOrCreate(
                            [
                                'competition_team_id' => $item->id,
                                'competition_event_id' => $eventId,
                            ],
                            [
                                'athlete_id'     => null,
                                'is_relay'     => true,
                                'entry_time'     => $teamEntryTime,
                                'status' => $entryStatus,
                            ]
                        );

                        // Soft delete anggota yang tidak ada di list baru
                        CompetitionEntryRelayMember::where('competition_entry_id', $temp->id)
                            ->whereNotIn('athlete_id', $athleteIds)
                            ->update(['status' => 'scratched']);

                        foreach ($athleteIds as $athId) {
                            CompetitionEntryRelayMember::updateOrCreate(
                                [
                                    'competition_entry_id' => $temp->id,
                                    'athlete_id' => $athId,
                                ],
                                [
                                    'leg_order' => $relayOrders[$athId] ?? null,
                                    'status' => 'active',
                                ]
                            );
                        }
                    } else {
                        // Soft delete atlet yang tidak ada di list baru
                        CompetitionEntry::where('competition_team_id', $item->id)
                            ->where('competition_event_id', $eventId)
                            ->whereNotIn('athlete_id', $athleteIds)
                            ->update(['status' => 'scratched']);

                        // jika atlet yang sebelumnya berstatus selain aktif, mis: scratched, withdrawn, atau pending, kecuali diskualifikasi karena akan diabaikan update datanya
                        // maka entry tersebut dibaca sebagai entry baru dengan status pending
                        // jika atlet memang sudah ada dan aktif maka jika ada pergantian atlet, juga dihitung sebagai entry baru, bukan ubah entry lama, entry lama dijadikan scratched dengan status pending
                        // namun jika atlet memang sudah ada dan aktif dan jika ada perubahan data selain pergantian atlet, maka status tetap aktif
                        $existingAthleteIds = CompetitionEntry::where('competition_team_id', $item->id)
                            ->where('competition_event_id', $eventId)
                            ->where('status', CompetitionTeamEntryStatus::Active->value)
                            ->pluck('athlete_id')
                            ->toArray() ?? [];

                        foreach ($athleteIds as $athId) {
                            $isNew = !in_array($athId, $existingAthleteIds);
                            CompetitionEntry::updateOrCreate(
                                [
                                    'competition_team_id' => $item->id,
                                    'competition_event_id'=> $eventId,
                                    'athlete_id'     => $athId,
                                ],
                                [
                                    'is_relay'     => false,
                                    'entry_time'     => $entryTimes[$athId]  ?? null,
                                    'status' => $isNew ? CompetitionTeamEntryStatus::Pending->value : CompetitionTeamEntryStatus::Active->value,
                                ]
                            );
                        }
                    }
                }

                // Soft delete event yang tidak ada di submission baru
                $incomingEventIds = array_keys($entries);
                CompetitionEntry::where('competition_team_id', $item->id)
                    ->whereNotIn('competition_event_id', $incomingEventIds)
                    ->update(['status' => 'scratched']);
            });

            return response()->json([
                'message' => 'Entry berhasil disimpan.',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data. Terjadi kesalahan pada database.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 422);
        }
    }

    private function needReviewCheck($competitionTeam, $entries){
        if(!$competitionTeam) return false;
        $events = CompetitionEvent::whereIn('id', array_keys($entries))
                ->pluck('event_type', 'id');
        foreach ($entries as $eventId => $entry) {
            $existingEntry = CompetitionEntry::where('competition_team_id', $competitionTeam->id)
                            ->where('competition_event_id', $eventId)
                            ->where('status', CompetitionTeamEntryStatus::Active->value)
                            ->first();
            dd($existingEntry);
            if(!$existingEntry) return true;

            $athleteIds  = $entry['athletes'] ?? [];
            $isRelay = $events[$eventId] === EventType::estafet->value;

            if($isRelay){
                // jika ada penambahan atlet pada team relay
                $existingAthleteIds = $existingEntry
                    ? $existingEntry->competitionEntryRelayMembers->where('status', 'active')->pluck('athlete_id')->toArray()
                    : [];
                $newAthletes = array_diff($athleteIds, $existingAthleteIds);
                if (!empty($newAthletes)) return true;
            }else{
                // jika ada penambahan atlet baru
                if ($competitionTeam && $competitionTeam->status === CompetitionTeamStatus::Active->value) {
                    $newAthlete = !in_array($existingEntry->athlete_id,$athleteIds);
                    if (!empty($newAthlete)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

}
