<?php

namespace Maengkom\Box;

use Illuminate\Support\ServiceProvider;

class BoxAPIServiceProviderLaravel4 extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('maengkom/boxapi');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app['config']->package('maengkom/boxapi', app_path().'/config/packages/maengkom/boxapi/');

        // create appuser
        $app['boxappuser'] = $app->share(function ($app) {
            return new BoxAppUser( $app['config']->get('boxapi::config') );
        });

        $app->alias('boxappuser', 'Maengkom\Box\BoxAppUser');
    }
}
