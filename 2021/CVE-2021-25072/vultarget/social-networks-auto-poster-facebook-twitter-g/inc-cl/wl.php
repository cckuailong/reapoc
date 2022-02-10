<?php    
//## NextScripts  Connection Class

//$nxs_snapAvNts[] = array('code'=>'WL', 'lcode'=>'wl', 'name'=>'WaNeLo');





//## Check current  WaNeLo session
if (!function_exists("doCheckWaNeLo")) {function doCheckWaNeLo(){ global $nxs_gCookiesArr, $nxs_gWLUName, $nxs_gTkn, $nxs_gWLBoards; $nxs_gWLUName = ''; $advSettings = array();  //prr($nxs_gCookiesArr); die();
  $contents = getCurlPageX('http://wanelo.com/following','http://wanelo.com/', true, '', false, $advSettings); $nxs_gTkn = CutFromTo($contents, '"authenticity_token" type="hidden" value="', '"');
  if ( stripos($contents, "'alerts-page-subhead-notification'")!==false) { $nxs_gWLUName = CutFromTo($contents, "'alerts-page-subhead-notification'", "/following")."/"; $nxs_gWLUName = CutFromTo($nxs_gWLUName, "/", "/"); }
  if (trim($nxs_gWLUName)!='') return false; else { echo "WTF? NO LOGIN?"; return "No Login"; }
}}
//## Login to WaNeLo
if (!function_exists("doConnectToWaNeLo")) {function doConnectToWaNeLo($email, $pass){ global $nxs_gCookiesArr, $nxs_gTkn, $nxs_gWLUName, $nxs_gWLBoards; $nxs_gCookiesArr = array(); $advSettings = array();// echo "UUU";
  $err = nxsCheckSSLCurl('https://wanelo.com/users/sign_in'); if ($err!==false && $err['errNo']=='60') $advSettings['noSSLSec'] = true;  
  if ($err!==false && stripos($err['errMsg'], 'Protocol https not supported')!==false) return 'Protocol https not supported or disabled in libcurl. Please install or enable OpenSSL. ';  
  $contents = getCurlPageX('http://wanelo.com/users/sign_in', 'http://wanelo.com/users/sign_in', true, '', false, $advSettings);        
  //## GET HIDDEN FIELDS
  $md = array(); $mids = '';  
  $frmTxt = CutFromTo($contents, 'action="https://wanelo.com/users/sign_in','</form>'); $md = array(); $flds  = array();// prr($frmTxt); 
    while (stripos($frmTxt, '<input')!==false){ $inpField = trim(CutFromTo($frmTxt,'<input', '>')); $name = trim(CutFromTo($inpField,'name="', '"'));
     if ( stripos($inpField, '"hidden"')!==false && $name!='' && !in_array($name, $md)) { $md[] = $name; $val = trim(CutFromTo($inpField,'value="', '"')); $flds[$name]= $val; $mids .= "&".$name."=".$val;}
     $frmTxt = substr($frmTxt, stripos($frmTxt, '<input')+8);
    } $flds['user[login]'] = $email; $flds['user[password]'] = $pass; $flds['user[remember_me]'] = '1'; $fldsTxt = build_http_query($flds); $advSettings['Origin'] = 'https://WaNeLo.com';
  //## ACTUAL LOGIN
  $contents = getCurlPageX('https://wanelo.com/users/sign_in?return_to=%2Fusers%2Fsign_in','http://wanelo.com/users/sign_in', false, $fldsTxt, false, $advSettings);   //   echo $fldsTxt; prr($flds); prr($contents); die();
  if ($contents['url']=='http://wanelo.com/users/sign_in') return 'Incorrect Username/Password ';  
  $contents = getCurlPageX('http://wanelo.com/following','http://wanelo.com/', true, '', false, $advSettings);// prr($contents);
  $nxs_gTkn = CutFromTo($contents, '"authenticity_token" type="hidden" value="', '"');
  $nxs_gWLUName = CutFromTo($contents, "'alerts-page-subhead-notification'", "/following")."/"; $nxs_gWLUName = CutFromTo($nxs_gWLUName, "/", "/"); 
  if (trim($nxs_gWLUName)!='') return false; else return "Something wrong";   
}}
//## Get WaNeLo Boards
if (!function_exists("doGetBoardsFromWaNeLo")) {function doGetBoardsFromWaNeLo(){ global $nxs_gCookiesArr, $nxs_gWLBoards, $nxs_gWLUName;
  $contents = getCurlPageX('http://wanelo.com/'.$nxs_gWLUName.'/collections', 'http://wanelo.com/', true, '', false, $advSettings);
  
  $txt = CutFromTo($contents,'href="/okapy/collections"', '<div class="page-links">'); $txta = explode("class='pull-left'>", $txt); //prr($txta);
  foreach ($txta as $txti)if (stripos($txti, "'followers-count'")!==false) { $val = CutFromTo($txti, 'href="','"'); $name = utf8_encode(CutFromTo($txti, '">','<'));
     $items .= '<option value="'.$val.'">'.$name.'</option>'; 
  } $nxs_gWLBoards = $items;  
  return $nxs_gWLBoards;
}}
//## Post to WaNeLo
if (!function_exists("doPostToWaNeLo")) {function doPostToWaNeLo($msg, $imgURL, $lnk, $boardID, $title = '', $price='', $via=''){  global $nxs_gTkn, $nxs_gCookiesArr;  $lnk = urlencode($lnk); $msg = substr($msg, 0, 480); $tgs = '';

$contents = getCurlPageX('http://wanelo.com/following','http://wanelo.com/', true, '', false, $advSettings); 

$nxs_gWLUName = CutFromTo($contents, "'alerts-page-subhead-notification'", "/following")."/"; $nxs_gWLUName = CutFromTo($nxs_gWLUName, "/", "/"); echo "}}}}"; prr($nxs_gWLUName);


$nxs_gTkn = CutFromTo($contents, '"authenticity_token" type="hidden" value="', '"');

$boundary  = uniqid('----WebKitFormBoundary');    

/*
$postData  = '
'."\r\n".'--'.$boundary."\r\n".'
Content-Disposition: form-data; name="utf8"'."\r\n".'
'."\r\n".'
'."\r\n".'
--'.$boundary."\r\n".'
Content-Disposition: form-data; name="authenticity_token"'."\r\n".'
'."\r\n".'
'.$nxs_gTkn."\r\n".'
--'.$boundary."\r\n".'
Content-Disposition: form-data; name="product[url]"'."\r\n".'
'."\r\n".'
http://www.bhphotovideo.com/c/product/892354-REG/Canon_8035b009_EOS_6D_Digital_Camera.html'."\r\n".'
--'.$boundary."\r\n".'
Content-Disposition: form-data; name="product[price]"'."\r\n".'
'."\r\n".'
2400.00'."\r\n".'
--'.$boundary."\r\n".'
Content-Disposition: form-data; name="product[shop]"'."\r\n".'
'."\r\n".'
'."\r\n".'
--'.$boundary."\r\n".'
Content-Disposition: form-data; name="publish_photo_to_fb"'."\r\n".'
'."\r\n".'
false'."\r\n".'
--'.$boundary."\r\n".'
Content-Disposition: form-data; name="product[name]"'."\r\n".'
'."\r\n".'
Canon EOS 6D Digital Camera with Canon 24-105mm f/4.0L IS USM AF Lens'."\r\n".'
--'.$boundary."\r\n".'
Content-Disposition: form-data; name="initial_collection"'."\r\n".'
'."\r\n".'
16321420'."\r\n".'
--'.$boundary."\r\n".'
Content-Disposition: form-data; name="collection_is_new"'."\r\n".'
'."\r\n".'
false'."\r\n".'
--'.$boundary."\r\n".'
Content-Disposition: form-data; name="comment"'."\r\n".'
'."\r\n".'
Canon 6d!'."\r\n".'
--'.$boundary."\r\n".'
Content-Disposition: form-data; name="product[remote_image_url]"'."\r\n".'
'."\r\n".'
http://static.bhphoto.com/images/images345x345/892354.jpg'."\r\n".'
--'.$boundary."\r\n".'
Content-Disposition: form-data; name="commit"'."\r\n".'
'."\r\n".'
Post to Wanelo'."\r\n".'
--'.$boundary.'--';
*/

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~";

$postData  = '--'.$boundary."\r".'Content-Disposition: form-data; name="utf8"'."\r".''."\r".''."\r".'--'.$boundary."\r".'Content-Disposition: form-data; name="authenticity_token"'."\r".''."\r".''.$nxs_gTkn."\r".'--'.$boundary."\r".'Content-Disposition: form-data; name="product[url]"'."\r".''."\r".'http://www.bhphotovideo.com/c/product/680103-USA/Canon_2751B002_EF_70_200mm_f_2_8L_IS.html'."\r".'--'.$boundary."\r".'Content-Disposition: form-data; name="product[price]"'."\r".''."\r".'0.00'."\r".'--'.$boundary."\r".'Content-Disposition: form-data; name="product[shop]"'."\r".''."\r".''."\r".'--'.$boundary."\r".'Content-Disposition: form-data; name="publish_photo_to_fb"'."\r".''."\r".'false'."\r".'--'.$boundary."\r".'Content-Disposition: form-data; name="product[name]"'."\r".''."\r".'Canon EOS 6D Digital Camera with Canon 24-105mm f/4.0L IS USM AF Lens'."\r".'--'.$boundary."\r".'Content-Disposition: form-data; name="initial_collection"'."\r".''."\r".'16321420'."\r".'--'.$boundary."\r".'Content-Disposition: form-data; name="collection_is_new"'."\r".''."\r".'false'."\r".'--'.$boundary."\r".'Content-Disposition: form-data; name="comment"'."\r".''."\r".'Canon 6d!'."\r".'--'.$boundary."\r".'Content-Disposition: form-data; name="product[remote_image_url]"'."\r".''."\r".'http://static.bhphoto.com/images/images345x345/892354.jpg'."\r".'--'.$boundary."\r".'Content-Disposition: form-data; name="commit"'."\r".''."\r".'Post to Wanelo'."\r".'--'.$boundary.'--';

//$headers   = array("Content-Type: multipart/form-data; boundary=$boundary", "Origin: http://wanelo.com", "Content-Length: ".strlen($postData));

$headers   = array("Content-Type: multipart/form-data; boundary=$boundary", "Origin: http://wanelo.com", "Expect:");

$advSettings = array('headers'=>$headers); //prr($advSettings); die();

// die();

$ref = 'http://wanelo.com/p/post?bookmarklet=&images%5B%5D=http%3A%2F%2Fstatic.bhphoto.com%2Fimages%2Fimages200x200%2F680103.jpg&url=http%3A%2F%2Fwww.bhphotovideo.com%2Fc%2Fproduct%2F680103-USA%2FCanon_2751B002_EF_70_200mm_f_2_8L_IS.html&title=Canon%20EF%2070-200mm%20f%2F2.8L%20IS%20II%20USM%20Telephoto%20Zoom%20Lens&price=%240.00&shop=&source=toolbar&ref=http%3A%2F%2Fwww.bhphotovideo.com%2Fc%2Fproduct%2F680103-USA%2FCanon_2751B002_EF_70_200mm_f_2_8L_IS.html';

$contents = getCurlPageX('http://wanelo.com/p', $ref, false, $postData, true, $advSettings); $contents['content'] = ''; prr($contents); prr($postData); die();

 // if (stripos($imgURL, 'youtube.com')!==false || stripos($imgURL, 'youtu.be')!==false) { $tgs = 'http://img.youtube.com/vi/'.str_ireplace('http://youtu.be/','',$imgURL).'/0.jpg'; }
  $fldsTxt = 'caption='.urlencode($msg).'&board='.$boardID.'&tags='.$tgs.'&replies=&buyable='.urldecode($price).'&title='.urldecode($title).'&media_url='.urlencode($imgURL).'&url='.$lnk.'&via='.urldecode($via).'&csrfmiddlewaretoken='.$nxs_gTkn.'&form_url=';  
  if (trim($boardID)=='') return "Board is not Set";  if (trim($imgURL)=='') return "Image is not Set"; // prr($fldsTxt);
  $contents = getCurlPageX('http://WaNeLo.com/pin/create/button/', '', true, $fldsTxt, false, $advSettings); //prr($contents);  
  if (stripos($contents, 'blocked this')!==false) { $retText = trim(str_replace(array("\r\n", "\r", "\n"), " | ", strip_tags(CutFromTo($contents, '<body>', '</body>'))));
    return "WaNeLo ERROR: 'The Source is blocked'. Please see https://support.WaNeLo.com/entries/21436306-why-is-my-pin-or-site-blocked-for-spam-or-inappropriate-content/ for more info | WaNeLo Message:".$retText;
  }  
  if (stripos($contents, 'Oops')!==false && stripos($contents, '<body>')!==false ) return 'WaNeLo ERROR MESSAGE : '.trim(str_replace(array("\r\n", "\r", "\n"), " | ", strip_tags(CutFromTo($contents, '<body>', '</body>'))));
  if (stripos($contents, 'pinSuccess')!==false) { $pinID = CutFromTo($contents, 'pinSuccess','</li>'); $pinID = CutFromTo($pinID, '<li','</a>'); $pinID = CutFromTo($pinID, 'href="','"');
      return array("code"=>"OK", "post_id"=>$pinID); 
  } else return "Somethig is Wrong - WaNeLo Returned Error 502";  
}}

