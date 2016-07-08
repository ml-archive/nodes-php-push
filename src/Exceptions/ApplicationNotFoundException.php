<?php
namespace Nodes\Push\Exceptions;

use Nodes\Exceptions\Exception as NodesException;

/**
 * Class ApplicationNotFoundException
 *
 * @package Nodes\Push\Exceptions
 */
class ApplicationNotFoundException extends NodesException
{
    /**
     * ApplicationNotFoundException constructor
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