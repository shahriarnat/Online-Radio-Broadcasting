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
        'name',
        'description',
        'image',
        'start_play',
        'end_play',
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

}
