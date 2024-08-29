<?php

namespace Clearlyip\LaravelFlagsmith\Listeners;

use Clearlyip\LaravelFlagsmith\Contracts\UserFlags;
use Clearlyip\LaravelFlagsmith\Jobs\SyncUser;
use Flagsmith\Utils\IdentitiesGenerator;
use Illuminate\Auth\Events\Login;

class UserLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        if (!($user instanceof UserFlags)) {
            return;
        }

        $queue = config('flagsmith.identity.queue');
        if ($queue === null) {
            return;
        }

        $cache = $user->getFlagsmith()->getCache();
        if ($cache === null) {
            return;
        }

        $key = IdentitiesGenerator::generateIdentitiesCacheKey(
            $user->getFlagIdentityId(),
            (object) $user->getFlagTraits(),
            false,
        );

        //Doesn't exist so get it now
        if (!$cache->has($key)) {
            SyncUser::dispatchSync($user);
        } else {
            SyncUser::dispatch($user)->onQueue($queue);
        }
    }
}
