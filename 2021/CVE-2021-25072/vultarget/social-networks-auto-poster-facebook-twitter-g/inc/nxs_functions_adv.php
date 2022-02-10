<?php
if (!function_exists("nxs_snapAjax")) { function nxs_snapAjax() {  
  if (empty($_REQUEST['_wpnonce']) && !empty($_REQUEST['nxsSsPageWPN_wpnonce'])) $_REQUEST['_wpnonce'] = $_REQUEST['nxsSsPageWPN_wpnonce'];  
  if (empty($_REQUEST['_wp_http_referer']) && !empty($_REQUEST['nxs_wp_http_referer']) ) $_REQUEST['_wp_http_referer'] = $_REQUEST['nxs_wp_http_referer'];  check_ajax_referer('nxsSsPageWPN'); $arg = ''; nxs_Filters::init(true);
  global $plgn_NS_SNAutoPoster, $nxs_SNAP; if (empty($nxs_SNAP)) $nxs_SNAP = new nxs_SNAP();
  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options; $networks = (!current_user_can( 'manage_options' ) && current_user_can( 'haveown_snap_accss' ) ) ? $nxs_SNAP->nxs_acctsU : $options; //prr($networks, 'NTS');
  
  if (get_magic_quotes_gpc() || (!empty($_POST['nxs_mqTest']) && $_POST['nxs_mqTest']=="\'")) {array_walk_recursive($_POST, 'nsx_stripSlashes');}  array_walk_recursive($_POST, 'nsx_fixSlashes');  unset($_POST['nxs_mqTest']); 
    
  if ($_POST['nxsact']=='getNTset') { $ii = $_POST['ii']; $nt = $_POST['nt']; $ntl = strtolower($nt); $pbo = $networks[$ntl][$ii]; $pbo['ntInfo']['lcode'] = $ntl; $clName = 'nxs_snapClass'.$nt; $ntObj = new $clName(); 
     $ntObj->showNTSettings($ii, $pbo);  
  }
  
  if ($_POST['nxsact']=='svEdFlds') { 
    $cn = str_replace(']','',$_POST['cname']); $cna = explode('[',$cn);  $id = $_POST['pid']; $nt = $cna[0]; $ntU = strtoupper($nt); $ii = $cna[1]; $fname = $cna[2];
    $savedMeta = maybe_unserialize(get_post_meta($id, 'snap'.$ntU, true));  $savedMeta[$ii][$fname] = $_POST['cval']; // prr($savedMeta);
    delete_post_meta($id, 'snap'.$ntU); add_post_meta($id, 'snap'.$ntU, str_replace('\\','\\\\',serialize($savedMeta)));   
  }
  if ($_POST['nxsact']=='getNewPostDlg') nxs_showNewPostForm($options);
  if ($_POST['nxsact']=='doNewPost') nxs_doNewNPPost($options);
  if ($_POST['nxsact']=='nxsCptCheckGP') nxs_CptCheckGP($options);  
  
  if ($_POST['nxsact']=='delNTAcc' && isset($_POST['id'])) { $indx = (int)$_POST['id']; unset($networks[$_POST['nt']][$indx]); $nxs_SNAP->saveNetworksOptions($networks);  nxs_save_ntwrksOpts($networks); die('OK'); }
  
  if ($_POST['nxsact']=='testPost' || $_POST['nxsact']=='manPost') {  $clName = 'nxs_snapClass'.strtoupper($_POST['nt']); $ntClInst = new $clName(); 
     if (method_exists($ntClInst,'ajaxPost')) $ntClInst->ajaxPost(); else { $fNm = 'nxs_rePostTo'.strtoupper($_POST['nt']).'_ajax';  $fNm(); }  
  }
  if ($_POST['nxsact']=='setNTset') { global $nxs_snapAvNts; unset($_POST['action']); unset($_POST['nxsact']); unset($_POST['_wp_http_referer']); unset($_POST['_wpnonce']); //unset($_POST['apDoSFB0']); // Do something    
    foreach ($nxs_snapAvNts as $avNt) if (isset($_POST[$avNt['lcode']])) { $clName = 'nxs_snapClass'.$avNt['code']; if (!isset($networks[$avNt['lcode']])) $networks[$avNt['lcode']] = array(); 
       $ntClInst = new $clName(); $ntOpt = $ntClInst->setNTSettings($_POST[$avNt['lcode']], $networks[$avNt['lcode']]); $networks[$avNt['lcode']] = $ntOpt; //prr($networks, 'NTS II');
    }  nxs_save_ntwrksOpts($networks); //$plgn_NS_SNAutoPoster->nxs_options = $networks; // prr($_POST['lj']);  prr($networks['lj']); 
    die('OK');
  } 
  
  if ($_POST['nxsact']=='nsDupl') { $indx = (int)$_POST['id']; $no = $networks[$_POST['nt']][$indx]; $no['nName'] .= ' [Copy]';    $networks[$_POST['nt']][] = $no;
    if (is_array($networks)) { nxs_save_ntwrksOpts($networks); echo "OK"; }
  }
  
  //## Get somrhting (like Boards or Cats) from NT
  if ($_POST['nxsact']=='getItFromNT') { $ntU = strtoupper($_POST['nt']); $clName = 'nxs_snapClass'.$ntU; $ntObj = new $clName(); $fName = $_POST['fName']; $ntObj->$fName($networks); die();}
  
  
  if ($_POST['nxsact']=='tknzsrch') {$termsOut = array(); $terms = get_terms($_POST['nxtype'], array('orderby'=>'name', 'hide_empty' => 0, 'number'=>10, 'name__like'=> $_POST['srch'] ) ); //$termsOut[] = array('value'=>'-', 'text'=>$_POST['srch'].' [Add]');
     foreach ($terms as $term) $termsOut[] = array('value'=>$term->term_id, 'text'=>$term->name); echo json_encode($termsOut);
  }  
  //## Evil Buttons
  if ($_POST['nxsact']=='resetSNAPInfoPosts') { global $wpdb; $wpdb->query( "DELETE FROM ". $wpdb->postmeta ." WHERE meta_key LIKE 'snap%'" );
      _e('Done. All SNAP data has been removed from posts.', 'social-networks-auto-poster-facebook-twitter-g');
  }
  if ($_POST['nxsact']=='deleteAllSNAPInfo') { global $wpdb; $wpdb->query( "DELETE FROM ". $wpdb->options ." WHERE option_name = 'NS_SNAutoPoster'" ); $wpdb->query( "DELETE FROM ". $wpdb->options ." WHERE option_name = 'NSX_PostsQuery'" ); 
    $wpdb->query( "DELETE FROM ". $wpdb->options ." WHERE option_name = '__plugins_cache_242'" ); $wpdb->query( "DELETE FROM ". $wpdb->options ." WHERE option_name = '__plugins_cache_244'" ); 
    $wpdb->query( "DELETE FROM ". $wpdb->options ." WHERE option_name = 'NS_SNriPosts'" );  $wpdb->query( "DELETE FROM ". $wpdb->postmeta ." WHERE meta_key LIKE 'snap%'" );
      _e('Done. All SNAP data has been removed. <script> setTimeout(function () { location.reload(1); }, 3000); </script>', 'social-networks-auto-poster-facebook-twitter-g');
  } 
  if ($_POST['nxsact']=='restBackup') { $dbOptions = get_option('nxsSNAPNetworks_bck'); update_option('NS_SNAutoPoster', $dbOptions);
      _e('Done. Backup has been restored. <script> setTimeout(function () { location.reload(1); }, 3000); </script>', 'social-networks-auto-poster-facebook-twitter-g');
  }  
  if ($_POST['nxsact']=='resetSNAPQuery') { global $wpdb;  $wpdb->query( "DELETE FROM ". $wpdb->options ." WHERE option_name = 'NSX_PostsQuery'" ); 
      _e('Done. SNAP query has been cleared.', 'social-networks-auto-poster-facebook-twitter-g');
  }
  if ($_POST['nxsact']=='resetSNAPCron') { $cron = maybe_unserialize(get_option('cron')); $cronX = $cron;
    foreach ($cron as $itsk=>$tsk) if (!empty($tsk) && is_array($tsk)) { $nm = key($tsk); if (stripos($nm, 'ns_doPublishTo')!==false) unset($cronX[$itsk]); }  update_option('cron',$cronX);   
    _e('Done. All SNAP Crons has been deleted.', 'social-networks-auto-poster-facebook-twitter-g');
  }
  if ($_POST['nxsact']=='resetSNAPCache') { global $wpdb; $wpdb->query( "DELETE FROM ". $wpdb->options ." WHERE option_name LIKE 'nxs_snap_%'" );
      _e('Done. All SNAP Cache has been cleared.', 'social-networks-auto-poster-facebook-twitter-g');
  } 
   
  do_action( 'nxsajax', $arg );  die();
}}
//## Format Message
if (!function_exists("nsFormatMessage")) { function nsFormatMessage($msg, $postID, $addURLParams='', $lng='', $ntOpts=''){ global $ShownAds, $plgn_NS_SNAutoPoster, $nxs_urlLen; 
  if (defined('DOING_CRON') && empty($GLOBALS['nxswpdone'])) { $GLOBALS['nxswpdone'] = 1; do_action( 'wp' ); }
  $post = get_post($postID); $options = $plgn_NS_SNAutoPoster->nxs_options; if (empty($ntOpts)) $ntOpts = array();
  if (!empty($options['brokenCntFilters'])) { $msg = str_replace('%FULLTITLE%','%TITLE%',$msg); $msg = str_replace('%PANNOUNCE%','%ANNOUNCE%',$msg); $msg = str_replace('%PANNOUNCER%','%ANNOUNCER%',$msg); 
    $msg = str_replace('%EXCERPT%','%RAWEXCERPT%',$msg);  $msg = str_replace('%FULLTEXT%','%RAWTEXT%',$msg);  
  } if (!empty($options['nxsHTSpace'])) $htS = $options['nxsHTSpace']; else $htS = ''; 
  if (!empty($options['nxsHTSepar'])) $htSep = $options['nxsHTSepar']; else $htSep = ', '; $htSep = str_replace('_',' ',$htSep); $htSep = str_replace('c',',',$htSep);
  // if ($addURLParams=='' && $options['addURLParams']!='') $addURLParams = $options['addURLParams'];
  $msg = str_replace('%TEXT%','%EXCERPT%',$msg); $msg = str_replace('%RAWEXTEXT%','%RAWEXCERPT%',$msg);
  $msg = stripcslashes($msg); if (isset($ShownAds)) $ShownAdsL = $ShownAds; // $msg = htmlspecialchars(stripcslashes($msg)); 
  $msg = nxs_doSpin($msg);
  if (preg_match('/%URL%/', $msg)) { $oo=array(); $oo = nxs_getURL($oo, $postID, $addURLParams); $url = $oo['urlToUse']; $nxs_urlLen = nxs_strLen($url); $msg = str_ireplace("%URL%", $url, $msg);}
  if (preg_match('/%SLUG%/', $msg)) { $msg = str_ireplace("%SLUG%", $post->post_name, $msg); }
  if (preg_match('/%MYURL%/', $msg)) { $url =  get_post_meta($postID, 'snap_MYURL', true); if($addURLParams!='') $url .= (strpos($url,'?')!==false?'&':'?').$addURLParams;  $nxs_urlLen = nxs_strLen($url); $msg = str_ireplace("%MYURL%", $url, $msg);}// prr($msg);
  if (preg_match('/%SURL%/', $msg)) { $oo=array(); $oo = nxs_getURL($oo, $postID, $addURLParams); $url = $oo['urlToUse']; $url = nxs_mkShortURL($url, $postID); $nxs_urlLen = nxs_strLen($url); $msg = str_ireplace("%SURL%", $url, $msg);  } 
  if (preg_match('/%ORID%/', $msg)) { $msg = str_ireplace("%ORID%", $postID, $msg); } 
  if (preg_match('/%IMG%/', $msg)) { $imgURL = nxs_getPostImage($postID, !empty($ntOpts['wpImgSize'])?$ntOpts['wpImgSize']:'full'); $msg = str_ireplace("%IMG%", $imgURL, $msg); } 
  if (preg_match('/%TITLE%/', $msg)) { $title = nxs_doQTrans($post->post_title, $lng); $msg = str_ireplace("%TITLE%", $title, $msg); }                    
  if (preg_match('/%FULLTITLE%/', $msg)) { $title = apply_filters('the_title', nxs_doQTrans($post->post_title, $lng));  $msg = str_ireplace("%FULLTITLE%", $title, $msg); }                    
  if (preg_match('/%STITLE%/', $msg)) { $title = nxs_doQTrans($post->post_title, $lng);   $title = substr($title, 0, 115); $msg = str_ireplace("%STITLE%", $title, $msg); }                    
  if (preg_match('/%AUTHORNAME%/', $msg)) { $aun = $post->post_author;  $aun = get_the_author_meta('display_name', $aun );  $msg = str_ireplace("%AUTHORNAME%", $aun, $msg);}                    
  if (preg_match('/%AUTHOREMAIL%/', $msg)) { $aun = $post->post_author;  $aun = get_the_author_meta('user_email', $aun );  $msg = str_ireplace("%AUTHOREMAIL%", $aun, $msg);}                    
  if (preg_match('/%AUTHORTWNAME%/', $msg)) { $aun = $post->post_author;  $aun = get_the_author_meta('twitter', $aun );  $msg = str_ireplace("%AUTHORTWNAME%", $aun, $msg);}                    
  if (preg_match('/%ANNOUNCE%/', $msg)) { $postContent = nxs_doQTrans($post->post_content, $lng);     
    $postContent = strip_tags(strip_shortcodes(str_ireplace('<!--more-->', '#####!--more--!#####', str_ireplace("&lt;!--more--&gt;", '<!--more-->', $postContent))));
    if (stripos($postContent, '#####!--more--!#####')!==false) { $postContentEx = explode('#####!--more--!#####',$postContent); $postContent = $postContentEx[0]; }    
      else $postContent = nsTrnc($postContent, $options['anounTagLimit'], ' ', '');  $msg = str_ireplace("%ANNOUNCE%", $postContent, $msg);
  }  
  if (preg_match('/%PANNOUNCE%/', $msg)) { $postContent = apply_filters('the_content', nxs_doQTrans($post->post_content, $lng));
    $postContent = strip_tags(strip_shortcodes(str_ireplace('<!--more-->', '#####!--more--!#####', str_ireplace("&lt;!--more--&gt;", '<!--more-->', $postContent))));
    if (stripos($postContent, '#####!--more--!#####')!==false) { $postContentEx = explode('#####!--more--!#####',$postContent); $postContent = $postContentEx[0]; }    
      else $postContent = nsTrnc($postContent, $options['anounTagLimit'], ' ', '');  $msg = str_ireplace("%PANNOUNCE%", $postContent, $msg);
  } 
  if (preg_match('/%ANNOUNCER%/', $msg)) { $postContent = nxs_doQTrans($post->post_content, $lng);     
    $postContent = strip_tags(strip_shortcodes(str_ireplace('<!--more-->', '#####!--more--!#####', str_ireplace("&lt;!--more--&gt;", '<!--more-->', $postContent))));
    if (stripos($postContent, '#####!--more--!#####')!==false) { $postContentEx = explode('#####!--more--!#####',$postContent); $postContent = $postContentEx[1]; }    
      else $postContent = str_replace(nsTrnc($postContent, $options['anounTagLimit'], ' ', ''), '', $postContent);  $msg = str_ireplace("%ANNOUNCER%", $postContent, $msg);
  }  
  if (preg_match('/%PANNOUNCER%/', $msg)) { $postContent = apply_filters('the_content', nxs_doQTrans($post->post_content, $lng));
    $postContent = strip_tags(strip_shortcodes(str_ireplace('<!--more-->', '#####!--more--!#####', str_ireplace("&lt;!--more--&gt;", '<!--more-->', $postContent))));
    if (stripos($postContent, '#####!--more--!#####')!==false) { $postContentEx = explode('#####!--more--!#####',$postContent); $postContent = $postContentEx[1]; }    
      else $postContent = str_replace(nsTrnc($postContent, $options['anounTagLimit'], ' ', ''), '', $postContent);  $msg = str_ireplace("%PANNOUNCER%", $postContent, $msg);
  } 
  if (preg_match('/%EXCERPT%/', $msg)) {      
    if ($post->post_excerpt!="") $excerpt = strip_tags(strip_shortcodes(apply_filters('the_content', nxs_doQTrans($post->post_excerpt, $lng)))); 
      else $excerpt= nsTrnc(strip_tags(strip_shortcodes(apply_filters('the_content', nxs_doQTrans($post->post_content, $lng)))), 300, " ", "..."); 
    $msg = str_ireplace("%EXCERPT%", $excerpt, $msg);
  }  
  if (preg_match('/%RAWEXCERPT%/', $msg)) {      
    if ($post->post_excerpt!="") $excerpt = strip_tags(strip_shortcodes(nxs_doQTrans($post->post_excerpt, $lng))); else $excerpt= nsTrnc(strip_tags(strip_shortcodes(nxs_doQTrans($post->post_content, $lng))), 300, " ", "..."); 
    $msg = str_ireplace("%RAWEXCERPT%", $excerpt, $msg);
  }
  if (preg_match('/%RAWEXCERPTHTML%/', $msg)) { 
      if ($post->post_excerpt!="") $excerpt = strip_shortcodes(nxs_doQTrans($post->post_excerpt, $lng)); else $excerpt= nsTrnc(strip_tags(strip_shortcodes(nxs_doQTrans($post->post_content, $lng))), 300, " ", "..."); 
       $msg = str_ireplace("%RAWEXCERPTHTML%", $excerpt, $msg);
  }
  if (preg_match('/%TAGS%/', $msg)) { $t = wp_get_object_terms($postID, 'product_tag'); if ( empty($t) || is_wp_error($t) || !is_array($t) ) $t = wp_get_post_tags($postID);
    $tggs = array(); foreach ($t as $tagA) {$tggs[] = $tagA->name;} $tags = implode(', ',$tggs); $msg = str_ireplace("%TAGS%", $tags, $msg);
  }
  if (preg_match('/%CATS%/', $msg)) { $t = wp_get_post_categories($postID); $cats = array();  foreach($t as $c){ $cat = get_category($c); $cats[] = str_ireplace('&','&amp;',$cat->name); } 
          $ctts = implode(', ',$cats); $msg = str_ireplace("%CATS%", $ctts, $msg);
  }
  if (preg_match('/%HCATS%/', $msg)) { $t = wp_get_post_categories($postID); $cats = array();  
    foreach($t as $c){ $cat = get_category($c);  $cats[] = "#".trim(str_replace(' ',$htS, str_replace('  ', ' ', trim(str_ireplace('&','',str_ireplace('&amp;','',$cat->name)))))); } 
    $ctts = implode($htSep,$cats); $msg = str_ireplace("%HCATS%", $ctts, $msg);
  }  
  if (preg_match('/%HTAGS%/', $msg)) { $t = wp_get_object_terms($postID, 'product_tag'); if ( empty($t) || is_wp_error($t) || !is_array($t) ) $t = wp_get_post_tags($postID);
    //$tggs = array(); foreach ($t as $tagA){$tggs[] = "#".trim(str_replace(' ', $htS, preg_replace('/[^a-zA-Z0-9\p{L}\p{N}\s]/u', '', trim(nxs_ucwords(str_ireplace('&','',str_ireplace('&amp;','',$tagA->name)))))));} 
    $tggs = array(); foreach ($t as $tagA){$tggs[] = "#".trim(str_replace(' ', $htS, nxs_clean_string(trim(nxs_ucwords(str_ireplace('&','',str_ireplace('&amp;','',$tagA->name)))))));} 
    $tags = implode($htSep,$tggs); $msg = str_ireplace("%HTAGS%", $tags, $msg);
  } 
  if (preg_match('/%+CF-[a-zA-Z0-9-_]+%/', $msg)) { $msgA = explode('%CF', $msg); $mout = '';
    foreach ($msgA as $mms) { 
        if (substr($mms, 0, 1)=='-' && stripos($mms, '%')!==false) { $mGr = CutFromTo($mms, '-', '%'); $cfItem =  get_post_meta($postID, $mGr, true);  $mms = str_ireplace("-".$mGr."%", $cfItem, $mms); } $mout .= $mms; 
    } $msg = $mout; 
  }  
  $mm = array(); if (preg_match_all('/%H?CT-[a-zA-Z0-9_]+%/', $msg, $mm)) { $msgA = explode('%CT', str_ireplace("%HCT", "%CT", $msg)); $mout = ''; $i = 0;
    foreach ($msgA as $mms) { 
      if (substr($mms, 0, 1)=='-' && stripos($mms, '%')!==false){ $h = strpos($mm[0][$i],'%HCT-')!==false; $i++; $mGr=CutFromTo($mms,'-','%'); $cfItem=wp_get_post_terms($postID,$mGr,array("fields"=>"names"));
        if (is_nxs_error($cfItem)) {nxs_addToLogN('E', 'Error', 'MSG', '-=ERROR=- '.$mGr.'|'.print_r($cfItem, true), '');  $mms=str_ireplace("-".$mGr."%",'',$mms);   } else { $tggs = array(); 
          //foreach ($cfItem as $frmTag) { if ($h) $frmTag = trim(str_replace(' ', $htS, preg_replace('/[^a-zA-Z0-9\p{L}\p{N}\s]/u', '', trim(nxs_ucwords(str_ireplace('&','',str_ireplace('&amp;','',$frmTag)))))));
          foreach ($cfItem as $frmTag) { if ($h) $frmTag = trim(str_replace(' ', $htS, nxs_clean_string(trim(nxs_ucwords(str_ireplace('&','',str_ireplace('&amp;','',$frmTag)))))));
          
              $tggs[] = ($h?'#':'').$frmTag; 
          } $cfItem = implode(' ',$tggs); $mms=str_ireplace("-".$mGr."%",$cfItem,$mms);    
        }
      } $mout.=$mms;  
    } $msg = $mout; 
  }
  if (preg_match('/%FULLTEXT%/', $msg)) { $postContent = apply_filters('the_content', nxs_doQTrans($post->post_content, $lng)); $msg = str_ireplace("%FULLTEXT%", $postContent, $msg);}                    
  if (preg_match('/%RAWTEXT%/', $msg)) { $postContent = nxs_doQTrans($post->post_content, $lng); $msg = str_ireplace("%RAWTEXT%", $postContent, $msg);}
  if (preg_match('/%SITENAME%/', $msg)) { $siteTitle = htmlspecialchars_decode(get_bloginfo('name'), ENT_QUOTES); $msg = str_ireplace("%SITENAME%", $siteTitle, $msg);}      
  if (preg_match('/%POSTFORMAT%/', $msg)) { $gg = get_post_format($postID);  $txt =  get_post_format_string($gg ? $gg : 'Post'); if (empty($txt)) $txt = 'Post'; $msg = str_ireplace("%POSTFORMAT%", $txt, $msg);}      
  if (preg_match('/%POSTTYPE%/', $msg)) { $gg = get_post_type_object(get_post_type($postID)); 
    if (!empty($gg->labels->singular_name)) $txt = $gg->labels->singular_name; elseif (!empty($gg->labels->name)) $txt = $gg->labels->name; else $txt = $gg->name; $msg = str_ireplace("%POSTTYPE%", $txt, $msg);
  }      
  if (isset($ShownAds)) $ShownAds = $ShownAdsL; // FIX for the quick-adsense plugin
  return trim($msg);
}}
//## Apply filters to posts
if (!function_exists("nxs_noSing")) { function nxs_noSing( &$obj ) { $obj->is_singular = false; $obj->is_single = false; return $obj; }}

