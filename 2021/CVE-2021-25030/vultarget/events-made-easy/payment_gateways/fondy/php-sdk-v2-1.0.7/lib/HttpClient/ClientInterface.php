<?php

namespace Cloudipsp\HttpClient;

interface ClientInterface
{
    /**
     * @param $method
     * @param $url
     * @param $headers
     * @param $data
     * @return mixed
     * HttpClient Interface
     */
    public function request($method, $url, $headers, $data);
}