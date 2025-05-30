<?php

namespace Database\Seeders;

use App\Models\Music;
use App\Models\Playlist;
use Illuminate\Database\Seeder;

class MockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 music records
        Music::factory()->count(1000)->create();

        // Create 5 playlists
        Playlist::factory()->count(5)->create();

        // Attach 25 random music records to each playlist with random positions
        Playlist::all()->each(function ($playlist) {
            $musicIds = Music::inRandomOrder()->take(400)->pluck('id');
            $musicWithPositions = $musicIds->mapWithKeys(function ($id) {
                return [$id => ['position' => rand(1, 400)]];
            });
            $playlist->musics()->attach($musicWithPositions);
        });

    }
}
