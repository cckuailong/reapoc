<?php    
//## NextScripts Facebook Connection Class
$nxs_snapAvNts[] = array('code'=>'PK', 'lcode'=>'pk', 'name'=>'Plurk', 'type'=>'Social Networks', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Autopost to your account. Ability to attach Image to messages');

if (!class_exists("nxs_snapClassPK")) { class nxs_snapClassPK extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'PK', 'lcode'=>'pk', 'name'=>'Plurk', 'defNName'=>'', 'tstReq' => true, 'instrURL'=>'http://www.nextscripts.com/setup-installation-plurk-social-networks-auto-poster-wordpress/');      
  var $defO = array('nName'=>'', 'do'=>'1', 'appKey'=>'', 'appSec'=>'', 'attchImg'=>1, 'msgFormat'=>"%TITLE% - %URL%",  'pkCat'=>'', 'pkURL'=>'');
  //#### Update
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = !empty($ntOpts['nName'])?$ntOpts['nName']:'';  
        $ntOptsOut['msgFormat'] = !empty($ntOpts['pkMsgFormat'])?$ntOpts['pkMsgFormat']:''; $ntOptsOut['appKey'] = !empty($ntOpts['pkConsKey'])?$ntOpts['pkConsKey']:'';  $ntOptsOut['appSec'] = !empty($ntOpts['pkConsSec'])?$ntOpts['pkConsSec']:''; 
        $ntOptsOut['accessToken'] = !empty($ntOpts['pkAccessTocken'])?$ntOpts['pkAccessTocken']:''; $ntOptsOut['accessTokenSec'] = !empty($ntOpts['pkAccessTockenSec'])?$ntOpts['pkAccessTockenSec']:''; 
        $ntOptsOut['oAuthToken'] = !empty($ntOpts['pkOAuthToken'])?$ntOpts['pkOAuthToken']:''; $ntOptsOut['oAuthTokenSecret'] = !empty($ntOpts['pkOAuthTokenSecret'])?$ntOpts['pkOAuthTokenSecret']:'';        
        $ntOptsOut['pgID'] = !empty($ntOpts['pkPgID'])?$ntOpts['pkPgID']:''; $ntOptsOut['pkURL'] = !empty($ntOpts['pkURL'])?$ntOpts['pkURL']:''; $ntOptsOut['pkCat'] =!empty($ntOpts['pkCat'])?$ntOpts['pkCat']:''; $ntOptsOut['attchImg'] = !empty($ntOpts['attchImg'])?$ntOpts['attchImg']:'';
        $ntOptsOut = nxs_arrMergeCheck($ntOptsOut, $this->defO); $ntOptsOut['isUpdd'] = '1'; $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  }   
   function pkCats() { return '<option value="">:freestyle(None)</option><option value="loves">loves</option><option value="likes">likes</option><option value="shares">shares</option><option value="gives">gives</option><option value="hates">hates</option><option value="wants">wants</option><option value="wishes">wishes</option><option value="needs">needs</option><option value="will">will</option><option value="hopes">hopes</option><option value="asks">asks</option><option value="has">has</option><option value="was">was</option><option value="wonders">wonders</option><option value="feels on">feels</option><option value="thinks">thinks</option><option value="says">says</option><option value="is">is</option>';}    
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts;  $this->showNTGroup(); }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $this->showGNewNTSettings($ii, $this->defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['pgID']) && !empty($options['accessToken']); }
  public function doAuth() { $ntInfo = $this->ntInfo; global $nxs_snapSetPgURL;     
   if ( isset($_GET['auth']) && $_GET['auth']=='pk'){ require_once('apis/plurkOAuth.php'); $options = $this->nt[$_GET['acc']];  prr($options, 'OPTS:');  prr($this->nt, 'OPTS:');
              $consumer_key = $options['appKey']; $consumer_secret = $options['appSec']; $callback_url = $nxs_snapSetPgURL."&auth=pka&acc=".$_GET['acc'];             
              $tum_oauth = new wpPlurkOAuth($consumer_key, $consumer_secret); $request_token = $tum_oauth->getReqToken($callback_url); 
              $options['oAuthToken'] = $request_token['oauth_token']; $options['oAuthTokenSecret'] = $request_token['oauth_token_secret']; //prr($tum_oauth); prr($options); //die();              
              switch ($tum_oauth->http_code) { case 200: $url = 'https://www.plurk.com/OAuth/authorize?oauth_token='.$options['oAuthToken']; nxs_save_glbNtwrks($ntInfo['lcode'],$_GET['acc'],$options,'*');
                echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$url.'"</script>'; break; 
                default: echo '<br/><b style="color:red">Could not connect to Plurk. Refresh the page or try again later.</b>'; die();
              }
              die();
            }
   if ( isset($_GET['auth']) && $_GET['auth']=='pka'){ require_once('apis/plurkOAuth.php'); $options = $this->nt[$_GET['acc']];
              $consumer_key = $options['appKey']; $consumer_secret = $options['appSec']; prr($options, 'OPTS:');
              $tum_oauth = new wpPlurkOAuth($consumer_key, $consumer_secret, $options['oAuthToken'], $options['oAuthTokenSecret']); //prr($tum_oauth);
              $access_token = $tum_oauth->getAccToken($_GET['oauth_verifier']); prr($access_token);
              $options['accessToken'] = $access_token['oauth_token'];  $options['accessTokenSec'] = $access_token['oauth_token_secret'];              
              nxs_save_glbNtwrks($ntInfo['lcode'],$_GET['acc'],$options,'*');
              $tum_oauth = new wpPlurkOAuth($consumer_key, $consumer_secret, $options['accessToken'], $options['accessTokenSec']); 
              $uinfo = $tum_oauth->makeReq('https://www.plurk.com/APP/Profile/getOwnProfile', $params); 
              if (is_array($uinfo) && isset($uinfo['user_info'])) $userinfo = $uinfo['user_info']['display_name'];
              if (empty($userinfo) && is_array($uinfo) && isset($uinfo['user_info'])) $userinfo = $uinfo['user_info']['nick_name'];  $options['pgID'] = $userinfo; 
              nxs_save_glbNtwrks($ntInfo['lcode'],$_GET['acc'],$options,'*');
              if ($options['pgID']!='') {  
                  $gGet = $_GET; unset($gGet['auth']); unset($gGet['acc']); unset($gGet['oauth_token']);  unset($gGet['oauth_verifier']); unset($gGet['post_type']);
                  $sturl = explode('?',$nxs_snapSetPgURL); $nxs_snapSetPgURL = $sturl[0].((!empty($gGet))?'?'.http_build_query($gGet):'');
                  echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$nxs_snapSetPgURL.'"</script>'; die();
              }
                else die("<span style='color:red;'>ERROR: Authorization Error: <span style='color:darkred; font-weight: bold;'>".$options['pgID']."</span></span>");              
            }  
  }    
  
  function accTab($ii, $options, $isNew=false){ global $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; ?>    
    <div style="width:100%;"><strong><?php _e('Where to Post', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong><i><?php _e('Your Plurk URL', 'social-networks-auto-poster-facebook-twitter-g'); ?></i></div><input name="<?php echo $nt; ?>[<?php echo $ii; ?>][pkURL]" style="width: 30%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['pkURL'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>
    <?php $this->elemKeySecret($ii,'App Key','App Secret', $options['appKey'], $options['appSec'],'appKey','appSec','https://www.plurk.com/PlurkApp/'); ?>    
    <div style=""> <div style="width:100%;"><strong id="altFormatText">Plurk prefix:</strong> </div>  
      <select name="pk[<?php echo $ii; ?>][pkCat]" id="pkCat<?php echo $ii; ?>">
        <?php  $pkCats = $this->pkCats(); if (isset($options['pkCat']) && $options['pkCat']!='') $pkCats = str_replace($options['pkCat'].'"', $options['pkCat'].'" selected="selected"', $pkCats);  echo $pkCats; ?>
      </select>            
    </div>      
    <div style="margin: 0px;"><input value="1"  id="apLIAttch" type="checkbox" name="pk[<?php echo $ii; ?>][attchImg]"  <?php if ((int)$options['attchImg'] == 1) echo "checked"; ?> /> <strong>Attach Image to Plurk Post</strong></div>
    <?php $this->elemMsgFormat($ii,'Post Format','msgFormat',$options['msgFormat']); ?>
    
    <br/><br/>
    <?php 
            if($options['appSec']=='') { ?>
            <b>Authorize Your Plurk Account</b>. Please save your settings and come back here to Authorize your account.
            <?php } else { if (!empty($options['accessToken']) && $options['accessTokenSec']!=='') { ?>
            Your Plurk Account has been authorized. Your display name: <?php _e(apply_filters('format_to_edit', htmlentities($options['pgID'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>. 
            You can Re- <?php } ?>            
            <a href="<?php echo $nxs_snapSetPgURL.(stripos($nxs_snapSetPgURL, '?')!==false?'&':'?');?>auth=pk&acc=<?php echo $ii; ?>">Authorize Your Plurk Account</a> 
              <?php if (empty($options['oAuthTokenSecret'])) { ?> <div class="blnkg">&lt;=== Authorize your account ===</div> <?php } ?>            
            <?php }  ?>                        
            <br/><br/><?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['appKey']) && !empty($pval['appKey'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
        //## Uniqe Items                
        if (isset($pval['pkCat'])) $options[$ii]['pkCat'] = trim($pval['pkCat']);
        if (isset($pval['attchImg'])) $options[$ii]['attchImg'] = $pval['attchImg']; else $options[$ii]['attchImg'] = 0;        
        if (isset($pval['pkURL']))  {   $options[$ii]['pkURL'] = trim($pval['pkURL']);  if ( substr($options[$ii]['pkURL'], 0, 4)!='http' )  $options[$ii]['pkURL'] = 'http://'.$options[$ii]['pkURL'];
            $pkPgID = $options[$ii]['pkURL']; if (substr($pkPgID, -1)=='/') $pkPgID = substr($pkPgID, 0, -1);  $pkPgID = substr(strrchr($pkPgID, "/"), 1);
            $options[$ii]['pgID'] = $pkPgID; 
        }                        
      } elseif ( count($pval)==1 ) if (isset($pval['do'])) $options[$ii]['do'] = $pval['do']; else $options[$ii]['do'] = 0; 
    } return $options;
  }
    
  //#### Show Post->Edit Meta Box Settings
  
  function showEdPostNTSettings($ntOpts, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code'];
      foreach($ntOpts as $ii=>$ntOpt)  { $isFin = $this->checkIfSetupFinished($ntOpt); if (!$isFin) continue; 
        $pMeta = maybe_unserialize(get_post_meta($post_id, 'snap'.$ntU, true)); if (is_array($pMeta) && !empty($pMeta[$ii])) $ntOpt = $this->adjMetaOpt($ntOpt, $pMeta[$ii]);         
        
        if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
        $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
        $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse']; $ntOpt['ii']=$ii; $this->nxs_tmpltAddPostMeta($post, $ntOpt, $pMeta);         
        $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);            
        nxs_showImgToUseDlg($nt, $ii, $imgToUse);            
       /* ## Select Image & URL ## */  nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);     
     }
  }
  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);     
    if (!empty($pMeta['attchImg'])) $optMt['attchImg'] = $pMeta['attchImg']; else $optMt['attchImg'] = 0;          
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ 
    if (!empty($postID)) { if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'full');
      if (preg_match("/noImg.\.png/i", $imgURL)) { $imgURL = ''; $isNoImg = true; }
      $message['imageURL'] = $imgURL;
    }
  }   
  
}}

if (!function_exists("nxs_doPublishToPK")) { function nxs_doPublishToPK($postID, $options){ 
    if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassPK(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}} 

?>