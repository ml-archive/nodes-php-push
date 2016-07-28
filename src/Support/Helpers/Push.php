<?php

use Nodes\Push\Contracts\ProviderInterface;

if (! function_exists('push')) {
    /**
     * push.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    function push() : ProviderInterface
    {
        return app('nodes.push');
    }
}
