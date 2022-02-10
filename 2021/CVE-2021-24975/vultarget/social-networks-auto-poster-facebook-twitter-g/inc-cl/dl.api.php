<?php    
//## NextScripts Delicious Connection Class
$nxs_snapAPINts[] = array('code'=>'DL', 'lcode'=>'dl', 'name'=>'Delicious');

if (!class_exists("nxs_class_SNAP_DL")) { class nxs_class_SNAP_DL {
    
    var $ntCode = 'DL';
    var $ntLCode = 'dl';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array();
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }        
    function getHeaders($ref, $org='', $post=false, $aj=false){ $hdrsArr = array(); 
        $hdrsArr['Cache-Control']='max-age=0'; $hdrsArr['Connection']='keep-alive'; $hdrsArr['Referer']=$ref;
        $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.36 Safari/537.36'; 
        if($post==='j') $hdrsArr['Content-Type']='application/json;charset=UTF-8'; elseif($post===true) $hdrsArr['Content-Type']='application/x-www-form-urlencoded';
        if($aj===true) $hdrsArr['X-Requested-With']='XMLHttpRequest';  if ($org!='') $hdrsArr['Origin']=$org; 
        $hdrsArr['Accept']='text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';// $hdrsArr['DNT']='1';
        if (function_exists('gzdeflate')) $hdrsArr['Accept-Encoding']='gzip,deflate,sdch'; 
        $hdrsArr['Accept-Language']='en-US,en;q=0.8'; return $hdrsArr; 
    }
    
    function doPostToNT($options, $message){ $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['uName']) || trim($options['uPass'])=='') { $badOut['Error'] = 'Not Configured'; return $badOut; }      
      $email = $options['uName'];  $pass = (substr($options['uPass'], 0, 5)=='g9c1a' || substr($options['uPass'], 0, 5)=='n5g9a')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];  
      //## Format
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); 
      if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message); $tags = nsTrnc($message['tags'], 195, ',', '');
      
      $link = $message['url']; $desc = substr($msgT, 0, 250); $ext = substr($msg, 0, 1000);      
      //$hdrsArr = $this->getHeaders('http://del.icio.us','http://del.icio.us',true); $flds = array('username'=>$email, 'password'=>base64_encode(strrev($pass)));      
      $hdrsArr = $this->getHeaders('https://del.icio.us','https://del.icio.us',true); $flds = array('username'=>$email, 'password'=>$pass);      
      $advSet = nxs_mkRemOptsArr($hdrsArr, '', $flds); $cnt = nxs_remote_post( 'https://del.icio.us/login', $advSet ); 
      if (is_nxs_error($cnt)) {  $badOut = "ERROR (Login Form): ".print_r($cnt, true); return $badOut; } $rep = json_decode($cnt['body'], true);       
      if ($rep['status']!='success') { $badOut = "ERROR (Login): ".print_r($cnt, true); return $badOut; } $ck = $cnt['cookies']; $ckk = explode('=', $rep['session'],1);      
      $ckX = new WP_Http_Cookie( array( 'name' => $ckk[0], 'value' => $ckk[1] )); $ck[] = $ckX; foreach ($ck as $ci=>$cc) $ck[$ci]->value = str_replace(' ','+', $cc->value);      
      $hdrsArr = $this->getHeaders('https://del.icio.us/',''); $hdrsArr['Accept'] = '*/*';
      $advSet = nxs_mkRemOptsArr($hdrsArr, $ck); $cnt = nxs_remote_get( 'https://del.icio.us/save/get_iframe_savelink?url=&title=&notes=', $advSet );// prr($cnt);
      if (is_nxs_error($cnt)) {  $badOut = "ERROR (Login Form): ".print_r($cnt, true); return $badOut; } $ct = CutFromTo($cnt['body'],'csrf_token="','"'); 
      $ck = $cnt['cookies'];  $ck[] = $ckX; foreach ($ck as $ci=>$cc) $ck[$ci]->value = str_replace(' ','+', $cc->value);
      $flds = array('url'=>$link, 'description'=>$desc, 'tags'=>$tags, 'note'=>$ext, 'replace'=>'true', 'private'=>'false', 'share'=>'', 'csrf_token'=>$ct);      
      $hdrsArr = $this->getHeaders('https://del.icio.us/save/get_iframe_savelink?url=&title=&notes=','https://del.icio.us',true, true);  $hdrsArr['Accept']='application/json, text/javascript, */*; q=0.01';
      $advSet = nxs_mkRemOptsArr($hdrsArr, $ck, $flds);  $cnt = nxs_remote_post( 'https://del.icio.us/save/bookmark', $advSet ); // prr($advSet);
      if (is_nxs_error($cnt)) {  $badOut = "ERROR (Post Form): ".print_r($rep, true); return $badOut; } $rep = json_decode($cnt['body'], true);// prr($rep);
      if (empty($rep['save_status']) || ($rep['save_status']!='new' && $rep['save_status']!='update')) { $badOut = "ERROR (Post): ".print_r($cnt, true); return $badOut; } 
      return array('postID'=>md5($message['url']), 'isPosted'=>1, 'postURL'=>'https://del.icio.us/url/'.md5($message['url']), 'pDate'=>date('Y-m-d H:i:s'));  
   }    
}}
?>