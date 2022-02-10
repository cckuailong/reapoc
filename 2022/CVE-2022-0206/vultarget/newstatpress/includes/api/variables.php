<?php

// Make sure plugin remains secure if called directly
if( !defined( 'ABSPATH' ) ) {
  if( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
  die(__('ERROR: This plugin requires WordPress and will not function if called directly.','newstatpress'));
}

/**
 * Ajax routine for getting variables values
 */
function nsp_variablesAjax() {
  global $wpdb;
  global $nsp_option_vars;
  $table_name = $wpdb->prefix . "statpress";

  // response output
  header( "Content-Type: application/json" );
  
  $nonce = $_POST['postCommentNonce'];
 
  // check to see if the submitted nonce matches with the
  // generated nonce we created earlier
  if (!wp_verify_nonce($nonce, 'newstatpress-nsp_variables-nonce')) {
    die ( 'Busted!');
  }
  
  // get the submitted parameters
  $var = $_POST['VAR'];

  $offsets = get_option($nsp_option_vars['stats_offsets']['name']);

  // test all vars
  if ($var=='alltotalvisits') {
    // no need prepare
    $qry = $wpdb->get_results(
    "SELECT count(distinct urlrequested, ip) AS pageview
     FROM $table_name AS t1
     WHERE
      spider='' AND
      feed='' AND
      urlrequested!='';
     ");
     if ($qry != null) {
       echo json_encode($qry[0]->pageview+$offsets['alltotalvisits']);
     }
  } elseif ($var=='visits') {
      // no need prepare
      $qry = $wpdb->get_results(
        "SELECT count(DISTINCT(ip)) AS pageview
         FROM $table_name
         WHERE
         date = '".gmdate("Ymd",current_time('timestamp'))."' AND
          spider='' and feed='';
        ");
     if ($qry != null) {
       echo json_encode($qry[0]->pageview);
     }
  } elseif ($var=='yvisits') {
      // no need prepare
      $qry = $wpdb->get_results(
        "SELECT count(DISTINCT(ip)) AS pageview
         FROM $table_name
         WHERE
          date = '".gmdate("Ymd",current_time('timestamp')-86400)."' AND
          spider='' and feed='';
        ");
     if ($qry != null) {
       echo json_encode($qry[0]->pageview);
     }
  } elseif ($var=='mvisits') {
      if (get_option($nsp_option_vars['calculation']['name'])=='sum') {
        // no need prepare
        $qry = $wpdb->get_results(
          "SELECT SUM(pagv) AS pageview FROM (
            SELECT count(DISTINCT(ip)) AS pagv
            FROM $table_name
            WHERE
             DATE >= DATE_FORMAT(CURDATE(), '%Y%m01') AND
             spider='' and feed=''
            GROUP BY DATE
           ) AS pageview;
        ");
      } else { 
          // no need prepare
          $qry = $wpdb->get_results(
            "SELECT count(DISTINCT(ip)) AS pageview
             FROM $table_name
             WHERE
              DATE >= DATE_FORMAT(CURDATE(), '%Y%m01') AND
              spider='' and feed='';
          ");
        }
      if ($qry != null) {
        echo json_encode($qry[0]->pageview);
      }
  } elseif ($var=='wvisits') {
      if (get_option($nsp_option_vars['calculation']['name'])=='sum') {
        // no need prepare
        $qry = $wpdb->get_results(
          "SELECT SUM(pagv) AS pageview FROM (
            SELECT count(DISTINCT(ip)) AS pagv
            FROM $table_name
            WHERE
             YEARWEEK (date) = YEARWEEK( CURDATE()) AND
             spider='' and feed=''
            GROUP BY DATE
           ) AS pageview;
            ");
     } else {
         // no need prepare
         $qry = $wpdb->get_results(
           "SELECT count(DISTINCT(ip)) AS pageview
            FROM $table_name
            WHERE
              YEARWEEK (date) = YEARWEEK( CURDATE()) AND
              spider='' and feed='';
           ");
       }
     if ($qry != null) {
       echo json_encode($qry[0]->pageview);
     }
  } elseif ($var=='totalvisits') {
      if (get_option($nsp_option_vars['calculation']['name'])=='sum') {
        // no need prepare
        $qry = $wpdb->get_results(
          "SELECT SUM(pagv) AS pageview FROM (
            SELECT count(DISTINCT(ip)) AS pagv
            FROM `avwp_statpress`
            WHERE
             spider='' AND
             feed=''
            GROUP BY DATE
           ) AS pageview;
            ");
      } else {
        // no need prepare
        $qry = $wpdb->get_results(
          "SELECT count(DISTINCT(ip)) AS pageview
           FROM $table_name
           WHERE
             spider='' AND
             feed='';
          ");
        }
     if ($qry != null) {
       echo json_encode($qry[0]->pageview);
     }
  } elseif ($var=='totalpageviews') {
      // no need prepare
      $qry = $wpdb->get_results(
        "SELECT count(id) AS pageview
         FROM $table_name
         WHERE
           spider='' AND
           feed='';
        ");
     if ($qry != null) {
       echo json_encode($qry[0]->pageview+$offsets['pageviews']);
     }
  } elseif ($var=='todaytotalpageviews') {
      // no need prepare
      $qry = $wpdb->get_results(
        "SELECT count(id) AS pageview
         FROM $table_name
         WHERE
           date = '".gmdate("Ymd",current_time('timestamp'))."' AND
           spider='' AND
           feed='';
        ");
     if ($qry != null) {
       echo json_encode($qry[0]->pageview);
     }
  } elseif ($var=='thistotalvisits') {
      //$url = esc_url($_REQUEST["URL"]);
      $url=$_REQUEST["URL"];    // sanitize in prepare
      
      // use prepare
      $qry = $wpdb->get_results( $wpdb->prepare(
        "SELECT count(DISTINCT(ip)) AS pageview
         FROM $table_name
         WHERE
           spider='' AND
           feed='' AND
           urlrequested=%s';
        ", $url));
     if ($qry != null) {
       echo json_encode($qry[0]->pageview);
     }
  } elseif ($var=='monthtotalpageviews') {
      // no need prepare
      $qry = $wpdb->get_results(
        "SELECT count(id) AS pageview
         FROM $table_name
         WHERE
          DATE >= DATE_FORMAT(CURDATE(), '%Y%m01') AND
          spider='' and feed='';
        "); 
     if ($qry != null) {
       echo json_encode($qry[0]->pageview);
     }
  } elseif ($var=='widget_topposts') {
      $limit = intval($_REQUEST["LIMIT"]);
      $showcounts = $_REQUEST["FLAG"];

      $res="\n<ul>\n";
      // use prepare
      $qry = $wpdb->get_results($wpdb->prepare(
        "SELECT urlrequested,count(*) as totale
         FROM $table_name
         WHERE
           spider='' AND
           feed='' AND
           urlrequested LIKE '%p=%'
         GROUP BY urlrequested
         ORDER BY totale DESC LIMIT %d;
        ", $limit));
     foreach ($qry as $rk) {
       $res.="<li><a href='?".$rk->urlrequested."' target='_blank'>".nsp_DecodeURL($rk->urlrequested)."</a></li>\n";
       if(strtolower($showcounts) == 'checked') { $res.=" (".$rk->totale.")"; }
     }
     echo json_encode("$res</ul>\n");
  }
  
  wp_die();
}
?>
