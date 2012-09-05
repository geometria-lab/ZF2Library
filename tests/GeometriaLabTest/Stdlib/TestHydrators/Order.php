<?php

namespace GeometriaLabTest\Stdlib\TestHydrators;

use GeometriaLab\Stdlib\Hydrator\Schema,
    GeometriaLab\Api\Stdlib\Hydrator;


class Order extends Hydrator
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