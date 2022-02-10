<?php

namespace Cloudipsp\Api\P2pcredit;

use Cloudipsp\Api\Api;
use Cloudipsp\Helper;

class Credit extends Api
{
    private $url = '/p2pcredit/';
    /**
     * Minimal required params to get checkout
     * @var array
     */
    private $requiredParams = [
        'merchant_id' => 'integer',
        'order_desc' => 'string',
        'amount' => 'integer',
        'currency' => 'string'
    ];

    /**
     * @param $data
     * @param array $headers
     * @param array $requiredParams
     * @return mixed
     * @throws \Cloudipsp\Exception\ApiException
     */
    public function get($data, $headers = [], $requiredParams = [])
    {
        if ($requiredParams)
            $this->requiredParams = array_merge($requiredParams, $this->requiredParams);
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
        if (!isset($prepared_params['order_id'])) {
            $prepared_params['order_id'] = Helper\ApiHelper::generateOrderID($this->mid);
        }
        if (!isset($prepared_params['order_desc'])) {
            $prepared_params['order_desc'] = Helper\ApiHelper::generateOrderDesc($prepared_params['order_id']);
        }
        if (empty($prepared_params['receiver_card_number']) && empty($prepared_params['receiver_rectoken'])) {
            throw new \InvalidArgumentException('Request must contain additional parameter receiver_card_number or receiver_rectoken');
        }
        return $prepared_params;
    }

}