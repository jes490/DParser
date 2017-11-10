<?php

namespace Jes490\DParser;

use Illuminate\Support\ServiceProvider;

class DParserServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(DParser::class, function ($app, array $parameters)
        {
            return new DParser($parameters[0]);
        });
    }
}
