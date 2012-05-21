<?php

namespace GeometriaLab\VBuckets\HashMethod;

use GeometriaLab\VBuckets\Map\MapInterface;

class Modulo implements HashMethodInterface
{
    public function getHash($key, MapInterface $map)
    {
        if (!is_scalar($key)) {
            throw new \Exception('Non scalar key');
        }

        if (is_string($key)) {
            $key = hexdec(substr(md5($key), 0, 15));
        }

        return ($key % $map->getVBucketsCount()) + 1;
    }
}