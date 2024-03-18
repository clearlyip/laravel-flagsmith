<?php

namespace CIP\Tests\Feature;

use CIP\Tests\App;
use CIP\Tests\Models\User;
use Clearlyip\LaravelFlagsmith\Jobs\SyncUser;
use Flagsmith\Flagsmith;
use Flagsmith\Models\Flags;
use Flagsmith\Utils\Collections\FlagModelsList;
use Illuminate\Support\Facades\Config;
use Flagsmith\Models\Flag;
use Mockery;

class SyncUserTest extends App
{
    public function testSyncUser()
    {
        $flagsmith = \Mockery::mock(Flagsmith::class);
        $this->app->instance(Flagsmith::class, $flagsmith);

        Config::set('flagsmith.identity.identifier', 'id');
        Config::set('flagsmith.identity.traits', ['email']);
        $user = User::create([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ]);

        $flagsmith
            ->shouldReceive('getIdentityFlags')
            ->once()
            ->withArgs(
                fn($identifier, $traits) => $identifier ===
                    (string) $user->id && $traits->email === $user->email,
            )
            ->andReturn(new Flags());

        $flagsmith
            ->shouldReceive('withSkipCache')
            ->once()
            ->andReturnSelf();

        (new SyncUser($user))->handle();
    }
}
