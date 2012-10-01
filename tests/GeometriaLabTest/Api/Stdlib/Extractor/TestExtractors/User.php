<?php

namespace GeometriaLabTest\Api\Stdlib\Extractor\TestExtractors;

use GeometriaLab\Api\Stdlib\Extractor\Schema,
    GeometriaLab\Api\Stdlib\Extractor\Extractor;


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
                'source' => 'order',
            ),
        ));
    }
}