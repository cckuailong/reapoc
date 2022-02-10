<?php
namespace Braintree;

use InvalidArgumentException;

/**
 * Braintree PaymentMethodGateway module
 *
 * @package    Braintree
 * @category   Resources
 */

/**
 * Creates and manages Braintree PaymentMethods
 *
 * <b>== More information ==</b>
 *
 *
 * @package    Braintree
 * @category   Resources
 */
class PaymentMethodGateway
{
    private $_gateway;
    private $_config;
    private $_http;

    public function __construct($gateway)
    {
        $this->_gateway = $gateway;
        $this->_config = $gateway->config;
        $this->_config->assertHasAccessTokenOrKeys();
        $this->_http = new Http($gateway->config);
    }


    public function create($attribs)
    {
        Util::verifyKeys(self::createSignature(), $attribs);
        return $this->_doCreate('/payment_methods', ['payment_method' => $attribs]);
    }

    /**
     * find a PaymentMethod by token
     *
     * @param string $token payment method unique id
     * @return CreditCard|PayPalAccount
     * @throws Exception\NotFound
     */
    public function find($token)
    {
        $this->_validateId($token);
        try {
            $path = $this->_config->merchantPath() . '/payment_methods/any/' . $token;
            $response = $this->_http->get($path);
            if (isset($response['creditCard'])) {
                return CreditCard::factory($response['creditCard']);
            } else if (isset($response['paypalAccount'])) {
                return PayPalAccount::factory($response['paypalAccount']);
            } else if (isset($response['coinbaseAccount'])) {
                return CoinbaseAccount::factory($response['coinbaseAccount']);
            } else if (isset($response['applePayCard'])) {
                return ApplePayCard::factory($response['applePayCard']);
            } else if (isset($response['androidPayCard'])) {
                return AndroidPayCard::factory($response['androidPayCard']);
            } else if (isset($response['amexExpressCheckoutCard'])) {
                return AmexExpressCheckoutCard::factory($response['amexExpressCheckoutCard']);
            } else if (isset($response['europeBankAccount'])) {
                return EuropeBankAccount::factory($response['europeBankAccount']);
            } else if (isset($response['usBankAccount'])) {
                return UsBankAccount::factory($response['usBankAccount']);
            } else if (isset($response['venmoAccount'])) {
                return VenmoAccount::factory($response['venmoAccount']);
            } else if (isset($response['visaCheckoutCard'])) {
                return VisaCheckoutCard::factory($response['visaCheckoutCard']);
            } else if (isset($response['masterpassCard'])) {
                return MasterpassCard::factory($response['masterpassCard']);
            } else if (isset($response['samsungPayCard'])) {
                return SamsungPayCard::factory($response['samsungPayCard']);
            } else if (is_array($response)) {
                return UnknownPaymentMethod::factory($response);
            }
        } catch (Exception\NotFound $e) {
            throw new Exception\NotFound(
                'payment method with token ' . $token . ' not found'
            );
        }
    }

    public function update($token, $attribs)
    {
        Util::verifyKeys(self::updateSignature(), $attribs);
        return $this->_doUpdate('/payment_methods/any/' . $token, ['payment_method' => $attribs]);
    }

    public function delete($token, $options=[])
    {
        Util::verifyKeys(self::deleteSignature(), $options);
        $this->_validateId($token);
        $queryString = "";
        if (!empty($options)) {
            $queryString = "?" . http_build_query(Util::camelCaseToDelimiterArray($options, '_'));
        }
        return $this->_doDelete('/payment_methods/any/' . $token  . $queryString);
    }

    public function grant($sharedPaymentMethodToken, $attribs=[])
    {
        if (is_bool($attribs) === true) {
            $attribs = ['allow_vaulting' => $attribs];
        }
        $options = [ 'shared_payment_method_token' => $sharedPaymentMethodToken ];

        return $this->_doCreate(
            '/payment_methods/grant',
            [
                'payment_method' => array_merge($attribs, $options)
            ]
        );
    }

    public function revoke($sharedPaymentMethodToken)
    {
        return $this->_doCreate(
            '/payment_methods/revoke',
            [
                'payment_method' => [
                    'shared_payment_method_token' => $sharedPaymentMethodToken
                ]
            ]
        );
    }

    private static function baseSignature()
    {
        $billingAddressSignature = AddressGateway::createSignature();
        $optionsSignature = [
            'failOnDuplicatePaymentMethod',
            'makeDefault',
            'verificationMerchantAccountId',
            'verifyCard',
            'verificationAmount',
            'usBankAccountVerificationMethod',
            ['paypal' => [
                'payee_email',
                'payeeEmail',
                'order_id',
                'orderId',
                'custom_field',
                'customField',
                'description',
                'amount',
                ['shipping' =>
                    [
                        'firstName', 'lastName', 'company', 'countryName',
                        'countryCodeAlpha2', 'countryCodeAlpha3', 'countryCodeNumeric',
                        'extendedAddress', 'locality', 'postalCode', 'region',
                        'streetAddress'],
                ],
            ]],
        ];
        return [
            'billingAddressId',
            'cardholderName',
            'cvv',
            'deviceData',
            'expirationDate',
            'expirationMonth',
            'expirationYear',
            'number',
            'paymentMethodNonce',
            'token',
            ['options' => $optionsSignature],
            ['billingAddress' => $billingAddressSignature]
        ];
    }

