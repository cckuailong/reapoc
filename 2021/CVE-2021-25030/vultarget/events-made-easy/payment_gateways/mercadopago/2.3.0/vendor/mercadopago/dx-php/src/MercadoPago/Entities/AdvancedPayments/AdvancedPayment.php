<?php
namespace MercadoPago\AdvancedPayments;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;
use MercadoPago\Entity;

/**
 * Advanced Payment class
 * @link https://www.mercadopago.com/developers/en/reference/advanced_payments/_advanced_payments_id_search/get/ Click here for more infos
 * 
 * @RestMethod(resource="/v1/advanced_payments", method="create")
 * @RestMethod(resource="/v1/advanced_payments/:id", method="read")
 * @RestMethod(resource="/v1/advanced_payments/search", method="search")
 * @RestMethod(resource="/v1/advanced_payments/:id", method="update")
 * @RestMethod(resource="/v1/advanced_payments/:id/refunds", method="refund")
 */
class AdvancedPayment extends Entity
{

    /**
     * id
     * @var int
     * @Attribute()
     */
    protected $id;

    /**
     * application_id
     * @var int
     * @Attribute()
     */
    protected $application_id;

    /**
     * payments
     * @var array
     * @Attribute()
     */
    protected $payments;

    /**
     * disbursements
     * @var array
     * @Attribute()
     */
    protected $disbursements;

    /**
     * payer
     * @var object
     * @Attribute()
     */
    protected $payer;

    /**
     * external_reference
     * @var string
     * @Attribute()
     */
    protected $external_reference;

    /**
     * description
     * @var string
     * @Attribute()
     */
    protected $description;

    /**
     * binary_mode
     * @var boolean
     * @Attribute()
     */
    protected $binary_mode;

    /**
     * status
     * @var string
     * @Attribute()
     */
    protected $status;

    /**
     * capture
     * @var boolean
     * @Attribute()
     */
    protected $capture;


    /**
     * cancel
     * @return bool|mixed
     * @throws \Exception
     */
    public function cancel() {
        $this->status = 'cancelled';

        return $this->update();
    }


    /**
     * capture
     * @return bool|mixed
     * @throws \Exception
     */
    public function capture()
    {
        $this->capture = true;

        return $this->update();
    }


    /**
     * refund
     * @param int $amount
     * @return bool
     * @throws \Exception
     */
    public function refund($amount = 0){
        $refund = new Refund(["advanced_payment_id" => $this->id]);
        if ($amount > 0){
            $refund->amount = $amount;
        }

        if ($refund->save()){
            $advanced_payment = self::get($this->id);
            $this->_fillFromArray($this, $advanced_payment->toArray());
            return true;
        }else{
            $this->error = $refund->error;
            return false;
        }
    }


    /**
     * refundDisbursement
     * @param $disbursement_id
     * @param int $amount
     * @return bool
     * @throws \Exception
     */
    public function refundDisbursement($disbursement_id, $amount = 0){
        $refund = new DisbursementRefund(["advanced_payment_id" => $this->id, "disbursement_id" => $disbursement_id]);
        if ($amount > 0){
            $refund->amount = $amount;
        }

        if ($refund->save()){
            $advanced_payment = self::get($this->id);
            $this->_fillFromArray($this, $advanced_payment->toArray());
            return true;
        }else{
            $this->error = $refund->error;
            return false;
        }
    }
}