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
$nxs_snapAPINts[] = array('code'=>'WB', 'lcode'=>'wb', 'name'=>'weibo');

if (!class_exists("nxs_class_SNAP_WB")) { class nxs_class_SNAP_WB {
    
    var $ntCode = 'WB';
    var $ntLCode = 'wb';
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array(); // return false;
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }
    
    function toBase($num, $b=62) { $base='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; $r = $num  % $b ; $res = $base[$r]; $q = floor($num/$b);
      while ($q) { $r = $q % $b; $q =floor($q/$b); $res = $base[$r].$res; } return $res;
    }   
    
    function wbCut($msg){ $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
      if(preg_match($reg_exUrl, $msg, $url)) { $u = $url[0]; $msg =  preg_replace($reg_exUrl, "^HRF^^LBW^", $msg);
        $msg = nsTrnc($msg, 140); $msg = str_ireplace("^HRF^^LBW^", $u, $msg);         
      } else $msg = nsTrnc($msg, 140); return $msg;
    }
    
    function doPostToNT($options, $message){ global $nxs_urlLen; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');     
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['accessToken']) || trim($options['accessToken'])=='') { $badOut['Error'] = 'Not Authorized'; return $badOut; }      
      if (empty($options['imgSize'])) $options['imgSize'] = '';
      //## Format Post
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message);
      //if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message); 
      if ($options['inclTags']=='1') $tags = nsTrnc($message['tags'], 195, ',', ''); else $tags = ''; if (empty($options['imgSize'])) $options['imgSize'] = 'original';
      //## Make Post            
      if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = '';  //$postType = $options['postType'];       
                                                                                                                                                                               
      $msg = str_replace('&amp;#039;', "'", $msg);  $msg = str_replace('&#039;', "'", $msg);  $msg = str_replace('#039;', "'", $msg);  $msg = str_replace('#039', "'", $msg);
      $msg = str_replace('&amp;#8217;', "'", $msg); $msg = str_replace('&#8217;', "'", $msg); $msg = str_replace('#8217;', "'", $msg); $msg = str_replace('#8217', "'", $msg);
      $msg = str_replace('&amp;#8220;', '"', $msg); $msg = str_replace('&#8220;', '"', $msg); $msg = str_replace('#8220;', '"', $msg); $msg = str_replace('#8220', "'", $msg);
      $msg = str_replace('&amp;#8221;', '"', $msg); $msg = str_replace('&#8221;', '"', $msg); $msg = str_replace('#8221;', '"', $msg); $msg = str_replace('#8221', "'", $msg);
      $msg = str_replace('&amp;#8212;', '-', $msg); $msg = str_replace('&#8212;', '-', $msg); $msg = str_replace('#8212;', '-', $msg); $msg = str_replace('#8212', "-", $msg);      $msg = $this->wbCut($msg);       
      $url='https://api.weibo.com/2/statuses/update.json'; $data['access_token'] = $options['accessToken'];  $data['status'] = $msg;        
      if ($options['attchImg']=='1') { $url='https://api.weibo.com/2/statuses/upload.json'; $rep = nxs_curlUploadImg($imgURL, $url, $data, 'pic'); } else { $hdrsArr = array(); $advSet = nxs_mkRemOptsArr($hdrsArr, '', $data); $rep = nxs_remote_post($url, $advSet); }
      $cont = json_decode($rep['body'], true); if (empty($cont) || empty($cont['created_at'])) {$badOut['Error'] .= print_r($rep, true);  return $badOut; } 
      $pid = $this->toBase(substr($cont['idstr'],0,2)).$this->toBase(substr($cont['idstr'],2,7)).$this->toBase(substr($cont['idstr'],9));
      return array('postID'=>$pid, 'isPosted'=>1, 'postURL'=>'http://weibo.com/'.$cont['user']['id'].'/'.$pid, 'pDate'=>date('Y-m-d H:i:s'));       
    }  
    
}}
?>