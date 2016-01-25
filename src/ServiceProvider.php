<?php
namespace Nodes\Push;

use Nodes\AbstractServiceProvider as NodesAbstractServiceProvider;
use Nodes\Push\Contracts\ProviderInterface as NodesPushProviderContract;

/**
 * Class ServiceProvider
 *
 * @package Nodes\Push
 */
class ServiceProvider extends NodesAbstractServiceProvider
{
    /**
     * Package name
     *
     * @var string
     */
    protected $package = 'push';

    /**
     * Register the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function register()
    {
        $this->app->singleton('nodes.push', function ($app) {
            // Retrieve push provider
            $provider = prepare_config_instance(config('nodes.push.provider'));

            // Validate push provider
            if (!$provider instanceof NodesPushProviderContract) {
                throw new Exception('Invalid Push Provider. Not implementing Push contract.');
            }

            return new Manager($provider);
        });

        $this->app->bind(Manager::class, function ($app) {
            return $app['nodes.push'];
        });
    }

    /**
     * Get the services provided by the provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return array
     */
    public function provides()
    {
        return ['nodes.push'];
    }
}