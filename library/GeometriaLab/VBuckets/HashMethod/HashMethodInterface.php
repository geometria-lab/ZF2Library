<?php

namespace GeometriaLab\VBuckets\HashMethod;

use GeometriaLab\VBuckets\Map\MapInterface;

interface HashMethodInterface
{
    public function getHash($key, MapInterface $map);
}