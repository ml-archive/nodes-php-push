<?php

use Nodes\Push\Exceptions\ApplicationNotFoundException;
use Nodes\Push\Exceptions\ConfigErrorException;
use Nodes\Push\Exceptions\InvalidArgumentException;
use Nodes\Push\Providers\AbstractProvider;
use Nodes\Push\ServiceProvider;

class AbstractProviderTest extends Orchestra\Testbench\TestCase
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
        $urbanAirshipV3->setBadge([]);

        $this->expectException(InvalidArgumentException::class);
        $urbanAirshipV3->setBadge($urbanAirshipV3);
    }

    /**
     * @dataProvider setBadgeSuccessProviderSuccess
     */
    public function testSetBadgeSuccess($a, $b, $expect)
    {
        $urbanAirshipV3 = $this->getProvider();
        $urbanAirshipV3->setBadge($a);
        $this->assertSame($a, $urbanAirshipV3->getBadge());
    }

    public function setBadgeSuccessProviderSuccess()
    {
        return [
            [1, 0, true],
            ['1', 0, true],
            ['auto', 0, true],
        ];
    }

    public function testSetExtraErrorObject()
    {
        $abstractProvider = $this->getProvider();
        $this->expectException(InvalidArgumentException::class);
        $extra = [
            'key' => [
                'key2' => $abstractProvider,
            ],
        ];

        $abstractProvider->setExtra($extra);
    }

    public function testSetExtraErrorArray()
    {
        $abstractProvider = $this->getProvider();
        $this->expectException(InvalidArgumentException::class);
        $extra = [
            'key' => [
                'key2' => 'value',
            ],
        ];

        $abstractProvider->setExtra($extra);
    }

    public function testSetExtra()
    {
        $extra = [
            'key' => uniqid(),
        ];

        $abstractProvider = $this->getProvider();
        $abstractProvider->setExtra($extra);

        $this->assertSame($extra, $abstractProvider->getExtra());
    }

    public function testSetMessageError()
    {
        $abstractProvider = $this->getProvider();
        $this->expectException(\Throwable::class);
        $abstractProvider->setMessage(['channel']);
    }

    public function testSetMessageSuccess()
    {
        $abstractProvider = $this->getProvider();
        $abstractProvider->setMessage('message');

        $this->assertSame('message', $abstractProvider->getMessage());
    }

    public function testSetChannelError()
    {
        $abstractProvider = $this->getProvider();
        $this->expectException(\Throwable::class);
        $abstractProvider->setChannel(['channel']);
    }

    public function testSetChannelSuccess()
    {
        $abstractProvider = $this->getProvider();
        $abstractProvider->setChannel('channel');

        $channels = $abstractProvider->getChannels();
        $this->assertSame('channel', $channels[0]);
    }

    public function testSetChannelsFail()
    {
        $abstractProvider = $this->getProvider();
        $this->expectException(\Throwable::class);
        $abstractProvider->setChannels([$abstractProvider]);
    }

    public function testSetChannelsSuccess()
    {
        $abstractProvider = $this->getProvider();
        $abstractProvider->setChannels(['channel', 1, 1.21]);

        $channels = $abstractProvider->getChannels();
        $this->assertSame('channel', $channels[0]);
        $this->assertSame('1', $channels[1]);
        $this->assertSame('1.21', $channels[2]);
    }

    public function testSetApplicationError()
    {
        $abstractProvider = $this->getProvider();
        $this->expectException(ApplicationNotFoundException::class);
        $abstractProvider->setAppGroup('default-app-group-not-found');
    }

    public function testSetApplicationSuccess()
    {
        $appGroup = 'default-app-group';
        $abstractProvider = $this->getProvider();
        $abstractProvider->setAppGroup($appGroup);
        $this->assertSame($appGroup, $abstractProvider->getAppGroup());
    }

    public function testInitProvideDefaultAppGroupDoesNotExist()
    {
        $this->expectException(ApplicationNotFoundException::class);
        new AbstractProviderTester([
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
        new AbstractProviderTester([
            'default-app-group' => 'default-app-group',
            'app-groups'        => 'string',
        ]);
    }

    public function testInitProvideEmptyAppGroup()
    {
        $this->expectException(ConfigErrorException::class);
        new AbstractProviderTester([
            'default-app-group' => 'default-app-group',
        ]);
    }

    public function testInitProviderSuccess()
    {
        $abstractProvider = $this->getProvider();
        $this->assertInstanceOf(AbstractProviderTester::class, $abstractProvider);
    }

    public function testInitProvideNullDefaultAppGroup()
    {
        $this->expectException(ConfigErrorException::class);
        new AbstractProviderTester([
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
        new AbstractProviderTester([
            'app-groups' => [
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
        new AbstractProviderTester([
            'default-app-group' => [
                'Not a string',
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

    private function getProvider()
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
}
