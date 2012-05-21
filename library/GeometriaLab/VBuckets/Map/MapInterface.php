<?php

namespace GeometriaLab\VBuckets\Map;

interface MapInterface
{
    public function getVBucket($id);
    public function getVBucketsCount();
}