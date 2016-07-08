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

//
//    ///////////////////////////////////////
//    /**
//     * Set push channels
//     *
//     * @author Morten Rugaard <moru@nodes.dk>
//     * @access public
//     * @param  array $channels
//     * @return $this
//     */
//    public function setChannels(array $channels);
//
//    /**
//     * Add additional channels to existing array of channels
//     *
//     * @author Morten Rugaard <moru@nodes.dk>
//     * @access public
//     * @param  array $channels
//     * @return $this
//     */
//    public function addChannels(array $channels);
//
//    /**
//     * Set text of push message
//     *
//     * @author Morten Rugaard <moru@nodes.dk>
//     * @access public
//     * @param  string $message
//     * @return $this
//     */
//    public function setMessage($message);
//
//    /**
//     * Set extra data
//     *
//     * @author Morten Rugaard <moru@nodes.dk>
//     * @access public
//     * @param  array $data
//     * @return $this
//     */
//    public function setExtra(array $data);
//
//    /**
//     * Add additional data to existing array of extra data
//     *
//     * @author Morten Rugaard <moru@nodes.dk>
//     * @access public
//     * @param  array $data
//     * @return $this
//     */
//    public function addExtra(array $data);
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