<?php

namespace App\Console\Commands;

use App\Http\Controllers\importListings\ListingsControllerTestVOW;
use App\Http\Controllers\importListings\ListingsSoldControllerTempIDX;
use App\Http\Controllers\importListings\ListingsSoldControllerTestVOW;
use App\Http\Controllers\TestController;
use Illuminate\Console\Command;

class TestCommandSoldVow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:testSoldVow';

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
        $new = new ListingsSoldControllerTestVOW();
        $new->importPropertyListing();
        return 0;
    }
}
