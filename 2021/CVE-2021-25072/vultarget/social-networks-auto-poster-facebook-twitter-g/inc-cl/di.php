<?php    
//## NextScripts Diigo Connection Class
$nxs_snapAvNts[] = array('code'=>'DI', 'lcode'=>'di', 'name'=>'Diigo', 'type'=>'Link Sharing/Boormarks', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Auto-submit bookmark to your account');

if (!class_exists("nxs_snapClassDI")) { class nxs_snapClassDI extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'DI', 'lcode'=>'di', 'name'=>'Diigo', 'defNName'=>'uName', 'tstReq' => false, 'instrURL'=>'http://www.nextscripts.com/setup-installation-diigo-social-networks-auto-poster-wordpress/');    
  
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  $ntOptsOut['inclTags'] = $ntOpts['diInclTags'];
        $ntOptsOut['msgTFormat'] = $ntOpts['diMsgTFormat']; $ntOptsOut['msgFormat'] = $ntOpts['diMsgFormat'];  $ntOptsOut['uName'] = $ntOpts['diUName'];  $ntOptsOut['uPass'] = $ntOpts['diPass']; $ntOptsOut['apiKey'] = $ntOpts['diAPIKey']; 
        $ntOptsOut['isUpdd'] = '1'; $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  } 
  
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'uPass'=>'',  'apiKey'=>'', 'inclTags'=>'1', 'msgTFormat'=>'%TITLE%', 'msgFormat'=>"%EXCERPT%"); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['uPass']); }
  function accTab($ii, $options, $isNew=false){ $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; ?><div id="altFormat" style="">
      <div style="width:100%;"><strong id="altFormatText">Diigo API Key:</strong> <span style="font-size: 11px; margin: 0px;">Get it from <a target="_blank" href="http://www.diigo.com/api_keys/">http://www.diigo.com/api_keys</a>.</span></div>
      <input name="<?php echo $nt; ?>[<?php echo $ii; ?>][apiKey]" style="width: 60%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['apiKey'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" />  <br/> 
    </div><?php $this->elemUserPass($ii, $options['uName'], $options['uPass']); $this->elemTitleFormat($ii,'Message Title Format','msgTFormat',$options['msgTFormat']);
    $this->elemMsgFormat($ii,'Message Text Format','msgFormat',$options['msgFormat']);?><br/ >
    <div style="margin-bottom: 20px;margin-top: 5px;"><input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][inclTags]"  <?php if ((int)$options['inclTags'] == 1) echo "checked"; ?> /> 
       <strong><?php _e('Post with tags', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong> <?php _e('Tags from the blogpost will be included', 'social-networks-auto-poster-facebook-twitter-g'); ?>                                
    </div> <?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['uPass']) && !empty($pval['uPass'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]); 
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
        <?php  $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat);  $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat); 
          /* ## Select Image & URL ## */ nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);        
     }
  }  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);  //   prr($optMt);    
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ 
      
  } 
  
}}

if (!function_exists("nxs_doPublishToDI")) { function nxs_doPublishToDI($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); 
  $cl = new nxs_snapClassDI(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}}  
?>