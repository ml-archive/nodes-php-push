<?php
declare (strict_types = 1);

namespace Nodes\Push\Contracts;

/**
 * Interface ProviderInterface
 *
 * @interface
 * @package Nodes\Push\Contacts
 */
interface ProviderInterface
{
    /**
     * setAppGroup, pick the app group to send pushes
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $appGroup
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setAppGroup(string $appGroup) : ProviderInterface;

    /**
     * getAppGroup
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return string
     */
    public function getAppGroup() : string;

    /**
     * setChannels for segmented push, this will override current channels
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $channels
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Throwable
     */
    public function setChannels(array $channels) : ProviderInterface;

    /**
     * setChannel for segmented push, this will override
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $channel
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setChannel(string $channel) : ProviderInterface;

    /**
     * getChannels for segmented push
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     */
    public function getChannels() : array;

    /**
     * setAliases, for segmented push
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $aliases
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setAliases(array $aliases) : ProviderInterface;

    /**
     * setAlias, for segmented push
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $alias
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setAlias(string $alias) : ProviderInterface;

    /**
     * getAliases, for segmented push
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     */
    public function getAliases() : array;

    /**
     * setMessage, which will be in notification center/title of the push notification
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $message
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setMessage(string $message) : ProviderInterface;

    /**
     * getMessage
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return string|null
     */
    public function getMessage();

    /**
     * setExtra, the payload of the push
     * Remember there is limits for size of push
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $extra
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setExtra(array $extra) : ProviderInterface;

    /**
     * getExtra
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     */
    public function getExtra() : array;

    /**
     * setIOSBadge
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string|int|null $iOSBadge
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setIOSBadge($iOSBadge) : ProviderInterface;

    /**
     * getIOSBadge
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return null|int|string
     */
    public function getIOSBadge();

    /**
     * setSound
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $sound
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setSound(string $sound) : ProviderInterface;

    /**
     * removeSound
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function removeSound() : ProviderInterface;

    /**
     * getSound
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return string|null
     */
    public function getSound();

    /**
     * setIosContentAvailable, iOS-8 feature to sent push notifications without they go in notification center
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param bool $iosContentAvailable
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setIosContentAvailable(bool $iosContentAvailable) : ProviderInterface;

    /**
     * isIosContentAvailable
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return bool
     */
    public function isIosContentAvailable() : bool;

    /**
     * send
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     * @throws \Nodes\Push\Exceptions\MissingArgumentException
     * @throws \Nodes\Push\Exceptions\SendPushFailedException
     * @throws \Nodes\Push\Exceptions\PushSizeLimitException
     */
    public function send() : array;

    /**
     * getRequestData, for debugging retrieve the request data
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     */
    public function getRequestData() : array;

    /**
     * setAndroidData, since android push messages can handle 4kb where ios is 0.5kb
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $androidData
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setAndroidData(array $androidData) : ProviderInterface;

    /**
     * getAndroidData
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     */
    public function getAndroidData() : array;

    /**
     * getInstance
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function getInstance() : ProviderInterface;
}
