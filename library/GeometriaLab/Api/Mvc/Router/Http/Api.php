<?php

namespace GeometriaLab\Api\Mvc\Router\Http;

use Traversable;
use Zend\Stdlib\ArrayUtils as ZendArrayUtils;
use Zend\Stdlib\RequestInterface as ZendRequest;
use Zend\Mvc\Router\Exception as ZendRouterException;
use Zend\Mvc\Router\Http\RouteMatch as ZendRouteMatch;

/**
 *
 */
class Api implements \Zend\Mvc\Router\Http\RouteInterface
{
    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults;

    /**
     * Constraints
     *
     * @var
     */
    protected $constraints;

    /**
     * List of assembled parameters.
     *
     * @var array
     */
    protected $assembledParams = array();

    /**
     * Create a new regex route.
     *
     * @param  array  $constraints
     * @param  array  $defaults
     */
    public function __construct(array $constraints = array(), array $defaults = array())
    {
        $this->constraints = $constraints;
        $this->defaults = $defaults;
    }

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    Route::factory()
     * @param  array|Traversable $options
     * @throws \Zend\Mvc\Router\Exception\InvalidArgumentException
     * @return Api
     */
    public static function factory($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ZendArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new ZendRouterException\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        if (!isset($options['constraints'])) {
            $options['constraints'] = array();
        }

        if (!isset($options['defaults'])) {
            $options['defaults'] = array();
        }

        return new static($options['constraints'], $options['defaults']);
    }

    /**
     * match(): defined by RouteInterface interface.
     *
     * @see    Route::match()
     * @param  ZendRequest $request
     * @param  string|null $pathOffset
     * @return ZendRouteMatch
     */
    public function match(ZendRequest $request, $pathOffset = null)
    {
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        $uri  = $request->getUri();
        $path = $uri->getPath();
        $path = trim($path, '/');

        foreach (array(\GeometriaLab\Api\View\Strategy\ApiStrategy::FORMAT_JSON, \GeometriaLab\Api\View\Strategy\ApiStrategy::FORMAT_XML) as $format) {
            $needle = '.' . $format;
            if (strpos($path, $needle) === strlen($path) - strlen($needle)) {
                $request->setMetadata('format', $format);
                $path = substr($path, 0, -strlen($needle));
                break;
            }
        }

        $pathParts = explode('/', $path);

        if (empty($pathParts[0]) || !preg_match('/^v(\d+)$/', $pathParts[0], $matches)) {
            return null;
        }
        $params['__NAMESPACE__'] = ucfirst($matches[0]) . '\Controller';

        if (empty($pathParts[1]) || !preg_match('/^([\w-]+)$/', $pathParts[1], $matches)) {
            return null;
        }
        $params['controller'] = ucfirst($matches[1]);

        if (isset($pathParts[3])) {
            if ($this->isValidId($pathParts[2])) {
                $params['id'] = $pathParts[2];
                $params['subResource'] = $pathParts[3];
            } else {
                return null;
            }
        } else if (isset($pathParts[2])) {
            if ($this->isValidId($pathParts[2])) {
                $params['id'] = $pathParts[2];
            } else {
                $params['subResource'] = $pathParts[2];
            }
        }

        return new ZendRouteMatch($params);
    }

    /**
     * assemble(): Defined by RouteInterface interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @throws \Zend\Mvc\Router\Exception\RuntimeException
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
        throw new \Zend\Mvc\Router\Exception\RuntimeException;
    }

    /**
     * getAssembledParams(): defined by RouteInterface interface.
     *
     * @see    Route::getAssembledParams
     * @return array
     */
    public function getAssembledParams()
    {
        return $this->assembledParams;
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isValidId($value)
    {
        return is_numeric($value) || preg_match('/^[a-hA-H0-9]{24}$/', $value);
    }
}
