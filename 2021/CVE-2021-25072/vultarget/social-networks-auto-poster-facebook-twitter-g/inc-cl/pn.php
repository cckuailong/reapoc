<?php    
//## NextScripts Facebook Connection Class
$nxs_snapAvNts[] = array('code'=>'PN', 'lcode'=>'pn', 'name'=>'Pinterest', 'type'=>'Social Networks', 'ptype'=>'P', 'status'=>'A', 'desc'=>'Post your blogpost\'s image to your Pinterest board.');

if (!class_exists("nxs_snapClassPN")) { class nxs_snapClassPN extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'PN', 'lcode'=>'pn', 'name'=>'Pinterest', 'defNName'=>'uName', 'tstReq' => false, 'instrURL'=>'http://www.nextscripts.com/setup-installation-pinterest-social-networks-auto-poster-wordpress');    
  
  var $noFuncMsg = 'Pinterest doesn\'t have a built-in API for automated posts yet. <br/>You need to get a special <a target="_blank" href="http://www.nextscripts.com/snap-api/">library module</a> to be able to publish your content to Pinterest.';  
  function checkIfFunc() { return class_exists('nxsAPI_PN'); }
  
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  
        $ntOptsOut['msgFormat'] = $ntOpts['pnMsgFormat'];  $ntOptsOut['uName'] = $ntOpts['pnUName'];  $ntOptsOut['uPass'] = $ntOpts['pnPass']; 
        $ntOptsOut['cImgURL'] = $ntOpts['cImgURL'];  $ntOptsOut['pnBoard'] = $ntOpts['pnBoard']; $ntOptsOut['isAttachVid'] = $ntOpts['isAttachVid'];  $ntOptsOut['defImg'] = $ntOpts['pnDefImg'];   
        $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  } 
  
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'uPass'=>'',  'cImgURL'=>'', 'isAttachVid'=>'', 'isAttachVid'=>'', 'defImg'=>'', 'msgFormat'=>"%EXCERPT%"); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['uPass']); }
  
  function getMergeOptInfo($options, $ii) { $p = $options['uPass']; 
    if (!empty($p)) { $p = (substr($p, 0, 5)=='n5g9a'||substr($p, 0, 5)=='g9c1a'||substr($p, 0, 5)=='b4d7s')?nsx_doDecode(substr($p, 5)):$p; $options['uPass'] = 'g9c1a'.nsx_doEncode($p); $tPST = (!empty($_POST))?$_POST:''; 
      $_POST['pnBoard'] = $options['pnBoard']; $_POST['u'] = $options['uName']; $_POST['p'] = $p; $_POST['ii'] = $ii; 
      $opNm = 'nxs_snap_pn_'.sha1('nxs_snap_pn'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); if (empty($opVal)) { $ntw[$nt][$ii]=$options; $opVal = $this->getListOfPNBoards($ntw); }
      if (!empty($opVal) & !is_array($opVal)) $options['uMsg'] = $opVal; else { if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } $_POST = $tPST; 
    } return $options;
  }
  
  function accTab($ii, $options, $isNew=false){  $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $this->elemUserPass($ii, $options['uName'], $options['uPass']); $options = $this->getMergeOptInfo($options, $ii); ?>
    
     <br/ ><div style="width:100%;"><b><?php _e('Board', 'nxs_snap'); ?></b>&nbsp;(<?php _e('Please select the board to post to', 'nxs_snap'); ?>)</div>
    <div id="nxsPNInfoDiv<?php echo $ii; ?>">
         <div style="width:100%;">
          <div>                   
          <select id="pnBoard<?php echo $ii; ?>" onchange="nxs_pnBoardChange('<?php echo $ii;?>',jQuery(this));" name="pn[<?php echo $ii;?>][pnBoard]">
            <?php $pgi = !empty($options['pnBoardsList'])?$options['pnBoardsList']:''; 
              echo (!empty($pgi) && stripos($pgi,$options['pnBoard'])===false)?'<option selected="selected" value="'.$options['pnBoard'].'">'.$options['pnBoard'].'</option>':''; 
              if (!empty($options['pnBoard'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['pnBoard'].'"','selected="selected" value="'.$options['pnBoard'].'"',$pgi); } 
                else echo '<option value="">None(Click refresh icon to retrieve your boards)</option>';
              echo $pgi;
            ?>
          </select><div id="nxsPNInfoDivBlock<?php echo $ii; ?>" style="display: inline-block;"> <input type="text" style="display: none;" id="pnBRDIDCst<?php echo $ii; ?>" value="<?php echo $options['pnBoard']; ?>" class="nxs_pnBRDIDcst" data-tid="pnBoard<?php echo $ii; ?>" />         
          <div style="display: inline-block;"><a onclick="nxs_pnGetBoards(<?php echo $ii;?>, 1); jQuery(this).blur(); return false;" href="#"><img id="<?php echo $nt.$ii;?>rfrshImg" style="vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/refresh16.png' /></a></div></div> <img id="<?php echo $nt.$ii;?>ldImg" style="display: none;vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          </div>          
          </div>     
          <div id="nxsPNMsgDiv<?php echo $ii; ?>"><?php if (!empty($options['uMsg'])) echo $options['uMsg']; ?><?php if ($isNew) { ?><?php _e('Please enter your login/password to see the list of your boards', 'nxs_snap'); ?><?php } ?></div>                                                 
    </div>
  <script type="text/javascript">      
      jQuery('#apPNUName<?php echo $ii; ?>').change(function() { var u = jQuery(this).val();  var p = jQuery('#apPNPass<?php echo $ii; ?>').val(); if( u!='' && p!='' ) { nxs_pnGetBoards(<?php echo $ii; ?>,0); }  }); 
      jQuery('#apPNPass<?php echo $ii; ?>').change(function() { var u = jQuery('#apPNUName<?php echo $ii; ?>').val();  var p = jQuery(this).val(); if( u!='' && p!='' ) { nxs_pnGetBoards(<?php echo $ii; ?>,0); }  });
  </script>
  
   <br/><strong><?php _e('Clickthrough URL', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> 
<div style="margin-bottom: 20px;margin-top: 5px;">
<input type="radio" name="pn[<?php echo $ii; ?>][cImgURL]" value="R" <?php if ( empty($options['cImgURL']) || $options['cImgURL'] == 'R') echo 'checked="checked"'; ?> /><?php _e('Regular Post URL', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;
<!-- <input type="radio" name="pn[<?php echo $ii; ?>][cImgURL]" value="S" <?php if (!empty($options['cImgURL']) && $options['cImgURL'] == 'S') echo 'checked="checked"'; ?> /> Shortened Post URL&nbsp;&nbsp; -->
<input type="radio" name="pn[<?php echo $ii; ?>][cImgURL]" value="N" <?php if (!empty($options['cImgURL']) && $options['cImgURL'] == 'N') echo 'checked="checked"'; ?> /><?php _e('No Clickthrough URL', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;</div>

            <div style="width:100%;"><strong><?php _e('URL of the Default Image to Pin', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> 
            <div style="font-size: 11px; margin: 0px;"><?php _e('If your post does not have any images this will be used instead.', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
            </div><input style="width:400px;" name="pn[<?php echo $ii; ?>][defImg]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['defImg'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /> 
            <br/><br/>   
             
             <div style="margin-bottom: 5px; margin-left: 0px; "><input value="1"  id="isAttachVid" type="checkbox" name="pn[<?php echo $ii; ?>][isAttachVid]"  <?php if (isset($options['isAttachVid']) && (int)$options['isAttachVid'] == 1) echo "checked"; ?> />    <strong><?php _e('If post has a video use it instead of image.', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong><br/><i><?php _e('Video will be pinned instead of featured image. Only Youtube is supported at this time.', 'social-networks-auto-poster-facebook-twitter-g'); ?></i>
    <br/><br/></div>  
    <?php $this->elemMsgFormat($ii,'Post Text Format','msgFormat',$options['msgFormat']);?><br/ ><?php
  }
  function advTab($ii, $options){ $this->showProxies($this->ntInfo['lcode'], $ii, $options); }
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['uPass']) && !empty($pval['uPass'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]); 
        
        if (isset($pval['cImgURL'])) $options[$ii]['cImgURL'] = trim($pval['cImgURL']); 
        if (isset($pval['pnBoard'])) $options[$ii]['pnBoard'] = trim($pval['pnBoard']); 
        if (isset($pval['isAttachVid'])) $options[$ii]['isAttachVid'] = trim($pval['isAttachVid']); else  $options[$ii]['isAttachVid'] = '0';
        if (isset($pval['defImg'])) $options[$ii]['defImg'] = trim($pval['defImg']); 
        
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
        <tr style="<?php echo !empty($ntOpt['do'])?'display:table-row;':'display:none;'; ?>" class="nxstbldo nxstbldo<?php echo strtoupper($nt).$ii; ?>"><th scope="row" style="text-align:right; width:150px; padding-top: 5px; padding-right:10px;">Select Board</th>
                <td><select name="pn[<?php echo $ii; ?>][pnBoard]">
            <?php  $ntOpt = $this->getMergeOptInfo($ntOpt, $ii); if (!empty($ntOpt['pnBoardsList'])){ 
              if ($ntOpt['pnBoard']!='') $gPNBoards = str_replace($ntOpt['pnBoard'].'"', $ntOpt['pnBoard'].'" selected="selected"', str_ireplace('selected="selected" ','', $ntOpt['pnBoardsList']));  echo $gPNBoards;} else { ?>
              <option value=""><?php _e('None(Please go to the settings and retrieve the list of boards)', 'social-networks-auto-poster-facebook-twitter-g'); ?></option>
            <?php } ?>
            </select></td>
        </tr> 
        <?php  $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);         
          /* ## Select Image & URL ## */ nxs_showImgToUseDlg($nt, $ii, $imgToUse); nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);        
     }
  }  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);  //   prr($optMt);
    if (isset($pMeta['pnBoard']) && $pMeta['pnBoard']!='' && $pMeta['pnBoard']!='0') $optMt['pnBoard'] = $pMeta['pnBoard'];     
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ 
      
  } 
   //## PN Specific
  function getListOfPNBoards($networks){ $opVal = array(); $pass = 'g9c1a'.nsx_doEncode($_POST['p']); $opNm = 'nxs_snap_pn_'.sha1('nxs_snap_pn'.$_POST['u'].$pass); $opVal = nxs_getOption($opNm); $ii = $_POST['ii']; 
     if (!class_exists("nxsAPI_PN")) { $outMsg = '<b style="color:red;">'.__('API Not Found').'&nbsp;-&nbsp;'.$loginError.'</b>'; if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }  $nt = new nxsAPI_PN(); $nt->debug = false; // prr($opVal);
     $currPstAs = !empty($_POST['pnBoard'])?$_POST['pnBoard']:(!empty($networks['pn'][$ii])?$networks['pn'][$ii]['pnBoard']:'');
     if (empty($_POST['force']) && !empty($opVal['ck']) && !empty($opVal['pnBoardsList']) ) $pgs = $opVal['pnBoardsList']; else { if (!empty($opVal['ck'])) $nt->ck = $opVal['ck']; $loginError=$nt->connect($_POST['u'],$_POST['p']);// var_dump($loginError);
       if (!$loginError){ $opVal['ck'] = $nt->ck; $pgs = $nt->getBoards($currPstAs); }
         else { $outMsg = '<b style="color:red;">'.__('Login Problem').'&nbsp;-&nbsp;'.$loginError.'</b>'; if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
     } $pgCust = (!empty($pgs) && !empty($currPstAs) && stripos($pgs,$currPstAs)===false)?'<option selected="selected" value="'.$currPstAs.'">'.$currPstAs.'</option>':'';     
     if (!empty($_POST['isOut'])) echo (stripos($pgs, 'No Boards Found')===false?$pgCust:'').$pgs; // .'<option style="color:#BD5200" value="a">'.__('...enter the SubReddit ID').'</option>';
     $opVal['pnBoardsList'] = $pgs; nxs_saveOption($opNm, $opVal); return $opVal;
  } 
  
}}

if (!function_exists("nxs_doPublishToPN")) { function nxs_doPublishToPN($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); 
  $cl = new nxs_snapClassPN(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}}  
?>