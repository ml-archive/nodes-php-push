<?php
namespace Nodes\Push;

use Illuminate\Support\Facades\Queue;
use Nodes\Exceptions\Exception as NodesException;
use Nodes\Push\Contracts\ProviderInterface as NodesPushProviderContract;
use Nodes\Push\Jobs\QueuePushNotification;

/**
 * Class Manager
 *
 * @package Nodes\Push
 */
class Manager
{
    /**
     * Push provider
     *
     * @var \Nodes\Push\Contracts\ProviderInterface
     */
    protected $provider;

    /**
     * Name of queue to add push notifications
     *
     * @var string|null
     */
    protected $queueName = null;

    /**
     * Manager constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param \Nodes\Push\Contracts\ProviderInterface $provider
     */
    public function __construct(NodesPushProviderContract $provider)
    {
        $this->provider = $provider;
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
        $this->provider->setChannels($channels);
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
        $this->provider->addChannels($channels);
        return $this;
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
        $this->provider->setMessage($message);
        return $this;
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
        $this->provider->setExtra($data);
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
        $this->provider->addExtra($data);
        return $this;
    }

    /**
     * Set title of push message (Android only)
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->provider->setTitle($title);
        return $this;
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
        $this->provider->setBadgeCount($count);
        return $this;
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
        $this->provider->setSound($sound);
        return $this;
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
        $this->provider->setSilence($silent);
        return $this;
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
        $this->provider->setContentAvailable($availability);
        return $this;
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
        $this->provider->setApp($name);
        return $this;
    }

    /**
     * Send push message
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function send()
    {
        return $this->provider->send();
    }

    /**
     * Queue push notification
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return mixed
     */
    public function queue()
    {
        // Add push notification to queue
        return (bool) Queue::pushOn($this->getQueueName(), new QueuePushNotification($this->getProvider()));
    }

    /**
     * Set queue name
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $queueName
     * @return $this
     */
    public function setQueueName($queueName)
    {
        $this->queueName = $queueName;
        return $this;
    }

    /**
     * Retrieve queue name
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getQueueName()
    {
        return !empty($this->queueName) ? $this->queueName : config(sprintf('queue.connections.%s.queue', config('queue.default', 'sync')), null);
    }

    /**
     * Retrieve provider instance
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * __call
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $name
     * @param  array  $arguments
     * @return mixed
     * @throws \Nodes\Exceptions\Exception
     */
    public function __call($name, $arguments = [])
    {
        if (!method_exists($this->provider, $name)) {
            throw new NodesException(sprintf('Undefined method [%s] on push provider', $name), 500);
        }

        return call_user_func_array([$this->provider, $name], $arguments);
    }
}