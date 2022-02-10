<?php
/**
 * Invoice class file
 */
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;

/**
 * Invoice class
 * @RestMethod(resource="/v1/invoices/:id", method="read")
 */

class Invoice extends Entity
{
  
  /**
   * id
   * @Attribute()
   * @var string
   */
  protected $id;
  
  /**
   * subscription_id
   * @Attribute()
   * @var string
   */
  protected $subscription_id;
  
  /**
   * plan_id
   * @Attribute()
   * @var string
   */
  protected $plan_id;
  
  /**
   * payer
   * @Attribute()
   * @var object
   */
  protected $payer;
  
  /**
   * application_fee
   * @Attribute()
   * @var float
   */
  protected $application_fee;
  
  /**
   * status
   * @Attribute()
   * @var string
   */
  protected $status;
  
  /**
   * description
   * @Attribute()
   * @var string
   */
  protected $description;
  
  /**
   * external_reference
   * @Attribute()
   * @var string
   */
  protected $external_reference;
  
  /**
   * date_created
   * @Attribute()
   * @var string
   */
  protected $date_created;
  
  /**
   * last_modified
   * @Attribute()
   * @var string
   */
  protected $last_modified;
  
  /**
   * live_mode
   * @Attribute()
   * @var boolean
   */
  protected $live_mode;
  
  
  /**
   * metadata
   * @Attribute()
   * @var object
   */
  protected $metadata;
  
  /**
   * payments
   * @Attribute()
   * @var array
   */
  protected $payments;
  
  /**
   * debit_date
   * @Attribute()
   * @var string
   */
  protected $debit_date;
  
  /**
   * next_payment_date
   * @Attribute()
   * @var string
   */
  protected $next_payment_date;

}
