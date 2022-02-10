<?php    
//## NextScripts App.net Connection Class
$nxs_snapAvNts[] = array('code'=>'SC', 'lcode'=>'sc', 'name'=>'Scoop.It', 'type'=>'Blogs/Publishing Platforms', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Autopost to your "Topics". Ability to attach your blogpost to scoop. Ability to make "Image" posts');

if (!class_exists("nxs_snapClassSC")) { class nxs_snapClassSC extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'SC', 'lcode'=>'sc', 'name'=>'Scoop.It', 'defNName'=>'', 'tstReq' => true, 'instrURL'=>'http://www.nextscripts.com/instructions/scoopit-social-networks-auto-poster-setup-installation/');      
  //#### Update
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName'];  
        $ntOptsOut['msgTFormat'] = $ntOpts['msgTFrmt']; $ntOptsOut['msgFormat'] = $ntOpts['msgFrmt']; $ntOptsOut['appKey'] = $ntOpts['appKey'];  $ntOptsOut['appSec'] = $ntOpts['appSec']; 
        $ntOptsOut['accessToken'] = $ntOpts['accessToken']; $ntOptsOut['accessTokenSec'] = $ntOpts['accessTokenSec']; $ntOptsOut['oAuthToken'] = $ntOpts['oAuthToken']; $ntOptsOut['oAuthTokenSecret'] = $ntOpts['oAuthTokenSecret'];
        $ntOptsOut['topicURL'] = $ntOpts['topicURL'];$ntOptsOut['inclTags'] = $ntOpts['inclTags']; $ntOptsOut['postType'] = $ntOpts['postType'];               
        $ntOptsOut['appAppUserID'] = $ntOpts['appAppUserID']; $ntOptsOut['appAppUserName'] = $ntOpts['appAppUserName'];  
        $ntOptsOut['isUpdd'] = '1'; $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  }   
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts;  $this->showNTGroup(); }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'appKey'=>'', 'appSec'=>'', 'inclTags'=>1, 'msgFormat'=>"%EXCERPT% \r\n\r\n%URL%", 'msgTFormat'=>"%TITLE%", 'imgSize'=>'original', 'topicURL'=>'', 'postType'=>'A'); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['appAppUserID']) && !empty($options['accessToken']); }
  public function doAuth() { $ntInfo = $this->ntInfo; global $nxs_snapSetPgURL;     
    if ( isset($_GET['auth']) && $_GET['auth']==$ntInfo['lcode']){ require_once('apis/scOAuth.php'); $options = $this->nt[$_GET['acc']];
              
              $consumer_key = $options['appKey']; $consumer_secret = $options['appSec'];
              $callback_url = $nxs_snapSetPgURL."&auth=".$ntInfo['lcode']."a&acc=".$_GET['acc'];
             
              $tum_oauth = new wpScoopITOAuth($consumer_key, $consumer_secret); 
              $request_token = $tum_oauth->getReqToken($callback_url); 
              $options['oAuthToken'] = $request_token['oauth_token'];
              $options['oAuthTokenSecret'] = $request_token['oauth_token_secret'];

              //prr($tum_oauth); prr($options); die();
              
              switch ($tum_oauth->http_code) { case 200: $url = 'http://www.scoop.it/oauth/authorize?oauth_token='.$options['oAuthToken']; 
                $optionsG = get_option('NS_SNAutoPoster'); $optionsG[$ntInfo['lcode']][$_GET['acc']] = $options;  update_option('NS_SNAutoPoster', $optionsG);
                echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$url.'"</script>'; break; 
                default: echo '<br/><b style="color:red">Could not connect to ScoopIT. Refresh the page or try again later.</b>'; die();
              }
              die();
            }
    if ( isset($_GET['auth']) && $_GET['auth']==$ntInfo['lcode'].'a'){ require_once('apis/scOAuth.php'); $options = $this->nt[$_GET['acc']];
              $consumer_key = $options['appKey']; $consumer_secret = $options['appSec'];
            
              $tum_oauth = new wpScoopITOAuth($consumer_key, $consumer_secret, $options['oAuthToken'], $options['oAuthTokenSecret']); //prr($tum_oauth);
              $access_token = $tum_oauth->getAccToken($_GET['oauth_verifier']); prr($access_token);
              $options['accessToken'] = $access_token['oauth_token'];  $options['accessTokenSec'] = $access_token['oauth_token_secret'];              
              $optionsG = get_option('NS_SNAutoPoster'); $optionsG[$ntInfo['lcode']][$_GET['acc']] = $options;  update_option('NS_SNAutoPoster', $optionsG);              
              $tum_oauth = new wpScoopITOAuth($consumer_key, $consumer_secret, $options['accessToken'], $options['accessTokenSec']);               
              $uinfo = $tum_oauth->makeReq('http://www.scoop.it/api/1/profile', ''); 
              if (is_array($uinfo) && isset($uinfo['user'])) { $options['appAppUserName'] = $uinfo['user']['name']."(".$uinfo['user']['shortName'].")";                            
                $options['appAppUserID'] = $uinfo['user']['id']; $optionsG = get_option('NS_SNAutoPoster'); $optionsG[$ntInfo['lcode']][$_GET['acc']] = $options;  update_option('NS_SNAutoPoster', $optionsG);
              } //die();
              if (!empty($options['appAppUserID'])) {  echo '<br/><br/>All good?! Redirecting ..... <script type="text/javascript">window.location = "'.$nxs_snapSetPgURL.'"</script>'; die();}
                else die("<span style='color:red;'>ERROR: Authorization Error: <span style='color:darkred; font-weight: bold;'>".print_r($uinfo, true)."</span></span>");              
            } 
  }    
  
  function accTab($ii, $options, $isNew=false){ global $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; $this->elemKeySecret($ii,'Consumer Key','Consumer Secret', $options['appKey'], $options['appSec'],'appKey','appSec','https://www.scoop.it/dev/apps'); ?>    
    <div style="width:100%;"><strong><?php echo $ntInfo['name']; ?> Topic URL:</strong> </div>http://www.scoop.it/t/<input name="<?php echo $nt; ?>[<?php echo $ii; ?>][topicURL]"  style="width: 20%;" value="<?php _e(apply_filters('format_to_edit', htmlentities($options['topicURL'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /><br/><br/>
    
    <?php $this->elemTitleFormat($ii,'Post Title Format','msgTFormat',$options['msgTFormat']);   $this->elemMsgFormat($ii,'Post Format','msgFormat',$options['msgFormat']); ?>
    <div style="margin: 0px;"><input value="1" type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][inclTags]"  <?php if ((int)$options['inclTags'] == 1) echo "checked"; ?> /> <strong><?php _e('Post with tags', 'social-networks-auto-poster-facebook-twitter-g'); ?></strong></div>
    
    <div style="width:100%;"><strong id="altFormatText">Post Type:</strong></div>                      
    <div style="margin-left: 10px;">
       <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="T" <?php if ($options['postType'] == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>                    
       <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="I" <?php if ($options['postType'] == 'I') echo 'checked="checked"'; ?> /> <?php _e('Image Post', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('big image with text message', 'social-networks-auto-poster-facebook-twitter-g'); ?></i><br/>
       <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="A" <?php if ( !isset($options['postType']) || $options['postType'] == '' || $options['postType'] == 'A') echo 'checked="checked"'; ?> /> <?php _e('Add blogpost to message as an attachment', 'social-networks-auto-poster-facebook-twitter-g'); ?><br/>
    </div>
    
    <br/><br/>
<?php  if($options['appKey']=='') { ?>
            <b><?php _e('Authorize Your '.$ntInfo['name'].' Account', 'social-networks-auto-poster-facebook-twitter-g'); ?></b> <?php _e('Please click "Update Settings" to be able to Authorize your account.', 'social-networks-auto-poster-facebook-twitter-g'); ?>
            <?php } else { if(isset($options['appAppUserID']) && $options['appAppUserID']>0) { ?>
            <?php _e('Your '.$ntInfo['name'].' Account has been authorized.', 'social-networks-auto-poster-facebook-twitter-g'); ?> User ID: <?php _e(apply_filters('format_to_edit', htmlentities($options['appAppUserID'].' - '.$options['appAppUserName'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>.
            <?php _e('You can', 'social-networks-auto-poster-facebook-twitter-g'); ?> Re- <?php } ?>            
            <a href="<?php echo $nxs_snapSetPgURL;?>&auth=<?php echo $nt; ?>&acc=<?php echo $ii; ?>">Authorize Your <?php echo $ntInfo['name']; ?> Account</a> 
            
            <?php if (!isset($options['appAppUserID']) || $options['appAppUserID']<1) { ?> <div class="blnkg">&lt;=== <?php _e('Authorize your account', 'social-networks-auto-poster-facebook-twitter-g'); ?> ===</div> <?php }?>
            <?php } ?>
            <br/><br/><?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ 
    foreach ($post as $ii => $pval){       
      if (!empty($pval['appKey']) && !empty($pval['appKey'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
        //## Uniqe Items        
        if (isset($pval['topicURL'])) $options[$ii]['topicURL'] = trim($pval['topicURL']);
        if (isset($pval['postType'])) $options[$ii]['postType'] = trim($pval['postType']);
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
        $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat);          
        $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat);      
        ?>
         <tr style="<?php echo !empty($ntOpt['do'])?'display:table-row;':'display:none;'; ?>" class="nxstbldo nxstbldo<?php echo strtoupper($nt).$ii; ?>"><th scope="row" style="text-align:right; width:150px; vertical-align:top; padding-top: 0px; padding-right:10px;"> <?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g') ?> <br/></th><td>     
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="T" <?php if ($postType == 'T') echo 'checked="checked"'; ?> /> <?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g') ?>  - <i><?php _e('just text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="I" <?php if ($postType == 'I') echo 'checked="checked"'; ?> /> <?php _e('Post as "Image post"', 'social-networks-auto-poster-facebook-twitter-g') ?> - <i><?php _e('big image with text message', 'social-networks-auto-poster-facebook-twitter-g') ?></i><br/>             
        <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][postType]" value="A" <?php if ( !isset($postType) || $postType == '' || $postType == 'A') echo 'checked="checked"'; ?> /><?php _e('Text Post with "attached" blogpost', 'social-networks-auto-poster-facebook-twitter-g') ?>
     </td></tr>
        <?php
              
        nxs_showImgToUseDlg($nt, $ii, $imgToUse);            
       /* ## Select Image & URL ## */  nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);     
     }
  }
  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);     
    if (!empty($pMeta['postType'])) $optMt['postType'] = $pMeta['postType'];
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ 
    if (!empty($postID)) { $postType = $options['postType'];
      if ($postType=='A') if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'medium');  
      if ($postType=='I') if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'full');      
      if (preg_match("/noImg.\.png/i", $imgURL)) { $imgURL = ''; $isNoImg = true; }
      $message['imageURL'] = $imgURL;
    }
  }   
  
}}

if (!function_exists("nxs_doPublishToSC")) { function nxs_doPublishToSC($postID, $options){ 
    if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassSC(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}} 

?>