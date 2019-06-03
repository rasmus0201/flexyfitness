<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:delete-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run command to delete all expired caches';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Hestevogn!');
    }
}
