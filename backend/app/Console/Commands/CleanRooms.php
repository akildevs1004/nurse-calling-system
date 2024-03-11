<?php

namespace App\Console\Commands;

use App\Models\Community\Room;
use Illuminate\Console\Command;

class CleanRooms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:rooms';

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
        Room::truncate();
        return 0;
    }
}
