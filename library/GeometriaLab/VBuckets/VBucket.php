<?php

namespace GeometriaLab\VBuckets;

use GeometriaLab\Model\Schemaless\Model;

class VBucket extends Model
{
    public function __construct($id, $data)
    {
        $data['id'] = $id;

        parent::__construct($data);
    }
}