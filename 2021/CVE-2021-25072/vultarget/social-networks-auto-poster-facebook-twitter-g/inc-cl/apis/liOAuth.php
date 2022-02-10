<?php
require_once("OAuth.php");

class nsx_LinkedIn {
  public $base_url = "http://api.linkedin.com";
  public $secure_base_url = "https://api.linkedin.com";
  public $oauth_callback = "oob";
  public $consumer;
  public $request_token;
  public $access_token;
  public $oauth_verifier;
  public $signature_method;
  public $request_token_path;
  public $access_token_path;
  public $authorize_path;
  public $debug = false;
  public $http_code;
  
  function __construct($consumer_key, $consumer_secret, $oauth_callback = NULL) {
    
    if($oauth_callback) {
      $this->oauth_callback = $oauth_callback;
    }
    
    $this->consumer = new nsx_trOAuthConsumer($consumer_key, $consumer_secret, $this->oauth_callback);
    $this->signature_method = new nsx_trOAuthSignatureMethod_HMAC_SHA1();
    $this->request_token_path = $this->secure_base_url . "/uas/oauth/requestToken?scope=r_basicprofile+r_emailaddress+w_share";
    $this->access_token_path = $this->secure_base_url . "/uas/oauth/accessToken";
    $this->authorize_path = $this->secure_base_url . "/uas/oauth/authorize";
    
  }

  function getRequestToken() {
    $consumer = $this->consumer;
    $request = nsx_trOAuthRequest::from_consumer_and_token($consumer, NULL, "GET", $this->request_token_path);
    $request->set_parameter("oauth_callback", $this->oauth_callback);
    $request->sign_request($this->signature_method, $consumer, NULL); // prr($request); die();
    $headers = Array();
    $url = $request->to_url(); // echo "^^^^^";  prr($url); 
    $response = $this->httpRequest($url, $headers, "GET"); //prr($response); 
    if ($response!='') $this->http_code = 200;    
    parse_str($response, $response_params); //prr($response_params); echo "!!!!";
    if (is_array($response_params) && !empty($response_params['oauth_problem'])) return print_r($response, true);
    $this->request_token = new nsx_trOAuthConsumer($response_params['oauth_token'], $response_params['oauth_token_secret'], 1); return $this->request_token;
  }

  function generateAuthorizeUrl() {
    $consumer = $this->consumer;
    $request_token = $this->request_token;
    return $this->authorize_path . "?oauth_token=" . $request_token->key;
  }

  function getAccessToken($oauth_verifier) {
    $request = nsx_trOAuthRequest::from_consumer_and_token($this->consumer, $this->request_token, "GET", $this->access_token_path);
    $request->set_parameter("oauth_verifier", $oauth_verifier);
    $request->sign_request($this->signature_method, $this->consumer, $this->request_token);
    $headers = Array();
    $url = $request->to_url(); // echo "==========";
    $response = $this->httpRequest($url, $headers, "GET"); //prr($request);
    parse_str($response, $response_params); // prr($response_params);
    if($this->debug) {
      echo $response . "\n";
    }
    $this->access_token = new nsx_trOAuthConsumer($response_params['oauth_token'], $response_params['oauth_token_secret'], 1);
  }
  
  function getProfile($resource = "~") {
    $profile_url = $this->base_url . "/v1/people/" . $resource;
    $request = nsx_trOAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "GET", $profile_url);
    $request->sign_request($this->signature_method, $this->consumer, $this->access_token); // prr($request); prr($profile_url); die();
    $auth_header = $request->to_header("https://api.linkedin.com"); # this is the realm
    # This PHP library doesn't generate the header correctly when a realm is not specified.
    # Make sure there is a space and not a comma after OAuth
    // $auth_header = preg_replace("/Authorization\: OAuth\,/", "Authorization: OAuth ", $auth_header);
    // # Make sure there is a space between OAuth attribute
    // $auth_header = preg_replace('/\"\,/', '", ', $auth_header);
    if ($this->debug) {
      echo $auth_header;
    }
    // $response will now hold the XML document
    $response = $this->httpRequest($profile_url, $auth_header, "GET");
    return $response;
  }

  function postShare($msg, $title='', $url='', $imgURL='', $dsc='') { $status_url = $this->base_url . "/v1/people/~/shares?format=json";  
    $dsc =  nxs_decodeEntitiesFull(strip_tags($dsc));  $msg = strip_tags(nxs_decodeEntitiesFull($msg));  $title =  nxs_decodeEntitiesFull(strip_tags($title));    
    $request = nsx_trOAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "POST", $status_url);
    $request->sign_request($this->signature_method, $this->consumer, $this->access_token);
    $auth_header = $request->to_header("https://api.linkedin.com"); 
    $toPost = array('comment'=>htmlspecialchars($msg, ENT_NOQUOTES, "UTF-8"), 'visibility'=>array('code'=>'anyone')); 
    if (!empty($url)) $toPost['content'] = array('submitted-url'=>$url, 'title'=>htmlspecialchars($title, ENT_NOQUOTES, "UTF-8"), 'description'=>htmlspecialchars($dsc, ENT_NOQUOTES, "UTF-8"));
    if (!empty($imgURL)) $toPost['content']['submitted-image-url'] = $imgURL;  
    $toPost = json_encode($toPost); $hdrsArr['Content-Type']='application/json'; $hdrsArr['x-li-format']='json';    
    $auth_header .=  "\n".'Content-Type: application/json'."\n".'x-li-format: json';
    //if ($debug) echo $auth_header . "\n"; 
    //prr($toPost);
    $response = $this->httpRequest($status_url, $auth_header, "POST", $toPost); $response = json_decode($response, true);
    return $response;
  }
  
    function setStatus($status) {
    $status_url = $this->base_url . "/v1/people/~/current-status";
    //echo "Setting status...\n";
    $xml = "<current-status>" . htmlspecialchars($status, ENT_NOQUOTES, "UTF-8") . "</current-status>";
    //echo $xml . "\n";
    $request = nsx_trOAuthRequest::from_consumer_and_token($this->consumer, $this->access_token, "PUT", $status_url);
    $request->sign_request($this->signature_method, $this->consumer, $this->access_token);
    $auth_header = $request->to_header("https://api.linkedin.com");
    if ($this->debug) {
      echo $auth_header . "\n";
    }
    $response = $this->httpRequest($status_url, $auth_header, "PUT", $xml); // prr($response);
    return $response;
  }
   
  
  function httpRequest($url, $auth_header, $method, $body = NULL) { // $this->debug = true; //if (!is_array($auth_header)) $auth_header = array($auth_header);
    if (!is_array($auth_header)) $auth_header = array($auth_header); 
    if (!$method) $method = "GET"; $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $auth_header); // Set the headers.

    if ($body) { $auth_header[] = "Content-Type: text/xml;charset=utf-8";
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $auth_header);   
    }
    global $nxs_skipSSLCheck; if ($nxs_skipSSLCheck===true) curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($curl); $errmsg = curl_error($curl); //prr($data);// die();
    
    //## NextScripts Fix
    if (curl_errno($curl) == 60 || stripos($errmsg, 'SSL')!==false) {  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); $data = curl_exec($curl);}
    if (curl_errno($curl) > 0) { $err = curl_errno($curl); $errmsg = curl_error($curl); prr($errmsg); prr($err);}    
    //## /NextScripts Fix    
    $header = curl_getinfo($curl); curl_close($curl);// prr($header);

    if ($this->debug) echo $data . "\n";    
        if (trim($data)=='' && ($header['http_code']=='201' || $header['http_code']=='200' || $header['http_code']=='202')) $data = '201';
    return $data; 
  }

}