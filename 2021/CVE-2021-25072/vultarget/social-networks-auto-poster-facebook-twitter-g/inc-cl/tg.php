<?php    
//## NextScripts Pinterest Connection Class (##Can't replace - it has .png)
$nxs_snapAvNts[] = array('code'=>'TG', 'lcode'=>'tg', 'name'=>'Telegram', 'type'=>'Messengers', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Autopost texts, images, or links to your channel, group or chat');

if (!class_exists("nxs_snapClassTG")) { class nxs_snapClassTG extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'TG', 'lcode'=>'tg', 'name'=>'Telegram', 'defNName'=>'uName', 'tstReq' => false, 'instrURL'=>'http://www.nextscripts.com/instructions/telegram-auto-poster-setup-installation/');	
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'botTkn'=>'', 'msgTFormat'=>'', 'whToPost'=>'', 'attchImg'=>0, 'webPrev'=>1, 'msgFormat'=>'%TITLE% - %URL%'); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['botTkn']); }
  function accTab($ii, $options, $isNew=false){ $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; if (empty($options['attchImg'])) $options['attchImg'] = 0; if (!isset($options['webPrev'])) $options['webPrev'] = 1; ?> <br/ >
	
	<div style="width:100%;"><strong><?php _e('Bot Token', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong></div><input name="tg[<?php echo $ii; ?>][botTkn]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['botTkn'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>
	<div style="width:100%;"><strong><?php _e('Where to Post', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong><i><?php _e('Channel ID, Group ID or Chat ID', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div><input name="tg[<?php echo $ii; ?>][whToPost]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['whToPost'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>    
	<br/><?php $this->elemMsgFormat($ii,'Message Format','msgFormat',$options['msgFormat']); ?>
    <div style="margin: 0px;"><input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][attchImg]"  <?php if ((int)$options['attchImg'] == 1) echo "checked"; ?> /> <strong><?php _e('Attach Image to the Post', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong></div>
    <div style="margin: 0px;"><input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][webPrev]"  <?php if ((int)$options['webPrev'] == 1) echo "checked"; ?> /> <strong><?php _e('Enable Web Preview', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong></div>
    <br/><?php 
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
	foreach ($post as $ii => $pval){       
	  if (!empty($pval['botTkn']) && !empty($pval['botTkn'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
		//## Uniqe Items
		if (isset($pval['botTkn']))   $options[$ii]['botTkn'] = trim($pval['botTkn']);                
        if (isset($pval['attchImg']))   $options[$ii]['attchImg'] = trim($pval['attchImg']); else $options[$ii]['attchImg'] = 0;
        if (isset($pval['webPrev']))   $options[$ii]['webPrev'] = trim($pval['webPrev']); else $options[$ii]['webPrev'] = 0;
		if (isset($pval['whToPost']))  $options[$ii]['whToPost'] = trim($pval['whToPost']);		        
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
		 
		$this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta);  if (!isset($ntOpt['webPrev'])) $ntOpt['webPrev'] = 1; ?>
        
				
		<?php $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat); ?>
                  <tr><td>&nbsp;</td><td><div style="margin: 0px;"><input value="0" type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][webPrev]"/>
          <input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][webPrev]"  <?php if ((int)$ntOpt['webPrev'] == 1) echo "checked"; ?> /> <strong><?php _e('Enable Web Preview', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong></div></td></tr>
            <tr><td>&nbsp;</td><td><div style="margin: 0px;"><input value="0" type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][attchImg]"/>
          <input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][attchImg]"  <?php if ((int)$ntOpt['attchImg'] == 1) echo "checked"; ?> /> <strong><?php _e('Attach Image to the Post', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong></div></td></tr>
        <?php
		  /* ## Select Image & URL ## */ nxs_showImgToUseDlg($nt, $ii, $imgToUse); nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);        
	 }
  }  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);  if (!isset($optMt['webPrev'])) $optMt['webPrev'] = 1; //   prr($optMt);
	//if (!empty($pMeta['tgBoard'])) $optMt['tgBoard'] = $pMeta['tgBoard'];       
    if (isset($pMeta['webPrev'])) $optMt['webPrev'] = trim($pMeta['webPrev']); else $optMt['webPrev'] = 0;
    if (isset($pMeta['attchImg'])) $optMt['attchImg'] = trim($pMeta['attchImg']); else $optMt['attchImg'] = 0;
	return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ 
	  
  } 
  
}}

if (!function_exists("nxs_doPublishToTG")) { function nxs_doPublishToTG($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); 
  $cl = new nxs_snapClassTG(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}}  
?>