if (!function_exists("nxs_getWLHeaders")) {  function nxs_getWLHeaders($ref, $post=false, $xhr=false){ $hdrsArr = array();  
 $hdrsArr['Connection']='keep-alive'; $hdrsArr['Referer']=$ref; $hdrsArr['Cache-Control']= 'max-age=0';
 $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.28 Safari/537.31';
 if ($post==true) $hdrsArr['Content-Type']='application/x-www-form-urlencoded'; 
 if (is_array($post)) $hdrsArr = array_merge($hdrsArr, $post);
 if ($xhr) $hdrsArr['X-Requested-With']='XMLHttpRequest'; 
 if ($xhr) $hdrsArr['Accept']='application/json, text/javascript, */*; q=0.01'; else $hdrsArr['Accept']='text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
 if ($xhr) $hdrsArr['X-CSRF-Token']=$xhr; 
 //$hdrsArr['If-None-Match']='91a86e9a04ffaaa0d65332e0b4bbb2c9';
 //$hdrsArr['Cookie']='csrf-token=oTvt5Bu20iFSYUKZxakhSez2uSr55iG4mLiziUcjuT4%3D; csrf-param=authenticity_token; _ssn=e0e3072aed46259a4b9d42b4b23a112f';
 $hdrsArr['Accept-Encoding']='gzip,deflate,sdch'; $hdrsArr['Accept-Language']='en-US,en;q=0.8'; $hdrsArr['Accept-Charset']='ISO-8859-1,utf-8;q=0.7,*;q=0.3'; return $hdrsArr;
}}

if (!function_exists("nxs_urlencCookies")) { function nxs_urlencCookies($value, $name) { return urlencode($value); }}

