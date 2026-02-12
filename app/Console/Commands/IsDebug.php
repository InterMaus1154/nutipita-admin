<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class IsDebug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:is-debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Returns true if app is in debug and local mode';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->isProduction()) {
            $this->warn("!!! App is in production !!!");
        } else if (!env('APP_DEBUG')) {
            $this->warn('Debug mode is turned off!');
        } else {
            $this->info("App is in local mode. Safe for local development");
        }
    }
}
