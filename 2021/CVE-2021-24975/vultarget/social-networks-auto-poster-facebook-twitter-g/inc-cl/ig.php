<?php    
//## NextScripts Instagram Connection Class (##Can't replace - it has .png)
$nxs_snapAvNts[] = array('code'=>'IG', 'lcode'=>'ig', 'name'=>'Instagram', 'type'=>'Social Networks', 'type'=>'Social Networks', 'ptype'=>'P', 'status'=>'A', 'desc'=>'Upload your blogpost\'s image to your Instagram account.');

if (!class_exists("nxs_snapClassIG")) { class nxs_snapClassIG extends nxs_snapClassNT {   
  var $ntInfo = array('code'=>'IG', 'lcode'=>'ig', 'name'=>'Instagram', 'defNName'=>'uName', 'tstReq' => false, 'imgAct'=>'E', 'instrURL'=>'http://www.nextscripts.com/instructions/instagram-auto-poster-setup-installation/');
  var $noFuncMsg = 'Instagram doesn\'t have a built-in API for automated posts yet. <br/>You need to get a special <a target="_blank" href="http://www.nextscripts.com/api/instagram-automated-posting/">library module</a> to be able to publish your content to Instagram.';
  var $noFuncMsg2 = 'Instagram API Library module NOT found.<br/><br/><span style="color:black; font-size:15px;">It looks like you got a SNAP Pro plugin with API package using "One time fee" promo offer more then a year ago. <br/>We are sorry, but Instagram was not a part of that package. <br/>As part of the offer agreement, you will be getting indefinite support and updates for Google+, Pinterest, LinkedIn, Reddit, Flipboard and all other networks what were included with your order, but Instagram and all other future networks requre an active API subscription. <a target="_blank" href="http://gd.is/expi">Please see here for more info</a></span><hr/>';
  function checkIfFunc() { return class_exists('nxsAPI_IG'); }
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts; $this->showNTGroup(); return; }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'uName'=>'', 'uPass'=>'', 'msgTFormat'=>'', 'msgFormat'=>"%TITLE% \n %HTAGS%"); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['uPass']); }
  function accTab($ii, $options, $isNew=false){ $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $this->elemUserPass($ii, $options['uName'], $options['uPass']); ?> <br/ >    
    <div style="width:100%;"><strong id="altFormatText"><?php _e('What do to with the image', 'social-networks-auto-poster-facebook-twitter-g'); ?> :</strong>&lt;-- (<a id="showShAtt" onmouseout="hidePopShAtt('<?php echo $ii; ?>IG');" onmouseover="showPopShAtt('<?php echo $ii; ?>IG', event);" onclick="return false;" class="underdash" href="#"><?php _e('What\'s the difference?', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>)  </div>                      
    <div style="margin-left: 10px;">
    <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][imgAct]" value="E" <?php if (empty($options['imgAct']) || $options['imgAct'] == 'E') echo 'checked="checked"'; ?> /> <?php _e('Make it Square: Extend', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Image will be extended to square', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>
    <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][imgAct]" value="C" <?php if (!empty($options['imgAct']) && $options['imgAct'] == 'C') echo 'checked="checked"'; ?> /> <?php _e('Make it Square: Crop', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Image will be cropped to square', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                    
    <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][imgAct]" value="D" <?php if (!empty($options['imgAct']) && $options['imgAct'] == 'D') echo 'checked="checked"'; ?> /> <?php _e("Don't change", 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Image will be untouched. (Could cause "Uploaded image isn\'t in an allowed aspect ratio" Error)', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>
    <br/></div>
    <?php $this->elemMsgFormat($ii,'Pin Description Format','msgFormat',$options['msgFormat']); 
  }
  function advTab($ii, $options){$this->showProxies($this->ntInfo['lcode'], $ii, $options);}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['uPass']) && !empty($pval['uPass'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);    
      if (isset($pval['imgAct'])) $options[$ii]['imgAct'] = trim($pval['imgAct']); else $options[$ii]['imgAct'] = 'E';                    
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
  
}}


if (!function_exists("nxs_doPublishToIG")) { function nxs_doPublishToIG($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true));
  ini_set('memory_limit','256M'); $cl = new nxs_snapClassIG(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}}  
?>