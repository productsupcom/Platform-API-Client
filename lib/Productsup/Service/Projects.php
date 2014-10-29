<?php

namespace Productsup\Service;
use Productsup\Service as Service;
use Productsup\Client as Client;
use Productsup\Http\Request as Request;
use Productsup\IO\Curl as Curl;
use Productsup\Platform\Project as PlatformProject;

class Projects extends Service
{
    const URL_PROJECTS_INSERT = '%s://%s/%s/%s/%s';
    const URL_PROJECTS_DELETE = '%s://%s/%s/%s/%s/%s';
    const URL_PROJECTS_GET = '%s://%s/%s/%s/%s';

    /**
     * function __construct()
     *
     * @param Productsup\Client A client object
     */
    public function __construct(Client $Client)
    {
        parent::__construct($Client);

        $this->version = 'v1';
        $this->serviceName = 'projects';
    }

    /**
     * function insert()
     * 
     * creates a new project
     * 
     * @param string $projectTitle Project Title
     * @return Productsup\Platform\Project Project
     */
    public function insert(PlatformProject $Project)
    {
        $InsertRequest = new Request($this->getClient());
        $InsertRequest->method = Request::METHOD_POST;
        $InsertRequest->postBody = $Project;
        $InsertRequest->url = sprintf(
            self::URL_PROJECTS_INSERT,
            $this->scheme,
            $this->host,
            $this->api,
            $this->version,
            $this->serviceName
        );

        $Curl = new Curl();
        $InsertResponse = $Curl->executeRequest($InsertRequest);
        if ($InsertResponse->getHttpStatus() !== 200) {
            throw new Exception('Api POST failed');
        }

        $response = $InsertResponse->getJsonBody();
        if ($response === false) {
            throw new Exception('Failed to create Project');
        }

        $PlatformProject = new PlatformProject($response);
        return $PlatformProject;
    }

    /**
     * function delete()
     * 
     * deletes a project and all sites in that project
     * 
     * @param Productsup\Platform\Project $Project Project Object
     * @return boolean True on success
     */
    public function delete(PlatformProject $Project)
    {
        $DeleteRequest = new Request($this->getClient());
        $DeleteRequest->method = Request::METHOD_DELETE;
        $DeleteRequest->postBody = $Project;
        $DeleteRequest->url = sprintf(
            self::URL_PROJECTS_INSERT,
            $this->scheme,
            $this->host,
            $this->api,
            $this->version,
            $this->serviceName,
            $Project->id
        );

        $Curl = new Curl();
        $DeleteResponse = $Curl->executeRequest($DeleteRequest);

        if ($DeleteResponse->getHttpStatus() !== 200) {
            throw new Exception('Api DELETE failed');
        }

        $response = $DeleteResponse->getJsonBody(); 
        if ($response === false || $response['success'] === false) {
            throw new Exception('Failed to delete project.');
        }
        return true;
    }

    /**
     * function get()
     * 
     * get a list of projects
     * 
     * @return array List of Productsup\Platform\Project
     */
    public function get()
    {
        $GetRequest = new Request($this->getClient());
        $GetRequest->method = Request::METHOD_GET;
        $GetRequest->url = sprintf(
            self::URL_PROJECTS_INSERT,
            $this->scheme,
            $this->host,
            $this->api,
            $this->version,
            $this->serviceName
        );

        $Curl = new Curl();
        $GetResponse = $Curl->executeRequest($GetRequest);

        if ($GetResponse->getHttpStatus() !== 200) {
            throw new \Exception('Api GET failed');
        }

        $response = $GetResponse->getJsonBody();
        if ($response === false || !isset($response['success']) || $response['success'] === false) {
            throw new \Exception('Failed to get project list');
        }

        $list = array();
        foreach ($response['peojects'] as $project) {
            $list[] = new PlatformProject($project);
        }

        return $list;
    }
}
