<?php

namespace GeometriaLab\Model\Schema\Property;

interface PropertyInterface
{
    public function setName($name);
    public function getName();

    public function setDefaultValue($value);
    public function getDefaultValue();

    public function prepare($value);
}