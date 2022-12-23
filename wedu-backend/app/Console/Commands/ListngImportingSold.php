<?php

namespace App\Console\Commands;

use App\Http\Controllers\importListings\SoldListingControllerVOW;
use Illuminate\Console\Command;

class ListngImportingSold extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ListingImportingSold';

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
        $new = new SoldListingControllerVOW();
        $new->importPropertyListing();
        return 0;
    }
}
