<?php
namespace MercadoPago\Config;

/**
 * Interface ParserInterface
 *
 * @package MercadoPago\Config
 */
interface ParserInterface
{
    /**
     * @param $path
     *
     * @return mixed
     */
    public function parse($path);

    /**
     * @return mixed
     */
    public function getSupportedExtensions();
}