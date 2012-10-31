<?php

namespace GeometriaLab\Api\Mvc\View\Strategy;

use GeometriaLab\Api\Exception\InvalidFormatException;

use Zend\View\ViewEvent as ZendViewEvent,
    Zend\View\Renderer\RendererInterface as ZendRendererInterface,
    Zend\Mvc\MvcEvent as ZendMvcEvent,
    Zend\EventManager\EventManagerInterface as ZendEventManagerInterface,
    Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface,
    Zend\ServiceManager\ServiceManager as ZendServiceManager,
    Zend\ServiceManager\ServiceManagerAwareInterface as ZendServiceManagerAwareInterface;

class RenderStrategy implements ZendListenerAggregateInterface, ZendServiceManagerAwareInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    /**
     * @var array
     */
    protected $renderers = array();
    /**
     * @var array
     */
    protected $contentTypes = array();
    /**
     * @var string
     */
    protected $defaultFormat;
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
        $view = $this->serviceManager->get('Zend\View\View');
        $events = $view->getEventManager();

        $this->listeners[] = $events->attach(ZendMvcEvent::EVENT_ROUTE, array($this, 'validateFormat'), -1);
        $this->listeners[] = $events->attach(ZendViewEvent::EVENT_RENDERER, array($this, 'selectRenderer'), 100);
        $this->listeners[] = $events->attach(ZendViewEvent::EVENT_RESPONSE, array($this, 'injectResponse'), 100);
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
     * Set configs
     *
     * @param ZendMvcEvent $e
     * @return RenderStrategy
     * @throws \InvalidArgumentException
     */
    public function validateFormat(ZendMvcEvent $e)
    {
        $config = $this->serviceManager->get('Config');

        if (!isset($config['view_render_strategy'])) {
            throw new \InvalidArgumentException('Need "view_render_strategy" param in config');
        }
        if (!$config['view_render_strategy']['renderers']){
            throw new \InvalidArgumentException('Need "view_render_strategy.renderers" param in config');
        }
        if (!$config['view_render_strategy']['default_format']){
            throw new \InvalidArgumentException('Need "view_render_strategy.default_format" param in config');
        }

        $this->createRenderers($config['view_render_strategy']['renderers']);
        $this->setDefaultFormat($config['view_render_strategy']['default_format']);

        return $this;
    }

    /**
     * Get renderers
     *
     * @return array
     */
    public function getRenderers()
    {
        return $this->renderers;
    }

    /**
     * Does it have renderer for format $format?
     *
     * @param string $format
     * @return bool
     */
    public function hasRenderer($format)
    {
        return isset($this->renderers[$format]);
    }

    /**
     * @param string $format
     * @param ZendRendererInterface $renderer
     * @param $contentType
     * @throws \InvalidArgumentException
     */
    public function setRenderer($format, ZendRendererInterface $renderer, $contentType)
    {
        if ($this->hasRenderer($format)) {
            throw new \InvalidArgumentException("Renderer for format '$format' already exist");
        }

        $this->renderers[$format] = $renderer;
        $this->contentTypes[$format] = $contentType;
    }

    /**
     * Get renderer for format $format
     *
     * @param string $format
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getRenderer($format)
    {
        if (!$this->hasRenderer($format)) {
            throw new \InvalidArgumentException("Renderer for format '$format' doesn't present");
        }

        return $this->renderers[$format];
    }

    /**
     * Set default renderer
     *
     * @param string $format
     * @return RenderStrategy
     * @throws \InvalidArgumentException
     */
    public function setDefaultFormat($format)
    {
        if (!$this->hasRenderer($format)) {
            throw new \InvalidArgumentException("Renderer for format '$format' doesn't present");
        }

        $this->defaultFormat = $format;

        return $this;
    }

    /**
     * Get default format
     *
     * @return string
     */
    public function getDefaultFormat()
    {
        return $this->defaultFormat;
    }

    /**
     * Get content type for format $format
     *
     * @param string $format
     * @return string|callable
     */
    public function getContentType($format)
    {
        return $this->contentTypes[$format];
    }

    /**
     * Select renderer
     *
     * @param ZendViewEvent $e
     * @return ZendRendererInterface
     */
    public function selectRenderer(ZendViewEvent $e)
    {
        $format = $e->getRequest()->getMetadata('format');

        if (!$this->hasRenderer($format)) {
            $format = $this->getDefaultFormat();
        }

        return $this->getRenderer($format);
    }

    /**
     * Inject content to response
     *
     * @param ZendViewEvent $e
     * @throws InvalidFormatException
     * @throws \RuntimeException
     */
    public function injectResponse(ZendViewEvent $e)
    {
        $response = $e->getResponse();
        $result = $e->getResult();
        /* @var \Zend\Http\Headers $headers */
        $headers = $response->getHeaders();
        $format = $e->getRequest()->getMetadata('format');

        if ($format === null) {
            $format = $this->getDefaultFormat();
        }

        if (!$this->hasRenderer($format)) {
            throw new InvalidFormatException("Format '$format' is not supported");
        }

        $contentType = $this->getContentType($format);

        if (is_string($contentType)) {
            $headers->addHeaderLine($contentType);
        } elseif (is_callable($contentType)) {
            call_user_func($contentType, $e);
        } else {
            throw new \RuntimeException('Content type must be string or callable');
        }

        $response->setContent($result);
    }

    /**
     * Create renderers
     *
     * @param array $renderers
     * @throws \InvalidArgumentException
     */
    protected function createRenderers(array $renderers)
    {
        foreach ($renderers as $format => $params) {
            if (!isset($params['class_name'])) {
                throw new \InvalidArgumentException('Need "class_name" param in "' . $format .'"');
            }
            if (!isset($params['content_type'])) {
                throw new \InvalidArgumentException('Need "content_type" param in "' . $format .'"');
            }

            $renderer = new $params['class_name'];
            $this->setRenderer($format, $renderer, $params['content_type']);
        }
    }
}
