<?php

interface AWeberOAuthAdapter {

    public function request($method, $uri, $data = array(), $options = array(), $headers = array());
    public function getRequestToken($callbackUrl=false);

}


?>
