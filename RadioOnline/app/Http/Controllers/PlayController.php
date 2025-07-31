<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\PlaylistMusic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlayController extends Controller
{

    private string $request_uuid;

    public function __construct()
    {
        $this->request_uuid = (string)Str::uuid();
        Log::channel('radio_broadcast')->info('-------------' . PHP_EOL . 'initialized', [
            'uuid' => $this->request_uuid
        ]);
    }

    public function play(Request $request): void
    {
        Log::channel('radio_broadcast')->info('play method called', [
            'channel_id' => $request->input('channel_id')
        ]);

        $playlist = Playlist::query()
            ->where('channel_playlist', $request->input('channel_id'))
            ->where('start_date', '<=', now()->toDateString())
            ->where('end_date', '>=', now()->toDateString())
            ->where('start_time', '<=', now()->toTimeString())
            ->where('end_time', '>=', now()->toTimeString())
            ->where('activate', 1);

dd($playlist->toRawSql());
        $playlist = $playlist->first();
        Log::channel('radio_broadcast')->info('playlist type found', [
            'playlist_type' => $playlist?->playlist_type,
        ]);

        match ($playlist?->playlist_type) {
            'live' => $this->handleLivePlaylist($playlist),
            'music' => $this->handleMusicPlaylist($playlist),
            default => $this->handleMostLikedPlaylist(),
        };

    }

    private function handleLivePlaylist(Playlist $playlist): void
    {
        Log::channel('radio_broadcast')->info('live started');

        $this->storeCachePlaylist($playlist);
        sleep(1);
        die('# live on air');
    }

    private function handleMusicPlaylist(Playlist $playlist): void
    {
        $currentMusic = $this->getPlaylistMusics($playlist);

        Log::channel('radio_broadcast')->info('music started');

        if ($currentMusic) {
            die($currentMusic);
        } else
            sleep(1);
    }

    private function handleMostLikedPlaylist(): void
    {
        $likePlaylist = Playlist::query()
            ->where('playlist_type', 'liked')
            ->where('activate', 1)
            ->first();

        if ($likePlaylist) {
            $currentMusic = $this->getPlaylistMusics($likePlaylist);

            Log::channel('radio_broadcast')->info('liked started');

            if ($currentMusic) {
                die($currentMusic);
            } else
                sleep(1);
        }

    }

    /**
     *  This block of code updates the status of the currently playing music in the playlist to "played". It performs the following steps:
     *  1. Queries the `PlaylistMusic` model to find all records associated with the given playlist ID (`$playlist->id`) where the `play_status` is "playing".
     *  2. Includes the related `music` data using the `with('music')` method for eager loading.
     *  3. Orders the results by the `position` field to ensure the correct sequence.
     *  4. Updates the `play_status` of these records to "played" and sets the `updated_at` timestamp to the current time.
     *
     * @param Playlist $playlist
     * @param string $playlist_type
     * @return string
     */
    private function getPlaylistMusics(Playlist $playlist): string|null
    {
        $channel_id = (int)request()->get('channel_id');
        $readCache = Cache::store('database')->get(config('cache.radio_broadcast_channel_name') . $channel_id);

        if (isset($readCache['next_track_sec']) && $readCache['next_track_sec'] >= now()->timestamp) {
            sleep(1);
            die('remaining time for next track on channel(' . $channel_id . ') ' . ($readCache['next_track_sec'] - now()->timestamp) . ' seconds');
        }

        PlaylistMusic::query()
            ->where('playlist_id', $playlist->id)
            ->where('play_status', 'playing')
            ->orderBy('position')
            ->update(['play_status' => 'played', 'updated_at' => now()]);

        $currentMusic = PlaylistMusic::query()
            ->with('music')
            ->where('playlist_id', $playlist->id)
            ->where('play_status', 'pending')
            ->orderBy('position')
            ->first();

        $this->storeCachePlaylist($playlist, $currentMusic?->music);

        if ($currentMusic) {

            $currentMusic->where('music_id', $currentMusic->music->id)->where('playlist_id', $currentMusic->playlist_id)
                ->update(['play_status' => 'playing', 'updated_at' => now()]);

            Log::channel('radio_broadcast')->info('music found', [
                'music_id' => $currentMusic->music->id,
            ]);

            return asset(Storage::url($currentMusic->music->music), false);
        } else {
            PlaylistMusic::query()
                ->where('playlist_id', $playlist->id)
                ->where('play_status', 'played')
                ->update(['play_status' => 'pending', 'updated_at' => null]);
        }
        return null;
    }

    private function storeCachePlaylist(Playlist $playlist, $music = null): void
    {
        $params = [
            'next_track_sec' => $music?->duration ? $music->duration + now()->timestamp : 0,
            'channel_id' => $playlist->channel_playlist,
            'playlist_type' => $playlist->playlist_type,
            'playlist_id' => $playlist->id,
            'music_id' => $music?->id,
        ];
        Cache::store('database')->put(config('cache.radio_broadcast_channel_name') . (int)request()->get('channel_id'), $params, 60 * 60 * 24);
    }

}
