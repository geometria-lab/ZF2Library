<?php

class GeometriaLab_VBuckets_Bucket
{
    protected $_data = array();

    public function __construct($id, array $data = array())
    {
        $this->_data = $data;
        $this->_data['id'] = $id;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __get($name)
    {
        if (!isset($this->_data[$name])) {
            throw new GeometriaLab_VBuckets_Exception('Invalid vbucket property');
        }

        return $this->_data[$name];
    }
}
 
