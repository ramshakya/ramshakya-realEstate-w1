<?php

namespace App\Console\Commands;

use App\Http\Controllers\importListings\ImagesControllerVOW;
use App\Http\Controllers\importListings\RetryImagesControllerVOW;
use Illuminate\Console\Command;

class RetryImagesListingsVOW extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:retryImageListingsVOW';

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
        $new = new RetryImagesControllerVOW();
        $new->imageImport();
        return 0;
    }
}
