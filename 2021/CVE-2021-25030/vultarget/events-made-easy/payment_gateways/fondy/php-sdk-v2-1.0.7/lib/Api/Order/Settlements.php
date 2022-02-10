<?php

namespace Cloudipsp\Api\Order;

use Cloudipsp\Api\Api;

class Settlements extends Api
{
    private $url = '/settlement/';
    /**
     * Minimal required params
     * @var array
     */
    private $requiredParams = [
        'merchant_id' => 'integer',
        'operation_id' => 'string'
    ];

    /**
     * @param $data
     * @param array $headers
     * @return mixed
     * @throws \Cloudipsp\Exception\ApiException
     */
    public function get($data, $headers = [])
    {
        $data['order_type'] = 'settlement';
        $requestData = $this->prepareParams($data);
        $this->validate($requestData, $this->requiredParams);
        return $this->Request($method = 'POST', $this->url, $headers, $requestData);
    }
}
