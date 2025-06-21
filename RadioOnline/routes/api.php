<?php

use App\Http\Controllers\LiveController;
use App\Http\Middleware\VisitorsMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BroadcasterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\GeneralController;

Route::prefix('general')->group(function () {
    Route::get('info', [GeneralController::class, 'info'])->name('general.info')->middleware(VisitorsMiddleware::class);
    Route::post('like', [MusicController::class, 'like'])->name('general.like');
});

/*
 * Routes for authentication, including login and logout functionality.
 * The logout route is protected by the 'auth:sanctum' middleware.
 */
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:sanctum');
});
/*
 * The following section defines routes for the 'playlist' resource, which are protected by the 'auth:sanctum' middleware.
 * These routes allow authenticated users to perform CRUD operations on playlists, such as retrieving all playlists,
 * viewing a specific playlist, creating a new playlist, updating an existing playlist, deleting a playlist,
 * and updating the position of music within a playlist.
 */
Route::prefix('playlist')->middleware('auth:sanctum')->group(function () {
    Route::get('all', [PlaylistController::class, 'index'])->name('playlist.all');
    Route::get('show/{id}', [PlaylistController::class, 'show'])->name('playlist.show');
    Route::post('store', [PlaylistController::class, 'store'])->name('playlist.store');
    Route::put('update/{id}', [PlaylistController::class, 'update'])->name('playlist.update');
    Route::delete('delete/{id}', [PlaylistController::class, 'destroy'])->name('playlist.delete');
    Route::put('update-position', [PlaylistController::class, 'updateMusicPosition'])->name('playlist.updateMusicPosition');
});

/*
 * The following section defines routes for the 'music' resource, which are protected by the 'auth:sanctum' middleware.
 * These routes allow authenticated users to perform CRUD operations on music records, such as retrieving all music,
 * viewing a specific music record, creating a new music record, updating an existing music record, and deleting a music record.
 * Additionally, it includes a route to retrieve all available genres associated with music records.
 */
Route::prefix('music')->middleware('auth:sanctum')->group(function () {
    Route::get('properties', [MusicController::class, 'properties'])->name('music.properties');
    Route::get('all', [MusicController::class, 'index'])->name('music.all');
    Route::get('show/{id}', [MusicController::class, 'show'])->name('music.show');
    Route::post('store', [MusicController::class, 'store'])->name('music.store');
    Route::post('update/{id}', [MusicController::class, 'update'])->name('music.update');
    Route::put('assign', [MusicController::class, 'assign'])->name('music.assignMusicPlaylist');
    Route::put('assign-bulk', [MusicController::class, 'assignBulk'])->name('music.assignBulkMusicPlaylist');
    Route::delete('delete/{id}', [MusicController::class, 'destroy'])->name('music.delete');
    Route::get('genre/all', [MusicController::class, 'genres'])->name('genre.all');
});

/*
 * The following section defines routes for the 'live' resource, which are protected by the 'auth:sanctum' middleware.
 * These routes allow authenticated users to retrieve all live sessions, create a new live session, and delete an existing live session.
 */
Route::prefix('live')->middleware('auth:sanctum')->group(function () {
    Route::get('all', [LiveController::class, 'index'])->name('live.all');
    Route::post('store', [LiveController::class, 'store'])->name('live.store');
    Route::delete('delete/{id}', [LiveController::class, 'destroy'])->name('live.delete');
});

/*
* The following section defines routes for the 'broadcaster' resource, which are protected by the 'auth:sanctum' middleware.
* These routes allow authenticated users to retrieve listener information for a specific channel and fetch overall broadcaster statistics.
*/
Route::prefix('broadcaster')->middleware('auth:sanctum')->group(function () {
    Route::get('listener', [BroadcasterController::class, 'listener'])->name('broadcaster.listener');
    Route::get('stats', [BroadcasterController::class, 'stats'])->name('broadcaster.stats');
});
