<?php
namespace Nodes\Push\Providers;

use Exception;
use Nodes\Push\Contracts\ProviderInterface as NodesPushProviderContract;
use Nodes\Push\Exceptions\ApplicationNotFoundException;
use Nodes\Push\Exceptions\SendPushFailedException;
use Parse\ParseClient;
use Parse\ParsePush;

/**
 * Class Parse
 *
 * @package Nodes\Push\Providers
 */
class Parse implements NodesPushProviderContract
{
    /**
     * Parse push handler
     *
     * @var \Parse\ParsePush
     */
    protected $parse;

    /**
     * Parse applications
     *
     * @var array
     */
    protected $parseApplications = [];

    /**
     * Is Parse used in a live environment?
     *
     * @var boolean
     */
    protected $live;

    /**
     * Active Parse application
     *
     * @var array
     */
    protected $application;

    /**
     * Push channels
     *
     * @var array
     */
    protected $channels = [];

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
     * Is content available? (iOS only)
     *
     * @var boolean
     */
    protected $contentAvailable = false;

    /**
     * Parse constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Parse\ParsePush $parse
     * @param  array            $applications
     * @param  boolean          $live
     */
    public function __construct(ParsePush $parse, array $applications, $live = true)
    {
        $this->parse = $parse;
        $this->parseApplications = (array) $applications;
        $this->live = (bool) $live;

        // Set default application
        $this->setApplication($live ? 'live' : 'development');
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
        $this->channels = array_merge_recursive($this->channels, $channels);
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
     * @return integer
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
     * @return string
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
     * Set push message silence state
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
     * Set which application we should send the push notification from.
     *
     * Name should correspond to the one the config file where
     * the application's credentials is located.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $name
     * @return $this
     * @throws \Nodes\Push\Exceptions\ApplicationNotFoundException
     */
    public function setApplication($name)
    {
        if (!array_key_exists($name, $this->parseApplications)) {
            throw new ApplicationNotFoundException(sprintf('Parse application [%s] not found.', $name));
        }

        $this->application = $this->parseApplications[$name];
        return $this;
    }

    /**
     * Retrieve active Parse application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return array
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Send push message
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     * @throws \Nodes\Push\Exceptions\SendPushFailedException
     */
    public function send()
    {
        try {
            // Retrieve parse application
            $application = $this->getApplication();

            // Load Parse client with application credentials
            ParseClient::initialize($application['app_id'], $application['rest_key'], $application['master_key']);

            // Send push notification
            $response = $this->parse->send([
                'channels' => !empty($this->getChannels()) ? $this->getChannels() : ['all'],
                'data' => $this->buildPushData()
            ]);
        } catch (Exception $e) {
            throw new SendPushFailedException(sprintf('Could not send pusn notification. Reason: %s', $e->getMessage()));
        }

        return (bool) $response['result'];
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

        // Push notification message
        $data[$this->isSilent() ? 'message' : 'alert'] = $this->getMessage();

        // Add extra data to push notification
        if (!empty($this->getExtra())) {
            $data['extra'] = $this->getExtra();
        }

        // Set badge count of push notification
        if (!is_null($this->getBadgeCount())) {
            $data['badge'] = $this->getBadgeCount();
        }

        // Set sound of push notification
        if (!is_null($this->getSound())) {
            $data['sound'] = $this->getSound();
        }

        // Set Content-Available for push notification (iOS only)
        if ($this->isContentAvailable()) {
            $data['content-available'] = $this->isContentAvailable();
        }

        return $data;
    }
}