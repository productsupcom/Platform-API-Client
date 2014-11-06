<?php

namespace Productsup\Service;
use Productsup\Client;
use Productsup\Http\Request;
use Productsup\IO\Curl;

abstract class Service
{
    /** @var string Hostname */
    protected $host = 'api.productsup.io';

    /** @var string Scheme */
    protected $scheme = 'http';

    /** @var string Service Version */
    protected $version = 'v1';

    /** @var string Service Name */
    protected $serviceName;

    /** @var string Service Name */
    protected $api = 'platform';

    /** @var \Productsup\Client Client */
    protected $_Client;
    protected $_postLimit = 5000;


    /**
     * @param Client $Client
     */
    public function __construct(Client $Client) {
        $this->_Client = $Client;
    }

    public function setPostLimit($limit)
    {
        if ($limit < 1) {
            throw new Exception('Post limit lower 1 not allowed');
        } elseif ($limit > 10000) {
            throw new Exception('Post limit higher 10.000 not allowed');
        }

        $this->_postLimit = $limit;
    }

    public function getPostLimit()
    {
        return $this->_postLimit;
    }

    /**
     * @return Client
     */
    public function getCLient()
    {
        return $this->_Client;
    }

    /**
     * returns a new request object
     * @return Request
     */
    protected function getRequest() {
        $request = new Request($this->getClient());
        $request->url = $this->getBaseUrl();
        return $request;
    }

    /**
     * returns the base url for the api and current resource
     * @return string
     */
    protected function getBaseUrl() {
        return $this->scheme.'://'.$this->host.'/'.$this->api.'/'.$this->version.'/'.$this->serviceName;
    }

    /**
     * returns the class that handles network requests, right now only Curl is supported
     * @return Curl
     */
    protected function getIoHandler() {
        return new Curl();
    }

    /**
     * executes the current request and returns it's result as an array
     * @param $request
     * @return array
     */
    protected function executeRequest($request) {
        $response = $this->getIoHandler()->executeRequest($request);
        return $response->getData();
    }
}
