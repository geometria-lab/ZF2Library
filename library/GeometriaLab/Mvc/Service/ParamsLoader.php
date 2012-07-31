<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 30.07.12
 * Time: 16:15
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Mvc\Service;

use Zend\Mvc\Router\RouteMatch;

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
    public function getByRouteMatch(RouteMatch $routeMatch)
    {
        $parts = explode('\\', $routeMatch->getParam('__NAMESPACE__'));
        $paramClassName = $parts[0] . '\\' . self::PARAM_DIR . '\\' . $this->event->getRouteMatch()->getParam('__CONTROLLER__') . '\\' . ucfirst($routeMatch->getParam('action'));

        return new $paramClassName;
    }
}