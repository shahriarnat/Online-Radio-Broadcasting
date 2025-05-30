<?php

namespace Database\Seeders;

use App\Models\Channel;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $channels = [
            ['name' => 'Channel One', 'slug' => 'CH1', 'description' => 'Default Description', 'stream_address' => '/stream1'],
            ['name' => 'Channel Two', 'slug' => 'CH2', 'description' => 'Default Description', 'stream_address' => '/stream2'],
        ];
        foreach ($channels as $channel) {
            Channel::query()->firstOrCreate($channel);
        }
    }
}
