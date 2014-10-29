<?php

namespace Productsup\Platform;

use Productsup\Platform\Site\Reference as Reference;
use Productsup\Exception as Exception;

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
                throw new Exception(Exception::E_INVALID_JSON_FOR_OBJECT);
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
        if ($Reference->getKey() == $Reference::REFERENCE_SITE) {
            throw new Exception(Exception::E_REFERENCE_TO_SITE);
        }
        $this->_references[] = $Reference->toArray();
    }
}
