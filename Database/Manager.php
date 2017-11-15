<?php
/**
 * User: yongli
 * Date: 17/11/14
 * Time: 10:09
 * Email: yong.li@szypwl.com
 * Copyright: 深圳优品未来科技有限公司
 */
namespace Database;

use Container\Container;
use Database\DatabaseManager;
use Connectors\ConnectionFactory;
use Support\CapsuleManagerTrait;

class Manager
{
    use CapsuleManagerTrait;
    /**
     * The database manager instance.
     *
     * @var
     */
    protected $manager;
    
    /**
     *  Create a new database capsule manager.
     *
     * Manager constructor.
     *
     * @param Container|null $container
     */
    public function __construct(Container $container = null)
    {
        $this->setupContainer($container ? : new Container);
        // Once we have the container setup, we will setup the default configuration
        // options in the container "config" binding. This will make the database
        // manager work correctly out of the box without extreme configuration.
        $this->setupDefaultConfiguration();
        $this->setupManager();
    }

    /**
     * Setup the default database configuration options.
     *
     * @return void
     */
    protected function setupDefaultConfiguration()
    {
        $this->container['config']['database.fetch']   = \PDO::FETCH_OBJ;
        $this->container['config']['database.default'] = 'default';
    }

    /**
     * Build the database manager instance.
     *
     * @return void
     */
    protected function setupManager()
    {
        $factory       = new ConnectionFactory($this->container);
        $this->manager = new DatabaseManager($this->container, $factory);
    }

    /**
     * Get a connection instance from the global manager.
     *
     * @param null $connection
     *
     * @return mixed
     */
    public static function connection($connection = null)
    {
        return static::$instance->getConnection($connection);
    }

    /**
     * Get a fluent query builder instance.
     *
     * @param      $table
     * @param null $connection
     *
     * @return mixed
     */
    public static function table($table, $connection = null)
    {
        return static::$instance->connection($connection)->table($table);
    }

    /**
     * Get a registered connection instance.
     *
     * @param null $name
     *
     * @return mixed
     */
    public function getConnection($name = null)
    {
        return $this->manager->connection($name);
    }

    /**
     * Register a connection with the manager.
     *
     * @param  array  $config
     * @param  string $name
     *
     * @return void
     */
    public function addConnection(array $config, $name = 'default')
    {
        $connections                                       = $this->container['config']['database.connections'];
        $connections[$name]                                = $config;
        $this->container['config']['database.connections'] = $connections;
    }

    /**
     * Bootstrap Eloquent so it is ready for usage.
     *
     * @return void
     */
    public function bootEloquent()
    {
        Eloquent::setConnectionResolver($this->manager);
        // If we have an event dispatcher instance, we will go ahead and register it
        // with the Eloquent ORM, allowing for model callbacks while creating and
        // updating "model" instances; however, it is not necessary to operate.
        if ($dispatcher = $this->getEventDispatcher()) {
            Eloquent::setEventDispatcher($dispatcher);
        }
    }

    /**
     * Set the fetch mode for the database connections.
     *
     * @param  int $fetchMode
     *
     * @return $this
     */
    public function setFetchMode($fetchMode)
    {
        $this->container['config']['database.fetch'] = $fetchMode;

        return $this;
    }
    
    /**
     * Get the database manager instance.
     *
     * @return mixed
     */
    public function getDatabaseManager()
    {
        return $this->manager;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return static::connection()->$method(...$parameters);
    }

   
}