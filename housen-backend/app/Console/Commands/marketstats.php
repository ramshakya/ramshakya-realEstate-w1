<?php

namespace App\Console\Commands;

use App\Http\Controllers\frontend\HomeController;
use Illuminate\Console\Command;

class marketstats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:marketstats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $new = new HomeController();
        // $new->Marketstates();
        // sleep(100);
        // $new->Marketstates1st();
        // sleep(100);
        // $new->Marketstates1st_city();
        // sleep(100);
        // $new->Marketstate2nd();
        $new->UpdateMarketStats();

        return 0;
    }
}
