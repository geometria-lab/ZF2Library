<?php

namespace GeometriaLab\Model\Persistent\Mapper;

use GeometriaLab\Model\ModelInterface;

interface MapperInterface
{
    public function create(ModelInterface $model);

    public function update(ModelInterface $data);

    public function delete(ModelInterface $data);
}