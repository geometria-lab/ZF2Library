<?php
namespace GeometriaLab\Model\Schema;

use GeometriaLab\Model\Schema\Property\PropertyInterface,
    GeometriaLab\Validator\ValidatorChain;

use Zend\Code\Reflection\DocBlock\Tag\TagInterface as ZendTagInterface,
    Zend\Code\Reflection\DocBlock\Tag\PropertyTag as ZendPropertyTag,
    Zend\Code\Reflection\ClassReflection as ZendClassReflection,
    Zend\Code\Reflection\Exception\InvalidArgumentException as ZendInvalidArgumentException,
    Zend\Code\Reflection\DocBlockReflection as ZendDocBlockReflection;

use Zend\Filter\FilterChain as ZendFilterChain,
    Zend\Filter\Exception\RuntimeException as ZendRuntimeException;

use Zend\Serializer\Serializer as ZendSerializer;

class DocBlockParser
{
    /**
     * @var DocBlockParser[]
     */
    static protected $instances = array();

    /**
     * Regular properties class map
     *
     * @var array
     */
    static protected $regularPropertiesClassMap = array(
        'string'  => 'GeometriaLab\Model\Schema\Property\StringProperty',
        'array'   => 'GeometriaLab\Model\Schema\Property\ArrayProperty',
        'boolean' => 'GeometriaLab\Model\Schema\Property\BooleanProperty',
        'float'   => 'GeometriaLab\Model\Schema\Property\FloatProperty',
        'integer' => 'GeometriaLab\Model\Schema\Property\IntegerProperty',
    );

    /**
     * Model property class name
     *
     * @var string
     */
    static protected $modelPropertyClass = '\GeometriaLab\Model\Schema\Property\ModelProperty';

    /**
     * Model class name
     *
     * @var string
     */
    static protected $modelClassName = 'GeometriaLab\Model\AbstractModel';

    /**
     * Schema class name
     *
     * @var string
     */
    static protected $schemaClassName = 'GeometriaLab\Model\Schema\Schema';

    /**
     * Params serializer adapter
     *
     * @var string
     */
    static protected $paramsSerializerAdapter = 'json';

    protected function __construct(){}

    final private function __clone(){}

    /**
     * @static
     * @return DocBlockParser
     */
    static public function getInstance()
    {
        $calledClass = get_called_class();

        if (!isset(static::$instances[$calledClass])) {
            static::$instances[$calledClass] = new $calledClass();
        }

        return static::$instances[$calledClass];
    }

    /**
     * @param $className
     * @return Schema
     */
    public function createSchema($className)
    {
        return $this->parseDocBlock($className);
    }

    /**
     * Parse class docblock
     *
     * @param string $className
     * @return Schema
     * @throws \InvalidArgumentException
     */
    protected function parseDocBlock($className)
    {
        /* @var SchemaInterface $schema */
        $schema = new static::$schemaClassName();
        $schema->setClassName($className);

        $classReflection = new ZendClassReflection($className);

        try {
            do {
                $docBlocks[] = new ZendDocBlockReflection($classReflection);
                $classReflection = $classReflection->getParentClass();
            } while ($classReflection && $classReflection->getName() !== static::$modelClassName);

        } catch (ZendInvalidArgumentException $e) {
            throw new \InvalidArgumentException('DocBlock not present');
        }

        foreach (array_reverse($docBlocks) as $docBlock) {
            /* @var \Zend\Code\Reflection\DocBlockReflection $docBlock */
            foreach($docBlock->getTags() as $tag) {
                /* @var \Zend\Code\Reflection\DocBlock\Tag\TagInterface $tag */
                $methodName = "parse{$tag->getName()}Tag";
                if (method_exists($this, $methodName)) {
                    call_user_func(array($this, $methodName), $tag, $schema);
                }
            }
        }

        return $schema;
    }

