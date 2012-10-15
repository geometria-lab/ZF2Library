<?php

namespace GeometriaLab\Validator;

use GeometriaLab\Model\Persistent\AbstractModel;

use Zend\Validator\AbstractValidator as ZendAbstractValidator,
    Zend\Validator\Exception\RuntimeException as ZendRuntimeException,
    Zend\Validator\Exception\InvalidArgumentException as ZendInvalidArgumentException;

class Unique extends ZendAbstractValidator
{
    const EXISTS = 'exists';

    /**
     * Error messages
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::EXISTS => "'%value%' already exists",
    );
    /**
     * Model class name for validation
     *
     * @var string
     */
    protected $class;
    /**
     * Field name for validation
     *
     * @var string
     */
    protected $field;

    /**
     * Set model class name
     *
     * @param string $class
     * @return Unique
     * @throws ZendInvalidArgumentException
     */
    public function setClass($class)
    {
        if (!is_subclass_of($class, '\GeometriaLab\Model\Persistent\AbstractModel')) {
            throw new ZendInvalidArgumentException('Class mus be instance of \GeometriaLab\Model\Persistent\AbstractModel');
        }

        $this->class = $class;

        return $this;
    }

    /**
     * Set field name
     *
     * @param string $field
     * @return Unique
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @param mixed $value
     * @return bool
     * @throws ZendRuntimeException
     */
    public function isValid($value)
    {
        if ($this->class === null) {
            throw new ZendRuntimeException("Class not configured");
        }

        if ($this->field === null) {
            throw new ZendRuntimeException("Field not configured");
        }

        $uniqueValidator = null;
        /* @var AbstractModel $model */
        $model = new $this->class;
        $validatorChain = $model->getSchema()->getProperty($this->field)->getValidatorChain();

        foreach ($validatorChain->getValidators() as $index => $validatorData) {
            if ($validatorData['instance'] instanceof Unique) {
                $uniqueValidator = $validatorData;
                $uniqueValidator['index'] = $index;
                $validatorChain->removeValidatorByIndex($index);
            }
        }

        $matchedCount = $model->getMapper()->count(array($this->field => $value));

        $validatorChain->addValidatorByIndex($uniqueValidator['index'], $uniqueValidator['instance'], $uniqueValidator['breakChainOnFailure']);

        if ($matchedCount) {
            $this->error(self::EXISTS, $value);
            return false;
        }

        return true;
    }
}