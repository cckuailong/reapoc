<?php    
//## NextScripts Facebook Connection Class
$nxs_snapAvNts[] = array('code'=>'YT', 'lcode'=>'yt', 'name'=>'YouTube', 'type'=>'Other', 'ptype'=>'P', 'status'=>'A', 'desc'=>'Post messages to your YouTube channel feed. Ability to attach existing YouTube videos to posts');

if (!class_exists("nxs_snapClassYT")) { class nxs_snapClassYT extends nxs_snapClassNT {   
  var $ntInfo = array('code'=>'YT', 'lcode'=>'yt', 'name'=>'YouTube', 'defNName'=>'uName', 'tstReq' => false, 'imgAct'=>'E', 'instrURL'=>'http://www.nextscripts.com/instructions/youtube-social-networks-auto-poster-wordpress-setup-installation/');
  var $noFuncMsg = 'YouTube doesn\'t have a built-in API for automated posts yet. <br/>You need to get a special <a target="_blank" href="http://www.nextscripts.com/google-plus-automated-posting//">library module</a> to be able to publish your content to YouTube.';
  
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  
        $ntOptsOut['msgFormat'] = $ntOpts['ytMsgFormat']; $ntOptsOut['uName'] = $ntOpts['ytUName'];  $ntOptsOut['uPass'] = $ntOpts['ytPass']; 
        $ntOptsOut['ytPageID'] = $ntOpts['ytPageID']; $ntOptsOut['ytGPPageID'] = $ntOpts['ytGPPageID']; $ntOptsOut['isUpdd'] = '1'; $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  }   
  function checkIfFunc() { return class_exists('nxsAPI_GP'); }
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'uPass'=>'', 'ytPageID'=>'', 'ytGPPageID'=>'', 'msgFormat'=>"New post: %TITLE% - %URL%"); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['uPass']); }
  function accTab($ii, $options, $isNew=false){ $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $this->elemUserPass($ii, $options['uName'], $options['uPass']); ?> <br/ >    
    <div style="width:100%;"><strong><?php _e('YouTube Channel Feed Page URL', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong><i><?php _e('YouTube Channel Page URL', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][ytPageID]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['ytPageID'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>    
    <div style="width:100%;"><strong><?php _e('Google+ Page ID', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong><i><?php _e('Fill this only if you are posting to youTube as your Google+ page. Please leave this empty otherwise.', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][ytGPPageID]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['ytGPPageID'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>    
    
    
    <?php $this->elemMsgFormat($ii,'Message Format','msgFormat',$options['msgFormat']); 
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['uPass']) && !empty($pval['uPass'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);    
      if (isset($pval['ytPageID']))  $options[$ii]['ytPageID'] = trim($pval['ytPageID']);                
      if (isset($pval['ytGPPageID']))  $options[$ii]['ytGPPageID'] = trim($pval['ytGPPageID']);                
      } elseif ( count($pval)==1 ) if (isset($pval['do'])) $options[$ii]['do'] = $pval['do']; else $options[$ii]['do'] = 0; 
    } return $options;
  }
    
    //######## STOPPED HERE
    
  //#### Show Post->Edit Meta Box  Settings
  function showEdPostNTSettings($ntOpts, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];
      foreach($ntOpts as $ii=>$ntOpt)  { $isFin = $this->checkIfSetupFinished($ntOpt); if (!$isFin) continue; 
        $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true)); if (is_array($pMeta) && !empty($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);         
        
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):''; 
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse']; $ntOpt['ii']=$ii;
         
        $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta); ?>
                
        <?php $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat); 
          /* ## Select Image & URL ## */ nxs_showImgToUseDlg($nt, $ii, $imgToUse); nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);        
     }
  }  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);     
    //if (!empty($pMeta['igBoard'])) $optMt['igBoard'] = $pMeta['igBoard'];       
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ 
      
  } 
  
}}


if (!function_exists("nxs_doPublishToYT")) { function nxs_doPublishToYT($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true));
  ini_set('memory_limit','256M'); $cl = new nxs_snapClassYT(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}}  
?>