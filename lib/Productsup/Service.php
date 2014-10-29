<?php

namespace Productsup;
use Productsup\Client as Client;

abstract class Service
{
    /** var $host string Hostname */
    public $host;

    /** var $scheme string Scheme */
    public $scheme;

    /** var $version string API Service Version */
    public $version;

    /** var $serviceName string Service Name */
    public $serviceName;

    /** var $serviceName string Service Name */
    public $api;

    /** var $referenceId string Reference Id */
    public $referenceId;

    /** var $site string Platform Site Id */
    public $siteId;

    private $_Client;

    /**
     * function __construct()
     *
     * @param Productsup\Client A client object
     */
    public function __construct(Client $Client)
    {
        $this->_Client = $Client;
        $this->host = 'api.productsup.io';
        $this->scheme = 'http';
        $this->api = 'platform';
    }

    /**
     * function getClient()
     *
     * @return Productsup\Client A client object
     */
    public function getCLient()
    {
        return $this->_Client;
    }
}
