<?php

namespace GeometriaLab\Api\Mvc\Controller\Action\Params;

use \GeometriaLab\Model\AbstractModel;

/**
 *
 */
class Params extends AbstractModel
{
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
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Set property value without throwing exception on validation
     *
     * @param string $name
     * @param mixed $value
     * @return AbstractModel|\GeometriaLab\Model\ModelInterface
     * @throws \InvalidArgumentException
     */
    public function set($name, $value)
    {
        if (!$this->has($name)) {
            $this->notPresentProperties[$name] = $value;

            return $this;
        }

        try {
            parent::set($name, $value);
        } catch (\InvalidArgumentException $e) {
            // Do nothing, keep silent...
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