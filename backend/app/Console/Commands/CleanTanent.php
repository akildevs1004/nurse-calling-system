<?php

namespace App\Console\Commands;

use App\Models\Community\Tanent;
use Illuminate\Console\Command;

class CleanTanent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:tanent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Tanent::truncate();
        return 0;
    }
}
