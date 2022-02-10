<?php    
//## NextScripts weibo Connection Class

/* 
1. Options

nName - Nickname of the account [Optional] (Presentation purposes only - No affect on functionality)
postType - A or T - "Attached link" or "Text"

2. Post Info

url
text

*/
$nxs_snapAPINts[] = array('code'=>'OK', 'lcode'=>'ok', 'name'=>'weibo');

if (!class_exists("nxs_class_SNAP_OK")) { class nxs_class_SNAP_OK {
    
    var $ntCode = 'OK';
    var $ntLCode = 'ok';
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array(); // return false;
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }
       
    
    function doPostToNT($options, $message){ global $nxs_urlLen; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');     
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }            
      if (empty($options['imgSize'])) $options['imgSize'] = '';
      //## Format Post
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); $msg = strip_tags($msg); $msg = nxs_decodeEntitiesFull($msg);  
      //if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message); 
      if ($options['inclTags']=='1') $tags = nsTrnc($message['tags'], 195, ',', ''); else $tags = ''; if (empty($options['imgSize'])) $options['imgSize'] = 'original';
      //## Make Post            
      if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = '';  //$postType = $options['postType'];       
      
      $baseURL = 'https://api.ok.ru/fb.do'; $jsonArr = array('media'=>array()); $jsonArr['media'][] = array('type'=>'text', 'text'=>$msg); 
      if ($options['postType']=='A') $jsonArr['media'][] = array('type'=>'link', 'url'=>$message['url']);
      if ($options['postType']=='I') {  $toSigStr = ''; $getArr = array('application_key'=>$options['appKey'], 'format'=>'json', 'gid'=>$options['gid'], 'method'=>'photosV2.getUploadUrl');      
          foreach ($getArr as $k=>$v) $toSigStr .= $k.'='.$v; $secKey = md5($options['access_token'].$options['appSec']); $sig = md5($toSigStr.$secKey);  $getArr['sig'] = $sig; $getArr['access_token'] = $options['access_token'];           
          $getStr = http_build_query($getArr); $hdrsArr = array(); $advSet = nxs_mkRemOptsArr($hdrsArr, ''); $rep = nxs_remote_get($baseURL.'?'.$getStr, $advSet); if(is_nxs_error($rep))  $badOut['Error'] .= 'IMAGE URL #1 ERROR: '. print_r($rep, true); 
          $cont = $rep['body']; if (empty($cont) || stripos($cont, 'error')!==false) { $rep['body']=''; $badOut['Error'] .= 'IMAGE URL ERROR: '. print_r($cont, true)."\r\n<br/>". print_r($rep, true);  } $cont = json_decode($cont, true);
          $uplURL = $cont['upload_url']; $phID = $cont['photo_ids']['0']; $fields = array(); $imgRes = nxs_curlUploadImg($imgURL, $uplURL, $fields, 'file'); 
          $imgRes = $imgRes['body']; if (empty($imgRes) || stripos($imgRes, 'error')!==false) $badOut['Error'] .= 'IMAGE UPLOAD ERROR: ('.$imgURL.') '. print_r($imgRes, true)."\r\n<br/>"; $imgRes = json_decode($imgRes, true); 
          if (!empty($imgRes['photos'])) { $phID = $imgRes['photos'][$phID]['token'];  $jsonArr['media'][] = array('type'=>'photo', 'list'=>array(array('id'=>$phID))); } // prr($cont); prr($jsonArr); //die();
      }  $jsonToPost = json_encode($jsonArr); //prr($jsonToPost);      
      $toSigStr = ''; $getArr = array('application_key'=>$options['appKey'], 'attachment'=>$jsonToPost, 'format'=>'json', 'gid'=>$options['gid'], 'method'=>'mediatopic.post', 'text_link_preview'=>'false', 'type'=>'GROUP_THEME');      
      foreach ($getArr as $k=>$v) $toSigStr .= $k.'='.$v; $secKey = md5($options['access_token'].$options['appSec']); $sig = md5($toSigStr.$secKey);  $getArr['sig'] = $sig; $getArr['access_token'] = $options['access_token']; 
      $getStr = http_build_query($getArr); $hdrsArr = array(); $advSet = nxs_mkRemOptsArr($hdrsArr, ''); $rep = nxs_remote_get($baseURL.'?'.$getStr, $advSet); if(is_nxs_error($rep)) { $badOut['Error'] .= 'POST #1 ERROR: '. print_r($rep, true); return $badOut; }    
      $cont = $rep['body']; if (empty($cont) || stripos($cont, 'error')!==false) { $rep['body']=''; $badOut['Error'] .= 'POST ERROR: '. print_r($cont, true)."\r\n<br/>". print_r($rep, true);  return $badOut; } $cont = str_replace('"','',$cont);
      return array('postID'=>$cont, 'isPosted'=>1, 'postURL'=>'https://ok.ru/group/'.$options['gid'].'/topic/'.$cont, 'pDate'=>date('Y-m-d H:i:s'), 'msg'=>$badOut['Error']);
    }  
    
}}
?>