<?php

namespace Productsup\Platform;

class Source extends DataModel
{
    const TYPE_DEFAULT = 1;
    const TYPE_ADDITIONAL = 2;

    public $id;
    public $site_id;
    /** @var string url to import source */
    public $source;
    /** @var int one of the TYPE_* constants */
    public $import_type = self::TYPE_DEFAULT;
}
