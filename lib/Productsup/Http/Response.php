<?php

namespace Productsup\Http;
use Productsup\Exceptions;

class Response {
    private $_httpStatus;
    private $_headers;
    private $_body;
    private $_data;

    private $_request;

    /**
     * @param $statusCode
     * @param $headers
     * @param $body
     */
    public function __construct($statusCode, $headers,$body, $request = null) {
        $this->_httpStatus = $statusCode;
        $this->_headers = $headers;
        $this->_body = $body;
        $this->_request = $request;
        $this->errorHandling();
    }

    /**
     * tries to decode received reply from server
     * @return array
     */
    public function getData() {
        if(!$this->_data) {
            $this->_data = $this->decodeJson();
        }
        return $this->_data;
    }

    public function verboseOutput() {
        echo "\nOutput:\nHeaders:\n".$this->_headers."\n\nBody:\n".$this->_body;
    }

    /**
     * throws exceptions if the api replied with an error
     * @throws \Productsup\Exceptions\ServerException
     * @throws \Productsup\Exceptions\ClientException
     */
    private function errorHandling() {
        $data = $this->getData();
        if($this->_httpStatus >= 500) {
            $message = isset($data['message']) ? $data['message'] : 'internal server error';
            throw new Exceptions\ServerException($message,$this->_httpStatus);
        } elseif($this->_httpStatus >= 400) {
            if(isset($data['message'])) {
                $message = $data['message'];
            } elseif($this->_httpStatus == 404) {
                $message = 'resource not found'; // no message, but 404 probably means the server doesn't know the route
            } else {
                $message = 'client error '.$this->_httpStatus;
            }
            throw new Exceptions\ClientException($message,$this->_httpStatus);
        } elseif(!isset($data['success']) || !$data['success']) {
            $message = isset($data['message']) ? $data['message'] : 'invalid response format';
            throw new Exceptions\ServerException($message);
        }
    }

    /**
     * returns an array of a HTTP JSON response body if possible
     *
     * @throws \Exception
     * @return array data parsed from JSON response
     */
    private function decodeJson() {
        $body = trim($this->_body);
        return json_decode($body, true);
    }
}
