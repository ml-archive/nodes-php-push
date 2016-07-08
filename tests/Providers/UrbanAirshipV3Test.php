<?php

use Nodes\Push\Exceptions\InvalidArgumentException;
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

    public function testSetBadgeError()
    {
        $urbanAirshipV3 = $this->getProvider();
        $this->expectException(InvalidArgumentException::class);
        $urbanAirshipV3->setBadge(-12);

        $this->expectException(InvalidArgumentException::class);
        $urbanAirshipV3->setBadge('no supported');
    }

    /**
     * @dataProvider setBadgeSuccessProviderSuccess
     */
    public function testSetBadgeSuccess($a, $b, $expect)
    {
        $urbanAirshipV3 = $this->getProvider();
        $urbanAirshipV3->setBadge($a);
        $this->assertSame($b, $urbanAirshipV3->getBadge());
    }

    public function setBadgeSuccessProviderSuccess()
    {
        return [
            [1, 1, true],
            ['1', 1, true],
            ['+1', '+1', true],
            ['-12', '-12', true],
            ['+5', '+5', true],
            [50, 50, true],
            ['auto', 'auto', true],
        ];
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
