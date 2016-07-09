<?php
declare (strict_types = 1);

namespace Nodes\Push\Providers;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\MessageBag;
use Nodes\Push\Contracts\ProviderInterface;
use Nodes\Push\Exceptions\InvalidArgumentException;
use Nodes\Push\Exceptions\MissingArgumentException;
use Nodes\Push\Exceptions\SendPushFailedException;

/**
 * Class UrbanAirship
 *
 * @package Nodes\Push\Providers
 */
class UrbanAirshipV3 extends AbstractProvider
{
    /**
     * Guzzle HTTP Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * setBadge
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param int|string $badge
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setBadge($badge) : ProviderInterface
    {
        // Convert to int, if badge does not start with +/-, since int means setting the value
        if (is_numeric($badge) && !starts_with($badge, '-') && !starts_with($badge, '+')) {
            $badge = intval($badge);
        }

        if (is_int($badge) && $badge < 0) {
            throw new InvalidArgumentException('Bagde was set to minus integer, either set 0 or as string fx "-5');
        }

        if (!is_int($badge) && $badge != 'auto' && !is_numeric($badge)) {
            throw new InvalidArgumentException('The passed badge is not supported');
        }

        $this->badge = $badge;

        return $this;
    }

    /**
     * setExtra
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $extra
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setExtra(array $extra) : ProviderInterface
    {
        $protectedUAKeys = [
            'from',
            'collapse_key',
        ];

        foreach ($extra as $key => $value) {
            if (in_array($key, $protectedUAKeys)) {
                throw new InvalidArgumentException(sprintf('The used key [%s] in extra is protected by UA', $key));
            }
        }

        return parent::setExtra($extra);
    }

    /**
     * send
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     * @throws \Nodes\Push\Exceptions\MissingArgumentException
     * @throws \Nodes\Push\Exceptions\SendPushFailedException
     */
    public function send() : array
    {
        $this->validateBeforePush();

        $results = [];

        // Loop through all apps in selected application group
        // and try and send the push message to each app.
        foreach ($applications = $this->appGroups[$this->appGroup] as $appName => $credentials) {

            // Skip empty credentials
            if ($this->emptyCredentials($credentials)) {
                $results[$appName] = 'skipped - empty credentials';
                continue;
            }

            try {
                // Send request to Urban Airship
                $response = $this->getHttpClient()->post('/api/push', [
                    'body' => json_encode($this->buildPushData()),
                    'auth' => [$credentials['app_key'], $credentials['master_secret']],
                ]);

                // Validate response by looking at the received status code
                if (!in_array($response->getStatusCode(), [200, 201, 202])) {
                    throw new SendPushFailedException(sprintf('[%s] Could not send push message. Status code received: %d %s', $appName, $response->getStatusCode(), $response->getReasonPhrase()));
                }

                // Decode response
                $content = json_decode($response->getBody()->getContents(), true);

                // Handle potential errors
                if (empty($content['ok']) || !$content['ok']) {
                    throw (new SendPushFailedException(sprintf('[%s] Could not send push message. Reason: %s', $appName, $content->error), $content->error_code))->setErrors(new MessageBag($content['details']));
                }

                $results[] = $content;
            } catch (ClientException $e) {
                if ($e->hasResponse()) {
                    $content = json_decode($e->getResponse()->getBody()->getContents(), true);
                } else {
                    $content = [];
                }
                throw (new SendPushFailedException(sprintf('[%s] Could not send push message. Reason: %s', $appName, $e->getMessage())))->setErrors(new MessageBag($content['details']));
            } catch (\Throwable $e) {
                throw new SendPushFailedException(sprintf('[%s] Could not send push message. Reason: %s', $appName, $e->getMessage()));
            }
        }

        return $results;
    }

    /**
     * sendAsync
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array GuzzleHttp\Promise\Promise
     * @throws \Nodes\Push\Exceptions\MissingArgumentException
     */
    public function sendAsync() : array
    {
        $this->validateBeforePush();

        $promises = [];

        // Loop through all apps in selected application group
        // and try and send the push message to each app.
        foreach ($applications = $this->appGroups[$this->appGroup] as $appName => $credentials) {

            // Skip empty credentials
            if ($this->emptyCredentials($credentials)) {
                $results[$appName] = 'skipped - empty credentials';
                continue;
            }

            // Send request to Urban Airship
            $promises[] = $this->getHttpClient()->postAsync('/api/push', [
                'body' => json_encode($this->buildPushData()),
                'auth' => [$credentials['app_key'], $credentials['master_secret']],
            ]);
        }

        return $promises;
    }

    /**
     * validateBeforePush
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return void
     * @throws \Nodes\Push\Exceptions\MissingArgumentException
     */
    protected function validateBeforePush()
    {
        if (!$this->getMessage()) {
            throw new MissingArgumentException('You have to setMessage() before sending push');
        }
    }

    /**
     * emptyCredentials
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $credentials
     * @return bool
     */
    protected function emptyCredentials(array $credentials) : bool
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
     * Retrieve HTTP client
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
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
     * Build push data array
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access protected
     * @return array
     */
    protected function buildPushData()
    {
        // Data container
        $data = [];

        // Set audience
        $data['audience'] = !empty($this->buildAudienceData()) ? $this->buildAudienceData() : 'all';

        // Set message
        $data['notification']['alert'] = $this->getMessage();

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
        $data['device_types'] = 'all';

        return $data;
    }

    /**
     * Build audience data array
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access protected
     * @return array
     */
    protected function buildAudienceData()
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
     * Build iOS data array
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access protected
     * @return array
     */
    protected function buildIOSData()
    {
        // Data container
        $ios = [];

        // Set extra data for push notification
        if (!empty($this->getExtra())) {
            $ios['extra'] = $this->getExtra();
        }

        // Set badge count for push notification
        if (!is_null($this->getBadge())) {
            $ios['badge'] = $this->getBadge();
        }

        // Set sound of push notification
        if (!is_null($this->getSound())) {
            $ios['sound'] = $this->getSound();
        }

        // Set Content-Available for push notification
        if ($this->isIosContentAvailable()) {
            $ios['content-available'] = $this->isIosContentAvailable();
        }

        return $ios;
    }

    /**
     * Build Android data array
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access protected
     * @return array
     */
    protected function buildAndroidData()
    {
        // Data container
        $android = [];

        // Set extra data of push notification
        if (!empty($this->getExtra())) {
            $android['extra'] = $this->getExtra();
        }

        // Set badge of push notification
        if (!is_null($this->getBadge())) {
            $android['extra']['badge'] = $this->getBadge();
        }

        // Set sound of push notification
        if (!is_null($this->getSound())) {
            $android['extra']['sound'] = $this->getSound();
        }

        return $android;
    }

    /**
     * buildWnsData
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     */
    protected function buildWnsData()
    {
        // Data container
        $wns = [];

        // Set extra data of push notification
        if (!empty($this->getExtra())) {
            $wns['toast']['binding']['template'] = 'ToastText01';
            $wns['toast']['binding']['text'] = $this->message;
            $wns['toast']['launch'] = json_encode($this->extra);
        }

        return $wns;
    }

    /**
     * getRequestData, for debugging retrieve the request data
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     */
    public function getRequestData() : array
    {
        return $this->buildPushData();
    }
}