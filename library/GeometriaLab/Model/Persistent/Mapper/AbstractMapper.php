<?php

namespace GeometriaLab\Model\Persistent\Mapper;

use \GeometriaLab\Model\ModelInterface,
    \GeometriaLab\Api\Paginator\ModelPaginator;

abstract class AbstractMapper implements MapperInterface
{
    /**
     * Model class name
     *
     * @var string
     */
    protected $modelClass = '\GeometriaLab\Model\Persistent\Model';

    /**
     * Collection class name
     *
     * @var string
     */
    protected $collectionClass = '\GeometriaLab\Model\Persistent\Collection';

    /**
     * Primary key generator
     *
     * @var PrimaryKeyGeneratorInterface|null
     */
    protected $primaryKeyGenerator;

    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
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
     * Set model class name
     *
     * @param string $modelClass
     * @return AbstractMapper
     */
    public function setModelClass($modelClass)
    {
        $this->modelClass = $modelClass;

        return $this;
    }

    /**
     * Get model class name
     *
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * Set collection class
     *
     * @param string $collectionClass
     * @return AbstractMapper
     */
    public function setCollectionClass($collectionClass)
    {
        $this->collectionClass = $collectionClass;

        return $this;
    }

    /**
     * Get collection class
     *
     * @return string
     */
    public function getCollectionClass()
    {
        return $this->collectionClass;
    }

    /**
     * Set primary key generator
     *
     * @param string|PrimaryKeyGeneratorInterface $primaryKeyGenerator
     * @return AbstractMapper
     * @throws \InvalidArgumentException
     */
    public function setPrimaryKeyGenerator($primaryKeyGenerator)
    {
        if (is_string($primaryKeyGenerator)) {
            $primaryKeyGenerator = new $primaryKeyGenerator;
        }

        if (!$primaryKeyGenerator instanceof PrimaryKeyGeneratorInterface) {
            throw new \InvalidArgumentException('PrimaryKeyGeneratorInterface');
        }

        $this->primaryKeyGenerator = $primaryKeyGenerator;
        $this->primaryKeyGenerator->setMapper($this);

        return $this;
    }

    /**
     * Get primary key generator
     *
     * @return PrimaryKeyGeneratorInterface
     */
    public function getPrimaryKeyGenerator()
    {
        return $this->primaryKeyGenerator;
    }

    /**
     * Create query
     *
     * @return QueryInterface
     */
    public function createQuery()
    {
        return new Query($this);
    }
}
