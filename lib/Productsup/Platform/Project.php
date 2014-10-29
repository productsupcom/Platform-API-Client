<?php

namespace Productsup\Platform;

class Project
{
    public $id;
    public $title;

    public function __construct(array $projectJsonResponse = null)
    {
        if ($projectJsonResponse !== null) {
            if(!isset($projectJsonResponse['id'])
            || !isset($projectJsonResponse['title'])) {
                throw new \Exception('Invalid JSON for Project Object');
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
