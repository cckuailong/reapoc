<?php    
//## NextScripts Reddit Connection Class
$nxs_snapAvNts[] = array('code'=>'RD', 'lcode'=>'rd', 'name'=>'Reddit', 'type'=>'Link Sharing/Boormarks', 'ptype'=>'P', 'status'=>'A', 'desc'=>'Autopost to your subreddits');

if (!class_exists("nxs_snapClassRD")) { class nxs_snapClassRD extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'RD', 'lcode'=>'rd', 'name'=>'Reddit', 'defNName'=>'uName', 'tstReq' => false, 'instrURL'=>'http://www.nextscripts.com/setup-installation-reddit-social-networks-auto-poster-wordpress/');    
  
  var $noFuncMsg = 'Reddit doesn\'t have a built-in API for automated posts yet. <br/>You need to get a special <a target="_blank" href="http://www.nextscripts.com/snap-api/">library module</a> to be able to publish your content to Reddit.';  
  function checkIfFunc() { return class_exists('nxsAPI_RD'); }
  
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  
        $ntOptsOut['msgTFormat'] = $ntOpts['rdTitleFormat']; $ntOptsOut['msgFormat'] = $ntOpts['rdTextFormat'];  $ntOptsOut['uName'] = $ntOpts['rdUName'];  $ntOptsOut['uPass'] = $ntOpts['rdPass']; 
        $ntOptsOut['postType'] = $ntOpts['postType'];  $ntOptsOut['rdSubReddit'] = $ntOpts['rdSubReddit'];  
        $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  } 
  
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'uPass'=>'', 'postType'=>'', 'rdSubReddit'=>'', 'msgTFormat'=>'%TITLE%', 'msgFormat'=>"%EXCERPT%"); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['uPass']); }
  function accTab($ii, $options, $isNew=false){ $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $this->elemUserPass($ii, $options['uName'], $options['uPass']);  $p = $options['uPass'];
      if (!empty($p)) { $p = (substr($p, 0, 5)=='n5g9a'||substr($p, 0, 5)=='g9c1a'||substr($p, 0, 5)=='b4d7s')?nsx_doDecode(substr($p, 5)):$p; $options['uPass'] = 'g9c1a'.nsx_doEncode($p); $tPST = (!empty($_POST))?$_POST:''; 
      $_POST['rdSR'] = $options['rdSubReddit']; $_POST['u'] = $options['uName']; $_POST['p'] = $p; $_POST['ii'] = $ii; 
      $opNm = 'nxs_snap_rd_'.sha1('nxs_snap_rd'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); if (empty($opVal)) { $ntw[$nt][$ii]=$options; $opVal = $this->getListOfSubReddits($ntw); }
      if (!empty($opVal) & !is_array($opVal)) $options['uMsg'] = $opVal; else { if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } $_POST = $tPST;
    } ?>
    
     <br/ ><div style="width:100%;"><b><?php _e('Subreddit ID', 'nxs_snap'); ?></b>&nbsp;(<?php _e('Please select Subreddit', 'nxs_snap'); ?>)</div>
    <div id="nxsRDInfoDiv<?php echo $ii; ?>">
         <div style="width:100%;">
          <div>                   
          <select id="rdSubReddit<?php echo $ii; ?>" onchange="nxs_rdSRChange('<?php echo $ii;?>',jQuery(this));" name="rd[<?php echo $ii;?>][rdSubReddit]">
            <?php $pgi = !empty($options['rdSubRedditsList'])?$options['rdSubRedditsList']:''; 
              echo (!empty($pgi) && stripos($pgi,$options['rdSubReddit'])===false)?'<option selected="selected" value="'.$options['rdSubReddit'].'">'.$options['rdSubReddit'].'</option>':''; 
              if (!empty($options['rdSubReddit'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['rdSubReddit'].'"','selected="selected" value="'.$options['rdSubReddit'].'"',$pgi); } 
                else echo '<option value="">None(Click refresh icon to retrieve your subreddits)</option>';
              echo $pgi;
            ?>
          </select><div id="nxsRDInfoDivBlock<?php echo $ii; ?>" style="display: inline-block;"> <input type="text" style="display: none;" id="rdSRIDCst<?php echo $ii; ?>" value="<?php echo $options['rdSubReddit']; ?>" class="nxs_rdSRIDcst" data-tid="rdSubReddit<?php echo $ii; ?>" />         
          <div style="display: inline-block;"><a onclick="nxs_rdGetSRs(<?php echo $ii;?>, 1); jQuery(this).blur(); return false;" href="#"><img id="<?php echo $nt.$ii;?>rfrshImg" style="vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/refresh16.png' /></a></div></div> <img id="<?php echo $nt.$ii;?>ldImg" style="display: none;vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          </div>          
          </div>     
          <div id="nxsRDMsgDiv<?php echo $ii; ?>"><?php if (!empty($options['uMsg'])) echo $options['uMsg']; ?><?php if ($isNew) { ?><?php _e('Please enter your login/password to see the list of your subreddits', 'nxs_snap'); ?><?php } ?></div>                                                 
    </div><i style="color: #580000;">Please do not try to post to subredits that you do not own. Reddit is very serious about it's policy that prohibits sharing your own links. You will loose posting privileges and you account will be <b>banned</b> if you post to public subreddits. </i>
            <br/>  <br/>  
            
    <div style="width:100%;"><strong id="altFormatText">Post Type:</strong></div>                      
      <div style="margin-left: 10px;">
        <input type="radio" name="rd[<?php echo $ii; ?>][postType]" value="A" <?php if ( empty($options['postType']) || $options['postType'] == 'A') echo 'checked="checked"'; ?> /> <?php _e('Link Post', 'social-networks-auto-poster-facebook-twitter-g'); ?>
        <br/>
        <input type="radio" name="rd[<?php echo $ii; ?>][postType]" value="T" <?php if (!empty($options['postType']) && $options['postType'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('set the text format below', 'social-networks-auto-poster-facebook-twitter-g'); ?></i>
     </div>
  
  <script type="text/javascript">      
      jQuery('#apRDUName<?php echo $ii; ?>').change(function() { var u = jQuery(this).val();  var p = jQuery('#apRDPass<?php echo $ii; ?>').val(); if( u!='' && p!='' ) { nxs_rdGetSRs(<?php echo $ii; ?>,0); }  });
      jQuery('#apRDPass<?php echo $ii; ?>').change(function() { var u = jQuery('#apRDUName<?php echo $ii; ?>').val();  var p = jQuery(this).val(); if( u!='' && p!='' ) { nxs_rdGetSRs(<?php echo $ii; ?>,0); }  });
  </script>
  
    <?php $this->elemTitleFormat($ii,'Post Title Format','msgTFormat',$options['msgTFormat']);
    $this->elemMsgFormat($ii,'Post Text Format','msgFormat',$options['msgFormat']);?><br/ ><?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['uPass']) && !empty($pval['uPass'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]); 
        
        if (isset($pval['rdSubReddit'])) $options[$ii]['rdSubReddit'] = trim($pval['rdSubReddit']); 
        
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
         <tr style="<?php echo !empty($ntOpt['do'])?'display:table-row;':'display:none;'; ?>" class="nxstbldo nxstbldo<?php echo strtoupper($nt).$ii; ?>"><th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 0px; padding-right:10px;"> <?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g') ?> </th><td>             
        <input type="radio" name="rd[<?php echo $ii; ?>][postType]" value="A" <?php if ( empty($postType) || $postType == 'A') echo 'checked="checked"'; ?> /><?php _e('Link Post', 'social-networks-auto-poster-facebook-twitter-g') ?>
        <br/>
      <input type="radio" name="rd[<?php echo $ii; ?>][postType]" value="T" <?php if ($postType == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g') ?><br/>               
     </td></tr>
        <?php
        
          /* ## Select Image & URL ## */ nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);        
     }
  }  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);  //   prr($optMt);
    //if (!empty($pMeta['dlBoard'])) $optMt['dlBoard'] = $pMeta['dlBoard']; if (!empty($pMeta['dlBoard'])) $optMt['dlBoard'] = $pMeta['dlBoard']; else $optMt['dlBoard'] = 0;            
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ 
      
  } 
   //## RD Specific
  function getListOfSubReddits($networks){ $opVal = array(); $pass = 'g9c1a'.nsx_doEncode($_POST['p']); $opNm = 'nxs_snap_rd_'.sha1('nxs_snap_rd'.$_POST['u'].$pass); $opVal = nxs_getOption($opNm); $ii = $_POST['ii']; $nt = new nxsAPI_RD(); $nt->debug = false; // prr($opVal);
     $currPstAs = !empty($_POST['rdSR'])?$_POST['rdSR']:(!empty($networks['rd'][$ii])?$networks['rd'][$ii]['rdSubReddit']:'');
     if (empty($_POST['force']) && !empty($opVal['ck']) && !empty($opVal['rdSubRedditsList']) ) $pgs = $opVal['rdSubRedditsList']; else { if (!empty($opVal['ck'])) $nt->ck = $opVal['ck']; $loginError=$nt->connect($_POST['u'],$_POST['p']);// var_dump($loginError);
       if (!$loginError){ $opVal['ck'] = $nt->ck; $nt->getSubReddits($currPstAs); $pgs = $nt->srList; }
         else { $outMsg = '<b style="color:red;">'.__('Login Problem').'&nbsp;-&nbsp;'.$loginError.'</b>'; if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
     } $pgCust = (!empty($pgs) && !empty($currPstAs) && stripos($pgs,$currPstAs)===false)?'<option selected="selected" value="'.$currPstAs.'">'.$currPstAs.'</option>':'';     
     if (!empty($_POST['isOut'])) echo $pgCust.$pgs; // .'<option style="color:#BD5200" value="a">'.__('...enter the SubReddit ID').'</option>';
     $opVal['rdSubRedditsList'] = $pgs; nxs_saveOption($opNm, $opVal); return $opVal;
  } 
  
}}

if (!function_exists("nxs_doPublishToRD")) { function nxs_doPublishToRD($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); 
  $cl = new nxs_snapClassRD(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}}  
?>