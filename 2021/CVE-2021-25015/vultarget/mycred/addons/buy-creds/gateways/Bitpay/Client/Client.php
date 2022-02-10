<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client;

use Bitpay\Client\Adapter\AdapterInterface;
use Bitpay\Network\NetworkInterface;
use Bitpay\TokenInterface;
use Bitpay\InvoiceInterface;
use Bitpay\PayoutInterface;
use Bitpay\Util\Util;
use Bitpay\PublicKey;
use Bitpay\PrivateKey;

/**
 * Client used to send requests and receive responses for BitPay's Web API
 *
 * @package Bitpay
 */
class Client implements ClientInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var TokenInterface
     */
    protected $token;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var PublicKey
     */
    protected $publicKey;

    /**
     * @var PrivateKey
     */
    protected $privateKey;

    /**
     * @var NetworkInterface
     */
    protected $network;

    /**
     * The network is either livenet or testnet and tells the client where to
     * send the requests.
     *
     * @param NetworkInterface
     */
    public function setNetwork(NetworkInterface $network)
    {
        $this->network = $network;
    }

    /**
     * Set the Public Key to use to help identify who you are to BitPay. Please
     * note that you must first pair your keys and get a token in return to use.
     *
     * @param PublicKey $key
     */
    public function setPublicKey(PublicKey $key)
    {
        $this->publicKey = $key;
    }

    /**
     * Set the Private Key to use, this is used when signing request strings
     *
     * @param PrivateKey $key
     */
    public function setPrivateKey(PrivateKey $key)
    {
        $this->privateKey = $key;
    }

    /**
     * @param AdapterInterface $adapter
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param TokenInterface $token
     * @return ClientInterface
     */
    public function setToken(TokenInterface $token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function createInvoice(InvoiceInterface $invoice)
    {
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPath('invoices');

        $currency     = $invoice->getCurrency();
        $item         = $invoice->getItem();
        $buyer        = $invoice->getBuyer();
        $buyerAddress = $buyer->getAddress();

        $this->checkPriceAndCurrency($item->getPrice(), $currency->getCode());

        $body = array(
            'price'             => $item->getPrice(),
            'currency'          => $currency->getCode(),
            'posData'           => $invoice->getPosData(),
            'notificationURL'   => $invoice->getNotificationUrl(),
            'transactionSpeed'  => $invoice->getTransactionSpeed(),
            'fullNotifications' => $invoice->isFullNotifications(),
            'extendedNotifications' => $invoice->isExtendedNotifications(),
            'notificationEmail' => $invoice->getNotificationEmail(),
            'redirectURL'       => $invoice->getRedirectUrl(),
            'orderID'           => $invoice->getOrderId(),
            'itemDesc'          => $item->getDescription(),
            'itemCode'          => $item->getCode(),
            'physical'          => $item->isPhysical(),
            'buyerName'         => trim(sprintf('%s %s', $buyer->getFirstName(), $buyer->getLastName())),
            'buyerAddress1'     => isset($buyerAddress[0]) ? $buyerAddress[0] : '',
            'buyerAddress2'     => isset($buyerAddress[1]) ? $buyerAddress[1] : '',
            'buyerCity'         => $buyer->getCity(),
            'buyerState'        => $buyer->getState(),
            'buyerZip'          => $buyer->getZip(),
            'buyerCountry'      => $buyer->getCountry(),
            'buyerEmail'        => $buyer->getEmail(),
            'buyerPhone'        => $buyer->getPhone(),
            'buyerNotify'       => $buyer->getNotify(),
            'guid'              => Util::guid(),
            'nonce'             => Util::nonce(),
            'token'             => $this->token->getToken(),
        );

        $request->setBody(json_encode($body));
        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);
        $this->request  = $request;
        $this->response = $this->sendRequest($request);

        $body = json_decode($this->response->getBody(), true);
        $error_message = false;
        $error_message = (!empty($body['error'])) ? $body['error'] : $error_message;
        $error_message = (!empty($body['errors'])) ? $body['errors'] : $error_message;
        $error_message = (is_array($error_message)) ? implode("\n", $error_message) : $error_message;
        if (false !== $error_message) {
            throw new \Exception($error_message);
        }
        $data = $body['data'];
        $invoiceToken = new \Bitpay\Token();
        $invoice
            ->setToken($invoiceToken->setToken($data['token']))
            ->setId($data['id'])
            ->setUrl($data['url'])
            ->setStatus($data['status'])
            ->setPrice($data['price'])
            ->setInvoiceTime($data['invoiceTime'])
            ->setExpirationTime($data['expirationTime'])
            ->setCurrentTime($data['currentTime'])
            ->setExceptionStatus($data['exceptionStatus']);

        return $invoice;
    }

    /**
     * @inheritdoc
     */
    public function getCurrencies()
    {
        $this->request = $this->createNewRequest();
        $this->request->setMethod(Request::METHOD_GET);
        $this->request->setPath('currencies');
        $this->response = $this->sendRequest($this->request);
        $body           = json_decode($this->response->getBody(), true);
        if (empty($body['data'])) {
            throw new \Exception('Error with request: no data returned');
        }
        $currencies = $body['data'];
        array_walk($currencies, function (&$value, $key) {
            $currency = new \Bitpay\Currency();
            $currency
                ->setCode($value['code'])
                ->setSymbol($value['symbol'])
                ->setPrecision($value['precision'])
                ->setExchangePctFee($value['exchangePctFee'])
                ->setPayoutEnabled($value['payoutEnabled'])
                ->setName($value['name'])
                ->setPluralName($value['plural'])
                ->setAlts($value['alts'])
                ->setPayoutFields($value['payoutFields']);
            $value = $currency;
        });

        return $currencies;
    }

    /**
     * @inheritdoc
     */
    public function createPayout(PayoutInterface $payout)
    {
        $request = $this->createNewRequest();
        $request->setMethod($request::METHOD_POST);
        $request->setPath('payouts');

        $amount         = $payout->getAmount();
        $currency       = $payout->getCurrency();
        $effectiveDate  = $payout->getEffectiveDate();
        $token          = $payout->getToken();

        $body = array(
            'token'         => $token->getToken(),
            'amount'        => $amount,
            'currency'      => $currency->getCode(),
            'instructions'  => array(),
            'effectiveDate' => $effectiveDate,
            'pricingMethod' => $payout->getPricingMethod(),
            'guid'          => Util::guid(),
            'nonce'         => Util::nonce()
        );

        // Optional
        foreach (array('reference','notificationURL','notificationEmail') as $value) {
            $function = 'get' . ucfirst($value);
            if ($payout->$function() != null) {
                $body[$value] = $payout->$function();
            }
        }

        // Add instructions
        foreach ($payout->getInstructions() as $instruction) {
            $body['instructions'][] = array(
                'label'   => $instruction->getLabel(),
                'address' => $instruction->getAddress(),
                'amount'  => $instruction->getAmount()
            );
        }

        $request->setBody(json_encode($body));
        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);

        $this->request  = $request;
        $this->response = $this->sendRequest($request);
        $body = json_decode($this->response->getBody(), true);
        $error_message = false;
        $error_message = (!empty($body['error'])) ? $body['error'] : $error_message;
        $error_message = (!empty($body['errors'])) ? $body['errors'] : $error_message;
        $error_message = (is_array($error_message)) ? implode("\n", $error_message) : $error_message;
        if (false !== $error_message) {
            throw new \Exception($error_message);
        }

        $data = $body['data'];
        $payout
            ->setId($data['id'])
            ->setAccountId($data['account'])
            ->setResponseToken($data['token'])
            ->setStatus($data['status']);

        foreach ($data['instructions'] as $c => $instruction) {
            $payout->updateInstruction($c, 'setId', $instruction['id']);
        }

        return $payout;
    }

    /**
     * @inheritdoc
     */
    public function getPayouts($status = null)
    {
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_GET);
        $path = 'payouts?token='
                    . $this->token->getToken()
                    . (($status == null) ? '' : '&status=' . $status);
        $request->setPath($path);

        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);

        $this->request  = $request;
        $this->response = $this->sendRequest($this->request);
        $body           = json_decode($this->response->getBody(), true);
        $error_message = false;
        $error_message = (!empty($body['error'])) ? $body['error'] : $error_message;
        $error_message = (!empty($body['errors'])) ? $body['errors'] : $error_message;
        $error_message = (is_array($error_message)) ? implode("\n", $error_message) : $error_message;
        if (false !== $error_message) {
            throw new \Exception($error_message);
        }

        $payouts = array();

        array_walk($body['data'], function ($value, $key) use (&$payouts) {
            $payout = new \Bitpay\Payout();
            $payout
                ->setId($value['id'])
                ->setAccountId($value['account'])
                ->setCurrency(new \Bitpay\Currency($value['currency']))
                ->setEffectiveDate($value['effectiveDate'])
                ->setRequestdate($value['requestDate'])
                ->setPricingMethod($value['pricingMethod'])
                ->setStatus($value['status'])
                ->setAmount($value['amount'])
                ->setResponseToken($value['token'])
                ->setRate(@$value['rate'])
                ->setBtcAmount(@$value['btc'])
                ->setReference(@$value['reference'])
                ->setNotificationURL(@$value['notificationURL'])
                ->setNotificationEmail(@$value['notificationEmail']);

            array_walk($value['instructions'], function ($value, $key) use (&$payout) {
                $instruction = new \Bitpay\PayoutInstruction();
                $instruction
                    ->setId($value['id'])
                    ->setLabel($value['label'])
                    ->setAddress($value['address'])
                    ->setAmount($value['amount'])
                    ->setStatus($value['status']);

                array_walk($value['transactions'], function ($value, $key) use (&$instruction) {
                    $transaction = new \Bitpay\PayoutTransaction();
                    $transaction
                        ->setTransactionId($value['txid'])
                        ->setAmount($value['amount'])
                        ->setDate($value['date']);

                    $instruction->addTransaction($transaction);
                });

                $payout->addInstruction($instruction);
            });

            $payouts[] = $payout;
        });

        return $payouts;
    }

    /**
     * @inheritdoc
     */
    public function deletePayout(PayoutInterface $payout)
    {
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_DELETE);
        $request->setPath(sprintf('payouts/%s?token=%s', $payout->getId(), $payout->getResponseToken()));

        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);

        $this->request  = $request;
        $this->response = $this->sendRequest($this->request);

        $body           = json_decode($this->response->getBody(), true);
        if (empty($body['data'])) {
            throw new \Exception('Error with request: no data returned');
        }

        $data   = $body['data'];

        $payout->setStatus($data['status']);

        return $payout;
    }

    /**
     * @inheritdoc
     */
    public function getPayout($payoutId)
    {
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setPath(sprintf('payouts/%s?token=%s', $payoutId, $this->token->getToken()));
        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);

        $this->request  = $request;
        $this->response = $this->sendRequest($this->request);

        $body           = json_decode($this->response->getBody(), true);
        if (empty($body['data'])) {
            throw new \Exception('Error with request: no data returned');
        }
        $data   = $body['data'];

        $payout = new \Bitpay\Payout();
        $payout
            ->setId($data['id'])
            ->setAccountId($data['account'])
            ->setStatus($data['status'])
            ->setCurrency(new \Bitpay\Currency($data['currency']))
            ->setRate(@$data['rate'])
            ->setAmount($data['amount'])
            ->setBtcAmount(@$data['btc'])
            ->setPricingMethod(@$data['pricingMethod'])
            ->setReference(@$data['reference'])
            ->setNotificationEmail(@$data['notificationEmail'])
            ->setNotificationUrl(@$data['notificationURL'])
            ->setRequestDate($data['requestDate'])
            ->setEffectiveDate($data['effectiveDate'])
            ->setResponseToken($data['token']);

        array_walk($data['instructions'], function ($value, $key) use (&$payout) {
            $instruction = new \Bitpay\PayoutInstruction();
            $instruction
                ->setId($value['id'])
                ->setLabel($value['label'])
                ->setAddress($value['address'])
                ->setStatus($value['status'])
                ->setAmount($value['amount'])
                ->setBtc($value['btc']);

            array_walk($value['transactions'], function ($value, $key) use (&$instruction) {
                $transaction = new \Bitpay\PayoutTransaction();
                $transaction
                    ->setTransactionId($value['txid'])
                    ->setAmount($value['amount'])
                    ->setDate($value['date']);

                $instruction->addTransaction($transaction);
            });

            $payout->addInstruction($instruction);
        });

        return $payout;
    }

    /**
     * @inheritdoc
     */
    public function getTokens()
    {
        $request = $this->createNewRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setPath('tokens');
        $this->addIdentityHeader($request);
        $this->addSignatureHeader($request);

        $this->request  = $request;
        $this->response = $this->sendRequest($this->request);
        $body           = json_decode($this->response->getBody(), true);
        if (empty($body['data'])) {
            throw new \Exception('Error with request: no data returned');
        }

        $tokens = array();

        array_walk($body['data'], function ($value, $key) use (&$tokens) {
            $key   = current(array_keys($value));
            $value = current(array_values($value));
            $token = new \Bitpay\Token();
            $token
                ->setFacade($key)
                ->setToken($value);

            $tokens[$token->getFacade()] = $token;
        });

        return $tokens;
    }

    /**
     * @inheritdoc
     */
    public function createToken(array $payload = array())
    {
        if (isset($payload['pairingCode']) && 1 !== preg_match('/^[a-zA-Z0-9]{7}$/', $payload['pairingCode'])) {
            throw new \InvalidArgumentException("pairing code is not legal");
        }

        $this->request = $this->createNewRequest();
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->setPath('tokens');
        $payload['guid'] = Util::guid();
        $this->request->setBody(json_encode($payload));
        $this->response = $this->sendRequest($this->request);
        $body           = json_decode($this->response->getBody(), true);

        if (isset($body['error'])) {
            throw new \Bitpay\Client\BitpayException($this->response->getStatusCode().": ".$body['error']);
        }

        $tkn = $body['data'][0];
        $createdAt = new \DateTime();
        $pairingExpiration = new \DateTime();

        $token = new \Bitpay\Token();
        $token
            ->setPolicies($tkn['policies'])
            ->setToken($tkn['token'])
            ->setFacade($tkn['facade'])
            ->setCreatedAt($createdAt->setTimestamp(floor($tkn['dateCreated']/1000)));

        if (isset($tkn['resource'])) {
            $token->setResource($tkn['resource']);
        }

        if (isset($tkn['pairingCode'])) {
            $token->setPairingCode($tkn['pairingCode']);
            $token->setPairingExpiration($pairingExpiration->setTimestamp(floor($tkn['pairingExpiration']/1000)));
        }

        return $token;
    }

    /**
     * Returns the Response object that BitPay returned from the request that
     * was sent
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns the request object that was sent to BitPay
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @inheritdoc
     */
    public function getInvoice($invoiceId)
    {
        $this->request = $this->createNewRequest();
        $this->request->setMethod(Request::METHOD_GET);
        if ($this->token && $this->token->getFacade() === 'merchant') {
            $this->request->setPath(sprintf('invoices/%s?token=%s', $invoiceId, $this->token->getToken()));
            $this->addIdentityHeader($this->request);
            $this->addSignatureHeader($this->request);
        } else {
            $this->request->setPath(sprintf('invoices/%s', $invoiceId));
        }
        $this->response = $this->sendRequest($this->request);
        $body = json_decode($this->response->getBody(), true);

        if (isset($body['error'])) {
            throw new \Exception($body['error']);
        }

        $data = $body['data'];
        
        
        $invoice = new \Bitpay\Invoice();
        $invoiceToken = new \Bitpay\Token();
        $invoice
            ->setToken($invoiceToken->setToken($data['token']))
            ->setUrl($data['url'])
            ->setPosData(array_key_exists('posData', $data) ? $data['posData'] : '')
            ->setStatus($data['status'])
            ->setBtcPrice($data['btcPrice'])
            ->setPrice($data['price'])
            ->setCurrency(new \Bitpay\Currency($data['currency']))
            ->setOrderId(array_key_exists('orderId', $data) ? $data['orderId'] : '')
            ->setInvoiceTime($data['invoiceTime'])
            ->setExpirationTime($data['expirationTime'])
            ->setCurrentTime($data['currentTime'])
            ->setId($data['id'])
            ->setBtcPaid($data['btcPaid'])
            ->setRate($data['rate'])
            ->setExceptionStatus($data['exceptionStatus'])
            //->setRefundAddress(array_key_exists('refundAddresses', $data) ? key($data['refundAddresses'][0]) : '');
            ->setRefundAddresses(array_key_exists('refundAddresses', $data) ? $data['refundAddresses'] : '');
    

        return $invoice;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request)
    {
        if (null === $this->adapter) {
            // Uses the default adapter
            $this->adapter = new \Bitpay\Client\Adapter\CurlAdapter();
        }

        return $this->adapter->sendRequest($request);
    }

    /**
     * @param RequestInterface $request
     */
    protected function addIdentityHeader(RequestInterface $request)
    {
        if (null === $this->publicKey) {
            throw new \Exception('Please set your Public Key.');
        }

        $request->setHeader('x-identity', (string) $this->publicKey);
    }

    /**
     * @param RequestInterface $request
     */
    protected function addSignatureHeader(RequestInterface $request)
    {
        if (null === $this->privateKey) {
            throw new \Exception('Please set your Private Key');
        }

        if (true == property_exists($this->network, 'isPortRequiredInUrl')) {
            if ($this->network->isPortRequiredInUrl === true) {
                $url = $request->getUriWithPort();
            } else {
                $url = $request->getUri();
            }
        } else {
            $url = $request->getUri();
        }

        $message = sprintf(
            '%s%s',
            $url,
            $request->getBody()
        );

        $signature = $this->privateKey->sign($message);

        $request->setHeader('x-signature', $signature);
    }

    /**
     * @return RequestInterface
     */
    protected function createNewRequest()
    {
        $request = new Request();
        $request->setHost($this->network->getApiHost());
        $request->setPort($this->network->getApiPort());
        $this->prepareRequestHeaders($request);

        return $request;
    }

    /**
     * Prepares the request object by adding additional headers
     *
     * @param RequestInterface $request
     */
    protected function prepareRequestHeaders(RequestInterface $request)
    {
        // @see http://en.wikipedia.org/wiki/User_agent
        $request->setHeader(
            'User-Agent',
            sprintf('%s/%s (PHP %s)', self::NAME, self::VERSION, phpversion())
        );
        $request->setHeader('X-BitPay-Plugin-Info', sprintf('%s/%s', self::NAME, self::VERSION));
        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('X-Accept-Version', '2.0.0');
    }

    protected function checkPriceAndCurrency($price, $currency)
    {
        $decimalPosition = strpos($price, '.');
        if ($decimalPosition == 0) {
            $decimalPrecision = 0;
        } else {
            $decimalPrecision = strlen(substr($price, $decimalPosition + 1));
        }
        if (($decimalPrecision > 2 && $currency != 'BTC') || $decimalPrecision > 6) {
            throw new \Exception('Incorrect price format or currency type.');
        }
    }
}
