<?php

namespace Paymill\Models\Request;

/**
 * Checksum Model
 *
 * A checksum validation is a simple method to nearly ensure the integrity of transferred data.
 * Basically we generate a hash out of the over given parameters and your private key.
 * If you send us a request with transaction data and the generated checksum, we can easily validate the data
 * because we know your private key and the used hash algorithm.
 * To make the checksum computation as easy as possible we provide this endpoint for you.
 * @tutorial https://paymill.com/de-de/dokumentation/referenz/api-referenz/#document-checksum
 */
class PaypalChecksum extends ChecksumBase
{
    /**
     * Different checksum actions which will enable different validations for
     * the input parameters.
     */
    const ACTION_PAYMENT = 'payment';
    const ACTION_TRANSACTION = 'transaction';

    /**
     * Checksum action
     *
     * @var string
     */
    private $checksumAction = null;

    /**
     * Shipping address
     *
     * @var array $shippingAddress
     */
    private $shippingAddress;

    /**
     * Billing address
     *
     * @var array $billingAddress
     */
    private $billingAddress;

    /**
     * Items
     *
     * @var array $items
     */
    private $items;

    /**
     * Shipping amount
     *
     * @var int $shipping_amount
     */
    private $shipping_amount;

    /**
     * Handling amount
     *
     * @var int $handling_amount
     */
    private $handling_amount;

    /**
     * Reusable payment
     *
     * @var bool $requireReusablePayment
     */
    private $requireReusablePayment;

    /**
     * Reusable payment description
     *
     * @var string $reusablePaymentDescription
     */
    private $reusablePaymentDescription;

    /**
     * Get checksum action
     *
     * @return string
     */
    public function getChecksumAction()
    {
        return $this->checksumAction;
    }

    /**
     * Set checksum action
     *
     * @param string $checksumAction Checksum action
     *
     * @return $this
     */
    public function setChecksumAction($checksumAction)
    {
        $this->checksumAction = $checksumAction;

        return $this;
    }

    /**
     * Get shipping address
     *
     * @return array
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Set shipping address
     *
     * @param array $shippingAddress Shipping address
     *
     * @return $this
     */
    public function setShippingAddress(array $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * Get billing address
     *
     * @return array
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set billing address
     *
     * @param array $billingAddress Billing address
     *
     * @return $this
     */
    public function setBillingAddress(array $billingAddress)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set items
     *
     * @param array $items Items
     *
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get shipping amount
     *
     * @return int
     */
    public function getShippingAmount()
    {
        return $this->shipping_amount;
    }

    /**
     * Set shipping_amount
     *
     * @param int $shipping_amount Shipping amount
     *
     * @return $this
     */
    public function setShippingAmount($shipping_amount)
    {
        $this->shipping_amount = $shipping_amount;

        return $this;
    }

    /**
     * Get handling amount
     *
     * @return int
     */
    public function getHandlingAmount()
    {
        return $this->handling_amount;
    }

    /**
     * Set handling amount
     *
     * @param int $handling_amount Handling amount
     *
     * @return $this
     */
    public function setHandlingAmount($handling_amount)
    {
        $this->handling_amount = $handling_amount;

        return $this;
    }

    /**
     * Get require reusable payment
     *
     * @return bool
     */
    public function getRequireReusablePayment()
    {
        return $this->requireReusablePayment;
    }

    /**
     * Set require reusable payment
     *
     * @param bool $requireReusablePayment Reusable payment
     *
     * @return $this
     */
    public function setRequireReusablePayment($requireReusablePayment)
    {
        $this->requireReusablePayment = $requireReusablePayment;

        return $this;
    }

    /**
     * Get reusable payment description
     *
     * @return string
     */
    public function getReusablePaymentDescription()
    {
        return $this->reusablePaymentDescription;
    }

    /**
     * Set reusable payment description
     *
     * @param string $reusablePaymentDescription Reusable payment description
     *
     * @return $this
     */
    public function setReusablePaymentDescription($reusablePaymentDescription)
    {
        $this->reusablePaymentDescription = $reusablePaymentDescription;

        return $this;
    }

    /**
     * Converts the model into an array to prepare method calls
     * @param string $method should be used for handling the required parameter
     * @return array
     */
    public function parameterize($method)
    {
        $parameterArray = parent::parameterize($method);

        if ('create' == $method) {
            if($this->getChecksumAction()) {
                $parameterArray['checksum_action'] = $this->getChecksumAction();
            }

            if($this->getShippingAddress()) {
                $parameterArray['shipping_address'] = $this->getShippingAddress();
            }

            if($this->getBillingAddress()) {
                $parameterArray['billing_address'] = $this->getBillingAddress();
            }

            if($this->getItems()) {
                $parameterArray['items'] = $this->getItems();
            }

            if($this->getShippingAmount()) {
                $parameterArray['shipping_amount'] = $this->getShippingAmount();
            }

            if($this->getHandlingAmount()) {
                $parameterArray['handling_amount'] = $this->getHandlingAmount();
            }

            if($this->getRequireReusablePayment()) {
                $parameterArray['require_reusable_payment'] = $this->getRequireReusablePayment();
            }

            if($this->getReusablePaymentDescription()) {
                $parameterArray['reusable_payment_description'] = $this->getReusablePaymentDescription();
            }

        }

        return $parameterArray;
    }
}
