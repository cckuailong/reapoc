<?php
  
  require_once dirname(__FILE__) . '/../../index.php';
  
  $config->set('ACCESS_TOKEN', 'ACCESS_TOKEN');

  # Create a Payment
  require_once dirname(__FILE__) . '/create.php';

  # Cancel the previous payment, only works from a pending or in_process status
  $payment->status = "canceled";
  $payment->update();
  
  echo $payment->status;
  echo $payment->status_detail;
  
?>