    /**
     * Parse property tag
     *
     * @param \Zend\Code\Reflection\DocBlock\Tag\PropertyTag $tag
     * @param Schema $schema
     * @return Property\PropertyInterface
     * @throws \InvalidArgumentException
     */
    protected function parsePropertyTag(ZendPropertyTag $tag, Schema $schema)
    {
        $name = substr($tag->getPropertyName(), 1);

        $params = $this->getParamsFromTag($tag);
        $params['name'] = $name;

        $type = $tag->getType();

        if (strpos($type, '[]') === strlen($type) - 2) {
            $itemPropertyType = substr($type, 0, strlen($type) - 2);
            $params['itemProperty'] = static::createProperty($itemPropertyType);
            $type = 'array';
        }

        $validatorChain = null;
        if (isset($params['validators'])) {
            $validatorChain = $this->createValidatorChain($params['validators']);
            unset($params['validators']);
        }

        $filterChain = null;
        if (isset($params['filters'])) {
            $filterChain = $this->createFilterChain($params['filters']);
            unset($params['filters']);
        }

        $property = static::createProperty($type, $params);

        if ($validatorChain !== null) {
            $property->setValidatorChain($validatorChain);
        }
        if ($filterChain !== null) {
            $property->setFilterChain($filterChain);
        }

        $schema->addProperty($property);

        return $property;
    }

    /**
     * Get params from tag
     *
     * @param \Zend\Code\Reflection\DocBlock\Tag\TagInterface $tag
     * @return array|mixed
     * @throws \InvalidArgumentException
     */
    protected function getParamsFromTag(ZendTagInterface $tag)
    {
        $description = $tag->getDescription();

        if ($description == '') {
            return array();
        }

        $description = preg_replace('#\s+#m', ' ', $description);

        $params = ZendSerializer::unserialize($description, static::$paramsSerializerAdapter);

        if (!is_array($params)) {
            $name = substr($tag->getPropertyName(), 1);
            throw new \InvalidArgumentException("Not valid params for property '$name'");
        }

        return $params;
    }

    /**
     * Create property by type and params
     *
     * @static
     * @param string $type
     * @param array $params
     * @return PropertyInterface
     * @throws \InvalidArgumentException
     */
    static protected function createProperty($type, array $params = array())
    {
        if (isset(static::$regularPropertiesClassMap[$type])) {
            $property = new static::$regularPropertiesClassMap[$type]($params);
        } else if (class_exists($type)) {
            $params['modelClass'] = $type;

            $property = new static::$modelPropertyClass($params);
        } else {
            throw new \InvalidArgumentException("Invalid property type '$type'");
        }

        return $property;
    }

    /**
     * @param array $validators
     * @return ValidatorChain
     * @throws ZendRuntimeException
     */
    public function createValidatorChain(array $validators)
    {
        $validatorChain = new ValidatorChain();

        foreach ($validators as $validator) {
            if (is_string($validator)) {
                $validator = array(
                    'name' => $validator,
                );
            }
            if (is_array($validator)) {
                if (!isset($validator['name'])) {
                    throw new ZendRuntimeException('Invalid validator specification provided; does not include "name" key');
                }
                $options = array();
                if (isset($validator['options'])) {
                    $options = $validator['options'];
                }
                $breakOnFailure = false;
                if (isset($validator['breakOnFailure'])) {
                    $breakOnFailure = intval($validator['breakOnFailure']);
                }
                $validatorChain->addByName($validator['name'], $options, $breakOnFailure);
            } else {
                throw new ZendRuntimeException('Invalid validator declaration: need string or array');
            }
        }

        return $validatorChain;
    }

    /**
     * @param array $filters
     * @return ZendFilterChain
     * @throws ZendRuntimeException
     */
    public function createFilterChain(array $filters)
    {
        $filterChain = new ZendFilterChain();

        foreach ($filters as $filter) {
            if (is_string($filter)) {
                $filter = array(
                    'name' => $filter,
                );
            }
            if (is_array($filter)) {
                if (!isset($filter['name'])) {
                    throw new ZendRuntimeException('Invalid filter specification provided; does not include "name" key');
                }
                $options = array();
                if (isset($filter['options'])) {
                    $options = $filter['options'];
                }
                $priority = ZendFilterChain::DEFAULT_PRIORITY;
                if (isset($filter['priority'])) {
                    $priority = intval($filter['priority']);
                }
                $filterChain->attachByName($filter['name'], $options, $priority);
            } else {
                throw new ZendRuntimeException('Invalid filter declaration: need string or array');
            }
        }

        return $filterChain;
    }
}
