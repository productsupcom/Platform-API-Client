<?php
/**
 * Parent for all data models, with abstraction for mutually used functions
 */
namespace Productsup\Platform;

class DataModel {
    public $id;

    protected $links;

    protected $reference;
    /**
     * @param null|array $data data to initialise
     */
    public function __construct($data = null) {
        if(is_array($data)) {
            return $this->fromArray($data);
        }
        return $this;
    }

    /**
     * @param array $data
     * @return static
     */
    public function fromArray(array $data) {
        $public = $this->getPublicProperties();
        foreach($data as $key => $value) {
            if(in_array($key,$public)) {
                $this->$key = $value;
            }
        }
        if(isset($data['links']) && is_array($data['links'])) {
            foreach($data['links'] as $row) {
                foreach($row as $type => $val) {
                    $this->links[$type] = $val;
                }
            }
        }
        return $this;
    }

    public function getLink($type) {
        return isset($this->links[$type]) ? $this->links[$type] : null;
    }

    /**
     * cast data to an array
     * @return array
     */
    public function toArray() {
        $result = array();
        foreach($this->getPublicProperties() as $property) {
            $result[$property] = $this->$property;
        }
        if($this->reference) {
            $result['reference'] = (string)$this->reference;
        }
        return $result;
    }

    /**
     * returns all public properties of current class
     * @return array
     */
    protected function getPublicProperties() {
        $reflect = new \ReflectionClass($this);
        $result = array();
        foreach($reflect->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $result[] = $property->getName();
        }
        return $result;
    }
}