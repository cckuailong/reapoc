<?php


namespace Cloudipsp;

use Cloudipsp\Api;
use Cloudipsp\Response\Response;

class P2pcredit
{

    /**
     * generate p2p request
     * @param $data
     * @param array $headers
     * @return Response
     * @throws Exception\ApiException
     */
    public static function start($data, $headers = [])
    {
        $api = new Api\P2pcredit\Credit('credit');
        $result = $api->get($data, $headers);
        return new Response($result, 'credit');
    }
}