if (!function_exists("doCheckWaNeLo2")) {function doCheckWaNeLo2(){ global $nxs_gCookiesArr, $nxs_gWLUName, $nxs_gTkn, $nxs_gWLBoards; $nxs_gWLUName = ''; 
  $hdrsArr = nxs_getWLHeaders('http://wanelo.com/', false, $nxs_gTkn); $reqArr = array('headers' => $hdrsArr, 'cookies' => $nxs_gCookiesArr); 
  $response = nxs_remote_get('http://wanelo.com/users/me?requesting_controller=home&requesting_action=trending&params=null&exclude_params%5B%5D=collections', $reqArr); 
  $contents = $response['body']; $jsn = json_decode($contents, true); if (!is_array($jsn)) return "No Login /J";  
  $user = $jsn['user']; if ($user=='null' || !is_array($user)) return "No Login /U"; 
  if (trim($user['username'])!='') return false; else { echo "WTF? NO LOGIN?"; return "No Login /X"; }
}}
if (!function_exists("doConnectToWaNeLo2")) {function doConnectToWaNeLo2($email, $pass){ global $nxs_gCookiesArr, $nxs_gTkn, $nxs_gWLUName, $nxs_gWLBoards; $nxs_gCookiesArr = array(); $advSettings = array();// echo "UUU";
  
  $err = nxsCheckSSLCurl('https://wanelo.com/users/sign_in'); if ($err!==false && $err['errNo']=='60') $advSettings['noSSLSec'] = true;  
  if ($err!==false && stripos($err['errMsg'], 'Protocol https not supported')!==false) return 'Protocol https not supported or disabled in libcurl. Please install or enable OpenSSL. ';    
  $hdrsArr = nxs_getWLHeaders('http://wanelo.com');  $response = nxs_remote_get('http://wanelo.com/users/sign_in', array( 'headers' => $hdrsArr)); if (is_wp_error($response)) return "Something wrong".print_r($response);  
  $contents = $response['body'];  $ckArr = $response['cookies'];  foreach ($ckArr as $ck) if ($ck->name=='csrf-token') $tkn = $ck->value;  
  //## GET HIDDEN FIELDS
  $md = array(); $mids = ''; $frmTxt = CutFromTo($contents, 'action="https://wanelo.com/users/sign_in','</form>'); $md = array(); $flds  = array();// prr($frmTxt); 
    while (stripos($frmTxt, '<input')!==false){ $inpField = trim(CutFromTo($frmTxt,'<input', '>')); $name = trim(CutFromTo($inpField,'name="', '"'));
     if ( stripos($inpField, '"hidden"')!==false && $name!='' && !in_array($name, $md)) { $md[] = $name; $val = trim(CutFromTo($inpField,'value="', '"')); $flds[$name]= $val; $mids .= "&".$name."=".$val;}
     $frmTxt = substr($frmTxt, stripos($frmTxt, '<input')+8);
    } $flds['user[login]'] = $email; $flds['user[password]'] = $pass; $flds['user[remember_me]'] = '1'; $flds['authenticity_token'] = $tkn;
  //## ACTUAL LOGIN
  $hdrsArr = nxs_getWLHeaders('https://wanelo.com/users/sign_in', true); 
  $response = nxs_remote_post('https://wanelo.com/users/sign_in?return_to=%2Fusers%2Fsign_in', array( 'method' => 'POST', 'timeout' => 45, 'redirection' => 0,  'headers' => $hdrsArr, 'body' => $flds, 'cookies' => $ckArr)); 
  if (is_wp_error($response)) return "Something wrong".print_r($response);  $contents = $response['body'];  $ckArr = nxs_MergeCookieArr($ckArr, $response['cookies']);     
  if ($response['headers']['location']=='http://wanelo.com/users/sign_in' || $response['headers']['location']=='https://wanelo.com/users/sign_in') return 'Incorrect Username/Password ';    
  //## GET WL Username/Check Login
  foreach ($ckArr as $ck) if ($ck->name=='csrf-token') $nxs_gTkn = $ck->value; // prr($nxs_gTkn);
  $hdrsArr = nxs_getWLHeaders('http://wanelo.com/', false, $nxs_gTkn); $reqArr = array('headers' => $hdrsArr, 'cookies' => $ckArr); 
  $response = nxs_remote_get('http://wanelo.com/users/me?requesting_controller=home&requesting_action=trending&params=null&exclude_params%5B%5D=collections', $reqArr);
  $contents = $response['body']; $jsn = json_decode($contents, true); if (!is_array($jsn)) return "Something wrong".print_r($response); 
  $user = $jsn['user']; if ($user=='null' || !is_array($user)) return "Invalid login - no user.".print_r($response); 
  $nxs_gWLUName = $user['username']; $nxs_gCookiesArr = $ckArr;
  if (trim($nxs_gWLUName)!='') return false; else return "Something wrong";   
}}
if (!function_exists("doPostToWaNeLo2")) {function doPostToWaNeLo2($msg, $imgURL, $lnk, $boardID, $title = '', $price='', $via=''){  global $nxs_gTkn, $nxs_gCookiesArr;  $uLnk = urlencode($lnk); $msg = substr($msg, 0, 480); $tgs = '';
$boardID = '16321420';
  foreach ($nxs_gCookiesArr as $ck) if ($ck->name=='csrf-token') $nxs_gTkn = $ck->value; // prr($nxs_gTkn);  
  add_filter('wp_http_cookie_value','nxs_urlencCookies', 10, 2 );
  
  $hdrsArr = nxs_getWLHeaders('http://wanelo.com/p/pre_post?utf8=%E2%9C%93&url='.$uLnk.'&commit=Post', false, $nxs_gTkn); $reqArr = array('headers' => $hdrsArr, 'cookies' => $nxs_gCookiesArr); 
  $response = nxs_remote_get('http://wanelo.com/users/me?requesting_controller=products&requesting_action=pre_post&params=null&exclude_params%5B%5D=collections', $reqArr);   
  $contents = $response['body']; $jsn = json_decode($contents, true); if (is_array($jsn) && isset($jsn['user']) && isset($jsn['user']['id'])) $userID = $jsn['user']['id']; else return "Invalid user";
  

  /*
  
  $hdrsArr = nxs_getWLHeaders('http://wanelo.com/trending', false); $reqArr = array('headers' => $hdrsArr, 'cookies' => $nxs_gCookiesArr); 
  $response = nxs_remote_get('http://wanelo.com/p/pre_post?utf8=%E2%9C%93&url='.$uLnk.'&commit=Post', $reqArr);    
  
  // prr($reqArr); prr($response); 
  
  $hdrsArr = nxs_getWLHeaders('http://wanelo.com/p/pre_post?utf8=%E2%9C%93&url='.$uLnk.'&commit=Post', false, $nxs_gTkn); $reqArr = array('headers' => $hdrsArr, 'cookies' => $nxs_gCookiesArr); 
  $response = nxs_remote_get('http://wanelo.com/users/me?requesting_controller=products&requesting_action=pre_post&params=null&exclude_params%5B%5D=collections', $reqArr);   
  $contents = $response['body']; $jsn = json_decode($contents, true); prr($jsn); 
  $nxs_gCookiesArr = nxs_MergeCookieArr($nxs_gCookiesArr, $response['cookies']);    prr($response);

  $hdrsArr = nxs_getWLHeaders('http://wanelo.com/p/pre_post?utf8=%E2%9C%93&url='.$uLnk.'&commit=Post', false, $nxs_gTkn); $reqArr = array('headers' => $hdrsArr, 'cookies' => $nxs_gCookiesArr); 
  $response = nxs_remote_get('http://wanelo.com/scraper/scrape?url='.$uLnk, $reqArr);    
  
  
  prr($response); die();

  //$nxs_gTkn = "666";
  
  */
 // $fff = urldecode('%E2%9C%93'); prr($fff); die();
  
$pstFlds = array(); $pstFlds['utf8']=urldecode('%E2%9C%93'); $pstFlds['authenticity_token']=$nxs_gTkn; $pstFlds['price']=''; $pstFlds['title']=$msg; 
$pstFlds['images[]']=$imgURL; $pstFlds['shop']=''; $pstFlds['availability']='InStock'; $pstFlds['url']=$lnk;
$headers = array("Content-Type" => "application/x-www-form-urlencoded", "Origin" =>"http://wanelo.com", "Expect"=>"");  
  
$hdrsArr = nxs_getWLHeaders('http://wanelo.com/p/pre_post?utf8=%E2%9C%93&url=http%3A%2F%2Fwww.couponswp.com%2F2013%2F03%2Ffalls%2F&commit=Post', $headers);  
$reqArr = array('method' => 'POST', 'httpversion' => '1.1', 'timeout' => 45, 'redirection' => 0,  'headers' => $hdrsArr, 'body' => $pstFlds, 'cookies' => $nxs_gCookiesArr); 
$response = nxs_remote_post('http://wanelo.com/p/post', $reqArr);   
if ( $response['response']['code']=='302' ) { $postID = $response['headers']['location']; if (stripos($postID, 'sign_in')!==false) return "Something is Wrong"; else  {
  $pstFlds = array(); $pstFlds['attributed_user_id']=$userID; $pstFlds['attributed_save_id']=''; $pstFlds['publish_photo_to_fb']='false'; $pstFlds['publish_photo_to_twitter']='false'; 
  $pstFlds['initial_collection']=$boardID; $pstFlds['collection_is_new']='false'; $pstFlds['comment']='';   
  $hdrsArr = nxs_getWLHeaders($postID, true, $nxs_gTkn);  
  $reqArr = array('method' => 'POST', 'httpversion' => '1.1', 'timeout' => 45, 'redirection' => 0,  'headers' => $hdrsArr, 'body' => $pstFlds, 'cookies' => $nxs_gCookiesArr); 
  $response = nxs_remote_post($postID.'/saves', $reqArr); //  prr($response);   
  return array("code"=>"OK", "post_id"=>$postID);  
}}

if ( $response['response']['code']=='200' ) { echo "########################################### - Code 200\r\n";
   
    
  $hdrsArr = nxs_getWLHeaders('http://wanelo.com/', false, $nxs_gTkn); $reqArr = array('headers' => $hdrsArr, 'cookies' => $nxs_gCookiesArr); 
  $response = nxs_remote_get('http://wanelo.com/users/me?requesting_controller=products&requesting_action=post&params=null&exclude_params%5B%5D=collections', $reqArr);     
  $contents = $response['body']; $jsn = json_decode($contents, true); prr($jsn);     
  $nxs_gCookiesArr = nxs_MergeCookieArr($nxs_gCookiesArr, $response['cookies']);    
  
  
  $pstFlds = array(); $pstFlds['utf8']=urldecode('%E2%9C%93'); $pstFlds['authenticity_token']=$nxs_gTkn; $pstFlds['product[url]']=$lnk; $pstFlds['product[price]']='0.00'; 
  $pstFlds['product[shop]']=''; $pstFlds['product[availability]']='InStock'; $pstFlds['publish_photo_to_fb']='false'; $pstFlds['product[name]']=$msg;  
  $pstFlds['initial_collection']=$boardID; $pstFlds['collection_is_new']='false'; $pstFlds['comment']=''; $pstFlds['product[remote_image_url]']=$imgURL;
  $pstFlds['commit']='Post to Wanelo'; // $headers = array("Content-Type" => "application/x-www-form-urlencoded", "Origin" =>"http://wanelo.com", "Expect"=>"");  
  $headers = array();
  
  //remove_filter('wp_http_cookie_value','nxs_urlencCookies', 10, 2 );
  
  // $headers = array("Content-Type" => "multipart/form-data; boundary=$boundary", "Origin" =>"http://wanelo.com", "Expect"=>"");
  $hdrsArr = nxs_getWLHeaders('http://wanelo.com/p/pre_post?utf8=%E2%9C%93&url=http%3A%2F%2Fwww.couponswp.com%2F2013%2F03%2Ffalls%2F&commit=Post', $headers);  
  $reqArr = array('method' => 'POST', 'httpversion' => '1.1', 'ar' => '1.1', 'timeout' => 45, 'redirection' => 0,  'headers' => $hdrsArr, 'body' => $pstFlds, 'cookies' => $nxs_gCookiesArr); 
  $response = nxs_remote_post('http://wanelo.com/p', $reqArr);   
  
     prr($reqArr);
  prr($response); die();

echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\r\n";
$boundary  = uniqid('----WebKitFormBoundary');    
$brk = "\n";
$postData  = '--'.$boundary.$brk.'Content-Disposition: form-data; name="utf8"'.$brk.$brk.urldecode('%E2%9C%93').$brk.'--'.$boundary.$brk.'Content-Disposition: form-data; name="authenticity_token"'.$brk.$brk.($nxs_gTkn).$brk.'--'.$boundary.$brk.'Content-Disposition: form-data; name="product[url]"'.$brk.$brk.$lnk.$brk.'--'.$boundary.$brk.'Content-Disposition: form-data; name="product[price]"'.$brk.$brk.'0.00'.$brk.'--'.$boundary.$brk.'Content-Disposition: form-data; name="product[shop]"'.$brk.$brk.''.$brk.'--'.$boundary.$brk.'Content-Disposition: form-data; name="publish_photo_to_fb"'.$brk.$brk.'false'.$brk.'--'.$boundary.$brk.'Content-Disposition: form-data; name="product[name]"'.$brk.$brk.$msg.$brk.'--'.$boundary.$brk.'Content-Disposition: form-data; name="initial_collection"'.$brk.$brk.$boardID.$brk.'--'.$boundary.$brk.'Content-Disposition: form-data; name="collection_is_new"'.$brk.$brk.'false'.$brk.'--'.$boundary.$brk.'Content-Disposition: form-data; name="comment"'.$brk.$brk.''.$brk.'--'.$boundary.$brk.'Content-Disposition: form-data; name="product[remote_image_url]"'.$brk.$brk.$imgURL.$brk.'--'.$boundary.$brk.'Content-Disposition: form-data; name="commit"'.$brk.$brk.'Post to Wanelo'.$brk.'--'.$boundary.'--'.$brk;

//$headers   = array("Content-Type: multipart/form-data; boundary=$boundary", "Origin: http://wanelo.com", "Content-Length: ".strlen($postData));

$headers = array("Content-Type" => "multipart/form-data; boundary=$boundary", "Origin" =>"http://wanelo.com", "Expect"=>"");

  $hdrsArr = nxs_getWLHeaders('http://wanelo.com/', $headers); $reqArr = array('method' => 'POST', 'httpversion' => '1.1', 'timeout' => 45, 'redirection' => 0,  'headers' => $hdrsArr, 'body' => $postData, 'cookies' => $nxs_gCookiesArr); 
  $response = nxs_remote_post('http://wanelo.com/p', $reqArr); 
  
  
  remove_filter('wp_http_cookie_value','nxs_urlencCookies', 10, 2 );
  
  prr($reqArr);
  prr($response); die();
  
}

$ref = 'http://wanelo.com/p/post?bookmarklet=&images%5B%5D=http%3A%2F%2Fstatic.bhphoto.com%2Fimages%2Fimages200x200%2F680103.jpg&url=http%3A%2F%2Fwww.bhphotovideo.com%2Fc%2Fproduct%2F680103-USA%2FCanon_2751B002_EF_70_200mm_f_2_8L_IS.html&title=Canon%20EF%2070-200mm%20f%2F2.8L%20IS%20II%20USM%20Telephoto%20Zoom%20Lens&price=%240.00&shop=&source=toolbar&ref=http%3A%2F%2Fwww.bhphotovideo.com%2Fc%2Fproduct%2F680103-USA%2FCanon_2751B002_EF_70_200mm_f_2_8L_IS.html';

$contents = getCurlPageX('http://wanelo.com/p', $ref, false, $postData, true, $advSettings); $contents['content'] = ''; prr($contents); prr($postData); die();

 // if (stripos($imgURL, 'youtube.com')!==false || stripos($imgURL, 'youtu.be')!==false) { $tgs = 'http://img.youtube.com/vi/'.str_ireplace('http://youtu.be/','',$imgURL).'/0.jpg'; }
  $fldsTxt = 'caption='.urlencode($msg).'&board='.$boardID.'&tags='.$tgs.'&replies=&buyable='.urldecode($price).'&title='.urldecode($title).'&media_url='.urlencode($imgURL).'&url='.$lnk.'&via='.urldecode($via).'&csrfmiddlewaretoken='.$nxs_gTkn.'&form_url=';  
  if (trim($boardID)=='') return "Board is not Set";  if (trim($imgURL)=='') return "Image is not Set"; // prr($fldsTxt);
  $contents = getCurlPageX('http://WaNeLo.com/pin/create/button/', '', true, $fldsTxt, false, $advSettings); //prr($contents);  
  if (stripos($contents, 'blocked this')!==false) { $retText = trim(str_replace(array("\r\n", "\r", "\n"), " | ", strip_tags(CutFromTo($contents, '<body>', '</body>'))));
    return "WaNeLo ERROR: 'The Source is blocked'. Please see https://support.WaNeLo.com/entries/21436306-why-is-my-pin-or-site-blocked-for-spam-or-inappropriate-content/ for more info | WaNeLo Message:".$retText;
  }  
  if (stripos($contents, 'Oops')!==false && stripos($contents, '<body>')!==false ) return 'WaNeLo ERROR MESSAGE : '.trim(str_replace(array("\r\n", "\r", "\n"), " | ", strip_tags(CutFromTo($contents, '<body>', '</body>'))));
  if (stripos($contents, 'pinSuccess')!==false) { $pinID = CutFromTo($contents, 'pinSuccess','</li>'); $pinID = CutFromTo($pinID, '<li','</a>'); $pinID = CutFromTo($pinID, 'href="','"');
      return array("code"=>"OK", "post_id"=>$pinID); 
  } else return "Somethig is Wrong - WaNeLo Returned Error 502";  
}}

