<?php

namespace Productsup\Service;
use Productsup\Exceptions;
use Productsup\Platform\Site;
use Productsup\Platform\User;

class Users extends Service {

    protected $serviceName = 'users';

    /**
     * @return \Productsup\Platform\DataModel|User
     */
    protected function getDataModel() {
        return new User();
    }

    /**
     * @param null $id
     * @return \Productsup\Platform\User[]
     */
    public function get($id = null) {
        return $this->_get($id);
    }

    /**
     * insert one site
     * notice that you need to set a valid project with setProject() or add a valid project_id to the passed object.
     * @param User $user
     * @return User
     */
    public function insert(User $user) {
        return $this->_insert($user);
    }

    /**
     * delete one site
     * @param int|User $id
     * @return bool
     */
    public function delete($id) {
        if($id instanceof User) {
            $id = $id->id;
        }
        return $this->_delete($id);
    }

    /**
     * update one existing site
     * @param User $user
     * @return User
     */
    public function update(User $user) {
        return $this->_update($user);
    }
}
