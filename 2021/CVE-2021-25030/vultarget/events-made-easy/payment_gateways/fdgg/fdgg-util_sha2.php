<?php
function fdgg_createHash($str) {
   $hex_str="";
   for ($i = 0; $i < strlen($str); $i++){
      $hex_str.=dechex(ord($str[$i]));
   }
   return hash('sha512', $hex_str);
}
?>
