<?php

namespace Cloudipsp\Helper;

use Cloudipsp\Configuration;
use Cloudipsp\Helper\ApiHelper as Signature;

class ResultHelper
{
    /**
     * Check is Payment Valid
     * @param $result
     * @param string $secretKey
     * @param string $ver
     * @return bool|string
     */
    public static function isPaymentValid($result, $secretKey = '', $ver = '')
    {
        if ($secretKey == '') {
            if (Configuration::getSecretKey() != '') {
                $secretKey = Configuration::getSecretKey();
            }elseif (Configuration::getCreditKey() != '') {
                $secretKey = Configuration::getCreditKey();
            }
        }
        if ($ver == '') {
            $ver = Configuration::getApiVersion();
        }
        if (!array_key_exists('signature', $result)) return 'Nothing to validate';
        $signature = $result['signature'];
        if ($ver === '2.0') {
            $encoded = $result['encodedData'];
            return $signature === Signature::generateSignature($encoded, $secretKey, $ver);
        } else {
            $response = self::clearResult($result);
        }
        return $signature === Signature::generateSignature($response, $secretKey, $ver);
    }

    /**
     * Clearing before generate signature
     * @param array $result
     * @return array
     */
    public static function clearResult(Array $result)
    {
        if (array_key_exists('response_signature_string', $result))
            unset($result['response_signature_string']);
        if (array_key_exists('encodedData', $result))
            unset($result['encodedData']);
        unset($result['signature']);

        return $result;
    }

    /**
     * @param array $result
     * @return bool
     */
    public static function isActiveMerchant(Array $result)
    {
        if (Configuration::getMerchantId() == $result['merchant_id'])
            return true;

        return false;
    }

    /**
     * @param $data
     * @param string $secretKey
     * @param string $ver
     * @return bool|string
     */
    public static function isPaymentApproved($data, $secretKey = '', $ver = '')
    {
        if (!isset($data['order_status']))
            return 'Nothing to check';
        $valid = self::isPaymentValid($data, $secretKey, $ver);
        if ($valid && $data['order_status'] === 'approved')
            return true;

        return false;

    }

    /**
     * @param $data
     * @return bool
     */
    public function getVerifyStatus($data)
    {
        $status = $data['verification_status'];
        if ($status)
            return $status;

        return false;
    }
}