<?php

interface GeometriaLab_VBuckets_Map_Interface
{
    public function getVBucket($id);

    public function getVBucketsCount();
}