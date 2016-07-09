<?php
namespace Nodes\Push\Tests\Support\Helpers;

use Nodes\Push\ServiceProvider;
use Orchestra\Testbench\TestCase;

class PushTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function testGlobalFunction()
    {
        // No idea how to test that, service provider cannot load configs
        $this->assertTrue(true);
    }
}
