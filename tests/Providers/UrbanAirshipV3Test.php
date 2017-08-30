<?php

namespace Nodes\Push\Tests\Providers;

use Carbon\Carbon;
use Nodes\Push\Constants\AndroidSettings;
use Nodes\Push\Exceptions\InvalidArgumentException;
use Nodes\Push\Exceptions\MissingArgumentException;
use Nodes\Push\Exceptions\PushSizeLimitException;
use Nodes\Push\Exceptions\SendPushFailedException;
use Nodes\Push\ServiceProvider;
use Nodes\Push\Tests\TestCase;

class UrbanAirshipV3Test extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function testAndroidDeliveryPriority()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();

        $urbanAirshipV3->setAndroidDeliveryPriorityHigh()->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);

        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'android' => [
                    'delivery_priority' => 'high',
                    'visibility'        => 1,
                ],
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testAndroidStyle()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider()->removeSound();

        $urbanAirshipV3->setAndroidStyle(AndroidSettings::STYLE_BIG_PICTURE, 'test', 'test1', 'test2');
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);

        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'android' => [
                    'visibility'        => 1,
                    'style'             => [
                        'type'        => 'big_picture',
                        'big_picture' => 'test',
                        'title'       => 'test1',
                        'summary'     => 'test2',
                    ],
                ],
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testGetRequestDataIOSBadge()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();

        $urbanAirshipV3->setIOSBadge('+1')->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'ios'     => [
                    'badge' => '+1',
                ],
                'android' => [
                    'visibility' => 1,
                ],
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testGetRequestDataAndroidExtra()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();

        $extra = [
            uniqid() => uniqid(),
            uniqid() => uniqid(),
        ];

        $urbanAirshipV3->setAndroidData($extra)->removeSound();
        $androidData = $urbanAirshipV3->getAndroidData();
        $this->assertSame($extra, $androidData);
    }

    public function testGetRequestDataExtra()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();

        $extra = [
            uniqid() => uniqid(),
            uniqid() => uniqid(),
        ];

        $urbanAirshipV3->setExtra($extra)->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'ios'     => [
                    'extra' => $extra,
                ],
                'android' => [
                    'extra'      => $extra,
                    'visibility' => 1,
                ]
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testGetRequestDataSound()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();

        $sound = uniqid();
        $urbanAirshipV3->setSound($sound);
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'ios'     => [
                    'sound' => $sound,
                ],
                'android' => [
                    'extra'      => [
                        'sound' => $sound,
                    ],
                    'visibility' => 1,
                ],
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testGetRequestDataContentAvailable()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();

        $urbanAirshipV3->setIosContentAvailable(true)->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'ios'     => [
                    'content-available' => true,
                ],
                'android' => [
                    'visibility' => 1,
                ],
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testGetRequestDataContentAvailableRemovesSoundAndBadge()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();

        $urbanAirshipV3->setIosContentAvailable(true)->setSound('sound')->setIOSBadge('1');
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'ios'     => [
                    'content-available' => true,
                ],
                'android' => [
                    'extra'      => [
                        'sound' => 'sound',
                    ],
                    'visibility' => 1,
                ],
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testGetRequestDataChannel()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $channel = uniqid();
        $urbanAirshipV3->setChannel($channel)->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);
        $this->assertSame([
            'audience'     => [
                'tag' => [
                    $channel,
                ],
            ],
            'notification' => [
                'android' => [
                    'visibility' => 1,
                ],
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testGetRequestDataAlias()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $alias = uniqid();
        $urbanAirshipV3->setAlias($alias)->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);
        $this->assertSame([
            'audience'     => [
                'alias' => [
                    $alias,
                ],
            ],
            'notification' => [
                'android' => [
                    'visibility' => 1,
                ],
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testGetRequestDataNamedUser()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $namedUser = uniqid();
        $urbanAirshipV3->setNamedUser($namedUser)->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);
        $this->assertSame([
            'audience'     => [
                'named_user' => [
                    $namedUser,
                ],
            ],
            'notification' => [
                'android' => [
                    'visibility' => 1,
                ],
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testGetRequestDataNamedUsers()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $namedUserA = uniqid();
        $namedUserB = uniqid();
        $urbanAirshipV3->setNamedUsers([$namedUserA, $namedUserB])->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);
        $this->assertSame([
            'audience'     => [
                'named_user' => [
                    $namedUserA,
                    $namedUserB,
                ],
            ],
            'notification' => [
                'android' => [
                    'visibility' => 1,
                ],
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testGetRequestDataMessage()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $message = uniqid();
        $urbanAirshipV3->setMessage($message)->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'ios'     => [
                    'alert' => strval($message),
                ],
                'android' => [
                    'alert'      => strval($message),
                    'visibility' => 1,
                ],
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testGetRequestDataEmpty()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider()->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        unset($requestData['notification']['wns']);
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'android' => [
                    'visibility' => 1,
                ],
            ],
            'device_types' => $urbanAirshipV3->getPlatforms(),
        ], $requestData);
    }

    public function testSendNoMessage()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $this->expectException(MissingArgumentException::class);
        $urbanAirshipV3->send();
    }

    public function testTooLongMessage()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $message = 'nodes/push php package - unittest - ' . __METHOD__;

        for ($i = 0; $i < 1000; $i++) {
            $message .= uniqid();
        }

        $urbanAirshipV3->setMessage($message);
        $result = $urbanAirshipV3->send();

        $this->assertTrue(!empty($result[0]['ok']) && $result[0]['ok']);
    }

    public function testAndroidDataSend()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $urbanAirshipV3->setMessage('nodes/push php package - unittest - ' . __METHOD__);
        $urbanAirshipV3->setExtra([
            'type' => 'created',
        ]);
        $urbanAirshipV3->setAndroidData([
            'id'         => 1,
            'name'       => 'test',
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
        $result = $urbanAirshipV3->send();
        $this->assertTrue(!empty($result[0]['ok']) && $result[0]['ok']);
    }

    public function testSend()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $urbanAirshipV3->setMessage('nodes/push php package - unittest - ' . __METHOD__);
        $result = $urbanAirshipV3->send();
        $this->assertTrue(!empty($result[0]['ok']) && $result[0]['ok']);
    }

    public function testSendProxy()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3WithProxyProvider();
        $urbanAirshipV3->setMessage('nodes/push php package - unittest - ' . __METHOD__);
        $this->expectException(SendPushFailedException::class);
        $urbanAirshipV3->send();
    }

    public function testSetExtraError1()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $this->expectException(InvalidArgumentException::class);
        $urbanAirshipV3->setExtra(['from' => 'test']);
    }

    public function testSetExtraError2()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();

        $this->expectException(InvalidArgumentException::class);
        $urbanAirshipV3->setIOSBadge('no supported');
    }

    public function testSetExtraSuccess()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $urbanAirshipV3->setExtra([0 => 'test']);

        $this->assertTrue(true);
    }

    public function testSetBadgeError1()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $this->expectException(InvalidArgumentException::class);
        $urbanAirshipV3->setIOSBadge(-12);
    }

    public function testSetBadgeError2()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();

        $this->expectException(InvalidArgumentException::class);
        $urbanAirshipV3->setIOSBadge('no supported');
    }

    public function testValidateSuccess()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $urbanAirshipV3->setMessage('nodes/push php package - unittest - ' . __METHOD__);

        $urbanAirshipV3->validateBeforePush();

        $this->assertTrue(true); // Making it here without errors is good
    }

    public function testValidateError()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $this->expectException(MissingArgumentException::class);
        $urbanAirshipV3->validateBeforePush();
    }

    public function testValidateError2()
    {
        $hugeString = '';
        for($i = 0; $i < 10000; $i++) {
            $hugeString .=uniqid();
        }

        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $urbanAirshipV3->setMessage('nodes/push php package - unittest - ' . __METHOD__);
        $urbanAirshipV3->setExtra([
            'test' => $hugeString
        ]);
        $this->expectException(PushSizeLimitException::class);
        $urbanAirshipV3->validateBeforePush();
    }

    public function testSetPlatformsExtrasSuccess()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $urbanAirshipV3->setPlatforms(['wns']);
        $urbanAirshipV3->setExtra([
            'test' => 'test'
        ]);

        $requestData = $urbanAirshipV3->getRequestData();

        $this->assertTrue(!empty($requestData['notification']['wns']));
        $this->assertTrue(empty($requestData['notification']['ios']));
        $this->assertTrue(empty($requestData['notification']['android']));

//        $platforms = $abstractProvider->getPlatforms();
//        $this->assertSame('plat1', $platforms[0]);
//        $this->assertSame('plat2', $platforms[1]);
//        $this->assertSame('plat3', $platforms[2]);
    }

    /**
     * @dataProvider setIOSBadgeSuccessProviderSuccess
     */
    public function testIOSSetBadgeSuccess($a, $b, $expect)
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $urbanAirshipV3->setIOSBadge($a);
        $this->assertSame($b, $urbanAirshipV3->getIOSBadge());
    }

    public function setIOSBadgeSuccessProviderSuccess()
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
}
