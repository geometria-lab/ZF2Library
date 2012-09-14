<?php

namespace GeometriaLab\Api\Mvc\Router\Http;

use Zend\Stdlib\ArrayUtils as ZendArrayUtils,
    Zend\Stdlib\RequestInterface as ZendRequestInterface,
    Zend\Mvc\Router\Exception as ZendRouterException,
    Zend\Mvc\Router\Http\RouteMatch as ZendRouteMatch,
    Zend\Mvc\Router\Exception\RuntimeException as ZendRuntimeException,
    Zend\Mvc\Exception\DomainException as ZendDomainException;

use GeometriaLab\Api\Mvc\View\Strategy\ApiStrategy;

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
     * @var string
     */
    protected $subResource;

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
     * @throws ZendDomainException
     */
    public function match(ZendRequestInterface $request, $pathOffset = null)
    {
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        $pathParts = $this->getPathParts($request);
        $routeMatch = new ZendRouteMatch(array());

        $namespace = $this->getNamespace($pathParts);
        $routeMatch->setParam('__NAMESPACE__', $namespace);

        $controller = $this->getController($pathParts);
        $routeMatch->setParam('controller', $controller);

        $id = $this->getId($pathParts);
        $routeMatch->setParam('id', $id);

        $action = $this->getAction($routeMatch, $request);
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
     * @param ZendRequestInterface $request
     * @return string
     */
    protected function getPathParts(ZendRequestInterface $request)
    {
        $uri  = $request->getUri();
        $path = $uri->getPath();
        $path = trim($path, '/');

        foreach (array(ApiStrategy::FORMAT_JSON, ApiStrategy::FORMAT_XML) as $format) {
            $needle = '.' . $format;
            if (strpos($path, $needle) === strlen($path) - strlen($needle)) {
                $request->setMetadata('format', $format);
                $path = substr($path, 0, -strlen($needle));
                break;
            }
        }

        return explode('/', $path);
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
     * @param array $pathParts
     * @return null|string
     */
    protected function getId($pathParts)
    {
        $id = null;

        if (isset($pathParts[3])) {
            if ($this->isValidId($pathParts[2])) {
                $id = $pathParts[2];
                $this->subResource = $pathParts[3];
            } else {
                return null;
            }
        } else if (isset($pathParts[2])) {
            if ($this->isValidId($pathParts[2])) {
                $id = $pathParts[2];
            } else {
                $this->subResource = $pathParts[2];
            }
        }

        return $id;
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
     * @param ZendRouteMatch $routeMatch
     * @param ZendRequestInterface $request
     * @return null|string
     * @throws ZendDomainException
     */
    protected function getAction(ZendRouteMatch $routeMatch, ZendRequestInterface $request)
    {
        switch (strtolower($request->getMethod())) {
            case 'get':
                if (null !== $routeMatch->getParam('id')) {
                    if (null !== $this->subResource) {
                        $action = 'get' . ucfirst($this->subResource);
                    } else {
                        $action = 'get';
                    }
                } else {
                    if (null !== $this->subResource) {
                        $action = 'get' . ucfirst($this->subResource) . 'List';
                    } else {
                        $action = 'getList';
                    }
                }
                break;
            case 'post':
                if (null !== $routeMatch->getParam('id')) {
                    throw new ZendDomainException('Post is allowed on resources only');
                }
                if (null !== $this->subResource) {
                    $action = 'create' . ucfirst($this->subResource);
                } else {
                    $action = 'create';
                }
                break;
            case 'put':
                if (null === $routeMatch->getParam('id')) {
                    throw new ZendDomainException('Missing identifier');
                }
                if (null !== $this->subResource) {
                    throw new ZendDomainException('Put is allowed on root resource object only');
                }
                $action = 'update';
                break;
            case 'delete':
                if (null === $routeMatch->getParam('id')) {
                    throw new ZendDomainException('Missing identifier');
                }
                if (null !== $this->subResource) {
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
