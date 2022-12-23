<?php

namespace App\Console\Commands;


use App\Http\Controllers\importListings\ImagesControllerSoldVOW;
use App\Http\Controllers\importListings\ImagesControllerVOW;
use Illuminate\Console\Command;

class ImagesListingSold extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:imagesListingSold';

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
        $new = new ImagesControllerSoldVOW();
        $new->getThumbnail();
        return 0;
    }
}
