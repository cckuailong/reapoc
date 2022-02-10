<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client\Adapter;

use Bitpay\Client\RequestInterface;
use Bitpay\Client\ResponseInterface;

/**
 * A client can be configured to use a specific adapter to make requests, by
 * default the CurlAdapter is what is used.
 *
 * @package Bitpay
 */
interface AdapterInterface
{
    /**
     * Send request to BitPay
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request);
}
