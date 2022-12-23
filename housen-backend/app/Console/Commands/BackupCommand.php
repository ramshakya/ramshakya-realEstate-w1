<?php

namespace App\Console\Commands;

use App\Http\Controllers\importListings\BackupController;
use Illuminate\Console\Command;

class BackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:takeBackup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will take backup of active records listings';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $test = new BackupController();
        $test->index();
        return 0;
    }
}
