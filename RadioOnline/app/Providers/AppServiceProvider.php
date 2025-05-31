<?php

namespace App\Providers;

use App\Services\IcecastService;
use App\Services\Interfaces\IcecastInterface;
use App\Services\Interfaces\MediaServiceInterface;
use App\Services\MediaService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
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
