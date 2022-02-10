<?php
namespace MercadoPago\Config;
use Exception;
use Symfony\Component\Yaml\Yaml as YamlParser;

/**
 * Yaml Class Doc Comment
 *
 * @package MercadoPago\Config
 */
class Yaml implements ParserInterface
{
    /**
     * @param $path
     *
     * @return mixed
     * @throws Exception
     */
    public function parse($path)
    {
        try {
            $data = YamlParser::parse(file_get_contents($path));
        } catch (Exception $exception) {
            throw new Exception('Error parsing YAML file');
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getSupportedExtensions()
    {
        return array('yaml', 'yml');
    }
}