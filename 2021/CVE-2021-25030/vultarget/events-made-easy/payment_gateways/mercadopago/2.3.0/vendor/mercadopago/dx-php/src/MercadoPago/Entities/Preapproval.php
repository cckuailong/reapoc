<?php
/**
 * Preapproval class file
 */
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;

/**
 * preapproval class
 * @RestMethod(resource="/preapproval/:id", method="read")
 * @RestMethod(resource="/preapproval/search", method="search")
 * @RestMethod(resource="/preapproval/", method="create")
 * @RestMethod(resource="/preapproval/:id", method="update")
 */

class Preapproval extends Entity
{
  
  /**
   * id
   * @Attribute()
   * @var int
   */
  protected $id;
  
  /**
   * payer_id
   * @Attribute()
   * @var int
   */
  protected $payer_id;
  
  /**
   * payer_email
   * @Attribute()
   * @var string
   */
  protected $payer_email;
  
  /**
   * back_url
   * @Attribute()
   * @var string
   */
  protected $back_url;
  
  /**
   * collector_id
   * @Attribute()
   * @var int
   */
  protected $collector_id;
  
  /**
   * application_id
   * @Attribute()
   * @var string
   */
  protected $application_id;
  
  /**
   * status
   * @Attribute()
   * @var string
   */
  protected $status;
  
  /**
   * auto_recurring
   * @Attribute()
   * @var boolean
   */
  protected $auto_recurring;
  
  /**
   * init_point
   * @Attribute()
   * @var string
   */
  protected $init_point;
  
  /**
   * sandbox_init_point
   * @Attribute()
   * @var string
   */
  protected $sandbox_init_point;
  
  /**
   * reason
   * @Attribute()
   * @var string
   */
  protected $reason;
  
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
   * preapproval_plan_id
   * @Attribute()
   * @var string
   */
  protected $preapproval_plan_id;
  
}
