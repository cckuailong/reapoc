<?php
namespace MercadoPago\Http;
/**
 * Interface HttpRequest
 *
 * @package MercadoPago\Http
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
    public function execute();

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getInfo($name);

    /**
     * @return mixed
     */
    public function close();

    /**
     * @return mixed
     */
    public function error();
}