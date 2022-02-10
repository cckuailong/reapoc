<?php

if (!class_exists('nxssc_SignatureMethod')) { abstract class nxssc_SignatureMethod
{
    abstract public function signing_base($request, $consumer, $token);
    abstract public function sign($request, $consumer, $token);
    public function check($request, $consumer, $token, $signature) {
    $built = $this->sign($request, $consumer, $token);
    return $built == $signature;
    }
}}

if (!class_exists('nxssc_SigMethod_HMAC_SHA1')) { class nxssc_SigMethod_HMAC_SHA1 extends nxssc_SignatureMethod {
    public $name = 'HMAC-SHA1';
    public function signing_base($request, $consumer_secret, $token) {
      $sig = array(rawurlencode($request['method']), rawurlencode($request['normalized_url']), rawurlencode($request['normalized_parameters']));
      $key = sprintf("%s&", rawurlencode($consumer_secret));
      if (isset($token)) $key .= rawurlencode($token->secret);
      $raw = implode('&', $sig);
      return array ($key, $raw);
    }
    public function sign($request, $consumer_secret, $token) {
      $key_raw = $this->signing_base($request, $consumer_secret, $token);// prr($key_raw);
      $basestring = base64_encode (hash_hmac('sha1', $key_raw[1], $key_raw[0], true)); //echo $basestring;
      return rawurlencode($basestring);
    }
    
    public static function urlencode_rfc3986($input) {
      if (is_array($input)) {
        return array_map(array('nxssc_SigMethod_HMAC_SHA1', 'urlencode_rfc3986'), $input);
      } else if (is_scalar($input)) {
        return str_replace(
          '+',
          ' ',
          str_replace('%7E', '~', rawurlencode($input))
        );
      } else {
        return '';
      }
    }
    public function get_normalized_http_url($url) {
      $parts = parse_url($url);

      $port = @$parts['port'];
      $scheme = $parts['scheme'];
      $host = $parts['host'];
      $path = @$parts['path'];

      $port or $port = ($scheme == 'https') ? '443' : '80';

      if (($scheme == 'https' && $port != '443')|| ($scheme == 'http' && $port != '80')) {
        $host = "$host:$port";
      }
      return "$scheme://$host$path";
    }
    public function get_signature_base_string($url, $params, $method = 'GET') {
      $parts = array( $method, $this->get_normalized_http_url($url), $params); //prr($parts);
      $parts = $this->urlencode_rfc3986($parts);
      return implode('&', $parts);
    }
    
    public function sign2($request, $consumer_secret, $token){
      
      $base_string = $this->get_signature_base_string($request['normalized_url'], $request['normalized_parameters'],  $request['method']);
      //$request->base_string = $base_string;
      
      //$key_parts = array( $consumer_secret);  if ($token) $key_parts[] = $token->secret;
      $key_parts = array( $consumer_secret, ($token) ? $token : "");
      
      //$key_parts = array( $consumer_secret,  "");
      
      $key_parts = $this->urlencode_rfc3986($key_parts);
      $key = implode('&', $key_parts); //prr($key); prr($base_string);
      return rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));
    }
    
    
    public function check($request, $consumer_secret, $token, $signature) {
      $built = $this->sign($request, $consumer_secret, $token);
      return $built == $signature;
    }
}}

class wpScoopITOAuth{
    public $baseURL = 'http://www.scoop.it';
    public $request_token_path = '/oauth/request';
    public $access_token_path = '/oauth/access';
    public $http_code;
    protected $version = '1.0';
    protected $sign_method;
    protected $request_token;
    protected $access_token;
    protected $access_secret;
    protected $consumer_key; 
    protected $consumer_secret;    
    
    function __construct($consumer_key, $consumer_secret, $access_token = NULL, $access_secret = NULL) {      
       $this->sign_method = new nxssc_SigMethod_HMAC_SHA1();      
       $this->consumer_key = $consumer_key; $this->consumer_secret = $consumer_secret;
       $this->access_token = $access_token; $this->access_secret = $access_secret;
      // if (!empty($access_token) && !empty($access_secret))  $this->authorize($access_token, $access_secret);
    }
    
    function get_normalized_parameters($params) { $items = array();
      foreach ($params as $key => $value) {
        if ($key == 'oauth_signature') continue;
        if (is_array($value))  $mtems = array_merge($value, $items); else  $items[$key] = $value;
      }
      ksort($items);
      $item_parts = array();
      foreach ($items as $key => $value) {
        $item_parts[] =
        sprintf("%s=%s",rawurlencode($key),rawurlencode($value));
      }
      return implode('&', $item_parts);
    }
    function genRndString($length = 8) { $chars = '0123456789abcdefghijklmnopqrstuvwxyzz';  $string = '';
      for ($p = 0; $p < $length; $p++)  $string .= $chars[mt_rand(0, strlen($chars)-1)];
      return $string;
    }
    function makeHTTPHeaders($ref, $post=false){ $hdrsArr = array(); 
      $hdrsArr['X-Requested-With']='XMLHttpRequest'; $hdrsArr['Connection']='keep-alive'; $hdrsArr['Referer']=$ref;
      $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.22 Safari/537.11';
      if($post) $hdrsArr['Content-Type']='application/x-www-form-urlencoded'; 
      $hdrsArr['Accept']='application/json, text/javascript, */*; q=0.01'; 
      $hdrsArr['Accept-Encoding']='gzip,deflate,sdch'; $hdrsArr['Accept-Language']='en-US,en;q=0.8'; $hdrsArr['Accept-Charset']='ISO-8859-1,utf-8;q=0.7,*;q=0.3'; return $hdrsArr;
    }
    