if (!function_exists("nxs_snapCheckFilters")) { function nxs_snapCheckFilters($options, $postObj) { $postID = $postObj->ID; //  prr($options, 'FLT1');
  if (!empty($options['fltrsOn']) && !empty($options['fltrs']) && empty($options['fltrAfter'])) {  $options['fltrs']['nxs_postID'] = $postID; $options['fltrs']['fullreturn']='1'; //echo "|Pre FLT 2|"; 
    add_filter( 'pre_get_posts', 'nxs_noSing' ); $pfidRet = get_posts_ids_by_filter( $options['fltrs'] ); /* prr($pfidRet); */ $pfid = $pfidRet['p']; remove_filter( 'pre_get_posts', 'nxs_noSing' ); //nxs_LogIt('I','','A','','FLT',print_r($pfidRet, true)."\r\n<br/>".print_r($postObj, true));    
    if (empty($pfid) || empty($pfid[0]) || $pfid[0]!=$postID) { $msg = nxsAnalyzePostFilters($options['fltrs'], $postObj); 
      $postLogRec = "ID: ".$postObj->ID." | ".$postObj->post_title." (".$postObj->post_name.") | Author(ID): ".$postObj->post_author." |Status: ".$postObj->post_status.
        " | Format ".$postObj->filter." | Post Type: ".$postObj->post_type." | <br/>".$msg;        
      //return "\r\n<br/>".'| Args: '.print_r($pfidRet['a'], true).' '."\r\n<br/>".'| Query: '.$pfidRet['q']."\r\n<br/>".'| Post Filters: '.$msg;
      return $msg;
    }
  } return false;   
}}
if (!function_exists("nxsAnalyzePostFilters")) { function nxsAnalyzePostFilters($filter, $post='' ) { $out = ''; //prr($post); prr($filter); 
    //## Cats
    if (!empty($filter['nxs_cats_names'])) { $fltCats = ''; $postCats = ''; foreach ($filter['nxs_cats_names'] as $cctts) { $cInfo = get_term( $cctts, 'category'); if (!empty($cInfo)) $fltCats .= $cInfo->name.'|'; }
      if (!empty($post)) { $gg = wp_get_object_terms( $post->ID, 'category'); foreach ($gg as $g) $postCats .= $g->name.'|'; }
      if (empty($post) || !empty($postCats) || (!empty($post) && empty($filter['nxs_ie_cats_names'])) ) { 
          $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Cats ('.(empty($filter['nxs_ie_cats_names'])?'Autopost Only':'Excluded').'): '.$fltCats.(!empty($postCats)?' | Post Cats: '.$postCats:''); 
      }
    }
    //## Tags    
    if (!empty($filter['nxs_tags_names'])) { $fltT = ''; $postT = '';
      foreach ($filter['nxs_tags_names'] as $cctts) { $cInfo = get_term( $cctts, 'post_tag'); $fltT .= $cInfo->name.'|'; }
      if (!empty($post)) {  $gg = wp_get_object_terms( $post->ID, 'post_tag'); foreach ($gg as $g) $postT .= $g->name.'|'; }
      if (empty($post) || !empty($postT) || (!empty($post) && empty($filter['nxs_ie_tags_names'])) ) { 
        $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Tags ('.(empty($filter['nxs_ie_tags_names'])?'Autopost Only':'Excluded').'): '.$fltT.(!empty($postT)?' | Post Tags: '.$postT:'');
      }
    }
    //## Type
    if (!empty($filter['nxs_post_type'])) { $fltT = ''; foreach ($filter['nxs_post_type'] as $cInfo) $fltT .= $cInfo.'|'; 
      $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Post Type ('.(empty($filter['nxs_ie_posttypes'])?'Autopost Only':'Excluded').'): '.$fltT.(!empty($post->post_type)?' | Post Type: '.$post->post_type:'');
    }
    //## Format
    if (!empty($filter['nxs_post_formats'])) { $fltT = ''; foreach ($filter['nxs_post_formats'] as $cInfo) $fltT .= $cInfo.'|'; $gg = get_post_format($post->ID);  $txt =  get_post_format_string($gg);
      $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Post Format (Autopost Only): '.$fltT.(!empty($txt)?' | Post Format: '.$txt:'');
    }
    //## Author
    if (!empty($filter['nxs_user_names'])) { $fltT = ''; $author = '';
      if (!empty($post)) { $author = get_user_by('id', $post->post_author); $author = $author->user_login."(".$author->user_nicename.")"; }
      foreach ($filter['nxs_user_names'] as $cctts) { $cInfo = get_user_by( 'id', $cctts); $fltT .= $cInfo->user_login."(".$cInfo->user_nicename.")"; }
      $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Users (Autopost Only): '.$fltT .(!empty($author)?' | Post Author: '.$author:'');
    }
    //## Search
    if (!empty($filter['nxs_search_keywords'])) { 
      $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Filter: Search (Autopost Only): '.$filter['nxs_search_keywords'];
    }
    //## Meta
    if (!empty($filter['nxs_meta_key'])) { $count_compares =  (int)$filter['nxs_count_meta_compares']; 
      for( $i = 1; $i <= $count_compares; $i++ ) { $postfix = $i == 1 ? '' : '_'. $i; 
        if (!empty($filter["nxs_meta_key$postfix"])) { if (!empty($post)) { $val = get_post_meta( $post->ID, $filter["nxs_meta_key$postfix"]); if (empty($val)) $val = 'NULL'; } else $val = '';
            $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Custom Field: '. $filter["nxs_meta_key$postfix"].' '.$filter["nxs_meta_operator$postfix"].' '.$filter["nxs_meta_value$postfix"][0].(!empty($val)?' | Actual: '.print_r($val, true):'');
        }
      }          
    }
    //## Taxonomies
    if (!empty($filter['nxs_term_names'])) { $count_compares =  (int)$filter['nxs_count_term_compares']; 
      for( $i = 1; $i <= $count_compares; $i++ ) { $postfix = $i == 1 ? '' : '_'. $i;     
        if (!empty($filter["nxs_tax_names$postfix"])) { if (!empty($post)) { $gg = wp_get_object_terms( $post->ID, $filter["nxs_tax_names$postfix"]); } else $gg = ''; 
            $out .= "<br/>\r\n".'&nbsp;&nbsp;&nbsp;&nbsp;Custom Taxonomy: '. $filter["nxs_tax_names$postfix"].' '.$filter["nxs_term_operator$postfix"].' '.print_r($filter["nxs_term_names$postfix"], true).(!empty($gg)?' | Actual: '.print_r($gg, true):'');
        }
      }          
    }
    
    //if (!empty($filter['nxs_term_names'])) $gg = wp_get_object_terms( $post->ID, $filter['tax']); 
    
   // prr($out);
    return $out;
}}
//##
if (!function_exists("nxs_clean_string")) { function nxs_clean_string($string) { $s = trim($string); 
  if (function_exists("iconv")) $s = iconv("UTF-8", "UTF-8//IGNORE", $s); elseif (function_exists("mb_convert_encoding") && function_exists("mb_split")) $s = mb_convert_encoding($s, "UTF-8", mb_detect_encoding($s)); // drop all non utf-8 characters
  //## this is some bad utf-8 byte sequence that makes mysql complain - control and formatting i think
  $s = preg_replace('/(?>[\x00-\x1F]|\xC2[\x80-\x9F]|\xE2[\x80-\x8F]{2}|\xE2\x80[\xA4-\xA8]|\xE2\x81[\x9F-\xAF])/', ' ', $s);
  $s = preg_replace('/\s+/', ' ', $s); // reduce all multiple whitespace to a single space
  $s = preg_replace("/[^[:alnum:][:space:]]/u", '', $s); // Leave only letters and numbers (Unicode)
  return $s;
}}
//##
if (!function_exists("nxs_mbConvertCaseUTF8var")){ function nxs_mbConvertCaseUTF8var($s) { $arr = preg_split("//u", $s, -1, PREG_SPLIT_NO_EMPTY); $result = ""; $mode = false; 
  foreach ($arr as $char) { $res = preg_match('/\\p{Mn}|\\p{Me}|\\p{Cf}|\\p{Lm}|\\p{Sk}|\\p{Lu}|\\p{Ll}|\\p{Lt}|\\p{Sk}|\\p{Cs}/u', $char) == 1; 
    if ($mode) { if (!$res)$mode = false; } elseif ($res) { $mode = true; $char = mb_convert_case($char, MB_CASE_TITLE, "UTF-8"); } $result .= $char; 
  } return $result; 
}} 
if (!function_exists("nxs_ucwords")){ function nxs_ucwords($str) { if (function_exists("mb_convert_case")) return nxs_mbConvertCaseUTF8var($str); else return ucwords($str); }}

