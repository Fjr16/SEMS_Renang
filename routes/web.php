<?php

use App\Http\Controllers\ClubController;
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

Route::get('/atlet', function(){
    return view('pages.atlet.index');
})->name('atlet');
Route::get('/club', function(){
    return view('pages.club.index');
})->name('club');
Route::get('/competition', function(){
    return view('pages.competition.index');
})->name('competition');
Route::get('/competition/show', function(){
    return view('pages.competition.show');
})->name('competition.show');

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
