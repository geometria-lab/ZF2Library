<?php

namespace GeometriaLab\Api\Mvc;

use GeometriaLab\Api\Mvc\Controller\Action\Params\Listener as ParamsListener,
    GeometriaLab\Api\Mvc\View\Http\CreateApiModelListener,
    GeometriaLab\Api\Mvc\View\Http\HandleExceptionStrategy,
    GeometriaLab\Api\Mvc\View\Strategy\ApiStrategy;

use Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface,
    Zend\EventManager\EventManagerInterface as ZendEvents,
    Zend\Mvc\MvcEvent as ZendMvcEvent,
    Zend\Mvc\ModuleRouteListener as ZendModuleRouteListener;

class Listener implements ZendListenerAggregateInterface
{
    /**
     * Listeners we've registered
     *
     * @var array
     */
    protected $listeners = array();

    /**
     * Attach listeners
     *
     * @param  ZendEvents $events
     * @return void
     */
    public function attach(ZendEvents $events)
    {
        $this->listeners[] = new ZendModuleRouteListener();
        $this->listeners[] = new ApiStrategy();
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
     * @param  ZendEvents $events
     * @return void
     */
    public function detach(ZendEvents $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function detachDefaultListeners(ZendMvcEvent $events)
    {
        $sm = $events->getApplication()->getServiceManager();
        $eventManager = $events->getApplication()->getEventManager();

        $viewManager = $sm->get('ViewManager');
        $viewManager->getRouteNotFoundStrategy()->detach($eventManager);
        $viewManager->getExceptionStrategy()->detach($eventManager);
        $sharedEvents = $eventManager->getSharedManager();
        $sharedEvents->clearListeners('Zend\Stdlib\DispatchableInterface');
    }
}
