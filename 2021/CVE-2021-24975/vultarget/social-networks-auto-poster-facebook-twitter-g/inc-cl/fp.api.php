<?php    
//## NextScripts Flipboard Connection Class

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
$nxs_snapAPINts[] = array('code'=>'FP', 'lcode'=>'fp', 'name'=>'Flipboard');

if (!function_exists("nxs_getFPHeaders")) {  function nxs_getFPHeaders($ref, $org='', $post=false, $aj=false){ $hdrsArr = array(); 
 $hdrsArr['Cache-Control']='max-age=0'; $hdrsArr['Connection']='keep-alive'; $hdrsArr['Referer']=$ref;
 $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.22 Safari/537.36'; 
 if($post==='j') $hdrsArr['Content-Type']='application/json;charset=UTF-8'; elseif($post===true) $hdrsArr['Content-Type']='application/x-www-form-urlencoded';
 if($aj===true) $hdrsArr['X-Requested-With']='XMLHttpRequest';  if ($org!='') $hdrsArr['Origin']=$org; 
 $hdrsArr['Accept']='text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';// $hdrsArr['DNT']='1';
 if (function_exists('gzdeflate')) $hdrsArr['Accept-Encoding']='gzip,deflate,sdch'; 
 $hdrsArr['Accept-Language']='en-US,en;q=0.8'; return $hdrsArr; 
}}

if (!class_exists("nxs_class_SNAP_FP")) { class nxs_class_SNAP_FP {
    
    var $ntCode = 'FP';
    var $ntLCode = 'fp';
    
    function createFile($imgURL) {
      $remImgURL = urldecode($imgURL); $urlParced = pathinfo($remImgURL); $remImgURLFilename = $urlParced['basename']; 
      $imgData = nxs_remote_get($remImgURL); if (is_nxs_error($imgData)) { $badOut['Error'] = print_r($imgData, true)." - ERROR"; return $badOut; }          
      $imgData = $imgData['body'];
      $tmp=array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));  
      if (!is_writable($tmp)) return "Your temporary folder or file (file - ".$tmp.") is not witable. Can't upload images to Flickr";
      rename($tmp, $tmp.='.png'); register_shutdown_function(create_function('', "unlink('{$tmp}');"));       
      file_put_contents($tmp, $imgData); if (!$tmp) return 'You must specify a path to a file'; if (!file_exists($tmp)) return 'File path specified does not exist';
      if (!is_readable($tmp)) return 'File path specified is not readable';      
      //  $data['name'] = basename($tmp);
      return "@$tmp";
      
    }
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array(); // return false;
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }
    
    function doPostToNT($options, $message){ global $nxs_urlLen; $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>''); 
      if (!class_exists("nxsAPI_FP")){ $badOut['Error'] .= "Flipboard API Library not found"; return $badOut; } 
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['uPass']) || trim($options['uPass'])=='') { $badOut['Error'] = 'Not Authorized'; return $badOut; }      
      if (empty($options['imgSize'])) $options['imgSize'] = '';
      //## Get Saved Login Info
      if (function_exists('nxs_getOption')) { $opVal = array(); $opNm = 'nxs_snap_fp_'.sha1('nxs_snap_fp'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } 
      //## Format Post
      if (!empty($message['pText'])) $text = $message['pText']; else $text = nxs_doFormatMsg($options['msgFormat'], $message); 
      //## Make Post            
      if (isset($message['imageURL'])) $imgURL = trim(nxs_getImgfrOpt($message['imageURL'], $options['imgSize'])); else $imgURL = '';
      //## Make Post   
      $pass = (substr($options['uPass'], 0, 5)=='n5g9a'||substr($options['uPass'], 0, 5)=='g9c1a'||substr($options['uPass'], 0, 5)=='b4d7s')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];  
      $nt = new nxsAPI_FP(); $nt->debug = false; if (!empty($ck)) $nt->ck = $ck; if (!empty($options['proxy'])&&!empty($options['proxyOn'])){ $nt->proxy['proxy'] = $options['proxy']['proxy']; if (!empty($options['proxy']['up'])) $nt->proxy['up'] = $options['proxy']['up'];};
      $loginErr = $nt->connect($options['uName'], $pass); if ($loginErr) { $badOut['Error'] .= 'Can\'t Connect - '.print_r($loginErr, true); return $badOut; }             
      $post = array('url'=>$message['url'], 'mgzURL'=>$options['mgzURL'], 'imgURL'=>$imgURL, 'text'=>$text ); $ret = $nt->post($post); 
      //## Save Login Info
      if (function_exists('nxs_saveOption')) { if (empty($opVal['ck'])) $opVal['ck'] = ''; if (is_array($ret) && $ret['isPosted']=='1' && $opVal['ck'] != $nt->ck) { $opVal['ck'] = $nt->ck; nxs_saveOption($opNm, $opVal); } }            
      return $ret;
    }      
}}
?>