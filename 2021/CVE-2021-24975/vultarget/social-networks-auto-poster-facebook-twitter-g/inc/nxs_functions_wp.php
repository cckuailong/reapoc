<?php
if (!function_exists('nxs_getPostImage')){ function nxs_getPostImage($postID, $size='large', $def='') { $imgURL = '';  
  global $plgn_NS_SNAutoPoster;  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options; $options['sImg'] = (defined('NXSAPIVER') && NXSAPIVER == '2.15.11')?1:0; 
  if (empty($options['imgNoCheck'])) $options['imgNoCheck'] = 0; if (empty($options['useUnProc'])) $options['useUnProc'] = 0;
  if (empty($options['imgNoCheck']) || $options['imgNoCheck'] != '1') { $indx = rand(0, 2); 
    $iTstArr = array('https://www.bing.com/s/a/hpc12.png','https://www.apple.com/global/elements/flags/16x16/usa_2x.png','https://www.google.com/logos/2004/winter_holiday_04_sah.gif'); 
    $imgURL = $iTstArr[$indx]; $res = nxs_chckRmImage($imgURL); $imgURL = ''; if (!$res) $options['imgNoCheck'] = '1';
  } if ($options['sImg']==1) return nsx_doDecode($options['useSSLCert']).'/logo2.png';
  //## Featured Image from Specified Location
  if ((int)$postID>0 && isset($options['featImgLoc']) && $options['featImgLoc']!=='') {  $afiLoc= get_post_meta($postID, $options['featImgLoc'], true); 
    if (is_array($afiLoc) && $options['featImgLocArrPath']!='') { $cPath = $options['featImgLocArrPath'];
      while (strpos($cPath, '[')!==false){ $arrIt = CutFromTo($cPath, '[', ']'); $arrIt = str_replace("'", "", str_replace('"', '', $arrIt)); $afiLoc = $afiLoc[$arrIt]; $cPath = substr($cPath, strpos($cPath, ']'));}    
    } $imgURL = ""; if (trim($afiLoc) !="") $imgURL = trim($options['featImgLocPrefix']) . trim($afiLoc); if ($imgURL!='' && stripos($imgURL, 'http')===false) $imgURL =  home_url().$imgURL;
  }
  if ($imgURL!='' && $options['imgNoCheck']!='1' && nxs_chckRmImage($imgURL)==false) $imgURL = '';  if ($imgURL!='') return $imgURL;
  //## Featured Image
  if ($imgURL=='') { if ((int)$postID>0 && function_exists("get_post_thumbnail_id") && function_exists('has_post_thumbnail') && has_post_thumbnail($postID) ){ 
    $imgURL = wp_get_attachment_image_src(get_post_thumbnail_id($postID), $size); $imgURL = $imgURL[0]; if ((trim($imgURL)!='')  && substr($imgURL, 0, 4)!='http') $imgURL = site_url($imgURL);
  }} 
  if ($imgURL!='' && $options['imgNoCheck']!='1' && nxs_chckRmImage($imgURL)==false) $imgURL = ''; if ($imgURL!='') return $imgURL;  
  //## plugin/categories-images
  if ((int)$postID>0 && function_exists('z_taxonomy_image_url')) {  $post_categories = wp_get_post_categories( $postID );
    foreach($post_categories as $c){ $cat = get_category( $c );  $imgURL = trim(z_taxonomy_image_url($cat->term_id)); if ($imgURL!='') break; }
    if ($imgURL!='' && substr($imgURL, 0, 4)!='http') {
      $stURL = site_url(); if (substr($stURL, -1)=='/') $stURL = substr($stURL, 0, -1);  if ($imgURL!='') $imgURL = $stURL.$imgURL; 
    }
  }
  if ($imgURL!='' && $options['imgNoCheck']!='1' && nxs_chckRmImage($imgURL)==false) $imgURL = ''; if ($imgURL!='') return $imgURL;
  //## YAPB
  if ((int)$postID>0 && class_exists("YapbImage")) { $imgURLObj = YapbImage::getInstanceFromDb($postID); if (is_object($imgURLObj)) $imgURL = $imgURLObj->uri; 
    $stURL = site_url(); if (substr($stURL, -1)=='/') $stURL = substr($stURL, 0, -1);  if ($imgURL!='') $imgURL = $stURL.$imgURL; 
  }
  if ($imgURL!='' && $options['imgNoCheck']!='1' && nxs_chckRmImage($imgURL)==false) $imgURL = ''; if ($imgURL!='') return $imgURL;
  //## Find Images in Post
  if ((int)$postID>0 && $imgURL=='') {$post = get_post($postID); $imgsFromPost = nsFindImgsInPost($post, $options['useUnProc'] == '1'); if (is_array($imgsFromPost) && count($imgsFromPost)>0) $imgURL = $imgsFromPost[0]; } //echo "##".count($imgsFromPost); prr($imgsFromPost);
  if ($imgURL!='' && $options['imgNoCheck']!='1' && nxs_chckRmImage($imgURL)==false) $imgURL = ''; if ($imgURL!='') return $imgURL;
  //## Attachements
  if ((int)$postID>0 && $imgURL=='') { $attachments = get_posts(array('post_type' => 'attachment', 'posts_per_page' => -1, 'post_parent' => $postID)); 
      if (is_array($attachments) && count($attachments)>0 && is_object($attachments[0])) { $imgURL = wp_get_attachment_image_src($attachments[0]->ID, $size); $imgURL = $imgURL[0]; }     
  }
  if ($imgURL!='' && $options['imgNoCheck']!='1' && nxs_chckRmImage($imgURL)==false) $imgURL = ''; if ($imgURL!='') return $imgURL;    
  //## Default
  if (trim($imgURL)=='' && trim($def)=='') $imgURL = $options['ogImgDef']; 
  if (trim($imgURL)=='' && trim($def)!='') $imgURL = $def; 

  return $imgURL;
}}
if (!function_exists('nsFindImgsInPost')){function nsFindImgsInPost($post, $advImgFnd=false) { global $ShownAds; if (isset($ShownAds)) $ShownAdsL = $ShownAds;  $postImgs = array(); if (!is_object($post)) return;
  if ($advImgFnd) $postCntEx = apply_filters('the_content', $post->post_excerpt); else $postCntEx = $post->post_excerpt;   
  if ($advImgFnd) $postCnt = apply_filters('the_content', $post->post_content); else $postCnt = $post->post_content; 
  $postCnt = $postCntEx.$postCnt;
  //$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $postCnt, $matches ); if ($output === false){return false;} 
  //$postCnt = str_replace("'",'"',$postCnt); $output = preg_match_all( '/src="([^"]*)"/', $postCnt, $matches ); if ($output === false){return false;}
  $postCnt = str_replace("'",'"',$postCnt); $output = preg_match_all( '/< *img[^>]*src *= *["\']?([^"\']*)/i', $postCnt, $matches ); // prr($matches);  
  if ($output === false || $output == 0){ $vids = nsFindVidsInPost($post, $advImgFnd==false); if (count($vids)>0)  $postImgs[] = 'http://img.youtube.com/vi/'.$vids[0].'/0.jpg';  else return false;} 
    else { foreach ($matches[1] as $match) { if (!preg_match('/^https?:\/\//', $match ) ) $match = site_url( '/' ) . ltrim( $match, '/' ); $postImgs[] = $match;} if (isset($ShownAds)) $ShownAds = $ShownAdsL; }  
  return $postImgs;
}}
if (!function_exists('nsFindAudioInPost')){function nsFindAudioInPost($post, $raw=true) {  //### !!!   $raw=false Breaks ob_start() [ref.outcontrol]: Cannot use output buffering in output buffering display handlers - Investigate
  global $ShownAds; if (isset($ShownAds)) $ShownAdsL = $ShownAds; $postVids = array();
  if (is_object($post)) { if ($raw) $postCnt = $post->post_content; else $postCnt = apply_filters('the_content', $post->post_content); } else $postCnt = $post;
  $regex_pattern = "((https?|ftp|gopher|telnet|file|notes|ms-help):((//)|(\\\\))+[\w\d:#@%/;$()~_?\+-=\\\.&]*\.(mp3|aac|m4a))";
  $output = preg_match_all( $regex_pattern, $postCnt, $matches );  if ($output === false){return false;}    
  foreach ($matches[0] as $match) { $postAu[] = $match; } $postAu = array_unique($postAu); if (isset($ShownAds)) $ShownAds = $ShownAdsL; return $postAu;
}}

