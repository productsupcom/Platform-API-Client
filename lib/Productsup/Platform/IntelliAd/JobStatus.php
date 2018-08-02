<?php

namespace Productsup\Platform\IntelliAd;

class JobStatus extends \Productsup\Platform\JobStatus
{
    public $id;
    public $jobId;
    public $jobStatus;
    public $clientId;
    public $channelId;
    public $accountId;
    public $result;
    public $payload;
    public $resultFileUrl;
}
