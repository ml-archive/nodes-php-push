<?php
namespace Nodes\Push\Exceptions;

use Nodes\Exceptions\Exception as NodesException;

/**
 * Class SendPushFailedException
 *
 * @package Nodes\Push\Exceptions
 */
class SendPushFailedException extends NodesException
{
    /**
     * SendPushFailedException constructor
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 500);
    }
}