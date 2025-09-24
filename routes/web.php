<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.dashboard');
})->name('dashboard');

Route::get('/atlet', function(){
    return view('pages.atlet.index');
})->name('atlet');
Route::get('/club', function(){
    return view('pages.club.index');
})->name('club');
Route::get('/competition', function(){
    return view('pages.competition.index');
})->name('competition');
Route::get('/startlist', function(){
    return view('pages.startlist.index');
})->name('startlist');
Route::get('/results', function(){
    return view('pages.result.index');
})->name('results');