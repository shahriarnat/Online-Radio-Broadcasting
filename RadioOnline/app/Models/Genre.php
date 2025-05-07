<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the musics associated with the genre.
     */
    public function musics()
    {
        return $this->hasMany(Music::class);
    }
}
