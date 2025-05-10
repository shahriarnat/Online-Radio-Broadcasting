<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Http\Requests\ShowMusicRequest;
use App\Http\Requests\StoreMusicRequest;
use App\Models\Genre;
use App\Models\Music;
use App\Services\Interfaces\MediaServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Generator\RandomBytesGenerator;

class MusicController extends Controller
{
    protected $mediaService;

    public function __construct(MediaServiceInterface $mediaService)
    {
        $this->mediaService = $mediaService;
    }

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
        try {
            $music = $request->file('music')->store('musics', 'public');
            $metadata = $this->mediaService->analyzeFile(public_path(Storage::url($music)));

            $cover = $metadata->getCoverBinary();
            if ($cover) {
                $coverPath = 'covers/' . Str::random(36) . '.' . $cover['image_ext'];
                Storage::disk('public')->put($coverPath, $cover['data']);
                $cover = $coverPath;
            }

            $data = Music::create([
                'title' => $metadata->getTitle(),
                'artist' => $metadata->getArtist(),
                'album' => $metadata->getAlbum(),
                'cover' => $cover,
                'music' => $music,
                'duration' => $metadata->getDuration(),
            ]);

            $data->cover = asset(Storage::url($cover));
            $data->music = asset(Storage::url($music));

            return ApiResponse::success($data, __('music.created'), 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
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

    public function genres(): JsonResponse
    {
        // Retrieve all unique genres from the music records
        $genres = Genre::all();
        return ApiResponse::success($genres);
    }
}
