<?php

class GeometriaLab_VBuckets_Bucket extends GeometriaLab_Model_Schemaless
{
    public function __construct($id, $data)
    {
        $data['id'] = $id;

        parent::__construct($data);
    }
}
 
