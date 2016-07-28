<?php

namespace Nodes\Push\Tests;

use Nodes\Push\Providers\AbstractProvider;
use Nodes\Push\Providers\UrbanAirshipV3;
use Nodes\Push\ServiceProvider;
use Nodes\Push\Contracts\ProviderInterface as NodesPushProviderContract;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function bindProviderToServiceContainer()
    {
        app()->singleton('nodes.push', function () {
            return $this->getUrbanAirshipV3Provider();
        });

        app()->bind(NodesPushProviderContract::class, function ($app) {
            return $app['nodes.push'];
        });
    }

    protected function getUrbanAirshipV3Provider()
    {
        return new UrbanAirshipV3([
            'default-app-group' => 'default-app-group',
            'app-groups'        => [
                'default-app-group' => [
                    'app-1' => [
                        'app_key'       => env('URBAN_AIRSHIP_APP_KEY'),
                        'app_secret'    => env('URBAN_AIRSHIP_APP_SECRET'),
                        'master_secret' => env('URBAN_AIRSHIP_MASTER_SECRET'),
                    ],
                ],
            ],
        ]);
    }

    protected function getAbstractProvider()
    {
        return new AbstractProviderTester([
            'default-app-group' => 'default-app-group',
            'app-groups'        => [
                'default-app-group' => [
                    'app-1' => [
                        'app_key'       => 'app-key',
                        'app_secret'    => 'app-secret',
                        'master_secret' => 'master-secret',
                    ],
                ],
            ],
        ]);
    }
}

class AbstractProviderTester extends AbstractProvider
{
    public function send() : array
    {
        throw new \Exception('Feature not supported', 500);
    }

    public function getRequestData() : array
    {
        throw new \Exception('Feature not supported', 500);
    }
}
