<?php

namespace Productsup\Http;
use Productsup\Client as Client;

class Request
{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PUT = 'PUT';

    public $method = self::METHOD_GET;
    public $url;
    public $postBody;
    private $headers = array();

    private $_Client;

    /**
     * @param \Productsup\Client A client object
     */
    public function __construct(Client $Client) {
        $this->_Client = $Client;
        $this->headers['X-Auth-Token'] = $this->_Client->getToken();
        $this->headers['Accept'] = 'application/json';
    }

    /**
     *
     * @return string UserAgent for HTTP Request
     */
    public function getUserAgent()
    {
        return 'Productsup API Client (PHP)';
    }

    public function getBody() {
        $body = null;
        if($this->hasData()) {
            return $this->encodeData();
        }
        return $body;
    }

    public function hasData() {
        return $this->postBody && is_array($this->postBody);
    }

    protected function encodeData() {
        return json_encode($this->postBody);
    }

    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
    }


    public function getHeaders() {
        if($this->hasData()) {
            $this->setHeader('Content-Type','application/json');
        }

        $headers = array();
        if(!empty($this->headers)) {
            foreach($this->headers as $key => $value) {
                $headers[] = $key.': '.$value;
            }
        }
        return $headers;
    }


}
