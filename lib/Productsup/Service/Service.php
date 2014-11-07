<?php

namespace Productsup\Service;
use Productsup\Client;
use Productsup\Http\Request;
use Productsup\IO\Curl;
use Productsup\Platform\DataModel;

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

    /**
     * get a list of projects
     * @param null|int $id
     * @throws \Productsup\Exceptions\ServerException
     * @throws \Productsup\Exceptions\ClientException
     * @return \Productsup\Platform\DataModel[]
     */
    protected function _get($id = null) {
        $request = $this->getRequest();
        $request->method = Request::METHOD_GET;
        if($id) {
            $request->url .= '/'.$id;
        }
        $response = $this->getIoHandler()->executeRequest($request);
        $data = $response->getData();
        $list = array();
        foreach ($data[ucfirst($this->serviceName)] as $project) {
            $list[] = $this->getDataModel()->fromArray($project);
        }
        return $list;
    }

    /**
     * deletes the resources identified by given id
     * @param $id
     * @return bool true on success
     */
    protected function _delete($id) {
        $request = $this->getRequest();
        $request->method = Request::METHOD_DELETE;
        $request->url .= '/'.$id;

        $response = $this->getIoHandler()->executeRequest($request);
        $data = $response->getData();
        return isset($data['success']) ? $data['success'] : false;
    }

    /**
     * @param DataModel $dataModel
     * @return static
     */
    protected function _insert($dataModel) {
        $request = $this->getRequest();
        $request->method = Request::METHOD_POST;
        $request->postBody = $dataModel->toArray();
        $data = $this->executeRequest($request);
        return $this->getDataModel()->fromArray($data[ucfirst($this->serviceName)][0]);
    }

    /**
     * @param DataModel $dataModel
     * @return static
     */
    protected function _update($dataModel) {
        $request = $this->getRequest();
        $request->method = Request::METHOD_PUT;
        $request->postBody = $dataModel->toArray();
        if($dataModel->id) {
            $request->url .= '/'.$dataModel->id;
        }
        $data = $this->executeRequest($request);
        return $this->getDataModel()->fromArray($data[ucfirst($this->serviceName)][0]);
    }

    /** @return DataModel */
    protected abstract function getDataModel();


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
    public function getClient()
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
