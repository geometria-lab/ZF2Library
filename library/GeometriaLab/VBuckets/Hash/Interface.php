<?php

interface GeometriaLab_VBuckets_Hash_Interface
{
    public function getHash($key, GeometriaLab_VBuckets_Map_Interface $map);
}