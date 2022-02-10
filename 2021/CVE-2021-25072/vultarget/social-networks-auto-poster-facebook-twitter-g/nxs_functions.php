<?php
if (!function_exists('prr')){ function prr($str,$id='') { echo $id."<pre>"; print_r($str); echo "</pre>\r\n"; }}
if (!function_exists('nsx_stripSlashes')){ function nsx_stripSlashes(&$value){$value = stripslashes($value);}}
if (!function_exists('nsx_fixSlashes')){ function nsx_fixSlashes(&$value){ while (strpos($value, '\\\\')!==false) $value = str_replace('\\\\','\\',$value);
   if (strpos($value, "\\'")!==false) $value = str_replace("\\'","'",$value); if (strpos($value, '\\"')!==false) $value = str_replace('\\"','"',$value);
}}
if (!function_exists('CutFromTo')){ function CutFromTo($string, $from, $to){$fstart = stripos($string, $from); $tmp = substr($string,$fstart+strlen($from)); $flen = stripos($tmp, $to);  return substr($tmp,0, $flen);}}
if (!function_exists('nsx_doEncode')){ function nsx_doEncode($string,$key='NSX') { $key = sha1($key); $strLen = strlen($string);$keyLen = strlen($key); $j = 0; $hash = '';
  for ($i = 0; $i < $strLen; $i++) { $ordStr = ord(substr($string,$i,1)); if ($j == $keyLen) $j = 0; $ordKey = ord(substr($key,$j,1)); $j++; $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));} return $hash;
}}
if (!function_exists('nsx_doDecode')){ function nsx_doDecode($string,$key='NSX') { $key = sha1($key); $keyLen = strlen($key); $hash = ''; $sX = str_split($string, 2560);
  foreach($sX as $ss){$j=0; $sA=str_split($ss, 2); foreach($sA as $oS){$oS=hexdec(base_convert(strrev($oS),36,16)); if ($j==$keyLen) $j=0; $oK=ord(substr($key,$j,1)); $j++; $hash.=chr($oS-$oK);}} return $hash;
}}
if (!function_exists('nxs_decodeEntitiesFull')){ function nxs_decodeEntitiesFull($string, $quotes = ENT_COMPAT, $charset = 'utf-8') {
  return html_entity_decode(preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/', 'nxs_convertEntity', $string), $quotes, $charset); 
}}
if (!function_exists('nxs_substr')){ function nxs_substr($str, $start){ preg_match_all("/./su", $str, $ar);
   if(func_num_args() >= 3) { $end = func_get_arg(2); return join("",array_slice($ar[0],$start,$end)); } else return join("",array_slice($ar[0],$start));
}}
if (!function_exists('utf8_decode')){ function utf8_decode($str) { return $str; }}
if (!function_exists('nxs_strLen')){ function nxs_strLen($str) { return count(str_split(utf8_decode($str))); }}
if (!function_exists('nxs_convertEntity')){ function nxs_convertEntity($matches, $destroy = true) {
  static $table = array('quot' => '&#34;','amp' => '&#38;','lt' => '&#60;','gt' => '&#62;','apos' => '&#39;','OElig' => '&#338;','oelig' => '&#339;','Scaron' => '&#352;','scaron' => '&#353;','Yuml' => '&#376;','circ' => '&#710;','tilde' => '&#732;','ensp' => '&#8194;','emsp' => '&#8195;','thinsp' => '&#8201;','zwnj' => '&#8204;','zwj' => '&#8205;','lrm' => '&#8206;','rlm' => '&#8207;','ndash' => '&#8211;','mdash' => '&#8212;','lsquo' => '&#8216;','rsquo' => '&#8217;','sbquo' => '&#8218;','ldquo' => '&#8220;','rdquo' => '&#8221;','bdquo' => '&#8222;','dagger' => '&#8224;','Dagger' => '&#8225;','permil' => '&#8240;','lsaquo' => '&#8249;','rsaquo' => '&#8250;','euro' => '&#8364;','fnof' => '&#402;','Alpha' => '&#913;','Beta' => '&#914;','Gamma' => '&#915;','Delta' => '&#916;','Epsilon' => '&#917;','Zeta' => '&#918;','Eta' => '&#919;','Theta' => '&#920;','Iota' => '&#921;','Kappa' => '&#922;','Lambda' => '&#923;','Mu' => '&#924;','Nu' => '&#925;','Xi' => '&#926;','Omicron' => '&#927;','Pi' => '&#928;','Rho' => '&#929;','Sigma' => '&#931;','Tau' => '&#932;','Upsilon' => '&#933;','Phi' => '&#934;','Chi' => '&#935;','Psi' => '&#936;','Omega' => '&#937;','alpha' => '&#945;','beta' => '&#946;','gamma' => '&#947;','delta' => '&#948;','epsilon' => '&#949;','zeta' => '&#950;','eta' => '&#951;','theta' => '&#952;','iota' => '&#953;','kappa' => '&#954;','lambda' => '&#955;','mu' => '&#956;','nu' => '&#957;','xi' => '&#958;','omicron' => '&#959;','pi' => '&#960;','rho' => '&#961;','sigmaf' => '&#962;','sigma' => '&#963;','tau' => '&#964;','upsilon' => '&#965;','phi' => '&#966;','chi' => '&#967;','psi' => '&#968;','omega' => '&#969;','thetasym' => '&#977;','upsih' => '&#978;','piv' => '&#982;','bull' => '&#8226;','hellip' => '&#8230;','prime' => '&#8242;','Prime' => '&#8243;','oline' => '&#8254;','frasl' => '&#8260;','weierp' => '&#8472;','image' => '&#8465;','real' => '&#8476;','trade' => '&#8482;','alefsym' => '&#8501;','larr' => '&#8592;','uarr' => '&#8593;','rarr' => '&#8594;','darr' => '&#8595;','harr' => '&#8596;','crarr' => '&#8629;','lArr' => '&#8656;','uArr' => '&#8657;','rArr' => '&#8658;','dArr' => '&#8659;','hArr' => '&#8660;','forall' => '&#8704;','part' => '&#8706;','exist' => '&#8707;','empty' => '&#8709;','nabla' => '&#8711;','isin' => '&#8712;','notin' => '&#8713;','ni' => '&#8715;','prod' => '&#8719;','sum' => '&#8721;','minus' => '&#8722;','lowast' => '&#8727;','radic' => '&#8730;','prop' => '&#8733;','infin' => '&#8734;','ang' => '&#8736;','and' => '&#8743;','or' => '&#8744;','cap' => '&#8745;','cup' => '&#8746;','int' => '&#8747;','there4' => '&#8756;','sim' => '&#8764;','cong' => '&#8773;','asymp' => '&#8776;','ne' => '&#8800;','equiv' => '&#8801;','le' => '&#8804;','ge' => '&#8805;','sub' => '&#8834;','sup' => '&#8835;','nsub' => '&#8836;','sube' => '&#8838;','supe' => '&#8839;','oplus' => '&#8853;','otimes' => '&#8855;','perp' => '&#8869;','sdot' => '&#8901;','lceil' => '&#8968;','rceil' => '&#8969;','lfloor' => '&#8970;','rfloor' => '&#8971;','lang' => '&#9001;','rang' => '&#9002;','loz' => '&#9674;','spades' => '&#9824;','clubs' => '&#9827;','hearts' => '&#9829;','diams' => '&#9830;','nbsp' => '&#160;','iexcl' => '&#161;','cent' => '&#162;','pound' => '&#163;','curren' => '&#164;','yen' => '&#165;','brvbar' => '&#166;','sect' => '&#167;','uml' => '&#168;','copy' => '&#169;','ordf' => '&#170;','laquo' => '&#171;','not' => '&#172;','shy' => '&#173;','reg' => '&#174;','macr' => '&#175;','deg' => '&#176;','plusmn' => '&#177;','sup2' => '&#178;','sup3' => '&#179;','acute' => '&#180;','micro' => '&#181;','para' => '&#182;','middot' => '&#183;','cedil' => '&#184;','sup1' => '&#185;','ordm' => '&#186;','raquo' => '&#187;','frac14' => '&#188;','frac12' => '&#189;','frac34' => '&#190;','iquest' => '&#191;','Agrave' => '&#192;','Aacute' => '&#193;','Acirc' => '&#194;','Atilde' => '&#195;','Auml' => '&#196;','Aring' => '&#197;','AElig' => '&#198;','Ccedil' => '&#199;','Egrave' => '&#200;','Eacute' => '&#201;','Ecirc' => '&#202;','Euml' => '&#203;','Igrave' => '&#204;','Iacute' => '&#205;','Icirc' => '&#206;','Iuml' => '&#207;','ETH' => '&#208;','Ntilde' => '&#209;','Ograve' => '&#210;','Oacute' => '&#211;','Ocirc' => '&#212;','Otilde' => '&#213;','Ouml' => '&#214;','times' => '&#215;','Oslash' => '&#216;','Ugrave' => '&#217;','Uacute' => '&#218;','Ucirc' => '&#219;','Uuml' => '&#220;','Yacute' => '&#221;','THORN' => '&#222;','szlig' => '&#223;','agrave' => '&#224;','aacute' => '&#225;','acirc' => '&#226;','atilde' => '&#227;','auml' => '&#228;','aring' => '&#229;','aelig' => '&#230;','ccedil' => '&#231;','egrave' => '&#232;','eacute' => '&#233;','ecirc' => '&#234;','euml' => '&#235;','igrave' => '&#236;','iacute' => '&#237;','icirc' => '&#238;','iuml' => '&#239;','eth' => '&#240;','ntilde' => '&#241;','ograve' => '&#242;','oacute' => '&#243;','ocirc' => '&#244;','otilde' => '&#245;','ouml' => '&#246;','divide' => '&#247;','oslash' => '&#248;','ugrave' => '&#249;','uacute' => '&#250;','ucirc' => '&#251;','uuml' => '&#252;','yacute' => '&#253;','thorn' => '&#254;','yuml' => '&#255;');
  if (isset($table[$matches[1]])) return $table[$matches[1]];
  // else 
  return $destroy ? '' : $matches[0];
}}

if (!function_exists('nsGetYTThumb')){function nsGetYTThumb($yt) {  
  $out = 'http://img.youtube.com/vi/'.$yt.'/maxresdefault.jpg'; $response  = nxs_remote_get($out); 
  if (is_nxs_error($response) || $response['response']['code']!='200' ) { $out = 'http://img.youtube.com/vi/'.$yt.'/sddefault.jpg';  
    $response  = nxs_remote_get($out); if (is_nxs_error($response) || $response['response']['code']!='200' ) $out = 'http://img.youtube.com/vi/'.$yt.'/0.jpg';
  } return $out;  
}}
if (!function_exists('nsTrnc')){ function nsTrnc($string, $limit, $break=" ", $pad=" ...") { if(nxs_strLen($string) <= $limit) return $string; if(nxs_strLen($pad) >= $limit) return ''; $string = nxs_substr($string, 0, $limit-nxs_strLen($pad)); 
  if (function_exists('mb_strripos')) $brLoc = mb_strripos($string, $break); else $brLoc = strripos(utf8_decode($string), $break);  if ($brLoc===false) return $string.$pad; else return nxs_substr($string, 0, $brLoc).$pad; 
}}
if (!function_exists('nsSubStrEl')){ function nsSubStrEl($string, $length, $end='...'){ if (strlen($string) > $length){ $length -= strlen($end); $string  = substr($string, 0, $length); $string .= $end; } return $string;}}

if (!function_exists('nxs_snapCleanHTML')){ function nxs_snapCleanHTML($html) { 
    $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $html); $html = preg_replace('/<!--(.*)-->/Uis', "", $html); return $html;
}}
if (!function_exists('nxs_chckRmImage')){function nxs_chckRmImage($url, $chType='head'){ if( ini_get('allow_url_fopen')=='1' && @getimagesize($url)!==false) return true;
  $hdrsArr = nxs_getNXSHeaders('http://www.google.com'); $nxsWPRemWhat = 'nxs_remote_'.$chType; $url = str_replace(' ', '%20', $url);  $advSet = nxs_mkRemOptsArr($hdrsArr); $rsp  = $nxsWPRemWhat($url, $advSet);  
  if(is_nxs_error($rsp)) { nxs_addToLogN('E', 'Error', 'IMAGE', '-=ERROR=- Server can\'t access it\'s own images. (Image URL: '.$url.') Most probably it\'s a DNS problem. Please contact your hosting provider. '.serialize($rsp), ''); return false; }
  if (is_array($rsp) && ($rsp['response']['code']=='200' || ( $rsp['response']['code']=='403' &&  $rsp['headers']['server']=='cloudflare-nginx') )) return true; 
    else { if ($chType=='head') { return  nxs_chckRmImage($url, 'get'); } else { nxs_addToLogN('E', 'Error', 'IMAGE', '-=ERROR=- Server can\'t access it\'s own images. (Image URL: '.$url.') Most probably it\'s a DNS problem. Please contact your hosting provider. '.serialize($rsp), $url); return false; }
    } 
}}

if (!function_exists('nxs_makeURLParams')){ function nxs_makeURLParams($params) {  global $plgn_NS_SNAutoPoster;  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options;
    if (!isset($options['addURLParams']) || $options['addURLParams']=='') return false; else $templ = $options['addURLParams'];
    if (preg_match('%NTNAME%', $templ)) $templ = str_ireplace("%NTNAME%", urlencode($params['NTNAME']), $templ);
    if (preg_match('%NTCODE%', $templ)) $templ = str_ireplace("%NTCODE%", urlencode($params['NTCODE']), $templ);
    if (preg_match('%ACCNAME%', $templ)) $templ = str_ireplace("%ACCNAME%", urlencode($params['ACCNAME']), $templ);
    if (preg_match('%POSTID%', $templ)) $templ = str_ireplace("%POSTID%", urlencode($params['POSTID']), $templ);
    if (preg_match('%POSTTITLE%', $templ)) { $post = get_post($params['POSTID']); if (is_object($post)) {$postName = $post->post_title; $templ = str_ireplace("%POSTTITLE%", urlencode($postName), $templ);}}
    if (preg_match('%SITENAME%', $templ)) { $siteTitle = urlencode(htmlspecialchars_decode(get_bloginfo('name'), ENT_QUOTES)); $templ = str_ireplace("%SITENAME%", $siteTitle, $templ); }
    return $templ;
}}

if (!function_exists("nxs_tiny_mce_before_init")) { function nxs_tiny_mce_before_init($init) { global $tinymce_version; 
  if (substr($tinymce_version,0,1)<4) $init['setup'] = "function( ed ) { ed.onChange.add( function( ed, e ) {  nxs_updateGetImgsX( e );   }); }"; else
    $init['setup'] = "function(ed) {ed.on('NodeChange', function(e){nxs_updateGetImgsX(e);});}";     
    return $init;
}}

