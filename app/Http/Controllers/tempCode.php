<?php

namespace App\Http\Controllers;
use App\Models\FinalResult;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Competition;
use App\Enums\RoundTypeEnum;
use App\Enums\Gender;
use App\Traits\HasApiResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class tempCode extends Controller {
    use HasApiResponse;

    public function bestSwimmer(Request $req){
        Carbon::setLocale('id');
        $validators = Validator::make($req->all(), [
            'competition_id' => 'required|exists:competitions,id',
        ]);
        if($validators->fails()) {
            return $this->notFound('Kompetisi tidak terdaftar');
        }

        $competition = Competition::find($req->competition_id ?? 1);
        $bestAtlet = DB::table('final_results as a')
            ->selectRaw('
                a.athlete_id,
                b.gender,
                b.age_group_id,
                c.label as age_group_label,
                d.name as athlete_name,
                d.code as athlete_code,
                e.club_name,
                SUM(CASE WHEN a.rank_in_event = 1 THEN 1 ELSE 0 END) as emas,
                SUM(CASE WHEN a.rank_in_event = 2 THEN 1 ELSE 0 END) as perak,
                SUM(CASE WHEN a.rank_in_event = 3 THEN 1 ELSE 0 END) as perunggu
            ')
            ->leftJoin('competition_events as b', 'b.id', '=', 'a.competition_event_id')
            ->leftJoin('age_groups as c', 'c.id', '=', 'b.age_group_id')
            ->leftJoin('athletes as d', 'd.id', '=', 'a.athlete_id')
            ->leftJoin('clubs as e', 'e.id', '=', 'd.club_id')
            ->where('a.round_type', RoundTypeEnum::final->value)
            ->where('a.competition_id', $req->competition_id ?? 1)
            ->whereNotNull('a.athlete_id')
            ->groupBy('a.athlete_id', 'b.gender', 'b.age_group_id')
            ->orderBy('b.age_group_id')
            ->orderBy('b.gender')
            ->orderByDesc('emas')
            ->orderByDesc('perak')
            ->orderByDesc('perunggu')
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
            'namaEvent' => $competition->name,
            'venue'     => $competition->venue->name,
            'tanggal'   => $tanggal,

            'logoKiri' => [
                public_path('images/logo-sumbar.png'),
                public_path('images/logo-kota.png'),
            ],
            'logoKanan' => [
                public_path('images/logo-akuatik.png'),
                public_path('images/logo-gb.png'),
            ],

            'atletTerbaik' => $atletTerbaik,
        ];

        $pdf = Pdf::loadView('pages.export_doc.best_swimmer', $data)
            ->setPaper('a4', 'landscape');

        $pdf->render();

        // $pdf->output();

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

        // return $dompdf->stream('atlet-terbaik.pdf', ['Attachment' => false]);
        return $dompdf->stream('atlet-terbaik.pdf');
    }
}
