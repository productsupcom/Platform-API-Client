<?php

namespace Productsup\Http;

class Response
{
    private $_httpStatus;
    private $_headers;
    private $_body;
    private $_effectiveUrl;

    /**
     * function __construct()
     * 
     * @param $curl Resource A resource from curl_init()
     */
    public function __construct($curl)
    {
        $response = curl_exec($curl);
        if ($response === false) {
          throw new Exception(curl_error($curl));
        }
        $this->_effectiveUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        list($responseHeaders, $responseBody) = $this->parseHttpResponse($response, $headerSize);
        $this->_httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $this->_headers = $responseHeaders;
        $this->_body = $responseBody;
    }

    /**
     * function parseHttpResponse()
     * 
     * returns HTTP response header and body splitted 
     * 
     * @param string $response A complete HTTP Response string
     * @param int $headerSite Size of the HTTP Header portion of the Response 
     * @return array An array of header and body strings
     */
    private function parseHttpResponse($response, $headerSize)
    {
        $header = trim(substr($response, 0, $headerSize));
        $body = trim(substr($response, $headerSize));
        return array($header, $body);
    }

    /**
     * function getHttpStatus()
     * 
     * returns HTTP response status code
     * 
     * @return int HTTP status code
     */
    public function getHttpStatus()
    {
        return $this->_httpStatus;
    }

    /**
     * function getHeaders()
     * 
     * returns an array of HTTP response header elements
     * 
     * @return array HTTP response headers
     */
    public function getHeaders()
    {
        $headerArray = array();
        $headers = explode("\n", $this->_headers);
        foreach($headers as $header)
        {
            list($key, $value) = explode(":", $header);
            $headerArray[trim($key)] = trim($value);
        }
        return $headerArray;
    }

    /**
     * function getBody()
     * 
     * returns the HTTP response body
     * 
     * @return string HTTP response body
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * function getJsonBody()
     * 
     * returns an array of a HTTP JSON response body if possible
     * 
     * @return array JSON Array
     */
    public function getJsonBody()
    {
        $body = trim($this->_body);
        if (empty($body)) {
            return sprintf('Empty Response from Server: %s', $this->_effectiveUrl);
        }

        $data = json_decode($this->_body, true);
        if (empty($data)) {
            return sprintf('Empty JSON Response from Server: %s', $this->_effectiveUrl);
        }
        
        if($data !== false) {
            return $data;
        }
        throw new Exception('Invalid JSON Body');
    }
}
