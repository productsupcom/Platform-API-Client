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
     * @param string $apiKind
     * @internal param null $request
     */
    public function __construct($statusCode, $headers,$body, $apiKind = 'REST') {
        $this->_httpStatus = $statusCode;
        $this->_headers = $headers;
        $this->_body = $body;
        if($apiKind == 'REST') {
            $this->restErrorHandling();

        }
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
        echo "\nOutput:\nHeaders:\n".$this->_headers."\n\nBody:\n".$this->getRawBody();
    }

    /**
     * throws exceptions if the api replied with an error
     * @throws \Productsup\Exceptions\ServerException
     * @throws \Productsup\Exceptions\ClientException
     */
    private function restErrorHandling() {
        $data = $this->getData();

        if($this->_httpStatus >= 500) {
            $message = isset($data['message']) ? $data['message'] : 'internal server error';
            //$this->verboseOutput();
            throw new Exceptions\ServerException($message,$this->_httpStatus);
        } elseif($this->_httpStatus >= 400) {
            if(isset($data['message'])) {
                $message = $data['message'];
            } elseif($this->_httpStatus == 404) {
                $message = 'resource not found'; // no message, but 404 probably means the server doesn't know the route
            } else {
                $message = 'client error '.$this->_httpStatus;
            }
            //$this->verboseOutput();
            throw new Exceptions\ClientException($message,$this->_httpStatus);
        } elseif(isset($data['success']) && !$data['success']) {
            $message = isset($data['message']) ? $data['message'] : 'invalid response format';
            //$this->verboseOutput();
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

    public function getHeader($header) {
        if(preg_match('/'.preg_quote($header).':(.*)/i',$this->_headers,$res)) {
            return trim($res[1]);
        }
        return null;
    }

    /**
     * @return string
     */
    public function getRawBody() {
        return $this->_body;
    }
}
