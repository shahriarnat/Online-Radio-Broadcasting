<?php

namespace App\Traits;

use App\Models\Playlist;
use Illuminate\Support\Facades\DB;

trait PlaylistTrait
{
    /**
     * @param int $playlist_id
     * @param string $start_datetime Y-m-d H:i:s
     * @param string $end_datetime Y-m-d H:i:s
     * @return bool
     */
    protected function isPlaylistPlaying(int $playlist_id, &$playlist_name = null): bool
    {
        $now = now();

        $check = Playlist::musicType()
            ->where('id', $playlist_id)
            ->where(DB::raw('CONCAT(start_date," ",start_time)'), '<=', $now)
            ->where(DB::raw('CONCAT(end_date," ",end_time)'), '>=', $now);

        $playlist_name = $check->first()?->name;

        return $check->exists();
    }
}
