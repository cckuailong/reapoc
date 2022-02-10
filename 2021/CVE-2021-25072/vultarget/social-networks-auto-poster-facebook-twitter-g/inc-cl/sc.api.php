<?php    
//## NextScripts App.net Connection Class

/* 
1. Options

nName - Nickname of the account [Optional] (Presentation purposes only - No affect on functionality)
rdUName - Reddit User Name
rdPass - Reddit User Passord
rdSubReddit - Name of the Sub-Reddit
postType - A or T - "Attached link" or "Text"

rdTitleFormat
rdTextFormat

2. Post Info

url
title - [up to 300 characters long] - title of the submission
text

*/
$nxs_snapAPINts[] = array('code'=>'SC', 'lcode'=>'sc', 'name'=>'Scoop.It');

if (!class_exists("nxs_class_SNAP_SC")) { class nxs_class_SNAP_SC {
    
    var $ntCode = 'SC';
    var $ntLCode = 'sc';
    
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
      if (!empty($message['pText'])) $text = $message['pText']; else $text = nxs_doFormatMsg($options['msgFormat'], $message);
      if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message); 
      //## Make Post            
      if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = '';  $postType = $options['postType'];       
      
      require_once('apis/scOAuth.php');   $tum_oauth = new wpScoopITOAuth($options['appKey'], $options['appSec'], $options['accessToken'], $options['accessTokenSec']);
      $tiID = $tum_oauth->makeReq('http://www.scoop.it/api/1/topic', array('urlName'=>$options['topicURL']));  
      if (!empty($tiID) && is_array($tiID) && !empty($tiID['topic']) && !empty($tiID['topic']['id'])) $tiID = $tiID['topic']['id']; else { $badOut['Error'] .= print_r($tiID, true); return $badOut; }
      $postArr = array('action'=>'create', 'title'=>$msgT, 'content'=>$text, 'url'=>$postType=='A'?$message['url']:'', 'imageUrl'=>(($postType=='I' || $postType=='A') && !empty($imgURL))?$imgURL:'', 'topicId'=>$tiID);  
      $postinfo = $tum_oauth->makeReq('http://www.scoop.it/api/1/post', $postArr, 'POST'); // prr($postinfo);
      
      if (is_array($postinfo) && isset($postinfo['post'])) { $apNewPostID = $postinfo['post']['id']; $apNewPostURL = $postinfo['post']['scoopUrl']; 
        if ($options['inclTags']=='1') { $postArr = array('action'=>'edit', 'tag'=>$message['tags'], 'id'=>$apNewPostID);  
          $postinfo = $tum_oauth->makeReq('http://www.scoop.it/api/1/post', $postArr, 'POST'); 
        }
              
      } $code = $tum_oauth->http_code;
      if (!empty($apNewPostID)) {         
         return array('postID'=>$apNewPostID, 'isPosted'=>1, 'postURL'=>$apNewPostURL, 'pDate'=>date('Y-m-d H:i:s'));          
      } else { $badOut['Error'] .= print_r($postinfo, true)." Code:".$tum_oauth->http_code; 
        return $badOut;
      }
      return $badOut;
    }  
    
}}
?>