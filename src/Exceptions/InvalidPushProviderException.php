<?php
namespace Nodes\Push\Exceptions;

use Nodes\Exceptions\Exception as NodesException;

/**
 * Class InvalidPushProviderException
 *
 * @package Nodes\Push\Exceptions
 */
class InvalidPushProviderException extends NodesException
{
    /**
     * InvalidPushProvider constructor
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $object
     */
    public function __construct($object)
    {
        $message = sprintf('The push provider used [%s] is not an instance of ProviderInstance', get_class($object));
        parent::__construct($message, 500);
    }
}