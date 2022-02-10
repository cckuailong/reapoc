<?php

// Make sure plugin remains secure if called directly
if( !defined( 'ABSPATH' ) ) {
  if( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
  die(__('ERROR: This plugin requires WordPress and will not function if called directly.','newstatpress'));
}

/**
 * Visits Page to finish
 */
function nsp_DisplayVisitsPage() {
  // global $wpdb;
  // global $newstatpress_dir;
  //
  // $table_name = nsp_TABLENAME;

  global $pagenow;
  $VisitsPage_tabs = array( 'lastvisitors' => __('Last visitors','newstatpress'),
                            'visitors' => __('Visitors','newstatpress'),
                            'spybot' => __('Spy Bot','newstatpress')
                          );
  $page='nsp_visits';

  print "<div class='wrap'><h2>".__('Visits','newstatpress')."</h2>";


  if ( isset ( $_GET['tab'] ) ) nsp_DisplayTabsNavbarForMenuPage($VisitsPage_tabs,$_GET['tab'],$page);
  else nsp_DisplayTabsNavbarForMenuPage($VisitsPage_tabs, 'lastvisitors',$page);

  if ( $pagenow == 'admin.php' && $_GET['page'] == $page ) {

    if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab'];
    else $tab = 'lastvisitors';

    switch ($tab) {

      case 'lastvisitors' :
      nsp_Spy();
      break;

      case 'visitors' :
      nsp_NewSpy();
      break;

      case 'spybot' :
      nsp_SpyBot();
      break;
    }
  }
}

/**
 * Get page period taken in statpress-visitors
 */
function newstatpress_page_periode() {
  // pp is the display page periode
  if(isset($_GET['pp'])) {
    // Get Current page periode from URL
    $periode = $_GET['pp'];
    if($periode <= 0)
      // Periode is less than 0 then set it to 1
      $periode = 1;
  } else
      // URL does not show the page set it to 1
      $periode = 1;
  return $periode;
}

/**
 * Get page post taken in statpress-visitors
 *
 * @return page
 ******************************************/
function newstatpress_page_posts() {
  global $wpdb;
  // pa is the display pages Articles
  if(isset($_GET['pa'])) {
    // Get Current page Articles from URL
    $pageA = $_GET['pa'];
    if($pageA <= 0)
      // Article is less than 0 then set it to 1
      $pageA = 1;
  } else
      // URL does not show the Article set it to 1
      $pageA = 1;
  return $pageA;
}


/**
 * New spy bot function taken in statpress-visitors
 */
function nsp_SpyBot() {
  global $wpdb;
  global $newstatpress_dir;

  $action="spybot";
  $table_name = nsp_TABLENAME;

  $LIMIT = get_option('newstatpress_bot_per_page_spybot');
  $LIMIT_PROOF = get_option('newstatpress_visits_per_bot_spybot');

  if ($LIMIT ==0) $LIMIT = 10;
  if ($LIMIT_PROOF == 0) $LIMIT_PROOF = 30;

  $pa = newstatpress_page_posts();
  $LimitValue = ($pa * $LIMIT) - $LIMIT;

  // limit the search 7 days ago
  $day_ago = gmdate('Ymd', current_time('timestamp') - 7*86400);
  // use prepare  
  $MinId = $wpdb->get_var($wpdb->prepare(
   "SELECT min(id) as MinId
    FROM $table_name
    WHERE date > %s
   ", $day_ago));

  // Number of distinct spiders after $day_ago
  // use prepare    
  $Num = $wpdb->get_var($wpdb->prepare(
   "SELECT count(distinct spider)
    FROM $table_name
    WHERE
      spider<>'' AND
      id > %d
   ", $MinId));
  $NA = ceil($Num/$LIMIT);

  // echo "<div class='wrap'><h2>" . __('Spy Bot', 'newstatpress') . "</h2>";
  echo "<br />";

  // selection of spider, group by spider, order by most recently visit (last id in the table)
  // use prepare
  $sql = $wpdb->prepare(
   "SELECT *
    FROM $table_name as T1
    JOIN
    (SELECT spider,max(id) as MaxId
     FROM $table_name
     WHERE spider<>''
     GROUP BY spider
     ORDER BY MaxId
     DESC LIMIT %d, %d
    ) as T2
    ON T1.spider = T2.spider
    WHERE T1.id > %d
    ORDER BY MaxId DESC, id DESC
  ", $LimitValue, $LIMIT, $MinId);
  $qry = $wpdb->get_results($sql);

  echo '<div align="center">';
  newstatpress_print_pp_pa_link (0,0,$action,$NA,$pa);
  echo '</div><div align="left">';
?>
<script>
function ttogle(thediv){
if (document.getElementById(thediv).style.display=="inline") {
document.getElementById(thediv).style.display="none"
} else {document.getElementById(thediv).style.display="inline"}
}
</script>
<table id="mainspytab" name="mainspytab" width="99%" border="0" cellspacing="0" cellpadding="4"><div align='left'>
<?php
  $spider="robot";
  $num_row=0;
  $robot="";
  foreach ($qry as $rk) {  // Bot Spy
    if ($robot <> $rk->spider) {
      echo "<div align='left'>
            <tr>
            <td colspan='2' bgcolor='#dedede'>";
      $img=str_replace(" ","_",strtolower($rk->spider));
      $img=str_replace('.','',$img).".png";
      $lines = file($newstatpress_dir.'/def/spider.dat');
      foreach($lines as $line_num => $spider) { //seeks the tooltip corresponding to the photo
        list($title,$id)=explode("|",$spider);
        if($title==$rk->spider) break; // break, the tooltip ($title) is found
      }
      echo "<IMG class='img_os' style='align:left;' alt='".$title."' title='".$title."' SRC='" .plugins_url('newstatpress/images/spider/'.$img, nsp_BASENAME). "'>
            <span style='color:#006dca;cursor:pointer;border-bottom:1px dotted #AFD5F9;font-size:8pt;' onClick=ttogle('" . $img . "');>http more info</span>
            <div id='" . $img . "' name='" . $img . "'><br /><small>" . $rk->ip . "</small><br><small>" . $rk->agent . "<br /></small></div>
            <script>document.getElementById('" . $img . "').style.display='none';</script>
            </tr>
            <tr><td valign='top' width='170'><div><font size='1' color='#3B3B3B'><strong>" . newstatpress_hdate($rk->date) . " " . $rk->time . "</strong></font></div></td>
            <td><div>" . newstatpress_Decode($rk->urlrequested) . "</div></td></tr>";
      $robot=$rk->spider;
      $num_row=1;
    } elseif ($num_row < $LIMIT_PROOF) {
        echo "<tr>
              <td valign='top' width='170'><div><font size='1' color='#3B3B3B'><strong>" . newstatpress_hdate($rk->date) . " " . $rk->time . "</strong></font></div></td>
              <td><div>" . newstatpress_Decode($rk->urlrequested) . "</div></td></tr>";
        $num_row+=1;
      }
      echo "</div></td></tr>\n";
  }
  echo "</table>";
  newstatpress_print_pp_pa_link (0,0,$action,$NA,$pa);
  echo "</div>";
}


/**
 * Newstatpress spy function
 */
function nsp_Spy() {
  global $wpdb;
  global $newstatpress_dir;

  $table_name = nsp_TABLENAME;

  # Spy
  $today = gmdate('Ymd', current_time('timestamp'));
  $yesterday = gmdate('Ymd', current_time('timestamp')-86400);
  // print "<div class='wrap'><h2>".__('Last visitors','newstatpress')."</h2>";
  echo "<br />";
  // use prepare
  $sql=$wpdb->prepare(
   "SELECT ip,nation,os,browser,agent
    FROM $table_name
    WHERE
      spider='' AND
      feed='' AND
      date BETWEEN %s AND %s
    GROUP BY ip ORDER BY id DESC LIMIT 20
   ", $yesterday, $today);
  $qry = $wpdb->get_results($sql);

?>
<script>
function ttogle(thediv){
if (document.getElementById(thediv).style.display=="inline") {
document.getElementById(thediv).style.display="none"
} else {document.getElementById(thediv).style.display="inline"}
}
</script>
<div>
<table id="mainspytab" name="mainspytab" width="99%" border="0" cellspacing="0" cellpadding="4">
<?php
  foreach ($qry as $rk) {
    print "<tr><td colspan='2' bgcolor='#dedede'><div align='left'>";

    if($rk->nation <> '') {
      // the nation exist
      $img=strtolower($rk->nation).".png";
      $lines = file($newstatpress_dir.'/def/domain.dat');
      foreach($lines as $line_num => $nation) {
        list($title,$id)=explode("|",$nation);
        if($id===$rk->nation) break;
      }
      echo "<IMG style='border:0px;height:16px;' alt='".$title."' title='".$title."' SRC='" .plugins_url('newstatpress/images/domain/'.$img, nsp_BASENAME). "'>  ";
    } else {
        $ch = curl_init('http://api.hostip.info/country.php?ip='.$rk->ip);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $output .=".png";
        $output = strtolower($output);
        curl_close($ch);
        echo "<IMG style='border:0px;width:18;height:12px;' alt='".$title."' title='".$title."' SRC='" .plugins_url('newstatpress/images/domain/'.$output, nsp_BASENAME). "'>  ";
      }


    print "<strong><span><font size='2' color='#7b7b7b'>".$rk->ip."</font></span></strong> ";
    print "<span class='visits-details' onClick=ttogle('".$rk->ip."');>".__('more info','newstatpress')."</span></div>";
    print "<div id='".$rk->ip."' name='".$rk->ip."'>";
    if(get_option('newstatpress_cryptip')!='checked') {
      print "<br><iframe class='visit-iframe' scrolling='no' marginwidth=0 marginheight=0 src=http://api.hostip.info/get_html.php?ip=".$rk->ip."></iframe>";
    }
    print "<br><small><span>OS or device:</span> ".$rk->os."</small>";
    if(filter_var($rk->ip, FILTER_VALIDATE_IP)) {
      print "<br><small><span>DNS Name:</span> ".gethostbyaddr($rk->ip)."</small>";
    }
    print "<br><small><span>Browser:</span> ".$rk->browser."</small>";
    print "<br><small><span>Browser Detail:</span> ".$rk->agent."</small>";
    print "<br><br></div>";
    print "<script>document.getElementById('".$rk->ip."').style.display='none';</script>";
    print "</td></tr>";
    // use prepare
    $qry2=$wpdb->get_results($wpdb->prepare(
     "SELECT *
      FROM $table_name
      WHERE
        ip= %s AND
        (date BETWEEN %s AND %s)
      ORDER BY id
      LIMIT 10
     ", $rk->ip, $yesterday, $today));
    foreach ($qry2 as $details) {
      print "<tr>";
      print "<td valign='top' width='151'><div><font size='1' color='#3B3B3B'><strong>".nsp_hdate($details->date)." ".$details->time."</strong></font></div></td>";
      print "<td><div><a href='".get_bloginfo('url')."/?".filter_var($details->urlrequested, FILTER_SANITIZE_URL)."' target='_blank'>".nsp_DecodeURL($details->urlrequested)."</a>";
      
      $details->referrer= filter_var($details->referrer, FILTER_SANITIZE_URL);
      
      if($details->searchengine != '') {
        print "<br><small>".__('arrived from','newstatpress')." <b>".$details->searchengine."</b> ".__('searching','newstatpress')." <a href='".$details->referrer."' target='_blank'>".$details->search."</a></small>";
      } elseif($details->referrer != '' && strpos($details->referrer,get_option('home'))===FALSE) {
          print "<br><small>".__('arrived from','newstatpress')." <a href='".$details->referrer."' target='_blank'>".$details->referrer."</a></small>";
        }
      print "</div></td>";
      print "</tr>\n";
    }
  }
?>
</table>
</div>
<?php
}

/**
 * New spy function taken in statpress-visitors
 */
function nsp_NewSpy() {
  global $wpdb;
  global $newstatpress_dir;
  $action="newspy";
  $table_name = nsp_TABLENAME;

  // number of IP or bot by page
  $LIMIT = get_option('newstatpress_ip_per_page_newspy');
  $LIMIT_PROOF = get_option('newstatpress_visits_per_ip_newspy');
  if ($LIMIT == 0) $LIMIT = 20;
  if ($LIMIT_PROOF == 0) $LIMIT_PROOF = 20;

  $pp = newstatpress_page_periode();

  // Number of distinct ip (unique visitors)
  // no need prepare
  $NumIP = $wpdb->get_var("
    SELECT count(distinct ip)
    FROM $table_name
    WHERE spider=''"
  );
  $NP = ceil($NumIP/$LIMIT);
  $LimitValue = ($pp * $LIMIT) - $LIMIT;

  // use prepare
  $sql = $wpdb->prepare(
   "SELECT *
    FROM $table_name as T1
    JOIN
      (SELECT max(id) as MaxId,min(id) as MinId,ip, nation
       FROM $table_name
       WHERE spider=''
       GROUP BY ip
       ORDER BY MaxId
       DESC LIMIT %i, %i ) as T2
    ON T1.ip = T2.ip
    WHERE id BETWEEN MinId AND MaxId
    ORDER BY MaxId DESC, id DESC
  ", $LimitValue, $LIMIT);

  $qry = $wpdb->get_results($sql);

  // echo "<div class='wrap'><h2>" . __('Visitors', 'newstatpress') . "</h2>";
?>
<script>
function ttogle(thediv){
if (document.getElementById(thediv).style.display=="inline") {
document.getElementById(thediv).style.display="none"
} else {document.getElementById(thediv).style.display="inline"}
}
</script>
<?php
  $ip = 0;
  $num_row=0;
  echo "<div id='paginating' align='center' class='pagination'>";
  newstatpress_print_pp_link($NP,$pp,$action);
  echo'</div><table id="mainspytab" name="mainspytab" width="99%" border="0" cellspacing="0" cellpadding="4">';
  foreach ($qry as $rk) {
    // Visitors
    if ($ip <> $rk->ip) {
      //this is the first time these ip appear, print informations
      echo "<tr><td colspan='2' bgcolor='#dedede'><div align='left'>";

      $title='';
      $id ='';
      ///if ($rk->country <> '') {
      ///  $img=strtolower($rk->country).".png";
      ///  $lines = file(ABSPATH.'wp-content/plugins/'.dirname(dirname(nsp_BASENAME)) .'/def/domain.dat');
      ///  foreach($lines as $line_num => $country) {
      ///    list($id,$title)=explode("|",$country);
      ///    if($id===strtolower($rk->country)) break;
      ///  }
      ///  echo "http country <IMG class='img_os' alt='".$title."' title='".$title."' SRC='" .plugins_url('newstatpress/images/domain/'.$img, dirname(dirname(dirname(__FILE__)))). "'>  ";
      ///} else
        if($rk->nation <> '') {
          // the nation exist
          $img=strtolower($rk->nation).".png";
          // echo plugins_url("newstatpress/images/domain/$img", nsp_BASENAME);

          $lines = file($newstatpress_dir.'/def/domain.dat');
          foreach($lines as $line_num => $nation) {
            list($title,$id)=explode("|",$nation);
            if($id===$rk->nation) break;
          }
          print "".__('Http domain', 'newstatpress')." <IMG class='img_os' alt='".$title."' title='".$title."' SRC='" .plugins_url('newstatpress/images/domain/'.$img, nsp_BASENAME). "'>  ";

        } else {
            $ch = curl_init('http://api.hostip.info/country.php?ip='.$rk->ip);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            $output .=".png";
            $output = strtolower($output);
            curl_close($ch);
            print "".__('Hostip country','newstatpress'). "<IMG style='border:0px;width:18;height:12px;' alt='".$title."' title='".$title."' SRC='" .plugins_url('newstatpress/images/domain/'.$output, nsp_BASENAME). "'>  ";
      }

        print "<strong><span><font size='2' color='#7b7b7b'>".$rk->ip."</font></span></strong> ";
        print "<span style='color:#006dca;cursor:pointer;border-bottom:1px dotted #AFD5F9;font-size:8pt;' onClick=ttogle('".$rk->ip."');>".__('more info','newstatpress')."</span></div>";
        print "<div id='".$rk->ip."' name='".$rk->ip."'>";

        if(get_option('newstatpress_cryptip')!='checked') {
          print "<br><iframe style='overflow:hidden;border:0px;width:100%;height:60px;font-family:helvetica;padding:0;' scrolling='no' marginwidth=0 marginheight=0 src=http://api.hostip.info/get_html.php?ip=".$rk->ip."></iframe>";
        }
        print "<br><small><span style='font-weight:700;'>OS or device:</span> ".$rk->os."</small>";
        print "<br><small><span style='font-weight:700;'>DNS Name:</span> ".gethostbyaddr($rk->ip)."</small>";
        print "<br><small><span style='font-weight:700;'>Browser:</span> ".$rk->browser."</small>";
        print "<br><small><span style='font-weight:700;'>Browser Detail:</span> ".$rk->agent."</small>";
        print "<br><br></div>";
        print "<script>document.getElementById('".$rk->ip."').style.display='none';</script>";
        print "</td></tr>";

        
        // sanitize if present javascript in DB
        $rk->referrer = filter_var($rk->referrer, FILTER_SANITIZE_URL);

        echo "<td valign='top' width='151'><div><font size='1' color='#3B3B3B'><strong>" . newstatpress_hdate($rk->date) . " " . $rk->time . "</strong></font></div></td>
              <td>" . newstatpress_Decode($rk->urlrequested) ."";
        if ($rk->searchengine != '') print "<br><small>".__('arrived from','newstatpress')." <b>" . $rk->searchengine . "</b> ".__('searching','newstatpress')." <a href='" . $rk->referrer . "' target=_blank>" . urldecode($rk->search) . "</a></small>";
        elseif ($rk->referrer != '' && strpos($rk->referrer, get_option('home')) === false) print "<br><small>".__('arrived from','newstatpress')." <a href='" . $rk->referrer . "' target=_blank>" . $rk->referrer . "</a></small>";
        echo "</div></td></tr>\n";
        $ip=$rk->ip;
        $num_row = 1;
    } elseif ($num_row < $LIMIT_PROOF) {
    
        // sanitize if present javascript in DB
        $rk->referrer = filter_var($rk->referrer, FILTER_SANITIZE_URL);
    
        echo "<tr><td valign='top' width='151'><div><font size='1' color='#3B3B3B'><strong>" . newstatpress_hdate($rk->date) . " " . $rk->time . "</strong></font></div></td>
              <td><div>" . newstatpress_Decode($rk->urlrequested) . "";
        if ($rk->searchengine != '') print "<br><small>".__('arrived from','newstatpress')." <b>" . $rk->searchengine . "</b> ".__('searching','newstatpress')." <a href='" . $rk->referrer . "' target=_blank>" . urldecode($rk->search) . "</a></small>";
        elseif ($rk->referrer != '' && strpos($rk->referrer, get_option('home')) === false) print "<br><small>".__('arrived from','newstatpress')." <a href='" . $rk->referrer . "' target=_blank>" . $rk->referrer . "</a></small>";
        $num_row += 1;
        echo "</div></td></tr>\n";
      }
   }
   echo "</div></td></tr>\n</table>";
   echo "<div id='paginating' align='center' class='pagination'>";
   newstatpress_print_pp_link($NP,$pp,$action);
   echo "</div></div>";
}

/**
 * Get true if permalink is enabled in Wordpress
 * (taken in statpress-visitors)
 *
 * @return true if permalink is enabled in Wordpress
 ***************************************************/
function nsp_PermalinksEnabled() {
  global $wpdb;

  // no needs prepare
  $result = $wpdb->get_row('SELECT `option_value` FROM `' . $wpdb->prefix . 'options` WHERE `option_name` = "permalink_structure"');
  if ($result->option_value != '') return true;
  else return false;
}


/**
 * Decode the url in a better manner
 *
 * @param out_url
 * @return url decoded
 ************************************/
function newstatpress_Decode($out_url) {
  $out_url=filter_var($out_url, FILTER_SANITIZE_URL);

  if(!nsp_PermalinksEnabled()) {
    if ($out_url == '') $out_url = __('Page', nsp_TEXTDOMAIN) . ": Home";
    if (nsp_MySubstr($out_url, 0, 4) == "cat=") $out_url = __('Category', nsp_TEXTDOMAIN) . ": " . get_cat_name(nsp_MySubstr($out_url, 4));
    if (nsp_MySubstr($out_url, 0, 2) == "m=") $out_url = __('Calendar', nsp_TEXTDOMAIN) . ": " . nsp_MySubstr($out_url, 6, 2) . "/" . nsp_MySubstr($out_url, 2, 4);
    if (nsp_MySubstr($out_url, 0, 2) == "s=") $out_url = __('Search', nsp_TEXTDOMAIN) . ": " . nsp_MySubstr($out_url, 2);
    if (nsp_MySubstr($out_url, 0, 2) == "p=") {
      $subOut=nsp_MySubstr($out_url, 2);
      $post_id_7 = get_post($subOut, ARRAY_A);
      $out_url = $post_id_7['post_title'];
    }
    if (nsp_MySubstr($out_url, 0, 8) == "page_id=") {
      $subOut=nsp_MySubstr($out_url, 8);
      $post_id_7 = get_page($subOut, ARRAY_A);
      $out_url = __('Page', nsp_TEXTDOMAIN) . ": " . $post_id_7['post_title'];
    }
 } else {
     if ($out_url == '') $out_url = __('Page', nsp_TEXTDOMAIN) . ": Home";
     else if (nsp_MySubstr($out_url, 0, 9) == "category/") $out_url = __('Category', nsp_TEXTDOMAIN) . ": " . get_cat_name(nsp_MySubstr($out_url, 9));
          else if (nsp_MySubstr($out_url, 0, 2) == "s=") $out_url = __('Search', nsp_TEXTDOMAIN) . ": " . nsp_MySubstr($out_url, 2);
               else if (nsp_MySubstr($out_url, 0, 2) == "p=") {
                      // not working yet
                      $subOut=nsp_MySubstr($out_url, 2);
                      $post_id_7 = get_post($subOut, ARRAY_A);
                      $out_url = $post_id_7['post_title'];
                    } else if (nsp_MySubstr($out_url, 0, 8) == "page_id=") {
                             // not working yet
                             $subOut=nsp_MySubstr($out_url, 8);
                             $post_id_7 = get_page($subOut, ARRAY_A);
                             $out_url = __('Page', nsp_TEXTDOMAIN) . ": " . $post_id_7['post_title'];
                           }
   }
   return $out_url;
}

/**
 * Display links for group of pages
 *
 * @param NP the group of pages
 * @param pp the page to show
 * @param action the action
 *
 * TODO change print into return $result
 */
function newstatpress_print_pp_link($NP,$pp,$action) {
  // For all pages ($NP) Display first 3 pages, 3 pages before current page($pp), 3 pages after current page , each 25 pages and the 3 last pages for($action)
  $GUIL1 = FALSE;
  $GUIL2 = FALSE;// suspension points  not writed  style='border:0px;width:16px;height:16px;   style="border:0px;width:16px;height:16px;"
  if ($NP >1) {
    // print "<font size='1'>".__('period of days','newstatpress')." : </font>";
    for ($i = 1; $i <= $NP; $i++) {
      if ($i <= $NP) {
        // $page is not the last page
        if($i == $pp) echo " <span class='current'>{$i} </span> "; // $page is current page
        else {
          // Not the current page Hyperlink them
          if (($i <= 3) or (($i >= $pp-3) and ($i <= $pp+3)) or ($i >= $NP-3) or is_int($i/100)) {
            echo '<a href="?page=nsp_visits&tab=visitors&newstatpress_action='.$action.'&pp=' . $i .'">' . $i . '</a> ';
          } else {

              if (($GUIL1 == FALSE) OR ($i==$pp+4)) {
                echo "...";
                $GUIL1 = TRUE;
              }
              if ($i == $pp-4) echo "..";
              if (is_int(($i-1)/100)) echo ".";
              if ($i == $NP-4) echo "..";
              // suspension points writed

         }
      }
    }
  }
}
}
/**
 * Display links for group of pages
 *
 * @param NP the group of pages
 * @param pp the page to show
 * @param action the action
 * @param NA group
 * @param pa current page
 *
 * TODO change print into return $result
 */
function newstatpress_print_pp_pa_link($NP,$pp,$action,$NA,$pa) {
  if ($NP<>0) newstatpress_print_pp_link($NP,$pp,$action);

  // For all pages ($NP) display first 5 pages, 3 pages before current page($pa), 3 pages after current page , 3 last pages
  $GUIL1 = FALSE;// suspension points not writed
  $GUIL2 = FALSE;

  echo '<table width="100%" border="0"><tr></tr></table>';
  if ($NA >1 ) {
    echo "<font size='1'>".__('Pages','newstatpress')." : </font>";
    for ($j = 1; $j <= $NA; $j++) {
      if ($j <= $NA) {  // $i is not the last Articles page
        if($j == $pa)  // $i is current page
          echo " [{$j}] ";
        else { // Not the current page Hyperlink them
          if (($j <= 5) or (( $j>=$pa-2) and ($j <= $pa+2)) or ($j >= $NA-2))
            echo '<a href="?page=newstatpress/newstatpress.php&newstatpress_action='.$action.'&pp=' . $pp . '&pa='. $j . '">' . $j . '</a> ';
          else {
            if ($GUIL1 == FALSE) echo "... "; $GUIL1 = TRUE;
            if (($j == $pa+4) and ($GUIL2 == FALSE)) {
              echo " ... ";
              $GUIL2 = TRUE;
            }
            // suspension points writed
          }
        }
      }
    }
  }
}


?>
