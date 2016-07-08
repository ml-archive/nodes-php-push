<?php
use Nodes\Push\Contracts\ProviderInterface;

if (!function_exists('push_send')) {
    /**
     * push_send
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param               $message
     * @param \Closure|null $callback
     * @return bool
     */
    function push_send($message, \Closure $callback = null)
    {
        // Retrieve push manager
        /** @var ProviderInterface $pushManager */
        $pushManager = app('nodes.push');

        // Set channels and message of push notification
        $pushManager->setMessage($message);

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
     * push_queue
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param               $message
     * @param \Closure|null $callback
     * @return mixed
     */
    function push_queue($message, \Closure $callback = null)
    {
        // Retrieve push manager
        /** @var ProviderInterface $pushManager */
        $pushManager = app('nodes.push');

        // Set channels and message of push notification
        $pushManager->setMessage($message);

        // If we have a valid callback, we'll execute
        // that given callback with the push manager as argument
        if ($callback instanceof \Closure) {
            call_user_func($callback, $pushManager);
        }

        // Queue push notification
        return $pushManager->queue();
    }
}