<?php    
//## NextScripts Facebook Connection Class
$nxs_snapAvNts[] = array('code'=>'GP', 'lcode'=>'gp', 'name'=>'Google+', 'type'=>'Social Networks', 'ptype'=>'P', 'status'=>'A', 'desc'=>'Post text, image or share a link to your profile, collection, business page or community');


if (!function_exists("nxs_CptCheckGP")) { function nxs_CptCheckGP($o){ session_id("nxs-temp-gpcpt"); session_start(); $sess = unserialize($_SESSION['nxs-temp-gpcpt']);
 if (!empty($_POST['c'])) { $ck = $sess['c']; $flds = $sess['f']; 
    $flds['recaptcha_response_field'] = $_POST['c']; $liObj = new nxsAPI_GP();   $hdrsArr = $liObj->headers('https://www.youtube.com/', 'https://www.youtube.com', 'POST', false);
    $advSet = array('headers' => $hdrsArr, 'httpversion' => '1.1', 'timeout' => 45, 'redirection' => 0, 'cookies' => $ck, 'body' => $flds);  prr($advSet);
    $rep = nxs_remote_post('https://www.youtube.com/das_captcha', $advSet); if (is_nxs_error($rep)) {  $badOut = print_r($rep, true)." - ERROR"; return $badOut; }  $contents2 = $rep['body'];    prr($rep); 
    if (stripos($contents2, 'id="error-box"')!==false) { echo 'The verification code was invalid or has timed out. Please try again.'; die(); }    
    if (stripos($contents2, 'The verification code was invalid')!==false) { echo 'The verification code was invalid or has timed out. Please try again.'; die(); }    
    if ($rep['response']['code']=='303' && !empty($rep['headers']['location']) ) { echo "OK. You are In";      
      $hdrsArr = $liObj->headers('http://www.youtube.com', 'https://www.youtube.com');  $ck = $rep['cookies'];    
      $advSet = array('headers' => $hdrsArr, 'httpversion' => '1.1', 'timeout' => 45, 'redirection' => 0, 'cookies' => $ck); // prr($advSet);
      $rep = nxs_remote_get($rep['headers']['location'], $advSet); prr($ck); if (is_nxs_error($rep)) {  $badOut = print_r($rep, true)." - ERROR"; return $badOut; }  $ck = $rep['cookies'];   
    }
 } 
}}

