<?php

namespace GeometriaLab\Api\Mvc;

use GeometriaLab\Api\Mvc\Controller\Action\Params\Listener as ParamsListener,
    GeometriaLab\Api\Mvc\View\Http\CreateApiModelListener,
    GeometriaLab\Api\Mvc\View\Http\HandleExceptionStrategy,
    GeometriaLab\Api\Mvc\View\Strategy\RenderStrategy;

use Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface,
    Zend\EventManager\EventManagerInterface as ZendEventManagerInterface,
    Zend\Mvc\ModuleRouteListener as ZendModuleRouteListener,
    Zend\ServiceManager\ServiceManager as ZendServiceManager,
    Zend\ServiceManager\ServiceManagerAwareInterface as ZendServiceManagerAwareInterface;

class Listener implements ZendListenerAggregateInterface, ZendServiceManagerAwareInterface
{
    /**
     * Listeners we've registered
     *
     * @var array
     */
    protected $listeners = array();
    /**
     * Service manager
     *
     * @var ZendServiceManager
     */
    protected $serviceManager;
    /**
     * Default dispatchable listeners
     *
     * @var \Zend\Stdlib\PriorityQueue[]
     */
    protected $defaultListeners = array();

    /**
     * @param ZendServiceManager $serviceManager
     */
    public function __construct(ZendServiceManager $serviceManager)
    {
        $this->setServiceManager($serviceManager);
    }

    /**
     * Attach listeners
     *
     * @param  ZendEventManagerInterface $events
     * @return void
     */
    public function attach(ZendEventManagerInterface $events)
    {
        // Detach default listeners
        $eventManager = $this->serviceManager->get('Application')->getEventManager();
        /* @var \Zend\Mvc\View\Http\ViewManager $viewManager */
        $viewManager = $this->serviceManager->get('ViewManager');
        $viewManager->getRouteNotFoundStrategy()->detach($eventManager);
        $viewManager->getExceptionStrategy()->detach($eventManager);
        $viewManager->getRendererStrategy()->detach($viewManager->getView()->getEventManager());

        /* @var \Zend\EventManager\SharedEventManager $sharedEvents */
        $sharedEvents = $eventManager->getSharedManager();
        $defaultEvents = $sharedEvents->getEvents('Zend\Stdlib\DispatchableInterface');
        foreach ($defaultEvents as $event) {
            $this->defaultListeners[$event] = $sharedEvents->getListeners('Zend\Stdlib\DispatchableInterface', $event);
        }
        $sharedEvents->clearListeners('Zend\Stdlib\DispatchableInterface');


        // Attach Mvc listeners
        $this->listeners[] = new ZendModuleRouteListener();
        $this->listeners[] = new RenderStrategy($this->serviceManager);
        $this->listeners[] = new ParamsListener();
        $this->listeners[] = new CreateApiModelListener();
        $this->listeners[] = new HandleExceptionStrategy();

        foreach($this->listeners as $listener) {
            $events->attachAggregate($listener);
        }
    }

    /**
     * Detach listeners
     *
     * @param  ZendEventManagerInterface $events
     * @return void
     */
    public function detach(ZendEventManagerInterface $events)
    {
        // Detach mvc listeners
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }

        // Attach default listeners
        $eventManager = $this->serviceManager->get('Application')->getEventManager();
        /* @var \Zend\EventManager\SharedEventManager $sharedEvents */
        $viewManager = $this->serviceManager->get('ViewManager');
        $viewManager->getRouteNotFoundStrategy()->attach($eventManager);
        $viewManager->getExceptionStrategy()->attach($eventManager);
        $viewManager->getRendererStrategy()->attach($viewManager->getView()->getEventManager());
        /* @var \Zend\EventManager\SharedEventManager $sharedEvents */
        $sharedEvents = $eventManager->getSharedManager();

        foreach ($this->defaultListeners as $event => $listener) {
            foreach ($listener->toArray($listener::EXTR_BOTH) as $itemData) {
                $sharedEvents->attach('Zend\Stdlib\DispatchableInterface', $event, $itemData['data']->getCallback(), $itemData['priority']);
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
}
