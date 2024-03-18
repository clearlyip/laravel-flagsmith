<?php

namespace CIP\Tests\Feature;

use CIP\Tests\App;
use CIP\Tests\Models\User;
use Flagsmith\Flagsmith;
use Flagsmith\Models\Flags;
use Flagsmith\Utils\Collections\FlagModelsList;
use Illuminate\Support\Facades\Config;
use Flagsmith\Models\Flag;
use Mockery;

class HasFlagsTraitTest extends App
{
    public function testGetFlagsmithOnUserModel()
    {
        Config::set('flagsmith.key', 'id');

        $user = User::create([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ]);

        $this->assertInstanceOf(Flagsmith::class, $user->getFlagsmith());
    }

    public function testGetFlags()
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

        $user->getFlags();
    }

    public function testSkipFlagCache()
    {
        $user = User::create([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ]);

        $this->assertFalse($user->getFlagsmith()->skipCache());

        $user->skipFlagCache();

        $this->assertTrue($user->getFlagsmith()->skipCache());
    }

    public function testIsFlagEnabled()
    {
        $flagsmith = \Mockery::mock(Flagsmith::class);
        $this->app->instance(Flagsmith::class, $flagsmith);

        Config::set('flagsmith.identity.identifier', 'id');
        $user = User::create([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ]);

        $FlagModelsList = new FlagModelsList([
            'bar' => (new Flag())->withFeatureName('bar')->withEnabled(true),
        ]);

        $flagsmith
            ->shouldReceive('getIdentityFlags')
            ->times(3)
            ->withArgs(
                fn($identifier, $traits) => $identifier ===
                    (string) $user->id && $traits->email === $user->email,
            )
            ->andReturn((new Flags())->withFlags($FlagModelsList));

        $this->assertFalse($user->isFlagEnabled('foo'));
        $this->assertTrue($user->isFlagEnabled('foo', true));

        $this->assertTrue($user->isFlagEnabled('bar'));
    }

    public function testGetFlagValue()
    {
        $flagsmith = \Mockery::mock(Flagsmith::class);
        $this->app->instance(Flagsmith::class, $flagsmith);

        Config::set('flagsmith.identity.identifier', 'id');
        $user = User::create([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ]);

        $FlagModelsList = new FlagModelsList([
            'bar' => (new Flag())
                ->withFeatureName('bar')
                ->withEnabled(true)
                ->withValue('foo'),
        ]);

        $flagsmith
            ->shouldReceive('getIdentityFlags')
            ->times(3)
            ->withArgs(
                fn($identifier, $traits) => $identifier ===
                    (string) $user->id && $traits->email === $user->email,
            )
            ->andReturn((new Flags())->withFlags($FlagModelsList));

        $this->assertNull($user->getFlagValue('foo'));
        $this->assertEquals('bar', $user->getFlagValue('foo', 'bar'));

        $this->assertEquals($user->getFlagValue('bar'), 'foo');
    }
}
