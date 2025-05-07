<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Genre;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Music>
 */
class MusicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'artist' => $this->faker->name(),
            'album' => $this->faker->word(),
            'cover' => $this->faker->imageUrl(200, 200, 'music'),
            'file' => $this->faker->filePath('musics', 'mp3'),
            'duration' => $this->faker->numberBetween(1, 300),
            'genre_id' => Genre::factory(),
            'is_ads' => $this->faker->boolean(),
        ];
    }
}
