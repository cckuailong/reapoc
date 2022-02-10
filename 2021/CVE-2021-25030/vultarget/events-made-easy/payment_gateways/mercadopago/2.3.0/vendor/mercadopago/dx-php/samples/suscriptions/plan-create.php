<?php
  
  require_once dirname(__FILE__) . '/../../index.php';

  $config->set('ACCESS_TOKEN', 'ACCESS_TOKEN');
  
  $plan = new MercadoPago\Plan();

  $plan->description = "Monthly premium package";
  $plan->auto_recurring = array(
    "frequency" => 1,
    "frequency_type" => "months",
    "transaction_amount" => 200
  );

  $plan->save();

  echo var_dump ($plan);
  
?>