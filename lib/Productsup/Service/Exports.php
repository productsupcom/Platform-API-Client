<?php

namespace Productsup\Service;
use Productsup\Exceptions;
use Productsup\Platform\Destination;
use Productsup\Platform\Export;
use Productsup\Platform\Site;
use Productsup\Platform\Site\Reference;

/**
 * Class Exports
 * exports define where the content of sites get exported to
 * @deprecated new sites will use Channels instead of Exports
 */
class Exports extends Service {

    protected $serviceName = 'exports';
    protected $parent = 'sites';

    const GOOGLE_CONTENT_API = 3337;

    public function setSite(Site $site) {
        $this->_parentIdentifier = $site->id;
    }

    /**
     * @return \Productsup\Platform\DataModel|Export
     */
    protected function getDataModel() {
        return new Export();
    }

    /**
     * @param null $id
     * @return \Productsup\Platform\Export[]
     */
    public function get($id = null) {
        return $this->_get($id);
    }

    /**
     * insert one site
     * notice that you need to set a valid project with setProject() or add a valid project_id to the passed object.
     * @param Export $export
     * @return Export
     */
    public function insert(Export $export) {
        return $this->_insert($export);
    }

    /**
     * delete one site
     * @param int|Export $id
     * @return bool
     */
    public function delete($id) {
        if($id instanceof Export) {
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

    /**
     * creating a new export to a merchant center
     * @param string $merchantCenterId google merchant center id
     * @param string $merchantCenterParentId parent of current merchant center id
     * @param string $targetLanguage language of the target, in ISO 3166-2 (2 letter country code)
     * @param string $targetCountry country of the target, in ISO 3166-2 (2 letter country code)
     * @return bool true on success
     */
    public function enableContentApi($merchantCenterId, $merchantCenterParentId, $targetLanguage, $targetCountry) {
        $export = $this->getDataModel();
        $export->export_id = 3337;
        $export = $this->insert($export);

        $destinationService = new Destinations($this->_Client);
        $destinationService->setExport($export);

        $destinationModel = new Destination();
        $destinationModel->merchant_center_id = $merchantCenterId;
        $destinationModel->merchant_center_parent_id = $merchantCenterParentId;
        $destinationModel->target_country = $targetCountry;
        $destinationModel->target_language = $targetLanguage;

        $insertedModel = $destinationService->insert($destinationModel);
        return $insertedModel instanceof Destination;
    }
}
