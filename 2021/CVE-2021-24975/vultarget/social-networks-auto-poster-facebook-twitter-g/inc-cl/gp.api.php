<?php    
//## NextScripts Google+ Connection Class
$nxs_snapAPINts[] = array('code'=>'GP', 'lcode'=>'gp', 'name'=>'Google+');

if (!class_exists("nxs_class_SNAP_GP")) { class nxs_class_SNAP_GP {
    
    var $ntCode = 'GP';
    var $ntLCode = 'gp';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array();
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }
    function doPostToNT($options, $message){ $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>''); $lnk = '';
      //## Check API Lib
      // if (!function_exists('doPostToGooglePlus')) if (file_exists('apis/postToGooglePlus.php')) require_once ('apis/postToGooglePlus.php'); elseif (file_exists('/home/_shared/deSrc.php')) require_once ('/home/_shared/deSrc.php'); 
      if (!class_exists('nxsAPI_GP')) { $badOut['Error'] = 'Google+ API Library not found'; return $badOut; }
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['uName']) || trim($options['uPass'])=='') { $badOut['Error'] = 'Not Configured'; return $badOut; }
      if (empty($options['imgSize'])) $options['imgSize'] = ''; // prr($options); die();
      //## Make Post      
      $gpPostType = $options['postType']; $opVal = array(); $opNm = md5('nxs_snap_gp'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); // prr($opVal);
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); // Make "message default"
      if ($gpPostType=='I' || $gpPostType=='A') { if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = ''; }             
      $email = $options['uName'];  $pass = (substr($options['uPass'], 0, 5)=='n5g9a' || substr($options['uPass'], 0, 5)=='g9c1a')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];    
      //prr($options);      
      
      //prr($options['ck']);      
      $nt = new nxsAPI_GP(); if(!empty($options['ck'])) $nt->ck = $options['ck'];  $nt->debug = false;  $loginError = $nt->connect($email, $pass); //  die('STOP IT');
      if (!$loginError){ 
         if ($gpPostType=='A') $lnk = $message['url']; elseif ($gpPostType=='I') { $lnk = array(); if ($imgURL!='') $lnk['img'] = $imgURL;  if ($imgURL=='' && $message['noImg']===true) $lnk['img'] = '';
            if (!empty($message['videoURL'])) $lnk['video'] = $message['videoURL']; 
         } $comPgID = ''; $comPGCatID = '';
         //if (!empty($options['gpPageID']) && empty($options['gpCommID']))  $pageID = $options['gpPageID']; 
         $pageID = (!empty($options['postAs']) && $options['postAs']!='p')?$options['postAs']:''; $postTo = (!empty($options['postTo']) && $options['postTo']!='p')?$options['postTo']:''; 
         if (!empty($postTo)) { if (strlen($postTo)<10) { $comPgID = $postTo;  /*Maybe get name */ }
           elseif (substr($postTo,0,1)=='c') { $comPgID = substr($postTo,1); $comPGCatID = $options['commCat'];}
         } // prr($pageID); prr($comPgID); prr($comPGCatID); //die();
         $result = $nt -> postGP($msg, $lnk, $pageID, $comPgID, $comPGCatID);
      } else {  $badOut['Error'] = "Login/Connection Error: ". print_r($loginError, true); return $badOut; }       
      //if (is_array($result) && $result['isPosted']=='1') nxs_save_glbNtwrks('gp', $options['ii'], $nt->ck, 'ck');
      if (is_array($result) && $result['isPosted']=='1') { $opVal['ck'] = $nt->ck; nxs_saveOption($opNm,$opVal); }
      return $result;
    }
    
}}
?>