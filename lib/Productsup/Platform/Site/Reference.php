<?php

namespace Productsup\Platform\Site;

class Reference
{
    const REFERENCE_SITE = 'pup-site';
    const REFERENCE_PROJECT = 'pup-project';

    private $_key;
    private $_value;

    /**
     * function setKey()
     * 
     * @param $key string Reference Key. Allowed chars [a-z0-9_]
     */
    public function setKey($key)
    {
        if (!preg_match('/^[a-z0-9_]+$/', $key)) {
            throw new Exception('Invalid Reference Key ([a-z0-9_])');
        }
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
