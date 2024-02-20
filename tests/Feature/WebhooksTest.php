<?php

namespace CIP\Tests\Feature;

use CIP\Tests\App;
use CIP\Tests\Models\User;
use Clearlyip\LaravelFlagsmith\Http\Controllers\Webhooks;
use Flagsmith\Flagsmith;
use Illuminate\Support\Facades\Config;
use Flagsmith\Cache;
use Illuminate\Support\Facades\Route;
use stdClass;

class WebhooksTest extends App
{
    public function testIdentityWebhook()
    {
        $user = User::create([
            'name' => fake()->name(),
            'email' => fake()->email(),
            'password' => fake()->password(),
        ]);

        $trait = new stdClass();
        $trait->id = 5638;
        $trait->trait_key = 'email';
        $trait->trait_value = $user->email;

        $flag = new stdClass();
        $flag->id = 12;
        $flag->feature = new stdClass();
        $flag->feature->id = 7168;
        $flag->feature->name = 'butter_bar';
        $flag->feature->created_date = '2021-02-10T20:03:43.348556Z';
        $flag->feature->description =
            'Show html in a butter bar for certain users';
        $flag->feature->initial_value = null;
        $flag->feature->default_enabled = false;
        $flag->feature->type = 'CONFIG';
        $flag->feature_state_value =
            '<strong>You are using the develop environment.</strong>';
        $flag->environment = 23;
        $flag->identity = null;
        $flag->feature_segment = null;
        $flag->enabled = false;

        $cachedIdentityFlagsApiResponse = new stdClass();
        $cachedIdentityFlagsApiResponse->traits = [$trait];
        $cachedIdentityFlagsApiResponse->flags = [$flag];

        Config::set('flagsmith.identity.queue', 'default');
        Config::set('flagsmith.identity.identifier', 'id');
        Config::set('flagsmith.identity.traits', ['email']);

        $flagsmith = \Mockery::mock(Flagsmith::class);
        $this->app->instance(Flagsmith::class, $flagsmith);

        Config::set('flagsmith.webhooks.feature.route', 'webhook');

        Route::middleware(
            config('flagsmith.webhooks.feature.middleware', []),
        )->post(config('flagsmith.webhooks.feature.route'), [
            Webhooks::class,
            'feature',
        ]);

        $flagsmith
            ->shouldReceive('getCache')
            ->times(3)
            ->andReturn(
                new Cache(
                    app(\Illuminate\Contracts\Cache\Factory::class)->store(),
                    'flagsmith',
                ),
            );

        $flagsmith
            ->shouldReceive('hasCache')
            ->times(2)
            ->andReturn(true);

        $flagsmithCache = $flagsmith->getCache();
        $flagsmithCache->set(
            'Identity.' . $user->id,
            $cachedIdentityFlagsApiResponse,
        );

        $res = $flagsmithCache->get('Identity.' . $user->id);
        $this->assertEquals($res->traits[0]->trait_key, 'email');
        $this->assertEquals($res->traits[0]->trait_value, $user->email);
        $this->assertFalse($res->flags[0]->enabled);

        $response = $this->post('webhook', [
            'data' => [
                'changed_by' => 'Ben Rometsch',
                'new_state' => [
                    'enabled' => true,
                    'environment' => [
                        'id' => 23,
                        'name' => 'Development',
                    ],
                    'feature' => [
                        'created_date' => '2021-02-10T20:03:43.348556Z',
                        'default_enabled' => false,
                        'description' =>
                            'Show html in a butter bar for certain users',
                        'id' => 7168,
                        'initial_value' => null,
                        'name' => 'butter_bar',
                        'project' => [
                            'id' => 12,
                            'name' => 'Flagsmith Website',
                        ],
                        'type' => 'CONFIG',
                    ],
                    'feature_segment' => null,
                    'feature_state_value' =>
                        '<strong>You are using the develop environment.</strong>',
                    'identity' => null,
                    'identity_identifier' => '1',
                ],
                'previous_state' => [
                    'enabled' => false,
                    'environment' => [
                        'id' => 23,
                        'name' => 'Development',
                    ],
                    'feature' => [
                        'created_date' => '2021-02-10T20:03:43.348556Z',
                        'default_enabled' => false,
                        'description' =>
                            'Show html in a butter bar for certain users',
                        'id' => 7168,
                        'initial_value' => null,
                        'name' => 'butter_bar',
                        'project' => [
                            'id' => 12,
                            'name' => 'Flagsmith Website',
                        ],
                        'type' => 'CONFIG',
                    ],
                    'feature_segment' => null,
                    'feature_state_value' =>
                        '<strong>You are using the develop environment.</strong>',
                    'identity' => null,
                    'identity_identifier' => '1',
                ],
                'timestamp' => '2021-06-18T07:50:26.595298Z',
            ],
            'event_type' => 'FLAG_UPDATED',
        ]);

        $response->assertOk();

        $res = $flagsmithCache->get('Identity.' . $user->id);
        $this->assertEquals($res->traits[0]->trait_key, 'email');
        $this->assertEquals($res->traits[0]->trait_value, $user->email);
        $this->assertTrue($res->flags[0]->enabled);

        $response = $this->post('webhook', [
            'data' => [
                'changed_by' => 'Ben Rometsch',
                'new_state' => [
                    'enabled' => true,
                    'environment' => [
                        'id' => 23,
                        'name' => 'Development',
                    ],
                    'feature' => [
                        'created_date' => '2021-02-10T20:03:43.348556Z',
                        'default_enabled' => false,
                        'description' =>
                            'Show html in a butter bar for certain users',
                        'id' => 7169,
                        'initial_value' => null,
                        'name' => 'spinach_bar',
                        'project' => [
                            'id' => 12,
                            'name' => 'Flagsmith Website',
                        ],
                        'type' => 'CONFIG',
                    ],
                    'feature_segment' => null,
                    'feature_state_value' =>
                        '<strong>You are using the develop environment.</strong>',
                    'identity' => null,
                    'identity_identifier' => '1',
                ],
                'previous_state' => [
                    'enabled' => false,
                    'environment' => [
                        'id' => 23,
                        'name' => 'Development',
                    ],
                    'feature' => [
                        'created_date' => '2021-02-10T20:03:43.348556Z',
                        'default_enabled' => false,
                        'description' =>
                            'Show html in a butter bar for certain users',
                        'id' => 7169,
                        'initial_value' => null,
                        'name' => 'spinach_bar',
                        'project' => [
                            'id' => 12,
                            'name' => 'Flagsmith Website',
                        ],
                        'type' => 'CONFIG',
                    ],
                    'feature_segment' => null,
                    'feature_state_value' =>
                        '<strong>You are using the develop environment.</strong>',
                    'identity' => null,
                    'identity_identifier' => '1',
                ],
                'timestamp' => '2021-06-18T07:50:26.595298Z',
            ],
            'event_type' => 'FLAG_UPDATED',
        ]);

        $response->assertOk();

        $res = $flagsmithCache->get('Identity.' . $user->id);
        $this->assertEquals($res->flags[1]->feature->name, 'spinach_bar');
    }

