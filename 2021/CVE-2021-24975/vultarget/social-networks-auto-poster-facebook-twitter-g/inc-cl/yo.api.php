<?php    
//## NextScripts Medium Connection Class

/* 
1. Options

nName - Nickname of the account [Optional] (Presentation purposes only - No affect on functionality)
postType - A or T - "Attached link" or "Text"

2. Post Info

url
text

*/
$nxs_snapAPINts[] = array('code'=>'YO', 'lcode'=>'yo', 'name'=>'Yo');

if (!class_exists("nxs_class_SNAP_YO")) { class nxs_class_SNAP_YO {
    
    var $ntCode = 'YO';
    var $ntLCode = 'yo';
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array(); // return false;
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }

    function doPostToNT($options, $message){ global $nxs_urlLen; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['apiKey'])) { $badOut['Error'] = 'Not Authorized'; return $badOut; }      
      
      //## Format Post
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message);
      //if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message);       
      //if ($options['inclTags']=='1') $tags = nsTrnc($message['tags'], 195, ',', ''); else $tags = ''; 
      
      //## Make Post            
            
      $msg = str_replace('&amp;#039;', "'", $msg);  $msg = str_replace('&#039;', "'", $msg);  $msg = str_replace('#039;', "'", $msg);  $msg = str_replace('#039', "'", $msg);
      $msg = str_replace('&amp;#8217;', "'", $msg); $msg = str_replace('&#8217;', "'", $msg); $msg = str_replace('#8217;', "'", $msg); $msg = str_replace('#8217', "'", $msg);
      $msg = str_replace('&amp;#8220;', '"', $msg); $msg = str_replace('&#8220;', '"', $msg); $msg = str_replace('#8220;', '"', $msg); $msg = str_replace('#8220', "'", $msg);
      $msg = str_replace('&amp;#8221;', '"', $msg); $msg = str_replace('&#8221;', '"', $msg); $msg = str_replace('#8221;', '"', $msg); $msg = str_replace('#8221', "'", $msg);
      $msg = str_replace('&amp;#8212;', '-', $msg); $msg = str_replace('&#8212;', '-', $msg); $msg = str_replace('#8212;', '-', $msg); $msg = str_replace('#8212', "-", $msg); 
      
      $msg = nsTrnc($msg, 30);
      
      $data = array( 'api_token'=>$options['apiKey'], 'text'=>$msg, 'link'=>$message['url'], 'response_pair'=>'Ok.Got it');
      $hdrsArr = array(); $hdrsArr['Content-Type']='application/x-www-form-urlencoded'; $hdrsArr['Accept-Charset']='utf-8';   
      $hdrsArr['Cache-Control']='max-age=0'; $hdrsArr['Connection']='keep-alive'; $hdrsArr['Referer']=''; 
      $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.54 Safari/537.36'; 
      
      global $wpdb; $gCnt = 0; $bCnt = 0;
      $sql = "SELECT meta_value, user_id FROM $wpdb->usermeta WHERE meta_key = 'nxs_yo'";       
      $users = $wpdb->get_results($sql);
      foreach ($users as $user) { $data['username'] = $user->meta_value;//  echo $user->user_id.'|'.$user->meta_value;
        $advSet = nxs_mkRemOptsArr($hdrsArr, '', $data); $rep = nxs_remote_post('http://api.justyo.co/yo/', $advSet); // prr($advSet); prr($rep);      
        $jsRep = json_decode($rep['body'], true); if (!empty($jsRep['success'])) $gCnt++; else $bCnt++;   // $hddr = print_r($rep['headers'], true);  $limit = trim(CutFromTo($hddr, '[x-ratelimit-remaining] =>', '[')); prr($limit);        
      } $outMsg = 'Yo. Sent to '.$gCnt.' users.' . (!empty($bCnt)?('Failed to send to '.$bCnt.' users '):'');
      return array('postID'=>'', 'isPosted'=>1, 'postURL'=>'', 'pDate'=>date('Y-m-d H:i:s'), 'msg' => $outMsg); 
    }  
    
}}
?>