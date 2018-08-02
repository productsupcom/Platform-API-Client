<?php

namespace Productsup\Service;

use Productsup\Exceptions;

class JobStatus extends Service
{
    protected $serviceName = 'jobstatuses';

    /**
     * @return \Productsup\Platform\JobStatus
     */
    protected function getDataModel()
    {
        return new \Productsup\Platform\JobStatus();
    }

    /**
     * @param null $id
     *
     * @throws \Productsup\Exceptions\ClientException
     */
    public function get($id = null)
    {
        throw new Exceptions\ClientException('use insert to add a job status and receive it\'s result');
    }

    /**
     * post a job status and receive produtsups result.
     *
     * @param \Productsup\Platform\JobStatus $status
     *
     * @return \Productsup\Platform\JobStatus
     */
    public function insert(\Productsup\Platform\JobStatus $status)
    {
        return $this->_insert($status);
    }

    /**
     * @param mixed $id
     *
     * @throws \Productsup\Exceptions\ClientException
     */
    public function delete($id)
    {
        throw new Exceptions\ClientException('job statuses cannot be deleted');
    }

    /**
     * @throws \Productsup\Exceptions\ClientException
     */
    public function update()
    {
        throw new Exceptions\ClientException('job statuses cannot be updated');
    }
}
