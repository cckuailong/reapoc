<?php    
//## NextScripts Reddit Connection Class

$nxs_snapAPINts[] = array('code'=>'RD', 'lcode'=>'rd', 'name'=>'Reddit');

if (!class_exists("nxs_class_SNAP_RD")) { class nxs_class_SNAP_RD {
    
    var $ntCode = 'RD';
    var $ntLCode = 'rd';
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array(); // return false;
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }     
    function doPostToNT($options, $message){ global $nxs_urlLen; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['uName']) || trim($options['uName'])=='' || !isset($options['uPass']) || trim($options['uPass'])=='') { $badOut['Error'] = 'No username/password Found'; return $badOut; }      
      //## Get Saved Login Info
      if (function_exists('nxs_getOption')) { $opVal = array(); $opNm = 'nxs_snap_rd_'.sha1('nxs_snap_rd'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } 
      //## Format Post
      if (!empty($message['pText'])) $text = $message['pText']; else $text = nxs_doFormatMsg($options['msgFormat'], $message); 
      if (!empty($message['pTitle'])) $title = $message['pTitle']; else $title = nxs_doFormatMsg($options['msgTFormat'], $message);  $title = nsTrnc($title, 300);       
      //## Make Post                  
      $pass = (substr($options['uPass'], 0, 5)=='n5g9a'||substr($options['uPass'], 0, 5)=='g9c1a'||substr($options['uPass'], 0, 5)=='b4d7s')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];       
      $nt = new nxsAPI_RD(); $nt->debug = false; if (!empty($options['ck'])) $nt->ck = $options['ck']; $loginErr = $nt->connect($options['uName'], $pass);  
      if (!$loginErr) { $ret = $nt->post($text, $title, $options['rdSubReddit'], $options['postType']=='A'?$message['url']:''); 
        //## Save Login Info
        if (function_exists('nxs_saveOption')) { if (empty($opVal['ck'])) $opVal['ck'] = ''; if (is_array($ret) && !empty($ret['isPosted']) && $ret['isPosted']=='1' && $opVal['ck'] != $nt->ck) { $opVal['ck'] = $nt->ck; nxs_saveOption($opNm, $opVal); } }
      } else { $badOut['Error'] .= 'Something went wrong - '.print_r($loginErr, true); $ret = $badOut; }            
      return $ret;
    }      
}}
?>