<?php

interface GeometriaLab_VBuckets_HashMethod_Interface
{
    public function getHash($key, GeometriaLab_VBuckets_Map_Interface $map);
}