    public function testGlobalWebhook()
    {
        $cachedGlobalFlagsApiResponse = [
            0 => [
                'id' => 12,
                'feature' => [
                    'id' => 7168,
                    'name' => 'butter_bar',
                    'created_date' => '2021-02-10T20:03:43.348556Z',
                    'description' =>
                        'Show html in a butter bar for certain users',
                    'initial_value' => null,
                    'default_enabled' => false,
                    'type' => 'CONFIG',
                ],
                'feature_state_value' =>
                    '<strong>You are using the develop environment.</strong>',
                'environment' => 23,
                'identity' => null,
                'feature_segment' => null,
                'enabled' => false,
            ],
        ];

        $flagsmith = \Mockery::mock(Flagsmith::class);
        $this->app->instance(Flagsmith::class, $flagsmith);

        Config::set('flagsmith.webhooks.feature.route', 'webhook');

        Route::middleware(
            config('flagsmith.webhooks.feature.middleware', []),
        )->post(config('flagsmith.webhooks.feature.route'), [
            Webhooks::class,
            'feature',
        ]);

        $flagsmith
            ->shouldReceive('getCache')
            ->times(3)
            ->andReturn(
                new Cache(
                    app(\Illuminate\Contracts\Cache\Factory::class)->store(),
                    'flagsmith',
                ),
            );

        $flagsmith
            ->shouldReceive('hasCache')
            ->times(2)
            ->andReturn(true);

        $flagsmithCache = $flagsmith->getCache();
        $flagsmithCache->set(
            'Global',
            json_decode(json_encode($cachedGlobalFlagsApiResponse)),
        );

        $res = $flagsmithCache->get('Global');
        $this->assertFalse($res[0]->enabled);

        $response = $this->post('webhook', [
            'data' => [
                'changed_by' => 'Ben Rometsch',
                'new_state' => [
                    'enabled' => true,
                    'environment' => [
                        'id' => 23,
                        'name' => 'Development',
                    ],
                    'feature' => [
                        'created_date' => '2021-02-10T20:03:43.348556Z',
                        'default_enabled' => false,
                        'description' =>
                            'Show html in a butter bar for certain users',
                        'id' => 7168,
                        'initial_value' => null,
                        'name' => 'butter_bar',
                        'project' => [
                            'id' => 12,
                            'name' => 'Flagsmith Website',
                        ],
                        'type' => 'CONFIG',
                    ],
                    'feature_segment' => null,
                    'feature_state_value' =>
                        '<strong>You are using the develop environment.</strong>',
                    'identity' => null,
                    'identity_identifier' => null,
                ],
                'previous_state' => [
                    'enabled' => false,
                    'environment' => [
                        'id' => 23,
                        'name' => 'Development',
                    ],
                    'feature' => [
                        'created_date' => '2021-02-10T20:03:43.348556Z',
                        'default_enabled' => false,
                        'description' =>
                            'Show html in a butter bar for certain users',
                        'id' => 7168,
                        'initial_value' => null,
                        'name' => 'butter_bar',
                        'project' => [
                            'id' => 12,
                            'name' => 'Flagsmith Website',
                        ],
                        'type' => 'CONFIG',
                    ],
                    'feature_segment' => null,
                    'feature_state_value' =>
                        '<strong>You are using the develop environment.</strong>',
                    'identity' => null,
                    'identity_identifier' => null,
                ],
                'timestamp' => '2021-06-18T07:50:26.595298Z',
            ],
            'event_type' => 'FLAG_UPDATED',
        ]);

        $response->assertOk();

        $res = $flagsmithCache->get('Global');
        $this->assertTrue($res[0]->enabled);

        $response = $this->post('webhook', [
            'data' => [
                'changed_by' => 'Ben Rometsch',
                'new_state' => [
                    'enabled' => true,
                    'environment' => [
                        'id' => 23,
                        'name' => 'Development',
                    ],
                    'feature' => [
                        'created_date' => '2021-02-10T20:03:43.348556Z',
                        'default_enabled' => false,
                        'description' =>
                            'Show html in a butter bar for certain users',
                        'id' => 7169,
                        'initial_value' => null,
                        'name' => 'spinach_bar',
                        'project' => [
                            'id' => 12,
                            'name' => 'Flagsmith Website',
                        ],
                        'type' => 'CONFIG',
                    ],
                    'feature_segment' => null,
                    'feature_state_value' =>
                        '<strong>You are using the develop environment.</strong>',
                    'identity' => null,
                    'identity_identifier' => null,
                ],
                'previous_state' => [
                    'enabled' => false,
                    'environment' => [
                        'id' => 23,
                        'name' => 'Development',
                    ],
                    'feature' => [
                        'created_date' => '2021-02-10T20:03:43.348556Z',
                        'default_enabled' => false,
                        'description' =>
                            'Show html in a butter bar for certain users',
                        'id' => 7169,
                        'initial_value' => null,
                        'name' => 'spinach_bar',
                        'project' => [
                            'id' => 12,
                            'name' => 'Flagsmith Website',
                        ],
                        'type' => 'CONFIG',
                    ],
                    'feature_segment' => null,
                    'feature_state_value' =>
                        '<strong>You are using the develop environment.</strong>',
                    'identity' => null,
                    'identity_identifier' => null,
                ],
                'timestamp' => '2021-06-18T07:50:26.595298Z',
            ],
            'event_type' => 'FLAG_UPDATED',
        ]);

        $response->assertOk();

        $res = $flagsmithCache->get('Global');
        $this->assertEquals($res[1]->feature->name, 'spinach_bar');
    }
}
