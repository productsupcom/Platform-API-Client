<?php

namespace Productsup\Platform;
use Productsup\Exception as Exception;

class Tag
{
    public $key;
    public $title;

    public function __construct(array $tagJsonResponse = null)
    {
        if ($tagJsonResponse !== null) {
            if(!isset($tagJsonResponse['key'])
            || !isset($tagJsonResponse['value'])) {
                throw new Exception(Exception::E_INVALID_JSON_FOR_OBJECT);
            }

            $this->key = $tagJsonResponse['key'];
            $this->value = $tagJsonResponse['value'];
        }
    }

    public function __toString()
    {
        return json_encode(array(
            'key' => $this->key, 
            'value' => $this->value
        ));
    }    
}
