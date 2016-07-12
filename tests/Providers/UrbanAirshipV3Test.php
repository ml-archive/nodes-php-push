<?php
namespace Nodes\Push\Tests\Providers;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use Nodes\Push\Exceptions\InvalidArgumentException;
use Nodes\Push\Exceptions\MissingArgumentException;
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

    public function testGetRequestDataIOSBadge()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();

        $urbanAirshipV3->setIOSBadge('+1')->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'alert' => null,
                'ios'   => [
                    'badge' => '+1',
                ],
            ],
            'device_types' => 'all',
        ], $requestData);
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
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'alert'   => null,
                'ios'     => [
                    'extra' => $extra,
                ],
                'android' => [
                    'extra' => $extra,
                ],
                'wns'     => [
                    'toast' => [
                        'binding' => [
                            'template' => 'ToastText01',
                            'text'     => null,
                        ],
                        'launch'  => json_encode($extra),
                    ],
                ],
            ],
            'device_types' => 'all',
        ], $requestData);
    }

    public function testGetRequestDataSound()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();

        $sound = uniqid();
        $urbanAirshipV3->setSound($sound);
        $requestData = $urbanAirshipV3->getRequestData();
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'alert'   => null,
                'ios'     => [
                    'sound' => $sound,
                ],
                'android' => [
                    'extra' => [
                        'sound' => $sound,
                    ],
                ],
            ],
            'device_types' => 'all',
        ], $requestData);
    }

    public function testGetRequestDataContentAvailable()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();

        $urbanAirshipV3->setIosContentAvailable(true)->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'alert' => null,
                'ios'   => [
                    'content-available' => true,
                ],
            ],
            'device_types' => 'all',
        ], $requestData);
    }

    public function testGetRequestDataChannel()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $channel = uniqid();
        $urbanAirshipV3->setChannel($channel)->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        $this->assertSame([
            'audience'     => [
                'tag' => [
                    $channel,
                ],
            ],
            'notification' => [
                'alert' => null,
            ],
            'device_types' => 'all',
        ], $requestData);
    }

    public function testGetRequestDataAlias()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $alias = uniqid();
        $urbanAirshipV3->setAlias($alias)->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        $this->assertSame([
            'audience'     => [
                'alias' => [
                    $alias,
                ],
            ],
            'notification' => [
                'alert' => null,
            ],
            'device_types' => 'all',
        ], $requestData);
    }

    public function testGetRequestDataMessage()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $message = uniqid();
        $urbanAirshipV3->setMessage($message)->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'alert' => strval($message),
            ],
            'device_types' => 'all',
        ], $requestData);
    }

    public function testGetRequestDataEmpty()
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider()->removeSound();
        $requestData = $urbanAirshipV3->getRequestData();
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'alert' => null,
            ],
            'device_types' => 'all',
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

    /**
     * @dataProvider setIOSBadgeSuccessProviderSuccess
     */
    public function testIOSSetBadgeSuccess($a, $b, $expect)
    {
        $urbanAirshipV3 = $this->getUrbanAirshipV3Provider();
        $urbanAirshipV3->setIOSBadge($a);
        $this->assertSame($b, $urbanAirshipV3->getIOSBadge());
    }

    /**
     * @codeCoverageIgnore
     */
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