    function oAuthRespToArr($str){ $arr = explode('&', $str); $out = array();
      foreach ($arr as $ar) { $strr = explode('=', $ar); $out[$strr[0]] = $strr[1];} return $out;
    }
    
    function getReqToken($cbu){      
      $args = array (
        'oauth_consumer_key' => $this->consumer_key,
        'oauth_timestamp' => time(),
        'oauth_nonce' => $this->genRndString(),
        'oauth_version' => $this->version,
        'oauth_callback' => $cbu,
        'oauth_signature_method' => 'HMAC-SHA1'
        
      );      
      $req = array();  $req['method'] = 'GET';  $token = '';
      $req['normalized_url'] = $this->baseURL.$this->request_token_path; 
      $req['normalized_parameters'] = $this->get_normalized_parameters($args);
      $args['oauth_signature'] = $this->sign_method->sign2($req, $this->consumer_secret, $token);      
      $cbu = nxssc_SigMethod_HMAC_SHA1::urlencode_rfc3986($cbu);  
      $url = $this->baseURL.$this->request_token_path.'?oauth_nonce='.$args['oauth_nonce'].'&oauth_timestamp='.$args['oauth_timestamp'].'&oauth_consumer_key='.$this->consumer_key.'&oauth_signature_method='.$args['oauth_signature_method'].'&oauth_version='.$args['oauth_version'].'&oauth_callback='.$cbu.'&oauth_signature='.$args['oauth_signature'];      
      echo "<br/>REQ Token URL: ".$url."<br/>";
      $hdrsArr = $this->makeHTTPHeaders($url); $ckArr = '';   
      $response = nxs_remote_get($url, array( 'method' => 'GET', 'timeout' => 45, 'redirection' => 0,  'headers' => $hdrsArr, 'cookies' => $ckArr));  
      if ( is_nxs_error($response) ) return print_r($response, true);
      $this->http_code = $response['response']['code']; //  prr($response);
      if (stripos($response['body'],'oauth_token_secret=')===false) echo 'Bad oAuth Login:'.$response['body']; else return $this->oAuthRespToArr($response['body']);
    }
    function getAccToken($verifier){
      $args = array (
        'oauth_token' => $this->access_token,
        'oauth_token_secret' => $this->access_secret,
        'oauth_timestamp' => time(),
        'oauth_nonce' => $this->genRndString(),
        'oauth_version' => $this->version,
        'oauth_consumer_key' => $this->consumer_key,
        'oauth_verifier' => $verifier,
        'oauth_signature_method' => 'HMAC-SHA1'        
      );      
      $req = array();  $req['method'] = 'GET';  $req['normalized_url'] = $this->baseURL.$this->access_token_path; // echo "ARGS:"; prr($args); 
      $req['normalized_parameters'] = $this->get_normalized_parameters($args);
      $args['oauth_signature'] = $this->sign_method->sign2($req, $this->consumer_secret, $this->access_secret); 
      $url = $this->baseURL.$this->access_token_path.'?oauth_nonce='.$args['oauth_nonce'].'&oauth_timestamp='.$args['oauth_timestamp'].'&oauth_token_secret='.$this->access_secret.'&oauth_signature_method='.$args['oauth_signature_method'].'&oauth_consumer_key='.$this->consumer_key.'&oauth_verifier='.$verifier.'&oauth_version='.$args['oauth_version'].'&oauth_token='.$this->access_token.'&oauth_signature='.$args['oauth_signature'];
      echo "<br/>REQ Token URL: ".$url."<br/>";
      $hdrsArr = $this->makeHTTPHeaders($url); $ckArr = '';   
      $response = nxs_remote_get($url, array( 'method' => 'GET', 'timeout' => 45, 'redirection' => 0,  'headers' => $hdrsArr, 'cookies' => $ckArr));  
      if ( is_nxs_error($response) ) return $response;
      $this->http_code = $response['response']['code']; 
      if (stripos($response['body'],'oauth_token_secret=')===false) echo 'Bad oAuth Login:'.$response['body']; else return $this->oAuthRespToArr($response['body']);        
    }
    
