<?php

namespace CIP\Tests\Feature;

use CIP\Tests\App;
use CIP\Tests\Models\User;
use Clearlyip\LaravelFlagsmith\Listeners\UserLogin;
use Flagsmith\Flagsmith;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Bus;
use Clearlyip\LaravelFlagsmith\Jobs\SyncUser;
use Flagsmith\Cache;

class UserLoginListenerTest extends App
{
    public function testUserLogin()
    {
        Bus::fake();

        $flagsmith = \Mockery::mock(Flagsmith::class);
        $this->app->instance(Flagsmith::class, $flagsmith);

        Config::set('flagsmith.identity.queue', 'default');
        Config::set('flagsmith.identity.identifier', 'id');
        Config::set('flagsmith.identity.traits', ['email']);
        $user = User::create([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ]);

        $flagsmith
            ->shouldReceive('getCache')
            ->once()
            ->andReturn(
                new Cache(
                    app(\Illuminate\Contracts\Cache\Factory::class)->store(),
                    'foo',
                ),
            );

        $login = new \Illuminate\Auth\Events\Login('foo', $user, false);

        $userLogin = new UserLogin();
        $userLogin->handle($login);

        Bus::assertDispatched(SyncUser::class);
    }
}
