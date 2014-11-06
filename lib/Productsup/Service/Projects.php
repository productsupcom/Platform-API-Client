<?php

namespace Productsup\Service;
use Productsup\Platform\Project;
use Productsup\Http\Request;
use Productsup\Platform\Project as PlatformProject;
use Productsup\Exceptions;

class Projects extends Service {
    protected $serviceName = 'projects';

    /**
     * creates a new project
     *
     * @param \Productsup\Platform\Project $Project
     * @internal param string $projectTitle Project Title
     * @return \Productsup\Platform\Project Project
     */
    public function insert(PlatformProject $Project)
    {
        $request = $this->getRequest();
        $request->method = Request::METHOD_POST;
        $request->postBody = $Project->toArray();

        $data = $this->executeRequest($request);

        $PlatformProject = new PlatformProject($data['Projects'][0]);
        return $PlatformProject;
    }

    public function update(PlatformProject $Project) {
        $request = $this->getRequest();
        $request->method = Request::METHOD_PUT;
        $request->postBody = $Project->toArray();
        if($Project->id) {
            $request->url .= '/'.$Project->id;
        }
        $data = $this->executeRequest($request);
        $PlatformProject = new PlatformProject($data['Projects'][0]);
        return $PlatformProject;
    }

    /**
     * function delete()
     *
     * deletes a project and all sites in that project
     *
     * @param $id
     * @internal param \Productsup\Platform\Project $Project Project Object
     * @return boolean True on success
     */
    public function delete($id) {
        if($id instanceof Project) {
            $id = $id->id;
        }
        $request = $this->getRequest();
        $request->method = Request::METHOD_DELETE;
        $request->url .= '/'.$id;

        $response = $this->getIoHandler()->executeRequest($request);
        $data = $response->getData();
        return isset($data['success']) ? $data['success'] : false;
    }

    /**
     * get a list of projects
     * @param null|int $id
     * @throws \Productsup\Exceptions\ServerException
     * @throws \Productsup\Exceptions\ClientException
     * @return \Productsup\Platform\Project[]
     */
    public function get($id = null) {
        $request = $this->getRequest();
        $request->method = Request::METHOD_GET;
        if($id) {
            $request->url .= '/'.$id;
        }
        $response = $this->getIoHandler()->executeRequest($request);
        $data = $response->getData();
        $list = array();
        foreach ($data['Projects'] as $project) {
            $list[] = new PlatformProject($project);
        }
        return $list;
    }


}
