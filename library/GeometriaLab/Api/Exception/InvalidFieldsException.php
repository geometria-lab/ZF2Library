<?php
/**
 * Created by JetBrains PhpStorm.
 * User: max
 * Date: 10.08.12
 * Time: 18:18
 * To change this template use File | Settings | File Templates.
 */

namespace GeometriaLab\Api\Exception;

/**
 *
 */
class InvalidFieldsException extends AbstractException
{
    /**
     * @var int
     */
    protected $code = 41;
    /**
     * @var string
     */
    protected $message = 'Invalid fields';
    /**
     * @var int
     */
    protected $httpCode = 400;
    /**
     * Fields array
     *
     * @var array
     */
    protected $fields = array();

    /**
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        if ($data !== null) {
            return $data;
        }

        $fields = $this->getFields();

        return $this->prepareFields($fields);
    }

    /**
     * Set fields
     *
     * @param array $fields
     * @return InvalidFieldsException
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Get fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $data
     * @param string $parentKey
     * @return array
     */
    private function prepareFields($data, $parentKey = '')
    {
        $result = array();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($parentKey !== '') {
                    $key = "$parentKey.$key";
                }
                $result = array_merge($result, $this->prepareFields($value, $key));
            } else {
                if ($parentKey !== '') {
                    $result[] = "$parentKey.$value";
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }
}