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
     * Exception constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string  $message   Error message
     * @param  integer $code      Error code
     * @param  array   $headers   List of headers
     * @param  boolean $report    Wether or not exception should be reported
     * @param  string  $severity  Options: "fatal", "error", "warning", "info"
     */
    public function __construct($message, $code = 500, $headers = [], $report = false, $severity = 'error')
    {
        parent::__construct($message, $code, $headers, $report, $severity);
    }
}