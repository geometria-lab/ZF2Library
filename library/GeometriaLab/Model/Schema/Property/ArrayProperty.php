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
            if (is_array($value)) {
                foreach($value as &$item) {
                    $item = $property->getItemProperty()->getFilterChain()->filter($item);

                    $validator = new Validator\ArrayItem($property);
                    $property->getValidatorChain()->addValidator($validator);
                }
            }

            return $value;
        });


        $validator = new Validator\ArrayItem($this);
        $this->getValidatorChain()->addValidator($validator);
    }
}