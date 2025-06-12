<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Models\PlaylistMusic;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlayController extends Controller
{
    public function play(Request $request): void
    {
        $previous = PlaylistMusic::query()
            ->with('music')
            ->whereHas('playlist.channel', function ($query) use ($request) {
                $query->where('id', $request->input('channel_id'));
            })
            ->where('play_status', 'playing')
            ->orderBy('position', 'asc');
        $previous->update(['play_status' => 'played', 'updated_at' => now()]);

        $music = PlaylistMusic::query()
            ->with('music')
            ->whereHas('playlist.channel', function ($query) use ($request) {
                $query->where('id', $request->input('channel_id'));
            })
            ->where('play_status', 'pending')
            ->orderBy('position', 'asc');

        $currentMusic = $music->first();

        if ($currentMusic === null) {
            PlaylistMusic::query()
                ->whereHas('playlist.channel', function ($query) use ($request) {
                    $query->where('id', $request->input('channel_id'));
                })
                ->where('play_status', 'played')
                ->update(['play_status' => 'pending', 'updated_at' => now()]);
        } else {
            echo asset(Storage::url($currentMusic->music->music), false);
            ob_end_flush();
            $music->update(['play_status' => 'playing', 'updated_at' => now()]);
        }

    }
}
