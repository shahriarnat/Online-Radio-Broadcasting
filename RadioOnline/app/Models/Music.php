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
        'genre',
        'duration',
        'file_path',
    ];

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_music');
    }
}
