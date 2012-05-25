<?php

namespace GeometriaLab\Code\Reflection\DocBlock\Tag;

use Zend\Code\Reflection\DocBlock\Tag\TagInterface;

class PropertyTag implements TagInterface
{
    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var string
     */
    protected $propertyName = null;

    /**
     * @var string
     */
    protected $description = null;

    /**
     * Params
     *
     * @var array
     */
    protected $params = array();

    /**
     * Initializer
     *
     * @param string $tagDocblockLine
     * @throws \InvalidArgumentException
     */
    public function initialize($tagDocblockLine)
    {
        $parts = preg_split("#[\s]+#", $tagDocblockLine, 3);

        if (count($parts) < 2 || 0 !== strpos($parts[1], '$')) {
            throw new \InvalidArgumentException('Invalid property definition');
        }

        // Get description and params
        if (isset($parts[2])) {
            // Remove new lines and spaces
            $parts[2] = preg_replace('#\s+#m', ' ', $parts[2]);

            // Try to encode description
            $params = json_decode($parts[2], true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($params)) {
                $this->params = $params;
            } else if ($parts[2] !== '') {
                $this->description = $parts[2];
            }
        }

        $this->propertyName = $parts[1];

        // Get type
        if (strpos($parts[0], '[]') === strlen($parts[0]) - 2) {
            $this->type = 'array';
            $this->params['itemType'] = substr($parts[0], 0, strlen($parts[0]) - 2);
        } else {
            $this->type = $parts[0];
        }
    }

    /**
     * Get tag name
     *
     * @return string
     */
    public function getName()
    {
        return 'property';
    }

    /**
     * Get property variable type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get property name
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * Get property description
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get property params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
