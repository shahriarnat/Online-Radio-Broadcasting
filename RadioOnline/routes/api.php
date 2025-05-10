<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\MusicController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:sanctum');
});

Route::prefix('playlist')/*->middleware('auth:sanctum')*/ ->group(function () {
    Route::get('all', [PlaylistController::class, 'index'])->name('playlist.all');
    Route::get('show/{id}', [PlaylistController::class, 'show'])->name('playlist.show');
    Route::post('store', [PlaylistController::class, 'store'])->name('playlist.store');
    Route::put('update/{id}', [PlaylistController::class, 'update'])->name('playlist.update');
    Route::delete('delete/{id}', [PlaylistController::class, 'destroy'])->name('playlist.delete');
    Route::put('update-position', [PlaylistController::class, 'updateMusicPosition'])->name('playlist.updateMusicPosition');
});

Route::prefix('music')/*->middleware('auth:sanctum')*/ ->group(function () {
    Route::get('all', [MusicController::class, 'index'])->name('music.all');
    Route::get('show/{id}', [MusicController::class, 'show'])->name('music.show');
    Route::post('store', [MusicController::class, 'store'])->name('music.store');
    Route::put('update/{id}', [MusicController::class, 'update'])->name('music.update');
    Route::delete('delete/{id}', [MusicController::class, 'destroy'])->name('music.delete');
    Route::get('genre/all', [MusicController::class, 'genres'])->name('music.all');
});
