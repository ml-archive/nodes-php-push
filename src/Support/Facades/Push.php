<?php
namespace Nodes\Push\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Push
 *
 * @package Nodes\Support\Facades
 */
class Push extends Facade
{
    /**
     * Get the registered name of the component
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @access protected
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'nodes.push';
    }
}