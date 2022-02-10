<?php   
//## NextScripts Facebook Connection Class
$nxs_snapAvNts[] = array('code'=>'VB', 'lcode'=>'vb', 'name'=>'vBulletin', 'type'=>'Forums', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Auto-submit your blogpost to vBulletin forums. Could create new threads or new posts');

if (!class_exists("nxs_snapClassVB")) { class nxs_snapClassVB extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'VB', 'lcode'=>'vb', 'name'=>'vBulletin', 'defNName'=>'uName', 'tstReq' => false, 'instrURL'=>'http://www.nextscripts.com/setup-installation-vbulletin-social-networks-auto-poster-wordpress/');    
  var $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'uPass'=>'', 'vbURL'=>'', 'inclTags'=>'1', 'msgTFormat'=>'%TITLE%', 'msgFormat'=>"%FULLTEXT%");
  
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  
        $ntOptsOut['msgTFormat'] = !empty($ntOpts['vbMsgTFormat'])?$ntOpts['vbMsgTFormat']:''; $ntOptsOut['msgFormat'] = !empty($ntOpts['vbMsgFormat'])?$ntOpts['vbMsgFormat']:'';  
        $ntOptsOut['uName'] = !empty($ntOpts['vbUName'])?$ntOpts['vbUName']:''; $ntOptsOut['uPass'] = !empty($ntOpts['vbPass'])?$ntOpts['vbPass']:''; 
        $ntOptsOut['inclTags'] = !empty($ntOpts['inclTags'])?$ntOpts['inclTags']:'';  
        $ntOptsOut = nxs_arrMergeCheck($ntOptsOut, $this->defO); $ntOptsOut['isUpdd'] = '1'; $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  } 
  
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){  $this->showGNewNTSettings($ii, $this->defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['uPass']); }
  function accTab($ii, $options, $isNew=false){ $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $this->elemUserPass($ii, $options['uName'], $options['uPass']); ?>
    
     
    <div id="altFormat" style="">
  <div style="width:100%;"><strong id="altFormatText">vBulletin URL:</strong> <span style="font-size: 11px; margin: 0px;">Could be Forum URL or Thread URL. Either new thread of new post will be created.</span></div>
                <input name="vb[<?php echo $ii; ?>][vbURL]" id="apVBURL" style="width: 60%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['vbURL'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" />  <br/> 
            
            </div>   
            
    <br/><br/>    
    <?php $this->elemTitleFormat($ii,'Post Title Format','msgTFormat',$options['msgTFormat']); $this->elemMsgFormat($ii,'Post Text Format','msgFormat',$options['msgFormat']);?>    
    <div style="margin-bottom: 20px;margin-top: 5px;"><input value="1"  id="vbInclTags<?php echo $ii; ?>" type="checkbox" name="vb[<?php echo $ii; ?>][inclTags]"  <?php if ((int)$options['inclTags'] == 1) echo "checked"; ?> /> 
        <b><?php _e('Post with tags.', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <?php _e('Tags from the blogpost will be auto posted to LiveJournal', 'social-networks-auto-poster-facebook-twitter-g') ?>                                            
    </div><br/>  
    
    <br/ ><?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['uPass']) && !empty($pval['uPass'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);         
        if (isset($pval['vbURL'])) $options[$ii]['vbURL'] = trim($pval['vbURL']);                 
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
        <?php  $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat);  $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat); ?>
         
        <?php
        
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

if (!function_exists("nxs_doPublishToVB")) { function nxs_doPublishToVB($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); 
  $cl = new nxs_snapClassVB(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}}  
?>