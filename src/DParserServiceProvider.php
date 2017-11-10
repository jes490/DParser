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
            if (count($parameters) > 0)
                return new DParser($parameters[0]);
            else
                throw new InvalidArgumentException('At least one argument needed.');
        });
    }
}
