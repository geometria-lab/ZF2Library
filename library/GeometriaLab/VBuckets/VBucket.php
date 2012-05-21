<?php

namespace GeometriaLab\VBuckets;

use GeometriaLab\Model;

class VBucket extends Model\Schemaless
{
    public function __construct($id, $data)
    {
        $data['id'] = $id;

        parent::__construct($data);
    }
}