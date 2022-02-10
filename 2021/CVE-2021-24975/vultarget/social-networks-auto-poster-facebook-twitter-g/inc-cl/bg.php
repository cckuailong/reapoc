<?php    
//## NextScripts Blogger  Connection Class
$nxs_snapAvNts[] = array('code'=>'BG', 'lcode'=>'bg', 'name'=>'Blogger', 'type'=>'Blogs/Publishing Platforms', 'ptype'=>'B', 'status'=>'A', 'desc'=>'Autopost to your blog. HTML is supported');

if (!class_exists("nxs_snapClassBG")) { class nxs_snapClassBG extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'BG', 'lcode'=>'bg', 'name'=>'Blogger', 'defNName'=>'', 'tstReq' => true, 'instrURL'=>'http://www.nextscripts.com/setup-installation-blogger-social-networks-auto-poster-wordpress/');
  //#### Update
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  $ntOptsOut['blogID'] = $ntOpts['bgBlogID']; 
        if (empty($ntOpts['apiToUse'])) { if (!empty($ntOpts['APIKey'])) $ntOpts['apiToUse'] = 'bg'; if (!empty($ntOpts['bgUName']) && !empty($ntOpts['bgPass'])) $ntOpts['apiToUse'] = 'nx'; } $ntOptsOut['apiToUse'] = $ntOpts['apiToUse'];
        if ($ntOptsOut['apiToUse']=='nx') { $ntOptsOut['uName'] = $ntOpts['bgUName'];  $ntOptsOut['uPass'] = $ntOpts['bgPass'];  } else { $ntOptsOut['appKey'] = $ntOpts['APIKey'];   $ntOptsOut['appSec'] = $ntOpts['APISec']; 
           $ntOptsOut['accessToken'] = $ntOpts['AccessToken']; $ntOptsOut['accessTokenSec'] = $ntOpts['AccessTokenSecret'];  $options['refreshToken'] =  $options['RefreshToken'];  $options['accessTokenExp'] =  $options['AccessTokenExp']; $ntOptsOut['blogInfo'] = $ntOpts['blogInfo']; 
        } $ntOptsOut['inclTags'] = $ntOpts['bgInclTags']; $ntOptsOut['msgFormat'] = $ntOpts['bgMsgFormat'];  $ntOptsOut['msgTFormat'] = $ntOpts['bgMsgTFormat']; $ntOptsOut['blogInfo'] = $ntOpts['blogInfo']; $ntOptsOut['blogURL'] = $ntOpts['blogURL'];  
        $ntOptsOut['isUpdd'] = '1'; $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  }   
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts;  $this->showNTGroup(); }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'blogID'=>'', 'appKey'=>'', 'appSec'=>'', 'uName'=>'', 'uPass'=>'', 'inclTags'=>1, 'msgFormat'=>"%RAWTEXT%", 'msgTFormat'=>"%TITLE%", 'imgSize'=>'original'); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['accessToken']) || !empty($options['uPass']); }
  public function doAuth() { $ntInfo = $this->ntInfo; global $nxs_snapSetPgURL;     
    if ( isset($_GET['code']) && $_GET['code']!='' && isset($_GET['state']) && substr($_GET['state'], 0, 7) == 'nxs-bg-'){  $at = $_GET['code'];  $ii = str_replace('nxs-bg-','',$_GET['state']);
      echo "----=={ oAuth 2.0 Wordflow }==----<br/>-= This is normal technical authorization info that will dissapear (Unless you get some errors) =- <br/><br/><br/>"; 
      $gGet = $_GET; unset($gGet['code']); unset($gGet['state']); unset($gGet['post_type']); unset($gGet['post']); 
      $sturl = explode('?',$nxs_snapSetPgURL); $nxs_snapSetPgURL = $sturl[0].((!empty($gGet))?'?'.http_build_query($gGet):'');       
      $options = $this->nt[$ii]; $wprg = array();  $wprg['sslverify'] = false;
      if (isset($options['appKey'])){ echo "-="; prr($options);// die();
        $tknURL = 'https://www.googleapis.com/oauth2/v3/token?code='.$at.'&redirect_uri='.urlencode($nxs_snapSetPgURL).'&scope=&client_id='.$options['appKey'].'&client_secret='.$options['appSec'].'&grant_type=authorization_code';
        $response  = nxs_remote_post($tknURL, $wprg); prr($tknURL);      
        if((is_object($response)&&(isset($response->errors)))){ prr($response); die(); }
        if (is_array($response)&& stripos($response['body'],'"error":')!==false){ prr($response['body']); prr(json_decode($response['body'],true)); die(); }
        $resp = json_decode($response['body'], true); prr($resp); if (!is_array($resp) || empty($resp['access_token'])) { prr($resp); die(); }
        if (function_exists('get_option')) $currTime = time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ); else  $currTime = time();
        $options['accessToken'] = $resp['access_token']; $options['accessTokenSec'] = 'No Need for oAuth V2';
        $options['accessTokenExp'] = $currTime + $resp['expires_in'];  $options['refreshToken'] = $resp['refresh_token'];  echo "<br/>----=={ Expires: ".date('Y-m-d H:i:s', $options['accessTokenExp'])." }==---- <br/>";
        
        if (!empty($options['blogID'])){
          if (substr($options['blogID'], 0, 4)=='http') $tknURL = 'https://www.googleapis.com/blogger/v3/blogs/byurl/?url='.$options['blogID'].'?access_token='.$options['accessToken'];  
            else $tknURL = 'https://www.googleapis.com/blogger/v3/blogs/'.$options['blogID'].'?access_token='.$options['accessToken']; 
        }        
        $response  = nxs_remote_get($tknURL, $wprg); prr($tknURL); prr($response);  $user = json_decode($response['body'], true); prr($user);       
        if (!empty($user['url'])) { $options['blogURL'] = $user['url']; $options['blogID'] = $user['id']; $options['blogInfo'] = $user['name']." [".$user['id']."] (".$user['url'].")"; nxs_save_glbNtwrks($ntInfo['lcode'],$ii,$options,'*');                      
          ?><script type="text/javascript">window.location = "<?php echo $nxs_snapSetPgURL; ?>"</script>      
        <?php }        
      }
      die();
    } 
  }    
  
  function accTab($ii, $options, $isNew=false){ global $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode'];?>
    <div style="width:100%;"><strong><?php _e('Blogger Blog ID', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong><i><?php _e('Log to your Blogger management panel and look at the URL of your blog: http://www.blogger.com/blogger.g?blogID=8959085979163812093#allposts. Your Blog ID will be: 8959085979163812093', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][blogID]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['blogID'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>     
    <div style="display: <?php echo (empty($options['apiToUse']))?"block":"none"; ?>;">    
      <div style="width:100%; text-align: center; color:#005800; font-weight: bold; font-size: 14px;">You can choose what API you would like to use. </div>          
      <span style="color:#005800; font-weight: bold; font-size: 14px;">Blogger Native API:</span> Free built-in API from Blogger. More secure, more stable. More complicated - <b style="color: red;">requires approval of access to API by Google (3-5 days)</b> and authorization. <br/><br/>    
      <span style="color:#005800; font-weight: bold; font-size: 14px;">NextScripts API for Blogger:</span> Premium API with extended functionality. Easier to configure, but less secure - requires your password.<br/><br/>
    
      <select name="<?php echo $nt; ?>[<?php echo $ii; ?>][apiToUse]" onchange="if (jQuery(this).val()=='<?php echo $nt; ?>') { jQuery('.nxs_<?php echo $nt; ?>_nxapi_<?php echo $ii; ?>').hide(); jQuery('.nxs_<?php echo $nt; ?>_bgapi_<?php echo $ii; ?>').show(); }else { jQuery('.nxs_<?php echo $nt; ?>_bgapi_<?php echo $ii; ?>').hide(); jQuery('.nxs_<?php echo $nt; ?>_nxapi_<?php echo $ii; ?>').show(); }"><option <?php echo (empty($options['apiToUse']) || $options['apiToUse'] =='bg')?"selected":""; ?> value="bg">Blogger API</option><option <?php echo (!empty($options['apiToUse']) && $options['apiToUse'] =='nx')?"selected":""; ?> value="nx">NextScripts API</option></select><hr/>
    
    </div>
    
    <div id="nxsAPIBG<?php echo $ii; ?>" class="nxs_<?php echo $nt; ?>_bgapi_<?php echo $ii; ?>" style="display: <?php echo (empty($options['apiToUse']) || $options['apiToUse'] =='bg')?"block":"none"; ?>;"><h3>Blogger API</h3>    
      <div class="subDiv" id="sub<?php echo $ii; ?>DivL" style="display: block;"> <?php $this->elemKeySecret($ii,'Client ID','Client Secret', $options['appKey'], $options['appSec'],'appKey','appSec','https://console.developers.google.com/'); ?>
      <br/><br/>
      <?php  if($options['appKey']=='') { ?>
        <b><?php _e('Authorize Your '.$ntInfo['name'].' Account', 'social-networks-auto-poster-facebook-twitter-g'); ?></b> <?php _e('Please click "Update Settings" to be able to Authorize your account.', 'social-networks-auto-poster-facebook-twitter-g');  
      } else { if(!empty($options['accessToken']) && !empty($options['accessTokenSec'])) { 
        _e('Your '.$ntInfo['name'].' Account has been authorized.', 'social-networks-auto-poster-facebook-twitter-g'); ?> <br/>Blog ID: <?php _e(apply_filters('format_to_edit', htmlentities($options['blogInfo'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g'); ?>.
        <?php _e('You can', 'social-networks-auto-poster-facebook-twitter-g'); ?> Re- <?php } ?>            
        <a  href="https://accounts.google.com/o/oauth2/auth?redirect_uri=<?php echo trim(urlencode($nxs_snapSetPgURL));?>&response_type=code&client_id=<?php echo trim($options['appKey']);?>&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fblogger&approval_prompt=force&access_type=offline&state=<?php echo 'nxs-bg-'.$ii; ?>">Authorize Your Blogger Account</a>        
        <?php if (empty($options['accessToken'])) { ?> <div class="blnkg">&lt;=== <?php _e('Authorize your account', 'social-networks-auto-poster-facebook-twitter-g'); ?> ===</div> <?php } 
      } ?><br/><br/>
      </div>
    </div>
    <div id="nxsAPINX<?php echo $ii; ?>" class="nxs_bg_nxapi_<?php echo $ii; ?>" style="display: <?php echo (!empty($options['apiToUse']) && $options['apiToUse'] =='nx')?"block":"none"; ?>;"><h3>NextScripts API</h3>
    <?php if (class_exists('nxsAPI_GP')) { ?>                 
        <div class="subDiv" id="sub<?php echo $ii; ?>DivN" style="display: block;"><?php $this->elemUserPass($ii, $options['uName'], $options['uPass']); ?></div>          
        <?php } else { nxs_show_noLibWrn('"NextScripts API Library for Blogger" is NOT installed'); } ?>           
    </div><br/>
    
    <br/><?php $this->elemTitleFormat($ii,'Message Title Format','msgTFormat',$options['msgTFormat']); $this->elemMsgFormat($ii,'Message Format','msgFormat',$options['msgFormat']); ?>
    <div style="margin: 0px;"><input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][inclTags]"  <?php if ((int)$options['inclTags'] == 1) echo "checked"; ?> /> <strong><?php _e('Post with tags', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong></div>
    
     <?php
  }
  function advTab($ii, $options){}                             
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['blogID']) && !empty($pval['blogID'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
        //## Uniqe Items        
        if (isset($pval['inclTags'])) $options[$ii]['inclTags'] = trim($pval['inclTags']); else $options[$ii]['inclTags'] = 0;                               
        if (isset($pval['apiToUse'])) $options[$ii]['apiToUse'] = trim($pval['apiToUse']);
        if (isset($pval['blogID'])) $options[$ii]['blogID'] = trim($pval['blogID']);
      } elseif ( count($pval)==1 ) if (isset($pval['do'])) $options[$ii]['do'] = $pval['do']; else $options[$ii]['do'] = 0; 
    } return $options;
  }  
    
  //#### Show Post->Edit Meta Box Settings
  
  function showEdPostNTSettings($ntOpts, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];
      foreach($ntOpts as $ii=>$ntOpt)  { $isFin = $this->checkIfSetupFinished($ntOpt); if (!$isFin) continue; 
        $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true)); if (is_array($pMeta) && !empty($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);         
        
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse']; $ntOpt['ii']=$ii;
        
        $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta); 
        
          $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat);          
          $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);  
    
       /* ## Select Image & URL ## */  nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);     
     }
  }
  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);         
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ 
    if (!empty($postID)) { if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'full');
      if (preg_match("/noImg.\.png/i", $imgURL)) { $imgURL = ''; $isNoImg = true; }
      $message['imageURL'] = $imgURL;
    }
  }   
  
}}

if (!function_exists("nxs_doPublishToBG")) { function nxs_doPublishToBG($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassBG(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); }} 

?>
