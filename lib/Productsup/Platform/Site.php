<?php

namespace Productsup\Platform;

class Site extends DataModel
{
    public $id;
    public $title;
    public $created_at;
    public $project_id;

    /** @var string in crontab format (compatible to jenkins, with "H" for "random") */
    public $import_schedule;
}