if (!class_exists("nxs_snapClassWL")) { class nxs_snapClassWL {
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){  global $nxs_plurl; $ntInfo = array('code'=>'WL', 'lcode'=>'wl', 'name'=>'WaNeLo', 'defNName'=>'wlUName', 'tstReq' => false); ?>    
    <div class="nxs_box">
      <div class="nxs_box_header"> 
        <div class="nsx_iconedTitle" style="margin-bottom:1px;background-image:url(<?php echo $nxs_plurl;?>img/<?php echo $ntInfo['lcode']; ?>16.png);"><?php echo $ntInfo['name']; ?>
          <?php $cbo = count($ntOpts); ?> 
          <?php if ($cbo>1){ ?><div class="nsBigText"><?php echo "(".($cbo=='0'?'No':$cbo)." "; _e('accounts', 'social-networks-auto-poster-facebook-twitter-g'); echo ")"; ?></div><?php } ?>
        </div>
      </div>
      <div class="nxs_box_inside">
        <?php if(!function_exists('doPostToWaNeLo')) {?>  WaNeLo doesn't have a built-in API for automated posts yet. <br/>You need to get a special <a target="_blank" href="http://www.nextscripts.com/wanelo-automated-posting">library module</a> to be able to publish your content to WaNeLo.
        <?php } else foreach ($ntOpts as $indx=>$pbo){ if (trim($pbo['nName']=='')) $pbo['nName'] = $pbo[$ntInfo['defNName']]; ?>
          <p style="margin:0px;margin-left:5px;">
            <input value="1" name="<?php echo $ntInfo['lcode']; ?>[<?php echo $indx; ?>][apDo<?php echo $ntInfo['code']; ?>]" onchange="doShowHideBlocks('<?php echo $ntInfo['code']; ?>');" type="checkbox" <?php if ((int)$pbo['do'.$ntInfo['code']] == 1) echo "checked"; ?> /> <?php if ((int)$pbo['catSel'] == 1) { ?>   <span onmouseout="nxs_hidePopUpInfo('popOnlyCat');" onmouseover="nxs_showPopUpInfo('popOnlyCat', event);"><?php echo "*[".(substr_count($pbo['catSelEd'], ",")+1)."]*" ?></span><?php } ?>
            <strong><?php  _e('Auto-publish to', 'social-networks-auto-poster-facebook-twitter-g'); ?> <?php echo $ntInfo['name']; ?> <i style="color: #005800;"><?php if($pbo['nName']!='') echo "(".$pbo['nName'].")"; ?></i></strong>
          &nbsp;&nbsp;<?php if ($ntInfo['tstReq'] && (!isset($pbo[$ntInfo['lcode'].'OK']) || $pbo[$ntInfo['lcode'].'OK']=='')){ ?><b style="color: #800000"><?php  _e('Attention requred. Unfinished setup', 'social-networks-auto-poster-facebook-twitter-g'); ?> ==&gt;</b><?php } ?><a id="do<?php echo $ntInfo['code'].$indx; ?>A" href="#" onclick="doShowHideBlocks2('<?php echo $ntInfo['code'].$indx; ?>');return false;">[<?php  _e('Show Settings', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;
          <a href="#" onclick="doDelAcct('<?php echo $ntInfo['lcode']; ?>', '<?php echo $indx; ?>', '<?php if (isset($pbo['bgBlogID'])) echo $pbo['nName']; ?>');return false;">[<?php  _e('Remove Account', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>
          </p><?php $pbo['ntInfo'] = $ntInfo; $this->showNTSettings($indx, $pbo);             
        } ?>
      </div>
    </div> <?php 
  }  
  //#### Show NEW Settings Page
  function showNewNTSettings($mgpo){ $po = array('nName'=>'', 'doWL'=>'1', 'wlUName'=>'', 'wlBoard'=>'', 'gpAttch'=>'', 'wlPass'=>'', 'wlDefImg'=>'', 'wlMsgFormat'=>'', 'wlBoard'=>'', 'wlBoardsList'=>'', 'doWL'=>1); 
  $po['ntInfo']= array('lcode'=>'wl'); $this->showNTSettings($mgpo, $po, true);}
  //#### Show Unit  Settings
  function showNTSettings($ii, $options, $isNew=false){  global $nxs_plurl; $nt = $options['ntInfo']['lcode']; $ntU = strtoupper($nt); 
    if (!isset($options['nHrs'])) $options['nHrs'] = 0; if (!isset($options['nMin'])) $options['nMin'] = 0;  if (!isset($options['catSel'])) $options['catSel'] = 0;  if (!isset($options['catSelEd'])) $options['catSelEd'] = ''; 
    if (!isset($options['nDays'])) $options['nDays'] = 0; if (!isset($options['qTLng'])) $options['qTLng'] = '';  ?>
             <div id="doWL<?php echo $ii; ?>Div" class="insOneDiv<?php if ($isNew) echo " clNewNTSets"; ?>">     <input type="hidden" name="apDoSWL<?php echo $ii; ?>" value="0" id="apDoSWL<?php echo $ii; ?>" />         
             
             <?php if(!function_exists('doPostToWaNeLo')) {?><span style="color:#580000; font-size: 16px;"><br/><br/>
            <b>WaNeLo API Library not found</b>
             <br/><br/> WaNeLo doesn't have a built-in API for automated posts yet.  <br/><br/>You need to get a special <a target="_blank" href="http://www.nextscripts.com/wanelo-automated-posting"><b>API Library Module</b></a> to be able to publish your content to WaNeLo.</span></div>
            
            <?php return; }; ?>
             
           
            <div id="doWL<?php echo $ii; ?>Div" style="margin-left: 10px;"> <div class="nsx_iconedTitle" style="float: right; background-image: url(<?php echo $nxs_plurl; ?>img/wl16.png);"><a style="font-size: 12px;" target="_blank"  href="http://www.nextscripts.com/setup-installation-wanelo-social-networks-auto-poster-wordpress/"><?php $nType="WaNeLo"; printf( __( 'Detailed %s Installation/Configuration Instructions', 'social-networks-auto-poster-facebook-twitter-g' ), $nType); ?></a></div>
            <div style="width:100%;"><strong><?php _e('Account Nickname', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> <i><?php _e('Just so you can easily identify it', 'social-networks-auto-poster-facebook-twitter-g'); ?></i> </div><input name="wl[<?php echo $ii; ?>][nName]" id="wlnName<?php echo $ii; ?>" style="font-weight: bold; color: #005800; border: 1px solid #ACACAC; width: 40%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['nName'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/>
            <?php echo nxs_addQTranslSel('wl', $ii, $options['qTLng']); ?>
            
              <br/>
    <ul class="nsx_tabs">
    <li><a href="#nsx<?php echo $nt.$ii ?>_tab1"><?php _e('Account Info', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></li>    
    <li><a href="#nsx<?php echo $nt.$ii ?>_tab2"><?php _e('Advanced', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></li>
    </ul>
    <div class="nsx_tab_container"><?php /* ######################## Account Tab ####################### */ ?>
    <div id="nsx<?php echo $nt.$ii ?>_tab1" class="nsx_tab_content" style="background-image: url(<?php echo $nxs_plurl; ?>img/<?php echo $nt; ?>-bg.png); background-repeat: no-repeat;  background-position:90% 10%;">
    
                  
            <div style="width:100%;"><strong>WaNeLo Email:</strong> </div><input name="wl[<?php echo $ii; ?>][apWLUName]" id="apWLUName<?php echo $ii; ?>" class="apWLUName<?php echo $ii; ?>"  style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['wlUName'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" />                
            <div style="width:100%;"><strong>WaNeLo Password:</strong> </div><input name="wl[<?php echo $ii; ?>][apWLPass]" id="apWLPass<?php echo $ii; ?>" type="password" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities(substr($options['wlPass'], 0, 5)=='g9c1a'?nsx_doDecode(substr($options['wlPass'], 5)):$options['wlPass'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" />  <br/>                
            <div style="width:100%;"><strong>Default Image to Pin:</strong> 
            <p style="font-size: 11px; margin: 0px;">If your post missing Featured Image this will be used instead.</p>
            </div><input name="wl[<?php echo $ii; ?>][apWLDefImg]" id="apWLDefImg" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['wlDefImg'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /> 
            <br/><br/>            
            
            <div style="width:100%;"><strong>Board:</strong> 
            Please <a href="#" onclick="getWLBoards(jQuery('<?php if ($isNew) echo "#nsx_addNT "; ?>#apWLUName<?php echo $ii; ?>').val(),jQuery('<?php if ($isNew) echo "#nsx_addNT "; ?>#apWLPass<?php echo $ii; ?>').val(), '<?php echo $ii; ?>'); return false;">click here to retrieve your boards</a>
            </div>
            <img id="wlLoadingImg<?php echo $ii; ?>" style="display: none;" src='<?php echo $nxs_plurl; ?>img/ajax-loader-sm.gif' />
            <select name="wl[<?php echo $ii; ?>][apWLBoard]" id="apWLBoard<?php echo $ii; ?>">
            <?php if ($options['wlBoardsList']!=''){ $gWLBoards = $options['wlBoardsList']; if ( base64_encode(base64_decode($gWLBoards)) === $gWLBoards) $gWLBoards = base64_decode($gWLBoards); 
              if ($options['wlBoard']!='') $gWLBoards = str_replace($options['wlBoard'].'"', $options['wlBoard'].'" selected="selected"', $gWLBoards);  echo $gWLBoards;} else { ?>
              <option value="0">None(Click above to retrieve your boards)</option>
            <?php } ?>
            </select>
            
            <br/><br/>            
            
            
            <div id="altFormat" style="">
              <div style="width:100%;"><strong id="altFormatText"><?php _e('Message text Format', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong>  <a href="#" id="apWLMsgFrmt<?php echo $ii; ?>HintInfo" onclick="mxs_showHideFrmtInfo('apWLMsgFrmt<?php echo $ii; ?>'); return false;"><?php _e('Show format info', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>             
              </div><input  name="wl[<?php echo $ii; ?>][apWLMsgFrmt]" id="apWLMsgFrmt" style="width: 50%;" value="<?php if ($options['wlMsgFormat']!='') _e(apply_filters('format_to_edit', htmlentities($options['wlMsgFormat'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g');  else echo "%TITLE% - %URL%"; ?>" onfocus="mxs_showFrmtInfo('apWLMsgFrmt<?php echo $ii; ?>');"  />
              
              <?php nxs_doShowHint("apWLMsgFrmt".$ii); ?>
            </div><br/>    
            <?php if ($isNew) { ?> <input type="hidden" name="wl[<?php echo $ii; ?>][apDoWL]" value="1" id="apDoNewWL<?php echo $ii; ?>" /> <?php } ?>
            <?php if ($options['wlPass']!='') { ?>
            
            <b><?php _e('Test your settings', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</b>&nbsp;&nbsp;&nbsp; <a href="#" class="NXSButton" onclick="testPost('WL', '<?php echo $ii; ?>'); return false;"><?php printf( __( 'Submit Test Post to %s', 'social-networks-auto-poster-facebook-twitter-g' ), $nType); ?></a>         
            <?php } ?>
            
            </div>
            <?php /* ######################## Advanced Tab ####################### */ ?>
    <div id="nsx<?php echo $nt.$ii ?>_tab2" class="nsx_tab_content">
    
    <?php if (!$isNew) { ?> <div class="nxs_tls_cpt"><?php _e('Categories', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
    <div style="width:100%;"><strong><?php _e('Categories', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong>
       <input value="0" id="catSelA<?php echo $ii; ?>" type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][catSel]" <?php if ((int)$options['catSel'] != 1) echo "checked"; ?> /> All                                  
       <input value="1" id="catSelS<?php echo $ntU; ?><?php echo $ii; ?>" type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][catSel]" <?php if ((int)$options['catSel'] == 1) echo "checked"; ?> /> <a href="#" style="text-decoration: none;" class="showCats" id="nxs_SCA_<?php echo $ntU; ?><?php echo $ii; ?>" onclick="jQuery('#catSelS<?php echo $ntU; ?><?php echo $ii; ?>').attr('checked', true); jQuery('#tmpCatSelNT').val('<?php echo $ntU; ?><?php echo $ii; ?>'); nxs_markCats( jQuery('#nxs_SC_<?php echo $ntU; ?><?php echo $ii; ?>').val() ); jQuery('#showCatSel').bPopup({ modalClose: false, appendTo: '#nsStForm', opacity: 0.6, follow: [false, false], position: [75, 'auto']}); return false;">Selected<?php if ($options['catSelEd']!='') echo "[".(substr_count($options['catSelEd'], ",")+1)."]"; ?></a>       
       <input type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][catSelEd]" id="nxs_SC_<?php echo $ntU; ?><?php echo $ii; ?>" value="<?php echo $options['catSelEd']; ?>" />
    <br/><i><?php _e('Only selected categories will be autoposted to this account', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div> 
    <br/>
    <?php } ?>
    
    <?php nxs_addPostingDelaySelV3($nt, $ii, $options['nHrs'], $options['nMin'], $options['nDays']); ?>
    <?php nxs_showRepostSettings($nt, $ii, $options); ?>
            
            
    </div> <?php /* #### End of Tab #### */ ?>
    </div><br/> <?php /* #### End of Tabs #### */ ?>
    
    <div class="submit clear" style="padding-bottom: 0px;"><input type="submit" class="button-primary" name="update_NS_SNAutoPoster_settings" value="<?php _e('Update Settings', 'social-networks-auto-poster-facebook-twitter-g') ?>" /></div>
            </div>
  </div>
            <?php
  }
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ global $nxs_snapThisPageUrl;// $code = 'WL'; $lcode = 'wl'; 
    foreach ($post as $ii => $pval){ 
      if (isset($pval['apWLUName']) && $pval['apWLUName']!=''){ if (!isset($options[$ii])) $options[$ii] = array();
        if (isset($pval['apDoWL']))   $options[$ii]['doWL'] = $pval['apDoWL']; else $options[$ii]['doWL'] = 0;
        if (isset($pval['nName']))          $options[$ii]['nName'] = trim($pval['nName']);
        if (isset($pval['apWLUName']))   $options[$ii]['wlUName'] = trim($pval['apWLUName']);
        if (isset($pval['apWLPass']))    $options[$ii]['wlPass'] = 'g9c1a'.nsx_doEncode($pval['apWLPass']); else $options[$ii]['wlPass'] = '';
        if (isset($pval['apWLBoard']))   $options[$ii]['wlBoard'] = trim($pval['apWLBoard']);                
        if (isset($pval['apWLDefImg']))  $options[$ii]['wlDefImg'] = trim($pval['apWLDefImg']);
        
        if (isset($pval['catSel'])) $options[$ii]['catSel'] = trim($pval['catSel']);
        if ($options[$ii]['catSel']=='1' && trim($pval['catSelEd'])!='') $options[$ii]['catSelEd'] = trim($pval['catSelEd']); else $options[$ii]['catSelEd'] = '';
        
        if (isset($pval['apWLMsgFrmt'])) $options[$ii]['wlMsgFormat'] = trim($pval['apWLMsgFrmt']);     
        
        $options[$ii] = nxs_adjRpst($options[$ii], $pval);       
        
        if (isset($pval['delayDays'])) $options[$ii]['nDays'] = trim($pval['delayDays']);
        if (isset($pval['delayHrs'])) $options[$ii]['nHrs'] = trim($pval['delayHrs']); if (isset($pval['delayMin'])) $options[$ii]['nMin'] = trim($pval['delayMin']); 
        if (isset($pval['qTLng'])) $options[$ii]['qTLng'] = trim($pval['qTLng']); 
      }
    } return $options;
  }  
  //#### Show Post->Edit Meta Box Settingswwww
  function showEdPostNTSettings($ntOpts, $post){ global $nxs_plurl; $post_id = $post->ID; $nt = 'wl'; $ntU = 'WL';
     foreach($ntOpts as $ii=>$ntOpt)  { $pMeta = maybe_unserialize(get_post_meta($post_id, 'snapWL', true));  if (is_array($pMeta)) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]); 
        $doWL = $ntOpt['doWL'] && (is_array($pMeta) || $ntOpt['catSel']!='1');   
        $isAvailWL =  $ntOpt['wlUName']!='' && $ntOpt['wlPass']!=''; $wlMsgFormat = htmlentities($ntOpt['wlMsgFormat'], ENT_COMPAT, "UTF-8");        
      ?>  
      <tr><th style="text-align:left;" colspan="2"><?php if ( $ntOpt['catSel']=='1' && trim($ntOpt['catSelEd'])!='' )  { ?> <input type="hidden" class="nxs_SC" id="nxs_SC_WL<?php echo $ii; ?>" value="<?php echo $ntOpt['catSelEd']; ?>" /> <?php } ?>
      <?php if ($isAvailWL) { ?><input class="nxsGrpDoChb" value="1" id="doWL<?php echo $ii; ?>"  type="checkbox" name="wl[<?php echo $ii; ?>][doWL]" <?php if ((int)$doWL == 1) echo 'checked="checked" title="def"';  ?> /> 
      <?php if ($post->post_status == "publish") { ?> <input type="hidden" name="wl[<?php echo $ii; ?>][doWL]" value="<?php echo $doWL;?>"> <?php } ?> <?php } ?>
      <div class="nsx_iconedTitle" style="display: inline; font-size: 13px; background-image: url(<?php echo $nxs_plurl; ?>img/wl16.png);">WaNeLo - <?php _e('publish to', 'social-networks-auto-poster-facebook-twitter-g') ?> (<i style="color: #005800;"><?php echo $ntOpt['nName']; ?></i>)</div></th> <td><?php //## Only show RePost button if the post is "published"
                    if ($post->post_status == "publish" && $isAvailWL) { ?><input alt="<?php echo $ii; ?>" style="float: right;" onmouseout="hidePopShAtt('SV');" onmouseover="showPopShAtt('SV', event);" onclick="return false;"  type="button" class="button" name="rePostToWL_repostButton" id="rePostToWL_button" value="<?php _e('Repost to WaNeLo', 'social-networks-auto-poster-facebook-twitter-g') ?>" />
                    <?php } ?>

                    <?php  if (is_array($pMeta) && is_array($pMeta[$ii]) && isset($pMeta[$ii]['pgID']) ) {                         
                        ?> <span id="pstdWL<?php echo $ii; ?>" style="float: right;padding-top: 4px; padding-right: 10px;">
          <a style="font-size: 10px;" href="http://WaNeLo.com<?php echo $pMeta[$ii]['pgID']; ?>" target="_blank"><?php $nType="WaNeLo"; printf( __( 'Posted on', 'social-networks-auto-poster-facebook-twitter-g' ), $nType); ?>  <?php echo (isset($pMeta[$ii]['pDate']) && $pMeta[$ii]['pDate']!='')?(" (".$pMeta[$ii]['pDate'].")"):""; ?></a>
                    </span><?php } ?>
                    
                </td></tr>                
                
                <?php if (!$isAvailWL) { ?><tr><th scope="row" style="text-align:right; width:150px; padding-top: 5px; padding-right:10px;"></th> <td><b>Setup your WaNeLo Account to AutoPost to WaNeLo</b>
                <?php } else { if ($post->post_status != "publish" && function_exists('nxs_doSMAS5') ) { nxs_doSMAS5($nt, $ii, $ntOpt); } ?>
                
                <?php if ($ntOpt['rpstOn']=='1') { ?> 
                
                <tr id="altFormat1" style=""><th scope="row" style="vertical-align:top; padding-top:6px; text-align:right; width:60px; padding-right:10px;">
                <input value="0"  type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstPostIncl]"/><input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstPostIncl]"  <?php if ((int)$ntOpt['rpstPostIncl'] == 1) echo "checked"; ?> /> 
                </th>
                <td> <?php _e('Include in "Auto-Reposting" to this network.', 'social-networks-auto-poster-facebook-twitter-g') ?>                
                </td></tr> <?php } ?>
                
                <tr><th scope="row" style="text-align:right; width:150px; padding-top: 5px; padding-right:10px;">Select Board</th>
                <td><select name="wl[<?php echo $ii; ?>][apWLBoard]" id="apWLBoard">
            <?php if (!empty($ntOpt['wlBoardsList'])){ $gWLBoards = $ntOpt['wlBoardsList']; if ( base64_encode(base64_decode($gWLBoards)) === $gWLBoards) $gWLBoards = base64_decode($gWLBoards); 
              if ($ntOpt['wlBoard']!='') $gWLBoards = str_replace($ntOpt['wlBoard'].'"', $ntOpt['wlBoard'].'" selected="selected"', $gWLBoards);  echo $gWLBoards;} else { ?>
              <option value="0">None(Click above to retrieve your boards)</option>
            <?php } ?>
            </select></td>
                </tr> 
                              
                <tr id="altFormat1" style=""><th scope="row" style="vertical-align:top; padding-top:6px; text-align:right; width:60px; padding-right:10px;"><?php _e('Text Message Format:', 'social-networks-auto-poster-facebook-twitter-g') ?></th>
                <td>                
                <textarea cols="150" rows="1" id="wl<?php echo $ii; ?>SNAPformat" name="wl[<?php echo $ii; ?>][SNAPformat]"  style="width:60%;max-width: 610px;" onfocus="jQuery('#wl<?php echo $ii; ?>SNAPformat').attr('rows', 4); jQuery('.nxs_FRMTHint').hide();mxs_showFrmtInfo('apWLMsgFrmt<?php echo $ii; ?>');"><?php echo $wlMsgFormat; ?></textarea>
                <?php nxs_doShowHint("apWLMsgFrmt".$ii); ?></td></tr>
                                
                <?php } 
     }
  }
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){  if (isset($pMeta['isPosted'])) $optMt['isPosted'] = $pMeta['isPosted']; else  $optMt['isPosted'] = '';
     if (isset($pMeta['SNAPformat'])) $optMt['wlMsgFormat'] = $pMeta['SNAPformat'];           
     if (isset($pMeta['imgToUse'])) $optMt['imgToUse'] = $pMeta['imgToUse'];      
     if (isset($pMeta['timeToRun']))  $optMt['timeToRun'] = $pMeta['timeToRun'];  if (isset($pMeta['rpstPostIncl']))  $optMt['rpstPostIncl'] = $pMeta['rpstPostIncl'];    
     if (isset($pMeta['apWLBoard']) && $pMeta['apWLBoard']!='' && $pMeta['apWLBoard']!='0') $optMt['wlBoard'] = $pMeta['apWLBoard']; 
     if (isset($pMeta['doWL'])) $optMt['doWL'] = $pMeta['doWL'] == 1?1:0; else { if (isset($pMeta['SNAPformat'])) $optMt['doWL'] = 0; }
     if (isset($pMeta['SNAPincludeWL']) && $pMeta['SNAPincludeWL'] == '1' ) $optMt['doWL'] = 1;  
     return $optMt;
  }  
}}
if (!function_exists("nxs_rePostToWL_ajax")) {
  function nxs_rePostToWL_ajax() { check_ajax_referer('nxsSsPageWPN');  $postID = $_POST['id']; $options = get_option('NS_SNAutoPoster');  
    foreach ($options['wl'] as $ii=>$two) if ($ii==$_POST['nid']) {    $two['ii'] = $ii; $two['pType'] = 'aj'; //if ($two['gpPageID'].$two['gpUName']==$_POST['nid']) {  
      $po =  get_post_meta($postID, 'snapWL', true); $po =  maybe_unserialize($po);// prr($gppo);
      if (is_array($po) && isset($po[$ii]) && is_array($po[$ii])){ $ntClInst = new nxs_snapClassWL(); $two = $ntClInst->adjMetaOpt($two, $po[$ii]); }
      $result = nxs_doPublishToWL($postID, $two); if ($result == 200) die("Successfully sent your post to WaNeLo."); else die($result);        
    }    
  }
}  

if (!function_exists("nxs_doPublishToWL")) { //## Second Function to Post to G+
  function nxs_doPublishToWL($postID, $options){ global $nxs_gCookiesArr; $ntCd = 'WL'; $ntCdL = 'wl'; $ntNm = 'WaNeLo'; 
    if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true));
    //if (isset($options['timeToRun'])) wp_unschedule_event( $options['timeToRun'], 'nxs_doPublishToWL',  array($postID, $options));
    $ii = $options['ii']; if (!isset($options['pType'])) $options['pType'] = 'im'; if ($options['pType']=='sh') sleep(rand(1, 10)); 
    $logNT = '<span style="color:#FA5069">WaNeLo</span> - '.$options['nName'];
    $snap_ap = get_post_meta($postID, 'snap'.$ntCd, true); $snap_ap = maybe_unserialize($snap_ap);     
    $isAttachVid = $options['isAttachVid']; $isAttachVid = '1';
    if ($options['pType']!='aj' && is_array($snap_ap) && (nxs_chArrVar($snap_ap[$ii], 'isPosted', '1') || nxs_chArrVar($snap_ap[$ii], 'isPrePosted', '1'))) {
        $snap_isAutoPosted = get_post_meta($postID, 'snap_isAutoPosted', true); if ($snap_isAutoPosted!='2') {  sleep(5);
         nxs_addToLogN('W', 'Notice', $logNT, '-=Duplicate=- Post ID:'.$postID, 'Already posted. No reason for posting duplicate'.' |'.$uqID); return;
        }
    }  
    $blogTitle = htmlspecialchars_decode(get_bloginfo('name'), ENT_QUOTES); if ($blogTitle=='') $blogTitle = home_url(); 
    
    if ($postID=='0') { echo "Testing ... <br/><br/>"; $msg = 'Test Post from '.$blogTitle; $link = home_url(); 
      if ($options['wlDefImg']!='') $imgURL = $options['wlDefImg']; else $imgURL ="http://direct.gtln.us/img/nxs/NextScriptsLogoT.png"; 
    }
    else { $post = get_post($postID); if(!$post) return; $wlMsgFormat = $options['wlMsgFormat'];  $msg = nsFormatMessage($wlMsgFormat, $postID); $link = get_permalink($postID); 
      nxs_metaMarkAsPosted($postID, $ntCd, $options['ii'], array('isPrePosted'=>'1')); $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'large',  $options['wlDefImg']); //prr($options); echo $imgURL."######"; // echo "WW".$postID."|";
      if ($isAttachVid=='1') { $vids = nsFindVidsInPost($post); if (count($vids)>0) { $vidURL = 'http://www.youtube.com/v/'.$vids[0]; $imgURL = 'http://img.youtube.com/vi/'.$vids[0].'/0.jpg'; }}      
    }
    $email = $options['wlUName']; $boardID = $options['wlBoard'];  $pass = substr($options['wlPass'], 0, 5)=='g9c1a'?nsx_doDecode(substr($options['wlPass'], 5)):$options['wlPass'];// prr($boardID); prr($_POST); die();    
    if (isset($options['wlSvC'])) $nxs_gCookiesArr = maybe_unserialize( $options['wlSvC']); $loginError = true; 
    if (is_array($nxs_gCookiesArr)) $loginError = doCheckWaNeLo2(); 
    $extInfo = ' | PostID: '.$postID." - ".$post->post_title; 
    if ($loginError!==false) $loginError = doConnectToWaNeLo2($email, $pass);  if ($loginError!==false) {echo $loginError; nxs_addToLogN('E', 'Error', $logNT, '-=ERROR=- '.print_r($loginError, true), $extInfo); return "BAD USER/PASS";}      
    
     
    
    if (serialize($nxs_gCookiesArr)!=$options['wlSvC']) { global $plgn_NS_SNAutoPoster;  $gOptions = $plgn_NS_SNAutoPoster->nxs_options; // prr($gOptions['wl']);
        if (isset($options['ii']) && $options['ii']!=='')  { $gOptions['wl'][$options['ii']]['wlSvC'] = serialize($nxs_gCookiesArr); update_option('NS_SNAutoPoster', $gOptions);  }        
        else foreach ($gOptions['wl'] as $ii=>$gpn) { $result = array_diff($options, $gpn);
          if (!is_array($result) || count($result)<1) { $gOptions['wl'][$ii]['wlSvC'] = serialize($nxs_gCookiesArr); update_option('NS_SNAutoPoster', $gOptions); break; }
        }        
    } // echo "WL SET:".$msg."|".$imgURL."|".$link."|".$boardID;    
    $ret = doPostToWaNeLo2($msg, $imgURL, $link, $boardID); if ($ret=='OK') $ret = array("code"=>"OK", "post_id"=>'');
    if ( (!is_array($ret)) && $ret!='OK') { if ($postID=='0') echo $ret; nxs_addToLogN('E', 'Error', $logNT, '-=ERROR=- '.print_r($ret, true), $extInfo); } else { if ($postID=='0') {  nxs_addToLogN('S', 'Test', $logNT, 'OK - TEST Message Posted '); echo 'OK - Message Posted, please see your WaNeLo Page'; } else { nxs_metaMarkAsPosted($postID, 'WL', $options['ii'], array('isPosted'=>'1', 'pgID'=>$ret['post_id'], 'pDate'=>date('Y-m-d H:i:s'))); nxs_addToLogN('S', 'Posted', $logNT, 'OK - Message Posted ', $extInfo);} }
    if ($ret['code']=='OK') return 200; else return $ret;
  }
}  
?>