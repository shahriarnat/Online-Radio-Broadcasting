<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\MusicController;

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
    Route::get('all', [MusicController::class, 'index'])->name('music.all');
    Route::get('show/{id}', [MusicController::class, 'show'])->name('music.show');
    Route::post('store', [MusicController::class, 'store'])->name('music.store');
    Route::post('update/{id}', [MusicController::class, 'update'])->name('music.update');
    Route::delete('delete/{id}', [MusicController::class, 'destroy'])->name('music.delete');
    Route::get('genre/all', [MusicController::class, 'genres'])->name('genre.all');
});

