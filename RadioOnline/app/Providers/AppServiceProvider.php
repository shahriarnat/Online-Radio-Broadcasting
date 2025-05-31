<?php

namespace App\Providers;

use App\Services\IcecastService;
use App\Services\Interfaces\IcecastInterface;
use App\Services\Interfaces\MediaServiceInterface;
use App\Services\MediaService;
use App\Session\SessionHandler;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /* Extend the session service to use a custom SessionHandler implementation */
        $this->app->singleton('session.handler', function ($app) {
            $connection = DB::connection(config('session.connection'));
            $table = config('session.table');
            $lifetime = config('session.lifetime');

            return new SessionHandler($connection, $table, $lifetime, $app);
        });

        $this->app->extend('session', function ($service, $app) {
            return new SessionManager($app);
        });

        $this->app->bind(MediaServiceInterface::class, MediaService::class);
        $this->app->bind(IcecastInterface::class, IcecastService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
