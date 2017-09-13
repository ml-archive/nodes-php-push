<?php

namespace Nodes\Push\Exceptions;

use Nodes\Exceptions\Exception as NodesException;

/**
 * Class InvalidAudienceException.
 */
class InvalidAudienceException extends NodesException
{
    /**
     * InvalidAudience constructor.
     *
     * @author Justin Busschau <jubu@nodesagency.com>
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 500);
    }
}
