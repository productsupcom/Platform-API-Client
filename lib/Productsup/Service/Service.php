<?php

namespace Productsup\Service;
use Productsup\Client;
use Productsup\Exceptions\ClientException;
use Productsup\Http\Request;
use Productsup\IO\Curl;
use Productsup\Platform\DataModel;

abstract class Service
{
    /** @var string Hostname */
    protected $host = 'platform-api.productsup.io';

    /** @var string Scheme */
    protected $scheme = 'https';

    /** @var string Service Version */
    protected $version = 'v1';

    /** @var string Service Name */
    protected $serviceName;

    /** @var string Service Name */
    protected $api = 'platform';

    /** @var \Productsup\Client Client */
    protected $_Client;

    /** @var string service name of the parent (e.g. "projects" for "sites") */
    protected $parent;

    /** @var  string identifier for the parent object referring to the current service
     * e.g. a $site->id or string representation of a Reference object
     */
    protected $_parentIdentifier;

    protected $_postLimit = 5000;

    protected $verbose = false;
    protected $debug = false;


    /**
     * @param Client $Client
     */
    public function __construct(Client $Client) {
        $this->_Client = $Client;
        // just for debugging, allow to pass params to trigger verbose and debug mode
        if(isset($_SERVER['argv']) && count($_SERVER['argv'])) {
            unset($_SERVER['argv'][0]);
            if(in_array('v',$_SERVER['argv'])) {
                $this->verbose = true;
            }
            if(in_array('d',$_SERVER['argv'])) {
                $this->debug = true;
            }
        }
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
     * @throws \Productsup\Exceptions\ClientException
     * @return bool true on success
     */
    protected function _delete($id) {
        if(empty($id)) {
            throw new ClientException('you have to provide an id, or an object with an id for deleting');
        }
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
        return $this->getDataModel()->fromArray($data[$this->getResultField()][0]);
    }

    /**
     * returns the name of the field where the api is expected to return the actual object
     * @return string
     */
    protected function getResultField() {
        return ucfirst($this->serviceName);
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
        $request->url = $this->getUrl();
        return $request;
    }

    /**
     * returns the base url for the api
     * @return string
     */
    protected function getBaseUrl() {
        return $this->scheme.'://'.$this->host.'/'.$this->api.'/'.$this->version.'/';
    }

    /**
     * returns the part of the url referencing to the current service
     * @return string
     */
    protected function getServiceUrl() {
        $url = '';
        if($this->parent && $this->_parentIdentifier) {
            $url .= $this->parent.'/'.$this->_parentIdentifier.'/';
        }
        $url .= $this->serviceName;
        return $url;
    }

    /**
     * returns the full url to the api endpoint of the current service
     * @return string
     */
    protected function getUrl() {
        return $this->getBaseUrl().$this->getServiceUrl();
    }


    /**
     * returns the class that handles network requests, right now only Curl is supported
     * @return Curl
     */
    protected function getIoHandler() {
        $io = new Curl();
        if($this->verbose) {
            $io->verbose = true;
        }
        if($this->debug) {
            $io->debug = true;
        }
        return $io;
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
