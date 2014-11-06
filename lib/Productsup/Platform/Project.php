<?php

namespace Productsup\Platform;
use Productsup\Exceptions;

class Project
{
    public $id;
    public $name;
    public $created_at;

    public function __construct($data = null) {
        if(is_array($data)) {
            return $this->fromArray($data);
        }
        return $this;
    }

    public function fromArray(array $data) {
        $public = $this->getPublicProperties();
        foreach($data as $key => $value) {
            if(in_array($key,$public)) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    public function toArray() {
        $result = array();
        foreach($this->getPublicProperties() as $property) {
            $result[$property] = $this->$property;
        }
        return $result;
    }

    public function __toString() {
        return json_encode($this->toArray());
    }

    protected function getPublicProperties() {
        $reflect = new \ReflectionClass($this);
        $result = array();
        foreach($reflect->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $result[] = $property->getName();
        }
        return $result;
    }

}