    public function to_header($params, $realm=null) { $first = true; prr($params);
      if($realm) { $out = 'OAuth realm="' . nxssc_SigMethod_HMAC_SHA1::urlencode_rfc3986($realm) . '"'; $first = false; } else $out = 'OAuth'; $total = array();
      foreach ($params as $k => $v) {
        if (substr($k, 0, 5) != "oauth") continue; 
        $out .= ($first) ? ' ' : ', '."\r"; $out .= nxssc_SigMethod_HMAC_SHA1::urlencode_rfc3986($k) . '="' . nxssc_SigMethod_HMAC_SHA1::urlencode_rfc3986($v) . '"';
        $first = false;
      } prr($out); return $out;
    }
    
    function makeReq($url, $params='', $type='GET'){
      $args = array (
        'oauth_token' => $this->access_token,
        'oauth_consumer_key' => $this->consumer_key,        
        'oauth_timestamp' => time(),        
        'oauth_nonce' => $this->genRndString(),
        'oauth_version' => $this->version,        
        'oauth_signature_method' => 'HMAC-SHA1'        
      );      
      if (is_array($params)) { $argsTS = array_merge($args, $params);} else $argsTS = $args;
      $req = array();  $req['method'] = $type;  $req['normalized_url'] = $url; 
      // if (!empty($argsTS['photo'])) unset($argsTS['photo']);
      $req['normalized_parameters'] = $this->get_normalized_parameters($argsTS); // prr($req);
      $args['oauth_signature'] = $this->sign_method->sign2($req, $this->consumer_secret, $this->access_secret); 
      if (is_array($params)) { if (isset($params['tag']) && is_array($params['tag']) ) { $tags = $params['tag']; unset($params['tag']); $argsAddStr = '';  foreach ($tags as $tg) $argsAddStr .= '&tag='.urlencode($tg); }
          $params = nxssc_SigMethod_HMAC_SHA1::urlencode_rfc3986($params);   $args = array_merge($args, $params);
      } $argsStr = ''; $argsT = array(); uksort($args, 'strcmp'); foreach ($args as $arN=>$arV){$argsT[] = $arN.'='.$arV;} $argsStr = implode('&', $argsT); 
      if ( $type=='GET') {  $url .= '?'.$argsStr;  $hdrsArr = $this->makeHTTPHeaders($url);  $ckArr = ''; // prr($url);
          $response = nxs_remote_get($url, array( 'method' => 'GET', 'timeout' => 45, 'redirection' => 0,  'headers' => $hdrsArr)); //prr($response);
      } else { $hdrsArr = $this->makeHTTPHeaders($url, true); if (!empty($argsAddStr)) $argsStr .= $argsAddStr; //prr($url);  prr($hdrsArr); prr($argsStr);  prr($argsT);
          $response = nxs_remote_post($url, array( 'timeout' => 45, 'redirection' => 0, 'body'=>$argsStr,  'headers' => $hdrsArr)); //prr($argsStr); prr($argsT);   prr($response);
      }
      if ( is_nxs_error($response) ) return $response;
      $this->http_code = $response['response']['code']; $body = $response['body']; $body = maybe_unserialize($body); if (is_array($body)) return $body; else  return json_decode($body, true);   
    }
    
    private function joinParameters($parameters){ $keys = array_keys($parameters); sort($keys, SORT_STRING); $keyValuePairs = array();
        foreach ($keys as $k) array_push($keyValuePairs, rawurlencode($k) . "=" . rawurlencode($parameters[$k])); return implode("&", $keyValuePairs);
    }
    private function sign($url, &$parameters) { $string = implode("&", array( 'POST', rawurlencode($url), rawurlencode($this->joinParameters($parameters))));            
        $parameters['oauth_signature'] = base64_encode(hash_hmac('sha1', $string, $this->consumer_secret.'&'.$this->access_secret, true));
    }
    private function httpRequest($url, $parameters) { $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); curl_setopt($curl, CURLOPT_TIMEOUT, '25'); curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, TRUE); curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);        
        $response = curl_exec($curl); $headers = curl_getinfo($curl); $errmsg = curl_error($curl);  curl_close($curl);
        if (!empty($errmsg)) return $errmsg; else  return $response;
    }
    
    public function flUploadPhoto ($imgFile, $parameters) { $url = 'https://www.flickr.com/services/upload/'; $requestParams = ($parameters == NULL ? array() : $parameters);    $phid = '';    
        $oauthParams = array (            
            'oauth_nonce' => $this->genRndString(),
            'oauth_timestamp' => time(),            
            'oauth_token' => $this->access_token,
            'oauth_consumer_key' => $this->consumer_key,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0',
        );
        $requestParams = array_merge($requestParams, $oauthParams); $this->sign($url, $requestParams); $requestParams['photo'] = $imgFile; // prr($requestParams);                
        $xml = $this->httpRequest($url, $requestParams); if (stripos($xml, '</photoid>')!==false && stripos($xml, 'stat="ok"')!==false) $phid = CutFromTo($xml, '<photoid>','</photoid>');
        return empty($phid) ? "Problem: ".$xml."<pre>".print_r($requestParams, true)."</pre>" : $phid;
    }
    
}
?>