<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete expired sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lifetime = config('session.lifetime');
        $expired = Carbon::now()->subMinutes($lifetime)->getTimestamp();

        DB::table('sessions')->where('last_activity', '<', $expired)->delete();

        $this->info('Expired sessions pruned.');
    }
}
