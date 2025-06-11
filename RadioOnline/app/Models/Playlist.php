<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Playlist extends Model
{
    use HasFactory;

    protected $table = 'playlists';

    protected $fillable = [
        'channel_playlist',
        'playlist_type',
        'playlist_options',
        'name',
        'description',
        'image',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'activate',
    ];

    protected $casts = [
        'activate' => 'boolean',
    ];

    public function musics(): BelongsToMany
    {
        return $this->belongsToMany(Music::class, 'playlist_music');
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class, 'channel_playlist', 'id');
    }

    public function scopePlaylistFilter($query, $request): void
    {
        if ($request->has('channel_id')) {
            $query->where('channel_playlist', $request->input('channel_id'));
        }

        if ($request->has('activate')) {
            $query->where('activate', $request->input('activate'));
        }
    }

    public function scopePaginating($query, $perPage = 30, $page = 1)
    {
        $perPage = $perPage ?? 30;
        $page = $page ?? 1;

        return $query->Paginate($perPage, ['*'], '', $page);
    }

    public function scopeMusicType($query): void
    {
        $query->where('playlist_type', 'music');
    }

    public function scopeLiveType($query): void
    {
        $query->where('playlist_type', 'live');
    }

    public function scopePodcastType($query): void
    {
        $query->where('playlist_type', 'podcast');
    }
}
