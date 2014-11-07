<?php

namespace Productsup\Service;
use Productsup\Platform\Project as PlatformProject;
use Productsup\Platform\Site as PlatformSite;
use Productsup\Exceptions;
use Productsup\Platform\Site;

class Sites extends Service {

    private $_parentObject;
    protected $serviceName = 'sites';
    protected $parent = 'projects';

    public function setProject(PlatformProject $Project) {
        $this->_parentObject = $Project;
    }

    /**
     * @return \Productsup\Platform\DataModel|Site
     */
    protected function getDataModel() {
        return new Site();
    }

    /**
     * @param null $id
     * @return \Productsup\Platform\Site[]
     */
    public function get($id = null) {
        return $this->_get($id);
    }

    /**
     * insert one site
     * notice that you need to set a valid project with setProject() or add a valid project_id to the passed object.
     * @param Site $Site
     * @return Site
     */
    public function insert(Site $Site) {
        return $this->_insert($Site);
    }

    /**
     * delete one site
     * @param int|Site $id
     * @return bool
     */
    public function delete($id) {
        if($id instanceof Site) {
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
     * returns the base url for the api and current resource
     * @return string
     */
    protected function getBaseUrl() {
        $url = $this->scheme.'://'.$this->host.'/'.$this->api.'/'.$this->version.'/';

        if($this->_parentObject) {
            $url .= $this->parent.'/'.$this->_parentObject->id.'/';
        }
        $url .= $this->serviceName;
        return $url;
    }


}
