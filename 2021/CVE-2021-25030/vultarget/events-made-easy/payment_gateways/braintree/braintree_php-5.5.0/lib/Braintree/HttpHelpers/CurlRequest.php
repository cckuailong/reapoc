<?php
namespace Braintree\HttpHelpers;

class CurlRequest implements HttpRequest
{
    private $_handle = null;

    public function __construct($url)
    {
        $this->_handle = curl_init($url);
    }

    public function setOption($name, $value)
    {
        curl_setopt($this->_handle, $name, $value);
    }

    public function execute()
    {
        return curl_exec($this->_handle);
    }

    public function getInfo($name)
    {
        return curl_getinfo($this->_handle, $name);
    }

    public function getErrorCode()
    {
        return curl_errno($this->_handle);
    }

    public function getError()
    {
        return curl_error($this->_handle);
    }

    public function close()
    {
        curl_close($this->_handle);
    }
}
