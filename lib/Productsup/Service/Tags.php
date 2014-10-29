<?php

namespace Productsup\Service;
use Productsup\Service as Service;
use Productsup\Client as Client;
use Productsup\Http\Request as Request;
use Productsup\IO\Curl as Curl;
use Productsup\Platform\Tag as PlatformTag;
use Productsup\Platform\Site as PlatformSite;
use Productsup\Exception as Exception;

class Tags extends Service
{
    const URL_TAGS_INSERT = '%s://%s/%s/%s/%s/%s/%s';
    const URL_TAGS_DELETE = '%s://%s/%s/%s/%s/%s/%s';
    const URL_TAGS_GET = '%s://%s/%s/%s/%s/%s/%s';

    private $_Site;

    /**
     * function __construct()
     *
     * @param Productsup\Client A client object
     */
    public function __construct(Client $Client)
    {
        parent::__construct($Client);

        $this->version = 'v1';
        $this->serviceName = 'tags';
    }

    /**
     * function insert()
     * 
     * creates a new tag
     * 
     * @param string $projectName Project Name
     * @return array Response
     */
    public function insert(PlatformTag $Tag)
    {
        if ($this->getReference() === null) {
            throw new Exception('Reference not defined.');
        }

        $InsertRequest = new Request($this->getClient());
        $InsertRequest->method = Request::METHOD_POST;
        $InsertRequest->postBody = $Tag;
        $InsertRequest->url = sprintf(
            self::URL_TAGS_INSERT,
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

        $response = $InsertResponse->getJsonBody();
        if ($response === false || !isset($response['success']) || $response['success'] === false || !isset($response['tag'])) {
            throw new Exception(Exception::E_FAILED_TO_CREATE_TAG);
        }

        $PlatformTag = new PlatformTag($response['tag']);
        return $PlatformTag;
    }

    /**
     * function delete()
     * 
     * deletes a tag
     * 
     * @param int $projectId Project ID
     * @return array Response
     */
    public function delete(PlatformTag $Tag)
    {
        $DeleteRequest = new Request($this->getClient());
        $DeleteRequest->method = Request::METHOD_DELETE;
        $DeleteRequest->postBody = $Tag;
        $DeleteRequest->url = sprintf(
            self::URL_TAGS_DELETE,
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
     * function get()
     * 
     * get a list of tags for a site
     * 
     * @return array List of Productsup\Platform\Tag
     */
    public function get()
    {
        $GetRequest = new Request($this->getClient());
        $GetRequest->method = Request::METHOD_GET;
        $GetRequest->url = sprintf(
            self::URL_TAGS_GET,
            $this->scheme,
            $this->host,
            $this->api,
            $this->version,
            $this->serviceName,
            urlencode($this->getReference()->getKey()),
            urlencode($this->getReference()->getValue())
        );

        $Curl = new Curl();
        $GetResponse = $Curl->executeRequest($GetRequest);

        if ($GetResponse->getHttpStatus() !== 200) {
            throw new Exception(Exception::E_GET_REQUEST_FAILED);
        }

        $response = $GetResponse->getJsonBody();
        if ($response === false || !isset($response['success']) || $response['success'] === false) {
            throw new Exception(Exception::E_FAILED_TO_GET_LIST);
        }

        $list = array();
        foreach ($response['tags'] as $tag) {
            $list[] = new PlatformTag($tag);
        }

        return $list;
    }
}
