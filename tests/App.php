<?php

namespace CIP\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithMigration;

define('TESTBENCH_WORKING_PATH', dirname(__DIR__));

#[WithMigration]
class App extends TestCase
{
    use WithWorkbench;
    use RefreshDatabase;
}
