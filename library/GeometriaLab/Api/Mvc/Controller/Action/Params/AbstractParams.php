<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params;

use \GeometriaLab\Model\AbstractModel;

abstract class AbstractParams extends AbstractModel
{
    /**
     * Parser class name
     *
     * @var string
     */
    static protected $parserClassName = 'GeometriaLab\Api\Mvc\Controller\Action\Params\Schema\DocBlockParser';

    /**
     * Non-existent properties, which was set
     *
     * @var array
     */
    protected $notPresentProperties = array();

    /**
     * Populate model from array or iterable object
     *
     * @param array|\Traversable|\stdClass $data  Model data (must be array or iterable object)
     * @return AbstractModel
     * @throws \InvalidArgumentException
     */
    public function populate($data)
    {
        if (!is_array($data) && !$data instanceof \Traversable && !$data instanceof \stdClass) {
            throw new \InvalidArgumentException("Can't populate data. Must be array or iterated object.");
        }

        foreach ($data as $key => $value) {
            if (!$this->has($key)) {
                $this->notPresentProperties[$key] = $value;
                continue;
            }
            try {
                $this->set($key, $value);
            } catch (\InvalidArgumentException $e) {
                // Do nothing, keep silent...
            }
        }

        return $this;
    }

    /**
     * Is Valid model data
     *
     * @return bool
     */
    public function isValid()
    {
        $result = parent::isValid();

        foreach ($this->notPresentProperties as $name => $value) {
            $this->errorMessages[$name]['notPresent'] = "Property does not exists";
            $result = false;
        }

        return $result;
    }
}