<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ImportJson as ImportJsonJob;

class ImportJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import JSON to the database.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ImportJsonJob::dispatch();
    }
}
