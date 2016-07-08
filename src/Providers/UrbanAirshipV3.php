<?php
namespace Nodes\Push\Providers;

use Nodes\Push\Contracts\ProviderInterface;

/**
 * Class UrbanAirship
 *
 * @package Nodes\Push\Providers
 */
class UrbanAirshipV3 implements ProviderInterface
{

    public function __construct(array $config = [])
    {
        // Validate configs
//
//        // Set Urban Airship app gorups
//        $this->appGroups = $appGroups;
//
//        // Set default Urban Airship app
//        if (!array_key_exists($defaultApp, $this->appGroups)) {
//            throw new ApplicationNotFoundException(sprintf('Default app [%s] was not found in list of '))
//            throw new ApplicationNotFoundException('Default application not found in list of registered Urban Airship app groups');
//        }

        // Set default used app
    }

    public function setChannels(array $channels)
    {
        // TODO: Implement setChannels() method.
    }

    public function addChannels(array $channels)
    {
        // TODO: Implement addChannels() method.
    }

    public function setMessage($message)
    {
        // TODO: Implement setMessage() method.
    }

    public function setExtra(array $data)
    {
        // TODO: Implement setExtra() method.
    }

    public function addExtra(array $data)
    {
        // TODO: Implement addExtra() method.
    }

    public function setTitle($title)
    {
        // TODO: Implement setTitle() method.
    }

    public function setBadgeCount($count)
    {
        // TODO: Implement setBadgeCount() method.
    }

    public function setSound($sound)
    {
        // TODO: Implement setSound() method.
    }

    public function setSilence($silent)
    {
        // TODO: Implement setSilence() method.
    }

    public function setContentAvailable($availability)
    {
        // TODO: Implement setContentAvailable() method.
    }

    public function setApplication($app)
    {
        // TODO: Implement setApplication() method.
    }

    public function send()
    {
        // TODO: Implement send() method.
    }

    public function enqueue()
    {
        // TODO: Implement enqueue() method.
    }
}