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