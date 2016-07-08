<?php

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

    public function testSetBadgeSuccess() {
        $urbanAirshipV3 = $this->getProvider();
        $urbanAirshipV3->setMessage('message');

        $this->assertSame('message', $urbanAirshipV3->getMessage());
    }

    public function testSetMessageError() {
        $urbanAirshipV3 = $this->getProvider();
        $this->expectException(\Throwable::class);
        $urbanAirshipV3->setMessage(['channel']);
    }

    public function testSetMessageSuccess() {
        $urbanAirshipV3 = $this->getProvider();
        $urbanAirshipV3->setMessage('message');

        $this->assertSame('message', $urbanAirshipV3->getMessage());
    }

    private function getProvider()
    {
        return new UrbanAirshipV3([
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
