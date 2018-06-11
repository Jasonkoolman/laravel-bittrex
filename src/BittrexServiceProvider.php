<?php

namespace Koolm\Bittrex;

use Koolm\Bittrex\BittrexClient;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container;

class BittrexServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerClient();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__ . '/../config/bittrex.php');

        $this->publishes([
            $source => config_path('bittrex.php'),
        ]);

        $this->mergeConfigFrom($source, 'bittrex');
    }

    /**
     * Register the API client.
     *
     * @return void
     */
    protected function registerClient()
    {
        $this->app->singleton('bittrex', function (Container $app) {
            $config = $app['config']->get('bittrex');

            return new BittrexClient(
                $config['key'],
                $config['secret']
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['bittrex'];
    }
}
