<?php    
//## NextScripts Tumblr Connection Class
$nxs_snapAvNts[] = array('code'=>'TR', 'lcode'=>'tr', 'name'=>'Tumblr', 'type'=>'Blogs/Publishing Platforms', 'ptype'=>'F', 'status'=>'A', 'desc'=>'Create a text post, image post, audio or video post on your Tumbler blog. HTML is supported.');

if (!class_exists("nxs_snapClassTR")) { class nxs_snapClassTR extends nxs_snapClassNT { 
  var $ntInfo = array('code'=>'TR', 'lcode'=>'tr', 'name'=>'Tumblr', 'defNName'=>'', 'tstReq' => true, 'instrURL'=>'http://www.nextscripts.com/instructions/tumblr-auto-poster-setup-installation/');      
  //#### Update
  function toLatestVer($ntOpts){ if( !empty($ntOpts['v'])) $v = $ntOpts['v']; else $v = 340; $ntOptsOut = '';  switch ($v) {
      case 340: $ntOptsOut = $this->toLatestVerNTGen($ntOpts); $ntOptsOut['do'] = $ntOpts['do'.$this->ntInfo['code']]; $ntOptsOut['nName'] = $ntOpts['nName']; // prr($ntOpts);
        if (!empty($ntOpts['trMsgFormat'])) $ntOptsOut['msgFormat'] = $ntOpts['trMsgFormat']; if (!empty($ntOpts['trMsgTFormat'])) $ntOptsOut['msgTFormat'] = $ntOpts['trMsgTFormat'];  
        if (!empty($ntOpts['trConsKey'])) $ntOptsOut['appKey'] = $ntOpts['trConsKey'];  if (!empty($ntOpts['trConsSec'])) $ntOptsOut['appSec'] = $ntOpts['trConsSec'];         
        $ntOptsOut['accessToken'] = !empty($ntOpts['trAccessTocken'])?$ntOpts['trAccessTocken']['oauth_token']:''; $ntOptsOut['accessTokenSec'] = !empty($ntOpts['trAccessTocken'])?$ntOpts['trAccessTocken']['oauth_token_secret']:''; 
        $ntOptsOut['oAuthToken'] = !empty($ntOpts['trOAuthToken'])?$ntOpts['trOAuthToken']:''; $ntOptsOut['oAuthTokenSecret'] = !empty($ntOpts['trOAuthTokenSecret'])?$ntOpts['trOAuthTokenSecret']:'';        
        $ntOptsOut['cImgURL'] = $ntOpts['cImgURL']; $ntOptsOut['trURL'] = $ntOpts['trURL']; $ntOptsOut['postDate'] = !empty($ntOpts['postDate'])?$ntOpts['postDate']:''; $ntOptsOut['postType'] = $ntOpts['trPostType']; $ntOptsOut['pgID'] = $ntOpts['trPgID'];
        $ntOptsOut['imgSize'] = !empty($ntOpts['imgSize'])?$ntOpts['imgSize']:''; $ntOptsOut['defImg'] = $ntOpts['trDefImg']; $ntOptsOut['inclCats'] = $ntOpts['trInclCats']; 
        $ntOptsOut['inclTags'] = $ntOpts['trInclTags'];  $ntOptsOut['useOrDate'] = !empty($ntOpts['useOrDate'])?$ntOpts['useOrDate']:''; $ntOptsOut['fillSrcURL'] = !empty($ntOpts['fillSrcURL'])?$ntOpts['fillSrcURL']:''; 
        $ntOptsOut['isUpdd'] = '1'; $ntOptsOut['v'] = NXS_SETV;
      break;
    }
    return !empty($ntOptsOut)?$ntOptsOut:$ntOpts; 
  }  
   
  //#### Show Common Settings
  function showGenNTSettings($ntOpts){ $this->nt = $ntOpts;  $this->showNTGroup(); }  
  //#### Show NEW Settings Page
  function showNewNTSettings($ii){ $defO = array('nName'=>'', 'do'=>'1', 'appKey'=>'', 'appSec'=>'',  'fillSrcURL'=>'1', 'useOrDate'=>'1', 'inclTags'=>'1', 'inclCats'=>'', 'cImgURL'=>'R', 'defImg'=>"", 'msgTFormat'=>"%TITLE%", 'msgFormat'=>"<p>New Post has been published on %URL%</p>\r\n<blockquote><p><strong>%TITLE%</strong></p>\r\n<p><img src=\"%IMG%\"/></p><p>%FULLTEXT%</p></blockquote>",  'trURL'=>'', 'postDate'=>'', 'imgSize'=>'', 'postType'=>''); $this->showGNewNTSettings($ii, $defO); }
  //#### Show Unit  Settings  
  function checkIfSetupFinished($options) { return !empty($options['pgID']) && !empty($options['accessToken']); }
  public function doAuth() { $ntInfo = $this->ntInfo; global $nxs_snapSetPgURL;     
    if ( isset($_GET['auth']) && $_GET['auth']=='tr'){ $this->showAuthTop(); require_once('apis/trOAuth.php'); $options = $this->nt[$_GET['acc']]; 
      $consumer_key = $options['appKey']; $consumer_secret = $options['appSec']; $callback_url = $nxs_snapSetPgURL."&auth=tra&acc=".$_GET['acc'];
      $tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret); prr($tum_oauth ); $request_token = $tum_oauth->getRequestToken($callback_url); echo "####"; prr($request_token);
      $options['oAuthToken'] = $request_token['oauth_token']; $options['oAuthTokenSecret'] = $request_token['oauth_token_secret'];// prr($tum_oauth ); die();
      switch ($tum_oauth->http_code) { case 200: $url = $tum_oauth->getAuthorizeURL($options['oAuthToken']); nxs_save_glbNtwrks($ntInfo['lcode'],$_GET['acc'],$options,'*');
        echo '<div style="text-align:center;color:green; font-weight: bold; font-size:20px;" >ALL OK. Redirecting to authorization....</div><script type="text/javascript">setTimeout(function(){ window.location = "'.$url.'"; }, 1000);</script>'; break; 
        default: echo '<br/><b style="color:red">Could not connect to Tumblr. Refresh the page or try again later.</b>'; die('</div></div>');
      } die('</div></div>');
    }
    if ( isset($_GET['auth']) && $_GET['auth']=='tra'){ $this->showAuthTop(); require_once('apis/trOAuth.php'); $options = $this->nt[$_GET['acc']]; prr($options);
      $consumer_key = $options['appKey']; $consumer_secret = $options['appSec'];  
      $tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret, $options['oAuthToken'], $options['oAuthTokenSecret']); 
      $options['accessToken'] = $tum_oauth->getAccessToken($_REQUEST['oauth_verifier']);  prr($tum_oauth, '**tum_oauth ==1== **');  
      prr($options['accessToken'], '**GOT ACCESS TOKEN **'); $options['accessTokenSec'] =  $options['accessToken']['oauth_token_secret']; $options['accessToken'] =  $options['accessToken']['oauth_token'];
      $tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret,  $options['accessToken'], $options['accessTokenSec']);            
      $userinfo = $tum_oauth->get('http://api.tumblr.com/v2/user/info'); prr($tum_oauth, '**tum_oauth ==2== **'); prr($userinfo, '**USERINFO**'); // prr($url); die();
      if ($userinfo->meta->status=='401') die("<span style='color:red;'>ERROR #1: Authorized USER don't have access to the specified blog: <span style='color:darkred; font-weight: bold;'>".$options['pgID']."</span></span></div>");
      if (is_array($userinfo->response->user->blogs)) { $options['authUser'] = $userinfo->response->user->name; $blogs = ''; $opNm = 'nxs_snap_tr_'.sha1('nxs_snap_tr'.$options['authUser'].$options['appKey']);
        foreach ($userinfo->response->user->blogs as $blog){ if (!empty($blog->uuid)) $uuid = $blog->uuid; else $uuid = rtrim(str_ireplace('http://','',str_ireplace('https://','',$blog->url)), '/');  if (empty($options['pgID'])) $options['pgID'] = $uuid;
          $blogs .= '<option '.($options['pgID']==$uuid ? 'selected="selected"':'').' value="'.$uuid.'">'.$blog->name.' ('.$uuid.')</option>'; 
        } nxs_save_glbNtwrks($ntInfo['lcode'],$_GET['acc'],$options,'*'); $opVal['blogList'] = $blogs;  nxs_saveOption($opNm, $opVal);       
        echo '<div style="text-align:center;color:green; font-weight: bold; font-size:22px;" >ALL OK. You have been authorized. Refreshing page....</div><script type="text/javascript">setTimeout(function(){ window.location = "'.$nxs_snapSetPgURL.'"; }, 3000);</script>'; die('</div></div>');
      } prr($userinfo); die("<span style='color:red;'>ERROR #2: Authorized USER don't have access to the specified blog: <span style='color:darkred; font-weight: bold;'>".$options['pgID']."</span></span></div></div>");     
    }  
  }    
  
  function getListOfBlogs($networks){ $opVal = array(); $opNm = 'nxs_snap_tr_'.sha1('nxs_snap_tr'.$_POST['u'].$_POST['p']); $opVal = nxs_getOption($opNm); $ii = $_POST['ii']; 
     $currPstAs = !empty($_POST['cBlog'])?$_POST['cBlog']:(!empty($networks['tr'][$ii])?$networks['tr'][$ii]['pgID']:'');
     if (empty($_POST['force']) && !empty($opVal['blogList']) ) $pgs = $opVal['blogList']; else { $options = $networks['tr'][$ii]; require_once('apis/trOAuth.php'); 
       $tum_oauth = new TumblrOAuth($options['appKey'], $options['appSec'],  $options['accessToken'], $options['accessTokenSec']); $userinfo = $tum_oauth->get('http://api.tumblr.com/v2/user/info');// prr($userinfo);
       if ($userinfo->meta->status=='401') { $outMsg = '<b style="color:red;">'.__('Auth Problem').' HTTP-401 (Not Authorized) &nbsp;-&nbsp;</b>'; if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
       if (is_array($userinfo->response->user->blogs)) { $options['authUser'] = $userinfo->response->user->name; $pgs = ''; 
        foreach ($userinfo->response->user->blogs as $blog){ if (!empty($blog->uuid)) $uuid = $blog->uuid; else $uuid = rtrim(str_ireplace('http://','',str_ireplace('https://','',$blog->url)), '/'); 
          $res = nxs_remote_get( "https://api.tumblr.com/v2/blog/".$uuid."/info?api_key=".$options['appKey'], nxs_mkRemOptsArr(nxs_getNXSHeaders())); $replRet = json_decode($res['body'], true); 
          if (!empty($replRet) && !empty($replRet['meta']) && $replRet['meta']['status']=='200') $pgs .= '<option '.($options['pgID']==$uuid ? 'selected="selected"':'').' value="'.$uuid.'">'.$blog->name.' ('.$uuid.')</option>';       
        }
       } else { $outMsg = '<b style="color:red;">'.__('Auth Problem').' #2&nbsp;-&nbsp;'.$loginError.'</b>'; if (!empty($_POST['isOut'])) echo $outMsg; return $outMsg; }
     } $pgCust = (!empty($pgs) && !empty($currPstAs) && stripos($pgs,$currPstAs)===false)?'<option selected="selected" value="'.$currPstAs.'">'.$currPstAs.'</option>':'';     
     if (!empty($_POST['isOut'])) echo $pgCust.$pgs.'<option style="color:#BD5200" value="a">'.__('...enter the Blog ID').'</option>'; // .'<option style="color:#BD5200" value="a">'.__('...enter the SubReddit ID').'</option>';
     $opVal['blogList'] = $pgs; nxs_saveOption($opNm, $opVal); return $opVal;
  }
  
  function accTab($ii, $options, $isNew=false){ global $nxs_snapSetPgURL; $ntInfo = $this->ntInfo; $nt = $ntInfo['lcode']; if (empty($options['authUser'])) $options['authUser'] = ''; // prr($options); 
    $opNm = 'nxs_snap_tr_'.sha1('nxs_snap_tr'.$options['authUser'].$options['appKey']); $opVal = nxs_getOption($opNm); 
    if (empty($opVal) && !empty($options['authUser'])) { 
      $tPST = (!empty($_POST))?$_POST:'';  $_POST['cBlog'] = $options['pgID']; $_POST['u'] = $options['authUser']; $_POST['p'] = $options['appKey']; $_POST['ii'] = $ii; $ntw[$nt][$ii]=$options; $opVal = $this->getListOfBlogs($ntw); $_POST = $tPST; 
    } if (!empty($opVal) & !is_array($opVal)) $options['uMsg'] = $opVal; else { if (!empty($opVal) & is_array($opVal)) $options = array_merge($options, $opVal); } 
  ?><br/ ><div style="width:100%;"><b><?php _e('Where to Post', 'nxs_snap'); ?></b>&nbsp;(<?php _e('Please select your blog', 'nxs_snap'); ?>)</div>
    <?php if (!empty($options['authUser'])) {?>
    <div id="nxsTRInfoDiv<?php echo $ii; ?>">
         <div style="width:100%;">
          <div>                   
          <select id="trpgID<?php echo $ii; ?>" onchange="nxs_trBlogChange('<?php echo $ii;?>',jQuery(this));" name="tr[<?php echo $ii;?>][pgID]">
            <?php $pgi = !empty($options['blogList'])?$options['blogList']:''; 
              if (!empty($options['pgID'])) { echo (!empty($options['pgID']) && stripos($pgi,$options['pgID'])===false)?'<option selected="selected" value="'.$options['pgID'].'">'.$options['pgID'].'</option>':''; }            
              if (!empty($options['pgID'])) { $pgi = str_ireplace('selected="selected" ','',$pgi); $pgi = str_ireplace('value="'.$options['pgID'].'"','selected="selected" value="'.$options['pgID'].'"',$pgi); } echo $pgi;
            ?><option value="a"><?php _e('.... Enter the Blog ID'); ?></option>
          </select><div id="nxsTRInfoDivBlock<?php echo $ii; ?>" style="display: inline-block;"> <input type="text" style="display: none;" id="trInpCst<?php echo $ii; ?>" value="<?php echo $options['pgID']; ?>" onchange="nxs_InpToDDChange(jQuery(this));" data-tid="trpgID<?php echo $ii; ?>" />         
          <div style="display: inline-block;"><a onclick="nxs_trGetBlogs(<?php echo $ii;?>, 1); jQuery(this).blur(); return false;" href="#"><img id="<?php echo $nt.$ii;?>rfrshImg" style="vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/refresh16.png' /></a></div></div> <img id="<?php echo $nt.$ii;?>ldImg" style="display: none;vertical-align: middle;" src='<?php echo NXS_PLURL; ?>img/ajax-loader-sm.gif' />
          </div>          
          </div> <div id="nxsTRMsgDiv<?php echo $ii; ?>"><?php if (!empty($options['uMsg'])) echo $options['uMsg']; ?><?php if ($isNew) { ?><?php _e('Please authorize your account', 'nxs_snap'); ?><?php } ?></div>                                                                                                    
    </div> <?php } ?> <input type="hidden" id="trAuthUser<?php echo $ii; ?>" value="<?php echo $options['authUser']; ?>"/> <br/><br/> 
    
    <?php $this->elemKeySecret($ii,'OAuth Consumer Key','Secret Key', $options['appKey'], $options['appSec'],'appKey','appSec','http://www.tumblr.com/oauth/apps'); ?>    
    
    <div style="width:100%;"><strong id="altFormatText">Default Post Type:</strong></div>                      
<div style="margin-left: 10px;">
    
    <input type="radio" name="tr[<?php echo $ii; ?>][postType]" value="T" <?php if ($options['postType'] != 'I') echo 'checked="checked"'; ?> onchange="nxs_TRSetEnable('T','<?php echo $ii; ?>');" /> Text Post<br/>            

    <div style="width:100%; margin-left: 15px;"><strong id="altFormatText"><?php _e('Post Title Format', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</strong> (<a href="#" id="apTRTMsgFrmt<?php echo $ii; ?>HintInfo" onclick="mxs_showHideFrmtInfo('apTRTMsgFrmt<?php echo $ii; ?>'); return false;"><?php _e('Show format info', 'social-networks-auto-poster-facebook-twitter-g'); ?></a>)  </div><div onblur="jQuery('#apTRMsgFrmt<?php echo $ii; ?>Hint').hide();">
              <input name="tr[<?php echo $ii; ?>][msgTFormat]" id="apTRMsgTFrmt<?php echo $ii; ?>" style="margin-left: 15px; width: 50%;" value="<?php if ($options['msgTFormat']!='') _e(apply_filters('format_to_edit', htmlentities($options['msgTFormat'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g'); else echo "New Post has been published on %SITENAME%"; ?>"  onfocus="jQuery('#apTRTMsgFrmt<?php echo $ii; ?>Hint').show();"  <?php if ($options['postType'] == 'I') echo 'disabled="disabled"'; ?>  /><br/>
              <?php nxs_doShowHint("apTRTMsgFrmt".$ii); ?>
            </div>
            
<input type="radio" name="tr[<?php echo $ii; ?>][postType]" value="I" <?php if ($options['postType'] == 'I') echo 'checked="checked"'; ?> onchange="nxs_TRSetEnable('I','<?php echo $ii; ?>');"/> Image Post
<i>Don't forget to change default "Post Text Format" to prevent duplicate images.</i><br/>

<div style="width:100%; margin-left: 15px;">

<strong>Clickthrough URL:</strong> 
<div style="margin-bottom: 20px;margin-top: 5px;">
<input type="radio" name="tr[<?php echo $ii; ?>][cImgURL]" value="R" <?php if ( empty($options['cImgURL']) || $options['cImgURL'] == 'R') echo 'checked="checked"'; ?> /> Regular Post URL&nbsp;&nbsp;
<input type="radio" name="tr[<?php echo $ii; ?>][cImgURL]" value="S" <?php if (!empty($options['cImgURL']) && $options['cImgURL'] == 'S') echo 'checked="checked"'; ?> /> Shortened Post URL&nbsp;&nbsp;
<input type="radio" name="tr[<?php echo $ii; ?>][cImgURL]" value="N" <?php if (!empty($options['cImgURL']) && $options['cImgURL'] == 'N') echo 'checked="checked"'; ?> /> No Clickthrough URL&nbsp;&nbsp;
</div><strong>Defailt Image to Post:</strong> 
            <p style="font-size: 11px; margin: 0px;">If your post is missing "Featured Image" and doesn't have any images in the text body this will be used instead.</p>
            </div><input name="tr[<?php echo $ii; ?>][defImg]" id="apTRDefImg<?php echo $ii; ?>" style=" margin-left: 15px; width: 30%;" <?php if ($options['postType'] != 'I') echo 'disabled="disabled"'; ?> value="<?php _e(apply_filters('format_to_edit', htmlentities($options['defImg'], ENT_COMPAT, "UTF-8")), 'social-networks-auto-poster-facebook-twitter-g') ?>" /> 
<br/>            
<input type="radio" name="tr[<?php echo $ii; ?>][postType]" value="U" <?php if ($options['postType'] == 'U') echo 'checked="checked"'; ?> /> Audio Post<br/>
<input type="radio" name="tr[<?php echo $ii; ?>][postType]" value="V" <?php if ($options['postType'] == 'V') echo 'checked="checked"'; ?> /> Video Post<br/>            
<i style="">Tip: Your post must contain link to Audio or Video file if you select "Audio Post" or "Video Post" , otherwise it will reverted to the "Text Post"</i>
            <br/><br/>

</div><?php $this->elemMsgFormat($ii,'Post Format','msgFormat',$options['msgFormat']); ?>
    
    <div style="margin-bottom: 20px;margin-top: 5px;">
              <input value="1" type="checkbox" name="tr[<?php echo $ii; ?>][fillSrcURL]"  <?php if ((int)$options['fillSrcURL'] == 1) echo "checked"; ?> /> 
              <strong>Fill "Source URL"</strong> Will fill Tumblr's "Source URL" with post URL or defined URL.
              
              <br/><input value="1" type="checkbox" name="tr[<?php echo $ii; ?>][useOrDate]"  <?php if ((int)$options['useOrDate'] == 1) echo "checked"; ?> /> 
              <strong>Keep Original Post Date</strong> Will post to Tumblr with original date of the post 
              
              <br/><input value="1" type="checkbox" name="tr[<?php echo $ii; ?>][inclTags]"  <?php if ((int)$options['inclTags'] == 1) echo "checked"; ?> /> 
              <strong>Post with tags.</strong> Tags from the blogpost will be auto posted to Tumblr                                
              
              <br/><input value="1" type="checkbox" name="tr[<?php echo $ii; ?>][inclCats]"  <?php if ((int)$options['inclCats'] == 1) echo "checked"; ?> /> 
              <strong>Post categories as tags.</strong> Categories from the blogpost will be auto posted to Tumblr as tags                                
            </div>
    <br/><br/>
    <?php 
      if($options['appSec']=='') { ?>
            <b>Authorize Your Tumblr Account</b>. Please save your settings and come back here to Authorize your account.
            <?php } else { if(!empty($options['accessToken'])) { ?>
            Your Tumblr Account has been authorized. User ID: <?php echo $options['authUser']; ?>| Blog ID: <?php echo $options['pgID']; ?>. 
            You can Re- <?php } ?>            
            <a href="<?php echo $nxs_snapSetPgURL.(stripos($nxs_snapSetPgURL, '?')!==false?'&':'?');?>auth=tr&acc=<?php echo $ii; ?>">Authorize Your Tumblr Account</a> 
              <?php if (empty($options['accessTokenSec'])) { ?> <div class="blnkg">&lt;=== Authorize your account ===</div> <?php } ?>            
            <?php }  ?>                          
            <br/><br/><?php
  }
  function advTab($ii, $options){}
  //#### Set Unit Settings from POST
  function setNTSettings($post, $options){ //######################## STOPPED HERE
    foreach ($post as $ii => $pval){       
      if (!empty($pval['appKey']) && !empty($pval['appKey'])){ if (!isset($options[$ii])) $options[$ii] = array(); $options[$ii] = $this->saveCommonNTSettings($pval,$options[$ii]);
        //## Uniqe Items                
        if (isset($pval['defImg'])) $options[$ii]['defImg'] = trim($pval['defImg']); else $options[$ii]['defImg'] = ""; 
        if (isset($pval['cImgURL'])) $options[$ii]['cImgURL'] = trim($pval['cImgURL']);
        if (isset($pval['useOrDate'])) $options[$ii]['useOrDate'] = $pval['useOrDate']; else $options[$ii]['useOrDate'] = 0;        
        if (isset($pval['fillSrcURL'])) $options[$ii]['fillSrcURL'] = $pval['fillSrcURL']; else $options[$ii]['fillSrcURL'] = 0;        
        
        if (isset($pval['inclTags'])) $options[$ii]['inclTags'] = $pval['inclTags']; else $options[$ii]['inclTags'] = 0;        
        if (isset($pval['inclCats'])) $options[$ii]['inclCats'] = $pval['inclCats']; else $options[$ii]['inclCats'] = 0;        
        
        if (isset($pval['pgID'])) $options[$ii]['pgID'] = trim($pval['pgID']);        
          elseif (isset($pval['trURL']))  {   $options[$ii]['trURL'] = trim($pval['trURL']);  if ( substr($options[$ii]['trURL'], 0, 4)!='http' )  $options[$ii]['trURL'] = 'http://'.$options[$ii]['trURL'];
            $trPgID = $options[$ii]['trURL']; if (substr($trPgID, -1)=='/') $trPgID = substr($trPgID, 0, -1);  $trPgID = substr(strrchr($trPgID, "/"), 1); $options[$ii]['pgID'] = $trPgID; //echo $fbPgID;
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
        ?>
        
        <tr style="<?php echo !empty($ntOpt['do'])?'display:table-row;':'display:none;'; ?>" class="nxstbldo nxstbldo<?php echo strtoupper($nt).$ii; ?>" style=""><th scope="row" style="text-align:right; width:60px; padding-right:10px;">
                <input type="radio" name="tr[<?php echo $ii; ?>][postType]" value="T" <?php if ($ntOpt['postType'] != 'I') echo 'checked="checked"'; ?>  /> <br/>                
                </th>
                <td style="align:"><table style="width:90%; display: inline-table;"><?php $this->elemEdTitleFormat($ii, __('Text Post. Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat); ?></table> </td></tr>
                
                <tr style="<?php echo !empty($ntOpt['do'])?'display:table-row;':'display:none;'; ?>" class="nxstbldo nxstbldo<?php echo strtoupper($nt).$ii; ?>" style=""><th scope="row" style="text-align:right; width:60px; padding-right:10px;">
                <input type="radio" name="tr[<?php echo $ii; ?>][postType]" value="I" <?php if ($ntOpt['postType'] == 'I') echo 'checked="checked"'; ?>  />  <br/>                
                </th>
                <td><b>Image Post</b>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="tr[<?php echo $ii; ?>][postType]" value="V" <?php if ($ntOpt['postType'] == 'V') echo 'checked="checked"'; ?>  /> <b>Video Post</b> <?php nxs_doShowHint("apTRTMsgFrmt".$ii); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="tr[<?php echo $ii; ?>][postType]" value="U" <?php if ($ntOpt['postType'] == 'U') echo 'checked="checked"'; ?>  /> <b>Audio Post</b> <?php nxs_doShowHint("apTRTMsgFrmt".$ii); ?> 
                </td></tr>
        
        <?php
        nxs_showImgToUseDlg($nt, $ii, $imgToUse);            
       /* ## Select Image & URL ## */  nxs_showURLToUseDlg($nt, $ii, $urlToUse); $this->nxs_tmpltAddPostMetaEnd($ii);     
     }
  }
  function showEdPostNTSettingsV4($ntOpt, $post){ $post_id = $post->ID; $nt = $this->ntInfo['lcode']; $ntU = $this->ntInfo['code']; $ii = $ntOpt['ii']; //prr($ntOpt['postType']);                                                   
       if (empty($ntOpt['imgToUse'])) $ntOpt['imgToUse'] = ''; if (empty($ntOpt['urlToUse'])) $ntOpt['urlToUse'] = ''; $postType = isset($ntOpt['postType'])?$ntOpt['postType']:'';
       $msgFormat = !empty($ntOpt['msgFormat'])?htmlentities($ntOpt['msgFormat'], ENT_COMPAT, "UTF-8"):''; $msgTFormat = !empty($ntOpt['msgTFormat'])?htmlentities($ntOpt['msgTFormat'], ENT_COMPAT, "UTF-8"):'';
       $imgToUse = $ntOpt['imgToUse'];  $urlToUse = $ntOpt['urlToUse']; $ntOpt['ii']=$ii;        
       //## Title and Message       
       $this->elemEdMsgFormat($ii, __('Message Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgFormat); ?>
        
   <div class="nxsPostEd_ElemWrap"> <div class="nxsPostEd_ElemLabel"><?php _e('Post Type:', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>       
     <div class="nxsPostEd_Elem">   
         <input type="radio" name="tr[<?php echo $ii; ?>][postType]" value="T" class="nxsEdElem" <?php if ($ntOpt['postType'] != 'I') echo 'checked="checked"'; ?>  /><?php _e('Text Post', 'social-networks-auto-poster-facebook-twitter-g'); ?>                
         <?php $this->elemEdTitleFormat($ii, __('Title Format:', 'social-networks-auto-poster-facebook-twitter-g'),$msgTFormat); ?>                
         <input type="radio" name="tr[<?php echo $ii; ?>][postType]" value="I" class="nxsEdElem" <?php if ($ntOpt['postType'] == 'I') echo 'checked="checked"'; ?>  /> <b>Image Post</b><br/>
         <input type="radio" name="tr[<?php echo $ii; ?>][postType]" value="V" class="nxsEdElem" <?php if ($ntOpt['postType'] == 'V') echo 'checked="checked"'; ?>  /> <b>Video Post</b><br/>
         <input type="radio" name="tr[<?php echo $ii; ?>][postType]" value="U" class="nxsEdElem" <?php if ($ntOpt['postType'] == 'U') echo 'checked="checked"'; ?>  /> <b>Audio Post</b> 
     </div>
   </div><?php
       // ## Select Image & URL       
       nxs_showImgToUseDlg($nt, $ii, $imgToUse);            
       nxs_showURLToUseDlg($nt, $ii, $urlToUse); 
  }  
  //#### Save Meta Tags to the Post
  function adjMetaOpt($optMt, $pMeta){ $optMt = $this->adjMetaOptG($optMt, $pMeta);     
    
    return $optMt;
  }
  
  function adjPublishWP(&$options, &$message, $postID){ 
    if (!empty($postID)) { if (trim($options['imgToUse'])!='') $imgURL = $options['imgToUse']; else $imgURL = nxs_getPostImage($postID, !empty($options['wpImgSize'])?$options['wpImgSize']:'full');
      if (preg_match("/noImg.\.png/i", $imgURL)) { $imgURL = ''; $isNoImg = true; }
      $message['imageURL'] = $imgURL; $post = get_post($postID);
      $message['postDate'] = (($options['useOrDate']=='1' && $post->post_date_gmt!='0000-00-00 00:00:00')?$post->post_date_gmt:gmdate("Y-m-d H:i:s", strtotime($post->post_date)))." GMT";  //## Adds date to Tumblr post. Thanks to Kenneth Lecky
      
      if($options['postType']=='V') { $vids = nsFindVidsInPost($post); if (count($vids)>0) $ytUrl = $vids[0]; if (trim($ytUrl)=='') $options['postType']='T'; }
      if($options['postType']=='U') { $aud = nsFindAudioInPost($post); if (count($aud)>0) $aUrl = $aud[0]; if (trim($aUrl)=='') $options['postType']='T'; }
      
      $message['tags'] = (!empty($options['inclTags'])?$message['tags']:''). (!empty($options['inclCats']) && !empty($options['inclTags']) && !empty($message['tags']) && !empty($message['cats'])?', ':'').(!empty($options['inclCats'])? $message['cats']:'');
      
    }
  }   
  
}}

if (!function_exists("nxs_doPublishToTR")) { function nxs_doPublishToTR($postID, $options){ 
    if (!is_array($options)) $options = maybe_unserialize(get_post_meta($postID, $options, true)); $cl = new nxs_snapClassTR(); $cl->nt[$options['ii']] = $options; return $cl->publishWP($options['ii'], $postID); 
}} 

?>