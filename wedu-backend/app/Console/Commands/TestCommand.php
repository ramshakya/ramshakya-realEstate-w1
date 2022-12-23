<?php

namespace App\Console\Commands;

use App\Http\Controllers\importListings\ListingsControllerTestVOW;
use App\Http\Controllers\importListings\ListingsSoldControllerTempIDX;
use App\Http\Controllers\TestController;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:test';

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
        $new = new TestController();
        $new->updateSlug();
        return 0;
    }
}
