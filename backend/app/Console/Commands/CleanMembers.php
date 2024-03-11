<?php

namespace App\Console\Commands;

use App\Models\Community\Member;
use Illuminate\Console\Command;

class CleanMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:members';

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
        Member::truncate();
        return 0;
    }
}
