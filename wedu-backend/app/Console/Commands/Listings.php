<?php

namespace App\Console\Commands;

use App\Http\Controllers\importListings\ListingsController;
use Illuminate\Console\Command;

class Listings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:listings';

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
	$listing = new ListingsController();
        $listing->importPropertyListing();
        return 0;
    }
}
