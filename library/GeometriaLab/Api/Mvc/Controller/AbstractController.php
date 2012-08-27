<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 27.07.12
 * Time: 15:28
 * To change this template use File | Settings | File Templates.
 */
namespace GeometriaLab\Api\Mvc\Controller;

use Zend\Mvc\Controller\AbstractController as ZendAbstractController;
use Zend\Http\PhpEnvironment\Response as ZendHttpResponse;
use Zend\Http\Request as ZendHttpRequest;
use Zend\Mvc\Exception as ZendMvcException;
use Zend\Mvc\MvcEvent as ZendMvcEvent;
use Zend\Stdlib\RequestInterface as ZendRequest;
use Zend\Stdlib\ResponseInterface as ZendResponse;

use GeometriaLab\Api\Mvc\Controller\Action\Params;

/**
 * Abstract Api Rest controller
 */
abstract class AbstractController extends ZendAbstractController
{
    /**
     * @var string
     */
    protected $eventIdentifier = __CLASS__;

    /**
     * Return list of resources
     *
     * @param Params $params
     * @return mixed
     */
    abstract public function getList(Params $params);


    /**
     * Return single resource
     *
     * @param Params $params
     * @return mixed
     */
    abstract public function get(Params $params);

    /**
     * Create a new resource
     *
     * @param Params $params
     * @return mixed
     */
    abstract public function create(Params $params);

    /**
     * Update an existing resource
     *
     * @param Params $params
     * @return mixed
     */
    abstract public function update(Params $params);

    /**
     * Delete an existing resource
     *
     * @param Params $params
     * @return mixed
     */
    abstract public function delete(Params $params);

    /**
     * Dispatch a request
     *
     * If the route match includes an "action" key, then this acts basically like
     * a standard action controller. Otherwise, it introspects the HTTP method
     * to determine how to handle the request, and which method to delegate to.
     *
     * @events dispatch.pre, dispatch.post
     * @param  ZendRequest $request
     * @param  null|ZendResponse $response
     * @return mixed|ZendResponse
     * @throws ZendMvcException\InvalidArgumentException
     */
    public function dispatch(ZendRequest $request, ZendResponse $response = null)
    {
        if (!$request instanceof ZendHttpRequest) {
            throw new ZendMvcException\InvalidArgumentException('Expected an HTTP request');
        }

        return parent::dispatch($request, $response);
    }

    /**
     * Handle the request
     *
     * @param  ZendMvcEvent $e
     * @return mixed
     * @throws ZendMvcException\DomainException if no route matches in event or invalid HTTP method
     */
    public function onDispatch(ZendMvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        if (!$routeMatch) {
            /**
             * @todo Determine requirements for when route match is missing.
             *       Potentially allow pulling directly from request metadata?
             */
            throw new ZendMvcException\DomainException('Missing route matches; unsure how to retrieve action');
        }

        $request = $e->getRequest();
        $action  = $routeMatch->getParam('action', false);
        if ($action) {
            // Handle arbitrary methods, ending in Action
            $method = static::getMethodFromAction($action);
            if (!method_exists($this, $method)) {
                $method = 'notFoundAction';
            }
            $return = $this->$method();
        } else {
            // RESTful methods

            $id = $routeMatch->getParam('id');
            if ($id === null) {
                $id = $request->getQuery()->get('id');
            }

            $subResource = $routeMatch->getParam('subResource');

            switch (strtolower($request->getMethod())) {
                case 'get':
                    if (null !== $id) {
                        if (null !== $subResource) {
                            $action = 'get' . ucfirst($subResource);
                        } else {
                            $action = 'get';
                        }
                    } else {
                        if (null !== $subResource) {
                            $action = 'get' . ucfirst($subResource) . 'List';
                        } else {
                            $action = 'getList';
                        }
                    }
                    break;
                case 'post':
                    if (null !== $id) {
                        throw new ZendMvcException\DomainException('Post is allowed on resources only');
                    }
                    if (null !== $subResource) {
                        $action = 'create' . ucfirst($subResource);
                    } else {
                        $action = 'create';
                    }
                    break;
                case 'put':
                    if (null === $id) {
                        throw new ZendMvcException\DomainException('Missing identifier');
                    }
                    if (null !== $subResource) {
                        throw new ZendMvcException\DomainException('Put is allowed on root resource object only');
                    }
                    $action = 'update';
                    break;
                case 'delete':
                    if (null === $id) {
                        throw new ZendMvcException\DomainException('Missing identifier');
                    }
                    if (null !== $subResource) {
                        throw new ZendMvcException\DomainException('Delete is allowed on root resource object only');
                    }
                    $action = 'delete';
                    break;
                default:
                    throw new ZendMvcException\DomainException('Invalid HTTP method!');
            }

            $routeMatch->setParam('action', $action);
        }

        $params = $this->getServiceLocator()->get('ParamsLoader')->getByRouteMatch($routeMatch);
        $return = $this->$action($params);

        // Emit post-dispatch signal, passing:
        // - return from method, request, response
        // If a listener returns a response object, return it immediately
        $e->setResult($return);
        return $return;
    }
}
