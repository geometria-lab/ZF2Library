<?php

namespace GeometriaLab\Model\Definition\Property;

interface PropertyInterface
{
    public function setName($name);
    public function getName();

    public function setDefaultValue($value);
    public function getDefaultValue();

    public function prepare($value);
}