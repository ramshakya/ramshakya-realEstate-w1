<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\importListings\GetImagesSize;

class GetImageSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getImageSize';

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
        $new = new GetImagesSize();
        $new->getSize();
        return 0;
    }
}