    public static function createSignature()
    {
        $signature = array_merge(self::baseSignature(), ['customerId', 'paypalRefreshToken', 'paypalVaultWithoutUpgrade']);
        return $signature;
    }

    public static function updateSignature()
    {
        $billingAddressSignature = AddressGateway::updateSignature();
        array_push($billingAddressSignature, [
            'options' => [
                'updateExisting'
            ]
        ]);
        $signature = array_merge(self::baseSignature(), [
            'deviceSessionId',
            'venmoSdkPaymentMethodCode',
            'fraudMerchantId',
            ['billingAddress' => $billingAddressSignature]
        ]);
        return $signature;
    }

    private static function deleteSignature()
    {
        return ['revokeAllGrants'];
    }

    /**
     * sends the create request to the gateway
     *
     * @ignore
     * @param string $subPath
     * @param array $params
     * @return mixed
     */
    public function _doCreate($subPath, $params)
    {
        $fullPath = $this->_config->merchantPath() . $subPath;
        $response = $this->_http->post($fullPath, $params);

        return $this->_verifyGatewayResponse($response);
    }

    /**
     * sends the update request to the gateway
     *
     * @ignore
     * @param string $subPath
     * @param array $params
     * @return mixed
     */
    public function _doUpdate($subPath, $params)
    {
        $fullPath = $this->_config->merchantPath() . $subPath;
        $response = $this->_http->put($fullPath, $params);

        return $this->_verifyGatewayResponse($response);
    }


    /**
     * sends the delete request to the gateway
     *
     * @ignore
     * @param string $subPath
     * @return mixed
     */
    public function _doDelete($subPath)
    {
        $fullPath = $this->_config->merchantPath() . $subPath;
        $this->_http->delete($fullPath);
        return new Result\Successful();
    }

    /**
     * generic method for validating incoming gateway responses
     *
     * creates a new CreditCard or PayPalAccount object
     * and encapsulates it inside a Result\Successful object, or
     * encapsulates a Errors object inside a Result\Error
     * alternatively, throws an Unexpected exception if the response is invalid.
     *
     * @ignore
     * @param array $response gateway response values
     * @return Result\Successful|Result\Error
     * @throws Exception\Unexpected
     */
    private function _verifyGatewayResponse($response)
    {
        if (isset($response['creditCard'])) {
            return new Result\Successful(
                CreditCard::factory($response['creditCard']),
                'paymentMethod'
            );
        } else if (isset($response['paypalAccount'])) {
            return new Result\Successful(
                PayPalAccount::factory($response['paypalAccount']),
                "paymentMethod"
            );
        } else if (isset($response['coinbaseAccount'])) {
            return new Result\Successful(
                CoinbaseAccount::factory($response['coinbaseAccount']),
                "paymentMethod"
            );
        } else if (isset($response['applePayCard'])) {
            return new Result\Successful(
                ApplePayCard::factory($response['applePayCard']),
                "paymentMethod"
            );
        } else if (isset($response['androidPayCard'])) {
            return new Result\Successful(
                AndroidPayCard::factory($response['androidPayCard']),
                "paymentMethod"
            );
        } else if (isset($response['amexExpressCheckoutCard'])) {
            return new Result\Successful(
                AmexExpressCheckoutCard::factory($response['amexExpressCheckoutCard']),
                "paymentMethod"
            );
        } else if (isset($response['usBankAccount'])) {
            return new Result\Successful(
                UsBankAccount::factory($response['usBankAccount']),
                "paymentMethod"
            );
        } else if (isset($response['venmoAccount'])) {
            return new Result\Successful(
                VenmoAccount::factory($response['venmoAccount']),
                "paymentMethod"
            );
        } else if (isset($response['visaCheckoutCard'])) {
            return new Result\Successful(
                VisaCheckoutCard::factory($response['visaCheckoutCard']),
                "paymentMethod"
            );
        } else if (isset($response['masterpassCard'])) {
            return new Result\Successful(
                MasterpassCard::factory($response['masterpassCard']),
                "paymentMethod"
            );
        } else if (isset($response['samsungPayCard'])) {
            return new Result\Successful(
                MasterpassCard::factory($response['samsungPayCard']),
                "paymentMethod"
            );
        } else if (isset($response['paymentMethodNonce'])) {
            return new Result\Successful(
                PaymentMethodNonce::factory($response['paymentMethodNonce']),
                "paymentMethodNonce"
            );
        } else if (isset($response['apiErrorResponse'])) {
            return new Result\Error($response['apiErrorResponse']);
        } else if (is_array($response)) {
            return new Result\Successful(
                UnknownPaymentMethod::factory($response),
                "paymentMethod"
            );
        } else {
            throw new Exception\Unexpected(
            'Expected payment method or apiErrorResponse'
            );
        }
    }

    /**
     * verifies that a valid payment method identifier is being used
     * @ignore
     * @param string $identifier
     * @param Optional $string $identifierType type of identifier supplied, default 'token'
     * @throws InvalidArgumentException
     */
    private function _validateId($identifier = null, $identifierType = 'token')
    {
        if (empty($identifier)) {
           throw new InvalidArgumentException(
                   'expected payment method id to be set'
                   );
        }
        if (!preg_match('/^[0-9A-Za-z_-]+$/', $identifier)) {
            throw new InvalidArgumentException(
                    $identifier . ' is an invalid payment method ' . $identifierType . '.'
                    );
        }
    }
}
class_alias('Braintree\PaymentMethodGateway', 'Braintree_PaymentMethodGateway');
