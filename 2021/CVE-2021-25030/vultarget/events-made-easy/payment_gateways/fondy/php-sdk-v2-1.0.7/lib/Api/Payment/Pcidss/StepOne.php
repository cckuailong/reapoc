<?php

namespace Cloudipsp\Api\Payment\Pcidss;

use Cloudipsp\Api\Api;

class StepOne extends Api
{
    private $url = '/3dsecure_step1/';
    /**
     * Minimal required params to get checkout
     * @var array
     */
    private $requiredParams = [
        'merchant_id' => 'integer',
        'order_id' => 'string',
        'order_desc' => 'string',
        'currency' => 'string',
        'amount' => 'integer',
        'card_number' => 'ccnumber',
        'cvv2' => 'integer',
        'expiry_date' => 'integer',
        'client_ip' => 'ip'
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

}