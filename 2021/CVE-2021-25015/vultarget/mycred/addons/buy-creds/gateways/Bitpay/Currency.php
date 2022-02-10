<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

use Bitpay\Client;

/**
 * For the most part this should conform to ISO 4217
 *
 * @see http://en.wikipedia.org/wiki/ISO_4217
 * @package Bitpay
 */
class Currency implements CurrencyInterface
{
    /**
     * @see https://bitpay.com/currencies
     * @var array
     */
    protected static $availableCurrencies = array(
        'BTC', 'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG',
        'AZN', 'BAM', 'BBD', 'BDT', 'BGN', 'BHD', 'BIF', 'BMD', 'BND', 'BOB',
        'BRL', 'BSD', 'BTN', 'BWP', 'BYR', 'BZD', 'CAD', 'CDF', 'CHF', 'CLF',
        'CLP', 'CNY', 'COP', 'CRC', 'CVE', 'CZK', 'DJF', 'DKK', 'DOP', 'DZD',
        'EEK', 'EGP', 'ERN', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL', 'GHS',
        'GIP', 'GMD', 'GNF', 'GTQ', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF',
        'IDR', 'ILS', 'INR', 'IQD', 'ISK', 'JEP', 'JMD', 'JOD', 'JPY', 'KES',
        'KGS', 'KHR', 'KMF', 'KRW', 'KWD', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR',
        'LRD', 'LSL', 'LTL', 'LVL', 'LYD', 'MAD', 'MDL', 'MGA', 'MKD', 'MMK',
        'MNT', 'MOP', 'MRO', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN', 'NAD',
        'NGN', 'NIO', 'NOK', 'NPR', 'NZD', 'OMR', 'PAB', 'PEN', 'PGK', 'PHP',
        'PKR', 'PLN', 'PYG', 'QAR', 'RON', 'RSD', 'RUB', 'RWF', 'SAR', 'SBD',
        'SCR', 'SDG', 'SEK', 'SGD', 'SHP', 'SLL', 'SOS', 'SRD', 'STD', 'SVC',
        'SYP', 'SZL', 'THB', 'TJS', 'TMT', 'TND', 'TOP', 'TRY', 'TTD', 'TWD',
        'TZS', 'UAH', 'UGX', 'USD', 'UYU', 'UZS', 'VEF', 'VND', 'VUV', 'WST',
        'XAF', 'XAG', 'XAU', 'XCD', 'XOF', 'XPF', 'YER', 'ZAR', 'ZMW', 'ZWL',
        'CUP', 'IRR', 'KPW'
    );

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $symbol;

    /**
     * @var integer
     */
    protected $precision;

    /**
     * @var string
     */
    protected $exchangePercentageFee;

    /**
     * @var boolean
     */
    protected $payoutEnabled;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $pluralName;

    /**
     * @var array
     */
    protected $alts;

    /**
     * @var array
     */
    protected $payoutFields;

    /**
     * @param  string    $code The Currency Code to use, ie USD
     * @throws Exception       Throws an exception if the Currency Code is not supported
     */
    public function __construct($code = null)
    {
        if (null !== $code) {
            $this->setCode($code);
        }

        $this->payoutEnabled = false;
        $this->payoutFields  = array();
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * This will change the $code to all uppercase
     *
     * @param  string            $code The Currency Code to use, ie USD
     * @throws Exception               Throws an exception if the Currency Code is not supported
     * @return CurrencyInterface
     */
    public function setCode($code)
    {
        if (null !== $code && !in_array(strtoupper($code), self::$availableCurrencies)) {
            throw new \Bitpay\Client\ArgumentException(
                sprintf('The currency code "%s" is not supported.', $code)
            );
        }

        $this->code = strtoupper($code);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     *
     * @return CurrencyInterface
     */
    public function setSymbol($symbol)
    {
        if (!empty($symbol) && ctype_print($symbol)) {
            $this->symbol = trim($symbol);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @param integer $precision
     *
     * @return CurrencyInterface
     */
    public function setPrecision($precision)
    {
        if (!empty($precision) && ctype_digit(strval($precision))) {
            $this->precision = (int) $precision;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExchangePctFee()
    {
        return $this->exchangePercentageFee;
    }

    /**
     * @param string $fee
     *
     * @return CurrencyInterface
     */
    public function setExchangePctFee($fee)
    {
        if (!empty($fee) && ctype_print($fee)) {
            $this->exchangePercentageFee = trim($fee);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isPayoutEnabled()
    {
        return $this->payoutEnabled;
    }

    /**
     * @param boolean $enabled
     *
     * @return CurrencyInterface
     */
    public function setPayoutEnabled($enabled)
    {
        $this->payoutEnabled = (boolean) $enabled;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return CurrencyInterface
     */
    public function setName($name)
    {
        if (!empty($name) && ctype_print($name)) {
            $this->name = trim($name);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return $this->pluralName;
    }

    /**
     * @param string $pluralName
     *
     * @return CurrencyInterface
     */
    public function setPluralName($pluralName)
    {
        if (!empty($pluralName) && ctype_print($pluralName)) {
            $this->pluralName = trim($pluralName);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAlts()
    {
        return $this->alts;
    }

    /**
     * @param array $alts
     *
     * @return CurrencyInterface
     */
    public function setAlts($alts)
    {
        $this->alts = $alts;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPayoutFields()
    {
        return $this->payoutFields;
    }

    /**
     * @param array $payoutFields
     *
     * @return CurrencyInterface
     */
    public function setPayoutFields(array $payoutFields)
    {
        $this->payoutFields = $payoutFields;

        return $this;
    }
}
