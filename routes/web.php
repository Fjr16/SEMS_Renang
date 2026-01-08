<?php

use App\Http\Controllers\AthleteController;
use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\CompetitionEventController;
use App\Http\Controllers\CompetitionSessionController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\OtherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenuesAndPoolController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.dashboard');
})->name('dashboard');

Route::prefix('/master')->group(function(){
    Route::get('/', function(){
        return view('pages.master.index');
    })->name('master.setting.index');

    Route::prefix('/venue')->group(function(){
        Route::get('/', [VenuesAndPoolController::class, 'index'])->name('master.venue.pools.index');
        Route::get('/data', [VenuesAndPoolController::class, 'venueData'])->name('master.venue.data');
        Route::post('/store', [VenuesAndPoolController::class, 'storeVenue'])->name('master.venue.store');
        Route::delete('/destroy/{id}', [VenuesAndPoolController::class, 'destroyVenue'])->name('master.venue.destroy');

        Route::get('/pools/data', [VenuesAndPoolController::class, 'poolData'])->name('master.pool.data');
        Route::post('/pools/store', [VenuesAndPoolController::class, 'storePool'])->name('master.venue.pool.store');
        Route::delete('/pools/destroy/{id}', [VenuesAndPoolController::class, 'destroyPool'])->name('master.venue.pool.destroy');
    });
    Route::prefix('/competition')->group(function(){
        Route::get('/', [CompetitionController::class, 'index'])->name('competition.index');
        Route::get('/data', [CompetitionController::class, 'data'])->name('competition.data');
        Route::post('/store', [CompetitionController::class, 'store'])->name('competition.store');
        Route::delete('/destroy/{id}', [CompetitionController::class, 'destroy'])->name('competition.destroy');
    });
    Route::prefix('/club')->group(function(){
        Route::get('/', [ClubController::class, 'index'])->name('club.index');
        Route::get('/data', [ClubController::class, 'data'])->name('club.data');
        Route::post('/store', [ClubController::class, 'store'])->name('club.store');
        Route::delete('/destroy/{id}', [ClubController::class, 'destroy'])->name('club.destroy');
    });
    Route::prefix('/atlet')->group(function(){
        Route::get('/', [AthleteController::class, 'index'])->name('atlet.index');
        Route::get('/data', [AthleteController::class, 'data'])->name('atlet.data');
        Route::post('/store', [AthleteController::class, 'store'])->name('atlet.store');
        Route::delete('/destroy/{id}', [AthleteController::class, 'destroy'])->name('atlet.destroy');
    });
    Route::prefix('/official')->group(function(){
        Route::get('/', [OfficialController::class, 'index'])->name('official.index');
        Route::get('/data', [OfficialController::class, 'data'])->name('official.data');
        Route::post('/store', [OfficialController::class, 'store'])->name('official.store');
        Route::delete('/destroy/{id}', [OfficialController::class, 'destroy'])->name('official.destroy');
    });

    Route::prefix('/users')->group(function(){
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/datatables', [UserController::class, 'getDataTables'])->name('users.get');

        Route::post('/store', [UserController::class, 'store'])->name('users.store');

        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/{id}/roles', [UserController::class, 'userRoles']);
        Route::post('/{id}/roles/sync', [UserController::class, 'syncUserRoles']);

        Route::get('/{id}/permissions', [UserController::class, 'userPermissions']);
        Route::post('/{id}/permissions/sync', [UserController::class, 'syncUserPermissions']);

        // Route::get('/users', function () {
        //     return view('pages.users.index');
        // })->name('users');
        // Route::get('/users/detail', function () {
        //     return view('pages.users.show');
        // })->name('users.detail');
    });
});

