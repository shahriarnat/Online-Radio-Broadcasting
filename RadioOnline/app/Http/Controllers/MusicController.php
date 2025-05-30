<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Http\Requests\DestroyMusicRequest;
use App\Http\Requests\ShowMusicRequest;
use App\Http\Requests\ShowMusicsRequest;
use App\Http\Requests\StoreMusicRequest;
use App\Http\Requests\UpdateMusicRequest;
use App\Http\Resources\PaginationResource;
use App\Models\Genre;
use App\Models\Music;
use App\Models\Playlist;
use App\Services\Interfaces\MediaServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MusicController extends Controller
{
    protected $mediaService;

    public function __construct(MediaServiceInterface $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function properties(): JsonResponse
    {
        return ApiResponse::success([
            'playlists' => Playlist::all()->map(function ($playlist) {
                return ['id' => $playlist->id, 'name' => $playlist->name];
            }),
            'genres' => Genre::all()->map(function ($genre) {
                return ['id' => $genre->id, 'name' => $genre->name];
            }),
            'artists' => Music::distinct()->pluck('artist')->filter()->values(),
        ], 'Extracted properties of the Music filters');
    }

    public function index(ShowMusicsRequest $request): JsonResponse
    {
        // Retrieve all music records
        $musics = Music::query()
            ->select(['id', 'genre_id', 'title', 'artist', 'cover', 'music', 'duration', 'is_ads', 'guest_like'])
            ->Filtering($request)
            ->with([
                'playlists' => function ($query) {
                    $query->select('id', 'name');
                },
                'genre' => function ($query) {
                    $query->select('id', 'name');
                }])
            ->Sorting($request->sort_by, $request->sort_order)
            ->Paginating($request->per_page, $request->page);

        return ApiResponse::paginate($musics);
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

    /**
     * Store a new music record.
     *
     * Relations:
     * - `playlists`: Retrieves associated playlists for the music.
     * - `genre`: Retrieves the genre associated with the music.
     */
    public function store(StoreMusicRequest $request): JsonResponse
    {
        try {
            $music = $request->file('music');
            $musicPath = Str::random(64) . '.' . $music->getClientOriginalExtension();
            $music = $music->storeAs('musics', $musicPath, 'public');

            $metadata = $this->mediaService->analyzeFile(public_path(Storage::url($music)));

            $cover = $metadata->getCoverBinary();
            if ($cover) {
                $coverPath = 'covers/' . Str::random(64) . '.' . $cover['image_ext'];
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

            $data->cover = $cover ? asset(Storage::url($cover)) : null;
            $data->music = $music ? asset(Storage::url($music)) : null;

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

        if ($music->music && Storage::disk('public')->exists($music->music)) {
            Storage::disk('public')->delete($music->music);
        }

        if ($music->cover && Storage::disk('public')->exists($music->cover)) {
            Storage::disk('public')->delete($music->cover);
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
