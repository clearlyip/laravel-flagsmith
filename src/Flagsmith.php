<?php
namespace Clearlyip\LaravelFlagsmith;

use Flagsmith\Flagsmith as FlagsmithClient;
use Flagsmith\Models\Identity;
use Flagsmith\Models\IdentityTrait;
use Psr\Http\Client\ClientInterface;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Support\Traits\ForwardsCalls;
use Psr\SimpleCache\CacheInterface;

/**
 * Clearlyip\LaravelFlagsmith\Flagsmith
 *
 * @mixin \Flagsmith\Flagsmith
 */
class Flagsmith
{
    use ForwardsCalls;

    private ClientInterface $client;
    private CacheFactory $cacheFactory;
    private FlagsmithClient $flagsmith;

    private ?CacheInterface $cache = null;

    public function __construct(
        ClientInterface $client,
        CacheFactory $cacheFactory
    ) {
        $this->client = $client;
        $this->cacheFactory = $cacheFactory;
        $this->flagsmith = (new FlagsmithClient(
            config('flagsmith.key'),
            config('flagsmith.host')
        ))
            ->withTimeToLive(15)
            ->withCachePrefix(config('flagsmith.cache.prefix'))
            ->withCache($this->getCacheInterface());
    }

    /**
     * Get the Underlying Cache Interface (PSR6)
     *
     * @return CacheInterface|null
     */
    public function getCacheInterface(): ?CacheInterface
    {
        if (isset($this->cache)) {
            return $this->cache;
        }

        $store = config('flagsmith.cache.store');

        if (empty($store)) {
            return null;
        }

        $this->cache = $this->cacheFactory->store(
            $store === 'default' ? null : $store
        );

        return $this->cache;
    }

    public function hasCache(): bool
    {
        return !is_null($this->getCacheInterface());
    }

    /**
     * Handle dynamic method calls into flagsmith.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardDecoratedCallTo(
            $this->flagsmith,
            $method,
            $parameters
        );
    }
}
