<?php

namespace GeometriaLab\Api\Mvc\View\Strategy;

use GeometriaLab\Api\Mvc\View\Renderer\XmlRenderer;

use Zend\View\ViewEvent as ZendViewEvent,
    Zend\View\Renderer\JsonRenderer as ZendJsonRenderer;

use Zend\EventManager\EventManagerInterface as ZendEventManagerInterface,
    Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface;

/**
 *
 */
class ApiStrategy implements ZendListenerAggregateInterface
{
    /**
     *
     */
    const FORMAT_JSON = 'json';

    /**
     *
     */
    const FORMAT_XML = 'xml';

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var \Zend\View\Renderer\JsonRenderer
     */
    protected $jsonRenderer;

    /**
     * @var XmlRenderer
     */
    protected $xmlRenderer;

    /**
     * @param \Zend\View\Renderer\JsonRenderer            $jsonRenderer
     * @param XmlRenderer $xmlRenderer
     */
    public function __construct(ZendJsonRenderer $jsonRenderer, XmlRenderer $xmlRenderer)
    {
        $this->jsonRenderer = $jsonRenderer;
        $this->xmlRenderer = $xmlRenderer;
    }

    /**
     * @return array
     */
    public function getFormats()
    {
        return array(
            self::FORMAT_JSON,
            self::FORMAT_XML
        );
    }

    /**
     * @param \Zend\EventManager\EventManagerInterface $events
     * @param int                                      $priority
     */
    public function attach(ZendEventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ZendViewEvent::EVENT_RENDERER, array($this, 'selectRenderer'), $priority);
        $this->listeners[] = $events->attach(ZendViewEvent::EVENT_RESPONSE, array($this, 'injectResponse'), $priority);
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
     * @param \Zend\View\ViewEvent $e
     * @return \GeometriaLab\Api\Mvc\View\Renderer\XmlRenderer|\Zend\View\Renderer\JsonRenderer
     */
    public function selectRenderer(ZendViewEvent $e)
    {
        $format = $e->getRequest()->getMetadata('format');

        if ($format == 'xml') {
            return $this->xmlRenderer;
        } else {
            return $this->jsonRenderer;
        }
    }

    /**
     * @param \Zend\View\ViewEvent $e
     */
    public function injectResponse(ZendViewEvent $e)
    {
        $renderer = $e->getRenderer();

        $response = $e->getResponse();
        $result   = $e->getResult();
        $headers = $response->getHeaders();

        if ($renderer == $this->jsonRenderer) {
            if (!is_string($result)) {
                // We don't have a string, and thus, no JSON
                return;
            }

            if ($this->jsonRenderer->hasJsonpCallback()) {
                $headers->addHeaderLine('content-type', 'application/javascript');
            } else {
                $headers->addHeaderLine('content-type', 'application/json');
            }
        } else if ($renderer == $this->xmlRenderer) {
            $headers->addHeaderLine('content-type', 'application/xml');
        } else {
            return;
        }

        $response->setContent($result);
    }
}
