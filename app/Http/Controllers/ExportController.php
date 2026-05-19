<?php

namespace App\Http\Controllers;

use App\Enums\EventType;
use App\Enums\RoundTypeEnum;
use App\Enums\Gender;
use App\Enums\Stroke;
use App\Exports\StartingListExport;
use App\Models\Competition;
use App\Models\CompetitionTeam;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function undanganKejurda(){

    }
    public function startingList(Request $request){
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
                    'label' => $e->distance . ' m',               // contoh: "50M", "100M"
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
                    'label' => $e->distance . ' m',
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
    }
    public function bukuAcara(Request $request){
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
                    'sesi' => $item->sessions->map(function ($sesi) use ($akhirKompetisi) {
                        return [
                            'nama' => $sesi->name ?? '-',
                            'acara' => $sesi->competitionEvents
                                // ->groupBy(fn($e) => $e->distance .'|'. $e->stroke . '|' . $e->age_group_id)
                                ->groupBy(fn($e) => $e->distance .'|'. $e->stroke . '|' . $e->event_type)
                                ->map(function($events){
                                    $first = $events->first();
                                    return [
                                        'nomor' => $first->distance . ' M ' . Stroke::tryFrom($first->stroke)->label() ?? '-',
                                        'tipe_event' => EventType::tryFrom($first->event_type)->label() ?? '-',
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
            $jadwalHari = [
                $item->sessions->groupBy('session_date')->map(function($byDate){
                    $tglSesi = Carbon::parse($byDate->first()->session_date);
                    return [
                        'label' => $tglSesi->translatedFormat('l, d F Y'),
                        'sesi' => $byDate->map(function ($sesi) {
                            return [
                                'nama' => $sesi->name ?? '-',
                                'acara' => $sesi->competitionEvents
                                    // ->groupBy(fn($e) => $e->distance .'|'. $e->stroke . '|' . $e->age_group_id)
                                    ->groupBy(fn($e) => $e->distance .'|'. $e->stroke . '|' . $e->event_type)
                                    ->map(function($events){
                                        $first = $events->first();
                                        return [
                                            'nomor' => $first->distance . ' M ' . Stroke::tryFrom($first->stroke)->label() ?? '-',
                                            'tipe_event' => EventType::tryFrom($first->event_type)->label() ?? '-',
                                            'ku_list' => $events->groupBy('age_group_id')->map(function ($eByKu){
                                                $noPa = $eByKu->where('gender', Gender::pria->value)->value('event_number');
                                                $noPi = $eByKu->where('gender', Gender::wanita->value)->value('event_number');
                                                return [
                                                    'pa' => $noPa,
                                                    'ku' => $eByKu->first()?->ageGroup?->label ?? '-',
                                                    'pi' => $noPi
                                                ];
                                            })->values()->toArray(),
                                        ];
                                })->values()->toArray(),
                            ];
                        })->values()->toArray(),
                    ];
                })->toArray(),
            ];
        }

        // list acara dan entry
        $acaraList = $item->events->sortBy('event_number')->map(function($e){
            $roundAwal = count($e->configs) > 1 ? $e->configs?->where('order', 1)->first()?->round_type : $e->configs?->first()?->round_type;
            // $usedLanes = count($e->configs) > 1 ? $e->configs?->where('order', 1)->first()?->used_lanes : $e->configs?->first()?->used_lanes;
            $totalLanes = $e->competitionSession?->pool?->total_lanes ?? '8';
            return [
                'nomor' => $e?->event_number,
                'nama' => ($e?->distance ?? '-') . ' M ' . Stroke::tryFrom($e->stroke)->label() ?? '-',
                'tanggal' => Carbon::parse($e->competitionSession?->session_date)->translatedFormat('l, d F Y') . ' — ' . ($e?->competitionSession?->name ?? '-'),
                'status' => $roundAwal ? RoundTypeEnum::from($roundAwal)->label() : '-',
                'limit'  => 'NO LIMIT ',
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
                            'id_lomba'  => '',
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
                public_path('images/logo-dispora.png'),
                public_path('images/logo-prsi-batam.png'),
            ],
            'logoKanan' => [
                public_path('images/logo-sponsor-a.png'),
                public_path('images/logo-sponsor-b.png'),
            ],

            'jadwalHari' => $jadwalHari,
            'acaraList' => $acaraList,
        ];
        // return $data;

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
            // Definisikan font DI DALAM closure
            $fontBold   = $fontMetrics->getFont("Arial", "bolder");
            $fontNormal = $fontMetrics->getFont("Arial", "normal");

            $canvas->text($w / 2 - 120, 18, $data['namaEvent'],  $fontBold,   9,   [0, 0, 0]);
            $canvas->text($w / 2 - 80,  32, $data['venue'],       $fontNormal, 7.5, [0, 0, 0]);
            $canvas->text($w / 2 - 60,  44, $data['tanggal'],     $fontBold,   7.5, [0, 0, 0]);
            $canvas->text($w / 2 - 35,  56, "BUKU ACARA",         $fontBold,   14,  [0, 0, 0]);
            $canvas->line(28, 72, $w - 28, 72, [0, 0, 0], 0.5);
        });

        // FOOTER — gunakan $fontNormal bukan $font
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

        // return $dompdf->stream('buku-acara.pdf', ['Attachment' => false]);
        return $dompdf->stream('buku-acara.pdf');
    }

    public function bukuHasil(){
        $events =[
            [
                'event_label'    => 'EVENT 101 PA -1500 M GAYA BEBAS',
                'category_label' => 'KU UMUM PUTRA',
                'line_label'     => 'RANK',
                'results'        => [
                    ['rank'=>1,  'nama'=>'SARBO',                    'papi'=>'PA','yob'=>2008,'age'=>'SENIOR','club'=>'MERLIN SC',       'kota'=>'PAD. PARIAMAN',  'best_time'=>'18.06.63','hasil'=>'19.19.39'],
                    ['rank'=>2,  'nama'=>'MUHAMMAD SIDDIQ',          'papi'=>'PA','yob'=>2011,'age'=>'2',     'club'=>'WARRIOR SC',      'kota'=>'BUKITTINGGI',    'best_time'=>'19.70.47','hasil'=>'19.54.20'],
                    ['rank'=>3,  'nama'=>'DARVESH EVAN PUTRA HUDA',  'papi'=>'PA','yob'=>2010,'age'=>'SENIOR','club'=>'SeaRIA AQUATIC',  'kota'=>'PADANG',         'best_time'=>'20.29.83','hasil'=>'20.24.17'],
                    ['rank'=>4,  'nama'=>'GILBERT HADIWARSA',        'papi'=>'PA','yob'=>2007,'age'=>'SENIOR','club'=>'WSC',             'kota'=>'PADANG',         'best_time'=>'20.07.00','hasil'=>'20.26.35'],
                    ['rank'=>5,  'nama'=>'QUSYAIRI MUAMMAR GIBRAN',  'papi'=>'PA','yob'=>2010,'age'=>'SENIOR','club'=>'SeaRIA AQUATIC',  'kota'=>'PADANG',         'best_time'=>'20.37.53','hasil'=>'21.11.70'],
                    ['rank'=>6,  'nama'=>'ROFIL',                    'papi'=>'PA','yob'=>2008,'age'=>'SENIOR','club'=>'ATHA.SC',         'kota'=>'SOLOK',          'best_time'=>'20.33.35','hasil'=>'21.32.03'],
                    ['rank'=>7,  'nama'=>'LUTTFY ARAASYID',          'papi'=>'PA','yob'=>2012,'age'=>'2',     'club'=>'SAMUDERA AC',     'kota'=>'50 KOTA',        'best_time'=>'22.09.89','hasil'=>'21.52.98'],
                    ['rank'=>8,  'nama'=>'ARYA PUTRA MULYA ANDESTO', 'papi'=>'PA','yob'=>2012,'age'=>'2',     'club'=>'BUILD SC',        'kota'=>'PADANG',         'best_time'=>'99.99.99','hasil'=>'22.31.95'],
                    ['rank'=>9,  'nama'=>'ALTHAF YAKSAN HAFIZ',      'papi'=>'PA','yob'=>2012,'age'=>'2',     'club'=>'DIAMOND SC',      'kota'=>'PADANG',         'best_time'=>'99.99.99','hasil'=>'22.43.61'],
                    ['rank'=>10, 'nama'=>'ARIEF RAZIQ AZHAR',        'papi'=>'PA','yob'=>2012,'age'=>'2',     'club'=>'DIAMOND SC',      'kota'=>'PADANG',         'best_time'=>'99.99.99','hasil'=>'22.46.96'],
                    ['rank'=>11, 'nama'=>'JULES CLOVIS VIGNE',       'papi'=>'PA','yob'=>2010,'age'=>'SENIOR','club'=>'GSC',             'kota'=>'PADANG',         'best_time'=>'21.50.45','hasil'=>'22.50.78'],
                    ['rank'=>12, 'nama'=>'MALIK MANDHALA',           'papi'=>'PA','yob'=>2011,'age'=>'2',     'club'=>'FITT SC',         'kota'=>'PADANG',         'best_time'=>'99.99.99','hasil'=>'23.59.15'],
                    ['rank'=>13, 'nama'=>'RIZQY SERCIO PRATAMA',     'papi'=>'PA','yob'=>2011,'age'=>'2',     'club'=>'HANNA SC',        'kota'=>'SOLOK SELATAN',  'best_time'=>'99.99.99','hasil'=>'24.01.47'],
                ],
            ],
            [
                'event_label'    => 'EVENT 104 PI -800 M GAYA BEBAS',
                'category_label' => 'KU UMUM PUTRI',
                'line_label'     => 'RANK',
                'results'        => [
                    ['rank'=>1,  'nama'=>'AUREL DONITA',                   'papi'=>'PI','yob'=>2009,'age'=>'SENIOR','club'=>'UNNATTACED',      'kota'=>'DHARMASRAYA',    'best_time'=>'10.59.18','hasil'=>'11.27.27'],
                    ['rank'=>2,  'nama'=>'RAYSA RAZELI N AL HABI',         'papi'=>'PI','yob'=>2013,'age'=>'3',     'club'=>'NSC',             'kota'=>'PAYAKUMBUH',     'best_time'=>'11.30.24','hasil'=>'11.31.61'],
                    ['rank'=>3,  'nama'=>'ZAHRA TUNISA',                   'papi'=>'PI','yob'=>2012,'age'=>'2',     'club'=>'ATHA.SC',         'kota'=>'SOLOK',          'best_time'=>'12.00.00','hasil'=>'12.01.02'],
                    ['rank'=>4,  'nama'=>'TALITA RAISSA',                  'papi'=>'PI','yob'=>2004,'age'=>'SENIOR','club'=>'MERLIN SC',       'kota'=>'PAD. PARIAMAN',  'best_time'=>'11.00.00','hasil'=>'12.09.27'],
                    ['rank'=>5,  'nama'=>'ALYA GHASSANI',                  'papi'=>'PI','yob'=>2012,'age'=>'2',     'club'=>'BUILD SC',        'kota'=>'PADANG',         'best_time'=>'12.30.49','hasil'=>'12.40.02'],
                    ['rank'=>6,  'nama'=>'DEANI JEHAN FATHIHA',            'papi'=>'PI','yob'=>2011,'age'=>'2',     'club'=>'HANNA SC',        'kota'=>'SOLOK SELATAN',  'best_time'=>'12.55.23','hasil'=>'12.46.83'],
                    ['rank'=>7,  'nama'=>'ARSHA FATIYA RAHMAH',            'papi'=>'PI','yob'=>2012,'age'=>'2',     'club'=>'GSC',             'kota'=>'PADANG',         'best_time'=>'12.54.94','hasil'=>'12.52.34'],
                    ['rank'=>8,  'nama'=>'LETICIA MIKAYLA AWAN JUVENTINI', 'papi'=>'PI','yob'=>2013,'age'=>'3',     'club'=>'SeaRIA AQUATIC',  'kota'=>'PADANG',         'best_time'=>'13.25.00','hasil'=>'13.53.05'],
                    ['rank'=>9,  'nama'=>'FATHIYAH FIFTA SYAKIRA',         'papi'=>'PI','yob'=>2016,'age'=>'4',     'club'=>'FITT SC',         'kota'=>'PADANG',         'best_time'=>'99.99.99','hasil'=>'14.00.56'],
                    ['rank'=>10, 'nama'=>'NEYSHA AZWA KIRANA',             'papi'=>'PI','yob'=>2011,'age'=>'2',     'club'=>'SeaRIA AQUATIC',  'kota'=>'PADANG',         'best_time'=>'16.00.00','hasil'=>'14.41.10'],
                    ['rank'=>11, 'nama'=>'ALYA NASYITRA ADELINA WIJAYA',   'papi'=>'PI','yob'=>2014,'age'=>'3',     'club'=>'SAILFISH SC',     'kota'=>'PARIAMAN',       'best_time'=>'99.99.99','hasil'=>'14.47.67'],
                    ['rank'=>12, 'nama'=>'SHAFA KHAFRIZA',                 'papi'=>'PI','yob'=>2011,'age'=>'2',     'club'=>'DIAMOND SC',      'kota'=>'PADANG',         'best_time'=>'99.99.99','hasil'=>'15.27.82'],
                ],
            ],
            // -----------------------------------------------------------------
            // EVENT 105 — PA 100 M GAYA DADA — KU 6 PUTRA
            // -----------------------------------------------------------------
            [
                'event_label'    => 'EVENT 105 PA -100 M GAYA DADA',
                'category_label' => 'KU 6 PUTRA',
                'line_label'     => 'LINE',
                'results'        => [
                    ['rank'=>1,'nama'=>'ANDRIEL SIMANULLANG',  'papi'=>'PA','yob'=>2019,'age'=>'6','club'=>'NSC',           'kota'=>'PAYAKUMBUH','best_time'=>'01.50.00','hasil'=>'02.06.81'],
                    ['rank'=>2,'nama'=>'ADITAMA DERMAWAN',     'papi'=>'PA','yob'=>2019,'age'=>'6','club'=>'GSC',           'kota'=>'PADANG',    'best_time'=>'02.12.44','hasil'=>'02.08.13'],
                    ['rank'=>3,'nama'=>'SYAUQI ARKANA VALERI', 'papi'=>'PA','yob'=>2019,'age'=>'6','club'=>'SeaRIA AQUATIC','kota'=>'PADANG',    'best_time'=>'02.20.00','hasil'=>'02.24.01'],
                    ['rank'=>4,'nama'=>'RASKA AKIO RITONGA',   'papi'=>'PA','yob'=>2019,'age'=>'6','club'=>'NSC',           'kota'=>'PAYAKUMBUH','best_time'=>'01.51.00','hasil'=>'02.25.90'],
                ],
            ],

            // -----------------------------------------------------------------
            // EVENT 105 — PA 100 M GAYA DADA — KU 5 PUTRA
            // -----------------------------------------------------------------
            [
                'event_label'    => 'EVENT 105 PA -100 M GAYA DADA',
                'category_label' => 'KU 5 PUTRA',
                'line_label'     => 'LINE',
                'results'        => [
                    ['rank'=>1, 'nama'=>'NIKI ALANKAR SHAKEEL',      'papi'=>'PA','yob'=>2018,'age'=>'5','club'=>'DIAMOND SC',    'kota'=>'PADANG',     'best_time'=>'02.01.00','hasil'=>'01.55.19'],
                    ['rank'=>2, 'nama'=>'MUHAMMAD FARHA ALGHANI',    'papi'=>'PA','yob'=>2017,'age'=>'5','club'=>'WARRIOR SC',    'kota'=>'BUKITTINGGI','best_time'=>'01.58.10','hasil'=>'01.56.03'],
                    ['rank'=>3, 'nama'=>'ABDILLAH ABQORY',           'papi'=>'PA','yob'=>2017,'age'=>'5','club'=>'DIAMOND SC',    'kota'=>'PADANG',     'best_time'=>'02.05.00','hasil'=>'01.57.66'],
                    ['rank'=>4, 'nama'=>'ARRAFIF GHIFFARI',          'papi'=>'PA','yob'=>2017,'age'=>'5','club'=>'BUILD SC',      'kota'=>'PADANG',     'best_time'=>'01.52.30','hasil'=>'01.58.95'],
                    ['rank'=>5, 'nama'=>'APRILIO GEVANO',            'papi'=>'PA','yob'=>2018,'age'=>'5','club'=>'BSSP',          'kota'=>'PAYAKUMBUH', 'best_time'=>'02.11.00','hasil'=>'02.03.05'],
                    ['rank'=>6, 'nama'=>'MUHAMMAD GAZA MAHENDRA',    'papi'=>'PA','yob'=>2018,'age'=>'5','club'=>'SeaRIA AQUATIC','kota'=>'PADANG',     'best_time'=>'02.25.88','hasil'=>'02.10.81'],
                    ['rank'=>7, 'nama'=>'ARZIKI ALKHALIFI FADHILA',  'papi'=>'PA','yob'=>2017,'age'=>'5','club'=>'BASAQUATICS',   'kota'=>'SOLOK',      'best_time'=>'99.99.99','hasil'=>'02.11.19'],
                    ['rank'=>8, 'nama'=>'BHAIKABA DAFFA ABYAN',      'papi'=>'PA','yob'=>2017,'age'=>'5','club'=>'BANTOLA',       'kota'=>'BUKITTINGGI','best_time'=>'99.99.99','hasil'=>'02.14.21'],
                    ['rank'=>9, 'nama'=>'IBRAHIM ALFARIZI',          'papi'=>'PA','yob'=>2018,'age'=>'5','club'=>'DIAMOND SC',    'kota'=>'PADANG',     'best_time'=>'02.16.00','hasil'=>'02.17.29'],
                    ['rank'=>10,'nama'=>'BAYEZID ABRIVA',            'papi'=>'PA','yob'=>2017,'age'=>'5','club'=>'FITT SC',       'kota'=>'PADANG',     'best_time'=>'02.30.91','hasil'=>'02.18.47'],
                    ['rank'=>11,'nama'=>'RAZIQ AL FAJRI',            'papi'=>'PA','yob'=>2017,'age'=>'5','club'=>'FITT SC',       'kota'=>'PADANG',     'best_time'=>'02.10.93','hasil'=>'02.20.11'],
                ],
            ],

            // -----------------------------------------------------------------
            // EVENT 105 — PA 100 M GAYA DADA — KU 4 PUTRA
            // -----------------------------------------------------------------
            [
                'event_label'    => 'EVENT 105 PA -100 M GAYA DADA',
                'category_label' => 'KU 4 PUTRA',
                'line_label'     => 'LINE',
                'results'        => [
                    ['rank'=>1, 'nama'=>'BRODERICK VINCENZO SALIM',  'papi'=>'PA','yob'=>2016,'age'=>'4','club'=>'DIAMOND SC',    'kota'=>'PADANG',    'best_time'=>'01.30.00','hasil'=>'01.32.18'],
                    ['rank'=>2, 'nama'=>'MUHAMMAD UWAIS ALQORNI',    'papi'=>'PA','yob'=>2015,'age'=>'4','club'=>'SeaRIA AQUATIC','kota'=>'PADANG',    'best_time'=>'01.40.00','hasil'=>'01.40.99'],
                    ['rank'=>3, 'nama'=>'HAZIM ZAIDAN SAKHI',        'papi'=>'PA','yob'=>2015,'age'=>'4','club'=>'BINTANG LAUT',  'kota'=>'PAYAKUMBUH','best_time'=>'01.47.10','hasil'=>'01.42.60'],
                    ['rank'=>4, 'nama'=>'ARKANANTA PILAR RANGKAIBUMI','papi'=>'PA','yob'=>2015,'age'=>'4','club'=>'GSC',          'kota'=>'PADANG',    'best_time'=>'01.41.84','hasil'=>'01.44.64'],
                    ['rank'=>5, 'nama'=>'FADHLURRAHMAN',             'papi'=>'PA','yob'=>2016,'age'=>'4','club'=>'AMELIA SC',     'kota'=>'BUKITTINGGI','best_time'=>'01.54.16','hasil'=>'01.53.98'],
                    ['rank'=>6, 'nama'=>'HADZIQ DE RICHO',           'papi'=>'PA','yob'=>2015,'age'=>'4','club'=>'BSSP',          'kota'=>'PAYAKUMBUH','best_time'=>'02.14.60','hasil'=>'01.57.50'],
                    ['rank'=>7, 'nama'=>'EXCEL MESHACH MAKMUR',      'papi'=>'PA','yob'=>2015,'age'=>'4','club'=>'DIAMOND SC',    'kota'=>'PADANG',    'best_time'=>'99.99.99','hasil'=>'02.01.21'],
                    ['rank'=>8, 'nama'=>'FAIZ ZAFRAN BAIHAQI',       'papi'=>'PA','yob'=>2015,'age'=>'4','club'=>'BULAKAN AQUATIC','kota'=>'50 KOTA',  'best_time'=>'01.47.63','hasil'=>'02.07.41'],
                    ['rank'=>9, 'nama'=>'MUHAMMAD AZZAM AL GHIFARI', 'papi'=>'PA','yob'=>2015,'age'=>'4','club'=>'SeaRIA AQUATIC','kota'=>'PADANG',    'best_time'=>'99.99.99','hasil'=>'02.17.34'],
                    ['rank'=>10,'nama'=>'ARSENIO NAUFAL',            'papi'=>'PA','yob'=>2015,'age'=>'4','club'=>'BINTANG LAUT',  'kota'=>'PAYAKUMBUH','best_time'=>'99.99.99','hasil'=>'02.19.24'],
                    ['rank'=>11,'nama'=>'AHMAD PARIS MAULANA',       'papi'=>'PA','yob'=>2016,'age'=>'4','club'=>'BSC',           'kota'=>'SIJUNJUNG', 'best_time'=>'99.99.99','hasil'=>'NF'],
                ],
            ],

            // -----------------------------------------------------------------
            // EVENT 106 — PI 100 M GAYA DADA — KU 5 PUTRI
            // -----------------------------------------------------------------
            [
                'event_label'    => 'EVENT 106 PI -100 M GAYA DADA',
                'category_label' => 'KU 5 PUTRI',
                'line_label'     => 'LINE',
                'results'        => [
                    ['rank'=>1, 'nama'=>'LILY NUR SAILA',                   'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'BANTOLA',       'kota'=>'BUKITTINGGI',   'best_time'=>'01.53.56','hasil'=>'01.49.13'],
                    ['rank'=>2, 'nama'=>'NAYYARA KEI SHANOM',               'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'SeaRIA AQUATIC','kota'=>'PESISIR SELATAN','best_time'=>'01.57.02','hasil'=>'01.57.13'],
                    ['rank'=>3, 'nama'=>'AZKIRA YUDHANTA',                  'papi'=>'PI','yob'=>2018,'age'=>'5','club'=>'DIAMOND SC',    'kota'=>'PADANG',        'best_time'=>'99.99.99','hasil'=>'02.04.69'],
                    ['rank'=>4, 'nama'=>'AYUMI FAZIA HELDI',                'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'RAKIK SC',      'kota'=>'BUKITTINGGI',   'best_time'=>'02.12.12','hasil'=>'02.05.53'],
                    ['rank'=>5, 'nama'=>'SHEENA KINARA ATHAWIDYA',          'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'SeaRIA AQUATIC','kota'=>'PADANG',        'best_time'=>'02.02.60','hasil'=>'02.05.78'],
                    ['rank'=>6, 'nama'=>'SHANUM NAVISHA ANANDYA',           'papi'=>'PI','yob'=>2018,'age'=>'5','club'=>'QSC',           'kota'=>'BUKITTINGGI',   'best_time'=>'02.08.16','hasil'=>'02.08.95'],
                    ['rank'=>7, 'nama'=>'QINARA ALMAHYRA YUNDRI',           'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'DIAMOND SC',    'kota'=>'PADANG',        'best_time'=>'01.57.00','hasil'=>'02.14.10'],
                    ['rank'=>8, 'nama'=>'NAUFALYN RABBANI HENDRI',          'papi'=>'PI','yob'=>2018,'age'=>'5','club'=>'BSSP',          'kota'=>'PAYAKUMBUH',    'best_time'=>'02.30.10','hasil'=>'02.17.05'],
                    ['rank'=>9, 'nama'=>'ARUMI SHAREEN',                    'papi'=>'PI','yob'=>2018,'age'=>'5','club'=>'SSC',           'kota'=>'SIJUNJUNG',     'best_time'=>'99.99.99','hasil'=>'02.19.68'],
                    ['rank'=>10,'nama'=>'KAYLA GUSVADELSON',                'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'RAKIK SC',      'kota'=>'BUKITTINGGI',   'best_time'=>'99.99.99','hasil'=>'02.19.83'],
                    ['rank'=>11,'nama'=>'ASHEEQA KHAYARA RAIFA',            'papi'=>'PI','yob'=>2018,'age'=>'5','club'=>'BINTANG LAUT',  'kota'=>'PAYAKUMBUH',    'best_time'=>'99.99.99','hasil'=>'02.26.73'],
                    ['rank'=>12,'nama'=>'DITA NATHANIA HALOHO',             'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'NSC',           'kota'=>'PAYAKUMBUH',    'best_time'=>'01.58.00','hasil'=>'02.27.83'],
                    ['rank'=>13,'nama'=>'NADYA TIUR IMANUELLA SIMORANGKIR', 'papi'=>'PI','yob'=>2018,'age'=>'5','club'=>'SeaRIA AQUATIC','kota'=>'PADANG',        'best_time'=>'99.99.99','hasil'=>'02.33.37'],
                    ['rank'=>14,'nama'=>'AMARA SAKHI AULIA',                'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'KSC',           'kota'=>'50 KOTA',       'best_time'=>'99.99.99','hasil'=>'NS'],
                ],
            ],
            // -----------------------------------------------------------------
            // EVENT 106 — PI 100 M GAYA DADA — KU 5 PUTRI
            // -----------------------------------------------------------------
            [
                'event_label'    => 'EVENT 106 PI -100 M GAYA DADA',
                'category_label' => 'KU 5 PUTRI',
                'line_label'     => 'LINE',
                'results'        => [
                    ['rank'=>1, 'nama'=>'LILY NUR SAILA',                   'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'BANTOLA',       'kota'=>'BUKITTINGGI',   'best_time'=>'01.53.56','hasil'=>'01.49.13'],
                    ['rank'=>2, 'nama'=>'NAYYARA KEI SHANOM',               'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'SeaRIA AQUATIC','kota'=>'PESISIR SELATAN','best_time'=>'01.57.02','hasil'=>'01.57.13'],
                    ['rank'=>3, 'nama'=>'AZKIRA YUDHANTA',                  'papi'=>'PI','yob'=>2018,'age'=>'5','club'=>'DIAMOND SC',    'kota'=>'PADANG',        'best_time'=>'99.99.99','hasil'=>'02.04.69'],
                    ['rank'=>4, 'nama'=>'AYUMI FAZIA HELDI',                'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'RAKIK SC',      'kota'=>'BUKITTINGGI',   'best_time'=>'02.12.12','hasil'=>'02.05.53'],
                    ['rank'=>5, 'nama'=>'SHEENA KINARA ATHAWIDYA',          'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'SeaRIA AQUATIC','kota'=>'PADANG',        'best_time'=>'02.02.60','hasil'=>'02.05.78'],
                    ['rank'=>6, 'nama'=>'SHANUM NAVISHA ANANDYA',           'papi'=>'PI','yob'=>2018,'age'=>'5','club'=>'QSC',           'kota'=>'BUKITTINGGI',   'best_time'=>'02.08.16','hasil'=>'02.08.95'],
                    ['rank'=>7, 'nama'=>'QINARA ALMAHYRA YUNDRI',           'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'DIAMOND SC',    'kota'=>'PADANG',        'best_time'=>'01.57.00','hasil'=>'02.14.10'],
                    ['rank'=>8, 'nama'=>'NAUFALYN RABBANI HENDRI',          'papi'=>'PI','yob'=>2018,'age'=>'5','club'=>'BSSP',          'kota'=>'PAYAKUMBUH',    'best_time'=>'02.30.10','hasil'=>'02.17.05'],
                    ['rank'=>9, 'nama'=>'ARUMI SHAREEN',                    'papi'=>'PI','yob'=>2018,'age'=>'5','club'=>'SSC',           'kota'=>'SIJUNJUNG',     'best_time'=>'99.99.99','hasil'=>'02.19.68'],
                    ['rank'=>10,'nama'=>'KAYLA GUSVADELSON',                'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'RAKIK SC',      'kota'=>'BUKITTINGGI',   'best_time'=>'99.99.99','hasil'=>'02.19.83'],
                    ['rank'=>11,'nama'=>'ASHEEQA KHAYARA RAIFA',            'papi'=>'PI','yob'=>2018,'age'=>'5','club'=>'BINTANG LAUT',  'kota'=>'PAYAKUMBUH',    'best_time'=>'99.99.99','hasil'=>'02.26.73'],
                    ['rank'=>12,'nama'=>'DITA NATHANIA HALOHO',             'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'NSC',           'kota'=>'PAYAKUMBUH',    'best_time'=>'01.58.00','hasil'=>'02.27.83'],
                    ['rank'=>13,'nama'=>'NADYA TIUR IMANUELLA SIMORANGKIR', 'papi'=>'PI','yob'=>2018,'age'=>'5','club'=>'SeaRIA AQUATIC','kota'=>'PADANG',        'best_time'=>'99.99.99','hasil'=>'02.33.37'],
                    ['rank'=>14,'nama'=>'AMARA SAKHI AULIA',                'papi'=>'PI','yob'=>2017,'age'=>'5','club'=>'KSC',           'kota'=>'50 KOTA',       'best_time'=>'99.99.99','hasil'=>'NS'],
                ],
            ],

            // -----------------------------------------------------------------
            // EVENT 106 — PI 100 M GAYA DADA — KU 4 PUTRI
            // -----------------------------------------------------------------
            [
                'event_label'    => 'EVENT 106 PI -100 M GAYA DADA',
                'category_label' => 'KU 4 PUTRI',
                'line_label'     => 'LINE',
                'results'        => [
                    ['rank'=>1, 'nama'=>'RANIA SHAZIA RAMADONNY',          'papi'=>'PI','yob'=>2015,'age'=>'4','club'=>'BSSP',           'kota'=>'PAYAKUMBUH',    'best_time'=>'01.44.09','hasil'=>'01.39.39'],
                    ['rank'=>2, 'nama'=>'VELLICIA AMEL SIMANULLANG',       'papi'=>'PI','yob'=>2016,'age'=>'4','club'=>'NSC',            'kota'=>'PAYAKUMBUH',    'best_time'=>'01.40.00','hasil'=>'01.44.10'],
                    ['rank'=>3, 'nama'=>'ADIBA SHAKILA ATMARINI',          'papi'=>'PI','yob'=>2016,'age'=>'4','club'=>'SeaRIA AQUATIC', 'kota'=>'PADANG',        'best_time'=>'01.42.81','hasil'=>'01.44.45'],
                    ['rank'=>4, 'nama'=>'WIEDY LUCHI CALLISTA',            'papi'=>'PI','yob'=>2015,'age'=>'4','club'=>'GSC',            'kota'=>'PADANG',        'best_time'=>'01.49.23','hasil'=>'01.44.49'],
                    ['rank'=>5, 'nama'=>'SHAQILLA RUMAISHA LUTHFYANDI',    'papi'=>'PI','yob'=>2015,'age'=>'4','club'=>'QSC',            'kota'=>'BUKITTINGGI',   'best_time'=>'01.52.56','hasil'=>'01.46.02'],
                    ['rank'=>6, 'nama'=>'JAZILA KAYSHA MUKHLAS',           'papi'=>'PI','yob'=>2015,'age'=>'4','club'=>'SeaRIA AQUATIC', 'kota'=>'PESISIR SELATAN','best_time'=>'01.56.39','hasil'=>'01.50.13'],
                    ['rank'=>7, 'nama'=>'CELINE CORDELIA SHADIQAH',        'papi'=>'PI','yob'=>2015,'age'=>'4','club'=>'HANNA SC',       'kota'=>'SOLOK SELATAN', 'best_time'=>'01.47.33','hasil'=>'01.50.59'],
                    ['rank'=>8, 'nama'=>'SYAHNAZ ADZKIA MUSYAFA',          'papi'=>'PI','yob'=>2015,'age'=>'4','club'=>'BANTOLA',        'kota'=>'BUKITTINGGI',   'best_time'=>'01.48.73','hasil'=>'01.50.94'],
                    ['rank'=>9, 'nama'=>'QATRUNNADA FETRINA',              'papi'=>'PI','yob'=>2016,'age'=>'4','club'=>'GSC',            'kota'=>'PADANG',        'best_time'=>'01.55.00','hasil'=>'01.52.41'],
                    ['rank'=>10,'nama'=>'EMBUN QOTRUNNADA ANDRISA',        'papi'=>'PI','yob'=>2016,'age'=>'4','club'=>'BSSP',           'kota'=>'PAYAKUMBUH',    'best_time'=>'01.53.12','hasil'=>'01.52.88'],
                    ['rank'=>11,'nama'=>'ALIKA MARWA PUTRI ADINDA',        'papi'=>'PI','yob'=>2016,'age'=>'4','club'=>'NSC',            'kota'=>'PAYAKUMBUH',    'best_time'=>'01.47.00','hasil'=>'01.54.23'],
                    ['rank'=>12,'nama'=>'FAYRUZ CHALISA',                  'papi'=>'PI','yob'=>2015,'age'=>'4','club'=>'BSC',            'kota'=>'BUKITTINGGI',   'best_time'=>'99.99.99','hasil'=>'01.54.36'],
                    ['rank'=>13,'nama'=>'CALLYSTA TWELVERINE SIRINGO RINGO','papi'=>'PI','yob'=>2016,'age'=>'4','club'=>'DIAMOND SC',    'kota'=>'PADANG',        'best_time'=>'01.46.00','hasil'=>'01.54.95'],
                    ['rank'=>14,'nama'=>'SAUFIQA KANZA APRILIA',           'papi'=>'PI','yob'=>2016,'age'=>'4','club'=>'SeaRIA AQUATIC', 'kota'=>'PESISIR SELATAN','best_time'=>'01.56.03','hasil'=>'01.56.59'],
                    ['rank'=>15,'nama'=>'FATHIYAH FIFTA SYAKIRA',          'papi'=>'PI','yob'=>2016,'age'=>'4','club'=>'FITT SC',        'kota'=>'PADANG',        'best_time'=>'01.42.04','hasil'=>'01.57.48'],
                    ['rank'=>16,'nama'=>'PUTRI ZAMBAQHATUL JANNAH ABIDAH', 'papi'=>'PI','yob'=>2015,'age'=>'4','club'=>'QSC',            'kota'=>'BUKITTINGGI',   'best_time'=>'01.54.12','hasil'=>'01.57.76'],
                    ['rank'=>17,'nama'=>'SAILA KENZHA',                    'papi'=>'PI','yob'=>2015,'age'=>'4','club'=>'SSC',            'kota'=>'SIJUNJUNG',     'best_time'=>'99.99.99','hasil'=>'01.58.33'],
                    ['rank'=>18,'nama'=>'ZALFFA NAQIYA',                   'papi'=>'PI','yob'=>2016,'age'=>'4','club'=>'BSC',            'kota'=>'SIJUNJUNG',     'best_time'=>'02.02.99','hasil'=>'02.04.76'],
                    ['rank'=>19,'nama'=>'FAWNIA NADIVA SYAKIRA',           'papi'=>'PI','yob'=>2015,'age'=>'4','club'=>'BSC',            'kota'=>'SIJUNJUNG',     'best_time'=>'01.59.33','hasil'=>'02.05.36'],
                    ['rank'=>20,'nama'=>'ANISSA NAWANG SHAUGI',            'papi'=>'PI','yob'=>2016,'age'=>'4','club'=>'BULAKAN AQUATIC','kota'=>'50 KOTA',       'best_time'=>'01.55.65','hasil'=>'02.07.52'],
                    ['rank'=>21,'nama'=>'SITI AISYAH ENDO',                'papi'=>'PI','yob'=>2015,'age'=>'4','club'=>'BSSP',           'kota'=>'PAYAKUMBUH',    'best_time'=>'02.16.00','hasil'=>'02.08.98'],
                    ['rank'=>22,'nama'=>'SEANA GYOVE INARA',               'papi'=>'PI','yob'=>2015,'age'=>'4','club'=>'BSC',            'kota'=>'BUKITTINGGI',   'best_time'=>'02.14.22','hasil'=>'02.12.27'],
                ],
            ],

            // -----------------------------------------------------------------
            // EVENT 107 — PA 100 M GAYA DADA — KU 3 PUTRA
            // -----------------------------------------------------------------
            [
                'event_label'    => 'EVENT 107 PA -100 M GAYA DADA',
                'category_label' => 'KU 3 PUTRA',
                'line_label'     => 'LINE',
                'results'        => [
                    ['rank'=>1, 'nama'=>'ISRAQ HAYAT',                         'papi'=>'PA','yob'=>2013,'age'=>'3','club'=>'WSC',          'kota'=>'PADANG',    'best_time'=>'01.23.59','hasil'=>'01.22.31'],
                    ['rank'=>2, 'nama'=>'AKDY PRADANA HUTASUHUT',              'papi'=>'PA','yob'=>2013,'age'=>'3','club'=>'BINTANG LAUT', 'kota'=>'PAYAKUMBUH','best_time'=>'01.30.95','hasil'=>'01.30.49'],
                    ['rank'=>3, 'nama'=>'AIRLANGGA SATRIA PRAMANA',            'papi'=>'PA','yob'=>2013,'age'=>'3','club'=>'DIAMOND SC',   'kota'=>'PADANG',    'best_time'=>'01.29.00','hasil'=>'01.33.30'],
                    ['rank'=>4, 'nama'=>'ARDI KASYAFANI GHAFUR',               'papi'=>'PA','yob'=>2013,'age'=>'3','club'=>'FITT SC',      'kota'=>'PADANG',    'best_time'=>'01.38.09','hasil'=>'01.35.11'],
                    ['rank'=>5, 'nama'=>'HABIBIE ASHRAF SALIM',                'papi'=>'PA','yob'=>2014,'age'=>'3','club'=>'GSC',          'kota'=>'PADANG',    'best_time'=>'01.34.27','hasil'=>'01.38.47'],
                    ['rank'=>6, 'nama'=>'ARKAN SAID RAMADHAN',                 'papi'=>'PA','yob'=>2014,'age'=>'3','club'=>'GSC',          'kota'=>'PADANG',    'best_time'=>'01.37.75','hasil'=>'01.40.88'],
                    ['rank'=>7, 'nama'=>'DZAKA FAUTO AULIANDA',                'papi'=>'PA','yob'=>2014,'age'=>'3','club'=>'WARRIOR SC',   'kota'=>'BUKITTINGGI','best_time'=>'01.53.83','hasil'=>'01.54.73'],
                    ['rank'=>8, 'nama'=>'MICHAEL ZAMA ZAIN',                   'papi'=>'PA','yob'=>2014,'age'=>'3','club'=>'BSSP',         'kota'=>'PAYAKUMBUH','best_time'=>'02.21.00','hasil'=>'01.57.12'],
                    ['rank'=>9, 'nama'=>'ARQAN ALFAEYZA FADHILA',              'papi'=>'PA','yob'=>2014,'age'=>'3','club'=>'BASAQUATICS',  'kota'=>'SOLOK',     'best_time'=>'99.99.99','hasil'=>'02.05.33'],
                    ['rank'=>10,'nama'=>'AHMAD ALGHAZALI PANDAPOTAN LUBIS',    'papi'=>'PA','yob'=>2014,'age'=>'3','club'=>'BSSP',         'kota'=>'PAYAKUMBUH','best_time'=>'02.33.00','hasil'=>'02.13.13'],
                    ['rank'=>11,'nama'=>'ARFA LIANDRI ALVARO',                 'papi'=>'PA','yob'=>2014,'age'=>'3','club'=>'SSC',          'kota'=>'SIJUNJUNG', 'best_time'=>'99.99.99','hasil'=>'02.13.68'],
                    ['rank'=>12,'nama'=>'SABIAN MAJID',                        'papi'=>'PA','yob'=>2013,'age'=>'3','club'=>'BINTANG LAUT', 'kota'=>'PAYAKUMBUH','best_time'=>'99.99.99','hasil'=>'02.36.64'],
                    ['rank'=>13,'nama'=>'MUHAMMAD BIMAA PRAWIRANEGARA',        'papi'=>'PA','yob'=>2013,'age'=>'3','club'=>'KSC',          'kota'=>'50 KOTA',   'best_time'=>'99.99.99','hasil'=>'NS'],
                ],
            ],

            // -----------------------------------------------------------------
            // EVENT 108 — PI 100 M GAYA DADA — KU 3 PUTRI
            // -----------------------------------------------------------------
            [
                'event_label'    => 'EVENT 108 PI -100 M GAYA DADA',
                'category_label' => 'KU 3 PUTRI',
                'line_label'     => 'LINE',
                'results'        => [
                    ['rank'=>1, 'nama'=>'WULAN SITI RAMADHANI',       'papi'=>'PI','yob'=>2013,'age'=>'3','club'=>'MSC',           'kota'=>'AGAM',         'best_time'=>'01.37.62','hasil'=>'01.31.88'],
                    ['rank'=>2, 'nama'=>'RAISYA NAZHIFA',             'papi'=>'PI','yob'=>2014,'age'=>'3','club'=>'WARRIOR SC',    'kota'=>'BUKITTINGGI',  'best_time'=>'01.23.50','hasil'=>'01.33.98'],
                    ['rank'=>3, 'nama'=>'AQILLA UFAIRAH DINATA',      'papi'=>'PI','yob'=>2013,'age'=>'3','club'=>'BULAKAN AQUATIC','kota'=>'50 KOTA',     'best_time'=>'01.37.59','hasil'=>'01.35.37'],
                    ['rank'=>4, 'nama'=>'QANITA AWIQYA ZAIRI',        'papi'=>'PI','yob'=>2013,'age'=>'3','club'=>'GSC',           'kota'=>'PADANG',       'best_time'=>'01.35.53','hasil'=>'01.35.95'],
                    ['rank'=>5, 'nama'=>'CHERYL SHELYNDRA BILQIS',    'papi'=>'PI','yob'=>2014,'age'=>'3','club'=>'BSSP',          'kota'=>'PAYAKUMBUH',   'best_time'=>'01.30.40','hasil'=>'01.36.02'],
                    ['rank'=>6, 'nama'=>'AHZA NUR AINI',              'papi'=>'PI','yob'=>2014,'age'=>'3','club'=>'BANTOLA',       'kota'=>'BUKITTINGGI',  'best_time'=>'01.41.32','hasil'=>'01.37.98'],
                    ['rank'=>7, 'nama'=>'INAYA AZMI ATIFA',           'papi'=>'PI','yob'=>2013,'age'=>'3','club'=>'NSC',           'kota'=>'PAYAKUMBUH',   'best_time'=>'01.30.00','hasil'=>'01.40.16'],
                    ['rank'=>8, 'nama'=>'HOORIYA FETRINA',            'papi'=>'PI','yob'=>2014,'age'=>'3','club'=>'GSC',           'kota'=>'PADANG',       'best_time'=>'01.47.76','hasil'=>'01.42.67'],
                    ['rank'=>9, 'nama'=>'RADIVA KHAIRUNISA',          'papi'=>'PI','yob'=>2013,'age'=>'3','club'=>'BASAQUATICS',   'kota'=>'SOLOK',        'best_time'=>'01.43.60','hasil'=>'01.44.30'],
                    ['rank'=>10,'nama'=>'FAHIRA LUTHFINA RANDIATHAR', 'papi'=>'PI','yob'=>2014,'age'=>'3','club'=>'SSC',           'kota'=>'SIJUNJUNG',    'best_time'=>'01.42.93','hasil'=>'01.46.56'],
                    ['rank'=>11,'nama'=>'KHURFATUL JANNAH',           'papi'=>'PI','yob'=>2014,'age'=>'3','club'=>'HANNA SC',      'kota'=>'SOLOK SELATAN','best_time'=>'01.44.15','hasil'=>'01.48.91'],
                    ['rank'=>12,'nama'=>'KHANZA HANDAYANI',           'papi'=>'PI','yob'=>2014,'age'=>'3','club'=>'BINTANG LAUT',  'kota'=>'PAYAKUMBUH',   'best_time'=>'99.99.99','hasil'=>'02.00.66'],
                    ['rank'=>13,'nama'=>'GHANIAH FATIN ILAHI',        'papi'=>'PI','yob'=>2014,'age'=>'3','club'=>'NSC',           'kota'=>'PAYAKUMBUH',   'best_time'=>'01.34.00','hasil'=>'02.05.46'],
                    ['rank'=>14,'nama'=>'NURUL HAYANA',               'papi'=>'PI','yob'=>2014,'age'=>'3','club'=>'BSSP',          'kota'=>'PAYAKUMBUH',   'best_time'=>'02.11.00','hasil'=>'DQ'],
                    ['rank'=>15,'nama'=>'QAFISHA QURATU AINI',        'papi'=>'PI','yob'=>2014,'age'=>'3','club'=>'KSC',           'kota'=>'50 KOTA',      'best_time'=>'99.99.99','hasil'=>'NS'],
                ],
            ],

            // -----------------------------------------------------------------
            // EVENT 109 — PA 100 M GAYA DADA — KU 2 PUTRA
            // -----------------------------------------------------------------
            [
                'event_label'    => 'EVENT 109 PA -100 M GAYA DADA',
                'category_label' => 'KU 2 PUTRA',
                'line_label'     => 'LINE',
                'results'        => [
                    ['rank'=>1, 'nama'=>'MUHAMMAD ANDRIAST KASTARA ANSHORI','papi'=>'PA','yob'=>2012,'age'=>'2','club'=>'SeaRIA AQUATIC','kota'=>'PADANG',       'best_time'=>'01.12.25','hasil'=>'01.11.52'],
                    ['rank'=>2, 'nama'=>'ILHAM HABIB IRAWAN',               'papi'=>'PA','yob'=>2012,'age'=>'2','club'=>'HANNA SC',      'kota'=>'SOLOK SELATAN','best_time'=>'01.19.35','hasil'=>'01.15.98'],
                    ['rank'=>3, 'nama'=>'FELIX DWICAHYO',                   'papi'=>'PA','yob'=>2011,'age'=>'2','club'=>'BUILD SC',      'kota'=>'PADANG',       'best_time'=>'01.21.30','hasil'=>'01.20.77'],
                    ['rank'=>4, 'nama'=>'NADHIF RAFISQY',                   'papi'=>'PA','yob'=>2011,'age'=>'2','club'=>'WARRIOR SC',    'kota'=>'BUKITTINGGI',  'best_time'=>'01.19.85','hasil'=>'01.23.88'],
                    ['rank'=>5, 'nama'=>'FAJAR PRATAMA EFENDI',             'papi'=>'PA','yob'=>2012,'age'=>'2','club'=>'BSSP',          'kota'=>'PAYAKUMBUH',   'best_time'=>'01.24.52','hasil'=>'01.27.70'],
                    ['rank'=>6, 'nama'=>'VADHIL GUSDINA RAMADHAN',          'papi'=>'PA','yob'=>2011,'age'=>'2','club'=>'WARRIOR SC',    'kota'=>'BUKITTINGGI',  'best_time'=>'01.27.64','hasil'=>'01.29.87'],
                    ['rank'=>7, 'nama'=>'ANUGRAH MAHENDRA',                 'papi'=>'PA','yob'=>2011,'age'=>'2','club'=>'MSC',           'kota'=>'AGAM',         'best_time'=>'01.47.34','hasil'=>'01.35.37'],
                    ['rank'=>8, 'nama'=>'FATURRAHMAN AL GHAZI',             'papi'=>'PA','yob'=>2012,'age'=>'2','club'=>'FITT SC',       'kota'=>'PADANG',       'best_time'=>'01.30.09','hasil'=>'01.36.70'],
                    ['rank'=>9, 'nama'=>'DUTA TEGUH EKA MULIA',             'papi'=>'PA','yob'=>2012,'age'=>'2','club'=>'SSC',           'kota'=>'SIJUNJUNG',    'best_time'=>'01.40.69','hasil'=>'01.37.15'],
                    ['rank'=>10,'nama'=>'MIRZA MUSLIADI',                   'papi'=>'PA','yob'=>2011,'age'=>'2','club'=>'BSSP',          'kota'=>'PAYAKUMBUH',   'best_time'=>'01.37.10','hasil'=>'01.42.80'],
                    ['rank'=>11,'nama'=>'MUHAMMAD ALFA ATSALIS',            'papi'=>'PA','yob'=>2011,'age'=>'2','club'=>'SSC',           'kota'=>'SIJUNJUNG',    'best_time'=>'01.54.66','hasil'=>'01.44.55'],
                    ['rank'=>12,'nama'=>'ANUGRAH RIFQI IZZATUL ISLAM',      'papi'=>'PA','yob'=>2011,'age'=>'2','club'=>'SSC',           'kota'=>'SIJUNJUNG',    'best_time'=>'99.99.99','hasil'=>'01.57.03'],
                    ['rank'=>13,'nama'=>'FAISAL AKBAR RAMADHAN',            'papi'=>'PA','yob'=>2012,'age'=>'2','club'=>'KSC',           'kota'=>'50 KOTA',      'best_time'=>'99.99.99','hasil'=>'NS'],
                    ['rank'=>14,'nama'=>'ARFA ABDUL FAZILA',                'papi'=>'PA','yob'=>2012,'age'=>'2','club'=>'KSC',           'kota'=>'50 KOTA',      'best_time'=>'99.99.99','hasil'=>'NS'],
                ],
            ],

            // -----------------------------------------------------------------
            // EVENT 110 — PI 100 M GAYA DADA — KU 2 PUTRI
            // -----------------------------------------------------------------
            [
                'event_label'    => 'EVENT 110 PI -100 M GAYA DADA',
                'category_label' => 'KU 2 PUTRI',
                'line_label'     => 'LINE',
                'results'        => [
                    ['rank'=>1, 'nama'=>'NAFISAH NUR RAMADHANI',   'papi'=>'PI','yob'=>2012,'age'=>'2','club'=>'BANTOLA',        'kota'=>'BUKITTINGGI',   'best_time'=>'01.33.11','hasil'=>'01.31.03'],
                    ['rank'=>2, 'nama'=>'SALSABILA PUTRI APRILIA', 'papi'=>'PI','yob'=>2011,'age'=>'2','club'=>'BSC',            'kota'=>'SIJUNJUNG',     'best_time'=>'01.28.62','hasil'=>'01.32.91'],
                    ['rank'=>3, 'nama'=>'TASBIH AYNIL ZIKRO',      'papi'=>'PI','yob'=>2011,'age'=>'2','club'=>'BSSP',           'kota'=>'PAYAKUMBUH',    'best_time'=>'01.36.00','hasil'=>'01.40.81'],
                    ['rank'=>4, 'nama'=>'RESTI AULIA SEDIZA',      'papi'=>'PI','yob'=>2011,'age'=>'2','club'=>'MSC',            'kota'=>'AGAM',          'best_time'=>'01.39.32','hasil'=>'01.42.30'],
                    ['rank'=>5, 'nama'=>'SHAFA KHAFRIZA',          'papi'=>'PI','yob'=>2011,'age'=>'2','club'=>'DIAMOND SC',     'kota'=>'PADANG',        'best_time'=>'01.42.00','hasil'=>'01.46.28'],
                    ['rank'=>6, 'nama'=>'FINA NAILATUL IZZA',      'papi'=>'PI','yob'=>2012,'age'=>'2','club'=>'BSC',            'kota'=>'BUKITTINGGI',   'best_time'=>'01.39.20','hasil'=>'01.50.36'],
                    ['rank'=>7, 'nama'=>'AZZAILA MAULANI RAHMAN',  'papi'=>'PI','yob'=>2011,'age'=>'2','club'=>'SSC',            'kota'=>'SIJUNJUNG',     'best_time'=>'01.46.16','hasil'=>'01.51.27'],
                    ['rank'=>8, 'nama'=>'AMEE YONANICO',           'papi'=>'PI','yob'=>2012,'age'=>'2','club'=>'BULAKAN AQUATIC','kota'=>'50 KOTA',       'best_time'=>'01.30.56','hasil'=>'01.55.74'],
                    ['rank'=>9, 'nama'=>'HALWA MADINAH',           'papi'=>'PI','yob'=>2011,'age'=>'2','club'=>'BINTANG LAUT',   'kota'=>'PAYAKUMBUH',    'best_time'=>'01.53.28','hasil'=>'01.56.63'],
                    ['rank'=>10,'nama'=>'DZAKHIA THALITA SAKHI',   'papi'=>'PI','yob'=>2012,'age'=>'2','club'=>'BSC',            'kota'=>'SIJUNJUNG',     'best_time'=>'01.45.67','hasil'=>'01.59.65'],
                    ['rank'=>11,'nama'=>'ESHAL ZAHIRA AFDALI',     'papi'=>'PI','yob'=>2012,'age'=>'2','club'=>'SeaRIA AQUATIC', 'kota'=>'PESISIR SELATAN','best_time'=>'01.56.85','hasil'=>'02.05.07'],
                    ['rank'=>12,'nama'=>'AYU AULIA RAHMAN',        'papi'=>'PI','yob'=>2011,'age'=>'2','club'=>'KSC',            'kota'=>'50 KOTA',       'best_time'=>'99.99.99','hasil'=>'NS'],
                ],
            ],
        ];
        $pdf = Pdf::loadView('pages.export_doc.buku_hasil', compact('events'));

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
    }

    public function rekapMedali(){
        Carbon::setLocale('id');

        // =============================================
        // DATA — ganti dengan query Eloquent
        // =============================================
        $data = [

            // Info event
            'namaEvent' => 'Golden Black Padang Open Swimming Champ 2025',
            'venue'     => 'Kolam Renang Teratai Padang',
            'tanggal'   => '19 - 20 SEPTEMBER 2025',

            // Logo — path absolut wajib untuk Dompdf
            'logoKiri'  => [
                public_path('images/logo-sumbar.png'),
                public_path('images/logo-kota.png'),
            ],
            'logoKanan' => [
                public_path('images/logo-akuatik.png'),
                public_path('images/logo-gb.png'),
            ],

            // =============================================
            // REKAP MEDALI
            // Ganti dengan: DB::table('rekap_medali')->...
            // atau model: RekapMedali::orderBy('posisi')->get()
            // =============================================
            'rekapMedali' => collect([
                ['posisi' => 1,  'nama_tim' => 'SEARIA AQUATIC PADANG',             'emas' => 31, 'perak' => 14, 'perunggu' => 17, 'poin' => 0],
                ['posisi' => 2,  'nama_tim' => 'GUNUNG SPORT CENTRE PADANG',        'emas' => 10, 'perak' => 9,  'perunggu' => 12, 'poin' => 0],
                ['posisi' => 3,  'nama_tim' => 'WOMENS SWIMMING CLUB PADANG',       'emas' => 10, 'perak' => 6,  'perunggu' => 4,  'poin' => 0],
                ['posisi' => 4,  'nama_tim' => 'DIAMOND SWIMMING CLUB PADANG',      'emas' => 5,  'perak' => 6,  'perunggu' => 4,  'poin' => 0],
                ['posisi' => 5,  'nama_tim' => 'HANNA SWIMMING CLUB SOLOK SELATAN', 'emas' => 5,  'perak' => 5,  'perunggu' => 5,  'poin' => 0],
                ['posisi' => 6,  'nama_tim' => 'HOYAK SWIMMING CLUB',               'emas' => 4,  'perak' => 4,  'perunggu' => 1,  'poin' => 0],
                ['posisi' => 7,  'nama_tim' => 'BUILD SWIMMING CLUB',               'emas' => 3,  'perak' => 9,  'perunggu' => 8,  'poin' => 0],
                ['posisi' => 8,  'nama_tim' => 'PR TIRTA KALUANG PADANG',           'emas' => 3,  'perak' => 2,  'perunggu' => 1,  'poin' => 0],
                ['posisi' => 9,  'nama_tim' => 'KITTINGGI BANTOLA SWIMMING CLUB',   'emas' => 2,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => 10, 'nama_tim' => 'SIJUNJUNG SWIMMING CLUB',           'emas' => 1,  'perak' => 5,  'perunggu' => 3,  'poin' => 0],
                ['posisi' => 11, 'nama_tim' => 'SEARIA AQUATIC PAINAN',             'emas' => 1,  'perak' => 2,  'perunggu' => 1,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'BULAKAN AQUATIC CLUB',             'emas' => 0,  'perak' => 3,  'perunggu' => 3,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'NARASINGA AKUATIK INDRAGIRI',      'emas' => 0,  'perak' => 2,  'perunggu' => 4,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'PIAMAN LAWEH',                     'emas' => 0,  'perak' => 2,  'perunggu' => 1,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'LENGAYANG SWIMMING CLUB',          'emas' => 0,  'perak' => 2,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'STAFAN SWIMMING CLUB BENGKALIS',   'emas' => 0,  'perak' => 2,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'BA SHARK AQUATICS',                'emas' => 0,  'perak' => 0,  'perunggu' => 2,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'ALINAFIKA AKUATIK PADANG',         'emas' => 0,  'perak' => 0,  'perunggu' => 1,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'GOLDEN BLACK SC',                  'emas' => 0,  'perak' => 0,  'perunggu' => 1,  'poin' => 0],
                // Tim tanpa medali (tampil tanpa angka)
                ['posisi' => '', 'nama_tim' => 'ATHA SWIMMING CLUB',                'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'BHAYANGKARA SWIMMING CLUB BUKITTINGGI', 'emas' => 0, 'perak' => 0, 'perunggu' => 0, 'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'LAWAI FANTASI SWIMMING CLUB',       'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'MADANI SWIMMING CLUB',              'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'MERLIN SWIMMING CLUB PADANG PARIAMAN', 'emas' => 0, 'perak' => 0, 'perunggu' => 0, 'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'PROFI SWIMMING CLUB',               'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'RAM SWIMMING CLUB',                 'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'SAMUDERA AQUATIC CLUB',             'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'SAWAHLUNTO SWIMMING CLUB',          'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'UNATTACHED (CLUB)',                 'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
            ]),

            // =============================================
            // HASIL PERLOMBAAN
            // Ganti dengan query dari DB
            // =============================================
            'acaraList' => [
                [
                    'nomor'    => '101',
                    'nama'     => '800 M GAYA BEBAS PUTRA, LCM',
                    'tanggal'  => '19 SEPTEMBER 2025',
                    'status'   => 'AKHIR',
                    'limit'    => 'NO LIMIT',
                    'kategori' => 'OPEN',
                    'hasil'    => [
                        ['pos'=>'1','id'=>'3158', 'nama'=>'GILBERT HADIWARSA',    'ket'=>'','lahir'=>'2007','umur'=>'18','ku'=>'OPEN','tim'=>'WOMENS SWIMMING CLUB PADANG',       'prestasi'=>'10.21', 'hasil'=>'11:01.48'],
                        ['pos'=>'2','id'=>'34264','nama'=>'M FATHUR YUZA SARESDI','ket'=>'','lahir'=>'2009','umur'=>'16','ku'=>'OPEN','tim'=>'BUILD SWIMMING CLUB',               'prestasi'=>'-',     'hasil'=>'11:08.41'],
                        ['pos'=>'3','id'=>'43929','nama'=>'ALFADRI RAMADHAN',     'ket'=>'','lahir'=>'2002','umur'=>'23','ku'=>'OPEN','tim'=>'HOYAK SWIMMING CLUB',               'prestasi'=>'-',     'hasil'=>'13:47.67'],
                    ],
                ],
                [
                    'nomor'    => '102',
                    'nama'     => '800 M GAYA BEBAS PUTRI, LCM',
                    'tanggal'  => '19 SEPTEMBER 2025',
                    'status'   => 'AKHIR',
                    'limit'    => 'NO LIMIT',
                    'kategori' => 'OPEN',
                    'hasil'    => [
                        ['pos'=>'1','id'=>'19691','nama'=>'AUREL DONITA',          'ket'=>'','lahir'=>'2009','umur'=>'16','ku'=>'OPEN','tim'=>'KITTINGGI BANTOLA SWIMMING CLUB',  'prestasi'=>'-',     'hasil'=>'11:12.29'],
                        ['pos'=>'2','id'=>'44612','nama'=>'ALVINA SYACHRANY',      'ket'=>'','lahir'=>'2009','umur'=>'16','ku'=>'OPEN','tim'=>'BUILD SWIMMING CLUB',               'prestasi'=>'-',     'hasil'=>'12:36.35'],
                        ['pos'=>'3','id'=>'19260','nama'=>'ZAHWA AQILLA FADILLAH', 'ket'=>'','lahir'=>'2009','umur'=>'16','ku'=>'OPEN','tim'=>'SEARIA AQUATIC PADANG',             'prestasi'=>'-',     'hasil'=>'13:06.34'],
                    ],
                ],
                // Tambahkan acara lainnya dari database...
            ],
        ];

        // =============================================
        // GENERATE PDF
        // =============================================
        $pdf = Pdf::loadView('pages.export_doc.rekap_medali', $data)
            ->setPaper('a4', 'portrait');

        $pdf->output();

        $dompdf = $pdf->getDomPDF();
        $canvas  = $dompdf->getCanvas();
        $font    = $dompdf->getFontMetrics()->getFont("Arial", "normal");

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

        return $dompdf->stream('hasil-kejuaraan.pdf', ['Attachment' => false]);
    }

    public function bestClub(){
        Carbon::setLocale('id');
        $data = [

            // Info event
            'namaEvent' => 'Golden Black Padang Open Swimming Champ 2025',
            'venue'     => 'Kolam Renang Teratai Padang',
            'tanggal'   => '19 - 20 SEPTEMBER 2025',

            // Logo — path absolut wajib untuk Dompdf
            'logoKiri'  => [
                public_path('images/logo-sumbar.png'),
                public_path('images/logo-kota.png'),
            ],
            'logoKanan' => [
                public_path('images/logo-akuatik.png'),
                public_path('images/logo-gb.png'),
            ],

            // =============================================
            // REKAP MEDALI
            // Ganti dengan: DB::table('rekap_medali')->...
            // atau model: RekapMedali::orderBy('posisi')->get()
            // =============================================
            'rekapMedali' => collect([
                ['posisi' => 1,  'nama_tim' => 'SEARIA AQUATIC PADANG',             'emas' => 31, 'perak' => 14, 'perunggu' => 17, 'poin' => 0],
                ['posisi' => 2,  'nama_tim' => 'GUNUNG SPORT CENTRE PADANG',        'emas' => 10, 'perak' => 9,  'perunggu' => 12, 'poin' => 0],
                ['posisi' => 3,  'nama_tim' => 'WOMENS SWIMMING CLUB PADANG',       'emas' => 10, 'perak' => 6,  'perunggu' => 4,  'poin' => 0],
                ['posisi' => 4,  'nama_tim' => 'DIAMOND SWIMMING CLUB PADANG',      'emas' => 5,  'perak' => 6,  'perunggu' => 4,  'poin' => 0],
                ['posisi' => 5,  'nama_tim' => 'HANNA SWIMMING CLUB SOLOK SELATAN', 'emas' => 5,  'perak' => 5,  'perunggu' => 5,  'poin' => 0],
                ['posisi' => 6,  'nama_tim' => 'HOYAK SWIMMING CLUB',               'emas' => 4,  'perak' => 4,  'perunggu' => 1,  'poin' => 0],
                ['posisi' => 7,  'nama_tim' => 'BUILD SWIMMING CLUB',               'emas' => 3,  'perak' => 9,  'perunggu' => 8,  'poin' => 0],
                ['posisi' => 8,  'nama_tim' => 'PR TIRTA KALUANG PADANG',           'emas' => 3,  'perak' => 2,  'perunggu' => 1,  'poin' => 0],
                ['posisi' => 9,  'nama_tim' => 'KITTINGGI BANTOLA SWIMMING CLUB',   'emas' => 2,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => 10, 'nama_tim' => 'SIJUNJUNG SWIMMING CLUB',           'emas' => 1,  'perak' => 5,  'perunggu' => 3,  'poin' => 0],
                ['posisi' => 11, 'nama_tim' => 'SEARIA AQUATIC PAINAN',             'emas' => 1,  'perak' => 2,  'perunggu' => 1,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'BULAKAN AQUATIC CLUB',             'emas' => 0,  'perak' => 3,  'perunggu' => 3,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'NARASINGA AKUATIK INDRAGIRI',      'emas' => 0,  'perak' => 2,  'perunggu' => 4,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'PIAMAN LAWEH',                     'emas' => 0,  'perak' => 2,  'perunggu' => 1,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'LENGAYANG SWIMMING CLUB',          'emas' => 0,  'perak' => 2,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'STAFAN SWIMMING CLUB BENGKALIS',   'emas' => 0,  'perak' => 2,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'BA SHARK AQUATICS',                'emas' => 0,  'perak' => 0,  'perunggu' => 2,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'ALINAFIKA AKUATIK PADANG',         'emas' => 0,  'perak' => 0,  'perunggu' => 1,  'poin' => 0],
                ['posisi' => '',  'nama_tim' => 'GOLDEN BLACK SC',                  'emas' => 0,  'perak' => 0,  'perunggu' => 1,  'poin' => 0],
                // Tim tanpa medali (tampil tanpa angka)
                ['posisi' => '', 'nama_tim' => 'ATHA SWIMMING CLUB',                'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'BHAYANGKARA SWIMMING CLUB BUKITTINGGI', 'emas' => 0, 'perak' => 0, 'perunggu' => 0, 'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'LAWAI FANTASI SWIMMING CLUB',       'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'MADANI SWIMMING CLUB',              'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'MERLIN SWIMMING CLUB PADANG PARIAMAN', 'emas' => 0, 'perak' => 0, 'perunggu' => 0, 'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'PROFI SWIMMING CLUB',               'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'RAM SWIMMING CLUB',                 'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'SAMUDERA AQUATIC CLUB',             'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'SAWAHLUNTO SWIMMING CLUB',          'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
                ['posisi' => '', 'nama_tim' => 'UNATTACHED (CLUB)',                 'emas' => 0,  'perak' => 0,  'perunggu' => 0,  'poin' => 0],
            ]),
        ];

        // =============================================
        // GENERATE PDF
        // =============================================
        $pdf = Pdf::loadView('pages.export_doc.best_club', $data)
            ->setPaper('a4', 'portrait');

        $pdf->output();

        $dompdf = $pdf->getDomPDF();
        $canvas  = $dompdf->getCanvas();
        $font    = $dompdf->getFontMetrics()->getFont("Arial", "normal");

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

        return $dompdf->stream('best-club.pdf', ['Attachment' => false]);
    }

    public function bestSwimmer(){
        Carbon::setLocale('id');

        $data = [
            'namaEvent' => 'Golden Black Padang Open Swimming Champ 2025',
            'venue'     => 'Kolam Renang Teratai Padang',
            'tanggal'   => '19 - 20 SEPTEMBER 2025',

            'logoKiri' => [
                public_path('images/logo-sumbar.png'),
                public_path('images/logo-kota.png'),
            ],
            'logoKanan' => [
                public_path('images/logo-akuatik.png'),
                public_path('images/logo-gb.png'),
            ],

            // =============================================
            // Ganti dengan query dari DB, contoh:
            // AtletTerbaik::orderBy('ku')->orderBy('pos')->get()
            // =============================================
            'atletTerbaik' => [
                ['pos'=>1, 'id'=>'19129', 'nama'=>'FATHUR ZENADRI',                    'sex'=>'PUTRA', 'ku'=>'GROUP 2', 'tim'=>'SEARIA AQUATIC PADANG',             'emas'=>7, 'perak'=>0, 'perunggu'=>0, 'poin'=>0],
                ['pos'=>1, 'id'=>'19131', 'nama'=>'QUSYAIRI AL ZIKRI',                 'sex'=>'PUTRA', 'ku'=>'GROUP 3', 'tim'=>'SEARIA AQUATIC PADANG',             'emas'=>5, 'perak'=>1, 'perunggu'=>0, 'poin'=>0],
                ['pos'=>1, 'id'=>'18413', 'nama'=>'MUHAMMAD RIBERY GERIAN',            'sex'=>'PUTRA', 'ku'=>'GROUP 4', 'tim'=>'SEARIA AQUATIC PADANG',             'emas'=>7, 'perak'=>3, 'perunggu'=>0, 'poin'=>0],
                ['pos'=>1, 'id'=>'43265', 'nama'=>'MUHAMMAD HABIBIE ABRAR',            'sex'=>'PUTRA', 'ku'=>'GROUP 5', 'tim'=>'HOYAK SWIMMING CLUB',               'emas'=>9, 'perak'=>2, 'perunggu'=>0, 'poin'=>0],
                ['pos'=>1, 'id'=>'20117', 'nama'=>'FATHAN ADRIANO',                    'sex'=>'PUTRA', 'ku'=>'GROUP 6', 'tim'=>'HANNA SWIMMING CLUB SOLOK SELATAN', 'emas'=>5, 'perak'=>0, 'perunggu'=>2, 'poin'=>0],
                ['pos'=>1, 'id'=>'3158',  'nama'=>'GILBERT HADIWARSA',                 'sex'=>'PUTRA', 'ku'=>'OPEN',    'tim'=>'WOMENS SWIMMING CLUB PADANG',        'emas'=>3, 'perak'=>0, 'perunggu'=>0, 'poin'=>0],
                ['pos'=>1, 'id'=>'2994',  'nama'=>'MAISHA AFNI',                       'sex'=>'PUTRI', 'ku'=>'GROUP 2', 'tim'=>'GUNUNG SPORT CENTRE PADANG',         'emas'=>5, 'perak'=>0, 'perunggu'=>0, 'poin'=>0],
                ['pos'=>1, 'id'=>'3262',  'nama'=>'BEBPIDIVA CANTIKA RAMOHTY SIRINGO-RINGO', 'sex'=>'PUTRI', 'ku'=>'GROUP 3', 'tim'=>'SEARIA AQUATIC PADANG',       'emas'=>7, 'perak'=>1, 'perunggu'=>0, 'poin'=>0],
                ['pos'=>1, 'id'=>'33785', 'nama'=>'LEANDRA GITA AZALIA',               'sex'=>'PUTRI', 'ku'=>'GROUP 4', 'tim'=>'SEARIA AQUATIC PADANG',             'emas'=>8, 'perak'=>0, 'perunggu'=>3, 'poin'=>0],
                ['pos'=>1, 'id'=>'19856', 'nama'=>'CALLYSTA TWELVERINE SIRINGO RINGO', 'sex'=>'PUTRI', 'ku'=>'GROUP 5', 'tim'=>'DIAMOND SWIMMING CLUB PADANG',      'emas'=>7, 'perak'=>0, 'perunggu'=>1, 'poin'=>0],
                ['pos'=>1, 'id'=>'43460', 'nama'=>'AZKIRA YUDHANTA',                   'sex'=>'PUTRI', 'ku'=>'GROUP 6', 'tim'=>'DIAMOND SWIMMING CLUB PADANG',      'emas'=>4, 'perak'=>2, 'perunggu'=>0, 'poin'=>0],
                ['pos'=>1, 'id'=>'19691', 'nama'=>'AUREL DONITA',                      'sex'=>'PUTRI', 'ku'=>'OPEN',    'tim'=>'KITTINGGI BANTOLA SWIMMING CLUB',   'emas'=>3, 'perak'=>1, 'perunggu'=>0, 'poin'=>0],
                ['pos'=>1, 'id'=>'19260', 'nama'=>'ZAHWA AQILLA FADILLAH',             'sex'=>'PUTRI', 'ku'=>'OPEN',    'tim'=>'SEARIA AQUATIC PADANG',             'emas'=>3, 'perak'=>1, 'perunggu'=>0, 'poin'=>0],
            ],
        ];

        $pdf = Pdf::loadView('pages.export_doc.best_swimmer', $data)
            ->setPaper('a4', 'landscape');

        $pdf->output();

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

        return $dompdf->stream('atlet-terbaik.pdf', ['Attachment' => false]);
    }
}
