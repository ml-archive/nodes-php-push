<?php

declare(strict_types = 1);

namespace Nodes\Push\Providers;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\MessageBag;
use Nodes\Push\Contracts\ProviderInterface;
use Nodes\Push\Exceptions\InvalidArgumentException;
use Nodes\Push\Exceptions\MissingArgumentException;
use Nodes\Push\Exceptions\PushSizeLimitException;
use Nodes\Push\Exceptions\SendPushFailedException;

/**
 * Class UrbanAirship.
 */
class UrbanAirshipV3 extends AbstractProvider
{ 

    const MAX_RETRIES = 1;

    /**
     * Guzzle HTTP Client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @var int
     */
    protected $retries = 0;


    /**
     * setBadge, badge is the small red icon on the app in the launcher.
     * The badge can be controlled by using this function.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param int|string $iOSBadge
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setIOSBadge($iOSBadge) : ProviderInterface
    {
        // Convert to int, if badge does not start with +/-, since int means setting the value
        if (is_numeric($iOSBadge) && !starts_with($iOSBadge, '-') && !starts_with($iOSBadge, '+')) {
            $iOSBadge = intval($iOSBadge);
        }

        if (is_int($iOSBadge) && $iOSBadge < 0) {
            throw new InvalidArgumentException('Bagde was set to minus integer, either set 0 or as string fx "-5');
        }

        if (!is_int($iOSBadge) && $iOSBadge != 'auto' && !is_numeric($iOSBadge)) {
            throw new InvalidArgumentException('The passed badge is not supported');
        }

        $this->iOSBadge = $iOSBadge;

        return $this;
    }

    /**
     * setExtra, extra is a map of key /value which can be passed to mobile
     * There is a hard limit on how big a push notification can be, specially for ios
     * Consider not putting too much in here, and consider using setAndroidData if you want to send more to android.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param array $extra
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setExtra(array $extra) : ProviderInterface
    {
        $this->validateExtra($extra);

        return parent::setExtra($extra);
    }

    /**
     * setAndroidData, since android can handle 4kb while ios only have 0.5kb
     * Note this will override keys in extra, if same keys are passed.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param array $androidData
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setAndroidData(array $androidData) : ProviderInterface
    {
        $this->validateExtra($androidData);

        return parent::setAndroidData($androidData);
    }

    /**
     * validateExtra.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param array $extra
     * @return void
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    protected function validateExtra(array $extra)
    {
        $protectedUAKeys = [
            'from',
            'collapse_key',
            'sound',
        ];

        foreach ($extra as $key => $value) {
            if (in_array(strval($key), $protectedUAKeys)) {
                throw new InvalidArgumentException(sprintf('The used key [%s] in extra is protected by UA', $key));
            }
        }

        parent::validateExtra($extra);
    }

    /**
     * send push notification in request.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return array
     * @throws \Nodes\Push\Exceptions\MissingArgumentException
     * @throws \Nodes\Push\Exceptions\SendPushFailedException
     * @throws \Nodes\Push\Exceptions\PushSizeLimitException
     */
    public function send() : array
    {
        $this->validateBeforePush();

        $results = [];

        // Loop through all apps in selected application group
        // and try and send the push message to each app.
        foreach ($applications = $this->appGroups[$this->appGroup] as $appName => $credentials) {

            // Skip empty credentials
            if ($this->hasEmptyCredentials($credentials)) {
                $results[$appName] = 'skipped - empty credentials';
                continue;
            }

            try {
                $UAData = [
                    'body'        => json_encode($this->buildPushData()),
                    'auth'        => [$credentials['app_key'], $credentials['master_secret']],
                ];

                if (!empty($this->proxy)) {
                    $UAData['proxy'] = $this->proxy;
                }

                // Send request to Urban Airship
                $response = $this->getHttpClient()->post('/api/push', $UAData);

                // Validate response by looking at the received status code
                if (!in_array($response->getStatusCode(), [200, 201, 202])) {
                    throw (new SendPushFailedException(sprintf('[%s] - [%s] Could not send push message. Status code received: %d %s',
                        $this->appGroup, $appName, $response->getStatusCode(), $response->getReasonPhrase())))
                        ->addMeta([
                            'responseCode' => $response->getStatusCode(),
                            'responseContent' => $response->getBody()->getContents()
                        ]);
                }

                // Decode response
                $content = json_decode($response->getBody()->getContents(), true);

                // Handle potential errors
                if (empty($content['ok']) || !$content['ok']) {
                    throw (new SendPushFailedException(sprintf('[%s]- [%s] Could not send push message. Reason: %s', $appName, $this->appGroup,
                        $content->error), $content->error_code))
                        ->addMeta([
                            'requestData' => $this->getRequestData(),
                            'responseCode' => $response->getStatusCode(),
                            'responseContent' => $content
                        ]);
                }

                $results[] = $content;
            } catch (ClientException $e) {
                if(in_array($e->getCode(), ['503', '504']) && $this->retries < self::MAX_RETRIES) {
                    $this->retries++;
                    sleep(1);

                    return $this->send();
                }

                throw (new SendPushFailedException(sprintf('[%s] - [%s] Could not send push message. Reason: %s', $this->appGroup, $appName,
                    $e->getMessage())))
                    ->addMeta([
                        'requestData' => $this->getRequestData(),
                        'responseCode' => $e->getResponse()->getStatusCode(),
                        'responseContent' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
                    ]);
            } catch (\Throwable $e) {
                throw (new SendPushFailedException(sprintf('[%s] - [%] Could not send push message. Reason: %s', $this->appGroup, $appName,
                    $e->getMessage()))
                )->addMeta([
                    'requestData' => $this->getRequestData()
                ]);
            }
        }

        // Reset retries in case we are running in queue --demon mode
        $this->retries = 0;

        return $results;
    }

