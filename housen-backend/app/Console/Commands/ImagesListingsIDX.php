<?php

namespace App\Console\Commands;

use App\Http\Controllers\importListings\ImagesControllerIDX;
use App\Http\Controllers\importListings\ImagesControllerIDXConvertWebp;
use Illuminate\Console\Command;

class ImagesListingsIDX extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:imageListingsIDX';

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
        $new = new ImagesControllerIDXConvertWebp();
        $new->imageImport();
        return 0;
    }
}
