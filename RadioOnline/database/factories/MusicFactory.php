<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
            'artist' => $this->faker->name,
            'album' => $this->faker->word,
            'cover' => 'covers/' . $this->faker->md5() . '.jpg',
            'music' => 'musics/' . $this->faker->md5() . '.mp3',
            'duration' => $this->faker->numberBetween(120, 360), // duration in seconds
            'genre_id' => $this->faker->numberBetween(1, 10),
            'guest_like' => $this->faker->numberBetween(0, 9999),
            'is_ads' => $this->faker->boolean(10),
        ];
    }
}
