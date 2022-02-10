<?php
   
  # Crear un boton de pago a partir de una preferencia con atributos requeridos minimos

  require_once dirname(__FILE__).'/../../index.php';
             
  $config->set('ACCESS_TOKEN', 'ACCESS_TOKEN');
  
  $preference = new MercadoPago\Preference();
  
  # Building an item
  
  $item = new MercadoPago\Item();
  $item->id = "00001";
  $item->title = "item"; 
  $item->quantity = 1;
  $item->unit_price = 100;
  
  $preference->items = array($item);
  
  $preference->save(); # Save the preference and send the HTTP Request to create
  
  # Return the HTML code for button
  
  echo "<a href='$preference->sandbox_init_point'> Pagar </a>";
  
?>