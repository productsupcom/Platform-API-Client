<?php

namespace Productsup\Platform;

class Tag extends DataModel
{
    public $id;
    public $key;
    public $value;
    public $site_id;
    public $readonly;

    public function __toString()
    {
        return $this->key . ':' . $this->value;
    }
}
