<?php

namespace GeometriaLabTest\Stdlib\Extractor\TestExtractors;

use GeometriaLab\Stdlib\Extractor\Schema,
    GeometriaLab\Stdlib\Extractor\Extractor;


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