<?php

namespace App\Http\Controllers;

use App\Enums\CompetitionTeamEntryStatus;
use App\Models\Competition;
use App\Models\CompetitionEvent;

class CompetitionHeatLaneController extends Controller
{
    public function generateHeat(){
        $competition = Competition::find(1);
        $events = $competition->events;

        $result = [];
        foreach ($events as $event) {
           $result[$event->id]['competition_event_id'] = $event->id;
           $result[$event->id]['heats'] = $this->generateHeatByEvent($event);
        }
        return $result;
    }

    // public function generateHeatByEvent(CompetitionEvent $event){
    //     $totalLanes = $event->competitionSession->pool->total_lanes;

    //     $entries = $event->entries()
    //             ->where('status', CompetitionTeamEntryStatus::Active->value)
    //             ->get();
    //     if ($entries->isEmpty()) return;

    //     $withTime = $entries->whereNotNull('seed_time')
    //                 ->sortByDesc('seed_time')
    //                 ->values();

    //     $withoutTime = $entries->whereNull('seed_time')
    //                 ->values();

    //     $orderedEntry = collect()->merge($withoutTime)->merge($withTime);
    //     $totalEntry = $orderedEntry->count();
    //     $totalHeat = (int) ceil ($totalEntry / $totalLanes);
    //     $totalEntryPerHeat = $totalEntry / $totalHeat;

    //     $assignEntryToHeat = $orderedEntry->chunk($totalEntryPerHeat);

    //     $lanes = $this->generateSnakeLanes($totalLanes);


    //     $arrHeats = [];
    //     foreach($assignEntryToHeat as $heatIndex => $entry){
    //         $arrHeats[$heatIndex]['heat_number'] = $heatIndex + 1;

    //         foreach ($entry->values() as $laneIndex => $item) {
    //             $lane = $lanes[$laneIndex];
    //             $arrHeats[$heatIndex]['lanes'][$laneIndex]['lane_number'] = $lane;
    //             $arrHeats[$heatIndex]['lanes'][$laneIndex]['entry_id'] = $item->id;
    //         }
    //     }
    //     return $arrHeats;
    // }
    public function generateHeatByEvent(CompetitionEvent $event){
        $totalLanes = $event->competitionSession->pool->total_lanes;

        $entries = $event->entries()
                ->where('status', CompetitionTeamEntryStatus::Active->value)
                ->get();
        if ($entries->isEmpty()) return;

        $withTime = $entries->whereNotNull('seed_time')
                    ->sortByDesc('seed_time')
                    ->values();

        $withoutTime = $entries->whereNull('seed_time')
                    ->values();

        $orderedEntry = collect()->merge($withoutTime)->merge($withTime);
        $totalEntry = $orderedEntry->count();
        $sisa = $totalEntry % $totalLanes;

        // Jika sisa ≤ setengah lane, redistribusi 2 heat pertama
        if ($sisa > 0 && $sisa <= (int) floor($totalLanes / 2)) {
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

            foreach ($entry->values() as $laneIndex => $item) {
                $lane = $lanes[$laneIndex];
                $arrHeats[$heatIndex]['lanes'][$laneIndex]['lane_number'] = $lane;
                $arrHeats[$heatIndex]['lanes'][$laneIndex]['entry_id'] = $item->id;
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
}
