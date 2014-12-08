<?php
namespace Productsup;

use Productsup\Exceptions\ClientException;
use Productsup\Http\Request;
use Productsup\IO\Curl;

class JsonRpcClient {
    private $uri;
    private $request;
    public function __construct($uri) {
        $this->request = new Request(new Client(),false);
        $this->request->url = $uri;
        $this->request->method = Request::METHOD_POST;
        $this->request->allowCompression = false;
    }

    public function setHeader($name, $value) {
        $this->request->setHeader($name, $value);
    }

    public function __call($method, $params) {
        $this->request->postBody = array(
            'method' => $method,
            'jsonrpc' => '2.0',
            'params' => $params[0],
            'id' => uniqid()
        );
        $response = $this->getIoHandler()->executeRequest($this->request,'JSON-RPC');
        $data = $response->getData();
        if(is_array($data) && isset($data['result'])) {
            return $data['result'];
        } elseif(is_array($data) && isset($data['error'])) {
            throw new ClientException($data['error']['message'],$data['error']['code']);
        } else {
            throw new ClientException('JSON Parse error of response',-32700);
        }
    }

    public function getIoHandler() {
        return new Curl();
    }
}