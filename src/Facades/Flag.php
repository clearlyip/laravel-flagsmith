<?php

namespace Clearlyip\LaravelFlagsmith\Facades;

use Illuminate\Support\Facades\Facade;
use Clearlyip\LaravelFlagsmith\Models\Flag as ModelsFlag;

/**
 * @method static \Flagsmith\Flagsmith withCustomHeaders(object $customHeaders)
 * @method static \Flagsmith\Flagsmith withRetries(\Flagsmith\Utils\Retry $retries)
 * @method static \Flagsmith\Flagsmith withEnvironmentTtl(int $environmentTtl)
 * @method static \Flagsmith\Flagsmith withAnalytics(\Flagsmith\Utils\AnalyticsProcessor $analytics)
 * @method static \Flagsmith\Flagsmith withDefaultFlagHandler(\Closure $defaultFlagHandler)
 * @method static \Flagsmith\Flagsmith withClient(\Psr\Http\Client\ClientInterface $client)
 * @method static \Flagsmith\Flagsmith withRequestFactory(\Psr\Http\Message\RequestFactoryInterface $requestFactory)
 * @method static \Flagsmith\Flagsmith withStreamFactory(\Psr\Http\Message\StreamFactoryInterface $streamFactory)
 * @method static \Flagsmith\Flagsmith withCache(\Psr\SimpleCache\CacheInterface|null $cache)
 * @method static bool hasCache()
 * @method static \Flagsmith\Cache|null getCache()
 * @method static \Flagsmith\Flagsmith withTimeToLive(int $timeToLive)
 * @method static \Flagsmith\Flagsmith withCachePrefix(string $cachePrefix)
 * @method static bool skipCache()
 * @method static \Flagsmith\Flagsmith withSkipCache(bool $skipCache)
 * @method static \Flagsmith\Flagsmith withUseCacheAsFailover(bool $useCacheAsFailover)
 * @method static \Flagsmith\Engine\Environments\EnvironmentModel getEnvironment()
 * @method static \Flagsmith\Models\Flags getEnvironmentFlags()
 * @method static \Flagsmith\Models\Flags getIdentityFlags(string $identifier, ?object $traits = null)
 * @method static array getIdentitySegments(string $identifier, ?object $traits = null)
 * @method static void updateEnvironment()
 *
 * @see \Flagsmith\Flagsmith
 */
class Flag extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ModelsFlag::class;
    }
}
