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
        $this->setHeader('X-Auth-Token',$this->_Client->getToken());
        $this->setHeader('Accept', 'application/json');
        $this->setHeader('X-Powered-By',phpversion());
    }

    /**
     *
     * @return string UserAgent for HTTP Request
     */
    public function getUserAgent()
    {
        return 'Productsup API Client (PHP)';
    }

    public function getBody($allowCompression = true) {
        $body = null;
        if($this->hasData()) {
            return $this->encodeData($allowCompression);
        }
        return $body;
    }

    public function hasData() {
        return $this->postBody && is_array($this->postBody);
    }

    protected function encodeData($allowCompression = true) {
        $encoded = json_encode($this->postBody);
        if($allowCompression && function_exists('gzdeflate')) {
            $encoded = gzdeflate($encoded, 9);
            $this->setHeader('Content-Encoding','gzip');
        }
        return $encoded;
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

    public function verboseOutput() {
        echo "Request:\n\n".$this->method.": ".$this->url." \nHeaders:".join("\n",$this->getHeaders())."\nBody:".$this->getBody(false);
    }


}
