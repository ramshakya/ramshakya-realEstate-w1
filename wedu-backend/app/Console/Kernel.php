<?php

namespace App\Console;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\Listings::class,
        Commands\RefreshListing::class,
        Commands\ImagesListing::class,
        Commands\GetGeocodeSqlCommand::class,
        Commands\ListingImportIDX::class,
        Commands\ListingImportVOW::class,
        Commands\ImagesListingsIDX::class,
        Commands\ImagesListingsVOW::class,
        Commands\ListingsImportSoldIDX::class,
        Commands\ListingsImportSoldVOW::class,
        Commands\SendAlerts::class,
        Commands\RetryImagesListingsVOW::class,
        Commands\TestCommand::class,
        Commands\DeleteListingPropertyVow::class,
        Commands\GetSoldGeoCode::class,
        Commands\ImagesListingsSoldVow::class,
        Commands\ImagesListingSold::class,
        Commands\TestCommandSoldVow::class,
        Commands\TestCommandVow::class,
	Commands\UpdateJson::class,
        Commands\BackupCommand::class,
        Commands\ListingSoldIdx::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         //$schedule->command('propertyCron')->everyFiveMinutes();
         $schedule->command('imagesListing')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
