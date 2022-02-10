<?php    
//## NextScripts Facebook Connection Class
$nxs_snapAPINts[] = array('code'=>'FB', 'lcode'=>'fb', 'name'=>'Facebook');

if (!class_exists("nxs_class_SNAP_FB")) { class nxs_class_SNAP_FB {
    
    var $ntCode = 'FB';
    var $ntLCode = 'fb';
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array(); //return false;
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }
    function doPostToNT($options, $message){ $badOut = array('Warning'=>'', 'Error'=>''); $wprg = array('sslverify'=>false, 'timeout' => 30); //prr($options);
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (empty($options['accessToken']) && empty($options['pageAccessToken']) && empty($options['tpt'])) { $badOut['Error'] = 'No Auth Token Found/Not configured'; return $badOut; }
      //## Make Post
      if (!empty($options['accessToken'])) if (!isset($options['pageAccessToken']) || trim($options['pageAccessToken'])=='') $options['pageAccessToken'] = $options['accessToken'];
      
      //## Get URL info.              
      if ($options['postType']!='I' && $options['postType']!='T'){ $url=$message['url']; //#### Let's ask FB to scrape/re-scrape it.
        $flds=array('id'=>$url, 'scrape'=>'true', 'access_token'=>$options['accessToken'], 'method'=>'post', 'limit'=>250); if (empty($options['tpt'])) $flds['appsecret_proof'] = hash_hmac('sha256', $options['accessToken'], $options['appSec']); sleep(2); 
        $advSet = nxs_mkRemOptsArr(nxs_getNXSHeaders(), '',$flds);  $response =  nxs_remote_post('https://graph.facebook.com', $advSet);
        
        if (is_nxs_error($response)) $badOut['Error'] = "Error(URL-Info): ". print_r($response, true); else { $response = json_decode($response['body'], true);     //  prr($response);     die();        
            global $plgn_NS_SNAutoPoster; if (!empty($plgn_NS_SNAutoPoster)) {
               $gOptions = $plgn_NS_SNAutoPoster->nxs_options;  {if (isset($gOptions['extDebug']) && $gOptions['extDebug']=='1') nxs_LogIt( 'S', 'Facebook URL Debug', 'FB', '', 'Facebook OG Metatags URL Scrape Info: ', print_r($response, true));}                 
            }            
            if (!empty($response['description'])) $message['urlDescr'] = $response['description'];  if (!empty($response['title'])) $message['urlTitle'] =  $response['title'];
            if (!empty($response['site_name'])) $message['siteName'] = $response['site_name']; elseif ($message['siteName']=='') $message['siteName'] = $message['title'];
            if (!empty($response['image'][0]['url'])) $message['imageURL'] = $response['image'][0]['url'];
            $message['urlCaption'] = '';
        }
      }            
      //prr($message); die();
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); 
      $imgURL = nxs_getImgfrOpt($message['imageURL']); $fbWhere = 'feed';       
      if ($options['imgUpl']!='2') $options['imgUpl'] = 'T'; else $options['imgUpl'] = 'A';      
      if (!empty($options['fbURL']) && stripos($options['fbURL'], '/groups/')!=false) $options['destType'] = 'gr';
      
      if (!empty($options['destType']) && $options['destType'] == 'pr') $page_id = $options['authUser']; else $page_id = $options['pgID'];        
      $msg = strip_tags($msg); $msg = str_ireplace('&lt;(")','<(")', $msg); //## FB Smiles FIX 3
      if (substr($msg, 0, 1)=='@') $msg = ' '.$msg; // ERROR] couldn't open file fix
      
      //## Own App Post
      if (!empty($options['pageAccessToken'])) { $mssg = array('access_token'=>$options['pageAccessToken'], 'method'=>'post', 'message'=>$msg);
        if (empty($options['tpt'])) $mssg['appsecret_proof'] = hash_hmac('sha256', $options['pageAccessToken'], $options['appSec']); //prr($mssg);
        
        if ($options['postType']=='I' && trim($imgURL)=='') $options['postType']='T';
        if ($options['postType']=='A' && !(preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $message['url']))) { 
            $badOut['Warning'] = 'Unvalid URL: '.$message['url'].'| Will be posting as text message'; $options['postType']='T'; 
        } 
        if ($options['postType']=='A' || $options['postType']=='') { 
          $message['urlTitle'] = nsTrnc($message['urlTitle'], 250, " ", "...");
          $attArr = array('name' => nsTrnc($message['urlTitle'], 250, " ", "..."), 'caption' => $message['urlCaption'], 'link' =>$message['url'], 'description' => $message['urlDescr']); $mssg = array_merge($mssg, $attArr); ;           
          //if ($options['attachType']=='A') $mssg['actions'] = json_encode(array('name' => nsTrnc($message['siteName'], 250, " ", "..."), 'link' =>$message['url']));
          if (trim($imgURL)!='') $mssg['picture'] = $imgURL;  //if (trim($message['videoURL'])!='') $mssg['source'] = $message['videoURL'];        
        } 
        elseif ($options['postType']=='I') { /* $facebook->setFileUploadSupport(true); */ $fbWhere = 'photos'; $mssg['url'] = $imgURL;  $mssg['caption'] =  $mssg['message']; 
          if ($options['imgUpl']=='T') { //## Try to Post to TImeline
          
            $aacct = array('access_token'=>$options['pageAccessToken'], 'method'=>'get'); if (empty($options['tpt'])) $aacct['appsecret_proof'] = hash_hmac('sha256', $options['pageAccessToken'], $options['appSec']); 
            $res = nxs_remote_get( "https://graph.facebook.com/$page_id/albums?".http_build_query($aacct, null, '&'), nxs_mkRemOptsArr(nxs_getNXSHeaders())); 
            if (is_nxs_error($res) || empty($res['body'])) $badOut['Error'] = ' [ERROR(Albums)] '.print_r($res, true); else {
              $albums = json_decode($res['body'], true);  if (empty($albums)) $badOut['Error'] .= "JSON ERROR (Albums): ".print_r($res, true); else { 
                if (is_array($albums) && is_array($albums["data"])) foreach ($albums["data"] as $album) { if (!empty($album["type"]) && $album["type"] == "wall") { $chosen_album = $album; break;}}
                if (isset($chosen_album) && isset($chosen_album["id"])) $page_id = $chosen_album["id"];
              }
            }
          }        
        }  if (!empty($mssg['name']) && function_exists('mb_strcut')) { mb_internal_encoding('UTF-8'); $mssg['name'] = mb_strcut($mssg['name'], 0, 250); }
        //## Actual Post                
        $destURL = "https://graph.facebook.com/$page_id/".$fbWhere; //  prr($destURL);  prr($mssg); die();
        $response = nxs_remote_post( $destURL, nxs_mkRemOptsArr(nxs_getNXSHeaders('',true),'',$mssg) ); // prr($response);
      }     
      
      if (is_nxs_error($response) || empty($response['body'])) return "ERROR: ".print_r($response, true);
      $res = json_decode($response['body'], true); if (empty($res)) return "JSON ERROR: ".print_r($response, true);
      if (!empty($res['error'])) if (!empty($res['error']['message'])) { $badOut['Error'] .= $res['error']['message']; //## Some Known Errors
        if (stripos($res['error']['message'], 'This API call requires a valid app_id')!==false) { 
            if ( !is_numeric($page_id) && stripos($options['fbURL'], '/groups/')!=false) $badOut['Error'] .= ' [ERROR] Unrecognized Facebook Group ID. Please use numeric ID. Please see <a href="http://gd.is/f412">FAQ 4.12</a>'; 
              else $badOut['Error'] .= " [ERROR] (invalid app_id) Authorization Error. <br/>\r\n<br/>\r\n Possible Reasons: <br/>\r\n 1. Your app is not authorized. Please go to the Plugin Settings - Facebook and authorize it.<br/>\r\n 2. The current authorized user have no rights to post to the specified page. Please login to Facebook as the correct user and Re-Authorize the Plugin.<br/>\r\n 3. You clicked 'Skip' or unchecked the 'Manage Pages' or 'Post on your behalf' permissions when Authorization wizard asked you. Please Re-Authorize the Plugin<br/>\r\n"; 
        }
        if (stripos($res['error']['message'], 'Some of the aliases you requested do not exist')!==false) $badOut['Error'] .= '| Please check what do you have in the "Facebook URL" field.';
        if (stripos($res['error']['message'], 'Unsupported post request')!==false) $badOut['Error'] .= "<br/>\r\n".'| Are you posting to a secret or closed group? Please see: <a target="_blank" href="http://gd.is/fbe2">http://gd.is/fbe2</a>';
        if (stripos($res['error']['message'], 'The target user has not authorized this action')!==false) $badOut['Error'] .= '| Please Authorize the plugin from the plugin settings Page - Facebook.';        
        if (stripos($res['error']['message'], 'The user has not authorized the application')!==false && $options['destType'] == 'gr') $badOut['Error'] .= '| Are you posting to a secret or closed group? Please see: <a target="_blank" href="http://gd.is/fbe2">http://gd.is/fbe2</a>';
        
        return $badOut;          
      } else return print_r($res['error'], true);
      if (empty($res['id'])) return print_r($res, true);
      //## All Good!
      $pgID = (isset($res['post_id']) && strpos($res['post_id'],'_')!==false)?$res['post_id']:$res['id']; $pgg = explode('_', $pgID); $postID = $pgg[1];
      $pgURL = 'http://www.facebook.com/'.$options['pgID'].'/posts/'.$postID; 
      return array('isPosted'=>'1', 'postID'=>$pgID, 'postURL'=>$pgURL, 'pDate'=>date('Y-m-d H:i:s'), 'log'=>$badOut);      
    }
}}
?>