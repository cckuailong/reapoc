<?php

namespace Cloudipsp\Response;

use Cloudipsp\Exception\ApiException;
use Cloudipsp\Helper\ResponseHelper;

class OrderResponse extends Response
{
    /**
     * @return array|mixed
     */
    public function getData()
    {
        if ($this->apiVersion === '2.0') {
            if (isset($this->response['response']['data'])) {
                return ResponseHelper::getBase64Data($this->response);
            } else {
                return $this->response['response'];
            }
        } else {
            return $this->response['response'];
        }
    }

    /**
     * @return bool
     */
    public function isReversed()
    {
        $data = $this->buildVerifyData();
        if (!isset($data['reverse_status']))
            return 'Nothing to check';
        $valid = $this->isValid();
        if ($valid && $data['reverse_status'] === 'approved')
            return true;

        return false;
    }

    /**
     * @return bool|string
     * @throws \Exception
     */
    public function isCaptured()
    {
        $data = $this->buildVerifyData();
        if (!isset($data['capture_status']))
            return 'Nothing to check';
        $valid = $this->isValid();
        if ($valid && $data['capture_status'] === 'captured')
            return true;

        return false;
    }

    /**
     * Checking if Captured transaction
     * @return bool
     * @throws \Exception
     */
    public function isCapturedByList()
    {
        $data = $this->getCapturedTransAction();

        if (!array_key_exists('capture_status', $data)) throw new \Exception('invalid response');
        return $data['capture_status'] != 'captured' ? false : true;
    }

    /**
     * @return mixed
     * @throws ApiException
     */
    private function getCapturedTransAction()
    {
        foreach ($this->getData() as $data) {
            if (($data['tran_type'] == 'purchase' || $data['tran_type'] == 'verification')
                && $data['preauth'] == 'Y'
                && $data['transaction_status'] == 'approved'
            ) {
                return $data;
            } else {
                throw new ApiException('Nothing to capture');
            }
        }
        return false;
    }
}