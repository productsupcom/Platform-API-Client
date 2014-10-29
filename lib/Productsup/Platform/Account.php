<?php

namespace Productsup\Platform;

class Account
{
    public $id;

    public function __toString()
    {
        return json_encode(array(
            'id' => $this->id
        ));
    }
}
