<?php    
//## NextScripts Facebook Connection Class
$nxs_snapAvNts[] = array('code'=>'FB', 'lcode'=>'fb', 'name'=>'Facebook', 'type'=>'Social Networks', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Autopost text, image or share a link to your profile, business page, community page, or Facebook group');

if (!class_exists("nxs_snapClassFB")) { class nxs_snapClassFB extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'FB', 'lcode'=>'fb', 'name'=>'Facebook', 'defNName'=>'', 'tstReq' => true, 'instrURL'=>'http://www.nextscripts.com/instructions/facebook-social-networks-auto-poster-setup-installation');      
  var $defO = array('nName'=>'', 'do'=>'1', 'appKey'=>'', 'appSec'=>'', 'tpt'=>'' ,'postType'=>'A', 'msgFormat'=>'New post (%TITLE%) has been published on %SITENAME%', 'attachInfo'=>'F', 'attachVideo'=>'N', 'pgID'=>'', 'msgATFormat'=>'%TITLE%', 'msgAFormat'=>'%EXCERPT%', 'msgACFormat'=>'', 'imgUpl'=>'T','fbURL'=>'');
  //#### Update
  public function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName']; // prr($ntOpts);
        if (empty($ntOpts['atchUse'])) $ntOpts['atchUse'] = 'F'; if (!empty($ntOpts['useFBGURLInfo']) && $ntOpts['useFBGURLInfo']=='1') $ntOpts['atchUse'] = 'F'; 
        if (isset($ntOpts['useFBGURLInfo']) && $ntOpts['useFBGURLInfo']=='0') $ntOpts['atchUse'] = 'A'; if (empty($ntOpts['fbAttchAsVid'])) $ntOpts['fbAttchAsVid'] = 'N'; 
        if (!empty($ntOpts['fbMsgAFrmt']) && empty($ntOpts['msgDAFrmt'])) { $ntOpts['msgDAFrmt'] = $ntOpts['fbMsgAFrmt']; $ntOpts['msgTAFrmt'] = '%TITLE%'; }
        if (!empty($ntOpts['fbMsgAFrmt'])) $ntOpts['atchUse'] = 'M'; if (isset($ntOpts['fbAttchAsVid']) && $ntOpts['fbAttchAsVid'] == '1') $ntOpts['fbAttchAsVid'] = 'A';        
        $ntOptsOut['msgFormat'] = (!empty($ntOpts['fbMsgFormat']))?$ntOpts['fbMsgFormat']:''; $ntOptsOut['msgAFormat'] = (!empty($ntOpts['msgTAFrmt']))?$ntOpts['msgTAFrmt']:''; $ntOptsOut['msgATFormat'] = (!empty($ntOpts['msgDAFrmt']))?$ntOpts['msgDAFrmt']:''; $ntOptsOut['msgACFormat'] = '';
        if (!empty($ntOpts['fbAppID'])) $ntOptsOut['appKey'] = $ntOpts['fbAppID'];  if (!empty($ntOpts['fbAppSec'])) $ntOptsOut['appSec'] = $ntOpts['fbAppSec']; if (!empty($ntOpts['fbPgID'])) $ntOptsOut['pgID'] = $ntOpts['fbPgID'];                
        
        $ntOptsOut['accessToken']=!empty($ntOpts['fbAppAuthToken'])?$ntOpts['fbAppAuthToken']:'';        $ntOptsOut['authUser']=!empty($ntOpts['fbAppAuthUser'])?$ntOpts['fbAppAuthUser']:''; 
        $ntOptsOut['authUserName']=!empty($ntOpts['fbAppAuthUserName'])?$ntOpts['fbAppAuthUserName']:''; $ntOptsOut['pageAccessToken']=!empty($ntOpts['fbAppPageAuthToken'])?$ntOpts['fbAppPageAuthToken']:''; 
        
        $ntOptsOut['appsecret_proof']=(!empty($ntOpts['appsecret_proof']))?$ntOpts['appsecret_proof']:''; $ntOptsOut['postType'] = $ntOpts['postType']; if (!empty($ntOpts['pgID'])) $ntOptsOut['pgID'] = $ntOpts['pgID']; 
        $ntOptsOut['attachInfo'] = $ntOpts['atchUse']; $ntOptsOut['imgUpl'] = $ntOpts['imgUpl'];
        $ntOptsOut['fbURL'] = $ntOpts['fbURL']; $ntOptsOut['destType'] = !empty($ntOpts['destType'])?$ntOpts['destType']:''; $ntOptsOut['attachVideo'] = $ntOpts['fbAttchAsVid']; 
        $ntOptsOut['riComments'] = $ntOpts['riComments']; $ntOptsOut['riCommentsAA'] = $ntOpts['riCommentsAA'];                         
        if ($ntOptsOut['imgUpl']!='T' && $ntOptsOut['imgUpl']!='A') { if ($ntOptsOut['imgUpl']=='1') $ntOptsOut['imgUpl'] = 'T'; else $ntOptsOut['imgUpl'] = 'A'; }        
        $ntOptsOut = nxs_arrMergeCheck($ntOptsOut, $this->defO); $ntOptsOut['isUpdd'] = '1'; $ntOptsOut['v'] = NXS_SETV; 
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  }  
   
  //#### Show Common Settings
  public function showGenNTSettings($ntOpts){ $this->nt = $ntOpts;  $this->showNTGroup(); }  
  //#### Show NEW Settings Page
  public function showNewNTSettings($ii){  $this->showGNewNTSettings($ii, $this->defO); }
  //#### Show Unit  Settings  
  public function checkIfSetupFinished($options) { return !empty($options['appKey']) && !empty($options['accessToken']) && (!empty($options['pgID']) || !empty($options['fbURL']) ); }
  public function makeUName($options, $ii) { return !empty($options['pgName'])?$options['pgName']: $this->ntInfo['name'].' #'.$ii; }
  public function doAuth() { $ntInfo = $this->ntInfo; global $nxs_snapSetPgURL; 
    if ( !empty($_GET['code']) && isset($_GET['state']) && substr($_GET['state'], 0, 7) == 'nxs-fb-'){ $this->showAuthTop(); echo "--== Auth ==--"; $at = $_GET['code'];  $ii = str_replace('nxs-fb-','',$_GET['state']); $gGet = array();     
      if (!empty($_SERVER['QUERY_STRING'])) parse_str($_SERVER['QUERY_STRING'], $gGet); elseif (!empty($_SERVER['argv'][0])) parse_str($_SERVER['argv'][0], $gGet); else { $gGet = $_GET; prr($_GET); unset($gGet['post_type']);} prr($gGet);  unset($gGet['code']); unset($gGet['state']); prr($gGet);
      $sturl = explode('?',$nxs_snapSetPgURL); $nxs_snapSetPgURL = $sturl[0].((!empty($gGet))?'?'.http_build_query($gGet):''); $fbo = $this->nt[$ii]; $advSet = nxs_mkRemOptsArr(nxs_getNXSHeaders()); prr($fbo); $fbo['uMsg'] = ''; 
      $tknURL = 'https://graph.facebook.com/oauth/access_token?client_id='.$fbo['appKey'].'&state=nxs-fb-'.$ii.'&redirect_uri='.urlencode($nxs_snapSetPgURL).'&client_secret='.$fbo['appSec'].'&code='.$at; $response  = nxs_remote_get($tknURL, $advSet); echo "<br/>TKN URL: "; prr($tknURL);   
      if ( (is_object($response) && (isset($response->errors))) || (is_array($response) && stripos($response['body'],'"error":')!==false )) { prr($response); die('</div></div>'); }      
      if (substr($response['body'],0,1)=='{') $params = json_decode($response['body'], true); else parse_str($response['body'], $params);  $at = $params['access_token']; echo "<br/>TKN PARAMS: "; prr($params); echo "<br/>TKN RESP: "; prr($response);  
      $response  = nxs_remote_get('https://graph.facebook.com/oauth/access_token?client_secret='.$fbo['appSec'].'&client_id='.$fbo['appKey'].'&grant_type=fb_exchange_token&fb_exchange_token='.$at, $advSet); 
      if ((is_object($response) && isset($response->errors))) {  prr($response); die('</div></div>');}
      if (substr($response['body'],0,1)=='{') $params = json_decode($response['body'], true); else parse_str($response['body'], $params);  $at = $params['access_token']; $fbo['accessToken'] = $at; 
      /* What is it? What I tried??
      $response = nxs_remote_get('https://graph.facebook.com/oauth/client_code?access_token='.$fbo['accessToken'].'&client_secret='.$fbo['appSec'].'&redirect_uri='.urlencode($nxs_snapSetPgURL).'&client_id='.$fbo['appKey'], $advSet); 
      $code = CutFromTo($response['body'],'code":"','"');
      $response = nxs_remote_get('https://graph.facebook.com/oauth/access_token?code='.$code.'&client_id='.$fbo['appKey'].'&redirect_uri='.urlencode($nxs_snapSetPgURL).'&machine_id=');
      if (substr($response['body'],0,1)=='{') $params = json_decode($response['body'], true); else parse_str($response['body'], $params);  $at = $params['access_token']; $fbo['accessTokenUser'] = $at;       
      prr($response); //die();
      */           
      $aacct = array('access_token'=>$fbo['accessToken'], 'appsecret_proof'=>hash_hmac('sha256', $fbo['accessToken'], $fbo['appSec']), 'method'=>'get');        
      //$uurl = "https://graph.facebook.com/".$fbo['appKey'].'?'.http_build_query($aacct, null, '&'); prr($uurl); $res = nxs_remote_get( $uurl, $advSet); prr($res); die('</div></div>'); 
      $uurl = "https://graph.facebook.com/me?".http_build_query($aacct, null, '&'); prr($uurl); $res = nxs_remote_get( $uurl, $advSet); 
      if (is_nxs_error($res) || empty($res['body'])) {  echo "Can't get Facebook User."; prr($res); die('</div></div>');} else {
        $user = json_decode($res['body'], true); if (empty($user)) {echo "Can't get Facebook User. JSON Error. "; prr($res); die('</div></div>');} else {
          if (!empty($user['id'])) {  echo "<b>-= Got user: </b>"; $fbo['authUser'] = $user['id'];  $fbo['authUserName'] = $user['name'].(!empty($user['username'])?" (".$user['username'].")":'');  prr($user); 
            if (empty($fbo['pgID'])) $fbo['pgID'] = $user['id']; else { $advSet = nxs_mkRemOptsArr(nxs_getNXSHeaders()); $aacct = array('access_token'=>$fbo['accessToken'], 'method'=>'get', 'metadata'=>'1', 'limit'=>250); 
              if (empty($fbo['tpt'])) $aacct['appsecret_proof'] = hash_hmac('sha256', $fbo['accessToken'], $fbo['appSec']); $resP = nxs_remote_get('https://graph.facebook.com/'.$fbo['pgID'].'?'.http_build_query($aacct, null, '&'), $advSet); $resP = json_decode($resP['body'], true);
              if (!empty($resP['metadata']) && !empty($resP['metadata']['type']) && $resP['metadata']['type']=='page') { echo "<br/>---==== GETTING TOKEN FOR ALREADY SELECTED PAGE - ID: ".$fbo['pgID'].'<br/>';
                $fbo = $this->getPageToken($fbo); if (!empty($fbo['uMsg'])) { echo '<b style="color:red">';  prr($fbo['uMsg']); if (!empty($user['id'])) nxs_save_glbNtwrks($ntInfo['lcode'],$ii,$fbo,'*');  die("<br/>---==== Can't get TOKEN for selected PAGE ID: ".$fbo['pgID'].'</b>'); } 
                  else  echo "<br/>---====OK. GOT TOKEN SUCCESSFULLY<br/><br/>";
              } else $fbo['pageAccessToken'] = $fbo['accessToken'];
            }
          } else {echo "Can't get User. NO USER RETURNED. "; prr($res); die('</div></div>');}
        }
      } if (!empty($user['id'])) { nxs_save_glbNtwrks($ntInfo['lcode'],$ii,$fbo,'*'); 
        echo '<div style="text-align:center;color:green; font-weight: bold; font-size:22px;" >ALL OK. You have been authorized. Refreshing page....</div><script type="text/javascript">setTimeout(function(){ window.location = "'.$nxs_snapSetPgURL.'"; }, 3000);</script>'; die('</div></div>');   
      } die('</div></div>'); 
    }    
  }
  function fbURLToPageID($fbo, $ii){ if (empty($fbo['pgID']) && !empty($fbo['fbURL'])) { if (empty($fbo['pgID']))  { $fbo['uMsg'] = ''; 
      if ( substr($fbo['fbURL'], 0, 4)!='http' )  $fbo['fbURL'] = 'http://'.$fbo['fbURL']; $fbPgID = $fbo['fbURL']; if (substr($fbPgID, -1)=='/') $fbPgID = substr($fbPgID, 0, -1);  $fbPgID = substr(strrchr($fbPgID, "/"), 1);
        if (strpos($fbPgID, '?')!==false) $fbPgID = substr($fbPgID, 0, strpos($fbPgID, '?')); if (strpos($fbPgID, '-')!==false) { $possID = substr(strrchr($fbPgID, "-"), 1);   $fbPgID = (strlen($possID)>10 && is_numeric($possID))?$possID:$fbPgID; }
        $fbo['pgID'] = $fbPgID; if (strpos($fbo['fbURL'], '?')!==false) $fbo['fbURL'] = substr($fbo['fbURL'], 0, strpos($fbo['fbURL'], '?')); //prr($fbo, 'BCHEK');// prr($pval); prr($options[$ii]); // die();
      } if (!is_numeric($fbo['pgID'])) { $pgInfo = $this->getPageInfo($fbo); /* prr($pgInfo, "PAGE INFO"); */  if (!is_array($pgInfo)) $fbo['uMsg'].=$pgInfo; else $fbo['pgID'] = $pgInfo['id']; }    
      if (!empty($fbo['pgID'])) { unset($fbo['fbURL']); nxs_save_glbNtwrks($this->ntInfo['lcode'],$ii,$fbo,'*'); }
    } return $fbo;
  }    
  function getPageInfo($fbo){  $advSet = nxs_mkRemOptsArr(nxs_getNXSHeaders()); $aacct = array('access_token'=>$fbo['accessToken'], 'method'=>'get', 'metadata'=>'1', 'limit'=>250); if (empty($fbo['tpt'])) $aacct['appsecret_proof'] = hash_hmac('sha256', $fbo['accessToken'], $fbo['appSec']);
      $resP = nxs_remote_get('https://graph.facebook.com/'.$fbo['pgID'].'?'.http_build_query($aacct, null, '&'), $advSet); if ((is_object($resP) && isset($resP->errors))) return 'Error  PG_INFO #1: '.print_r($resP, true);
      $page = json_decode($resP['body'], true); if ( is_array($page) && !empty($page['error']) ) return 'Error PG_INFO #2: '.(!empty($page['error'])?print_r($page['error'], true):''); return $page;
  }
  function getPageToken($fbo){ $advSet = nxs_mkRemOptsArr(nxs_getNXSHeaders()); $errMsg = ''; $fbPgID = $fbo['pgID'];      
      $aacct = array('access_token'=>$fbo['accessToken'], 'method'=>'get', 'limit'=>250); if (empty($fbo['tpt'])) $aacct['appsecret_proof'] = hash_hmac('sha256', $fbo['accessToken'], $fbo['appSec']);
      $res = nxs_remote_get( "https://graph.facebook.com/$fbPgID?fields=access_token&".http_build_query($aacct, null, '&'), $advSet); // prr($res);
      if (is_nxs_error($res) || empty($res['body']) || $res['response']['code']!='200') { $errMsg = "Can't get Page Token. ".print_r($res, true); $fbo['uMsg'] = $errMsg; return $fbo; } else {
        $token = json_decode($res['body'], true); if (empty($token)) { $errMsg =  "Can't get Page Token. JSON Error. ".print_r($res, true); $fbo['uMsg'] = $errMsg; return $fbo; } else {
          if (!empty($token['error'])) if (!empty($token['error']['message'])) { $errMsg = $token['error']['message'];
            if ( stripos($errMsg, 'Unknown fields: access_token')!==false || stripos($errMsg, 'Cannot query users by their username')!==false || stripos($errMsg, 'node type (User)')!==false) {
               $token['access_token'] = $fbo['accessToken']; $fbo['destType'] =  (stripos($fbo['fbURL'], '/groups/')!=false)?'gr':'pr'; } else { 
                 if (stripos($errMsg, 'Unsupported get request')!==false) $errMsg = "<b style='color:red;'>Error </b>: Your Facebook URL ( <i>".$fbo['fbURL']."</i> ) is either incorrect or authorzing user don't have rights to post there.<br/><br/>Reported Error: ". $errMsg."\n";  
                 $fbo['uMsg'] = $errMsg; return $fbo;
               }
          }
          if (!empty($token['access_token'])) { $fbo['pageAccessToken'] = $token['access_token']; } else { $errMsg =  "Can't get Page Token. NO TOKEN RETURNED. Are you sure that user you are trying to authorize is an admin of the page? This message means user was authorized as profile, but page refused to return authorization token. This usually happens when user has <b>no rights</b> to post to that page. ".print_r($res, true); $fbo['uMsg'] = $errMsg; return $fbo; }
        } 
      } $fbo['uMsg'] = $errMsg; return $fbo;
  }
  function getListOfPages($networks){ $opVal = array(); $opNm = 'nxs_snap_fb_'.sha1('nxs_snap_fb'.$_POST['u'].$_POST['p']); $opVal = nxs_getOption($opNm); $ii = $_POST['ii']; $pgs = ''; if (empty($options['pgID'])) $options['pgID'] = '';
     $currPstAs = !empty($_POST['pgID'])?$_POST['pgID']:(!empty($networks['fb'][$ii])?$networks['fb'][$ii]['pgID']:'');
     if (empty($_POST['force']) && !empty($opVal['pageList']) ) $pgs = $opVal['pageList']; else { $options = $networks['fb'][$ii];        
       $advSet = nxs_mkRemOptsArr(nxs_getNXSHeaders()); $aacct = array('access_token'=>$options['accessToken'], 'method'=>'get', 'limit'=>250); if (empty($options['tpt'])) $aacct['appsecret_proof'] = hash_hmac('sha256', $options['accessToken'], $options['appSec']); 
       //## Account Info 
       $resP = nxs_remote_get('https://graph.facebook.com/'.$options['authUser'].'/?'.http_build_query($aacct, null, '&'), $advSet);  //prr($resP, 'ACCOUNT'); 
       if (is_nxs_error($resP) || empty($resP['body'])) { $outMsg= 'Auth Error Account #1: '.print_r($resP, true);  if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
       $accInfo = json_decode($resP['body'], true); if ((is_array($accInfo) && !empty($accInfo['error']))) { $outMsg = 'Auth Error Account #2: '.print_r($accInfo['error'], true); if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }       
       $pgs .= '<option class="nxsTeal" '.($options['pgID']==$accInfo['id'] ? 'selected="selected"':'').' value="'.$accInfo['id'].'">Profile: '.$accInfo['name'].' ('.$accInfo['id'].')</option>'; 
       //## List of pages       
       $resP = nxs_remote_get('https://graph.facebook.com/'.$options['authUser'].'/accounts?'.http_build_query($aacct, null, '&'), $advSet); 
       if (is_nxs_error($resP) || empty($resP['body'])) { $outMsg= 'Auth Error #1: '.print_r($resP, true);  if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
       $pages = json_decode($resP['body'], true); if ((is_array($pages) && !empty($pages['error']))) { $outMsg = 'Auth Error #2: '.print_r($pages['error'], true); if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }       
       if (!empty($pages['data'])) { $pages = $pages['data']; $opVal = array();
         $nxPgL = array(); foreach ($pages as $pg) $nxPgL[] = array('id'=>$pg['id'], 'nm'=>$pg['name'], 'tk'=>$pg['access_token']);           
         if (!empty($nxPgL)) { uasort($nxPgL, array($this, 'pgCmp')); $pgs .= '<option disabled>'.__('Pages', 'social-networks-auto-poster-facebook-twitter-g').'</option>'; 
           foreach ($nxPgL as $pg) $pgs .= '<option class="nxsBlue" '.($options['pgID']==$pg['id'] ? 'selected="selected"':'').' value="p'.$pg['id'].'">&nbsp;&nbsp;&nbsp;'.$pg['nm'].' ('.$pg['id'].')</option>';
         }
       }
       //## List of Groups       
       $resP = nxs_remote_get('https://graph.facebook.com/'.$options['authUser'].'/groups?'.http_build_query($aacct, null, '&'), $advSet); // prr($resP, 'GROUPS');
       if (is_nxs_error($resP) || empty($resP['body'])) { $outMsg= 'Auth Error #1: '.print_r($resP, true);  if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
       $pages = json_decode($resP['body'], true); if ((is_array($pages) && !empty($pages['error']))) { $outMsg = 'Auth Error #2: '.print_r($pages['error'], true); if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }       
       if (!empty($pages['data'])) { $pages = $pages['data']; $opVal = array();
         $nxGpO = array(); $nxGpC = array(); $nxGpS = array(); foreach ($pages as $pg) { $arr = array('id'=>$pg['id'], 'nm'=>$pg['name']); //prr($pg);
         if ($pg['privacy']=='OPEN') $nxGpO[] = $arr; elseif ($pg['privacy']=='SECRET' && (!isset($pg['administrator']) || !empty($pg['administrator']))) $nxGpS[] = $arr; 
           elseif ($pg['privacy']=='CLOSED' && (!isset($pg['administrator']) || !empty($pg['administrator']))) $nxGpC[] = $arr; }          
         if (!empty($nxGpO)) { uasort($nxGpS, array($this, 'pgCmp')); $pgs .= '<option disabled>'.__('Public Groups', 'social-networks-auto-poster-facebook-twitter-g').'</option>';
           foreach ($nxGpO as $pg) $pgs .= '<option class="nxsGreen" '.($options['pgID']==$pg['id'] ? 'selected="selected"':'').' value="'.$pg['id'].'">&nbsp;&nbsp;&nbsp;'.$pg['nm'].' ('.$pg['id'].')</option>'; 
         }
         if (!empty($nxGpC)) { uasort($nxGpC, array($this, 'pgCmp')); $pgs .= '<option disabled>'.__('Closed Groups', 'social-networks-auto-poster-facebook-twitter-g').'</option>';
           foreach ($nxGpC as $pg) $pgs .= '<option class="nxsOrange" '.($options['pgID']==$pg['id'] ? 'selected="selected"':'').' value="'.$pg['id'].'">&nbsp;&nbsp;&nbsp;'.$pg['nm'].' ('.$pg['id'].')</option>'; 
         }
         if (!empty($nxGpS)) { uasort($nxGpS, array($this, 'pgCmp')); $pgs .= '<option disabled>'.__('Secret Groups', 'social-networks-auto-poster-facebook-twitter-g').'</option>';
           foreach ($nxGpS as $pg) $pgs .= '<option class="nxsDarkOrange" '.($options['pgID']==$pg['id'] ? 'selected="selected"':'').' value="'.$pg['id'].'">&nbsp;&nbsp;&nbsp;'.$pg['nm'].' ('.$pg['id'].')</option>'; 
         }
         
         $opVal['pageListArr'] = $nxPgL; // $opVal['pageList'] = $pgs;   
       }
       
     } $pgCust = (!empty($pgs) && !empty($currPstAs) && stripos($pgs,$currPstAs)===false)?'<option selected="selected" value="'.$currPstAs.'">'.$currPstAs.'</option>':'';     
     if (!empty($_POST['isOut'])) echo $pgCust.$pgs.'<option style="color:#BD5200" value="a">'.__('...enter the Page ID').'</option>'; // .'<option style="color:#BD5200" value="a">'.__('...enter the SubReddit ID').'</option>';
     $opVal['pageList'] = $pgs; nxs_saveOption($opNm, $opVal); return $opVal;
  }
  
  function pgCmp($a, $b) { return strcasecmp ($a['nm'],$b['nm']); }
 
  
  function accTab($ii, $options, $isNew=false){ global $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $ntU = $ntInfo['code']; if (empty($options['uMsg'])) $options['uMsg'] = ''; // prr($options); 
    if (!empty($options['accessToken']) && !empty($options['authUser'])) { if (empty($options['authUser'])) $options['authUser'] = ''; $options = $this->fbURLToPageID($options, $ii);       
      if (!is_numeric($options['pgID'])) { $pgInfo = $this->getPageInfo($options); /* prr($pgInfo, "PAGE INFO"); */  if (!is_array($pgInfo)) $options['uMsg'].=$options; else $options['pgID'] = $pgInfo['id']; }          
      $opNm = 'nxs_snap_fb_'.sha1('nxs_snap_fb'.$options['authUser'].$options['appKey']); $opVal = nxs_getOption($opNm);// prr($opVal);
      if (empty($opVal)) { $tPST = (!empty($_POST))?$_POST:'';  $_POST['pgID'] = $options['pgID']; $_POST['u'] = $options['authUser']; $_POST['p'] = $options['appKey']; $_POST['ii'] = $ii; $ntw[$nt][$ii]=$options; $opVal = $this->getListOfPages($ntw); $_POST = $tPST; }
      if (!empty($opVal) & !is_array($opVal)) $options['uMsg'] .= $opVal; else { if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } 
      ?><br/ ><div style="width:100%; font-size: 14px;"><b><?php _e('Where to Post', 'nxs_snap'); ?></b>.&nbsp;<?php _e('Please select your profile/page/group', 'social-networks-auto-poster-facebook-twitter-g'); ?>.<span style="color:#580058; font-size: 12px;">&nbsp;(<?php _e('Please see ', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <a href="http://nxs.fyi/fb-not-in-the-list" target="_blank"><?php _e('Why Page/Group is not in the list and how to add it', 'social-networks-auto-poster-facebook-twitter-g'); ?>)</a></span></div>
       <div id="nxsFBInfoDiv<?php echo $ii; ?>">
         <div style="width:100%;">
          <div>                   
          <select id="fbpgID<?php echo $ii; ?>" onchange="nxs_fbPageChange('<?php echo $ii;?>',jQuery(this));" name="fb[<?php echo $ii;?>][pgID]">
            <?php $pgi = !empty($options['pageList'])?$options['pageList']:''; 
              if (!empty($options['pgID'])) { echo (!empty($options['pgID']) && stripos($pgi,$options['pgID'])===false)?'<option selected="selected" value="'.$options['pgID'].'">'.$options['pgID'].'</option>':''; }            
              if (!empty($options['pgID'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['pgID'].'"','selected="selected" value="'.$options['pgID'].'"',$pgi); 
                $pgi = str_ireplace('value="p'.$options['pgID'].'"','selected="selected" value="p'.$options['pgID'].'"',$pgi);               
              } echo $pgi;
            ?><option value="a"><?php _e('.... Enter the Page ID'); ?></option>
          </select> <div id="nxsFBInfoDivBlock<?php echo $ii; ?>" style="display: inline-block;">
        <input type="text"  name="fb[<?php echo $ii;?>][pgIDCst]" style="width:450px;display: none;" id="fbInpCst<?php echo $ii; ?>" value="<?php echo $options['pgID']; ?>" onblur="nxs_InpToDDBlur(jQuery(this));" onchange="nxs_InpToDDChange(jQuery(this));" data-tid="fbpgID<?php echo $ii; ?>" />  
          <div style="display: inline-block;"><a onclick="nxs_fbGetPages(<?php echo $ii;?>, 1); jQuery(this).blur(); return false;" href="#"><img id="<?php echo $nt.$ii;?>rfrshImg" style="vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/refresh16.png' /></a></div></div> <img id="<?php echo $nt.$ii;?>ldImg" style="display: none;vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          <?php if (empty($options['pgID'])) {?> <div style="display: inline-block;" class="blnkg">&lt;=== <?php _e('Please select where to post', 'social-networks-auto-poster-facebook-twitter-g'); ?> ===</div><?php } ?>             
          </div>   
          </div> <div style="color:#BB2727;" id="nxsFBMsgDiv<?php echo $ii; ?>"><pre><?php if (!empty($options['uMsg'])) echo $options['uMsg']; ?></pre></div>                                                                                                    
       </div> <input type="hidden" id="fbAuthUser<?php echo $ii; ?>" value="<?php echo $options['authUser']; ?>"/> <br/>
       <?php } else { ?> <div style="width:100%; font-size: 14px;"><b><?php _e('Where to Post', 'nxs_snap'); ?></b><br/><span style="color:#008000">&nbsp;&nbsp;&nbsp;<?php _e('Please enter App ID, App Secret and Authorize Your Account to be able to choose where to post....', 'social-networks-auto-poster-facebook-twitter-g'); ?></span></div>
         <div style="color:#BB2727;" id="nxsFBMsgDiv<?php echo $ii; ?>"><pre><?php if (!empty($options['uMsg'])) echo $options['uMsg']; ?></pre></div> 
       <?php } ?>
       <?php if (!empty($options['tpt'])) { echo '<span style="color:red;">*************'; _e('Third Party Auth Token is used', 'social-networks-auto-poster-facebook-twitter-g'); echo '</span><br/><br/>'; } else { 
             $this->elemKeySecret($ii,'App ID','App Secret', $options['appKey'], $options['appSec'],'appKey','appSec','https://developers.facebook.com/apps/'); ?><br/><br/><?php $this->elemMsgFormat($ii,'Post Format','msgFormat',$options['msgFormat']); 
         } ?>
    
      <div style="width:100%;"><strong style="font-size: 16px;" id="altFormatText">Post Type:</strong>&lt;-- (<a id="showShAtt" onmouseout="hidePopShAtt('<?php echo $ii; ?>X');" onmouseover="showPopShAtt('<?php echo $ii; ?>X', event);" onclick="return false;" class="underdash" href="http://www.nextscripts.com/blog/"><?php _e('What\'s the difference?', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>)  </div>                      
<div style="margin-left: 10px;">
        
        <input type="radio" name="fb[<?php echo $ii; ?>][postType]" value="T" <?php if ($options['postType'] == 'T') echo 'checked="checked"'; ?> onchange="if (jQuery(this).is(':checked')) jQuery('.nxsDivFBPostTypeOpts<?php echo $ii; ?>').hide();"/> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                    
        
        <input type="radio" name="fb[<?php echo $ii; ?>][postType]" value="I" <?php if ($options['postType'] == 'I') echo 'checked="checked"'; ?> onchange="if (jQuery(this).is(':checked')) { jQuery('.nxsDivFBPostTypeOpts<?php echo $ii; ?>').hide(); jQuery('#nxsDivFBImgUplOpts<?php echo $ii; ?>').show();}"/> <?php _e('Image Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('big image with text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>
          <div style="width:100%; margin-left: 15px;<?php if (empty($options['postType']) || $options['postType'] != 'I') echo "display:none;"; ?>" id="nxsDivFBImgUplOpts<?php echo $ii; ?>" class="nxsDivFBPostTypeOpts<?php echo $ii; ?>" ><strong><?php _e('Upload Images to', 'social-networks-auto-poster-facebook-twitter-g'); ?>:&nbsp;</strong> 
             <input value="A" id="apFBImgUplAPP<?php echo $ii; ?>" type="radio" name="fb[<?php echo $ii; ?>][imgUpl]" <?php if ($options['imgUpl'] == 'A') echo "checked"; ?> /> <?php _e('App Album', 'social-networks-auto-poster-facebook-twitter-g'); ?> 
             <input value="T" id="apFBImgUplTML<?php echo $ii; ?>" type="radio" name="fb[<?php echo $ii; ?>][imgUpl]" <?php if ($options['imgUpl'] != 'A') echo "checked"; ?> /> 
              <?php _e('Timeline', 'social-networks-auto-poster-facebook-twitter-g'); ?> &lt;-- (<a id="showShAtt" onmouseout="hidePopShAtt('<?php echo $ii; ?>I');" onmouseover="showPopShAtt('<?php echo $ii; ?>I', event);" onclick="return false;" class="underdash" href="http://www.nextscripts.com/blog/"><?php _e('What\'s the difference?', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>)      
          </div>        
        
        <input type="radio" name="fb[<?php echo $ii; ?>][postType]" value="A" <?php if ( !isset($options['postType']) || $options['postType'] == '' || $options['postType'] == 'A') echo 'checked="checked"'; ?> onchange="if (jQuery(this).is(':checked')) { jQuery('.nxsDivFBPostTypeOpts<?php echo $ii; ?>').hide(); jQuery('#nxsDivFBAttchLinkOpts<?php echo $ii; ?>').show();}" /> <?php _e('Text Post with "attached" link', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>

<div id="nxsDivFBAttchLinkOpts<?php echo $ii; ?>" class="nxsDivFBPostTypeOpts<?php echo $ii; ?>" style="width:100%; margin-left: 15px; line-height: 22px;<?php if (!empty($options['postType']) && $options['postType'] != 'A') echo "display:none;"; ?>">
     
        <strong><?php _e('If post has a video:', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;</strong> 
          <input value="N" type="radio" name="fb[<?php echo $ii; ?>][attachVideo]" <?php if (empty($options['attachVideo']) || $options['attachVideo'] == 'N' || $options['attachVideo'] == 'A') echo 'checked="checked"'; ?> /><?php _e('Do nothing', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;&nbsp;          
          <input value="V" id="nxsRBVidThumbV<?php echo $ii; ?>" type="radio" name="fb[<?php echo $ii; ?>][attachVideo]" <?php if ($options['attachVideo'] == 'V') echo 'checked="checked"'; ?> /> 
          <?php _e('Attach Video(!)', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;
          &lt;-- (<a id="showShAtt" onmouseout="hidePopShAtt('<?php echo $ii; ?>Video');" onmouseover="showPopShAtt('<?php echo $ii; ?>Video', event);" onclick="return false;" class="underdash" href="#"><?php _e('What\'s the difference?', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>)
          <br/>          
      
</div><br/></div>
  
<div class="popShAtt" style="z-index: 9999; width: 350px;" id="popShAtt<?php echo $ii; ?>I"><h3><?php _e('Where to upload Images', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3> <b><?php _e('(App Album)', 'social-networks-auto-poster-facebook-twitter-g'); ?></b> <?php _e('Facebook automatically creates an album for your app. Images will be grouped there as in any regular album.', 'social-networks-auto-poster-facebook-twitter-g'); ?>  <br/><br/><b><?php _e('(Timeline)', 'social-networks-auto-poster-facebook-twitter-g'); ?></b> <?php _e('Images will be posted to the special "Wall/Timeline" album and won\'t be grouped. "Wall/Timeline" album must exist. It\'s created when first image posted to timeline manually.', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>  
<div class="popShAtt" style="z-index: 9999" id="popShAtt<?php echo $ii; ?>"><h3><?php _e('Two ways of attaching post on Facebook', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3><img src="<?php echo NXS_PLURL; ?>img/fb2wops.png" width="600" height="257" alt="<?php _e('Two ways of attaching post on Facebook', 'social-networks-auto-poster-facebook-twitter-g'); ?>"/></div>
<div class="popShAtt" style="z-index: 9999" id="popShAtt<?php echo $ii; ?>X"><h3><?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3><img src="<?php echo NXS_PLURL; ?>img/fbPostTypesDiff6.png" width="600" height="398" alt="<?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?>"/></div>

<div class="popShAtt" style="z-index: 9999; width: 350px;" id="popShAtt<?php echo $ii; ?>Video"><h3><?php _e('If post has a video', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3> <b><?php _e('(Do Nothing)', 'social-networks-auto-poster-facebook-twitter-g'); ?></b> <?php _e('Plugin is not going to do anything specific about video', 'social-networks-auto-poster-facebook-twitter-g'); ?> <br/><br/><b><?php _e('(Attach video page to the Facebook post)', 'social-networks-auto-poster-facebook-twitter-g'); ?></b> <?php _e('If you choose this, YouTube or Vimeo page will be attached to the Facebook post as <b>playable thumbnail</b>. Please note! <b>There will be no link to your site.</b> Please use %URL% in the message format if you need a backlink', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>  
    <br/><?php  
    if(isset($options['authUser']) && $options['authUser']>0) { ?>
            <?php _e('Your Facebook Account has been authorized.', 'social-networks-auto-poster-facebook-twitter-g'); ?> User ID: <?php _e(apply_filters('format_to_edit', htmlentities($options['authUser'].(!empty($options['authUserName'])?" - ".$options['authUserName']:''), ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>.
            <br/><?php _e('You can', 'social-networks-auto-poster-facebook-twitter-g'); ?> Re- <?php } ?>            
            <a href="#" onclick="var url = 'https://www.facebook.com/dialog/oauth?client_id='+jQuery('#fbappKey<?php echo $ii; ?>').val()+'&scope=publish_actions,manage_pages,publish_pages,user_managed_groups,user_posts,user_photos&state=nxs-fb-<?php echo $ii; ?>&redirect_uri=<?php echo trim(urlencode($nxs_snapSetPgURL));?>'; nxs_svSetAdv('<?php echo $nt; ?>', '<?php echo $ii; ?>', '<?php echo $isNew?'dom'.$ntU.$ii.'Div':'nxsAllAccntsDiv'; ?>','nxs<?php echo $ntU; ?>MsgDiv<?php echo $ii; ?>',url,'1'); return false;">Authorize Your Facebook Account</a>                    
            <?php if (!isset($options['authUser']) || $options['authUser']<1) { ?> <div class="blnkg">&lt;=== <?php _e('Authorize your account', 'social-networks-auto-poster-facebook-twitter-g'); ?> ===</div> 
            <br/><br/><i> <?php _e('If you get Facebook message:', 'social-networks-auto-poster-facebook-twitter-g'); ?> <b>"Error. An error occurred. Please try again later."</b> or <b>"Error 191"</b>  <?php _e('please make sure that domain name in your Facebook App matches your website domain exactly. Please note that www. and non www. versions are different domains.', 'social-networks-auto-poster-facebook-twitter-g'); ?></i> <?php }?>

            <br/><br/><?php
  }
  function advTab($ii, $options){?>
     <div style="width:100%;"><strong style="font-size: 16px;"><?php _e('Facebook Comments:', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong> </div>
   <div style="margin-bottom: 5px; margin-left: 10px; ">
   <p style="font-size: 11px; margin: 0px;"><?php _e('Plugin could grab comments from Facebook and import them as Wordpress Comments', 'social-networks-auto-poster-facebook-twitter-g'); ?></p>
   
   <?php global $plgn_NS_SNAutoPoster; $gOptions = $plgn_NS_SNAutoPoster->nxs_options; if ( !empty($gOptions['riActive']) && $gOptions['riActive'] == '1' ) { ?>
   <input value="1"  id="apFBMsgAFrmtA<?php echo $ii; ?>" <?php if (!empty($options['riComments']) && trim($options['riComments'])=='1') echo "checked"; ?> type="checkbox" name="fb[<?php echo $ii; ?>][riComments]"/> <strong><?php _e('Import Facebook Comments', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong>
   <br/>
   
   <div style="margin-bottom: 5px; margin-left: 10px; ">
     <input value="1"  id="apFBMsgAFrmtA<?php echo $ii; ?>" <?php if (!empty($options['riCommentsAA']) && trim($options['riCommentsAA'])=='1') echo "checked"; ?> type="checkbox" name="fb[<?php echo $ii; ?>][riCommentsAA]"/> <strong><?php _e('Auto-approve imported comments', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong></div>   
     <?php } else { echo "<br/>"; _e('Please activate the "Comments Import" from SNAP Settings Tab', 'social-networks-auto-poster-facebook-twitter-g'); } ?>
   
   </div>
   
   <div style="margin-bottom: 5px; margin-top: 10px; "> <b style="font-size: 16px;"><?php _e('Auth Token:', 'social-networks-auto-poster-facebook-twitter-g'); ?></b><br/>
   <a href="#" style="margin-left:50px; " class="button" onclick="jQuery(this).next().show(); jQuery(this).hide(); return false;">I understand that this is an unofficial feature.</a>   
   <div style="margin-bottom: 5px; margin-left: 10px; display: none;"><div style="color:red;">
   <?php _e('"Third party auth tokens" is an unofficial workaround for the limitations of the standard Facebook API functionality. By using this you are confirming that you understand and agree to:', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
   <div style="color:red; margin-left: 10px; margin-top: 5px; margin-bottom: 10px;">
   -<?php _e('<b>We do not support</b> or encourage the usage of this feature', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
   -<?php _e('This functionality might not work or might stop working at any time', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
   -<?php _e('Your <b>Facebook account might be suspended</b>', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
   -<?php _e('You are using this feature at your own risk. We are not responsible for any outcome of it\'s usage.', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/></div></div>
   <a href="#" style="margin-left:50px; " class="button" onclick="jQuery(this).next().show(); jQuery(this).hide(); return false;">I understand that this risky feature is provided "as is" with <b>no support</b> and might not work correctly.</a>   
   <div style="margin-bottom: 5px; margin-left: 10px; display: none;"> <b style="font-size: 16px;"><?php _e('Auth Token:', 'social-networks-auto-poster-facebook-twitter-g'); ?></b><?php if (empty($options['tpt'])) $options['tpt'] = ''; ?>
     <input type="text" name="fb[<?php echo $ii; ?>][tpt]" style="width: 40%;border: 1px solid #ACACAC;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['tpt'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" />   
   </div>   </div>   </div>
   
   <?php }
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){      
      if (!empty($pval['appKey']) || !empty($pval['tpt'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);  
        //## Uniqe Items                
        if (isset($pval['tpt'])) { $options[$ii]['tpt'] = trim($pval['tpt']); if (empty($options[$ii]['appKey'])) $options[$ii]['appKey'] = 'AppKey';
          if (!empty($pval['tpt'])) { $options[$ii]['pageAccessToken'] = trim($pval['tpt']);     $options[$ii]['accessToken'] = trim($pval['tpt']);    $options[$ii]['authUser'] = 'me';   }
        } else  $options[$ii]['tpt'] = ''; // prr($options);
        if (isset($pval['postType'])) $options[$ii]['postType'] = trim($pval['postType']);        
        if (isset($pval['attachInfo'])) $options[$ii]['attachInfo'] = trim($pval['attachInfo']);
        if (isset($pval['attachVideo'])) $options[$ii]['attachVideo'] = trim($pval['attachVideo']);
        
        if (isset($pval['pgID']) && $pval['pgID']!=$options[$ii]['pgID'] && $pval['pgID']!='p'.$options[$ii]['pgID']) { 
          if (!empty($pval['pgIDCst']) && $pval['pgID']==$pval['pgIDCst']) { $pval['pgIDCst'] = trim($pval['pgIDCst']); //## Custom Page
              //## URL
              if ( substr($pval['pgIDCst'], 0, 4)=='http' ) { if (strpos($pval['pgIDCst'], 'profile.php?')!==false) $pval['pgIDCst'] = CutFromTo($pval['pgIDCst'].'&', 'id=','&'); else {
                  if (strpos($pval['pgIDCst'], '?')!==false) $pval['pgIDCst'] = substr($pval['pgIDCst'], 0, strpos($pval['pgIDCst'], '?')); 
                  if (substr($pval['pgIDCst'], -1)=='/') $pval['pgIDCst'] = substr($pval['pgIDCst'], 0, -1);  $pval['pgIDCst'] = substr(strrchr($pval['pgIDCst'], "/"), 1);                 
                  if (strpos($pval['pgIDCst'], '-')!==false) { $possID = substr(strrchr($pval['pgIDCst'], "-"), 1);   $pval['pgIDCst'] = (strlen($possID)>10 && is_numeric($possID))?$possID:$pval['pgIDCst']; }
                }
              }              
              //prr($pval['pgIDCst']); prr($pval['pgID']);
              if ( (strlen($pval['pgIDCst'])<10 || !is_numeric($pval['pgIDCst'])) && (strpos($pval['pgID'], 'groups')!==false))  echo 'Please use numeric Group ID. <a href="http://nxs.fyi/faq216" target="_blank">FAQ #2.16</a>';           
              
              $options[$ii]['pgID'] = $pval['pgIDCst']; $pgInfo = $this->getPageInfo($options[$ii]); /* prr($pgInfo, "PAGE INFO"); */ if (!is_array($pgInfo)) echo 'Checking '.$options[$ii]['pgID'].' | '.$pgInfo; else $options[$ii]['pgID'] = $pgInfo['id'];
              if (!empty($pgInfo['metadata']) && !empty($pgInfo['metadata']['type']) && $pgInfo['metadata']['type']=='page') $options[$ii]['pgID'] = 'p'.$options[$ii]['pgID'];
              
          } else $options[$ii]['pgID'] = trim($pval['pgID']); // prr($options[$ii]['pgID']);
          if (substr($options[$ii]['pgID'],0,1)=='p') { $options[$ii]['pgID'] = substr($options[$ii]['pgID'],1); $options[$ii] = $this->getPageToken($options[$ii]); 
            if (!empty($options[$ii]['uMsg']) && function_exists('nxs_LogIt')) nxs_LogIt('E', 'FB', 'Page Auth', '', 'Error - FB Auth', print_r($options[$ii]['uMsg'], true));
          } else $options[$ii]['pageAccessToken'] = $options[$ii]['accessToken'];
        }
        if (isset($pval['imgUpl'])) $options[$ii]['imgUpl'] = trim($pval['imgUpl']); else $options[$ii]['imgUpl'] = 'T';
        if (isset($pval['fbURL'])) $options[$ii]['fbURL'] = trim($pval['fbURL']);
        if (isset($pval['destType'])) $options[$ii]['destType'] = trim($pval['destType']);
        
        if (isset($pval['msgACFormat'])) $options[$ii]['msgACFormat'] = trim($pval['msgACFormat']);        
        if (isset($pval['riComments'])) $options[$ii]['riComments'] = $pval['riComments']; else $options[$ii]['riComments'] = 0;        
        if (isset($pval['riCommentsAA'])) $options[$ii]['riCommentsAA'] = $pval['riCommentsAA']; else $options[$ii]['riCommentsAA'] = 0;                
        
      } elseif ( count($pval)==1 ) if (isset($pval['do'])) $options[$ii]['do'] = $pval['do']; else $options[$ii]['do'] = 0; 
    } return $options;
  }
    
  //#### Show Post->Edit Meta Box Settings
  
  function showEdPostNTSettings($ntOpts, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code']; 
      foreach($ntOpts as $ii=>$ntOpt)  { $isFin = $this->checkIfSetupFinished($ntOpt); if (!$isFin) continue;
        $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true)); if (is_array($pMeta) && !empty($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);      //   prr($ntOpt);
        
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse']; $ntOpt['ii']=$ii; $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta);         
        $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);            
        ?>
        
       <tr style="<?php echo !empty($ntOpt['do'])?'display:table-row;':'display:none;'; ?>" class="nxstbldo nxstbldo<?php echo strtoupper($nt).$ii; ?>"><th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 0px; padding-right:10px;"> <?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g'); ?> <br/>
                (<a id="showShAtt" style="font-weight: normal" onmouseout="hidePopShAtt('<?php echo $ii; ?>X');" onmouseover="showPopShAtt('<?php echo $ii; ?>X', event);" onclick="return false;" class="underdash" href="http://www.nextscripts.com/blog/"><?php _e('What\'s the difference?', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>)</th><td>     
        
        <input class="nxs_postEditCtrl"  type="radio" name="fb[<?php echo $ii; ?>][postType]" value="T" <?php if ($ntOpt['postType'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?>  - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>       
        <input class="nxs_postEditCtrl"  type="radio" name="fb[<?php echo $ii; ?>][postType]" value="I" <?php if ($ntOpt['postType'] == 'I') echo 'checked="checked"'; ?> /> <?php _e('Image Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('big image with text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>             
        <input class="nxs_postEditCtrl"  type="radio" name="fb[<?php echo $ii; ?>][postType]" value="A" <?php if ( !isset($ntOpt['postType']) || $ntOpt['postType'] == '' || $ntOpt['postType'] == 'A') echo 'checked="checked"'; ?> /> <?php _e('Text Post with "attached" blogpost', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>

<div class="popShAtt" id="popShAtt<?php echo $ii; ?>X"><h3><?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3><img src="<?php echo NXS_PLURL; ?>img/fbPostTypesDiff6.png" width="600" height="398" alt="<?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?>"/></div>
     </td></tr>
        <?php
        nxs_showImgToUseDlg($nt, $ii, $imgToUse);            
       /* ## Select Image & URL ## */  nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);     
     }
  }
  
  function showEdPostNTSettingsV4($ntOpt, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code']; $ii = $ntOpt['ii']; //prr($ntOpt['postType']);
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; 
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse'];
        
        $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);            
        ?>
        
   <div class="nxsPostEd_ElemWrap">   <div class="nxsPostEd_ElemLabel">   
       <?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g'); ?>(<a id="showShAtt" style="font-weight: normal" onmouseout="hidePopShAtt('<?php echo $ii; ?>X');" onmouseover="showPopShAtt('<?php echo $ii; ?>X', event);" onclick="return false;" class="underdash" href="http://www.nextscripts.com/blog/"><?php _e('What\'s the difference?', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>)
     </div>   
     <div class="nxsPostEd_Elem">   
        <input type="radio" name="fb[<?php echo $ii; ?>][postType]" value="T" class="nxsEdElem" <?php if ($ntOpt['postType'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?>  - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>       
        <input type="radio" name="fb[<?php echo $ii; ?>][postType]" value="I" class="nxsEdElem" <?php if ($ntOpt['postType'] == 'I') echo 'checked="checked"'; ?> /> <?php _e('Image Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('big image with text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>             
        <input type="radio" name="fb[<?php echo $ii; ?>][postType]" value="A" class="nxsEdElem" <?php if ( !isset($ntOpt['postType']) || $ntOpt['postType'] == '' || $ntOpt['postType'] == 'A') echo 'checked="checked"'; ?> /> <?php _e('Text Post with "attached" blogpost', 'social-networks-auto-poster-facebook-twitter-g'); ?>
     </div>
     <div class="popShAtt" id="popShAtt<?php echo $ii; ?>X"><h3><?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3><img src="<?php echo NXS_PLURL; ?>img/fbPostTypesDiff6.png" width="600" height="398" alt="<?php _e('Facebook Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?>"/></div>
   </div>     
     
        <?php
        // ## Select Image & URL 
        nxs_showImgToUseDlg($nt, $ii, $imgToUse);            
        nxs_showURLToUseDlg($nt, $ii, $urlToUse); 

  }
  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);          
    if (!empty($pMeta['postType'])) $optMt['postType'] = $pMeta['postType'];    
    return $optMt;
  }  
  
  function fixFBMsgs($msg) { $msg = str_replace('<br>', "\n", $msg); $msg = str_replace('<br/>', "\n", $msg); $msg = str_replace('<br />', "\n", $msg); $msg = str_ireplace('<3','&lt;3', $msg); $msg = str_ireplace('<(','&lt;(', $msg);  //## FB Smiles FIX.            
    $msg = strip_tags($msg); $msg = nxs_decodeEntitiesFull($msg); 
    $msg = str_ireplace('&#039;',"'", $msg); $msg = str_ireplace('&039;',"'", $msg); $msg = str_ireplace('&#39;',"'", $msg); $msg = str_ireplace('<3','&lt;3', $msg); $msg = str_ireplace('<(','&lt;(', $msg);  //## FB Smiles FIX 2.
    return $msg;
  }

  function adjPreFormatWP(&$options, $postID){  $post = get_post($postID); 
    if ($options['attachVideo']=='V') {$vids = nsFindVidsInPost($post, false); if (count($vids)>0) { $options['attachVideo']='A';
      if (strlen($vids[0])==11) $options['urlToUse'] = 'http://youtu.be/'.$vids[0];
      if (strlen($vids[0])==8 || strlen($vids[0])==9) $options['urlToUse'] = 'https://vimeo.com/'.$vids[0];
      if (strlen($vids[0])==15) $options['urlToUse'] = 'https://www.facebook.com/video.php?v='.$vids[0];            
    }}      
  }
  function adjPublishWP(&$options, &$message, $postID){ 
    if (!empty($postID)) { if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'full');
      if (preg_match("/noImg.\.png/i", $imgURL)) { $imgURL = ''; $isNoImg = true; }  $post = get_post($postID);                   
    } elseif ($postID==='0') { $message['urlTitle'] = 'Test Title'; $message['urlDescr'] = 'Test Descr'; $message['urlCaption'] = ''; }
    $message['urlTitle'] = $this->fixFBMsgs($message['urlTitle']); $message['urlDescr'] = $this->fixFBMsgs($message['urlDescr']); $options['msgFormat'] = $this->fixFBMsgs($options['msgFormat']);
  }  
  
  function importComments($options='', $postID='', $po='') { $ci = 0; if (empty($postID)) $postID = $_POST['pid'];
    if (empty($options)) {  global $plgn_NS_SNAutoPoster; $options = $plgn_NS_SNAutoPoster->nxs_options; } if (isset($_POST['ii'])) $options = $options[$_POST['nt']][$_POST['ii']];  
    if (empty($po)) { $po =  maybe_unserialize(get_post_meta($postID, 'snap'.strtoupper($_POST['nt']), true)); $po = $po[$_POST['ii']]; }    
    if (empty($options) || empty($options['pageAccessToken'])) return; $ptype =  get_post_type( $postID ); 
    $aacct = array('access_token'=>$options['pageAccessToken'], 'method'=>'get'); if (empty($options['tpt'])) $aacct['appsecret_proof'] = hash_hmac('sha256', $options['pageAccessToken'], $options['appSec']);
    $res = nxs_remote_get( "https://graph.facebook.com/".$po['pgID']."/comments?filter=toplevel&limit=250&".http_build_query($aacct, null, '&'), nxs_mkRemOptsArr(nxs_getNXSHeaders()));
    if (is_nxs_error($res) || empty($res['body'])) $badOut['Error'] = ' [ERROR] '.print_r($res, true); else { //prr($res);
    $ret = json_decode($res['body'], true); if (empty($ret)) $badOut['Error'] .= "JSON ERROR: ".print_r($res, true); else { //   prr($ret);    
      $impCmnts = get_post_meta($postID, 'snapImportedFBComments', true); if (!is_array($impCmnts)) $impCmnts = array(); //prr($impCmnts);   
      if (is_array($ret) && !empty($ret['data']) && is_array($ret['data'])) foreach ($ret['data'] as $comment){ $cid = $comment['id']; if (empty($cid)) continue;
      if (!empty($comment['message']) && !in_array('fbxcw'.$cid, $impCmnts)) { // prr($comment);
          if ($ptype=='topic'){ $my_post = array('post_title' => '', 'post_content' => $comment['message'], 'post_status' => 'publish', 'post_parent' => $postID, 'post_author' => 0, 'post_type' => 'reply');
              $wpCid = wp_insert_post($my_post); add_post_meta($wpCid, '_bbp_anonymous_name', $comment['from']['name']); $fid = get_post_meta($postID, '_bbp_forum_id', true);
              add_post_meta($wpCid, '_bbp_anonymous_email', $comment['from']['id'].'@facebook.com'); add_post_meta($wpCid, '_bbp_anonymous_website', 'http://www.facebook.com/'.$comment['from']['id']);
              add_post_meta($wpCid, '_bbp_topic_id', $postID); add_post_meta($wpCid, '_bbp_forum_id', $fid);
              
          } else { $commentdata = array( 'comment_post_ID' => $postID, 'comment_author' => $comment['from']['name'], 'comment_author_email' => $comment['from']['id'].'@facebook.com', 
              'comment_author_url' => 'https://www.facebook.com/'.$comment['from']['id'], 'comment_content' => $comment['message'], 'comment_date_gmt' => date('Y-m-d H:i:s', strtotime( $comment['created_time'] ) ), 'comment_type' => '');             
            $wpCid = nxs_postNewComment($commentdata, $options['riCommentsAA']=='1'); // prr($commentdata);
          } $ci++; $impCmnts[$wpCid] = 'fbxcw'.$cid; 
      } else $wpCid = array_search('fbxcw'.$cid, $impCmnts);            
      $res = nxs_remote_get( "https://graph.facebook.com/".$cid."/comments?".http_build_query($aacct, null, '&'), nxs_mkRemOptsArr(nxs_getNXSHeaders())); $replRet = json_decode($res['body'], true);
      if (is_array($replRet) && is_array($replRet['data'])) foreach ($replRet['data'] as $rComment){ $rCid = $rComment['id']; 
        if (!empty($rCid) && !empty($comment['message']) && !in_array('fbxcw'.$rCid, $impCmnts)) {  // prr($impCmnts);          
          if ($ptype=='topic'){ $my_post = array('post_title' => '', 'post_content' => $rComment['message'], 'post_status' => 'publish', 'post_parent' => $postID, 'post_author' => 0, 'post_type' => 'reply');
              $wpCid = wp_insert_post($my_post); add_post_meta($wpCid, '_bbp_anonymous_name', $rComment['from']['name']); $fid = get_post_meta($postID, '_bbp_forum_id', true);
              add_post_meta($wpCid, '_bbp_anonymous_email', $rComment['from']['id'].'@facebook.com'); add_post_meta($wpCid, '_bbp_anonymous_website', 'http://www.facebook.com/'.$rComment['from']['id']);
              add_post_meta($wpCid, '_bbp_topic_id', $postID); add_post_meta($wpCid, '_bbp_forum_id', $fid);              
          } else {
            $commentdata = array( 'comment_parent' => $wpCid, 'comment_post_ID' => $postID, 'comment_author' => $rComment['from']['name'], 'comment_author_email' => $rComment['from']['id'].'@facebook.com', 
              'comment_author_url' => 'https://www.facebook.com/'.$rComment['from']['id'], 'comment_content' => $rComment['message'], 'comment_date_gmt' => date('Y-m-d H:i:s', strtotime( $rComment['created_time'] ) ), 'comment_type' => '');
            // prr($commentdata);
            nxs_postNewComment($commentdata, $options['riCommentsAA']=='1'); 
          } $ci++; $impCmnts[] = 'fbxcw'.$rCid; 
        }
      }        
    }    
    delete_post_meta($postID, 'snapImportedFBComments'); add_post_meta($postID, 'snapImportedFBComments', $impCmnts ); 
    //## if Importing manually from Button echo result.
    if (isset($_POST['pid']) && $_POST['pid']!='') printf( _n( '%d comment has been imported.', '%d comments has been imported.', $ci, 'social-networks-auto-poster-facebook-twitter-g'), $ci );
   }}
  }
  
}}


//if (!function_exists("nxs_getBackFBComments")) { function nxs_getBackFBComments}}
// ShortCode [nxs_fbembed accnum=0]
if (!function_exists("nxs_fbembed_func")) {function nxs_fbembed_func( $atts ) { extract( shortcode_atts( array('accnum' => '0'), $atts ) );  $pid = get_the_ID(); $fbpo =  get_post_meta($pid, 'snapFB', true); $fbpo =  maybe_unserialize($fbpo);     
  if (!is_array($fbpo) || !is_array($fbpo[$accnum]) || !isset($fbpo[$accnum]['pgID']) || strpos($fbpo[$accnum]['pgID'], '_')===false ) return; $fbpo = $fbpo[$accnum]['pgID']; 
  $fbpoA = explode('_',$fbpo);  $fpg = $fbpoA[0];  $fpid = $fbpoA[1]; 
  $txtOut = '<div id="fb-root"></div> <script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/en_US/all.js#xfbml=1"; fjs.parentNode.insertBefore(js, fjs); }(document, \'script\', \'facebook-jssdk\'));</script>
<div class="fb-post" data-href="https://www.facebook.com/permalink.php?story_fbid='.$fpid.'&amp;id='.$fpg.'"><div class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/permalink.php?story_fbid='.$fpid.'&amp;id='.$fpg.'">Post</a></div></div>';
  return $txtOut;
}}
if (function_exists("add_shortcode")) add_shortcode( 'nxs_fbembed', 'nxs_fbembed_func' );


if (!function_exists("nxs_doPublishToFB")) { function nxs_doPublishToFB($postID, $options){ 
    if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassFB(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}} 

?>