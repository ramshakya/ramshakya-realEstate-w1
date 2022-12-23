<?php

namespace App\Console\Commands;

use App\Http\Controllers\importListings\ListingsSoldControllerIDX;
use Illuminate\Console\Command;

class ListingsImportSoldIDX extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:soldListingsImportIDX';

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
        $new = new ListingsSoldControllerIDX();
        $new->importPropertyListing();
        return 0;
    }
}
