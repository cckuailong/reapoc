<?php    
//## NextScripts FriendFeed Connection Class
$nxs_snapAPINts[] = array('code'=>'VB', 'lcode'=>'vb', 'name'=>'vBulletin');

if (!class_exists('nxsAPI_VB')){class nxsAPI_VB{ var $ck = array(); var $ver = 4; var $debug = false;
    function check($u, $url){  if ($this->debug) echo '[VB] Checking '.$u.";<br/>\r\n";
      $ck = $this->ck;  if (!empty($ck) && is_array($ck)) { $hdrsArr = nxs_getNXSHeaders($url); $advSet = nxs_mkRemOptsArr($hdrsArr, $ck); $response = nxs_remote_get($url,$advSet);   
        if(is_nxs_error($response)) { if ($this->debug) echo "Invalid Connection. ".print_r($response, true); return false;  }
        if (stripos($response['body'],'logouthash=')===false) { if ($this->debug) echo "[VB] Bad Saved Login;<br/>\r\n"; return false;  }
        if ( stripos($response['body'], 'usercp.php')!==false && stripos($response['body'], 'logouthash')!==false){ /*echo "You are IN"; */ return true; } else { if ($this->debug) echo "[VB] No Login;<br/>\r\n"; return false;  }
      } return false; 
    }
    function connect($u,$p, $url){ $badOut = 'Error: ';
      //## Check if alrady IN
      if (!$this->check($u, $url)){ if ($this->debug) echo "[VB] NO Saved Data;<br/>\r\n"; $hdrsArr = nxs_getNXSHeaders($url); $mids = ''; $ck = $this->ck; $advSet = nxs_mkRemOptsArr($hdrsArr, $ck); $response = nxs_remote_get($url,$advSet);
        if(is_nxs_error($response)) return "Invalid Connection. ".print_r($response, true); $contents = $response['body']; //$response['body'] = htmlentities($response['body']);  prr($response);    die();
        $ck = $response['cookies']; $mdhashLoc = stripos($contents, 'md5hash(vb_login_password'); if ($mdhashLoc===false) { 
          if (stripos($contents, '"idLoginIframe"')!==false) { $lUrl = CutFromTo($contents, '"idLoginIframe"', '</iframe>' ); $lUrl = CutFromTo($lUrl, 'src="', '"' );  $response = nxs_remote_get($lUrl,$advSet); $contents = $response['body'];
             $mdhashLoc = stripos($contents, 'id="idLoginForm"'); if ($mdhashLoc===false) { return "L2. No supported vBulletin script found at ".$lUrl; }
             $frmTxt = CutFromTo($contents, 'id="idLoginForm"','</form>'); $this->ver = 5;
          } else return "No supported vBulletin script found at ".$url; 
        } else  $frmTxt = CutFromTo($contents, 'md5hash(vb_login_password','</form>'); $md = array(); $flds  = array();
        
        while (stripos($frmTxt, '<input')!==false){ $inpField = trim(CutFromTo($frmTxt,'<input', '>')); $name = trim(CutFromTo($inpField,'name="', '"'));
          if ( stripos($inpField, '"hidden"')!==false && $name!='' && !in_array($name, $md)) { $md[] = $name; $val = trim(CutFromTo($inpField,'value="', '"')); $flds[$name]= $val; $mids .= "&".$name."=".$val;}
          $frmTxt = substr($frmTxt, stripos($frmTxt, '<input')+8);
        } $flds['vb_login_username'] = $u; $flds['vb_login_md5password'] = md5($p);  $flds['vb_login_md5password_utf'] = md5($p); $flds['cookieuser'] = '1'; $flds['do'] = 'login';   $flds['rememberme'] = 'on'; $flds['username'] = $u;  $flds['password'] = '';   
        // $logURL = substr($contents, $mdhashLoc-250, 250); $logURL = CutFromTo($logURL, 'action="', '"');          
        if (stripos($contents, 'base href="')!==false) $baseURL = trim(CutFromTo($contents,'base href="', '"')); else { $uarr = explode('/',$url);  $dd = $uarr[count($uarr)-1]; $baseURL = str_replace($dd, '', $url);}
        $hdrsArr = nxs_getNXSHeaders($url, true); $hdrsArr['Upgrade-Insecure-Requests'] = 1;
        $advSet = nxs_mkRemOptsArr($hdrsArr, $ck, $flds);  $r2 = nxs_remote_post( $baseURL.($this->ver==5?'auth/login':'login.php?do=login'), $advSet); // prr($advSet); prr( $baseURL.($this->ver==5?'auth/login':'login.php?do=login') ); 
        if (stripos($r2['body'],'exec_refresh()')!==false || stripos($r2['body'],'url = url.replace')!==false) { $ck = nxsMergeArraysOV($ck, $r2['cookies']); $this->ck = $ck; return false; } else return "Bad Username/Password";
      } else { if ($this->debug) echo "[VB] Saved Data is OK;<br/>\r\n"; return false; }
    }
    function post($url, $subj, $msg, $lnk, $tags){ $hdrsArr = nxs_getNXSHeaders($url); $ck = $this->ck; $mids=''; $advSet = nxs_mkRemOptsArr($hdrsArr, $ck); $response = nxs_remote_get($url, $advSet);   
      if(is_nxs_error($response)) return "Invalid Connection. ".print_r($response, true); $contents = $response['body']; // $response['body'] = htmlentities($response['body']);  prr($response);    die();
      if (stripos($contents, 'base href="')!==false) $baseURL = trim(CutFromTo($contents,'base href="', '"')); else { $uarr = explode('/',$url); $dd = $uarr[count($uarr)-1]; $baseURL = str_replace($dd, '', $url); }
      
     
      
      if (stripos($contents, '"nodeid": "')!==false) { $node = CutFromTo($contents, '"nodeid": "','"');
       $this->ver = 5; if (stripos($contents, 'new-conversation-btn')!==false) $mdd='t'; elseif (stripos($contents, 'create-content/text/')!==false) $mdd='p'; else return "No Thread/Post Controls found";
      } elseif (stripos($contents, 'newthread.php?do=newthread')!==false) $mdd='t'; elseif (stripos($contents, 'newreply.php?')!==false) $mdd='p'; else return "No Thread/Post Controls found";
  
      if ($mdd=='t'){ if ($this->ver == 5) $tURL =  $baseURL.'new-content/'.$node; else { $fid = CutFromTo($contents, 'newthread.php?do=newthread','"');  $tURL =  $baseURL.'newthread.php?do=newthread'.str_replace('&amp;','&',$fid); }
        $response = nxs_remote_get( $tURL, $advSet); $contents = $response['body'];
        $frmTxt = ($this->ver == 5)?CutFromTo($contents, 'create-content/text/','</form>'):CutFromTo($contents, 'newthread.php?do=postthread','</form>');  $md = array(); $flds  = array(); //prr($frmTxt);
        while (stripos($frmTxt, '<input')!==false){ $inpField = trim(CutFromTo($frmTxt,'<input', '>')); $name = trim(CutFromTo($inpField,'name="', '"')); 
          if ( stripos($inpField, '"hidden"')!==false && $name!='' && !in_array($name, $md)) { $md[] = $name; $val = trim(CutFromTo($inpField,'value="', '"')); $flds[$name]= $val; $mids .= "&".$name."=".$val;}
          $frmTxt = substr($frmTxt, stripos($frmTxt, '<input')+8);
        }  $flds['subject'] = $subj;  $flds['text'] = $msg;   $flds['title'] = $subj; $flds['message'] = $msg; $flds['message_backup'] = $msg; $flds['wysiwyg'] = '1'; $flds['do'] = 'postthread'; $flds['taglist'] = $tags;  $flds['parseurl'] = '1';  $flds['sbutton'] = 'Submit+New+Thread';  
        $smURL = $baseURL . (($this->ver == 5)? 'create-content/text/':'newthread.php?do=postthread'.str_replace('&amp;','&',$fid));
      } //prr($flds);
      if ($mdd=='p'){ 
        if ($this->ver < 5) { $fid = CutFromTo($contents, 'newreply.php?do=newreply','"'); $response = nxs_remote_get( $baseURL.'newreply.php?do=newreply'.str_replace('&amp;','&',$fid),$advSet); $contents = $response['body']; }
        $frmTxt =  ($this->ver == 5)?CutFromTo($contents, 'action="create-content/text/"','</form>'):CutFromTo($contents, 'newreply.php?do=postreply','</form>'); $md = array(); $flds  = array(); //prr($frmTxt);    
        while (stripos($frmTxt, '<input')!==false){ $inpField = trim(CutFromTo($frmTxt,'<input', '>')); $name = trim(CutFromTo($inpField,'name="', '"')); 
          if ( $name!='' && !in_array($name, $md)) { $md[] = $name; $val = trim(CutFromTo($inpField,'value="', '"')); $flds[$name]= $val; $mids .= "&".$name."=".$val;}
          $frmTxt = substr($frmTxt, stripos($frmTxt, '<input')+8);
        }  $flds['title'] = $subj; $flds['text'] = $msg; $flds['message'] = $msg; $flds['message_backup'] = $msg; $flds['wysiwyg'] = '1'; $flds['do'] = 'postreply';  $flds['parseurl'] = '1';  $flds['sbutton'] = 'Submit+Reply';          
        $smURL = $baseURL. (($this->ver == 5)? 'create-content/text/':'newreply.php?do=postreply'.str_replace('&amp;','&',$fid));
      } //prr($flds);
      //prr($baseURL); prr($smURL); prr($flds);// die();
      unset($flds['preview']); $advSet = nxs_mkRemOptsArr($hdrsArr, $ck, $flds); $r2 = nxs_remote_post( $smURL, $advSet);  // prr($r2['response']);  prr(htmlentities($r2['body'])); $r2['body'] = ''; prr($r2); die();
      if(is_nxs_error($r2)) return "Invalid Connection. ".print_r($r2, true);  
      if (stripos($r2['body'], 'tag can only be ')!==false) { $lgLim =  trim(CutFromTo($r2['body'], 'tag can only be ',' characters')); $flds['taglist'] = substr($flds['taglist'], 0, $lgLim); 
        $advSet = nxs_mkRemOptsArr($hdrsArr, $ck, $flds); $r2 = nxs_remote_post( $smURL, $advSet);
      }
      if(is_nxs_error($r2)) return "Invalid Connection. ".print_r($r2, true);  
      
      if (stripos($r2['body'], '{"')!==false) { $jsRet = json_decode($r2['body'], true); if (is_array($jsRet)) { if (empty($jsRet['retUrl']) && !empty($jsRet['nodeId']) && !empty($flds['ret'])) $jsRet['retUrl'] =  $flds['ret'];
          if (!empty($jsRet['retUrl']) && !empty($jsRet['nodeId'])) return array('postID'=>$jsRet['nodeId'], 'isPosted'=>1, 'postURL'=>$jsRet['retUrl'], 'pDate'=>date('Y-m-d H:i:s')); else return "Something wrong - Error: ".print_r($jsRet, true);    
      }} if (stripos($r2['body'], 'errorblock')!==false) return trim(strip_tags( CutFromTo($r2['body'], 'errorblock','</div>')));
      if (stripos($r2['body'], 'exec_refresh()')!==false && stripos($r2['body'], 'blockrow restore">')!==false) return trim(strip_tags( CutFromTo($r2['body'], 'blockrow restore">','</p>')));
      if (stripos($r2['body'], '<error>')!==false) return trim(strip_tags( CutFromTo($r2['body'], '<error>','</error>')));
      if ( $r2['response']['code']=='302' || $r2['response']['code']=='303') { return array('postID'=>$r2['headers']['location'], 'isPosted'=>1, 'postURL'=>$r2['headers']['location'], 'pDate'=>date('Y-m-d H:i:s'));  }
      if (stripos($r2['body'], '<newpostid>')!==false || stripos($r2['body'], 'postbit postid="')!==false ) return array('postID'=>'', 'isPosted'=>1, 'postURL'=>'', 'pDate'=>date('Y-m-d H:i:s')); 
      return "Something wrong - Error: ".print_r($r2, true);      
    }
}}

