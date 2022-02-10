<?php
namespace Braintree;

/**
 * Braintree Xml parser and generator
 * superclass for Braintree XML parsing and generation
 */
class Xml
{
    /**
     * @ignore
     */
    protected function  __construct()
    {

    }

    /**
     *
     * @param string $xml
     * @return array
     */
    public static function buildArrayFromXml($xml)
    {
        return Xml\Parser::arrayFromXml($xml);
    }

    /**
     *
     * @param array $array
     * @return string
     */
    public static function buildXmlFromArray($array)
    {
        return Xml\Generator::arrayToXml($array);
    }
}
