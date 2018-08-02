<?php

namespace Productsup\Service;

use Productsup\Platform\Site;
use Productsup\Platform\Source;

/**
 * Class Destinations
 * destinations define where a export exports to, one export may have more than one destination.
 */
class Sources extends Service
{
    protected $serviceName = 'Sources';
    protected $parent = 'sites';

    public function setSite(Site $site)
    {
        $this->_parentIdentifier = $site->id;
    }

    /**
     * @return \Productsup\Platform\DataModel|Source
     */
    protected function getDataModel()
    {
        return new Source();
    }

    /**
     * @param null $id
     *
     * @return \Productsup\Platform\Source[]
     */
    public function get($id = null)
    {
        return $this->_get($id);
    }

    /**
     * insert one destination
     * notice that you need to set a valid project with setProject() or add a valid project_id to the passed object.
     *
     * @param Source $source
     *
     * @return Source
     */
    public function insert(Source $source)
    {
        return $this->_insert($source);
    }

    /**
     * delete one site.
     *
     * @param int|Source $id Source to delete
     *
     * @return bool
     */
    public function delete($id)
    {
        if ($id instanceof Source) {
            $id = $id->id;
        }

        return $this->_delete($id);
    }

    /**
     * update one existing destination.
     *
     * @param Source $source
     *
     * @return Source
     */
    public function update(Source $source)
    {
        return $this->_update($source);
    }
}
