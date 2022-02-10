<?php    
//## NextScripts deviantART Connection Class

/* 
1. Options

nName - Nickname of the account [Optional] (Presentation purposes only - No affect on functionality)
rdUName - Reddit User Name
rdPass - Reddit User Passord
rdSubReddit - Name of the Sub-Reddit

rdTitleFormat
rdTextFormat

2. Post Info

url
title - [up to 300 characters long] - title of the submission
text

*/
$nxs_snapAPINts[] = array('code'=>'DA', 'lcode'=>'da', 'name'=>'deviantART');

if (!function_exists("doConnectToDeviantART")) { function doConnectToDeviantART($unm, $pass){ }}

if (!class_exists('nxsAPI_DA')){class nxsAPI_DA{ var $ck = array(); var $mh = '';  var $debug = false;
    function headers($ref, $org='', $post=false, $aj=false){  $hdrsArr = array(); 
 $hdrsArr['Cache-Control']='max-age=0'; $hdrsArr['Connection']='keep-alive'; $hdrsArr['Referer']=$ref;
 $hdrsArr['User-Agent']=': Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.39 Safari/537.36';
 if($post==true) $hdrsArr['Content-Type']='application/x-www-form-urlencoded'; 
 if($aj==true) $hdrsArr['X-Requested-With']='XMLHttpRequest'; 
 if ($org!='') $hdrsArr['Origin']=$org; 
 $hdrsArr['Accept']='text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';// $hdrsArr['DNT']='1';
 if (function_exists('gzdeflate')) $hdrsArr['Accept-Encoding']='gzip,deflate,sdch'; 
 $hdrsArr['Accept-Language']='en-US,en;q=0.8'; return $hdrsArr;         
    }
    function check(){ $ck = $this->ck;  if (!empty($ck) && is_array($ck)) { $hdrsArr = $this->headers('https://www.deviantart.com'); if ($this->debug) echo "[DA] Checking....;<br/>\r\n";
        $rep = nxs_remote_get('https://www.deviantart.com', array('headers' => $hdrsArr, 'httpversion' => '1.1', 'cookies' => $ck)); 
        if (is_nxs_error($rep)) {  $badOut = print_r($rep, true)." - ERROR https://www.deviantart.com is not accessible. "; return $badOut; }  
        $ck2 =  $rep['cookies']; for($i=0;$i<count($ck);$i++) if ($ck[$i]->name=='userinfo') $ck[$i]->value = urlencode($ck2[0]->value);  $this->ck = $ck;
        if (is_nxs_error($rep)) return false; $contents = $rep['body']; //if ($this->debug) prr($contents);
        $mh = CutFromTo($rep['body'], '$(\'#logoutme\').submit();">', 'data-ga_click_event'); $mh = CutFromTo($mh, 'href="', '"'); $this->mh = $mh;
        return stripos($contents, 'https://www.deviantart.com/users/logout')!==false;
      } else return false;
    }
    function connect($u,$p){ $badOut = 'Error: ';
        //## Check if alrady IN
        if (!$this->check()){ if ($this->debug) echo "[DA] NO Saved Data;<br/>\r\n";
          $url = "https://www.deviantart.com/users/login";  $hdrsArr = $this->headers('http://www.deviantart.com/'); $advSet = nxs_mkRemOptsArr($hdrsArr); $rep = nxs_remote_get($url, $advSet); 
          if (is_nxs_error($rep)) {  $badOut = print_r($rep, true)." - ERROR Login 1"; return $badOut; }  $ck =  $rep['cookies'];
          $rTok = CutFromTo($rep['body'], 'name="validate_token" value="', '"'); $rKey = CutFromTo($rep['body'], 'name="validate_key" value="', '"'); $ck[0]->value = urlencode($ck[0]->value);
          $hdrsArr = $this->headers('https://www.deviantart.com/users/login', 'https://www.deviantart.com/', true);
          $flds = array('ref' => 'https://www.deviantart.com/users/loggedin', 'username' => $u, 'password' => $p, 'remember_me' => '1', 'validate_token' => $rTok, 'validate_key' => $rKey);
          $advSet = nxs_mkRemOptsArr($hdrsArr, $ck, $flds); $response = nxs_remote_post( $url, $advSet); if (is_nxs_error($response)) {  $badOut = print_r($response, true)." - ERROR Login 2"; return $badOut; } 
          $ck =  $response['cookies']; foreach ($ck as $i=>$ckk) $ck[$i]->value = urlencode($ckk->value);   
          if (isset($response['headers']['location']) && stripos($response['headers']['location'], 'wrong-password')!==false  ) {  $badOut = "Wrong Password - ERROR"; return $badOut; }  
          if (isset($response['headers']['location']) && ( $response['headers']['location']=='http://www.deviantart.com' || $response['headers']['location']=='https://www.deviantart.com/users/loggedin')) { 
            $hdrsArr = $this->headers('http://www.deviantart.com'); $advSet = nxs_mkRemOptsArr($hdrsArr, $ck, '', '', 2); $rep = nxs_remote_get('http://www.deviantart.com/browse/all/',$advSet);// prr($advSet); prr($rep); die();
            
            if (is_nxs_error($rep)) {  $badOut = "ERROR (Login 3): ".print_r($rep, true); return $badOut; } 
            $mh = CutFromTo($rep['body'], "logoutme').submit();\"><i></i>", 'data-ga_click_event'); $mh = CutFromTo($mh, 'href="', '"'); // prr($mh);
            $ck2 =  $rep['cookies']; for($i=0;$i<count($ck);$i++) if ($ck[$i]->name=='userinfo') $ck[$i]->value = urlencode($ck2[0]->value);  $this->ck = $ck; $this->mh = $mh; return false;
          } else  $badOut = print_r($response, true)." - ERROR  Login 4"; return $badOut; 
        } else { if ($this->debug) echo "[DA] Saved Data is OK;<br/>\r\n"; return false; }
    }
    function post($post){ $ck = $this->ck; $mh = $this->mh; $hdrsArr = $this->headers('http://www.deviantart.com/'); $badOut = '';      
      $rep = nxs_remote_get( $mh.'/journal/?edit', nxs_mkRemOptsArr($hdrsArr, $ck)); if (is_nxs_error($rep)) {  $badOut = " - ERROR (Post 1): ".$mh.'/journal/?edit' . " | " . print_r($rep, true); return $badOut; } 
      $contents = CutFromTo($rep['body'], '<h3 class="journal-editor-create">', '</form>'); // prr($contents);      
      $md = array();  while (stripos($contents, '"hidden"')!==false){$contents = substr($contents, stripos($contents, '"hidden"')+8); $name = trim(CutFromTo($contents,'name="', '"'));
        if (!in_array($name, $md)) { $md[] = $name; $val = trim(CutFromTo($contents,'value="', '"')); $flds[$name]= urldecode (nxs_decodeEntitiesFull($val)); }
      } $flds['subject'] = nsTrnc(nxs_decodeEntitiesFull(addslashes($post['title'])), 50); $flds['body'] = trim(addslashes($post['text']));  $flds['song'] = '';       
      $flds['game'] = ''; $flds['book'] = ''; $flds['food'] = ''; $flds['movie'] = ''; $flds['drink'] = ''; $flds['flip'] = '0'; $flds['featured'] = '1'; 
      $flds['portal'] = '1'; $flds['skinlabel'] = 'No+skin'; $flds['jheader'] = ''; $flds['jcss'] = ''; $flds['jfooter'] = '';       
      $ck2 =  $rep['cookies']; for($i=0;$i<count($ck);$i++) if ($ck[$i]->name=='userinfo') $ck[$i]->value = urlencode($ck2[0]->value);       
      $fldsOut = http_build_query ($flds); $fldsOut = str_replace('No%2Bskin','No+skin',$fldsOut); $ckk = array(); for($i=0;$i<count($ck);$i++)  
      if ($ck[$i]->name=='userinfo' || $ck[$i]->name=='auth') {$ckk[] = $ck[$i]; if ($ck[$i]->name=='userinfo') $ui = $ck[$i]->value; } $ck = $ckk; sleep(6); //## Important.      
      $pid = CutFromTo($contents, '"pageviewID":"','"'); $iid = CutFromTo($contents, '"requestid":"','"');      
      $dflds = array('ui'=>$ui,'pid'=>$pid, 'iid'=>$iid.'-i7ex4avh-1.0','t'=>'json'); $dfldsq = http_build_query($dflds);      
      $dfldsq1 = 'ui='.$ui.'&c%5B%5D=%22Stash%22%2C%22create_journal%22%2C%5B%2235020854%22%2C%22'.urlencode($flds['subject']).'%22%2C%22'.urlencode($flds['subject']).'%22%2C%22-1%22%2C%7B%7D%5D&pid='.$pid.'&iid='.$iid.'-i7exlgzv-1.0&t=json';      
      $hdrsArr = $this->headers($mh.'/journal/?edit', $mh, true); $advSets = nxs_mkRemOptsArr($hdrsArr, $ck, $dfldsq1); $rep = nxs_remote_post($mh.'/global/difi/?', $advSets); //prr($mh.'/global/difi/?');  prr($advSets);       prr($rep); 
      if (is_nxs_error($rep)) {  $badOut = print_r($rep, true)." - ERROR DFI 1"; return $badOut; } $cnt = $rep['body']; 
      if (stripos($cnt, '"status":"SUCCESS"')===false || stripos($cnt, '"args":["')===false) {  $badOut = print_r($cnt, true)." - ERROR DFI 1.1"; return $badOut; } else $npid = CutFromTo($cnt, '"args":["','"');
      $dfldsq2 = 'ui='.$ui.'&c%5B%5D=%22Deviation%22%2C%22DeleteSingle%22%2C%5B%'.$npid.'%22%2C%221%22%5D&pid='.$pid.'&iid='.$iid.'-i7exlgzv-1.0&t=json';
      $advSets = nxs_mkRemOptsArr($hdrsArr, $ck, $dfldsq2); $rep = nxs_remote_post($mh.'/global/difi/?', $advSets); // sleep(6); //## Important.      //prr($advSets); prr($rep); die();
      $advSets = nxs_mkRemOptsArr($hdrsArr, $ck, $fldsOut); $response = nxs_remote_post($mh.'/journal/?edit', $advSets); //prr($mh.'/journal/?edit');  prr($advSets); prr($response);
      if ($response['response']['code']=='200' && stripos($response['body'],'field_error')!==false) { $eRRMsg = CutFromTo($response['body'],'field_error', '</div>');  $eRRMsg = trim(strip_tags(CutFromTo($eRRMsg."|GGG|",'>', '|GGG|')));
           $badOut = "POST Error: ".$eRRMsg; return $badOut;
      }      
      if ($response['response']['code']=='302') { $hdrsArr = $this->headers($mh);  $advSet = nxs_mkRemOptsArr($hdrsArr, $ck); 
          $rep = nxs_remote_get( $mh.'/journal/', $advSet); if (is_nxs_error($rep)) {  $badOut = print_r($rep, true)." - ERROR DFI 1"; return $badOut; }
          $daNewPostURL = CutFromTo($rep['body'], 'a data-deviationid="', '</a>'); $daNewPostURL = CutFromTo($daNewPostURL, 'href="', '"'); $daNewPostID = CutFromTo($rep['body'], 'a data-deviationid="', '"');          
         return array('postID'=>$daNewPostID, 'isPosted'=>1, 'postURL'=>$daNewPostURL, 'pDate'=>date('Y-m-d H:i:s'));          
      } else { $badOut .= 'Somethibng is not right';
        return $badOut;
      }
      return $badOut;         
    }
    
} }

