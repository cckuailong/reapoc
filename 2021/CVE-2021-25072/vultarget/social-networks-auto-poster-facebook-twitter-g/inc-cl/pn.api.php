<?php    
//## NextScripts FriendFeed Connection Class
$nxs_snapAPINts[] = array('code'=>'PN', 'lcode'=>'pn', 'name'=>'Pinterest');

if (!class_exists("nxs_class_SNAP_PN")) { class nxs_class_SNAP_PN {
    
    var $ntCode = 'PN';
    var $ntLCode = 'pn';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array();
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }    
    function doPostToNT($options, $message){ global $nxs_gCookiesArr; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      if (!class_exists("nxsAPI_PN")){ $badOut['Error'] .= "Pinterest API Library not found"; return $badOut; } 
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['uName']) || trim($options['uPass'])=='') { $badOut['Error'] = 'Not Configured'; return $badOut; }            
      $pass = (substr($options['uPass'], 0, 5)=='n5g9a'||substr($options['uPass'], 0, 5)=='g9c1a'||substr($options['uPass'], 0, 5)=='b4d7s')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];       
      if (empty($options['imgSize'])) $options['imgSize'] = '';
      //## Format
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); $boardID = $options['pnBoard'];  // prr($boardID); prr($_POST); die();    
      if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = ''; if ($imgURL=='') $badOut['Error'] .= 'NO Image.';
      $urlToGo = (!empty($message['url']))?$message['url']:'';
            
      //## Get Saved Login Info
      if (function_exists('nxs_getOption')) { $opVal = array(); $opNm = 'nxs_snap_pn_'.sha1('nxs_snap_pn'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } 
      
      $nt = new nxsAPI_PN(); $nt->debug = false; if(!empty($options['ck'])) $nt->ck = $options['ck']; if (!empty($options['proxy'])&&!empty($options['proxyOn'])){ $nt->proxy['proxy'] = $options['proxy']['proxy']; if (!empty($options['proxy']['up'])) $nt->proxy['up'] = $options['proxy']['up'];};      
      $loginErr = $nt->connect($options['uName'], $pass); if ($loginErr) { $badOut['Error'] .= 'Can\'t Connect - '.print_r($loginErr, true); return $badOut; }       
            
      if (preg_match ( '/\$(\d+\.\d+)/', $msg, $matches )) $price = $matches[0];  else $price = '';      
      if (isset($options['cImgURL']) && $options['cImgURL']=='S' ) $urlToGo = nxs_mkShortURL($urlToGo); elseif (isset($options['cImgURL']) && $options['cImgURL']=='N' ) $urlToGo = '';      
      if (!empty($nt->ck['chkPnt3'])) unset($nt->ck['chkPnt3']);  $ret = $nt->post($msg, $imgURL, $urlToGo, $boardID, 'T', $price, $urlToGo);
      //## Save Login Info
      if (function_exists('nxs_saveOption')) { if (empty($opVal['ck'])) $opVal['ck'] = ''; if (is_array($ret) && $ret['isPosted']=='1' && $opVal['ck'] != $nt->ck) { $opVal['ck'] = $nt->ck; nxs_saveOption($opNm, $opVal); } }      
      return $ret;
   }    
}}
?>