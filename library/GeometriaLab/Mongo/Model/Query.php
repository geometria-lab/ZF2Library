<?php

namespace GeometriaLab\Mongo\Model;

use GeometriaLab\Model\Persistent\Mapper\Query as AbstractQuery,
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
     * @return AbstractQuery|QueryInterface|Query
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
}
