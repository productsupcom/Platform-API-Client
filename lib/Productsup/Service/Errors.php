<?php

namespace Productsup\Service;

use Productsup\Exceptions;
use Productsup\Platform\Error;
use Productsup\Platform\Site;
use Productsup\Platform\Site\Reference;

/**
 * Class Error
 * get and add errors.
 */
class Errors extends Service
{
    protected $serviceName = 'errors';
    protected $parent = 'sites';

    public function setSite(Site $site)
    {
        $this->_parentIdentifier = $site->id;
    }

    /**
     * @return \Productsup\Platform\DataModel|Error
     */
    protected function getDataModel()
    {
        return new Error();
    }

    /**
     * @param null $id
     *
     * @return \Productsup\Platform\Error[]
     */
    public function get($id = null)
    {
        return $this->_get($id);
    }

    /**
     * insert one site
     * notice that you need to set a valid project with setProject() or add a valid project_id to the passed object.
     *
     * @param Error $error
     *
     * @return Error
     */
    public function insert(Error $error)
    {
        return $this->_insert($error);
    }

    /**
     * delete one site.
     *
     * @param int|Error $id
     *
     * @return bool
     */
    public function delete($id)
    {
        if ($id instanceof Error) {
            $id = $id->id;
        }

        return $this->_delete($id);
    }

    /**
     * define the site these products belong to.
     *
     * @param Reference $reference
     *
     * @throws Exceptions\ClientException
     */
    public function setReference(Reference $reference)
    {
        $this->_parentIdentifier = (string) $reference;
    }
}
