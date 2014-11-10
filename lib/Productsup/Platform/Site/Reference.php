<?php

namespace Productsup\Platform\Site;
use Productsup\Exception as Exception;

class Reference
{
    const REFERENCE_SITE = 'pup-site';
    const REFERENCE_PROJECT = 'pup-project';

    private $_key;
    private $_value;

    public function __toString()
    {
        return json_encode(array(
            'key' => $this->_key, 
            'value' => $this->_value
        ));
    }    

    public function toArray()
    {
        return array(
            'key' => $this->_key, 
            'value' => $this->_value
        );
    }

    /**
     * function setKey()
     * 
     * @param $key string Reference Key. Allowed chars [a-z0-9_]
     */
    public function setKey($key)
    {
        $this->_key = $key;
    }

    /**
     * function setValue()
     * 
     * @param $value string Reference value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * function getKey()
     * 
     * @return string Reference key
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * function getValue()
     * 
     * @return string Reference value
     */
    public function getValue()
    {
        return $this->_value;
    }
}
