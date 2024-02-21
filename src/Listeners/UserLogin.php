<?php

namespace Clearlyip\LaravelFlagsmith\Listeners;

use Clearlyip\LaravelFlagsmith\Contracts\UserFlags;
use Clearlyip\LaravelFlagsmith\Jobs\SyncUser;
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

        //Doesn't exist so get it now
        if (!$cache->has('Identity.' . $user->getFlagIdentityId())) {
            SyncUser::dispatchSync($user);
        } else {
            SyncUser::dispatch($user)->onQueue($queue);
        }
    }
}
