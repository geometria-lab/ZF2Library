<?php

namespace GeometriaLab\Api\Stdlib\Extractor;

use Zend\ServiceManager\FactoryInterface as ZendFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface,
    Zend\ServiceManager\Exception\ServiceNotCreatedException as ZendServiceNotCreatedException,
    Zend\Stdlib\Exception\BadMethodCallException as ZendBadMethodCallException;

use GeometriaLab\Model\ModelInterface,
    GeometriaLab\Api\Stdlib\Extractor\Extractor,
    GeometriaLab\Api\Exception\WrongFieldsException;

class Service implements ZendFactoryInterface
{
    /**
     * @var Extractor[]
     */
    static private $extractorInstances;
    /**
     * @var string
     */
    private $extractorsNamespace;
    /**
     * Wrong fields
     *
     * @var array
     */
    private $wrongFields = array();

    /**
     * @param ZendServiceLocatorInterface $serviceLocator
     * @return Service
     * @throws \InvalidArgumentException
     */
    public function createService(ZendServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');

        if (!isset($config['extractor'])) {
            throw new \InvalidArgumentException('Need not empty "extractor" param in config');
        }
        if (!isset($config['extractor']['__NAMESPACE__'])) {
            throw new \InvalidArgumentException('Need not empty "extractor.__NAMESPACE__" param in config');
        }

        $this->setNamespace($config['extractor']['__NAMESPACE__']);

        return $this;
    }

    /**
     * @param string $nameSpace
     * @return Service
     */
    public function setNamespace($nameSpace)
    {
        $this->extractorsNamespace = $nameSpace;
        return $this;
    }

    /**
     * @param ModelInterface $model
     * @param array $fields
     * @return array
     * @throws WrongFieldsException
     * @throws ZendBadMethodCallException
     * @throws \InvalidArgumentException
     */
    public function extract(ModelInterface $model, $fields = array())
    {
        if ($this->extractorsNamespace === null) {
            throw new \InvalidArgumentException('Need setup Namespace');
        }

        $extractFields = $fields;
        if (isset($fields['*'])) {
            $extractFields = array();
        }

        $parts = explode('\\', get_class($model));
        $extractorName = $this->extractorsNamespace . '\\' . array_pop($parts);

        if (!isset(static::$extractorInstances[$extractorName])) {
            if (!is_subclass_of($extractorName, '\GeometriaLab\Api\Stdlib\Extractor\Extractor')) {
                throw new ZendBadMethodCallException("Invalid extractor for model '" . get_class($model) . "'");
            }
            static::$extractorInstances[$extractorName] = new $extractorName;
        }

        $extractor = static::$extractorInstances[$extractorName];
        $data = $extractor->extract($model, $extractFields);
        $wrongFields = $extractor->getWrongFields();

        foreach ($data as $name => $field) {
            if ($field instanceof \GeometriaLab\Model\ModelInterface) {
                $parentExtractFields = isset($fields[$name]) ? $fields[$name] : array();
                $data[$name] = $this->extract($field, $parentExtractFields);
                $subWrongFields = $this->getWrongFields();
                if (!empty($subWrongFields)) {
                    $wrongFields[$name] = $subWrongFields;
                }
            }
        }

        $this->wrongFields = $wrongFields;

        return $data;
    }

    /**
     * Get wrong fields
     *
     * @return array
     */
    public function getWrongFields()
    {
        return $this->wrongFields;
    }
}
