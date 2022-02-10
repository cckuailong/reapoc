<?php


namespace Cloudipsp;

use Cloudipsp\Api\Checkout as Api;
use Cloudipsp\Response\Response;

class Verification
{
    /**
     * Minimal required params to get checkout
     * @var array
     */
    private static $defaultParams = [
        'verification' => 'Y',
        'verification_type' => 'amount'
    ];

    /**
     * return checkout url with card verify
     * @param $data
     * @param array $headers
     * @return Response
     * @throws Exception\ApiException
     */
    public static function url($data, $headers = [])
    {
        $data = array_merge($data, self::$defaultParams);
        $api = new Api\Verification();
        $result = $api->get($data, $headers);
        return new Response($result);
    }

    /**
     * return checkout form with card verify
     * @param $data
     * @return string
     * @throws Exception\ApiException
     */
    public static function form($data)
    {
        $api = new Api\Form();
        return $api->get($data);
    }

}