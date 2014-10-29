<?php

namespace Productsup\Platform;

class Site
{
    public $id;
    public $title;

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
            'title' => $this->title
        ));
    }    
}
