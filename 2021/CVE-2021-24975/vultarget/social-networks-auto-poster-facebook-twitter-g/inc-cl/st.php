<?php    
//## NextScripts sett.com Connection Class
$nxs_snapAvNts[] = array('code'=>'ST', 'lcode'=>'st', 'name'=>'SETT', 'type'=>'Blogs/Publishing Platforms', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Auto-post to your Sett.com blog');

if (!class_exists("nxs_snapClassST")) { class nxs_snapClassST extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'ST', 'lcode'=>'st', 'name'=>'SETT', 'defNName'=>'uName', 'tstReq' => false, 'instrURL'=>'http://www.nextscripts.com/instructions/sett-social-networks-auto-poster-setup-installation/');    
  
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];          
        $ntOptsOut['msgTFormat'] = $ntOpts['msgTFrmt']; $ntOptsOut['msgFormat'] = $ntOpts['msgFrmt'];  $ntOptsOut['uName'] = $ntOpts['uName'];  $ntOptsOut['uPass'] = $ntOpts['uPass']; $ntOptsOut['mgzURL'] = $ntOpts['mgzURL'];
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  } 
   
  
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'uPass'=>'', 'mgzURL'=>'',  'msgTFormat'=>'%TITLE%', 'msgFormat'=>"%FULLTEXT% \r\n\r\n<a href=".'"%URL%"'.">Source</a>"); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['uPass']); }
  function accTab($ii, $options, $isNew=false){ $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $this->elemUserPass($ii, $options['uName'], $options['uPass']); ?>
    <div style="width:100%;"><strong><?php echo $ntInfo['name']; ?> Blog URL:</strong> </div>http://sett.com/<input name="<?php echo $nt; ?>[<?php echo $ii; ?>][mgzURL]"  style="width: 20%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['mgzURL'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/>
    <br/><?php $this->elemMsgFormat($ii,'Comment Text Format','msgFormat',$options['msgFormat']);
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['uPass']) && !empty($pval['uPass'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]); 
        if (isset($pval['mgzURL']))   $options[$ii]['mgzURL'] = trim($pval['mgzURL']); 
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
        <?php  $this->elemEdMsgFormat($ii, __('Comment Text Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat); ?>        
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

if (!function_exists("nxs_doPublishToST")) { function nxs_doPublishToST($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); 
  $cl = new nxs_snapClassST(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}}  
?>