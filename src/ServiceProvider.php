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
     * Boot the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->publishGroups();
    }

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
     * Register publish groups
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function publishGroups()
    {
        // Config files
        $this->publishes([
            __DIR__ . '/../config/push.php' => config_path('nodes/push.php'),
        ], 'config');
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