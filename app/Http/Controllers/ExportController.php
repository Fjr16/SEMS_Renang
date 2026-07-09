<?php

namespace App\Http\Controllers;

use App\Enums\CompetitionTeamStatus;
use App\Enums\EventType;
use App\Enums\RoundTypeEnum;
use App\Enums\Gender;
use App\Enums\Stroke;
use App\Exports\StartingListExport;
use App\Models\AgeGroup;
use App\Models\Competition;
use App\Models\CompetitionHeatLane;
use App\Models\CompetitionSession;
use App\Models\CompetitionTeam;
use App\Traits\HasApiResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    use HasApiResponse;

    public function startingList(Request $request){
        try {
            $request->validate([
                'competition_team_id'  => 'required|exists:competition_teams,id',
            ]);

            $item = CompetitionTeam::with([
                        'competition.events',
                        'competitionEntries.athlete',
                        'competitionEntries.competitionEvent',
                        'competitionEntries.competitionEvent.ageGroup',
                    ])
                    ->find($request->competition_team_id);

            if(!$item){
                return response()->json([
                    'status' => false,
                    'message' => 'Data pendaftaran tim tidak ditemukan'
                ]);
            }

            $eventGroups = $item->competition->events
            ->where('event_type', EventType::individual->value)
            ->groupBy('stroke')
            ->map(fn($events, $gaya) => [
                'name'   => $gaya,
                'events' => $events
                    ->sortBy('distance')
                    ->map(fn($e) => [
                        'id'    => $e->id,
                        'label' => $e->distance . 'm' . ($e->equipment ? ' ' . ucfirst($e->equipment) : ''),
                    ])->values()->toArray(),
            ])
            ->values()
            ->toArray();

            $peserta = $item->competitionEntries
            ->where('is_relay', false)
            ->groupBy('athlete_id')
            ->values()
            ->map(fn($entries, $i) => [
                'no'        => $i + 1,
                'nama'      => $entries->first()->athlete?->name ?? '-',
                'ku'        => $entries->first()->competitionEvent?->ageGroup?->label ?? '-',
                'pa_pi'     => $entries->first()->competitionEvent?->gender === 'male' ? 'PA' : 'PI',
                'event_ids' => $entries->pluck('competition_event_id')->toArray(),
                'biaya' => $entries->sum(fn($entry) => $entry->competitionEvent?->registration_fee ?? 0)
            ])->toArray();

            $eventGroupsRelay = $item->competition->events
            ->where('event_type', EventType::estafet->value)
            ->groupBy('stroke')
            ->map(fn($events, $gaya) => [
                'name'   => $gaya,
                'events' => $events
                    ->sortBy('distance')
                    ->map(fn($e) => [
                        'id'    => $e->id,
                        'label' => ($e->max_relay_athletes ? $e->max_relay_athletes . 'x' : '') . $e->distance . 'm',
                    ])->values()->toArray(),
            ])
            ->values()
            ->toArray();

            // untuk relay tidak perlu group by, karena satu tim hanya dapat mendaftarkan
            // 1 tim estafet dalam satu event yang sama
            // jadi tidak akan ada data team_id, dan event_id yang sama pada entries untuk tipe estafet
            $entryRelay = $item->competitionEntries
            ->where('is_relay',true)
            ->values()
            ->map(fn($entry, $i) => [
                'no'        => $i + 1,
                'nama_tim'  => $entry->competitionTeam?->team->club_name ?? '-',
                'ku'        => $entry->competitionEvent?->ageGroup?->label ?? '-',
                'pa_pi'     => $entry->competitionEvent?->gender === 'male' ? 'PA' : 'PI',
                'event_ids' => [$entry->competition_event_id],
                'biaya' => $entry->competitionEvent?->registration_fee ?? 0,
                'anggota' => $entry->competitionEntryRelayMembers
                            ->sortBy('leg_order')
                            ->map(fn($m) => [
                                'leg'  => $m->leg_order,
                                'nama' => $m->athlete?->name ?? '-',
                            ])->values()->toArray() ?? [],
            ])->toArray();

            $nama_club = $item?->team?->club_name ?? '-';
            $tahun = $item?->competition?->start_date ? Carbon::parse($item?->competition?->start_date)->format('Y') : '-';

            $export = new StartingListExport(
                peserta: $peserta,
                eventGroups: $eventGroups,
                entryRelay: $entryRelay,
                eventGroupsRelay: $eventGroupsRelay,
                clubName: $nama_club,
                competitionName: $item?->competition?->name ?? '-',
                year: $tahun,
            );

            $filename = 'starting_list_' . str_replace(' ', '_', $nama_club) . '_' . $tahun . '.xlsx';

            return Excel::download($export, $filename);
        } catch (\Throwable $th) {
            return $this->error(substr($th->getMessage(),0,150));
        }
    }
    public function bukuAcara(Request $request){
        try {
            Carbon::setLocale('Id');
            $item = Competition::with([
                'venue',
                'sessions',
                'events',
                'entries',
            ])->find($request->competition_id);

            $mulaiKompetisi = Carbon::parse($item->start_date);
            $akhirKompetisi = Carbon::parse($item->end_date);

            $jadwalHari = [];
            $acaraList = [];

            if ($mulaiKompetisi->isSameDay($akhirKompetisi)) {
                $tanggal = $mulaiKompetisi->translatedFormat('l, d F Y');
            } elseif ($mulaiKompetisi->isSameMonth($akhirKompetisi)) {
                $tanggal = $mulaiKompetisi->translatedFormat('d') . ' – ' . $akhirKompetisi->translatedFormat('d F Y');
            } else {
                $tanggal = $mulaiKompetisi->translatedFormat('d F') . ' – ' . $akhirKompetisi->translatedFormat('d F Y');
            }
            // jadwal
            if ($mulaiKompetisi->isSameDay($akhirKompetisi)) {
                $jadwalHari = [
                    [
                        'label' => $akhirKompetisi->translatedFormat('l, d F Y'),
                        'sesi' => $item->sessions->map(function ($sesi) {
                            return [
                                'nama' => $sesi->name ?? '-',
                                'acara' => $sesi->competitionEvents
                                    ->groupBy(fn($e) => $e->distance .'|'. $e->stroke . '|' . $e->event_type . '|' . ($e->equipment ?? ''))
                                    ->map(function($events){
                                        $first = $events->first();
                                        $distLabel = ($first->event_type === EventType::estafet->value && $first->max_relay_athletes)
                                            ? $first->max_relay_athletes . 'x' . $first->distance
                                            : $first->distance;
                                        $equipLabel = $first->equipment ? ' ' . ucfirst($first->equipment) : '';
                                        return [
                                            'nomor' => $distLabel . ' M ' . (Stroke::tryFrom($first->stroke)?->label() ?? '-') . $equipLabel,
                                            'tipe_event' => EventType::tryFrom($first->event_type)?->label() ?? '-',
                                            'ku_list' => $events->groupBy('age_group_id')->map(function ($eByKu){
                                                $noPa = $eByKu->where('gender', Gender::pria->value)->value('event_number');
                                                $noPi = $eByKu->where('gender', Gender::wanita->value)->value('event_number');
                                                $noCampuran = $eByKu->where('gender', 'mixed')->value('event_number');
                                                return [
                                                    'ku' => $eByKu->first()?->ageGroup?->label ?? '-',
                                                    'pa' => $noPa,
                                                    'pi' => $noPi,
                                                    'mix' => $noCampuran
                                                ];
                                            })->values()->toArray(),
                                        ];
                                })->values()->toArray(),
                            ];
                        })->toArray(),
                    ],
                ];
            } else {
                $jadwalHari = $item->sessions->groupBy('session_date')->map(function($byDate){
                    $tglSesi = Carbon::parse($byDate->first()->session_date);
                    return [
                        'label' => $tglSesi->translatedFormat('l, d F Y'),
                        'sesi' => $byDate->map(function ($sesi) {
                            return [
                                'nama' => $sesi->name ?? '-',
                                'acara' => $sesi->competitionEvents
                                    ->groupBy(fn($e) => $e->distance .'|'. $e->stroke . '|' . $e->event_type . '|' . ($e->equipment ?? ''))
                                    ->map(function($events){
                                        $first = $events->first();
                                        $distLabel = ($first->event_type === EventType::estafet->value && $first->max_relay_athletes)
                                            ? $first->max_relay_athletes . 'x' . $first->distance
                                            : $first->distance;
                                        $equipLabel = $first->equipment ? ' ' . ucfirst($first->equipment) : '';
                                        return [
                                            'nomor' => $distLabel . ' M ' . (Stroke::tryFrom($first->stroke)?->label() ?? '-') . $equipLabel,
                                            'tipe_event' => EventType::tryFrom($first->event_type)?->label() ?? '-',
                                            'ku_list' => $events->groupBy('age_group_id')->map(function ($eByKu){
                                                $noPa = $eByKu->where('gender', Gender::pria->value)->value('event_number');
                                                $noPi = $eByKu->where('gender', Gender::wanita->value)->value('event_number');
                                                $noCampuran = $eByKu->where('gender', 'mixed')->value('event_number');
                                                return [
                                                    'pa' => $noPa,
                                                    'ku' => $eByKu->first()?->ageGroup?->label ?? '-',
                                                    'pi' => $noPi,
                                                    'mix' => $noCampuran
                                                ];
                                            })->values()->toArray(),
                                        ];
                                })->values()->toArray(),
                            ];
                        })->values()->toArray(),
                    ];
                })->toArray();
            }

            // list acara dan entry
            $acaraList = $item->events->sortBy('event_number')->map(function($e){
                $roundAwal = count($e->configs) > 1 ? $e->configs?->where('order', 1)->first()?->round_type : $e->configs?->first()?->round_type;
                $totalLanes = $e->competitionSession?->pool?->total_lanes ?? '8';
                return [
                    'nomor' => $e?->event_number,
                    'nama' => (($e->event_type === EventType::estafet->value && $e->max_relay_athletes) ? $e->max_relay_athletes . 'x' : '') . ($e?->distance ?? '-') . ' M ' . (Stroke::tryFrom($e->stroke)?->label() ?? '-') . ($e->equipment ? ' ' . ucfirst($e->equipment) : '') . ', ' . $e?->competitionSession->pool->course_type ?? '-' ,
                    'tanggal' => Carbon::parse($e->competitionSession?->session_date)->translatedFormat('l, d F Y') . ' — ' . ($e?->competitionSession?->name ?? '-'),
                    'status' => $roundAwal ? RoundTypeEnum::from($roundAwal)->label() : '-',
                    'limit'  => ($e?->limit_waktu ?? 'NO LIMIT'),
                    'kategori' => $e?->ageGroup?->label ?? '-',
                    'seri' => $e->heats->where('round_type', $roundAwal)->sortBy('heat_number')->map(function($heat) use ($e, $totalLanes){
                        $existingLanes = $heat->heatLanes->keyBy('lane_number');

                        $atlets = collect(range(1, $totalLanes))->map(function($laneNumber) use ($existingLanes, $e){
                            $lane = $existingLanes->get($laneNumber);
                            $tglLahir = $lane?->entry?->athlete?->bod;

                            return [
                                'ln'        => $laneNumber,
                                'id'        => $lane?->entry?->athlete?->code ?? '-',
                                'nama'      => $lane?->entry?->athlete?->name ?? '-',
                                'ket'       => '',
                                'lahir'     => $tglLahir ? Carbon::parse($tglLahir)->format('Y') : '-',
                                'umur'      => $tglLahir ? Carbon::parse($tglLahir)->age : '-',
                                'ket_mosc'  => '',
                                'ku'        => $e?->ageGroup?->label ?? '-',
                                'tim'       => $lane?->entry?->competitionTeam?->team?->club_name ?? '-',
                                'prestasi'  => $lane?->entry?->seed_time ?? '-',
                                'id_lomba'  => '-', //id kompetisi dari prestasi berasal
                            ];
                        });

                        return [
                            'nomor' => $heat?->heat_number ?? '-',
                            'atlet' => $atlets
                        ];
                    })->values()->toArray(),
                ];
            })->values()->toArray();

            $data = [
                'namaEvent' => strtoupper($item?->name ?? 'Kompetisi -'),
                'tanggal'   => strtoupper($tanggal),
                'venue'     => ($item?->venue?->name ?? '-') . ', ' . ($item?->venue?->city ?? '-'),

                'logoKiri'  => [
                    public_path('assets/logo-sumbar.png'),
                    public_path('assets/logo-kota.png'),
                    public_path('assets/akuatik-indonesia-seeklogo.png'),
                ],

                'jadwalHari' => $jadwalHari,
                'acaraList' => $acaraList,
            ];

            $pdf = Pdf::loadView('pages.export_doc.buku_acara', $data)
                ->setPaper('a4', 'portrait');

            $pdf->render();

            $dompdf = $pdf->getDomPDF();
            $canvas  = $dompdf->getCanvas();
            $fontMetrics = $dompdf->getFontMetrics(); // ← simpan sebagai fontMetrics

            $fontNormal = $fontMetrics->getFont("Arial", "normal");

            $w = $canvas->get_width();
            $h = $canvas->get_height();

            // HEADER
            $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($data, $w) {
                if ($pageNumber === 1) return;
                $fontBold   = $fontMetrics->getFont("Arial", "bolder");
                $fontNormal = $fontMetrics->getFont("Arial", "normal");

                $centerX = function ($text, $font, $size) use ($fontMetrics, $w) {
                    $textWidth = $fontMetrics->getTextWidth($text, $font, $size);
                    return ($w - $textWidth) / 2;
                };

                $canvas->text($centerX($data['namaEvent'], $fontBold,   9),   18, $data['namaEvent'], $fontBold,   9,   [0, 0, 0]);
                $canvas->text($centerX($data['venue'],     $fontNormal, 7.5), 32, $data['venue'],     $fontNormal, 7.5, [0, 0, 0]);
                $canvas->text($centerX($data['tanggal'],   $fontBold,   7.5), 44, $data['tanggal'],   $fontBold,   7.5, [0, 0, 0]);
                $canvas->text($centerX("BUKU ACARA",       $fontBold,   14),  56, "BUKU ACARA",       $fontBold,   14,  [0, 0, 0]);

                $canvas->line(28, 72, $w - 28, 72, [0, 0, 0], 0.5);
            });

            $canvas->page_text(
                28, $h - 16,
                "Dicetak " . Carbon::now()->translatedFormat('l d F Y H:i'),
                $fontNormal, 7, [0, 0, 0]
            );

            $canvas->page_text(
                $w / 2 - 40, $h - 16,
                "Halaman {PAGE_NUM} dari {PAGE_COUNT}",
                $fontNormal, 7, [0, 0, 0]
            );

            $canvas->page_text(
                $w - 80, $h - 16,
                "Sponsor Resmi",
                $fontNormal, 7, [0, 0, 0]
            );

            return $dompdf->stream('buku-acara.pdf');
        } catch (\Throwable $th) {
            return $this->error(substr($th->getMessage(),0,150));
        }
    }

    public function bukuHasil(Request $req){
        try {
            Carbon::setLocale('id');
            $hariText = [
                1 => 'Pertama',
                2 => 'Kedua',
                3 => 'Ketiga',
                4 => 'Keempat',
                5 => 'Kelima',
            ];

            $fDate = $req->comp_date;
            $idKompetisi = $req->comp_id;
            $orderSesi = CompetitionSession::where('competition_id',$idKompetisi)
                        ->select('session_date')
                        ->distinct()
                        ->orderBy('session_date')
                        ->get()
                        ->mapWithKeys(function($item, $index) use ($hariText){
                            $date = Carbon::parse($item->session_date);
                            return [
                                $item->session_date => 'Hari ' . ($hariText[$index + 1] ?? $index + 1) . ', ' . $date->translatedFormat('l d F Y'),
                            ];
                        })
                        ->toArray();
            $hariLabel = $orderSesi[$req->comp_date];

            $ageGroups = AgeGroup::all();
            $data = CompetitionHeatLane::query()
                    ->from('competition_heat_lanes as chl')
                    ->select(
                        'at.name as nama_atlet',
                        'at.gender as atlet_gender',
                        'at.bod',
                        'ag.label as kelompok_umur',
                        'cl.club_name',
                        'cl.club_city',
                        'ch.round_type',
                        'cen.seed_time as best_time',
                        'chl.swim_time as hasil',
                        'ce.id as competition_event_id',
                        'ce.event_number',
                        'ce.gender as event_gender',
                        'ce.distance',
                        'ce.stroke',
                        'ce.event_type',
                        'ce.max_relay_athletes',
                        'ce.equipment',
                        'cs.name',
                        'cs.session_date',
                        'comp.id',
                        'comp.name as nama_meet',
                        'comp.start_date'
                    )
                    ->leftjoin('competition_entries as cen', 'cen.id', '=', 'chl.competition_entry_id')
                    ->leftjoin('athletes as at', 'at.id', '=', 'cen.athlete_id')
                    ->leftjoin('clubs as cl', 'cl.id', '=', 'at.club_id')
                    ->leftjoin('competition_heats as ch', 'ch.id', '=', 'chl.competition_heat_id')
                    ->leftjoin('competition_events as ce', 'ce.id', '=', 'ch.competition_event_id')
                    ->leftjoin('age_groups as ag', 'ag.id', '=', 'ce.age_group_id')
                    ->leftjoin('competition_sessions as cs', 'cs.id', '=', 'ce.competition_session_id')
                    ->leftjoin('competitions as comp', 'comp.id', '=', 'cs.competition_id')
                    ->where('chl.status', 'valid')
                    ->where('cs.session_date', $fDate)
                    ->where('comp.id', $idKompetisi)
                    ->get();

            if($data->isEmpty()){
                return $this->empty('Hasil belum tersedia');
            }

            $mulaiKompetisi = Carbon::parse($data->first()->start_date);
            $roundOrder = [
                RoundTypeEnum::prelim->value => 1,
                RoundTypeEnum::semi->value => 2,
                RoundTypeEnum::final->value => 3,
            ];
            $events = $data->groupBy('competition_event_id')->map(function($item) use ($ageGroups, $mulaiKompetisi, $roundOrder){
                $first = $item->first();
                $genderEvFull = match($first->event_gender){
                    Gender::pria->value => 'PUTRA',
                    Gender::wanita->value => 'PUTRI',
                    default => 'CAMPURAN'
                };
                $genderEv = ($genderEvFull === 'PUTRA' ? 'PA' : ($genderEvFull === 'PUTRI' ? 'PI' : 'CAMPURAN'));
                $strokeEv = Stroke::from($first->stroke)->label();
                $isRelayEv = $first->event_type === EventType::estafet->value;
                $distEv = ($isRelayEv && $first->max_relay_athletes) ? ($first->max_relay_athletes . 'x' . $first->distance) : $first->distance;
                $equipEv = $first->equipment ? ' ' . ucfirst($first->equipment) : '';
                return [
                    'event_label' => 'EVENT ' . $first->event_number . ' ' . $genderEv . ' - ' . $distEv . ' M ' . $strokeEv . $equipEv,
                    'category_label' => 'KU ' . $first->kelompok_umur . ' ' . $genderEvFull,
                    'line_label' => 'RANK',
                    'results' => $item->groupBy('round_type')->map(function ($entryByRound, $roundType) use ($ageGroups, $mulaiKompetisi){
                        return [
                            'name' => $roundType,
                            'label' => RoundTypeEnum::from($roundType)->label(),
                            'data' =>  $entryByRound->sortBy(fn($atlet) => $this->swimTimeToCs($atlet->hasil))
                                ->values()
                                ->map(function($row, $index) use ($ageGroups, $mulaiKompetisi){
                                    $atletGender = Gender::pria->value === $row->atlet_gender ? 'PA' : 'PI';
                                    $bod = Carbon::parse($row->bod);
                                    $usia = $bod->diff($mulaiKompetisi)->y;
                                    $yob = $bod->format('Y');
                                    $kuAtlet = $ageGroups->first(function ($ag) use ($usia) {
                                        return !is_null($ag->min_age) &&
                                            !is_null($ag->max_age) &&
                                            $ag->min_age <= $usia &&
                                            $ag->max_age >= $usia;
                                    });

                                    if(!$kuAtlet){
                                        $kuAtlet = $ageGroups
                                            ->first(fn($ag) => is_null($ag->min_age) && is_null($ag->max_age));
                                    }

                                    return [
                                        'rank' => $index+1,
                                        'nama' => $row->nama_atlet,
                                        'papi' => $atletGender,
                                        'yob' => $yob,
                                        'age' => $kuAtlet?->label,
                                        'club' => $row->club_name,
                                        'kota' => $row->club_city,
                                        'best_time' => $row->best_time ?? 'NT',
                                        'hasil' => $row->hasil,
                                    ];
                                })->toArray(),
                        ];
                    })
                    ->sortBy(fn($round) => $roundOrder[$round['name']] ?? 999)
                    ->values()
                    ->toArray(),
                ];
            })->values()->toArray();

            $pdf = Pdf::loadView('pages.export_doc.buku_hasil', compact('events', 'hariLabel'));

            $pdf->output();
            $dompdf = $pdf->getDomPDF();
            $canvas = $dompdf->getCanvas();
            $font   = $dompdf->getFontMetrics()->getFont("Arial", "normal");

            $canvas->page_text(
                270,  // X
                820,   // Y (dari atas)
                "Halaman {PAGE_NUM} dari {PAGE_COUNT}",
                $font,
                8,    // font size
                [0, 0, 0] // warna RGB
            );

            return $dompdf->stream('buku-hasil.pdf', ['Attachment' => true]);
        } catch (\Throwable $th) {
            return $this->error(substr($th->getMessage(),0,150));
        }
    }

    public function bestClub(Request $req){
        try {
            $item = Competition::find($req->competition_id);
            if(!$item) return $this->notFound('Kompetisi tidak terdaftar');

            Carbon::setLocale('id');
            $mulaiKompetisi = Carbon::parse($item->start_date);
            $akhirKompetisi = Carbon::parse($item->end_date);

            if ($mulaiKompetisi->isSameDay($akhirKompetisi)) {
                $tanggal = $mulaiKompetisi->translatedFormat('l, d F Y');
            } elseif ($mulaiKompetisi->isSameMonth($akhirKompetisi)) {
                $tanggal = $mulaiKompetisi->translatedFormat('d') . ' – ' . $akhirKompetisi->translatedFormat('d F Y');
            } else {
                $tanggal = $mulaiKompetisi->translatedFormat('d F') . ' – ' . $akhirKompetisi->translatedFormat('d F Y');
            }

            $rekapMedali = DB::table('competition_teams as a')
                ->leftjoin('clubs as b', 'b.id', '=', 'a.team_id')
                ->leftJoin('final_results as c', 'c.competition_team_id', '=', 'a.id')
                ->selectRaw('
                    b.club_code,
                    b.club_name,
                    SUM(CASE WHEN c.rank_in_event = 1 THEN 1 ELSE 0 END) as gold,
                    SUM(CASE WHEN c.rank_in_event = 2 THEN 1 ELSE 0 END) as silver,
                    SUM(CASE WHEN c.rank_in_event = 3 THEN 1 ELSE 0 END) as bronze,
                    SUM(CASE WHEN c.rank_in_event IN (1,2,3) THEN 1 ELSE 0 END) as total_medali
                ')
                ->where('a.competition_id', $req->competition_id)
                ->where('a.status', CompetitionTeamStatus::from('active')->value)
                ->whereNull('a.deleted_at')
                ->groupBy('a.id', 'b.club_code', 'b.club_name')
                ->orderByDesc('gold')
                ->orderByDesc('silver')
                ->orderByDesc('bronze')
                ->get();

            $ranked  = [];
            $items   = $rekapMedali->values();

            foreach ($items as $index => $row) {
                $prev   = $index > 0 ? $ranked[$index - 1] : null;
                $posisi = $index + 1;

                if ($prev &&
                    (int) $row->gold   === $prev['emas'] &&
                    (int) $row->silver === $prev['perak'] &&
                    (int) $row->bronze === $prev['perunggu']
                ) {
                    $posisi = $prev['posisi']; // ties → posisi sama
                }

                $ranked[] = [
                    'posisi'   => $posisi,
                    'nama_tim' => strtoupper($row->club_name ?? '-'),
                    'emas'     => (int) $row->gold,
                    'perak'    => (int) $row->silver,
                    'perunggu' => (int) $row->bronze,
                ];
            }

            $data = [
                'namaEvent' => strtoupper($item?->name ?? 'Kompetisi -'),
                'tanggal'   => strtoupper($tanggal),
                'venue'     => ($item?->venue?->name ?? '-') . ', ' . ($item?->venue?->city ?? '-'),

                'logoKiri'  => [
                    public_path('assets/logo-sumbar.png'),
                    public_path('assets/logo-kota.png'),
                ],
                'logoKanan' => [
                    public_path('assets/akuatik-indonesia-seeklogo.png'),
                ],

                'rekapMedali' => collect($ranked)
            ];


            // =============================================
            // GENERATE PDF
            // =============================================
            $pdf = Pdf::loadView('pages.export_doc.best_club', $data)
                ->setPaper('a4', 'portrait');

            // Render dulu agar canvas tersedia
            $pdf->render();

            $dompdf = $pdf->getDomPDF();
            $canvas = $dompdf->getCanvas();
            $font   = $dompdf->getFontMetrics()->getFont("Arial", "normal");

            $w = $canvas->get_width();
            $h = $canvas->get_height();

            // Footer kiri: tanggal cetak
            $canvas->page_text(
                28, $h - 16,
                "Dicetak " . Carbon::now()->translatedFormat('l d F Y H:i'),
                $font, 7, [0, 0, 0]
            );

            // Footer tengah: nomor halaman
            $canvas->page_text(
                $w / 2 - 40, $h - 16,
                "Halaman {PAGE_NUM} dari {PAGE_COUNT}",
                $font, 7, [0, 0, 0]
            );

            // Footer kanan
            $canvas->page_text(
                $w - 80, $h - 16,
                "Sponsor Resmi",
                $font, 7, [0, 0, 0]
            );

            return $dompdf->stream('best-club.pdf');
        } catch (\Throwable $th) {
            return $this->error(substr($th->getMessage(),0,150));
        }
    }

    public function bestSwimmer(Request $req){
        try {
            Carbon::setLocale('id');
            $validators = Validator::make($req->all(), [
                'competition_id' => 'required|exists:competitions,id',
            ]);
            if($validators->fails()) {
                return $this->notFound('Kompetisi tidak terdaftar');
            }

            $competition = Competition::find($req->competition_id ?? 1);
            $bestAtlet = DB::table(function ($sub) use ($req) {
                $sub->from('final_results as a')
                    ->selectRaw('
                        a.athlete_id,
                        b.gender,
                        b.age_group_id,
                        SUM(CASE WHEN a.rank_in_event = 1 THEN 1 ELSE 0 END) as emas,
                        SUM(CASE WHEN a.rank_in_event = 2 THEN 1 ELSE 0 END) as perak,
                        SUM(CASE WHEN a.rank_in_event = 3 THEN 1 ELSE 0 END) as perunggu
                    ')
                    ->leftJoin('competition_events as b', 'b.id', '=', 'a.competition_event_id')
                    ->where('a.round_type', RoundTypeEnum::final->value)
                    ->where('a.competition_id', $req->competition_id ?? 1)
                    ->whereNotNull('a.athlete_id')
                    ->groupBy('a.athlete_id', 'b.gender', 'b.age_group_id');
            }, 'agg')
            ->select(
                'agg.*',
                'c.label as age_group_label',
                'd.name as athlete_name',
                'd.code as athlete_code',
                'e.club_name'
            )
            ->leftJoin('age_groups as c', 'c.id', '=', 'agg.age_group_id')
            ->leftJoin('athletes as d', 'd.id', '=', 'agg.athlete_id')
            ->leftJoin('clubs as e', 'e.id', '=', 'd.club_id')
            ->orderBy('agg.age_group_id')
            ->orderBy('agg.gender')
            ->orderByDesc('agg.emas')
            ->orderByDesc('agg.perak')
            ->orderByDesc('agg.perunggu')
            ->get();

            $atletTerbaik = $bestAtlet
                ->groupBy(fn($item) => $item->age_group_id . '|' . $item->gender)
                ->map(function($group) {
                    $best = $group->first();
                    $topMedal = [$best->emas, $best->perak, $best->perunggu];

                    return $group
                        ->filter(fn($row) => [$row->emas, $row->perak, $row->perunggu] === $topMedal)
                        ->map(fn($row) => [
                            'pos'      => 1,
                            'id'       => $row->athlete_code ?? '-',
                            'nama'     => $row->athlete_name ?? '-',
                            'sex'      => Gender::tryFrom($row->gender)?->label() ?? '-',
                            'ku'       => $row->age_group_label ?? '-',
                            'tim'      => $row->club_name ?? '-',
                            'emas'     => $row->emas,
                            'perak'    => $row->perak,
                            'perunggu' => $row->perunggu,
                            // 'poin'     => ($row->emas * 3) + ($row->perak * 2) + ($row->perunggu * 1),
                            'poin'     => 0,
                        ]);
                })
                ->flatten(1)
                ->values()
                ->toArray();

            $mulaiKompetisi = Carbon::parse($competition->start_date);
            $akhirKompetisi = Carbon::parse($competition->end_date);

            if ($mulaiKompetisi->isSameDay($akhirKompetisi)) {
                $tanggal = $mulaiKompetisi->translatedFormat('l, d F Y');
            } elseif ($mulaiKompetisi->isSameMonth($akhirKompetisi)) {
                $tanggal = $mulaiKompetisi->translatedFormat('d') . ' – ' . $akhirKompetisi->translatedFormat('d F Y');
            } else {
                $tanggal = $mulaiKompetisi->translatedFormat('d F') . ' – ' . $akhirKompetisi->translatedFormat('d F Y');
            }

            $data = [
                'namaEvent' => strtoupper($competition?->name ?? 'Kompetisi -'),
                'tanggal'   => strtoupper($tanggal),
                'venue'     => ($competition?->venue?->name ?? '-') . ', ' . ($competition?->venue?->city ?? '-'),

                'logoKiri' => [
                    public_path('assets/logo-sumbar.png'),
                    public_path('assets/logo-kota.png'),
                ],
                'logoKanan' => [
                    public_path('assets/akuatik-indonesia-seeklogo.png'),
                ],

                'atletTerbaik' => $atletTerbaik,
            ];

            $pdf = Pdf::loadView('pages.export_doc.best_swimmer', $data)
                ->setPaper('a4', 'landscape');

            $pdf->render();

            $dompdf = $pdf->getDomPDF();
            $canvas  = $dompdf->getCanvas();
            $font    = $dompdf->getFontMetrics()->getFont("Arial", "normal");

            $w = $canvas->get_width();
            $h = $canvas->get_height();

            // Kiri: tanggal cetak
            $canvas->page_text(
                28, $h - 16,
                "Dicetak " . Carbon::now()->translatedFormat('l d F Y H:i'),
                $font, 7, [0, 0, 0]
            );

            // Kanan: nomor halaman
            $canvas->page_text(
                $w - 85, $h - 16,
                "Halaman {PAGE_NUM}/{PAGE_COUNT}",
                $font, 7, [0, 0, 0]
            );

            // Bawah tengah: sponsor
            $canvas->page_text(
                $w / 2 - 30, $h - 10,
                "Sponsor Resmi",
                $font, 7, [0, 0, 0]
            );

            return $dompdf->stream('atlet-terbaik.pdf');
        } catch (\Throwable $th) {
            return $this->error(substr($th->getMessage(),0,150));
        }
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
}
