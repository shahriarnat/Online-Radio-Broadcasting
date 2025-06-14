<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('sanctum:prune-expired')->everyMinute();
Schedule::command('session:prune')->everyMinute();
Schedule::command('app:prune-expired-like-caches')->everyMinute();
Schedule::command('app:generate-most-like-musics')->daily()->at('12:00');
