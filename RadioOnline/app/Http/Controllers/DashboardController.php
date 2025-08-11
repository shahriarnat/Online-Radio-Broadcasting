<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Helpers\LogReader;
use App\Models\Music;
use App\Models\Visitor;
use App\Services\IcecastService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Stepanenko3\LaravelSystemResources\Facades\SystemResources;

class DashboardController extends Controller
{
    public function info()
    {
        $icecastService = new IcecastService();

        $musics = Music::count() ?? 0;

        $listeners = (int)$icecastService->getStats()['listeners'] ?? 0;

        $most_likedMusics = Music::NoAds()
            ->orderBy('guest_like', 'desc')
            ->take(5)
            ->get(['title', DB::raw('guest_like AS likes')]);

        $visitors = Visitor::query()
            ->selectRaw('COUNT(*) AS total, DATE(created_at) AS date')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(7)
            ->get()
            ->map(function ($visitor) {
                return ['date' => $visitor->date, 'visitor' => $visitor->total];
            });

        return ApiResponse::success([
            'listeners' => $listeners,
            'musics' => $musics,
            'most_liked_musics' => $most_likedMusics,
            'visitors' => $visitors,
        ]);

    }

    public function logs()
    {
        return ApiResponse::success([
            'logs' => LogReader::readLatestLogFromChannel('radio_broadcast', 10),
        ]);
    }

    public function resources()
    {
        $ramUsed = SystemResources::ramUsed();
        $ramTotal = SystemResources::ramTotal();

        $diskUsed = SystemResources::diskUsed();
        $diskTotal = SystemResources::diskTotal();

        $cpu = SystemResources::cpu() . '%';

        return ApiResponse::success([
            'ram' => [
                'used' => number_format($ramUsed / 1024, 2) . ' MB',
                'total' => number_format($ramTotal / 1024, 2) . ' MB',
                'percentage' => round(($ramUsed / $ramTotal) * 100, 2),
            ],
            'disk' => [
                'used' => number_format($diskUsed / 1024, 2) . ' MB',
                'total' => number_format($diskTotal / 1024, 2) . ' MB',
                'percentage' => round(($diskUsed / $diskTotal) * 100, 2),
            ],
            'cpu' => $cpu,
        ]);
    }

}
