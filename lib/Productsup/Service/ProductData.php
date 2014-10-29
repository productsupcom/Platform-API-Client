<?php

namespace Productsup\Service;
use Productsup\Service as Service;
use Productsup\Client as Client;
use Productsup\Http\Request as Request;
use Productsup\IO\Curl as Curl;

class ProductData extends Service
{
    const URL_REFERENCEID = '%s://%s/%s/%s/refid/%s/%s';
    const URL_SITEID = '%s://%s/%s/%s/site/%s/%s';

    private $_insert = array();
    private $_delete = array();

    /**
     * function __construct()
     *
     * @param Productsup\Client A client object
     */
    public function __construct(Client $Client)
    {
        parent::__construct($Client);

        $this->version = 'v1';
        $this->serviceName = 'platform';
        $this->apiEndpoint = 'products';
    }

    /**
     * function delete()
     * 
     * submits products to the product-data api
     * 
     * @param array A single product
     */
    public function insert(array $product)
    {
        $this->_insert[] = $product;
    }

    /**
     * function delete()
     * 
     * submits products to the product-data api
     * 
     * @param array A single product
     */
    public function delete(array $product)
    {
        $this->_delete[] = $product;
    }

    /**
     * function submitInsert()
     * 
     * submits products to the product-data api
     * 
     * @return array Reponse Status
     */
    public function submit()
    {
        $response = array();
        if(count($this->_insert) > 0) {
            $response['insert'] = $this->submitInsert();
        }

        if(count($this->_delete) > 0) {
            $response['delete'] = $this->submitDelete();
        }

        return $response;
    }

    /**
     * function submitInsert()
     * 
     * submits products to the product delete api
     * 
     * @return array Reponse Status
     */
    private function submitDelete()
    {
        $DeleteRequest = new Request($this->getClient());
        $DeleteRequest->method = Request::METHOD_DELETE;
        $DeleteRequest->postBody = $this->_delete;
        $DeleteRequest->url = sprintf(
            $this->referenceId ? self::URL_REFERENCEID : self::URL_SITEID,
            $this->scheme,
            $this->host,
            $this->serviceName,
            $this->version,
            $this->referenceId ?: $this->siteId,
            $this->apiEndpoint
        );

        $Curl = new Curl();
        $DeleteResponse = $Curl->executeRequest($DeleteRequest);

        if ($DeleteResponse->getHttpStatus() !== 200) {
            throw new Exception('Api POST failed');
        }

        return $DeleteResponse->getJsonBody();       
    }

    /**
     * function submitInsert()
     * 
     * submits products to the product insert api
     * 
     * @return array Reponse Status
     */
    private function submitInsert()
    {
        $InsertRequest = new Request($this->getClient());
        $InsertRequest->method = Request::METHOD_POST;
        $InsertRequest->postBody = $this->_insert;
        $InsertRequest->url = sprintf(
            $this->referenceId ? self::URL_REFERENCEID : self::URL_SITEID,
            $this->scheme,
            $this->host,
            $this->serviceName,
            $this->version,
            $this->referenceId ?: $this->siteId,
            $this->apiEndpoint
        );

        $Curl = new Curl();
        $InsertResponse = $Curl->executeRequest($InsertRequest);
        if ($InsertResponse->getHttpStatus() !== 200) {
            throw new Exception('Api DELETE failed');
        }

        return $InsertResponse->getJsonBody();
    }
}
