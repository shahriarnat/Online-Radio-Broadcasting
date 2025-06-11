<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ShowPlaylistResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'activate' => $this->activate,
            'channel' => [
                'id' => $this->channel->id,
                'name' => $this->channel->name,
                'slug' => $this->channel->slug,
            ],
            'musics' => $this->musics?->map(function ($music) {
                return [
                    'id' => $music->id,
                    'title' => $music->title,
                    'artist' => $music->artist,
                    'duration' => $music->duration,
                    'cover' => $music->cover ? asset(Storage::url($music->cover)) : null,
                    'guest_like' => $music->guest_like,
                    'position' => $music->position,
                ];
            }),
        ];
    }
}
