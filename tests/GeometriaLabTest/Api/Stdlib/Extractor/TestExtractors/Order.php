<?php

namespace GeometriaLabTest\Api\Stdlib\Extractor\TestExtractors;

use GeometriaLab\Api\Stdlib\Extractor\Schema,
    GeometriaLab\Api\Stdlib\Extractor\Extractor;


class Order extends Extractor
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
            'transactionId' => array(
                'source' => 'transactionId',
            ),
        ));
    }
}