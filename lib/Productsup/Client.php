<?php

namespace Productsup;

use Productsup\Exceptions\ClientException;

class Client {
    /** var $id int Productsup Account Id */
    public $id;

    /** var $secret string Productsup Account Secret */
    public $secret;

    /**
     * generates access token from given credentials
     */
    public function getToken() {
        if(!$this->id) {
            throw new ClientException('there is no id set to your client, please add the id you identify with');
        }
        if(!$this->secret) {
            throw new ClientException('there is no secret set to your client, please add the secret you identify with');
        }
        return sprintf('%s:%s', $this->id, $this->secret);
    }
}
