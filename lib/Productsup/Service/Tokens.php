<?php

namespace Productsup\Service;

use Productsup\Platform\Token;
use Productsup\Platform\User;

class Tokens extends Service
{
    protected $serviceName = 'tokens';
    protected $parent = 'users';

    public function setUser(User $user)
    {
        $this->_parentIdentifier = $user->id;
    }

    /**
     * @return Token
     */
    protected function getDataModel()
    {
        return new Token();
    }

    /**
     * @param null $id
     *
     * @return Token[]
     */
    public function get($id = null)
    {
        return $this->_get($id);
    }

    /**
     * @param Token $token
     *
     * @return Token $token
     */
    public function insert(Token $token)
    {
        return $this->_insert($token);
    }

    /**
     * delete one token.
     *
     * @param int|Token $id
     *
     * @return bool
     */
    public function delete($id)
    {
        if ($id instanceof Token) {
            $id = $id->id;
        }

        return $this->_delete($id);
    }

    /**
     * update one existing token.
     *
     * @param Token $token
     *
     * @return Token
     */
    public function update(Token $token)
    {
        return $this->_update($token);
    }
}
