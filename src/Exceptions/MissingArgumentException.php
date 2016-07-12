<?php
namespace Nodes\Push\Exceptions;

use Nodes\Exceptions\Exception as NodesException;

/**
 * Class MissingArgumentException
 *
 * @package Nodes\Push\Exceptions
 */
class MissingArgumentException extends NodesException
{
    /**
     * InvalidArgumentException constructor
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