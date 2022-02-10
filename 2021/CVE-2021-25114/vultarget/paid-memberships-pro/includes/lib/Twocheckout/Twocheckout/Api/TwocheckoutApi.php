<?php

class Twocheckout_Api_Requester
{
    public $baseUrl;
    public $environment;
    private $user;
    private $pass;
    private $sid;
    private $privateKey;

	function __construct() {
        $this->user = Twocheckout::$username;
        $this->pass = Twocheckout::$password;
        $this->sid = Twocheckout::$sid;
        $this->baseUrl = Twocheckout::$baseUrl;
        $this->verifySSL = Twocheckout::$verifySSL;
        $this->privateKey = Twocheckout::$privateKey;
    }

	function doCall($urlSuffix, $data=array())
    {
        $url = $this->baseUrl . $urlSuffix;
        $ch = curl_init($url);
        if (isset($data['api'])) {
            unset( $data['api'] );
            $data['privateKey'] = $this->privateKey;
            $data['sellerId'] = $this->sid;
            $data = json_encode($data);
            $header = array("content-type:application/json","content-length:".strlen($data));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        } else {
            $header = array("Accept: application/json");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "{$this->user}:{$this->pass}");
        }
        if ($this->verifySSL == false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_USERAGENT, "2Checkout PHP/0.1.0%s");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $resp = curl_exec($ch);
        curl_close($ch);
        if ($resp === FALSE) {
            throw new Twocheckout_Error("cURL call failed", "403");
        } else {
            return utf8_encode($resp);
        }
	}

}
