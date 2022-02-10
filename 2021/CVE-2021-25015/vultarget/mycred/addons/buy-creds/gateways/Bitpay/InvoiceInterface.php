<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * Invoice
 *
 * @package Bitpay
 */
interface InvoiceInterface
{
    /**
     * An invoice starts in this state.  When in this state and only in this state, payments
     * to the associated bitcoin address are credited to the invoice.  If an invoice has
     * received a partial payment, it will still reflect a status of new to the merchant
     * (from a merchant system perspective, an invoice is either paid or not paid, partial
     * payments and over payments are handled by bitpay.com by either refunding the
     * customer or applying the funds to a new invoice.
     */
    const STATUS_NEW = 'new';

    /**
     * As soon as full payment (or over payment) is received, an invoice goes into the
     * paid status.
     */
    const STATUS_PAID = 'paid';

    /**
     * The transaction speed preference of an invoice determines when an invoice is
     * confirmed.  For the high speed setting, it will be confirmed as soon as full
     * payment is received on the bitcoin network (note, the invoice will go from a status
     * of new to confirmed, bypassing the paid status).  For the medium speed setting,
     * the invoice is confirmed after the payment transaction(s) have been confirmed by
     * 1 block on the bitcoin network.  For the low speed setting, 6 blocks on the bitcoin
     * network are required.  Invoices are considered complete after 6 blocks on the
     * bitcoin network, therefore an invoice will go from a paid status directly to a
     * complete status if the transaction speed is set to low.
     */
    const STATUS_CONFIRMED = 'confirmed';

    /**
     * When an invoice is complete, it means that BitPay.com has credited the
     * merchant’s account for the invoice.  Currently, 6 confirmation blocks on the
     * bitcoin network are required for an invoice to be complete.  Note, in the future (for
     * qualified payers), invoices may move to a complete status immediately upon
     * payment, in which case the invoice will move directly from a new status to a
     * complete status.
     */
    const STATUS_COMPLETE = 'complete';

    /**
     * An expired invoice is one where payment was not received and the 15 minute
     * payment window has elapsed.
     */
    const STATUS_EXPIRED = 'expired';

    /**
     * An invoice is considered invalid when it was paid, but payment was not confirmed
     * within 1 hour after receipt.  It is possible that some transactions on the bitcoin
     * network can take longer than 1 hour to be included in a block.  In such
     * circumstances, once payment is confirmed, BitPay.com will make arrangements
     * with the merchant regarding the funds (which can either be credited to the
     * merchant account on another invoice, or returned to the buyer).
     */
    const STATUS_INVALID = 'invalid';

    /**
     * Code comment for each transaction speed
     */
    const TRANSACTION_SPEED_HIGH   = 'high';
    const TRANSACTION_SPEED_MEDIUM = 'medium';
    const TRANSACTION_SPEED_LOW    = 'low';

    /**
     * This is the amount that is required to be collected from the buyer. Note, if this is
     * specified in a currency other than BTC, the price will be converted into BTC at
     * market exchange rates to determine the amount collected from the buyer.
     *
     * @return string
     */
    public function getPrice();

    /**
     * This is the currency code set for the price setting.  The pricing currencies
     * currently supported are USD, EUR, BTC, and all of the codes listed on this page:
     * https://bitpay.com/bitcoin­exchange­rates
     *
     * @return CurrencyInterface
     */
    public function getCurrency();

    /**
     * @return ItemInterface
     */
    public function getItem();

    /**
     * @return BuyerInterface
     */
    public function getBuyer();

    /**
     * default value: set in your https://bitpay.com/order­settings, the default value set in
     * your merchant dashboard is “medium”.
     *
     * ● “high”: An invoice is considered to be "confirmed" immediately upon
     *   receipt of payment.
     * ● “medium”: An invoice is considered to be "confirmed" after 1 block
     *   confirmation (~10 minutes).
     * ● “low”: An invoice is considered to be "confirmed" after 6 block
     *   confirmations (~1 hour).
     *
     * NOTE: Orders are posted to your Account Summary after 6 block confirmations
     * regardless of this setting.
     *
     * @return string
     */
    public function getTransactionSpeed();

    /**
     * Bitpay.com will send an email to this email address when the invoice status
     * changes.
     *
     * @return string
     */
    public function getNotificationEmail();

    /**
     * A URL to send status update messages to your server (this must be an https
     * URL, unencrypted http URLs or any other type of URL is not supported).
     * Bitpay.com will send a POST request with a JSON encoding of the invoice to
     * this URL when the invoice status changes.
     *
     * @return string
     */
    public function getNotificationUrl();

    /**
     * This is the URL for a return link that is displayed on the receipt, to return the
     * shopper back to your website after a successful purchase. This could be a page
     * specific to the order, or to their account.
     *
     * @return string
     */
    public function getRedirectUrl();

    /**
     * A passthru variable provided by the merchant and designed to be used by the
     * merchant to correlate the invoice with an order or other object in their system.
     * Maximum string length is 100 characters.
     *
     * @return array|object
     */
    public function getPosData();

