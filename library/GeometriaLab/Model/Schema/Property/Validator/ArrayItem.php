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
class ArrayItem extends ZendAbstractValidator
{
    const NOT_ARRAY          = 'notArray';
    const INVALID_ARRAY_ITEM = 'invalidArrayItem';

    protected $messageTemplates = array(
        self::NOT_ARRAY          => "Value must be a array",
        self::INVALID_ARRAY_ITEM => null
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
     * @return ArrayItem
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

        if (!is_array($value)) {
            $this->error(self::NOT_ARRAY);
            return false;
        }

        $itemProperty = $property->getItemProperty();

        if ($itemProperty === null) {
            return true;
        }

        foreach($value as $item) {
            if (!$itemProperty->getValidatorChain()->isValid($item)) {
                if (isset($this->messageTemplates[self::INVALID_ARRAY_ITEM])) {
                    $this->error(self::INVALID_ARRAY_ITEM);
                } else {
                    $itemMessages = $itemProperty->getValidatorChain()->getMessages();
                    $this->abstractOptions['messages'][self::INVALID_ARRAY_ITEM] = $itemMessages;
                }

                return false;
            }
        }

        return true;
    }
}
