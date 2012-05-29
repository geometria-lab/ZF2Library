<?php

$GLOBALS['THRIFT_ROOT'] = 'Thrift';

require_once APPLICATION_PATH . '/../library/phpcassa/connection.php';
require_once APPLICATION_PATH . '/../library/phpcassa/columnfamily.php';
require_once APPLICATION_PATH . '/../library/phpcassa/uuid.php';

/**
 * @author munkie
 *
 * @method static Geometria_Manager_Cassandra getInstance()
 */
class Geometria_Manager_Cassandra extends Geometria_Manager_Abstract
{
    /**
     * @var ConnectionPool[]
     */
    private $_pools = array();

    /**
     * @return ConnectionPool
     */
    public function getPool($name)
    {
        if (!isset($this->_pools[$name])) {
            if (!isset($this->_configs[$name])) {
                throw new Geometria_Cassandra_Exception("Cassandra pool config '$name' not found");
            }
            $pool = $this->_initPoll($this->_configs[$name]);
            $this->setPool($name, $pool);
        }
        return $this->_pools[$name];
    }

    /**
     * @param string $name
     * @param ConnectionPool $pool
     */
    public function setPool($name, ConnectionPool $pool)
    {
        $this->_pools[$name] = $pool;
    }

    /**
     * @param array $options
     * @return ConnectionPool
     * @throws Geometria_Cassandra_Exception
     */
    private function _initPoll(array $options)
    {
        if (!isset($options['keyspace'])) {
            throw new Geometria_Cassandra_Exception('keyspace is missing in pool options');
        }
        if (isset($options['servers']) && is_array($options['servers'])) {
            $options['servers'] = array_filter($options['servers']);
            if (isset($options['servers_shuffle']) && true == $options['servers_shuffle']) {
                shuffle($options['servers']);
            }
        }
        $poolOptions = array(
            'keyspace'          => '',
            'servers'           => NULL,
            'pool_size'         => NULL,
            'max_retries'       => ConnectionPool::DEFAULT_MAX_RETRIES,
            'send_timeout'      => 5000,
            'recv_timeout'      => 5000,
            'recycle'           => ConnectionPool::DEFAULT_RECYCLE,
            'credentials'       => NULL,
            'framed_transport'  => true
        );
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $poolOptions)) {
                $poolOptions[$key] = $value;
            }
        }
        $pool = new ConnectionPool(
            $poolOptions['keyspace'],
            $poolOptions['servers'],
            $poolOptions['pool_size'],
            $poolOptions['max_retries'],
            $poolOptions['send_timeout'],
            $poolOptions['recv_timeout'],
            $poolOptions['recycle'],
            $poolOptions['credentials'],
            $poolOptions['framed_transport']
        );
        return $pool;
    }

    /**
     * @static
     * @param string $name
     * @return ConnectionPool
     */
    static public function staticGetPool($name)
    {
        return self::getInstance()->getPool($name);
    }

    /**
     * @param $name
     * @return Geometria_Manager_Cassandra
     */
    public function clearPool($name)
    {
        if (isset($this->_pools[$name])) {
            unset($this->_pools[$name]);
        }
        return $this;
    }
}