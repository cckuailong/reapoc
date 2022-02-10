<?php

namespace Cloudipsp\Api\Payment;

use Cloudipsp\Api\Api;

class Reports extends Api
{
    private $url = '/reports/';
    /**
     * Minimal required params to get reports
     * @var array
     */
    private $requiredParams = [
        'merchant_id' => 'integer',
        'date_from' => 'date',
        'date_to' => 'date',
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
        $this->validate($requestData, $this->requiredParams, $dateFormat = 'd.m.Y H:i:s');
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