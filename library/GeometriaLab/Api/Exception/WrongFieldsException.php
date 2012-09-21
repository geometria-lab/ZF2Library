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
class WrongFieldsException extends AbstractException
{
    /**
     * @var int
     */
    protected $code = 41;
    /**
     * @var string
     */
    protected $message = 'Wrong fields';
    /**
     * @var int
     */
    protected $httpCode = 400;

    /**
     * @return array
     */
    public function getData()
    {
        return $this->prepareData($this->data);
    }

    /**
     * @param array $data
     * @param string $parentKey
     * @return array
     */
    private function prepareData($data, $parentKey = '')
    {
        $result = array();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if ($parentKey !== '') {
                    $key = "$parentKey.$key";
                }
                $result = array_merge($result, $this->prepareData($value, $key));
            } else {
                if ($parentKey !== '') {
                    $result[] = "$parentKey.$value";
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;


        if (is_array($field)) {
            foreach ($field as $childKey => $childField) {
                if (is_array($childField)) {
                    $result = $this->prepareData($childKey, $childField);
                } else {
                    //$result =
                }
            }
        } else {
            return $field;
        }
    }
}