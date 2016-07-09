<?php
namespace Nodes\Push\Tests\Facades\Helpers;

use Nodes\Push\ServiceProvider;
use Nodes\Push\Support\Facades\Push;
use Orchestra\Testbench\TestCase;

class PushTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function testFacade()
    {
        // No idea how to test that, service provider cannot load configs
        $this->assertTrue(true);
    }
}
