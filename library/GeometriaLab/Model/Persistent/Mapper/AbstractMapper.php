<?php

namespace GeometriaLab\Model\Persistent\Mapper;

use \GeometriaLab\Model\PersistentInterface;

use \Zend\Stdlib\Options as ZendOptions;

abstract class AbstractMapper implements MapperInterface
{
    /**
     * Model class name
     *
     * @var string
     */
    protected $modelClass = '\GeometriaLab\Model\Persistent';

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
     * Property names map
     *
     * @var array
     */
    protected $propertyNamesMap = array();

    /**
     * Constructor
     *
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function __construct(array $options = array())
    {
        foreach($options as $option => $value) {
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
     * Validate model
     *
     * @param PersistentInterface $model
     * @throws \InvalidArgumentException
     */
    protected function validateModel(PersistentInterface $model)
    {
        if (!is_a($model, $this->getModelClass())) {
            throw new \InvalidArgumentException("Model must be {$this->getModelClass()}");
        }
    }

    /**
     * Transform model data for storage
     *
     * @param array $data
     * @return array
     */
    protected function transformModelDataForStorage(array $data)
    {
        foreach($this->propertyNamesMap as $model => $storage) {
            $data[$storage] = $data[$model];
            unset($data[$model]);
        }

        return $data;
    }

    /**
     * Transform storage data for model
     *
     * @param array $data
     * @return array
     */
    protected function transformStorageDataForModel(array $data)
    {
        foreach($this->propertyNamesMap as $model => $storage) {
            $data[$model] = $data[$storage];
            unset($data[$storage]);
        }

        return $data;
    }
}
