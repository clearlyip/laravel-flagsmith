<?php
namespace Clearlyip\LaravelFlagsmith;

use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    const FLAGSMITH_CONFIG_PATH = __DIR__ . '/../config/flagsmith.php';

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(self::FLAGSMITH_CONFIG_PATH, 'flagsmith');
        $this->app
            ->when(Flagsmith::class)
            ->needs(ClientInterface::class)
            ->give(Client::class);
        $this->app->scoped('flagsmith', function ($app) {
            return $app->make(Flagsmith::class);
        });
        //$this->app->alias(Flagsmith::class, 'flagsmith');
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
                self::FLAGSMITH_CONFIG_PATH => config_path('flagsmith.php'),
            ],
            ['flagsmith']
        );
    }
}
