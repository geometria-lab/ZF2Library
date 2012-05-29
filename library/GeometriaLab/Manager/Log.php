<?php
/**
 *
 * @author munkie
 *
 */
class Geometria_Manager_Log extends Geometria_Manager_Abstract
{
    /*
     * Default priority for staticLog() method
     */
    const DEFAULT_PRIORITY = Zend_Log::WARN;

    /**
     * Logs instances
     *
     * @var array
     */
    protected $_logs = array();

    /**
     *
     * @param string $name
     * @param array  $args
     */
    static public function __callStatic($name, $args)
    {
        if (0 === strpos($name, 'static')) {
            $method = substr($name, 6);
            $instance = self::getInstance();
            if (!method_exists($instance, $method)) {
                throw new Zend_Log_Exception("Can't call $method staticaly, it does not exist");
            } else if (!is_callable(array($instance, $method))) {
                throw new Zend_Log_Exception("Can't call $method staticaly, it is not callable");
            }
            return call_user_func_array(array($instance, $method), $args);
        }

        throw new Zend_Log_Exception("Static method $name does not exists");
    }

    /**
     *
     * @param  string $name
     * @return Zend_Log
     */
    public function getLog($name)
    {
        if (!isset($this->_logs[$name])) {
            $config = $this->getConfig($name);
            $log = $this->_initLog($config);
            $this->setLog($name, $log);
        }
        return $this->_logs[$name];
    }

    /**
     * @param array $config
     * @return Zend_Log
     */
    private function _initLog(array $config)
    {
        // dont pass 'defaultPriority' param, it will break factory
        if (isset($config['defaultPriority'])) {
            unset($config['defaultPriority']);
        }
        return Zend_Log::factory($config);
    }

    /**
     *
     * @param string   $name
     * @param Zend_Log $log
     */
    public function setLog($name, Zend_Log $log)
    {
        $this->_logs[$name] = $log;
    }

    /**
     *
     * @param string $name
     * @return Zend_Log
     */
    static public function staticGetLog($name)
    {
        return self::getInstance()->getLog($name);
    }

    /**
     *
     * @param string  $name
     * @param string  $message
     * @param integer $priority
     * @param mixed   $extras
     */
    static public function staticLog($name, $message, $priority = null, $extras = null)
    {
        $config = self::getInstance()->getLogConfig($name);
        // detect default priority for log instance
        if (null === $priority) {
            if (isset($config['defaultPriority'])) {
                $priority = $config['defaultPriority'];
                // Add support for constants
                if (!is_numeric($priority) && defined($priority)) {
                    $priority = constant($priority);
                }
            } else {
                $priority = self::DEFAULT_PRIORITY;
            }
        }
        return self::staticGetLog($name)->log($message, $priority, $extras);
    }
}