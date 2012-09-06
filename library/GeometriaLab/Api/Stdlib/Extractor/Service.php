<?php

namespace GeometriaLab\Api\Stdlib\Extractor;

use Zend\ServiceManager\FactoryInterface as ZendFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface,
    Zend\ServiceManager\Exception\ServiceNotCreatedException as ZendServiceNotCreatedException,
    Zend\Stdlib\Exception\BadMethodCallException as ZendBadMethodCallException;

use GeometriaLab\Model\ModelInterface,
    GeometriaLab\Stdlib\Extractor\Extractor,
    GeometriaLab\Api\Exception\WrongFields;

class Service implements ZendFactoryInterface
{
    const EXTRACTOR_DIR = 'Extractor';

    /**
     * @var array
     */
    private $fields;

    /**
     * @var Extractor[]
     */
    static private $extractorInstances;

    /**
     * @var string
     */
    private $extractorPath;

    /**
     * @param ZendServiceLocatorInterface $serviceLocator
     * @return Service
     */
    public function createService(ZendServiceLocatorInterface $serviceLocator)
    {
        $controllerNameSpace = $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch()->getParam('__NAMESPACE__');
        $parts = explode('\\', $controllerNameSpace);
        array_pop($parts);
        $this->extractorPath = implode('\\', $parts) . '\\' . self::EXTRACTOR_DIR;

        $fieldsParam = $serviceLocator->get('Application')->getMvcEvent()->getRequest()->getQuery()->get('_fields');
        $this->fields = self::createFieldsFromString($fieldsParam);

        return $this;
    }

    /**
     * @param ModelInterface $model
     * @return array
     * @throws ZendBadMethodCallException
     * @throws ZendServiceNotCreatedException
     */
    public function extract(ModelInterface $model)
    {
        if ($this->extractorPath === null) {
            throw new ZendServiceNotCreatedException('Get me from Service Manager');
        }

        $parts = explode('\\', get_class($model));
        $extractorName = $this->extractorPath . '\\' . array_pop($parts);

        if (!isset(static::$extractorInstances[$extractorName])) {
            if (is_subclass_of($extractorName, 'Extractor')) {
                throw new ZendBadMethodCallException("Invalid extractor for model '" . get_class($model) . "'");
            }
            static::$extractorInstances[$extractorName] = new $extractorName;
        }

        return static::$extractorInstances[$extractorName]->extract($model, $this->fields);
    }

    /**
     * Create Fields from string
     *
     * @static
     * @param $fieldsString
     * @return array
     * @throws WrongFields
     */
    static public function createFieldsFromString($fieldsString)
    {
        $fieldsString = str_replace(' ', '', $fieldsString);
        $fields = array();
        $level = 0;
        $stack = array();
        $stack[$level] = &$fields;
        $field = '';
        $len = strlen($fieldsString) - 1;

        for ($i = 0; $i <= $len; $i++) {
            $char = $fieldsString[$i];
            switch ($char) {
                case ',':
                    if ('' != $field) {
                        $stack[$level][$field] = true;
                        $field = '';
                    }
                    break;
                case '(':
                    $stack[$level][$field] = array();
                    $oldLevel = $level;
                    $stack[++$level] = &$stack[$oldLevel][$field];
                    $field = '';
                    break;
                case ')':
                    if ('' != $field) {
                        $stack[$level][$field] = true;
                    }
                    unset($stack[$level--]);
                    if ($level < 0) {
                        throw new WrongFields('Bad _fields syntax');
                    }
                    $field = '';
                    break;
                default:
                    $field.= $char;
                    if ($i == $len && '' !== $field) {
                        $stack[$level][$field] = true;
                        $field = '';
                    }
            }
        }
        if (count($stack) > 1) {
            throw new WrongFields('Bad _fields syntax');
        }

        return $fields;
    }
}
