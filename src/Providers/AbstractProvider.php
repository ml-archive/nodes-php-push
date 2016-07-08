<?php
namespace Nodes\Push\Providers;

use Nodes\Push\Contracts\ProviderInterface;
use Nodes\Push\Exceptions\ApplicationNotFoundException;
use Nodes\Push\Exceptions\ConfigErrorException;
use Nodes\Push\Exceptions\InvalidArgumentException;

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
    protected $defaultAppGroup;

    /**
     * @var string
     */
    protected $appGroup;

    /**
     * @var array
     */
    protected $appGroups;

    /**
     * @var array
     */
    protected $channels = [];

    /**
     * @var string|null
     */
    protected $message;

    /**
     * @var array
     */
    protected $extra = [];

    /**
     * @var null|int|string
     */
    protected $badge;

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
            throw new ApplicationNotFoundException(sprintf('default-app-group [%s] was not found in list of of app-groups',
                $this->defaultAppGroup));
        }
    }

    /**
     * set the app group which should be used
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $appGroup
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\ApplicationNotFoundException
     */
    public function setAppGroup(string $appGroup) : ProviderInterface
    {
        if (!array_key_exists($appGroup, $this->appGroups)) {
            throw new ApplicationNotFoundException(sprintf('The passed appGroup [%s] was not found in list of of app-groups',
                $this->defaultAppGroup));
        }

        $this->appGroup = $appGroup;

        return $this;
    }

    /**
     * getAppGroup
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return string
     */
    public function getAppGroup() : string
    {
        return $this->appGroup;
    }

    /**
     * setChannels
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $channels
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Throwable
     */
    public function setChannels(array $channels) : ProviderInterface
    {
        // Make sure channels are strings
        foreach ($channels as &$channel) {
            $channel = strval($channel);
        }

        $this->channels = $channels;

        return $this;
    }

    /**
     * setChannel
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $channel
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setChannel(string $channel) : ProviderInterface
    {
        $this->channels = [$channel];

        return $this;
    }

    /**
     * getChannels
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     */
    public function getChannels() : array
    {
        return $this->channels;
    }

    /**
     * setMessage
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $message
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setMessage(string $message) : ProviderInterface
    {
        $this->message = $message;

        return $this;
    }

    /**
     * getMessage
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * setExtra
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $extra
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setExtra(array $extra) : ProviderInterface
    {
        // Make sure channels are strings
        foreach ($extra as $key => $value) {
            if (!is_scalar($value)) {
                throw new InvalidArgumentException(sprintf('Extra key [%s] was an array/object', $key));
            }
        }

        $this->extra = $extra;

        return $this;
    }

    /**
     * getExtra
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     */
    public function getExtra() : array
    {
        return $this->extra;
    }

    /**
     * setBadge
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param mixed $badge
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setBadge($badge) : ProviderInterface
    {
        if (!is_scalar($badge)) {
            throw new InvalidArgumentException('The passed badge was an array/object');
        }

        $this->badge = $badge;

        return $this;
    }

    /**
     * getBadge
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return null|int|string
     */
    public function getBadge()
    {
        return $this->badge;
    }
}