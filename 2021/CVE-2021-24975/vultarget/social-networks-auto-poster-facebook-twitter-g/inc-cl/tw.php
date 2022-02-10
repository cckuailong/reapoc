<?php    
//## NextScripts Twitter Connection Class
$nxs_snapAvNts[] = array('code'=>'TW', 'lcode'=>'tw', 'name'=>'Twitter', 'type'=>'Social Networks', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Autopost to your account. Ability to attach images to tweets');

if (!class_exists("nxs_snapClassTW")) { class nxs_snapClassTW extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'TW', 'lcode'=>'tw', 'name'=>'Twitter', 'defNName'=>'', 'tstReq' => true, 'instrURL'=>'http://www.nextscripts.com/setup-installation-twitter-social-networks-auto-poster-wordpress/');      
  var $defO = array('nName'=>'', 'do'=>'1', 'twURL'=>'', 'appKey'=>'', 'appSec'=>'', 'accessToken'=>'', 'accessTokenSec'=>'', 'attchImg'=>1, 'msgFormat'=>"New post (%TITLE%) has been published on %SITENAME% - %URL%", 'imgSize'=>'original');
  //#### Update
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  $ntOptsOut['attchImg'] = $ntOpts['attchImg'];
        $ntOptsOut['msgFormat'] = $ntOpts['twMsgFormat']; $ntOptsOut['appKey'] = $ntOpts['twConsKey'];  $ntOptsOut['appSec'] = $ntOpts['twConsSec'];   $ntOptsOut['twURL'] = $ntOpts['twURL']; 
        $ntOptsOut['accessToken'] = $ntOpts['twAccToken']; $ntOptsOut['accessTokenSec'] = $ntOpts['twAccTokenSec']; $ntOptsOut['imgSize'] = !empty($ntOpts['imgSize'])?$ntOpts['imgSize']:''; 
        $ntOptsOut = nxs_arrMergeCheck($ntOptsOut, $this->defO); $ntOptsOut['isUpdd'] = '1'; $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  }   
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts;  $this->showNTGroup(); }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $this->showGNewNTSettings($ii, $this->defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['appKey']) && !empty($options['accessToken']); }
  public function doAuth() { }    
  
  function accTab($ii, $options, $isNew=false){ global $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; ?>    
    <div class="nxs_tls_lbl"><strong>Your <?php echo $ntInfo['name']; ?> URL:</strong> </div><input type="text" name="<?php echo $nt; ?>[<?php echo $ii; ?>][twURL]" style="width: 40%;border: 1px solid #ACACAC;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['twURL'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>    
    <?php $this->elemKeySecret($ii,'API Key','API Secret', $options['appKey'], $options['appSec'],'appKey','appSec','https://apps.twitter.com/');  $this->elemKeySecret($ii,'Access Token','Access Token Secret', $options['accessToken'], $options['accessTokenSec'],'accessToken','accessTokenSec'); ?>
    <br/><?php $this->elemMsgFormat($ii,'Message Format','msgFormat',$options['msgFormat']); ?>    
    <div style="margin: 0px;"><input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][attchImg]"  <?php if ((int)$options['attchImg'] == 1) echo "checked"; ?> /> <strong><?php _e('Attach Image to the Post', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong></div>
    <br/><br/><?php
  }
  function advTab($ii, $options){ $nt = $this->ntInfo['lcode']; ?><div class="nxs_tls_cpt"><?php _e('Auto Import of Replies and Mentions:', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
   <div class="nxs_tls_bd">
   <div class="nxs_tls_sbInfo"><?php _e('Plugin could grab Replies and Mentions from Twitter and import them as Wordpress Comments', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
   <?php global $plgn_NS_SNAutoPoster; $gOptions = $plgn_NS_SNAutoPoster->nxs_options; if ( !empty($gOptions['riActive']) && $gOptions['riActive'] == '1' ) { ?>
   <input value="1" id="riC<?php echo $ii; ?>" <?php if (!empty($options['riComments']) && trim($options['riComments'])=='1') echo "checked"; ?> type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][riComments]"/> <b><?php _e('Import Twitter Replies', 'social-networks-auto-poster-facebook-twitter-g'); ?></b>
   <br/>
   <input value="1" id="riCM<?php echo $ii; ?>" <?php if (!empty($options['riCommentsM']) && trim($options['riCommentsM'])=='1') echo "checked"; ?> type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][riCommentsM]"/> <b><?php _e('Import Twitter Mentions', 'social-networks-auto-poster-facebook-twitter-g'); ?></b>
   <br/> 
   <input value="1" id="riCA<?php echo $ii; ?>" <?php if (!empty($options['riCommentsAA']) && trim($options['riCommentsAA'])=='1') echo "checked"; ?> type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][riCommentsAA]"/> <b><?php _e('Auto-approve imported comments', 'social-networks-auto-poster-facebook-twitter-g'); ?></b>
   <?php } else { echo "<br/>"; _e('Please activate the "Comments Import" from SNAP Settings Tab', 'social-networks-auto-poster-facebook-twitter-g'); } ?>   
   </div><?php }
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['appKey']) && !empty($pval['appKey'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
        //## Uniqe Items        
        if (isset($pval['twURL'])) $options[$ii]['twURL'] = trim($pval['twURL']); 
        if (isset($pval['accessToken'])) $options[$ii]['accessToken'] = trim($pval['accessToken']); 
        if (isset($pval['accessTokenSec'])) $options[$ii]['accessTokenSec'] = trim($pval['accessTokenSec']); 
        
        if (isset($pval['riComments']))      $options[$ii]['riComments'] = $pval['riComments']; else $options[$ii]['riComments'] = 0;
        if (isset($pval['riCommentsM']))     $options[$ii]['riCommentsM'] = $pval['riCommentsM']; else $options[$ii]['riCommentsM'] = 0;
        if (isset($pval['riCommentsAA']))    $options[$ii]['riCommentsAA'] = $pval['riCommentsAA']; else $options[$ii]['riCommentsAA'] = 0;
        
        if (isset($pval['attchImg'])) $options[$ii]['attchImg'] = trim($pval['attchImg']); else $options[$ii]['attchImg'] = 0;
      } elseif ( count($pval)==1 ) if (isset($pval['do'])) $options[$ii]['do'] = $pval['do']; else $options[$ii]['do'] = 0; 
    } return $options;
  }
    
  //#### Show Post->Edit Meta Box Settings
  
  function showEdPostNTSettings($ntOpts, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];
      foreach($ntOpts as $ii=>$ntOpt)  { $isFin = $this->checkIfSetupFinished($ntOpt); if (!$isFin) continue; 
        $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true)); if (is_array($pMeta) && !empty($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);         
        
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse']; $ntOpt['ii']=$ii;
        
        $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta); 
        
          $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat); // prr($ntOpt, 'OPTS');
          ?>
          <tr><td>&nbsp;</td><td><div style="margin: 0px;"><input value="0" type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][attchImg]"/>
          <input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][attchImg]"  <?php if ((int)$ntOpt['attchImg'] == 1) echo "checked"; ?> /> <strong><?php _e('Attach Image to the Post', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong></div></td></tr>
          <?php nxs_showImgToUseDlg($nt, $ii, $imgToUse);        
    
       /* ## Select Image & URL ## */  nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);     
     }
  }
  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta); 
    if (!empty($pMeta['attchImg'])) $optMt['attchImg'] = $pMeta['attchImg']; else $optMt['attchImg'] = 0;// prr($pMeta, 'SV'); prr($optMt, 'SAVE'); //die();
    return $optMt;
  }
  
  function adjPreFormatWP(&$options, $postID){if (!empty($postID)) { 
    
    $twLim = 140;  global $plgn_NS_SNAutoPoster, $nxs_SNAP; 
    
    $gOptions = (!empty($plgn_NS_SNAutoPoster))?$plgn_NS_SNAutoPoster->nxs_options:$nxs_SNAP->nxs_options; if (!empty($gOptions['nxsHTSpace'])) $htS = $gOptions['nxsHTSpace']; else $htS = '';
    $addParams = nxs_makeURLParams(array('NTNAME'=>$this->ntInfo['name'], 'NTCODE'=>$this->ntInfo['code'], 'POSTID'=>$postID, 'ACCNAME'=>$options['nName'])); 
    
    $post = get_post($postID); if(!$post) return; $twMsgFormat = $options['msgFormat']; $extInfo = ' | PostID: '.$postID." - ".$post->post_title.' |'.$options['pType'];        
        if (stripos($twMsgFormat, '%URL%')!==false || stripos($twMsgFormat, '%SURL%')!==false) $twLim = $twLim - 23; 
        if (stripos($twMsgFormat, '%AUTHORNAME%')!==false) { $aun = $post->post_author;  $aun = get_the_author_meta('display_name', $aun ); $twLim = $twLim - nxs_strLen($aun); }
        $noRepl = str_ireplace("%TITLE%", "", $twMsgFormat); $noRepl = str_ireplace("%SITENAME%", "", $noRepl); $noRepl = str_ireplace("%URL%", "", $noRepl);$noRepl = str_ireplace("%RAWEXCERPT%", "", $noRepl); 
        $noRepl = str_ireplace("%SURL%", "", $noRepl);$noRepl = str_ireplace("%TEXT%", "", $noRepl);$noRepl = str_ireplace("%FULLTEXT%", "", $noRepl);$noRepl = str_ireplace("%EXCERPT%", "", $noRepl); 
        $noRepl = str_ireplace("%ANNOUNCE%", "", $noRepl); $noRepl = str_ireplace("%AUTHORNAME%", "", $noRepl);  $noRepl = str_ireplace("%TAGS%", "", $noRepl); $noRepl = str_ireplace("%CATS%", "", $noRepl);         
        $noRepl = str_ireplace("%HTAGS%", "", $noRepl); $noRepl = str_ireplace("%HCATS%", "", $noRepl);
        $noRepl = preg_replace('/%H?C(F|T)-[a-zA-Z0-9_]+%/', '', $noRepl); $twLim = $twLim - nxs_strLen($noRepl); if (stripos($noRepl, 'http')!==false) $twLim = $twLim - 5;
        $pTitle = nxs_doQTrans($post->post_title); if ($post->post_excerpt!="") $exrText = nxs_doQTrans($post->post_excerpt); else $exrText= nxs_doQTrans($post->post_content); 
        $pText = (empty($gOptions['brokenCntFilters']))?apply_filters('the_content', $exrText):$exrText;      
        $pRawText = nxs_doQTrans($post->post_content); $pFullText = (empty($gOptions['brokenCntFilters']))?apply_filters('the_content', $pRawText):$pRawText;  
        if (stripos($twMsgFormat, '%TITLE%')!==false || stripos($twMsgFormat, '%TEXT%')!==false || stripos($twMsgFormat, '%EXCERPT%')!==false || stripos($twMsgFormat, '%ANNOUNCE%')!==false || stripos($twMsgFormat, '%RAWEXCERPT%')!==false || stripos($twMsgFormat, '%FULLTEXT%')!==false || stripos($twMsgFormat, '%RAWTEXT%')!==false) $whatToleave = 45; else $whatToleave = 0;
        if (stripos($twMsgFormat, '%XTAGS%')!==false || stripos($twMsgFormat, '%HTAGS%')!==false) {
          $t = wp_get_object_terms($postID, 'product_tag'); if ( empty($t) || !is_array($t) ) $t = wp_get_post_tags($postID); 
          // $tggs = array(); foreach ($t as $tagA) { $frmTag =  trim(str_replace(' ', $htS, preg_replace('/xC2xA0/',$htS, preg_replace('/[^a-zA-Z0-9\p{L}\p{N}\s]/u', '', trim(ucwords(str_ireplace('&','',str_ireplace('&amp;','',$tagA->name))))))));
          $tggs = array(); foreach ($t as $tagA) { $frmTag =  trim(str_replace(' ', $htS, preg_replace('/xC2xA0/',$htS, nxs_clean_string(trim(ucwords(str_ireplace('&','',str_ireplace('&amp;','',$tagA->name))))))));
              if (preg_match('/\b'.$frmTag.'\b/iu', $pTitle)) $pTitle = trim(preg_replace('/\b'.$frmTag.'\b/iu', '#'.$frmTag, $pTitle)); 
              if (preg_match('/\b'.$frmTag.'\b/iu', $pFullText)) $pFullText = trim(preg_replace('/\b'.$frmTag.'\b/iu', '#'.$frmTag, $pFullText)); 
              if (preg_match('/\b'.$frmTag.'\b/iu', $pText)) $pText = trim(preg_replace('/\b'.$frmTag.'\b/iu', '#'.$frmTag, $pText)); 
              if (preg_match('/\b'.$frmTag.'\b/iu', $pRawText)) $pRawText = trim(preg_replace('/\b'.$frmTag.'\b/iu', '#'.$frmTag, $pRawText)); 
              if ( ((stripos($twMsgFormat, '%TITLE%')!==false) && preg_match('/\b'.$frmTag.'\b/i', $pTitle)) ||
                   ((stripos($twMsgFormat, '%TEXT%')!==false) && preg_match('/\b'.$frmTag.'\b/i', $pText)) ||
                   ((stripos($twMsgFormat, '%EXCERPT%')!==false) && preg_match('/\b'.$frmTag.'\b/i', $pText)) ||
                   ((stripos($twMsgFormat, '%RAWEXCERPT%')!==false) && preg_match('/\b'.$frmTag.'\b/i', $pText)) ||
                   ((stripos($twMsgFormat, '%ANNOUNCE%')!==false) && preg_match('/\b'.$frmTag.'\b/i', $pText)) ||
                   ((stripos($twMsgFormat, '%FULLTEXT%')!==false) && preg_match('/\b'.$frmTag.'\b/i', $pFullText)) ||
                   ((stripos($twMsgFormat, '%RAWTEXT%')!==false) && preg_match('/\b'.$frmTag.'\b/i', $pRawText)) ) {} else $tggs[] = '#'.$frmTag;
          } 
          
          if (count($tggs)<2) $tags = implode(' ', $tggs); else { $tags = array_shift($tggs); //## Always keep the first. 
            $tags2 = implode(' ', $tggs); //## other elemets
            $tgsTwLim = $twLim-$whatToleave; $tags2 = trim(nsTrnc(' '.$tags2.' ', $tgsTwLim, " ", "")); $tags .= !empty($tags2)?(' '.$tags2):'';
          }
          
          
          $twMsgFormat = str_ireplace("%XTAGS%", $tags, $twMsgFormat);  $twMsgFormat = str_ireplace("%HTAGS%", $tags, $twMsgFormat);
          $twLim = $twLim - nxs_strLen($tags); 
        }  
        if (stripos($twMsgFormat, '%XCATS%')!==false || stripos($twMsgFormat, '%HCATS%')!==false) {
          $t = wp_get_post_categories($postID); $ctts = array();  foreach($t as $c){ $cat = get_category($c); //$frmTag =  trim(str_replace(' ','', str_replace('  ',' ',str_ireplace('&','&amp;',trim(ucwords($cat->name)))))); prr($frmTag);
          //$frmTag =  trim(str_replace(' ',$htS,preg_replace('/[^a-zA-Z0-9\p{L}\p{N}\s]/u', '', trim(ucwords(str_ireplace('&','',str_ireplace('&amp;','',$cat->name)))))));
          $frmTag =  trim(str_replace(' ', $htS, nxs_clean_string(trim(ucwords(str_ireplace('&','',str_ireplace('&amp;','',$cat->name)))))));          
          if (stripos($pTitle, $cat->name)!==false) $pTitle = str_ireplace($cat->name, '#'.$frmTag, $pTitle); elseif (stripos($pTitle, $frmTag)!==false) $pTitle = str_ireplace($frmTag, '#'.$frmTag, $pTitle); 
              if (stripos($pText, $cat->name)!==false) $pText = str_ireplace($cat->name, '#'.$frmTag, $pText); elseif (stripos($pText, $frmTag)!==false) $pText = str_ireplace($frmTag, '#'.$frmTag, $pText); 
              if (stripos($pFullText, $cat->name)!==false) $pFullText = str_ireplace($cat->name, '#'.$frmTag, $pFullText); elseif (stripos($pFullText, $frmTag)!==false) $pFullText = str_ireplace($frmTag, '#'.$frmTag, $pFullText); 
              if (stripos($pRawText, $cat->name)!==false) $pRawText = str_ireplace($cat->name, '#'.$frmTag, $pRawText); elseif (stripos($pRawText, $frmTag)!==false) $pRawText = str_ireplace($frmTag, '#'.$frmTag, $pRawText); 
              if ( ((stripos($twMsgFormat, '%TITLE%')!==false) && (stripos($pTitle, $cat->name)!==false || stripos($pTitle, $frmTag)!==false)) ||
                   ((stripos($twMsgFormat, '%TEXT%')!==false) && (stripos($pText, $cat->name)!==false || stripos($pText, $frmTag)!==false)) ||
                   ((stripos($twMsgFormat, '%EXCERPT%')!==false) && (stripos($pText, $cat->name)!==false || stripos($pText, $frmTag)!==false)) ||
                   ((stripos($twMsgFormat, '%RAWEXCERPT%')!==false) && (stripos($exrText, $cat->name)!==false || stripos($exrText, $frmTag)!==false)) ||
                   ((stripos($twMsgFormat, '%ANNOUNCE%')!==false) && (stripos($pText, $cat->name)!==false || stripos($pText, $frmTag)!==false)) ||
                   ((stripos($twMsgFormat, '%FULLTEXT%')!==false) && (stripos($pFullText, $cat->name)!==false || stripos($pFullText, $frmTag)!==false)) ||
                   ((stripos($twMsgFormat, '%RAWTEXT%')!==false) && (stripos($pRawText, $cat->name)!==false || stripos($pRawText, $frmTag)!==false)) ) {} else $ctts[] = '#'.$frmTag; 
          } 
          
          if (count($ctts)<2) $cats = implode(' ', $ctts); else { $cats = array_shift($ctts); //## Always keep the first. 
            $cats2 = implode(' ', $ctts); //## other elemets
            $tgsTwLim = $twLim-$whatToleave; $cats2 = trim(nsTrnc(' '.$cats2.' ', $tgsTwLim, " ", "")); $cats .= !empty($cats2)?(' '.$cats2):'';
          }
          
          
          //prr($ctts, 'CT1'); $cats = implode(' ',$ctts);  prr($cats, 'CT2'); $tgsTwLim = $twLim-45; prr($tgsTwLim, 'CL'); $cats = nsTrnc($cats.' ', $tgsTwLim, " ", ""); prr($cats, 'CT3'); 
          
          
          
          $twMsgFormat = str_ireplace("%XCATS%", $cats, $twMsgFormat);  $twMsgFormat = str_ireplace("%HCATS%", $cats, $twMsgFormat);
          $twLim = $twLim - nxs_strLen($cats);
          
          
        }  
        if (preg_match('/%H?CT-[a-zA-Z0-9_]+%/', $twMsgFormat)) { $msgA = explode('%CT', str_ireplace("%HCT", "%CT", $twMsgFormat)); $mout = '';
          foreach ($msgA as $mms) { 
            if (substr($mms, 0, 1)=='-' && stripos($mms, '%')!==false) { $mGr=CutFromTo($mms,'-','%'); $cfItem=wp_get_post_terms($postID,$mGr,array("fields" => "names"));  
              if (is_nxs_error($cfItem)) {nxs_addToLogN('E', 'Error', $logNT, '-=ERROR=- '.$mGr.'|'.print_r($cfItem, true), $extInfo);  } else { $tggs = array(); 
                foreach ($cfItem as $frmTag) { 
                  //$frmTag =  trim(str_replace(' ', $htS, preg_replace('/[^a-zA-Z0-9\p{L}\p{N}\s]/u', '', trim(ucwords(str_ireplace('&','',str_ireplace('&amp;','',$frmTag)))))));  $tggs[] = '#'.$frmTag; 
                  $frmTag =  trim(str_replace(' ', $htS, nxs_clean_string(trim(ucwords(str_ireplace('&','',str_ireplace('&amp;','',$frmTag)))))));  $tggs[] = '#'.$frmTag; 
                } $cfItem = implode(' ',$tggs); $twLim = $twLim - nxs_strLen($cfItem); $mms=str_ireplace("-".$mGr."%",$cfItem,$mms);               
              }
            } $mout.=$mms; 
          } $twMsgFormat = $mout; 
        } $twMsgFormat = str_ireplace("  ", " ", $twMsgFormat);
        if (stripos($twMsgFormat, '%TITLE%')!==false) { if (stripos($pTitle, '.co.uk')!==false) $twLim = $twLim - 14;
           if (stripos($pTitle, '.com')!==false) $twLim = $twLim - 16; if (stripos($pTitle, '.net')!==false) $twLim = $twLim - 16; if (stripos($pTitle, '.org')!==false) $twLim = $twLim - 16;
           $pTitle = html_entity_decode(strip_tags($pTitle), ENT_NOQUOTES, 'UTF-8'); //$ttlTwLim = $twLim-20;                      
           $ttlTwLim = $twLim; $pTitle = nsTrnc($pTitle, $ttlTwLim); $twMsgFormat = str_ireplace("%TITLE%", $pTitle, $twMsgFormat); $twLim = $twLim - nxs_strLen($pTitle);            
        } // prr($twMsgFormat); prr($twLim);
        if (stripos($twMsgFormat, '%SITENAME%')!==false) {
          $siteTitle = htmlspecialchars_decode(get_bloginfo('name'), ENT_QUOTES); $siteTitle = nsTrnc($siteTitle, $twLim); $twMsgFormat = str_ireplace("%SITENAME%", $siteTitle, $twMsgFormat); $twLim = $twLim - nxs_strLen($siteTitle);
        }     
        if (stripos($twMsgFormat, '%TEXT%')!==false) {          
          $pText = nsTrnc(strip_tags(strip_shortcodes($pText)), 140, " ", "..."); 
          $pText = nsTrnc($pText, $twLim); $twMsgFormat = str_ireplace("%TEXT%", $pText, $twMsgFormat); $twLim = $twLim - nxs_strLen($pText);
        } 
        if (stripos($twMsgFormat, '%EXCERPT%')!==false) {          
          $pText = nsTrnc(strip_tags(strip_shortcodes($pText)), 140, " ", "...");
          $pText = nsTrnc($pText, $twLim); $twMsgFormat = str_ireplace("%EXCERPT%", $pText, $twMsgFormat); $twLim = $twLim - nxs_strLen($pText);
        } 
        if (stripos($twMsgFormat, '%ANNOUNCE%')!==false) {          
          $pText = nsTrnc(strip_tags(strip_shortcodes($pText)), 140, " ", "..."); 
          $pText = nsTrnc($pText, $twLim); $twMsgFormat = str_ireplace("%ANNOUNCE%", $pText, $twMsgFormat); $twLim = $twLim - nxs_strLen($pText);
        } 
        if (stripos($twMsgFormat, '%RAWEXCERPT%')!==false) {          
          $exrText = nsTrnc(strip_tags(strip_shortcodes($exrText)), 140, " ", "..."); 
          $exrText = nsTrnc($exrText, $twLim); $twMsgFormat = str_ireplace("%RAWEXCERPT%", $exrText, $twMsgFormat); $twLim = $twLim - nxs_strLen($exrText);
        } 
        if (stripos($twMsgFormat, '%FULLTEXT%')!==false) {
           $pFullText = nsTrnc(strip_tags($pFullText), $twLim); $twMsgFormat = str_ireplace("%FULLTEXT%", $pFullText, $twMsgFormat); $twLim = $twLim - nxs_strLen($pFullText);
        }          
        if (stripos($twMsgFormat, '%RAWTEXT%')!==false) {
           $pRawText = nsTrnc(strip_tags($pRawText), $twLim); $twMsgFormat = str_ireplace("%RAWTEXT%", $pRawText, $twMsgFormat); $twLim = $twLim - nxs_strLen($pRawText);
        }  $msg = nsFormatMessage($twMsgFormat, $postID, $addParams);
    } 
    $msg = str_replace('&amp;#039;', "'", $msg);  $msg = str_replace('&#039;', "'", $msg);  $msg = str_replace('#039;', "'", $msg);  $msg = str_replace('#039', "'", $msg);
    $msg = str_replace('&amp;#8217;', "'", $msg); $msg = str_replace('&#8217;', "'", $msg); $msg = str_replace('#8217;', "'", $msg); $msg = str_replace('#8217', "'", $msg);
    $msg = str_replace('&amp;#8220;', '"', $msg); $msg = str_replace('&#8220;', '"', $msg); $msg = str_replace('#8220;', '"', $msg); $msg = str_replace('#8220', "'", $msg);
    $msg = str_replace('&amp;#8221;', '"', $msg); $msg = str_replace('&#8221;', '"', $msg); $msg = str_replace('#8221;', '"', $msg); $msg = str_replace('#8221', "'", $msg);
    $msg = str_replace('&amp;#8212;', '-', $msg); $msg = str_replace('&#8212;', '-', $msg); $msg = str_replace('#8212;', '-', $msg); $msg = str_replace('#8212', "-", $msg);     
    $msg = nxs_decodeEntitiesFull($msg); $options['msgFormat'] = $msg;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ $imgData = '';
    
    if (!empty($options['attchImg']) && $options['attchImg']=='1') { if (!empty($options['imgToUse'])) $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'large');  if (preg_match("/noImg.\.png/i", $imgURL)) $imgURL = '';  
      if(trim($imgURL)=='') $options['attchImg'] = 0; else { $imgURL = str_replace(' ', '%20', $imgURL); $hdrsArr = nxs_getNXSHeaders(); $advSet=nxs_mkRemOptsArr($hdrsArr); $imgData = nxs_remote_get($imgURL, $advSet);         
        if(is_nxs_error($imgData) || empty($imgData['body']) || (!empty($imgData['headers']['content-length']) && (int)$imgData['headers']['content-length']<200) || 
          $imgData['headers']['content-type'] == 'text/html' ||  $imgData['response']['code'] == '403' ) { $options['attchImg'] = 0; 
            nxs_addToLogN('E','Error',$logNT,'Could not get image ('.$imgURL.'), will post without it - ', print_r($imgData, true)); 
        } else $imgData = $imgData['body']; 
      }      
    } $message['img'] = $imgData;
    
  }   
  
  function importComments($options='', $postID='', $po='') { if (empty($postID)) $postID = $_POST['pid']; 
    if (empty($options)) {  global $plgn_NS_SNAutoPoster; $options = $plgn_NS_SNAutoPoster->nxs_options; }
    if (empty($po)) { $po =  maybe_unserialize(get_post_meta($postID, 'snap'.strtoupper($_POST['nt']), true)); $po = $po[$_POST['ii']]; }
    if (isset($_POST['ii'])) $options = $options[$_POST['nt']][$_POST['ii']];  
    
    require_once ('apis/tmhOAuth.php'); $tmhOAuth = new NXS_tmhOAuth(array( 'consumer_key' => $options['appKey'], 'consumer_secret' => $options['appSec'], 'user_token' => $options['accessToken'], 'user_secret' => $options['accessTokenSec']));
    $code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/statuses/mentions_timeline')); 
    if ($code=='200' && isset($tmhOAuth->response['response']) ) $twList = json_decode($tmhOAuth->response['response'], true); else $twList = ''; 
     
    $impCmnts = get_post_meta($postID, 'snapImportedComments', true); 
    if(!is_array($impCmnts)) $impCmnts = array(); $twsToImp = array(); $lastID = '';  
    //## Do Replies
    if (!empty($twList) && is_array($twList)) foreach ($twList as $tw) if ($tw['in_reply_to_status_id_str'] == $po['pgID']) $twsToImp[] = $tw;
    if (!empty($twList) && is_array($twsToImp) && count($twsToImp)>0)
      foreach ($twsToImp as $comment){ $cid = $comment['id_str']; if (trim($cid)=='' || in_array('twxcw'.$cid, $impCmnts)) continue; else $impCmnts[] = 'twxcw'.$cid;  
        $commentdata = array( 'comment_post_ID' => $postID, 'comment_author' => $comment['user']['name'], 'comment_agent' => "SNAP||".str_ireplace('_normal.','_bigger.',$comment['user']['profile_image_url_https']), 
          'comment_author_email' => $comment['user']['screen_name'].'@twitter.com', 'comment_author_url' => 'http://twitter.com/'.$comment['user']['screen_name'], 
          'comment_content' => str_ireplace('@'.$comment['in_reply_to_screen_name'],'', $comment['text']), 'comment_date_gmt' => date('Y-m-d H:i:s', strtotime( $comment['created_at'] ) ), 'comment_type' => '');
        nxs_postNewComment($commentdata, $options['riCommentsAA']=='1'); $ci++; echo $ci;
      }     
     
    //## Do mentions.
    require_once ('apis/tmhOAuth.php'); $tmhOAuth = new NXS_tmhOAuth(array( 'consumer_key' => $options['appKey'], 'consumer_secret' => $options['appSec'], 'user_token' => $options['accessToken'], 'user_secret' => $options['accessTokenSec']));    
    if (isset($options['urlToUse']) && trim($options['urlToUse'])!='') $urlToSrch = $options['urlToUse']; else $urlToSrch = get_permalink($postID);     
    $code = $tmhOAuth->request('GET', $tmhOAuth->url('1.1/search/tweets'), array('rpp'=>'100', 'since_id'=>$lastID, 'q'=> urlencode($urlToSrch)));       
    if ($code=='200' && isset($tmhOAuth->response['response']) ) { $tweets = json_decode($tmhOAuth->response['response'], true); //prr($tweets);
     if (is_array($tweets) && is_array($tweets['statuses'])) {    
      foreach ($tweets['statuses'] as $comment){ $cid = $comment['id_str']; if (trim($cid)=='' || in_array('twxcw'.$cid, $impCmnts) || $cid==$po['pgID']) continue; else $impCmnts[] = 'twxcw'.$cid;  // prr($impCmnts);
        $commentdata = array( 'comment_post_ID' => $postID, 'comment_author' => $comment['user']['name'], 'comment_author_email' =>  $comment['user']['screen_name'].'@twitter.com', 
          'comment_agent' => "SNAP||".str_ireplace('_normal.','_bigger.',$comment['user']['profile_image_url_https']), 
          'comment_author_url' => 'http://twitter.com/'.$comment['user']['screen_name'], 'comment_content' => $comment['text'], 'comment_date_gmt' => date('Y-m-d H:i:s', strtotime( $comment['created_at'] ) ), 'comment_type' => '');
        nxs_postNewComment($commentdata, $options['riCommentsAA']=='1'); $ci++;
      }
    }}
    delete_post_meta($postID, 'snapImportedComments'); add_post_meta($postID, 'snapImportedComments', $impCmnts );
    if ( isset($_POST['pid']) && $_POST['pid']!='') printf( _n('%d comment has been imported.', '%d comments has been imported.', $ci, 'social-networks-auto-poster-facebook-twitter-g'), $ci );
  }
  
}}

if (!function_exists("nxs_doPublishToTW")) { function nxs_doPublishToTW($postID, $options){ if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassTW(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); }} 

//## TW Specific Functions
if (!function_exists("nxs_getBackTWCommentsList")) { function nxs_getBackTWCommentsList($options) { 
  
}}

?>