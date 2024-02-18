<?php

namespace Clearlyip\LaravelFlagsmith\Jobs;

use Clearlyip\LaravelFlagsmith\Contracts\UserFlags;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class SyncUser implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Authenticatable&UserFlags $user;

    /**
     * Create a new job instance.
     *
     * @param  Authenticatable&UserFlags  $user
     * @return void
     */
    public function __construct(Authenticatable&UserFlags $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->skipFlagCache(true)->getFlags();
    }
}
