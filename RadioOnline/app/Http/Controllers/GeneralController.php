<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Models\Channel;
use App\Models\PlaylistMusic;
use Illuminate\Support\Facades\Storage;

class GeneralController extends Controller
{
    public function info()
    {
        return ApiResponse::success([
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'stream_url' => config('icecast.stream_url'),
            'app_locale' => config('app.locale'),
            'channels' => Channel::all()->map(function ($channel) {
                return [
                    'id' => $channel->id,
                    'name' => $channel->name,
                    'slug' => $channel->slug,
                    'description' => $channel->description,
                    'stream_address' => $channel->stream_address,
                    'current_playing' => $this->getCurrentPlaying($channel->id),
                    'next_play' => $this->getNextPlay($channel->id),
                ];
            }),
        ]);
    }

    private function getCurrentPlaying(int $channel_id): array|null
    {
        $music = PlaylistMusic::query()
            ->with(['music.genre', 'playlist.channel'])
            ->where('play_status', 'playing')
            ->whereHas('playlist.channel', function ($query) use ($channel_id) {
                $query->where('id', $channel_id);
            })
            ->orderBy('position')
            ->first();

        if ($music) {
            return [
                'id' => $music->music->id,
                'title' => $music->music->title,
                'artist' => $music->music->artist,
                'genre' => $music->music?->genre ? $music->music->genre->name : null,
                'duration' => $music->music->duration,
                'cover' => $music->music->cover ? asset(Storage::url($music->music->cover)) : null,
                'like' => $music->music->guest_like,
                'ads' => $music->music->is_ads,
                'mode' => $music->playlist->playlist_type,
            ];
        }

        return null;
    }

    private function getNextPlay(int $channel_id): array|null
    {
        $music = PlaylistMusic::query()
            ->with(['music.genre', 'playlist.channel'])
            ->where('play_status', 'pending')
            ->whereHas('playlist.channel', function ($query) use ($channel_id) {
                $query->where('id', $channel_id);
            })
            ->orderBy('position')
            ->first();

        if ($music) {
            return [
                'id' => $music->music->id,
                'title' => $music->music->title,
                'artist' => $music->music->artist,
                'genre' => $music->music?->genre ? $music->music->genre->name : null,
                'duration' => $music->music->duration,
                'cover' => $music->music?->cover ? asset(Storage::url($music->music->cover)) : null,
                'like' => $music->music->guest_like,
                'ads' => $music->music->is_ads,
                'mode' => $music->playlist->playlist_type,
            ];
        }

        return null;
    }
}
