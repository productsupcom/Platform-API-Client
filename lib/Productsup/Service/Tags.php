<?php

namespace Productsup\Service;

use Productsup\Platform\Site;
use Productsup\Platform\Tag;

class Tags extends Service
{
    protected $serviceName = 'tags';
    protected $parent = 'sites';

    /**
     * adds the site the tags should reference to.
     *
     * @param Site $site
     */
    public function setSite(Site $site)
    {
        $this->_parentIdentifier = $site->id;
    }

    /**
     * @return \Productsup\Platform\DataModel|Tag
     */
    protected function getDataModel()
    {
        return new Tag();
    }

    /**
     * @param null $id
     *
     * @return \Productsup\Platform\Tag[]
     */
    public function get($id = null)
    {
        return $this->_get($id);
    }

    /**
     * insert one site
     * notice that you need to set a valid project with setSite() or add a valid site_id to the passed object.
     *
     * @param Tag $tag
     *
     * @return Tag
     */
    public function insert(Tag $tag)
    {
        return $this->_insert($tag);
    }

    /**
     * delete one site.
     *
     * @param int|Tag $id
     *
     * @return bool
     */
    public function delete($id)
    {
        if ($id instanceof Tag) {
            $id = $id->id;
        }

        return $this->_delete($id);
    }

    /**
     * update one existing site.
     *
     * @param \Productsup\Platform\Tag $tag
     *
     * @return Tag
     */
    public function update(Tag $tag)
    {
        return $this->_update($tag);
    }
}
