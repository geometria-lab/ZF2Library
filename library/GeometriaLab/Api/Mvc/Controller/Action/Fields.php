<?php

namespace GeometriaLab\Api\Mvc\Controller\Action;

use Zend\Filter\Exception\RuntimeException as ZendFilterRuntimeException;

class Fields implements \ArrayAccess, \Countable
{
    const FLAG = true;

    /**
     * @var array
     */
    private $data = array();

    /**
     * @param array $fields
     */
    public function __construct($fields = array())
    {
        foreach ($fields as $key => $value) {
            $this[$key] = $value;
        }
    }

    public function __clone()
    {
        foreach ($this->data as $key => $value) {
            if ($value instanceof self) {
                $this[$key] = clone $value;
            }
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_array($value)) {
            $value = new self($value);
        }

        if ($offset === null) {
            $this->data[$value] = self::FLAG;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * Is has some fields
     *
     * @return bool
     */
    public function hasFields()
    {
        return !count($this) || isset($this['*']);
    }

    /**
     * Create Fields from string
     *
     * @static
     * @param $fieldsString
     * @return Fields
     * @throws ZendFilterRuntimeException
     */
    static public function createFromString($fieldsString)
    {
        $fieldsString = str_replace(' ', '', $fieldsString);
        $fields = array();
        $level = 0;
        $stack = array();
        $stack[$level] = &$fields;
        $field = '';
        $len = strlen($fieldsString) - 1;

        for ($i = 0; $i <= $len; $i++) {
            $char = $fieldsString[$i];
            switch ($char) {
                case ',':
                    if ('' != $field) {
                        $stack[$level][$field] = self::FLAG;
                        $field = '';
                    }
                    break;
                case '(':
                    $stack[$level][$field] = array();
                    $oldLevel = $level;
                    $stack[++$level] = &$stack[$oldLevel][$field];
                    $field = '';
                    break;
                case ')':
                    if ('' != $field) {
                        $stack[$level][$field] = self::FLAG;
                    }
                    unset($stack[$level--]);
                    if ($level < 0) {
                        throw new ZendFilterRuntimeException('Bad _fields syntax');
                    }
                    $field = '';
                    break;
                default:
                    $field.= $char;
                    if ($i == $len && '' !== $field) {
                        $stack[$level][$field] = self::FLAG;
                        $field = '';
                    }
            }
        }
        if (count($stack) > 1) {
            throw new ZendFilterRuntimeException('Bad _fields syntax');
        }

        return new self($fields);
    }
}
