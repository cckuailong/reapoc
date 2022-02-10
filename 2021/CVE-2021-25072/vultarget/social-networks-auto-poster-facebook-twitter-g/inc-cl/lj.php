<?php    
//## NextScripts LJ Connection Class
$nxs_snapAvNts[] = array('code'=>'LJ', 'lcode'=>'lj', 'name'=>'LiveJournal', 'type'=>'Blogs/Publishing Platforms', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Auto-submit your blogpost to LiveJournal blog or community. LiveJournal engine based website DreamWidth.org is also supported');

if (!class_exists("nxs_snapClassLJ")) { class nxs_snapClassLJ extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'LJ', 'lcode'=>'lj', 'name'=>'LiveJournal', 'defNName'=>'uName', 'tstReq' => false, 'instrURL'=>'http://www.nextscripts.com/setup-installation-livejournal-social-networks-auto-poster-for-wordpress/');    
  
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  
        $ntOptsOut['msgTFormat'] = $ntOpts['ljMsgTFormat']; $ntOptsOut['msgFormat'] = $ntOpts['ljMsgFormat'];  $ntOptsOut['uName'] = $ntOpts['ljUName'];  $ntOptsOut['uPass'] = $ntOpts['ljPass']; 
        $ntOptsOut['commID'] = $ntOpts['commID'];  $ntOptsOut['ljSrv'] = $ntOpts['ljSrv'];  $ntOptsOut['inclTags'] = $ntOpts['inclTags'];  
        $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  } 
  
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'uPass'=>'', 'ljSrv'=>'livejournal.com',  'commID'=>'', 'inclTags'=>'1', 'msgTFormat'=>'%TITLE%', 'msgFormat'=>"%FULLTEXT%"); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['uPass']); }
  function accTab($ii, $options, $isNew=false){ $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $this->elemUserPass($ii, $options['uName'], $options['uPass']); ?>
    
     <br/ ><div style="width:100%;"><b><?php _e('Website', 'nxs_snap'); ?></b><br/><?php _e('Please select your website. SNAP could post to LJ Engine Based sites like DreamWidth.org', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
    <div id="nxsLJInfoDiv<?php echo $ii; ?>">
     <div style="width:100%;">
      <select id="lj1delayHrs" name="lj[<?php echo $ii; ?>][ljSrv]"><option  <?php if ( empty($options['ljSrv']) || $options['ljSrv']=='LJ') {?> selected="selected" <?php } ?> value="LJ">LiveJournal.com</option>
         <option <?php if ( isset($options['ljSrv']) && $options['ljSrv']=='DW') {?> selected="selected" <?php } ?> value="DW">DreamWidth.org</option>
      </select> 
     </div>         
    </div><br/>
    <div style="width:100%;"><br/><b><?php _e('Blog/Community URL or ID', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</b><br/><?php _e('Please specify the Blog or Community URL or ID. Use this only if you are posting NOT to your own journal.', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/><input name="lj[<?php echo $ii; ?>][commID]" id="commID" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['commID'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" />                
    </div> 
    <br/><br/>    
    <?php $this->elemTitleFormat($ii,'Post Title Format','msgTFormat',$options['msgTFormat']); $this->elemMsgFormat($ii,'Post Text Format','msgFormat',$options['msgFormat']);?>    
    <div style="margin-bottom: 20px;margin-top: 5px;"><input value="1"  id="ljInclTags<?php echo $ii; ?>" type="checkbox" name="lj[<?php echo $ii; ?>][inclTags]"  <?php if ((int)$options['inclTags'] == 1) echo "checked"; ?> /> 
        <b><?php _e('Post with tags.', 'social-networks-auto-poster-facebook-twitter-g') ?></b> <?php _e('Tags from the blogpost will be auto posted to LiveJournal', 'social-networks-auto-poster-facebook-twitter-g') ?>                                            
    </div><br/>  
    
    <br/ ><?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['uPass']) && !empty($pval['uPass'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]); 
        
        if (isset($pval['commID'])) $options[$ii]['commID'] = trim($pval['commID']); 
        if (isset($pval['ljSrv'])) $options[$ii]['ljSrv'] = trim($pval['ljSrv']); 
        
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

if (!function_exists("nxs_doPublishToLJ")) { function nxs_doPublishToLJ($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); 
  $cl = new nxs_snapClassLJ(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}}  
?>