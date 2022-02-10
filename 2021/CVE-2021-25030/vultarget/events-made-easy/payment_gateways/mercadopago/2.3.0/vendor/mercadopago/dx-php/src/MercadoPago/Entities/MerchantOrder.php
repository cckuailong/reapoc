<?php
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute; 

/**
 * This class will allow you to create and manage your orders. You can attach one or more payments in your merchant order.
 * @link https://www.mercadopago.com/developers/en/reference/merchant_orders/_merchant_orders_search/get/ Click here for more infos
 * 
 * @RestMethod(resource="/merchant_orders/:id", method="read") 
 * @RestMethod(resource="/merchant_orders/", method="create")
 * @RestMethod(resource="/merchant_orders/:id", method="update")
 */

class MerchantOrder extends Entity
{
  
  /**
   * id
   * @Attribute()
   * @var int
   */
  protected $id;
  
  /**
   * preferenceId
   * @Attribute()
   * @var string
   */
  protected $preferenceId;
  
  /**
   * dateCreated
   * @Attribute()
   * @var string
   */
  protected $dateCreated;
  
  /**
   * lastUpdated
   * @Attribute()
   * @var string
   */
  protected $lastUpdate;
  
  /**
   * applicationId
   * @Attribute
   * @var string
   */
  protected $applicationId;
  
  /**
   * status
   * @Attribute()
   * @var string
   */
  protected $status;
  
  /**
   * siteId
   * @Attribute()
   * @var string
   */
  protected $siteId;
  
  /**
   * payer
   * @Attribute()
   * @var object
   */
  protected $payer;
  
  /**
   * collector
   * @Attribute()
   * @var object
   */
  protected $collector;
  
  /**
   * sponsorId
   * @Attribute()
   * @var int
   */
  protected $sponsorId;
  
  /**
   * payments
   * @Attribute()
   * @var array
   */
  protected $payments;
  
  /**
   * paidAmount
   * @Attribute()
   * @var float
   */
  protected $paidAmount;
  
  /**
   * refundedAmount
   * @Attribute()
   * @var float
   */
  protected $refundedAmount;
  
  /**
   * shippingCost
   * @Attribute()
   * @var float
   */
  protected $shippingCost;
  
  /**
   * cancelled
   * @Attribute()
   * @var boolean
   */
  protected $cancelled;
  
  /**
   * items
   * @Attribute()
   * @var array
   */
  protected $items;
  
  /**
   * shipments
   * @Attribute()
   * @var array
   */
  protected $shipments;
  
  /**
   * notificationUrl
   * @Attribute()
   * @var string
   */
  protected $notificationUrl;
  
  /**
   * additionalInfo
   * @Attribute()
   * @var string
   */
  protected $additionalInfo;
  
  /**
   * externalReference
   * @Attribute()
   * @var string
   */
  protected $externalReference;
  
  /**
   * marketplace
   * @Attribute()
   * @var string
   */
  protected $marketplace;
  
  /**
   * totalAmount
   * @Attribute()
   * @var float
   */
  protected $totalAmount;
  
}
