<?php
namespace Clearlyip\LaravelFlagsmith\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class SyncUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Authenticatable $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Authenticatable $user)
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
        //Our Trait exists
        if (!method_exists($this->user, 'getFlagsmith')) {
            return;
        }

        $this->user->skipFeatureCache(true);
        $this->user->getFeatures();
    }
}
