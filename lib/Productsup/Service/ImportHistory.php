<?php

namespace Productsup\Service;
use Productsup\Exceptions;
use Productsup\Platform\Channel;
use Productsup\Platform\Site;
use Productsup\Platform\Site\Reference;

/**
 * Class Channels
 * exports define where the content of sites get exported to
 */
class ImportHistory extends Service {

    protected $serviceName = 'importhistory';
    protected $parent = 'sites';

    public function setSite(Site $site) {
        $this->_parentIdentifier = $site->id;
    }

    /**
     * @return \Productsup\Platform\DataModel|\Productsup\Platform\ImportHistory
     */
    protected function getDataModel() {
        return new \Productsup\Platform\ImportHistory();
    }

    /**
     * @param string|null $id
     * @param string|null $action additional action (i.e. "history" to get also the history)
     * @return \Productsup\Platform\Export[]
     */
    public function get($id = null,$action = null) {
        return $this->_get($id, $action);
    }

    /**
     * define the site these products belong to
     * @param Reference $reference
     * @throws Exceptions\ClientException
     */
    public function setReference(Reference $reference) {
        $this->_parentIdentifier = (string)$reference;
    }

}
