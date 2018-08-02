<?php

namespace Productsup\Platform;

use Productsup\Exceptions\ClientException;
use Productsup\Platform\Site\Reference;

class Process extends DataModel
{
    public $action;
    public $action_id;
    public $site_id;

    /**
     * adds a reference to a site that can later be used as an identifier
     * note: this is only possible when creating a site or project.
     *
     * @param Reference $reference
     *
     * @throws ClientException When adding non site reference
     */
    public function addReference(Reference $reference)
    {
        if ($reference->getKey() != Reference::REFERENCE_SITE) {
            throw new ClientException('Process only accepts site as a reference.');
        }

        $this->site_id = $reference->getValue();
        parent::addReference($reference);
    }

    /**
     * cast data to an array.
     *
     * @param bool $full
     *
     * @return array
     */
    public function toArray($full = true)
    {
        $data = [
            'id'     => $this->action_id,
            'action' => $this->action,
        ];

        if ($full) {
            $data['site_id'] = $this->site_id;
        }

        return $data;
    }
}
