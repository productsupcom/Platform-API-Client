<?php

namespace Productsup\Http;
use Productsup\Client as Client;

class Request
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_DELETE = 'DELETE';

    public $method = self::METHOD_GET;
    public $url;
    public $postBody;
    public $headers = array();

    private $_Client;

    /**
     * function __construct()
     *
     * @param Productsup\Client A client object
     */
    public function __construct(Client $Client)
    {
        $this->_Client = $Client;
        $this->headers['X-Auth-Token'] = $this->getToken();
        $this->headers['Accept'] = 'application/json';
    }

    /**
     * function getUserAgent()
     *
     * @return string UserAgent for HTTP Request
     */
    public function getUserAgent()
    {
        return 'Productsup API Client (PHP)';
    }

    /**
     * function getUserAgent()
     *
     * @return string Authentication Token for HTTP Request
     */
    public function getToken()
    {
        return sprintf('%s:%s', $this->_Client->id, $this->_Client->secret);
    }

}
