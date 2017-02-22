<?php

namespace Maengkom\Box;

use Illuminate\Support\ServiceProvider;

class BoxAPIServiceProviderLaravel5 extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/config.php' => config_path('boxapi.php')
        ], 'boxapi');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        // merge default config
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'boxapi');

        // create appuser
        $app->singleton('boxappuser', function ($app) {
            return new BoxAppUser( $app['config']->get('boxapi') );
        });

        $app->alias('boxappuser', 'Maengkom\Box\BoxAppUser');

        // create standard user
        $app->singleton('boxstandarduser', function ($app) {
            return new BoxStandardUser( $app['config']->get('boxapi') );
        });

        $app->alias('boxstandarduser', 'Maengkom\Box\BoxStandardUser');

    }
}
