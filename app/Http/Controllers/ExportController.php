<?php

namespace App\Http\Controllers;

use App\Enums\EventType;
use App\Exports\StartingListExport;
use App\Models\CompetitionEntryRelayMember;
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
    public function bukuAcara(){
        return view('pages.export_doc.buku_acara');
    }
}