if (!function_exists("nxs_getURL")){ function nxs_getURL($options, $postID, $addURLParams='') { global $plgn_NS_SNAutoPoster; $gOptions = $plgn_NS_SNAutoPoster->nxs_options; 
  if (!isset($options['urlToUse']) || trim($options['urlToUse'])=='') $myurl =  trim(get_post_meta($postID, 'snap_MYURL', true));
  $ssl = (!empty($gOptions['ht']) && $gOptions['ht'] == ord('h')); if (!empty($myurl)) $options['urlToUse'] = $myurl;
  if ((isset($options['urlToUse']) && trim($options['urlToUse'])!='') || $ssl) { $options['atchUse'] = 'F'; } else $options['urlToUse'] = get_permalink($postID);      
  $options['urlToUse'] = $ssl?nsx_doDecode($gOptions['useSSLCert']):$options['urlToUse']; // $addURLParams = trim($gOptions['addURLParams']);  
  if($addURLParams!='') $options['urlToUse'] .= (strpos($options['urlToUse'],'?')!==false?'&':'?').$addURLParams;  $forceSURL = trim(get_post_meta($postID, '_snap_forceSURL', true));
  if (empty($forceSURL)) $forceSURL = !empty($gOptions['forceSURL']); else $forceSURL = $forceSURL =='1'; if (!empty($options['suUName'])) $forceSURL = false; //## SU does not allow Shorteners  
  if (!empty($gOptions['forcessl']) && $gOptions['forcessl'] == 'N') $options['urlToUse'] = str_ireplace('https','http',$options['urlToUse']); 
  if (!empty($gOptions['forcessl']) && $gOptions['forcessl'] == 'S') $options['urlToUse'] = str_ireplace('http','https',str_ireplace('https','http',$options['urlToUse']));
  if ($forceSURL) $options['urlToUse'] = nxs_mkShortURL($options['urlToUse'], $postID); return $options;
}}

