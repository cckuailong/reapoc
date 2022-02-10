<?php
/**
 * Subscription class file
 */
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;

/**
 * Subscription class
 * @RestMethod(resource="/v1/subscriptions/:id", method="read") 
 * @RestMethod(resource="/v1/subscriptions/", method="create")
 * @RestMethod(resource="/v1/subscriptions/:id", method="update")
 */

class Subscription extends Entity
{
  
  /**
   * id
   * @Attribute()
   * @var string
   */
  protected $id;

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
   * @var \DateTime
   */
  protected $date_created;
  
  /**
   * last_modified
   * @Attribute()
   * @var \DateTime
   */
  protected $last_modified;
  
  /**
   * live_mode
   * @Attribute()
   * @var boolean
   */
  protected $live_mode;
  
  /**
   * start_date
   * @Attribute()
   * @var \DateTime
   */
  protected $start_date;
  
  /**
   * end_date
   * @Attribute()
   * @var \DateTime
   */
  protected $end_date;
  
  /**
   * metadata
   * @Attribute()
   * @var object
   */
  protected $metadata;
  
  /**
   * charge_detail
   * @Attribute()
   * @var object
   */
  protected $charges_detail;
  
  /**
   * setup_fee
   * @Attribute()
   * @var float
   */
  protected $setup_fee;

}
