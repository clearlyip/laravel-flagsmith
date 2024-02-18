<img width="100%" src="https://github.com/Flagsmith/flagsmith/raw/main/static-files/hero.png"/>

Laravel-flagsmith was created by, and is maintained by **[Andrew Nagy](https://github.com/tm1000)**, the package is designed to allow Laravel to work with [Flagsmith](https://flagsmith.com/)

<p align="center">
<a href="https://packagist.org/packages/clearlyip/laravel-flagsmith"><img src="https://img.shields.io/packagist/dt/clearlyip/laravel-flagsmith" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/clearlyip/laravel-flagsmith"><img src="https://img.shields.io/packagist/v/clearlyip/laravel-flagsmith" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/clearlyip/laravel-flagsmith"><img src="https://img.shields.io/packagist/l/clearlyip/laravel-flagsmith" alt="License"></a>
</p>

## Features

-   Provides a trait to be able to get flags based on Laravel Users ([Flagsmith Identities](https://docs.flagsmith.com/basic-features/managing-identities))
-   Utilizes [Laravel's Queue](https://laravel.com/docs/8.x/queues) system to update flags in the background
-   Utilizes [Laravel's Cache](https://laravel.com/docs/8.x/cache) system to store flags in a cache for quick access
-   Utilizes [Laravel's Task Scheduling](https://laravel.com/docs/8.x/scheduling) system to update flags on a schedule
-   Adds a route to utilize [Flagsmith's webhooks](https://docs.flagsmith.com/advanced-use/system-administration) to update the cache when flags change

## Installation & Usage

> **Requires [PHP 8.2+](https://php.net/releases/)**

Require Laravel-flagsmith using [Composer](https://getcomposer.org):

```bash
composer require clearlyip/laravel-flagsmith
```

## Laravel Version Compatibility

| Laravel | Laravel Flagsmith |
| :------ | :---------------- |
| 8.x     | 1.x               |
| 9.x     | 2.x               |
| 10.x    | 3.x               |

## Usage

### Configuration Files

-   Publish the Laravel Flagsmith configuration file using the `vendor:publish` Artisan command. The `flagsmith` configuration file will be placed in your `config` directory (Use `--force` to overwrite your existing `clearly` config file):
    -   `php artisan vendor:publish --tag="flagsmith" [--force]`

All options are fully documented in the configuration file

### User

It's advised to add the interface `Clearlyip\LaravelFlagsmith\Contracts\UserFlags` to your user model. This will give you the ability to access flags directly from your user object.

You can add the following trait `Clearlyip\LaravelFlagsmith\Concerns\HasFlagss` to your user model to fulfill the requirements of `UserFlags`

During initial login user flags are synced through a queue which keeps them as up to date as possible

#### Get All Flags for a User

```php
$user = Auth::user();
$flags = $user->getFlags();
```

### Check if flag is enabled for a user

An optional second parameter can be added as the default if the flag does not exist

```php
$user = Auth::user();
$flags = $user->isFlagEnabled('foo');
```

#### Get a Flag value for a User

An optional second parameter can be added as the default if the flag does not exist

```php
$user = Auth::user();
$vakue = $user->getFlagValue('foo');
```

### Accessing

The Flagsmith Class can be accessed through Laravel's Container. The returned class is simply [flagsmith-php-client](https://github.com/Flagsmith/flagsmith-php-client)

```php
$flagsmith = App::make(Flagsmith::class);
```
