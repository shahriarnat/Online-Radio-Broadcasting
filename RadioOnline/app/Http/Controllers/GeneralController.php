<?php

namespace App\Http\Controllers;

use app\Helpers\ApiResponse;
use App\Models\Channel;

class GeneralController extends Controller
{
    public function info()
    {
        return ApiResponse::success([
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_locale' => config('app.locale'),
            'channels' => Channel::all()->map(function ($channel) {
                return [
                    'name' => $channel->name,
                    'slug' => $channel->slug,
                    'description' => $channel->description,
                    'stream_address' => $channel->stream_address,
                ];
            }),
        ]);
    }
}
