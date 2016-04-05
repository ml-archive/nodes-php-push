<?php
namespace Nodes\Push\Providers;

use Exception;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\MessageBag;
use Nodes\Push\Contracts\ProviderInterface as NodesPushProviderContract;
use Nodes\Push\Exceptions\ApplicationNotFoundException;
use Nodes\Push\Exceptions\SendPushFailedException;

/**
 * Class UrbanAirship
 *
 * @package Nodes\Push\Providers
 */
class UrbanAirship implements NodesPushProviderContract
{
    /**
     * UrbanAirship API version
     *
     * @var integer
     */
    protected $version = 3;

    /**
     * UrbanAirship API URL
     *
     * @var string
     */
    protected $url = 'https://go.urbanairship.com';

    /**
     * Send and receive content type
     *
     * @var string
     */
    protected $format = 'json';

    /**
     * Urban Airship app groups
     *
     * @var array
     */
    protected $appGroups = [];

    /**
     * Use Urban Airship app
     *
     * @var string|null
     */
    protected $useApp = null;

    /**
     * Array of apps from an "app group"
     * that we should only send to
     *
     * @var array
     */
    protected $onlySendToApps = [];

    /**
     * Guzzle HTTP Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Set channels
     *
     * @var array
     */
    protected $channels = [];

    /**
     * Set aliases
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Set targeted device types
     *
     * @var array
     */
    protected $deviceTypes = [];

    /**
     * Push message
     *
     * @var string
     */
    protected $message;

    /**
     * Extra data
     *
     * @var array
     */
    protected $extra = [];

    /**
     * Title of push notification
     *
     * @var string
     */
    protected $title = null;

    /**
     * Badge count
     *
     * @var integer|null
     */
    protected $badgeCount = null;

    /**
     * Sound of push notification
     *
     * @var string|null
     */
    protected $sound = null;

    /**
     * Send push as silent
     *
     * @var boolean
     */
    protected $silent = false;

    /**
     * Add content available option (iOS only)
     *
     * @var boolean
     */
    protected $contentAvailable = false;

    /**
     * Expiry time
     *
     * @var integer|null
     */
    protected $expiry = null;

    /**
     * UrbanAirship constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array  $appGroups
     * @param  string $defaultApp
     * @throws \Nodes\Push\Exceptions\ApplicationNotFoundException
     */
    public function __construct(array $appGroups = [], $defaultApp = null)
    {
        // Set Urban Airship app gorups
        $this->appGroups = $appGroups;

        // Set default Urban Airship app
        if (!array_key_exists($defaultApp, $this->appGroups)) {
            throw new ApplicationNotFoundException('Default application not found in list of registered Urban Airship app groups');
        }

        // Set default used app
        $this->useApp = $defaultApp;
    }

    /**
     * Set push channels
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $channels
     * @return $this
     */
    public function setChannels(array $channels)
    {
        $this->channels = $channels;
        return $this;
    }

    /**
     * Add additional channels to existing array of channels
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $channels
     * @return $this
     */
    public function addChannels(array $channels)
    {
        $this->channels = array_merge_recursive($this->channels,  $channels);
        return $this;
    }

