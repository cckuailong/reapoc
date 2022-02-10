<?php
   
  # Crear un boton de pago a partir de una preferencia con atributos requeridos minimos

  require_once dirname(__FILE__).'/../../index.php';
  
  $config->set('ACCESS_TOKEN', 'ACCESS_TOKEN');
  
  $preference = new MercadoPago\Preference();
  
  # Building an item
  
  $item1 = new MercadoPago\Item();
  $item1->id = "00001";
  $item1->title = "item"; 
  $item1->quantity = 1;
  $item1->unit_price = 100;
  
  $item2 = new MercadoPago\Item();
  $item2->id = "00001";
  $item2->title = "item"; 
  $item2->quantity = 1;
  $item2->unit_price = 100;
  
  $preference->items = array($item1, $item2);
  
  $preference->payment_methods = array(
    "excluded_payment_types" => array(
      array("id" => "credit_card")
    ),
    "installments" => 12
  );
  
  $preference->external_reference = "A Custom External Reference";
  
  $preference->save(); # Save the preference and send the HTTP Request to create
  
  # Return the HTML code for button
  
  echo "<a href='$preference->sandbox_init_point'> Pagar </a>";
  
?>