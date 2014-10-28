<?php

namespace Productsup\IO;
use Productsup\Http\Request as Request;
use Productsup\Http\Response as Response;

class Curl
{
    public function executeRequest(Request $Request)
    {
        $curl = curl_init();

        $requestHeaders = $Request->headers;

        if ($Request->postBody) {
            if(is_array($Request->postBody)) {
                $body = json_encode($Request->postBody);
                $requestHeaders['Content-Type'] = 'application/json';
            } else {
                $body = $Request->postBody;
            }
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }

        if ($requestHeaders && is_array($requestHeaders)) {
            $curlHeaders = array();
            foreach ($requestHeaders as $k => $v) {
                $curlHeaders[] = "$k: $v";
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);
        }   

        curl_setopt($curl, CURLOPT_URL, $Request->url);

        var_dump($Request->url);
        
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $Request->method);
        curl_setopt($curl, CURLOPT_USERAGENT, $Request->getUserAgent());

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        return new Response($curl);
    }
}
