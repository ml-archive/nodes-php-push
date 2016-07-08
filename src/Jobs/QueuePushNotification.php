<?php
namespace Nodes\Push\Jobs;

use App\Jobs\Job;
use Illuminate\Contracts\Queue\ShouldQueue;
use Nodes\Push\Contracts\ProviderInterface as NodesPushProviderContract;

/**
 * Class QueuePushNotification
 *
 * @package Nodes\Push\Jobs
 */
class QueuePushNotification extends Job implements ShouldQueue
{
    /**
     * Push provider
     *
     * @var \Nodes\Push\Contracts\ProviderInterface
     */
    protected $provider;

    /**
     * QueuePushNotification constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @param  \Nodes\Push\Contracts\ProviderInterface $provider
     */
    public function __construct(NodesPushProviderContract $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Handle job
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @return void
     */
    public function handle()
    {
        // Send push notification
        $this->provider->send();
    }
}