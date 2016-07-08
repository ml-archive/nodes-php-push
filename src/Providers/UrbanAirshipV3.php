<?php
namespace Nodes\Push\Providers;

use Nodes\Push\Contracts\ProviderInterface;
use Nodes\Push\Exceptions\InvalidArgumentException;

/**
 * Class UrbanAirship
 *
 * @package Nodes\Push\Providers
 */
class UrbanAirshipV3 extends AbstractProvider
{
    /**
     * setBadge
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param int|string $badge
     * @return \Nodes\Push\Contracts\ProviderInterface
     * @throws \Nodes\Push\Exceptions\InvalidArgumentException
     */
    public function setBadge($badge) : ProviderInterface
    {
        // Convert to int, if badge does not start with +/-, since int means setting the value
        if(is_numeric($badge) && !starts_with($badge, '-') && !starts_with($badge, '+')) {
            $badge = intval($badge);
        }

        if(is_int($badge) && $badge < 0) {
            throw new InvalidArgumentException('Bagde was set to minus integer, either set 0 or as string fx "-5');
        }

        if (!is_int($badge) && $badge != 'auto' && !is_numeric($badge)) {
            throw new InvalidArgumentException('The passed badge is not supported');
        }

        $this->badge = $badge;

        return $this;
    }

}