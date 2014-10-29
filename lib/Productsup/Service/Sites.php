<?php

namespace Productsup\Service;
use Productsup\Service as Service;
use Productsup\Client as Client;
use Productsup\Http\Request as Request;
use Productsup\IO\Curl as Curl;
use Productsup\Platform\Project as PlatformProject;
use Productsup\Platform\Site as PlatformSite;

class Sites extends Service
{
    const URL_SITES_INSERT = '%s://%s/%s/%s/%s/%s/%s';
    const URL_SITES_DELETE = '%s://%s/%s/%s/%s/%s/%s/%s';
    const URL_SITES_GET = '%s://%s/%s/%s/%s/%s/%s';

    private $_Project;

    /**
     * function __construct()
     *
     * @param Productsup\Client A client object
     */
    public function __construct(Client $Client)
    {
        parent::__construct($Client);

        $this->version = 'v1';
        $this->serviceName = 'sites';
        $this->apiEndpoint = 'projects';
    }

    public function setProject(PlatformProject $Project) {
        $this->_Project = $Project;
    }

    /**
     * function insert()
     * 
     * creates a new project
     * 
     * @param string $projectName Project Name
     * @return array Response
     */
    public function insert(PlatformSite $Site)
    {
        if ($this->_Project === null) {
            throw new Exception('Project not defined.');
        }

        $InsertRequest = new Request($this->getClient());
        $InsertRequest->method = Request::METHOD_POST;
        $InsertRequest->postBody = $Site;
        $InsertRequest->url = sprintf(
            self::URL_SITES_INSERT,
            $this->scheme,
            $this->host,
            $this->api,
            $this->version,
            $this->apiEndpoint,
            $this->_Project->id,
            $this->serviceName
        );

        $Curl = new Curl();
        $InsertResponse = $Curl->executeRequest($InsertRequest);
        if ($InsertResponse->getHttpStatus() !== 200) {
            throw new \Exception('Api POST failed');
        }

        $response = $InsertResponse->getJsonBody();
        if ($response === false || !isset($response['success']) || $response['success'] === false || !isset($response['site'])) {
            throw new \Exception('Failed to create Site');
        }

        $PlatformSite = new PlatformSite($response['site']);
        return $PlatformSite;
    }

    /**
     * function delete()
     * 
     * deletes a project and all sites in that project
     * 
     * @param int $projectId Project ID
     * @return array Response
     */
    public function delete($siteId)
    {
        $DeleteRequest = new Request($this->getClient());
        $DeleteRequest->method = Request::METHOD_DELETE;
        $DeleteRequest->postBody = $this->_delete;
        $DeleteRequest->url = sprintf(
            self::URL_SITES_DELETE,
            $this->scheme,
            $this->host,
            $this->api,
            $this->version,
            $this->apiEndpoint,
            $this->_Project->id,
            $this->serviceName,
            $siteId
        );

        $Curl = new Curl();
        $DeleteResponse = $Curl->executeRequest($DeleteRequest);

        if ($DeleteResponse->getHttpStatus() !== 200) {
            throw new Exception('Api DELETE failed');
        }

        return $DeleteResponse->getJsonBody(); 
    }

    /**
     * function get()
     * 
     * get a list of projects
     * 
     * @return array List of Productsup\Platform\Site
     */
    public function get()
    {
        $GetRequest = new Request($this->getClient());
        $GetRequest->method = Request::METHOD_GET;
        $GetRequest->url = sprintf(
            self::URL_SITES_GET,
            $this->scheme,
            $this->host,
            $this->api,
            $this->version,
            $this->apiEndpoint,
            $this->_Project->id,
            $this->serviceName
        );

        $Curl = new Curl();
        $GetResponse = $Curl->executeRequest($GetRequest);

        if ($GetResponse->getHttpStatus() !== 200) {
            throw new \Exception('Api GET failed');
        }

        $response = $GetResponse->getJsonBody();
        if ($response === false || !isset($response['success']) || $response['success'] === false) {
            throw new \Exception('Failed to get site list');
        }

        $list = array();
        foreach ($response['sites'] as $site) {
            $list[] = new PlatformSite($site);
        }

        return $list;
    }
}
