<?php

namespace App\Console\Commands;

use App\Http\Controllers\frontend\propertiesListings\PropertiesController;
use Illuminate\Console\Command;

class gererateGeoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:geoData';

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
        $new = new PropertiesController();
        $new->getPolygonsData();
        return Command::SUCCESS;
    }
}
