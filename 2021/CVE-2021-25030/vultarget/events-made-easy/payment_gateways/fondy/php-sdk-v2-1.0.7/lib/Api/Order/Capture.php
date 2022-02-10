<?php

namespace Cloudipsp\Api\Order;

use Cloudipsp\Api\Api;

class Capture extends Api
{
    private $url = '/capture/order_id/';
    /**
     * Minimal required params
     * @var array
     */
    private $requiredParams = [
        'merchant_id' => 'integer',
        'order_id' => 'string',
        'amount' => 'integer',
        'currency' => 'string'
    ];

    /**
     * @param $data
     * @param array $headers
     * @return mixed
     * @throws \Cloudipsp\Exception\ApiException
     */
    public function get($data, $headers = [])
    {
        $requestData = $this->prepareParams($data);
        $this->validate($requestData, $this->requiredParams);
        return $this->Request($method = 'POST', $this->url, $headers, $requestData);
    }

    /**
     * @param $params
     * @return mixed
     */
    protected function prepareParams($params)
    {
        $prepared_params = $params;

        if (!isset($prepared_params['merchant_id'])) {
            $prepared_params['merchant_id'] = $this->mid;
        }
        return $prepared_params;
    }
}