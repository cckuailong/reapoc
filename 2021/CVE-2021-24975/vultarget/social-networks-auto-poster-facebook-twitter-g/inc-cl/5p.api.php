<?php    
//## NextScripts 500px Connection Class

/* 
1. Options

nName - Nickname of the account [Optional] (Presentation purposes only - No affect on functionality)
postType - A or T - "Attached link" or "Text"

2. Post Info

url
text

*/
$nxs_snapAPINts[] = array('code'=>'5P', 'lcode'=>'5p', 'name'=>'500px');

if (!class_exists("nxs_class_SNAP_5P")) { class nxs_class_SNAP_5P {
    
    var $ntCode = 'XI';
    var $ntLCode = 'xi';
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array(); // return false;
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }

    function doPostToNT($options, $message){ global $nxs_urlLen; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['accessToken']) || trim($options['accessToken'])=='') { $badOut['Error'] = 'Not Authorized'; return $badOut; }      
      if (empty($options['imgSize'])) $options['imgSize'] = '';
      //## Format Post
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message);
      if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message); 
      if ($options['inclTags']=='1') $tags = nsTrnc($message['tags'], 195, ',', ''); else $tags = ''; if (empty($options['imgSize'])) $options['imgSize'] = 'original';
      //## Make Post            
      if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = ''; // $postType = $options['postType'];       
      
      $tum_oauth = new nxs_OAuthBaseCl($options['appKey'], $options['appSec'], $options['accessToken'], $options['accessTokenSec']);
      $tum_oauth->baseURL = 'https://api.500px.com'; 
      
      $msg = str_replace('&amp;#039;', "'", $msg);  $msg = str_replace('&#039;', "'", $msg);  $msg = str_replace('#039;', "'", $msg);  $msg = str_replace('#039', "'", $msg);
      $msg = str_replace('&amp;#8217;', "'", $msg); $msg = str_replace('&#8217;', "'", $msg); $msg = str_replace('#8217;', "'", $msg); $msg = str_replace('#8217', "'", $msg);
      $msg = str_replace('&amp;#8220;', '"', $msg); $msg = str_replace('&#8220;', '"', $msg); $msg = str_replace('#8220;', '"', $msg); $msg = str_replace('#8220', "'", $msg);
      $msg = str_replace('&amp;#8221;', '"', $msg); $msg = str_replace('&#8221;', '"', $msg); $msg = str_replace('#8221;', '"', $msg); $msg = str_replace('#8221', "'", $msg);
      $msg = str_replace('&amp;#8212;', '-', $msg); $msg = str_replace('&#8212;', '-', $msg); $msg = str_replace('#8212;', '-', $msg); $msg = str_replace('#8212', "-", $msg); 
      
      
      $postArr = array('name' =>$msgT, 'description' =>$msg, 'tags'=>$tags, 'privacy'=>'0', 'category'=>$options['cat']); 
      $postinfo = $tum_oauth->makeReq('https://api.500px.com/v1/photos', $postArr, 'POST');// prr($options['appAppUserID']); prr($postArr); prr($postinfo, 'POSTINFO');       
      $uplk = $postinfo['upload_key']; $upid = $postinfo['photo']['id'];      
      $url = 'https://upload.500px.com/v1/upload';  $fields = array( 'photo_id' => $upid, 'upload_key' => $uplk, 'consumer_key' => $options['appKey'], 'access_key' => $options['accessToken'] ); $imgRes = nxs_curlUploadImg($imgURL, $url, $fields, 'file');
      if (!empty($imgRes) && !empty($imgRes['body']) && stripos($imgRes['body'], '"error":"None')!==false) {  
         /*/## Gallery
         $uinfo = $tum_oauth->makeReq('https://api.500px.com/v1/users/'.$options['appAppUserID'].'/galleries/'.$options['gal'], ''); 
         if (!empty($uinfo) && !empty($uinfo['gallery'])) $gid = $uinfo['gallery']['id']; $postGArr = json_encode(array('add' =>array('after'=>array('id'=>'161754155'),'photos'=>array($upid)))); prr($postGArr); // {"add":{"photos":[161777661]}}
         $postGinfo = $tum_oauth->makeReq('https://api.500px.com/v1/users/'.$options['appAppUserID'].'/galleries/'.$gid.'/items', $postGArr, 'PUT'); prr($postGinfo);
         */
         return array('postID'=>$upid, 'isPosted'=>1, 'postURL'=>'https://www.500px.com'.$postinfo['photo']['url'], 'pDate'=>date('Y-m-d H:i:s'));  
      } else $badOut['Error'] .= print_r($imgRes, true)." <br/> Code: ".print_r($postinfo, true);
      return $badOut;
    }  
    
}}
?>