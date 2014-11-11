<?php

namespace Productsup\Platform;

use Productsup\Platform\Site\Reference;

class Site extends DataModel {
    public $id;
    public $title;
    public $created_at;
    public $project_id;

    /**
     * adds a reference to a site that can later be used as an identifier
     * note: this is only possible when creating a site
     * @param Reference $reference
     */
    public function addReference(Reference $reference) {
        $this->reference = $reference;
    }
}
