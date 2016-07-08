<?php

use Nodes\Push\ServiceProvider;

class UrbanAirshipV3Test extends Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }


}
