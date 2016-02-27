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
            __DIR__.'/config/boxapi.php' => config_path('boxapi.php')
        ]);
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
        $app['boxappuser'] = $app->share(function ($app) {
            return new BoxAppUser( $app['config']->get('boxapi') );
        });

        $app->alias('boxappuser', 'Maengkom\Box\BoxAppUser');

        // create standard user
        $app['boxstandarduser'] = $app->share(function ($app) {
            // return new BoxStandardUser( $app['config']->get('boxapi') );
            return new BoxStandardUser();
        });

        $app->alias('boxstandarduser', 'Maengkom\Box\BoxStandardUser');

    }
}
