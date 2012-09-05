<?php

namespace GeometriaLabTest\Stdlib\TestHydrators;

use GeometriaLab\Stdlib\Hydrator\Schema,
    GeometriaLab\Api\Stdlib\Hydrator;


class User extends Hydrator
{
    /**
     * @return Schema
     */
    public function createSchema()
    {
        return new Schema(array(
            'id' => array(
                'source' => 'id',
            ),
            'name' => array(
                'source' => 'name',
                'filters' => array(
                    function($value) {
                        return $value . ' Rodriguez';
                    }
                )
            ),
            'order' => array(
                'source' => 'GeometriaLabTest\StdLib\TestHydrators\Order',
            ),
        ));
    }
}