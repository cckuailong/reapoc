<?php    
//## NextScripts 500Px Connection Class
$nxs_snapAvNts[] = array('code'=>'OK', 'lcode'=>'ok', 'name'=>'ok.ru', 'type'=>'Social Networks', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Autopost text, image or share a link to your profile or group');

if (!class_exists("nxs_snapClassOK")) { class nxs_snapClassOK extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'OK', 'lcode'=>'ok', 'name'=>'ok.ru', 'defNName'=>'', 'tstReq' => true, 'instrURL'=>'http://www.nextscripts.com/instructions/setup-installation-okru-social-networks-auto-poster/');      
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts;  $this->showNTGroup(); }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'appKey'=>'', 'appSec'=>'', 'gid'=>'', 'access_token'=>'', 'secret_session_key'=>'', 'postType'=>'A', 'msgFormat'=>"%EXCERPT%\r\n\r\n%URL%", 'imgSize'=>'original'); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['gid']) && !empty($options['appKey']); }
  public function doAuth() {  }    
  
  function accTab($ii, $options, $isNew=false){ global $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; 
    $this->elemKeySecret($ii,'App Key','App Secret', $options['appKey'], $options['appSec'],'appKey','appSec'); $this->elemKeySecret($ii,'access_token','secret_session_key', $options['access_token'], $options['secret_session_key'],'access_token','secret_session_key'); ?>
    
    <div style="width:100%;"><strong>Group ID:</strong> </div>    
    <input name="<?php echo $nt; ?>[<?php echo $ii; ?>][gid]"style="width: 50%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['gid'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" />              
    <br/><br/>   
    
    <br/><?php $this->elemMsgFormat($ii,'Message Format','msgFormat',$options['msgFormat']); ?>
    
    <div style="width:100%;"><strong id="altFormatText">Post Type:</strong></div>
    <div style="margin-left: 10px;">
      <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="T" <?php if ($options['postType'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                                  
      <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="I" <?php if ($options['postType'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Image Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Text with image', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                                  
      <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="A" <?php if ( empty($options['postType']) || $options['postType'] == 'A') echo 'checked="checked"'; ?> /> <?php _e('Post link to the blogpost', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
    </div><br/><br/>
    
    <br/><br/> <?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['appKey']) && !empty($pval['appKey'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
        //## Uniqe Items        
        if (isset($pval['gid'])) $options[$ii]['gid'] = trim($pval['gid']);
        if (isset($pval['access_token'])) $options[$ii]['access_token'] = trim($pval['access_token']);
        if (isset($pval['secret_session_key'])) $options[$ii]['secret_session_key'] = trim($pval['secret_session_key']);
        if (isset($pval['postType'])) $options[$ii]['postType'] = trim($pval['postType']); else $options[$ii]['postType'] = 'A';                               
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
          <tr class="nxstbldo <?php echo 'nxstbldo'.strtoupper($nt).$ii; ?>"><th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 0px; padding-right:10px;"> <?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g') ?> <br/></th><td>     
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="T" <?php if ($postType == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g') ?>  - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="I" <?php if ($postType == 'I') echo 'checked="checked"'; ?> /> <?php _e('Image Post', 'social-networks-auto-poster-facebook-twitter-g') ?>  - <i><?php _e('Text with Image', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="A" <?php if ( empty($postType) || $postType == 'A') echo 'checked="checked"'; ?> /><?php _e('Text Post with "attached" blogpost', 'social-networks-auto-poster-facebook-twitter-g') ?>
     </td></tr>
          <?php
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

if (!function_exists("nxs_doPublishToOK")) { function nxs_doPublishToOK($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassOK(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); }} 

?>