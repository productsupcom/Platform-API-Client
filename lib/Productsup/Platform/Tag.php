<?php

namespace Productsup\Platform;

use Productsup\Exceptions\ClientException;

class Tag extends DataModel {
    public $key;
    public $title;

    public function __toString() {
        return $this->key.':'.$this->title;
    }

    private function isValidString($str) {
        if(preg_match('/[a-zA-Z0-9]+/si',$str)) {
            return true;
        }
        return false;
    }
}