if (!function_exists('nsFindVidsInPost')){function nsFindVidsInPost($post, $raw=true) {  //### !!!  $raw=false ## Breaks ob_start() [ref.outcontrol]: Cannot use output buffering in output buffering display handlers - Investigate
  global $ShownAds; if (isset($ShownAds)) $ShownAdsL = $ShownAds; $postVids = array();
  if (is_object($post)) { if ($raw) $postCnt = $post->post_content; else $postCnt = apply_filters('the_content', $post->post_content); } else $postCnt = $post; //prr($postCnt);
  $postCnt = preg_replace('/youtube.com\/vi\/(.*)\/(.*).jpg/isU', "youtube.com/v/$1/", $postCnt);  
  $output = preg_match_all( '@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?(#[a-z_.-][a-z0-9+\$_.-]*)?)*)@', $postCnt, $matches ); if ($output === false){return false;} 
  foreach ($matches[0] as $match) {  
     $output2 = preg_match_all( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"<>&?/ ]{11})%i', $match, $matches2 ); if ($output2 === false){return false;} 
     foreach ($matches2[1] as $match2) {  $match2 = trim($match2); if (strlen($match2)==11) $postVids[] = $match2;} 
     $output3 = preg_match_all( '/^https?:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/', $match, $matches3 );  if ($output3 === false){return false;} 
     foreach ($matches3[3] as $match3) {  $match3 = trim($match3); if (strlen($match3)==8 || strlen($match3)==9) $postVids[] = $match3;} 
     $output3 = preg_match_all( '#https?://(player\.)?vimeo\.com(/video)?/(\d+)#i', $match, $matches3 );  if ($output3 === false){return false;} 
     foreach ($matches3[3] as $match3) {  $match3 = trim($match3); if (strlen($match3)==8 || strlen($match3)==9) $postVids[] = $match3;}      
     $output3 = preg_match_all( '#https?://(www\.)?facebook\.com/video\.php\?v=(\d+)#i', $match, $matches3 ); if ($output3 === false){return false;} 
     foreach ($matches3[2] as $match3) {  $match3 = trim($match3); if (strlen($match3)==15) $postVids[] = $match3;} 
     $output3 = preg_match_all( '#https?://(www\.)?facebook\.com/video/embed(/)?\?video_id=(\d+)#i', $match, $matches3 ); if ($output3 === false){return false;} 
     foreach ($matches3[3] as $match3) {  $match3 = trim($match3); if (strlen($match3)==15) $postVids[] = $match3;} 
  }   $postVids = array_unique($postVids); if (isset($ShownAds)) $ShownAds = $ShownAdsL; return $postVids;  
}}


if (!function_exists('nxsUserProfileSettings')){ function nxsUserProfileSettings( $user ) { global $wp_roles; return;

        if ( ! is_super_admin() && ! $user->has_cap( 'administrator' ) )  return;
        
        
        $nxs_roles = array();
        foreach ( $wp_roles->roles as $role => $role_value ) {
            if ( substr( $role, 0, 4 ) != 'nxs_' ) {
                continue;
            }
            $nxs_roles[ $role ] = $role_value;
        }

        
        if ( ! empty( $user->roles[ 0 ] ) && in_array( $user->roles[ 0 ], array_keys( $nxs_roles ), true ) ) {
            return;
        }

        ?>
            <h3><?php echo 'NextScripts User Rights' ?></h3>
            <table class="form-table">
                <tr>
                    <th>
                        <label for="nxs_role"><?php _e( 'Add NXS Role', 'nxsuse' ); ?></label>
                    </th>
                    <td>
                        <select name="nxs_role" id="nxs_role" style="display:inline-block; float:none;">
                            <option value=""><?php _e( '&mdash; No additional role for NXS &mdash;', 'backwpup' ); ?></option>
                            <?php
                            foreach ( $nxs_roles as $role => $role_value ) {
                                echo '<option value="'.$role.'" '. selected( $user->has_cap( $role ), TRUE, FALSE ) .'>'. $role_value[ 'name' ] . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
        <?php
    }
}
?>