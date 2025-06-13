<?php

namespace App\Console\Commands;

use App\Models\Music;
use App\Models\Playlist;
use App\Models\PlaylistMusic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateMostLikeMusics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-most-like-musics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a list of the most liked music tracks daily.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mostLikedMusic = Music::query()
            ->where('guest_like', '>', 0)
            ->orderBy('guest_like', 'desc')
            ->pluck('id');

        $likedPlaylist = Playlist::query()
            ->where('playlist_type', 'liked')
            ->where('activate', 1)
            ->first();

        if ($mostLikedMusic) {

            PlaylistMusic::where('playlist_id', $likedPlaylist->id)->delete();
            $this->info('Most liked musics have been cleared from the playlist.');

            $this->info('Adding most liked musics to the playlist...');
            $position = 0;
            foreach ($mostLikedMusic as $music) {
                PlaylistMusic::insert([
                    'playlist_id' => $likedPlaylist->id,
                    'music_id' => $music,
                    'position' => ++$position,
                ]);
            }

            $this->info($position . ' Most liked musics have been added to the playlist.');
        }
    }
}
