<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;

class CompetitionTabEntriesController extends Controller
{
    // belum digunakan
    public function partialReload(Competition $competition){
        return $competition;
        return view('pages.competition.tabs.entries');
    }
}
