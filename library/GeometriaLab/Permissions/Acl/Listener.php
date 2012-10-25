<?php

namespace GeometriaLab\Permissions\Acl;

use Application\Model\Guest;

use Zend\Mvc\MvcEvent as ZendMvcEvent,
    Zend\EventManager\EventManagerInterface as ZendEventManagerInterface,
    Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface;

class Listener implements ZendListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @param ZendEventManagerInterface $events
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
