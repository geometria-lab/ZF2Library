<?php

namespace GeometriaLab\Api\Mvc\View\Renderer;

use Zend\View\Renderer\RendererInterface as ZendViewRendererInterface,
    Zend\View\Resolver\ResolverInterface as ZendViewResolver;

class XmlRenderer implements ZendViewRendererInterface
{
    protected $resolver;

    /**
     * Return the template engine object, if any
     *
     * If using a third-party template engine, such as Smarty, patTemplate,
     * phplib, etc, return the template engine object. Useful for calling
     * methods on these objects, such as for setting filters, modifiers, etc.
     *
     * @return mixed
     */
    public function getEngine()
    {
        return $this;
    }

    public function setResolver(ZendViewResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function render($nameOrModel, $values = null)
    {
        throw new \RuntimeException('Not implemented yet');
    }
}
