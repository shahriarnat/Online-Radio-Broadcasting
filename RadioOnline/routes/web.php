<?php

use App\Http\Middleware\VisitorsMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\Music;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/track.pls', function () {
    $randomMusic = Music::inRandomOrder()->first();
    return asset(Storage::url($randomMusic->music), false);
});

