<?php

namespace App\Http\Controllers;

use App\Enums\CompetitionStatus;
use App\Models\Athlete;
use App\Models\Club;
use App\Models\Competition;
use Illuminate\Http\Request;

class MyTeamController extends Controller
{
    public function dashboard(){
        $item = Club::first();
        return view('pages.club.dashboard', compact('item'));
    }
    // public function indexRegistComp(){
    //     return view('pages.club.registrations.index');
    // }
    public function showRegistComp(){
        return view('pages.club.registrations.show');
    }
    public function storeRegistComp(){
        return 'berhasil';
    }

    public function athletes(Club $club){
        $q = request('q');
        $gender = request('gender');
        $province = request('province');

        $query = Athlete::query()
        ->where('club_id', $club->id)
        ->with('club')
        ->when($q, function($qq) use ($q){
            $qq->where(function($subQ) use ($q){
                $subQ->where('name', 'LIKE', '%'.$q.'%')
                    ->orWhere('code', 'LIKE', '%'.$q.'%')
                    ->orWhere('club_name', 'LIKE', '%'.$q.'%')
                    ->orWhere('city_name', 'LIKE', '%'.$q.'%')
                    ->orWhere('school_name', 'LIKE', '%'.$q.'%')
                    ->orWhere('province_name', 'LIKE', '%'.$q.'%');
            });
        })
        ->when($gender, function($qq) use ($gender){
            $qq->where('gender', strtolower($gender));
        })
        ->orderBy('name', 'asc');
        $athletes = $query->paginate(21)->withQueryString();

        if(request()->ajax()){
            return view('pages.guest.atlet.partials.cards', compact('athletes'))->render();
        }

        $accessType = 'Manajer Tim';
        return view('pages.guest.atlet.index',compact('athletes', 'accessType'));
    }

    public function indexRegistComp(){
        $q = request('q');
        $stts = request('status', null);

        $query = Competition::query()
        ->where('status', $stts ?? CompetitionStatus::register->value)
        ->when($q, function($qq) use ($q){
            $qq->where(function($subQ) use ($q){
                $subQ->where('name', 'LIKE', '%'.$q.'%')
                    ->orWhere('organizer', 'LIKE', '%'.$q.'%')
                    ->orWhere('location', 'LIKE', '%'.$q.'%');
            });
        })
        ->orderBy('created_at', 'desc');
        $data = $query->paginate(21)->withQueryString();
        // $historyEntries =

        if(request()->ajax()){
            return view('pages.club.registrations.partials.cards', compact('data'))->render();
        }

        $compStts = CompetitionStatus::cases();
        return view('pages.club.registrations.index',compact('data', 'compStts'));
    }
}
