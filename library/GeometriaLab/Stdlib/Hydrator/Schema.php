<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 02.08.12
 * Time: 16:43
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Stdlib\Hydrator;

/**
 *
 */
class Schema
{
    /**
     * @var array
     */
    protected $properties;

    /**
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = array();

        foreach ($properties as $name => $options) {
            $this->properties[] = new Schema\Property($name, $options);
        }
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }
}