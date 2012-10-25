<?php

namespace GeometriaLab\Permissions\Acl;

use Zend\Mvc\MvcEvent as ZendMvcEvent,
    Zend\EventManager\EventManagerInterface as ZendEventManagerInterface,
    Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface,
    Zend\ServiceManager\ServiceManager as ZendServiceManager,
    Zend\ServiceManager\ServiceManagerAwareInterface as ZendServiceManagerAwareInterface;

class Listener implements ZendListenerAggregateInterface, ZendServiceManagerAwareInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    /**
     * Service Manager
     *
     * @var ZendServiceManager
     */
    protected $serviceManager;

    /**
     * @param ZendServiceManager $serviceManager
     */
    public function __construct(ZendServiceManager $serviceManager)
    {
        $this->setServiceManager($serviceManager);
    }

    /**
     * @param \Zend\EventManager\EventManagerInterface $events
     * @param int $priority
     */
    public function attach(ZendEventManagerInterface $events, $priority = 1)
    {
        $sharedEvents = $events->getSharedManager();
        $this->listeners[] = $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', ZendMvcEvent::EVENT_DISPATCH, array($this, 'checkAccess'), 101);
    }

    /**
     * @param \Zend\EventManager\EventManagerInterface $events
     */
    public function detach(ZendEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Set serviceManager
     *
     * @param \Zend\ServiceManager\ServiceManager $serviceManager
     */
    public function setServiceManager(ZendServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Check access
     *
     * @param ZendMvcEvent $e
     */
    public function checkAccess(ZendMvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        /* @var \Zend\Permissions\Acl\Acl $acl */
        $acl = $serviceManager->get('Acl');
        /* @var \Zend\Mvc\Router\RouteMatch $routeMatch */
        $routeMatch = $e->getRouteMatch();
        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');
        /* @var \Application\Service\UserService $userService */
        $userService = $serviceManager->get('UserService');
        $currentUser = $userService->getCurrent();
        // @TODO Get current user's role
        if (!$acl->isAllowed('moderator', $controller, $action)) {
            //throw new AccessDeniedException();
        }
    }
}
