<?php
namespace MercadoPago;

use Exception;
 
/**
 * MercadoPago cURL RestClient
 */
class RestClient
{

    /**
     *
     */
    protected static $verbArray = [
        'get'    => 'GET',
        'post'   => 'POST',
        'put'    => 'PUT',
        'delete' => 'DELETE'
    ];

    /**
     * @var Http\CurlRequest|null
     */
    protected $httpRequest = null;
    /**
     * @var array
     */
    protected static $defaultParams = [];
    protected $customParams = [];

    /**
     * RestClient constructor.
     */
    public function __construct()
    {
        $this->httpRequest = new Http\CurlRequest();
    }

    /**
     * @param Http\HttpRequest $connect
     * @param                  $headers
     */
    protected function setHeaders(Http\HttpRequest $connect, $customHeaders)
    {
        $default_header = array(
            'Content-Type' => 'application/json',
            'User-Agent' => 'MercadoPago DX-PHP SDK/ v' . Version::$_VERSION,
            'x-product-id' => 'BC32A7RU643001OI3940',
            'x-tracking-id' => 'platform:' . PHP_MAJOR_VERSION .'|' . PHP_VERSION . ',type:SDK' . Version::$_VERSION . ',so;'
        );

        if ($customHeaders) {
            $default_header = array_merge($default_header, $customHeaders);
        }

        if(!isset($default_header['Authorization'])){
            $default_header['Authorization'] = 'Bearer '.SDK::getAccessToken();
        }

        foreach ($default_header as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }

        $connect->setOption(CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * @param Http\HttpRequest $connect
     * @param                  $data
     * @param string           $content_type
     *
     * @throws Exception
     */
    protected function setData(Http\HttpRequest $connect, $data, $content_type = '')
    {
        
        if ($content_type == "application/json") {
                
            if (gettype($data) == "string") {
                json_decode($data, true);
            } else { 
                $data = json_encode($data); 
            }

            if (function_exists('json_last_error')) {
                $json_error = json_last_error();
                if ($json_error != JSON_ERROR_NONE) {
                    throw new Exception("JSON Error [{$json_error}] - Data: {$data}");
                }
            }
 
            
        } 
        if ($data != "[]") {
            $connect->setOption(CURLOPT_POSTFIELDS, $data);
        } else {
            $connect->setOption(CURLOPT_POSTFIELDS, "");
        }
        
    }

    /**
     * @param $request
     */
    public function setHttpRequest($request)
    {
        $this->httpRequest = $request;
    }

    /**
     * @return Http\CurlRequest|null
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * @param $options
     *
     * @return array
     * @throws Exception
     */
    protected function exec($options)
    {  
        $method = key($options);
        $requestPath = reset($options);
        $verb = self::$verbArray[$method];
        
        $headers = $this->getArrayValue($options, 'headers');
        $url_query = $this->getArrayValue($options, 'url_query');
        $formData = $this->getArrayValue($options, 'form_data');
        $jsonData = $this->getArrayValue($options, 'json_data');
        

        $defaultHttpParams = self::$defaultParams;
        $connectionParams = array_merge($defaultHttpParams, $this->customParams);
        $query = '';

        if ($url_query > 0) {
            $query = http_build_query($url_query);
        }
        
        $address = $this->getArrayValue($connectionParams, 'address');
        $uri = $address . $requestPath;
        if ($query != '') {
            if (parse_url($uri, PHP_URL_QUERY)) {
                $uri .= '&' . $query;
            } else {
                $uri .= '?' . $query;
            }
        }

        $connect = $this->getHttpRequest();
        $connect->setOption(CURLOPT_URL, $uri);
        if ($userAgent = $this->getArrayValue($connectionParams, 'user_agent')) {
            $connect->setOption(CURLOPT_USERAGENT, $userAgent);
        }
        $connect->setOption(CURLOPT_RETURNTRANSFER, true);
        $connect->setOption(CURLOPT_CUSTOMREQUEST, $verb);
        
        $this->setHeaders($connect, $headers);
        $proxyAddress = $this->getArrayValue($connectionParams, 'proxy_addr');
        $proxyPort = $this->getArrayValue($connectionParams, 'proxy_port');
        if (!empty($proxyAddress)) {
            $connect->setOption(CURLOPT_PROXY, $proxyAddress);
            $connect->setOption(CURLOPT_PROXYPORT, $proxyPort);
        }
        if ($useSsl = $this->getArrayValue($connectionParams, 'use_ssl')) {
            $connect->setOption(CURLOPT_SSL_VERIFYPEER, $useSsl);
        }
        if ($sslVersion = $this->getArrayValue($connectionParams, 'ssl_version')) {
            $connect->setOption(CURLOPT_SSLVERSION, $sslVersion);
        }
        if ($verifyMode = $this->getArrayValue($connectionParams, 'verify_mode')) {
            $connect->setOption(CURLOPT_SSL_VERIFYHOST, $verifyMode);
        }
        if ($caFile = $this->getArrayValue($connectionParams, 'ca_file')) {
            $connect->setOption(CURLOPT_CAPATH, $caFile);
        }
        
        $connect->setOption(CURLOPT_FOLLOWLOCATION, true);

        if ($formData) {
            $this->setData($connect, $formData);
        }
        if ($jsonData) {
            $this->setData($connect, $jsonData, "application/json");
        }
 
        $apiResult = $connect->execute();
        $apiHttpCode = $connect->getInfo(CURLINFO_HTTP_CODE);
        
        if ($apiResult === false) {
            throw new Exception ($connect->error());
        }
        
        $response['response'] = [];
        
        if ($apiHttpCode != "200" && $apiHttpCode != "201") {
            error_log($apiResult);
        }
        
        $response['response'] = json_decode($apiResult, true);
        $response['code'] = $apiHttpCode;

        $connect->error();
        
        return ['code' => $response['code'], 'body' => $response['response']];
    }

    /**
     * @param       $uri
     * @param array $options
     *
     * @return array
     * @throws Exception
     */
    public function get($uri, $options = [])
    {
        return $this->exec(array_merge(['get' => $uri], $options));
    }

    /**
     * @param       $uri
     * @param array $options
     *
     * @return array
     * @throws Exception
     */
    public function post($uri, $options = [])
    {  
        return $this->exec(array_merge(['post' => $uri], $options));
    }

    /**
     * @param       $uri
     * @param array $options
     *
     * @return array
     * @throws Exception
     */
    public function put($uri, $options = [])
    {
        return $this->exec(array_merge(['put' => $uri], $options));
    }

    /**
     * @param       $uri
     * @param array $options
     *
     * @return array
     * @throws Exception
     */
    public function delete($uri, $options = [])
    {
        return $this->exec(array_merge(['delete' => $uri], $options));
    }

    /**
     * @param $param
     * @param $value
     */
    public function setHttpParam($param, $value)
    {
        self::$defaultParams[$param] = $value;
    }

    /**
     * @param $array
     * @param $key
     *
     * @return bool
     */
    protected function getArrayValue($array, $key)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return false;
        }
    }
}
