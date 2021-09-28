<?php
namespace Clearlyip\LaravelFlagsmith;

use Flagsmith\Flagsmith;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Support\Facades\Event;
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
        $this->app->scoped(Flagsmith::class, function ($app) {
            $store = config('flagsmith.cache.store', null);

            $cacheFactory = $app->make(
                \Illuminate\Contracts\Cache\Factory::class
            );

            $cacheProvider = $cacheFactory->store(
                $store === 'default' ? null : $store
            );

            return (new Flagsmith(
                config('flagsmith.key'),
                config('flagsmith.host')
            ))
                ->withTimeToLive(config('flagsmith.cache.ttl'))
                ->withCachePrefix(config('flagsmith.cache.prefix'))
                ->withCache($cacheProvider)
                ->withUseCacheAsFailover(config('flagsmith.cache.failover'));
        });
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
        $this->loadRoutesFrom(dirname(__DIR__) . '/routes/flagsmith.php');

        Event::listen(\Illuminate\Auth\Events\Login::class, [
            \Clearlyip\LaravelFlagsmith\Listeners\UserLogin::class,
            'handle',
        ]);
    }
}
