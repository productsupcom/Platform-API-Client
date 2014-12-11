<?php

namespace Productsup\Platform;

class User extends DataModel {
    public $id;
    public $email;
    public $client_id;
    public $token;
    public $created_at;
    public $updated_at;
    public $client_name;
}