    /**
     * validateBeforePush.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return void
     * @throws \Nodes\Push\Exceptions\MissingArgumentException
     * @throws \Nodes\Push\Exceptions\PushSizeLimitException
     */
    public function validateBeforePush()
    {
        if (!$this->getMessage()) {
            throw new MissingArgumentException('You have to setMessage() before sending push');
        }

        // Check kb size
        if (mb_strlen(json_encode($this->buildIOSData())) > 2048) {
            throw new PushSizeLimitException(sprintf('Limit of ios is 2048b, %s was send',
                mb_strlen(json_encode($this->buildIOSData()))));
        }

        if (mb_strlen(json_encode($this->buildWnsData())) > 2048) {
            throw new PushSizeLimitException(sprintf('Limit of wns is 2048b, %s was send',
                mb_strlen(json_encode($this->buildWnsData()))));
        }

        if (mb_strlen(json_encode($this->buildAndroidData())) > 4096) {
            throw new PushSizeLimitException(sprintf('Limit of android is 4096b, %s was send',
                mb_strlen(json_encode($this->buildAndroidData()))));
        }
    }

    /**
     * hasEmptyCredentials.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param array $credentials
     * @return bool
     */
    protected function hasEmptyCredentials(array $credentials) : bool
    {
        // If one or more required credentials are missing
        // we'll have to "invalidate" that app and notify about it in our logs
        if (empty($credentials['app_key']) || empty($credentials['app_secret']) ||
            empty($credentials['master_secret'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve HTTP client.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient() : HttpClient
    {
        if (!is_null($this->httpClient)) {
            return $this->httpClient;
        }

        return $this->httpClient = new HttpClient([
            'base_uri' => 'https://go.urbanairship.com',
            'headers'  => [
                'Accept'       => sprintf('application/vnd.urbanairship+json; version=3;'),
                'Content-Type' => sprintf('application/json'),
            ],
            'timeout'  => 30,
        ]);
    }

    /**
     * Build push data array.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return array
     */
    protected function buildPushData() : array
    {
        // Data container
        $data = [];

        // Set audience
        $data['audience'] = !empty($this->buildAudienceData()) ? $this->buildAudienceData() : 'all';

        // Set iOS data
        if (!empty($this->buildIOSData())) {
            $data['notification']['ios'] = $this->buildIOSData();
        }

        // Set Android data
        if (!empty($this->buildAndroidData())) {
            $data['notification']['android'] = $this->buildAndroidData();
        }

        // Set Windows data
        if (!empty($this->buildWnsData())) {
            $data['notification']['wns'] = $this->buildWnsData();
        }

        // Set device types
        $data['device_types'] = $this->getPlatforms();

        return $data;
    }

    /**
     * Build audience data array.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return array
     */
    protected function buildAudienceData() : array
    {
        // Data container
        $audience = [];

        // Add target channnels
        if (!empty($this->getChannels())) {
            $audience['tag'] = $this->getChannels();
        }

        // Add target aliases
        if (!empty($this->getAliases())) {
            foreach ($this->getAliases() as $alias) {
                $audience['alias'][] = $alias;
            }
        }

        return $audience;
    }

    /**
     * Build iOS data array.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return array
     */
    protected function buildIOSData() : array
    {
        // Avoid building a payload for a platform, not in platforms
        if(!in_array('ios', $this->getPlatforms())) {
            return [];
        }

        // Data container
        $ios = [];

        // Set extra data for push notification
        if (!empty($this->getExtra())) {
            $ios['extra'] = $this->getExtra();
        }

        // Set badge count for push notification
        if (!is_null($this->getIOSBadge())) {
            $ios['badge'] = $this->getIOSBadge();
        }

        // Set sound of push notification
        if (!is_null($this->getSound())) {
            $ios['sound'] = $this->getSound();
        }

        // Set Content-Available for push notification
        if ($this->isIosContentAvailable()) {
            $ios['content-available'] = $this->isIosContentAvailable();
            unset($ios['badge']);
            unset($ios['sound']);
        } // Set message
        else if ($this->getMessage()) {
            $ios['alert'] = $this->getMessage();
        }

        return $ios;
    }

    /**
     * Build Android data array.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return array
     */
    protected function buildAndroidData() : array
    {
        // Avoid building a payload for a platform, not in platforms
        if(!in_array('android', $this->getPlatforms())) {
            return [];
        }

        // Data container
        $android = [];

        // Set message
        if ($this->getMessage()) {
            $android['alert'] = $this->getMessage();
        }

        // Set extra data of push notification
        if (!empty($this->getExtra())) {
            $android['extra'] = $this->getExtra();
        }

        // Add android data
        if (!empty($this->androidData)) {
            if (empty($android['extra'])) {
                $android['extra'] = $this->androidData;
            } else {
                $android['extra'] = array_merge($android['extra'], $this->androidData);
            }
        }

        // Set sound of push notification
        if (!is_null($this->getSound())) {
            $android['extra']['sound'] = $this->getSound();
        }

        // Add delivery priority - defaults to normal
        if ($this->getAndroidDeliveryPriority() != 'normal') {
            $android['delivery_priority'] = $this->getAndroidDeliveryPriority();
        }

        // Set the visibility of the push notification
        $android['visibility'] = $this->getAndroidVisibility();

        // Set android style if available
        if (!empty($this->getAndroidStyle())) {
            $android['style'] = $this->getAndroidStyle();
        }

        return $android;
    }

    /**
     * buildWnsData.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return array
     */
    protected function buildWnsData() : array
    {
        // Avoid building a payload for a platform, not in platforms
        if(!in_array('wns', $this->getPlatforms())) {
            return [];
        }
        

        // Init the windows extras
        $windowsExtra = $this->extra;

        // Data container
        $wns = [];

        // Set message
        if ($this->getMessage()) {
            $windowsExtra['wns_alert'] = $this->getMessage();
        }

        // apply sound as a key
        if ($this->sound) {
            $windowsExtra['sound'] = $this->sound;

            #Waiting for UA to implement this
            #$wns['toast']['audio']['sound']= 'src=ms-appdata:///local/' . $this->sound;
            #$wns['toast']['audio']['loop'] = 'false';
        } else {
        }

        // Set extra data of push notification
        $wns['toast']['binding']['template'] = 'ToastText01';
        $wns['toast']['binding']['text'] = $this->message;
        $wns['toast']['launch'] = json_encode($windowsExtra);

        return $wns;
    }

    /**
     * getRequestData, for debugging retrieve the request data.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return array
     */
    public function getRequestData() : array
    {
        return $this->buildPushData();
    }

    /**
     * setMessage.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param string $message
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setMessage(string $message) : ProviderInterface
    {
        // Max strlen is 254
        if (strlen($message) > 254) {
            $message = substr($message, 0, 251) . '...';
        }

        return parent::setMessage($message);
    }
}
