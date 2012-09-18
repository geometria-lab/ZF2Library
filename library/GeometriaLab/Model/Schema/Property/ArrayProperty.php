<?php

namespace GeometriaLab\Model\Schema\Property;

class ArrayProperty extends AbstractProperty
{
    /**
     * @var PropertyInterface
     */
    protected $itemProperty;

    /**
     * Set item property
     *
     * @param PropertyInterface $property
     * @return ArrayProperty
     */
    public function setItemProperty(PropertyInterface $property)
    {
        $this->itemProperty = $property;

        return $this;
    }

    /**
     * Get item property
     *
     * @return PropertyInterface
     */
    public function getItemProperty()
    {
        return $this->itemProperty;
    }

    protected function setup()
    {
        $property = $this;
        $this->getFilterChain()->attach(function($value) use ($property) {
            $itemProperty = $property->getItemProperty();
            if (is_array($value) && $itemProperty !== null) {
                foreach($value as &$item) {
                    $item = $itemProperty->getFilterChain()->filter($item);
                }
            }

            return $value;
        });


        $validator = new Validator\ArrayItem($this);
        $this->getValidatorChain()->addValidator($validator);
    }
}