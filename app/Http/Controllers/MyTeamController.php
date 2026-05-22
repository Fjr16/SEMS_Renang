<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Enums\License;
use App\Enums\TeamType;
use App\Models\Athlete;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyTeamController extends Controller
{

    public $club;
    public function __construct()
    {
        $this->club = Auth::user()->club;
    }

    public function dashboard(){
        $item = $this->club;
        if(!$item) return back()->with('error', 'Anda belum terhubung ke klub');
        return view('pages.club.dashboard', compact('item'));
    }

    public function athletes(Club $club){
        if(!auth()->user()->hasAnyPermission(['Tim Saya.Kelola Atlet-List', 'Tim Saya.Kelola Atlet-Tambah'])){
            abort(403);
        };
        $q = request('q');
        $gender = request('gender');

        $query = Athlete::query()
        ->where('club_id', $club->id)
        ->with('club')
        ->where('status', 'active')
        ->when($q, function($qq) use ($q){
            $qq->where(function($subQ) use ($q){
                $subQ->where('name', 'LIKE', '%'.$q.'%')
                    ->orWhere('code', 'LIKE', '%'.$q.'%')
                    ->orWhere('registration_number', 'LIKE', '%'.$q.'%')
                    ->orWhereHas('club', function($clubQ) use ($q){
                        $clubQ->where('club_name', 'LIKE', '%'.$q.'%')
                            ->orWhere('club_code', 'LIKE', '%'.$q.'%');
                    });
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
        $genders = Gender::cases();
        return view('pages.guest.atlet.index',compact('athletes', 'accessType', 'club', 'genders'));
    }

    public function officials(){
        if(!auth()->user()->can('Tim Saya.Kelola Official-List')){
            abort(403);
        };
        $club = $this->club;
        $genders = Gender::cases();
        $licenses = License::cases();
        $clubCategories = TeamType::cases();
        return view('pages.club.official-klub', compact('genders', 'clubCategories', 'licenses', 'club'));
    }
}
