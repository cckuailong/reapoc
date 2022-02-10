<?php    
//## NextScripts 500Px Connection Class
$nxs_snapAvNts[] = array('code'=>'WB', 'lcode'=>'wb', 'name'=>'Weibo', 'type'=>'Social Networks', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Biggest Chinese Microblogging Service. You can post your messages and images');

if (!class_exists("nxs_snapClassWB")) { class nxs_snapClassWB extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'WB', 'lcode'=>'wb', 'name'=>'weibo', 'defNName'=>'', 'tstReq' => true, 'instrURL'=>'http://www.nextscripts.com/instructions/setup-installation-weibo-social-networks-auto-poster/');      
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts;  $this->showNTGroup(); }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'appKey'=>'', 'appSec'=>'', 'inclTags'=>'1', 'cat'=>0, 'gal'=>'', 'attchImg'=>1, 'msgFormat'=>"%EXCERPT%\r\n\r\n%URL%", 'imgSize'=>'original'); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['appAppUserID']) && !empty($options['accessToken']); }
  public function doAuth() { $ntInfo = $this->ntInfo; global $nxs_snapSetPgURL; if (isset($_GET['acc'])) $options = $this->nt[$_GET['acc']];
    if ( isset($_GET['auth']) && $_GET['auth']==$ntInfo['lcode']){
        /*
      $consumer_key = $options['appKey']; $consumer_secret = $options['appSec']; $callback_url = $nxs_snapSetPgURL."&auth=".$ntInfo['lcode']."a&acc=".$_GET['acc'];
      $tum_oauth = new nxs_OAuthBaseCl($consumer_key, $consumer_secret); $tum_oauth->baseURL = 'https://api.weibo.com'; $tum_oauth->request_token_path = '/v1/oauth/request_token';
      $request_token = $tum_oauth->getReqToken($callback_url); $options['oAuthToken'] = $request_token['oauth_token']; $options['oAuthTokenSecret'] = $request_token['oauth_token_secret']; 
      prr($tum_oauth); prr($options);               
      */
      global $nxs_snapSetPgURL; $state = $ntInfo['lcode'].'a-'.$_GET['acc'];
      $url = 'https://api.weibo.com/oauth2/authorize?client_id='.$options['appKey'].'&redirect_uri='.urlencode($nxs_snapSetPgURL).'&scope=all&response_type=code&state='.$state;
      echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$url.'"</script>'; 
      die();
    }
    if ( isset($_GET['code']) && isset($_GET['state']) && stripos($_GET['state'],$ntInfo['lcode'].'a-')!==false){ $ii = explode('-',$_GET['state']); $ii = $ii[1]; $options = $this->nt[$ii]; 
      $appkey = $options['appKey']; $appSecret = $options['appSec']; $url = 'https://api.weibo.com/oauth2/access_token?client_id='.$appkey.'&client_secret='.$appSecret.'&grant_type=authorization_code&redirect_uri='.urlencode($nxs_snapSetPgURL).'&code='.$_GET['code'];
      $rep = nxs_remote_post($url); $cont = json_decode($rep['body'], true); if (empty($cont) || empty($cont['access_token'])) {prr($cont); prr($rep); die();}      
      $options['accessToken'] = $cont['access_token']; $options['appAppUserID'] = $cont['uid']; $options['appAppUserName'] = $cont['uid'];  nxs_save_glbNtwrks($ntInfo['lcode'],$ii,$options,'*');  //prr($options); die();
      if (!empty($options['appAppUserID'])) {  echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$nxs_snapSetPgURL.'"</script>';  die();}
        else die("<span style='color:red;'>ERROR: Authorization Error: <span style='color:darkred; font-weight: bold;'>".print_r($uinfo, true)."</span></span>");              
    }
  }  
  
  
  function accTab($ii, $options, $isNew=false){ global $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $this->elemKeySecret($ii,'App Key','App Secret', $options['appKey'], $options['appSec'],'appKey','appSec','http://open.weibo.com/development'); ?>
    <br/><?php $this->elemMsgFormat($ii,'Message Format','msgFormat',$options['msgFormat']); ?>
    
    <div style="margin: 0px;"><input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][attchImg]"  <?php if ((int)$options['attchImg'] == 1) echo "checked"; ?> /> <strong><?php _e('Attach Image to the Post', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong></div>
    
    <br/><br/>
    <?php  if($options['appKey']=='') { ?>
      <b><?php _e('Authorize Your '.$ntInfo['name'].' Account', 'social-networks-auto-poster-facebook-twitter-g'); ?></b> <?php _e('Please click "Update Settings" to be able to Authorize your account.', 'social-networks-auto-poster-facebook-twitter-g');  
    } else { if(isset($options['appAppUserID']) && $options['appAppUserID']>0) { 
      _e('Your '.$ntInfo['name'].' Account has been authorized.', 'social-networks-auto-poster-facebook-twitter-g'); ?> User ID: <?php _e(apply_filters('format_to_edit', htmlentities($options['appAppUserID'].' - '.$options['appAppUserName'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>.
      <?php _e('You can', 'social-networks-auto-poster-facebook-twitter-g'); ?> Re- <?php } ?>            
      <a href="<?php echo $nxs_snapSetPgURL;?>&auth=<?php echo $nt; ?>&acc=<?php echo $ii; ?>">Authorize Your <?php echo $ntInfo['name']; ?> Account</a>            
      <?php if (!isset($options['appAppUserID']) || $options['appAppUserID']<1) { ?> <div class="blnkg">&lt;=== <?php _e('Authorize your account', 'social-networks-auto-poster-facebook-twitter-g'); ?> ===</div> <?php } 
    } ?><br/><br/> <?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['appKey']) && !empty($pval['appKey'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
        //## Uniqe Items        
        if (isset($pval['attchImg'])) $options[$ii]['attchImg'] = trim($pval['attchImg']); else $options[$ii]['attchImg'] = 0;                               
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
        
          $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);  
          ?>
          <tr><td>&nbsp;</td><td><div style="margin: 0px;"><input value="0" type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][attchImg]"/>
          <input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][attchImg]"  <?php if ((int)$ntOpt['attchImg'] == 1) echo "checked"; ?> /> <strong><?php _e('Attach Image to the Post', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong></div></td></tr>          
          <?php nxs_showImgToUseDlg($nt, $ii, $imgToUse);        
    
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

if (!function_exists("nxs_doPublishToWB")) { function nxs_doPublishToWB($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassWB(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); }} 

?>