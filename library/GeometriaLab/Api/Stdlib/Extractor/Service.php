<?php

namespace GeometriaLab\Api\Stdlib\Extractor;

use Zend\ServiceManager\FactoryInterface as ZendFactoryInterface,
    Zend\ServiceManager\ServiceLocatorInterface as ZendServiceLocatorInterface,
    Zend\Stdlib\Exception\BadMethodCallException as ZendBadMethodCallException;

use GeometriaLab\Model\ModelInterface,
    GeometriaLab\Model\CollectionInterface,
    GeometriaLab\Api\Paginator\ModelPaginator,
    GeometriaLab\Api\Stdlib\Extractor\Extractor,
    GeometriaLab\Api\Exception\InvalidFieldsException;

class Service implements ZendFactoryInterface
{
    /**
     * @var Extractor[]
     */
    static private $extractorInstances;
    /**
     * @var string
     */
    private $extractorsNamespace;
    /**
     * Invalid fields
     *
     * @var array
     */
    private $invalidFields = array();

    /**
     * @param ZendServiceLocatorInterface $serviceLocator
     * @return Service
     * @throws \InvalidArgumentException
     */
    public function createService(ZendServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Configuration');

        if (!isset($config['extractor'])) {
            throw new \InvalidArgumentException('Need not empty "extractor" param in config');
        }
        if (!isset($config['extractor']['__NAMESPACE__'])) {
            throw new \InvalidArgumentException('Need not empty "extractor.__NAMESPACE__" param in config');
        }

        $this->setNamespace($config['extractor']['__NAMESPACE__']);

        return $this;
    }

    /**
     * @param string $namespace
     * @return Service
     */
    public function setNamespace($namespace)
    {
        $this->extractorsNamespace = $namespace;
        return $this;
    }

    /**
     * @param ModelInterface|CollectionInterface|ModelPaginator $data
     * @param array $fields
     * @return array
     * @throws InvalidFieldsException
     * @throws ZendBadMethodCallException
     * @throws \InvalidArgumentException
     */
    public function extract($data, $fields = array())
    {
        if ($this->extractorsNamespace === null) {
            throw new \InvalidArgumentException('Need setup Namespace');
        }

        $extractFields = $fields;
        if (isset($fields['*'])) {
            $extractFields = array();
        }

        if ($data instanceof ModelPaginator) {
            $collection = $data->getItems();
        } else if ($data instanceof CollectionInterface) {
            $collection = $data;
        } else if ($data instanceof ModelInterface) {
            $collection = new \GeometriaLab\Model\Collection();
            $collection->push($data);
        } else {
            throw new \InvalidArgumentException('Data must be CollectionInterface, ModelInterface or ModelPaginator');
        }

        if (!$collection->isEmpty()) {
            $firstModel = $collection->getFirst();

            $parts = explode('\\', get_class($firstModel));
            $type = array_pop($parts);
            $extractorName = $this->extractorsNamespace . '\\' . $type;

            if (!isset(static::$extractorInstances[$extractorName])) {
                if (!is_subclass_of($extractorName, '\GeometriaLab\Api\Stdlib\Extractor\Extractor')) {
                    throw new ZendBadMethodCallException("Invalid extractor for model '" . get_class($firstModel) . "'");
                }
                static::$extractorInstances[$extractorName] = new $extractorName;
            }

            $extractor = static::$extractorInstances[$extractorName];

            $dataCollection = array();
            foreach ($collection as $model) {
                $fieldsData = $extractor->extract($model, $extractFields);
                $invalidFields = $extractor->getInvalidFields();

                foreach ($fieldsData as $name => $field) {
                    // @todo Fetch all available relations
                    if ($field instanceof ModelInterface || $fields instanceof CollectionInterface) {
                        $parentExtractFields = isset($fields[$name]) ? $fields[$name] : array();
                        $fieldsData[$name] = $this->extract($field, $parentExtractFields);
                        $subInvalidFields = $this->getInvalidFields();
                        if (!empty($subInvalidFields)) {
                            $invalidFields[$name] = $subInvalidFields;
                        }
                    }
                }
                $dataCollection[] = $fieldsData;
            }
        } else {
            $dataCollection = array();
            $type = null;
        }

        $this->invalidFields = $invalidFields;

        $extractedData = array();

        if ($data instanceof ModelPaginator) {
            $extractedData['items'] = $dataCollection;
            $extractedData['totalCount'] = empty($dataCollection) ? 0 : count($data);
            $extractedData['limit']  = $data->getLimit();
            $extractedData['offset'] = $data->getOffset();
        } else if ($data instanceof CollectionInterface) {
            $extractedData['items'] = $dataCollection;
        } else if ($data instanceof ModelInterface) {
            $extractedData['item'] = $dataCollection[0];
        }

        if ($type !== null) {
            $extractedData['type'] = $type;
        }

        return $extractedData;
    }

    /**
     * Get invalid fields
     *
     * @return array
     */
    public function getInvalidFields()
    {
        return $this->invalidFields;
    }
}
