<?php

namespace Maengkom\Box;

use Illuminate\Support\ServiceProvider;

class BoxAPIServiceProviderLumen extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        // merge default config
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'boxapi');

        $config = array(
            'client_id'         => $_ENV['BOX_CLIENT_ID'],
            'client_secret'     => $_ENV['BOX_CLIENT_SECRET'],
            'redirect_uri'      => $_ENV['BOX_REDIRECT_URI'],
            'enterprise_id'     => $_ENV['BOX_ENTERPRISE_ID'],
            'app_user_name'     => $_ENV['BOX_APP_USER_NAME'],
            'app_user_id'       => $_ENV['BOX_APP_USER_ID'],
            'public_key_id'     => $_ENV['BOX_PUBLIC_KEY_ID'],
            'passphrase'        => $_ENV['BOX_PASSPHRASE'],
            'expiration'        => $_ENV['BOX_EXPIRATION'],
            'private_key_file'  => base_path() . "/" . $_ENV['BOX_PRIVATE_KEY_FILE']
        );

        // create image
        $app['boxappuser'] = $app->share(function ($app) use ($config) {
            return new BoxAppUser($config);
        });

        $app->alias('boxappuser', 'Maengkom\Box\BoxAppUser');
    }
}
