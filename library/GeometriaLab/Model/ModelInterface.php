<?php

namespace GeometriaLab\Model;

interface ModelInterface
{
    public function populate($data);

    public function get($value);

    public function set($name, $value);

    public function has($name);
}