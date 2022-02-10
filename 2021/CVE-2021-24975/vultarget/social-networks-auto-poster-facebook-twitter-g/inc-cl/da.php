<?php    
//## NextScripts deviantART Connection Class
$nxs_snapAvNts[] = array('code'=>'DA', 'lcode'=>'da', 'name'=>'deviantART (!)', 'type'=>'Image Sharing', 'ptype'=>'F', 'status'=>'B', 'desc'=>'Autopost to your blog. HTML is supported');

if (!class_exists("nxs_snapClassDA")) { class nxs_snapClassDA extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'DA', 'lcode'=>'da', 'name'=>'deviantART (!)', 'defNName'=>'uName', 'tstReq' => false, 'instrURL'=>'http://www.nextscripts.com/setup-installation-deviantart-social-networks-auto-poster-wordpress/');    
  
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  
        $ntOptsOut['msgTFormat'] = $ntOpts['daTitleFormat']; $ntOptsOut['msgFormat'] = $ntOpts['daTextFormat'];  $ntOptsOut['uName'] = $ntOpts['daUName'];  $ntOptsOut['uPass'] = $ntOpts['daPass'];         
        $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  } 
  
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'uPass'=>'', 'msgTFormat'=>'%TITLE%', 'msgFormat'=>"%FULLTEXT%"); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['uPass']); }
  function accTab($ii, $options, $isNew=false){ $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; ?>
    <div style="color:red;padding:5px;margin:5px; border: 1px solid darkred;">DeviantArt API is being extremely unstable and buggy on their side for the last several months. Until further notice DeviantArt connector is provided "as is". Please use it on your own risk. This may cause numerous of different issues including disapering posts, broken accounts, messed up posts, etc...<br/></div>
    <?php $this->elemUserPass($ii, $options['uName'], $options['uPass']); 
    $this->elemTitleFormat($ii,'Post Title Format','msgTFormat',$options['msgTFormat']); $this->elemMsgFormat($ii,'Post Text Format','msgFormat',$options['msgFormat']);
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

if (!function_exists("nxs_doPublishToDA")) { function nxs_doPublishToDA($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); 
  $cl = new nxs_snapClassDA(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}}  
?>