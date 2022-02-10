<?php
/**
 
 */
namespace MercadoPago;
use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute; 

/**
 * This class provides the methods to access the API that will allow you to create your own payment experience on your website.
 *  
 * From basic to advanced configurations, you control the whole experience.
 *  
 * @link https://www.mercadopago.com/developers/en/guides/online-payments/checkout-api/introduction/ Click here for more infos
 *
 * @RestMethod(resource="/v1/payments", method="create")
 * @RestMethod(resource="/v1/payments/:id", method="read")
 * @RestMethod(resource="/v1/payments/search", method="search")
 * @RestMethod(resource="/v1/payments/:id", method="update")
 * @RestMethod(resource="/v1/payments/:id/refunds", method="refund")
 */
class Payment extends Entity
{

    /**
     * id
     * @var int
     * @Attribute(primaryKey = true)
     */
    protected $id;

    /**
     * acquirer
     * @var string
     * @Attribute()
     */
    protected $acquirer;

    /**
     * acquirer_reconciliation
     * @var string
     * @Attribute()
     */
    protected $acquirer_reconciliation;

    /**
     * site_id
     * @var string
     * @Attribute(idempotency = true)
     */
    protected $site_id;

    /**
     * sponsor_id
     * @var int
     * @Attribute()
     */
    protected $sponsor_id;

    /**
     * operation_type
     * @var string
     * @Attribute()
     */
    protected $operation_type;

    /**
     * order_id
     * @var int
     * @Attribute(idempotency = true)
     */
    protected $order_id;

    /**
     * order
     * @var int
     * @Attribute()
     */
    protected $order;

    /**
     * binary_mode
     * @var boolean
     * @Attribute()
     */
    protected $binary_mode;

    /**
     * external_reference
     * @var string
     * @Attribute()
     */
    protected $external_reference;

    /**
     * status
     * @var string
     * @Attribute()
     */
    protected $status;

    /**
     * status_detail
     * @var string
     * @Attribute()
     */
    protected $status_detail;

    /**
     * store_id
     * @var int
     * @Attribute()
     */
    protected $store_id;

    /**
     * taxes_amount
     * @var float
     * @Attribute()
     */
    protected $taxes_amount;

    /**
     * payment_type
     * @var string
     * @Attribute(type = "string")
     */
    protected $payment_type;

    /**
     * date_created
     * @var \DateTime
     * @Attribute()
     */
    protected $date_created;

    /**
     * last_modified
     * @var \DateTime
     * @Attribute()
     */
    protected $last_modified;

    /**
     * live_mode
     * @var boolean
     * @Attribute()
     */
    protected $live_mode;

    /**
     * date_last_update
     * @var \DateTime
     * @Attribute()
     */
    protected $date_last_updated;

    /**
     * date_of_expiration
     * @var \DateTime
     * @Attribute()
     */
    protected $date_of_expiration;

    /**
     * deduction_schema
     * @var string
     * @Attribute()
     */
    protected $deduction_schema;

    /**
     * date_approved
     * @var \DateTime
     * @Attribute()
     */
    protected $date_approved;

    /**
     * money_release_date
     * @var \DateTime
     * @Attribute()
     */
    protected $money_release_date;

    /**
     * money_release_schema
     * @var string
     * @Attribute()
     */
    protected $money_release_schema;

    /**
     * currency_id
     * @var string
     * @Attribute()
     */
    protected $currency_id;

    /**
     * transaction_amount
     * @var float
     * @Attribute(type = "float")
     */
    protected $transaction_amount;

    /**
     * transaction_amount_refunded
     * @var float
     * @Attribute(type = "float")
     */
    protected $transaction_amount_refunded;

    /**
     * shipping_cost
     * @var float
     * @Attribute()
     */
    protected $shipping_cost;

    /**
     * total_paid_amount
     * @var float
     * @Attribute(idempotency = true)
     */
    protected $total_paid_amount;

    /**
     * finance_charge
     * @var float
     * @Attribute(type = "float")
     */
    protected $finance_charge;

    /**
     * net_received_amount
     * @var float
     * @Attribute()
     */
    protected $net_received_amount;

    /**
     * marketplace
     * @var string
     * @Attribute()
     */
    protected $marketplace;

    /**
     * marketplace_fee
     * @var float
     * @Attribute(type = "float")
     */
    protected $marketplace_fee;

    /**
     * reason
     * @var string
     * @Attribute()
     */
    protected $reason;

    /**
     * payer
     * @var object
     * @Attribute()
     */
    protected $payer;

    /**
     * collector
     * @var object
     * @Attribute()
     */
    protected $collector;

    /**
     * collector_id
     * @var int
     * @Attribute()
     */
    protected $collector_id;

