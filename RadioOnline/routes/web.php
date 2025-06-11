<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/track.pls', [PlayController::class, 'play'])->name('play');
