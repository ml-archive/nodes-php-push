<?php
use Nodes\Push\Contracts\ProviderInterface;

if (!function_exists('push')) {
    /**
     * push
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    function push() : ProviderInterface
    {
        return app('nodes.push');
    }
}