    /**
     * counter_currency
     * @var string
     * @Attribute()
     */
    protected $counter_currency;

    /**
     * payment_method_id
     * @var string
     * @Attribute()
     */
    protected $payment_method_id;

    /**
     * payment_type_id
     * @var string
     * @Attribute()
     */
    protected $payment_type_id;

    /**
     * pos_id
     * @var string
     * @Attribute()
     */
    protected $pos_id;

    /**
     * transaction_details
     * @var object
     * @Attribute()
     */
    protected $transaction_details;

    /**
     * fee_details
     * @var object
     * @Attribute()
     */
    protected $fee_details;

    /**
     * differential_pricing_id
     * @var int
     * @Attribute()
     */
    protected $differential_pricing_id;

    /**
     * application_fee
     * @var float
     * @Attribute()
     */
    protected $application_fee;

    /**
     * authorization_code
     * @var string
     * @Attribute()
     */
    protected $authorization_code;

    /**
     * capture
     * @var boolean
     * @Attribute()
     */
    protected $capture;

    /**
     * captured
     * @var boolean
     * @Attribute()
     */
    protected $captured;

    /**
     * card
     * @var int
     * @Attribute()
     */
    protected $card;

    /**
     * call_for_authorize_id
     * @var string
     * @Attribute()
     */
    protected $call_for_authorize_id;

    /**
     * statement_descriptor
     * @var string
     * @Attribute()
     */
    protected $statement_descriptor;

    /**
     * refunds
     * @var object
     * @Attribute()
     */
    protected $refunds;

    /**
     * Shipping_amount
     * @var float
     * @Attribute()
     */
    protected $shipping_amount;

    /**
     * additional_info
     * @var array
     * @Attribute()
     */
    protected $additional_info;

    /**
     * campaign_id
     * @var string
     * @Attribute()
     */
    protected $campaign_id;

    /**
     * coupon_amount
     * @var float
     * @Attribute()
     */
    protected $coupon_amount;

    /**
     * installments
     * @var int
     * @Attribute(type = "int")
     */
    protected $installments;

    /**
     * token
     * @var string
     * @Attribute()
     */
    protected $token;

    /**
     * description
     * @var string
     * @Attribute()
     */
    protected $description;

    /**
     * notification_url
     * @var string
     * @Attribute()
     */
    protected $notification_url;

    /**
     * issuer_id
     * @var string
     * @Attribute()
     */
    protected $issuer_id;

    /**
     * processing_mode
     * @var string
     * @Attribute()
     */
    protected $processing_mode;

    /**
     * merchant_account_id
     * @var int
     * @Attribute()
     */
    protected $merchant_account_id;

    /**
     * merchant_number
     * @var int
     * @Attribute()
     */
    protected $merchant_number;

    /**
     * metadata
     * @var object
     * @Attribute()
     */
    protected $metadata;

    /**
     * callback_url
     * @var string
     * @Attribute()
     */
    protected $callback_url;

    /**
     * amount_refunded
     * @var float
     * @Attribute()
     */
    protected $amount_refunded;

    /**
     * coupon_code
     * @var string
     * @Attribute()
     */
    protected $coupon_code;

    /**
     * barcode
     * @var string
     * @Attribute()
     */
    protected $barcode;

    /**
     * marketplace_owner
     * @var int
     * @Attribute()
     */
    protected $marketplace_owner;

    /**
     * integrator_id
     * @var string
     * @Attribute()
     */
    protected $integrator_id;

    /**
     * corporation_id
     * @var string
     * @Attribute()
     */
    protected $corporation_id;

    /**
     * platform_id
     * @var string
     * @Attribute()
     */
    protected $platform_id;

    /**
     * charges details
     * @var object
     * @Attribute()
     */
    protected $charges_details;

    /**
     * taxes
     * @Attribute(type = "array")
     * @var array
     */
    protected $taxes;

    /**
     * net_amount
     * @var float
     * @Attribute(type = "float")
     */
    protected $net_amount;

    /**
     * payer
     * @var object
     * @Attribute()
     */
    protected $point_of_interaction;

    /**
     * refund
     * @param int $amount
     * @return bool
     * @throws \Exception
     */
    public function refund($amount = 0){
        $refund = new Refund(["payment_id" => $this->id]);
        if ($amount > 0){
            $refund->amount = $amount;
        }

        if ($refund->save()){
            $payment = self::get($this->id);
            $this->_fillFromArray($this, $payment->toArray());
            return true;
        }else{
            $this->error = $refund->error;
            return false;
        }
    }

    /**
     * capture
     * @param int $amount
     * @return Payment
     * @throws \Exception
     */
    public function capture($amount = 0)
    {
        $this->capture = true;
        if ($amount > 0){
            $this->transaction_amount = $amount;
        }

        return $this->update();
    }
}
