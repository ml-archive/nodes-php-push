<?php
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
     * setAppGroup
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $app
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setAppGroup(string $app) : ProviderInterface;

    /**
     * getAppGroup
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return string
     */
    public function getAppGroup() : string;

    /**
     * setChannels for segmented push, this will override
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
     * @access pu
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
     * setBadge
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param mixed $badge
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setBadge($badge) : ProviderInterface;

    /**
     * getBadge
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return null|int|string
     */
    public function getBadge();
//
//    ///////////////////////////////////////

//
//    /**
//     * setBadge
//     *
//     * @author Casper Rasmussen <cr@nodes.dk>
//     * @access public
//     * @param $badge
//     * @return $this
//     */
//    public function setBadge($badge);
//
//    /**
//     * Set sound of push notification
//     *
//     * @author Morten Rugaard <moru@nodes.dk>
//     * @access public
//     * @param  string $sound
//     * @return $this
//     */
//    public function setSound($sound);
//
//    /**
//     * Set push message as silent
//     *
//     * @author Morten Rugaard <moru@nodes.dk>
//     * @access public
//     * @param  boolean $silent
//     * @return $this
//     */
//    public function setSilence($silent);
//
//    /**
//     * Set Content-Available state (iOS only)
//     *
//     * @author Morten Rugaard <moru@nodes.dk>
//     * @access public
//     * @param  boolean $availability
//     * @return $this
//     */
//    public function setContentAvailable($availability);
//
//    /**
//     * Send push message
//     *
//     * @author Morten Rugaard <moru@nodes.dk>
//     * @access public
//     * @return boolean
//     */
//    public function send();
//
//    /**
//     * enqueue
//     *
//     * @author Casper Rasmussen <cr@nodes.dk>
//     * @access public
//     * @return bool
//     * @throws TODO
//     */
//    public function sendAsync();
}