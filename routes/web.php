<?php

use App\Http\Controllers\AthleteController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\CompetitionSessionController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\OtherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.dashboard');
})->name('dashboard');

Route::prefix('/master/club')->group(function(){
    Route::get('/', [ClubController::class, 'index'])->name('club.index');
    Route::get('/data', [ClubController::class, 'data'])->name('club.data');
    Route::post('/store', [ClubController::class, 'store'])->name('club.store');
    Route::delete('/destroy/{id}', [ClubController::class, 'destroy'])->name('club.destroy');
});
Route::prefix('/master/atlet')->group(function(){
    Route::get('/', [AthleteController::class, 'index'])->name('atlet.index');
    Route::get('/data', [AthleteController::class, 'data'])->name('atlet.data');
    Route::post('/store', [AthleteController::class, 'store'])->name('atlet.store');
    Route::delete('/destroy/{id}', [AthleteController::class, 'destroy'])->name('atlet.destroy');
});
Route::prefix('/master/official')->group(function(){
    Route::get('/', [OfficialController::class, 'index'])->name('official.index');
    Route::get('/data', [OfficialController::class, 'data'])->name('official.data');
    Route::post('/store', [OfficialController::class, 'store'])->name('official.store');
    Route::delete('/destroy/{id}', [OfficialController::class, 'destroy'])->name('official.destroy');
});
Route::prefix('/master/competition')->group(function(){
    Route::get('/', [CompetitionController::class, 'index'])->name('competition.index');
    Route::get('/data', [CompetitionController::class, 'data'])->name('competition.data');
    Route::post('/store', [CompetitionController::class, 'store'])->name('competition.store');
    Route::delete('/destroy/{id}', [CompetitionController::class, 'destroy'])->name('competition.destroy');
});

Route::prefix('/competition/{competition}')->group(function(){
    Route::get('/', [CompetitionController::class, 'show'])->name('competition.show');

    // tiap tab sebagai partial HTML (untuk Bootstrap tab)
    Route::get('/tab/sessions', [CompetitionSessionController::class, 'index'])->name('competition.tab.sessions');
    Route::get('/tab/sessions/store', [CompetitionSessionController::class, 'store'])->name('competition.tab.sessions.store');
    Route::get('/tab/sessions/destroy/{id}', [CompetitionSessionController::class, 'destroy'])->name('competition.tab.sessions.destroy');

    Route::get('/tab/events',   [CompetitionSessionController::class, 'events'])->name('competition.tab.events');
    Route::get('/tab/entries',  [CompetitionSessionController::class, 'entries'])->name('competition.tab.entries');
    Route::get('/tab/heats',    [CompetitionSessionController::class, 'heats'])->name('competition.tab.heats');
    Route::get('/tab/results',  [CompetitionSessionController::class, 'results'])->name('competition.tab.results');
    Route::get('/tab/points',   [CompetitionSessionController::class, 'points'])->name('competition.tab.points');
    Route::get('/tab/officials',[CompetitionSessionController::class, 'officials'])->name('competition.tab.officials');
    Route::get('/tab/payments', [CompetitionSessionController::class, 'payments'])->name('competition.tab.payments');
});

Route::get('/club', function(){
    return view('pages.club.index');
})->name('club');

Route::get('/events', function(){
    return view('pages.competition.events.index');
})->name('events');
Route::get('/events/entries', function(){
    return view('pages.competition.events.show');
})->name('events.entries');

Route::get('/entries', function(){
    return view('pages.entries.index');
})->name('entries');
Route::get('/heats', function(){
    return view('pages.heats.index');
})->name('heats');
Route::get('/startlist', function(){
    return view('pages.startlist.index');
})->name('startlist');
Route::get('/results', function(){
    return view('pages.result.index');
})->name('results');

Route::get('/users', function () {
    return view('pages.users.index');
})->name('users');
Route::get('/users/detail', function () {
    return view('pages.users.show');
})->name('users.detail');


Route::get('/select2/getClubByCategory', [OtherController::class, 'getClubByCategory'])->name('getClubByCategory');
Route::get('/findAtletById/{id}', [OtherController::class, 'findAtletById'])->name('findAtletById');
Route::get('/findOfficialById/{id}', [OtherController::class, 'findOfficialById'])->name('findOfficialById');