if (!class_exists("nxs_class_SNAP_DA")) { class nxs_class_SNAP_DA {
    
    var $ntCode = 'DA';
    var $ntLCode = 'da';
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array(); // return false;
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }       
    
    function doPostToNT($options, $message){ global $nxs_urlLen; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut = 'No Options'; return $badOut; }      
      if (!isset($options['uName']) || trim($options['uName'])=='' || !isset($options['uPass']) || trim($options['uPass'])=='') { $badOut = 'No username/password Found'; return $badOut; }      
      //## Format Post
      if (!empty($message['pTitle'])) $title = $message['pTitle']; else $title = nxs_doFormatMsg($options['msgTFormat'], $message); $title = nsTrnc($title, 300);  
      if (!empty($message['pText'])) $text = $message['pText']; else $text = nxs_doFormatMsg($options['msgFormat'], $message);           
      //## Get Saved Login Info
      if (function_exists('nxs_getOption')) { $opVal = array(); $opNm = 'nxs_snap_da_'.sha1('nxs_snap_da'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } 
      //## Make Post            
      $pass = (substr($options['uPass'], 0, 5)=='n5g9a'||substr($options['uPass'], 0, 5)=='g9c1a'||substr($options['uPass'], 0, 5)=='b4d7s')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];      
      $nt = new nxsAPI_DA(); $nt->debug = false; if (!empty($options['ck'])) $nt->ck = $options['ck']; if (!empty($options['mh'])) $nt->mh = $options['mh']; $loginErr = $nt->connect($options['uName'], $pass); 
      if (!$loginErr) { $post = array('title'=>$title, 'text'=>$text ); $ret = $nt->post($post);         
        if (is_array($ret)) { 
           //## Save Login Info
           if (function_exists('nxs_saveOption')) { if (empty($opVal['ck'])) $opVal['ck'] = ''; if (is_array($ret) && $ret['isPosted']=='1' && $opVal['ck'] != $nt->ck) { $opVal['ck'] = $nt->ck; $opVal['mh'] = $nt->mh; nxs_saveOption($opNm, $opVal); } }
           return $ret;  
        } else return print_r($ret, true);
      } else return print_r($loginErr, true);  
    }  
}}
?>