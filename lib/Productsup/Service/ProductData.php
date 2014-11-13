<?php

namespace Productsup\Service;
use Productsup\Client as Client;
use Productsup\Http\Request as Request;
use Productsup\Http\Response;
use Productsup\Exceptions;
use Productsup\Platform\Site\Reference;

class ProductData extends Service {
    /** @var array stores products to add */
    private $_insert = array();
    /** @var array stores products to delete */
    private $_delete = array();
    /** @var string identifier of the current batch */
    private $_batchId;
    /** @var array logs os all submits */
    private $_submitLog = array();
    /** @var bool is the current batch finished?*/
    private $finished = false;

    /** @var string  */
    protected $parent = 'sites';
    /** @var string  */
    protected $serviceName = 'products';


    /**
     * @param Client $Client
     */
    public function __construct(Client $Client) {
        parent::__construct($Client);
        $this->createBatchId();
        register_shutdown_function(array($this, 'shutdownHandler'));
    }

    /**
     * creates a new batch id for multi-request submits
     */
    private function createBatchId() {
        $this->_batchId = md5(microtime().uniqid());
    }

    /**
     * add one product to the data sent to the API
     *
     * note:
     * the data does not get sent to the server until the post limit is reached or you commit() the upload
     * uncommitted uploads will be discarded and are not processed
     * @param array $product A single product
     * @throws \Productsup\Exceptions\ClientException
     */
    public function insert(array $product) {
        $this->addRow($this->_insert,$product);
    }

    /**
     * delete one product to the data sent to the API
     *
     * note:
     * the data does not get sent to the server until the post limit is reached or you commit() the upload
     * uncommitted uploads will be discarded and are not processed
     * @param array $product A single product
     * @throws \Productsup\Exceptions\ClientException
     */
    public function delete(array $product) {
        $this->addRow($this->_delete,$product);
    }

    /**
     * define the site these products belong to
     * @param Reference $reference
     * @throws Exceptions\ClientException
     */
    public function setReference(Reference $reference) {
        if(count($this->_submitLog)) {
            throw new Exceptions\ClientException('references may not get updated after the first data was submitted');
        }
        $this->_parentIdentifier = (string)$reference;
    }

    /**
     * send products to the api that were not sent yet,
     * and tells the api that the current batch is complete and may get processed
     * @return array Submit Log for Debugging
     */
    public function commit() {
        $this->checkSubmit(0); // send all unsent products
        $request = $this->getRequest();
        $request->method = Request::METHOD_POST;
        $request->url .= '/commit';
        $response = $this->getIoHandler()->executeRequest($request);
        $this->finished = true;
        return $this->getSubmitLog();
    }

    /**
     * if you do not want to continue one batch, you can discard it so the files get also removed from the server
     * @return bool
     */
    public function discard() {
        $request = $this->getRequest();
        $request->method = Request::METHOD_POST;
        $request->url .= '/discard';
        $response = $this->getIoHandler()->executeRequest($request)->getData();
        $this->finished = true;
        return isset($response['success']) ? $response['success'] : false;
    }

    /**
     * define maximum number of products sent within one request
     * you still may provide more data, but internally the products will get already transferred to the server before
     * @param int $limit
     * @throws Exceptions\ClientException
     */
    public function setPostLimit($limit) {
        if ($limit < 1) {
            throw new Exceptions\ClientException('Post limit lower 1 not allowed');
        } elseif ($limit > 10000) {
            throw new Exceptions\ClientException('Post limit higher 10.000 not allowed');
        }
        $this->_postLimit = $limit;
    }

    /**
     * @param $data
     * @param $row
     * @throws \Productsup\Exceptions\ClientException
     */
    private function addRow(&$data,$row) {
        if($this->isArrayMultiDimensional($row)) {
            throw new Exceptions\ClientException('please pass only one product/row at once, rows are not allowed to contain arrays');
        }
        if($this->finished) {
            throw new Exceptions\ClientException('the current batch is already finished, please create a new one');
        }
        $data[] = $row;
        $this->checkSubmit($this->getPostLimit());
    }

    /**
     * check if data reached the limit, if so commit it
     * @var int $limit maximum rows
     */
    private function checkSubmit($limit) {
        if (count($this->_insert) >= $limit) {
            $this->submitInsert();
        }
        if(count($this->_delete) >= $limit) {
            $this->submitDelete();
        }
    }

    /**
     * returns log messages from submits and empties the log
     * @return array
     */
    private function getSubmitLog() {
        $log = $this->_submitLog;
        $this->_submitLog = array();
        return $log;
    }

    /**
     * submits products to delete to the api
     * @return array data provided in response
     */
    private function submitDelete() {
        $this->_submit('delete',$this->_delete);
    }

    /**
     * submits products to insert to the api
     * @return array data provided in response
     */
    private function submitInsert() {
        $this->_submit('insert',$this->_insert);
    }

    /**
     * submits products to the api
     * @param $type
     * @param $data
     * @return array
     */
    private function _submit($type, &$data) {
        if(count($data) == 0) { // no data, do not send request
            return array();
        }
        $request = $this->getRequest();
        $request->method = Request::METHOD_POST;
        $request->url .= '/'.$type;
        $request->postBody = $data;

        $response = $this->getIoHandler()->executeRequest($request);
        $data = array();
        $this->logResponse($response);
        return $response->getData();
    }

    /**
     * convert api response to a log message
     * @param Response $response
     */
    private function logResponse(Response $response) {
        $data = $response->getData();
        foreach($data as $key => $val) {
            if($key == 'success') continue; // we know it was a success, otherwise it would have failed before
            $this->_submitLog[] = date('Y-m-d H:i:s').' added '.$val['count'].' product(s) '.' for '.$key.' to batch '.$this->_batchId;
        }
    }

    /**
     * try to check quickly if passed array is multi dimensional
     * @param $array
     * @return bool
     */
    private function isArrayMultiDimensional($array) {
        return !((count($array) == count($array, COUNT_RECURSIVE)));
    }


    /**
     * returns the currently defined post limit
     * @return int
     */
    public function getPostLimit() {
        return $this->_postLimit;
    }

    /**
     * url to the api endpoint requested
     * @return string
     * @throws Exceptions\ClientException
     */
    protected function getServiceUrl() {
        if(!$this->_parentIdentifier) {
            throw new Exceptions\ClientException('please set a reference before the first data is submitted');
        }
        return $this->parent.'/'.$this->_parentIdentifier.'/'.$this->serviceName.'/'.$this->_batchId;
    }

    /**
     * to stay compatible with parent abstract class.
     * however, data models are not used for batch services
     * @throws \Exception
     */
    protected function getDataModel() {
        throw new \Exception('Data Model is not needed/supported for batch services');
    }

    /**
     * discard sent products if they were not handled until the service is unset
     */
    public function shutdownHandler() {
        if(!$this->finished) {
            $this->discard();
        }
    }

}
