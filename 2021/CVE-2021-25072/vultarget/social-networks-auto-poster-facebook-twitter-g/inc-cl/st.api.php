<?php    
//## NextScripts SETT Connection Class 

$nxs_snapAPINts[] = array('code'=>'ST', 'lcode'=>'st', 'name'=>'SETT');

if (!function_exists("nxs_getSTHeaders")) {  function nxs_getSTHeaders($ref, $org='', $post=false, $aj=false){ $hdrsArr = array(); 
 $hdrsArr['Cache-Control']='max-age=0'; $hdrsArr['Connection']='keep-alive'; $hdrsArr['Referer']=$ref;
 $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.22 Safari/537.36'; 
 if($post==='j') $hdrsArr['Content-Type']='application/json;charset=UTF-8'; elseif($post===true) $hdrsArr['Content-Type']='application/x-www-form-urlencoded';
 if($aj===true) $hdrsArr['X-Requested-With']='XMLHttpRequest';  if ($org!='') $hdrsArr['Origin']=$org; 
 $hdrsArr['Accept']='text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';// $hdrsArr['DNT']='1';
 if (function_exists('gzdeflate')) $hdrsArr['Accept-Encoding']='gzip,deflate,sdch'; 
 $hdrsArr['Accept-Language']='en-US,en;q=0.8'; return $hdrsArr; 
}}

if (!function_exists("doPostToSETT")) { function doPostToSETT(){} }

