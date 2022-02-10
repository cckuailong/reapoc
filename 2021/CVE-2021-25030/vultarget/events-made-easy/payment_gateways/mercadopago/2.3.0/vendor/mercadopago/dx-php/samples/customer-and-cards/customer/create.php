<?php

    require_once dirname(__FILE__).'/../../index.php';

    $config->set('ACCESS_TOKEN', 'ACCESS_TOKEN');

    $customer = new MercadoPago\Customer();
    $customer->email = "your.payer@email.com";
    $customer->save();

?>