<?php

namespace GeometriaLab\Validator;

use Zend\Validator\AbstractValidator as ZendAbstractValidator,
    Zend\Validator\Exception\InvalidArgumentException as ZendInvalidArgumentException,
    Zend\Validator\Exception\RuntimeException as ZendRuntimeException;

/**
 * Created by JetBrains PhpStorm.
 * User: ivanshumkov
 * Date: 13.09.12
 * Time: 19:41
 * To change this template use File | Settings | File Templates.
 */
class IsType extends ZendAbstractValidator
{
    /**
     * Types
     */
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_STRING  = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT   = 'float';
    const TYPE_ARRAY   = 'array';

    /**
     * Error codes
     */
    const NOT_BOOLEAN = 'notBoolean';
    const NOT_STRING  = 'notString';
    const NOT_INTEGER = 'notInteger';
    const NOT_FLOAT   = 'notFloat';
    const NOT_ARRAY   = 'notArray';

    /**
     * Supported types
     *
     * @var array
     */
    protected $supportedTypes = array('boolean', 'string', 'integer', 'float', 'array');

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_BOOLEAN  => 'Value must be a boolean, %valueType% is present',
        self::NOT_STRING   => 'Value must be a string, %valueType% is present',
        self::NOT_INTEGER  => 'Value must be a integer, %valueType% is present',
        self::NOT_FLOAT    => 'Value must be a float, %valueType% is present',
        self::NOT_ARRAY    => 'Value must be a array, %valueType% is present',
    );

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $messageVariables = array(
        'type'      => 'type',
        'valueType' => 'valueType'
    );

    /**
     * Type
     *
     * @var string
     */
    protected $type;

    /**
     * Present value type
     *
     * @var string
     */
    protected $valueType;

    /**
     * Constructor
     *
     * @param array|string $options Options to use
     */
    public function __construct($options)
    {
        if (is_string($options)) {
            $options = array('type' => $options);
        }

        parent::__construct($options);
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param $type
     * @return IsType
     * @throws \Zend\Validator\Exception\InvalidArgumentException
     */
    public function setType($type)
    {
        if (!in_array($type, $this->supportedTypes)) {
            throw new ZendInvalidArgumentException("Invalid type '$type'. Supported types: " . implode(', ', $this->supportedTypes));
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Defined by Zend\Validator\ValidatorInterface
     *
     * @param  string $value
     * @return boolean
     * @throws ZendRuntimeException
     */
    public function isValid($value)
    {
        if ($this->type === null) {
            throw new ZendRuntimeException("Type not configured");
        }

        $this->valueType = gettype($value);

        if ($this->valueType === 'double') {
            $this->valueType = 'float';
        }

        if ($this->valueType !== $this->type) {
            $this->error('not' . ucfirst($this->type));
            return false;
        }

        return true;
    }
}
