<?php

namespace Productsup\Platform;

class Token extends DataModel
{
    public $id;
    public $user_id;
    public $token;
    public $created_at;
    public $user;
}
