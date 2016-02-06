<?php
namespace Nodes\Push;

use Nodes\AbstractServiceProvider;
use Nodes\Push\Contracts\ProviderInterface as NodesPushProviderContract;

/**
 * Class ServiceProvider
 *
 * @package Nodes\Push
 */
class ServiceProvider extends AbstractServiceProvider
{
    /**
     * Package name
     *
     * @var string
     */
    protected $package = 'push';

    /**
     * Facades to install
     *
     * @var array
     */
    protected $facades = [
        'NodesPush' => \Nodes\Push\Support\Facades\Push::class
    ];

    /**
     * Array of configs to copy
     *
     * @var array
     */
    protected $configs = [
        'config/push.php' => 'config/nodes/push.php'
    ];

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
        parent::register();

        $this->registerPushManager();

    }

    /**
     * Register push manager
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function registerPushManager()
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