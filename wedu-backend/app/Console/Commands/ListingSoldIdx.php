<?php

namespace App\Console\Commands;

use App\Http\Controllers\importListings\ListingsSoldControllerIDXComm;
use Illuminate\Console\Command;

class ListingSoldIdx extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:listingSoldIdx';

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
        $new = new ListingsSoldControllerIDXComm();
        $new->importPropertyListing();
        return 0;
    }
}
