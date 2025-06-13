<?php

namespace Database\Seeders;

use App\Models\Playlist;
use Illuminate\Database\Seeder;

class PlaylistDefaultSeeds extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Playlist::create([
            'channel_playlist' => 1,
            'playlist_type' => 'liked',
            'name' => 'Most liked playlist',
            'description' => 'This is the default playlist description.',
            'start_date' => '9999-12-31',
            'end_date' => '9999-12-31',
            'start_time' => '00:00:00',
            'end_time' => '00:00:00',
            'activate' => 1,
        ]);
    }
}
