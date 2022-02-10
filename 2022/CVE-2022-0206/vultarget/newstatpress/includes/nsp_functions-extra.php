<?php

// Make sure plugin remains secure if called directly
if( !defined( 'ABSPATH' ) ) {
  if( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
  die(__('ERROR: This plugin requires WordPress and will not function if called directly.','newstatpress'));
}

/**
 * Get valide user IP even behind proxy or load balancer (Could be fake)
 * added by cHab
 *
 * @return $user_IP
 */
function nsp_GetUserIP() {
  $user_IP = "";
  $ip_pattern = '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/';
	$http_headers = array('HTTP_X_REAL_IP',
                        'HTTP_X_CLIENT',
                        'HTTP_X_FORWARDED_FOR',
                        'HTTP_CLIENT_IP',
                        'REMOTE_ADDR'
                      );

  foreach($http_headers as $header) {
    if ( isset($_SERVER[$header]) ) {
      if (function_exists('filter_var') && filter_var($_SERVER[$header], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE) !== false ) {
          $user_IP = $_SERVER[$header];
          break;
      }
      else { // for php version < 5.2.0
        if(preg_match($ip_pattern,$_SERVER[$header])) {
          $user_IP = $_SERVER[$header];
          break;
        }
      }
    }
  }

	return $user_IP;
}

function nsp_ConnexionIsSSL() {
	if( !empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) { return TRUE; }
	if( !empty( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) { return TRUE; }
	if( !empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) { return TRUE; }
	if( !empty( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && 'off' !== $_SERVER['HTTP_X_FORWARDED_SSL'] ) { return TRUE; }
	return FALSE;
}
//---------------------------------------------------------------------------
// CRON Functions
//---------------------------------------------------------------------------

/**
 * Add Cron intervals : 4 times/day, Once/week, Once/mounth
 * added by cHab
 *
 * @param $schedules
 * @return $schedules
 */
function nsp_CronIntervals($schedules) {
  $schedules['fourlybyday'] = array(
   'interval' => 21600, // seconds
   'display' => __('Four time by Day','newstatpress')
  );
  $schedules['weekly'] = array(
   'interval' => 604800,
   'display' => __('Once a Week','newstatpress')
  );
  $schedules['monthly'] = array(
   'interval' => 2635200,
   'display' => __('Once a Month','newstatpress')
  );
  return $schedules;
}
add_filter( 'cron_schedules', 'nsp_CronIntervals');





//---------------------------------------------------------------------------
// NOTICE Functions
//---------------------------------------------------------------------------
function nsp_CalculateEpochOffsetTime( $t1, $t2, $output_unit ) { //to complete with more output_unit
  $offset_time_in_seconds = abs($t1-$t2);

  if($output_unit=='day')
    $offset_time=$offset_time_in_seconds/86400;
  if($output_unit=='hour')
      $offset_time=$offset_time_in_seconds/3600;
  else {
    $offset_time=$offset_time_in_seconds;
  }

  return $offset_time;
}

function nsp_GetDaysInstalled() {
  global $nsp_option_vars;
  $name=$nsp_option_vars['settings']['name'];
  $settings=get_option($name);
	$install_date	= empty( $settings['install_date'] ) ? time() : $settings['install_date'];
	$num_days_inst	= nsp_CalculateEpochOffsetTime($install_date, time(), 'day');
  if( $num_days_inst < 1 )
    $num_days_inst = 1;

	return $num_days_inst;
}

//---------------------------------------------------------------------------
// URL Functions
//---------------------------------------------------------------------------

/**
 * Extract the feed from the given url
 *
 * @param the url to parse
 * @return the extracted url
 *************************************/
function nsp_ExtractFeedFromUrl($url) {
  // list($null,$q)=explode("?",$url); /*old def before PHP7, to delete if no pb.*/
  list($null,$q)=array_pad(explode("?",$url,2), 2, null);

  if (strpos($q, "&")!== false)
    list($res,$null)=explode("&",$q);
  else
    $res=$q;

  return $res;
}

function nsp_GetUrl() {
	$url  = nsp_ConnexionIsSSL() ? 'https://' : 'http://';
  //$url = 'http://';
	$url .= nsp_SERVER_NAME.$_SERVER['REQUEST_URI'];
	return $url;
}

/**
* Fix poorly formed URLs so as not to throw errors or cause problems
*
* @return $url
*/
function nsp_FixUrl( $url, $rem_frag = FALSE, $rem_query = FALSE, $rev = FALSE ) {
	$url = trim( $url );
	/* Too many forward slashes or colons after http */
	$url = preg_replace( "~^(https?)\:+/+~i", "$1://", $url );
	/* Too many dots */
	$url = preg_replace( "~\.+~i", ".", $url );
	/* Too many slashes after the domain */
	$url = preg_replace( "~([a-z0-9]+)/+([a-z0-9]+)~i", "$1/$2", $url );
	/* Remove fragments */
	if( !empty( $rem_frag ) && strpos( $url, '#' ) !== FALSE ) { $url_arr = explode( '#', $url ); $url = $url_arr[0]; }
	/* Remove query string completely */
	if( !empty( $rem_query ) && strpos( $url, '?' ) !== FALSE ) { $url_arr = explode( '?', $url ); $url = $url_arr[0]; }
	/* Reverse */
	if( !empty( $rev ) ) { $url = strrev($url); }
	return $url;
}

/***
* Get query string array from URL
***/
function nsp_GetQueryArgs( $url ) {
	if( empty( $url ) ) { return array(); }
	$query_str = nsp_GetQueryString( $url );
	parse_str( $query_str, $args );
	return $args;
}

function nsp_GetQueryString( $url ) {
	/***
	* Get query string from URL
	* Filter URLs with nothing after http
	***/
	if( empty( $url ) || preg_match( "~^https?\:*/*$~i", $url ) ) { return ''; }
	/* Fix poorly formed URLs so as not to throw errors when parsing */
	$url = nsp_FixUrl( $url );
	/* NOW start parsing */
	$parsed = @parse_url($url);
	/* Filter URLs with no query string */
	if( empty( $parsed['query'] ) ) { return ''; }
	$query_str = $parsed['query'];
	return $query_str;
}

function nsp_AdminNagNotices() {
	global $current_user;
	$nag_notices = get_user_meta( $current_user->ID, 'newstatpress_nag_notices', TRUE );
	if( !empty( $nag_notices ) ) {
		$nid			= $nag_notices['nid'];
		$style		= $nag_notices['style']; /* 'error'  or 'updated' */
		$timenow	= time();
		$url			= nsp_GetUrl();
		$query_args		= nsp_GetQueryArgs( $url );
		$query_str		= '?' . http_build_query( array_merge( $query_args, array( 'newstatpress_hide_nag' => '1', 'nid' => $nid ) ) );
		$query_str_con	= 'QUERYSTRING';
		$notice			= str_replace( array( $query_str_con ), array( $query_str ), $nag_notices['notice'] );
		// echo '<div class="'.$style.'"><p>'.$notice.'</p></div>';

    global $pagenow;
    $page_nsp=0;
    
    if (isset($_GET['page'])) {
      switch ($_GET['page']) {
        case 'nsp-main':
                          $page_nsp=1;
                          break;
        case 'nsp_details':
                          $page_nsp=1;
                          break;
        case 'nsp_visits':
                          $page_nsp=1;
                          break;
        case 'nsp_search':
                          $page_nsp=1;
                          break;
        case 'nsp_tools':
                          $page_nsp=1;
                          break;
        case 'nsp_options':
                          $page_nsp=1;
                          break;
        case 'nsp_credits':
                          $page_nsp=1;
                          break;

        default:
                          $page_nsp=0;
                          break;
      }
    }

    //Display NSP box if user are in plugins page or nsp plugins
    if ( ($nid!="n03" && $page_nsp==1) || ($nid=="n03" && $pagenow=="plugins.php") ) {
    ?>
      <div id="nspnotice" class="<?php echo $style; ?>" style="padding:10px">
        <?php
          if ($nid=="n03") {
            echo "<a id=\"close\" class=\"close\" href=\"$query_str\" target=\"_self\" rel=\"external\"><span class=\"dashicons dashicons-no\"></span>close</a>";
            echo '<h4>'.__('NewStatPress News','newstatpress').'</h4>';
          }
          echo $notice
        ?>
      </div>
    <?php
    }
	}
}

function nsp_CheckNagNotices() {
	global $current_user;
	$status	= get_user_meta( $current_user->ID, 'newstatpress_nag_status', TRUE );
	if( !empty( $status['currentnag'] ) ) { add_action( 'admin_notices', 'nsp_AdminNagNotices' ); return; }
	if( !is_array( $status ) ) { $status = array(); update_user_meta( $current_user->ID, 'newstatpress_nag_status', $status ); }
	$timenow		= time();
	$num_days_inst	= nsp_GetDaysInstalled();
  $votedate=14;
  $donatedate=90;
	$query_str_con	= 'QUERYSTRING';
  //$num_days_inst=95; //debug
	/* Notices (Positive Nags) */
  if( empty( $status['news'] ) ) {
    $nid = 'n03';
    $style = 'updated ';
    $notice_text='<p>'.__('Since the introduction of new method for speed up the display (needed for dashboard and overview), you must activate the external API in Options>Api page.','newstatpress').'</p>';
    $notice_text.= __('This major change was necessary in aim to improve the plugin for users with big database. In addition, it will allow to be more flexible about the graphical informations in next versions.', 'newstatpress' ).'</p>';
    $status['currentnag'] = TRUE;
    $status['news'] = FALSE;
  }


	if( empty( $status['currentnag'] ) && ( empty( $status['lastnag'] ) || $status['lastnag'] <= $timenow - 1209600 ) ) {
		if( empty( $status['vote'] ) && $num_days_inst >= $votedate ) {
			$nid = 'n01';
      $style = 'notice';

      $notice_text = '<p>'. __( 'It looks like you\'ve been using NewStatPress for a while now. That\'s great!', 'newstatpress' ).'</p>';
      $notice_text.= '<p>'. __( 'If you find this plugin useful, would you take a moment to give it a rating on WordPress.org?', 'newstatpress' );
      $notice_text.='<br /><i> ('.__( 'NB: please open a ticket on the support page instead of adding it to your rating commentaries if you wish to report an issue with the plugin, it will be processed more quickly by the team.', 'newstatpress' ).')</i></p>';
      $notice_text.= '<a class=\"button button-primary\" href=\"'.nsp_RATING_URL.'\" target=\"_blank\" rel=\"external\">'. __( 'Yes, I\'d like to rate it!', 'newstatpress' ) .'</a>';
      $notice_text.= ' &nbsp; ';
      $notice_text.= '<a class=\"button button-default\" href=\"'.$query_str_con.'\" target=\"_self\" rel=\"external\">'. __( 'I already did!', 'newstatpress' ) .'</a>';

      $status['currentnag'] = TRUE;
      $status['vote'] = FALSE;
		}
		elseif( empty( $status['donate'] ) && $num_days_inst >= $donatedate ) {
			$nid = 'n02';
      $style = 'notice';

      $notice_text = '<p>'. __( 'You\'ve been using NewStatPress for several months now. We hope that means you like it and are finding it helpful.', 'newstatpress' ).'</p>';
      $notice_text.= '<p>'. __( 'NewStatPress is provided for free and maintained only on free time. If you like the plugin, consider a donation to help further its development', 'newstatpress' ).'</p>';
      $notice_text.= '<a class=\"button button-primary\" href=\"'.nsp_DONATE_URL.'\" target=\"_blank\" rel=\"external\">'. __( 'Yes, I\'d like to donate!', 'newstatpress' ) .'</a>';
      $notice_text.= ' &nbsp; ';
      $notice_text.= '<a class=\"button button-default\" href=\"'.$query_str_con.'\" target=\"_self\" rel=\"external\">'. __( 'I already did!', 'newstatpress' ) .'</a>';

			$status['currentnag'] = TRUE;
      $status['donate'] = FALSE;
		}

	}

	if( !empty( $status['currentnag'] ) ) {
		add_action( 'admin_notices', 'nsp_AdminNagNotices' );
		$new_nag_notice = array( 'nid' => $nid, 'style' => $style, 'notice' => $notice_text );
		update_user_meta( $current_user->ID, 'newstatpress_nag_notices', $new_nag_notice );
		update_user_meta( $current_user->ID, 'newstatpress_nag_status', $status );
	}
}

function nsp_AdminNotices() {
	$admin_notices = get_option('newstatpress_admin_notices');
	if( !empty( $admin_notices ) ) {
		$style 	= $admin_notices['style']; /* 'error' or 'updated' */
		$notice	= $admin_notices['notice'];
    $query_str_con	= 'QUERYSTRING';
    echo '<div class="'.$style.'"><p>'.$notice.'</p></div>';
	}
	delete_option('newstatpress_admin_notices');
}

add_action( 'admin_init', 'nsp_HideNagNotices', -10 );
function nsp_HideNagNotices() {
	// if( !nsp_is_user_admin() ) { return; }
	$ns_codes		= array( 'n01' => 'vote',
                       'n02' => 'donate',
                       'n03' => 'news' );
	if( !isset( $_GET['newstatpress_hide_nag'], $_GET['nid'], $ns_codes[$_GET['nid']] ) || $_GET['newstatpress_hide_nag'] != '1' ) { return; }
	global $current_user;
	$status			= get_user_meta( $current_user->ID, 'newstatpress_nag_status', TRUE );
	$timenow		= time();
	$url			= nsp_GetUrl();
	$query_args		= nsp_GetQueryArgs( $url ); unset( $query_args['newstatpress_hide_nag'],$query_args['nid'] );
	$query_str		= http_build_query( $query_args ); if( $query_str != '' ) { $query_str = '?'.$query_str; }
	$redirect_url	= nsp_FixUrl( $url, TRUE, TRUE ) . $query_str;
	$status['currentnag'] = FALSE;
  if ($_GET['nid']!="n03")
    $status['lastnag'] = $timenow;
  $status[$ns_codes[$_GET['nid']]] = TRUE;
	update_user_meta( $current_user->ID, 'newstatpress_nag_status', $status );
	update_user_meta( $current_user->ID, 'newstatpress_nag_notices', array() );
	wp_redirect( $redirect_url );
	exit;
}


//---------------------------------------------------------------------------
// OTHER Functions
//---------------------------------------------------------------------------


function nsp_load_time()
{
	echo "<font size='1'>Page generated in " . timer_stop(0,2) . "s ".get_num_queries()." SQL queries</font>";
}



/**
 * Display tabs pf navigation bar for menu in page
 *
 * @param menu_tabs list of menu tabs
 * @param current current tabs
 * @param ref page reference
 */
function nsp_DisplayTabsNavbarForMenuPage($menu_tabs, $current, $ref) {
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $menu_tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class tab$tab' href='?page=$ref&tab=$tab'>$name</a>";
    }
    echo '</h2>';
}


// function nsp_DisplayTabsNavbarForMenuPages($menu_tabs, $current, $ref) {
//
//     echo "<div id='usual1' class='icon32 usual'><br></div>";
//     echo "<h2  class='nav-tab-wrapper'>";
//     foreach( $menu_tabs as $tab => $name ){
//         $class = ( $tab == $current ) ? ' nav-tab-active selected' : '';
//         echo "<a class='nav-tab$class' href='#$tab'>$name</a>";
//     }
//     echo '</h2>';
// }








//---------------------------------------------------------------------------
// TABLE Functions
//---------------------------------------------------------------------------

function nsp_TableSize($table) {
  global $wpdb;
  // use prepare  
  $res = $wpdb->get_results($wpdb->prepare("SHOW TABLE STATUS LIKE %s", $table));
  foreach ($res as $fstatus) {
    $data_lenght = $fstatus->Data_length;
    $data_rows = $fstatus->Rows;
  }
  return number_format(($data_lenght/1024/1024), 2, ",", " ")." Mb ($data_rows ". __('records','newstatpress').")";
}

function nsp_TableSize2($table) {
  global $wpdb;
  // use prepare 
  $res = $wpdb->get_results($wpdb->prepare("SHOW TABLE STATUS LIKE %s", $table));
  foreach ($res as $fstatus) {
    $data_lenght = $fstatus->Data_length;
    $data_rows = $fstatus->Rows;
  }
    return number_format(($data_lenght/1024/1024), 2, ",", " ")."  ". __('Mb','newstatpress');
}

function nsp_TableRecords($table) {
  global $wpdb;
  // use prepare 
  $res = $wpdb->get_results($wpdb->prepare("SHOW TABLE STATUS LIKE %s", $table));
  foreach ($res as $fstatus) {
    $data_lenght = $fstatus->Data_length;
    $data_rows = $fstatus->Rows;
  }
  return $data_rows;
}


?>