if (!function_exists('nxsLstSort')){function nxsLstSort($a, $b) { if (empty($a['do'])) $a['do'] = 0; if (empty($b['do'])) $b['do'] = 0;  if (empty($a['nName'])) $a['nName'] = ''; if (empty($b['nName'])) $b['nName'] = '';
  if ($a['do']=='2') $a['do'] = 1;  if ($b['do']=='2') $b['do'] = 1; if ($a['do'] == $b['do']) { return strcasecmp($a['nName'],$b['nName']); } return ($a['do'] > $b['do']) ? -1 : 1;    
}}

if (!function_exists('nxs_showListRow')){function nxs_showListRow($ntParams) { $ntInfo = $ntParams['ntInfo']; $nxs_plurl = $ntParams['nxs_plurl']; $ntOpts = $ntParams['ntOpts'];  ?>
          <div class="nxs_box" onmouseover="jQuery('.addMore<?php echo $ntInfo['code']; ?>').show();" onmouseout="jQuery('.addMore<?php echo $ntInfo['code']; ?>').hide();">
            <div class="nxs_box_header"> 
              <div class="nsx_iconedTitle" style="margin-bottom:1px;background-image:url(<?php echo $nxs_plurl;?>img/<?php echo $ntInfo['lcode']; ?>16.png);"><?php echo $ntInfo['name']; ?>
              <?php $cbo = count($ntOpts); ?> 
              <?php if ($cbo>1){ ?><div class="nsBigText"><?php echo "(".($cbo=='0'?'No':$cbo)." "; _e('accounts', 'social-networks-auto-poster-facebook-twitter-g'); echo ")"; ?></div><?php } ?>
              <span style="display: none;" class="addMore<?php echo $ntInfo['code']; ?>">&nbsp;&nbsp;&nbsp;<a data-nt="<?php echo $ntInfo['code'].(1+max(array_keys($ntOpts))); ?>" style="font-size: 12px; text-decoration: none;" href="#" class="nxs_snapAddNew">[Add New <?php echo $ntInfo['name']; ?> account]</a></span>              
              </div>
            </div>
            <div class="nxs_box_inside">            
            <?php $jj = 0; if(!empty($ntParams['checkFunc']) && !function_exists($ntParams['checkFunc']['funcName']) && !class_exists($ntParams['checkFunc']['funcName'])) echo $ntParams['checkFunc']['msg']; 
            else { foreach ($ntOpts as $indx=>$pbo){ if (empty($pbo['nName'])) $pbo['nName'] = $ntInfo['name']; if (!isset($pbo['do'])) $pbo['do'] = $pbo['do'.$ntInfo['code']];  $pbo = nxs_FltrsV3toV4($pbo); $ntOpts[$indx]=$pbo; } uasort($ntOpts, 'nxsLstSort'); 
              foreach ($ntOpts as $indx=>$pbo){ $jj++; if (empty($pbo[$ntInfo['lcode'].'OK'])) $pbo[$ntInfo['lcode'].'OK'] = !empty($pbo[$ntParams['chkField']])?'1':''; ?>              
              <div style="padding-bottom: 3px;<?php echo ($cbo>7 && $jj>5)?'display:none;" class="showMore'.$ntInfo['code'].'"':'"'; ?>  onmouseover="jQuery('.showInlineMenu<?php echo $ntInfo['code'].$indx; ?>').show();jQuery(this).addClass('nxsHiLightBorder');" onmouseout="jQuery('.showInlineMenu<?php echo $ntInfo['code'].$indx; ?>').hide();jQuery(this).removeClass('nxsHiLightBorder');"">
              <div style="margin:0px;margin-left:5px;"> <img id="<?php echo $ntInfo['code'].$indx;?>LoadingImg" style="display: none;" src='<?php echo $nxs_plurl; ?>img/ajax-loader-sm.gif' />
              <?php if (function_exists('nxs_adminInitFunc')) { /* if standalone API - don't show checkbox */ ?> 
              <?php  if ((int)$pbo['do'.$ntInfo['code']]>0 && isset($pbo['fltrsOn']) && (int)$pbo['fltrsOn'] == 1) {     $fltInfo = nxsAnalyzePostFilters($pbo['fltrs']);                 
                ?> <input type="radio" id="rbtn<?php echo $ntInfo['lcode'].$indx; ?>" value="2" name="<?php echo $ntInfo['lcode']; ?>[<?php echo $indx; ?>][apDo<?php echo $ntInfo['code']; ?>]" class="nxs_acctcbR" checked="checked" data-fltinfo="<?php echo $fltInfo; ?>" /> <?php } else { ?>
                <input value="0" name="<?php echo $ntInfo['lcode']; ?>[<?php echo $indx; ?>][apDo<?php echo $ntInfo['code']; ?>]" type="hidden" />             
                <input value="1" name="<?php echo $ntInfo['lcode']; ?>[<?php echo $indx; ?>][apDo<?php echo $ntInfo['code']; ?>]" type="checkbox" <?php if ((int)$pbo['do'.$ntInfo['code']] > 0) echo "checked"; ?> />             
              <?php } ?>       
              
              <?php if (isset($pbo['rpstOn']) && (int)$pbo['rpstOn'] == 1) { ?> <span onmouseout="nxs_hidePopUpInfo('popReActive');" onmouseover="nxs_showPopUpInfo('popReActive', event);"><?php echo "*[R]*" ?></span><?php } ?>
              <?} else { ?>  <?php } ?>
              <strong><?php  _e('Auto-publish to', 'social-networks-auto-poster-facebook-twitter-g'); ?> <?php echo $ntInfo['name']; ?> <i style="color: #005800;"><?php if($pbo['nName']!='') echo "(".$pbo['nName'].")"; ?></i></strong>
              <?php if ($ntInfo['tstReq'] && (!isset($pbo[$ntInfo['lcode'].'OK']) || $pbo[$ntInfo['lcode'].'OK']=='')){ ?><b style="color: #800000">&nbsp;&nbsp;<?php  _e('Attention required. Unfinished setup', 'social-networks-auto-poster-facebook-twitter-g'); ?> ==&gt;</b><?php } ?>              
              <?php if ($ntInfo['lcode']=='li' && !empty($pbo['grpID'])){ ?><b style="color: #800000"><?php  _e('Attention required. Groups are no longer supported by LinkedIn Native API', 'social-networks-auto-poster-facebook-twitter-g'); ?> ==&gt;</b><?php } ?>              
              
              <span style="padding-left: 0px; display: none;" class="showInlineMenu<?php echo $ntInfo['code'].$indx; ?>"><a id="do<?php echo $ntInfo['code'].$indx; ?>AG" href="#" onclick="doGetHideNTBlock('<?php echo $ntInfo['code'];?>' , '<?php echo $indx; ?>');return false;">[<?php  _e('Show Settings', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a>&nbsp;&nbsp;&nbsp;<a href="#" onclick="doDelAcct('<?php echo $ntInfo['lcode']; ?>', '<?php echo $indx; ?>', '<?php if (isset($pbo['bgBlogID'])) echo $pbo['nName']; ?>');return false;">[<?php  _e('Remove Account', 'social-networks-auto-poster-facebook-twitter-g'); ?>]</a></span>
              
              </div><div id="nxsNTSetDiv<?php echo $ntInfo['code'].$indx; ?>"></div>
              </div>
            <?php }} if ($jj>7) { ?> <div style="padding-left:5px;padding-top:5px;"><a href="#" onclick="jQuery('.showMore<?php echo $ntInfo['code']; ?>').show(); jQuery(this).parent().hide(); return false;">Show More[<?php echo ($cbo-5); ?>]</a></div>  <?php } ?>
            </div>
          </div> <?php            
        }
}

if (!function_exists("nxs_get_admin_url")) { function nxs_get_admin_url($path=''){ //## Workaround for some buggy 'admin hiding' plugins.
  $admURL = admin_url($path); if (substr($admURL, 0, 4)!='http') { $admURL = admin_url($path, 'https'); $admURL = str_ireplace('https://', 'http://', $admURL);} return $admURL;
}}
//## Process Spin 
if (!function_exists("nxs_spinRecursion")) { function nxs_spinRecursion(&$txt, $startCh) { global $nxs_spin_lCh, $nxs_spin_rCh, $nxs_spin_splCh; $startPos = $startCh;
  while ($startCh++ < strlen($txt)) {
    if (substr($txt, $startCh, strlen($nxs_spin_lCh)) == $nxs_spin_lCh)  $txt = nxs_spinRecursion($txt, $startCh);
    elseif (substr($txt, $startCh, strlen($nxs_spin_rCh)) == $nxs_spin_rCh) {
      $tmpTxt = substr($txt, $startPos+strlen($nxs_spin_lCh), ($startCh - $startPos)-strlen($nxs_spin_rCh));
      $toRepl = nxs_spinReplace($tmpTxt); $txt = str_replace($nxs_spin_lCh.$tmpTxt.$nxs_spin_rCh, $toRepl, $txt);
    }
  } return $txt;
}}
if (!function_exists("nxs_spinReplace")) { function nxs_spinReplace($txt) { global $nxs_spin_splCh; $txt = explode($nxs_spin_splCh, $txt);  $out = $txt[mt_rand(0,count($txt)-1)]; return $out; }}
if (!function_exists("nxs_doSpin")) { function nxs_doSpin($msg){  global $nxs_spin_lCh, $nxs_spin_rCh, $nxs_spin_splCh;
    $nxs_spin_lCh = '{'; $nxs_spin_rCh='}'; $nxs_spin_splCh='|'; $msg = nxs_spinRecursion($msg, -1); return $msg;
}}

if (!function_exists("nxs_getImgfrOpt")) { function nxs_getImgfrOpt($imgOpts, $defSize=''){ if (!is_array($imgOpts)) return $imgOpts;// prr($imgOpts);
   if ($defSize!='' && isset($imgOpts[$defSize]) && trim($imgOpts[$defSize])!='') return $imgOpts[$defSize];
   if (isset($imgOpts['large']) && trim($imgOpts['large'])!='') return $imgOpts['large'];
   if (isset($imgOpts['original']) && trim($imgOpts['original'])!='') return $imgOpts['original'];
   if (isset($imgOpts['thumb']) && trim($imgOpts['thumb'])!='') return $imgOpts['thumb'];
   if (isset($imgOpts['medium']) && trim($imgOpts['medium'])!='') return $imgOpts['medium'];
}}

if (!function_exists("nxs_memCheck")) { function nxs_memCheck() { global $nxs_snapThisPageUrl; $mLimit = (int) ini_get('memory_limit'); $mLimit = empty($mLimit) ? __('N/A') :$mLimit . __(' MByte');
  $mUsageP = function_exists('memory_get_usage') ? round(memory_get_peak_usage() / 1024 / 1024, 2) : 0; $mUsageP = empty($mUsageP) ? __('N/A') : $mUsageP . __(' MByte'); 
  
  $mUsage = function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 2) : 0; $mUsage = empty($mUsage) ? __('N/A') : $mUsage . __(' MByte'); ?>
    <div><strong><?php _e('PHP Version'); ?></strong>: <span><?php echo PHP_VERSION; ?>;&nbsp;</span>
      <strong><?php _e('Memory limit'); ?></strong>: <span><?php echo $mLimit; ?>; &nbsp;</span>
      <strong><?php _e('Memory usage'); ?></strong>: <span><?php echo $mUsage; ?>; &nbsp;</span> <strong><?php _e('Memory peak usage'); ?></strong>: <span><?php echo $mUsageP; ?>; &nbsp;</span>
      <br/><a target="_blank" href="<?php echo $nxs_snapThisPageUrl; ?>&do=test">[Test Connections and HTTPS/SSL]</a>&nbsp;&nbsp;<a target="_blank" href="<?php echo $nxs_snapThisPageUrl; ?>&do=crtest">[Show Cron Test Results]</a>
    </div> <?php
}}
//## Check SSL Sec
if (!function_exists("nxsCheckSSLCurl")){function nxsCheckSSLCurl($url){
  $ch = curl_init($url); $headers = array(); $headers[] = 'Accept: text/html, application/xhtml+xml, */*'; $headers[] = 'Cache-Control: no-cache';
  $headers[] = 'Connection: Keep-Alive'; $headers[] = 'Accept-Language: en-us';  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)"); 
  $content = curl_exec($ch); $err = curl_errno($ch); $errmsg = curl_error($ch); if ($err!=0) return array('errNo'=>$err, 'errMsg'=>$errmsg); else return false;
}}
if (!function_exists("nxs_cron_check")){function nxs_cron_check() { if (stripos($_SERVER["REQUEST_URI"], 'wp-cron.php')!==false) {  
  $cronCheckArray = get_option('NXS_cronCheck'); if (empty($cronCheckArray)) $cronCheckArray = array('cronCheckStartTime'=>time(), 'cronChecks'=>array());    
  if (($cronCheckArray['cronCheckStartTime']+900)>time()) {  ( $offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
    $cronCheckArray['cronChecks'][] = '['.date_i18n('Y-m-d H:i:s', $_SERVER["REQUEST_TIME"]+$offset).'] - WP Cron called from '.$_SERVER["REMOTE_ADDR"].' ('.$_SERVER["HTTP_USER_AGENT"].')';
    //nxs_addToLogN('S', 'Cron Check', '', 'WP Cron called from '.$_SERVER["REMOTE_ADDR"].' ('.$_SERVER["HTTP_USER_AGENT"].')', date_i18n('Y-m-d H:i:s', $_SERVER["REQUEST_TIME"]+$offset));
  } elseif (empty($cronCheckArray['status']) &&  is_array($cronCheckArray['cronChecks'])) $cronCheckArray['status'] = (count($cronCheckArray['cronChecks'])<17 && count($cronCheckArray['cronChecks'])>1)?1:0;
  update_option("NXS_cronCheck", $cronCheckArray);    
}}}
//## Headers and Remote Options Array
if (!function_exists("nxs_makeHeaders")) { function nxs_makeHeaders($ref, $org='', $type='GET', $aj=false){  $hdrsArr = array(); 
 $hdrsArr['Cache-Control']='max-age=0'; $hdrsArr['Connection']='keep-alive'; $hdrsArr['Referer']=$ref;
 $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.67 Safari/537.36'; 
 if($type=='JSON') $hdrsArr['Content-Type']='application/json;charset=UTF-8'; elseif($type=='POST') $hdrsArr['Content-Type']='application/x-www-form-urlencoded'; 
   elseif($type=='JS') $hdrsArr['Content-Type']='application/javascript; charset=UTF-8'; elseif($type=='PUT') $hdrsArr['Content-Type']='application/octet-stream';
 if($aj===true) $hdrsArr['X-Requested-With']='XMLHttpRequest';  if ($org!='') $hdrsArr['Origin']=$org; 
 if ($type=='GET') $hdrsArr['Accept']='text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'; else $hdrsArr['Accept']='*/*';
 if (function_exists('gzdeflate')) $hdrsArr['Accept-Encoding']='deflate,sdch';  $hdrsArr['Accept-Language']='en-US,en;q=0.8'; return $hdrsArr;         
}}
if (!function_exists("nxs_getNXSHeaders")) {  function nxs_getNXSHeaders($ref='', $post=false){ return nxs_makeHeaders($ref, '', $post?'POST':'GET'); }} //## Compatibility function...

if (!function_exists("nxs_mkRemOptsArr")) {function nxs_mkRemOptsArr($hdrsArr, $ck='', $flds='', $p='', $rdr=0, $timt=45, $sslverify = false){ $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36';
  if (empty($hdrsArr)) $hdrsArr = array('headers'=>array('Referer'=>'http://'.$_SERVER['HTTP_HOST'], 'User-Agent'=>$ua)); $a = array('headers' => $hdrsArr, 'httpversion' => '1.1', 'timeout' => $timt, 'redirection' => $rdr, 'sslverify'=>$sslverify, 'user-agent'=>$ua); 
  if (!empty($flds)) $a['body'] = $flds; if (!empty($p)) $a['proxy'] = $p;  if (!empty($ck)) $a['cookies'] = $ck; return $a;
}}

if (!function_exists('nxsMergeArraysOV')){function nxsMergeArraysOV($Arr1, $Arr2){
  foreach($Arr2 as $key => $value) { if(array_key_exists($key, $Arr1) && is_array($value)) $Arr1[$key] = nxsMergeArraysOV($Arr1[$key], $Arr2[$key]); else $Arr1[$key] = $value;} return $Arr1;
}}
if (!function_exists('nxs_MergeCookieArr')){function nxs_MergeCookieArr($ArrO, $ArrN){ $namesArr = array(); foreach($ArrO as $key => $value) { if (is_object($value)) $namesArr[$key] = $value->name; }             
  foreach($ArrN as $key => $value) { if (is_object($value) && $value->value!='deleted') { $isEx = array_search($value->name, $namesArr); if ($isEx===false) $ArrO[] = $value; else $ArrO[$isEx] = $value;}} return $ArrO;
}}
if (!function_exists('nxs_altCurlProxy')){ function nxs_altCurlProxy( $ch, $r='' ){ if (empty($r['proxy'])) return; curl_setopt($ch, CURLOPT_PROXY, $r['proxy']['proxy']);  
  if (!empty($r['proxy']['up'])) curl_setopt($ch, CURLOPT_PROXYUSERPWD, $r['proxy']['up']); if (!empty($r['proxy']['proxys5']))  curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
}}
if (!function_exists('nxs_getCKVal')){ function nxs_getCKVal($name, $ck) { foreach ($ck as $c) if ($c->name==$name) return($c->value); return false; } }
if (!function_exists('nxsClnCookies')){ function nxsClnCookies($ck) { $ckOut = array(); $t =time(); foreach ($ck as $c) { if ($c->value!='deleted' && $c->value!='deleteMe' && $c->value!='delete me' && $c->value!='"delete me"' && (empty($c->expires) || $c->expires>$t)) $ckOut[] = $c; } return $ckOut; }}
if (!function_exists('nxsLeaveOnlyCookies')){ function nxsLeaveOnlyCookies($ck, $lv) { $ckOut = array(); foreach ($ck as $c) { if (in_array($c->name, $lv)) $ckOut[] = $c; } return $ckOut; }}
if (!function_exists('nxsDelCookie')){ function nxsDelCookie($ck, $dc) { $ckOut = array(); foreach ($ck as $c) if ($c->name!=$dc) $ckOut[] = $c; return $ckOut; }}


if (!function_exists("nxs_show_noLibWrn")) {function nxs_show_noLibWrn($msg){ ?> <div style="border: 2px solid darkred; padding: 25px 15px 15px 15px; margin: 3px; background-color: #fffaf0;"> 
            <span style="font-size: 16px; color:darkred;"><?php echo $msg ?></span>&nbsp;<a href="http://www.nextscripts.com/faq/third-party-libraries-autopost-google-pinterest/" target="_blank">More info about third party libraries.</a><br/><hr/> <div style="font-size: 16px; color:#005800; font-weight: bold; margin-top: 12px; margin-bottom: 7px;">You can get API library from NextScripts.</div>
            <div style="padding-bottom: 5px;"><a href="http://www.nextscripts.com/snap-api/">SNAP API Libarary</a> adds autoposting to:</div> <span class="nxs_txtIcon nxs_ti_gp">Google+</span>, <span class="nxs_txtIcon nxs_ti_pn">Pinterest</span>, <span class="nxs_txtIcon nxs_ti_bg">Instagram</span>, <span class="nxs_txtIcon nxs_ti_rd">Reddit</span>, &nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_yt">YouTube</span>,&nbsp;&nbsp;<span class="nxs_txtIcon nxs_ti_fp">Flipboard</span>, <span class="nxs_txtIcon nxs_ti_li">LinkedIn Company Pages and Groups</span><br><br>          
            All NextScripts SNAP API libraries are included and automatically installed with the  <a href="http://www.nextscripts.com/social-networks-auto-poster-for-wp-multiple-accounts/" target="_blank">"Pro" (Multiaccount) Edition of the SNAP plugin</a>. Pro version upgrade also adds the ability to configure more then one account for each social network and some addidional features.<br><br>
            <div align="center"><a target="_blank" href="http://www.nextscripts.com/social-networks-auto-poster-for-wp-multiple-accounts/#getit" class="NXSButton" id="nxs_snapUPG">Get SNAP Pro Plugin with SNAP API</a></div>
            <div style="font-size: 10px; margin-top: 20px;">*If you already have API, please follow instructions from the readme.txt file.</div>
          </div> <?php 
}}

if (!function_exists("nxs_save_glbNtwrks")) { function nxs_save_glbNtwrks($nt, $ii, $ntOptsOrVal, $field='', $networks='')  { if (empty($networks)) { if ($field=='*') {$field=''; $merge = true;} else $merge = false;
    if (function_exists("nxs_settings_open")) $networks = nxs_settings_open(); else { 
        if  ((!current_user_can('manage_options' ) && current_user_can('haveown_snap_accss'))) {  $nxs_SNAP = new nxs_SNAP();  $networks = $nxs_SNAP->nxs_acctsU; }
          elseif (class_exists('NS_SNAutoPoster')) { global $plgn_NS_SNAutoPoster; if (!isset($plgn_NS_SNAutoPoster)) $plgn_NS_SNAutoPoster = new NS_SNAutoPoster(); $networks = $plgn_NS_SNAutoPoster->nxs_options; }       
    }
  } if(!empty($field)) $networks[$nt][$ii][$field] = $ntOptsOrVal; else $networks[$nt][$ii] = $merge?(array_merge($networks[$nt][$ii],$ntOptsOrVal)):$ntOptsOrVal; nxs_save_ntwrksOpts($networks);   
  if (isset($plgn_NS_SNAutoPoster)) $plgn_NS_SNAutoPoster->nxs_options = $networks; //if (isset($nxs_SNAP))  $nxs_SNAP->nxs_accts = $networks; // prr($networks[$nt]); var_dump($merge);
}}

if (!function_exists("nxs_save_ntwrksOpts")) { function nxs_save_ntwrksOpts($networks) {
  if (function_exists('nxs_settings_save')) nxs_settings_save($networks); else if (function_exists('get_option') && !empty($networks)) { 
      if  ((!current_user_can('manage_options' ) && current_user_can('haveown_snap_accss'))) { global $nxs_SNAP; if (empty($nxs_SNAP))  $nxs_SNAP = new nxs_SNAP();  update_option($nxs_SNAP->dbNtsNameU, $networks); } else { update_option('NS_SNAutoPoster', $networks); } 
  }
}}

if (!function_exists("nxs_saveOption")) { function nxs_saveOption($optName, $val) { 
  if (function_exists('nxs_settings_save') && function_exists('nxs_settings_open')) { $n = nxs_settings_open(); if (!empty($n) && is_array($n)) $n['_opts'][$optName] = $val; nxs_settings_save($n); } else if (function_exists('get_option')) update_option($optName, $val, false); 
}}
if (!function_exists("nxs_getOption")) { function nxs_getOption($optName) { $val = '';
  if (function_exists('nxs_settings_open')) { $n = nxs_settings_open(); if (!empty($n) && !empty($n['_opts']) && !empty($n['_opts'][$optName])) $val = $n['_opts'][$optName]; } else if (function_exists('get_option')) $val = get_option($optName);  return maybe_unserialize($val);
}}

function nxs_toolbar_link_to_mypage( $wp_admin_bar ) { return;
    $args = array(
        'id'    => 'snap-post',
        'title' => '<span style="font-weight:bold; color:#2ecc2e;">{SNAP} </span> New Post to Social Networks',
        'parent' => 'new-content',
        'href'  => '#',        
        'meta'  => array( 'class' => 'my-toolbar-page', 'onclick'  => 'nxs_showNewPostFrom();return false;' )
    );
    $wp_admin_bar->add_node( $args );
}
//## Yo Account 
if (!function_exists("nxs_adjTime")){ function nxs_adjTime($dateTime){ if (!function_exists('get_option')) return $dateTime;
    $tmCorr = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS; $tm = strtotime($dateTime); $frmt = get_option( 'date_format' ).' '.get_option( 'time_format' ); return date($frmt, ($tm+$tmCorr)); 
}}

if (!function_exists("nxs_getCurrTime")){ function nxs_getCurrTime(){ $tm = microtime(); $tma = explode(' ',$tm); $tmCorr = (function_exists('get_option'))?get_option( 'gmt_offset' ) * HOUR_IN_SECONDS :0; $tm = number_format((float)$tma[0]+(float)$tma[1], 8, '.', ''); return $tm+$tmCorr; }}

if (!function_exists("nxs_show_yo_profile_fields")) { function nxs_show_yo_profile_fields( $user ) { ?><h3>Yo account information</h3>
    <table class="form-table"><tr><th><label for="twitter">Yo Username</label></th>
      <td><input type="text" name="nxs_yo" id="nxs_yo" value="<?php echo esc_attr( get_the_author_meta( 'nxs_yo', $user->ID ) ); ?>" class="regular-text" /><br /><span class="description">Please enter your Yo username to receive notifications.</span></td>
    </tr></table><?php 
}}
if (!function_exists("nxs_save_yo_profile_fields")) { function nxs_save_yo_profile_fields( $user_id ) { if ( !current_user_can( 'edit_user', $user_id ) ) return false; update_user_meta( $user_id, 'nxs_yo', $_POST['nxs_yo'] );}}

if (!function_exists("nxs_add_array_sort")) {  function nxs_add_array_sort($a, $b){ $c = array('Social Networks'=>1, 'Blogs/Publishing Platforms'=>2, 'Link Sharing/Boormarks'=>3, 'Email Marketing'=>4, 'Messengers'=>5,  'Image Sharing'=>6,  'Forums'=>7, 'Other'=>8);
    return $c[$a]>$c[$b] ? 1: -1;
}}

if (!function_exists("nxs_arrMergeCheck")) { function nxs_arrMergeCheck($a1, $a2){ foreach ($a2 as $ak=>$a) if (!in_array($ak, array_keys($a1))) $a1[$ak] = $a; return $a1; }}

//## Delete Account
if (!function_exists("ns_delNT_ajax")) { function ns_delNT_ajax(){ check_ajax_referer('nxsSsPageWPN'); $indx = (int)$_POST['id']; 
  global $plgn_NS_SNAutoPoster;  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options; 
  unset($options[$_POST['nt']][$indx]); if (is_array($options)) { update_option('NS_SNAutoPoster', $options); $plgn_NS_SNAutoPoster->nxs_options = $options; }
}}
if (!function_exists("nsAuthFBSv_ajax")) { function nsAuthFBSv_ajax() { check_ajax_referer('nsFB');  $pgID = $_POST['pgID']; $fbs = array();
  global $plgn_NS_SNAutoPoster;  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options;   
  foreach ($options['fb'] as $two) { if ($two['fbPgID']==$pgID) $two['wfa']=time(); $fbs[] = $two; } $options['fb'] = $fbs; if (is_array($options)) { update_option('NS_SNAutoPoster', $options); $plgn_NS_SNAutoPoster->nxs_options = $options; }
}}  

if (!function_exists("nxs_delPostSettings_ajax")) { function nxs_delPostSettings_ajax(){ check_ajax_referer('nxsSsPageWPN'); global $nxs_snapAvNts; $pid = (int)$_POST['pid'];
  foreach ($nxs_snapAvNts as $avNt) delete_post_meta($pid, 'snap'.strtoupper($avNt['code']));  delete_post_meta($pid, 'snap_isAutoPosted'); delete_post_meta($pid, 'snap_MYURL');
  echo "OK"; die();
}}
    
//## Set all posts to Include/exclude from/to Auto-Reposting
if (!function_exists("nxs_SetRpstAll_ajax")) { 
 function nxs_SetRpstAll_ajax() { check_ajax_referer('nxsSsPageWPN'); global $plgn_NS_SNAutoPoster; if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options;//  prr($options[$_POST['t']][$_POST['ii']]);   
   if ($_POST['ed']=='X' || $_POST['ed']=='L') { // prr($options[$_POST['t']][$_POST['ii']]); prr($options); die();
     if ($_POST['ed']=='X') { $options[$_POST['t']][$_POST['ii']]['rpstLastPostID'] = ''; 
       $options[$_POST['t']][$_POST['ii']]['rpstLastShTime'] = ''; $options[$_POST['t']][$_POST['ii']]['rpstLastPostTime'] = '';  $options[$_POST['t']][$_POST['ii']]['rpstNxTime'] = ''; 
     } elseif ($_POST['ed']=='L' && trim($_POST['lpid'])!='' && (int)$_POST['lpid'] > 0) { 
         $post = get_post($_POST['lpid']);
         $options[$_POST['t']][$_POST['ii']]['rpstLastPostTime'] = $post->post_date;
         $options[$_POST['t']][$_POST['ii']]['rpstLastPostID'] = trim($_POST['lpid']);     
     }
     if (is_array($options)) { update_option('NS_SNAutoPoster', $options); $plgn_NS_SNAutoPoster->nxs_options = $options; } //  echo "|".$_POST['t'].$_POST['ii']."|"; prr($options[$_POST['t']][$_POST['ii']]);
   } else { 
    if (!empty($options['nxsCPTSeld'])) $tpArray = maybe_unserialize($options['nxsCPTSeld']); if (!empty($tpArray) && !is_array($tpArray)) $tpArray = array('post'); else $tpArray[] = 'post'; 
    foreach ($tpArray  as $tp) if (!empty($tp)) { 
    $args = array( 'post_type' => $tp, 'post_status' => 'publish', 'numberposts' => 30, 'offset'=> 0, 'fields'=>'ids' ); $posts = get_posts( $args );// prr($args);
    while (count($posts)>0){
      foreach ($posts as $postID){ $pMeta = maybe_unserialize(get_post_meta($postID, 'snap'.strtoupper($_POST['t']), true)); 
        if (!isset($pMeta) || !is_array($pMeta)) $pMeta = array();  if (!isset($pMeta[$_POST['ii']]) || !is_array($pMeta[$_POST['ii']])) $pMeta[$_POST['ii']] = array();// prr($pMeta);
        if ($_POST['ed']!='2') $pMeta[$_POST['ii']]['rpstPostIncl'] = $_POST['ed']=='0'?'0':'nxsi'.$_POST['ii'].$_POST['t'];  else {  $doPost   = false; $exclCats = maybe_unserialize($options['exclCats']); $postCats = wp_get_post_categories($postID);
                                
                                $optMt = $options[$_POST['t']][$_POST['ii']];
                                
                                $optMt['fltrs']['nxs_cats_names'] = implode(',', $optMt['fltrs']['nxs_cats_names']);
                                if ($optMt['fltrsOn'] == '1' && trim($optMt['fltrs']['nxs_cats_names']) != '') {
                                    $inclCats = explode(',', $optMt['fltrs']['nxs_cats_names']);
                                    foreach ($postCats as $pCat) {
                                        if (in_array($pCat, $inclCats)) {
                                            $doPost = true;
                                            //var_dump('found: ' . $postID);    
                                            break;
                                        }
                                    }
                                }
                                
                                if (!$doPost) {
                                    //var_dump('FALSE ' . $postID );
                                }                                
                                if (!$optMt['fltrsOn']) {
                                    $doPost                                              = true;
                                    $pMeta[$_POST['ii']]['do']                           = 1;
                                    $pMeta[$_POST['ii']]['do' . strtoupper($_POST['t'])] = 1;
                                } else {
                                    if (!$doPost) {
                                        $pMeta[$_POST['ii']]['do']                           = 0;
                                        $pMeta[$_POST['ii']]['do' . strtoupper($_POST['t'])] = 0;
                                    }
                                }                               
                                //var_dump('POST:' . $postID, $optMt['fltrsOn'], $doPost, $_POST['ii'], $_POST['t'] );
                                $pMeta[$_POST['ii']]['rpstPostIncl'] = $doPost ? 'nxsi' . $_POST['ii'] . $_POST['t'] : '0';                               
                                
                            } delete_post_meta($postID, 'snap'.strtoupper($_POST['t'])); add_post_meta($postID, 'snap'.strtoupper($_POST['t']), serialize($pMeta));        
      } $args['offset'] = $args['offset']+30;  $posts = get_posts( $args );
    } 
    }
  } echo "OK"; die(); 
}}  
if (!function_exists("nxs_clLgo_ajax")) { function nxs_clLgo_ajax() { check_ajax_referer('nxsSsPageWPN'); global $wpdb;
  //update_option('NS_SNAutoPosterLog', ''); 
  $wpdb->query( 'DELETE FROM '.$wpdb->prefix . 'nxs_log' ); echo "OK";
}} 
if (!function_exists("nxs_rfLgo_ajax")) { function nxs_rfLgo_ajax() { check_ajax_referer('nxsSsPageWPN');  echo "Y:"; nxs_getShowLog(); }} 
if (!function_exists("nxs_getShowLog")) { function nxs_getShowLog() { $logInfo = nxs_getnxsLog();
  if (is_array($logInfo))foreach (($logInfo) as $logline) { $msgSt = "color:#585858;"; if (empty($logline['nttype'])) $logline['nttype'] = '';  if (!empty($logline['uid'])) $uu = ' [UserID:'.$logline['uid'].']'; else $uu = '';
    switch ($logline['type']) {
      case 'M': $actSt = "color:#585858;"; break; case 'S': $actSt = "color:#005800; font-weight:bold;"; break;  case 'BI': $actSt = "color:#0000FF; font-weight:bold;"; break; 
      case 'I': $actSt = "color:#0000FF;"; break; case 'W': $actSt = "color:#0000FF;"; break; case 'A': $actSt = "color:#580058;"; break; case 'GR': $actSt = "color:#580058;"; break; 
      case 'E': $actSt = "color:#FF0000;"; $msgSt = "color:#FF0000;"; break; case 'BG': $msgSt = "color:#008000; font-weight:bold;"; $actSt = "color:#008000; font-weight:bold;"; break;
      default: $actSt = "color:#585858;";     
    }
    switch ($logline['nttype']) {
      case 'BG': $ntSyle = "color:#FF8000;"; break; case 'DI': $ntSyle = "color:#05789B;"; break; case 'DL': $ntSyle = "color:#003DD6;"; break; case 'FB': $ntSyle = "color:#003DD6;"; break; case 'FP': $ntSyle = "color:#D60000;"; break; 
      case 'GP': $ntSyle = "color:#D60000;"; break; case 'IG': $ntSyle = "color:#571F01;"; break; case 'LI': $ntSyle = "color:#006DB0;"; break; case 'LJ': $ntSyle = "color:#009EFF;"; break; case 'PK': $ntSyle = "color:#D60000;"; break;
      case 'PN': $ntSyle = "color:#D60000;"; break; case 'RD': $ntSyle = "color:#67C5FF;"; break; case 'SC': $ntSyle = "color:#0B9700;"; break; case 'SU': $ntSyle = "color:#FF5A00;"; break; case 'TG': $ntSyle = "color:#05789B;"; break;
      case 'TR': $ntSyle = "color:#00396E;"; break; case 'TW': $ntSyle = "color:#05789B;"; break; case 'VB': $ntSyle = "color:#00396E;"; break; case 'VK': $ntSyle = "color:#00396E;"; break; case 'WP': $ntSyle = "color:#00396E;"; break;
      case 'XI': $ntSyle = "color:#00396E;"; break; case 'YT': $ntSyle = "color:#D60000;"; break; 
      default: $ntSyle = "color:#585858;";     
    }
    if (!empty($logline['nt'])) $ntInfo = ' ['.$logline['nt'].'] '; else $ntInfo = '';
    echo '<snap style="color:#008000">['.$logline['date'].']</snap>'.$uu.' - <snap style="'.$actSt.'">['.$logline['act'].']</snap>'.'<snap style="'.$ntSyle.'">'.$ntInfo.'</snap> - <snap style="'.$msgSt.'">'.$logline['msg'].'</snap> '.$logline['extInfo'].'<br/>'; 
  }
}}
//## Upload image as file from URL to remote server
if (!function_exists('nxs_altCurlUploadImg')){ function nxs_altCurlUploadImg( $ch, $r ){ $pstArray = unserialize($r['headers']['nxsPstArr']); $tmp = $r['headers']['nxsUplFile']; $fld = $r['headers']['nxsPstField'];
    unset($r['headers']['nxsPstArr']); unset($r['headers']['nxsUplFile']); unset($r['headers']['nxsPstField']);    
    if (function_exists('curl_file_create')) $file  = curl_file_create($tmp); else $file = '@'.$tmp;  $pstArray[$fld] = $file; $r['body'] = http_build_query($pstArray);  // prr($pstArray);
    if ( !empty( $r['headers'] ) ) { $headers = array(); foreach ( $r['headers'] as $name => $value ) if ($name!=='Content-Length')  $headers[] = "{$name}: $value"; curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );}
    curl_setopt($ch, CURLOPT_POST, TRUE); curl_setopt($ch, CURLOPT_POSTFIELDS, $pstArray); return array('ch'=>$ch, 'r'=>$r);
}}
if (!function_exists('nxs_curlUploadImg')){ function nxs_curlUploadImg($imgURL, $uplURL, $pstArray, $pstField, $ck='') { $remImgURL = urldecode($imgURL); $urlParced = pathinfo($remImgURL); $remImgURLFilename = $urlParced['basename']; 
  $imgType = substr(  $remImgURL, strrpos( $remImgURL , '.' )+1 ); if (stripos($imgType,'?')!==false) $imgType = @reset((explode('?', $imgType))); $ia = array("jpg", "png", "gif", "jpeg"); if (!in_array($imgType, $ia)) $imgType = 'jpg'; 
  $hdrsArr = array('User-Agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36', 'Referer'=>$remImgURL); 
  $advSet = nxs_mkRemOptsArr($hdrsArr);  $imgData = nxs_remote_get($remImgURL, $advSet);// prr($remImgURL);  // prr($imgData);
  if(is_nxs_error($imgData) || empty($imgData['body']) || (!empty($imgData['headers']['content-length']) && (int)$imgData['headers']['content-length']<200) || 
    $imgData['headers']['content-type'] == 'text/html' ||  $imgData['response']['code'] == '403' ) return array('err'=>print_r($imgData, true)); else $imgData = $imgData['body'];  
  $tmpX=array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile()))); 
  if (!is_writable($tmpX)) { $msg = "Can't upload image. Your temporary folder or file (file - ".$tmpX.") is not writable.";
    if (function_exists('wp_upload_dir')) { $uDir = wp_upload_dir(); $tmpX = tempnam($uDir['path'], "nx"); if (!is_writable($tmpX)) return $msg." Your UPLOADS folder or file (file - ".$tmpX.") is not writable. ";} else return $msg;
  } rename($tmpX, $tmpX.='.'.$imgType);  register_shutdown_function(create_function('', "@unlink('{$tmpX}');")); file_put_contents($tmpX, $imgData);  
  $hdrsArr['Content-type'] = 'multipart/form-data'; $hdrsArr['nxsUplFile'] = $tmpX; $hdrsArr['nxsPstArr'] = serialize($pstArray); $hdrsArr['nxsPstField'] = $pstField;  $advSet = nxs_mkRemOptsArr($hdrsArr, $ck, $pstArray); $advSet['postAsArray'] = 1; //prr($advSet);
  $rep = nxs_remote_post($uplURL, $advSet);  @unlink($tmpX); if(is_nxs_error($rep)) return array('err'=>print_r($rep, true)); else return $rep;  
}}
//## Upload Image with Content-Disposition
if (!function_exists('nxs_uploadImgCD')){ function nxs_uploadImgCD($imgURL, $uplURL, $pstArray, $pstField, $ck='') { 
    $imgType = substr(  $imgURL, strrpos( $imgURL , '.' )+1 ); if (stripos($imgType,'?')!==false) $imgType = @reset((explode('?', $imgType))); $ia = array("jpg", "png", "gif", "jpeg"); if (!in_array($imgType, $ia)) $imgType = 'jpg'; 
    $hdrsArr = nxs_makeHeaders($imgURL);  $advSet = nxs_mkRemOptsArr($hdrsArr);  $imgData = nxs_remote_get($imgURL, $advSet);
    if(is_nxs_error($imgData) || empty($imgData['body']) || (!empty($imgData['headers']['content-length']) && (int)$imgData['headers']['content-length']<200) || 
      $imgData['headers']['content-type'] == 'text/html' ||  $imgData['response']['code'] == '403' ) return array('err'=>print_r($imgData, true)); else $imgData = $imgData['body'];  
    //## Post
    $params = ''; if (is_array($pstArray)) foreach ($pstArray as $n=>$v) $params .= "------WebKitFormFQc7dbZE\r\nContent-Disposition: form-data; name=\"".$n."\"\r\n\r\n".$v."\r\n";
    $params  .= "------WebKitFormFQc7dbZE\r\nContent-Disposition: form-data; name=\"".$pstField."\"; filename=\"image.".$imgType."\"\r\nContent-Type: image/".$imgType."\r\n\r\n".$imgData."\r\n------WebKitFormFQc7dbZE--";              
    $hdrsArr = nxs_makeHeaders($imgURL,'','POST'); $hdrsArr['Content-Type']='multipart/form-data; boundary=----WebKitFormFQc7dbZE'; $advSet = nxs_mkRemOptsArr($hdrsArr, $ck, $params);  $rep = nxs_remote_post($uplURL, $advSet); 
    if(is_nxs_error($rep)) return array('err'=>print_r($rep, true)); else return $rep; 
}}


