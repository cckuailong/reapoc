<?php    
//## NextScripts FriendFeed Connection Class
$nxs_snapAPINts[] = array('code'=>'PK', 'lcode'=>'pk', 'name'=>'Plurk');

if (!class_exists("nxs_class_SNAP_PK")) { class nxs_class_SNAP_PK {
    
    var $ntCode = 'PK';
    var $ntLCode = 'pk';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array();
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }    
    function doPostToNT($options, $message){ $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['appKey']) || trim($options['appSec'])=='' || empty($options['accessToken'])) { $badOut['Error'] = 'Not Configured'; return $badOut; }   
      if (empty($options['imgSize'])) $options['imgSize'] = '';               
      //## Format
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message);       
      //## Post    
      require_once('apis/plurkOAuth.php'); $consumer_key = $options['appKey']; $consumer_secret = $options['appSec'];
      $tum_oauth = new wpPlurkOAuth($consumer_key, $consumer_secret, $options['accessToken'], $options['accessTokenSec']); 
      $pkURL = trim(str_ireplace('http://', '', $options['pkURL'])); if (substr($pkURL,-1)=='/') $pkURL = substr($pkURL,0,-1);     
      if ($options['pkCat']=='') $options['pkCat'] = ':';    
      if ($options['attchImg']=='1') { if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = ''; if ($imgURL!='') $msg .= " ".$imgURL; }         
    
      $postArr = array('content'=>$msg, 'qualifier'=>$options['pkCat']);  $postinfo = $tum_oauth->makeReq('https://www.plurk.com/APP/Timeline/plurkAdd', $postArr); //  prr($postinfo);
      if (is_array($postinfo) && isset($postinfo['plurk_id'])) $pkID = $postinfo['plurk_id'];  $code = $tum_oauth->http_code; // echo "XX".print_r($code);  prr($postinfo); // prr($msg); prr($postinfo); echo $code."VVVV"; die("|====");
    
      if ($code == 200 && $pkID!='') { $alphabet = str_split("0123456789abcdefghijklmnopqrstuvwxyz"); $shorten = ''; $plurk_id = $pkID;
          while ($plurk_id != 0){ $i = $plurk_id % 36; $plurk_id = intval($plurk_id / 36); $shorten = $alphabet[$i].$shorten;}  $link = 'https://www.plurk.com/p/'.$shorten;
          return array('postID'=>$pkID, 'isPosted'=>1, 'postURL'=>$link, 'pDate'=>date('Y-m-d H:i:s')); 
      } else { $badOut['Error'] .= " ERROR: - ".$postinfo['error_text']; }  
      return $badOut;
   }    
}}
?>