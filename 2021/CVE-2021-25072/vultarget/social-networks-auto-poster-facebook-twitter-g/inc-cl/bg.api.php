<?php    
//## NextScripts Facebook Connection Class
$nxs_snapAPINts[] = array('code'=>'BG', 'lcode'=>'bg', 'name'=>'Blogger');

if (!class_exists("nxs_class_SNAP_BG")) { class nxs_class_SNAP_BG {
    
    var $ntCode = 'BG';
    var $ntLCode = 'bg';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; 
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }    
    
    function doPostToNT($options, $message){ $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>''); // prr($message);  prr($options);
      //## Check API Lib
      //if (!function_exists('doConnectToBlogger')) if (file_exists('apis/postToGooglePlus.php')) require_once ('apis/postToGooglePlus.php'); elseif (file_exists('/home/_shared/deSrc.php')) require_once ('/home/_shared/deSrc.php');       
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }   
      if ( (!isset($options['uName']) || empty($options['uPass'])) && empty($options['accessToken'])) { $badOut['Error'] = 'Not Configured'; return $badOut; }      
      //## Format
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); 
      if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message); 
      if ($options['inclTags']=='1') $tags = nsTrnc($message['tags'], 195, ',', ''); else $tags = ''; 
      //## Check/Fix HTML   
      if (class_exists('DOMDocument')) {$doc = new DOMDocument();  @$doc->loadHTML('<?xml encoding="UTF-8">' .$msg); $doc->encoding = 'UTF-8'; $msg = $doc->saveHTML(); $msg = CutFromTo($msg, '<body>', '</body>'); 
        $msg = preg_replace('/<br(.*?)\/?>/','<br$1/>',$msg);   $msg = preg_replace('/<img(.*?)\/?>/','<img$1/>',$msg);
        require ('apis/htmlNumTable.php');  if (is_array($HTML401NamedToNumeric)) { $msg = strtr($msg, $HTML401NamedToNumeric); $msgT = strtr($msgT, $HTML401NamedToNumeric); }
      }    
      $msg = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $msg); $msg = preg_replace('/<!--(.*)-->/Uis', "", $msg);  $nxshf = new NXS_HtmlFixer(); $nxshf->debug = false; $msg = $nxshf->getFixedHtml($msg);     
      $msg = str_replace("\r\n","\n", $msg); $msg = str_replace("\n\r","\n", $msg); $msg = str_replace("\r","\n", $msg); $msg = str_replace("\n","<br/>", $msg);  
      //## Make Post
      $blogID = $options['blogID']; 
      //prr($options); // prr($msgT); prr($msg); die();
      if (class_exists('nxsAPI_GP') && !empty($options['uName']) && empty($options['accessToken'])) {           
          $pass = (substr($options['uPass'], 0, 5)=='n5g9a'||substr($options['uPass'], 0, 5)=='g9c1a'||substr($options['uPass'], 0, 5)=='b4d7s')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];      
          $nt = new nxsAPI_GP(); if(!empty($options['ck'])) $nt->ck = $options['ck'];  $nt->debug = false;  $loginError = $nt->connect($options['uName'], $pass, 'BG');  
          if (!$loginError){          
             $result = $nt -> postBG($blogID, $msgT, $msg, $tags);// prr($result); 
          } else {  $badOut['Error'] = "Login/Connection Error: ". print_r($loginError, true); return $badOut; }       
          if (is_array($result) && $result['isPosted']=='1') nxs_save_glbNtwrks('bg', $options['ii'], $nt->ck, 'ck');
          return $result;         
      } else { 
        //## Refresh token
        if (function_exists('get_option')) $currTime = time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ); else  $currTime = time();
        if ($options['accessTokenExp']<$currTime){
          $tknURL = 'https://www.googleapis.com/oauth2/v3/token?refresh_token='.$options['refreshToken'].'&client_id='.$options['appKey'].'&client_secret='.$options['appSec'].'&grant_type=refresh_token';
          $response  = nxs_remote_post($tknURL); $resp = json_decode($response['body'], true); $options['accessToken'] = $resp['access_token']; $options['accessTokenExp'] = $currTime + $resp['expires_in'];
          nxs_save_glbNtwrks('bg', $options['ii'], $resp['access_token'], 'accessToken'); nxs_save_glbNtwrks('bg', $options['ii'], $options['accessTokenExp'], 'accessTokenExp');   
          //nxs_addToLogN('S', 'Test', $logNT, 'Token Refreshed '.date('Y-m-d H:i:s',$options['AccessTokenExp'])."|".$tknURL.$options['AccessToken'].print_r($response, true));
        } 
        //## Post
        $post = array("kind"=>"blogger#post", "blog"=>array("id"=>$blogID), "title"=> $msgT,  "content" => $msg ); $post = json_encode($post); // prr($post);        
        $hdrsArr = array('Content-Type'=>'application/json'); $advSet = array('headers' => $hdrsArr, 'httpversion' => '1.1', 'timeout' => 45, 'redirection' => 0, 'body' => $post);         
        $tknURL = 'https://www.googleapis.com/blogger/v3/blogs/'.$blogID.'/posts?access_token='.$options['accessToken'].''; $ret = ''; $response  = nxs_remote_post($tknURL, $advSet); //prr($tknURL); prr($response);      
        if ((is_object($response) && isset($response->errors))) $badOut['Error'] = print_r($response, true); else $ret = json_decode($response['body'], true);  //prr($ret);
        if (is_array($ret) && !empty($ret['id'])) return array('postID'=>$ret['id'], 'isPosted'=>1, 'postURL'=>$ret['url'], 'pDate'=>date('Y-m-d H:i:s')); 
          else { $badOut['Error'].= "Error: ".print_r($ret, true); return $badOut;}        
      } 
      //## Return      
      if (is_array($ret) && $ret['post_id']!='') {
         return array('postID'=>$ret['post_id'], 'isPosted'=>1, 'postURL'=>$ret['post_id'], 'pDate'=>date('Y-m-d H:i:s'));          
      } else { $badOut['Error'] .= print_r($ret, true); 
         return $badOut;
      }
    }
}}
?>