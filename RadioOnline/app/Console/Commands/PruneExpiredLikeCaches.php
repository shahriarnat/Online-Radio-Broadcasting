<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneExpiredLikeCaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-expired-like-caches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune expired like caches from the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Pruning expired like caches...');

        // Get the current time
        $now = now();

        // Calculate the expiration time (1 week ago)
        $expirationTime = $now->subWeek()->timestamp;

        // Delete all like caches that are older than 1 hour
        DB::table('cache')
            ->where('key', 'like', '%music_like%')
            ->where('expiration', '<', $expirationTime)
            ->delete();

        $this->info('Expired like caches have been pruned successfully.');
    }
}
