<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AllMusicsResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "artist" => $this->artist,
            "cover" => $this->cover ? asset(Storage::url($this->cover)) : null,
            "music" => asset(Storage::url($this->music)),
            "duration" => $this->duration,
            "is_ads" => $this->is_ads,
            "guest_like" => $this->guest_like,
            "playlists" => $this->playlists?->map(function ($playlist) {
                return [
                    "id" => $playlist->id,
                    "name" => $playlist->name,
                ];
            }),
            "genre" => $this->genre?->id ? ["id" => $this->genre->id, "name" => $this->genre->name] : null,
        ];
    }
}
