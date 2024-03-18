<?php

namespace Clearlyip\LaravelFlagsmith\Models;

use Flagsmith\Flagsmith;
use Illuminate\Support\Traits\ForwardsCalls;

class Flag
{
    use ForwardsCalls;

    public function __construct(protected Flagsmith $flagsmith)
    {
    }

    public function __call(mixed $name, mixed $arguments)
    {
        return $this->forwardCallTo($this->flagsmith, $name, $arguments);
    }
}
