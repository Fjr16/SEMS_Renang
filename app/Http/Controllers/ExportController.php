<?php

namespace App\Http\Controllers;

use App\Exports\StartingListExport;
use App\Models\CompetitionTeam;
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
            // 'club_name'  => 'required|string|max:255',
            // 'event_name' => 'required|string|max:255',
            // 'year'       => 'required|digits:4',
        ]);

        $item = CompetitionTeam::with([
                    'competition.events',
                    'competitionEntries.athlete',
                    'competitionEntries.competitionEvent',
                    'competitionEntries.competitionEvent.ageGroup'
                ])
                ->find($request->competition_team_id);

        if(!$item){
            return response()->json([
                'status' => false,
                'message' => 'Data pendaftaran tim tidak ditemukan'
            ]);
        }

         $eventGroups = $item->competition->events
        ->groupBy('stroke')
        ->map(fn($events, $gaya) => [
            'name'   => $gaya,
            'events' => $events->map(fn($e) => [
                'id'    => $e->id,
                'label' => $e->distance . ' m',               // contoh: "50M", "100M"
            ])->values()->toArray(),
        ])
        ->values()
        ->toArray();

         $peserta = $item->competitionEntries
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

        $nama_club = $item?->team?->club_name ?? '-';
        $tahun = $item?->competition?->start_date ? Carbon::parse($item?->competition?->start_date)->format('Y') : '-';

        $export = new StartingListExport(
            peserta: $peserta,
            eventGroups: $eventGroups,
            clubName: $nama_club,
            competitionName: $item?->competition?->name ?? '-',
            year: $tahun,
        );

        $filename = 'starting_list_' . str_replace(' ', '_', $nama_club) . '_' . $tahun . '.xlsx';

        return Excel::download($export, $filename);
    }
    public function bukuAcara(){

    }

    private function getDummyData(): array
    {
        return [
            [
                'no' => 1,
                'nama' => 'Fathur Zenadri',
                'ku' => 'II',
                'pa_pi' => 'PA',
                'gb_ppn75' => '', 'gb_25m' => '',  'gb_50m' => 'V', 'gb_100m' => 'V', 'gb_200m' => '',  'gb_400m' => '',
                'gk_25m'   => '',  'gk_50m' => 'V', 'gk_100m' => '', 'gk_200m' => '',
                'gp_50m'   => 'V', 'gp_100m' => '',  'gp_200m' => '',
                'gd_25m'   => '',  'gd_50m' => 'V', 'gd_100m' => 'V', 'gd_200m' => 'V',
                'gg_200m'  => 'V',
            ],
            [
                'no' => 2, 'nama' => 'Qusyairi Muammar Gibran', 'ku' => 'II', 'pa_pi' => 'PA',
                'gb_ppn75' => '', 'gb_25m' => '',  'gb_50m' => 'V', 'gb_100m' => 'V', 'gb_200m' => 'V',  'gb_400m' => 'V',
                'gk_25m'   => '',  'gk_50m' => 'V', 'gk_100m' => 'V', 'gk_200m' => 'V',
                'gp_50m'   => 'V', 'gp_100m' => 'V',  'gp_200m' => 'V',
                'gd_25m'   => '',  'gd_50m' => 'V', 'gd_100m' => 'V', 'gd_200m' => 'V',
                'gg_200m'  => 'V',
            ],
            [
                'no' => 3, 'nama' => 'Darvesh Evan Putra Huda', 'ku' => 'II', 'pa_pi' => 'PA',
                'gb_ppn75' => '', 'gb_25m' => '',  'gb_50m' => '', 'gb_100m' => 'V', 'gb_200m' => 'V',  'gb_400m' => 'V',
                'gk_25m'   => '',  'gk_50m' => '', 'gk_100m' => 'V', 'gk_200m' => 'V',
                'gp_50m'   => '', 'gp_100m' => '',  'gp_200m' => '',
                'gd_25m'   => '',  'gd_50m' => '', 'gd_100m' => '', 'gd_200m' => '',
                'gg_200m'  => '',
            ],
            [
                'no' => 4, 'nama' => 'Dwi Natasha Brades', 'ku' => 'II', 'pa_pi' => 'PI',
                'gb_ppn75' => '', 'gb_25m' => '',  'gb_50m' => 'V', 'gb_100m' => 'V', 'gb_200m' => 'V',  'gb_400m' => 'V',
                'gk_25m'   => '',  'gk_50m' => 'V', 'gk_100m' => 'V', 'gk_200m' => 'V',
                'gp_50m'   => 'V', 'gp_100m' => 'V',  'gp_200m' => 'V',
                'gd_25m'   => '',  'gd_50m' => 'V', 'gd_100m' => 'V', 'gd_200m' => 'V',
                'gg_200m'  => 'V',
            ],
            [
                'no' => 5, 'nama' => 'Zahwa Aqilla Fadillah', 'ku' => 'II', 'pa_pi' => 'PI',
                'gb_ppn75' => '', 'gb_25m' => '',  'gb_50m' => 'V', 'gb_100m' => 'V', 'gb_200m' => 'V',  'gb_400m' => 'V',
                'gk_25m'   => '',  'gk_50m' => 'V', 'gk_100m' => 'V', 'gk_200m' => 'V',
                'gp_50m'   => 'V', 'gp_100m' => 'V',  'gp_200m' => 'V',
                'gd_25m'   => '',  'gd_50m' => 'V', 'gd_100m' => 'V', 'gd_200m' => 'V',
                'gg_200m'  => 'V',
            ],
        ];
    }
}
