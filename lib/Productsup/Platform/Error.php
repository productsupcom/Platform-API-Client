<?php

namespace Productsup\Platform;

class Error extends DataModel
{
    public $id;
    /** @var string process id */
    public $pid;
    /** @var int id of the error */
    public $error;
    /** @var array additional infos */
    public $data;
}