//## ShortCode [nxs_fbembed accnum=0]
if (!function_exists("nxs_postedlinks_func")) {function nxs_postedlinks_func( $atts ) { extract( shortcode_atts( array('accnum' => '0'), $atts ) );  $pid = get_the_ID();   global $nxs_snapAvNts; $txtOut = '';
  global $plgn_NS_SNAutoPoster; if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options;

   foreach ($nxs_snapAvNts as $avNt) { $opt =  get_post_meta($pid, 'snap'.$avNt['code'], true); $opt =  maybe_unserialize($opt); 
       if (!empty($opt) && is_array($opt)) foreach ($opt as $ii=>$op) { 
          if (!empty($op['postURL'])) $txtOut .= '<a href="'.$op['postURL'].'">'.$avNt['name'].(!empty($options[$avNt['lcode']][$ii]['nName'])?' ('.$options[$avNt['lcode']][$ii]['nName'].')':'').'</a><br/>';
       }
   } return $txtOut;
}}

if (function_exists("add_shortcode")) add_shortcode( 'nxs_postedlinks', 'nxs_postedlinks_func' );

//## ShortCode [nxs-ntinsrlist accnum=0]
if (!function_exists("nxs_scInstrList")) {function nxs_scInstrList( $atts ) { extract( shortcode_atts( array('accnum' => '0'), $atts ) ); global $nxs_snapAvNts; 
  $outHtml = '<div class="nxsright"><div style="padding-left: 0px; padding-bottom:5px;"><a style="font-size: 14px;" target="_blank" href="http://www.nextscripts.com/instructions/">'.__('Setup/Installation Instructions:', 'social-networks-auto-poster-facebook-twitter-g').'</a></div>';
  foreach ($nxs_snapAvNts as $avNt) { $clName = 'nxs_snapClass'.$avNt['code']; $nt = new $clName();
    $outHtml .= '<div style="padding-left: 10px; padding-top:5px;"><a style="background-image:url('.NXS_PLURL.'img/'.$avNt['lcode'].'16.png) !important;" class="nxs_icon16" target="_parent" href="'.$nt->ntInfo['instrURL'].'">  '.$nt->ntInfo['name'].'</a></div>';
  }
  $outHtml .= '</div>'; return $outHtml;
}}

if (function_exists("add_shortcode")) add_shortcode( 'nxs-ntinsrlist', 'nxs_scInstrList' );

?>