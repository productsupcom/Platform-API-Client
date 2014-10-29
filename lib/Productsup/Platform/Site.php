<?php

namespace Productsup\Platform;

use Productsup\Platform\Site\Reference as Reference;

class Site
{
    public $id;
    public $title;
    private $_references = array();

    public function __construct(array $siteJsonResponse = null)
    {
        if ($siteJsonResponse !== null) {
            if(!isset($siteJsonResponse['id'])
            || !isset($siteJsonResponse['title'])) {
                throw new \Exception('Invalis JSON for Site Object');
            }

            $this->id = $siteJsonResponse['id'];
            $this->title = $siteJsonResponse['title'];
        }
    }

    public function __toString()
    {
        return json_encode(array(
            'id' => $this->id, 
            'title' => $this->title,
            'references' => $this->_references
        ));
    }    

    public function addReference(Reference $Reference)
    {
        $this->_references[] = $Reference->toArray();
    }
}
