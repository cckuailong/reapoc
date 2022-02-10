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
$nxs_snapAPINts[] = array('code'=>'AP', 'lcode'=>'ap', 'name'=>'app.net');

if (!class_exists("nxs_class_SNAP_AP")) { class nxs_class_SNAP_AP {
    
    var $ntCode = 'AP';
    var $ntLCode = 'ap';
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array(); // return false;
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }
    function createFile($imgURL, $auth) { $data = array();       
      $remImgURL = urldecode($imgURL); $urlParced = pathinfo($remImgURL); $remImgURLFilename = $urlParced['basename']; 
      $imgData = nxs_remote_get($remImgURL); if (is_nxs_error($imgData)) { $badOut['Error'] = print_r($imgData, true)." - ERROR"; return $badOut; }          
      $imgData = $imgData['body'];
      $tmp=array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));  
      if (!is_writable($tmp)) return "Your temporary folder or file (file - ".$tmp.") is not witable. Can't upload image to App.Net";
      rename($tmp, $tmp.='.png'); register_shutdown_function(create_function('', "unlink('{$tmp}');"));       
      file_put_contents($tmp, $imgData); if (!$tmp) return 'You must specify a path to a file'; if (!file_exists($tmp)) return 'File path specified does not exist';
      if (!is_readable($tmp)) return 'File path specified is not readable';
      if (!array_key_exists('name', $data)) $data['name'] = basename($tmp);
      if (array_key_exists('mime-type', $data)) { $mimeType = $data['mime-type']; unset($data['mime-type']);} else $mimeType = null;
      if (!array_key_exists('kind', $data)) { $test = @getimagesize($tmp); 
        if ($test && array_key_exists('mime', $test)) { $data['kind'] = 'image'; if (!$mimeType) $mimeType = $test['mime']; } else $data['kind'] = 'other';
      }
      if (!$mimeType && function_exists('finfo_open') ) { $finfo = finfo_open(FILEINFO_MIME_TYPE); $mimeType = finfo_file($finfo, $tmp); finfo_close($finfo); }
      if (!$mimeType) return 'Unable to determine mime type of file, try specifying it explicitly';  $data['type'] = "com.nextscripts.photos";      
      if (function_exists('curl_file_create')) $data['content'] = curl_file_create($tmp,$mimeType); else $data['content'] = "@$tmp;type=$mimeType";      
      $url = "https://alpha-api.app.net/stream/0/files?access_token=".$auth; 
      $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $url); curl_setopt($ch, CURLOPT_POST, 1); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      global $nxs_skipSSLCheck; if ($nxs_skipSSLCheck===true) curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data); $response = curl_exec($ch); $errmsg = curl_error($ch); curl_close($ch); //prr($response);
      if ($errmsg!='') return $errmsg; else $response = json_decode($response, true);
      if (!is_array($response) || !isset($response['meta']) || $response['meta']['code']!='200' || $response['data']['file_token']=='') return print_r($response, true);
      return array('id'=>$response['data']['id'], 'file_token'=>$response['data']['file_token'], 'url'=>$response['data']['url']);
    }

    function doPostToNT($options, $message){ global $nxs_urlLen; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['accessToken']) || trim($options['accessToken'])=='') { $badOut['Error'] = 'Not Authorized'; return $badOut; }      
      if (empty($options['imgSize'])) $options['imgSize'] = '';
      //## Format Post
      if (!empty($message['pText'])) $text = $message['pText']; else $text = nxs_doFormatMsg($options['msgFormat'], $message); $text = nsTrnc($text, 256); 
      //## Make Post            
      if (isset($message['imageURL'])) $img = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $img = '';  
      if ($options['attchImg']!=false && $img!='') $remoteImg = $this->createFile($img, $options['accessToken']); $ann = array();   
      if (is_array($remoteImg)) {
         $ann[] = array("type"=>"net.app.core.oembed", "value"=> array("+net.app.core.file"=>array("file_id" => $remoteImg['id'], "file_token" => $remoteImg['file_token'], "format"=> "oembed"))); 
      }
      $url = "https://alpha-api.app.net/stream/0/posts?include_post_annotations=1&access_token=".$options['accessToken'];            
      $flds = array('text' => $text, 'annotations' => $ann); $flds = json_encode($flds); $hdrsArr = array('Content-Type' => 'application/json');  $advSet = nxs_mkRemOptsArr($hdrsArr,'',$flds); $response = nxs_remote_post($url, $advSet);  
      if (is_nxs_error($response)) {  $badOut['Error'] = print_r($response, true)." - ERROR"; return $badOut; } 
      $response = json_decode($response['body'], true); //prr($response); die();      
      //## Check Result
      if (!is_array($response) || !isset($response['meta']) || $response['meta']['code']!='200' || $response['data']['canonical_url']=='') { $badOut['Error'] = print_r($response, true)." - ERROR"; return $badOut; }       
      $apNewPostURL = $response['data']['canonical_url']; $apNewPostID = $response['data']['id'];  
      if ($apNewPostID!='') {         
         return array('postID'=>$apNewPostID, 'isPosted'=>1, 'postURL'=>$apNewPostURL, 'pDate'=>date('Y-m-d H:i:s'));          
      } else { $badOut['Error'] .= print_r($tmhOAuth->response['response'], true)." MSG:".print_r($msg, true); 
        return $badOut;
      }
      return $badOut;
    }  
    
}}
?>