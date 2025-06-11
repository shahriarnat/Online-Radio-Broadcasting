<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Http\Requests\AllLiveRequest;
use App\Http\Requests\DestroyLiveRequest;
use App\Http\Requests\StoreLiveRequest;
use App\Http\Resources\AllLiveResponse;
use App\Models\Playlist;
use Illuminate\Http\JsonResponse;

class LiveController extends Controller
{
    public function index(AllLiveRequest $request): JsonResponse
    {
        $lives = Playlist::query()
            ->LiveType()
            ->Paginating($request->per_page, $request->page);
        return ApiResponse::paginate(AllLiveResponse::collection($lives));
    }

    public function store(StoreLiveRequest $request): JsonResponse
    {
        $default_channel = 1;
        /* Check for overlapping playlists */
        $overlap = Playlist::query()
            ->where('channel_playlist', $default_channel)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->input('start_time'), $request->input('end_time')])
                    ->orWhereBetween('end_time', [$request->input('start_time'), $request->input('end_time')]);
            })
            ->exists();

        if ($overlap) {
            return ApiResponse::error(__('playlist.time_overlap_error'));
        }

        try {
            // Create a new playlist with live
            $playlist = Playlist::create([
                'channel_playlist' => $default_channel,
                'playlist_type' => 'live',
                'playlist_options' => collect($request->input('presenter'))->toJson(),
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'activate' => $request->input('activate'),
            ]);
            $playlist->playlist_options = json_decode($playlist->playlist_options, true);
            return ApiResponse::success($playlist, __('playlist.playlist_live_created'));
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    /* Placeholder for future implementation or documentation */
    public function destroy(DestroyLiveRequest $id): JsonResponse
    {
        try {
            $playlist = Playlist::LiveType()->findOrFail($id->id);
            $playlist->delete();
            return ApiResponse::success(null, __('playlist.playlist_live_deleted'));
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

}
