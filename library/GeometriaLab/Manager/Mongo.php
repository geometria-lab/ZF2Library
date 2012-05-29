<?php
/**
 * 
 * @author munkie
 *
 * @method static Geometria_Manager_Mongo getInstance()
 */
class Geometria_Manager_Mongo extends Geometria_Manager_Abstract
{
    /**
     * Instances of mongo instances
     *
     * @var Mongo[]
     */
    private $_mongos = array();

    /**
     *
     * @param  string $name
     * @return Mongo
     */
    public function getMongo($name)
    {
        if (!isset($this->_mongos[$name])) {
            $config = $this->getConfig($name);
            $mongo = $this->_initMongo($config);
            $this->setMongo($name, $mongo);
        }
        return $this->_mongos[$name];
    }

    /**
     * Get all mongos
     *
     * @return Mongo[]
     */
    public function getMongos()
    {
        return $this->_mongos;
    }

    /**
     * 
     * @param array $config
     * @return Mongo
     * @throw Geometria_Manager_Exception
     */
    private function _initMongo(array $config)
    {
        if (!class_exists('Mongo')) {
            throw new Geometria_Manager_Exception('PHP extension Mongo is not installed');
        }
        
        if (isset($config['connectionString'])) {
            $server = $config['connectionString'];

            if (isset($config['options']) && is_array($config['options'])) {
                if (isset($config['options']['connect'])) {
                    $config['options']['connect'] = (bool) $config['options']['connect'];
                }
                if (array_key_exists('persist', $config['options']) && "" == (string) $config['options']['persist']) {
                    unset($config['options']['persist']);
                }

                $slaveOk = null;
                if (array_key_exists('slaveOk', $config['options'])) {
                    $slaveOk = !empty($config['options']['slaveOk']);
                    unset($config['options']['slaveOk']);
                }

                $mongo = new Mongo($server, $config['options']);

                if ($slaveOk !== null) {
                    $mongo->setSlaveOkay($slaveOk);
                }

                return $mongo;
            } else {
                return new Mongo($server);
            }
        } else {
            throw new Geometria_Manager_Exception('Mongo config is invalid: ' . var_export($config, true));
        }
    }

    /**
     *
     * @param string   $name
     * @param Mongo    $mongo
     * 
     * @return Geometria_Manager_Mongo
     */
    public function setMongo($name, Mongo $mongo)
    {
        $this->_mongos[$name] = $mongo;
        return $this;
    }
    
    /**
     * 
     * @param string $name
     * @param boolean $forceClose force to close connection
     * @return Geometria_Manager_Mongo
     */
    public function clearMongo($name, $forceClose = false)
    {
        if (isset($this->_mongos[$name])) {
            if ($forceClose) {
                $this->_mongos[$name]->close();
            }
            unset($this->_mongos[$name]);
        }
        return $this;
    }
    
    /**
     * 
     * Clear all mongo instances
     * 
     * @param boolean $forceClose force to close connection
     * @return Geometria_Manager_Mongo
     */
    public function clearMongos($forceClose = false)
    {
        foreach ($this->_mongos as $name => $mongo) {
            $this->clearMongo($name, $forceClose);
        }
        return $this;
    }

    
    /**
     * 
     * @param string $name
     * @return MongoDb
     * @throws Geometria_Manager_Exception
     */
    public function getMongoDb($name)
    {
        $mongo = $this->getMongo($name);
        $config = $this->getConfig($name);
        if (!isset($config['db'])) {
            throw new Geometria_Manager_Exception('Db param not found in config');
        }
        return $mongo->selectDb($config['db']);
    }

    /**
     * @return MongoDb[]
     */
    public function getMongoDbs()
    {
        $dbs = array();

        foreach ($this->_configs as $name => $config) {
            $dbs[] = $this->getMongoDb($name);
        }

        return $dbs;
    }

    /**
     *
     * @param string $name
     * @return Mongo
     */
    static public function staticGetMongo($name)
    {
        return self::getInstance()->getMongo($name);
    }
    
    /**
     *
     * @param string $name
     * @return MongoDb
     */
    static public function staticGetMongoDb($name)
    {
        return self::getInstance()->getMongoDb($name);
    }
}