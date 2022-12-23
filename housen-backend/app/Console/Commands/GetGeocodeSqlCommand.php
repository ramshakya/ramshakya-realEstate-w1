<?php

namespace App\Console\Commands;

use App\Http\Controllers\GetGeocodeMongoController;
use App\Http\Controllers\importListings\GetGeocodeSqlController;
use Illuminate\Console\Command;

class GetGeocodeSqlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getGeoCodeSql';

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
        $new = new GetGeocodeSqlController();
        $new->index();
        return Command::SUCCESS;
    }
}
