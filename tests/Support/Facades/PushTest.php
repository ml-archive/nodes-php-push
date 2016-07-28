<?php

namespace Nodes\Push\Tests\Facades\Helpers;

use Nodes\Push\Contracts\ProviderInterface;
use Nodes\Push\ServiceProvider;
use Nodes\Push\Support\Facades\Push;
use Nodes\Push\Tests\TestCase;

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
        $this->bindProviderToServiceContainer();

        $this->assertInstanceOf(ProviderInterface::class, Push::getInstance());
    }
}
