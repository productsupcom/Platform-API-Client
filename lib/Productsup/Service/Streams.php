<?php

namespace Productsup\Service;
use Productsup\Platform\DataModel;
use Productsup\Platform\Site;
use Productsup\Platform\Site\Reference;
use Productsup\Platform\Stream;
use Productsup\Http\Request;

class Streams extends Service {
    protected $serviceName = 'streams';
    protected $parent = 'sites';

    public function setSite(Site $site) {
        $this->_parentIdentifier = $site->id;
    }

    /**
     * @return DataModel|Stream
     */
    protected function getDataModel() {
        return new Stream();
    }

    public function get($id = null,$action = null) {
        $request = $this->getRequest();
        $request->method = Request::METHOD_GET;
        if(!empty($this->params)) {
            $request->queryParams = $this->params;
        }

        if($id) {
            $request->url .= '/'.$id;
            if($action) {
                $request->url .= '/'.$action;
            }
        }

        $data = $this->executeRequest($request);

        if (isset($data['success']) && array_key_exists('Sources', $data)) {
            $data['Streams'] = $data['Sources'];
            unset($data['Sources']);
            return $data;
        }

        return false;
    }


    public function setReference(Reference $reference) {
        $this->_parentIdentifier = (string)$reference;
    }
}
