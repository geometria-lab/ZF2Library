<?php

class GeometriaLab_VBuckets_Hash_Modulo implements GeometriaLab_VBuckets_Hash_Interface
{
    public function getHash($key, GeometriaLab_VBuckets_Map_Interface $map)
    {
        if (!is_scalar($key)) {
            throw new GeometriaLab_VBuckets_Hash_Exception('Non scalar key');
        }

        if (is_string($key)) {
            $key = hexdec(substr(md5($key), 0, 15));
        }

        return ($key % $map->getVBucketsCount()) + 1;
    }
}