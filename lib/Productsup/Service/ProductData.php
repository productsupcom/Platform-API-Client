<?php

namespace Productsup\Service;
use Productsup\Service as Service;
use Productsup\Client as Client;
use Productsup\Http\Request as Request;
use Productsup\IO\Curl as Curl;
use Productsup\Exception as Exception;

class ProductData extends Service
{
    const URL_PRODUCTS = '%s://%s/%s/%s/%s/%s/%s';

    private $_insert = array();
    private $_delete = array();
    private $_batchId;
    private $_submitLog = array();

    /**
     * function __construct()
     *
     * @param \Productsup\Client A client object
     */
    public function __construct(Client $Client)
    {
        parent::__construct($Client);

        $this->version = 'v1';
        $this->serviceName = 'products';
        $this->createBatchId();
    }

    /**
     * funcrtion createBatchId
     * 
     * creates a new batch id for multi-request submits
     * @return string Hash
     */
    private function createBatchId()
    {
        $this->_insert = array();
        $this->_delete = array();
        $this->_batchId = md5(microtime().uniqid());
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
        $this->checkSubmit();
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
        $this->checkSubmit();
    }

    /**
     * function checkSubmit()
     * 
     * Checks if the post limit has been reached and will submit a chunk
     */
    private function checkSubmit()
    {
        if(count($this->_insert) + count($this->_delete) > $this->getPostLimit()) {
            $this->submit();
        }
    }

    /**
     * function submitInsert()
     * 
     * submits products to the product-data api
     */
    private function submit()
    {
        if($this->getReference() === null) {
            throw new Exception(Exception::E_MISSING_REFERENCE);
        }

        $response = array();
        if(count($this->_insert) > 0) {
            $this->_submitLog[] = $this->submitInsert();
        }

        if(count($this->_delete) > 0) {
            $this->_submitLog[] = $this->submitDelete();
        }
    }

    /**
     * function commit()
     * 
     * after 1-n submits, this will commit the final chunk of products and
     * resets the class so that a new products will be a new batch.
     * 
     * @return array Submit Log for Debugging
     */
    public function commit()
    {
        if (count($this->_insert) > 0 || count($this->_delete) > 0) {
            $this->submit();
        }

        $this->createBatchId();

        return $this->getSubmitLog();
    }

    private function getSubmitLog()
    {
        $log = $this->_submitLog;
        $this->_submitLog = array();
        return $log;
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
        $DeleteRequest->postBody = array(
            'batchid' => $this->_batchId, 
            'products' => $this->_delete
        );
        $DeleteRequest->url = sprintf(
            self::URL_PRODUCTS,
            $this->scheme,
            $this->host,
            $this->api,
            $this->version,
            $this->serviceName,
            urlencode($this->getReference()->getKey()),
            urlencode($this->getReference()->getValue())
        );

        $Curl = new Curl();
        $DeleteResponse = $Curl->executeRequest($DeleteRequest);

        if ($DeleteResponse->getHttpStatus() !== 200) {
            throw new Exception(Exception::E_DELETE_REQUEST_FAILED);
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
        $InsertRequest->postBody = array(
            'batchid' => $this->_batchId, 
            'products' => $this->_insert
        );
        $InsertRequest->url = sprintf(
            self::URL_PRODUCTS,
            $this->scheme,
            $this->host,
            $this->api,
            $this->version,
            $this->serviceName,
            urlencode($this->getReference()->getKey()),
            urlencode($this->getReference()->getValue())
        );

        $Curl = new Curl();
        $InsertResponse = $Curl->executeRequest($InsertRequest);
        if ($InsertResponse->getHttpStatus() !== 200) {
            throw new Exception(Exception::E_INSERT_REQUEST_FAILED);
        }

        return $InsertResponse->getJsonBody();
    }
}
