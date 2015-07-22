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
class Channels extends Service {

    protected $serviceName = 'channels';
    protected $parent = 'sites';

    public function setSite(Site $site) {
        $this->_parentIdentifier = $site->id;
    }

    /**
     * @return \Productsup\Platform\DataModel|Channel
     */
    protected function getDataModel() {
        return new Channel();
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
     * insert one site
     * notice that you need to set a valid project with setProject() or add a valid project_id to the passed object.
     * @param Channel $channel
     * @return Channel
     */
    public function insert(Channel $channel) {
        return $this->_insert($channel);
    }

    /**
     * delete one site
     * @param int|Channel $id
     * @return bool
     */
    public function delete($id) {
        if($id instanceof Channel) {
            $id = $id->id;
        }
        return $this->_delete($id);
    }

    /**
     * update one existing site
     * @param Site $site
     * @return Site
     */
    public function update(Site $site) {
        return $this->_update($site);
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
