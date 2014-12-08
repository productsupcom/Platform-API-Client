<?php

namespace Productsup\IO;
use Productsup\Exceptions\ServerException;
use Productsup\Http\Request as Request;
use Productsup\Http\Response as Response;

class Curl {
    private $curl;
    public $debug = 0;
    public $verbose = 0;

    public function __construct() {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HEADER, true);

    }

    private function prepareRequest(Request $request) {
        if ($request->hasData()) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $request->getBody());
        }

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $request->getHeaders());
        if($this->debug) {
            $request->url .= '?debug=1';
        }
        curl_setopt($this->curl, CURLOPT_URL, $request->url);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $request->method);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $request->getUserAgent());
    }


    /**
     * @param Request $Request
     * @throws ServerException
     * @return Response
     */
    public function executeRequest(Request $Request,$apiKind = 'REST') {
        if($this->verbose) {
            $Request->verboseOutput();
        }
        $this->prepareRequest($Request);
        if(!$curl_response = curl_exec($this->curl)) {
            throw new ServerException(curl_error($this->curl),curl_errno($this->curl));
        }

        list($responseHeaders, $responseBody) = $this->parseHttpResponse($curl_response);
        $statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $responseObject = new Response($statusCode,$responseHeaders,$responseBody,$apiKind);
        if($this->verbose) {
            $responseObject->verboseOutput();
        }
        return $responseObject;
    }


    /**
     * split headers and body from full curl response
     * @param string $response A complete HTTP Response string
     * @internal param int $headerSite Size of the HTTP Header portion of the Response
     * @return array An array of header and body strings
     */
    private function parseHttpResponse($response) {
        $headerSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        $header = trim(substr($response, 0, $headerSize));
        $body = trim(substr($response, $headerSize));
        return array($header, $body);
    }
}
