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
    private $headers = [];

    public $queryParams = [];

    public $allowCompression = true;

    private $_Client;

    /**
     * @param \Productsup\Client A client object
     * @param bool   $productsUpAuth
     * @param Client $Client
     */
    public function __construct(Client $Client, $productsUpAuth = true)
    {
        $this->_Client = $Client;
        if ($productsUpAuth) {
            $this->setHeader('X-Auth-Token', $this->_Client->getToken());
        }
        $this->setHeader('Accept', 'application/json');
        $this->setHeader('X-Powered-By', PHP_VERSION);
    }

    /**
     * @return string UserAgent for HTTP Request
     */
    public function getUserAgent()
    {
        return 'Productsup API Client (PHP)';
    }

    public function getBody()
    {
        $body = null;
        if ($this->hasData()) {
            return $this->encodeData();
        }

        return $body;
    }

    public function hasData()
    {
        return $this->postBody && \is_array($this->postBody);
    }

    protected function encodeData()
    {
        $encoded = json_encode($this->postBody);
        if ($this->allowCompression && \function_exists('gzdeflate')) {
            $encoded = gzdeflate($encoded, 9);
            $this->setHeader('Content-Encoding', 'gzip');
        }

        return $encoded;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    public function getHeaders()
    {
        if ($this->hasData()) {
            $this->setHeader('Content-Type', 'application/json');
        }

        $headers = [];
        if (!empty($this->headers)) {
            foreach ($this->headers as $key => $value) {
                $headers[] = $key . ': ' . $value;
            }
        }

        return $headers;
    }

    public function getUrl()
    {
        $url = $this->url;
        if ($this->queryParams) {
            $url .= '?' . http_build_query($this->queryParams);
        }

        return $url;
    }

    public function verboseOutput()
    {
        echo "Request:\n\n" . $this->method . ': ' . $this->getUrl() . " \nHeaders:" . implode("\n", $this->getHeaders()) . "\nBody:" . $this->getBody();
    }
}
