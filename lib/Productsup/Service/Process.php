<?php

namespace Productsup\Service;

use Productsup\Exceptions\ClientException;
use Productsup\Http\Request;
use Productsup\Platform\Process as ProcessModel;

class Process extends Service {

    protected $serviceName = 'process';
    protected $validActions = [
        'import', 'export', 'channel', 'all', 'export-all'
    ];
    protected $actionsRequiringId = ['export', 'channel'];

    // Not applicable to process
    protected function getDataModel()
    {
        return new \Productsup\Platform\Process();
    }

    /**
     * Trigger an action for a site
     *
     * @param ProcessModel $model
     *
     * @return bool
     *
     * @throws ClientException When an invalid model is given
     */
    public function post(ProcessModel $model) {
        if (!$model->site_id) {
            throw new ClientException('A site id is required for a process.');
        }
        if (!in_array($model->action, $this->validActions)) {
            throw new ClientException(sprintf(
                'Only the following actions are allowed: %s',
                implode(', ', $this->validActions)
            ));
        }
        if (in_array($model->action, $this->actionsRequiringId) && !$model->action_id) {
            throw new ClientException('An export or channel id needs to be set for this action.');
        }

        $request = $this->getRequest();
        $request->method = Request::METHOD_POST;
        $request->postBody = $model->toArray();
        $request->url .= '/'.$model->site_id;
        $data = $this->executeRequest($request);

        if (isset($data['success'])) {
            return $data['success'];
        }

        return false;
    }
}
