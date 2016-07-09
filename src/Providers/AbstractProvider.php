<?php
declare (strict_types = 1);

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
     * @var array
     */
    protected $aliases = [];

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
    protected $iOSBadge;

    /**
     * Custom sounds
     *
     * @var string|null
     */
    protected $sound;

    /**
     * Silent push notifications for iOS
     *
     * @var bool
     */
    protected $iosContentAvailable = false;

    /**
     * Data just added for android in extra
     *
     * @var array
     */
    protected $androidData = [];

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
     * setAliases,
     * Aliases are typically used as userId
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $aliases
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Throwable
     */
    public function setAliases(array $aliases) : ProviderInterface
    {
        // Make sure channels are strings
        foreach ($aliases as &$alias) {
            $alias = strval($alias);
        }

        $this->aliases = $aliases;

        return $this;
    }

    /**
     * setAlias,
     * Aliases are typically used as userId
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $alias
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setAlias(string $alias) : ProviderInterface
    {
        $this->aliases = [$alias];

        return $this;
    }

    /**
     * getAliases
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     */
    public function getAliases() : array
    {
        return $this->aliases;
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
        $this->validateExtra($extra);

        $this->extra = $extra;

        return $this;
    }

    protected function validateExtra(array $extra)
    {
        $protectedKeys = [
            'sound',
            'data',
        ];

        // Make sure channels are strings
        foreach ($extra as $key => $value) {
            if (!is_scalar($value)) {
                throw new InvalidArgumentException(sprintf('Extra key [%s] was an array/object', $key));
            }

            if (in_array($key, $protectedKeys)) {
                throw new InvalidArgumentException(sprintf('The used key [%s] in extra is protected by package', $key));
            }
        }
    }

    /**
     * setAndroidData
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $androidData
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setAndroidData(array $androidData) : ProviderInterface
    {
        $this->validateExtra($androidData);

        $this->androidData = $androidData;

        return $this;
    }

    /**
     * getAndroidData
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     */
    public function getAndroidData() : array
    {
        return $this->androidData;
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
     * setIOSBadge
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string|int|null $iOSBadge
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setIOSBadge($iOSBadge) : ProviderInterface
    {
        if (!is_scalar($iOSBadge)) {
            throw new InvalidArgumentException('The passed badge was an array/object');
        }

        $this->iOSBadge = $iOSBadge;

        return $this;
    }

    /**
     * getBadge
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return null|int|string
     */
    public function getIOSBadge()
    {
        return $this->iOSBadge;
    }

    /**
     * setSound, this custom sound string will be passed specific for ios and in extras for android,
     * the sound needs to be registered in the apps
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param string $sound
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setSound(string $sound) : ProviderInterface
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * removeSound, clear the custom sound
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function removeSound() : ProviderInterface
    {
        $this->sound = null;

        return $this;
    }

    /**
     * getSound
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return null|string
     */
    public function getSound()
    {
        return $this->sound;
    }

    /**
     * setIosContentAvailable
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param bool $iosContentAvailable
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setIosContentAvailable(bool $iosContentAvailable) : ProviderInterface
    {
        $this->iosContentAvailable = $iosContentAvailable;

        return $this;
    }

    /**
     * isIosContentAvailable
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return bool
     */
    public function isIosContentAvailable() : bool
    {
        return $this->iosContentAvailable;
    }
}