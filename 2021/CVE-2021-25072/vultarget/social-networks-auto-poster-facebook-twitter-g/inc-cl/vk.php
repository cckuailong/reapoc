<?php    
//## NextScripts vKontakte(VK) Connection Class
$nxs_snapAvNts[] = array('code'=>'VK', 'lcode'=>'vk', 'name'=>'VK.Com', 'type'=>'Social Networks', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Post text, image or share a link to your profile or group page');

if (!class_exists("nxs_snapClassVK")) { class nxs_snapClassVK extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'VK', 'lcode'=>'vk', 'name'=>'VK.Com', 'defNName'=>'uName', 'tstReq' => false, 'instrURL'=>'http://www.nextscripts.com/setup-installation-vkontakte-social-networks-auto-poster-wordpress/');    
  
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  
        $ntOptsOut['url'] = $ntOpts['url']; $ntOptsOut['pgID'] = $ntOpts['vkPgID']; $ntOptsOut['pgIntID'] = $ntOpts['pgIntID']; $ntOptsOut['postType'] = $ntOpts['postType']; 
        
        $ntOptsOut['appID'] = $ntOpts['vkAppID'];  $ntOptsOut['appAuthToken'] = $ntOpts['vkAppAuthToken'];  $ntOptsOut['appAuthUser'] = $ntOpts['vkAppAuthUser']; $ntOptsOut['authResp'] = $ntOpts['apVKAuthResp']; 
        $ntOptsOut['msgFormat'] = $ntOpts['msgFrmt']; 
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  } 
  
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'url'=>'', 'pgID'=>'', 'pgIntID'=>'', 'postType'=>'A', 'appID'=>'', 'appAuthToken'=>'','appAuthUser'=>'','authResp'=>'', 'msgFormat'=>"%EXCERPT%"); 
    $this->showGNewNTSettings($ii, $defO); 
  }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['appAuthUser']); }
  function accTab($ii, $options, $isNew=false){ $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; ?>
     <div style="width:100%;"><strong><?php echo $ntInfo['name']?>&nbsp;<?php _e('URL', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> </div>
     <div style="font-size: 11px; margin: 0px;"><?php _e('Could be your VK.Com Profile, Public or Group Page', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
     <input name="vk[<?php echo $ii; ?>][url]" id="apurl" style="width: 50%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['url'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" />                
     <br/><br/>
     <div style="width:100%;"><strong><?php echo $ntInfo['name']?>&nbsp;<?php _e('Application ID', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> <a href="https://vk.com/apps?act=manage" target="_blank"><?php _e('[VK Apps]', 'social-networks-auto-poster-facebook-twitter-g'); ?></a> </div> 
    <input name="vk[<?php echo $ii; ?>][appID]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['appID'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" />  
    <br/>
    <?php  if($options['appID']!='') { ?>
    <div style="width:100%;"><strong><?php echo $ntInfo['name']?>&nbsp;<?php _e('Auth Response', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> </div><input name="vk[<?php echo $ii; ?>][authResp]" style="width: 50%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['authResp'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>
    <?php } ?>
    <?php  if($options['appID']=='') { ?>
            <?php _e('<b>Authorize Your vKontakte(VK) Account</b>. Please click "Update Settings" to be able to Authorize your account.', 'social-networks-auto-poster-facebook-twitter-g'); ?>
            <?php } else { if(isset($options['appAuthUser']) && $options['appAuthUser']>0) { ?>
            <?php _e('Your vKontakte(VK) Account has been authorized.'); ?> User ID: <?php _e(apply_filters('format_to_edit', htmlentities($options['appAuthUser'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>.
            <?php _e('You can', 'social-networks-auto-poster-facebook-twitter-g'); ?> Re- <?php } ?>      
            <a target="_blank" href="https://oauth.vk.com/authorize?client_id=<?php echo $options['appID'];?>&scope=offline,wall,photos,pages&redirect_uri=https://oauth.vk.com/blank.html&display=page&v=5.42&response_type=token<?php '&auth=vk&acc='.$ii;?>">Authorize Your vKontakte(VK) Account</a>                  
            <?php if (!isset($options['appAuthUser']) || $options['appAuthUser']<1) { ?> <div class="blnkg">&lt;=== <?php _e('Authorize your account', 'social-networks-auto-poster-facebook-twitter-g'); ?> ===</div> <?php } ?>        
            <?php } ?><br/><br/> <?php $this->elemMsgFormat($ii,'Message Text Format','msgFormat',$options['msgFormat']); ?>
    
     <div style="width:100%;"><strong id="altFormatText">Post Type:</strong> &lt;-- (<a id="showShAtt" onmouseout="hidePopShAtt('<?php echo $ii; ?>VKX');" onmouseover="showPopShAtt('<?php echo $ii; ?>VKX', event);" onclick="return false;" class="underdash" href="http://www.nextscripts.com/blog/"><?php _e('What\'s the difference?', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>) </div>                      
<div style="margin-left: 10px;">
        
        <input type="radio" name="vk[<?php echo $ii; ?>][postType]" value="T" <?php if ($options['postType'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                    
        <input type="radio" name="vk[<?php echo $ii; ?>][postType]" value="I" <?php if ($options['postType'] == 'I') echo 'checked="checked"'; ?> /> <?php _e('Image Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('big image with text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>
        <input type="radio" name="vk[<?php echo $ii; ?>][postType]" value="A" <?php if ( empty($options['postType']) || $options['postType'] == 'A') echo 'checked="checked"'; ?> /> <span><?php _e('Text Post with "attached" link', 'social-networks-auto-poster-facebook-twitter-g'); ?></span><br/>

   </div>        
   <div class="popShAtt" style="z-index: 9999" id="popShAtt<?php echo $ii; ?>VKX"><h3><?php echo $ntInfo['name']?>&nbsp;<?php _e('Post Types', 'social-networks-auto-poster-facebook-twitter-g'); ?></h3><img src="<?php echo NXS_PLURL; ?>img/vkPostTypesDiff6.png" width="600" height="257"/></div>
   <?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['appID'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]); 
        if (isset($pval['url'])) $options[$ii]['url'] = trim($pval['url']); 
        if (isset($pval['appID'])) $options[$ii]['appID'] = trim($pval['appID']);         
        //## Get VK Info        
        if (isset($pval['url']))  {  $options[$ii]['url'] = trim($pval['url']);   if ( substr($options[$ii]['url'], 0, 4)!=='http' )  $options[$ii]['url'] = 'https://'.$options[$ii]['url'];
          $vkPgID = $options[$ii]['url']; if (substr($vkPgID, -1)=='/') $vkPgID = substr($vkPgID, 0, -1);  $vkPgID = substr(strrchr($vkPgID, "/"), 1); 
          if (strpos($vkPgID, '?')!==false) $vkPgID = substr($vkPgID, 0, strpos($vkPgID, '?')); $options[$ii]['pgID'] = $vkPgID; //echo $vkPgID;
          if (strpos($options[$ii]['url'], '?')!==false) $options[$ii]['url'] = substr($options[$ii]['url'], 0, strpos($options[$ii]['url'], '?'));// prr($pval); prr($options[$ii]); // die();
        }          
        if (isset($pval['authResp'])) { $options[$ii]['authResp'] = trim($pval['authResp']); 
          $options[$ii]['appAuthToken'] = trim( CutFromTo($pval['authResp'].'&', 'access_token=','&')); 
          $options[$ii]['appAuthUser'] = trim( CutFromTo($pval['authResp']."&", 'user_id=','&')); 
          if (!empty($pval['authResp']))  { $hdrsArr = nxs_getNXSHeaders($pval['url']); $advSet = nxs_mkRemOptsArr($hdrsArr); $response = nxs_remote_get($pval['url'], $advSet); //prr($response);
            if (is_nxs_error($response)) { echo "ERROR: <br/>"; prr($response); return;} $contents = $response['body']; $contents = utf8_decode($contents);    
            if (stripos($contents, '"group_id":')!==false) { $options[$ii]['pgIntID'] =  '-'.CutFromTo($contents, '"group_id":', ','); $type='all'; }  
            if (stripos($contents, '"public_id":')!==false) { $options[$ii]['pgIntID'] =  '-'.CutFromTo($contents, '"public_id":', ','); $type='all'; }  
            if (stripos($contents, '"user_id":')!==false) {   $options[$ii]['pgIntID'] =  CutFromTo($contents, '"user_id":', ','); $type='own'; }  
          }
        } else $options[$ii]['authResp'] = ''; 
      } elseif ( count($pval)==1 ) if (isset($pval['do'])) $options[$ii]['do'] = $pval['do']; else $options[$ii]['do'] = 0; 
    } return $options;
  }
    
  //#### Show Post->Edit Meta Box Settings
  function showEdPostNTSettings($ntOpts, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];
      foreach($ntOpts as $ii=>$ntOpt)  { $isFin = $this->checkIfSetupFinished($ntOpt); if (!$isFin) continue; 
        $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true)); if (is_array($pMeta) && !empty($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);         
        
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):''; 
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse'];  $ntOpt['ii']=$ii;
         
        $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta); ?>
        <?php  $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat); ?>
       <tr style="<?php echo !empty($ntOpt['do'])?'display:table-row;':'display:none;'; ?>" class="nxstbldo nxstbldo<?php echo strtoupper($nt).$ii; ?>"><th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 0px; padding-right:10px;"> <?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g') ?> <br/>
          (<a id="showShAtt" style="font-weight: normal" onmouseout="hidePopShAtt('<?php echo $ii; ?>VKX');" onmouseover="showPopShAtt('<?php echo $ii; ?>VKX', event);" onclick="return false;" class="underdash" href="http://www.nextscripts.com/blog/"><?php _e('What\'s the difference?', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>)</th><td>     
          <input class="nxs_postEditCtrl"  type="radio" name="vk[<?php echo $ii; ?>][postType]" value="T" <?php if ($postType == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g') ?> - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>       
          <input class="nxs_postEditCtrl"  type="radio" name="vk[<?php echo $ii; ?>][postType]" value="I" <?php if ($postType == 'I') echo 'checked="checked"'; ?> /> <?php _e('Image Post', 'social-networks-auto-poster-facebook-twitter-g') ?> - <i><?php _e('big image with text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
          <input class="nxs_postEditCtrl"  type="radio" name="vk[<?php echo $ii; ?>][postType]" value="A" <?php if ( !isset($postType) || $postType == '' || $postType == 'A') echo 'checked="checked"'; ?> /> <?php _e('Text Post with "attached" blogpost', 'social-networks-auto-poster-facebook-twitter-g') ?>
          <br/><div class="popShAtt" id="popShAtt<?php echo $ii; ?>VKX"><h3>vKontakte(VK) <?php _e('Post Types', 'social-networks-auto-poster-facebook-twitter-g') ?></h3><img src="<?php echo NXS_PLURL; ?>img/vkPostTypesDiff6.png" width="600" height="257" alt="<?php _e('Post Types', 'social-networks-auto-poster-facebook-twitter-g') ?>"/></div>
        </td></tr>
        <?php /* ## Select Image & URL ## */ nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);        
     }
  }  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);  //   prr($optMt);    
      
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ 
      
  }
}}

if (!function_exists("nxs_doPublishToVK")) { function nxs_doPublishToVK($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); 
  $cl = new nxs_snapClassVK(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}}  
?>