<?php

namespace GeometriaLab\Model\Schema\Property\Validator;

use GeometriaLab\Model\Schema\Property\PropertyInterface;

use Zend\Validator\AbstractValidator as ZendAbstractValidator,
    Zend\Validator\Exception\RuntimeException as ZendRuntimeException;

/**
 * Created by JetBrains PhpStorm.
 * User: ivanshumkov
 * Date: 13.09.12
 * Time: 19:41
 * To change this template use File | Settings | File Templates.
 */
class Model extends ZendAbstractValidator
{
    const INVALID_MODEL = 'invalidModel';
    const INVALID_MODEL_OBJECT = 'invalidModelObject';

    protected $messageTemplates = array(
        self::INVALID_MODEL        => null,
        self::INVALID_MODEL_OBJECT => "Value must be a model object of '%type%'"
    );

    /**
     * Type
     *
     * @var PropertyInterface
     */
    protected $property;

    /**
     * Constructor
     *
     * @param array|string $options Options to use
     */
    public function __construct($options)
    {
        if (is_object($options) && $options instanceof PropertyInterface) {
            $options = array('property' => $options);
        }

        parent::__construct($options);
    }

    /**
     * @param PropertyInterface $property
     * @return Model
     */
    public function setProperty(PropertyInterface $property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * @return PropertyInterface
     */
    public function getProperty()
    {
        return $this->property;
    }

    public function isValid($value)
    {
        $property = $this->getProperty();

        if (!$property instanceof PropertyInterface) {
            throw new ZendRuntimeException('Property object not configured');
        }

        if (!is_object($value) || !is_a($value, $property->getModelClass())) {
            $this->error(self::INVALID_MODEL_OBJECT);
            return false;
        }

        if ($value instanceof \GeometriaLab\Model\ModelInterface) {
            /** @var \GeometriaLab\Model\ModelInterface $model */
            if (!$value->isValid()) {
                if (isset($this->messageTemplates[self::INVALID_MODEL])) {
                    $this->error(self::INVALID_MODEL);
                } else {
                    $errorMessages = $value->getErrorMessages();
                    $this->abstractOptions['messages'][self::INVALID_MODEL] = $errorMessages;
                }

                return false;
            }
        }

        return true;
    }
}
