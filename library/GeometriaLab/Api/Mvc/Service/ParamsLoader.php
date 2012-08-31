<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 30.07.12
 * Time: 16:15
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Api\Mvc\Service;

use Zend\Mvc\Router\RouteMatch as ZendRouteMatch;

/**
 *
 */
class ParamsLoader
{
    /**
     *
     */
    const PARAM_DIR = 'Param';

    /**
     * @param \Zend\Mvc\Router\RouteMatch $routeMatch
     * @return mixed
     */
    public function getByRouteMatch(ZendRouteMatch $routeMatch)
    {
        $parts = explode('\\', $routeMatch->getParam('__NAMESPACE__'));
        array_pop($parts);
        $paramClassName = implode('\\', $parts) . '\\' . self::PARAM_DIR . '\\' . $routeMatch->getParam('__CONTROLLER__') . '\\' . ucfirst($routeMatch->getParam('action'));

        return new $paramClassName;
    }
}