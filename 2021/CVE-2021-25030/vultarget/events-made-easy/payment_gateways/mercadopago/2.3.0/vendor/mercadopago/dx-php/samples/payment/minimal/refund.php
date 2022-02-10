<?php
  
  require_once dirname(__FILE__) . '/../../index.php';
  
  # Create a Payment
  require_once dirname(__FILE__) . '/create.php';
  
  # Refunding
  $refund = new MercadoPago\Refund();
  $refund->amount   = $payment->total_paid_amount;
  $refund->metadata = "A total refund";
  $refund->save();
  
  echo $refund->id;
  echo "Refunds \n";

  var_dump($payment->refunds);
  
?>