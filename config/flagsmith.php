<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Flagsmith Host
    |--------------------------------------------------------------------------
    | Value: String
    |
    | The Flagsmith API host. This is the host that will be used to make
    | requests to the Flagsmith API.
    |
    */

    'host' => env('FLAGSMITH_HOST', 'https://api.flagsmith.com/api/v1/'),

    /*
    |--------------------------------------------------------------------------
    | Flagsmith API Key
    |--------------------------------------------------------------------------
    | Value: String
    |
    | The Flagsmith API key. This is the key that will be used to make
    | requests to the Flagsmith API.
    |
    */

    'key' => env('FLAGSMITH_API_KEY', ''),

    'global' => [
        /*
        |--------------------------------------------------------------------------
        | Update Schedule
        |--------------------------------------------------------------------------
        | Value: String|null (In cron format)
        |
        | Define a cron format here to update the global features in the cache (see Cache)
        | on a schedule. This can be used in place of or in conjunction with webhooks
        |
        | A null value will disable the scheduler
        |
        */

        'schedule' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Flagsmith Identity Settings
    |--------------------------------------------------------------------------
    |
    */
    'identity' => [
        /*
        |--------------------------------------------------------------------------
        | Identifier
        |--------------------------------------------------------------------------
        | Value: string
        |
        | The Eloquent Attribute to use as the identifier for the user.
        |
        */

        'identifier' => 'id',

        /*
        |--------------------------------------------------------------------------
        | Traits Mapping
        |--------------------------------------------------------------------------
        | Value: array<String>
        |
        | The Eloquent Attribute Traits on a user to send back to the Flagsmith API
        |
        */

        'traits' => ['email'],

        /*
        |--------------------------------------------------------------------------
        | Update Queue
        |--------------------------------------------------------------------------
        | Value: String|null
        |
        | Define a queue here that will be used to update the identities feature
        | cache on login through a queue instead of synchronosly. This will speed
        | up login attempts because features will only be updated when a user logs
        | in through a queue that will not block the request thread.
        |
        | If the user does not exist in cache (or cache is disabled, see Cache settings)
        | then the query will run asynchronously regardless of this setting
        |
        | A null value will disable the queue and update the identity synchronously
        |
        */

        'queue' => null,

        /*
        |--------------------------------------------------------------------------
        | Update Schedule
        |--------------------------------------------------------------------------
        | Value: String|null (In cron format)
        |
        | Define a cron format here to update the identities in the cache (see Cache)
        | on a schedule. This can be used in place of or in conjunction with webhooks
        |
        | A null value will disable the scheduler
        |
        */

        'schedule' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhooks
    |--------------------------------------------------------------------------
    |
    | The following is a list of Webhooks that can be accepted
    |
    */

    'webhooks' => [
        /*
        |--------------------------------------------------------------------------
        | Feature
        |--------------------------------------------------------------------------
        |
        | Feature webhooks let you know when features have changed
        |
        | https://docs.flagsmith.com/advanced-use/system-administration#web-hooks
        |
        */

        'feature' => [
            /*
            |--------------------------------------------------------------------------
            | Route
            |--------------------------------------------------------------------------
            | Value: String|null
            |
            | The route that will be used to handle the webhook.
            |
            | Setting this value to null disables the route. Otherwise it will
            | work off the base route
            |
            | Note: This route is disabled (null) by default because it is well known and Flagsmith
            | does not allow authentication against webhooks at this time
            |
            */

            'route' => null,

            /*
            |--------------------------------------------------------------------------
            | Middleware
            |--------------------------------------------------------------------------
            | Value: Array
            |
            | Add any middleware to apply to the feature webhook route
            |
            */

            'middleware' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | The following is a list of Laravel Caching options.
    |
    | All Global and User features will be cached
    |
    */

    'cache' => [
        /*
        |--------------------------------------------------------------------------
        | Store
        |--------------------------------------------------------------------------
        | Value: String|null
        |
        | The caching (see cache.php in config) store to use.
        |
        | Set to null to disable caching
        |
        | Caching is recommended for performance reasons.
        |
        */

        'store' => 'default',

        /*
        |--------------------------------------------------------------------------
        | Prefix
        |--------------------------------------------------------------------------
        | Value: String
        |
        | The prefix used for storing the cache. This is used to namespace
        |
        */

        'prefix' => 'flagsmith',

        /*
        |--------------------------------------------------------------------------
        | TTL (Time To Live)
        |--------------------------------------------------------------------------
        | Value: Integer (in Seconds)
        |
        | Time to live or hop limit is a mechanism which limits the lifespan or
        | lifetime of data in a computer or network. TTL may be implemented as
        | a counter or timestamp attached to or embedded in the data.
        | Once the prescribed event count or timespan has elapsed, data is
        | discarded or revalidated
        |
        */
        'ttl' => env('FLAGSMITH_CACHE_TTL', 15),

        /*
        |--------------------------------------------------------------------------
        | Failover
        |--------------------------------------------------------------------------
        | Value: Boolean
        |
        | In cases where the remote server is down this option will utilize
        | the local cache.
        |
        */
        'failover' => env('FLAGSMITH_CACHE_FAILOVER', true),
    ],
];
