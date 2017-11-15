<?php
/**
 * User: yongli
 * Date: 17/11/14
 * Time: 10:34
 * Email: yong.li@szypwl.com
 * Copyright: 深圳优品未来科技有限公司
 */
namespace Connectors;

use Container\Container;
use Support\Arr;
use MySqlConnector;
use Database\MySqlConnection;

class ConnectionFactory
{
    /**
     * The IoC container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * Create a new connection factory instance.
     *
     * ConnectionFactory constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    // -------------------------- 已使用的方法 ------------------

    /**
     * Establish a PDO connection based on the configuration.
     *
     * @param array $config
     * @param null  $name
     *
     * @return MySqlConnection|mixed
     */
    public function make(array $config, $name = null)
    {
        $config = $this->parseConfig($config, $name);
        if (isset($config['read'])) {
            return $this->createReadWriteConnection($config);
        }

        return $this->createSingleConnection($config);
    }



    // -------------------------- end -----------------------

    /**
     * Parse and prepare the database configuration.
     *
     * @param  array  $config
     * @param  string $name
     *
     * @return array
     */
    protected function parseConfig(array $config, $name)
    {
        return Arr::add(Arr::add($config, 'prefix', ''), 'name', $name);
    }

    /**
     * Create a single database connection instance.
     *
     * @param array $config
     *
     * @return MySqlConnection
     */
    protected function createSingleConnection(array $config)
    {
        $pdo = $this->createPdoResolver($config);

        return $this->createConnection($config['driver'], $pdo, $config['database'], $config['prefix'], $config);
    }

    /**
     * Create a single database connection instance.
     *
     * @param array $config
     *
     * @return mixed
     */
    protected function createReadWriteConnection(array $config)
    {
        $connection = $this->createSingleConnection($this->getWriteConfig($config));

        return $connection->setReadPdo($this->createReadPdo($config));
    }

    /**
     * Create a new PDO instance for reading.
     *
     * @param  array $config
     *
     * @return \Closure
     */
    protected function createReadPdo(array $config)
    {
        return $this->createPdoResolver($this->getReadConfig($config));
    }

    /**
     * Get the read configuration for a read / write connection.
     *
     * @param  array $config
     *
     * @return array
     */
    protected function getReadConfig(array $config)
    {
        return $this->mergeReadWriteConfig($config, $this->getReadWriteConfig($config, 'read'));
    }

    /**
     * Get the read configuration for a read / write connection.
     *
     * @param  array $config
     *
     * @return array
     */
    protected function getWriteConfig(array $config)
    {
        return $this->mergeReadWriteConfig($config, $this->getReadWriteConfig($config, 'write'));
    }

    /**
     * Get a read / write level configuration.
     *
     * @param  array  $config
     * @param  string $type
     *
     * @return array
     */
    protected function getReadWriteConfig(array $config, $type)
    {
        return isset($config[$type][0]) ? $config[$type][array_rand($config[$type])] : $config[$type];
    }

    /**
     * Merge a configuration for a read / write connection.
     *
     * @param  array $config
     * @param  array $merge
     *
     * @return array
     */
    protected function mergeReadWriteConfig(array $config, array $merge)
    {
        return Arr::except(array_merge($config, $merge), ['read', 'write']);
    }

    /**
     * Create a new Closure that resolves to a PDO instance.
     *
     * @param  array $config
     *
     * @return \Closure
     */
    protected function createPdoResolver(array $config)
    {
        return array_key_exists('host',
            $config) ? $this->createPdoResolverWithHosts($config) : $this->createPdoResolverWithoutHosts($config);
    }

    /**
     * Create a new Closure that resolves to a PDO instance with a specific host or an array of hosts.
     *
     * @param  array $config
     *
     * @return \Closure
     */
    protected function createPdoResolverWithHosts(array $config)
    {
        return function () use ($config) {
            foreach (shuffle($hosts = $this->parseHosts($config)) as $key => $host) {
                $config['host'] = $host;
                try {
                    return $this->createConnector($config)->connect($config);
                } catch (\PDOException $e) {
                    if (count($hosts) - 1 === $key && $this->container->bound(ExceptionHandler::class)) {
                        $this->container->make(ExceptionHandler::class)->report($e);
                    }
                }
            }
            throw $e;
        };
    }

    /**
     * Parse the hosts configuration item into an array.
     *
     * @param array $config
     *
     * @return mixed
     */
    protected function parseHosts(array $config)
    {
        $hosts = Arr::wrap($config['host']);
        if (empty($hosts)) {
            throw new \InvalidArgumentException('Database hosts array is empty.');
        }

        return $hosts;
    }

    /**
     * Create a new Closure that resolves to a PDO instance where there is no configured host.
     *
     * @param  array $config
     *
     * @return \Closure
     */
    protected function createPdoResolverWithoutHosts(array $config)
    {
        return function () use ($config) {
            return $this->createConnector($config)->connect($config);
        };
    }

    /**
     * Create a connector instance based on the configuration.
     *
     * @param array $config
     *
     * @return mixed|MySqlConnector
     */
    public function createConnector(array $config)
    {
        if (!isset($config['driver'])) {
            throw new \InvalidArgumentException('A driver must be specified.');
        }
        if ($this->container->bound($key = "db.connector.{$config['driver']}")) {
            return $this->container->make($key);
        }
        if ($config['driver'] == 'mysql') {
            return new MySqlConnector;
        }
        throw new \InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }

    /**
     * Create a new connection instance.
     *
     * @param   string        $driver
     * @param   \PDO|\Closure $connection
     * @param   string        $database
     * @param string          $prefix
     * @param array           $config
     *
     * @return MySqlConnection
     */
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        if ($resolver = \Database\Connection::getResolver($driver)) {
            return $resolver($connection, $database, $prefix, $config);
        }
        if ($driver == 'mysql') {
            return new MySqlConnection($connection, $database, $prefix, $config);
        }
        throw new \InvalidArgumentException("Unsupported driver [$driver]");
    }
}