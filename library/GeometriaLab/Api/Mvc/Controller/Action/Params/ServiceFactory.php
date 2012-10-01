<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 30.07.12
 * Time: 16:15
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Api\Mvc\Controller\Action\Params;

use Zend\Mvc\Router\RouteMatch as ZendRouteMatch,
    Zend\ServiceManager\FactoryInterface as ZendFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface;

class ServiceFactory implements ZendFactoryInterface
{
    /**
     * @var string
     */
    private $paramsNamespace;

    /**
     * @param ZendServiceLocatorInterface $serviceLocator
     * @return Params
     * @throws \InvalidArgumentException
     */
    public function createService(ZendServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');

        if (!isset($config['params'])) {
            throw new \InvalidArgumentException('Need not empty "params" param in config');
        }
        if (!isset($config['params']['__NAMESPACE__'])) {
            throw new \InvalidArgumentException('Need not empty "params.__NAMESPACE__" param in config');
        }

        $this->setNamespace($config['params']['__NAMESPACE__']);

        /* @var ZendRouteMatch $routeMatch */
        $routeMatch= $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch();

        return $this->getByRouteMatch($routeMatch);
    }

    /**
     * @param string $nameSpace
     * @return ServiceFactory
     */
    public function setNamespace($nameSpace)
    {
        $this->paramsNamespace = $nameSpace;
        return $this;
    }

    /**
     * @param ZendRouteMatch $routeMatch
     * @return Params
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function getByRouteMatch(ZendRouteMatch $routeMatch)
    {
        if ($this->paramsNamespace === null) {
            throw new \InvalidArgumentException('Need setup Namespace');
        }

        $paramClassName = $this->paramsNamespace . '\\' . $routeMatch->getParam('__CONTROLLER__') . '\\' . ucfirst($routeMatch->getParam('action'));

        if (!class_exists($paramClassName)) {
            throw new \RuntimeException("Class '$paramClassName' doesn't exist");
        }

        return new $paramClassName();
    }
}