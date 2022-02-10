<?php

if (!class_exists('nxs_snapClassNT')) { class nxs_snapClassNT {    
    var $nt = array(); var $ntInfo = array(); var $noFuncMsg = ''; var $wp_ntwrksSaveName='NS_SNAutoPoster';
    
    public function checkIfSetupFinished($options) { return 1; }
    public function checkIfFunc() { return true; }
    public function doAuth() { }
    public function showAuthTop(){  global $nxs_snapSetPgURL;  ?>
        <div class="nxs_authPopup"><div class="nxs_authPopupHdr">Auth Info. This is normal technical authorization info that will dissapear unless you get some errors.<div class="nxs_authPopupClose"><a href="#" onclick="window.location = '<?php echo $nxs_snapSetPgURL;?>'">Close</a></div></div><div class="nxs_authPopupIn">
        <?php echo '-= This is normal technical authorization info that will dissapear. (Unless you get some errors. If you do get errors please check them at the <a target="_blank" href="http://www.nextscripts.com/support-faq/">FAQ Page</a>) =- <br/><br/>';
    }
    public function toLatestVerNTGen($ntOpts) { $out = array(); $out['rpstBtwDays'] = !empty($ntOpts['rpstBtwDays'])?$ntOpts['rpstBtwDays']:''; $out['rpstRndMins'] = !empty($ntOpts['rpstRndMins'])?$ntOpts['rpstRndMins']:''; 
      $out['rpstPostIncl'] = !empty($ntOpts['rpstPostIncl'])?$ntOpts['rpstPostIncl']:''; $out['rpstType'] = !empty($ntOpts['rpstType'])?$ntOpts['rpstType']:'';       $out['rpstTimeType'] = !empty($ntOpts['rpstTimeType'])?$ntOpts['rpstTimeType']:'';
      $out['rpstFromTime'] = !empty($ntOpts['rpstFromTime'])?$ntOpts['rpstFromTime']:''; $out['rpstToTime'] = !empty($ntOpts['rpstToTime'])?$ntOpts['rpstToTime']:''; $out['rpstOLDays'] = !empty($ntOpts['rpstOLDays'])?$ntOpts['rpstOLDays']:''; 
      $out['rpstNWDays'] = !empty($ntOpts['rpstNWDays'])?$ntOpts['rpstNWDays']:'';       $out['nxsCPTSeld'] = !empty($ntOpts['nxsCPTSeld'])?$ntOpts['nxsCPTSeld']:''; $out['tagsSel'] = !empty($ntOpts['tagsSel'])?$ntOpts['tagsSel']:'';
      $out['rpstBtwHrsT'] = !empty($ntOpts['rpstBtwHrsT'])?$ntOpts['rpstBtwHrsT']:'';    $out['tagsSelX'] = !empty($ntOpts['tagsSelX'])?$ntOpts['tagsSelX']:'';       $out['rpstBtwHrsType'] = !empty($ntOpts['rpstBtwHrsType'])?$ntOpts['rpstBtwHrsType']:''; 
      $out['rpstBtwHrsF'] = !empty($ntOpts['rpstBtwHrsF'])?$ntOpts['rpstBtwHrsF']:'';    $out['nDays'] = !empty($ntOpts['nDays'])?$ntOpts['nDays']:'';                $out['nHrs'] = !empty($ntOpts['nHrs'])?$ntOpts['nHrs']:'';
      $out['proxy'] = !empty($ntOpts['proxy'])?$ntOpts['proxy']:'';  $out['fltrs'] = !empty($ntOpts['fltrs'])?$ntOpts['fltrs']:'';  $out['fltrsOn'] = !empty($ntOpts['fltrsOn'])?$ntOpts['fltrsOn']:'';
      $out['nMin'] = !empty($ntOpts['nMin'])?$ntOpts['nMin']:''; $out['qTLng'] = !empty($ntOpts['qTLng'])?$ntOpts['qTLng']:''; if (!empty($ntOpts['wpImgSize'])) $out['wpImgSize'] = $ntOpts['wpImgSize']; $out['v'] = NXS_SETV;     
      return $out;
    }
    
    public function showNTGroup() { $cbo = count($this->nt); $this->doAuth();  ?> <div class="nxs_box" onmouseover="jQuery('.addMore<?php echo $this->ntInfo['code']; ?>').show();" onmouseout="jQuery('.addMore<?php echo $this->ntInfo['code']; ?>').hide();">
        <div class="nxs_box_header">
          <div class="nsx_iconedTitle" style="margin-bottom:1px;background-image:url(<?php echo NXS_PLURL;?>img/<?php echo (!empty($this->ntInfo['imgcode']))?$this->ntInfo['imgcode']:$this->ntInfo['lcode']; ?>16.png);"><?php echo $this->ntInfo['name']; ?>
            <?php if ($cbo>1){ ?><div class="nsBigText"><?php echo '(<span id="nxsNumOfAcc_'.$this->ntInfo['lcode'].'">'.$cbo."</span> "; _e('accounts', 'social-networks-auto-poster-facebook-twitter-g'); echo ")"; ?></div><?php } ?>
            <span style="display: none;" class="addMore<?php echo $this->ntInfo['code']; ?>">&nbsp;&nbsp;&nbsp;<a data-nt="<?php echo $this->ntInfo['code'].(1+max(array_keys($this->nt))); ?>" style="font-size: 12px; text-decoration: none;" href="#" class="nxs_snapAddNew">[Add New <?php echo $this->ntInfo['name']; ?> account]</a></span>
          </div>
        </div>
        <div class="nxs_box_inside"><?php $jj = 0;  if(!$this->checkIfFunc()) echo $this->noFuncMsg; else { uasort($this->nt, 'nxsLstSort'); foreach ($this->nt as $indx=>$pbo){ $jj++; $pbo['jj']=$jj; $pbo['cbo']=$cbo; $this->showNTLine($indx, $pbo); }}
          if ($jj>7) { ?> <div style="padding-left:5px;padding-top:5px;"><a href="#" onclick="jQuery('.showMore<?php echo $this->ntInfo['code']; ?>').show(); jQuery(this).parent().hide(); return false;">Show More[<?php echo ($cbo-5); ?>]</a></div>  <?php } ?>        
        </div>
      </div> <?php
    }
    public function showNTLine($indx, $pbo) { if (!isset($pbo['aName'])) $pbo['aName'] = ''; if (!isset($pbo['do']) && isset($pbo['do'.$this->ntInfo['code']])) $pbo['do'] = $pbo['do'.$this->ntInfo['code']]; $jj = $pbo['jj']; $cbo = $pbo['cbo'];
      if (empty($pbo['nName'])) $pbo['nName'] = $this->makeUName($pbo, $indx); if (empty($pbo[$this->ntInfo['lcode'].'OK'])) $pbo[$this->ntInfo['lcode'].'OK'] = $this->checkIfSetupFinished($pbo); ?>
      <div id="dom<?php echo $this->ntInfo['code'].$indx; ?>Div" style="padding-bottom: 3px;<?php echo ($cbo>7 && $jj>5)?'display:none;" class="showMore'.$this->ntInfo['code'].'"':'"'; ?>  onmouseover="jQuery('.showInlineMenu<?php echo $this->ntInfo['code'].$indx; ?>').show();jQuery(this).addClass('nxsHiLightBorder');" onmouseout="jQuery('.showInlineMenu<?php echo $this->ntInfo['code'].$indx; ?>').hide();jQuery(this).removeClass('nxsHiLightBorder');"">
        <div style="margin:0px;margin-left:5px;"> <img id="<?php echo $this->ntInfo['code'].$indx;?>LoadingImg" style="display: none;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          <?php if ((int)$pbo['do'] > 0 && ((isset($pbo['fltrsOn']) && (int)$pbo['fltrsOn'] == 1))) { $fltInfo = nxsAnalyzePostFilters($pbo['fltrs']); ?> 
            <input type="radio" id="rbtn<?php echo $this->ntInfo['lcode'].$indx; ?>" value="2" name="<?php echo $this->ntInfo['lcode']; ?>[<?php echo $indx; ?>][do]" checked="checked" class="nxs_acctcb" data-fltinfo="<?php echo $fltInfo; ?>" /> 
          <?php } else { ?>            
            <input value="0" name="<?php echo $this->ntInfo['lcode']; ?>[<?php echo $indx; ?>][do]" type="hidden" />             
            <input value="1" name="<?php echo $this->ntInfo['lcode']; ?>[<?php echo $indx; ?>][do]" type="checkbox" class="nxs_acctcb" <?php if ((int)$pbo['do'] > 0) echo "checked"; ?> />             
          <?php } ?>              
            <strong><?php  _e('Auto-publish to', 'social-networks-auto-poster-facebook-twitter-g'); ?> <?php echo $this->ntInfo['name']; ?> <i style="color: #005800;"><?php if($pbo['nName']!='') echo "(".$pbo['nName'].")"; ?></i></strong>
            &nbsp;&nbsp;<?php if ($this->ntInfo['tstReq'] && empty($pbo[$this->ntInfo['lcode'].'OK'])){ ?><b style="color: #800000"><?php  _e('Attention required. Unfinished setup', 'social-networks-auto-poster-facebook-twitter-g'); ?> ==&gt;</b><?php } ?>              
            <span style="padding-left: 0px; display: none;" class="showInlineMenu<?php echo $this->ntInfo['code'].$indx; ?>">
            <a id="do<?php echo $this->ntInfo['code'].$indx; ?>AG" href="#" onclick="doGetHideNTBlock('<?php echo $this->ntInfo['code'];?>' , '<?php echo $indx; ?>');return false;">[<?php  _e('Show Settings', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;          
            <a href="#" onclick="doDelAcct('<?php echo $this->ntInfo['lcode']; ?>', '<?php echo $indx; ?>', '<?php if (isset($pbo['bgBlogID'])) echo $pbo['nName']; ?>');return false;">[<?php  _e('Remove', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;          
            <a href="#" onclick="doDuplAcct('<?php echo $this->ntInfo['lcode']; ?>', '<?php echo $indx; ?>', '<?php if (isset($pbo['bgBlogID'])) echo $pbo['nName']; ?>');return false;">[<?php  _e('Duplicate', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a></span>
        </div><div id="nxsNTSetDiv<?php echo $this->ntInfo['code'].$indx; ?>"></div> 
      </div><?php 
    }
       
    function showNoAPIMsg($ii, $options){ ?> <div id="do<?php echo $this->ntInfo['code'].$ii; ?>Div" class="insOneDiv<?php echo " clNewNTSets"; ?>"><div style="border: 2px solid darkred; padding: 25px 15px 15px 15px; margin: 3px; background-color: #fffaf0;"> 
            <span style="font-size: 16px; color:darkred;line-height: 24px;"><?php global $nxs_apiLInfo; if ($this->ntInfo['code']=='IG' && $nxs_apiLInfo['noIG']==true) echo $this->noFuncMsg2; else echo $this->noFuncMsg; ?></span><br/><a href="http://www.nextscripts.com/faq/third-party-libraries-autopost-google-pinterest/" target="_blank">More info about third party libraries.</a><br/><hr/> <div style="font-size: 16px; color:#005800; font-weight: bold; margin-top: 12px; margin-bottom: 7px;">You can get API library from NextScripts.</div>
            <div style="padding-bottom: 5px;"><a href="http://www.nextscripts.com/snap-api/">SNAP API Libarary</a> adds autoposting to:</div> <span class="nxs_txtIcon nxs_ti_gp">Google+</span>, <span class="nxs_txtIcon nxs_ti_pn">Pinterest</span>, <span class="nxs_txtIcon nxs_ti_ig">Instagram</span>, <span class="nxs_txtIcon nxs_ti_rd">Reddit</span>, &nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_yt">YouTube</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_fp">Flipboard</span>, <span class="nxs_txtIcon nxs_ti_li">LinkedIn Company Pages and Groups</span><br><br>          
            All NextScripts SNAP API libraries are included and automatically installed with the  <a href="http://www.nextscripts.com/social-networks-auto-poster-for-wp-multiple-accounts/" target="_blank">"Pro" (Multiaccount) Edition of the SNAP plugin</a>. Pro version upgrade also adds the ability to configure more then one account for each social network and some addidional features.<br><br>
            <div align="center"><a target="_blank" href="http://www.nextscripts.com/social-networks-auto-poster-for-wp-multiple-accounts/#getit" class="NXSButton" id="nxs_snapUPG">Get SNAP Pro Plugin with SNAP API</a></div>
            <div style="font-size: 10px; margin-top: 20px;">*If you already have API, please follow instructions from the readme.txt file.</div>
          </div> </div> <?php 
    }
    
    
    public function showGNewNTSettings($ii,$options) { ?><div id="dom<?php echo $this->ntInfo['code'].$ii; ?>Div"> <?php if(!$this->checkIfFunc()) $this->showNoAPIMsg($ii,$options); else $this->showNTSettings($ii, $options, true);  ?></div> <?php }
    public function showNewNTSettings($mgpo) { 
                        
    }
    public function makeUName($options, $ii) { return $this->ntInfo['name']." #".$ii; }                        
    
    public function showNTSettings($ii, $options, $isNew=false) { $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code']; $isFin = $this->checkIfSetupFinished($options); ?> 
      <div id="do<?php echo $this->ntInfo['code'].$ii; ?>Div" class="insOneDiv<?php if ($isNew) echo " clNewNTSets"; ?>">   <input type="hidden" name="apDoS<?php echo $this->ntInfo['code'].$ii; ?>" value="0" id="apDoS<?php echo $this->ntInfo['code'].$ii; ?>" />
        <?php if ($isNew) { ?>    <input type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][do]" value="1" id="apDoNew<?php echo $this->ntInfo['code'].$ii; ?>" /> <?php } ?>
        <div class="nsx_iconedTitle" style="float: right; max-width: 392px; text-align: right; background-image: url(<?php echo NXS_PLURL; ?>img/<?php echo (!empty($this->ntInfo['imgcode']))?$this->ntInfo['imgcode']:$nt; ?>16.png);"><a style="font-size: <?php echo !$isFin?'13':'12'; ?>px;" target="_blank"  href="<?php echo $this->ntInfo['instrURL'];?>"><?php  printf( __( 'Detailed %s Installation/Configuration Instructions', 'social-networks-auto-poster-facebook-twitter-g' ), $this->ntInfo['name']); ?></a>
        </div><?php if (!$isFin) { ?><div style="float: right;" ><img src="<?php echo NXS_PLURL; ?>img/arrow_r_green_c1.png" /></div><?php } ?>    
        <?php if (empty($options['nName'])) $options['nName'] = $this->makeUName($options, $ii); ?>    
        <div style="width:100%;"><strong><?php _e('Account Nickname', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> <i><?php _e('Just so you can easily identify it', 'social-networks-auto-poster-facebook-twitter-g'); ?></i> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][nName]" id="<?php echo $nt; ?>nName<?php echo $ii; ?>" style="font-weight: bold; color: #005800; border: 1px solid #ACACAC; width: 40%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['nName'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>        
        <ul class="nsx_tabs">
          <li><a href="#nsx<?php echo $nt.$ii ?>_tab1"><?php _e('Account Info', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></li>    
          <?php if (!$isNew) { ?>  <li><a id="nsx<?php echo $nt.$ii ?>_tabAdv" href="#nsx<?php echo $nt.$ii ?>_tab2"><?php _e('Advanced', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></li>  <?php } ?>        
        </ul>
        <div class="nsx_tab_container"><?php /* ######################## Account Tab ####################### */ ?>
          <div id="nsx<?php echo $nt.$ii ?>_tab1" class="nsx_tab_content" style="background-image: url(<?php echo NXS_PLURL; ?>img/<?php echo (!empty($this->ntInfo['imgcode']))?$this->ntInfo['imgcode']:$nt; ?>-bg.png); background-repeat: no-repeat;  background-position:90% 10%;">
            <?php $this->accTab($ii, $options, $isNew); ?>
            <?php if ($isNew) { ?> <input type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][do]" value="1" /> <?php } ?>
            <?php if ($isFin) { ?>  <b><?php _e('Test your settings', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</b>&nbsp;&nbsp;&nbsp; <a href="#" class="NXSButton" onclick="testPost('<?php echo $this->ntInfo['code']; ?>', '<?php echo $ii; ?>'); return false;"><?php printf( __( 'Submit Test Post to %s', 'social-networks-auto-poster-facebook-twitter-g' ), $this->ntInfo['name']); ?></a><?php } ?>
          </div> 
          <?php /* ######################## Advanced Tab ####################### */ ?>
          <?php if (!$isNew && function_exists('_make_url_clickable_cb') ) { ?>   
            <div id="nsx<?php echo $nt.$ii ?>_tab2" class="nsx_tab_content"> 
              <?php nxs_showNTFilters($nt, $ii, $options); nxs_showImgSizeChoice($nt, $ii, !empty($options['wpImgSize'])?$options['wpImgSize']:'full'); nxs_addPostingDelaySelV4($nt, $ii, $options); $this->advTab($ii, $options);               
                $nr = array('di','dl','rd','su','mc','yo'); if (!in_array($nt, $nr)) nxs_showRepostSettings($nt, $ii, $options); 
              ?>
            </div> <?php } ?> <?php /* #### End of Tab #### */ ?>
        </div><br/> <?php /* #### End of Tabs #### */ ?>
        
        
        <div class="submitX nxclear" style="padding-bottom: 0px;">
          <input type="button" id="svBtn<?php echo $nt.$ii ?>" onclick="nxs_svSetAdv('<?php echo $nt; ?>', '<?php echo $ii; ?>', '<?php echo $isNew?'dom'.$ntU.$ii.'Div':'nxsAllAccntsDiv'; ?>','nxs<?php echo $ntU; ?>MsgDiv<?php echo $ii; ?>','<?php echo $isNew?'r':''; ?>','1'); return false;" class="button-primary" value="<?php echo $isNew?__('Add Account', 'nxs_snap'):__('Update Account Info', 'nxs_snap'); ?>" />            
          <div id="nxsSaveLoadingImg<?php echo $nt.$ii; ?>" class="doneMsg">Saving.....</div> <div id="doneMsg<?php echo $nt.$ii; ?>" class="doneMsg">Done</div>
          <?php if ($isNew) {  ?><input style="float: right;" type="button" onclick="jQuery('#nxs_spPopup').bPopup().close();" class="button-primary" value="<?php _e('Close', 'social-networks-auto-poster-facebook-twitter-g') ?>" /><?php }  ?>
          <?php global $nxs_apiLInfo; if (isset($nxs_apiLInfo) && !empty($nxs_apiLInfo) && !empty($this->ntInfo['l']) && !empty($nxs_apiLInfo[$this->ntInfo['l']])) { ?>
            <div style="float: right; display: block; clear: both; font-size: 10px; position: relative; bottom: -10px;">NextScripts <?php echo $this->ntInfo['name'].' '.$nxs_apiLInfo[$this->ntInfo['l']];?></div>
          <?php } ?>
        </div>    
                    
      </div><?php         
    }
    //## Advanced Blocks
    function showProxies($nt, $ii, $options){ if (empty($options['proxy'])) $options['proxy'] = array('proxy'=>'','up'=>'');
        ?> <div class="nxs_tls_cpt"><?php  _e('Proxy', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;<span class="nxsInstrSpan"><a href="http://www.nextscripts.com/snap-features/proxy" target="_blank"><?php _e('[Instructions]', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></span></div><h3 style="padding-left: 15px; font-size: 16px;"> 
     <input type="checkbox" onchange="if (jQuery(this).is(':checked')) jQuery('#nxs_proxy<?php echo $nt.$ii; ?>').show(); else jQuery('#nxs_proxy<?php echo $nt.$ii; ?>').hide();" class="nxs_acctcb" <?php if (!empty($options['proxyOn'])) echo "checked"; ?>  name="<?php echo $nt; ?>[<?php echo $ii; ?>][proxyOn]" value="1" /> 
     <?php  _e('Use Proxy', 'social-networks-auto-poster-facebook-twitter-g'); ?> </h3><div id="nxs_proxy<?php echo $nt.$ii; ?>" style="margin-left: 30px;<?php if (empty($options['proxyOn'])) echo "display:none;"; ?>"> 
     
   <div style="width:100%;"><strong><?php _e('IP:Port', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][proxy]" style="width: 30%;" value="<?php echo htmlentities($options['proxy']['proxy'], ENT_COMPAT, "UTF-8"); ?>"/>
   <div style="width:100%;"><strong><?php _e('Username:Password', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][proxyup]" style="width: 30%;" value="<?php echo htmlentities($options['proxy']['up'], ENT_COMPAT, "UTF-8"); ?>"/>
      <br/><hr/>
     
      </div> <?php
    }
    //## Elements
    public function elemUserPass($ii, $u, $p, $t='',$onchange='') { $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code']; ?>
      <div style="width:100%;"><strong><?php echo $this->ntInfo['name']; ?>&nbsp;<?php _e('Login', 'social-networks-auto-poster-facebook-twitter-g'); if ($t=='e') { echo " "; _e('Email', 'social-networks-auto-poster-facebook-twitter-g'); } ?>:</strong> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][uName]" id="ap<?php echo $ntU; ?>UName<?php echo $ii; ?>" style="width: 30%;" value="<?php echo htmlentities($u, ENT_COMPAT, "UTF-8"); ?>"  onchange="if (jQuery(this).val()!='' && jQuery('#ap<?php echo $ntU; ?>Pass<?php echo $ii; ?>').val()!=''){jQuery('#<?php echo $nt.$ii; ?>getPgs').val(1);nxs_svSetAdv('<?php echo $nt; ?>', '<?php echo $ii; ?>','nxsAllAccntsDiv','<?php echo $nt.$ii; ?>pgsList','','');} return false;"/>
      <div style="width:100%;"><strong><?php echo $this->ntInfo['name']; ?>&nbsp;<?php _e('Password', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> </div><input autocomplete="false" readonly onfocus="this.removeAttribute('readonly');"  name="<?php echo $nt; ?>[<?php echo $ii; ?>][uPass]" id="ap<?php echo $ntU; ?>Pass<?php echo $ii; ?>" type="password" style="width: 30%;" value="<?php echo htmlentities((substr($p, 0, 5)=='n5g9a'||substr($p, 0, 5)=='g9c1a'||substr($p, 0, 5)=='b4d7s')?nsx_doDecode(substr($p, 5)):$p, ENT_COMPAT, "UTF-8"); ?>" <?php echo !empty($onchange)?'onchange="'.$onchange.'"':''; ?> /><br/><?php
    }
    public function elemKeySecret($ii,$lKey,$lSec,$key,$sec,$fnKey='appKey',$fnSec='appSec', $aurl='') { $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code']; $aurl = !empty($aurl)?' (<a style="font-size: 12px;" href="'.$aurl.'" target="_blank">'.$aurl.'</a>)':''; ?>
      <div style="width:100%;"><b style="font-size: 14px;"><?php echo $lKey; echo $aurl; ?> </b></div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][<?php echo $fnKey; ?>]" id="<?php echo $nt.$fnKey.$ii; ?>" style="width: 30%;" value="<?php echo htmlentities($key, ENT_COMPAT, "UTF-8"); ?>" />  
      <div style="width:100%;"><b style="font-size: 14px;"><?php echo $lSec; ?>:</b></div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][<?php echo $fnSec; ?>]" id="<?php echo $fnSec.$ii; ?>" style="width: 30%;" value="<?php echo htmlentities($sec, ENT_COMPAT, "UTF-8"); ?>" /><?php                
    }
    public function elemURL($ii,$fn,$val,$lbl,$subLbl) { $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];?>
      <div style="width:100%;"><strong><?php echo $lbl; ?>:</strong><i><?php  echo $subLbl; ?></i></div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][<?php echo $fn; ?>]" style="width: 50%;" value="<?php echo htmlentities($val, ENT_COMPAT, "UTF-8"); ?>" /><br/><?php
    }
    public function elemMsgFormat($ii,$l,$fn,$val,$isVisible=true) { $nt = $this->ntInfo['lcode']; ?>
      <div class="nxsMsgFormatDiv" style="display:<?php echo ($isVisible)?"block":"none"; ?>;"> 
        <div style="width:100%;"><b style="font-size: 15px;"><?php echo $l; ?>:</b> (<a href="#" id="msgFrmt<?php echo $nt.$ii; ?>HintInfo" onclick="nxs_showHideFrmtInfo('<?php echo $fn.$nt.$ii; ?>'); return false;"><?php _e('Show format info', 'nxs_snap'); ?></a>)</div>
        <textarea cols="150" rows="3" id="nxsF<?php echo $fn.$nt.$ii; ?>" name="<?php echo $nt; ?>[<?php echo $ii; ?>][<?php echo $fn; ?>]"  style="width:51%;max-width: 610px;" onfocus="jQuery('#nxsF<?php echo $fn.$nt.$ii; ?>').attr('rows', 6); nxs_showFrmtInfo('<?php echo $fn.$nt.$ii; ?>');"><?php echo htmlentities($val, ENT_COMPAT, "UTF-8"); ?></textarea> <?php nxs_doShowHint($fn.$nt.$ii); ?><br/>
      </div><?php                        
    }
    public function elemTitleFormat($ii,$l,$fn,$val,$isVisible=true) { $nt = $this->ntInfo['lcode']; ?> 
      <div class="nxsMsgTFormatDiv" style="display:<?php echo ($isVisible)?"block":"none"; ?>;">
        <div style="width:100%;"><b style="font-size: 15px;"><?php echo $l; ?>:</b> </div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][<?php echo $fn; ?>]" id="<?php echo $nt.$ii; ?>SNAPformatT" style="width: 50%;" value="<?php echo htmlentities($val, ENT_COMPAT, "UTF-8"); ?>" onfocus="nxs_showFrmtInfo('msgFrmtT<?php echo $nt.$ii; ?>');" /><?php nxs_doShowHint("msgFrmtT".$nt.$ii); ?>
      </div><?php                        
    }
    
    //## Edit post Elements
    public function elemEdTitleFormat($ii,$l,$msgTFormat) { $nt = $this->ntInfo['lcode']; ?>
      <tr><th scope="row" class="nxsTHRow"><?php echo $l; ?></th><td>                
          <input name="<?php echo $nt; ?>[<?php echo $ii; ?>][msgTFormat]" id="<?php echo $nt.$ii; ?>SNAPformatT" style="width: 50%;" value="<?php echo htmlentities($msgTFormat, ENT_COMPAT, "UTF-8"); ?>" onfocus="nxs_showFrmtInfo('msgFrmtT<?php echo $nt.$ii; ?>');" /><?php nxs_doShowHint("msgFrmtT".$nt.$ii); ?></td>
        </tr><?php                        
    }
    public function elemEdMsgFormat($ii,$l,$msgFormat) { $nt = $this->ntInfo['lcode']; ?>
      <tr><th scope="row" class="nxsTHRow"><?php echo $l; ?></th><td>                
          <textarea cols="150" rows="2" id="<?php echo $nt.$ii; ?>msgFormat" name="<?php echo $nt; ?>[<?php echo $ii; ?>][msgFormat]"  style="width:60%;max-width: 610px;" onfocus="jQuery('#<?php echo $nt.$ii; ?>msgFormat').attr('rows', 4); jQuery('.nxs_FRMTHint').hide();nxs_showFrmtInfo('msgFormat<?php echo $nt.$ii; ?>');"><?php echo $msgFormat; ?></textarea> <?php nxs_doShowHint("msgFormat".$nt.$ii); ?></td>
        </tr><?php                        
    }
    
    public function saveCommonNTSettings($pval, $o) { if (isset($pval['do'])) $o['do'] = $pval['do']; else $o['do'] = 0;  if (isset($pval['nName'])) $o['nName'] = trim($pval['nName']); if (isset($pval['qTLng'])) $o['qTLng'] = trim($pval['qTLng']);
      if (isset($pval['delayDays'])) $o['nDays'] = trim($pval['delayDays']);  if (isset($pval['delayHrs']))  $o['nHrs'] = trim($pval['delayHrs']); if (isset($pval['delayMin'])) $o['nMin'] = trim($pval['delayMin']); 
      
      //$o['do'.$this->ntInfo['code']] = $o['do']; //V3 COMP CHANGE
            
      $o = nxs_adjRpst($o, $pval);       
      
      //## Common Items (Possible)
      if (isset($pval['uName']))   $o['uName'] = trim($pval['uName']); else if (isset($o['uName'])) unset($o['uName']);
      if (!empty($pval['uPass']))    $o['uPass'] = 'g9c1a'.nsx_doEncode($pval['uPass']); else if (isset($o['uPass'])) unset($o['uPass']);
      if (isset($pval['msgFormat'])) $o['msgFormat'] = trim($pval['msgFormat']); else if (isset($o['msgFormat'])) unset($o['msgFormat']);
      if (isset($pval['msgTFormat'])) $o['msgTFormat'] = trim($pval['msgTFormat']); else if (isset($o['msgTFormat'])) unset($o['msgTFormat']);
      if (isset($pval['msgAFormat'])) $o['msgAFormat'] = trim($pval['msgAFormat']); else if (isset($o['msgAFormat'])) unset($o['msgAFormat']);
      if (isset($pval['msgATFormat'])) $o['msgATFormat'] = trim($pval['msgATFormat']); else if (isset($o['msgATFormat'])) unset($o['msgATFormat']);
      if (isset($pval['appKey'])) $o['appKey'] = trim($pval['appKey']); else if (isset($o['appKey'])) unset($o['appKey']);
      if (isset($pval['appSec'])) $o['appSec'] = trim($pval['appSec']); else if (isset($o['appSec'])) unset($o['appSec']);
      if (isset($pval['postType'])) $o['postType'] = $pval['postType'];
      if (isset($pval['apiKey'])) $o['apiKey'] = trim($pval['apiKey']); else if (isset($o['apiKey'])) unset($o['apiKey']);
      if (isset($pval['inclTags'])) $o['inclTags'] = trim($pval['inclTags']); else if (isset($o['inclTags'])) $o['inclTags'] = 0;
      if (isset($pval['inclCats'])) $o['inclCats'] = trim($pval['inclCats']); else if (isset($o['inclCats'])) $o['inclCats'] = 0;  
      //## Filters
      if (isset($pval['fltrsOn'])) $o['fltrsOn'] = trim($pval['fltrsOn']); else $o['fltrsOn'] = 0;  
      if (isset($pval['fltrAfter'])) $o['fltrAfter'] = trim($pval['fltrAfter']); else if (isset($o['fltrAfter'])) unset($o['fltrAfter']); $o['fltrs'] = array(); 
      //## Proxy
      if (isset($pval['proxyOn'])) $o['proxyOn'] = trim($pval['proxyOn']); else $o['proxyOn'] = 0;  //prr($o);
      if (isset($pval['proxy']))   $o['proxy']['proxy'] = trim($pval['proxy']); 
      if (isset($pval['proxyup'])) $o['proxy']['up'] = trim($pval['proxyup']);
      //## Image Selection      
      if (isset($pval['wpImgSize'])) $o['wpImgSize'] = trim($pval['wpImgSize']); 
      //##
      if (!empty($pval['nxs_ie_tags_names'])) $o['fltrs']['nxs_ie_tags_names'] = $pval['nxs_ie_tags_names'];
      if (isset($pval['nxs_tags_names'])) { foreach ($pval['nxs_tags_names'] as $jj=>$tag) { $exT=''; if (is_numeric($tag)) $exT = term_exists((int)$tag, 'post_tag'); else $exT = term_exists($tag, 'post_tag'); 
          if (empty($exT)) $exT = wp_insert_term($tag, 'post_tag'); $pval['nxs_tags_names'][$jj]= $exT['term_id'];
        } $o['fltrs']['nxs_tags_names'] = $pval['nxs_tags_names'];
      }      
      if (!empty($pval['nxs_ie_cats_names'])) $o['fltrs']['nxs_ie_cats_names'] = $pval['nxs_ie_cats_names'];      
      if (isset($pval['nxs_cats_names'])) { foreach ($pval['nxs_cats_names'] as $jj=>$tag) { $exT=''; if (is_numeric($tag)) $exT = term_exists((int)$tag, 'category'); else $exT = term_exists($tag, 'category');  
          if (empty($exT)) $exT = wp_insert_term($tag, 'category');  $pval['nxs_cats_names'][$jj]= $exT['term_id'];
        } $o['fltrs']['nxs_cats_names'] = $pval['nxs_cats_names'];
      }      
      if (isset($pval['nxs_post_status'])) $o['fltrs']['nxs_post_status'] = $pval['nxs_post_status']; 
      if (!empty($pval['nxs_ie_posttypes'])) $o['fltrs']['nxs_ie_posttypes'] = $pval['nxs_ie_posttypes'];
      if (isset($pval['nxs_post_type'])) $o['fltrs']['nxs_post_type'] = $pval['nxs_post_type'];
      if (isset($pval['nxs_post_formats'])) $o['fltrs']['nxs_post_formats'] = $pval['nxs_post_formats'];
      if (isset($pval['nxs_user_names'])) $o['fltrs']['nxs_user_names'] = $pval['nxs_user_names'];
      if (!empty($pval['nxs_search_keywords'])) $o['fltrs']['nxs_search_keywords'] = $pval['nxs_search_keywords'];
      
      //prr($pval['nxs_count_term_compares']);
      //## Meta
      if (!empty($pval['nxs_count_meta_compares'])) $o['fltrs']['nxs_count_meta_compares'] = $pval['nxs_count_meta_compares'];      
      if (!empty($pval['nxs_meta_key'])) {       
        $o['fltrs']['nxs_meta_operator'] = (isset($pval['nxs_meta_operator']))?$pval['nxs_meta_operator']:'';
        $o['fltrs']['nxs_meta_key'] = (isset($pval['nxs_meta_key']))?$pval['nxs_meta_key']:'';
        $o['fltrs']['nxs_meta_value'] = (isset($pval['nxs_meta_value']))?$pval['nxs_meta_value']:'';
        $o['fltrs']['nxs_meta_relation'] = (isset($pval['nxs_meta_relation']))?$pval['nxs_meta_relation']:''; 
      } //prr($pval['nxs_count_term_compares']);
      if (!empty($pval['nxs_count_meta_compares']) && (int)$pval['nxs_count_meta_compares']>1) for( $jj = 2; $jj <= $pval['nxs_count_meta_compares']; $jj++ ) { if (!empty($pval['nxs_meta_key_'.$jj])){ 
          $o['fltrs']['nxs_meta_operator_'.$jj] = (isset($pval['nxs_meta_operator_'.$jj]))?$pval['nxs_meta_operator_'.$jj]:'';
          $o['fltrs']['nxs_meta_key_'.$jj] = (isset($pval['nxs_meta_key_'.$jj]))?$pval['nxs_meta_key_'.$jj]:'';
          $o['fltrs']['nxs_meta_value_'.$jj] = (isset($pval['nxs_meta_value_'.$jj]))?$pval['nxs_meta_value_'.$jj]:'';         
          $o['fltrs']['nxs_meta_relation_'.$jj] = (isset($pval['nxs_meta_relation_'.$jj]))?$pval['nxs_meta_relation_'.$jj]:''; 
        }
      }
      
      //## Taxonomies
      if (!empty($pval['nxs_count_term_compares'])) $o['fltrs']['nxs_count_term_compares'] = $pval['nxs_count_term_compares'];      
      if (!empty($pval['nxs_term_names'])) {
        $o['fltrs']['nxs_tax_names'] = (isset($pval['nxs_tax_names']))?$pval['nxs_tax_names']:'';  $o['fltrs']['nxs_term_names'] = (isset($pval['nxs_term_names']))?$pval['nxs_term_names']:'';
        $o['fltrs']['nxs_term_operator'] = (isset($pval['nxs_term_operator']))?$pval['nxs_term_operator']:'';
        $o['fltrs']['nxs_term_children'] = (isset($pval['nxs_term_children']))?$pval['nxs_term_children']:'';
        $o['fltrs']['nxs_term_relation'] = (isset($pval['nxs_term_relation']))?$pval['nxs_term_relation']:'';
      } //prr($pval['nxs_count_term_compares']);
      if (!empty($pval['nxs_count_term_compares']) && (int)$pval['nxs_count_term_compares']>1) for( $jj = 2; $jj <= $pval['nxs_count_term_compares']; $jj++ ) { if (!empty($pval['nxs_term_names_'.$jj])){ 
          $o['fltrs']['nxs_tax_names_'.$jj] = (isset($pval['nxs_tax_names_'.$jj]))?$pval['nxs_tax_names_'.$jj]:''; $o['fltrs']['nxs_term_names_'.$jj] = (isset($pval['nxs_term_names_'.$jj]))?$pval['nxs_term_names_'.$jj]:'';
          $o['fltrs']['nxs_term_operator_'.$jj] = (isset($pval['nxs_term_operator_'.$jj]))?$pval['nxs_term_operator_'.$jj]:'';
          $o['fltrs']['nxs_term_children_'.$jj] = (isset($pval['nxs_term_children_'.$jj]))?$pval['nxs_term_children_'.$jj]:'';
          $o['fltrs']['nxs_term_relation_'.$jj] = (isset($pval['nxs_term_relation_'.$jj]))?$pval['nxs_term_relation_'.$jj]:'';
        }
      } $o['v'] = NXS_SETV;     return $o;                  
    }
    
    public function saveNTSettings() {
                        
    }
    
    public function showEditPostNTSettings() {
                        
    }
    
    function nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta){ $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code']; $ntName = $this->ntInfo['name']; $ii = $ntOpt['ii']; $doNT = $ntOpt['do']; if(!$this->checkIfFunc()) return; ?>
      <tr <?php if (!empty($ntOpt['fltrsOn']) && $ntOpt['fltrsOn']=='1') { ?> onmouseout="hidePopShAtt('FLT');" onmouseover="showPopShAtt('FLT', event);" <?php } ?>><th style="text-align:left;" colspan="2"> <?php 
      if ($post->post_status != "publish" && ((empty($pMeta) && !empty($ntOpt['fltrsOn']) && $ntOpt['fltrsOn']=='1')||($ntOpt['do']=='2'))){  $fltInfo = nxsAnalyzePostFilters($ntOpt['fltrs']); ?>
        <input type="radio" id="rbtn<?php echo $ntU.$ii; ?>" value="2" name="<?php echo $nt; ?>[<?php echo $ii; ?>][do]" checked="checked" class="nxsGrpDoChb" data-fltinfo="<?php echo $fltInfo; ?>"/> <?php } 
      else { ?>
         <input class="nxsGrpDoChb" value="1" id="do<?php echo $ntU.$ii; ?>" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][do]" <?php if ((int)$doNT == 1) echo 'checked="checked" title="def"';  ?> /> 
      <?php } ?>
        
        
      <div class="nsx_iconedTitle" id="ldo<?php echo $ntU.$ii; ?>" style="display: inline; font-size: 13px; background-image: url(<?php echo NXS_PLURL; ?>img/<?php echo (!empty($this->ntInfo['imgcode']))?$this->ntInfo['imgcode']:$nt;; ?>16.png);"><?php echo $ntName; ?> (<i style="color: #005800;"><?php echo $ntOpt['nName']; ?></i>)&nbsp;<span class="nxs_ldos" id="bldo<?php echo $ntU.$ii; ?>"><?php echo !empty($ntOpt['do'])?'[-]':'[+]'; ?></span>
      </div></th><td><?php //## Only show RePost button if the post is "published"
      if ($post->post_status == "publish") { ?>
        <input alt="<?php echo $ii; ?>" style="float: right;" onmouseout="hidePopShAtt('SV');" onmouseover="showPopShAtt('SV', event);" onclick="return false;" data-ntname="<?php echo $ntName; ?>" type="button" class="button manualPostBtn" name="<?php echo $nt."-".$post->ID; ?>" value="<?php _e('Post to ', 'social-networks-auto-poster-facebook-twitter-g'); echo $ntName; ?>" />
    
      <?php if (!empty($ntOpt['riComments']) && $ntOpt['riComments']=='1' && is_array($pMeta) && isset($pMeta[$ii]) && is_array($pMeta[$ii]) && !empty($pMeta[$ii]['pgID'])) { ?>
      <input style="float: right; " onclick="return false;" type="button" data-ii="<?php echo $ii; ?>" data-pid="<?php echo $post->ID; ?>" data-nt="<?php echo $nt; ?>" class="button riTo_button" value="<?php _e('Import Comments/Replies', 'nxs_snap') ?>" />
      <?php } ?>
        <?php  if (is_array($pMeta) && isset($pMeta[$ii]) && is_array($pMeta[$ii]) && !empty($pMeta[$ii]['pgID'])) { ?> <span style="float: right;padding-top: 4px; padding-right: 10px;">
          <a id="pstd<?php echo $ntU; ?><?php echo $ii; ?>" style="font-size: 10px;" href="<?php echo $pMeta[$ii]['postURL'];  ?>" target="_blank"><?php printf( __( 'Posted on', 'nxs_snap' ), $ntName); ?>  <?php echo (isset($pMeta[$ii]['pDate']) && $pMeta[$ii]['pDate']!='')?(nxs_adjTime($pMeta[$ii]['pDate'])):""; ?></a>
        </span><?php } } ?>
      </td></tr> <?php     
      if ($post->post_status != "publish" && function_exists('nxs_doSMAS5') ) { $ntOpt['postTime'] = get_post_time('U', false, $post->ID); nxs_doSMAS5($nt, $ii, $ntOpt); }
      
      if (((int)$ntOpt['do'] == 1) && $post->post_status == "publish" && isset($ntOpt['timeToRun']) && $ntOpt['timeToRun'] > time()) { ?> <tr><th style="text-align:left; color: purple;" colspan="2">
                ===&gt;&gt;&gt;&gt;&nbsp;<?php _e('Autopost has been schedulled for', 'social-networks-auto-poster-facebook-twitter-g') ?> <?php echo date('F j, Y, g:i a', $ntOpt['timeToRun']) ?></th> 
      <?php } ?><tbody style="<?php echo !empty($ntOpt['do'])?'display:table-row-group;':'display:none;'; ?>" class="nxstbldo <?php echo 'nxstbldo'.strtoupper($nt).$ii; ?>" ><?php        
    }
    public function nxs_tmpltAddPostMetaEnd($ii){ $ntU = $this->ntInfo['code'];
      ?></tbody><tr class="nxstbldo <?php echo 'nxstbldo'.$ntU.$ii; ?>"><td colspan="2" style="padding:5px;border-bottom:1px solid #F0F0F0;"></td></tr><?php
    }
    
    public function adjMetaOptG($optMt, $pMeta) { $optMt['isPosted'] = isset($pMeta['isPosted'])?$pMeta['isPosted']:''; if (isset($pMeta['postType'])) $optMt['postType'] = $pMeta['postType']; 
      if (isset($pMeta['msgFormat'])) $optMt['msgFormat'] = $pMeta['msgFormat']; if (isset($pMeta['msgTFormat'])) $optMt['msgTFormat'] = $pMeta['msgTFormat'];     
      if (isset($pMeta['imgToUse'])) $optMt['imgToUse'] = $pMeta['imgToUse']; if (isset($pMeta['urlToUse'])) $optMt['urlToUse'] = $pMeta['urlToUse']; 
      if (isset($pMeta['postType'])) $optMt['postType'] = $pMeta['postType']; 
      if (isset($pMeta['timeToRun']))  $optMt['timeToRun'] = $pMeta['timeToRun']; $optMt['do'] = 0;  
      if (isset($pMeta['do'])) $optMt['do'] = $pMeta['do']; else { if (isset($pMeta['msgFormat'])) $optMt['do'] = 0; }  // What is that?
      if (isset($optMt['do'.$this->ntInfo['code']])) unset($optMt['do'.$this->ntInfo['code']]); //prr($optMt); die();
      return $optMt;                    
    } 
    
    public function adjMetaOpt($optMt, $pMeta) { return $this->adjMetaOptG($optMt, $pMeta); }
    
    public function ajaxPost() { check_ajax_referer('nxsSsPageWPN');  $postID = $_POST['id'];  $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code']; $ntName = $this->ntInfo['name']; 
    
    if (!current_user_can( 'manage_options' ) && current_user_can( 'haveown_snap_accss' ) ) { if (empty($nxs_SNAP)) $nxs_SNAP = new nxs_SNAP();   $options = $nxs_SNAP->nxs_acctsU; } else  $options = get_option('NS_SNAutoPoster');  
      foreach ($options[$nt] as $ii=>$nto) if ($ii==$_POST['nid']) {  $nto['ii'] = $ii; $nto['pType'] = 'aj';  $po =  get_post_meta($postID, 'snap'.$ntU, true); $po =  maybe_unserialize($po); $clName = 'nxs_snapClass'.$ntU; $ntClInst = new $clName();
        if (is_array($po) && isset($po[$ii]) && is_array($po[$ii])){ $nto = $ntClInst->adjMetaOpt($nto, $po[$ii]); } 
        $result = $this->publish($postID, $nto); if ($result == '200') die("Your post has been successfully sent to ".$ntName); else die($result);        
      }
    }
    
    public function publish($postID, $nto) { $fnName = 'nxs_doPublishTo'.$this->ntInfo['code']; return $fnName($postID, $nto); }
    
    function adjPreFormatWP(&$options, $postID){}
    
    function publishWP($ii, $postID=0){ $options = $this->nt[$ii]; $extInfo =''; $addParams = nxs_makeURLParams(array('NTNAME'=>$this->ntInfo['name'], 'NTCODE'=>$this->ntInfo['code'], 'POSTID'=>$postID, 'ACCNAME'=>$options['nName'])); $uid = 0;
      if ($postID!='0') { $post = get_post($postID); $postUser = $post->post_author; $uid = 0; if (!user_can($postUser, 'manage_options' ) && user_can($postUser, 'haveown_snap_accss'))  $uid = $postUser; }
      $blogTitle = htmlspecialchars_decode(get_bloginfo('name'), ENT_QUOTES); if ($blogTitle=='') $blogTitle = home_url(); if (!isset($options['imgToUse'])) $options['imgToUse'] = ''; if (!isset($options['imgSize'])) $options['imgSize'] = '';
      $logNT = '<span style="color:#FA5069">'.$this->ntInfo['name'].'</span> - '.$options['nName']; $snap_ap = get_post_meta($postID, 'snap'.$this->ntInfo['code'], true); $snap_ap = maybe_unserialize($snap_ap);
      if (!isset($options['pType'])) $options['pType'] = 'im'; if ($options['pType']=='sh') sleep(rand(1, 10)); //## Sho eto?
      if ($options['pType']!='aj' && is_array($snap_ap) && (nxs_chArrVar($snap_ap[$ii], 'isPosted', '1') || nxs_chArrVar($snap_ap[$ii], 'isPrePosted', '1'))) { //## Check this!!!!
        $snap_isAutoPosted = get_post_meta($postID, 'snap_isAutoPosted', true); if ($snap_isAutoPosted!='2') {  
         nxs_LogIt('W', 'Notice', $logNT, $this->ntInfo['code'], '-=Duplicate=- Post ID:'.$postID, 'Already posted. No reason for posting duplicate |'.$uqID,'snap',$uid); return;
        }
      }    
      $isNoImg = false; $tagsA = array(); $tags = ''; $catsA = array(); $cats = '';//## Fix this with defaults
      if ($postID=='0') { echo "Testing ... <br/><br/>"; $urlToGo = home_url(); $options['msgFormat'] = 'Test Post from '.$blogTitle."\r\n".$urlToGo; $options['msgTFormat'] = 'Test Post from '.$blogTitle;
        if (!empty($options['defImg'])) $imgURL = $options['defImg']; else $imgURL ="http://direct.gtln.us/img/nxs/NXS-Lama.jpg"; $nxs_urlLen = 0;
      } else { $post = get_post($postID); if(empty($post)) {nxs_LogIt('E', 'Error', $logNT, $this->ntInfo['code'],'No Post','','snap',$uid);} if (!isset($options['defImg'])) $options['defImg'] = ''; global $nxs_urlLen; $nxs_urlLen = 0;
        $this->adjPreFormatWP($options, $postID);
        if (!empty($options['msgFormat']))$options['msgFormat'] = nsFormatMessage( $options['msgFormat'], $postID, $addParams, '', $options); if (!empty($options['msgTFormat'])) $options['msgTFormat'] = nsFormatMessage( $options['msgTFormat'], $postID, $addParams);
        //## MyURL - URLToGo code
        $options = nxs_getURL($options, $postID, $addParams); $urlToGo = $options['urlToUse']; if (is_object($post)) $urlToGo = apply_filters( 'nxs_adjust_ex_url', $urlToGo, $post->post_content);      
        if (!empty($options['imgToUse'])) $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'full', $options['defImg']); if (preg_match("/noImg.\.png/i", $imgURL)) { $imgURL = ''; $isNoImg = true; }
        if (!empty($options['inclTags']) && $options['inclTags']=='1'){$t = wp_get_post_tags($postID); foreach ($t as $tagA) {$tagsA[] = $tagA->name;} $tags = implode(',',$tagsA); }
        if (!empty($options['inclCats']) && $options['inclCats']=='1'){$postCats = wp_get_post_categories($postID); $cats = array();  foreach($postCats as $c){ $cat = get_category($c); $catsA[] = str_ireplace('&','&amp;',$cat->name); $cats = implode(',',$catsA); }} 
        
        if (!empty($options['attchAsVid']) && $options['attchAsVid']=='A') { $vids = nsFindVidsInPost($post); if (count($vids)>0) {        
          if (strlen($vids[0])==11) { $vidURL = 'http://www.youtube.com/watch?v='.$vids[0]; $imgVURL = 'http://img.youtube.com/vi/'.$vids[0].'/maxresdefault.jpg'; } 
          if (strlen($vids[0])==8) { $vidURL = 'https://secure.vimeo.com/moogaloop.swf?clip_id='.$vids[0].'&autoplay=1';
            //$mssg['source'] = 'http://player.vimeo.com/video/'.$vids[0]; 
            $apiURL = "http://vimeo.com/api/v2/video/".$vids[0].".json?callback=showThumb"; $json = nxs_remote_get($apiURL);
            if (!is_nxs_error($json)) { $json = $json['body']; $json = str_replace('showThumb(','',$json); $json = str_replace('])',']',$json);  $json = json_decode($json, true); $imgVURL = $json[0]['thumbnail_large']; }           
          }
         } if (trim($imgVURL)!='') $imgURL = $imgVURL; 
        }        
        $extInfo = ' | PostID: '.$postID." - ".(is_object($post))?$post->post_title:'';               
      } $message = array('siteName'=>$blogTitle, 'tags'=>$tags, 'tagsA'=>$tagsA, 'cats'=>$cats, 'catsA'=>$catsA, 'url'=>$urlToGo, 'imageURL'=>$imgURL, 'videoURL'=>'', 'urlLength'=>$nxs_urlLen, 'noImg'=>$isNoImg, 'message'=>'', 'urlTitle'=>'', 'urlDescr'=>''); 
      //## Post
      //## Adjust Per network
      $this->adjPublishWP($options, $message, $postID); //prr($options); prr($message); die();
      //## Actual Post
      $clName = 'nxs_class_SNAP_'.$this->ntInfo['code']; $ntToPost = new $clName(); $ret = $ntToPost->doPostToNT($options, $message);        
      //## Process Results
      if (!is_array($ret) || empty($ret['isPosted']) || $ret['isPosted']!='1') { //## Error 
         if ($postID=='0') prr($ret); nxs_LogIt('E', 'Error', $logNT, $this->ntInfo['code'],'-=ERROR=- '.print_r($ret, true), $extInfo,'snap',$uid); 
      } else {  // ## All Good - log it.
        if (!empty($ret['msg'])) nxs_LogIt('I', 'Message', $logNT,$this->ntInfo['code'], print_r($ret['msg'], true), $extInfo,'snap',$uid); 
        if (!empty($_POST['nxsact'])&&($_POST['nxsact']=='manPost' || $_POST['nxsact']=='testPost')) 
          { _e('SUCCESS','nxs_snap'); echo '<br/><br/>'.$logNT.' Page.<br/>'.((!empty($ret['postURL']))?' Post link: <a href="'.$ret['postURL'].'" target="_blank">'.$ret['postURL'].'</a><br/><br/>':'').(!empty($ret['msg'])?print_r($ret['msg'], true):'').'<br/>'; }
        if (!empty($ret['ck'])) nxs_save_glbNtwrks($this->ntInfo['lcode'], $ii, $ret['ck'], 'ck');
        if ($postID=='0')  { nxs_LogIt('S', 'Test', $logNT, $this->ntInfo['code'],'OK - TEST Message Posted | <a href="'.$ret['postURL'].'" target="_blank">Post Link</a>','','snap',$uid);} 
          else { nxs_addToRI($postID); nxs_metaMarkAsPosted($postID, $this->ntInfo['code'], $ii, array('isPosted'=>'1', 'pgID'=>$ret['postID'], 'postURL'=>$ret['postURL'], 'pDate'=>date('Y-m-d H:i:s'))); 
          $extInfo .= ' | <a href="'.$ret['postURL'].'" target="_blank">Post Link</a>'; nxs_LogIt('S', 'Posted', $logNT, $this->ntInfo['code'], 'OK - Message Posted ', $extInfo,'snap',$uid); 
        }
      }
      //## Return Result
      if (!empty($ret['isPosted']) && $ret['isPosted']=='1') return 200; else return print_r($ret, true); 
    }
}}
?>