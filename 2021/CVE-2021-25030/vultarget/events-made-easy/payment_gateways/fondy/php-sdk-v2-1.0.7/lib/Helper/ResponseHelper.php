<?php

namespace Cloudipsp\Helper;


class ResponseHelper
{
    /**
     * @param string
     * @return array
     */
    public static function getBase64Data($data)
    {
        if (isset($data['response']['token']))
            return $data['response'];
        if (isset($data['response']['data'])) {
            $orderData = json_decode(base64_decode($data['response']['data']), TRUE)['order'];
        } else {
            $orderData = $data['response'];
        }
        return $orderData;
    }

    /**
     * @param string
     * @return array
     */
    public static function jsonToArray($data)
    {
        return json_decode($data, TRUE);
    }

    /**
     * convert xml to array
     * @param $contents
     * @param bool $getAttributes
     * @param bool $tagPriority
     * @param string $encoding
     * @return array
     */
    public static function xmlToArray($contents, $getAttributes = true, $tagPriority = true, $encoding = 'UTF-8')
    {
        $contents = trim($contents);
        $contents = str_replace('\n', '', $contents);
        if (empty($contents)) {
            return [];
        }
        $parser = xml_parser_create($encoding);
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $encoding);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        if (xml_parse_into_struct($parser, $contents, $xmlValues) === 0) {
            xml_parser_free($parser);
            return [];
        }
        xml_parser_free($parser);
        if (empty($xmlValues)) {
            return [];
        }
        unset($contents, $parser);
        $xmlArray = [];
        $current = &$xmlArray;
        $repeatedTagIndex = [];
        foreach ($xmlValues as $num => $xmlTag) {
            $result = null;
            $attributesData = null;
            if (isset($xmlTag['value'])) {
                if ($tagPriority) {
                    $result = $xmlTag['value'];
                } else {
                    $result['value'] = $xmlTag['value'];
                }
            }
            if (isset($xmlTag['attributes']) and $getAttributes) {
                foreach ($xmlTag['attributes'] as $attr => $val) {
                    if ($tagPriority) {
                        $attributesData[$attr] = $val;
                    } else {
                        $result['@attributes'][$attr] = $val;
                    }
                }
            }
            if ($xmlTag['type'] == 'open') {
                $parent[$xmlTag['level'] - 1] = &$current;
                if (!is_array($current) or (!in_array($xmlTag['tag'], array_keys($current)))) {
                    $current[$xmlTag['tag']] = $result;
                    unset($result);
                    if ($attributesData) {
                        $current['@' . $xmlTag['tag']] = $attributesData;
                    }
                    $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] = 1;
                    $current = &$current[$xmlTag['tag']];
                } else {
                    if (isset($current[$xmlTag['tag']]['0'])) {
                        $current[$xmlTag['tag']][$repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']]] = $result;
                        unset($result);
                        if ($attributesData) {
                            if (isset($repeatedTagIndex['@' . $xmlTag['tag'] . '_' . $xmlTag['level']])) {
                                $current[$xmlTag['tag']][$repeatedTagIndex['@' . $xmlTag['tag'] . '_' . $xmlTag['level']]] = $attributesData;
                            }
                        }
                        $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] += 1;
                    } else {
                        $current[$xmlTag['tag']] = [$current[$xmlTag['tag']], $result];
                        unset($result);
                        $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] = 2;
                        if (isset($current['@' . $xmlTag['tag']])) {
                            $current[$xmlTag['tag']]['@0'] = $current['@' . $xmlTag['tag']];
                            unset($current['@' . $xmlTag['tag']]);
                        }
                        if ($attributesData) {
                            $current[$xmlTag['tag']]['@1'] = $attributesData;
                        }
                    }
                    $lastItemIndex = $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] - 1;
                    $current = &$current[$xmlTag['tag']][$lastItemIndex];
                }
            } elseif ($xmlTag['type'] == 'complete') {
                if (!isset($current[$xmlTag['tag']]) and empty($current['@' . $xmlTag['tag']])) {
                    $current[$xmlTag['tag']] = $result;
                    unset($result);
                    $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] = 1;
                    if ($tagPriority and $attributesData) {
                        $current['@' . $xmlTag['tag']] = $attributesData;
                    }
                } else {
                    if (isset($current[$xmlTag['tag']]['0']) and is_array($current[$xmlTag['tag']])) {
                        $current[$xmlTag['tag']][$repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']]] = $result;
                        unset($result);
                        if ($tagPriority and $getAttributes and $attributesData) {
                            $current[$xmlTag['tag']]['@' . $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']]] = $attributesData;
                        }
                        $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] += 1;
                    } else {
                        $current[$xmlTag['tag']] = [
                            $current[$xmlTag['tag']],
                            $result,
                        ];
                        unset($result);
                        $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] = 1;
                        if ($tagPriority and $getAttributes) {
                            if (isset($current['@' . $xmlTag['tag']])) {
                                $current[$xmlTag['tag']]['@0'] = $current['@' . $xmlTag['tag']];
                                unset($current['@' . $xmlTag['tag']]);
                            }
                            if ($attributesData) {
                                $current[$xmlTag['tag']]['@' . $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']]] = $attributesData;
                            }
                        }
                        $repeatedTagIndex[$xmlTag['tag'] . '_' . $xmlTag['level']] += 1;
                    }
                }
            } elseif ($xmlTag['type'] == 'close') {
                $current = &$parent[$xmlTag['level'] - 1];
            }
            unset($xmlValues[$num]);
        }
        return $xmlArray;
    }

    /**
     * @param string
     * @return array
     */
    public static function formToArray($data)
    {
        $response = [];
        parse_str($data, $response);
        return $response;
    }
}