<?php

namespace GeometriaLab\Api\Mvc\View\Strategy;

use GeometriaLab\Api\Exception\InvalidFormatException;

use Zend\View\ViewEvent as ZendViewEvent,
    Zend\View\Renderer\RendererInterface as ZendRendererInterface,
    Zend\EventManager\EventManagerInterface as ZendEventManagerInterface,
    Zend\EventManager\ListenerAggregateInterface as ZendListenerAggregateInterface,
    Zend\ServiceManager\ServiceManager as ZendServiceManager;

class ApiStrategy implements ZendListenerAggregateInterface
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
    protected $defaultRenderer;

    /**
     * @param \Zend\EventManager\EventManagerInterface $events
     * @param int $priority
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
     * Set configs
     *
     * @param array $config
     * @return ApiStrategy
     * @throws \InvalidArgumentException
     */
    public function setConfig($config)
    {
        if (!isset($config['view_strategy'])) {
            throw new \InvalidArgumentException('Need "view_strategy" param in config');
        }
        if (!$config['view_strategy']['renderers']){
            throw new \InvalidArgumentException('Need "view_strategy.renderers" param in config');
        }
        if (!$config['view_strategy']['default_renderer']){
            throw new \InvalidArgumentException('Need "view_strategy.default_renderer" param in config');
        }

        $this->createRenderers($config['view_strategy']['renderers']);
        $this->setDefaultRenderer($config['view_strategy']['default_renderer']);

        return $this;
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

            $renderer = new $params['class_name'];
            $this->setRenderer($format, $renderer);

            if (isset($params['content_type'])) {
                $this->setContentType($format, $params['content_type']);
            }
        }
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
     * Set renderer
     *
     * @param string $format
     * @param ZendRendererInterface $renderer
     * @throws \InvalidArgumentException
     */
    public function setRenderer($format, ZendRendererInterface $renderer)
    {
        if ($this->hasRenderer($format)) {
            throw new \InvalidArgumentException("Renderer for format '$format' already exist");
        }

        $this->renderers[$format] = $renderer;
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
     * @return ApiStrategy
     */
    public function setDefaultRenderer($format)
    {
        $this->defaultRenderer = $this->getRenderer($format);
        return $this;
    }

    /**
     * Get default renderer
     *
     * @return string
     */
    public function getDefaultRenderer()
    {
        return $this->defaultRenderer;
    }

    /**
     * Does it have content type for format $format?
     *
     * @param string $format
     * @return bool
     */
    public function hasContentType($format)
    {
        return isset($this->contentTypes[$format]);
    }

    /**
     * Set content type for format $format
     *
     * @param string $format
     * @param string|callable $contentType
     * @return ApiStrategy
     * @throws \InvalidArgumentException
     */
    public function setContentType($format, $contentType)
    {
        if (!$this->hasRenderer($format)) {
            throw new \InvalidArgumentException("Renderer for format '$format' doesn't present");
        }

        $this->contentTypes[$format] = $contentType;

        return $this;
    }

    /**
     * Get content type for format $format
     *
     * @param string $format
     * @return string|callable
     * @throws \InvalidArgumentException
     */
    public function getContentType($format)
    {
        if (!$this->hasContentType($format)) {
            throw new \InvalidArgumentException("Content type for format '$format' doesn't present");
        }

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

        if ($this->hasRenderer($format)) {
            return $this->getRenderer($format);
        }

        return $this->getDefaultRenderer();
    }

    /**
     * Inject content to response
     *
     * @param ZendViewEvent $e
     * @throws InvalidFormatException
     */
    public function injectResponse(ZendViewEvent $e)
    {
        $response = $e->getResponse();
        $result = $e->getResult();
        $headers = $response->getHeaders();
        $format = $e->getRequest()->getMetadata('format');

        if (!$this->hasRenderer($format)) {
            throw new InvalidFormatException("Format '$format' is not supported");
        }

        if ($this->hasContentType($format)) {
            $contentType = $this->getContentType($format);

            if (is_string($contentType)) {
                $headers->addHeaderLine('content-type', 'application/xml');
            } elseif (is_callable($contentType)) {
                call_user_func($contentType, $e);
            }
        }

        $response->setContent($result);
    }
}
