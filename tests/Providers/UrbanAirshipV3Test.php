<?php

use Nodes\Push\Exceptions\ApplicationNotFoundException;
use Nodes\Push\Exceptions\ConfigErrorException;
use Nodes\Push\Providers\UrbanAirshipV3;
use Nodes\Push\ServiceProvider;

class UrbanAirshipV3Test extends Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function testInitProvideDefaultAppGroupDoesNotExist()
    {
        $this->expectException(ApplicationNotFoundException::class);
        new UrbanAirshipV3([
            'default-app-group' => 'default-app-group-not-found',
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

    public function testInitProvideAppGroupIsNotAnArray()
    {
        $this->expectException(ConfigErrorException::class);
        new UrbanAirshipV3([
            'default-app-group' => 'default-app-group',
            'app-groups'        => 'string'
        ]);
    }

    public function testInitProvideEmptyAppGroup()
    {
        $this->expectException(ConfigErrorException::class);
        new UrbanAirshipV3([
            'default-app-group' => 'default-app-group',
        ]);
    }

    public function testInitProviderSuccess()
    {
        $urbanAirshipV3 = new UrbanAirshipV3([
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

        $this->assertInstanceOf(UrbanAirshipV3::class, $urbanAirshipV3);
    }

    public function testInitProvideNullDefaultAppGroup()
    {
        $this->expectException(ConfigErrorException::class);
        new UrbanAirshipV3([
            'default-app-group' => null,
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

    public function testInitProvideEmptyDefaultAppGroup()
    {
        $this->expectException(ConfigErrorException::class);
        new UrbanAirshipV3([
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

    public function testInitProvideDefaultAppGroupIsNotAString()
    {
        $this->expectException(ConfigErrorException::class);
        new UrbanAirshipV3([
            'default-app-group' => [
                'Not a string'
            ],
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
