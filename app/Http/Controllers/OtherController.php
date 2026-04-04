<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Club;
use App\Models\CompetitionEvent;
use App\Models\Official;
use App\Models\Organization;
use App\Models\Pool;
use App\Models\Venue;
use Illuminate\Http\Request;

class OtherController extends Controller
{
    public function getClubByCategory(Request $r){
        $type = $r->input('team_type');
        $keyword = $r->input('q', '');
        $page = $r->input('page', 1);
        $perPage = 10;

        $query = Club::query()
        ->when($type, function($q) use ($type){
            $q->where('team_type', $type);
        })
        ->when($keyword != '', function($q) use ($keyword){
            $q->where(function($qq) use ($keyword){
                $qq->where('club_name', 'like', "%{$keyword}%")
                   ->orWhere('club_code', 'like', "%{$keyword}%");
            });
        })
        ->orderBy('club_name', 'asc');

        $paginated = $query->simplePaginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginated->items(),
            'pagination' => [
                'more' => $paginated->hasMorePages(),
            ],
        ]);
    }
    public function findAtletById($id){
        try {
            $item = Athlete::findOrFail($id);
            $club = null;
            if($item && $item->club_id){
                $club = $item->club;
            }
            return response()->json([
                'status' => true,
                'data' => [
                    'athlete' => $item,
                    'club' => $club ?? null
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => substr($th->getMessage(), 0,100) || 'Data tidak ditemukan'
            ]);
        }
    }
    public function findOfficialById($id){
        try {
            $item = Official::findOrFail($id);
            $club = null;
            if($item && $item->club_id){
                $club = $item->club;
            }
            return response()->json([
                'status' => true,
                'data' => [
                    'official' => $item,
                    'club' => $club ?? null
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => substr($th->getMessage(), 0,100) || 'Data tidak ditemukan'
            ]);
        }
    }
    public function getOrganization(Request $r){
        $keyword = $r->input('q', '');
        $page = $r->input('page', 1);
        $perPage = 10;

        $query = Organization::query()
                ->when($keyword != '', function($q) use ($keyword){
                    $q->where('name', 'like', "%{$keyword}%");
                })
                ->orderBy('name', 'asc');
        $paginated = $query->simplePaginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginated->items(),
            'pagination' => [
                'more' => $paginated->hasMorePages(),
            ],
        ]);
    }
    public function getVenue(Request $r){
        $keyword = $r->input('q', '');
        $page = $r->input('page', 1);
        $perPage = 10;

        $query = Venue::query()
                ->where('is_active', true)
                ->when($keyword != '', function($q) use ($keyword){
                    $q->where('code', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%");
                })
                ->orderBy('name', 'asc');
        $paginated = $query->simplePaginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginated->items(),
            'pagination' => [
                'more' => $paginated->hasMorePages(),
            ],
        ]);
    }

    public function getAllEvent(Request $r){
        $competition_id = $r->input('competition_id');
        $keyword = $r->input('q', '');
        $page = $r->input('page', 1);
        $perPage = 10;

        $query = CompetitionEvent::query()
        ->whereHas('competitionSession', function($sesi) use ($competition_id){
            $sesi->where('competition_id', $competition_id);
        })
        ->when($keyword != '', function($q) use ($keyword){
            $q->where(function($qq) use ($keyword){
                $qq->where('event_number', 'like', "%{$keyword}%")
                   ->orWhere('stroke', 'like', "%{$keyword}%")
                   ->orWhere('gender', 'like', "%{$keyword}%")
                   ->orWhere('event_type', 'like', "%{$keyword}%");
            });
        })
        ->orderBy('event_number', 'asc');

        $paginated = $query->simplePaginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginated->items(),
            'pagination' => [
                'more' => $paginated->hasMorePages(),
            ],
        ]);
    }

    // public function getPoolByVenue(Request $r){
    //     $keyword = $r->input('q', '');
    //     $page = $r->input('page', 1);
    //     $perPage = 10;
    //     $venue_id = $r->input('venue_id');

    //     $query = Pool::query()
    //             ->where('venue_id', $venue_id)
    //             ->where('status', 'active')
    //             ->when($keyword != '', function($q) use ($keyword){
    //                 $q->where('code', 'like', "%{$keyword}%")
    //                 ->orWhere('name', 'like', "%{$keyword}%");
    //             })
    //             ->orderBy('name', 'asc');
    //     $paginated = $query->simplePaginate($perPage, ['*'], 'page', $page);

    //     return response()->json([
    //         'data' => $paginated->items(),
    //         'pagination' => [
    //             'more' => $paginated->hasMorePages(),
    //         ],
    //     ]);
    // }
}
