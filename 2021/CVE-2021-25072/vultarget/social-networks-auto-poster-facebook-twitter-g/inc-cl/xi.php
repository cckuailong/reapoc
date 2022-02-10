<?php    
//## NextScripts XING Connection Class
$nxs_snapAvNts[] = array('code'=>'XI', 'lcode'=>'xi', 'name'=>'XING', 'type'=>'Social Networks', 'ptype'=>'P', 'status'=>'A', 'desc'=>'Post text messages, images or share links.');

if (!class_exists("nxs_snapClassXI")) { class nxs_snapClassXI extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'XI', 'lcode'=>'xi', 'name'=>'XING', 'defNName'=>'', 'tstReq' => true, 'instrURL'=>'http://www.nextscripts.com/instructions/setup-installation-xing-social-networks-auto-poster/');  
  
  function toLatestVer($ntOpts){ if (!empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName']; $ntOptsOut['appKey'] = $ntOpts['appKey']; $ntOptsOut['appSec'] = $ntOpts['appSec']; $ntOptsOut['inclTags'] = $ntOpts['inclTags'];
        $ntOptsOut['postType'] = $ntOpts['postType']; $ntOptsOut['msgFormat'] = $ntOpts['msgFrmt']; $ntOptsOut['appAppUserID'] = $ntOpts['appAppUserID'];  $ntOptsOut['appAppUserName'] = $ntOpts['appAppUserName'];   $ntOptsOut['appPGUserName'] = $ntOpts['appPGUserName'];
        $ntOptsOut['oAuthToken'] = $ntOpts['oAuthToken']; $ntOptsOut['oAuthTokenSecret'] = $ntOpts['oAuthTokenSecret']; $ntOptsOut['accessToken'] = $ntOpts['accessToken']; $ntOptsOut['accessTokenSec'] = $ntOpts['accessTokenSec'];         
      break;
    } return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  }   
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts;  $this->showNTGroup(); }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'appKey'=>'', 'appSec'=>'', 'uName'=>'', 'uPass'=>'', 'inclTags'=>'1', 'postType'=>'A', 'postTypeP'=>'A', 'postTypeG'=>'I', 'msgFormat'=>"%EXCERPT%\r\n\r\n%URL%"); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return true;  return ((!empty($options['appAppUserID']) && !empty($options['accessToken'])) || !empty($options['uName']) );   }
  public function doAuth() { $ntInfo = $this->ntInfo; global $nxs_snapSetPgURL; if (isset($_GET['acc'])) $options = $this->nt[$_GET['acc']];
    if ( isset($_GET['auth']) && $_GET['auth']==$ntInfo['lcode']){
      $consumer_key = $options['appKey']; $consumer_secret = $options['appSec']; $callback_url = $nxs_snapSetPgURL."&auth=".$ntInfo['lcode']."a&acc=".$_GET['acc'];
      $tum_oauth = new nxs_OAuthBaseCl($consumer_key, $consumer_secret); $tum_oauth->baseURL = 'https://api.xing.com'; $tum_oauth->request_token_path = '/v1/request_token';
      $request_token = $tum_oauth->getReqToken($callback_url); $options['oAuthToken'] = $request_token['oauth_token']; $options['oAuthTokenSecret'] = $request_token['oauth_token_secret']; 
      prr($tum_oauth); prr($options);               
      switch ($tum_oauth->http_code) { case 201: case 200: $url = 'https://api.xing.com/v1/authorize?oauth_token='.$options['oAuthToken']; nxs_save_glbNtwrks($ntInfo['lcode'],$_GET['acc'],$options,'*'); 
        echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$url.'"</script>'; break; 
        default: echo '<br/><b style="color:red">Could not connect to XING. Refresh the page or try again later.</b>'; die();
      } die();
    }
    if ( isset($_GET['auth']) && $_GET['auth']==$ntInfo['lcode'].'a'){ $consumer_key = $options['appKey']; $consumer_secret = $options['appSec']; 
      $tum_oauth = new nxs_OAuthBaseCl($consumer_key, $consumer_secret, $options['oAuthToken'], $options['oAuthTokenSecret']); //prr($tum_oauth);
      $tum_oauth->baseURL = 'https://api.xing.com'; $tum_oauth->access_token_path = '/v1/access_token'; $access_token = $tum_oauth->getAccToken($_GET['oauth_verifier']); prr($access_token);
      $options['accessToken'] = $access_token['oauth_token'];  $options['accessTokenSec'] = $access_token['oauth_token_secret'];  
      $tum_oauth = new nxs_OAuthBaseCl($consumer_key, $consumer_secret, $options['accessToken'], $options['accessTokenSec']);               
      $uinfo = $tum_oauth->makeReq('https://api.xing.com/v1/users/me', ''); prr($uinfo);
      if (is_array($uinfo) && isset($uinfo['users']) && isset($uinfo['users'][0]) && is_array($uinfo['users'][0])) { $uinfo = $uinfo['users'][0]; $options['appPGUserName'] = $uinfo['page_name'];
        $options['appAppUserName'] = $uinfo['display_name']."(".$uinfo['page_name'].")"; $options['appAppUserID'] = $uinfo['id'];                         
      }  nxs_save_glbNtwrks($ntInfo['lcode'],$_GET['acc'],$options,'*');  //prr($options); die();
      if (!empty($options['appAppUserID'])) {  echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$nxs_snapSetPgURL.'"</script>';  die();}
        else die("<span style='color:red;'>ERROR: Authorization Error: <span style='color:darkred; font-weight: bold;'>".print_r($uinfo, true)."</span></span>");              
    }
  }
  
  function getPgsList($networks){ $opVal = array(); $pass = 'g9c1a'.nsx_doEncode($_POST['p']); $opNm = 'nxs_snap_xi_'.sha1('nxs_snap_xi'.$_POST['u'].$pass); $opVal = nxs_getOption($opNm); $ii = $_POST['ii']; $nt = new nxsAPI_XI();// $nt->debug = true; // prr($opVal);
     $currPstAs = !empty($_POST['pgcID'])?$_POST['pgcID']:(!empty($networks['xi'][$ii])?$networks['xi'][$ii]['pgcID']:'');
     if (empty($_POST['force']) && !empty($opVal['ck']) && !empty($opVal['pgsList']) ) $pgs = $opVal['pgsList']; else { if (!empty($opVal['ck'])) $nt->ck = $opVal['ck']; $loginError=$nt->connect($_POST['u'],$_POST['p']); 
       if (!$loginError){ $opVal['ck'] = $nt->ck;  $pgs = $nt->getPgsList($currPstAs); }
         else { $outMsg = '<b style="color:red;">'.__('Login Problem').'&nbsp;-&nbsp;'.$loginError.'</b>'; if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
     } $pgCust = (!empty($pgs) && !empty($currPstAs) && stripos($pgs,$currPstAs)===false)?'<option selected="selected" value="'.$currPstAs.'">'.$currPstAs.'</option>':'';     
     if (!empty($_POST['isOut'])) echo $pgCust.$pgs.'<option style="color:#BD5200" value="a">'.__('...enter the Company Page ID').'</option>';
     $opVal['pgsList'] = $pgs; nxs_saveOption($opNm, $opVal); return $opVal;
  }
  function getGrpList($networks){ $opVal = array(); $pass = 'g9c1a'.nsx_doEncode($_POST['p']); $opNm = 'nxs_snap_xi_'.sha1('nxs_snap_xi'.$_POST['u'].$pass); $opVal = nxs_getOption($opNm); $ii = $_POST['ii']; $nt = new nxsAPI_XI(); // prr($opVal);
     $currPstAs = !empty($_POST['pggID'])?$_POST['pggID']:(!empty($networks['xi'][$ii]['pggID'])?$networks['xi'][$ii]['pggID']:'');
     if (empty($_POST['force']) && !empty($opVal['ck']) && !empty($opVal['grpList']) ) $pgs = $opVal['grpList']; else { if (!empty($opVal['ck'])) $nt->ck = $opVal['ck']; $loginError=$nt->connect($_POST['u'],$_POST['p']); 
       if (!$loginError){ $opVal['ck'] = $nt->ck;  $pgs = $nt->getGrpList($currPstAs); }
         else { $outMsg = '<b style="color:red;">'.__('Login Problem').'&nbsp;-&nbsp;'.$loginError.'</b>'; if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
     } $pgCust = (!empty($pgs) && !empty($currPstAs) && stripos($pgs,$currPstAs)===false)?'<option selected="selected" value="'.$currPstAs.'">'.$currPstAs.'</option>':'';     
     if (!empty($_POST['isOut'])) echo $pgCust.$pgs.'<option style="color:#BD5200" value="a">'.__('...enter the Group ID').'</option>';
     $opVal['grpList'] = $pgs; nxs_saveOption($opNm, $opVal); return $opVal;
  }
  function getGrpForums($networks){ $opVal = array(); $pass = 'g9c1a'.nsx_doEncode($_POST['p']); $opNm = 'nxs_snap_xi_'.sha1('nxs_snap_xi'.$_POST['u'].$pass); $opVal = nxs_getOption($opNm); $ii = $_POST['ii']; $nt = new nxsAPI_XI();  $nt->debug = true;// prr($opVal);
     $currPstAs = !empty($_POST['pggID'])?$_POST['pggID']:(!empty($networks['xi'][$ii]['pggID'])?$networks['xi'][$ii]['pggID']:''); $currForum = !empty($_POST['gpfID'])?$_POST['gpfID']:(!empty($networks['xi'][$ii]['gpfID'])?$networks['xi'][$ii]['gpfID']:'');
     if (empty($_POST['force']) && !empty($opVal['ck']) && !empty($opVal['grpForums']) ) $pgs = $opVal['grpForums']; else { if (!empty($opVal['ck'])) $nt->ck = $opVal['ck']; $loginError=$nt->connect($_POST['u'],$_POST['p']); 
       if (!$loginError){ $opVal['ck'] = $nt->ck;  $pgs = $nt->getGrpForums('https://www.xing.com/communities/groups/'.$currPstAs, $currForum); }
         else { $outMsg = '<b style="color:red;">'.__('Login Problem').'&nbsp;-&nbsp;'.$loginError.'</b>'; if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
     } $pgCust = (!empty($pgs) && !empty($currForum) && stripos($pgs,$currForum)===false)?'<option selected="selected" value="'.$currForum.'">'.$currForum.'</option>':'';     
     if (!empty($_POST['isOut'])) echo $pgCust.$pgs.'<option style="color:#BD5200" value="a">'.__('...enter the Group Forum ID').'</option>';
     $opVal['grpForums'] = $pgs; nxs_saveOption($opNm, $opVal); return $opVal;
  }
  
  function accTab($ii, $options, $isNew=false){ global $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; ?>
      
   <div style="display: <?php echo (empty($options['apiToUse']) && empty($options['appKey']))?"block":"none"; ?>;">    
      <div style="width:100%; text-align: center; color:#005800; font-weight: bold; font-size: 14px;">You can choose what API you would like to use. </div>          
      <span style="color:#005800; font-weight: bold; font-size: 14px;">NextScripts API for XING:</span> Premium API with extended functionality. <br/>
      <span style="color:#FF0000; font-weight: bold; font-size: 14px;">[<a style="color:#FF0000;" target="_blank" href="http://nxs.fyi/xingmsg">Officially Discontinued by XING</a>]</span>&nbsp;<span style="color:#005800; font-weight: bold; font-size: 14px;"> XING Native API:</span> Free built-in API from XING. <br/><br/>              
      
      <select name="<?php echo $nt; ?>[<?php echo $ii; ?>][apiToUse]" onchange="jQuery('.nxs_<?php echo $nt; ?>_apidiv_<?php echo $ii; ?>').hide(); jQuery('.nxs_<?php echo $nt; ?>_api'+jQuery(this).val()+'div_<?php echo $ii; ?>').show();  "><option <?php echo (empty($options['apiToUse']) || $options['apiToUse'] =='nx')?"selected":""; ?> value="nx">NextScripts API</option><option <?php echo (!empty($options['apiToUse']) && $options['apiToUse'] =='xixi')?"selected":""; ?> value="xixi">XING API</option></select><hr/>    
    </div>
    <?php if (!empty($options['appKey'])) $options['apiToUse'] = 'xixi'; ?>
    <div id="nxs_<?php echo $nt; ?>_apinxdiv_<?php echo $ii; ?>" class="nxs_<?php echo $nt; ?>_apidiv_<?php echo $ii; ?> nxs_<?php echo $nt; ?>_apinxdiv_<?php echo $ii; ?>" style="display: <?php echo ((empty($options['apiToUse']) && empty($options['appKey'])) || $options['apiToUse'] =='nx')?"block":"none"; ?>;">
    <h3>NextScripts API</h3>
    
    <?php if (class_exists('nxsAPI_XI')) { $opNm = 'nxs_snap_xi_'.sha1('nxs_snap_xi'.$options['uName'].$options['uPass']); $opVal = nxs_getOption($opNm); //prr($opVal);
      if (empty($opVal) && !empty($options['uPass'])){ $tPST=(!empty($_POST))?$_POST:''; $_POST['pgcID']=!empty($options['pgcID'])?$options['pgcID']:'';  $_POST['pggID']=!empty($options['pggID'])?$options['pggID']:''; 
        $_POST['u']=$options['uName']; $_POST['p']=$options['uPass']; $_POST['ii']=$ii; $ntw[$nt][$ii]=$options; $opVal = $this->getPgsList($ntw); $opVal = $this->getGrpList($ntw); $_POST = $tPST; 
      } if (!empty($opVal) & !is_array($opVal)) $options['uMsg'] = $opVal; else { if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } 
    ?>
    <div class="subDiv" id="sub<?php echo $ii; ?>DivN" style="display: block;"><?php $this->elemUserPass($ii, $options['uName'], $options['uPass']); ?></div><br/>
    <script type="text/javascript">      
      jQuery('#apXIUName<?php echo $ii; ?>').change(function() { var u = jQuery(this).val();  var p = jQuery('#apXIPass<?php echo $ii; ?>').val(); if( u!='' && p!='' ) { nxs_xi2GetPages(<?php echo $ii; ?>,0); }  });
      jQuery('#apXIPass<?php echo $ii; ?>').change(function() { var u = jQuery('#apXIUName<?php echo $ii; ?>').val();  var p = jQuery(this).val(); if( u!='' && p!='' ) { nxs_xi2GetPages(<?php echo $ii; ?>,0); }  });
      
      jQuery('.xiWhereToPost<?php echo $ii; ?>').change(function() { if (jQuery(this).val()!='P') jQuery('#xiPostType<?php echo $ii; ?>').show(); else jQuery('#xiPostType<?php echo $ii; ?>').hide();  });
      
    </script>
    <div style="width:100%;"><b style="font-size: 15px;"><?php _e('Where to Post', 'social-networks-auto-poster-facebook-twitter-g'); if (empty($options['whToPost'])) $options['whToPost'] = 'PR'; ?>:</b> </div>
      <div style="margin-left: 10px;">        
        <input class="xiWhereToPost<?php echo $ii; ?>" type="radio" name="xi[<?php echo $ii; ?>][whToPost]" value="PR" <?php if ($options['whToPost'] == 'PR') echo 'checked="checked"'; ?> /> 
        <span style="font-size: 15px;"><?php _e('Profile Update', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Post to your profile', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/></span>
        
        <div style="margin-left: 30px;"> <div style="width:100%;"><strong style="font-size: 15px;">Post Type:</strong></div><div style="margin-left: 10px;">
           <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postTypeP]" value="T" <?php if ($options['postTypeP'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                                  
           <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postTypeP]" value="A" <?php if ( !isset($options['postTypeP']) || $options['postTypeP'] == '' || $options['postTypeP'] == 'A') echo 'checked="checked"'; ?> /> <?php _e('Post link to the blogpost', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
         </div><br/>
        </div>
        
        <input class="xiWhereToPost<?php echo $ii; ?>" type="radio" name="xi[<?php echo $ii; ?>][whToPost]" value="C" <?php if ($options['whToPost'] == 'C') echo 'checked="checked"'; ?> /> 
         <span style="font-size: 15px;"><?php _e('Company Page', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Post to Company page', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/> </span>
        
        <div style="margin-left: 30px;<?php echo (empty($options['uName']) && empty($options['uPass']))?'display:none;':''; ?>" id="nxsXI2InfoDiv<?php echo $ii; ?>"> 
         <div style="width:100%;">
          <div>                   
          <select id="xi2pgID<?php echo $ii; ?>" onchange="nxs_xi2PageChange('<?php echo $ii;?>',jQuery(this));" name="xi[<?php echo $ii;?>][pgcID]">
            <?php $pgi = !empty($options['pgsList'])?$options['pgsList']:''; 
              if (!empty($options['pgcID'])) { echo (!empty($options['pgcID']) && stripos($pgi,$options['pgcID'])===false)?'<option selected="selected" value="'.$options['pgcID'].'">'.$options['pgcID'].'</option>':''; }            
              if (!empty($options['pgcID'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['pgcID'].'"','selected="selected" value="'.$options['pgcID'].'"',$pgi); 
                $pgi = str_ireplace('data-val="'.$options['pgcID'].'"','selected="selected" data-val="'.$options['pgcID'].'"',$pgi);
              }               
              echo $pgi;
            ?><option value="a"><?php _e('.... Enter the Company Page ID'); ?></option>
          </select>
          <div id="nxsXI2InfoDivBlock<?php echo $ii; ?>" style="display: inline-block;">
          <input type="text" style="display: none;" id="xi2InpCst<?php echo $ii; ?>" value="<?php echo $options['pgcID']; ?>" onblur="nxs_InpToDDBlur(jQuery(this));"  onchange="nxs_InpToDDChange(jQuery(this));" data-tid="xi2pgID<?php echo $ii; ?>" />         
          <div style="display: inline-block;"><a onclick="nxs_xi2GetPages(<?php echo $ii;?>, 1); jQuery(this).blur(); return false;" href="#"><img id="<?php echo $nt.$ii;?>2rfrshImg" style="vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/refresh16.png' /></a></div></div> <img id="<?php echo $nt.$ii;?>2ldImg" style="display: none;vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          </div>          
          </div> <div id="nxsXI2MsgDiv<?php echo $ii; ?>"><?php if (!empty($options['uMsg'])) echo $options['uMsg']; ?><?php if ($isNew) { ?><i style="color: #800080"><?php _e('Please Enter your username and password to select your page', 'nxs_snap'); ?><?php } ?></i></div>                        <div style="margin-top: 10px; margin-bottom: 10px;"><?php $this->elemTitleFormat($ii,'Company Update Title Format','msgCTFormat',empty($options['msgCTFormat'])?'%TITLE%':$options['msgCTFormat']); ?></div>                                                                                                                          
        </div> 
        
        <br/><input class="xiWhereToPost<?php echo $ii; ?>" type="radio" name="xi[<?php echo $ii; ?>][whToPost]" value="G" <?php if ($options['whToPost'] == 'G') echo 'checked="checked"'; ?> /> 
        <span style="font-size: 15px;"><?php _e('Group', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Post to LinkedIn Group', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/> </span>
        
        <div style="margin-left: 30px;<?php echo (empty($options['uName']) && empty($options['uPass']))?'display:none;':''; ?>" id="nxsXI2GInfoDiv<?php echo $ii; ?>"> 
         <div style="width:100%;">
          <div>                   
          <select id="xi2GpgID<?php echo $ii; ?>" onchange="nxs_xi2GPageChange('<?php echo $ii;?>',jQuery(this));" name="xi[<?php echo $ii;?>][pggID]">
            <?php $pgi = !empty($options['grpList'])?$options['grpList']:''; 
              if (!empty($options['pggID'])) { echo (!empty($options['pggID']) && stripos($pgi,$options['pggID'])===false)?'<option selected="selected" value="'.$options['pggID'].'">'.$options['pggID'].'</option>':''; }            
              if (!empty($options['pggID'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['pggID'].'"','selected="selected" value="'.$options['pggID'].'"',$pgi); 
                $pgi = str_ireplace('data-val="'.$options['pggID'].'"','selected="selected" data-val="'.$options['pggID'].'"',$pgi);
              }               
              echo $pgi;
            ?><option value="a"><?php _e('.... Enter the Group ID'); ?></option>
          </select>
          <div id="nxsXI2GInfoDivBlock<?php echo $ii; ?>" style="display: inline-block;">
          <input type="text" style="display: none;" id="xi2GInpCst<?php echo $ii; ?>" value="<?php echo $options['pgcID']; ?>" onblur="nxs_InpToDDBlur(jQuery(this));"  onchange="nxs_InpToDDChange(jQuery(this));" data-tid="xi2GpgID<?php echo $ii; ?>" />         
          </div> <img id="<?php echo $nt.$ii;?>3ldImg" style="display: none;vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          </div>          
          </div>  
          
        </div>                        
        
        <div style="margin-left: 20px;">
        
        <div style="margin-left: 30px; margin-top: 10px; font-size: 15px;<?php echo (empty($options['uName']) && empty($options['uPass']))?'display:none;':''; ?>" id="nxsXI2GInfoDiv<?php echo $ii; ?>"> <?php _e('Please select a Group Forum', 'nxs_snap'); ?><br/>
         <div style="width:100%;">
          <div>                   
          <select id="xi2GfID<?php echo $ii; ?>" onchange="nxs_xi2GfChange('<?php echo $ii;?>',jQuery(this));" name="xi[<?php echo $ii;?>][gpfID]">
            <?php $pgi = !empty($options['grpForums'])?$options['grpForums']:''; 
              if (!empty($options['gpfID'])) { echo (!empty($options['gpfID']) && stripos($pgi,$options['gpfID'])===false)?'<option selected="selected" value="'.$options['gpfID'].'">'.$options['gpfID'].'</option>':''; }            
              if (!empty($options['gpfID'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['gpfID'].'"','selected="selected" value="'.$options['gpfID'].'"',$pgi); 
                $pgi = str_ireplace('data-val="'.$options['gpfID'].'"','selected="selected" data-val="'.$options['gpfID'].'"',$pgi);
              }               
              echo $pgi;
            ?><option value="a"><?php _e('.... Enter the Group Forum ID'); ?></option>
          </select>
          <div id="nxsXI2GInfoDivBlock<?php echo $ii; ?>" style="display: inline-block;">
          <input type="text" style="display: none;" id="xi2GfInpCst<?php echo $ii; ?>" value="<?php echo $options['gpfID']; ?>" onblur="nxs_InpToDDBlur(jQuery(this));"  onchange="nxs_InpToDDChange(jQuery(this));" data-tid="xi2GpgID<?php echo $ii; ?>" />         
          </div> <img id="<?php echo $nt.$ii;?>3ldImg" style="display: none;vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          </div>          
          </div>  
          <div style="margin-top: 10px; margin-bottom: 10px;"><?php $this->elemTitleFormat($ii,'Group Title Format','msgTFormat',empty($options['msgTFormat'])?'%TITLE%':$options['msgTFormat']); ?></div>
          
          <div style="width:100%;"><strong style="font-size: 15px;">Post Type:</strong></div><div style="margin-left: 10px;">
            <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postTypeG]" value="T" <?php if ($options['postTypeG'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                                  
            <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postTypeG]" value="I" <?php if ( !isset($options['postTypeG']) || $options['postTypeG'] == '' || $options['postTypeG'] == 'I') echo 'checked="checked"'; ?> /> <?php _e('Image Post', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
          </div>
        </div>                       
        
        </div>
        
        </div>
        
        <br/>     
          
    <?php } else { nxs_show_noLibWrn('"NextScripts API Library for LinkedIN" is NOT installed'); } ?>           
    </div>
    
    <div id="nxs_<?php echo $nt; ?>_apixixidiv_<?php echo $ii; ?>" class="nxs_<?php echo $nt; ?>_apidiv_<?php echo $ii; ?> nxs_<?php echo $nt; ?>_apixixidiv_<?php echo $ii; ?>" style="display: <?php echo (!empty($options['appKey']) || (!empty($options['apiToUse']) && $options['apiToUse'] =='xixi'))?"block":"none"; ?>;">
   
   <div style="color:red;padding:5px;margin:5px; border: 1px solid darkred;">[January 2017] XING has decided to discontinue it's free public API. More Info: <a target="_blank" href="http://nxs.fyi/xingmsg">http://nxs.fyi/xingmsg</a>.<br/>If you have existing XING app, you still can use it.<br/></div>
   
    <?php $this->elemKeySecret($ii,'XING Consumer Key','XING Consumer Secret', $options['appKey'], $options['appSec']); ?>    <br/>
    <?php  if($options['appKey']=='') { ?>
      <b><?php _e('Authorize Your '.$ntInfo['name'].' Account', 'social-networks-auto-poster-facebook-twitter-g'); ?></b> <?php _e('Please click "Update Settings" to be able to Authorize your account.', 'social-networks-auto-poster-facebook-twitter-g');  
    } else { if(isset($options['appAppUserID']) && $options['appAppUserID']>0) { 
      _e('Your '.$ntInfo['name'].' Account has been authorized.', 'social-networks-auto-poster-facebook-twitter-g'); ?> User ID: <?php _e(apply_filters('format_to_edit', htmlentities($options['appAppUserID'].' - '.$options['appAppUserName'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>.
      <?php _e('You can', 'social-networks-auto-poster-facebook-twitter-g'); ?> Re- <?php } ?>            
      <a href="<?php echo $nxs_snapSetPgURL;?>&auth=<?php echo $nt; ?>&acc=<?php echo $ii; ?>">Authorize Your <?php echo $ntInfo['name']; ?> Account</a>            
      <?php if (!isset($options['appAppUserID']) || $options['appAppUserID']<1) { ?> <div class="blnkg">&lt;=== <?php _e('Authorize your account', 'social-networks-auto-poster-facebook-twitter-g'); ?> ===</div> <?php } 
    } ?><br/><br/> 
    
    <div style="width:100%;"><strong id="altFormatText">Post Type:</strong></div>
    <div style="margin-left: 10px;">
      <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="T" <?php if ($options['postType'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                                  
     <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="A" <?php if ( !isset($options['postType']) || $options['postType'] == '' || $options['postType'] == 'A') echo 'checked="checked"'; ?> /> <?php _e('Post link to the blogpost', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
    </div><br/>
    
    </div>    
    <br/><?php $this->elemMsgFormat($ii,'Message Text Format','msgFormat',$options['msgFormat']); ?>
    <br/>
    
    <?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['appKey']) || !empty($pval['uName'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
        //## Uniqe Items        
        if (isset($pval['apiToUse'])) $options[$ii]['apiToUse'] = trim($pval['apiToUse']);
        if (isset($pval['whToPost'])) $options[$ii]['whToPost'] = trim($pval['whToPost']);  
        
        if (isset($pval['postTypeP'])) $options[$ii]['postTypeP'] = trim($pval['postTypeP']);  
        if (isset($pval['postTypeG'])) $options[$ii]['postTypeG'] = trim($pval['postTypeG']);  
        
        if (isset($pval['gpfID'])) $options[$ii]['gpfID'] = trim($pval['gpfID']);  
        if (isset($pval['pggID'])) $options[$ii]['pggID'] = trim($pval['pggID']);  
        if (isset($pval['pgcID'])) $options[$ii]['pgcID'] = trim($pval['pgcID']);  
        
        
        
        if (isset($pval['msgCTFormat'])) $options[$ii]['msgCTFormat'] = trim($pval['msgCTFormat']);
        
        if (isset($pval['inclTags'])) $options[$ii]['inclTags'] = trim($pval['inclTags']); else $options[$ii]['inclTags'] = 0;                       
      } elseif ( count($pval)==1 ) if (isset($pval['do'])) $options[$ii]['do'] = $pval['do']; else $options[$ii]['do'] = 0; 
    } return $options;
  }
    
  //#### Show Post->Edit Meta Box Settings
  
  function showEdPostNTSettings($ntOpts, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];
      foreach($ntOpts as $ii=>$ntOpt)  { $isFin = $this->checkIfSetupFinished($ntOpt); if (!$isFin) continue; 
        $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true)); if (is_array($pMeta) && !empty($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);         
        
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
        $postTypeP = isset($ntOpt['postTypeP'])?$ntOpt['postTypeP']:''; $postTypeG = isset($ntOpt['postTypeG'])?$ntOpt['postTypeG']:'';
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; 
        $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):''; $msgTCFormat = !empty($ntOpt['msgCTFormat'])?htmlentities($ntOpt['msgCTFormat'], ENT_COMPAT, "UTF-8"):'';        
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse']; $ntOpt['ii']=$ii;
        
        $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta); 
        
        if (!empty($ntOpt['apiToUse']) && $ntOpt['apiToUse']=='nx') { 
            if (empty($ntOpt['whToPost']) || $ntOpt['whToPost']=='PR') { ?>
        
        <tr class="nxstbldo <?php echo 'nxstbldo'.strtoupper($nt).$ii; ?>"><th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 0px; padding-right:10px;"> <?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g') ?> <br/></th><td>     
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postTypeP]" value="T" <?php if ($postTypeP == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g') ?>  - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postTypeP]" value="A" <?php if ( !isset($postTypeP) || $postTypeP == '' || $postTypeP == 'A') echo 'checked="checked"'; ?> /><?php _e('Text Post with "attached" blogpost', 'social-networks-auto-poster-facebook-twitter-g') ?>
        </td></tr>    
        <?php } elseif( $ntOpt['whToPost']=='C') { $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'), $msgTCFormat);  } else { $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'), $msgTFormat); ?> 
            <tr class="nxstbldo <?php echo 'nxstbldo'.strtoupper($nt).$ii; ?>"><th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 0px; padding-right:10px;"> <?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g') ?> <br/></th><td>     
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postTypeG]" value="T" <?php if ($postTypeG == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g') ?>  - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postTypeG]" value="A" <?php if ( !isset($postTypeG) || $postTypeG == '' || $postTypeG == 'I') echo 'checked="checked"'; ?> /><?php _e('Image Post', 'social-networks-auto-poster-facebook-twitter-g') ?>
        </td></tr>    
        <?php } ?>    
         
        <?php } else { ?> 
        <tr class="nxstbldo <?php echo 'nxstbldo'.strtoupper($nt).$ii; ?>"><th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 0px; padding-right:10px;"> <?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g') ?> <br/></th><td>     
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="T" <?php if ($postType == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g') ?>  - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="A" <?php if ( !isset($postType) || $postType == '' || $postType == 'A') echo 'checked="checked"'; ?> /><?php _e('Text Post with "attached" blogpost', 'social-networks-auto-poster-facebook-twitter-g') ?>
        </td></tr>
        <?php } $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat); 
       /* ## Select Image & URL ## */  nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);     
     }
  }
  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);     
    if (!empty($pMeta['msgTFormat'])) $optMt['msgCTFormat'] = $pMeta['msgTFormat'];       
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ //prr($message); prr($options);
    if (!empty($postID)) { $postType = $options['postType'];
      if ($postType=='A') if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'medium');  
      if ($postType=='I') if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'full');
      if (preg_match("/noImg.\.png/i", $imgURL)) { $imgURL = ''; $isNoImg = true; }
      $message['imageURL'] = $imgURL;
    }
  }   
  
}}

if (!function_exists("nxs_doPublishToXI")) { function nxs_doPublishToXI($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassXI(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); }} 

?>