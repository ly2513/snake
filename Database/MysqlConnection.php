<?php
/**
 * User: yongli
 * Date: 17/11/14
 * Time: 10:51
 * Email: yong.li@szypwl.com
 * Copyright: 深圳优品未来科技有限公司
 */
namespace Database;

use PDO;

class MySqlConnection extends Connection
{
    /**
     * Get the default query grammar instance.
     *
     * @return Grammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar);
    }

    /**
     * Get the default post processor instance.
     *
     * @return MySqlProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new MySqlProcessor;
    }

    /**
     * Get the Doctrine DBAL driver.
     *
     * @return Driver
     */
    protected function getDoctrineDriver()
    {
        return new Driver;
    }

    /**
     * Bind values to their parameters in the given statement.
     *
     * @param  \PDOStatement $statement
     * @param  array         $bindings
     *
     * @return void
     */
    public function bindValues($statement, $bindings)
    {
        foreach ($bindings as $key => $value) {
            $statement->bindValue(is_string($key) ? $key : $key + 1, $value,
                is_int($value) || is_float($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
    }
}

/**
 * PDO MySql driver.
 *
 * @since 2.0
 */
class Driver
{
    /**
     * {@inheritdoc}
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        try {
            $conn = new PDOConnection(
                $this->constructPdoDsn($params),
                $username,
                $password,
                $driverOptions
            );
        } catch (\PDOException $e) {
            throw \DBALException::driverException($this, $e);
        }

        return $conn;
    }

    /**
     * Constructs the MySql PDO DSN.
     *
     * @param array $params
     *
     * @return string The DSN.
     */
    protected function constructPdoDsn(array $params)
    {
        $dsn = 'mysql:';
        if (isset($params['host']) && $params['host'] != '') {
            $dsn .= 'host=' . $params['host'] . ';';
        }
        if (isset($params['port'])) {
            $dsn .= 'port=' . $params['port'] . ';';
        }
        if (isset($params['dbname'])) {
            $dsn .= 'dbname=' . $params['dbname'] . ';';
        }
        if (isset($params['unix_socket'])) {
            $dsn .= 'unix_socket=' . $params['unix_socket'] . ';';
        }
        if (isset($params['charset'])) {
            $dsn .= 'charset=' . $params['charset'] . ';';
        }

        return $dsn;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pdo_mysql';
    }
}

class MySqlProcessor extends Processor
{
    /**
     * Process the results of a column listing query.
     *
     * @param  array  $results
     * @return array
     */
    public function processColumnListing($results)
    {
        return array_map(function ($result) {
            return with((object) $result)->column_name;
        }, $results);
    }
}
