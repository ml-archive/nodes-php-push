<?php
namespace Nodes\Push\Providers;

use Nodes\Push\Contracts\ProviderInterface;
use Nodes\Push\Exceptions\ApplicationNotFoundException;
use Nodes\Push\Exceptions\ConfigErrorException;

/**
 * Class AbstractProvider
 *
 * @package Nodes\Push\Providers
 */
abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @var string
     */
    private $defaultAppGroup;

    /**
     * @var string
     */
    private $appGroup;

    /**
     * @var array
     */
    private $appGroups;

    /**
     * AbstractProvider constructor
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $config
     * @throws ConfigErrorException
     * @throws ApplicationNotFoundException
     */
    public function __construct(array $config = [])
    {
        // Validate configs
        if (empty($config['default-app-group'])) {
            throw new ConfigErrorException('Missing default-app-group config');
        }

        if (!is_string($config['default-app-group'])) {
            throw new ConfigErrorException('default-app-group is not a string');
        }

        $this->appGroup = $this->defaultAppGroup = $config['default-app-group'];

        if (empty($config['app-groups'])) {
            throw new ConfigErrorException('Missing app-groups config');
        }

        if (!is_array($config['app-groups'])) {
            throw new ConfigErrorException('app-groups is not an array');
        }

        $this->appGroups = $config['app-groups'];

        if (!array_key_exists($this->defaultAppGroup, $config['app-groups'])) {
            throw new ApplicationNotFoundException(sprintf('Default app [%s] was not found in list of of app-groups',
                $this->defaultAppGroup));
        }
    }

    public function setAppGroup($appGroup) {
        if (!array_key_exists($this->defaultAppGroup, $appGroup)) {
            throw new ApplicationNotFoundException(sprintf('Default app [%s] was not found in list of of app-groups',
                $this->defaultAppGroup));
        }
    }
}