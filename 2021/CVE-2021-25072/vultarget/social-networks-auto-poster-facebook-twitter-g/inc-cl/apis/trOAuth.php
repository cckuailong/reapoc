<?php

/*
 * Abraham Williams (abraham@abrah.am) http://abrah.am
 *
 * The first PHP Library to support OAuth for Tumblr's REST API.  (Originally for Twitter, modified for Tumblr by Lucas)
 */

/* Load OAuth lib. You can find it at http://oauth.net */
require_once('OAuth.php');

/**
 * Tumblr OAuth class
 */
class TumblrOAuth {
  /* Contains the last HTTP status code returned. */
  public $http_code;
  /* Contains the last API call. */
  public $url;
  /* Set up the API root URL. */
  public $host = "http://api.tumblr.com/v2/";
  /* Set timeout default. */
  public $timeout = 45;
  /* Set connect timeout. */
  public $connecttimeout = 45; 
  /* Verify SSL Cert. */
  public $ssl_verifypeer = FALSE;
  /* Respons format. */
  public $format = 'json';
  /* Decode returned json data. */
  public $decode_json = TRUE;
  /* Contains the last HTTP headers returned. */
  public $http_info;
  /* Set the useragnet. */
  public $useragent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; InfoPath.3; .NET4.0E; .NET CLR 1.1.4322)';
  /* Immediately retry the API call if the response was not successful. */
  //public $retry = TRUE;

  /**
   * Set API URLS
   */
  function accessTokenURL()  { return 'https://www.tumblr.com/oauth/access_token'; }
  function authenticateURL() { return 'https://www.tumblr.com/oauth/authorize'; }
  function authorizeURL()    { return 'https://www.tumblr.com/oauth/authorize'; }
  function requestTokenURL() { return 'https://www.tumblr.com/oauth/request_token'; }

  /**
   * Debug helpers
   */
  function lastStatusCode() { return $this->http_status; }
  function lastAPICall() { return $this->last_api_call; }

  /**
   * construct TumblrOAuth object
   */
  function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {
    $this->sha1_method = new nsx_trOAuthSignatureMethod_HMAC_SHA1();
    $this->consumer = new nsx_trOAuthConsumer($consumer_key, $consumer_secret);
    if (!empty($oauth_token) && !empty($oauth_token_secret)) {
      $this->token = new nsx_trOAuthConsumer($oauth_token, $oauth_token_secret);
    } else {
      $this->token = NULL;
    }
  }

  /**
   * Get a request_token from Tumblr
   *
   * @returns a key/value array containing oauth_token and oauth_token_secret
   */
  function getRequestToken($oauth_callback = NULL) {
    $parameters = array();
    if (!empty($oauth_callback)) {
      $parameters['oauth_callback'] = $oauth_callback;
    } 
    $request = $this->oAuthRequest($this->requestTokenURL(), 'GET', $parameters); 
    $token = nsx_trOAuthUtil::parse_parameters($request);
    $this->token = new nsx_trOAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   * Get the authorize URL
   *
   * @returns a string
   */
  function getAuthorizeURL($token, $sign_in_with_tumblr = TRUE) {
    if (is_array($token)) {
      $token = $token['oauth_token'];
    }
    if (empty($sign_in_with_tumblr)) {
      return $this->authorizeURL() . "?oauth_token={$token}";
    } else {
       return $this->authenticateURL() . "?oauth_token={$token}";
    }
  }

  /**
   * Exchange request token and secret for an access token and
   * secret, to sign API calls.
   *
   * @returns array("oauth_token" => "the-access-token",
   *                "oauth_token_secret" => "the-access-secret",
   *                "user_id" => "9436992",
   *                "screen_name" => "abraham")
   */
  function getAccessToken($oauth_verifier = FALSE) {
    $parameters = array();
    if (!empty($oauth_verifier)) {
      $parameters['oauth_verifier'] = $oauth_verifier;
    }
    $request = $this->oAuthRequest($this->accessTokenURL(), 'GET', $parameters);
    $token = nsx_trOAuthUtil::parse_parameters($request);
    $this->token = new nsx_trOAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   * One time exchange of username and password for access token and secret.
   *
   * @returns array("oauth_token" => "the-access-token",
   *                "oauth_token_secret" => "the-access-secret",
   *                "user_id" => "9436992",
   *                "screen_name" => "abraham",
   *                "x_auth_expires" => "0")
   */  
  function getXAuthToken($username, $password) {
    $parameters = array();
    $parameters['x_auth_username'] = $username;
    $parameters['x_auth_password'] = $password;
    $parameters['x_auth_mode'] = 'client_auth';
    $request = $this->oAuthRequest($this->accessTokenURL(), 'POST', $parameters);
    $token = nsx_trOAuthUtil::parse_parameters($request);
    $this->token = new nsx_trOAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }

  /**
   * GET wrapper for oAuthRequest.
   */
  function get($url, $parameters = array()) {
    $response = $this->oAuthRequest($url, 'GET', $parameters);
    if ($this->format === 'json' && $this->decode_json) {
      return json_decode($response);
    }
    return $response;
  }
  
  /**
   * POST wrapper for oAuthRequest.
   */
  function post($url, $parameters = array()) {
    $response = $this->oAuthRequest($url, 'POST', $parameters); //prr($response);
    if ($this->format === 'json' && $this->decode_json) {
      return json_decode($response);
    }
    return $response;
  }

  /**
   * DELETE wrapper for oAuthReqeust.
   */
  function delete($url, $parameters = array()) {
    $response = $this->oAuthRequest($url, 'DELETE', $parameters);
    if ($this->format === 'json' && $this->decode_json) {
      return json_decode($response);
    }
    return $response;
  }

  /**
   * Format and sign an OAuth / API request
   */
  function oAuthRequest($url, $method, $parameters) { 
    if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
      $url = "{$this->host}{$url}";
    }
    $request = nsx_trOAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters);
    $request->sign_request($this->sha1_method, $this->consumer, $this->token); 
    switch ($method) {
    case 'GET':
      return $this->http($request->to_url(), 'GET');
    default:
      return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata());
    }
  }

  /**
   * Make an HTTP request
   *
   * @return API results
   */
  function http($url, $method, $postfields = NULL) {  $ref = '';
    $this->http_info = array();
    if ($method=='DELETE') $ci = curl_init(); else $ci = curl_init($url);
    
    $headers = array();
$headers[] = 'Connection: keep-alive';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.34 Safari/536.11';
$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
$headers[] = 'Accept-Encoding: gzip,deflate,sdch';
$headers[] = 'Accept-Language: en-US,en;q=0.8';
$headers[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3';
    
     $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
//        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; InfoPath.3; .NET4.0E; .NET CLR 1.1.4322)",
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
//        CURLOPT_MAXREDIRS      => $redirs,       // stop after 10 redirects
        CURLOPT_REFERER        => $ref,       // stop after 10 redirects        
        CURLINFO_HEADER_OUT    => true
    ); 
    curl_setopt_array($ci, $options);   
        
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
    curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
//    curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));


    switch ($method) {
      case 'POST':
        curl_setopt($ci, CURLOPT_POST, TRUE);
        if (!empty($postfields)) {
          curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
        }
        break;
      case 'DELETE':
        curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
        if (!empty($postfields)) {
          $url = "{$url}?{$postfields}";
        }
    }
//prr($url);
    if ($method=='DELETE')  curl_setopt($ci, CURLOPT_URL, $url);  
    
    global $nxs_skipSSLCheck; if ($nxs_skipSSLCheck===true) curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ci);  $out = array();
  
    /*  
    $err = curl_errno($ci); if ($err==28){ sleep(10); $tm = true; $response = curl_exec($ci); } //   echo "##".$err; $errmsg = curl_error($ci); $out['errno'] = $err; $out['errmsg'] = $errmsg; prr($out);
    $err = curl_errno($ci); if ($err>0){ $errmsg = curl_error($ci); $out['errno'] = $err; $out['errmsg'] = $errmsg; curl_close($ci); return $out; }
    */
    //$err = curl_errno($ci); $errmsg = curl_error($ci); $header = curl_getinfo($ci);  $header['errno']   = $err;  $header['errmsg']  = $errmsg;  $header['content'] = $response;  prr($header); //die();
    
    $err = curl_errno($ci); if ($err>0){ $errmsg = curl_error($ci); $out['errno'] = $err; $out['errmsg'] = $errmsg.". Tumblr API is down. Please try later.";  $this->http_code = '404'; curl_close($ci); return json_encode($out); }
    
    $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE); 
    $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
    $this->url = $url; //prr($this);
    curl_close ($ci); // prr($response);
    return $response;
  }

  /**
   * Get the header info to store.
   */
  function getHeader($ch, $header) {
    $i = strpos($header, ':');
    if (!empty($i)) {
      $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
      $value = trim(substr($header, $i + 2));
      $this->http_header[$key] = $value;
    }
    return strlen($header);
  }
}