if (!class_exists("nxs_snapClassGP")) { class nxs_snapClassGP extends nxs_snapClassNT {   
  var $ntInfo = array('code'=>'GP', 'lcode'=>'gp', 'name'=>'Google+', 'defNName'=>'uName', 'tstReq' => false, 'imgAct'=>'E', 'instrURL'=>'http://www.nextscripts.com/instructions/setup-installation-google-plus-social-networks-auto-poster/');
  var $noFuncMsg = 'Google+ doesn\'t have a built-in API for automated posts yet. <br/>You need to get a special <a target="_blank" href="http://www.nextscripts.com/google-plus-automated-posting//">library module</a> to be able to publish your content to Google+.';
  
  function toLatestVer($ntOpts){ //return $ntOpts; 
  if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = $ntOpts; switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  
        $ntOptsOut['msgFormat'] = $ntOpts['gpMsgFormat']; $ntOptsOut['uName'] = $ntOpts['gpUName'];  $ntOptsOut['uPass'] = $ntOpts['gpPass']; 
        $ntOptsOut['pageID'] = $ntOpts['gpPageID']; $ntOptsOut['postType'] = $ntOpts['postType'];
        $ntOptsOut['commID'] = $ntOpts['gpCommID']; $ntOptsOut['commCat'] = $ntOpts['gpCCat'];        
      case 350: 
        if (empty($ntOptsOut['postTo'])) $ntOptsOut['postTo'] = !empty($ntOptsOut['commID']) ? 'c'.$ntOptsOut['commID'] : 'p'; 
        if (empty($ntOptsOut['postAs'])) $ntOptsOut['postAs'] = !empty($ntOptsOut['pageID']) ? $ntOptsOut['pageID'] : 'p';
       // unset($ntOptsOut['pageID']); unset($ntOptsOut['commID']); unset($ntOptsOut['ck']);  unset($ntOptsOut['commCatsList']);    //          
        $ntOptsOut['isUpdd'] = '1'; $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  }   
  function checkIfFunc() { return class_exists('nxsAPI_GP'); }
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'uPass'=>'', 'postType'=>'A', 'postTo'=>'', 'postAs'=>'', 'postAsNm'=>'', 'commCat'=>'', 'msgFormat'=>"New post: %TITLE% - %URL%"); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['uPass']); }
  function accTab($ii, $options, $isNew=false){ // $options['v']='340'; $options = $this->toLatestVer($options);
    $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $p = $options['uPass']; $this->elemUserPass($ii, $options['uName'], $p); //prr($options['postTo'], 'POST TO');  prr($options['postAs'], 'POST AS'); // prr($options);
    if (!empty($p)) { $p = (substr($p, 0, 5)=='n5g9a'||substr($p, 0, 5)=='g9c1a'||substr($p, 0, 5)=='b4d7s')?nsx_doDecode(substr($p, 5)):$p; $options['uPass'] = 'g9c1a'.nsx_doEncode($p); $tPST = (!empty($_POST))?$_POST:''; 
      $_POST['pg'] = $options['postTo']; $_POST['pstAs'] = $options['postAs']; $_POST['u'] = $options['uName']; $_POST['p'] = $p; $_POST['ii'] = $ii; $ntw[$nt][$ii]=$options;
      $opNm = 'nxs_snap_gp_'.sha1('nxs_snap_gp'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); if (empty($opVal)) $opVal = $this->getListOfPages($ntw); 
      if (!empty($opVal) & !is_array($opVal)) $options['uMsg'] = $opVal; else { if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal);
        $opNm = 'nxs_snap_gp_wh'.sha1('nxs_snap_gpwh'.$options['uName'].$options['uPass'].$options['postAs']); $opVal = nxs_getOption($opNm); 
        if (empty($opVal)) $opVal = $this->getListWhereToPost($ntw); if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); // prr($opVal, 'opVal:');
        if (substr($options['postTo'],0,1)=='c') { $opNm = 'nxs_snap_gp_cl_'.sha1('nxs_snap_gpcl'.$options['uName'].$options['uPass'].$options['postTo']); $opVal = nxs_getOption($opNm); 
          if (empty($opVal)) $opVal = $this->getGPCommInfo($ntw); if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); // prr($opVal, 'opVal:');
        }
      } $_POST = $tPST;
    }  if (empty($options['postAsNm']) && !empty($options['postAs']) && $options['postAs']!='p') $options['postAsNm'] = 'Page. ID:'.$options['postAs'].')';
    ?>
    
    <br/ ><div style="width:100%;"><b><?php _e('Post as', 'nxs_snap'); ?></b>&nbsp;(<?php _e('Please select profile, business page or branded page', 'nxs_snap'); ?>)</div>
    <div id="nxsGPInfoDiv<?php echo $ii; ?>">
         <div style="width:100%;">
          <div>                   
          <select id="gpPostAs<?php echo $ii; ?>" onchange="nxs_gpPostAsChange('<?php echo $ii;?>',jQuery(this));" name="gp[<?php echo $ii;?>][postAs]">
            <?php $pgi = !empty($options['pgsList'])?$options['pgsList']:''; 
              if (!empty($options['postAs'])) { echo (!empty($pgi) && !empty($options['postAs']) && stripos($pgi,$options['postAs'])===false)?'<option selected="selected" value="'.$options['postAs'].'">'.$options['postAs'].'</option>':''; }
            ?><option <?php if (empty($options['postAs'])) echo 'selected="selected"' ?> value="p"><?php _e('Profile'); ?></option><?php 
              if (!empty($options['postAs'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['postAs'].'"','selected="selected" value="'.$options['postAs'].'"',$pgi); } echo $pgi;
            ?><option value="a"><?php _e('.... Enter the Page ID'); ?></option>
          </select><input type="hidden" id="gpPostAsNm<?php echo $ii; ?>" name="gp[<?php echo $ii;?>][postAsNm]" value="<?php echo $options['postAsNm']; ?>"><div id="nxsGPInfoDivBlock<?php echo $ii; ?>" style="display: inline-block;"> <input type="text" style="display: none;" id="gpPstAsCst<?php echo $ii; ?>" value="<?php echo $options['postAs']; ?>" onchange="nxs_InpToDDChange(jQuery(this));" data-tid="gpPostAs<?php echo $ii; ?>" />         
          <div style="display: inline-block;"><a onclick="nxs_gpGetAllInfo(<?php echo $ii;?>, 1); jQuery(this).blur(); return false;" href="#"><img id="<?php echo $nt.$ii;?>rfrshImg" style="vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/refresh16.png' /></a></div></div> <img id="<?php echo $nt.$ii;?>ldImg" style="display: none;vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          </div>          
          </div>                                                    
    </div> 
    
    <br/><div style="width:100%;"><b><?php _e('Where to post:', 'nxs_snap'); ?></b>&nbsp;(<?php _e('Could be profile, page, collection or community', 'nxs_snap'); ?>)</div>
    <div id="nxsGPInfoDiv<?php echo $ii; ?>">
         <div style="width:100%;">
          <div>                   
          <select id="gpWhToPost<?php echo $ii; ?>" onchange="nxs_gpWhToPostChange('<?php echo $ii;?>',jQuery(this));" name="gp[<?php echo $ii;?>][postTo]">
            <?php $pgi = !empty($options['whToList'])?$options['whToList']:'';    
              if (!empty($options['postTo'])) { echo (empty($pgi) || (!empty($pgi) && stripos($pgi,$options['postTo']))===false)?'<option selected="selected" value="'.$options['postTo'].'">'.$options['postTo'].'</option>':''; }
            ?><option <?php if (empty($options['postTo']) || $options['postTo']=='p') echo 'selected="selected"' ?> value="p"><?php echo (!empty($options['postAsNm'])&& $options['postAs']!='p')?$options['postAsNm']:__('Profile'); ?></option><?php 
              if (!empty($options['postTo'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['postTo'].'"','selected="selected" value="'.$options['postTo'].'"',$pgi); } echo $pgi;
            ?><option value="a"><?php _e('.... Enter the Collection or Community ID'); ?></option>
          </select><div id="nxsGPInfoDivBlock<?php echo $ii; ?>" style="display: inline-block;"> <input type="text" style="display: none;" id="gpPgIDcst<?php echo $ii; ?>" value="<?php echo $options['postTo']; ?>" onchange="nxs_InpToDDChange(jQuery(this));" data-tid="gpWhToPost<?php echo $ii; ?>" /> </div> 
          </div>          
          </div>                                          
          <div id="nxsGPMsgDiv<?php echo $ii; ?>"><?php if (!empty($options['uMsg'])) echo $options['uMsg']; ?><?php if ($isNew) { ?><?php _e('Please enter your login/password to see the list of your pages, collections, and communities', 'nxs_snap'); ?><?php } ?></div>  
    </div> 
    
    <div id="nxsGPInfoDivComm<?php echo $ii; ?>" style="padding-left:15px; display: <?php echo (!empty($options['postTo']) && substr($options['postTo'],0,1)=='c')?'block':'none'; ?>;"><b><?php _e('Community Category:', 'nxs_snap'); ?></b>
      <div style="padding-left:15px;">      
          <select id="gpCommCat<?php echo $ii; ?>" name="gp[<?php echo $ii;?>][commCat]">
            <?php $pgi = (!empty($options['commCatsList']))?$options['commCatsList']:''; 
              if (!empty($options['commCat'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['commCat'].'"','selected="selected" value="'.$options['commCat'].'"',$pgi); } echo $pgi;
          ?></select>        
    </div></div>
    
    
   <br/><br/> 
   <div style="width:100%;"><strong id="altFormatText">Post Type:</strong>&lt;-- (<a id="showShAtt" onmouseout="hidePopShAtt('<?php echo $ii; ?>XG');" onmouseover="showPopShAtt('<?php echo $ii; ?>XG', event);" onclick="return false;" class="underdash" href="http://www.nextscripts.com/blog/"><?php _e('What\'s the difference?', 'nxs_snap'); ?></a>)  </div>                      
<div style="margin-left: 10px;">        
        <input type="radio" name="gp[<?php echo $ii; ?>][postType]" value="T" <?php if ($options['postType'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'nxs_snap'); ?> - <i><?php _e('just text message', 'nxs_snap'); ?></i><br/>                    
        <input type="radio" name="gp[<?php echo $ii; ?>][postType]" value="I" <?php if ($options['postType'] == 'I') echo 'checked="checked"'; ?> /> <?php _e('Google+ Image Post', 'nxs_snap'); ?> - <i><?php _e('big image with text message', 'nxs_snap'); ?></i><br/>
        <input type="radio" name="gp[<?php echo $ii; ?>][postType]" value="A" <?php if ( !isset($options['postType']) || $options['postType'] == '' || $options['postType'] == 'A') echo 'checked="checked"'; ?> /> <?php _e('Add blogpost to Google+ message as an attachment', 'nxs_snap'); ?><br/>
<div class="popShAtt" id="popShAtt<?php echo $ii; ?>XG"><h3><?php _e('Google+ Post Types', 'nxs_snap'); ?></h3><img src="<?php echo NXS_PLURL; ?>img/gpPostTypesDiff6.png" width="600" height="285" alt="<?php _e('Google+ Post Types', 'nxs_snap'); ?>"/></div>
   </div><br/>
    
    <script type="text/javascript">      
      jQuery('#apGPUName<?php echo $ii; ?>').change(function() { var u = jQuery(this).val();  var p = jQuery('#apGPPass<?php echo $ii; ?>').val(); if( u!='' && p!='' ) { nxs_gpGetAllInfo(<?php echo $ii; ?>,0); }  });
      jQuery('#apGPPass<?php echo $ii; ?>').change(function() { var u = jQuery('#apGPUName<?php echo $ii; ?>').val();  var p = jQuery(this).val(); if( u!='' && p!='' ) { nxs_gpGetAllInfo(<?php echo $ii; ?>,0); }  });
    </script> <?php $this->elemMsgFormat($ii,'Message Format','msgFormat',$options['msgFormat']); 
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['uPass']) && !empty($pval['uPass'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);    
        //if (empty($options[$ii]['pageID'])) { $loginError = $nt->connect($pval['uName'], $pval['uPass']); if (!$loginError){ $nt->getPgsCmns($pval['pageID']); }
        if (isset($pval['postTo']))  $options[$ii]['postTo'] = trim($pval['postTo']);                
        if (isset($pval['postAs']))  $options[$ii]['postAs'] = trim($pval['postAs']);  if (isset($pval['postAsNm']))  $options[$ii]['postAsNm'] = trim($pval['postAsNm']);                                
        if (isset($pval['commCat']))  $options[$ii]['commCat'] = trim($pval['commCat']);                
        if (isset($pval['postType']))  $options[$ii]['postType'] = trim($pval['postType']); else $options[$ii]['postType'] = 'A';
      } elseif ( count($pval)==1 ) if (isset($pval['do'])) $options[$ii]['do'] = $pval['do']; else $options[$ii]['do'] = 0; 
    } return $options;
  }
    
  //#### Show Post->Edit Meta Box  Settings
  function showEdPostNTSettings($ntOpts, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];
      foreach($ntOpts as $ii=>$ntOpt)  { $isFin = $this->checkIfSetupFinished($ntOpt); if (!$isFin) continue; 
        $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true)); if (is_array($pMeta) && !empty($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);         
        
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):''; 
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse']; $ntOpt['ii']=$ii;
         
        $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta); ?>
                
        <?php $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat); 
        ?>          
        <tr class="nxstbldo <?php echo 'nxstbldo'.strtoupper($nt).$ii; ?>"><th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 0px; padding-right:10px;"> <?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g') ?> <br/></th><td>     
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="T" <?php if ($postType == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g') ?>  - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="I" <?php if ($postType == 'I') echo 'checked="checked"'; ?> /> <?php _e('Image Post', 'social-networks-auto-poster-facebook-twitter-g') ?>  - <i><?php _e('Text with Image', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="A" <?php if ( empty($postType) || $postType == 'A') echo 'checked="checked"'; ?> /><?php _e('Text Post with "attached" blogpost', 'social-networks-auto-poster-facebook-twitter-g') ?>
        </td></tr>
          <?php
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
  //## GP Specific
  function getListOfPages($networks){ $opVal = array(); $pass = 'g9c1a'.nsx_doEncode($_POST['p']); $opNm = 'nxs_snap_gp_'.sha1('nxs_snap_gp'.$_POST['u'].$pass); $opVal = nxs_getOption($opNm); $ii = $_POST['ii']; $nt = new nxsAPI_GP(); // prr($opVal);
     $currPstAs = !empty($_POST['pstAs'])?$_POST['pstAs']:(!empty($networks['gp'][$ii])?$networks['gp'][$ii]['postAs']:'');
     if (empty($_POST['force']) && !empty($opVal['ck']) && !empty($opVal['pgsList']) ) $pgs = $opVal['pgsList']; else { if (!empty($opVal['ck'])) $nt->ck = $opVal['ck']; $loginError=$nt->connect($_POST['u'],$_POST['p']); 
       if (!$loginError){ $opVal['ck'] = $nt->ck; $pgs = $nt->getPgsList($currPstAs); }
         else { $outMsg = '<b style="color:red;">'.__('Login Problem').'&nbsp;-&nbsp;'.$loginError.'</b>'; if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
     } $pgCust = (!empty($pgs) && !empty($currPstAs) && stripos($pgs,$currPstAs)===false)?'<option selected="selected" value="'.$currPstAs.'">'.$currPstAs.'</option>':'';     
     if (!empty($_POST['isOut'])) echo $pgCust.'<option '.($currPstAs=='p'?'selected="selected" ':'').'value="p">'.__('Profile').'</option>'.$pgs.'<option style="color:#BD5200" value="a">'.__('...enter the Page ID').'</option>';
     $opVal['pgsList'] = $pgs; nxs_saveOption($opNm, $opVal); return $opVal;
  } 
  function getListWhereToPost($networks){ $opVal = array(); $pass = 'g9c1a'.nsx_doEncode($_POST['p']); $ii = $_POST['ii']; $nt = new nxsAPI_GP(); 
     $currPstAs = !empty($_POST['pstAs'])?$_POST['pstAs']:(!empty($networks['gp'][$ii])?$networks['gp'][$ii]['postAs']:''); $currPg = !empty($_POST['pg'])?$_POST['pg']:(!empty($networks['gp'][$ii])?$networks['gp'][$ii]['postTo']:'');
     $opNm = 'nxs_snap_gp_'.sha1('nxs_snap_gp'.$_POST['u'].$pass); $opVal = nxs_getOption($opNm); $opNm2 = 'nxs_snap_gp_wh'.sha1('nxs_snap_gpwh'.$_POST['u'].$pass.$currPstAs); $opVal2 = nxs_getOption($opNm2);
     
     if (empty($_POST['force']) && !empty($opVal['ck']) && !empty($opVal2['whToList']) ) $pgs = $opVal2['whToList']; else { if (!empty($opVal['ck'])) $nt->ck = $opVal['ck']; $loginError=$nt->connect($_POST['u'],$_POST['p']); 
       if (!$loginError){ $opVal['ck'] = $nt->ck; $pgs = $nt->getWhereToPostList($currPg, $currPstAs); }
         else { $outMsg = '<b style="color:red;">'.__('Login Problem').'&nbsp;-&nbsp;'.$loginError.'</b>'; if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
     } $pgCust = (!empty($currPg) && (empty($pgs) || (!empty($pgs) && stripos($pgs,$currPg)===false)))?'<option selected="selected" value="'.$currPg.'">'.$currPg.'</option>':'';     
     if (!empty($_POST['isOut'])) echo $pgCust.'<option '.($currPg=='p'?'selected="selected" ':'').'value="p">'.(!empty($_POST['pstAsNm'])?$_POST['pstAsNm']:__('Profile')).'</option>'.$pgs.'<option style="color:#BD5200" value="a">'.__('...enter the Collection or Community ID').'</option>';
     $opVal2['whToList'] = $pgs; nxs_saveOption($opNm2, $opVal2); return $opVal2;
  } 
  function getGPCommInfo($networks){ $ii = $_POST['ii']; $currPgID = !empty($_POST['pg'])?$_POST['pg']:(!empty($networks['gp'][$ii])?$networks['gp'][$ii]['postTo']:''); 
     $opVal = array(); $pass = 'g9c1a'.nsx_doEncode($_POST['p']); $opNm = 'nxs_snap_gp_cl_'.sha1('nxs_snap_gpcl'.$_POST['u'].$pass.$currPgID); $opVal = nxs_getOption($opNm);
     $currCat = !empty($_POST['comCat'])?$_POST['comCat']:(!empty($networks['gp'][$ii])?$networks['gp'][$ii]['commCat']:'');
     if (empty($_POST['force']) && !empty($opVal) && !empty($opVal['commCatsList'])) { 
         if (!empty($currCat)) { $opVal['commCatsList'] = str_ireplace('selected="selected" ','',$opVal['commCatsList']); $opVal['commCatsList'] = str_ireplace('value="'.$currCat.'"','selected="selected" value="'.$currCat.'"',$opVal['commCatsList']); }     
         if (!empty($_POST['isOut'])) echo $opVal['commCatsList']; 
     } else { $nt = new nxsAPI_GP(); $ii = $_POST['ii']; $loginError = $nt->connect(!empty($_POST['u'])?$_POST['u']:$networks['gp'][$ii]['uName'],!empty($_POST['p'])?$_POST['p']:$networks['gp'][$ii]['uPass']); 
       if (!$loginError){  //!empty($networks['gp'][$ii])?$networks['gp'][$ii]['pageID']:'';  echo "DX ".$currPgID; 
          if (!empty($currPgID) && substr($currPgID,0,1)=='c') { $currPgID = substr($currPgID,1); $opVal['commCatsList'] = $nt->getCCatsGP($currPgID,$currCat); nxs_saveOption($opNm, $opVal); if (!empty($_POST['isOut']))  echo $opVal['commCatsList']; }
       } else { if (!empty($_POST['isOut'])) echo $loginError; return $loginError; }
     } return $opVal;
  }
}}


if (!function_exists("nxs_doPublishToGP")) { function nxs_doPublishToGP($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true));
  ini_set('memory_limit','256M'); $cl = new nxs_snapClassGP(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}}  
?>