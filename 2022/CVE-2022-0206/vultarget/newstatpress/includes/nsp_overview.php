<?php

// Make sure plugin remains secure if called directly
if( !defined( 'ABSPATH' ) ) {
  if( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
  die(__('ERROR: This plugin requires WordPress and will not function if called directly.','newstatpress'));
}

/**
 * Generate overwiew meta-box-order
 *
 **********************************/

function nsp_generate_overview_agents(){
  global $wpdb;
  $table_name = nsp_TABLENAME;

  global $newstatpress_dir;
  $_newstatpress_url=nsp_PluginUrl();

  // determine the structure to use for URL
  $permalink_structure = get_option('permalink_structure');
  if ($permalink_structure=='') $extra="/?";
  else $extra="/";

  $querylimit=((get_option('newstatpress_el_overview')=='') ? 10:get_option('newstatpress_el_overview'));
  // use prepare
  $useragents = $wpdb->get_results($wpdb->prepare(
   "SELECT agent,os,browser,spider
    FROM $table_name
    GROUP BY agent,os,browser,spider
    ORDER BY id DESC LIMIT %d
  ", $querylimit));
  ?>
  <table class='widefat nsp'>
    <thead>
      <tr>
        <th scope='col'><?php _e('Agent',nsp_TEXTDOMAIN) ?> (<a id="hider" class='hider'>Hide Spiders</a>)</th>
        <th scope='col'></th><th style='width:140px;'><?php _e('OS',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'></th><th style='width:120px;'><?php _e('Browser',nsp_TEXTDOMAIN); echo '/'; _e('Spider',nsp_TEXTDOMAIN) ?></th>
      </tr>
    </thead>
    <tbody id='the-list'>
    <?php
      foreach ($useragents as $rk) {
        if($rk->spider!=NULL)
          print "<tr class='spiderhide' style=\"background-color: #f6f6f0;\"><td>".$rk->agent."</td>";
        else
          print "<tr><td>".$rk->agent."</td>";
        if($rk->os != '') {
          $val=nsp_GetOsImg($rk->os);
          $img=str_replace(" ","_",strtolower($val)).".png";
          print "<td class='right nospace-r'><img class='img_os' src='".$_newstatpress_url."images/os/$img'></td>";
        }
        else {
            print "<td></td>";
          }
        if($rk->os != '')
          print "<td class='left nospace-l'>". $rk->os . "</td>";
        else {
          print "<td>unknow</td>";
        }
        if($rk->browser != '') {
          $val=nsp_GetBrowserImg($rk->browser);
          $img=str_replace(" ","",strtolower($val)).".png";
          print "<td class='right nospace-r'><img class='img_browser' src='".$_newstatpress_url."images/browsers/$img'></td>";
        } else {
            print "<td></td>";
          }
        print "<td class='left nospace-l'>".$rk->browser." ".$rk->spider."</td></tr>\n";
      }
  ?>
  </tbody>
 </table>
 <?php
}

function nsp_generate_overview_lasthits() {

  global $wpdb;
  $table_name = nsp_TABLENAME;

  global $newstatpress_dir;
  $_newstatpress_url=nsp_PluginUrl();


  // determine the structure to use for URL
  $permalink_structure = get_option('permalink_structure');
  if ($permalink_structure=='') $extra="/?";
  else $extra="/";

  $querylimit=((get_option('newstatpress_el_overview')=='') ? 10:get_option('newstatpress_el_overview'));
  // use prepare
  $lasthits = $wpdb->get_results($wpdb->prepare(
   "SELECT *
    FROM $table_name
    WHERE (os<>'' OR feed<>'')
    ORDER bY id DESC LIMIT %d
  ",  $querylimit));
  ?>
      <table class='widefat nsp'>
        <thead>
          <tr>
            <th scope='col'><?php _e('Date',nsp_TEXTDOMAIN); ?></th>
            <th scope='col'><?php _e('Time',nsp_TEXTDOMAIN); ?></th>
            <th scope='col'><?php _e('IP',nsp_TEXTDOMAIN); ?></th>
            <th scope='col'><?php echo __('Country',nsp_TEXTDOMAIN).'/'.__('Language',nsp_TEXTDOMAIN); ?></th>
            <th scope='col'><?php _e('Page',nsp_TEXTDOMAIN); ?></th>
            <th scope='col'><?php _e('Feed',nsp_TEXTDOMAIN); ?></th>
            <th></th>
            <th scope='col' style='width:120px;'><?php _e('OS',nsp_TEXTDOMAIN); ?></th>
            <th></th>
            <th scope='col' style='width:120px;'><?php _e('Browser',nsp_TEXTDOMAIN); ?></th>
          </tr>
        </thead>
        <tbody id='the-list'>
        <?php
          foreach ($lasthits as $fivesdraft) {
            print "<tr>
                    <td>".nsp_hdate($fivesdraft->date)."</td>
                    <td>$fivesdraft->time</td>
                    <td>$fivesdraft->ip</td>
                    <td>$fivesdraft->nation</td>
                    <td>".nsp_Abbreviate(nsp_DecodeURL(filter_var($fivesdraft->urlrequested, FILTER_SANITIZE_URL)),30)."</td>
                    <td>$fivesdraft->feed</td>";

            if($fivesdraft->os != '') {
              $val=nsp_GetOsImg($fivesdraft->os);
              $img=$_newstatpress_url."images/os/".str_replace(" ","_",strtolower($val)).".png";
              print "<td class='right nospace-r'><img class='img_os' src='$img'></td>";
            }
            else
                print "<td></td>";

            print "<td class='left nospace-l'> $fivesdraft->os</td>";

            if($fivesdraft->browser != '') {
              $val=nsp_GetBrowserImg($fivesdraft->browser);
              $img=$_newstatpress_url."images/browsers/".str_replace(" ","_",strtolower($val)).".png";
              print "<td class='right nospace-r'><img class='img_browser' src='$img'></td>";
            }
            else {
               print "<td></td>";
            }
            print "<td class='left nospace-l'>".$fivesdraft->browser."</td></tr>\n";
          }
        ?>
        </tbody>
      </table>
  <?php
}

function nsp_generate_overview_lastsearchterms() {
  global $wpdb;
  $table_name = nsp_TABLENAME;

  global $newstatpress_dir;
  $_newstatpress_url=nsp_PluginUrl();


  // determine the structure to use for URL
  $permalink_structure = get_option('permalink_structure');
  if ($permalink_structure=='') $extra="/?";
  else $extra="/";

  $querylimit=((get_option('newstatpress_el_overview')=='') ? 10:get_option('newstatpress_el_overview'));
  // use prepare
  $lastsearchterms = $wpdb->get_results($wpdb->prepare(
   "SELECT date,time,referrer,urlrequested,search,searchengine
    FROM $table_name
    WHERE search<>''
    ORDER BY id DESC LIMIT %d
  ", $querylimit));
  ?>
  <table class='widefat nsp'>
    <thead>
      <tr>
        <th scope='col'><?php _e('Date',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('Time',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('Terms',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('Engine',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('Result',nsp_TEXTDOMAIN) ?></th>
      </tr>
    </thead>
    <tbody id='the-list'>
    <?php
      foreach ($lastsearchterms as $rk) {
        print "<tr>
                <td>".nsp_hdate($rk->date)."</td><td>".$rk->time."</td>
                <td><a href='".$rk->referrer."' target='_blank'>".$rk->search."</a></td>
                <td>".$rk->searchengine."</td><td><a href='".get_bloginfo('url').$extra.filter_var($rk->urlrequested, FILTER_SANITIZE_URL)."' target='_blank'>". __('page viewed',nsp_TEXTDOMAIN). "</a></td>
              </tr>\n";
      }
    ?>
    </tbody>
  </table>
  <?php
}

function nsp_generate_overview_lastreferrers() {
  global $wpdb;
  $table_name = nsp_TABLENAME;

  global $newstatpress_dir;
  $_newstatpress_url=nsp_PluginUrl();


  // determine the structure to use for URL
  $permalink_structure = get_option('permalink_structure');
  if ($permalink_structure=='') $extra="/?";
  else $extra="/";

  $querylimit=((get_option('newstatpress_el_overview')=='') ? 10:get_option('newstatpress_el_overview'));
  // use prepare
  $lastreferrers = $wpdb->get_results($wpdb->prepare(
   "SELECT date,time,referrer,urlrequested
    FROM $table_name
    WHERE
     ((referrer NOT LIKE '".get_option('home')."%') AND
      (referrer <>'') AND
      (searchengine='')
     ) ORDER BY id DESC LIMIT %d
  ", $querylimit));
  ?>
  <table class='widefat nsp'>
    <thead>
      <tr>
        <th scope='col'><?php _e('Date',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('Time',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('URL',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('Result',nsp_TEXTDOMAIN) ?></th>
      </tr>
    </thead>
    <tbody id='the-list'>
    <?php
      foreach ($lastreferrers as $rk) {
        print "<tr>
                <td>".nsp_hdate($rk->date)."</td>
                <td>$rk->time</td>
                <td ><a class='urlicon' href='".$rk->referrer."' target='_blank'>".nsp_Abbreviate($rk->referrer,80)."</a></td>
                <td><a href='".get_bloginfo('url').filter_var($extra.$rk->urlrequested, FILTER_SANITIZE_URL)."'  target='_blank'>". __('page viewed',nsp_TEXTDOMAIN). "</a></td>
              </tr>\n";
      }
    ?>
    </tbody>
  </table>
  <?php
}

function nsp_generate_overview_pages() {
  global $wpdb;
  $table_name = nsp_TABLENAME;

  global $newstatpress_dir;
  $_newstatpress_url=nsp_PluginUrl();


  // determine the structure to use for URL
  $permalink_structure = get_option('permalink_structure');
  if ($permalink_structure=='') $extra="/?";
  else $extra="/";

  $querylimit=((get_option('newstatpress_el_overview')=='') ? 10:get_option('newstatpress_el_overview'));
  // use prepare
  $pages = $wpdb->get_results($wpdb->prepare(
   "SELECT date,time,urlrequested,os,browser,spider
    FROM $table_name
    WHERE (spider='' AND feed='')
    ORDER BY id DESC LIMIT %d
  ", $querylimit));
  ?>
  <table class='widefat nsp'>
    <thead>
      <tr>
        <th scope='col'><?php _e('Date',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('Time',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('Page',nsp_TEXTDOMAIN) ?></th>
        <th scope='col' style='width:17px;'></th>
        <th scope='col' style='width:120px;'><?php _e('OS',nsp_TEXTDOMAIN) ?></th>
        <th style='width:17px;'></th>
        <th scope='col' style='width:120px;'><?php _e('Browser',nsp_TEXTDOMAIN) ?></th>
      </tr>
    </thead>
    <tbody id='the-list'>
    <?php
      foreach ($pages as $rk) {
        print "<tr><td>".nsp_hdate($rk->date)."</td><td>".$rk->time."</td>\n<td>".nsp_Abbreviate(nsp_DecodeURL(filter_var($rk->urlrequested, FILTER_SANITIZE_URL)),60)."</td>";
        if($rk->os != '') {
          $val=nsp_GetOsImg($rk->os);
          $img=str_replace(" ","_",strtolower($val)).".png";
          print "<td><img class='img_os' src='$_newstatpress_url"."images/os/$img'></td>";
        } else {
            print "<td></td>";
          }
        print "<td>". $rk->os . "</td>";
        if($rk->browser != '') {
          $val=nsp_GetBrowserImg($rk->browser);
          $img=str_replace(" ","",strtolower($val)).".png";
          print "<td><IMG class='img_browser' SRC='".$_newstatpress_url."images/browsers/$img'></td>";
        } else {
            print "<td></td>";
          }
        print "<td>".$rk->browser." ".$rk->spider."</td></tr>\n";
      }
    ?>
    </tbody>
  </table>
  <?php
}

function nsp_generate_overview_spiders() {
  global $wpdb;
  $table_name = nsp_TABLENAME;

  global $newstatpress_dir;
  $_newstatpress_url=nsp_PluginUrl();


  // determine the structure to use for URL
  $permalink_structure = get_option('permalink_structure');
  if ($permalink_structure=='') $extra="/?";
  else $extra="/";

  $querylimit=((get_option('newstatpress_el_overview')=='') ? 10:get_option('newstatpress_el_overview'));
  // use prepare
  $spiders = $wpdb->get_results($wpdb->prepare(
   "SELECT date,time,agent,os,browser,spider
    FROM $table_name
    WHERE (spider<>'')
    ORDER BY id DESC LIMIT %d
  ",  $querylimit));
  ?>
  <table class='widefat nsp'>
    <thead>
      <tr>
        <th scope='col'><?php _e('Date',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('Time',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('Terms',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('Engine',nsp_TEXTDOMAIN) ?></th>
        <th scope='col'><?php _e('Result',nsp_TEXTDOMAIN) ?></th>
      </tr>
    </thead>
    <tbody id='the-list'>
    <?php
    foreach ($spiders as $rk) {
      print "<tr>
                <td>".nsp_hdate($rk->date)."</td>
                <td>".$rk->time."</td>";
      if($rk->spider != '') {
        $img=str_replace(" ","_",strtolower($rk->spider)).".png";
        print "<td><IMG class='img_os' SRC='".$_newstatpress_url."/images/spider/$img'> </td>";
      }
      else
        print "<td></td>";
      print "<td>".$rk->spider."</td>
             <td> ".$rk->agent."</td></tr>\n";
    }
    ?>
    </tbody>
  </table>
  <?php
}




/**
 * Show overwiew
 *
 *****************/
 function nsp_NewStatPressMain() {

   ?>

   <div class="wrap">
     <h2><?php esc_html_e('Overview','newstatpress') ?></h2>

     <?php wp_nonce_field( 'some-action-nonce' );

     /* Used to save closed meta boxes and their order */
     wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
     wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

     <div id="poststuff">
       <div id="post-body" class="metabox-holder columns-1">
         <div id="post-body-content">
           <div class='wrap testnsp'>
             <div id="nsp_result-overview">
               <div class="loadAJAX">
                 <?php
                 $api_key=get_option('newstatpress_apikey');
                 $_newstatpress_url=nsp_PluginUrl();
                 $url=$_newstatpress_url."/includes/api/external.php";

                 $msg_activated=__('Loading... (Refresh page if no information is displayed).','newstatpress');
                 $msg_not_activated='<span class=\'bold\'>'.__('Impossible to load the overview:','newstatpress').'</span> '.__('You must activate the external api first (Page Option>Api)','newstatpress');

                 get_option('newstatpress_externalapi')=='checked' ? $message=$msg_activated:$message=$msg_not_activated;

                 wp_enqueue_script('wp_ajax_nsp_js_overview', plugins_url('./js/nsp_overview.js', __FILE__), array('jquery'));
                 wp_localize_script( 'wp_ajax_nsp_js_overview', 'nsp_externalAjax_overview', array(
                   'ajaxurl' => admin_url( 'admin-ajax.php' ),
                   'Key' => md5(gmdate('m-d-y H i').$api_key),
                   'postCommentNonce' => wp_create_nonce( 'newstatpress-nsp_external-nonce' )
                 ));

                 echo "<img id=\"nsp_error-overview\" class=\"imgerror\" src=\"$_newstatpress_url/images/error.png\">";
                 echo "<img id=\"nsp_loader-overview\" src=\"$_newstatpress_url/images/ajax-loader.gif\"> ".$message;
                 ?>
               </div>
             </div>
           </div> <!-- .wrap -->

           <div id="postbox-container-1" class="postbox-container">
               <?php do_meta_boxes('','normal',null); ?>
               <?php do_meta_boxes('','advanced',null); ?>
           </div>

         </div> <!-- #post-body content-->
        </div> <!-- #post-body -->
      </div> <!-- #poststuff -->
   </div> <!-- .wrap -->

   <?php

}
function nsp_NewStatPressMain3() {
  global $wpdb;
  $table_name = nsp_TABLENAME;

  global $newstatpress_dir;


    echo "<div class='wrap'><h2>". __('Overview','newstatpress'). "</h2>";

    $api_key=get_option('newstatpress_apikey');
    $_newstatpress_url=nsp_PluginUrl();
    $url=$_newstatpress_url."/includes/api/external.php";

    wp_enqueue_script('wp_ajax_nsp_js_overview', plugins_url('./js/nsp_overview.js', __FILE__), array('jquery'));
    wp_localize_script( 'wp_ajax_nsp_js_overview', 'nsp_externalAjax_overview', array(
                   'ajaxurl' => admin_url( 'admin-ajax.php' ),
                   'Key' => md5(gmdate('m-d-y H i').$api_key),
                   'postCommentNonce' => wp_create_nonce( 'newstatpress-nsp_external-nonce' )
                ));
    echo "<div id=\"nsp_result-overview\"><img id=\"nsp_loader-overview\" src=\"$_newstatpress_url/images/ajax-loader.gif\"></div>";


 /// nsp_MakeOverview('main');
  $_newstatpress_url=nsp_PluginUrl();


  // determine the structure to use for URL
  $permalink_structure = get_option('permalink_structure');
  if ($permalink_structure=='') $extra="/?";
  else $extra="/";

  $querylimit=((get_option('newstatpress_el_overview')=='') ? 10:get_option('newstatpress_el_overview'));
  // use prepare
  $lasthits = $wpdb->get_results($wpdb->prepare(
   "SELECT *
    FROM $table_name
    WHERE (os<>'' OR feed<>'')
    ORDER bY id DESC LIMIT %d
  ", $querylimit));
  
  // use prepare
  $lastsearchterms = $wpdb->get_results($wpdb->prepare(
   "SELECT date,time,referrer,urlrequested,search,searchengine
    FROM $table_name
    WHERE search<>''
    ORDER BY id DESC LIMIT %d
  ", $querylimit));

  // use prepare
  $lastreferrers = $wpdb->get_results($wpdb->prepare(
   "SELECT date,time,referrer,urlrequested
    FROM $table_name
    WHERE
     ((referrer NOT LIKE '".get_option('home')."%') AND
      (referrer <>'') AND
      (searchengine='')
     ) ORDER BY id DESC LIMIT %d
  ", $querylimit));

  // use prepare
  $useragents = $wpdb->get_results($wpdb->prepare(
   "SELECT agent,os,browser,spider
    FROM $table_name
    GROUP BY agent,os,browser,spider
    ORDER BY id DESC LIMIT %d
  ", $querylimit));

  // use prepare
  $pages = $wpdb->get_results($wpdb->prepare(
   "SELECT date,time,urlrequested,os,browser,spider
    FROM $table_name
    WHERE (spider='' AND feed='')
    ORDER BY id DESC LIMIT %d
  ", $querylimit));
  
  // use prepare
  $spiders = $wpdb->get_results($wpdb->prepare(
   "SELECT date,time,agent,os,browser,spider
    FROM $table_name
    WHERE (spider<>'')
    ORDER BY id DESC LIMIT %d
  ", $querylimit));
  ?>

  <!-- Last hits table -->
  <div class='wrap'>
    <h2> <?php echo  __('Last hits',nsp_TEXTDOMAIN); ?></h2>
    <table class='widefat nsp'>
      <thead>
        <tr>
          <th scope='col'><?php _e('Date',nsp_TEXTDOMAIN); ?></th>
          <th scope='col'><?php _e('Time',nsp_TEXTDOMAIN); ?></th>
          <th scope='col'><?php _e('IP',nsp_TEXTDOMAIN); ?></th>
          <th scope='col'><?php echo __('Country',nsp_TEXTDOMAIN).'/'.__('Language',nsp_TEXTDOMAIN); ?></th>
          <th scope='col'><?php _e('Page',nsp_TEXTDOMAIN); ?></th>
          <th scope='col'><?php _e('Feed',nsp_TEXTDOMAIN); ?></th>
          <th></th>
          <th scope='col' style='width:120px;'><?php _e('OS',nsp_TEXTDOMAIN); ?></th>
          <th></th>
          <th scope='col' style='width:120px;'><?php _e('Browser',nsp_TEXTDOMAIN); ?></th>
        </tr>
      </thead>
      <tbody id='the-list'>
      <?php
        foreach ($lasthits as $fivesdraft) {
          print "<tr>";
          print "<td>". nsp_hdate($fivesdraft->date) ."</td>";
          print "<td>". $fivesdraft->time ."</td>";
          print "<td>". $fivesdraft->ip ."</td>";
          print "<td>". $fivesdraft->nation ."</td>";
          print "<td>". nsp_Abbreviate(nsp_DecodeURL(filter_var($fivesdraft->urlrequested, FILTER_SANITIZE_URL)),30) ."</td>";
          print "<td>". $fivesdraft->feed . "</td>";

          if($fivesdraft->os != '') {
            $val=nsp_GetBrowserImg($fivesdraft->os);
            $img=$_newstatpress_url."/images/os/".str_replace(" ","_",strtolower($val)).".png";
            print "<td class='browser'><img class='img_browser' SRC='$img'></td>";
          }
          else {
              print "<td></td>";
          }
          print "<td>".$fivesdraft->os . "</td>";

          if($fivesdraft->browser != '') {
            $img=str_replace(" ","",strtolower($fivesdraft->browser)).".png";
            print "<td><img class='img_browser' SRC='".$_newstatpress_url."/images/browsers/$img'></td>";
          }
          else {
             print "<td></td>";
          }
          print "<td>".$fivesdraft->browser."</td></tr>\n";
        }
      ?>
      </tbody>
    </table>
  </div>

  <!-- Last Search terms table -->
  <div class='wrap'>
    <h2><?php _e('Last search terms',nsp_TEXTDOMAIN) ?></h2>
    <table class='widefat nsp'>
      <thead>
        <tr>
          <th scope='col'><?php _e('Date',nsp_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Time',nsp_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Terms',nsp_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Engine',nsp_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Result',nsp_TEXTDOMAIN) ?></th>
        </tr>
      </thead>
      <tbody id='the-list'>
      <?php
        foreach ($lastsearchterms as $rk) {
          print "<tr>
                  <td>".nsp_hdate($rk->date)."</td><td>".$rk->time."</td>
                  <td><a href='".$rk->referrer."' target='_blank'>".$rk->search."</a></td>
                  <td>".$rk->searchengine."</td><td><a href='".get_bloginfo('url').$extra.filter_var($rk->urlrequested, FILTER_SANITIZE_URL)."' target='_blank'>". __('page viewed',nsp_TEXTDOMAIN). "</a></td>
                </tr>\n";
        }
      ?>
      </tbody>
    </table>
  </div>

  <!-- Last Referrers table -->
  <div class='wrap'>
    <h2><?php _e('Last referrers',nsp_TEXTDOMAIN) ?></h2>
    <table class='widefat nsp'>
      <thead>
        <tr>
          <th scope='col'><?php _e('Date',nsp_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Time',nsp_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('URL',nsp_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Result',nsp_TEXTDOMAIN) ?></th>
        </tr>
      </thead>
      <tbody id='the-list'>
      <?php
        foreach ($lastreferrers as $rk) {
          print "<tr><td>".nsp_hdate($rk->date)."</td><td>".$rk->time."</td><td><a href='".$rk->referrer."' target='_blank'>".nsp_Abbreviate($rk->referrer,80)."</a></td><td><a href='".get_bloginfo('url').$extra.filter_var($rk->urlrequested, FILTER_SANITIZE_URL)."'  target='_blank'>". __('page viewed',nsp_TEXTDOMAIN). "</a></td></tr>\n";
        }
      ?>
      </tbody>
    </table>
  </div>

  <!-- Last Agents -->
  <div class='wrap'>
    <h2><?php _e('Last agents',nsp_TEXTDOMAIN) ?></h2>
    <table class='widefat nsp'>
      <caption><?php _e('Last agents',nsp_TEXTDOMAIN) ?></caption>
      <thead>
        <tr>
          <th scope='col'><?php _e('Agent',nsp_TEXTDOMAIN) ?></th>
          <th scope='col'></th><th scope='col' style='width:120px;'><?php _e('OS',nsp_TEXTDOMAIN) ?></th>
          <th scope='col'></th>
          <th scope='col' style='width:120px;'> <?php _e('Browser',nsp_TEXTDOMAIN); echo '/'; _e('Spider',nsp_TEXTDOMAIN) ?><button id="hider">Hide</button><button id="shower">Show</button></th>
        </tr>
      </thead>
      <tbody id='the-list'>
      <?php
        foreach ($useragents as $rk) {
          if($rk->spider!=NULL)
            print "<tr class='spiderhide' style=\"background-color: #f6f6f0;\"><td>".$rk->agent."</td>";
          else
            print "<tr><td>".$rk->agent."</td>";
          if($rk->os != '') {
            $val=nsp_GetOsImg($rk->os);

            $img=str_replace(" ","_",strtolower($val)).".png";
            print "<td><IMG class='img_browser' SRC='".$_newstatpress_url."images/os/$img'> </td>";
          }
          else {
              print "<td></td>";
            }
          if($rk->os != '')
            print "<td>". $rk->os . "</td>";
          else {
            print "<td>unknow</td>";
          }
          if($rk->browser != '') {
            $val=nsp_GetBrowserImg($rk->browser);
            $img=str_replace(" ","",strtolower($val)).".png";
            print "<td><IMG class='img_browser' SRC='".$_newstatpress_url."images/browsers/$img'></td>";
          } else {
              print "<td></td>";
            }
          print "<td>".$rk->browser." ".$rk->spider."</td></tr>\n";
        }
      ?>
      </tbody>
    </table>
  </div>

  <!-- Last Pages -->
  <div class='wrap'>
    <h2><?php _e('Last pages',nsp_TEXTDOMAIN) ?></h2>
    <table class='widefat nsp'>
      <thead>
        <tr>
          <th scope='col'><?php _e('Date',nsp_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Time',nsp_TEXTDOMAIN) ?></th>
          <th scope='col'><?php _e('Page',nsp_TEXTDOMAIN) ?></th>
          <th scope='col' style='width:17px;'></th>
          <th scope='col' style='width:120px;'><?php _e('OS',nsp_TEXTDOMAIN) ?></th>
          <th style='width:17px;'></th>
          <th scope='col' style='width:120px;'><?php _e('Browser',nsp_TEXTDOMAIN) ?></th>
        </tr>
      </thead>
      <tbody id='the-list'>
      <?php
        foreach ($pages as $rk) {
          print "<tr><td>".nsp_hdate($rk->date)."</td><td>".$rk->time."</td>\n<td>".nsp_Abbreviate(nsp_DecodeURL(filter_var($rk->urlrequested, FILTER_SANITIZE_URL)),60)."</td>";
          if($rk->os != '') {
            $img=str_replace(" ","_",strtolower($rk->os)).".png";
            print "<td><IMG class='img_browser' SRC='".$_newstatpress_url."/images/os/$img'> </td>";
          } else {
              print "<td></td>";
            }
          print "<td>". $rk->os . "</td>";
          if($rk->browser != '') {
            $img=str_replace(" ","",strtolower($rk->browser)).".png";
            print "<td><IMG class='img_browser' SRC='".$_newstatpress_url."/images/browsers/$img'></td>";
          } else {
              print "<td></td>";
            }
          print "<td>".$rk->browser." ".$rk->spider."</td></tr>\n";
        }
      ?>
      </tbody>
    </table>
  </div>


  <?php
  # Last Spiders
  print "<div class='wrap'><h2>".__('Last spiders',nsp_TEXTDOMAIN)."</h2><table class='widefat nsp'><thead><tr><th scope='col'>".__('Date',nsp_TEXTDOMAIN)."</th><th scope='col'>".__('Time',nsp_TEXTDOMAIN)."</th><th scope='col'></th><th scope='col'>".__('Spider',nsp_TEXTDOMAIN)."</th><th scope='col'>".__('Agent',nsp_TEXTDOMAIN)."</th></tr></thead>";
  print "<tbody id='the-list'>";

  foreach ($spiders as $rk) {
    print "<tr><td>".nsp_hdate($rk->date)."</td><td>".$rk->time."</td>";
    if($rk->spider != '') {
      $img=str_replace(" ","_",strtolower($rk->spider)).".png";
      print "<td><IMG class='img_os' SRC='".$_newstatpress_url."/images/spider/$img'> </td>";
    } else print "<td></td>";
    print "<td>".$rk->spider."</td><td> ".$rk->agent."</td></tr>\n";
  }
  print "</table></div>";

  print "<br />";
  print "&nbsp;<i>StatPress table size: <b>".nsp_TableSize(nsp_TABLENAME)."</b></i><br />";
  print "&nbsp;<i>StatPress current time: <b>".current_time('mysql')."</b></i><br />";
  print "&nbsp;<i>RSS2 url: <b>".get_bloginfo('rss2_url').' ('.nsp_ExtractFeedFromUrl(get_bloginfo('rss2_url')).")</b></i><br />";
  nsp_load_time();
}

/**
 * Abbreviate the given string to a fixed length
 *
 * @param s the string
 * @param c the number of chars
 * @return the abbreviate string
 ***********************************************/
function nsp_Abbreviate($s,$c) {
  $s=__($s);
  $res=""; if(strlen($s)>$c) { $res="..."; }
  return substr($s,0,$c).$res;
}


?>
