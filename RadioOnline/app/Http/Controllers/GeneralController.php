<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Models\Channel;
use App\Models\Music;
use App\Models\Playlist;
use App\Models\PlaylistMusic;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class GeneralController extends Controller
{
    public function info()
    {

        $trigger = RateLimiter::attempt(
            'get_info-' . request()->getClientIp(), 15, function () {
        }, 60);

        if (!$trigger) {
            return ApiResponse::error('Too many requests', Response::HTTP_TOO_MANY_REQUESTS);
        }

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
                    //'next_play' => $this->getNextPlay($channel->id),
                ];
            }),
        ]);
    }

    private function getCurrentPlaying(int $channel_id): array|null
    {

        $readCache = Cache::store('database')->get(config('cache.radio_broadcast_channel_name') . $channel_id);

        if ($readCache) {

            if ($readCache['playlist_type'] === 'live') {
                $playlist = Playlist::query()
                    ->where('id', $readCache['playlist_id'])
                    ->where('channel_playlist', $channel_id)
                    ->first();
                return [
                    'id' => $playlist->id,
                    'title' => $playlist->name,
                    'description' => $playlist->description,
                    'mode' => $playlist->playlist_type,
                    'presenter' => json_decode($playlist->playlist_options, true),
                ];
            } elseif (in_array($readCache['playlist_type'], ['music', 'liked']) && $readCache['music_id'] > 0) {
                $music = Music::find($readCache['music_id']);
                return [
                    'id' => $music->id,
                    'title' => $music->title,
                    'artist' => $music->artist,
                    'duration' => $music->duration,
                    'cover' => $music->cover ? asset(Storage::url($music->cover)) : null,
                    'like' => $music->guest_like,
                    'ads' => $music->is_ads,
                    'mode' => 'music',
                ];
            }
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
