<?php

namespace Productsup\Service;

use Productsup\Platform\Project;
use Productsup\Platform\Site;
use Productsup\Platform\User;

class Access extends Service
{
    protected $serviceName = 'access';
    protected $parent = 'sites';

    public function setSite(Site $site)
    {
        $this->_parentIdentifier = $site->id;
    }

    /**
     * @return \Productsup\Platform\Access
     */
    protected function getDataModel()
    {
        return new \Productsup\Platform\Access();
    }

    /**
     * @param null $id
     *
     * @return \Productsup\Platform\Access[]
     */
    public function get($id = null)
    {
        return $this->_get($id);
    }

    /**
     * insert one site
     * notice that you need to set a valid project with setProject() or add a valid project_id to the passed object.
     *
     * @param \Productsup\Platform\User $user
     *
     * @return Site
     */
    public function insert(User $user)
    {
        $accessModel = $this->getDataModel();
        if ($user->id) {
            $accessModel->login_id = $user->id;
        } else {
            $accessModel->user = $user;
        }

        return $this->_insert($accessModel);
    }

    /**
     * delete one site.
     *
     * @param int|Access $id
     *
     * @return bool
     */
    public function delete($id)
    {
        if ($id instanceof \Productsup\Platform\Access) {
            $id = $id->id;
        }

        return $this->_delete($id);
    }

    /**
     * update one existing site.
     *
     * @param \Productsup\Platform\Access $access
     *
     * @return \Productsup\Platform\Access
     */
    public function update(\Productsup\Platform\Access $access)
    {
        return $this->_update($access);
    }
}
