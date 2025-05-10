<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
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

}
