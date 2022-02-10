<?php
  
  require_once dirname(__FILE__) . '/../../index.php';

  $config->set('ACCESS_TOKEN', 'ACCESS_TOKEN');


  # Create a Plan
  require_once dirname(__FILE__) . '/plan-create.php';
  
  $subscription = new MercadoPago\Subscription();
  
  $subscription->plan_id = $plan->id;
  
  $subscription->payer = array("id" => "customer_id");
  
  $subscription->save();
  
  
?>