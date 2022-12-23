<?php

namespace App\Console\Commands;

use App\Http\Controllers\importListings\ListingsSoldControllerVOW;
use App\Http\Controllers\importListings\ListingsSoldControllerVOWBck;
use Illuminate\Console\Command;

class ListingsImportSoldVOW extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:soldListingsImportVOW';

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
        $new = new ListingsSoldControllerVOW();
        $new->importPropertyListing();
        return 0;
    }
}
