<?php

namespace GeometriaLab\Code\Reflection\DocBlock\Tag;

use Zend\Code\Reflection\DocBlock\Tag\ParamTag;

class PropertyTag extends ParamTag
{
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
            $parts[2] = preg_replace('#\s+#', ' ', $parts[2]);

            // Try to encode description
            $params = json_decode($parts[2], true);

            if (json_last_error() === JSON_ERROR_NONE && is_object($params)) {
                $this->params = $params;
            } else {
                $this->description = $parts[2];
            }
        }

        $this->variableName = $parts[1];

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
     * Get tag params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