if (!class_exists('nxsAPI_ST')){class nxsAPI_ST{ var $ck = array();  var $debug = false;
    function headers($ref, $org='', $type='GET', $aj=false){  $hdrsArr = array(); 
      $hdrsArr['Cache-Control']='max-age=0'; $hdrsArr['Connection']='keep-alive'; $hdrsArr['Referer']=$ref;
      $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.22 Safari/537.36'; 
      if($type=='JSON') $hdrsArr['Content-Type']='application/json;charset=UTF-8'; elseif($type=='POST') $hdrsArr['Content-Type']='application/x-www-form-urlencoded';
      if($aj===true) $hdrsArr['X-Requested-With']='XMLHttpRequest';  if ($org!='') $hdrsArr['Origin']=$org; 
      if ($type=='GET') $hdrsArr['Accept']='text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'; else $hdrsArr['Accept']='*/*';
      if (function_exists('gzdeflate')) $hdrsArr['Accept-Encoding']='gzip,deflate,sdch'; 
      $hdrsArr['Accept-Language']='en-US,en;q=0.8'; return $hdrsArr;         
    }
    function makeSlug($str){ $str = strtolower($str); $str = preg_replace('/[^a-zA-Z0-9]/i',' ', $str); $str = trim($str); $str = preg_replace('/\s+/', ' ', $str); $str = preg_replace('/\s+/', '-', $str); return $str;}
    function check(){ $ck = $this->ck; $sslverify = false; if (!empty($ck) && is_array($ck)) { $hdrsArr = $this->headers('https://sett.com'); if ($this->debug) echo "[ST] Checking....;<br/>\r\n";
        $rep = nxs_remote_get('http://sett.com/', array('headers' => $hdrsArr, 'httpversion' => '1.1', 'cookies' => $ck, 'sslverify'=>$sslverify)); 
        if (is_nxs_error($rep)) return false; $ck = $rep['cookies']; $contents = $rep['body']; //if ($this->debug) prr($contents);
        return stripos($contents, 'data-url="')!==false;
      } else return false;
    }
    function connect($u,$p){ $badOut = 'Error: '; 
      //## Check if alrady IN
      if (!$this->check()){ if ($this->debug) echo "[ST] NO Saved Data;<br/>\r\n"; $sslverify = false;  $llURL = 'https://sett.com/login.php?email='.urlencode($u).'&pw='.urlencode($p).'&remember=on&undefined=Log+in';
        $hdrsArr = $this->headers('http://sett.com/'); $rep = nxs_remote_get('http://sett.com/', array('headers' => $hdrsArr, 'httpversion' => '1.1', 'sslverify'=>$sslverify));
        if (is_nxs_error($rep)) {  $badOut = "ERROR (Login Form): ".print_r($rep, true); return $badOut; } if ($rep['response']['code']!='200') { $badOut = "ERROR (Login Form): ".print_r($rep, true); return $badOut; }
        $ck = $rep['cookies'];  $rep = nxs_remote_get($llURL, array('headers' => $hdrsArr, 'cookies' => $ck, 'httpversion' => '1.1', 'sslverify'=>$sslverify)); 
        if (is_nxs_error($rep)) {  $badOut = print_r($rep, true); if (stripos($badOut, 'Operation timed out')!==false) return "Invalid login email!"; else return "ERROR (Login Form 2): ".$badOut;  } 
        if ($rep['response']['code']!='200') { $badOut = "ERROR (Login Form 2): ".print_r($rep, true); return $badOut; } //prr($rep);
        if ($rep['response']['code']=='200') { $content = $rep['body']; if (stripos($content, '{"user":{"')!==false) { $ck = $rep['cookies']; $this->ck = $ck; $loginArr = json_decode($content, true); /* prr($loginArr); */  return false;}
          if (stripos($content, '{"errors":[')!==false) { $loginArr = json_decode($content, true); $err = $loginArr['errors'][0]['title']; return "Error: ".$err." | \r\n<br/>\r\n".print_r($loginArr, true); }
        } return "ERROR (Login): ".$badOut.print_r($rep, true);
      } else { if ($this->debug) echo "[TH] Saved Data is OK;<br/>\r\n"; return false; }
    }
    function post($post){ $ck = $this->ck; $sslverify = false; $oneTime = '0R4qFyHCMAYYclyZQFNYrOkq4uy4mN5'; $enText = nxs_AesCtr::encrypt($post['text'], $oneTime, 256); $blogID = '';
      $hdrsArr = $this->headers('http://sett.com/'); $rep = nxs_remote_get('http://sett.com/'.$post['toURL'], array('headers' => $hdrsArr, 'timeout' => 45, 'cookies' => $ck, 'httpversion' => '1.1', 'sslverify'=>$sslverify));
      if (is_nxs_error($rep)) {  $badOut = "ERROR (Blog URL): ".print_r($rep, true); return $badOut; } $content = $rep['body']; 
      if (stripos($content, 'window.site_id =')!==false) $blogID = trim(CutFromTo($content, 'window.site_id =', ';')); if (empty($blogID)) return "ERROR (NO Blog ID found): ";
    
    //if ($_GET('page')=='NextScripts_SNAP.php') prr($admURL);
    
      $hdrsArr = $this->headers('http://sett.com', 'http://sett.com', 'POST'); $flds = array('text'=>$enText);
      $advSet = array('headers' => $hdrsArr, 'httpversion' => '1.1', 'sslverify'=>$sslverify, 'timeout' => 45, 'redirection' => 0, 'cookies' => $ck, 'body' => $flds); //prr($advSet);
      $rep = nxs_remote_post('http://sett.com/storetext.php', $advSet); if (is_nxs_error($rep)) {  $badOut = "ERROR (Post Post):".print_r($rep, true); return $badOut; } 
      if (stripos($rep['body'], '{"id":')!==false) { $textPush = json_decode($rep['body'], true); } else {  $badOut = "ERROR (Post Post):".print_r($rep, true); return $badOut; } 
      $pURL = 'https://sett.com/newpost.php?autosave=0&context=&is_html=&offset=&one_time=&parent_uid=&promote=&site=&submit_visible=1&to=&uid=&title='.urlencode($post['title']).'&url='.$this->makeSlug($post['title']).'&category=&allow_comments=1&allow_promoting=1&allow_indexing=1&subscribe=1&context=false&is_html=false&offset=-4&parent_uid=&promote=1&site_id='.$blogID.'&type=post&uid=&one_time='.$oneTime.'&text_id='.$textPush['id'].'&text_hash='.$textPush['hash']; $ck=nxsClnCookies($ck);
      $hdrsArr = $this->headers('http://sett.com/'.$post['toURL']); $rep = nxs_remote_get($pURL, array('headers' => $hdrsArr, 'timeout' => 45, 'cookies' => $ck, 'httpversion' => '1.1', 'sslverify'=>$sslverify));
      if (is_nxs_error($rep)) { $badOut = "ERROR (Post Info): ".print_r($rep, true); return $badOut; }       
      if ($rep['response']['code']=='200') { $content = $rep['body']; 
        if (stripos($content, '{"errors":[')!==false) { $loginArr = json_decode($content, true); $err = $loginArr['errors'][0]['title']; return "Error: ".$err." | \r\n<br/>\r\n".print_r($loginArr, true); }
      }
      
      if (stripos($rep['body'],'{"uid":')!==false){$content = json_decode($rep['body'], true); return array('isPosted'=>'1', 'postID'=>$content['uid'], 'postURL'=>'http://sett.com/'.$post['toURL']."/".$content['uid'], 'pDate'=>date('Y-m-d H:i:s'));}
      return 'Error: '.print_r($rep, true); 
    }    
}}

