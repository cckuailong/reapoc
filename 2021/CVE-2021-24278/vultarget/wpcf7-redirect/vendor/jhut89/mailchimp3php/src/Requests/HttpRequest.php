<?php

namespace MailchimpAPI\Requests;

/**
 * Interface HttpRequest
 * @package MailchimpAPI\Requests
 */
interface HttpRequest
{

    /**
     * @param $name
     * @param $value
     *
     * @return mixed
     */
    public function setOption($name, $value);

    /**
     * @return mixed
     */
    public function executeCurl();

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getInfo($name);

    /**
     * @return string
     */
    public function getError();

    /**
     * @return mixed
     */
    public function close();
}
