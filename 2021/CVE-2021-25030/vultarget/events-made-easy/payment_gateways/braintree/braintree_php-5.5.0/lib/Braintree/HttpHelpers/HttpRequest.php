<?php
namespace Braintree\HttpHelpers;

interface HttpRequest
{
    public function setOption($name, $value);
    public function execute();
    public function getInfo($name);
    public function getErrorCode();
    public function getError();
    public function close();
}

