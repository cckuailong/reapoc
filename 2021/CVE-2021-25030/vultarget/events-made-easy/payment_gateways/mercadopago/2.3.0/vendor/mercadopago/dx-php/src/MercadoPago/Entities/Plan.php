<?php
/**
 * Plan class file
 */
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;

/**
 * Plan class
 * @RestMethod(resource="/v1/plans/:id", method="read") 
 * @RestMethod(resource="/v1/plans/", method="create")
 * @RestMethod(resource="/v1/plans/:id", method="update")
 */

class Plan extends Entity
{
  /**
   * id
   * @Attribute()
   * @var string
   */
  protected $id;
  
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
   * auto_recurring
   * @Attribute()
   * @var boolean
   */
  protected $auto_recurring;
  
  /**
   * live_mode
   * @Attribute()
   * @var boolean
   */
  protected $live_mode;
  
  /**
   * setup_fee
   * @Attribute()
   * @var float
   */
  protected $setup_fee;
  
  /**
   * metadata
   * @Attribute()
   * @var object
   */
  protected $metadata;

}