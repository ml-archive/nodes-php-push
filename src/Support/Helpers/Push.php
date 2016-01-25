<?php
if (!function_exists('push_send')) {
    /**
     * Sned push notification
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string  $message
     * @param  array   $channels
     * @param  array   $extra
     * @param  string  $sound
     * @param  boolean $silent
     * @param  boolean $contentAvailable
     * @return mixed
     */
    function push_send($message, $channels = [], $extra = [], $sound = 'default', $silent = false, $contentAvailable = false)
    {
        return \NodesPush::setChannels($channels)
                         ->setMessage($message)
                         ->setExtra($extra)
                         ->setSound($sound)
                         ->setSilence($silent)
                         ->setContentAvailable($contentAvailable)
                         ->send();
    }
}

if (!function_exists('push_queue')) {
    /**
     * Sned push notification via queue
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string  $message
     * @param  array   $channels
     * @param  array   $extra
     * @param  string  $sound
     * @param  boolean $silent
     * @param  boolean $contentAvailable
     * @param  string  $queue
     * @return mixed
     */
    function push_queue($message, $channels = [], $extra = [], $sound = null, $silent = false, $contentAvailable = false, $queue = null)
    {
        return \NodesPush::setQueueName($queue)
                         ->setChannels($channels)
                         ->setMessage($message)
                         ->setExtra($extra)
                         ->setSound($sound)
                         ->setSilence($silent)
                         ->setContentAvailable($contentAvailable)
                         ->queue();
    }
}