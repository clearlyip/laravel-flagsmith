<?php

namespace Clearlyip\LaravelFlagsmith\Facades;

use Illuminate\Support\Facades\Facade;
use Flagsmith\Flagsmith;

class LaravelFlagsmith extends Facade {

	protected static function getFacadeAccessor() {
        return Flagsmith::class;
    }
}
