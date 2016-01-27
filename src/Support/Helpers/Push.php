<?php
if (!function_exists('push_send')) {
    /**
     * Sned push notification
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string   $message
     * @param  array    $channels
     * @param  \Closure $callback
     * @return mixed
     */
    function push_send($message, $channels = [], \Closure $callback = null)
    {
        // Retrieve push manager
        $pushManager = app('nodes.push');

        // Set channels and message of push notification
        $pushManager->setChannels((array) $channels)
                    ->setMessage($message);

        // If we have a valid callback, we'll execute
        // that given callback with the push manager as argument
        if ($callback instanceof \Closure) {
            call_user_func($callback, $pushManager);
        }

        // Send push notification
        return $pushManager->send();
    }
}

if (!function_exists('push_queue')) {
    /**
     * Sned push notification via queue
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string   $message
     * @param  array    $channels
     * @param  \Closure $callback
     * @param  string   $queue
     * @return mixed
     */
    function push_queue($message, $channels = [], \Closure $callback, $queue = null)
    {
        // Retrieve push manager
        $pushManager = app('nodes.push');

        // Set channels and message of push notification
        $pushManager->setChannels((array) $channels)
                    ->setMessage($message);

        // Set name of queue, where we'll add the push notification to
        if (!empty($queue)) {
            $pushManager->setQueueName($queue);
        }

        // If we have a valid callback, we'll execute
        // that given callback with the push manager as argument
        if ($callback instanceof \Closure) {
            call_user_func($callback, $pushManager);
        }

        // Queue push notification
        return $pushManager->queue();
    }
}