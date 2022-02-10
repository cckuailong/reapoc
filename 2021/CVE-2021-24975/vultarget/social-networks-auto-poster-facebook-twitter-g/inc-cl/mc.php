<?php    
//## NextScripts Telegram Connection Class (##Can't replace MC - it has showNTGroup, mc - ok to replace)
$nxs_snapAvNts[] = array('code'=>'MC', 'lcode'=>'mc', 'name'=>'MailChimp', 'type'=>'Email Marketing', 'ptype'=>'F', 'status'=>'A', 'desc'=>'One of the most popular email marketing tools. You can send your blogposts as email campaigns to specific subscribers');

if (!class_exists("nxs_snapClassMC")) { class nxs_snapClassMC extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'MC', 'lcode'=>'mc', 'name'=>'MailChimp', 'defNName'=>'uName', 'tstReq' => false, 'instrURL'=>'http://www.nextscripts.com/instructions/mailchimp-auto-poster-setup-installation/');    
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'apikey'=>'', 'fromEmail'=>'', 'fromName'=>'', 'dc'=>'', 'msgTFormat'=>'%TITLE%', 'listID'=>'',  'msgFormat'=>'%EXCERPT% - %URL%'); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['apikey']); }
  function accTab($ii, $options, $isNew=false){ $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; ?> <br/ >
	
	<div style="width:100%;"><strong><?php _e('API Key', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong></div><input name="mc[<?php echo $ii; ?>][apikey]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['apikey'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>
	<div style="width:100%;"><strong><?php _e('Recipients List ID', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong><i><?php _e('The unique recipients list id', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div><input name="mc[<?php echo $ii; ?>][listID]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['listID'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>    	
	<div style="width:100%;"><strong><?php _e('From name', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong><i><?php _e('The [From] name on the campaign (not an email address).', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div><input name="mc[<?php echo $ii; ?>][fromName]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['fromName'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>
	<div style="width:100%;"><strong><?php _e('From Email', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong><i><?php _e('The reply-to email address for the campaign', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div><input name="mc[<?php echo $ii; ?>][fromEmail]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['fromEmail'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>
	<br/><?php $this->elemTitleFormat($ii,'Message Subject','msgTFormat',$options['msgTFormat']); $this->elemMsgFormat($ii,'Message Text','msgFormat',$options['msgFormat']); 
  }
  function advTab(){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
	foreach ($post as $ii => $pval){       
	  if (!empty($pval['apikey']) && !empty($pval['apikey'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
		//## Uniqe Items
		if (isset($pval['apikey'])) { $options[$ii]['apikey'] = trim($pval['apikey']); $options[$ii]['dc'] = end(explode('-',trim($pval['apikey']))); }
		if (isset($pval['listID'])) $options[$ii]['listID'] = trim($pval['listID']);                
		if (isset($pval['fromEmail']))  $options[$ii]['fromEmail'] = trim($pval['fromEmail']);
		if (isset($pval['fromName']))  $options[$ii]['fromName'] = trim($pval['fromName']);
	  } elseif ( count($pval)==1 ) if (isset($pval['do'])) $options[$ii]['do'] = $pval['do']; else $options[$ii]['do'] = 0; 
	} return $options;
  }
	
  //#### Show Post->Edit Meta Box Settings
  function showEdPostNTSettings($ntOpts, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];
	  foreach($ntOpts as $ii=>$ntOpt)  { $isFin = $this->checkIfSetupFinished($ntOpt); if (!$isFin) continue; 
		$pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true)); if (is_array($pMeta) && !empty($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);         
		
		if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
		$msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):''; 
		$doNT = $ntOpt['do'] && (is_array($pMeta) || $ntOpt['fltrsOn']!='1');   $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse'];  $ntOpt['ii']=$ii; $ntOpt['doNT'] = $doNT;
		 
		$this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta); ?>
				
		<?php $this->elemEdTitleFormat($ii, __('Subject Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat);  $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat); 
		  /* ## Select Image & URL ## */ nxs_showImgToUseDlg($nt, $ii, $imgToUse); nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);        
	 }
  }  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);     
	//if (!empty($pMeta['mcBoard'])) $optMt['mcBoard'] = $pMeta['mcBoard'];       
	return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ 
	  
  }
  
  
}}

if (!function_exists("nxs_doPublishToMC")) { function nxs_doPublishToMC($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassMC(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); }}  
?>