<?php

declare(strict_types=1);

namespace Nodes\Push\Providers;

use Nodes\Push\Contracts\ProviderInterface;
use Nodes\Push\Exceptions\ApplicationNotFoundException;
use Nodes\Push\Exceptions\ConfigErrorException;
use Nodes\Push\Exceptions\InvalidArgumentException;

/**
 * Class AbstractProvider.
 */
abstract class AbstractProvider implements ProviderInterface
{
    /**
     * Fallback app group if nothing is set.
     *
     * @var string
     */
    protected $defaultAppGroup;

    /**
     * App group set, will be used instead of fallback.
     *
     * @var string
     */
    protected $appGroup;

    /**
     * List of all app groups.
     *
     * @var array
     */
    protected $appGroups;

    /**
     * Channels for segmented push.
     *
     * @var array
     */
    protected $channels = [];

    /**
     * Aliases for segmented push.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * The message which will be shown in the push notification.
     *
     * @var string|null
     */
    protected $message;

    /**
     * A map of key/value which will be passed in the push.
     *
     * @var array
     */
    protected $extra = [];

    /**
     * A var to control the badge on app icon.
     *
     * @var null|int|string
     */
    protected $iOSBadge;

    /**
     * Custom sound.
     *
     * @var string|null
     */
    protected $sound = 'default';

    /**
     * Silent push notifications for iOS.
     *
     * @var bool
     */
    protected $iosContentAvailable = false;

    /**
     * Delivery priority for android
     * in GCM "normal" and "high" is supported
     *
     * @var string
     */
    protected $androidDeliveryPriority = 'normal';

    /**
     * Data just added for android in extra.
     *
     * @var array
     */
    protected $androidData = [];

    /**
     * String with proxy URL and Port
     *
     * @var string|null
     */
    protected $proxy = null;

    /**
     * AbstractProvider constructor.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
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

        if (!empty($config['proxy'])) {
            $this->proxy = $config['proxy'];
        }

        if (!array_key_exists($this->defaultAppGroup, $config['app-groups'])) {
            throw new ApplicationNotFoundException(sprintf('default-app-group [%s] was not found in list of of app-groups',
                $this->defaultAppGroup));
        }
    }

    /**
     * set the app group which should be used to send pushes.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
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
     * getAppGroup.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return string
     */
    public function getAppGroup() : string
    {
        return $this->appGroup;
    }

    /**
     * setChannels, for segmented push
     * This will override current channels.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
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
     * setChannel, for segmented push.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param string $channel
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setChannel(string $channel) : ProviderInterface
    {
        $this->channels = [$channel];

        return $this;
    }

    /**
     * getChannels.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return array
     */
    public function getChannels() : array
    {
        return $this->channels;
    }

    /**
     * setAliases,
     * Aliases are typically used as userId for segmented push.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
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
     * Aliases are typically used as userId for segmented push.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param string $alias
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setAlias(string $alias) : ProviderInterface
    {
        $this->aliases = [$alias];

        return $this;
    }

    /**
     * getAliases.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return array
     */
    public function getAliases() : array
    {
        return $this->aliases;
    }

    /**
     * setMessage, the message which will be shown in the push notification.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param string $message
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setMessage(string $message) : ProviderInterface
    {
        $this->message = $message;

        return $this;
    }

    /**
     * getMessage.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * setExtra, extra is a map of key /value which can be passed to mobile
     * There is a hard limit on how big a push notification can be, specially for ios
     * Consider not putting too much in here, and consider using setAndroidData if you want to send more to android.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
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

    /**
     * validateExtra.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param array $extra
     * @return void
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    protected function validateExtra(array $extra)
    {
        $protectedKeys = [];

        // Make sure channels are strings
        foreach ($extra as $key => $value) {
            if (!is_scalar($value)) {
                throw new InvalidArgumentException(sprintf('Extra key [%s] was array/object/null', $key));
            }

            if (in_array(strval($key), $protectedKeys)) {
                throw new InvalidArgumentException(sprintf('The used key [%s] in extra is protected by package', $key));
            }
        }
    }

    /**
     * setAndroidData, since android can handle 4kb while ios only have 0.5kb
     * Note this will override keys in extra, if same keys are passed.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
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
     * getAndroidData.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return array
     */
    public function getAndroidData() : array
    {
        return $this->androidData;
    }

    /**
     * getExtra.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return array
     */
    public function getExtra() : array
    {
        return $this->extra;
    }

    /**
     * setIOSBadge, badge is the small count on the app icon.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
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
     * getBadge.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return null|int|string
     */
    public function getIOSBadge()
    {
        return $this->iOSBadge;
    }

    /**
     * setSound, this custom sound string will be passed specific for ios and in extras for android,
     * the sound needs to be registered in the apps.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param string $sound
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setSound(string $sound) : ProviderInterface
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * removeSound, clear the custom sound.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function removeSound() : ProviderInterface
    {
        $this->sound = null;

        return $this;
    }

    /**
     * getSound.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return null|string
     */
    public function getSound()
    {
        return $this->sound;
    }

    /**
     * setIosContentAvailable, silent push notifications, will not appear in notification center on ios.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param bool $iosContentAvailable
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setIosContentAvailable(bool $iosContentAvailable) : ProviderInterface
    {
        $this->iosContentAvailable = $iosContentAvailable;

        return $this;
    }

    /**
     * isIosContentAvailable.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return bool
     */
    public function isIosContentAvailable() : bool
    {
        return $this->iosContentAvailable;
    }

    /**
     * setAndroidDeliveryPriorityNormal
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setAndroidDeliveryPriorityNormal() : ProviderInterface
    {
        $this->androidDeliveryPriority = 'normal';
    }

    /**
     * setAndroidDeliveryPriorityHigh
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function setAndroidDeliveryPriorityHigh() : ProviderInterface
    {
        $this->androidDeliveryPriority = 'high';

        return $this;
    }

    /**
     * getAndroidDeliveryPriority
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return string
     */
    public function getAndroidDeliveryPriority() : string
    {
        return $this->androidDeliveryPriority;
    }

    /**
     * getInstance.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Nodes\Push\Contracts\ProviderInterface
     */
    public function getInstance() : ProviderInterface
    {
        return $this;
    }
}
