<?php
namespace MercadoPago\Config;
use Exception;

/**
 * Json Class Doc Comment
 *
 * @package MercadoPago\Config
 */
class Json implements ParserInterface
{

    /**
     * @param $path
     *
     * @return mixed
     * @throws Exception
     */
    public function parse($path)
    {
        $data = json_decode(file_get_contents($path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_message  = 'Syntax error';
            if (function_exists('json_last_error_msg')) {
                $error_message = json_last_error_msg();
            }
            
            throw new Exception($error_message);
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getSupportedExtensions()
    {
        return array('json');
    }
}