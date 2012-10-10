<?php

namespace GeometriaLab\Api\Mvc\Router\Http;

use Zend\Stdlib\ArrayUtils as ZendArrayUtils,
    Zend\Stdlib\RequestInterface as ZendRequestInterface,
    Zend\Mvc\Router\Exception as ZendRouterException,
    Zend\Mvc\Router\Http\RouteMatch as ZendRouteMatch,
    Zend\Mvc\Router\Exception\RuntimeException as ZendRuntimeException,
    Zend\Mvc\Exception\DomainException as ZendDomainException;

/**
 *
 */
class Api implements \Zend\Mvc\Router\Http\RouteInterface
{
    const API_MODULE_DIR = 'Api';

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
     * @param  array|\Traversable $options
     * @throws \Zend\Mvc\Router\Exception\InvalidArgumentException
     * @return Api
     */
    public static function factory($options = array())
    {
        if ($options instanceof \Traversable) {
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
     * @param  ZendRequestInterface $request
     * @param  string|null $pathOffset
     * @return ZendRouteMatch
     */
    public function match(ZendRequestInterface $request, $pathOffset = null)
    {
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        $id = null;
        $subResource = null;
        $routeMatch = new ZendRouteMatch(array());
        $method = $request->getMethod();

        if (APPLICATION_ENV == 'development') {
            $method = $request->getQuery('_method', $method);
        }

        $uri  = $request->getUri();
        $path = trim($uri->getPath(), '/');
        $pathParts = explode('/', $path);

        // 1. Get format
        $lastPart = end($pathParts);
        $formatPart = explode('.', $lastPart, 2);
        if (count($formatPart) == 2) {
            $pathParts[count($pathParts) - 1] = $formatPart[0];
            $format = $formatPart[1];
            $request->setMetadata('format', $format);
        }

        // 2. Get id and sub resource
        if (isset($pathParts[3])) {
            if ($this->isValidId($pathParts[2])) {
                $id = $pathParts[2];
                $subResource = $pathParts[3];
            } else {
                return null;
            }
        } elseif (isset($pathParts[2])) {
            if ($this->isValidId($pathParts[2])) {
                $id = $pathParts[2];
            } else {
                $subResource = $pathParts[2];
            }
        }

        if ($subResource !== null) {
            $subResource = $this->prepareSubResource($subResource);
        }

        $routeMatch->setParam('id', $id);

        // 3. Get namespace
        $namespace = $this->getNamespace($pathParts);
        $routeMatch->setParam('__NAMESPACE__', $namespace);

        // 4. Get controller
        $controller = $this->getController($pathParts);
        $routeMatch->setParam('controller', $controller);

        // 5. Get action
        $action = $this->getAction($id, $method, $subResource);
        $routeMatch->setParam('action', $action);

        return $routeMatch;
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
        throw new ZendRuntimeException("Not implemented");
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

    /**
     * Prepare sub resource
     *
     * @param $subResource
     * @return string
     * @throws ZendDomainException
     */
    protected function prepareSubResource($subResource)
    {
        if (!preg_match('/^[\w][\w-]+$/', $subResource)) {
            throw new ZendDomainException('Invalid sub resource name');
        }

        return str_replace(' ' , '', ucwords(str_replace('-', ' ', $subResource)));
    }

    /**
     * @param array $pathParts
     * @return null|string
     */
    protected function getNamespace($pathParts)
    {
        if (empty($pathParts[0]) || !preg_match('/^v(\d+)$/', $pathParts[0], $matches)) {
            return null;
        }

        return self::API_MODULE_DIR . '\\' . ucfirst($matches[0]) . '\Controller';
    }

    /**
     * @param array $pathParts
     * @return null|string
     */
    protected function getController($pathParts)
    {
        if (empty($pathParts[1]) || !preg_match('/^([\w-]+)$/', $pathParts[1], $matches)) {
            return null;
        }

        return ucfirst($matches[1]);
    }

    /**
     * @param $id
     * @param $method
     * @param $subResource
     * @return string
     * @throws ZendDomainException
     *
     * @todo Must throws bad request
     */
    protected function getAction($id, $method, $subResource)
    {
        switch (strtolower($method)) {
            case 'get':
                if (null !== $id) {
                    if (null !== $subResource) {
                        $action = 'get' . $subResource;
                    } else {
                        $action = 'get';
                    }
                } else {
                    if (null !== $subResource) {
                        $action = 'get' . $subResource . 'List';
                    } else {
                        $action = 'getList';
                    }
                }
                break;
            case 'post':
                if (null !== $subResource) {
                    if (null === $id) {
                        throw new ZendDomainException('Missing identifier');
                    }
                    $action = lcfirst($subResource);
                } else {
                    if (null !== $id) {
                        throw new ZendDomainException('Post is allowed on resources only');
                    }
                    $action = 'create';
                }
                break;
            case 'put':
                if (null === $id) {
                    throw new ZendDomainException('Missing identifier');
                }
                if (null !== $subResource) {
                    throw new ZendDomainException('Put is allowed on root resource object only');
                }
                $action = 'update';
                break;
            case 'delete':
                if (null === $id) {
                    throw new ZendDomainException('Missing identifier');
                }
                if (null !== $subResource) {
                    throw new ZendDomainException('Delete is allowed on root resource object only');
                }
                $action = 'delete';
                break;
            default:
                throw new ZendDomainException('Invalid HTTP method!');
        }
        
        return $action;
    }
}
