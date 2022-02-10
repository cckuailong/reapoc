<?php

function eme_generate_worldpay_signature($md5_secret,$params_arr,$instId,$cartId,$currency,$amount) {
    $defaults = [
    'instId' => $instId,
    'cartId' => $cartId,
    'currency' => $currency,
    'amount' => $amount
    ];
    $parameters = array_intersect_key($defaults,array_flip($params_arr));
    return md5((string) $md5_secret.':'.implode(':', $parameters));
}

?>
