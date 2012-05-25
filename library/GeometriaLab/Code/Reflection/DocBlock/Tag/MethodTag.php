<?php

namespace GeometriaLab\Code\Reflection\DocBlock\Tag;

use Zend\Code\Reflection\DocBlock\Tag\TagInterface;

class MethodTag implements TagInterface
{
    /**
     * @var string
     */
    protected $returnType = null;

    /**
     * @var string
     */
    protected $methodName = null;

    /**
     * @var string
     */
    protected $description = null;

    /**
     * Is static method
     *
     * @var bool
     */
    protected $isStatic = false;

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
        if (!preg_match('#^(static)?[\s]*(.+)[\s]+(.+\(\))[\s]*(.*)$#m', $tagDocblockLine, $match)) {
            throw new \InvalidArgumentException('Invalid method definition');
        }

        // Get flags
        $this->isStatic = $match[1] === 'static';

        // Get type
        $this->type = $match[2];

        // Get name
        $this->methodName = $match[3];

        // Get description or params
        if (isset($match[4])) {
            // Remove new lines and spaces
            $match[4] = preg_replace('#\s+#m', ' ', $match[4]);

            // Try to encode description
            $params = json_decode($match[4], true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($params)) {
                $this->params = $params;
            } else if ($match[4] !== '') {
                $this->description = $match[4];
            }
        }
    }

    /**
     * Get tag name
     *
     * @return string
     */
    public function getName()
    {
        return 'method';
    }

    /**
     * Get return value type
     *
     * @return string
     */
    public function getReturnType()
    {
        return $this->returnType;
    }

    /**
     * Get method name
     *
     * @return string
     */
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * Get method description
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Is method static
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->isStatic;
    }

    /**
     * Get method params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
