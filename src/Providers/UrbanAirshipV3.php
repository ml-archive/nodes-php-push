<?php
namespace Nodes\Push\Providers;

use Nodes\Push\Contracts\ProviderInterface;


/**
 * Class UrbanAirship
 *
 * @package Nodes\Push\Providers
 */
class UrbanAirshipV3 extends AbstractProvider
{
    /**
     * UrbanAirshipV3 constructor
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param array $config
     * @throws ConfigErrorException
     * @throws ApplicationNotFoundException
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

}