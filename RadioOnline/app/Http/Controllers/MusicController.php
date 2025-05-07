<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Http\Requests\ShowMusicRequest;
use App\Http\Requests\StoreMusicRequest;
use App\Models\Music;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MusicController extends Controller
{
    //
    public function index(): JsonResponse
    {
        // Retrieve all music records
        $musics = Music::all();
        collect($musics)->each(function ($music) {
            $music->playlists = $music->playlists()->get(['name']);
        });
        return ApiResponse::success($musics);
    }

    public function show(ShowMusicRequest $id): JsonResponse
    {
        // Retrieve a single music record by ID
        $music = Music::find($id);
        return ApiResponse::success($music);
    }

    public function store(StoreMusicRequest $request): JsonResponse
    {
        // Create a new music record
        $music = Music::create($request->all());
        return ApiResponse::success($music, __('music.created'), 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        // Update an existing music record
        $music = Music::findOrFail($id);
        $music->update($request->all());
        return response()->json($music);
    }

    public function destroy($id): JsonResponse
    {
        // Delete a music record
        $music = Music::findOrFail($id);
        $music->delete();
        return response()->json(null, 204);
    }
}
