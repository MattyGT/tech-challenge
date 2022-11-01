<?php
namespace App\Http\Services;

use Exception;

/**
 * Base class for making console API requests
 * @author Matthew Tyler
*/
class ApiRequest {

    public $proxies = [];

    public function __construct(string $apiurl, array $proxies = [])
    {
        $this->proxies = count($proxies)<=0?$this->getProxies($apiurl):$proxies;
    }

    /**
     * Get an array of useable proxies
    */
    public function getProxies(string $proxyUrl):array
    {
        if ( $proxyUrl === '' ) {
            throw new \InvalidArgumentException("No proxy API url provided.");
        }
        if ( filter_var($proxyUrl, FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED) === false ) {
            throw new \Exception("Proxy API endpoint invalid.");
        }
        $optsArr = [
            CURLOPT_URL => $proxyUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ];

        return explode("\n",trim($this->makeRequest($optsArr)));
    }

    /**
     * Get the headers from the provided URL using collected proxies
    */
    public function getURIHeader(string $url, string $proxy):string
    {
        if ( $proxy === '' ) {
            throw new \Exception("No proxy provided.");
        }
        $optsArr = [
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_PROXY => 'http://'.str_replace("\r", '', $proxy),
            CURLOPT_NOBODY => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ];
        $response = $this->makeRequest($optsArr);

        return $response;
    }

    /**
     * Send a http request and return the response
    */
    public function makeRequest(array $curlOpts):string
    {
        $curl = curl_init();
        curl_setopt_array($curl,$curlOpts);
        $response = curl_exec($curl);
        if ( !$response ) {
            return curl_error($curl);
        }
        curl_close($curl);
        
        return $response;
    }

}