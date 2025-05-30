<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class Music extends Model
{

    use HasFactory;

    protected $table = 'musics';
    protected $fillable = [
        'title',
        'artist',
        'album',
        'cover',
        'music',
        'duration',
        'genre_id',
    ];
    protected $casts = [
        'is_ads' => 'boolean',
    ];

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_music');
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function scopeFiltering($query, $filters = [])
    {
        if (isset($filters->title)) {
            $query->where('title', 'like', '%' . $filters->title . '%');
        }
        if (isset($filters->artist)) {
            $query->where('artist', 'like', '%' . $filters->artist . '%');
        }
        if (isset($filters->is_ads)) {
            $query->where('is_ads', $filters->is_ads);
        }
        if (isset($filters->genre_id)) {
            $query->where('genre_id', $filters->genre_id);
        }
        if (isset($filters->playlist_id)) {
            $query->whereHas('playlists', function ($q) use ($filters) {
                $q->where('id', $filters->playlist_id);
            });
        }
        if (isset($filters->channel_id)) {
            $query->whereHas('playlists.channel', function ($q) use ($filters) {
                $q->where('id', $filters->channel_id);
            });
        }

        return $query;
    }

    public function scopePaginating($query, $perPage = 30, $page = 1)
    {
        $perPage = $perPage ?? 30;
        $page = $page ?? 1;

        return $query->Paginate($perPage, ['*'], '', $page);
    }

    public function scopeSorting($query, $sortBy = 'id', $sortOrder = 'asc')
    {
        $sortBy = $sortBy ?? 'id';
        $sortOrder = $sortOrder ?? 'asc';

        return $query->orderBy($sortBy, $sortOrder);
    }

}
