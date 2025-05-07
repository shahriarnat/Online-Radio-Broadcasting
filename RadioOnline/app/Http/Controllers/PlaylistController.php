<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Http\Requests\DestroyPlaylistRequest;
use App\Http\Requests\ShowPlaylistRequest;
use App\Http\Requests\StorePlaylistRequest;
use App\Http\Requests\UpdateMusicPositionRequest;
use App\Http\Requests\UpdatePlaylistRequest;
use App\Models\Playlist;
use App\Models\PlaylistMusic;
use Illuminate\Http\JsonResponse;

class PlaylistController extends Controller
{
    public function index(): JsonResponse
    {
        $playlists = Playlist::all();
        collect($playlists)->each(function ($playlist) {
            $playlist->musics = $playlist->musics()->count();
        });
        return ApiResponse::success($playlists);
    }

    public function show(ShowPlaylistRequest $id): JsonResponse
    {
        $playlist = Playlist::with('musics')->find($id->id);
        return ApiResponse::success($playlist);
    }

    public function store(StorePlaylistRequest $request): JsonResponse
    {
        try {
            // Create a new playlist
            $playlist = Playlist::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'start_play' => $request->input('start_play'),
                'end_play' => $request->input('end_play'),
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
            $playlist = Playlist::where('id', $request->id)->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'start_play' => $request->input('start_play'),
                'end_play' => $request->input('end_play'),
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
            PlaylistMusic::where('playlist_id', $request->playlist_id)
                ->where('music_id', $request->music_id)->update([
                    'position' => $request->position,
                ]);
            return ApiResponse::success(null, __('playlist.playlist_music_position_updated'));
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

}
