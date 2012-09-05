<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 02.08.12
 * Time: 16:43
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Stdlib\Hydrator;

use GeometriaLab\Stdlib\Hydrator\Schema\Property;

/**
 *
 */
class Schema
{
    /**
     * @var Property[]
     */
    protected $properties;

    /**
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = array();

        foreach ($properties as $name => $options) {
            $this->properties[] = new Property($name, $options);
        }
    }

    /**
     * @return Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }
}