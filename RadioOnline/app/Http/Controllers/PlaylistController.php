<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Http\Requests\DestroyPlaylistRequest;
use App\Http\Requests\ShowPlaylistRequest;
use App\Http\Requests\StorePlaylistRequest;
use App\Http\Requests\UpdatePlaylistRequest;
use App\Models\Playlist;
use Illuminate\Http\JsonResponse;

class PlaylistController extends Controller
{
    public function index(): JsonResponse
    {
        $playlists = Playlist::all();
        return ApiResponse::success($playlists);
    }

    public function show(ShowPlaylistRequest $id): JsonResponse
    {
        $playlist = Playlist::find($id);
        return ApiResponse::success($playlist);
    }

    public function store(StorePlaylistRequest $request): JsonResponse
    {
        // Handle file upload
        $imagePath = $request->file('image')->store('playlist/cover', 'public');

        // Create a new playlist
        $playlist = Playlist::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'image' => $imagePath,
            'start_play' => $request->input('start_play'),
            'end_play' => $request->input('end_play'),
            'activate' => $request->input('activate'),
        ]);

        $playlist->image = url('storage/' . $playlist->image);

        return ApiResponse::success($playlist, __('Playlist created successfully.'));

    }

    public function update(UpdatePlaylistRequest $request)
    {
        // Logic to update an existing playlist
    }

    public function destroy(DestroyPlaylistRequest $id)
    {
        // Logic to delete a playlist
    }
}
