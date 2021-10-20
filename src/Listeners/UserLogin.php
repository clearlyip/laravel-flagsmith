<?php
namespace Clearlyip\LaravelFlagsmith\Listeners;

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
        //Our Trait exists
        if (!method_exists($event->user, 'getFlagsmith')) {
            //TODO: should we log this?
            return;
        }

        $queue = config('flagsmith.identity.queue');
        if (is_null($queue) || !$event->user->featuresInCache()) {
            SyncUser::dispatchSync($event->user);
        } else {
            SyncUser::dispatch($event->user)->onQueue($queue);
        }
    }
}
