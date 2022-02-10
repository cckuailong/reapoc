<?php    
//## NextScripts FriendFeed Connection Class
$nxs_snapAPINts[] = array('code'=>'VK', 'lcode'=>'vk', 'name'=>'VK.Com');

if (!class_exists("nxs_class_SNAP_VK")) { class nxs_class_SNAP_VK {
    
    var $ntCode = 'VK';
    var $ntLCode = 'vk';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array();
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }    
    function nxs_uplImgtoVK($imgURL, $options){
      $postUrl = 'https://api.vk.com/method/photos.getWallUploadServer?gid='.(str_replace('-','',$options['pgIntID'])).'&access_token='.$options['appAuthToken'];
      $response = nxs_remote_get($postUrl);  if(is_nxs_error($response)) return "Error: URL:".$postUrl." | ". print_r($response, true); $thumbUploadUrl = $response['body'];    
      if (!empty($thumbUploadUrl)) { $thumbUploadUrlObj = json_decode($thumbUploadUrl); $VKuploadUrl = $thumbUploadUrlObj->response->upload_url; }   // prr($thumbUploadUrlObj); echo "UURL=====-----";
      if (!empty($VKuploadUrl)) {    
        // if (stripos($VKuploadUrl, '//pu.vkontakte.ru/c')!==false) { $c = 'c'.CutFromTo($VKuploadUrl, '.ru/c', '/'); $VKuploadUrl = str_ireplace('/pu.','/'.$c.'.',str_ireplace($c.'/','',$VKuploadUrl)); }
        $remImgURL = urldecode($imgURL); $urlParced = pathinfo($remImgURL); $remImgURLFilename = $urlParced['basename']; $imgData = nxs_remote_get($remImgURL); 
        if(is_nxs_error($imgData) || empty($imgData['body']) || (!empty($imgData['headers']['content-length']) && (int)$imgData['headers']['content-length']<200) || 
          $imgData['headers']['content-type'] == 'text/html' ||  $imgData['response']['code'] == '403' ) return 'Could not get image ( '.$remImgURL.' ), will post without it - '.print_r($imgData, true); else $imgData = $imgData['body'];        
        $tmp=array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));  
        if (!is_writable($tmp)) { $msg = "Can't upload image to VK. Your temporary folder or file (file - ".$tmp.") is not writable.";
          if (function_exists('wp_upload_dir')) { $uDir = wp_upload_dir(); $tmp = tempnam($uDir['path'], "nx"); 
            if (!is_writable($tmp)) return $msg." Your UPLOADS folder or file (file - ".$tmp.") is not writable. ";
          } else return $msg;
        } rename($tmp, $tmp.='.png'); register_shutdown_function(create_function('', "unlink('{$tmp}');")); file_put_contents($tmp, $imgData); 
        $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $VKuploadUrl); curl_setopt($ch, CURLOPT_POST, 1); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        global $nxs_skipSSLCheck; if ($nxs_skipSSLCheck===true) curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        if (function_exists('curl_file_create')) { $file  = curl_file_create($tmp); curl_setopt($ch, CURLOPT_POSTFIELDS, array('photo' => $file)); } 
          else curl_setopt($ch, CURLOPT_POSTFIELDS, array('photo' => '@' . $tmp)); 
        $response = curl_exec($ch); $errmsg = curl_error($ch); curl_close($ch); $uploadResultObj = json_decode($response); // prr($response); //prr($uploadResultObj);      
        if (!empty($uploadResultObj->server) && !empty($uploadResultObj->photo) && !empty($uploadResultObj->hash)) {
          $postUrl = 'https://api.vk.com/method/photos.saveWallPhoto?server='.$uploadResultObj->server.'&photo='.$uploadResultObj->photo.'&hash='.$uploadResultObj->hash.'&gid='.(str_replace('-','',$options['pgIntID'])).'&access_token='.$options['appAuthToken'];
          $response = nxs_remote_get($postUrl);  if(is_nxs_error($response)) return "Error: URL:".$postUrl." | ". print_r($response, true);          
          $resultObject = json_decode($response['body']); //prr($resultObject);
          if (isset($resultObject) && isset($resultObject->response[0]->id)) { return $resultObject->response[0]; } else { return 'Image Upload Error'; }
        }
      }
    }
    function doPostToNT($options, $message){ $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>''); $atts = array(); //prr($message); die();
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (empty($options['imgSize'])) $options['imgSize'] = '';
      if (!isset($options['appAuthToken']) || trim($options['appAuthToken'])=='')  { $badOut['Error'] = 'Not Configured'; return $badOut; }                  
      //## Format
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); $urlToGo = (!empty($message['url']))?$message['url']:'';      
      $postType = $options['postType']; //$link = urlencode($link); $desc = urlencode(substr($msg, 0, 500));        
      if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = '';
      $msgOpts = array(); $msgOpts['uid'] =  $options['pgID']; // if ($link!='') $msgOpts['link'] = $link;            
      if (!empty($message['videoURL']) && $postType=="I") { $postType='A';  $urlToGo=$message['videoURL']; $msgOpts['vID'] = $vids[0]; }  
      if ($postType=='I' && trim($imgURL)=='') $postType='T';  $msgOpts['type'] = $postType;   
      if ($postType=='I') { $imgUpld = $this->nxs_uplImgtoVK($imgURL, $options); if (is_object($imgUpld)) { $imgID = $imgUpld->id; $atts[] = $imgID; } else  $badOut['Error'] .= '-=IMG ERROR=- '.print_r($imgUpld, true); }
      
      if($postType=='A') $atts[] = $urlToGo; if (is_array($atts)) $atts = implode(',', $atts); $postUrl = 'https://api.vk.com/method/wall.post'; $msg = strip_tags($msg);
      $postArr = array('owner_id'=>$options['pgIntID'], 'access_token'=>$options['appAuthToken'], 'from_group'=>'1', 'message'=>$msg, 'attachment'=>$atts);        
      $hdrsArr =  nxs_getNXSHeaders('https://api.vk.com',true); $advSet = nxs_mkRemOptsArr($hdrsArr, '', $postArr); $response = nxs_remote_post($postUrl, $advSet); // prr($advSet); prr($response);        
      if ( is_nxs_error($response) || (is_object($response) && (isset($response->errors))) || (is_array($response) && stripos($response['body'],'"error":')!==false )) {  $badOut['Error'] .= 'Error: '. print_r($response, true); } 
        else { $respJ = json_decode($response['body'], true);  $ret = $options['pgIntID'].'_'.$respJ['response']['post_id']; }
      if (isset($ret) && $ret!='') return array('postID'=>$ret, 'isPosted'=>1, 'postURL'=>'http://vk.com/wall'.$ret, 'pDate'=>date('Y-m-d H:i:s'), 'msg'=>$badOut['Error']);       
      return $badOut;      
   }    
}}
?>