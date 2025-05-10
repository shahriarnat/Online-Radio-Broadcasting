<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Http\Requests\DestroyMusicRequest;
use App\Http\Requests\ShowMusicRequest;
use App\Http\Requests\StoreMusicRequest;
use App\Http\Requests\UpdateMusicRequest;
use App\Models\Genre;
use App\Models\Music;
use App\Services\Interfaces\MediaServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $music->genre = $music->genre()->first(['id', 'name']);
        });
        return ApiResponse::success($musics);
    }

    public function show(ShowMusicRequest $id): JsonResponse
    {
        try {
            $music = Music::findOrFail($id)->first();

            $music->playlists = $music->playlists()->get(['name']);
            $music->genre = $music->genre()->first(['id', 'name']);

            $music->music = asset(Storage::url($music->music));
            $music->cover = asset(Storage::url($music->cover));

            return ApiResponse::success($music, __('music.show'));
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function store(StoreMusicRequest $request): JsonResponse
    {
        try {
            $music = $request->file('music')->store('musics', 'public');
            $metadata = $this->mediaService->analyzeFile(public_path(Storage::url($music)));

            $cover = $metadata->getCoverBinary();
            if ($cover) {
                $coverPath = 'covers/' . Str::random(32) . '.' . $cover['image_ext'];
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

    public function update(UpdateMusicRequest $request, $id): JsonResponse
    {
        // Update an existing music record
        try {
            $music = Music::findOrFail($id);

            $cover = $request->hasFile('cover') ? $request->file('cover')->store('covers', 'public') : $music->cover;

            $music->title = $request->input('title');
            $music->artist = $request->input('artist');
            $music->album = $request->input('album');
            $music->genre_id = $request->input('genre_id');
            $music->is_ads = $request->input('is_ads');
            $music->cover = $cover;

            $music->save();

            $music->playlists()->sync(Str::of($request->input('playlists'))->explode(','));
            $music->playlists = $music->playlists()->get(['name']);

            $music->genre_id = $music->genre()->first(['id', 'name']);

            $music->music = asset(Storage::url($music->music));
            $music->cover = asset(Storage::url($music->cover));

            return ApiResponse::success($music, __('music.updated'));
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(DestroyMusicRequest $id): JsonResponse
    {
        // Delete a music record
        $music = Music::findOrFail($id)->first();

        if (Storage::disk('public')->exists($music->music)) {
            $music_file = Storage::disk('public')->delete($music->music);
        }

        if (Storage::disk('public')->exists($music->cover)) {
            $cover_file = Storage::disk('public')->delete($music->cover);
        }

        $music->delete();

        return ApiResponse::success(null, __('music.deleted'));
    }

    public function genres(): JsonResponse
    {
        // Retrieve all unique genres from the music records
        $genres = Genre::all();
        return ApiResponse::success($genres);
    }
}
