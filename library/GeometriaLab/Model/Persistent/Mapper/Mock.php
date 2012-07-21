<?php

namespace GeometriaLab\Model\Persistent\Mapper;

use GeometriaLab\Model\Persistent\ModelInterface,
    GeometriaLab\Model\Persistent\CollectionInterface,
    GeometriaLab\Model\Persistent\Mapper\AbstractMapper,
    GeometriaLab\Model\Persistent\Mapper\QueryInterface;

class Mock extends AbstractMapper
{
    /**
     * @var CollectionInterface
     */
    protected $data;

    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $this->data = new $this->getCollectionClass();
    }

    /**
     * @todo equals to mongo mapper. move to abstaract?
     * @param int $id
     * @return ModelInterface
     */
    public function get($id)
    {
        $query = $this->createQuery()->where(array('id' => $id));

        return $this->getOne($query);
    }

    /**
     * @todo equals to mongo mapper. move to abstaract?
     * @param QueryInterface $query
     * @return ModelInterface
     * @throws \InvalidArgumentException
     */
    public function getOne(QueryInterface $query = null)
    {
        if ($query === null) {
            $query = $this->createQuery();
        }

        if (!$query instanceof Query) {
            throw new \InvalidArgumentException('Query must be GeometriaLab\Model\Persistent\Mapper\Query');
        }

        if (!$query->hasLimit()) {
            $query->limit(1);
        } else if ($query->getLimit() !== 1) {
            throw new \InvalidArgumentException('getOne accepts query with limit 1');
        }

        return $this->getAll($query)->getFirst();
    }


    public function getAll(QueryInterface $query = null)
    {
        if ($query === null) {
            $query = $this->createQuery();
        }

        if (!$query instanceof Query) {
            throw new \InvalidArgumentException('Query must be GeometriaLab\Model\Persistent\Mapper\Query');
        }

        $collection = $this->data->getByCondition($query->getWhere());

        if ($query->hasSort()) {
            $collection->sort($query->getSort());
        }

        if ($query->hasOffset()) {
            $collection = $collection->getSlice($query->getOffset());
        }

        if ($query->hasLimit()) {
            $collection = $collection->getSlice(0, $query->getLimit());
        }

        return $collection;
    }

    public function create(ModelInterface $model)
    {
        if (!is_a($model, $this->getModelClass())) {
            throw new \InvalidArgumentException("Model must be {$this->getModelClass()}");
        }

        if ($model->id === null) {
            $model->id = count($this->data) + 1;
        }

        $this->data->push($model);

        $model->markClean();

        return true;
    }

    public function update(ModelInterface $model)
    {
        if (!is_a($model, $this->getModelClass())) {
            throw new \InvalidArgumentException("Model must be {$this->getModelClass()}");
        }

        foreach($this->data as $index => $storedModel) {
            if ($storedModel->id === $model->id) {
                $this->data->set($index, $model);

                $model->markClean();

                return true;
            }
        }

        return false;
    }

    public function delete(ModelInterface $model)
    {
        $id = $model->get('id');

        if ($id === null) {
            throw new \InvalidArgumentException('Cant delete model - primary property id is empty');
        }

        $this->data->removeByCondition(array('id' => $model->id));

        $model->markClean(false);

        return true;
    }

    public function count(array $condition = array())
    {
        throw new \RuntimeException('Not implemented');
    }
}
