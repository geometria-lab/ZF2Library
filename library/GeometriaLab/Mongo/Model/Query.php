<?php

namespace GeometriaLab\Mongo\Model;

use GeometriaLab\Model\Persistent\Mapper\AbstractQuery,
    GeometriaLab\Model\Persistent\Mapper\MapperInterface;

class Query extends AbstractQuery
{
    protected $mongoKeys = array('$gt', '$gte', '$lt', '$lte', '$all',
                                 '$exists', '$mod', '$ne', '$in', '$nin',
                                 '$nor', '$or', '$and', '$size', '$type',
                                 '$near', '$regex');

    /**
     * @param array $condition
     * @return Query
     */
    public function condition(array $condition)
    {
        if (!empty($condition)) {
            foreach ($condition as $field => $value) {
                if (is_array($value)) {
                    $keys = array_intersect(array_keys($value), $this->mongoKeys);

                    if (!empty($keys)) {
                        foreach ($value as $serviceKey => $data) {
                            $this->where[$field][$serviceKey] = $this->_formatValue($field, $data);
                        }
                    } else {
                        $this->where[$field]['$in'] = $this->_formatValue($field, $value);
                    }
                } else {
                    $this->where[$field] = $this->_formatValue($field, $value);
                }
            }
            $this->where = $this->translateToStorage($condition);
        }

        return $this;
    }

    public function sort($field, $ascending = true)
    {

    }





    /**
     *
     * @param array   $cond
     * @param array   $sort
     * @param integer $count
     * @param integer $offset
     *
     * @return Morph_Query
     */
    protected function _makeQuery($cond = array(), $sort = array(), $count = null, $offset = null)
    {
        $query = array();

        $query['cond'] = $this->_makeCond($cond);
        $query['sort'] = $this->_makeSort($sort);

        if ($count) {
            $query['limit'] = $count;
        }

        if ($offset) {
            $query['offset'] = $offset;
        }

        return $query;
    }

    /**
     *
     * @param array $sort
     * @return array
     */
    protected function _makeSort($sort = array())
    {
        $sorting = array();

        if (!empty($sort)) {
            foreach ($sort as $key => $value) {
                $sorting[$this->_translateKey($key)] = ($value == 1) ? 1 : -1;
            }
        }

        return $sorting;
    }

    /**
     *
     * @param $data
     * @return array
     */
    protected function _prepare($data)
    {
        $formattedData = array();

        foreach ($data as $key => $value) {
            $formattedData[$key] = $this->_formatValue($key, $value);
        }

        return $formattedData;
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     */
    protected function _formatValue($key, $value)
    {
        $type = call_user_func_array(array($this->getModelClass(), 'getPropertyType'), array($key));

        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = $this->_formatValue($key, $val);
            }
            return $value;
        }

        if ($value instanceof MongoId || $value instanceof MongoRegex) {
            return $value;
        }

        switch ($type) {
            case 'int':
            case 'integer':
            case 'int[]':
            case 'integer[]':
                return (int) $value;
            case 'str':
            case 'string':
            case 'str[]':
            case 'string[]':
                return (string) $value;
            case 'bool':
            case 'boolean':
            case 'bool[]':
            case 'boolean[]':
                return (boolean)$value;
            case 'float':
            case 'float[]':
                return (float) $value;
            case 'MongoId':
                return new MongoId($value);
            default:
                $method = '_format' . $key;
                if (method_exists($this, $method)) {
                    return $this->$method($value);
                }
                return $value;
        }
    }
}
