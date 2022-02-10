<?php    
//## NextScripts FriendFeed Connection Class
$nxs_snapAPINts[] = array('code'=>'WP', 'lcode'=>'wp', 'name'=>'WP Based Blog');

if (!class_exists("nxs_class_SNAP_WP")) { class nxs_class_SNAP_WP {
    
    var $ntCode = 'WP';
    var $ntLCode = 'wp';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array();
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }    
    
    function doPostToNT($options, $message){ $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['uName']) || trim($options['uPass'])=='') { $badOut['Error'] = 'Not Configured'; return $badOut; }            
      $pass = (substr($options['uPass'], 0, 5)=='n5g9a'||substr($options['uPass'], 0, 5)=='g9c1a'||substr($options['uPass'], 0, 5)=='b4d7s')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];       
      if (empty($options['imgSize'])) $options['imgSize'] = '';
      //## Format
      if (empty($message['orID'])) $message['orID']=''; if (empty($message['tags'])) $message['tags']=''; if (empty($message['cats'])) $message['cats']='';
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); 
      if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message);      
      if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = '';
      $link = urlencode($message['url']); $ext = substr($msg, 0, 1000);      
      //## Fix missing xmlrpc.php
      if (substr($options['wpURL'], -1)=='/') $options['wpURL'] = substr($options['wpURL'], 0, -1); if (substr($options['wpURL'], -10)!='xmlrpc.php') $options['wpURL'] .= "/xmlrpc.php";     
      //## Post   
      require_once ('apis/xmlrpc-client.php'); $nxsToWPclient = new NXS_XMLRPC_Client($options['wpURL']); $nxsToWPclient->debug = false;
      if ($imgURL!=='' && stripos($imgURL, 'http')!==false) {      
        // $handle = fopen($imgURL, "rb"); $filedata = ''; while (!feof($handle)) {$filedata .= fread($handle, 8192);} fclose($handle);
        $filedata = nxs_remote_get($imgURL); if (! is_nxs_error($filedata) ) $filedata = $filedata['body']; // echo "AWC?";
        $data = array('name'  => 'image-'.$message['orID'].'.jpg', 'type'  => 'image/jpg', 'bits'  => new NXS_XMLRPC_Base64($filedata), true); 
        $status = $nxsToWPclient->query('metaWeblog.newMediaObject', $message['orID'], $options['uName'], $pass, $data);  $imgResp = $nxsToWPclient->getResponse();  $gid = $imgResp['id'];
      } else $gid = '';
      
      $params = array(0, $options['uName'], $pass, array('software_version')); // prr($params);
      if (!$nxsToWPclient->query('wp.getOptions', $params)) { $ret = 'Something went wrong - '.$nxsToWPclient->getErrorCode().' : '.$nxsToWPclient->getErrorMessage();} else $ret = 'OK';      
      $rwpOpt = $nxsToWPclient->getResponse(); if (!empty($rwpOpt['software_version'])) { $rwpOpt = $rwpOpt['software_version']['value']; $rwpOpt = floatval($rwpOpt); } else $rwpOpt = 0; //prr($rwpOpt);prr($nxsToWPclient);
      //## MAIN Post
      if ($rwpOpt==0) { 
        $errMsg = $nxsToWPclient->getErrorMessage(); if ($errMsg!='') $ret = $errMsg; else  $ret = 'XMLRPC is not found or not active. WP admin - Settings - Writing - Enable XML-RPC'; 
      } else if ($rwpOpt<3.0)  $ret = 'XMLRPC is too OLD - '.$rwpOpt.' You need at least 3.0'; else {
       
        if ($rwpOpt>3.3){
          $nxsToWPContent = array('title'=>$msgT, 'description'=>$msg, 'post_status'=>'draft', 'mt_excerpt'=>$ext, 'mt_allow_comments'=>1, 'mt_allow_pings'=>1, 'post_type'=>'post', 'mt_keywords'=>$message['tags'], 'categories'=>$message['catsA'], 'custom_fields' =>  '');
          $params = array(0, $options['uName'], $pass, $nxsToWPContent, true);
          if (!$nxsToWPclient->query('metaWeblog.newPost', $params)) { $ret = 'Something went wrong - '.$nxsToWPclient->getErrorCode().' : '.$nxsToWPclient->getErrorMessage();} else $ret = 'OK';
          $pid = $nxsToWPclient->getResponse();  
       
          if ($gid!='') {      
            $nxsToWPContent = array('post_thumbnail'=>$gid);  $params = array(0, $options['uName'], $pass, $pid, $nxsToWPContent, true);      
            if (!$nxsToWPclient->query('wp.editPost', $params)) { $ret = 'Something went wrong - '.$nxsToWPclient->getErrorCode().' : '.$nxsToWPclient->getErrorMessage();} else $ret = 'OK';
          }
          $nxsToWPContent = array('post_status'=>'publish');  $params = array(0, $options['uName'], $pass, $pid, $nxsToWPContent, true);      
          if (!$nxsToWPclient->query('wp.editPost', $params)) { $ret = 'Something went wrong - '.$nxsToWPclient->getErrorCode().' : '.$nxsToWPclient->getErrorMessage();} else $ret = 'OK';
        } else {
          $nxsToWPContent = array('title'=>$msgT, 'description'=>$msg, 'post_status'=>'publish', 'mt_allow_comments'=>1, 'mt_allow_pings'=>1, 'post_type'=>'post', 'mt_keywords'=>$message['tags'], 'categories'=>$message['catsA'], 'custom_fields' => '');
          $params = array(0, $options['uName'], $pass, $nxsToWPContent, true);
          if (!$nxsToWPclient->query('metaWeblog.newPost', $params)) { $ret = 'Something went wrong - '.$nxsToWPclient->getErrorCode().' : '.$nxsToWPclient->getErrorMessage();} else $ret = 'OK';
          $pid = $nxsToWPclient->getResponse();  
        }
      }       
      if ($ret!='OK') $badOut['Error'] .= '-=ERROR=- '.print_r($ret, true); else { 
        $wpURL = str_ireplace('/xmlrpc.php','',$options['wpURL']); if(substr($wpURL, -1)=='/') $wpURL=substr($wpURL, 0, -1); $wpURL .= '/?p='.$pid; return array('postID'=>$pid, 'isPosted'=>1, 'postURL'=>$wpURL, 'pDate'=>date('Y-m-d H:i:s'));
      } return $badOut;      
   }    
}}
?>