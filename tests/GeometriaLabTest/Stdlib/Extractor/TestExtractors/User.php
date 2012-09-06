<?php

namespace GeometriaLabTest\Stdlib\Extractor\TestExtractors;

use GeometriaLab\Stdlib\Extractor\Schema,
    GeometriaLab\Stdlib\Extractor\Extractor;


class User extends Extractor
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
                'source' => 'GeometriaLabTest\StdLib\Extractor\TestExtractors\Order',
            ),
        ));
    }
}