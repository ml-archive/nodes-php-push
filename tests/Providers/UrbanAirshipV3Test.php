<?php

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response;
use Nodes\Push\Exceptions\InvalidArgumentException;
use Nodes\Push\Exceptions\MissingArgumentException;
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

    public function testGetRequestDataIOSBadge()
    {
        $urbanAirshipV3 = $this->getProvider();

        $urbanAirshipV3->setIOSBadge('+1');
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
        $urbanAirshipV3 = $this->getProvider();

        $extra = [
            uniqid() => uniqid(),
            uniqid() => uniqid(),
        ];

        $urbanAirshipV3->setExtra($extra);
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
        $urbanAirshipV3 = $this->getProvider();

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
        $urbanAirshipV3 = $this->getProvider();

        $urbanAirshipV3->setIosContentAvailable(true);
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
        $urbanAirshipV3 = $this->getProvider();
        $channel = uniqid();
        $urbanAirshipV3->setChannel($channel);
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
        $urbanAirshipV3 = $this->getProvider();
        $alias = uniqid();
        $urbanAirshipV3->setAlias($alias);
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
        $urbanAirshipV3 = $this->getProvider();
        $message = uniqid();
        $urbanAirshipV3->setMessage($message);
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
        $urbanAirshipV3 = $this->getProvider();
        $requestData = $urbanAirshipV3->getRequestData();
        $this->assertSame([
            'audience'     => 'all',
            'notification' => [
                'alert' => null,
            ],
            'device_types' => 'all',
        ], $requestData);
    }

    public function testSendAsync()
    {
        $urbanAirshipV3 = $this->getProvider();
        $urbanAirshipV3->setMessage('nodes/push php package - unittest - testSendAsync');
        $promises = $urbanAirshipV3->sendAsync();
        /** @var Promise $promise */
        $promise = $promises[0];
        $promise->then(function(Response $response) {
            $result = json_decode($response->getBody()->getContents(), true);
            $this->assertTrue(!empty($result[0]['ok']) && $result[0]['ok']);
        }, function(RequestException $requestException) {
            $this->assertTrue(false);
        });
        $promise->wait();

        $this->assertTrue(true);
    }

    public function testSendAsyncNoMessage()
    {
        $urbanAirshipV3 = $this->getProvider();
        $this->expectException(MissingArgumentException::class);
        $urbanAirshipV3->sendAsync();
    }

    public function testSendNoMessage()
    {
        $urbanAirshipV3 = $this->getProvider();
        $this->expectException(MissingArgumentException::class);
        $urbanAirshipV3->send();
    }

    public function testTooLongMessage()
    {
        $urbanAirshipV3 = $this->getProvider();
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
        $urbanAirshipV3 = $this->getProvider();
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
        $urbanAirshipV3 = $this->getProvider();
        $urbanAirshipV3->setMessage('nodes/push php package - unittest - ' . __METHOD__);
        $result = $urbanAirshipV3->send();
        $this->assertTrue(!empty($result[0]['ok']) && $result[0]['ok']);
    }

    public function testSetExtraError()
    {
        $urbanAirshipV3 = $this->getProvider();
        $this->expectException(InvalidArgumentException::class);
        $urbanAirshipV3->setExtra(['from' => 'test']);

        $this->expectException(InvalidArgumentException::class);
        $urbanAirshipV3->setIOSBadge('no supported');
    }

    public function testSetBadgeError()
    {
        $urbanAirshipV3 = $this->getProvider();
        $this->expectException(InvalidArgumentException::class);
        $urbanAirshipV3->setIOSBadge(-12);

        $this->expectException(InvalidArgumentException::class);
        $urbanAirshipV3->setIOSBadge('no supported');
    }

    /**
     * @dataProvider setIOSBadgeSuccessProviderSuccess
     */
    public function testIOSSetBadgeSuccess($a, $b, $expect)
    {
        $urbanAirshipV3 = $this->getProvider();
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

    private function getProvider()
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
}
