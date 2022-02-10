<?php    
//## NextScripts FriendFeed Connection Class
$nxs_snapAPINts[] = array('code'=>'YT', 'lcode'=>'yt', 'name'=>'YouTube');

if (!class_exists("nxs_class_SNAP_YT")) { class nxs_class_SNAP_YT {
    
    var $ntCode = 'YT';
    var $ntLCode = 'yt';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array();
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }    
    function doPostToNT($options, $message){ $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['uName']) || trim($options['uPass'])=='') { $badOut['Error'] = 'Not Configured'; return $badOut; }   $email = $options['uName'];
      $pass = (substr($options['uPass'], 0, 5)=='n5g9a' || substr($options['uPass'], 0, 5)=='g9c1a')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];
      //## Format
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); 
            
      $nt = new nxsAPI_GP(); if(!empty($options['ck'])) $nt->ck = $options['ck'];  $nt->debug = false; $loginError = $nt->connect($email, $pass, 'YT');     
      if (!$loginError){          
         $result = $nt -> postYT($msg, $options['ytPageID'], $message['videoURL'], $options['ytGPPageID']); 
      } else {  $badOut['Error'] = "Login/Connection Error: ". print_r($loginError, true); return $badOut; }       
      if (is_array($result) && $result['isPosted']=='1') nxs_save_glbNtwrks('yt', $options['ii'], $nt->ck, 'ck');
      return $result;  
   }    
}}
?>