// Route::prefix('/master/club')->group(function(){
//     Route::get('/', [ClubController::class, 'index'])->name('club.index');
//     Route::get('/data', [ClubController::class, 'data'])->name('club.data');
//     Route::post('/store', [ClubController::class, 'store'])->name('club.store');
//     Route::delete('/destroy/{id}', [ClubController::class, 'destroy'])->name('club.destroy');
// });
// Route::prefix('/master/atlet')->group(function(){
//     Route::get('/', [AthleteController::class, 'index'])->name('atlet.index');
//     Route::get('/data', [AthleteController::class, 'data'])->name('atlet.data');
//     Route::post('/store', [AthleteController::class, 'store'])->name('atlet.store');
//     Route::delete('/destroy/{id}', [AthleteController::class, 'destroy'])->name('atlet.destroy');
// });
// Route::prefix('/master/official')->group(function(){
//     Route::get('/', [OfficialController::class, 'index'])->name('official.index');
//     Route::get('/data', [OfficialController::class, 'data'])->name('official.data');
//     Route::post('/store', [OfficialController::class, 'store'])->name('official.store');
//     Route::delete('/destroy/{id}', [OfficialController::class, 'destroy'])->name('official.destroy');
// });
// Route::prefix('/master/competition')->group(function(){
//     Route::get('/', [CompetitionController::class, 'index'])->name('competition.index');
//     Route::get('/data', [CompetitionController::class, 'data'])->name('competition.data');
//     Route::post('/store', [CompetitionController::class, 'store'])->name('competition.store');
//     Route::delete('/destroy/{id}', [CompetitionController::class, 'destroy'])->name('competition.destroy');
// });

Route::prefix('/competition/{competition}')->group(function(){
    Route::get('/', [CompetitionController::class, 'show'])->name('competition.show');

    // tiap tab sebagai partial HTML (untuk Bootstrap tab)
    Route::get('/tab/sessions/data', [CompetitionSessionController::class, 'data'])->name('competition.tab.sessions.data');
    Route::post('/tab/sessions/store', [CompetitionSessionController::class, 'store'])->name('competition.tab.sessions.store');
    Route::delete('/tab/sessions/destroy/{id}', [CompetitionSessionController::class, 'destroy'])->name('competition.tab.sessions.destroy');

    Route::get('/tab/events/data', [CompetitionEventController::class, 'data'])->name('competition.tab.events.data');
    Route::post('/tab/events/store', [CompetitionEventController::class, 'store'])->name('competition.tab.events.store');
    Route::delete('/tab/events/destroy/{id}', [CompetitionEventController::class, 'destroy'])->name('competition.tab.events.destroy');

    Route::get('/tab/entries',  [CompetitionSessionController::class, 'entries'])->name('competition.tab.entries');
    Route::get('/tab/heats',    [CompetitionSessionController::class, 'heats'])->name('competition.tab.heats');
    Route::get('/tab/results',  [CompetitionSessionController::class, 'results'])->name('competition.tab.results');
    Route::get('/tab/points',   [CompetitionSessionController::class, 'points'])->name('competition.tab.points');
    Route::get('/tab/officials',[CompetitionSessionController::class, 'officials'])->name('competition.tab.officials');
    Route::get('/tab/payments', [CompetitionSessionController::class, 'payments'])->name('competition.tab.payments');
});

Route::prefix('/guest')->group(function(){
    Route::get('/atlet', [AthleteController::class, 'indexGuest'])->name('guest.atlet.index');
    Route::get('/atlet/show', [AthleteController::class, 'showGuest'])->name('guest.atlet.show');
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

// Route::get('/entries', function(){
//     return view('pages.entries.index');
// })->name('entries');
// Route::get('/heats', function(){
//     return view('pages.heats.index');
// })->name('heats');
// Route::get('/startlist', function(){
//     return view('pages.startlist.index');
// })->name('startlist');
// Route::get('/results', function(){
//     return view('pages.result.index');
// })->name('results');


Route::get('/select2/getClubByCategory', [OtherController::class, 'getClubByCategory'])->name('getClubByCategory');
Route::get('/findAtletById/{id}', [OtherController::class, 'findAtletById'])->name('findAtletById');
Route::get('/findOfficialById/{id}', [OtherController::class, 'findOfficialById'])->name('findOfficialById');
