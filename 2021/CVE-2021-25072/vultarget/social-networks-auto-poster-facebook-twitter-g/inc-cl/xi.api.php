<?php    
//## NextScripts XING Connection Class

/* 
1. Options

nName - Nickname of the account [Optional] (Presentation purposes only - No affect on functionality)
postType - A or T - "Attached link" or "Text"

2. Post Info

url
text

*/
$nxs_snapAPINts[] = array('code'=>'XI', 'lcode'=>'xi', 'name'=>'XING');

if (!class_exists("nxs_class_SNAP_XI")) { class nxs_class_SNAP_XI {
    
    var $ntCode = 'XI';
    var $ntLCode = 'xi';
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array(); // return false;
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }

    function doPostToNT($options, $message){ global $nxs_urlLen; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      
      if (empty($options['imgSize'])) $options['imgSize'] = '';
      //## Format Post
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message);
      
      if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message); 
      //if (!empty($message['pCTitle'])) $msgTG = $message['pCTitle']; else $msgTG = nxs_doFormatMsg($options['msgCTFormat'], $message); 
      //## Make Post            
      if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = '';  $postType = $options['postType'];       
      
      $pass = (substr($options['uPass'], 0, 5)=='n5g9a' || substr($options['uPass'], 0, 5)=='g9c1a')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass']; 
      
      if (!empty($options['uName'])) {
        $opVal = array(); $opNm = md5('nxs_snap_xi'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); 
        $nt = new nxsAPI_XI(); if(!empty($options['ck'])) $nt->ck = $options['ck'];  $nt->debug = true;  $loginError = $nt->connect($options['uName'], $pass); 
        
        if ($loginError==false){ $opVal['ck'] = $nt->ck; nxs_saveOption($opNm,$opVal);
        //prr($options); die();
          switch ( $options['whToPost'] ) { 
            case 'PR': $result = $nt -> post($msg, $options['postTypeP']=='A'?$message['url']:''); break;
            case 'C' : $result = $nt -> postC($msg, $msgT, $options['pgcID']); break;
            case 'G' : $result = $nt -> postG($msg, $msgT, $options['pggID'], $options['gpfID'], $imgURL); break;                
          }
          return $result;
        }
        
      } else {
        if (!isset($options['accessToken']) || trim($options['accessToken'])=='') { $badOut['Error'] = 'Not Authorized'; return $badOut; }         
        $tum_oauth = new nxs_OAuthBaseCl($options['appKey'], $options['appSec'], $options['accessToken'], $options['accessTokenSec']);
        $tum_oauth->baseURL = 'https://api.xing.com'; 
      
        $msg = str_replace('&amp;#039;', "'", $msg);  $msg = str_replace('&#039;', "'", $msg);  $msg = str_replace('#039;', "'", $msg);  $msg = str_replace('#039', "'", $msg);
        $msg = str_replace('&amp;#8217;', "'", $msg); $msg = str_replace('&#8217;', "'", $msg); $msg = str_replace('#8217;', "'", $msg); $msg = str_replace('#8217', "'", $msg);
        $msg = str_replace('&amp;#8220;', '"', $msg); $msg = str_replace('&#8220;', '"', $msg); $msg = str_replace('#8220;', '"', $msg); $msg = str_replace('#8220', "'", $msg);
        $msg = str_replace('&amp;#8221;', '"', $msg); $msg = str_replace('&#8221;', '"', $msg); $msg = str_replace('#8221;', '"', $msg); $msg = str_replace('#8221', "'", $msg);
        $msg = str_replace('&amp;#8212;', '-', $msg); $msg = str_replace('&#8212;', '-', $msg); $msg = str_replace('#8212;', '-', $msg); $msg = str_replace('#8212', "-", $msg); 
      
        if ($postType=='A') { $postArr = array('uri'=> urlencode($message['url']), 'text'=>nsTrnc($msg, 420)); 
          $postinfo = $tum_oauth->makeReq('https://api.xing.com/v1/users/me/share/link', $postArr, 'POST'); 
        } else { $postArr = array('message'=>nsTrnc($msg, 420), 'id'=>$options['appAppUserID']); 
          $postinfo = $tum_oauth->makeReq('https://api.xing.com/v1/users/'.$options['appAppUserID'].'/status_message', $postArr, 'POST');// prr($options['appAppUserID']); prr($postArr); prr($postinfo, 'POSTINFO');
        }
        $code = $tum_oauth->http_code; if ($code=='201') { 
          if (!empty($postinfo) && is_array($postinfo) && !empty($postinfo['ids']) && !empty($postinfo['ids'][0])){ $apNewPostID = $postinfo['ids'][0]; $np = explode('_',$apNewPostID); $apNewPostURL = 'https://www.xing.com/feedy/stories/'.$np[0]; }
            else { $apNewPostID = ''; $apNewPostURL = 'https://www.xing.com/profile/'.$options['appPGUserName'].'/activities'; } 
        }
        if (!empty($apNewPostID) || $code=='201') {         
           return array('postID'=>$apNewPostID, 'isPosted'=>1, 'postURL'=>$apNewPostURL, 'pDate'=>date('Y-m-d H:i:s'));          
        } else { $badOut['Error'] .= print_r($postinfo, true)." Code:".$tum_oauth->http_code; 
          return $badOut;
        }
        return $badOut;
      }
    }  
    
}}
?>