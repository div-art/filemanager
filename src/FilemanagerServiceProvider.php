<?php

namespace Divart\Filemanager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Route;

class FilemanagerServiceProvider extends ServiceProvider{

    /**
     * Register any package services.
     *
     * @return void
     */

    public function register()
    {
        $this->app->bind('filemanager', function ($app)
        {
            return new Filemanager;
        });
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/Http/api.php');
        $this->publishes([__DIR__ . "/config/config.php" => config_path('filemanager.php')], 'filemanager-config');
    }

}