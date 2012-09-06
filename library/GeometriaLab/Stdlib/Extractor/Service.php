<?php

namespace GeometriaLab\Stdlib\Extractor;

use Zend\ServiceManager\FactoryInterface as ZendFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface,
    Zend\ServiceManager\Exception\ServiceNotCreatedException as ZendServiceNotCreatedException,
    Zend\Stdlib\Exception\BadMethodCallException as ZendBadMethodCallException;

use GeometriaLab\Model\ModelInterface,
    GeometriaLab\Stdlib\Extractor\Extractor,
    GeometriaLab\Stdlib\Extractor\Fields;

class Service implements ZendFactoryInterface
{
    const EXTRACTOR_DIR = 'Extractor';

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

        return $this;
    }

    /**
     * @param ModelInterface $model
     * @param Fields $fields
     * @return array
     * @throws ZendBadMethodCallException
     * @throws ZendServiceNotCreatedException
     */
    public function extract(ModelInterface $model, Fields $fields = null)
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

        return static::$extractorInstances[$extractorName]->extract($model, $fields);
    }
}