    /**
     * The current invoice status. The possible states are described earlier in this
     * document.
     *
     * @return string
     */
    public function getStatus();

    /**
     * default value: true
     * ● true: Notifications will be sent on every status change.
     * ● false: Notifications are only sent when an invoice is confirmed (according
     *   to the “transactionSpeed” setting).
     *
     * @return boolean
     */
    public function isFullNotifications();

    /**
     * default value: false
     * ● true: Notifications will also be sent for expired invoices and refunds.
     * ● false: Notifications will not be sent for expired invoices and refunds
     *
     * @return boolean
     */
    public function isExtendedNotifications();

    /**
     * The unique id of the invoice assigned by bitpay.com
     *
     * @return string
     */
    public function getId();

    /**
     * An https URL where the invoice can be viewed.
     *
     * @return string
     */
    public function getUrl();

    /**
     * The amount of bitcoins being requested for payment of this invoice (same as the
     * price if the merchant set the price in BTC).
     *
     * @return string
     */
    public function getBtcPrice();

    /**
     * The time the invoice was created in milliseconds since midnight January 1,
     * 1970. Time format is “2014­01­01T19:01:01.123Z”.
     *
     * @return DateTime
     */
    public function getInvoiceTime();

    /**
     * The time at which the invoice expires and no further payment will be accepted (in
     * milliseconds since midnight January 1, 1970). Currently, all invoices are valid for
     * 15 minutes. Time format is “2014­01­01T19:01:01.123Z”.
     *
     * @return DateTime
     */
    public function getExpirationTime();

    /**
     * The current time on the BitPay.com system (by subtracting the current time from
     * the expiration time, the amount of time remaining for payment can be
     * determined). Time format is “2014­01­01T19:01:01.123Z”.
     *
     * @return DateTime
     */
    public function getCurrentTime();

    /**
     * Used to display your public order number to the buyer on the BitPay invoice. In
     * the merchant Account Summary page, this value is used to identify the ledger
     * entry. Maximum string length is 100 characters.
     *
     * @return string
     */
    public function getOrderId();

    /**
     * Used to display an item description to the buyer. Maximum string length is 100
     * characters.
     *
     * @deprecated
     * @return string
     */
    public function getItemDesc();

    /**
     * Used to display an item SKU code or part number to the buyer. Maximum string
     * length is 100 characters.
     *
     * @deprecated
     * @return string
     */
    public function getItemCode();

    /**
     * default value: false
     * ● true: Indicates a physical item will be shipped (or picked up)
     * ● false: Indicates that nothing is to be shipped for this order
     *
     * @deprecated
     * @return boolean
     */
    public function isPhysical();

    /**
     * These fields are used for display purposes only and will be shown on the invoice
     * if provided. Maximum string length of each field is 100 characters.
     *
     * @deprecated
     * @return string
     */
    public function getBuyerName();

    /**
     * These fields are used for display purposes only and will be shown on the invoice
     * if provided. Maximum string length of each field is 100 characters.
     *
     * @deprecated
     * @return string
     */
    public function getBuyerAddress1();

    /**
     * These fields are used for display purposes only and will be shown on the invoice
     * if provided. Maximum string length of each field is 100 characters.
     *
     * @deprecated
     * @return string
     */
    public function getBuyerAddress2();

    /**
     * These fields are used for display purposes only and will be shown on the invoice
     * if provided. Maximum string length of each field is 100 characters.
     *
     * @deprecated
     * @return string
     */
    public function getBuyerCity();

    /**
     * These fields are used for display purposes only and will be shown on the invoice
     * if provided. Maximum string length of each field is 100 characters.
     *
     * @deprecated
     * @return string
     */
    public function getBuyerState();

    /**
     * These fields are used for display purposes only and will be shown on the invoice
     * if provided. Maximum string length of each field is 100 characters.
     *
     * @deprecated
     * @return string
     */
    public function getBuyerZip();

    /**
     * These fields are used for display purposes only and will be shown on the invoice
     * if provided. Maximum string length of each field is 100 characters.
     *
     * @deprecated
     * @return string
     */
    public function getBuyerCountry();

    /**
     * These fields are used for display purposes only and will be shown on the invoice
     * if provided. Maximum string length of each field is 100 characters.
     *
     * @deprecated
     * @return string
     */
    public function getBuyerEmail();

    /**
     * These fields are used for display purposes only and will be shown on the invoice
     * if provided. Maximum string length of each field is 100 characters.
     *
     * @deprecated
     * @return string
     */
    public function getBuyerPhone();

    /**
     */
    public function getExceptionStatus();

    /**
     */
    public function getBtcPaid();

    /**
     */
    public function getRate();

    /**
     */
    public function getToken();

    /**
     * An array containing all bitcoin addresses linked to the invoice. 
     * Only filled when doing a getInvoice using the Merchant facade.
     * The array contains
     *  [refundAddress] => Array
     *       [type] => string (e.g. "PaymentProtocol")
     *       [date] => datetime string
     *
     * @return array|object
     */
    public function getRefundAddresses();
}
