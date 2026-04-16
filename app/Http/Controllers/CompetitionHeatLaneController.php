<?php

namespace App\Http\Controllers;

use App\Enums\CompetitionTeamEntryStatus;
use App\Enums\RoundTypeEnum;
use App\Models\Competition;
use App\Models\CompetitionEvent;
use App\Models\CompetitionHeat;
use App\Models\CompetitionHeatLane;

class CompetitionHeatLaneController extends Controller
{
    public function partialReload(Competition $competition, $event_id = null){
        $event = !$event_id
        ? $competition->events()->first()
        : CompetitionEvent::find($event_id);
        $event->load(['heats']);

        return view('pages.competition.tabs.heats', compact(
            'competition',
            'event'
        ));
    }
    public function generateHeat(){
        $competition = Competition::find(1);
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

    // public function generateHeatByEventRound(CompetitionEvent $event, string $roundType){
    //     $totalLanes = $event->competitionSession->pool->total_lanes;

    //     $entries = $event->entries()
    //             ->where('status', CompetitionTeamEntryStatus::Active->value)
    //             ->get();
    //     if ($entries->isEmpty()) return;

    //     if($roundType === RoundTypeEnum::prelim->value){
    //         $withTime = $entries->whereNotNull('seed_time')
    //                 ->sortByDesc('seed_time')
    //                 ->values();

    //         $withoutTime = $entries->whereNull('seed_time')
    //                     ->values();

    //         $orderedEntry = collect()->merge($withoutTime)->merge($withTime);
    //         $totalEntry = $orderedEntry->count();
    //         $sisa = $totalEntry % $totalLanes;

    //         // Jika sisa ≤ setengah lane, redistribusi 2 heat pertama
    //         if($totalEntry <= $totalLanes){
    //             $assignEntryToHeat = collect([$orderedEntry]);
    //         }else if ($sisa > 0 && $sisa <= (int) floor($totalLanes / 2)) {
    //             $twoHeatTotal  = $sisa + $totalLanes; // gabung 2 heat pertama
    //             $firstCount    = (int) floor($twoHeatTotal / 2);
    //             $secondCount   = (int) ceil($twoHeatTotal / 2);

    //             $firstHeat     = $orderedEntry->slice(0, $firstCount)->values();
    //             $secondHeat    = $orderedEntry->slice($firstCount, $secondCount)->values();
    //             $restOfHeats   = $orderedEntry->slice($firstCount + $secondCount)->chunk($totalLanes);

    //             $assignEntryToHeat = collect([$firstHeat, $secondHeat])->merge($restOfHeats);
    //         } else {
    //             // Sisa cukup banyak, heat pertama pakai sisa
    //             $firstHeat         = $orderedEntry->slice(0, $sisa ?: $totalLanes)->values();
    //             $restOfHeats       = $orderedEntry->slice($sisa ?: $totalLanes)->chunk($totalLanes);
    //             $assignEntryToHeat = collect([$firstHeat])->merge($restOfHeats);
    //         }

    //         $lanes = $this->generateSnakeLanes($totalLanes);


    //         $arrHeats = [];
    //         foreach($assignEntryToHeat as $heatIndex => $entry){
    //             $arrHeats[$heatIndex]['heat_number'] = $heatIndex + 1;
    //             $entry = $entry->sortBy(function ($e) {
    //                 if (!$e->seed_time) return PHP_INT_MAX;
    //                 [$minute, $second] = explode(':', $e->seed_time);
    //                 return ((int)$minute * 60) + (float)$second;
    //             })->values();

    //             foreach ($entry->values() as $laneIndex => $item) {
    //                 $lane = $lanes[$laneIndex];
    //                 $arrHeats[$heatIndex]['lanes'][$laneIndex]['lane_number'] = $lane;
    //                 $arrHeats[$heatIndex]['lanes'][$laneIndex]['entry_id'] = $item->id;
    //                 $arrHeats[$heatIndex]['lanes'][$laneIndex]['seed_time'] = $item->seed_time;
    //             }
    //         }
    //         return $arrHeats;
    //     }else if(in_array($roundType, [RoundTypeEnum::semi->value, RoundTypeEnum::final->value])){

    //     }else if($roundType === RoundTypeEnum::timed_final->value){

    //     }else{
    //         return null;
    //     }


    // }
}
