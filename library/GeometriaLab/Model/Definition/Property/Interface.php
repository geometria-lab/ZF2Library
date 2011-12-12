<?php

interface GeometriaLab_Model_Definition_Property_Interface
{
    public function setName($name);
    public function getName();
    public function setDefaultValue($value);
    public function getDefaultValue();
    public function isValid($value);
}