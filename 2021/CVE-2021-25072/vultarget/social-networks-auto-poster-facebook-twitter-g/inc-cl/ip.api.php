<?php    
//## NextScripts FriendFeed Connection Class
$nxs_snapAPINts[] = array('code'=>'IP', 'lcode'=>'ip', 'name'=>'Instapaper');

if (!class_exists("nxs_class_SNAP_IP")) { class nxs_class_SNAP_IP {
    
    var $ntCode = 'IP';
    var $ntLCode = 'ip';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array();
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }    
    function nxs_getIPHeaders($up){ $hdrsArr = array(); 
      $hdrsArr['Cache-Control']='no-cache'; $hdrsArr['Connection']='keep-alive'; 
      $hdrsArr['User-Agent']='SNAP for Wordpress; Ver 3';
      $hdrsArr['Accept']='text/html, application/xhtml+xml, */*'; $hdrsArr['DNT']='1';
      $hdrsArr['Authorization'] = 'Basic ' . base64_encode("$up");
      if (function_exists('gzdeflate')) $hdrsArr['Accept-Encoding']='gzip,deflate'; 
      $hdrsArr['Accept-Language']='en-US,en;q=0.8'; $hdrsArr['Accept-Charset']='ISO-8859-1,utf-8;q=0.7,*;q=0.3'; return $hdrsArr;
    }
    function doPostToNT($options, $message){ $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['uName']) || trim($options['uPass'])=='') { $badOut['Error'] = 'Not Configured'; return $badOut; }            
      $pass = (substr($options['uPass'], 0, 5)=='n5g9a'||substr($options['uPass'], 0, 5)=='g9c1a'||substr($options['uPass'], 0, 5)=='b4d7s')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];       
      //## Format
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); 
      if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message);      
      $link = urlencode($message['url']); $desc = urlencode(substr($msgT, 0, 250)); $ext = urlencode(substr($msg, 0, 1000)); $tags = $message['tags'];      
      if (!(preg_match("@^(https?|ftp)://[^\s/$.?#].[^\s]*$@iS", $message['url']))) return 'Error: Unvalid URL: '.$message['url'];            
      $apicall = "https://www.instapaper.com/api/add?red=api&url=$link&title=$desc&selection=$ext";
      $hdrsArr = $this->nxs_getIPHeaders($options['uName'].':'.$pass);  $advSet = nxs_mkRemOptsArr($hdrsArr);  $cnt = nxs_remote_get( $apicall, $advSet );//  prr($cnt);
      
      if(is_nxs_error($cnt)) { $error_string = $cnt->get_error_message(); if (stripos($error_string, ' timed out')!==false) { sleep(10); 
        $cnt = nxs_remote_get( $apicall, $advSet );}
      } 
      if(is_nxs_error($cnt)) $ret = 'Something went wrong - '.print_r($cnt, true);  else {      
        if (is_array($cnt) &&  stripos($cnt['body'],'bookmark_id')!==false) return array('postID'=>CutFromTo($cnt['body'],'"bookmark_id": ','}'), 'isPosted'=>1, 'postURL'=>'IP', 'pDate'=>date('Y-m-d H:i:s'));
          else { $ret = "Error: "; if ( is_array($cnt) && $cnt['response']['code']=='401') $ret .= " Incorrect Username/Password "; else $ret .= print_r($cnt, true);  $ret .= $cnt['response']['message']; }
      } $badOut['Error'] .= $ret;  return $badOut;
   }    
}}
?>