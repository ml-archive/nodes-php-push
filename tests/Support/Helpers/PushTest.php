<?php

namespace Nodes\Push\Tests\Support\Helpers;

use Nodes\Push\Contracts\ProviderInterface;
use Nodes\Push\ServiceProvider;
use Nodes\Push\Tests\TestCase;

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
        $this->bindProviderToServiceContainer();

        $this->assertInstanceOf(ProviderInterface::class, push());
    }
}
