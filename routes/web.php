<?php

use App\Http\Controllers\AgeGroupController;
use App\Http\Controllers\AthleteController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\CompetitionEntryController;
use App\Http\Controllers\CompetitionEventController;
use App\Http\Controllers\CompetitionHeatLaneController;
use App\Http\Controllers\CompetitionSessionController;
use App\Http\Controllers\CompetitionTabEntriesController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\MyTeamController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OtherController;
use App\Http\Controllers\RolesPermissionsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VenuesAndPoolController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('guest.competition.index');
})->name('dashboard');

Route::middleware(['auth'])->group(function(){
    Route::get('/users/profile/{id}', [UserController::class, 'profile'])->name('user.profile');
    Route::prefix('/master')->group(function(){
        Route::get('/', function(){
            return view('pages.master.index');
        })->name('master.setting.index');

        Route::prefix('/venue')->group(function(){
            Route::get('/', [VenuesAndPoolController::class, 'index'])->name('master.venue.pools.index')->middleware('permission:Master Setting.Lokasi & Kolam-List');
            Route::get('/data', [VenuesAndPoolController::class, 'venueData'])->name('master.venue.data')->middleware('permission:Master Setting.Lokasi & Kolam-List');
            Route::post('/store', [VenuesAndPoolController::class, 'storeVenue'])->name('master.venue.store')->middleware('permission:Master Setting.Lokasi & Kolam-Tambah|Master Setting.Lokasi & Kolam-Ubah');
            Route::delete('/destroy/{id}', [VenuesAndPoolController::class, 'destroyVenue'])->name('master.venue.destroy')->middleware('permission:Master Setting.Lokasi & Kolam-Hapus');

            Route::get('/pools/data', [VenuesAndPoolController::class, 'poolData'])->name('master.pool.data')->middleware('permission:Master Setting.Lokasi & Kolam-List');
            Route::post('/pools/store', [VenuesAndPoolController::class, 'storePool'])->name('master.venue.pool.store')->middleware('permission:Master Setting.Lokasi & Kolam-Tambah|Master Setting.Lokasi & Kolam-Ubah');
            Route::delete('/pools/destroy/{id}', [VenuesAndPoolController::class, 'destroyPool'])->name('master.venue.pool.destroy')->middleware('permission:Master Setting.Lokasi & Kolam-Hapus');
        });
        Route::prefix('/competition')->group(function(){
            Route::get('/', [CompetitionController::class, 'index'])->name('competition.index')->middleware('permission:Master Setting.Kompetisi-List');
            Route::get('/data', [CompetitionController::class, 'data'])->name('competition.data')->middleware('permission:Master Setting.Kompetisi-List');
            Route::post('/store', [CompetitionController::class, 'store'])->name('competition.store')->middleware('permission:Master Setting.Kompetisi-Tambah|Master Setting.Kompetisi-Ubah');
            Route::delete('/destroy/{id}', [CompetitionController::class, 'destroy'])->name('competition.destroy')->middleware('Master Setting.Kompetisi-Hapus');
        });
        Route::prefix('/club')->group(function(){
            Route::get('/', [ClubController::class, 'index'])->name('club.index')->middleware('permission:Master Setting.Klub-List');
            Route::get('/data', [ClubController::class, 'data'])->name('club.data')->middleware('permission:Master Setting.Klub-List');
            Route::post('/store', [ClubController::class, 'store'])->name('club.store')->middleware('permission:Master Setting.Klub-Tambah|Master Setting.Klub-Ubah');
            Route::delete('/destroy/{id}', [ClubController::class, 'destroy'])->name('club.destroy')->middleware('permission:Master Setting.Klub-Hapus');
        });
        Route::prefix('/atlet')->group(function(){
            Route::get('/', [AthleteController::class, 'index'])->name('atlet.index')->middleware('permission:Master Setting.Atlet-List');
            Route::get('/data', [AthleteController::class, 'data'])->name('atlet.data')->middleware('permission:Master Setting.Atlet-List');
            Route::post('/store', [AthleteController::class, 'store'])->name('atlet.store')->middleware('permission:Master Setting.Atlet-Tambah|Master Setting.Atlet-Ubah|Tim Saya.Kelola Atlet-Tambah|Tim Saya.Kelola Atlet-Ubah');
            Route::delete('/destroy/{id}', [AthleteController::class, 'destroy'])->name('atlet.destroy')->middleware('permission:Master Setting.Atlet-Hapus');
        });
        Route::prefix('/official')->group(function(){
            Route::get('/', [OfficialController::class, 'index'])->name('official.index')->middleware('permission:Master Setting.Official-List');
            Route::get('/data', [OfficialController::class, 'data'])->name('official.data')->middleware('permission:Master Setting.Official-List|Tim Saya.Kelola Official-List');
            Route::post('/store', [OfficialController::class, 'store'])->name('official.store')->middleware('permission:Master Setting.Official-Tambah|Master Setting.Official-Ubah|Tim Saya.Kelola Official-Tambah|Tim Saya.Kelola Official-Ubah');
            Route::delete('/destroy/{id}', [OfficialController::class, 'destroy'])->name('official.destroy')->middleware('permission:Master Setting.Official-Hapus|Tim Saya.Kelola Official-Hapus');
        });

        Route::prefix('/users')->group(function(){
            Route::get('/', [UserController::class, 'index'])->name('users.index')->middleware('permission:Master Setting.User Hak Akses-List');
            Route::get('/datatables', [UserController::class, 'getDataTables'])->name('users.get')->middleware('permission:Master Setting.User Hak Akses-List');

            Route::post('/store', [UserController::class, 'store'])->name('users.store')->middleware('permission:Master Setting.User Hak Akses-Tambah User|Master Setting.User Hak Akses-Ubah User');

            Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:Master Setting.User Hak Akses-Hapus User');
            Route::post('/{id}/restore', [UserController::class, 'restore'])->name('users.restore')->middleware('permission:Master Setting.User Hak Akses-Hapus User');

            Route::get('/{id}/roles', [UserController::class, 'userRoles']);
            Route::post('/{id}/roles/sync', [UserController::class, 'syncUserRoles'])->middleware('permission:Master Setting.User Hak Akses-Tambah Role User');
        });

        Route::prefix('/roles')->group(function(){
            Route::get('/datatables', [RolesPermissionsController::class, 'rolesData'])->name('roles.get')->middleware('permission:Master Setting.User Hak Akses-List');

            Route::post('/store', [RolesPermissionsController::class, 'storeRole'])->name('roles.store')->middleware('permission:Master Setting.User Hak Akses-Tambah Role|Master Setting.User Hak Akses-Ubah Role');

            Route::get('/{id}/permissions', [RolesPermissionsController::class, 'rolePermissions']);
            Route::post('/{id}/permissions/sync', [RolesPermissionsController::class, 'syncRolePermissions'])->middleware('permission:Master Setting.User Hak Akses-Tambah Hak Akses Role');

        });

        Route::prefix('/age/group')->group(function(){
            Route::get('/', [AgeGroupController::class, 'index'])->name('age.group.index')->middleware('permission:Master Setting.Kelompok Umur-List');
            Route::get('/data', [AgeGroupController::class, 'data'])->name('age.group.data')->middleware('permission:Master Setting.Kelompok Umur-List');
            Route::post('/store', [AgeGroupController::class, 'store'])->name('age.group.store')->middleware('permission:Master Setting.Kelompok Umur-Tambah|Master Setting.Kelompok Umur-Ubah');
            Route::delete('/destroy/{id}', [AgeGroupController::class, 'destroy'])->name('age.group.destroy')->middleware('permission:Master Setting.Kelompok Umur-Hapus');
        });
        Route::prefix('/organization')->group(function(){
            Route::get('/', [OrganizationController::class, 'index'])->name('organizations.index')->middleware('permission:Master Setting.Organisasi-List');
            Route::get('/data', [OrganizationController::class, 'data'])->name('organizations.data')->middleware('permission:Master Setting.Organisasi-List');
            Route::post('/store', [OrganizationController::class, 'store'])->name('organizations.store')->middleware('permission:Master Setting.Organisasi-Tambah|Master Setting.Organisasi-Ubah');
            Route::delete('/destroy/{id}', [OrganizationController::class, 'destroy'])->name('organizations.destroy')->middleware('permission:Master Setting.Organisasi-Hapus');
        });
    });
    // manager club menu
    Route::prefix('/club')->group(function() {
        Route::get('/dashboard', [MyTeamController::class, 'dashboard'])->name('manager.club.dashboard')->middleware('permission:Tim Saya.Dashboard');

        // entries
        Route::get('/registrations', [CompetitionEntryController::class, 'index'])->name('manager.club.registration')->middleware('permission:Tim Saya.Pendaftaran Kompetisi');
        Route::get('/registrations/create/{competition}', [CompetitionEntryController::class, 'create'])->name('manager.club.registration.create')->middleware('permission:Tim Saya.Pendaftaran Kompetisi');
        Route::post('/registrations/store', [CompetitionEntryController::class, 'store'])->name('manager.club.registration.store')->middleware('permission:Tim Saya.Pendaftaran Kompetisi');
        // entries

        Route::get('/atlet/{club}', [MyTeamController::class, 'athletes'])->name('manager.club.atlet')->middleware('permission:Tim Saya.Kelola Atlet-List');
        Route::get('/official', [MyTeamController::class, 'officials'])->name('manager.club.official')->middleware('permission:Tim Saya.Kelola Official-List');
    });

    Route::prefix('/export')->group(function(){
        // Route::get('/undangan/kejurda', [ExportController::class, 'undanganKejurda'])->name('export.undangan.kejurda');
        Route::post('/starting/list', [ExportController::class, 'startingList'])->name('export.starting.list');
        Route::post('/buku/acara', [ExportController::class, 'bukuAcara'])->name('export.buku.acara');
        Route::post('/buku/hasil', [ExportController::class, 'bukuHasil'])->name('export.buku.hasil');
        Route::get('/rekap/medali/kontingen', [ExportController::class, 'rekapMedali'])->name('export.rekap.medali');
        Route::get('/best/club', [ExportController::class, 'bestClub'])->name('export.best.club');
        Route::get('/best/swimmer', [ExportController::class, 'bestSwimmer'])->name('export.best.swimmer');
    });

    Route::prefix('/competition/{competition}')->group(function(){
        Route::get('/', [CompetitionController::class, 'show'])->name('competition.show');
        Route::put('/update-status', [CompetitionController::class, 'updateStatus'])->name('competition.updateStatus');

        // tiap tab sebagai partial HTML (untuk Bootstrap tab)
        Route::get('/tab/sessions/data', [CompetitionSessionController::class, 'data'])->name('competition.tab.sessions.data');
        Route::post('/tab/sessions/store', [CompetitionSessionController::class, 'store'])->name('competition.tab.sessions.store');
        Route::delete('/tab/sessions/destroy/{id}', [CompetitionSessionController::class, 'destroy'])->name('competition.tab.sessions.destroy');

        Route::get('/tab/events/partial/reload', [CompetitionEventController::class, 'partialReload'])->name('competition.tab.events.partial');
        Route::post('/tab/events/store', [CompetitionEventController::class, 'store'])->name('competition.tab.events.store');
        Route::get('tab/events/edit/{event}', [CompetitionEventController::class, 'edit'])->name('competition.tab.events.edit');
        Route::put('/tab/events/update/{event}', [CompetitionEventController::class, 'update'])->name('competition.tab.events.update');
        Route::delete('/tab/events/destroy/{event}', [CompetitionEventController::class, 'destroy'])->name('competition.tab.events.destroy');

        Route::get('/tab/entries/partial/reload', [CompetitionTabEntriesController::class, 'partialReload'])->name('competition.tab.entries.partial');
        Route::post('/tab/entries/update', [CompetitionTabEntriesController::class, 'store'])->name('competition.tab.entries.store');
        Route::post('/tab/entries/verification', [CompetitionTabEntriesController::class, 'verification'])->name('competition.tab.entries.verification');
        Route::post('/tab/entries/update-seed-time', [CompetitionTabEntriesController::class, 'updateSeedTime'])->name('competition.tab.entries.updateSeedTime');
        Route::post('/tab/entries/update-status-entry', [CompetitionTabEntriesController::class, 'updateStatusEntry'])->name('competition.tab.entries.updateStatusEntry');
        Route::delete('/tab/entries/delete-entry/{id}', [CompetitionTabEntriesController::class, 'destroyEntry'])->name('competition.tab.entries.deleteEntry');

        // Route::get('/tab/results',  [CompetitionSessionController::class, 'results'])->name('competition.tab.results');
        // Route::get('/tab/points',   [CompetitionSessionController::class, 'points'])->name('competition.tab.points');
        // Route::get('/tab/officials',[CompetitionSessionController::class, 'officials'])->name('competition.tab.officials');
        // Route::get('/tab/payments', [CompetitionSessionController::class, 'payments'])->name('competition.tab.payments');

        Route::get('/heats/',          [CompetitionHeatLaneController::class, 'partialReload'])->name('competition.heats.partial');
        Route::post('/heats/generate', [CompetitionHeatLaneController::class, 'generate'])->name('competition.heats.generate');
        Route::delete('/heats/generate/by/round',  [CompetitionHeatLaneController::class, 'generateByRound'])->name('competition.heats.generateByRound');
        Route::post('/heats/reset/by/event', [CompetitionHeatLaneController::class, 'resetByEvent'])->name('competition.heats.resetByEvent');
        Route::post('/heats/save/result', [CompetitionHeatLaneController::class, 'saveResult'])->name('competition.heats.saveResult');
        Route::post('/heats/promote/atlet', [CompetitionHeatLaneController::class, 'promoteAthletes'])->name('competition.heats.promoteAthletes');
    })->middleware('permission:Master Setting.Kompetisi-Kelola');
});

Route::prefix('/guest')->group(function(){
    Route::get('/atlet', [AthleteController::class, 'indexGuest'])->name('guest.atlet.index');
    Route::get('/atlet/show/{athlete_id}', [AthleteController::class, 'showGuest'])->name('guest.atlet.show');
    Route::get('/competition', [CompetitionEntryController::class, 'indexGuest'])->name('guest.competition.index');
});


Route::get('/select2/getClubByCategory', [OtherController::class, 'getClubByCategory'])->name('getClubByCategory');
Route::get('/findAtletById/{id}', [OtherController::class, 'findAtletById'])->name('findAtletById');
Route::get('/findOfficialById/{id}', [OtherController::class, 'findOfficialById'])->name('findOfficialById');
Route::get('/select2/getOrganization', [OtherController::class, 'getOrganization'])->name('getOrganization');
Route::get('/select2/getVenue', [OtherController::class, 'getVenue'])->name('getVenue');
// Route::get('/select2/getAllEvent', [OtherController::class, 'getAllEvent'])->name('getAllEvent');

require __DIR__ . '/auth.php';
