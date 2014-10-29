<?php

namespace Productsup;
use Productsup\Client as Client;
use Productsup\Platform\Site\Reference as Reference;

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

    /** var $site string Platform Site Id */
    public $siteId;


    private $_Client;
    private $_Reference;
    private $_postLimit = 5000;

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

    public function setReference(Reference $Reference)
    {
        $this->_Reference = $Reference;
    }

    public function getReference()
    {
        return $this->_Reference;
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
