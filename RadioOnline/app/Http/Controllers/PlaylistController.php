<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Http\Requests\AllPlaylistRequest;
use App\Http\Requests\DestroyPlaylistRequest;
use App\Http\Requests\ShowPlaylistRequest;
use App\Http\Requests\StorePlaylistRequest;
use App\Http\Requests\UpdateMusicPositionRequest;
use App\Http\Requests\UpdatePlaylistRequest;
use App\Http\Resources\ShowPlaylistResponse;
use App\Models\Playlist;
use App\Models\PlaylistMusic;
use Illuminate\Http\JsonResponse;

class PlaylistController extends Controller
{
    public function index(AllPlaylistRequest $request): JsonResponse
    {
        $playlists = Playlist::query()
            ->where('playlist_type', 'music')
            ->PlaylistFilter($request)
            ->get();
        if ($playlists) {
            collect($playlists)->each(function ($playlist) {
                $playlist->music_count = $playlist->musics()->count();
                $playlist->channel = $playlist->channel()->first()->only(['id', 'name', 'slug']);
            });
        }
        return ApiResponse::success($playlists);
    }

    public function show(ShowPlaylistRequest $id): JsonResponse
    {
        $playlist = Playlist::with([
            'channel' => function ($channel) {
                $channel->select(['id', 'name', 'slug']);
            },
            'musics' => function ($musics) {
                $musics->select(['id', 'title', 'artist', 'duration', 'cover', 'guest_like', 'position'])
                    ->orderBy('position', 'asc');
            }
        ])->MusicType()->find($id->id);
        return ApiResponse::success(ShowPlaylistResponse::make($playlist));
    }

    public function store(StorePlaylistRequest $request): JsonResponse
    {
        /* Check for overlapping playlists */
        $overlap = Playlist::query()
            ->where('channel_playlist', $request->input('channel_playlist'))
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->input('start_date'), $request->input('end_date')]);
                $query->orWhereBetween('end_date', [$request->input('end_date'), $request->input('end_date')]);
            })
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->input('start_time'), $request->input('end_time')]);
                $query->orWhereBetween('end_time', [$request->input('start_time'), $request->input('end_time')]);
            })
            ->first();

        if ($overlap) {
            return ApiResponse::error(__('playlist.time_overlap_error', ['playlist_name' => $overlap->name, 'start_time' => $overlap->start_time, 'end_time' => $overlap->end_time]));
        }

        try {
            // Create a new playlist
            $playlist = Playlist::create([
                'channel_playlist' => $request->input('channel_playlist'),
                'playlist_type' => 'music',
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'activate' => $request->input('activate'),
            ]);
            return ApiResponse::success($playlist, __('playlist.playlist_created'));
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(UpdatePlaylistRequest $request): JsonResponse
    {
        try {
            $playlist = Playlist::findOrFail($request->id);
            $playlist->update([
                'channel_playlist' => $request->input('channel_playlist'),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'activate' => $request->input('activate'),
            ]);
            return ApiResponse::success($playlist, __('playlist.playlist_updated'));
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(DestroyPlaylistRequest $id): JsonResponse
    {
        try {
            $playlist = Playlist::findOrFail($id->id);
            $playlist->delete();
            return ApiResponse::success(null, __('playlist.playlist_deleted'));
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function updateMusicPosition(UpdateMusicPositionRequest $request): JsonResponse
    {
        try {
            foreach ($request->input('musics') as $music) {
                PlaylistMusic::where('playlist_id', $request->input('playlist_id'))
                    ->where('music_id', $music['music_id'])
                    ->update(['position' => $music['position']]);
            }
            return ApiResponse::success(null, __('playlist.playlist_music_position_updated'));
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

}
