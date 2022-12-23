<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
	//
	if (class_exists('Swift_Preferences')) {
        \Swift_Preferences::getInstance()->setTempDir(storage_path().'/tmp');
    } else {
        Log::warning('Class Swift_Preferences does not exists');
    }
    }
}