    /**
     * Retrieve push channels
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return array
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * Set push aliases
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $aliases
     * @return $this
     */
    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
        return $this;
    }

    /**
     * setAlias
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param string $alias
     * @return $this
     */
    public function setAlias(string $alias)
    {
        $this->aliases = [$alias];
        return $this;
    }

    /**
     * Add push aliases
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $aliases
     * @return $this
     */
    public function addAliases(array $aliases)
    {
        $this->aliases = array_merge_recursive($this->aliases,  $aliases);
        return $this;
    }

    /**
     * addAlias
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param string $alias
     * @return $this
     */
    public function addAlias(string $alias)
    {
        $this->aliases[] = $alias;
        return $this;
    }

    /**
     * Retrieve push aliases
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Set targeted device types
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $deviceTypes
     * @return $this
     */
    public function setDeviceTypes(array $deviceTypes)
    {
        $this->deviceTypes = $deviceTypes;
        return $this;
    }

    /**
     * Add device types to existing array of targeted device types
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $deviceTypes
     * @return $this
     */
    public function addDeviceTypes(array $deviceTypes)
    {
        $this->deviceTypes = array_merge_recursive($this->deviceTypes, $deviceTypes);
        return $this;
    }

    /**
     * Retrieve targeted device types
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return array
     */
    public function getDeviceTypes()
    {
        return $this->deviceTypes;
    }

    /**
     * Check if device is targeted
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $device
     * @return boolean
     */
    public function isDeviceTargeted($device)
    {
        return (empty($this->deviceTypes) || in_array($device, $this->deviceTypes)) ? true : false;
    }

    /**
     * Set text of push message
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Retrieve push message
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set extra data
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $data
     * @return $this
     */
    public function setExtra(array $data)
    {
        $this->extra = $data;
        return $this;
    }

    /**
     * Add additional data to existing array of extra data
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $data
     * @return $this
     */
    public function addExtra(array $data)
    {
        $this->extra = array_merge_recursive($this->extra, $data);
        return $this;
    }

    /**
     * Retrieve extra data
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * Set title of push notification
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Retrieve title of push notification
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set badge count
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  integer
     * @return $this
     */
    public function setBadgeCount($count)
    {
        $this->badgeCount = (int) $count;
        return $this;
    }

    /**
     * Retrieve badge count
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return integer|null
     */
    public function getBadgeCount()
    {
        return $this->badgeCount;
    }

    /**
     * Set sound of push notification
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $sound
     * @return $this
     */
    public function setSound($sound)
    {
        $this->sound = $sound;
        return $this;
    }

    /**
     * Retrieve sound of push notification
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string|null
     */
    public function getSound()
    {
        return $this->sound;
    }

    /**
     * Disable push notification sound
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return $this
     */
    public function disableSound()
    {
        return $this->setSound(null);
    }

    /**
     * Set push message as silent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  boolean $silent
     * @return $this
     */
    public function setSilence($silent)
    {
        $this->silent = (bool) $silent;
        return $this;
    }

    /**
     * Set push message as silent
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return $this
     */
    public function silent()
    {
        return $this->setSilence(true);
    }

    /**
     * Set push message as loud
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return $this
     */
    public function loud()
    {
        return $this->setSilence(false);
    }

    /**
     * Retrieve silence state
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function isSilent()
    {
        return $this->silent;
    }


    /**
     * Set Content-Available state (iOS only)
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  boolean $availability
     * @return $this
     */
    public function setContentAvailable($availability)
    {
        $this->contentAvailable = (bool) $availability;
        return $this;
    }

    /**
     * Retrieve Content-Available state
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function isContentAvailable()
    {
        return (bool) $this->contentAvailable;
    }

    /**
     * Set expiry time of push notification
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  integer $expiry
     * @return $this
     */
    public function setExpiry($expiry)
    {
        $this->expiry = (int) $expiry;
        return $this;
    }

    /**
     * Retrieve expiry time of push notification
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return integer|null
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * Set which application group we should use to send push messages from.
     *
     * Name should correspond to the one the config file where
     * the application's credentials is located.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $appGroupName
     * @return $this
     */
    public function setApplication($appGroupName)
    {
        $this->useApp = $appGroupName;
        return $this;
    }

    /**
     * Alias of setApplication()
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $appGroupName
     * @return $this
     */
    public  function setApplicationGroup($appGroupName)
    {
        return $this->setApplication($appGroupName);
    }

    /**
     * Retrieve application credentials
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return array
     */
    public function getApplication()
    {
        return $this->appGroups[$this->useApp];
    }

    /**
     * Set apps in "app group" that we should only send to.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return array
     */
    public function setOnlySendToApps(array $apps)
    {
        $this->onlySendToApps = $apps;
        return $this;
    }

    /**
     * Retrieve array of apps in "app group"
     * that we should only send to.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return array
     */
    public function getOnlySendToApps()
    {
        return $this->onlySendToApps;
    }

    /**
     * Send push message
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $onlySendToApps
     * @return boolean
     * @throws \Nodes\Push\Exceptions\SendPushFailedException
     */
    public function send(array $onlySendToApps = [])
    {
        // Merge global "Only send to apps" with current method
        $onlySendToApps = array_merge_recursive($this->onlySendToApps, $onlySendToApps);

        // Loop through all apps in selected application group
        // and try and send the push message to each app.
        foreach ($this->getApplication() as $appName => $credentials) {
            // If we have valid "Only send to apps" array,
            // we'll validate each app and only send to those apps
            // present in that array.
            if (!empty($onlySendToApps) && !in_array($appName, $onlySendToApps)) {
                continue;
            }

            try {
                // Send request to Urban Airship
                $response = $this->getHttpClient()->post('/api/push', [
                    'body' => json_encode($this->buildPushData()),
                    'auth' => [$credentials['app_key'], $credentials['master_secret']]
                ]);

                // Validate response by looking at the received status code
                if (!in_array($response->getStatusCode(), [200, 201, 202])) {
                    throw new SendPushFailedException(sprintf('[%s] Could not send push message. Status code received: %d %s', $appName, $response->getStatusCode(), $response->getReasonPhrase()));
                }

                // Decode response
                $content = json_decode((string) $response->getBody());

                // Handle potential errors
                if (empty($content->ok)) {
                    throw (new SendPushFailedException(sprintf('[%s] Could not send push message. Reason: %s', $appName, $content->error), $content->error_code))->setErrors(new MessageBag($content->details));
                }
            } catch (Exception $e) {
                throw new SendPushFailedException(sprintf('[%s] Could not send push message. Reason: %s', $appName, $e->getMessage()));
            }
        }

        return true;
    }

    /**
     * Send to group of application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $appGroupName
     * @param  array  $onlySendToApps
     * @return boolean
     * @throws \Nodes\Push\Exceptions\ApplicationNotFoundException
     * @throws \Nodes\Push\Exceptions\SendPushFailedException
     */
    public function sendTo($appGroupName, array $onlySendToApps = [])
    {
        // Validate app that we'll send to
        if (!array_key_exists($appGroupName, $this->appGroups)) {
            throw new ApplicationNotFoundException('Application not found in list of registered Urban Airship app groups');
        }

        // Set application group
        $this->setApplication($appGroupName);

        // Send to application group
        return $this->send($onlySendToApps);
    }

    /**
     * Build push data array
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
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
        if ($this->isDeviceTargeted('ios') && !empty($this->buildIOSData())) {
            $data['notification']['ios'] = $this->buildIOSData();
        }

        // Set Android data
        if ($this->isDeviceTargeted('android') && !empty($this->buildAndroidData())) {
            $data['notification']['android'] = $this->buildAndroidData();
        }

        // Set Windows data
        if ($this->isDeviceTargeted('wns') && !empty($this->buildWnsData())) {
            $data['notification']['wns'] = $this->buildWnsData();
        }

        // Set device types
        $data['device_types'] = !empty($this->getDeviceTypes()) ? $this->getDeviceTypes() : 'all';

        return $data;
    }

    /**
     * Build audience data array
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
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
        // Add target aliases
        if (!empty($this->getAliases())) {
            foreach ($this->getAliases() as $alias) {
                $audience['alias'][] = (string) $alias;
            }
        }

        return $audience;
    }

    /**
     * Build iOS data array
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return array
     */
    protected function buildIOSData()
    {
        // Data container
        $ios = [];

        // Set title of push notification
        if (!empty($this->getTitle())) {
            $ios['title'] = $this->getTitle();
        }

        // Set extra data for push notification
        if (!empty($this->getExtra())) {
            $ios['extra'] = $this->getExtra();
        }

        // Set badge count for push notification
        if (!is_null($this->getBadgeCount())) {
            $ios['badge'] = $this->getBadgeCount();
        }

        // Set sound of push notification
        if (!is_null($this->getSound())) {
            $ios['sound'] = $this->getSound();
        }

        // Set Content-Available for push notification
        if ($this->isContentAvailable()) {
            $ios['content-available'] = $this->isContentAvailable();
        }

        // Set expiry time of push notifications
        if (!is_null($this->getExpiry())) {
            $ios['expiry'] = $this->getExpiry();
        }

        return $ios;
    }

    /**
     * Build Android data array
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return array
     */
    protected function buildAndroidData()
    {
        // Data container
        $android = [];

        // Set title of push notification (Android only)
        if (!empty($this->getTitle())) {
            $android['title'] = $this->getTitle();
        }

        // Set extra data of push notification
        if (!empty($this->getExtra())) {
            $android['extra'] = $this->getExtra();
        }

        // Set badge count of push notification
        if (!is_null($this->getBadgeCount())) {
            $android['extra']['badge'] = $this->getBadgeCount();
        }

        // Set sound of push notification
        if (!is_null($this->getSound())) {
            $android['extra']['sound'] = $this->getSound();
        }

        // Set expiry time of push notifications
        if (!is_null($this->getExpiry())) {
            $android['time-to-live'] = $this->getExpiry();
        }

        return $android;
    }

    /**
     * buildWnsData
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @return array
     */
    protected function buildWnsData()
    {
        // Data container
        $wns = [];

        // Set extra data of push notification
        if (!empty($this->getExtra())) {
            $wns['extra'] = $this->getExtra();
        }

        return $wns;
    }

    /**
     * Retrieve HTTP client
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        if (!is_null($this->httpClient)) {
            return $this->httpClient;
        }

        return $this->httpClient = new HttpClient([
            'base_uri' => $this->url,
            'headers' => [
                'Accept' => sprintf('application/vnd.urbanairship+%s; version=%d;', $this->format, $this->version),
                'Content-Type' => sprintf('application/%s', $this->format),
            ],
            'timeout' => 30
        ]);
    }
}