<?php

namespace App\Console\Commands;

use App\Http\Controllers\agent\AlertsController;
use Illuminate\Console\Command;

class SendAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendAlert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command This is for sending alerts';

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
        $new = new AlertsController();
        $new->index();
        return 0;
    }
}
