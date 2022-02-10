<?php
//## NextScripts Flickr Connection Class
$nxs_snapAvNts[] = array('code'=>'FL', 'lcode'=>'fl', 'name'=>'Flickr', 'type'=>'Image Sharing', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Autopost images to your photostream and/or sets. Tags are supported');

if (!class_exists("nxs_snapClassFL")) { class nxs_snapClassFL extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'FL', 'lcode'=>'fl', 'name'=>'Flickr', 'defNName'=>'', 'tstReq' => true, 'instrURL'=>'http://www.nextscripts.com/instructions/flickr-social-networks-auto-poster-setup-installation/');      
  //#### Update
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  
        $ntOptsOut['msgTFormat'] = $ntOpts['msgTFrmt']; $ntOptsOut['msgFormat'] = $ntOpts['msgFrmt']; $ntOptsOut['appKey'] = $ntOpts['appKey'];  $ntOptsOut['appSec'] = $ntOpts['appSec']; 
        $ntOptsOut['accessToken'] = $ntOpts['accessToken']; $ntOptsOut['accessTokenSec'] = $ntOpts['accessTokenSec'];
        $ntOptsOut['appAppUserID'] = $ntOpts['appAppUserID']; $ntOptsOut['appAppUserName'] = $ntOpts['appAppUserName'];  
        $ntOptsOut['userURL'] = $ntOpts['userURL']; $ntOptsOut['setID'] = $ntOpts['setID'];   $ntOptsOut['inclTags'] = $ntOpts['inclTags'];       $ntOptsOut['defImg'] = $ntOpts['defImg'];       
        $ntOptsOut['isUpdd'] = '1'; $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  }   
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts;  $this->showNTGroup(); }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'appKey'=>'', 'appSec'=>'', 'inclTags'=>1, 'msgFormat'=>"%EXCERPT% \r\n\r\n%URL%", 'msgTFormat'=>"%TITLE%", 'imgSize'=>'original', 'setID'=>'', 'userURL'=>''); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['appAppUserID']) && !empty($options['accessToken']); }
  public function doAuth() { $ntInfo = $this->ntInfo; global $nxs_snapSetPgURL;     
   if ( isset($_GET['auth']) && $_GET['auth']==$ntInfo['lcode']){ require_once('apis/scOAuth.php'); $options = $this->nt[$_GET['acc']];
           $consumer_key = $options['appKey']; $consumer_secret = $options['appSec'];
           $callback_url = $nxs_snapSetPgURL."&auth=".$ntInfo['lcode']."a&acc=".$_GET['acc'];
           $tum_oauth = new wpScoopITOAuth($consumer_key, $consumer_secret);
           $tum_oauth->baseURL = 'https://www.flickr.com/services'; $tum_oauth->request_token_path = '/oauth/request_token'; $tum_oauth->access_token_path = '/oauth/access_token';
           $request_token = $tum_oauth->getReqToken($callback_url); $options['oAuthToken'] = $request_token['oauth_token']; $options['oAuthTokenSecret'] = $request_token['oauth_token_secret'];
           switch ($tum_oauth->http_code) { case 200: $url = 'https://www.flickr.com/services/oauth/authorize?oauth_token='.$options['oAuthToken'];
             nxs_save_glbNtwrks($ntInfo['lcode'],$_GET['acc'],$options,'*');
             echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$url.'"</script>'; break;
             default: echo '<br/><b style="color:red">Could not connect to Flickr. Refresh the page or try again later.</b>'; die();
           } die();
    }
    if ( isset($_GET['auth']) && $_GET['auth']==$ntInfo['lcode'].'a'){ require_once('apis/scOAuth.php'); $options = $this->nt[$_GET['acc']];
           $consumer_key = $options['appKey']; $consumer_secret = $options['appSec'];

           $tum_oauth = new wpScoopITOAuth($consumer_key, $consumer_secret, $options['oAuthToken'], $options['oAuthTokenSecret']); //prr($tum_oauth);
           $tum_oauth->baseURL = 'https://www.flickr.com/services'; $tum_oauth->request_token_path = '/oauth/request_token'; $tum_oauth->access_token_path = '/oauth/access_token';
           $access_token = $tum_oauth->getAccToken($_GET['oauth_verifier']); prr($access_token);
           $options['accessToken'] = $access_token['oauth_token'];  $options['accessTokenSec'] = $access_token['oauth_token_secret'];
           nxs_save_glbNtwrks($ntInfo['lcode'],$_GET['acc'],$options,'*');
           $tum_oauth = new wpScoopITOAuth($consumer_key, $consumer_secret, $options['accessToken'], $options['accessTokenSec']);
           echo "OK. Let's Get Profile: "; prr($access_token);
           $params = array ('format' => 'php_serial', 'method'=>'flickr.urls.getUserProfile');
           $uinfo = $tum_oauth->makeReq('https://api.flickr.com/services/rest/',$params); // prr($uinfo);die();
           if (is_array($uinfo) && isset($uinfo['user'])) { $options['appAppUserName'] = $access_token['username']."(".urldecode($access_token['fullname']).")";
             $options['appAppUserID'] = urldecode($uinfo['user']['nsid']);  $options['userURL'] = urldecode($uinfo['user']['url']);
             nxs_save_glbNtwrks($ntInfo['lcode'],$_GET['acc'],$options,'*');
           } //die();
           if (!empty($options['appAppUserID'])) {
             $gGet = $_GET; unset($gGet['auth']); unset($gGet['acc']); unset($gGet['oauth_token']);  unset($gGet['oauth_verifier']); unset($gGet['post_type']);
             $sturl = explode('?',$nxs_snapSetPgURL); $nxs_snapSetPgURL = $sturl[0].((!empty($gGet))?'?'.http_build_query($gGet):'');
             echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$nxs_snapSetPgURL.'"</script>'; die();
           } else die("<span style='color:red;'>ERROR: Authorization Error: <span style='color:darkred; font-weight: bold;'>".print_r($uinfo, true)."</span></span>");
    }  
  }    
  
  function accTab($ii, $options, $isNew=false){ global $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $this->elemKeySecret($ii,'Consumer Key','Consumer Secret', $options['appKey'], $options['appSec'],'appKey','appSec','https://www.flickr.com/services/apps/'); ?>    
    <div style="width:100%;"><strong><?php _e('Flickr Set ID', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong><i><?php _e('(Optional)', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][setID]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['setID'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>
    
    <?php  $this->elemTitleFormat($ii,'Post Title Format','msgTFormat',$options['msgTFormat']);   $this->elemMsgFormat($ii,'Post Format','msgFormat',$options['msgFormat']); ?>
    <div style="margin: 0px;"><input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][inclTags]"  <?php if ((int)$options['inclTags'] == 1) echo "checked"; ?> /> <strong><?php _e('Post with tags', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong></div>
    
    <div style="width:100%;"><strong><?php _e('Default Image to use', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong><i><?php _e('If your post does not have any images this will be used instead.', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][defImg]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['defImg'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>
    
    
    <br/><br/>
    <?php  if($options['appKey']=='') { ?>
            <b><?php _e('Authorize Your '.$ntInfo['name'].' Account', 'social-networks-auto-poster-facebook-twitter-g'); ?></b> <?php _e('Please click "Update Settings" to be able to Authorize your account.', 'social-networks-auto-poster-facebook-twitter-g'); ?>
            <?php } else { if(isset($options['appAppUserID']) && $options['appAppUserID']>0) { ?>
            <?php _e('Your '.$ntInfo['name'].' Account has been authorized.', 'social-networks-auto-poster-facebook-twitter-g'); ?> User ID: <?php _e(apply_filters('format_to_edit', htmlentities($options['appAppUserID'].' - '.$options['appAppUserName'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>.
            <?php _e('You can', 'social-networks-auto-poster-facebook-twitter-g'); ?> Re- <?php } ?>
            <a href="<?php echo $nxs_snapSetPgURL.(stripos($nxs_snapSetPgURL, '?')!==false?'&':'?');?>auth=<?php echo $nt; ?>&acc=<?php echo $ii; ?>">Authorize Your <?php echo $ntInfo['name']; ?> Account</a>

            <?php if (!isset($options['appAppUserID']) || $options['appAppUserID']<1) { ?> <div class="blnkg">&lt;=== <?php _e('Authorize your account', 'social-networks-auto-poster-facebook-twitter-g'); ?> ===</div> <?php }?>
            <?php } ?>
            <br/><br/><?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['appKey']) && !empty($pval['appKey'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
        //## Uniqe Items        
        if (isset($pval['setID'])) $options[$ii]['setID'] = trim($pval['setID']);
        if (isset($pval['defImg'])) $options[$ii]['defImg'] = trim($pval['defImg']);
      } elseif ( count($pval)==1 ) if (isset($pval['do'])) $options[$ii]['do'] = $pval['do']; else $options[$ii]['do'] = 0; 
    } return $options;
  }
    
  //#### Show Post->Edit Meta Box Settings
  
  function showEdPostNTSettings($ntOpts, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];
      foreach($ntOpts as $ii=>$ntOpt)  { $isFin = $this->checkIfSetupFinished($ntOpt); if (!$isFin) continue; 
        $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true)); if (is_array($pMeta) && !empty($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);         
        
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse']; $ntOpt['ii']=$ii; $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta); 
        $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat);          
        $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);            
        nxs_showImgToUseDlg($nt, $ii, $imgToUse);            
       /* ## Select Image & URL ## */  nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);     
     }
  }
  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);     
    if (!empty($pMeta['attchImg'])) $optMt['attchImg'] = $pMeta['attchImg']; else $optMt['attchImg'] = 0;          
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ 
    if (!empty($postID)) { if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'full');
      if (preg_match("/noImg.\.png/i", $imgURL)) { $imgURL = ''; $isNoImg = true; }
      $message['imageURL'] = $imgURL;
    }
  }   
  
}}

if (!function_exists("nxs_doPublishToFL")) { function nxs_doPublishToFL($postID, $options){ 
    if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassFL(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}} 

?>