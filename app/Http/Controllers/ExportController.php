<?php

namespace App\Http\Controllers;

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
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    use HasApiResponse;
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
                    'sesi' => $item->sessions->map(function ($sesi) {
                        return [
                            'nama' => $sesi->name ?? '-',
                            'acara' => $sesi->competitionEvents
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
            $jadwalHari = $item->sessions->groupBy('session_date')->map(function($byDate){
                $tglSesi = Carbon::parse($byDate->first()->session_date);
                return [
                    'label' => $tglSesi->translatedFormat('l, d F Y'),
                    'sesi' => $byDate->map(function ($sesi) {
                        return [
                            'nama' => $sesi->name ?? '-',
                            'acara' => $sesi->competitionEvents
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
                'nama' => ($e?->distance ?? '-') . ' M ' . (Stroke::tryFrom($e->stroke)->label() ?? '-') . ', ' . $e?->competitionSession->pool->course_type ?? '-' ,
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
                public_path('assets/akuatik-indonesia-seeklogo.png'),
                // public_path('assets/akuatik-indonesia-seeklogo.png'),
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

            // Helper: hitung x agar teks tepat di tengah
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

    public function bukuHasil(Request $req){
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
                    'cen.seed_time as best_time',
                    'chl.swim_time as hasil',
                    'ce.id as competition_event_id',
                    'ce.event_number',
                    'ce.gender as event_gender',
                    'ce.distance',
                    'ce.stroke',
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
        $events = $data->groupBy('competition_event_id')->map(function($item) use ($ageGroups, $mulaiKompetisi){
            $first = $item->first();
            $genderEvFull = match($first->event_gender){
                Gender::pria->value => 'PUTRA',
                Gender::wanita->value => 'PUTRI',
                default => 'CAMPURAN'
            };
            $genderEv = ($genderEvFull === 'PUTRA' ? 'PA' : ($genderEvFull === 'PUTRI' ? 'PI' : 'CAMPURAN'));
            $strokeEv = Stroke::from($first->stroke)->label();
            return [
                'event_label' => 'EVENT ' . $first->event_number . ' ' . $genderEv . ' - ' . $first->distance . ' M ' . $strokeEv,
                'category_label' => 'KU ' . $first->kelompok_umur . ' ' . $genderEvFull,
                'line_label' => 'RANK',
                'results' => $item->sortBy(fn($atlet) => $this->swimTimeToCs($atlet->hasil))
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
