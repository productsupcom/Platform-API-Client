<?php

namespace Productsup\Service;
use Productsup\Exceptions;
use Productsup\Platform\Destination;
use Productsup\Platform\Export;
use Productsup\Platform\Site;

/**
 * Class Destinations
 * destinations define where a export exports to, one export may have more than one destination
 */
class Destinations extends Service {

    protected $serviceName = 'destinations';
    protected $parent = 'exports';

    public function setExport(Export $export) {
        $this->_parentIdentifier = $export->id;
    }

    /**
     * @return \Productsup\Platform\DataModel|Destination
     */
    protected function getDataModel() {
        return new Destination();
    }

    /**
     * @param null $id
     * @return \Productsup\Platform\Destination[]
     */
    public function get($id = null) {
        return $this->_get($id);
    }

    /**
     * insert one destination
     * notice that you need to set a valid project with setProject() or add a valid project_id to the passed object.
     * @param Destination $destination
     * @return Destination
     */
    public function insert(Destination $destination) {
        return $this->_insert($destination);
    }

    /**
     * delete one site
     * @param int|Destination $id destination to delete
     * @return bool
     */
    public function delete($id) {
        if($id instanceof Destination) {
            $id = $id->id;
        }
        return $this->_delete($id);
    }

    /**
     * update one existing destination
     * @param Destination $destination
     * @return Destination
     */
    public function update(Destination $destination) {
        return $this->_update($destination);
    }

}
