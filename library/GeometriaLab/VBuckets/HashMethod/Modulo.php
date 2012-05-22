<?php

namespace GeometriaLab\VBuckets\HashMethod;

use GeometriaLab\VBuckets\Map\MapInterface;

class Modulo implements HashMethodInterface
{
    /**
     * Get hash
     *
     * @param $key
     * @param \GeometriaLab\VBuckets\Map\MapInterface $map
     * @return int
     * @throws \InvalidArgumentException
     */
    public function getHash($key, MapInterface $map)
    {
        if (!is_scalar($key)) {
            throw new \InvalidArgumentException('Non scalar key');
        }

        if (is_string($key)) {
            $key = hexdec(substr(md5($key), 0, 15));
        }

        return ($key % $map->getVBucketsCount()) + 1;
    }
}