if (!class_exists("nxs_class_SNAP_ST")) { class nxs_class_SNAP_ST {
    
    var $ntCode = 'ST';
    var $ntLCode = 'st';
    
    function createFile($imgURL) {
      $remImgURL = urldecode($imgURL); $urlParced = pathinfo($remImgURL); $remImgURLFilename = $urlParced['basename']; 
      $imgData = nxs_remote_get($remImgURL); if (is_nxs_error($imgData)) { $badOut['Error'] = print_r($imgData, true)." - ERROR"; return $badOut; }          
      $imgData = $imgData['body'];
      $tmp=array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));  
      if (!is_writable($tmp)) return "Your temporary folder or file (file - ".$tmp.") is not witable. Can't upload images to Flickr";
      rename($tmp, $tmp.='.png'); register_shutdown_function(create_function('', "unlink('{$tmp}');"));       
      file_put_contents($tmp, $imgData); if (!$tmp) return 'You must specify a path to a file'; if (!file_exists($tmp)) return 'File path specified does not exist';
      if (!is_readable($tmp)) return 'File path specified is not readable';      
      //  $data['name'] = basename($tmp);
      return "@$tmp";
      
    }
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array(); // return false;
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }
    
    function doPostToNT($options, $message){ global $nxs_urlLen; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['uPass']) || trim($options['uPass'])=='') { $badOut['Error'] = 'Not Authorized'; return $badOut; }      
      if (empty($options['imgSize'])) $options['imgSize'] = '';
      //## Get Saved Login Info
      if (function_exists('nxs_getOption')) { $opVal = array(); $opNm = 'nxs_snap_st_'.sha1('nxs_snap_st'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } 
      //## Format Post
      if (!empty($message['pText'])) $text = $message['pText']; else $text = nxs_doFormatMsg($options['msgFormat'], $message); 
      if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message);
      //## Make Post            
      if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = '';       
      //## Make Post   
      $pass = (substr($options['uPass'], 0, 5)=='n5g9a'||substr($options['uPass'], 0, 5)=='g9c1a'||substr($options['uPass'], 0, 5)=='b4d7s')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];
      $nt = new nxsAPI_ST(); $nt->debug = false; if (!empty($ck)) $nt->ck = $ck; $loginErr = $nt->connect($options['uName'], $pass); 
      if (!$loginErr) { $post = array('url'=>$message['url'], 'toURL'=>$options['mgzURL'], 'imgURL'=>$imgURL, 'title'=>$msgT, 'text'=>$text ); $ret = $nt->post($post);         
        //## Save Login Info
        if (function_exists('nxs_saveOption')) { if (empty($opVal['ck'])) $opVal['ck'] = ''; if (is_array($ret) && $ret['isPosted']=='1' && $opVal['ck'] != $nt->ck) { $opVal['ck'] = $nt->ck; nxs_saveOption($opNm, $opVal); } }           
        if (is_array($ret)) { $ret['ck'] = $nt->ck; return $ret;  } else return print_r($ret, true);
      } else return print_r($loginErr, true);      
    }      
}}

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
/*  AES implementation in PHP                                                                     */
/*    (c) Chris Veness 2005-2011 www.movable-type.co.uk/scripts                                   */
/*    Right of free use is granted for all commercial or non-commercial use providing this        */
/*    copyright notice is retainded. No warranty of any form is offered.                          */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
if (!class_exists('nxs_Aes')) { class nxs_Aes {
  public static function cipher($input, $w) {    // main cipher function [§5.1]
    $Nb = 4;                 // block size (in words): no of columns in state (fixed at 4 for AES)
    $Nr = count($w)/$Nb - 1; // no of rounds: 10/12/14 for 128/192/256-bit keys
  
    $state = array();  // initialise 4xNb byte-array 'state' with input [§3.4]
    for ($i=0; $i<4*$Nb; $i++) $state[$i%4][floor($i/4)] = $input[$i];
  
    $state = self::addRoundKey($state, $w, 0, $Nb);
  
    for ($round=1; $round<$Nr; $round++) {  // apply Nr rounds
      $state = self::subBytes($state, $Nb);
      $state = self::shiftRows($state, $Nb);
      $state = self::mixColumns($state, $Nb);
      $state = self::addRoundKey($state, $w, $round, $Nb);
    }
  
    $state = self::subBytes($state, $Nb);
    $state = self::shiftRows($state, $Nb);
    $state = self::addRoundKey($state, $w, $Nr, $Nb);
  
    $output = array(4*$Nb);  // convert state to 1-d array before returning [§3.4]
    for ($i=0; $i<4*$Nb; $i++) $output[$i] = $state[$i%4][floor($i/4)];
    return $output;
  }
  private static function addRoundKey($state, $w, $rnd, $Nb) {  // xor Round Key into state S [§5.1.4]
    for ($r=0; $r<4; $r++) {
      for ($c=0; $c<$Nb; $c++) $state[$r][$c] ^= $w[$rnd*4+$c][$r];
    }
    return $state;
  }
  private static function subBytes($s, $Nb) {    // apply SBox to state S [§5.1.1]
    for ($r=0; $r<4; $r++) {
      for ($c=0; $c<$Nb; $c++) $s[$r][$c] = self::$sBox[$s[$r][$c]];
    }
    return $s;
  }
  private static function shiftRows($s, $Nb) {    // shift row r of state S left by r bytes [§5.1.2]
    $t = array(4);
    for ($r=1; $r<4; $r++) {
      for ($c=0; $c<4; $c++) $t[$c] = $s[$r][($c+$r)%$Nb];  // shift into temp copy
      for ($c=0; $c<4; $c++) $s[$r][$c] = $t[$c];           // and copy back
    }          // note that this will work for Nb=4,5,6, but not 7,8 (always 4 for AES):
    return $s;  // see fp.gladman.plus.com/cryptography_technology/rijndael/aes.spec.311.pdf 
  }
  private static function mixColumns($s, $Nb) {   // combine bytes of each col of state S [§5.1.3]
    for ($c=0; $c<4; $c++) {
      $a = array(4);  // 'a' is a copy of the current column from 's'
      $b = array(4);  // 'b' is a•{02} in GF(2^8)
      for ($i=0; $i<4; $i++) {
        $a[$i] = $s[$i][$c];
        $b[$i] = $s[$i][$c]&0x80 ? $s[$i][$c]<<1 ^ 0x011b : $s[$i][$c]<<1;
      }
      // a[n] ^ b[n] is a•{03} in GF(2^8)
      $s[0][$c] = $b[0] ^ $a[1] ^ $b[1] ^ $a[2] ^ $a[3]; // 2*a0 + 3*a1 + a2 + a3
      $s[1][$c] = $a[0] ^ $b[1] ^ $a[2] ^ $b[2] ^ $a[3]; // a0 * 2*a1 + 3*a2 + a3
      $s[2][$c] = $a[0] ^ $a[1] ^ $b[2] ^ $a[3] ^ $b[3]; // a0 + a1 + 2*a2 + 3*a3
      $s[3][$c] = $a[0] ^ $b[0] ^ $a[1] ^ $a[2] ^ $b[3]; // 3*a0 + a1 + a2 + 2*a3
    }
    return $s;
  }
  public static function keyExpansion($key) {  // generate Key Schedule from Cipher Key [§5.2]
    $Nb = 4;              // block size (in words): no of columns in state (fixed at 4 for AES)
    $Nk = count($key)/4;  // key length (in words): 4/6/8 for 128/192/256-bit keys
    $Nr = $Nk + 6;        // no of rounds: 10/12/14 for 128/192/256-bit keys
  
    $w = array();
    $temp = array();
  
    for ($i=0; $i<$Nk; $i++) {
      $r = array($key[4*$i], $key[4*$i+1], $key[4*$i+2], $key[4*$i+3]);
      $w[$i] = $r;
    }
  
    for ($i=$Nk; $i<($Nb*($Nr+1)); $i++) {
      $w[$i] = array();
      for ($t=0; $t<4; $t++) $temp[$t] = $w[$i-1][$t];
      if ($i % $Nk == 0) {
        $temp = self::subWord(self::rotWord($temp));
        for ($t=0; $t<4; $t++) $temp[$t] ^= self::$rCon[$i/$Nk][$t];
      } else if ($Nk > 6 && $i%$Nk == 4) {
        $temp = self::subWord($temp);
      }
      for ($t=0; $t<4; $t++) $w[$i][$t] = $w[$i-$Nk][$t] ^ $temp[$t];
    }
    return $w;
  }
  private static function subWord($w) {    // apply SBox to 4-byte word w
    for ($i=0; $i<4; $i++) $w[$i] = self::$sBox[$w[$i]];
    return $w;
  }
  private static function rotWord($w) {    // rotate 4-byte word w left by one byte
    $tmp = $w[0];
    for ($i=0; $i<3; $i++) $w[$i] = $w[$i+1];
    $w[3] = $tmp;
    return $w;
  }
  private static $sBox = array(
    0x63,0x7c,0x77,0x7b,0xf2,0x6b,0x6f,0xc5,0x30,0x01,0x67,0x2b,0xfe,0xd7,0xab,0x76,
    0xca,0x82,0xc9,0x7d,0xfa,0x59,0x47,0xf0,0xad,0xd4,0xa2,0xaf,0x9c,0xa4,0x72,0xc0,
    0xb7,0xfd,0x93,0x26,0x36,0x3f,0xf7,0xcc,0x34,0xa5,0xe5,0xf1,0x71,0xd8,0x31,0x15,
    0x04,0xc7,0x23,0xc3,0x18,0x96,0x05,0x9a,0x07,0x12,0x80,0xe2,0xeb,0x27,0xb2,0x75,
    0x09,0x83,0x2c,0x1a,0x1b,0x6e,0x5a,0xa0,0x52,0x3b,0xd6,0xb3,0x29,0xe3,0x2f,0x84,
    0x53,0xd1,0x00,0xed,0x20,0xfc,0xb1,0x5b,0x6a,0xcb,0xbe,0x39,0x4a,0x4c,0x58,0xcf,
    0xd0,0xef,0xaa,0xfb,0x43,0x4d,0x33,0x85,0x45,0xf9,0x02,0x7f,0x50,0x3c,0x9f,0xa8,
    0x51,0xa3,0x40,0x8f,0x92,0x9d,0x38,0xf5,0xbc,0xb6,0xda,0x21,0x10,0xff,0xf3,0xd2,
    0xcd,0x0c,0x13,0xec,0x5f,0x97,0x44,0x17,0xc4,0xa7,0x7e,0x3d,0x64,0x5d,0x19,0x73,
    0x60,0x81,0x4f,0xdc,0x22,0x2a,0x90,0x88,0x46,0xee,0xb8,0x14,0xde,0x5e,0x0b,0xdb,
    0xe0,0x32,0x3a,0x0a,0x49,0x06,0x24,0x5c,0xc2,0xd3,0xac,0x62,0x91,0x95,0xe4,0x79,
    0xe7,0xc8,0x37,0x6d,0x8d,0xd5,0x4e,0xa9,0x6c,0x56,0xf4,0xea,0x65,0x7a,0xae,0x08,
    0xba,0x78,0x25,0x2e,0x1c,0xa6,0xb4,0xc6,0xe8,0xdd,0x74,0x1f,0x4b,0xbd,0x8b,0x8a,
    0x70,0x3e,0xb5,0x66,0x48,0x03,0xf6,0x0e,0x61,0x35,0x57,0xb9,0x86,0xc1,0x1d,0x9e,
    0xe1,0xf8,0x98,0x11,0x69,0xd9,0x8e,0x94,0x9b,0x1e,0x87,0xe9,0xce,0x55,0x28,0xdf,
    0x8c,0xa1,0x89,0x0d,0xbf,0xe6,0x42,0x68,0x41,0x99,0x2d,0x0f,0xb0,0x54,0xbb,0x16);
  private static $rCon = array( 
    array(0x00, 0x00, 0x00, 0x00),
    array(0x01, 0x00, 0x00, 0x00),
    array(0x02, 0x00, 0x00, 0x00),
    array(0x04, 0x00, 0x00, 0x00),
    array(0x08, 0x00, 0x00, 0x00),
    array(0x10, 0x00, 0x00, 0x00),
    array(0x20, 0x00, 0x00, 0x00),
    array(0x40, 0x00, 0x00, 0x00),
    array(0x80, 0x00, 0x00, 0x00),
    array(0x1b, 0x00, 0x00, 0x00),
    array(0x36, 0x00, 0x00, 0x00) ); 
}  }
if (!class_exists('nxs_AesCtr')) { class nxs_AesCtr extends nxs_Aes {
  public static function encrypt($plaintext, $password, $nBits) {
    $blockSize = 16;  // block size fixed at 16 bytes / 128 bits (Nb=4) for AES
    if (!($nBits==128 || $nBits==192 || $nBits==256)) return '';  // standard allows 128/192/256 bit keys
    // note PHP (5) gives us plaintext and password in UTF8 encoding!
    
    // use AES itself to encrypt password to get cipher key (using plain password as source for  
    // key expansion) - gives us well encrypted key
    $nBytes = $nBits/8;  // no bytes in key
    $pwBytes = array();
    for ($i=0; $i<$nBytes; $i++) $pwBytes[$i] = ord(substr($password,$i,1)) & 0xff;
    $key = nxs_Aes::cipher($pwBytes, nxs_Aes::keyExpansion($pwBytes));
    $key = array_merge($key, array_slice($key, 0, $nBytes-16));  // expand key to 16/24/32 bytes long 
  
    // initialise 1st 8 bytes of counter block with nonce (NIST SP800-38A §B.2): [0-1] = millisec, 
    // [2-3] = random, [4-7] = seconds, giving guaranteed sub-ms uniqueness up to Feb 2106
    $counterBlock = array();
    $nonce = floor(microtime(true)*1000);   // timestamp: milliseconds since 1-Jan-1970
    $nonceMs = $nonce%1000;
    $nonceSec = floor($nonce/1000);
    $nonceRnd = floor(rand(0, 0xffff));
    
    for ($i=0; $i<2; $i++) $counterBlock[$i]   = self::urs($nonceMs,  $i*8) & 0xff;
    for ($i=0; $i<2; $i++) $counterBlock[$i+2] = self::urs($nonceRnd, $i*8) & 0xff;
    for ($i=0; $i<4; $i++) $counterBlock[$i+4] = self::urs($nonceSec, $i*8) & 0xff;
    
    // and convert it to a string to go on the front of the ciphertext
    $ctrTxt = '';
    for ($i=0; $i<8; $i++) $ctrTxt .= chr($counterBlock[$i]);
  
    // generate key schedule - an expansion of the key into distinct Key Rounds for each round
    $keySchedule = nxs_Aes::keyExpansion($key);
    //print_r($keySchedule);
    
    $blockCount = ceil(strlen($plaintext)/$blockSize);
    $ciphertxt = array();  // ciphertext as array of strings
    
    for ($b=0; $b<$blockCount; $b++) {
      // set counter (block #) in last 8 bytes of counter block (leaving nonce in 1st 8 bytes)
      // done in two stages for 32-bit ops: using two words allows us to go past 2^32 blocks (68GB)
      for ($c=0; $c<4; $c++) $counterBlock[15-$c] = self::urs($b, $c*8) & 0xff;
      for ($c=0; $c<4; $c++) $counterBlock[15-$c-4] = self::urs($b/0x100000000, $c*8);
  
      $cipherCntr = nxs_Aes::cipher($counterBlock, $keySchedule);  // -- encrypt counter block --
  
      // block size is reduced on final block
      $blockLength = $b<$blockCount-1 ? $blockSize : (strlen($plaintext)-1)%$blockSize+1;
      $cipherByte = array();
      
      for ($i=0; $i<$blockLength; $i++) {  // -- xor plaintext with ciphered counter byte-by-byte --
        $cipherByte[$i] = $cipherCntr[$i] ^ ord(substr($plaintext, $b*$blockSize+$i, 1));
        $cipherByte[$i] = chr($cipherByte[$i]);
      }
      $ciphertxt[$b] = implode('', $cipherByte);  // escape troublesome characters in ciphertext
    }
  
    // implode is more efficient than repeated string concatenation
    $ciphertext = $ctrTxt . implode('', $ciphertxt);
    $ciphertext = base64_encode($ciphertext);
    return $ciphertext;
  }  
  private static function urs($a, $b) {
    $a &= 0xffffffff; $b &= 0x1f;  // (bounds check)
    if ($a&0x80000000 && $b>0) {   // if left-most bit set
      $a = ($a>>1) & 0x7fffffff;   //   right-shift one bit & clear left-most bit
      $c = $b-1;
      $a = $a >> ($c);           //   remaining right-shifts
    } else {                       // otherwise
      $a = ($a>>$b);               //   use normal right-shift
    } 
    return $a; 
  }
}  }
?>