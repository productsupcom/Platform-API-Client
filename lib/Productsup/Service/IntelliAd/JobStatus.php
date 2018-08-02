<?php

namespace Productsup\Service\IntelliAd;

class JobStatus extends \Productsup\Service\JobStatus
{
    protected $serviceName = 'jobstatuses/intelliad';

    protected function getResultField()
    {
        return 'Jobstatuses';
    }

    protected function getDataModel()
    {
        return new \Productsup\Platform\IntelliAd\JobStatus();
    }
}