//## CSS && JS
if (!function_exists("jsPostToSNAP")) { function jsPostToSNAP() {  global $nxs_snapAvNts, $nxs_plurl; ?>
    <script type="text/javascript" >  
    function nxs_updateGetImgsX(e){ }
    jQuery(document).on('change', '#content', function( e ) { nxs_updateGetImgsX( e ); });
    
    function nxs_updateGetImgsXX(e){ 
      var targetId = e.target.id; 
      var text = 'Kortinko';
      switch ( targetId ) {
         case 'content':
             text = jQuery('#content').val(); 
             break;
         case 'tinymce':
             if ( tinymce.activeEditor ) text = tinymce.activeEditor.getContent();
             break;
      }
      jQuery('.nxs_imgPrevList').html( text );
    }    
    function nxs_clPrvImgShow(tIdN){ jQuery("#isAutoImg-"+tIdN).trigger('click'); jQuery("#isAutoImg-"+tIdN).trigger('click');  }    
    function nxs_clPrvImg(id, ii){ jQuery("#imgToUse-"+ii).val(jQuery("#"+id+" img").attr('src')); jQuery(".nxs_prevIDiv"+ii+" .nxs_checkIcon").hide();
      jQuery(".nxs_prevIDiv"+ii).removeClass("nxs_chImg_selDiv"); jQuery(".nxs_prevIDiv"+ii+" img").removeClass("nxs_chImg_selImg"); 
      jQuery("#"+id+" img").addClass("nxs_chImg_selImg"); jQuery("#"+id).addClass("nxs_chImg_selDiv"); jQuery("#"+id+" .nxs_checkIcon").show();
    }    
    function nxs_getOriginalWidthOfImg(img_element) { var t = new Image();  t.src = (img_element.getAttribute ? img_element.getAttribute("src") : false) || img_element.src; /* alert(t.src+" | "+t.width); */ return t.width; }        
    function nxs_updateGetImgs(e){ 
        var textOut=''; var text = '';
        var tId = e.target.id; 
        var tIdN = tId.replace("isAutoImg-", "");
        if ( tinymce.activeEditor ) text = tinymce.activeEditor.getContent();
        if ( text == '' ) text = jQuery('#content').val();                
        
        jQuery('#NS_SNAP_AddPostMetaTags').append('<div id="nxs_tempDivImgs" style="display: none;"></div>'); jQuery('#nxs_tempDivImgs').append(text);
        var textOutA = new Array(); var currSelImg =  jQuery("#imgToUse-"+tIdN).val();
                
        textOutA.push('http://cdn.gtln.us/img/nxs/noImgC.png');  
        
        var fImg = jQuery('#set-post-thumbnail > img').attr('src'); if (fImg!='' && fImg!=undefined) { textOutA.push(fImg); if (currSelImg=='') currSelImg = fImg; }        
        var fImg = jQuery('#yapbdiv img').attr('src'); if (fImg!='' && fImg!=undefined) { textOutA.push(fImg); if (currSelImg=='') currSelImg = fImg; }
        
        jQuery('#nxs_tempDivImgs img').each(function(){ var prWidth; prWidth = nxs_getOriginalWidthOfImg(this); if (prWidth!=1) textOutA.push(jQuery(this).attr('src'));  });                
        jQuery('#nxs_tempDivImgs').remove();
        var index;  for (index = 0; index < textOutA.length; ++index) { var isSel = currSelImg == textOutA[index] ? 'nxs_chImg_selImg' : ''; var isSelDisp = currSelImg == textOutA[index] ? 'style="display:block;"' : ''; 
          textOut = textOut + '<div class="nxs_prevIDiv'+tIdN+' nxs_prevImagesDiv" id="nxs_idiv'+tIdN+index+'" onclick="nxs_clPrvImg(\'nxs_idiv'+tIdN+index+'\', \''+tIdN+'\');"><img class="nxs_prevImages '+isSel+'" src="'+textOutA[index]+'"><div '+isSelDisp+' class="nxs_checkIcon"><div class="media-modal-icon"></div></div></div>'; 
        }
        jQuery('#imgPrevList-'+tIdN).html( textOut );
        if (jQuery('#'+tId).is(":checked")) jQuery('#imgPrevList-'+tIdN).hide(); else {  jQuery('#nxs_'+tIdN+'_idivD').hide(); jQuery('#imgPrevList-'+tIdN).show();  }
        
    }
    
    jQuery(document).on('change', '.isAutoURL', function( e ) {    var tId = e.target.id; var tIdN = tId.replace("isAutoURL-", "");
       if (jQuery('#'+tId).is(":checked")) { jQuery('#isAutoURLFld-'+tIdN).hide(); jQuery('#URLToUse-'+tIdN).val(''); } else { jQuery('#isAutoURLFld-'+tIdN).show(); }
    });    
    jQuery(document).on('change', '.isAutoImg', function( e ) {   
        nxs_updateGetImgs( e );
    });    
    jQuery(document).on('change', '#wp-content-editor-container #conXXtent', function() {
        nxs_updateGetImgs();
    });
    jQuery(document).on('change', '#tinXXymce', function() {
        nxs_updateGetImgs();
    });       
    jQuery(document).ready(function($) {
    
      //jQuery('.nxstbldo').hide();          
            
      jQuery('input#riToFB_button').click(function() { var data = { action: 'rePostToFB', id: jQuery('input#post_ID').val(), ri:1, nid:jQuery(this).attr('alt'), _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}; callAjSNAP(data, 'Facebook'); });            
      jQuery('input#riToTW_button').click(function() { var data = { action: 'rePostToTW', id: jQuery('input#post_ID').val(), ri:1, nid:jQuery(this).attr('alt'), _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}; callAjSNAP(data, 'Twitter'); });
    
       function callAjSNAP(data, label) { 
            var style = "position: fixed; display: none; z-index: 1000; top: 50%; left: 50%; background-color: #E8E8E8; border: 1px solid #555; padding: 15px; width: 350px; min-height: 80px; margin-left: -175px; margin-top: -40px; text-align: center; vertical-align: middle;";
            jQuery('body').append("<div id='test_results' style='" + style + "'></div>");
            jQuery('#test_results').html("<p>Sending update to "+label+"</p>" + "<p><img src='<?php echo $nxs_plurl; ?>img/ajax-loader-med.gif' /></p>");
            jQuery('#test_results').show();            
            jQuery.post(ajaxurl, data, function(response) { if (response=='') response = 'Message Posted';
                jQuery('#test_results').html('<p> ' + response + '</p>' +'<input type="button" class="button" name="results_ok_button" id="results_ok_button" value="OK" />');
                jQuery('#results_ok_button').click(remove_results);
            });            
        }        
        function remove_results() { jQuery("#results_ok_button").unbind("click");jQuery("#test_results").remove();
            if (typeof document.body.style.maxHeight == "undefined") { jQuery("body","html").css({height: "auto", width: "auto"}); jQuery("html").css("overflow","");}
            document.onkeydown = "";document.onkeyup = "";  return false;
        }
    });
    </script>    
    <?php
  }
}
if (!function_exists("nxs_jsPostToSNAP2")){ function nxs_jsPostToSNAP2() { global $nxs_snapAvNts, $nxs_snapThisPageUrl, $plgn_NS_SNAutoPoster, $nxs_plurl; 
   if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options; 
?>
            
<script type="text/javascript">   

  jQuery(document).ready(function($) {<?php  if (!empty($options['howToShowNTS']) && $options['howToShowNTS']=='C') { ?> 
      jQuery('.nxstbldo').hide();  jQuery('.nxs_ldos').html('[+]');  <?php } elseif (!empty($options['howToShowNTS']) && $options['howToShowNTS']=='E') { ?> jQuery('.nxstbldo').show();  jQuery('.nxs_ldos').html('[-]'); 
    <?php } ?>
  });



  function seFBA(pgID,fbAppID,fbAppSec){ var data = { pgID: pgID, action: 'nsAuthFBSv', _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}; 
    jQuery.post(ajaxurl, data, function(response) {  
      window.location = "https://www.facebook.com/dialog/oauth?client_id="+fbAppID+"&client_secret="+fbAppSec+"&scope=publish_stream,offline_access,read_stream,manage_pages&redirect_uri=<?php echo $nxs_snapThisPageUrl;?>";
    });                       
  }
  function doLic(){ var lk = jQuery('#eLic').val();  jQuery.post(ajaxurl,{lk:lk, action: 'nxsDoLic', id: 0, _wpnonce: jQuery('input#doLic_wpnonce').val()}, function(j){ 
      if (jQuery.trim(j)=='OK') window.location = "<?php echo $nxs_snapThisPageUrl; ?>"; else alert('<?php _e('Wrong key, please contact support', 'social-networks-auto-poster-facebook-twitter-g'); ?>');
    }, "html")
  }
  
  function testPost(nt, nid){  var data = {  action:'nxs_snap_aj', nxsact: 'testPost', nt:nt, id: 0, nid: nid, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()};
     jQuery('#nxs_gPopupContent').html("<p>Sending update to "+nt+" ....</p>" + "<p><img src='<?php echo NXS_PLURL; ?>img/ajax-loader-med.gif' /></p>");
     jQuery('#nxs_gPopup').bPopup({ modalClose: false, appendTo: '#nsStForm', opacity: 0.6, positionStyle: 'fixed'});  
     jQuery.post(ajaxurl, data, function(response) { if (response=='') response = 'Message Posted';
       jQuery('#nxs_gPopupContent').html('<p> ' + response + '</p>' +'<input type="button" class="bClose" value="OK" />');
     });      
       
  }
    
  function nxs_doAJXPopup(act, nt, nid, msg, addprms, butText){  var data = {  action:'nxs_snap_aj', nxsact: act, nt:nt, id: 0, nid: nid, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()};
     jQuery('#nxs_gPopupContent').html(msg+" <p><img src='<?php echo NXS_PLURL; ?>img/ajax-loader-med.gif' /></p>");
     jQuery('#nxs_gPopup').bPopup({ modalClose: false, appendTo: '#wpbody-content', opacity: 0.6, positionStyle: 'fixed'});  
     jQuery.post(ajaxurl, data, function(response) { if (response=='') response = 'Message Posted'; if (butText === undefined || butText=='undefined' || butText=='') butText = 'Close';
       jQuery('#nxs_gPopupContent').html('<p> ' + response + '</p>' +'<input type="button" class="bClose" value="'+butText+'" />');
     });      
       
  }
  
  function testPost_V3(nt, nid){ jQuery(".blnkg").hide(); <?php foreach ($nxs_snapAvNts as $avNt) {?>
    if (nt=='<?php echo $avNt['code']; ?>') { 
       var data = { action: 'rePostTo<?php echo $avNt['code']; ?>', id: 0, nid: nid, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()}; callAjSNAP(data, '<?php echo $avNt['name']; ?>'); 
    }<?php } ?>
  }
  
  function nxs_doTabs(){
    jQuery('#nxsAPIUpd').dblclick(function() { doLic(); });

    jQuery(".nsx_tab_content").hide(); 
    jQuery("ul.nsx_tabs > li:first-child").addClass("active").show(); 
    jQuery(".nsx_tab_container > .nsx_tab_content:first-child").show();


    jQuery("ul.nsx_tabs li").click(function() {
      jQuery(this).parent().children("li").removeClass("active");
      jQuery(this).addClass("active"); 
      jQuery(this).parent().parent().children(".nsx_tab_container").children(".nsx_tab_content").hide(); 
      var activeTab = jQuery(this).find("a").attr("href"); 
      jQuery(activeTab).show(); 
      return false;
    });
      
  }
  
  function nxs_doTabsInd(iid){    
    jQuery(iid+" .nsx_tab_content").hide(); 
    jQuery(iid+" ul.nsx_tabs > li:first-child").addClass("active").show(); 
    jQuery(iid+" .nsx_tab_container > .nsx_tab_content:first-child").show(); 

    
    jQuery(iid+" ul.nsx_tabs li").click(function() {
      jQuery(this).parent().children("li").removeClass("active"); 
      jQuery(this).addClass("active"); 
      jQuery(this).parent().parent().children(".nsx_tab_container").children(".nsx_tab_content").hide(); 
      var activeTab = jQuery(this).find("a").attr("href"); 
      jQuery(activeTab).show(); 
      return false;
    });      
  }
  
  function nxs_in_array(needle, haystack) { for(var i in haystack) { if(haystack[i] == needle) return true;} return false; }
  
 
  
  jQuery(document).ready(function() {    nxs_doTabs();

    var nxs_curTagsValue = []; jQuery('.the-tags').each(function() {if (jQuery(this).val()!='') nxs_curTagsValue[jQuery(this).attr('id')] = jQuery(this).val(); });
    jQuery(function () { setTimeout(nxs_checkTagsChangesX, 50); });
    
    function nxs_checkTagsChangesX() { var isChanged = false; var nxs_isLocked = jQuery('#nxsLockIt').val(); if (nxs_isLocked=='1') return;
      jQuery('.the-tags').each(function() {       
        currentValue = jQuery( this ).val(); currID = jQuery(this).attr('id');   
        if ((currentValue) && currentValue != nxs_curTagsValue[currID] && currentValue != '') isChanged = true;
      });          
      if (isChanged) { 
        jQuery('.the-tags').each(function() { if (jQuery(this).val()!='') nxs_curTagsValue[jQuery(this).attr('id')] = jQuery(this).val(); });
        var nxs_curTagsValueX = ''; var tValX = [];
        jQuery('.the-tags').each(function() { 
           var tVals = jQuery( this ).val().toLowerCase().split(","); var tID = jQuery( this ).attr('id').replace("tax-input-",""); 
           for(var ii in tVals) tValX.push(tID+"|"+jQuery.trim(tVals[ii])); 
        }); 
        jQuery(".nxs_TG").each(function(index) { var cats = jQuery(this).val();  var catsA = cats.split(','); uqID = jQuery(this).attr('id'); uqID = uqID.replace("nxs_TG_", "do", "gi");

        for(var ii in catsA) { var tgVal = jQuery.trim(catsA[ii]).toLowerCase();
          if (tgVal.indexOf("|")<1 && tgVal!="") tgVal = "post_tag|"+tgVal;
          if (tgVal!="" && jQuery.inArray(tgVal, tValX)>-1) {  jQuery('#'+uqID).attr('checked','checked'); }           
        }        
        });
      } setTimeout(nxs_checkTagsChangesX, 50);
    }
    
    function nxs_doManPost(obj){ var nnid = jQuery( this ).attr('name'); var nid = jQuery( this ).attr('alt'); var res = nnid.split("-"); var nt = res[0]; var pid = res[1]; var ntn = jQuery( this ).attr('data-ntname');
     var data = {  action:'nxs_snap_aj', nxsact: 'manPost', nt:nt, id: pid, nid: nid, et_load_builder_modules:1, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()};
     jQuery('#nxs_gPopupContent').html("<p>Sending update to "+ntn+" ....</p>" + "<p><img src='<?php echo NXS_PLURL; ?>img/ajax-loader-med.gif' /></p>");
     jQuery('#nxs_gPopup').bPopup({ modalClose: false, appendTo: '#nsStForm', opacity: 0.6, positionStyle: 'fixed'});  
     jQuery.post(ajaxurl, data, function(response) { if (response=='') response = 'Message Posted';
       jQuery('#nxs_gPopupContent').html('<p> ' + response + '</p>' +'<input type="button" class="bClose" value="Close" />');
      }); 
    }
    
    function nxs_doAllManPost(obj){ jQuery('#nxs_gPopup').bPopup({ modalClose: false, appendTo: '#nsStForm', opacity: 0.6, positionStyle: 'fixed'}); jQuery('#nxs_gPopupContent').html('<p></p>');  
      jQuery('.nxsGrpDoChb:checked').each(function() { var nnm = jQuery(this).attr('name').replace(']','');  var res = nnm.split("["); var nt = res[0]; var ii = res[1]; var ntn = nt; var pid = jQuery('input#post_ID').val();
        var data = {  action:'nxs_snap_aj', nxsact: 'manPost', nt:nt, id: pid, nid: ii, et_load_builder_modules:1, _wpnonce: jQuery('input#nxsSsPageWPN_wpnonce').val()};
        jQuery('#nxs_gPopupContent').append("<div id='nxsTempImg"+nt+ii+"'><p>Sending update to "+ntn+" ....</p>" + "<p><img src='<?php echo NXS_PLURL; ?>img/ajax-loader-med.gif' /></p></div>");        
        jQuery.post(ajaxurl, data, function(response) { if (response=='') response = 'Message Posted';
          jQuery('#nxsTempImg'+nt+ii).html('<p> ' + response + '</p>');
        });         
      }); jQuery('#nxs_gPopupContent').append('<input type="button" class="bClose" value="Close" />');
    }
    
    jQuery( ".manualPostBtn" ).on( "click", nxs_doManPost);
    jQuery( ".manualAllPostBtn" ).on( "click", nxs_doAllManPost);
    
  });
</script>

<style type="text/css">
.NXSButton { background-color:#89c403;
    background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #89c403), color-stop(1, #77a809) );
    background:-moz-linear-gradient( center top, #89c403 5%, #77a809 100% );
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#89c403', endColorstr='#77a809');    
    -moz-border-radius:4px; -webkit-border-radius:4px; border-radius:4px; border:1px solid #74b807; display:inline-block; color:#ffffff;
    font-family:Trebuchet MS; font-size:12px; font-weight:bold; padding:4px 5px;  text-decoration:none;  text-shadow:1px 1px 0px #528009;
}.NXSButton:hover {color:#ffffff; background-color:#77a809;
    background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #77a809), color-stop(1, #89c403) );
    background:-moz-linear-gradient( center top, #77a809 5%, #89c403 100% );
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#77a809', endColorstr='#89c403');    
}.NXSButton:active {color:#ffffff; position:relative; top:1px;}.NXSButton:focus {color:#ffffff; position:relative; top:1px;} .nsBigText{font-size: 14px; color: #585858; font-weight: bold; display: inline;}
.NXSButtonB { background-color:#038bc4;
    background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #038bc4), color-stop(1, #096aa8) );
    background:-moz-linear-gradient( center top, #038bc4 5%, #096aa8 100% );
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#038bc4', endColorstr='#096aa8');    
    -moz-border-radius:4px; -webkit-border-radius:4px; border-radius:4px; border:1px solid #077cb8; display:inline-block; color:#ffffff;
    font-family:Trebuchet MS; font-size:12px; font-weight:bold; padding:4px 5px;  text-decoration:none;  text-shadow:1px 1px 0px #095d80;
}.NXSButtonB:hover {color:#ffffff; background-color:#096aa8;
    background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #096aa8), color-stop(1, #038bc4) );
    background:-moz-linear-gradient( center top, #096aa8 5%, #038bc4 100% );
    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#096aa8', endColorstr='#038bc4');    
}.NXSButtonB:active {color:#ffffff; position:relative; top:1px;}.NXSButton:focus {color:#ffffff; position:relative; top:1px;} .nsBigText{font-size: 14px; color: #585858; font-weight: bold; display: inline;}
#nxs_ntType {width: 250px;}
#nsx_addNT {width: 600px;}
.nxsInfoMsg{  margin: 1px auto; padding: 3px 10px 3px 5px; border: 1px solid #ffea90;  background-color: #fdfae4; display: inline; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; }
.blnkg{text-decoration:blink; font-size: 17px; color: #0CB107; font-weight: bold; display: inline;}

div.popShAtt { display: none; position: absolute; width: 600px; padding: 10px; background: #eeeeee; color: #000000; border: 1px solid #1a1a1a; font-size: 90%; }
.underdash {border-bottom: 1px #21759B dashed; text-decoration:none;}
.underdash a:hover {border-bottom: 1px #21759B dashed}

.nxsTHRow {vertical-align:top; padding-top:6px; text-align:right; width:145px; padding-right:10px;}

ul.nsx_tabs {margin: 0;padding: 0; margin-top:5px;float: left;list-style: none;height: 32px;border-bottom: 1px solid #999;border-left: 1px solid #999;width: 99%;}
ul.nsx_tabs li {float: left;margin: 0;padding: 0;height: 31px;line-height: 31px;border: 1px solid #999;border-left: none;margin-bottom: -1px;overflow: hidden;position: relative;background: #e0e0e0;}
ul.nsx_tabs li a {text-decoration: none;color: #000; display: block; font-size: 1.2em; padding: 0 15px; border: 1px solid #fff; outline: none;}
ul.nsx_tabs li a:hover { background: #ccc;}
html ul.nsx_tabs li.active, html ul.nsx_tabs li.active a:hover  { background: #fff; border-bottom: 1px solid #fff; }
.nsx_tab_container {border: 1px solid #999; border-top: none; overflow: hidden; clear: both; float: left; width: 99%; background: #fff;}
.nsx_tab_content {padding: 10px;}

.nxs_tls_cpt{width:100%; padding-bottom: 5px; padding-top: 10px;font-size: 16px; font-weight: bold;}
.nxs_tls_bd{width:100%; padding-left: 10px; padding-bottom: 10px;}
.nxs_tls_sbInfo{font-style: italic; padding-bottom: 10px; padding-top: 2px;}
.nxs_tls_sbInfo2{font-style: italic; padding-left: 10px; padding-bottom: 5px; line-height: 10px; font-size: 11px;}
.nxs_tls_lbl{width:100%;padding-top:7px;padding-bottom:1px;}
.nxsInstrSpan{ font-size: 11px; }


.subDiv{margin-left: 15px;}
.nxs_hili {color:#008000;}
.clNewNTSets{width: 800px;}
.nxclear {clear: both;}

.nxs_icon16 { font-size: 14px; line-height: 18px;
    background-position: 3px 50% !important;
    background-repeat: no-repeat !important;
    display: inline-block;
    padding: 1px 0 1px 23px !important;
}

.nxs_box{border-color: #DFDFDF; border-radius: 3px 3px 3px 3px; box-shadow: 0 1px 0 #FFFFFF inset; border-style: solid; border-width: 1px; line-height: 1; margin-bottom: 10px; padding: 0; /* max-width: 1080px; */}
.nxs_box_header{border-bottom-color: #DFDFDF; box-shadow: 0 1px 0 #FFFFFF; text-shadow: 0 1px 0 #FFFFFF;font-size: 15px;font-weight: normal;line-height: 1;margin: 0;padding: 6px;
background:#f1f1f1;background-image:-webkit-gradient(linear,left bottom,left top,from(#ececec),to(#f9f9f9));background-image:-webkit-linear-gradient(bottom,#ececec,#f9f9f9);background-image:-moz-linear-gradient(bottom,#ececec,#f9f9f9);background-image:-o-linear-gradient(bottom,#ececec,#f9f9f9);background-image:linear-gradient(to top,#ececec,#f9f9f9)
-moz-user-select: none;border-bottom-style: solid;border-bottom-width: 1px;}
.nxs_box_inside{line-height: 1.4em; padding: 10px;}
.nxs_box_inside input[type=text]{ padding: 5px; height: 24px; border: 1px solid #ACACAC;}
.nxs_box_inside .insOneDiv, #nsx_addNT .insOneDiv{max-width: 1020px; background-color: #f8f9f9; background-repeat: no-repeat; margin: 10px; border: 1px solid #808080; padding: 10px; display:none; overflow: hidden;}
.nxs_box_inside .itemDiv {margin:5px;margin-left:10px;}
.nxs_box_header h3 {font-size: 14px; margin-bottom: 2px; margin-top: 2px;}
.nxs_newLabel {font-size: 11px; color:red; padding-left: 5px; padding-right: 5px;}

.nxs_prevImagesDiv {border:1px solid #0f3c6d;  width:110px; height:110px; margin:3px; padding:3px; text-align:center; float:left; position: relative;}
.nxs_prevImages {padding:1px; max-height:100px; max-width:100px;}
.nxs_chImg_selDiv {border:1px solid #800000;}
.nxs_chImg_selImg {border:4px solid #800000;}
.nxs_checkIcon{position: absolute;}

.nxs_checkIcon{display:none; height:24px;width:24px;position:absolute;top:-7px;right:-7px;outline:0;border:1px solid #fff;border-radius:3px;box-shadow:0 0 0 1px rgba(0,0,0,0.4);background:#800000;background-image:-webkit-gradient(linear,left top,left bottom,from(#800000),to(#570000));background-image:-webkit-linear-gradient(top,#800000,#570000);background-image:-moz-linear-gradient(top,#800000,#570000);background-image:-o-linear-gradient(top,#800000,#570000);background-image:linear-gradient(to bottom,#800000,#570000)}
.nxs_checkIcon{ top:-5px; right: -3px; width: 15px; height: 15px; box-shadow:0 0 0 1px #800000;background:#800000;background-image:-webkit-gradient(linear,left top,left bottom,from(#800000),to(#570000));background-image:-webkit-linear-gradient(top,#800000,#570000);background-image:-moz-linear-gradient(top,#800000,#570000);background-image:-o-linear-gradient(top,#800000,#570000);background-image:linear-gradient(to bottom,#800000,#570000)}
.nxs_checkIcon div{background-position:-21px 0; width: 15px; height: 15px;}
/* #nxsDivWrap .postbox .inside {overflow: hidden;}  */
#nxsDivWrap .postbox .description {vertical-align: middle; color: #ACACAC;}  

.doneMsg {color:#005800; font-weight: bold; display: inline-block; font-size: 16px; display: none; float: right; margin-right: 50px;}

.submitX {padding-top: 7px; padding-bottom: 5px;}

  #nxs_spPopup, #nxs_spNTPopup, #nxs_gPopup, #nxs_spFltPopup, #nxs_popupDiv, #showLicForm, .nxsbPP{ min-height: 250px; z-index:999991; background-color: #FFFFFF; border-radius: 5px 5px 5px 5px;  box-shadow: 0 0 3px 2px #999999; color: #111111; display: none;  min-width: 850px; padding: 25px;}
  #nxs_gPopup {min-width: 650px;}
  #nxs_gPopupContent {overflow-y: auto; overflow-x: auto;  max-width: 700px; max-height: 700px; min-height: 300px;}


.nxs_txtIcon { margin: 0px; padding-left: 20px; background-repeat: no-repeat;} .nxs_ti_gp {background-image: url('<?php echo $nxs_plurl; ?>img/gp16.png');} 
            .nxs_ti_li {background-image: url('<?php echo $nxs_plurl; ?>img/li16.png');}  .nxs_ti_rd {background-image: url('<?php echo $nxs_plurl; ?>img/rd16.png');} 
            .nxs_ti_fp {background-image: url('<?php echo $nxs_plurl; ?>img/fp16.png');}  .nxs_ti_yt {background-image: url('<?php echo $nxs_plurl; ?>img/yt16.png');} 
            .nxs_ti_bg {background-image: url('<?php echo $nxs_plurl; ?>img/bg16.png');}  .nxs_ti_pn {background-image: url('<?php echo $nxs_plurl; ?>img/pn16.png');} 
            .nxs_ti_ig {background-image: url('<?php echo $nxs_plurl; ?>img/ig16.png');} 
            
input[readonly]{ background-color:white; }         
.nxs-selz-dropwodn {max-width: 200px;}

.dd-container {display:inline-block;}
.dd-selected {color:black;}
.dd-selected-text {line-height: 20px; color:black;}
.dd-options {max-height: 500px;}
</style>
<?php }}

if (!function_exists('nxs_doShowHint')){ function nxs_doShowHint($t, $ex='', $wdth='79'){ ?>
<div id="<?php echo $t; ?>Hint" class="nxs_FRMTHint" style="font-size: 11px; margin: 2px; margin-top: 0px; padding:7px; border: 1px solid #C0C0C0; width: <?php echo $wdth; ?>%; background: #fff; display: none;"><span class="nxs_hili">%TITLE%</span> - <?php _e('Inserts the Title of the post', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%URL%</span> - <?php _e('Inserts the URL of the post', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%SURL%</span> - <?php _e('Inserts the <b>shortened URL</b> of your post', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%IMG%</span> - <?php _e('Inserts the featured image URL', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%EXCERPT%</span> - <?php _e('Inserts the excerpt of the post (processed)', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%RAWEXCERPT%</span> - <?php _e('Inserts the excerpt of the post (as typed)', 'social-networks-auto-poster-facebook-twitter-g'); ?>,  <span class="nxs_hili">%ANNOUNCE%</span> - <?php _e('Inserts the text till the &lt;!--more--&gt; tag or first N words of the post', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%FULLTEXT%</span> - <?php _e('Inserts the processed body(text) of the post', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%RAWTEXT%</span> - <?php _e('Inserts the body(text) of the post as typed', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%TAGS%</span> - <?php _e('Inserts post tags', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%CATS%</span> - <?php _e('Inserts post categories', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%HTAGS%</span> - <?php _e('Inserts post tags as hashtags', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%HCATS%</span> - <?php _e('Inserts post categories as hashtags', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%AUTHORNAME%</span> - <?php _e('Inserts the author\'s name', 'social-networks-auto-poster-facebook-twitter-g'); ?>, <span class="nxs_hili">%SITENAME%</span> - <?php _e('Inserts the the Blog/Site name', 'social-networks-auto-poster-facebook-twitter-g'); ?>. <?php echo $ex; ?></div>
<?php }}

if (!function_exists('nxs_doSMAS')){ function nxs_doSMAS($nType, $typeii) { ?><div id="do<?php echo $typeii; ?>Div" class="clNewNTSets" style="margin-left: 10px; display:none; "><div style="font-size: 15px; text-align: center;"><br/><br/>
<?php printf( __( 'You already have %s configured.  This plugin supports only one %s account. <br/><br/> Please consider getting <a target="_blank" href="http://www.nextscripts.com/social-networks-auto-poster-for-wp-multiple-accounts">Multiple Accounts Edition</a> if you would like to add another %s account for auto-posting.', 'social-networks-auto-poster-facebook-twitter-g' ), $nType, $nType, $nType );  ?>
</div></div><?php 
}}

if (!function_exists('nxs_snapCleanup')){ function nxs_snapCleanup($options){   global $nxs_snapAvNts; 
    foreach ($nxs_snapAvNts as $avNt) { if (!isset($options[$avNt['lcode']]) || count($options[$avNt['lcode']])>1) { $copt = ''; $t = '';
      if (isset($options[$avNt['lcode']]) && is_array($options[$avNt['lcode']])) $copt = array_values( $options[$avNt['lcode']] );  
      $t = (isset($copt[0]) && is_array($copt[0]) && count($copt[0]>2))?$copt[0]:''; $options[$avNt['lcode']] = array(); if ($t!='') $options[$avNt['lcode']][] = $t;
    }}
    return $options;
}}

function nxs_html_to_utf8($str){ $str = html_entity_decode($str, ENT_QUOTES, "utf-8"); return $str;}

//if (!function_exists('nxs_html_to_utf8')){ function nxs_html_to_utf8 ($data){return preg_replace("/\\&\\#([0-9]{3,10})\\;/e", 'nxs__html_to_utf8("\\1")', $data); }}
if (!function_exists('nxs_html_to_utf8')){ function nxs_html_to_utf8X ($data){ var_dump($data); return preg_replace_callback("/\\&\\#([0-9]{3,10})\\;/", create_function ('$matches', ' var_dump($matches); return nxs__html_to_utf8($matches[2]);'), $data); }}
if (!function_exists('nxs__html_to_utf8')){ function nxs__html_to_utf8 ($data){ echo "<br/>|X|"; var_dump($data); if ($data > 127){ $i = 5; while (($i--) > 0){
  if ($data != ($a = $data % ($p = pow(64, $i)))){ 
    $ret = chr(base_convert(str_pad(str_repeat(1, $i + 1), 8, "0"), 2, 10) + (($data - $a) / $p)); for ($i; $i > 0; $i--) $ret .= chr(128 + ((($data % pow(64, $i)) - ($data % ($p = pow(64, $i - 1)))) / $p)); break; }
  }} else $ret = "&#$data;";
  return $ret;
}}
if (!function_exists("nxs_chArrVar")) { function nxs_chArrVar($arr, $varN, $varV){ return (isset($arr) && is_array($arr) && isset($arr[$varN]) && $arr[$varN]==$varV); }}
    
    
if (!function_exists("nxs_metaMarkAsPosted")) { function nxs_metaMarkAsPosted($postID, $nt, $did, $args=''){ $mpo =  get_post_meta($postID, 'snap'.$nt, true);  $mpo =  maybe_unserialize($mpo);
  //prr($postID); prr('snap'.$nt);  prr($mpo); echo "#####".$postID."|".$nt."|".$did."|".$args;
  if (!is_array($mpo)) $mpo = array(); if (!isset($mpo[$did]) || !is_array($mpo[$did])) $mpo[$did] = array();
  if ($args=='' || ( is_array($args) && isset($args['isPosted']) && $args['isPosted']=='1')) $mpo[$did]['isPosted'] = '1';  
  if (is_array($args) && isset($args['isPrePosted']) && $args['isPrePosted']==1) $mpo[$did]['isPrePosted'] = '1';  
  if (is_array($args) && isset($args['pgID'])) $mpo[$did]['pgID'] = $args['pgID'];  
  if (is_array($args) && isset($args['postURL'])) $mpo[$did]['postURL'] = $args['postURL'];  
  if (is_array($args) && isset($args['pDate'])) $mpo[$did]['pDate'] = $args['pDate'];  
  /*$mpo = mysql_real_escape_string(serialize($mpo)); */ delete_post_meta($postID, 'snap'.$nt); add_post_meta($postID, 'snap'.$nt, str_replace('\\','\\\\', serialize($mpo)));
}}
if (!function_exists('nxs_checkAddLogTable')){ function nxs_checkAddLogTable(){ global $nxs_tpWMPU, $wpdb; if($nxs_tpWMPU=='S') switch_to_blog(1);  
  $installed_ver = get_option( "nxs_log_db_table_version" ); if ($installed_ver=='1.5') return true;
  $table_name = $wpdb->prefix . "nxs_log";
  $sql = "CREATE TABLE $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    date datetime DEFAULT '1970-01-01 00:00:01' NOT NULL,
    uid bigint(20) DEFAULT 0 NOT NULL,
    act VARCHAR(255) DEFAULT '' NOT NULL,
    nt VARCHAR(255) DEFAULT '' NOT NULL,
    type VARCHAR(255) DEFAULT '' NOT NULL,
    flt VARCHAR(20) DEFAULT '' NOT NULL,
    nttype VARCHAR(20) DEFAULT '' NULL,
    msg text NOT NULL,    
    extInfo text NULL,    
    UNIQUE KEY id (id)
  ) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); dbDelta($sql);
  delete_option("nxs_log_db_table_version"); add_option("nxs_log_db_table_version", '1.5');
  if($nxs_tpWMPU=='S' && function_exists("restore_current_blog")) restore_current_blog();
}}
if (!function_exists('nxs_getnxsLog')){ function nxs_getnxsLog($prm='',$pg=0){ global $wpdb; $pg = $pg*150; $wh = '';  $wh2 = ''; $whOut = '';
  if (!empty($prm)) { 
    if (!empty($prm[1]) && $prm[1]==1) $wh[] = 'flt = "snap"'; elseif (!empty($prm[0]) && $prm[0]==1) $wh[] = '(flt = "snap" AND (type = "E" OR type="W"))'; 
    if (!empty($prm[3]) && $prm[3]==1) $wh[] = 'flt = "cron"'; elseif (!empty($prm[2]) && $prm[2]==1) $wh[] = '(flt = "cron" AND (type = "E" OR type="W"))';     
    if (!empty($prm[4]) && $prm[4]==1) $wh[] = 'flt = "sys"';       
    if (!empty($wh)) $wh = ' ('.implode(' OR ', $wh).') '; 
    if (!empty($wh2)) $wh2 = ' ('.implode(' OR ', $wh2).') ';    
    $isItUserWhoCan = (!current_user_can('manage_options' ) && current_user_can('haveown_snap_accss'));  if ($isItUserWhoCan) { $u = wp_get_current_user(); $uid = $u->ID; $whu = ' uid='.$uid; } else $whu = '';       
    $whOut = ((!empty($wh) || !empty($wh2) || !empty($whu))?' WHERE ':'').$wh.((!empty($wh) && !empty($wh2))?' AND ':'').$wh2.(((!empty($wh)||!empty($wh2)) && !empty($whu))?' AND ':'').$whu;
    echo "| ".$whOut." |<br/>";    
  }
  $log = $wpdb->get_results( "SELECT * FROM ". $wpdb->prefix . "nxs_log ".$whOut." ORDER BY id DESC LIMIT ".$pg.",150", ARRAY_A );  if (!is_array($log)) return array(); else return $log;
}}

if (!function_exists('nxs_LogIt')){ function nxs_LogIt($type, $action, $ntName, $ntType='', $title='', $extInfo='', $flt='snap', $uid=0){  global $nxs_tpWMPU, $wpdb; if($nxs_tpWMPU=='S') switch_to_blog(1); 
  global $plgn_NS_SNAutoPoster; if (isset($plgn_NS_SNAutoPoster)) $options = $plgn_NS_SNAutoPoster->nxs_options;  if (!empty($options) && !empty($options['numLogRows'])) $numLogRows = $options['numLogRows']; else $numLogRows = 250;
  //## Skip if Minimal Only Setting
  if (isset($options['extDebug']) && $options['extDebug']=='2' && stripos($action, 'Skipped')!==false ) return;   
  $logItem = array('date'=>date_i18n('Y-m-d H:i:s'), 'act'=>$action, 'type'=>$type, 'nt'=>$ntName, 'flt'=>$flt, 'nttype'=>$ntType, 'uid'=>$uid, 'msg'=> strip_tags($title), 'extInfo'=>$extInfo);   
  $nxDB = $wpdb->insert( $wpdb->prefix . "nxs_log", $logItem );  $lid = $wpdb->insert_id; $lid = $lid-$numLogRows;
  if ($lid>0) $wpdb->query( 'DELETE FROM '.$wpdb->prefix . 'nxs_log WHERE id<'.$lid );      
  if ($type=='E' && (isset($options['errNotifEmailCB']) && (int)$options['errNotifEmailCB'] == 1 && isset($options['errNotifEmail']) && trim($options['errNotifEmail']) != '')) { 
    $log = maybe_unserialize(get_option('NSX_LogToEmail')); if (!is_array($log)) $log = array(); $log[] = $logItem; delete_option("NSX_LogToEmail"); add_option("NSX_LogToEmail", $log, '', 'no');
  }
  if($nxs_tpWMPU=='S' && function_exists("restore_current_blog")) restore_current_blog();     
}}

if (!function_exists('nxs_addToLog')){ function nxs_addToLog ($type, $action, $nt, $msg=''){ nxs_LogIt($type, $action, $nt, '', $msg); }}
if (!function_exists('nxs_addToLogN')){ function nxs_addToLogN ($type, $action, $nt, $msg, $extInfo=''){ nxs_LogIt($type, $action, $nt, '', $msg, $extInfo); }}

if (!function_exists('nxs_addPostingDelaySel')){function nxs_addPostingDelaySel($nt, $ii, $hrs=0, $min=0, $days=0){ 
  global $plgn_NS_SNAutoPoster, $nxs_plurl;  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options; if ($options['nxsHTDP']=='I') return 'Not Compatible with "Publish Immediately"';
  if (function_exists('nxs_doSMAS4')) return nxs_doSMAS4($nt, $ii, $hrs, $min, $days); else return '<br/>';
}}
if (!function_exists('nxs_addPostingDelaySelV3')){function nxs_addPostingDelaySelV3($nt, $ii, $hrs=0, $min=0, $days=0){ 
  if (function_exists('nxs_doSMAS4')) { ?> <div class="nxs_tls_cpt"><?php _e('Posting Delay', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>  
    <div class="nxs_tls_bd"><?php  global $plgn_NS_SNAutoPoster, $nxs_plurl;  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options; 
      if ($options['nxsHTDP']=='I') _e('Not Compatible with "Publish Immediately"'); else  echo nxs_doSMAS4($nt, $ii, $hrs, $min, $days); ?></div>      
  <?php } else echo '<br/>';
}}
 

if (!function_exists("nxs_doQTrans")) { function nxs_doQTrans($txt, $lng=''){ if (!function_exists("qtrans_split") && !function_exists("qtranxf_split")) return $txt; 
  $txt = str_ireplace('<3','&lt;3', $txt); $txt = str_ireplace('<(','&lt;(', $txt); //$txt = preg_replace('/\[caption\s[^\]]*\]/', '', $txt);
  $txt = preg_replace('/\[caption[\s]{0,}(.*?)\][\s]{0,}(<a[\s]{0,}.*?<\/a>)[\s]{0,}(.*?)\[\/caption\]/ims', '<p $1> $2 <snap class="wpimgcaption">$3</snap> </p>', $txt); // WP Image with Caption fix
  if (function_exists("qtrans_split") && strpos($txt, '<!--:')!==false ) { $tta = qtrans_split($txt); if ($lng!='') return $tta[$lng]; else return reset($tta); }
  if (function_exists("qtranxf_split") && (strpos($txt, '<!--:')!==false || strpos($txt, '[:')!==false) ){ $tta = qtranxf_split($txt); if ($lng!='') return $tta[$lng]; else return reset($tta); }    
}}

if (!function_exists('nxs_addQTranslSel')){function nxs_addQTranslSel($nt, $ii, $selLng){  
  if (function_exists('nxs_doSMAS6')) return nxs_doSMAS6($nt, $ii, $selLng); else return '';  
}}

if (!function_exists("nxs_hideTip_ajax")) { function nxs_hideTip_ajax() {  check_ajax_referer('nxsSsPageWPN');   
   global $plgn_NS_SNAutoPoster, $nxs_plurl;  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options; 
   $options['hideTopTip'] = '1';    update_option($plgn_NS_SNAutoPoster->dbOptionsName, $options); $plgn_NS_SNAutoPoster->nxs_options = $options;
}}

if (!function_exists("nxs_mkShortURL")) { function nxs_mkShortURL($url, $postID=''){ $rurl = '';  global $plgn_NS_SNAutoPoster;  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options; 
    if ($options['nxsURLShrtnr']=='B' && trim($options['bitlyUname']!='') && trim($options['bitlyAPIKey']!='')) {      
      $response  = nxs_remote_get('http://api-ssl.bitly.com/v3/shorten?login='.$options['bitlyUname'].'&apiKey='.$options['bitlyAPIKey'].'&longUrl='.urlencode($url), nxs_mkRemOptsArr('')); 
      if (is_nxs_error($response)) { nxs_addToLogN('E', 'bit.ly', '', '-=ERROR=- '.print_r($response, true));  return $url; }
      $rtr = json_decode($response['body'],true);
      if ($rtr['status_code']=='200') $rurl = $rtr['data']['url']; else nxs_LogIt('E', '', 'bit.ly','','Error - bit.ly', print_r($rtr, true));
    } //echo "###".$rurl;
    if ($options['nxsURLShrtnr']=='A' && trim($options['adflyUname']!='') && trim($options['adflyAPIKey']!='')) {      
      $response  = nxs_remote_get('http://api.adf.ly/api.php?key='.$options['adflyAPIKey'].'&uid='.$options['adflyUname'].'&advert_type=int&domain='.$options['adflyDomain'].'&url='.urlencode($url), nxs_mkRemOptsArr(''));       
      if (is_nxs_error($response)) {   nxs_addToLogN('E', 'adf.ly', '', '-=ERROR=- '.print_r($response, true));  return $url; }     
      if ( $response['body']!='error')  $rurl = $response['body']; else {  nxs_addToLogN('E', 'adf.ly', '', '-=ERROR=- '.print_r($response, true)); return $url; }
    }
    if ($options['nxsURLShrtnr']=='C' && trim($options['clkimAPIKey']!='')) {    
      $response  = nxs_remote_get('http://api.clkim.com/?key='.$options['clkimAPIKey'].'&url='.urlencode($url), nxs_mkRemOptsArr(''));
      if (is_nxs_error($response)) { nxs_addToLogN('E', 'clk.im', '', '-=ERROR (SYS)=- '.print_r($response, true)); return $url; }  $r = json_decode($response['body'], true); //prr($r); die();
      if (!is_array($r) || $r['error']!='0') { nxs_addToLogN('E', 'clk.im', '', '-=ERROR (JSON)=- '.print_r($response['body'], true)); return $url; } else $rurl = urldecode($r['short']);      
    }
    if ($options['nxsURLShrtnr']=='X' && trim($options['xcoAPIKey']!='')) {    
      $response  = nxs_remote_get('http://api.x.co/Squeeze.svc/text/'.$options['xcoAPIKey'].'?url='.urlencode($url), nxs_mkRemOptsArr('')); 
      if (is_nxs_error($response)) { nxs_addToLogN('E', 'x.co', '', '-=ERROR (SYS)=- '.print_r($response, true)); return $url; }  $r = $response['body'];
      if (empty($r) || stripos($r, 'http://')===false) { nxs_addToLogN('E', 'x.co', '', '-=ERROR (RES)=- '.print_r($r, true)); return $url; } else $rurl = $r;
    }
    
    if ($options['nxsURLShrtnr']=='U') {    
      $flds = array('a'=>'add', 'url'=>$url); $response  = nxs_remote_post('http://u.to/', array('body' => $flds)); 
      if (is_nxs_error($response)) { nxs_addToLogN('E', 'u.to', '', '-=ERROR (SYS)=- '.print_r($response, true)); return $url; }  $r = $response['body'];
      if (empty($r) || stripos($r, "#shurlout').val('")===false) { nxs_addToLogN('E', 'x.co', '', '-=ERROR (RES)=- '.print_r($r, true)); return $url; } else $rurl = CutFromTo($r,"#shurlout').val('","'");
    }
    
    if ($options['nxsURLShrtnr']=='R') { $urlR = 'https://api.rebrandly.com/v1/links/new?destination='.urlencode($url).'&domain[fullName]='.$options['rblyDomain']; 
      $hdrsArr = array('Content-Type'=>'application/json', 'apikey'=>$options['rblyAPIKey']); $advSet = nxs_mkRemOptsArr($hdrsArr); $response  = nxs_remote_get($urlR, $advSet);
      if (is_nxs_error($response)) { nxs_addToLogN('E', 'Rebrandly', '', '-=ERROR (SYS)=- '.print_r($response, true)); return $url; }  $rtr = json_decode($response['body'],true); // prr($rtr);
      if (!is_array($rtr) || empty($rtr['shortUrl']) ) {   nxs_addToLogN('E', 'goo.gl', '', '-=ERROR=- '.print_r($response, true));  return $url; } $rurl = 'http://'.$rtr['shortUrl'];
    }
    
    if ($options['nxsURLShrtnr']=='P' && trim($options['postAPIKey']!='')) {      
      $response  = nxs_remote_get('http://po.st/api/shorten?longUrl='.urlencode($url).'&apiKey='.$options['postAPIKey'], nxs_mkRemOptsArr(''));       
      if (is_nxs_error($response)) { nxs_addToLogN('E', 'po.st', '', '-=ERROR (SYS)=- '.print_r($response, true)); return $url; }  $r = json_decode($response['body'], true); 
      if (!is_array($r) || $r['status_txt']!='OK') { nxs_addToLogN('E', 'po.st', '', '-=ERROR (JSON)=- '.print_r($response['body'], true)); return $url; } else $rurl = $r['short_url'];
    }
    if ($options['nxsURLShrtnr']=='W' && function_exists('wp_get_shortlink')) { global $post; $post = get_post($postID);  $rurl = wp_get_shortlink($postID, 'post'); }
    if ($options['nxsURLShrtnr']=='Y' && trim($options['YOURLSKey']!='') && trim($options['YOURLSURL']!='')) { $timestamp = time(); $signature = md5( $timestamp . $options['YOURLSKey'] ); 
      $flds = array('signature'=>$signature, 'action' => 'shorturl', 'url'=>$url, 'format'=>'json', 'timestamp'=>$timestamp);  
      $response  = nxs_remote_post(($options['YOURLSURL']), array('body' => $flds)); 
      if (is_nxs_error($response)) {  nxs_addToLogN('E', 'YOURLS', '', '-=ERROR=- '.print_r($response, true)); return $url; } 
      $rtr = json_decode($response['body'],true);  if (!is_array($rtr) || !isset($rtr['shorturl']) ) {   nxs_addToLogN('E', 'goo.gl', '', '-=ERROR=- '.print_r($response, true));  return $url; }      
      $rurl = $rtr['shorturl'];
    
    }   
    if ($options['nxsURLShrtnr']=='O' || $options['nxsURLShrtnr']=='' || $options['nxsURLShrtnr']=='G') { $hdrsArr = array('Content-Type'=>'application/json', 'Referer'=>'http://'.$_SERVER['HTTP_HOST']); 
      $advSet = nxs_mkRemOptsArr($hdrsArr,'','{"longUrl": "'.$url.'"}'); $response  = nxs_remote_post('https://www.googleapis.com/urlshortener/v1/url'.($options['gglAPIKey']!=''?'?key='.$options['gglAPIKey']:''), $advSet); 
      if (is_nxs_error($response)) {   nxs_addToLogN('E', 'goo.gl', '', '-=ERROR=- '.print_r($response, true));  return $url; } 
      $rtr = json_decode($response['body'],true); if (!is_array($rtr) || isset($rtr['error']) || !isset($rtr['id']) ) {   nxs_addToLogN('E', 'goo.gl', '', '-=ERROR=- '.print_r($response, true));  return $url; }      
      $rurl = $rtr['id'];
    }    
    //if ($rurl=='') { $response  = nxs_remote_get('http://gd.is/gtq/'.$url); if ((is_array($response) && ($response['response']['code']=='200'))) $rurl = $response['body']; }
    if ($rurl!='') $url = $rurl;  return $url;
}}
//## Comments - DISQUS native function has global $post; overwriting $post parameter in the middle of it.
function nxs_dsq_export_wp($nxPost, $comments=null) { global $wpdb, $wp_query, $post; $post = $nxPost;  ob_start(); echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . '"?' . ">\n";?>
  <?php the_generator('export');?><rss version="2.0" xmlns:excerpt="http://wordpress.org/export/<?php echo WXR_VERSION; ?>/excerpt/" xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:dsq="http://www.disqus.com/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:wp="http://wordpress.org/export/<?php echo WXR_VERSION; ?>/">
  <channel>
    <title><?php bloginfo_rss('name'); ?></title><link><?php bloginfo_rss('url') ?></link><pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></pubDate>
    <generator>WordPress <?php bloginfo_rss('version'); ?>; Disqus <?php echo DISQUS_VERSION; ?></generator>
  <?php $wp_query->in_the_loop = true; setup_postdata($post); ?>
  <item><title><?php echo apply_filters('the_title_rss', $post->post_title); ?></title><link><?php the_permalink_rss() ?></link>
  <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
  <dc:creator><?php echo dsq_export_wxr_cdata(get_the_author()); ?></dc:creator>
  <guid isPermaLink="false"><?php the_guid(); ?></guid>
  <content:encoded><?php echo dsq_export_wxr_cdata( apply_filters('the_content_export', $post->post_content) ); ?></content:encoded>
  <dsq:thread_identifier><?php echo dsq_identifier_for_post($post); ?></dsq:thread_identifier>
  <wp:post_id><?php echo $post->ID; ?></wp:post_id>
  <wp:post_date_gmt><?php echo $post->post_date_gmt; ?></wp:post_date_gmt>
  <wp:comment_status><?php echo $post->comment_status; ?></wp:comment_status>
  <?php
  if ( $comments ) { foreach ( $comments as $c ) { ?>
    <wp:comment>
    <wp:comment_id><?php echo $c->comment_ID; ?></wp:comment_id>
    <wp:comment_author><?php echo dsq_export_wxr_cdata($c->comment_author); ?></wp:comment_author>
    <wp:comment_author_email><?php echo $c->comment_author_email; ?></wp:comment_author_email>
    <wp:comment_author_url><?php echo $c->comment_author_url; ?></wp:comment_author_url>
    <wp:comment_author_IP><?php echo $c->comment_author_IP; ?></wp:comment_author_IP>
    <wp:comment_date><?php echo $c->comment_date; ?></wp:comment_date>
    <wp:comment_date_gmt><?php echo $c->comment_date_gmt; ?></wp:comment_date_gmt>
    <wp:comment_content><?php echo dsq_export_wxr_cdata($c->comment_content) ?></wp:comment_content>
    <wp:comment_approved><?php echo $c->comment_approved; ?></wp:comment_approved>
    <wp:comment_type><?php echo $c->comment_type; ?></wp:comment_type>
    <wp:comment_parent><?php echo $c->comment_parent; ?></wp:comment_parent>
    </wp:comment>
  <?php } } // comments ?>
    </item>
  </channel>
  </rss>
  <?php $output = ob_get_clean(); return $output;
}
//## 
if (!function_exists("nxs_postNewComment")) { function nxs_postNewComment($cmnt, $aa = false) { $cmnt['comment_post_ID'] = (int) $cmnt['comment_post_ID'];
  $cmnt['comment_parent'] = isset($cmnt['comment_parent']) ? absint($cmnt['comment_parent']) : 0; $ae =  get_option('admin_email');
  //$u = get_user_by( 'email', get_option('admin_email') );   $cmnt['user_id'] = $u->ID; //???
  $u = get_user_by( 'email', $cmnt['comment_author_email'] ); if (!empty($u)) $cmnt['user_id'] = $u->ID; else $cmnt['user_id'] = 0;

  $parent_status = ( 0 < $cmnt['comment_parent'] ) ? wp_get_comment_status($cmnt['comment_parent']) : ''; 
  $cmnt['comment_parent'] = ( 'approved' == $parent_status || 'unapproved' == $parent_status ) ? $cmnt['comment_parent'] : 0;
  $cmnt['comment_author_IP'] = ''; if (empty($cmnt['comment_agent'])) $cmnt['comment_agent'] = 'SNAP'; $cmnt['comment_date'] =  get_date_from_gmt( $cmnt['comment_date_gmt'] );    
  $cmnt = wp_filter_comment($cmnt); if ($aa) $cmnt['comment_approved'] = 1; else $cmnt['comment_approved'] = nxs_wp_allow_comment($cmnt); // echo "INSERT";  prr($cmnt);
  if ( $cmnt['comment_approved'] != 'spam' && $cmnt['comment_approved']>1 ) return $cmnt['comment_approved'];  else  { $cmntID = wp_insert_comment($cmnt); do_action( 'comment_post', $cmntID, $cmnt['comment_approved'] ); }
  if (empty($cmntID)) {  nxs_addToLogN('E', 'Error', 'Comments', '-=ERROR=-', print_r($cmnt, true)); return; }
  
  if ( 'spam' !== $cmnt['comment_approved'] ) { if ( '0' == $cmnt['comment_approved'] ) wp_notify_moderator($cmntID); $post = get_post($cmnt['comment_post_ID']);
    if ( get_option('comments_notify') && $cmnt['comment_approved'] && ( ! isset( $cmnt['user_id'] ) || $post->post_author != $cmnt['user_id'] ) ) wp_notify_postauthor($cmntID);  
    global $wpdb, $dsq_api;
    if (isset($dsq_api) && is_object($post)) { $plugins_url = str_replace( 'social-networks-auto-poster-facebook-twitter-g/', '', plugin_dir_path( __FILE__ )); 
    if (file_exists($plugins_url.'disqus-conditional-load/disqus-core/export.php')) require_once( $plugins_url.'disqus-conditional-load/disqus-core/export.php'); 
      elseif (file_exists($plugins_url.'disqus-comment-system/export.php')) require_once( $plugins_url.'disqus-comment-system/export.php');
    if (function_exists('dsq_export_wp')) {
      $comments = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_ID = %d", $cmntID) ); 
      $wxr = nxs_dsq_export_wp($post, $comments); $response = $dsq_api->import_wordpress_comments($wxr, time()); 
    }}
  } 
  return $cmntID;
}}

//#### Native WP Function that has wp_die in the middle of it ?????
function nxs_wp_allow_comment($commentdata) { global $wpdb; extract($commentdata, EXTR_SKIP); 
    // Simple duplicate check // expected_slashed ($comment_post_ID, $comment_author, $comment_author_email, $comment_content)
    $dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND comment_parent = '$comment_parent' AND comment_approved != 'trash' AND ( comment_author = '$comment_author' ";
    if ( $comment_author_email ) $dupe .= "OR comment_author_email = '$comment_author_email' "; $dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
    $dupeID = $wpdb->get_var($dupe); if ( $dupeID ) { do_action( 'comment_duplicate_trigger', $commentdata ); return $dupeID; } 
    do_action( 'check_comment_flood', $comment_author_IP, $comment_author_email, $comment_date_gmt );
    if ( ! empty( $user_id ) ) { $user = get_userdata( $user_id ); $post_author = $wpdb->get_var($wpdb->prepare("SELECT post_author FROM $wpdb->posts WHERE ID = %d LIMIT 1", $comment_post_ID)); }
    if ( isset( $user ) && ( $user_id == $post_author || $user->has_cap( 'moderate_comments' ) ) ) { // The author and the admins get respect.
        $approved = 1;
     } else { // Everyone else's comments will be checked.
        if ( check_comment($comment_author, $comment_author_email, $comment_author_url, $comment_content, $comment_author_IP, $comment_agent, $comment_type) ) $approved = 1; else $approved = 0;
        if ( wp_blacklist_check($comment_author, $comment_author_email, $comment_author_url, $comment_content, $comment_author_IP, $comment_agent) ) $approved = 'spam';
    } $approved = apply_filters( 'pre_comment_approved', $approved, $commentdata ); return $approved;
}

if (!function_exists("ns_get_avatar")) { function ns_get_avatar($avatar, $id_or_email, $size=96, $default='', $alt='') { 
    if ( is_object($id_or_email) ) { 
      if ($id_or_email->comment_agent=='SNAP' && stripos($id_or_email->comment_author_url, 'facebook.com')!==false) { $fbuID = str_ireplace('@facebook.com','',$id_or_email->comment_author_email);        
        $avatar = "<img alt='{$id_or_email->comment_author}' src='https://graph.facebook.com/v2.3/$fbuID/picture' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";              }
      if (stripos($id_or_email->comment_agent, 'SNAP||')!==false && stripos($id_or_email->comment_author_url, 'twitter.com')!==false) { $fbuID = str_ireplace('SNAP||','',$id_or_email->comment_agent);
        $avatar = "<img alt='{$id_or_email->comment_author}' src='{$fbuID}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";        
      }
      
    }
    return $avatar;
}}

if (!function_exists('nxs_doProcessTags')){ function nxs_doProcessTags($tags){ $tagsA = array(); if (!is_array($tags)) { $tags = explode(',', $tags); 
  foreach ($tags as $tg) $tagsA[] = trim($tg); } else $tagsA = $tags; $tagsA = array_unique($tagsA);  $tags = array(); 
  //foreach ($tagsA as $tg) { $tags['tagsA'][] = $tg; $tags['htagsA'][] = "#".trim(str_replace(' ', '', preg_replace('/[^a-zA-Z0-9\p{L}\p{N}\s]/u', '', trim(ucwords(str_ireplace('&', '', str_ireplace('&amp;','',$tg))))))); } 
  foreach ($tagsA as $tg) { $tags['tagsA'][] = $tg; $tags['htagsA'][] = "#".trim(str_replace(' ', '', nxs_clean_string(trim(ucwords(str_ireplace('&', '', str_ireplace('&amp;','',$tg))))))); }   
  $tags['tags'] =  implode(', ', $tags['tagsA']); $tags['htags'] = implode(', ', $tags['htagsA']);
  return $tags;
}}            
if (!function_exists('nxs_doFormatMsg')){ function nxs_doFormatMsg($format, $message, $addURLParams=''){ global $nxs_urlLen; $msg = nxs_doSpin($format); // prr($msg); prr($message);// Make "message default"
  $msgDef = array('title'=>'','announce'=>'','text'=>'','url'=>'','surl'=>'','urlDescr'=>'','urlTitle'=>'','imageURL' => array(),'videoCode'=>'','videoURL'=>'','siteName'=>'','tags'=>'','cats'=>'','authorName'=>'','orID'=>''); $message = array_merge($msgDef, $message);
  if (preg_match('/%URL%/', $msg)) { $url = $message['url']; if($addURLParams!='') $url .= (strpos($url,'?')!==false?'&':'?').$addURLParams;  $nxs_urlLen = nxs_strLen($url); $msg = str_ireplace("%URL%", $url, $msg);}
  if (preg_match('/%SURL%/', $msg)) { 
    if (isset($message['surl']) && $message['surl']!='') $url = $message['surl']; else { $url = $message['url']; if($addURLParams!='') $url .= (strpos($url,'?')!==false?'&':'?').$addURLParams; $url = nxs_mkShortURL($url); } 
    $nxs_urlLen = nxs_strLen($url); $msg = str_ireplace("%SURL%", $url, $msg);
  }
  if (preg_match('/%IMG%/', $msg)) { if (isset($message['imgURL']) && is_array($message['imgURL'])) { $imgURL = trim($message['imgURL']['large']); if ($imgURL=='') $imgURL = trim($message['imgURL']['medium']);   
      if ($imgURL=='') $imgURL = trim($message['imgURL']['original']); if ($imgURL=='') $imgURL = trim($message['imgURL']['thumb']);
    } elseif (!empty($message['imgURL'])) $imgURL = $message['imgURL']; else $imgURL = '';    $msg = str_ireplace("%IMG%", $imgURL, $msg); 
  }
  if (preg_match('/%IMGLARGE%/', $msg)) $msg = str_ireplace("%IMG%", trim($message['imgURL']['large'], $msg));  
  if (preg_match('/%IMGMEDIUM%/', $msg)) $msg = str_ireplace("%IMGMEDIUM%", trim($message['imgURL']['medium'], $msg));  
  if (preg_match('/%IMGTHUMB%/', $msg)) $msg = str_ireplace("%IMGTHUMB%", trim($message['imgURL']['thumb'], $msg));  
  if (preg_match('/%IMGORIGINAL%/', $msg)) $msg = str_ireplace("%IMGORIGINAL%", trim($message['imgURL']['original'], $msg));  
  
  if (preg_match('/%ORID%/', $msg)) $msg = str_ireplace("%ORID%", $message['orID'], $msg);  
  if (preg_match('/%TITLE%/', $msg)) $msg = str_ireplace("%TITLE%", $message['title'], $msg);  
  if (preg_match('/%STITLE%/', $msg)) { $title = substr($message['title'], 0, 115); $msg = str_ireplace("%STITLE%", $title, $msg); }                    
  if (preg_match('/%AUTHORNAME%/', $msg)) $msg = str_ireplace("%AUTHORNAME%", $message['authorName'], $msg);
  if (preg_match('/%SITENAME%/', $msg)) $msg = str_ireplace("%SITENAME%", $message['siteName'], $msg); 
  
  if (preg_match('/%ANNOUNCE%/', $msg)) { $sText =  trim($message['announce'])!=''?$message['announce']:nsTrnc($message['description'], 300, " ", "...");  $msg = str_ireplace("%ANNOUNCE%", $sText, $msg); }
  if (preg_match('/%EXCERPT%/', $msg)) { $sText =  trim($message['announce'])!=''?$message['announce']:nsTrnc($message['description'], 300, " ", "...");  $msg = str_ireplace("%EXCERPT%", $sText, $msg); }
  if (preg_match('/%RAWEXCERPT%/', $msg)) { $sText =  trim($message['announce'])!=''?$message['announce']:nsTrnc($message['description'], 300, " ", "...");  $msg = str_ireplace("%RAWEXCERPT%", $sText, $msg); }
  
  if (preg_match('/%TEXT%/', $msg)) $msg = str_ireplace("%TEXT%", $message['description'], $msg);     
  if (preg_match('/%FULLTEXT%/', $msg)) $msg = str_ireplace("%FULLTEXT%", $message['description'], $msg);     
  if (preg_match('/%RAWTEXT%/', $msg)) $msg = str_ireplace("%RAWTEXT%", $message['description'], $msg);     
      
  
  if (preg_match('/%TAGS%/', $msg)) { $tags = nxs_doProcessTags($message['tags']);  $msg = str_ireplace("%TAGS%", $tags['tags'], $msg); }
  if (preg_match('/%HTAGS%/', $msg)) { $tags = nxs_doProcessTags($message['tags']);  $msg = str_ireplace("%HTAGS%", $tags['htags'], $msg); }
  if (preg_match('/%CATS%/', $msg)) { $tags = nxs_doProcessTags($message['cats']);  $msg = str_ireplace("%CATS%", $tags['cats'], $msg); }
  if (preg_match('/%HCATS%/', $msg)) { $tags = nxs_doProcessTags($message['hcats']);  $msg = str_ireplace("%HCATS%", $tags['hcats'], $msg); }
    
  if (preg_match('/%+CF-[a-zA-Z0-9-_]+%/', $msg)) { $msgA = explode('%CF', $msg); $mout = '';
    foreach ($msgA as $mms) { 
        if (substr($mms, 0, 1)=='-' && stripos($mms, '%')!==false) { $mGr = CutFromTo($mms, '-', '%'); $cfItem = $message[$mGr]; $mms = str_ireplace("-".$mGr."%", $cfItem, $mms); } $mout .= $mms; 
    } $msg = $mout; 
  }  
  
  
  
  return trim($msg);
}}
//## Common Dialogs
if (!function_exists('nxs_showImgToUseDlg')){ function nxs_showImgToUseDlg($nt, $ii, $imgToUse, $hide=false){ ?>
 <tr class="nxstbldo nxstbldo<?php echo strtoupper($nt).$ii; ?>" id="altFormatIMG<?php echo $nt.$ii; ?>" style="<?php echo $hide?'display:none;':''; ?>"><th scope="row" style="vertical-align:top; padding-top: 6px; text-align:right; width:60px; padding-right:10px;"><?php _e('Image(s) to use:', 'social-networks-auto-poster-facebook-twitter-g') ?></th>
                  <td><input type="checkbox" class="isAutoImg" <?php if ($imgToUse=='') { ?>checked="checked"<?php } ?>  id="isAutoImg-<?php echo $nt; ?><?php echo $ii; ?>" name="<?php echo $nt; ?>[<?php echo $ii; ?>][isAutoImg]" value="A"/> <?php _e('Auto', 'social-networks-auto-poster-facebook-twitter-g'); ?>
                  <?php if ($imgToUse!='') { ?> <a onclick="nxs_clPrvImgShow('<?php echo $nt; ?><?php echo $ii; ?>');return false;" href="#"><?php _e('Show all', 'social-networks-auto-poster-facebook-twitter-g'); ?></a><br/>  
                    <div class="nxs_prevImagesDiv" id="nxs_<?php echo $nt; ?><?php echo $ii; ?>_idivD"><img class="nxs_prevImages" src="<?php echo $imgToUse; ?>"><div style="display:block;" class="nxs_checkIcon"><div class="media-modal-icon"></div></div></div>
                  <?php } else { ?><br/><?php } ?>
                    <div id="imgPrevList-<?php echo $nt; ?><?php echo $ii; ?>" class="nxs_imgPrevList"></div>  
                    <input type="hidden" name="<?php echo $nt; ?>[<?php echo $ii; ?>][imgToUse]" value="<?php echo $imgToUse ?>" id="imgToUse-<?php echo $nt; ?><?php echo $ii; ?>" /> 
                </td></tr> 
<?php }}
if (!function_exists('nxs_showURLToUseDlg')){ function nxs_showURLToUseDlg($nt, $ii, $urlToUse){ ?>
 <tr class="nxstbldo nxstbldo<?php echo strtoupper($nt).$ii; ?>" style=""><th scope="row" style="vertical-align:top; padding-top: 6px; text-align:right; width:60px; padding-right:10px;"><?php _e('URL to use:', 'social-networks-auto-poster-facebook-twitter-g') ?></th>
                  <td><input type="checkbox" class="isAutoURL" <?php if ($urlToUse=='') { ?>checked="checked"<?php } ?>  id="isAutoURL-<?php echo $nt; ?><?php echo $ii; ?>" name="<?php echo $nt; ?>[<?php echo $ii; ?>][isAutoURL]" value="A"/> <?php _e('Auto', 'social-networks-auto-poster-facebook-twitter-g'); ?> - <i><?php _e('Post URL or globally defined URL will be used', 'social-networks-auto-poster-facebook-twitter-g'); ?></i>
                  
                    <div class="nxs_prevURLDiv" <?php if (trim($urlToUse)=='') { ?> style="display:none;"<?php } ?> id="isAutoURLFld-<?php echo $nt.$ii; ?>">
                      &nbsp;&nbsp;&nbsp;<?php _e('URL:', 'social-networks-auto-poster-facebook-twitter-g') ?> <input size="90" type="text" name="<?php echo $nt; ?>[<?php echo $ii; ?>][urlToUse]" value="<?php echo $urlToUse ?>" id="URLToUse-<?php echo $nt.$ii; ?>" /> 
                      <br/><span><?php _e('This will trigger "Network will decide attachment info". Image and other settings will be ignored.', 'social-networks-auto-poster-facebook-twitter-g') ?></span>
                    </div>
                  
                </td></tr> 
<?php }}

//## Tests
function nxs_cURLTestCode($url){  
  $out = 'There is a problem with cURL. You need to contact your server admin or hosting provider. Here is the PHP code to reproduce the problem:<br/><pre style="color:#005800">&lt;?php '."\r\n".' $ch = curl_init(); '."\r\n".' curl_setopt($ch, CURLOPT_URL, "'.$url.'"); '."\r\n".' curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36"); '."\r\n".' curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); '."\r\n".' curl_setopt($ch, CURLOPT_TIMEOUT, 10); '."\r\n".' curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); '."\r\n".' $response = curl_exec($ch); '."\r\n".' $errmsg = curl_error($ch); '."\r\n".' $cInfo = curl_getinfo($ch); '."\r\n".' curl_close($ch); '."\r\n".' print_r($errmsg); '."\r\n".' print_r($cInfo); '."\r\n".' print_r($response); '."\r\n".'?&gt;</pre>'; return $out; 
}
function nxs_cURLTest($url, $msg, $testText){ if ($testText=='getMyIP') echo 'Getting IP... <br/>'; else echo "<br/>--== Test Requested ... ".$url."<br/>";  $ch = curl_init(); curl_setopt($ch, CURLOPT_URL, $url); 
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.39 Safari/537.36"); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); curl_setopt($ch, CURLOPT_TIMEOUT, 10); curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
  $response = curl_exec($ch); $errmsg = curl_error($ch); $cInfo = curl_getinfo($ch); curl_close($ch); 
  if ($testText=='getMyIP') { $ip = strip_tags(CutFromTo($response,'Your IP Address:', '</span>')); echo "Your Server IP:".$ip.'<br/>'; } else { echo "Testing ... ".$url." - ".$cInfo['url']."<br/>";
    if (stripos($response, $testText)!==false) echo "....".$msg." - OK<br/>"; else { echo "....<b style='color:red;'>".$msg." - Problem</b><br/>"; prr($response); prr($errmsg); prr($cInfo); echo nxs_cURLTestCode($url);  }
  }
}

//## Reposter
if (!function_exists('nxs_adjRpst')){ function nxs_adjRpst($optionsii, $pval){  if (empty($optionsii['rpstDays'])) $optionsii['rpstDays'] = 0; if (empty($optionsii['rpstHrs'])) $optionsii['rpstHrs'] = 0; if (empty($optionsii['rpstMins'])) $optionsii['rpstMins'] = 0;
    
    $rpstEvrySecEx = $optionsii['rpstDays']*86400+$optionsii['rpstHrs']*3600+$optionsii['rpstMins']*60; $isRpstWasOn = isset($optionsii['rpstOn']) && $optionsii['rpstOn']=='1';
    
    if (isset($pval['rpstOn']))    $optionsii['rpstOn'] = $pval['rpstOn']; else $optionsii['rpstOn'] = 0;
    
    if (isset($pval['rpstDays']))  $optionsii['rpstDays'] = trim($pval['rpstDays']);       
    if (isset($pval['rpstHrs']))   $optionsii['rpstHrs'] = trim($pval['rpstHrs']);     if ((int)$optionsii['rpstHrs']>23) $optionsii['rpstHrs'] = 23;
    if (isset($pval['rpstMins']))  $optionsii['rpstMins'] = trim($pval['rpstMins']);   if ((int)$optionsii['rpstMins']>59) $optionsii['rpstMins'] = 59;    
    if (isset($pval['rpstRndMins']))  $optionsii['rpstRndMins'] = trim($pval['rpstRndMins']);       
    if (isset($pval['rpstPostIncl']))  $optionsii['rpstPostIncl'] = trim($pval['rpstPostIncl']);     
    
    if (isset($pval['rpstStop']))  $optionsii['rpstStop'] = trim($pval['rpstStop']); else $optionsii['rpstStop'] = 'O';      
    
     
    
    $rpstEvrySecNew = $optionsii['rpstDays']*86400+$optionsii['rpstHrs']*3600+$optionsii['rpstMins']*60;
    $rpstRNDSecs = isset($optionsii['rpstRndMins'])?$optionsii['rpstRndMins']*60:0; if ($rpstRNDSecs>$rpstEvrySecNew) $optionsii['rpstRndMins'] = 0;
    
    if ($rpstEvrySecNew!=$rpstEvrySecEx || (!$isRpstWasOn && $optionsii['rpstOn']=='1')) { $currTime = time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ); $optionsii['rpstNxTime'] = $currTime + $rpstEvrySecNew; }
    if (isset($pval['rpstType']))  $optionsii['rpstType'] = trim($pval['rpstType']);       
    if (isset($pval['rpstTimeType']))  $optionsii['rpstTimeType'] = trim($pval['rpstTimeType']);       
    if (isset($pval['rpstFromTime']))  $optionsii['rpstFromTime'] = trim($pval['rpstFromTime']);       
    if (isset($pval['rpstToTime']))  $optionsii['rpstToTime'] = trim($pval['rpstToTime']);       
    if (isset($pval['rpstOLDays']))  $optionsii['rpstOLDays'] = trim($pval['rpstOLDays']);       
    if (isset($pval['rpstNWDays']))  $optionsii['rpstNWDays'] = trim($pval['rpstNWDays']);       
    if (isset($pval['rpstOnlyPUP']))  $optionsii['rpstOnlyPUP'] = trim($pval['rpstOnlyPUP']); else $optionsii['rpstOnlyPUP'] = 0;     
    
    if (isset($pval['fltrsOn'])) $optionsii['fltrsOn'] = trim($pval['fltrsOn']); else $optionsii['fltrsOn'] = 0;     
            
    if (isset($pval['rpstBtwHrsType']))  $optionsii['rpstBtwHrsType'] = trim($pval['rpstBtwHrsType']);       
    if (isset($pval['rpstBtwHrsT']))  $optionsii['rpstBtwHrsT'] = trim($pval['rpstBtwHrsT']);  if (isset($optionsii['rpstBtwHrsT'])&&(int)$optionsii['rpstBtwHrsT']>23) $optionsii['rpstBtwHrsT'] = 23;         
    if (isset($pval['rpstBtwHrsF']))  $optionsii['rpstBtwHrsF'] = trim($pval['rpstBtwHrsF']);  if (isset($optionsii['rpstBtwHrsF'])&&(int)$optionsii['rpstBtwHrsF']>23) $optionsii['rpstBtwHrsF'] = 23;               
    if (isset($pval['rpstBtwDays']))  $optionsii['rpstBtwDays'] = $pval['rpstBtwDays']; else $optionsii['rpstBtwDays'] = array(); 
    return $optionsii;
}}

if (!function_exists('nxs_FltrsV3toV4')){ function nxs_FltrsV3toV4($o) { if (empty($o['fltrs'])) $o['fltrs'] = array(); if (empty($o['fltrs']['nxs_cats_names'])) $o['fltrs']['nxs_cats_names'] = array(); if (empty($o['fltrs']['nxs_tags_names'])) $o['fltrs']['nxs_tags_names'] = array();
  //## Conver filters from V3
  if (!empty($o['catSel'])) { $o['fltrsOn']='1';
     $ccts = trim($o['catSelEd']); $ccts = explode(',',$ccts); $o['fltrs']['nxs_cats_names'] = array_merge($ccts, $o['fltrs']['nxs_cats_names']); unset($o['catSel']); unset($o['catSelEd']); 
     if (isset($o['fltrs']['nxs_cats_names'])) {  foreach ($o['fltrs']['nxs_cats_names'] as $jj=>$tag) if (empty($tag)) unset($o['fltrs']['nxs_cats_names'][$jj]); else { $exT=''; if (is_numeric($tag)) $exT = term_exists((int)$tag, 'category'); else $exT = term_exists($tag, 'category');  
          if (!empty($exT)) $o['fltrs']['nxs_cats_names'][$jj]= $exT['term_id'];
        } 
     }
  }
  if (!empty($o['tagsSel'])) { $o['fltrsOn']='1';
     $ccts = trim($o['tagsSel']); $ccts = explode(',',$ccts); foreach ($ccts as $jj=>$cct) $ccts[$jj] = trim($cct);  $o['fltrs']['nxs_tags_names'] = array_merge($ccts, $o['fltrs']['nxs_tags_names']); unset($o['tagsSel']); unset($o['tagsSelX']);     
     if (isset($o['fltrs']['nxs_tags_names'])) { foreach ($o['fltrs']['nxs_tags_names'] as $jj=>$tag) if (empty($tag)) unset($o['fltrs']['nxs_tags_names'][$jj]); else { $exT=''; if (is_numeric($tag)) $exT = term_exists((int)$tag, 'post_tag'); else $exT = term_exists($tag, 'post_tag'); 
          if (empty($exT)) $exT = wp_insert_term($tag, 'post_tag'); if(!is_nxs_error($exT)) $o['fltrs']['nxs_tags_names'][$jj]= $exT['term_id'];
        } 
      }      
  } return $o;
}}

if (!function_exists('nxs_adjFilters')){ function nxs_adjFilters($pval, $o) {
      //## Filters
      if (isset($pval['fltrsOn'])) $o['fltrsOn'] = trim($pval['fltrsOn']); else $o['fltrsOn'] = 0;  //prr($o);
      if (isset($pval['fltrAfter'])) $o['fltrAfter'] = trim($pval['fltrAfter']); else if (isset($o['fltrAfter'])) unset($o['fltrAfter']); $o['fltrs'] = array(); 
      //## Image Selection      
      if (isset($pval['wpImgSize']))   $o['wpImgSize'] = trim($pval['wpImgSize']);
      //## Proxy
      if (isset($pval['proxyOn'])) $o['proxyOn'] = trim($pval['proxyOn']); else $o['proxyOn'] = 0;  //prr($o);
      if (isset($pval['proxy']))   $o['proxy']['proxy'] = trim($pval['proxy']); 
      if (isset($pval['proxyup'])) $o['proxy']['up'] = trim($pval['proxyup']);      
      //## Tags
      if (!empty($pval['nxs_ie_tags_names'])) $o['fltrs']['nxs_ie_tags_names'] = $pval['nxs_ie_tags_names'];
      if (isset($pval['nxs_tags_names'])) { foreach ($pval['nxs_tags_names'] as $jj=>$tag) if (empty($tag)) unset($pval['nxs_tags_names'][$jj]); else { $exT=''; if (is_numeric($tag)) $exT = term_exists((int)$tag, 'post_tag'); else $exT = term_exists($tag, 'post_tag'); 
          if (empty($exT)) $exT = wp_insert_term($tag, 'post_tag'); $pval['nxs_tags_names'][$jj]= $exT['term_id'];
        } $o['fltrs']['nxs_tags_names'] = $pval['nxs_tags_names'];
      }      
      //## Cats
      if (!empty($pval['nxs_ie_cats_names'])) $o['fltrs']['nxs_ie_cats_names'] = $pval['nxs_ie_cats_names'];      
      if (isset($pval['nxs_cats_names'])) {  foreach ($pval['nxs_cats_names'] as $jj=>$tag) if (empty($tag)) unset($pval['nxs_cats_names'][$jj]); else { $exT=''; if (is_numeric($tag)) $exT = term_exists((int)$tag, 'category'); else $exT = term_exists($tag, 'category');  
          if (empty($exT)) $exT = wp_insert_term($tag, 'category'); $pval['nxs_cats_names'][$jj]= $exT['term_id'];
        } $o['fltrs']['nxs_cats_names'] = $pval['nxs_cats_names'];
      }     
      $o = nxs_FltrsV3toV4($o);
      if (isset($pval['nxs_post_status'])) $o['fltrs']['nxs_post_status'] = $pval['nxs_post_status'];
      if (!empty($pval['nxs_ie_posttypes'])) $o['fltrs']['nxs_ie_posttypes'] = $pval['nxs_ie_posttypes'];
      if (isset($pval['nxs_post_type'])) $o['fltrs']['nxs_post_type'] = $pval['nxs_post_type'];
      if (isset($pval['nxs_post_formats'])) $o['fltrs']['nxs_post_formats'] = $pval['nxs_post_formats'];
      if (isset($pval['nxs_user_names'])) $o['fltrs']['nxs_user_names'] = $pval['nxs_user_names'];
      if (!empty($pval['nxs_search_keywords'])) $o['fltrs']['nxs_search_keywords'] = $pval['nxs_search_keywords'];
      
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
      
      if (!empty($pval['nxs_count_term_compares'])) $o['fltrs']['nxs_count_term_compares'] = $pval['nxs_count_term_compares'];
      if (!empty($pval['nxs_term_names'])) {
        $o['fltrs']['nxs_tax_names'] = (isset($pval['nxs_tax_names']))?$pval['nxs_tax_names']:'';  $o['fltrs']['nxs_term_names'] = (isset($pval['nxs_term_names']))?$pval['nxs_term_names']:'';
        $o['fltrs']['nxs_term_operator'] = (isset($pval['nxs_term_operator']))?$pval['nxs_term_operator']:'';
        $o['fltrs']['nxs_term_children'] = (isset($pval['nxs_term_children']))?$pval['nxs_term_children']:'';
        $o['fltrs']['nxs_term_relation'] = (isset($pval['nxs_term_relation']))?$pval['nxs_term_relation']:'';
      } 
      if (!empty($pval['nxs_count_term_compares']) && $pval['nxs_count_term_compares']>1) for( $jj = 2; $jj <= $pval['nxs_count_term_compares']; $jj++ ) {
        $o['fltrs']['nxs_tax_names_'.$jj] = (isset($pval['nxs_tax_names_'.$jj]))?$pval['nxs_tax_names_'.$jj]:''; $o['fltrs']['nxs_term_names_'.$jj] = (isset($pval['nxs_term_names_'.$jj]))?$pval['nxs_term_names_'.$jj]:'';
        $o['fltrs']['nxs_term_operator_'.$jj] = (isset($pval['nxs_term_operator_'.$jj]))?$pval['nxs_term_operator_'.$jj]:'';
        $o['fltrs']['nxs_term_children_'.$jj] = (isset($pval['nxs_term_children_'.$jj]))?$pval['nxs_term_children_'.$jj]:'';
        $o['fltrs']['nxs_term_relation_'.$jj] = (isset($pval['nxs_term_relation_'.$jj]))?$pval['nxs_term_relation_'.$jj]:'';        
      } return $o;   
 }}

function nxs_showRepostSettings($nt, $ii, $options){ global $nxs_snapAvNts, $nxs_plurl;   if ((!current_user_can( 'manage_options' ) && current_user_can( 'haveown_snap_accss' ) )) return;
  if (empty($options['rpstPostIncl'])) $options['rpstPostIncl'] = 0; if (empty($options['rpstPostIncl'])) $options['rpstLastShTime'] = ''; if (empty($options['rpstNxTime'])) $options['rpstNxTime'] = '';
  if (empty($options['rpstLastPostID'])) $options['rpstLastPostID'] = ''; if (empty($options['rpstType'])) $options['rpstType'] = '2'; if (empty($options['rpstTimeType'])) $options['rpstTimeType'] = ''; 
  if (empty($options['rpstFromTime'])) $options['rpstFromTime'] = ''; if (empty($options['rpstToTime'])) $options['rpstToTime'] = ''; if (empty($options['rpstType'])) $options['rpstType'] = '';
  if (empty($options['rpstNWDays'])) $options['rpstNWDays'] = ''; if (empty($options['rpstOLDays'])) $options['rpstOLDays'] = '';

  ?>
    <div class="nxs_tls_cpt">
   <?php _e('Auto Reposting', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;<span class="nxsInstrSpan"><a href="http://www.nextscripts.com/snap-features/old-posts-auto-reposting/" target="_blank"><?php _e('[Instructions]', 'social-networks-auto-poster-facebook-twitter-g'); ?></a> &nbsp;&nbsp; <br/><b style="color: darkred;">Please note:</b> This feature is depreciated, <a href="http://www.nextscripts.com/blog/old-posts-reposting-no-longer-supported/" target="_blank">no longer supported</a> and will be replaced with something much better in the upcoming <a href="http://www.nextscripts.com/tag/v4/" target="_blank">SNAP Version 4</a> </span>
   </div>
   
   <?php $cr = get_option('NXS_cronCheck'); if (!empty($cr) && is_array($cr) && isset($cr['status']) && $cr['status']=='0') { 
      global $plgn_NS_SNAutoPoster; if (!isset($plgn_NS_SNAutoPoster)) return; $gOptions = $plgn_NS_SNAutoPoster->nxs_options; 
       if (isset($gOptions['forceBrokenCron']) && $gOptions['forceBrokenCron'] =='1') { ?> 
         <span style="color: red"> <?php _e('Your WP Cron is not working correctly. Auto Reposting service is active by force. <br/> This might cause problems. Please see the test results and recommendations', 'social-networks-auto-poster-facebook-twitter-g'); ?>
         &nbsp;-&nbsp;<a target="_blank" href="<?php global $nxs_snapThisPageUrl; echo $nxs_snapThisPageUrl; ?>&do=crtest">WP Cron Test Results</a></span>
        <?php } else { ?> <span style="color: red"> <?php _e('Auto Reposting service is Disabled. Your WP Cron is not working correctly. Please see the test results and recommendations', 'social-networks-auto-poster-facebook-twitter-g'); ?>
     &nbsp;-&nbsp;<a target="_blank" href="<?php global $nxs_snapThisPageUrl; echo $nxs_snapThisPageUrl; ?>&do=crtest">WP Cron Test Results</a></span>
   <?php return; } } ?>
   
   <a href="#" style="margin-left:50px; " class="button" onclick="jQuery(this).next().show(); jQuery(this).hide(); return false;">I understand that this feature is provided "as is" with <b>no support</b> and might not work correctly.</a>
   <div class="nxs_tls_bd" style="display: none;">
     <div class="nxs_tls_sbInfo"><?php _e('Plugin could autorepost existing posts', 'social-networks-auto-poster-facebook-twitter-g'); ?></div>
     <input value="1"  id="riC<?php echo $ii; ?>" <?php if (isset($options['rpstOn']) && trim($options['rpstOn'])=='1') echo "checked"; ?> type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstOn]"/> 
       <b><?php _e('Repost existing posts every', 'social-networks-auto-poster-facebook-twitter-g'); ?> </b>
     
     <input type="text" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstDays]" style="width: 35px;" value="<?php echo isset($options['rpstDays'])?$options['rpstDays']:'0'; ?>" />&nbsp;<?php _e('Days', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;
     <input type="text" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstHrs]" style="width: 35px;" value="<?php echo isset($options['rpstHrs'])?$options['rpstHrs']:'2'; ?>" />&nbsp;<?php _e('Hours', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;
     <input type="text" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstMins]" style="width: 35px;" value="<?php echo isset($options['rpstMins'])?$options['rpstMins']:'0'; ?>" />&nbsp;<?php _e('Minutes', 'social-networks-auto-poster-facebook-twitter-g'); ?>     
     <div style="padding-left:10px;padding-top:10px;line-height:30px;"> 
     
     <b><?php _e('Randomize posting time &#177;', 'social-networks-auto-poster-facebook-twitter-g'); ?> </b>
     <input type="text" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstRndMins]" style="width: 35px;" value="<?php echo isset($options['rpstRndMins'])?$options['rpstRndMins']:'15'; ?>" onmouseout="hidePopShAtt('RPST1');" onmouseover="showPopShAtt('RPST1', event);" />&nbsp;<?php _e('Minutes', 'social-networks-auto-poster-facebook-twitter-g'); ?>
     <br/>     
     <input value="1"  id="riOC<?php echo $ii; ?>" <?php if (isset($options['rpstOnlyPUP']) && trim($options['rpstOnlyPUP'])=='1') echo "checked"; ?> type="checkbox" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstOnlyPUP]"/> 
       <b><?php _e('Repost ONLY previously unautoposted posts', 'social-networks-auto-poster-facebook-twitter-g'); ?></b>
     <br/>    
     <?php if (function_exists('nxs_doSMAS41')) nxs_doSMAS41($nt, $ii, $options); ?>          
     <b><?php _e('Get posts', 'social-networks-auto-poster-facebook-twitter-g'); ?></b>
       <select id="riS<?php echo $nt; ?><?php echo $ii; ?>" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstType]" onchange="nxs_actDeActTurnOff(jQuery(this).attr('id'));"><?php if (function_exists('nxs_doSMAS42')) nxs_doSMAS42($options); ?>        
        <option value="2" <?php  if (isset($options['rpstType']) && $options['rpstType']=='2') echo 'selected="selected"' ?>>One By One - Old to New</option><option value="3" <?php if (isset($options['rpstType']) && $options['rpstType']=='3') echo 'selected="selected"' ?>>One By One - New to Old</option>
        </select> 
      <br/> 
      <div style="padding-left: 15px;">
      <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstTimeType]" value="D" <?php if (isset($options['rpstTimeType']) && $options['rpstTimeType']=='D') echo 'checked="checked"'; ?> />
      
      <?php _e('from', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;<input type="text" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstFromTime]" style="width: 75px;" value="<?php echo isset($options['rpstFromTime'])?$options['rpstFromTime']:''; ?>" />&nbsp;          
      <?php _e('to', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;<input type="text" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstToTime]" style="width: 75px;" value="<?php echo isset($options['rpstToTime'])?$options['rpstToTime']:''; ?>" />
     <br/>
     <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstTimeType]" value="A" <?php if (!isset($options['rpstTimeType']) || $options['rpstTimeType']=='A') echo 'checked="checked"'; ?> />
     <?php _e('Older than', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;<input type="text" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstOLDays]" style="width: 35px;" value="<?php  echo isset($options['rpstOLDays'])?$options['rpstOLDays']:'30'; ?>" />&nbsp;<?php _e('Days', 'social-networks-auto-poster-facebook-twitter-g'); ?>          
     <?php _e('and Newer than', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;<input type="text" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstNWDays]" style="width: 35px;" value="<?php  echo isset($options['rpstNWDays'])?$options['rpstNWDays']:'365'; ?>" />&nbsp;<?php _e('Days', 'social-networks-auto-poster-facebook-twitter-g'); ?>     
     </div>
     <div id="riS<?php echo $nt; ?><?php echo $ii; ?>xd"  style="padding-left: 0px;<?php if (isset($options['rpstType']) && $options['rpstType']=='1') echo "display:none;"; ?>"><b><?php _e('When finished', 'social-networks-auto-poster-facebook-twitter-g'); ?>:</b>       
         <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstStop]" value="O" <?php if (empty($options['rpstStop']) || (isset($options['rpstStop']) && trim($options['rpstStop'])=='O')) echo "checked"; ?>  /> <?php _e('Auto Turn Reposting Off', 'social-networks-auto-poster-facebook-twitter-g') ?>
         &nbsp;&nbsp;&nbsp;
         <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstStop]" value="W" <?php if (isset($options['rpstStop']) && trim($options['rpstStop'])=='W') echo 'checked="cheXcked"'; ?> /> <?php _e('Wait for new posts', 'social-networks-auto-poster-facebook-twitter-g') ?>
         &nbsp;&nbsp;&nbsp;
         <input type="radio" name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstStop]" value="R" <?php if (isset($options['rpstStop']) && trim($options['rpstStop'])=='R') echo 'checked="cheTcked"'; ?> /> <?php _e('Loop it. Reset and Start from the beginning', 'social-networks-auto-poster-facebook-twitter-g'); ?>
         </div>
    
     <hr/>
     <strong style="font-size: 12px; margin: 10px; margin-left: 1px;">New posts will be set by default to:</strong>
             <select name="<?php echo $nt; ?>[<?php echo $ii; ?>][rpstPostIncl]"><option <?php echo !empty($options['rpstPostIncl'])?'selected="selected"':''; ?> value="nxsi<?php echo $ii.$nt; ?>">Enabled for Repost</option>
             <option <?php echo empty($options['rpstPostIncl'])?'selected="selected"':''; ?>  value="0">Disabled for Repost</option></select><br/>
      <div style="padding-left: 15px;"> <img id="nxsLoadingImg<?php echo $nt; ?><?php echo $ii; ?>" style="display: none;" src='<?php echo $nxs_plurl; ?>img/ajax-loader-sm.gif' />       
      
      <?php      
        global $nxs_rpst_older, $nxs_rpst_newer, $nxs_rpst_lastID, $nxs_rpst_lastTime, $nxs_rpst_type, $nxs_rpst_code, $nxs_rpst_NT;  $ntOpts = $options;         
        $currTime = time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
        if (!empty($ntOpts['nxsCPTSeld'])) $tpArray = maybe_unserialize($ntOpts['nxsCPTSeld']); else $tpArray = 'post';
        if ($ntOpts['rpstType']=='1') $args = array ( 'orderby' => 'rand', 'posts_per_page' => '1', 'post_type' => $tpArray, 'ignore_sticky_posts' => 1, 'post_status' => 'publish', 'suppress_filters' => false ); 
        if ($ntOpts['rpstType']=='2') $args = array ( 'posts_per_page' => '1', 'orderby' => 'date ID', 'order'=>'ASC', 'post_type' => $tpArray, 'post_status' => 'publish', 'suppress_filters' => false );
        if ($ntOpts['rpstType']=='3') $args = array ( 'posts_per_page' => '1', 'orderby' => 'date ID', 'order'=>'DESC', 'post_type' => $tpArray, 'post_status' => 'publish', 'suppress_filters' => false );                   
        $rpstToTime = strtotime($ntOpts['rpstToTime']); if ($currTime > $rpstToTime) $rpstToTime = $currTime;
        if (!empty($ntOpts['rpstFromTime'])) { $rpstFromTime = strtotime($ntOpts['rpstFromTime']); if ($currTime < $rpstFromTime) $rpstFromTime = $currTime;} else $rpstFromTime = '1999-01-01 00:00:00'; 
        if ($ntOpts['rpstTimeType']=='D') { $nxs_rpst_older = ceil(abs($currTime - $rpstToTime) / 86400); $nxs_rpst_newer = ceil(abs($currTime - $rpstFromTime) / 86400);                  
          } else { $nxs_rpst_older = !empty($ntOpts['rpstOLDays'])?$ntOpts['rpstOLDays']:0; $nxs_rpst_newer = !empty($ntOpts['rpstNWDays'])?$ntOpts['rpstNWDays']:5000; } $ggg = $ntOpts['rpstType']=='1'?'Random':($ntOpts['rpstType']=='3'?'New to Old':'Old to New');
        if ($nxs_rpst_newer>5000) $nxs_rpst_newer = 5000;  if ($nxs_rpst_newer<$nxs_rpst_older) $nxs_rpst_older = 0;                 
        $nxs_rpst_code = 'nxsi'.$ii.$nt; $nxs_rpst_NT = strtoupper($nt);
        add_filter( 'posts_join' , 'nxs_custom_posts_join');
        if (isset($ntOpts['rpstOnlyPUP']) && trim($ntOpts['rpstOnlyPUP'])=='1')  {  add_filter( 'posts_where', 'nxs_filter_where_only' ); }
        add_filter( 'posts_where', 'nxs_filter_where' ); $query = new WP_Query( $args ); /* prr($query->request); */ remove_filter( 'posts_where', 'filter_where' );
        echo "Total posts included in reposting: ".$query->found_posts;      
      ?><br/>
      
     <?php _e('Set All Existing Posts to: ', 'social-networks-auto-poster-facebook-twitter-g'); ?>
     &nbsp;&nbsp;<span class="nxsInstrSpan"><a href="#" onclick="nxs_setRpstAll('<?php echo $nt; ?>','1','<?php echo $ii; ?>'); return false;"><?php _e('[Enabled for Repost]', 'social-networks-auto-poster-facebook-twitter-g'); ?></a> </span>
     &nbsp;&nbsp;<span class="nxsInstrSpan"><a href="#" onclick="nxs_setRpstAll('<?php echo $nt; ?>','0','<?php echo $ii; ?>'); return false;"><?php _e('[Disabled for Repost]', 'social-networks-auto-poster-facebook-twitter-g'); ?></a> </span>        
     &nbsp;&nbsp;<span class="nxsInstrSpan"><a href="#" onclick="nxs_setRpstAll('<?php echo $nt; ?>','2','<?php echo $ii; ?>'); return false;"><?php _e('[Enabled/Disabled for Repost according to Categories/Tags/Taxonomies filters]', 'social-networks-auto-poster-facebook-twitter-g'); ?></a> </span>        
              
     </div><hr/>
     <b><?php _e('Last post', 'social-networks-auto-poster-facebook-twitter-g'); ?></b>&nbsp;(ID:&nbsp;<?php echo !empty($options['rpstLastPostID'])?$options['rpstLastPostID']:''; ?>)&nbsp;<b><?php _e('was re-posted on:', 'social-networks-auto-poster-facebook-twitter-g'); ?></b>&nbsp;<?php echo $options['rpstLastShTime']>0?date_i18n('Y-m-d H:i', $options['rpstLastShTime']):'Never'; ?>
        &nbsp;&nbsp;<b><?php _e('Next post will be ~', 'social-networks-auto-poster-facebook-twitter-g'); ?></b>&nbsp;<?php echo $options['rpstNxTime']>0?date_i18n('Y-m-d H:i', $options['rpstNxTime']):'Never'; ?> &lt;==
        &nbsp;&nbsp;<span class="nxsInstrSpan"><a href="#" onclick="nxs_setRpstAll('<?php echo $nt; ?>','X','<?php echo $ii; ?>'); return false;"><?php _e('[Reset]', 'social-networks-auto-poster-facebook-twitter-g'); ?></a> </span>
     <br/>
     <b><?php _e('Set "Last re-posted post ID" to:', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;<input type="text" id="<?php echo $nt; ?><?php echo $ii; ?>SetLPID" style="width: 65px;" value="<?php echo $options['rpstLastPostID']; ?>" />
     &nbsp;&nbsp;<span class="nxsInstrSpan"><a href="#" onclick="nxs_setRpstAll('<?php echo $nt; ?>','L','<?php echo $ii; ?>'); return false;"><?php _e('[Set]', 'social-networks-auto-poster-facebook-twitter-g'); ?></a> </span></b>
     </div>      
   </div>      
    <?php 
}
function nxs_custom_posts_join($join){ global $wpdb; $join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id "; return $join;}
function nxs_filter_where_only( $where = '' ) { global $wpdb; $where .= " AND ($wpdb->postmeta.meta_key = 'snap_isAutoPosted' AND $wpdb->postmeta.meta_value = '1') "; return $where; }
function nxs_filter_where( $where = '' ) { global $wpdb, $nxs_rpst_older, $nxs_rpst_newer, $nxs_rpst_lastID, $nxs_rpst_lastTime, $nxs_rpst_type, $nxs_rpst_typeONLY, $nxs_rpst_code, $nxs_rpst_NT; 
    $where .= " AND post_date > '" . date_i18n('Y-m-d', strtotime('-'.$nxs_rpst_newer.' days')) . " 00:00:00'" . " AND post_date < '" . date_i18n('Y-m-d', strtotime('-'.$nxs_rpst_older.' days')) . " 23:59:59'";    
    $where .= " AND ($wpdb->postmeta.meta_key = 'snap".$nxs_rpst_NT."' AND $wpdb->postmeta.meta_value LIKE '%".$nxs_rpst_code."%') ";
    if ($nxs_rpst_type=='2' && $nxs_rpst_lastID!='') $where .= " AND ( (post_date = '".$nxs_rpst_lastTime."' && ID > ".$nxs_rpst_lastID." ) || post_date > '".$nxs_rpst_lastTime."' )";
    if ($nxs_rpst_type=='3' && $nxs_rpst_lastID!='') $where .= " AND ( (post_date = '".$nxs_rpst_lastTime."' && ID < ".$nxs_rpst_lastID." ) || post_date < '".$nxs_rpst_lastTime."' )";
    if ($nxs_rpst_typeONLY) $where .= " AND ($wpdb->postmeta.meta_key = 'snap_isAutoPosted' AND $wpdb->postmeta.meta_value = '1') ";
    return $where; 
}

function nxs_rePoster(){ global $nxs_snapAvNts,$plgn_NS_SNAutoPoster, $nxs_rpst_older, $nxs_rpst_newer, $nxs_rpst_lastID, $nxs_rpst_lastTime, $nxs_rpst_type, $nxs_rpst_code, $nxs_rpst_NT;   
  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options;  $rpstBtwHrsF = 0; $rpstBtwHrsT = 0;
  $currTime = time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );  $hasChanged = false;   
  // if (stripos($_SERVER["REQUEST_URI"], 'wp-cron.php')!==false) nxs_addToLogN('A', 'NXSPoster - Cron', $logNT, '-=Time=- '.$ret. "ERR: ".$currTime, $extInfo);
  if (stripos($_SERVER["REQUEST_URI"], 'wp-cron.php')===false) return false; $warn = true;
  
  foreach ($nxs_snapAvNts as $avNt) {
    if (isset($options[$avNt['lcode']]) && is_array($options[$avNt['lcode']]) && count($options[$avNt['lcode']])>0) { 
        foreach ($options[$avNt['lcode']] as $ii=>$ntOpts) { $logNT = '<span style="color:#800000">'.$avNt['name'].'</span> - '.(isset($ntOpts['nName'])?$ntOpts['nName']:'');      
          if (isset($ntOpts['rpstOn']) && $ntOpts['rpstOn']=='1') {    
            //## Calculate Times
            $lastTime = (isset($ntOpts['rpstLastShTime']) && (int)$ntOpts['rpstLastShTime']>0 )?$ntOpts['rpstLastShTime']:$currTime;
            $rpstEvrySec = $ntOpts['rpstDays']*86400+$ntOpts['rpstHrs']*3600+$ntOpts['rpstMins']*60; $rndSec = $ntOpts['rpstRndMins']*60;
            $nxTime = (isset($ntOpts['rpstNxTime']) && (int)$ntOpts['rpstNxTime']>0)?$ntOpts['rpstNxTime']:$currTime+$rpstEvrySec;  
            //## First time? Set the options and get out. 
            if (empty($ntOpts['rpstLastShTime'])) {  $ntOpts['rpstLastShTime'] = $currTime; $ntOpts['rpstNxTime'] = $lastTime + $rpstEvrySec + rand(0-$rndSec, $rndSec); 
              $options[$avNt['lcode']][$ii] = $ntOpts; update_option($plgn_NS_SNAutoPoster->dbOptionsName, $options); $plgn_NS_SNAutoPoster->nxs_options = $options;  
               nxs_addToLogN('S', 'RE-Poster', $logNT, 'Initialization - First post will be: '.date_i18n('Y-m-d H:i:s', $ntOpts['rpstNxTime']), $extInfo); continue; 
            }
            //## Is it time to post?
            if ($nxTime < $currTime) {  $times =  "Requested Time: ".date_i18n('Y-m-d H:i:s', $nxTime)." | Current Time:".date_i18n('Y-m-d H:i:s', $currTime)." | "; 
              //## Check if WP Cron is healthy   
              if ($warn) { $cr = get_option('NXS_cronCheck'); $warn = false; if (!empty($cr) && is_array($cr) && isset($cr['status']) && $cr['status']=='0') { global $nxs_snapThisPageUrl;     
                if (isset($options['forceBrokenCron']) && $options['forceBrokenCron'] =='1') 
                  nxs_addToLogN('W', 'Re-Poster is active by force. This could cause problems.', $logNT, 'Please see ', '<a target="_blank" href="'.$nxs_snapThisPageUrl.'&do=crtest">WP Cron Test Results</a>|'.$times); 
                else { nxs_addToLogN('W', 'Re-Poster is Disabled', $logNT, 'Please see ', '<a target="_blank" href="'.$nxs_snapThisPageUrl.'&do=crtest">WP Cron Test Results</a>|'.$times); return; }
              }} 
              
              if (!isset($ntOpts['rpstNxTime']) || (int)$ntOpts['rpstNxTime']<1) $ntOpts['rpstNxTime'] = 0; //prr($ntOpts);            
              //## Check if Day or Hour is excluded
              if (isset($ntOpts['rpstBtwHrsType']) && $ntOpts['rpstBtwHrsType']=='D'){ 
                //## Check Days
                if (isset($ntOpts['rpstBtwDays']) && count($ntOpts['rpstBtwDays'])>0 ) $rpstBtwDays = $ntOpts['rpstBtwDays']; else $rpstBtwDays = array();            
                if (is_array($rpstBtwDays) && count($rpstBtwDays)>0) { $currDay = (int)date_i18n('w'); if (!(in_array($currDay, $rpstBtwDays))) { // echo "D :( ";
                  nxs_addToLogN('S', 'RE-Poster', $logNT, 'Skipped - Excluded Day - '.$currDay, $extInfo);  continue;  
                }}
                //## Check Hours
                if (isset($ntOpts['rpstBtwHrsF']) && (int)$ntOpts['rpstBtwHrsF']>0) $rpstBtwHrsF = (int)$ntOpts['rpstBtwHrsF']; else $rpstBtwHrsF = 0;
                if (isset($ntOpts['rpstBtwHrsT']) && (int)$ntOpts['rpstBtwHrsT']>0) $rpstBtwHrsT = (int)$ntOpts['rpstBtwHrsT'];
                if ($rpstBtwHrsT>0) { $currHour = (int)date_i18n('H', $currTime);  //echo "H ".$currHour." ?";
                if ( !( ($rpstBtwHrsF<$rpstBtwHrsT && $currHour<$rpstBtwHrsT && $currHour>=$rpstBtwHrsF) || ($rpstBtwHrsF>$rpstBtwHrsT && $currHour<$rpstBtwHrsF && $currHour>=$rpstBtwHrsT) )) {  //echo "H :( ";
                  nxs_addToLogN('S', 'RE-Poster', $logNT, 'Skipped - Excluded Hour - '.$currHour, $extInfo);  continue;  
                }}
              }
              //## Do Post                
              $hasChanged = true; $nxs_rpst_type = $ntOpts['rpstType'];
              $nxs_rpst_lastID = (isset($ntOpts['rpstLastPostID']) && (int)$ntOpts['rpstLastPostID']>0)?$ntOpts['rpstLastPostID']:($ntOpts['rpstType']=='3'?'90000000':'0');
              $nxs_rpst_lastTime = (!empty($ntOpts['rpstLastPostTime']) && $ntOpts['rpstLastPostTime']!='2050-12-12' && $ntOpts['rpstLastPostTime']!='1975-01-01')?$ntOpts['rpstLastPostTime']:($ntOpts['rpstType']=='3'?'2050-12-12':'1975-01-01');
              if (!empty($ntOpts['nxsCPTSeld'])) $tpArray = maybe_unserialize($ntOpts['nxsCPTSeld']); else $tpArray = 'post';
              //nxs_addToLogN('S', 'pTypes', $logNT, print_r($tpArray, true), $extInfo); // $tpArray = array('post', 'location');
              if ($ntOpts['rpstType']=='1') $args = array ( 'orderby' => 'rand', 'posts_per_page' => '1', 'post_type' => $tpArray, 'ignore_sticky_posts' => 1, 'post_status' => 'publish', 'suppress_filters' => false ); 
              if ($ntOpts['rpstType']=='2') $args = array ( 'posts_per_page' => '1', 'orderby' => 'date ID', 'order'=>'ASC', 'post_type' => $tpArray, 'post_status' => 'publish', 'suppress_filters' => false );
              if ($ntOpts['rpstType']=='3') $args = array ( 'posts_per_page' => '1', 'orderby' => 'date ID', 'order'=>'DESC', 'post_type' => $tpArray, 'post_status' => 'publish', 'suppress_filters' => false );                   
              //## Get Post for Reposting
              //   nxs_addToLogN('S', 'pTypes- ARG', $logNT, print_r($args, true), $extInfo);
              $rpstToTime = strtotime($ntOpts['rpstToTime']); if ($currTime < $rpstToTime) $rpstToTime = $currTime;  $rpstFromTime = strtotime($ntOpts['rpstFromTime']); if ($currTime < $rpstFromTime) $rpstFromTime = $currTime;
              if ($ntOpts['rpstTimeType']=='D') { $nxs_rpst_older = ceil(abs($currTime - $rpstToTime) / 86400); 
                $nxs_rpst_newer = ceil(abs($currTime - $rpstFromTime) / 86400);                   
              } else {  $nxs_rpst_older = !empty($ntOpts['rpstOLDays'])?$ntOpts['rpstOLDays']:0; $nxs_rpst_newer = !empty($ntOpts['rpstNWDays'])?$ntOpts['rpstNWDays']:5000; } $ggg = $ntOpts['rpstType']=='1'?'Random':($ntOpts['rpstType']=='3'?'New to Old':'Old to New');
              if ($nxs_rpst_newer>5000) $nxs_rpst_newer = 5000;  if ($nxs_rpst_newer<$nxs_rpst_older) $nxs_rpst_older = 0;
                $nxs_rpst_code = 'nxsi'.$ii.$avNt['lcode']; $nxs_rpst_NT = strtoupper($avNt['lcode']);
                add_filter( 'posts_join' , 'nxs_custom_posts_join');
                if (isset($ntOpts['rpstOnlyPUP']) && trim($ntOpts['rpstOnlyPUP'])=='1')  {  add_filter( 'posts_where', 'nxs_filter_where_only' ); }
                add_filter( 'posts_where', 'nxs_filter_where' ); $query = new WP_Query( $args ); remove_filter( 'posts_where', 'filter_where' ); // $extInfo = print_r($query, true);                   
                   
                $rpstLastPostID = $query->posts[0]->ID;  $rpstLastPostTime = $query->posts[0]->post_date;  $ntOpts['rpstLastPostTime'] =  ($rpstLastPostTime!='')?$rpstLastPostTime:($ntOpts['rpstType']=='3'?'1985-01-01':'2050-12-12');
                $extInfo = " | Reposting (<b>".$ggg."</b>) POST ID:".$rpstLastPostID. " | Total posts included in reposting: ".$query->found_posts." | Prev Post ID:".$nxs_rpst_lastID ." | ".($options['extDebug']=='1'?"|Query: ".print_r($query->request, true):'');
                   
                //echo "<br/>\r\n".$rpstEvrySec."<br/>\r\n";
                $ntOpts['rpstLastShTime'] = $currTime; $rndTime = rand(0-$rndSec, $rndSec); $ntOpts['rpstNxTime'] = $lastTime + $rpstEvrySec*2 + $rndTime;  
                if ((int)$rpstLastPostID<1) {  
                  $extInfo = " | Reposting (<b>".$ggg."</b>) | Total posts included in reposting: ".$query->found_posts." | ".($options['extDebug']=='1'?"|Query: ".print_r($query->request, true):'');                      
                  if ($ntOpts['rpstType']=='1') nxs_addToLogN('S','Random Re-Posting - Nothing to post',$logNT, $times.'| Last Time:'.date_i18n('Y-m-d H:i:s', $lastTime).' RND Time: '.$rndTime.' - Next Time - '.date_i18n('Y-m-d H:i:s', $ntOpts['rpstNxTime']).")", $extInfo);
                  else { if (!isset($ntOpts['rpstStop']) || (isset($ntOpts['rpstStop']) && trim($ntOpts['rpstStop'])=='O')) { $ntOpts['rpstOn']='0';  
                      nxs_addToLogN('S','RE-Posting',$logNT,'End of Query - Turning Auto-Reposting Off - '.$times.' | Last Time:'.date_i18n('Y-m-d H:i:s', $lastTime).' Next Time - '.date_i18n('Y-m-d H:i:s', $ntOpts['rpstNxTime']).")", $extInfo);
                    } elseif ( trim($ntOpts['rpstStop'])=='W') 
                      nxs_addToLogN('S','RE-Posting',$logNT,'(Waiting mode is ON) <b>Nothing to Repost.</b> | '.$times.' | Last Time:'.date_i18n('Y-m-d H:i:s', $lastTime).' Next Time - '.date_i18n('Y-m-d H:i:s', $ntOpts['rpstNxTime']).")", $extInfo);
                    elseif ( trim($ntOpts['rpstStop'])=='R') { $ntOpts['rpstLastPostID'] = ($ntOpts['rpstType']=='3'?'90000000':'0');
                      nxs_addToLogN('S','RE-Posting',$logNT,'(<b>Nothing to Repost.</b> - End of Query - <b>Resetting<b>)  | '.$times.' | Last Time:'.date_i18n('Y-m-d H:i:s', $lastTime).' Next Time - '.date_i18n('Y-m-d H:i:s', $ntOpts['rpstNxTime']).")", $extInfo);                    }
                  }
                } else { $ntOpts['rpstLastPostID'] = $rpstLastPostID;                                      
                  //## Actual Post                     
                  $clName = 'nxs_snapClass'.strtoupper($avNt['lcode']); $pFuncName = 'nxs_doPublishTo'.strtoupper($avNt['lcode']);                     
                  $po =  get_post_meta($rpstLastPostID, 'snap'.strtoupper($avNt['lcode']), true); $po =  maybe_unserialize($po); 
                  $ntClInst = new $clName(); $ntOpts['ii'] = $ii; $ntOpts['pType'] = 'aj'; $ntOptsPost = $ntClInst->adjMetaOpt($ntOpts, $po[$ii]); 
                  if ($options['extDebug']=='1') $extInfo .= "<br/><br/>NT OPTS: ".print_r($ntOptsPost, true);  if ($options['extDebug']=='1') $extInfo .= "ARGS: <br/><br/>".print_r($args, true);
                  $result = $pFuncName($rpstLastPostID, $ntOptsPost); //if ($result == 200) die("Successfully sent your post to App.Net."); else die($result);                          
                  nxs_addToLogN('S', 'RE-Posting', $logNT, 'OK  | '.$times.' | Previous Time:'.date_i18n('Y-m-d H:i:s', $lastTime).'| Next Shedulled Time - '.date_i18n('Y-m-d H:i:s', $ntOpts['rpstNxTime']).")<br/>", $extInfo);
                }
              } $options[$avNt['lcode']][$ii] = $ntOpts; // prr($ntOpts);
            }
        }      
    }
  } if ($hasChanged) { update_option($plgn_NS_SNAutoPoster->dbOptionsName, $options); $plgn_NS_SNAutoPoster->nxs_options = $options; }
}

function nxs_chckBrwsr() { $isOK = false; if (empty($_SERVER['HTTP_USER_AGENT'])) return true;
  if (preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) $isOK = true;    if (preg_match('/Internet Explorer/i',$_SERVER['HTTP_USER_AGENT'])) $isOK = true;
  if (preg_match('/Firefox/i',$_SERVER['HTTP_USER_AGENT'])) $isOK = true; if (preg_match('/Opera/i',$_SERVER['HTTP_USER_AGENT'])) $isOK = true;
  if (preg_match('/Chrome/i',$_SERVER['HTTP_USER_AGENT'])) $isOK = true;  if (preg_match('/Safari/i',$_SERVER['HTTP_USER_AGENT'])) $isOK = true;
  if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT'])) || (!empty($_SERVER['HTTP_ACCEPT']) && strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) $isOK = false;
  return $isOK;
}

//## NXS Cron
if (!function_exists("nxs_psCron")) { function nxs_psCron() { if (!is_home() && !is_front_page()) return; if (nxs_chckBrwsr()==false) return;    
   //if (stripos($_SERVER["REQUEST_URI"], 'admin-ajax.php')!==false || stripos($_SERVER["REQUEST_URI"], 'cf_action')!==false || stripos($_SERVER["REQUEST_URI"], 'wp-cron.php')!==false) return;   
   $ltc = get_option('NSX_LastTChecked'); if (empty($ltc)) { add_option("NSX_LastTChecked", time()); return; } if (time()<$ltc+300) return;  $sh =_get_cron_array();  $itmsToPush = array();   
   if (is_array($sh)) foreach ($sh as $evTime => $evDataX) if (is_array($evDataX)) foreach ($evDataX as $evFunc=>$evData) if (strpos($evFunc, 'ns_doPublishTo')!==false) { $chkTime = rand(360, 600); //$chkTime = rand(5, 7);
     if ($evTime>'1359495839' && $evTime<time()-$chkTime) $itmsToPush[] = array('time'=>$evTime);
   } if (count($itmsToPush)<1) return;  
   /*
   $snapIP = get_post_meta($toPush['args'][0], 'snap_mp_'.$toPush['func'], true); 
   nxs_addToLogN('S', 'Missed Scheduled Autoposts Found', '', ' - ('.$evTime."&lt;".(time()-$chkTime).') - Trying to Post', '');
   delete_post_meta(); add_post_meta($toPush['args'][0], 'snap_mp_'.$toPush['func'], (time()+300));
   */
   update_option("NSX_LastTChecked", time());
   $cron_url = site_url('wp-cron.php?doing_wp_cron=0'); ?><script type="text/javascript" > jQuery(document).ready(function($) { jQuery.get('<?php echo $cron_url; ?>'); }); </script><?php // die();
   return true;         
}}

if (!function_exists("nxs_addToRI")) { function nxs_addToRI($postID) { global $plgn_NS_SNAutoPoster;  if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options; if (empty($options['riHowManyPostsToTrack'])) $options['riHowManyPostsToTrack'] = 0;
  $riPosts = get_option('NS_SNriPosts'); if (!is_array($riPosts)) $riPosts = array();  $options['riHowManyPostsToTrack'] = (int) $options['riHowManyPostsToTrack'];  if ($options['riHowManyPostsToTrack']==0) return;
  array_unshift($riPosts, $postID);  $riPosts = array_unique($riPosts); $riPosts = array_slice($riPosts, 0, $options['riHowManyPostsToTrack']); update_option('NS_SNriPosts', $riPosts);
}}

if (!function_exists("nxs_activation")) { function nxs_activation(){ if (!wp_next_scheduled('nxs_hourly_event')){wp_schedule_event(time(), 'hourly', 'nxs_hourly_event');} if (!wp_next_scheduled('nxs_querypost_event')){wp_schedule_event(time(), 'nxsreposter', 'nxs_querypost_event');}  }}
function nxs_do_this_hourly() {  $options = get_option('NS_SNAutoPoster');   
  if (isset($options['errNotifEmailCB']) && (int)$options['errNotifEmailCB'] == 1 && isset($options['errNotifEmail']) && trim($options['errNotifEmail']) != '') { $logToSend = maybe_unserialize(get_option('NSX_LogToEmail')); // echo "SSS"; 
//  prr($logToSend);
    if (is_array($logToSend) && count($logToSend)>0) { $to = $options['errNotifEmail']; $subject = "SNAP Error Log for ".$_SERVER["SERVER_NAME"]; $message = print_r($logToSend, true);
      $eml = get_bloginfo('admin_email'); if (trim($eml)=='') $eml = "snap-notify@".str_ireplace('www.','',$_SERVER["SERVER_NAME"]); 
      $headers = "From: " . $eml . "\r\n"; $headers .= "Reply-To: ". $eml . "\r\n"; $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; $retval = wp_mail($to, $subject, $message, $headers); echo ($to ."|". $subject."|". $message."|". $headers);
      if ($retval == true) nxs_addToLogN( 'S', 'Log sent to email '.$options['errNotifEmail'], 'ALL', count($logToSend).' records sent', '');  
        else nxs_addToLogN( 'ER', '[FALIED] Log sent to email '.$options['errNotifEmail'], 'ALL', count($logToSend).' records were NOT sent', '');  
      delete_option("NSX_LogToEmail");  
  }}  
  $riPosts = get_option('NS_SNriPosts'); if (!is_array($riPosts)) $riPosts = array(); //## Check for Incoming Comments if nessesary.  
  if ( empty($options['riActive']) || $options['riActive'] != 1 || count($riPosts)<1 ) return;
  if (isset($options['extDebug']) && $options['extDebug']=='1') nxs_addToLogN( 'S', 'Comments Import', 'ALL', 'Checking for new comments now...', print_r($riPosts, true));
  //## Facebook
  if (is_array($options['fb'])) foreach ($options['fb'] as $ii=>$fbo) if (!empty($fbo['riComments']) && $fbo['riComments']=='1') {  $fbo['ii'] = $ii; $fbo['pType'] = 'aj';
    foreach ($riPosts as $postID) {  
      $fbpo =  get_post_meta($postID, 'snapFB', true); $fbpo =  maybe_unserialize($fbpo); 
      if (is_array($fbpo) && isset($fbpo[$ii]) && is_array($fbpo[$ii]) && isset($fbpo[$ii]['pgID']) && trim($fbpo[$ii]['pgID'])!=''){ 
          $ntClInst = new nxs_snapClassFB(); $fbo = $ntClInst->adjMetaOpt($fbo, $fbpo[$ii]);  $ntClInst->importComments($fbo, $postID, $fbpo[$ii]);
      }
    }      
  }   
  //## Twitter
  if (is_array($options['tw'])) foreach ($options['tw'] as $ii=>$fbo) if (!empty($fbo['riComments']) && $fbo['riComments']=='1') {  $fbo['ii'] = $ii; $fbo['pType'] = 'aj'; 
    foreach ($riPosts as $postID) {  
      $fbpo =  get_post_meta($postID, 'snapTW', true); $fbpo =  maybe_unserialize($fbpo); 
      if (is_array($fbpo) && isset($fbpo[$ii]) && is_array($fbpo[$ii])  && isset($fbpo[$ii]['pgID']) && trim($fbpo[$ii]['pgID'])!=''){ 
         $ntClInst = new nxs_snapClassTW(); $fbo = $ntClInst->adjMetaOpt($fbo, $fbpo[$ii]); $ntClInst->importComments($fbo, $postID, $fbpo[$ii]);
      }
    }      
  } 
}

//#### V3 new Query Poster
function nxs_addToPostingQuery($postID){ global $plgn_NS_SNAutoPoster; if (!isset($plgn_NS_SNAutoPoster)) return; $options = $plgn_NS_SNAutoPoster->nxs_options; 
  $quPosts = maybe_unserialize(get_option('NSX_PostsQuery')); if (!is_array($quPosts)) $quPosts = array();  
  if (!in_array($postID, $quPosts)) $quPosts[] = $postID; update_option('NSX_PostsQuery', $quPosts);    
  //## Update Next Post time
  $currTime = time() + 10 + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ); $nxTime = !empty($options['quNxTime'])?$options['quNxTime']:0;
  if (empty($nxTime) || $nxTime < $currTime) { $options['quNxTime'] = $currTime; update_option('NS_SNAutoPoster', $options);  $plgn_NS_SNAutoPoster->nxs_options = $options; }
}
function nxs_do_post_from_query() { nxs_cron_check(); // nxs_addToLogN('A', 'Debug info only. - Cron Time', 'X', '', $extInfo);      
  if (stripos($_SERVER["REQUEST_URI"], 'wp-cron.php')!==false) nxs_rePoster(); //## Run Reposter.
  $options = get_option('NS_SNAutoPoster'); $quPosts = maybe_unserialize(get_option('NSX_PostsQuery')); if (!is_array($quPosts)) $quPosts = array();
  if (empty($options['quLimit']) || $options['quLimit'] != '1' || count($quPosts)<1) return;  $currTime = time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );  $hasChanged = false; 
  $postToPost = array_shift($quPosts);   
  
  $pstEvrySec = $options['quDays']*86400+$options['quHrs']*3600+$options['quMins']*60; $rndSec = $options['quLimitRndMins']*60;  
  $nxTime = !empty($options['quNxTime'])?$options['quNxTime']:$currTime+$pstEvrySec; 
  
  $extInfo = 'Query Time:'.date_i18n('Y-m-d H:i:s', $options['quNxTime'])."|Previous Time:".date_i18n('Y-m-d H:i:s', $options['quLastShTime']);
  
  if (empty($options['quNxTime']) || $nxTime < $currTime) $hasChanged = true;  // Do Post  
       
  if ($hasChanged) { //## Do Post     
    nxs_addToLogN('A', '**POST STARTED** NXSPoster - WP CRON - Post from Query - Post ID: '.$postToPost, '', 'Curr Time: '.date_i18n('Y-m-d H:i:s', $currTime).'~', $extInfo);        
    $options['quLastShTime'] = $currTime; $rndTime = rand(0-$rndSec, $rndSec); $options['quNxTime'] = $currTime + $pstEvrySec + $rndTime;    
    if ($options['nxsOverLimit']=='D') { $dateC = date("d"); $dayN = date("d", $nxTime); if ($dayN!=$dateC) $quPosts = array(); }    
    update_option('NSX_PostsQuery', $quPosts);  update_option('NS_SNAutoPoster', $options);    
    nxs_snapPublishTo($postToPost, '', true);      
    nxs_addToLogN('A', '**POST FINISHED** NXSPoster - WP CRON - Post from Query - Post ID: '.$postToPost, '', '-=Time=- '.$currTime, $extInfo); 
  }
}

class NXS_HtmlFixer { public $dirtyhtml; public $fixedhtml; public $allowed_styles; private $matrix; public $debug; private $fixedhtmlDisplayCode;
    public function __construct() { $this->dirtyhtml = ""; $this->fixedhtml = ""; $this->debug = false; $this->fixedhtmlDisplayCode = ""; $this->allowed_styles = array();}
    public function getFixedHtml($dirtyhtml) { $c = 0; $this->dirtyhtml = $dirtyhtml; $this->fixedhtml = ""; $this->fixedhtmlDisplayCode = ""; if (is_array($this->matrix)) unset($this->matrix); $errorsFound=0;
      while ($c<10) { if ($c>0) $this->dirtyhtml = $this->fixedxhtml; $errorsFound = $this->charByCharJob(); if (!$errorsFound) $c=10;  $this->fixedxhtml=str_replace('<root>','',$this->fixedxhtml); 
        $this->fixedxhtml=str_replace('</root>','',$this->fixedxhtml); $this->fixedxhtml = $this->removeSpacesAndBadTags($this->fixedxhtml); $c++;
      } return $this->fixedxhtml;
    }
    private function fixStrToLower($m){ $right = strstr($m, '='); $left = str_replace($right,'',$m); return strtolower($left).$right;}
    private function fixQuotes($s){ $q = "\""; if (!stristr($s,"=")) return $s; $out = $s; preg_match_all("|=(.*)|",$s,$o,PREG_PATTERN_ORDER);
      for ($i = 0; $i< count ($o[1]); $i++) { $t = trim ( $o[1][$i] ) ; $lc=""; if ($t!="") { if ($t[strlen($t)-1]==">") { $lc= ($t[strlen($t)-2].$t[strlen($t)-1])=="/>"  ?  "/>"  :  ">" ; $t=substr($t,0,-1);}
        if (($t[0]!="\"")&&($t[0]!="'")) $out = str_replace( $t, "\"".$t,$out); else $q=$t[0]; if (($t[strlen($t)-1]!="\"")&&($t[strlen($t)-1]!="'")) $out = str_replace( $t.$lc, $t.$q.$lc,$out);
      }} return $out;
    }
    private function fixTag($t){  $t = preg_replace ( array( '/borderColor=([^ >])*/i', '/border=([^ >])*/i' ),  array('',''), $t);
        preg_match_all('/(?:"[^"]*"|\'[^\']*\'|[^"\'\s]+)+/', $t, $ar);  $ar = $ar[0];// prr($ar);
        $nt = ""; for ($i=0;$i<count($ar);$i++) { if (strpos($ar[$i], 'href=\\\\\\"')!==false) {$ar[$i] = str_replace('\\\\\\"','"',$ar[$i]);}
          if (strpos($ar[$i], 'href=\\"')!==false) {$ar[$i] = str_replace('\\"','"',$ar[$i]);} if (strpos($ar[$i], 'href=\"')!==false) {$ar[$i] = str_replace('\"','"',$ar[$i]);}
          $ar[$i]=$this->fixStrToLower($ar[$i]); if (stristr($ar[$i],"=")) $ar[$i] = $this->fixQuotes($ar[$i]); $nt.=$ar[$i]." ";   
        } $nt=preg_replace("/<( )*/i","<",$nt); $nt=preg_replace("/( )*>/i",">",$nt); return trim($nt);
    }
    private function extractChars($tag1,$tag2,$tutto) {  if (!stristr($tutto, $tag1)) return ''; $s=stristr($tutto,$tag1); $s=substr( $s,strlen($tag1)); if (!stristr($s,$tag2)) return '';
        $s1=stristr($s,$tag2); return substr($s,0,strlen($s)-strlen($s1));
    }
    private function mergeStyleAttributes($s) { $x = ""; $temp = ""; $c = 0;
        while(stristr($s,"style=\"")) {$temp = $this->extractChars("style=\"","\"",$s); if ($temp=="") { return preg_replace("/(\/)?>/i","\"\\1>",$s);}
            if ($c==0) $s = str_replace("style=\"".$temp."\"","##PUTITHERE##",$s); $s = str_replace("style=\"".$temp."\"","",$s); if (!preg_match("/;$/i",$temp)) $temp.=";"; $x.=$temp; $c++;
        }
        if (count($this->allowed_styles)>0) { $check=explode(';', $x); $x=""; foreach($check as $chk){ foreach($this->allowed_styles as $as) if(stripos($chk, $as) !== False) { $x.=$chk.';'; break; } }}
        if ($c>0) $s = str_replace("##PUTITHERE##","style=\"".$x."\"",$s);return $s;
    }
    private function fixAutoclosingTags($tag,$tipo=""){ if (in_array( $tipo, array ("img","input","br","hr")) ) { if (!stristr($tag,'/>')) $tag = str_replace('>','/>',$tag ); } return $tag; }
    private function getTypeOfTag($tag) { $tag = trim(preg_replace("/[\>\<\/]/i","",$tag)); $a = explode(" ",$tag); return $a[0];}
    private function checkTree() { $errorsCounter = 0; for ($i=1;$i<count($this->matrix);$i++) { $flag=false;
      if ($this->matrix[$i]["tagType"]=="div") { $parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]; if (in_array($parentType, array("p","b","i","font","u","small","strong","em"))) $flag=true; }
      if (in_array( $this->matrix[$i]["tagType"], array( "b", "strong" )) ) {  $parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]; if (in_array($parentType, array("b","strong"))) $flag=true; }
      if (in_array( $this->matrix[$i]["tagType"], array ( "i", "em") )) {  $parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]; if (in_array($parentType, array("i","em"))) $flag=true; }
      if ($this->matrix[$i]["tagType"]=="p") { $parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]; if (in_array($parentType, array("p","b","i","font","u","small","strong","em"))) $flag=true; }
      if ($this->matrix[$i]["tagType"]=="table") { $parentType = $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]; if (in_array($parentType, array("p","b","i","font","u","small","strong","em","tr","table"))) $flag=true; }
      if ($flag) { $errorsCounter++; if ($this->debug) echo "<div style='color:#ff0000'>Found a <b>".$this->matrix[$i]["tagType"]."</b> tag inside a <b>".htmlspecialchars($parentType)."</b> tag at node $i: MOVED</div>";                
        $swap = $this->matrix[$this->matrix[$i]["parentTag"]]["parentTag"]; if ($this->debug) echo "<div style='color:#ff0000'>Every node that has parent ".$this->matrix[$i]["parentTag"]." will have parent ".$swap."</div>";
        $this->matrix[$this->matrix[$i]["parentTag"]]["tag"]="<!-- T A G \"".$this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]."\" R E M O V E D -->"; $this->matrix[$this->matrix[$i]["parentTag"]]["tagType"]="";
        $hoSpostato=0;for ($j=count($this->matrix)-1;$j>=$i;$j--) { if ($this->matrix[$j]["parentTag"]==$this->matrix[$i]["parentTag"]) { $this->matrix[$j]["parentTag"] = $swap; $hoSpostato=1; }}
      }}return $errorsCounter;
    }
    private function findSonsOf($parentTag) { $out= "";
      for ($i=1;$i<count($this->matrix);$i++) { if ($this->matrix[$i]["parentTag"]==$parentTag) {
          if ($this->matrix[$i]["tag"]!="") { $out.=$this->matrix[$i]["pre"]; $out.=$this->matrix[$i]["tag"]; $out.=$this->matrix[$i]["post"]; } else { $out.=$this->matrix[$i]["pre"]; $out.=$this->matrix[$i]["post"];}
          if ($this->matrix[$i]["tag"]!="") { $out.=$this->findSonsOf($i); if ($this->matrix[$i]["tagType"]!="") { if (!in_array($this->matrix[$i]["tagType"], array ( "br","img","hr","input"))) $out.="</". $this->matrix[$i]["tagType"].">";}}
      }}return $out;
    }
    private function findSonsOfDisplayCode($parentTag) { $out= "";
        for ($i=1;$i<count($this->matrix);$i++) {
            if ($this->matrix[$i]["parentTag"]==$parentTag) { $out.= "<div style=\"padding-left:15\"><span style='float:left;background-color:#FFFF99;color:#000;'>{$i}:</span>";
                if ($this->matrix[$i]["tag"]!="") { if ($this->matrix[$i]["pre"]!="") $out.=htmlspecialchars($this->matrix[$i]["pre"])."<br>";
                    $out.="".htmlspecialchars($this->matrix[$i]["tag"])."<span style='background-color:red; color:white'>{$i} <em>".$this->matrix[$i]["tagType"]."</em></span>";
                    $out.=htmlspecialchars($this->matrix[$i]["post"]);
                } else { if ($this->matrix[$i]["pre"]!="") $out.=htmlspecialchars($this->matrix[$i]["pre"])."<br>"; $out.=htmlspecialchars($this->matrix[$i]["post"]);}
                if ($this->matrix[$i]["tag"]!="") { $out.="<div>".$this->findSonsOfDisplayCode($i)."</div>\n";
                    if ($this->matrix[$i]["tagType"]!="") {
                        if (($this->matrix[$i]["tagType"]!="br") && ($this->matrix[$i]["tagType"]!="img") && ($this->matrix[$i]["tagType"]!="hr")&& ($this->matrix[$i]["tagType"]!="input"))
                            $out.="<div style='color:red'>".htmlspecialchars("</". $this->matrix[$i]["tagType"].">")."{$i} <em>".$this->matrix[$i]["tagType"]."</em></div>";
                    }
                } $out.="</div>\n";
            }
        }return $out;
    }
    private function removeSpacesAndBadTags($s) { $i=0;
      while ($i<10) { $i++; $s = preg_replace (
        array( '/  /i', '/<p([^>])*>(&nbsp;)*\s*<\/p>/i', '/<span([^>])*>(&nbsp;)*\s*<\/span>/i', '/<strong([^>])*>(&nbsp;)*\s*<\/strong>/i', '/<em([^>])*>(&nbsp;)*\s*<\/em>/i',
          '/<font([^>])*>(&nbsp;)*\s*<\/font>/i', '/<small([^>])*>(&nbsp;)*\s*<\/small>/i', '/<\?xml:namespace([^>])*><\/\?xml:namespace>/i', '/<\?xml:namespace([^>])*\/>/i', '/class=\"MsoNormal\"/i',
          '/<o:p><\/o:p>/i', '/<!DOCTYPE([^>])*>/i', '/<!--(.|\s)*?-->/', '/<\?(.|\s)*?\?>/'), 
        array(' ', ' ', '', '', '', '', '', '', '', '', '', ' ', '', '' ) , trim($s));
      }return $s;
    }
    private function charByCharJob() { $s = $this->removeSpacesAndBadTags($this->dirtyhtml); if ($s=="") return; //echo "\r\n=!= ".$s." =!=\r\n<br/>\r\n";
        $s = "<root>".$s."</root>"; $contenuto = ""; $ns = ""; $i=0; $j=0; $ss=''; $indexparentTag=0; $padri=array(); array_push($padri,"0"); $this->matrix[$j]["tagType"]="";
        $this->matrix[$j]["tag"]=""; $this->matrix[$j]["parentTag"]="0"; $this->matrix[$j]["pre"]=""; $this->matrix[$j]["post"]=""; $tags=array();
        // echo "\r\n=#= ".$s." =#=\r\n<br/>\r\n";
        while($i<strlen($s)) {
            if ( $s[$i] =="<") { $contenuto = $ns; $ns = ""; $tag=""; while( $i<strlen($s) && $s[$i]!=">" ){ $tag.=$s[$i]; $i++;} $tag.=$s[$i]; if (stristr($tag,'<param') && stristr($tag,'/>')) $tag = str_replace('/>','></param>',$tag);
            $ss .= $tag;                 
        } else $ss .= $s[$i]; $i++; }
        $i=0; $s = $ss; //echo "\r\n== ".$s." ==\r\n<br/>\r\n";
        while($i<strlen($s)) {
            if ( $s[$i] =="<") { $contenuto = $ns; $ns = ""; $tag=""; while( $i<strlen($s) && $s[$i]!=">" ){ $tag.=$s[$i]; $i++;} $tag.=$s[$i];                
                if($s[$i]==">") { $tag = $this->fixTag($tag); $tagType = $this->getTypeOfTag($tag); $tag = $this->fixAutoclosingTags($tag,$tagType);
                    $tag = $this->mergeStyleAttributes($tag); if (!isset($tags[$tagType])) $tags[$tagType]=0; $tagok=true;
                    if (($tags[$tagType]==0)&&(stristr($tag,'/'.$tagType.'>'))&&(stristr($tag,'<'.$tagType)!==false)) { $tagok=false; if ($this->debug) echo "<div style='color:#ff0000'>Found a closing tag <b>".htmlspecialchars($tag)."</b> at char $i without open tag: REMOVED</div>";} else $tagok=true;
                }
                if ($tagok) { $j++; $this->matrix[$j]["pre"]=""; $this->matrix[$j]["post"]=""; $this->matrix[$j]["parentTag"]=""; $this->matrix[$j]["tag"]=""; $this->matrix[$j]["tagType"]="";
                    if (stristr($tag,'/'.$tagType.'>')) { $ind = array_pop($padri); $this->matrix[$j]["post"]=$contenuto; $this->matrix[$j]["parentTag"]=$ind; $tags[$tagType]--;
                    } else { if (@preg_match("/".$tagType."\/>$/i",$tag)||preg_match("/\/>/i",$tag)) { $this->matrix[$j]["tagType"]=$tagType; $this->matrix[$j]["tag"]=$tag;
                      $indexparentTag = array_pop($padri); array_push($padri,$indexparentTag); $this->matrix[$j]["parentTag"]=$indexparentTag; $this->matrix[$j]["pre"]=$contenuto; $this->matrix[$j]["post"]="";
                    } else { $tags[$tagType]++; $this->matrix[$j]["tagType"]=$tagType; $this->matrix[$j]["tag"]=$tag; $indexparentTag = array_pop($padri); array_push($padri,$indexparentTag);
                      array_push($padri,$j); $this->matrix[$j]["parentTag"]=$indexparentTag; $this->matrix[$j]["pre"]=$contenuto; $this->matrix[$j]["post"]=""; }
                    }
                }
            } else { $ns.=$s[$i]; } $i++;
        } for ($eli=$j+1;$eli<count($this->matrix);$eli++) { $this->matrix[$eli]["pre"]=""; $this->matrix[$eli]["post"]=""; $this->matrix[$eli]["parentTag"]=""; $this->matrix[$eli]["tag"]=""; $this->matrix[$eli]["tagType"]="";}
        $errorsCounter = $this->checkTree();  $this->fixedxhtml=$this->findSonsOf(0);return $errorsCounter;
    }
}

//## parse_str() implementation - max_vars fix.
if (!function_exists("NXS_doSetArrRecursive")) { function NXS_doSetArrRecursive(&$array, $path, $value){ $key = array_shift($path); //prr($path); prr($key); echo "|-"; prr($array); echo "-|";
  if (empty($path)) if (trim($key)=='')  $array[] = $value;  else  $array[$key] = $value; else { if (!isset($array[$key]) || !is_array($array[$key])) $array[$key] = array(); NXS_doSetArrRecursive($array[$key], $path, $value); }
}}
if (!function_exists("NXS_parseQueryStr")) { function NXS_parseQueryStr($url){ $tokens = explode("&", $url); $urlVars = array();
  foreach ($tokens as $token) { $token = urldecode($token); $value = NXS_parseEQStr($token, "=", "");
    if (preg_match('/^([^\[]*)(\[.*\])$/', $token, $matches)) { if (preg_match_all('/\[([^\]]*)\]/', $matches[2], $matches2)) $gg = $matches2[1]; array_unshift($gg, $matches[1]); NXS_doSetArrRecursive($urlVars, $gg, $value);} 
      else $urlVars[$token] = $value;
  } return $urlVars;
}}
if (!function_exists("NXS_parseEQStr")) { function NXS_parseEQStr(&$a, $delim='.', $default=false){ $n = strpos($a, $delim); if ($n === false) return $default; $result = substr($a, $n+strlen($delim)); $a = substr($a, 0, $n); return $result;}}


//## V4 Future

if (!function_exists("nxs_showNTFilters")) {  function nxs_showNTFilters($nt, $ii, $options){ if (empty($options['fltrs'])) $options['fltrs'] = ''; $isFOn = !empty($options['fltrsOn']) && (int)$options['fltrsOn'] == 1; ?> 

<div class="nxs_tls_cpt"><?php  _e('Filters', 'social-networks-auto-poster-facebook-twitter-g'); ?>&nbsp;&nbsp;<span class="nxsInstrSpan"><a href="http://www.nextscripts.com/snap-features/filters" target="_blank"><?php _e('[Instructions]', 'social-networks-auto-poster-facebook-twitter-g'); ?></a></span></div> <h3 style="padding-left: 15px;font-size: 16px;"> 
<input value="1" name="<?php echo $nt ?>[<?php echo $ii; ?>][fltrsOn]" type="checkbox" onchange="if (jQuery(this).is(':checked')) jQuery('#nxs_flrts<?php echo $nt.$ii; ?>').show(); else jQuery('#nxs_flrts<?php echo $nt.$ii; ?>').hide();" class="nxs_acctcb" <?php if ($isFOn) echo "checked"; ?> /> 
   <?php  _e('Filter Posts (Only posts that meet the following criteria will be autoposted)', 'social-networks-auto-poster-facebook-twitter-g'); ?> </h3><div id="nxs_flrts<?php echo $nt.$ii; ?>" style="margin-left: 30px;<?php if (!$isFOn) echo "display:none;"; ?>"> 
   <?php nxs_Filters::print_posts_metabox(0,$nt,$ii, $options['fltrs']);?> </div> <?php
}}
 
if (!function_exists("nxs_showImgSizeChoice")) { function nxs_showImgSizeChoice($nt, $ii, $currSel='full'){ ?><div class="nxs_tls_cpt"><?php  _e('Image size', 'social-networks-auto-poster-facebook-twitter-g'); ?> </div>
   <div id="nxs_flrts<?php echo $nt.$ii; ?>" style="margin-left: 16px;"> <?php  _e('What image size should be used if several sizes are avaiable', 'social-networks-auto-poster-facebook-twitter-g'); ?> <br/>
     <?php global $_wp_additional_image_sizes; $sizes = array(); foreach ( get_intermediate_image_sizes() as $_size ) {
        if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) { $sizes[ $_size ]['w']  = get_option( "{$_size}_size_w" ); $sizes[ $_size ]['h'] = get_option( "{$_size}_size_h" ); $sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );} 
          elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {$sizes[ $_size ] = array('w'  => $_wp_additional_image_sizes[ $_size ]['width'],'h' => $_wp_additional_image_sizes[ $_size ]['height'],'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],);}
       } $pgs = '<select name="'.$nt.'['.$ii.'][wpImgSize]"><option class="nxsBlue" '.($currSel=='full'?'selected="selected"':'').' value="full">Full (Originally Uploaded)</option>';
       foreach ($sizes as $szk=>$isz) { $pgs .= '<option class="nxsBlue" '.($currSel==$szk?'selected="selected"':'').' value="'.$szk.'">'.$szk.' ('.$isz['w'].'x'.$isz['h'].')</option>'; } $pgs .='</select>'; echo $pgs; ?>   
   </div> <?php
}}
if (!function_exists("nxs_addPostingDelaySelV4")) { function nxs_addPostingDelaySelV4($nt, $ii, $options) { if (!isset($options['nHrs'])) $options['nHrs'] = ''; if (!isset($options['nMin'])) $options['nMin'] = ''; if (!isset($options['nDays'])) $options['nDays'] = '';
    nxs_addPostingDelaySelV3($nt, $ii, $options['nHrs'], $options['nMin'], $options['nDays']); 
}}
?>