<?php

// Make sure plugin remains secure if called directly
if( !defined( 'ABSPATH' ) ) {
  if( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
  die(__('ERROR: This plugin requires WordPress and will not function if called directly.','newstatpress'));
}


/**
 * API: WP Version
 *
 * Return the current version of wordpress as json/html
 *
 * @param typ the type of result (Json/Html)
 * @return the result
 */
function nsp_ApiWpVersion($typ) {
  global $_NEWSTATPRESS;

  $resultJ=array(
    'wpversion' => get_bloginfo('version')
  );

  if ($typ=="JSON") return $resultJ;         // avoid to calculte HTML if not necessary
  
  $resultH="<div>".$resultJ[$var]."</div>";  
  return $resultH;
}
?>
