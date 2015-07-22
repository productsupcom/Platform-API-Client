<?php

namespace Productsup\Service;
use Productsup\Exceptions\ClientException;
use Productsup\Platform\Project as PlatformProject;

class Feeds extends Service {
    protected $serviceName = 'projects';

    /**
     * @throws \Productsup\Exceptions\ClientException
     * @return \Productsup\Platform\Project
     */
    protected function getDataModel() {
        throw new ClientException('not needed for a feed');
    }

    /**
     * @param null $id
     * @return \Productsup\Platform\Project[]
     */
    public function get($id = null) {
        return $this->_get($id);
    }

    /**
     * creates a new project
     *
     * @param \Productsup\Platform\Project $Project
     * @return \Productsup\Platform\Project Project
     */
    public function insert(PlatformProject $Project) {
        return $this->_insert($Project);
    }


    /**
     * @param \Productsup\Platform\Project $Project
     * @return \Productsup\Platform\Project
     */
    public function update(PlatformProject $Project) {
        return $this->_update($Project);
    }

    /**
     * deletes a project and all sites in that project
     *
     * @param $id
     * @return boolean true on success
     */
    public function delete($id) {
        if($id instanceof PlatformProject) {
            $id = $id->id;
        }
        return $this->_delete($id);
    }


}
