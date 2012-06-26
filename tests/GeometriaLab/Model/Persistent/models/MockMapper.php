<?php

namespace GeometriaLabTest\Model\Persistent\Models;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\Mapper\AbstractMapper,
    GeometriaLab\Model\Persistent\Mapper\QueryInterface;

class MockMapper extends AbstractMapper
{
    /**
     * @var ModelInterface[]
     */
    protected $data;

    public function count(array $condition = array())
    {
        throw new \RuntimeException('Not implemented');
    }

    public function get($id)
    {
        if (isset($this->data[$id])) {
            return $this->data[$id];
        }
    }

    public function getByCondition(array $condition)
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getAll(QueryInterface $query = null)
    {
        throw new \RuntimeException('Not implemented');
    }

    public function create(ModelInterface $model)
    {
        if ($model->id === null) {
            $model->id = count($this->data) + 1;
        }

        $this->data[$model->id] = $model;

        $model->markClean();

        return true;
    }

    public function update(ModelInterface $model)
    {
        $this->data[$model->id] = $model;

        $model->markClean();

        return true;
    }

    public function updateByCondition(array $data, array $condition)
    {
        throw new \RuntimeException('Not implemented');
    }

    public function delete(ModelInterface $model)
    {
        if (isset($this->data[$model->id])) {
            unset($this->data[$model->id]);

            $model->markClean(false);
        }

        return true;
    }

    public function deleteByCondition(array $condition)
    {
        throw new \RuntimeException('Not implemented');
    }
}
