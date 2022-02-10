<?php
  
  require_once dirname(__FILE__) . '/../../index.php';

  $config->set('ACCESS_TOKEN', 'ACCESS_TOKEN');


  # Create a Plan
  require_once dirname(__FILE__) . '/subscribe-to-plan';
  
  $subscription->status = "paused";
  $subscription->update();
  
  
  
  
?>