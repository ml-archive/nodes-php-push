<?php

namespace Nodes\Push\Exceptions;

use Nodes\Exceptions\Exception as NodesException;

/**
 * Class InvalidPushProviderException.
 */
class InvalidPushProviderException extends NodesException
{
    /**
     * InvalidPushProvider constructor.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param string $object
     */
    public function __construct($object)
    {
        if (is_object($object)) {
            $provider = get_class($object);
        } elseif (empty($object)) {
            $provider = 'NULL';
        } else {
            $provider = $object;
        }
        $message = sprintf('The push provider used [%s] is not an instance of ProviderInstance', $provider);
        parent::__construct($message, 500);
    }
}