if (!class_exists("nxs_class_SNAP_VB")) { class nxs_class_SNAP_VB {
    
    var $ntCode = 'VB';
    var $ntLCode = 'vb';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array();
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }       
    
    function doPostToNT($options, $message){ $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>''); global $nxs_vbCkArray; 
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['uName']) || trim($options['uPass'])=='') { $badOut['Error'] = 'Not Configured'; return $badOut; }            
      //## Get Saved Login Info
      if (function_exists('nxs_getOption')) { $opVal = array(); $opNm = 'nxs_snap_vb_'.sha1('nxs_snap_vb'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } 
      $pass = (substr($options['uPass'], 0, 5)=='n5g9a'||substr($options['uPass'], 0, 5)=='g9c1a'||substr($options['uPass'], 0, 5)=='b4d7s')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];       
      //## Format
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); 
      if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message);
      $urlToGo = (!empty($message['url']))?$message['url']:''; 
      //## Post
      $nt = new nxsAPI_VB(); $nt->debug = false; if (!empty($options['ck'])) $nt->ck = $options['ck']; $loginErr = $nt->connect($options['uName'], $pass, $options['vbURL']); 
      if (!$loginErr) { $ret = $nt->post($options['vbURL'], $msgT, $msg, $urlToGo, $message['tags']); 
        if (is_array($ret) && $ret['isPosted']=='1') { 
           //## Save Login Info
           if (function_exists('nxs_saveOption')) { if (empty($opVal['ck'])) $opVal['ck'] = ''; if (is_array($ret) && $ret['isPosted']=='1' && $opVal['ck'] != $nt->ck) { $opVal['ck'] = $nt->ck; nxs_saveOption($opNm, $opVal); } }
           return $ret;  
        } else return print_r($ret, true);
      } else return print_r($loginErr, true);        
     
   }    
}}
?>