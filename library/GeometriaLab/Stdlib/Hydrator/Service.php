<?php

namespace GeometriaLab\Stdlib\Hydrator;

use Zend\ServiceManager\FactoryInterface as ZendFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface,
    Zend\ServiceManager\Exception\ServiceNotCreatedException as ZendServiceNotCreatedException,
    Zend\Stdlib\Exception\BadMethodCallException as ZendBadMethodCallException;

use GeometriaLab\Model\ModelInterface,
    GeometriaLab\Api\Stdlib\Hydrator,
    GeometriaLab\Stdlib\Hydrator\Fields;

class Service implements ZendFactoryInterface
{
    const HYDRATOR_DIR = 'Hydrator';

    /**
     * @var Hydrator[]
     */
    static private $hydratorInstances;

    /**
     * @var string
     */
    private $hydratorPath;

    /**
     * @param ZendServiceLocatorInterface $serviceLocator
     * @return Service
     */
    public function createService(ZendServiceLocatorInterface $serviceLocator)
    {
        $controllerNameSpace = $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch()->getParam('__NAMESPACE__');
        $parts = explode('\\', $controllerNameSpace);
        array_pop($parts);
        $this->hydratorPath = implode('\\', $parts) . '\\' . self::HYDRATOR_DIR;

        return $this;
    }

    /**
     * @param ModelInterface $model
     * @param Fields $fields
     * @return array
     * @throws ZendBadMethodCallException
     * @throws ZendServiceNotCreatedException
     */
    public function hydrate(ModelInterface $model, Fields $fields = null)
    {
        if ($this->hydratorPath === null) {
            throw new ZendServiceNotCreatedException('Get me from Service Manager');
        }

        $parts = explode('\\', get_class($model));
        $hydratorName = $this->hydratorPath . '\\' . array_pop($parts);

        if (!isset(static::$hydratorInstances[$hydratorName])) {
            if (is_subclass_of($hydratorName, 'Hydrator')) {
                throw new ZendBadMethodCallException("Invalid hydrator for model '" . get_class($model) . "'");
            }
            static::$hydratorInstances[$hydratorName] = new $hydratorName;
        }

        return static::$hydratorInstances[$hydratorName]->extract($model, $fields);
    }
}
