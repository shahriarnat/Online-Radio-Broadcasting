<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ShowMusicResponse extends JsonResource
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
            'title' => $this->title,
            'artist' => $this->artist,
            'album' => $this->album,
            'cover' => $this?->cover,
            'music' => $this->music,
            'duration' => $this->duration,
            'guest_like' => $this->guest_like,
            'is_ads' => $this->is_ads,
            'playlists' => $this->playlists?->map(function ($playlist) {
                return [
                    'id' => $playlist->pivot->playlist_id,
                    'name' => $playlist->name,
                ];
            }),
            'genre' => $this->genre?->id ? ['id' => $this->genre->id, 'name' => $this->genre->name] : null,
        ];
    }
}
