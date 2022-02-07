<?php

namespace Eher\OAuth;

class Consumer {
  public $key;
  public $secret;

  function __construct($key, $secret, $callback_url=NULL) {
    $this->key = $key;
    $this->secret = $secret;
    $this->callback_url = $callback_url;
  }

  function __toString() {
    return "Consumer[key=$this->key,secret=$this->secret]";
  }
}

