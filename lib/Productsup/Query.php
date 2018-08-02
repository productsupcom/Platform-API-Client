<?php

namespace Productsup;

class Query
{
    /** @var string query conditions */
    public $filter = '';
    /** @var int limit for returned entries */
    public $limit = 5000;
    /** @var int offset for returned entries */
    public $offset = 0;
    /** @var array fields returned, if empty all fields are returned */
    public $fields = [];
    /** @var int should also hidden fields be included? */
    public $hidden = 0;
}
