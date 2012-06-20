<?php

namespace GeometriaLab\Mongo\Model;

use GeometriaLab\Model\Persistent\Mapper\AbstractQuery,
    GeometriaLab\Model\Persistent\Mapper\QueryInterface,
    GeometriaLab\Model\Persistent\Mapper\MapperInterface;

class Query extends AbstractQuery
{
    protected $mongoKeys = array('$gt', '$gte', '$lt', '$lte', '$all',
                                 '$exists', '$mod', '$ne', '$in', '$nin',
                                 '$nor', '$or', '$and', '$size', '$type',
                                 '$near', '$regex');



    /**
     * Add where condition
     *
     * @param array $where
     * @return QueryInterface|Query
     */
    public function where(array $where)
    {
        if (!empty($where)) {
            $conditions = array();
            foreach($where as $field => $value) {
                if (is_array($value)) {
                    $keys = array_intersect(array_keys($value), $this->mongoKeys);
                    if (!empty($keys)) {
                        foreach ($value as $serviceKey => $data) {
                            $conditions[$field][$serviceKey] = $this->validateFieldValue($field, $data);
                        }
                    } else {
                        $conditions[$field]['$in'] = $this->validateFieldValue($field, $value);
                    }
                } else {
                    $conditions[$field] = $this->validateFieldValue($field, $value);
                }
            }

            if ($this->where === null) {
                $this->where = $conditions;
            } else {
                 $this->where = array_merge($this->where, $conditions);
            }
        }

        return $this;
    }

    /**
     * Add sorting by field
     *
     * @param string $field
     * @param boolean $ascending
     * @return Query|QueryInterface
     * @throws \InvalidArgumentException
     */
    public function sort($field, $ascending = true)
    {
        if (!$this->getModelSchema()->hasProperty($field)) {
            throw new \InvalidArgumentException("Sorted field '$field' not present in model!");
        }

        $sort = array($field => $ascending ? 1 : -1);

        if ($this->sort === null) {
            $this->sort = $sort;
        } else {
            $this->sort = array_merge($this->sort, $sort);
        }

        return $this;
    }

    protected function getModelSchema()
    {
        $schemas = \GeometriaLab\Model\Schema\Manager::getInstance();
        return $schemas->get($this->getMapper()->getModelClass());
    }
}
