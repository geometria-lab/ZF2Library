<?php

namespace GeometriaLab\Model\Schema\Property;

use GeometriaLab\Validator\IsType,
    GeometriaLab\Validator\ValidatorChain,
    GeometriaLab\Model\Schema\Property\Validator\Exception\InvalidValueException;

use Zend\Filter\FilterChain as ZendFilterChain,
    Zend\Validator\NotEmpty as ZendNotEmptyValidator;

abstract class AbstractProperty implements PropertyInterface
{
    /**
     * Name
     *
     * @var string
     */
    protected $name;
    /**
     * Default value
     *
     * @var mixed
     */
    protected $defaultValue;
    /**
     * Required property
     *
     * @var boolean
     */
    protected $isRequired = false;
    /**
     * Allow empty value
     *
     * @var bool
     */
    protected $allowEmpty = false;
    /**
     * @var ZendFilterChain
     */
    protected $filterChain;
    /**
     * @var ValidatorChain
     */
    protected $validatorChain;
    /**
     * @var boolean
     */
    protected $notEmptyValidator = false;
    /**
     * Type validators
     *
     * @var array
     */
    static protected $typeValidators = array();

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
        $this->setup();
    }

    /**
     * Set options
     *
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            $method = "set$option";
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                throw new \InvalidArgumentException("Unknown property option '$option'");
            }
        }
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param $name
     * @return AbstractProperty
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Mark property as Required
     *
     * @param boolean $required
     * @return PropertyInterface
     */
    public function setRequired($required)
    {
        $this->isRequired = $required;

        return $this;
    }

    /**
     * Is required
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->isRequired;
    }

    /**
     * Set allow empty
     *
     * @param boolean $allowEmpty
     * @return PropertyInterface
     * @throws \RuntimeException
     */
    public function setAllowEmpty($allowEmpty)
    {
        if ($this->notEmptyValidator && $allowEmpty) {
            throw new \RuntimeException("Can't change 'allow empty' from false to true");
        }

        $this->allowEmpty = $allowEmpty;
        $this->addNotEmptyValidator();

        return $this;
    }

    /**
     * Is allow empty
     *
     * @return boolean
     */
    public function isAllowEmpty()
    {
        return $this->allowEmpty;
    }

    /**
     * Get default value
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set default value
     *
     * @param $value
     * @return AbstractProperty
     */
    public function setDefaultValue($value)
    {
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * @param ZendFilterChain $filterChain
     * @return AbstractProperty
     */
    public function setFilterChain(ZendFilterChain $filterChain)
    {
        $this->filterChain = $filterChain;

        return $this;
    }

    /**
     * @return ZendFilterChain
     */
    public function getFilterChain()
    {
        if ($this->filterChain === null) {
            $this->filterChain = new ZendFilterChain();
        }

        return $this->filterChain;
    }

    /**
     * @param ValidatorChain $validatorChain
     * @return AbstractProperty
     */
    public function setValidatorChain(ValidatorChain $validatorChain)
    {
        $this->validatorChain = $validatorChain;

        return $this;
    }

    /**
     * @return ValidatorChain
     */
    public function getValidatorChain()
    {
        if ($this->validatorChain === null) {
            $this->validatorChain = new ValidatorChain();
        }

        return $this->validatorChain;
    }

    /**
     * Filter and validate value
     *
     * @param mixed $value
     * @return mixed
     * @throws InvalidValueException
     */
    public function filterAndValidate($value)
    {
        $value = $this->getFilterChain()->filter($value);

        if (!$this->getValidatorChain()->isValid($value)) {
            $errorMessages = $this->getValidatorChain()->getMessages();
            $this->getValidatorChain()->cleanupMessages();

            $exception = new InvalidValueException("Invalid value for property '{$this->getName()}'");
            $exception->setValidationErrorMessages($errorMessages);

            throw $exception;
        }

        return $value;
    }

    /**
     * Add validator by type
     *
     * @param $type
     */
    protected function addTypeValidator($type)
    {
        if (!isset(static::$typeValidators[$type])) {
            static::$typeValidators[$type] = new IsType($type);
        }

        $this->getValidatorChain()->prependValidator(static::$typeValidators[$type], true);
    }

    /**
     * Add not empty validator
     *
     * @param string|int $type
     */
    protected function addNotEmptyValidator($type = ZendNotEmptyValidator::ALL)
    {
        if (!($this->isRequired() && !$this->isAllowEmpty()) || $this->notEmptyValidator) {
            return;
        }

        $chain = $this->getValidatorChain();
        $validators = $chain->getValidators();

        if (isset($validators[0]['instance']) && $validators[0]['instance'] instanceof ZendNotEmptyValidator) {
            $this->notEmptyValidator = true;
            return;
        }

        $this->notEmptyValidator = true;

        $validator = new ZendNotEmptyValidator();
        $validator->setType($type);

        $chain->prependValidator($validator, true);
    }

    /**
     * Setup something
     */
    protected function setup()
    {

    }
}
