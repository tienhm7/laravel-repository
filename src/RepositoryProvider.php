<?php

namespace tienhm7\Repository;

use Illuminate\Support\ServiceProvider;
use tienhm7\Repository\Console\RepositoryMakeCommand;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RepositoryMakeCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}