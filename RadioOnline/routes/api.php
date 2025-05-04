<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaylistController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:sanctum');
});
//->middleware('auth:sanctum')
Route::prefix('playlist')->group(function () {
    Route::get('all', [PlaylistController::class, 'index'])->name('playlist.all');
    Route::get('show/{id}', [PlaylistController::class, 'show'])->name('playlist.show');
    Route::post('store', [PlaylistController::class, 'store'])->name('playlist.store');
    Route::put('update/{id}', [PlaylistController::class, 'update'])->name('playlist.update');
    Route::delete('delete/{id}', [PlaylistController::class, 'destroy'])->name('playlist.delete');
});

