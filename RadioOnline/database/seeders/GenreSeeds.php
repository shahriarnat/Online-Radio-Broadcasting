<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeds extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'Pop',
            'Hip-hop',
            'Electronic',
            'Heavy metal',
            'Popular',
            'Country',
            'Folk',
            'Punk rock',
            'Classical',
            'Blues',
            'Rhythm and blues',
            'Alternative rock',
            'Rock and roll',
            'New wave',
            'Jazz',
            'World',
            'Hard rock',
            'Hardcore',
            'Reggae',
            'Indie rock',
            'Thrash metal',
            'Vaporwave',
            'Western',
            'Post-punk',
            'Experimental',
            'Modernism',
            'Pop rock',
            'Hip-hop culture',
            'Music of Latin America',
            'Funk',
            'Techno',
            'K-pop',
            'Synth-pop',
            'Soul',
            'Shoegaze',
            'No wave',
            'Disco',
            'Electronic dance',
            'Melodic death metal',
            'New-age',
            'Ska',
            'Christian',
            'American folk',
            'Dance',
            'Music of Africa',
            'Dubstep',
            'Ambient',
            'Independent',
            'Vocal',
            'Music of Asia',
            'Middle Eastern',
        ];
        foreach ($genres as $genre) {
            Genre::firstOrCreate(['name' => $genre]);
        }
    }
}
