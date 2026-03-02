<?php

namespace App\Http\Controllers;

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompetitionEntryController extends Controller
{
    public function index(){
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

        return view('pages.club.registrations.index',compact('data', 'compClass'));
    }

    public function create(Competition $competition){
        $events = $competition->events;
        $club = Auth::user()->club;
        $athletes = $club->athletes;
        $officials = $club->officials;
        return view('pages.club.registrations.create', [
            'comp' => $competition,
            'events' => $events,
            'club' => $club,
            'athletes' => $athletes,
            'officials' => $officials,
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

    // public function store(Request $r){
    //     $validators = Validator::make($r->all(), [
    //         'atlet_ids' => 'required|array',
    //         'atlet_ids.*' => 'required|exists:athletes,id',
    //         'event_ids' => 'required|array',
    //         'event_ids.*' => 'required|exists:competition_events,id',


    //     ]);
    // }
}
