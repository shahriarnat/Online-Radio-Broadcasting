<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $table = 'playlists';
    protected $fillable = [
        'name',
        'description',
        'image',
        'start_play',
        'end_play',
        'activate',
    ];

    public function musics()
    {
        return $this->belongsToMany(Music::class, 'playlist_music');
    }

}
