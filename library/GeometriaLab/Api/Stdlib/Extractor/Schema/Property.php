<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 02.08.12
 * Time: 16:45
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Api\Stdlib\Extractor\Schema;

use Zend\Filter as ZendFilter;
use Zend\Stdlib\Exception\BadMethodCallException as ZendBadMethodCallException;

/**
 *
 */
class Property
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $source;

    /**
     * @var ZendFilter\FilterChain
     */
    protected $filterChain;

    /**
     * @param string $name
     * @param array  $options
     * @throws \Zend\Stdlib\Exception\BadMethodCallException
     */
    public function __construct($name, $options)
    {
        $this->setName($name);

        if (empty($options['source'])) {
            throw new ZendBadMethodCallException("Source option is required");
        }
        $this->setSource($options['source']);

        $this->setFilterChain(new ZendFilter\FilterChain());
        if (!empty($options['filters'])) {
            $this->populateFilters($options['filters']);
        }
    }

    /**
     * @param array $filters
     * @throws \Zend\Filter\Exception\RuntimeException
     */
    public function populateFilters(array $filters)
    {
        foreach ($filters as $filter) {
            if (is_object($filter) || is_callable($filter)) {
                $this->filterChain->attach($filter);
                continue;
            }

            if (is_array($filter)) {
                if (!isset($filter['name'])) {
                    throw new ZendFilter\Exception\RuntimeException(
                        'Invalid filter specification provided; does not include "name" key'
                    );
                }
                $name = $filter['name'];
                $options = array();
                if (isset($filter['options'])) {
                    $options = $filter['options'];
                }
                $this->filterChain->attachByName($name, $options);
                continue;
            }

            throw new ZendFilter\Exception\RuntimeException(
                'Invalid filter specification provided; was neither a filter instance nor an array specification'
            );
        }
    }

    /**
     * @return \Zend\Filter\FilterChain
     */
    public function getFilterChain()
    {
        return $this->filterChain;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param ZendFilter\FilterChain $filterChain
     */
    public function setFilterChain(ZendFilter\FilterChain $filterChain)
    {
        $this->filterChain = $filterChain;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return bool
     */
    public function hasFilters()
    {
        return $this->getFilterChain()->count() > 0;
    }
}