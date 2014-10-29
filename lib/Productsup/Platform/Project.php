<?php

namespace Productsup\Platform;
use Productsup\Exception as Exception;

class Project
{
    public $id;
    public $title;

    public function __construct(array $projectJsonResponse = null)
    {
        if ($projectJsonResponse !== null) {
            if(!isset($projectJsonResponse['id'])
            || !isset($projectJsonResponse['title'])) {
                throw new Exception(Exception::E_INVALID_JSON_FOR_OBJECT);
            }

            $this->id = $projectJsonResponse['id'];
            $this->title = $projectJsonResponse['title'];
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
