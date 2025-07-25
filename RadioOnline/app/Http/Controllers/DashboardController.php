<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Models\Channel;
use App\Models\Music;
use App\Models\Visitor;
use App\Services\IcecastService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function info()
    {
        $icecastService = new IcecastService();

        $musics = Music::NoAds()->count() ?? 0;

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
}
