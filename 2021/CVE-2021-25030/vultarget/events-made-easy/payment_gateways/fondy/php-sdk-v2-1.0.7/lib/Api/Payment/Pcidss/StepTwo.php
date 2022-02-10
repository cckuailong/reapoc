<?php

namespace Cloudipsp\Api\Payment\Pcidss;

use Cloudipsp\Api\Api;

class StepTwo extends Api
{
    private $url = '/3dsecure_step2/';
    /**
     * Minimal required params to get checkout
     * @var array
     */
    private $requiredParams = [
        'merchant_id' => 'integer',
        'order_id' => 'string',
        'pares' => 'string',
        'md' => 'string'
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
        if (!isset($params['merchant_id'])) {
            $params['merchant_id'] = $this->mid;
        }
        $returnData = [];
        foreach ($params as $key => $value) {
            $returnData[strtolower($key)] = trim($value);
        }
        return $returnData;
    }

}