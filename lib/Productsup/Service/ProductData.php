<?php

namespace Productsup\Service;
use Productsup\Client as Client;
use Productsup\Http\Request as Request;
use Productsup\Http\Response;
use Productsup\Exceptions;
use Productsup\Platform\Site\Reference;

class ProductData extends Service {
    /** @var array stores products to add or delete*/
    private $_productData = array();
    /** @var string identifier of the current batch */
    private $_batchId;
    /** @var array logs os all submits */
    private $_submitLog = array();
    /** @var bool is the current batch finished?*/
    private $finished = false;

    /** @var string what kind of import is this? see constants for further information */
    private $importType = self::TYPE_FULL;

    /** @var bool mainly for debugging - if true, client does raise exceptions if tried to send discards anyway */
    private $disableDiscards = false;

    /** a full import, this upload replaces all earlier uploads for the referenced site, once it is completed */
    const TYPE_FULL = 'full';
    /** a delta import, this is a incremental update to the last full import of the referenced site */
    const TYPE_DELTA = 'delta';

    /** @var string this name is reserved to mark products as delete */
    private $deleteFlagName = 'pup:isdeleted';

    /** @var bool was data already submitted? */
    private $didSubmit = false;


    /** @var string  */
    protected $parent = 'sites';
    /** @var string  */
    protected $serviceName = 'products';



    /** names for the different stages for querying */
    const STAGE_IMPORT = 'import';
    const STAGE_INTERMEDIATE = 'intermediate';
    const STAGE_EXPORT = 'export';
    const STAGE_CHANNEL = 'channel';

    /**
     * @param Client $Client
     * @param bool $useShutdownHandler set to false to disable shutdown handler
     */
    public function __construct(Client $Client, $useShutdownHandler = true) {
        parent::__construct($Client);
        $this->createBatchId();
        if($useShutdownHandler) {
            register_shutdown_function(array($this, 'shutdownHandler'));
        }
    }

    /**
     * creates a new batch id for multi-request submits
     */
    private function createBatchId() {
        $this->_batchId = md5(microtime().uniqid());
    }

    public function disableDiscards() {
        $this->disableDiscards = true;
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
        $this->addRow($product,false);
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
        $this->addRow($product, true);
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

    public function setImportType($type) {
        if($type !== self::TYPE_FULL && $type !== self::TYPE_DELTA) {
            throw new Exceptions\ClientException('unsupported import type, use one of the type constants');
        }
        $this->importType = $type;
    }


    /**
     * send products to the api that were not sent yet,
     * and tells the api that the current batch is complete and may get processed
     * @return array Submit Log for Debugging
     */
    public function commit() {
        $this->checkSubmit(1); // send all unsent products

        if(!$this->didSubmit) {
            throw new Exceptions\ClientException('no data submitted yet');
        }

        $request = $this->getRequest();
        $request->method = Request::METHOD_POST;
        $request->postBody = array(
            'type' => $this->importType
        );
        $request->url .= '/commit';
        $response = $this->getIoHandler()->executeRequest($request);
        $this->finished = true;
        return $this->getSubmitLog();
    }

    /**
     * if you do not want to continue one batch, you can discard it so the files get also removed from the server
     * @return bool
     * @throws Exceptions\ClientException
     * @throws Exceptions\ServerException
     */
    public function discard() {
        if($this->disableDiscards) {
            throw new Exceptions\ClientException('discards were disabled, but tried to send anyway');
        }
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
     * @param $row array of the actual data
     * @param $isDelete bool flag if this product is a delete or insert
     * @throws Exceptions\ClientException
     */
    private function addRow($row,$isDelete = false) {
        if($this->isArrayMultiDimensional($row)) {
            throw new Exceptions\ClientException('please pass only one product/row at once, rows are not allowed to contain arrays');
        }
        if($this->finished) {
            throw new Exceptions\ClientException('the current batch is already finished, please create a new one');
        }
        if(array_key_exists($this->deleteFlagName,$row)) {
            throw new Exceptions\ClientException('"'.$this->deleteFlagName.'" is a reserved name to flag deleted products, please use another name');
        }
        if(!array_key_exists('id',$row)) {
            throw new Exceptions\ClientException('adding one column "id" to the product data is mandatory');
        }

        if($isDelete) {
            $row[$this->deleteFlagName] = 1;
        }
        $this->_productData[] = $row;
        $this->checkSubmit($this->getPostLimit());
    }

    /**
     * check if data reached the limit, if so commit it
     * @var int $limit maximum rows
     */
    private function checkSubmit($limit) {
        if (count($this->_productData) >= $limit) {
            $this->_submit();
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
     * submits products to the api
     * @return array response of the api
     */
    private function _submit() {
        if(count($this->_productData) == 0) { // no data, do not send request
            return array();
        }
        $this->didSubmit = true;
        $request = $this->getRequest();
        $request->method = Request::METHOD_POST;
        $request->url .= '/upload';
        $request->postBody = $this->_productData;

        try {
            $response = $this->getIoHandler()->executeRequest($request);
        } catch (\Exception $e) {
            $found = $this->testJson($this->_productData);

            if ($found > 0) {
                $this->_submitLog[] = sprintf(
                    '%s: %u products with malformed json. removing affected products and retrying batch: %s',
                    date('Y-m-d H:i:s'),
                    $found,
                    $this->_batchId
                );

                $this->_submit();
            } else {
                throw $e;
            }
        }
        $this->_productData = array();
        $this->logResponse($response);
        return $response->getData();
    }

    private function testJson(array &$a)
    {
        $found = 0;

        foreach ($a as $key => $row) {
            $j = json_encode($row);
            if (json_last_error()) {
                unset($a[$key]);
                $found++;
                continue;
            }
            json_decode($j);
            if (json_last_error()) {
                unset($a[$key]);
                $found++;
            }
        }

        return $found;
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
        if($this->didSubmit && !$this->finished) {
            $this->discard();
        }
    }

    private function getPdaRequest($stage,$id) {
        $request = $this->getRequest();
        $request->method = Request::METHOD_GET;
        $request->url = $this->scheme.'://'.$this->host.'/product/'.$this->version.'/site/'.$this->_parentIdentifier;
        $request->url .= '/stage/'.$stage;
        if($id) {
            $request->url .= '/'.$id;
        }

        return $request;
    }

    /**
     * @param string $stage source|intermediate|channel
     * @param int|null $id id of the stage (or null for source)
     * @param array $params
     * @return array
     */
    public function get($stage, $id,$params) {
        $request = $this->getPdaRequest($stage,$id);

        $request->url .= '/';
        $request->queryParams = (array)$params;
        $data = $this->executeRequest($request);
        return isset($data['products']) ? $data['products'] : array();
    }

    /**
     * @param string $stage source|intermediate|channel
     * @param int|null $id id of the stage (or null for source)
     * @return array
     */
    public function getProperties($stage,$id, $params = null) {
        $request = $this->getPdaRequest($stage,$id);
        if(!$id) {
            $request->url .= '/0';
        }
        $request->url .= '/properties/';
        if($params) {
            $request->queryParams = (array)$params;
        }
        return $this->executeRequest($request);
    }
}
