<?php
/**********************************************************
 Plugin Name: NewStatPress
 Plugin URI: http://newstatpress.altervista.org
 Text Domain: newstatpress
 Description: Real time stats for your Wordpress blog
 Version: 1.3.5
 Author: Stefano Tognon and cHab (from Daniele Lippi works)
 Author URI: http://newstatpress.altervista.org
************************************************************/
                   
// Make sure plugin remains secure if called directly
if( !defined( 'ABSPATH' ) ) {
  if( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
  die(__('ERROR: This plugin requires WordPress and will not function if called directly.','newstatpress'));
}

$_NEWSTATPRESS['version']='1.3.5';
$_NEWSTATPRESS['feedtype']='';

global $newstatpress_dir,
       $wpdb,
       $nsp_option_vars,
       $nsp_overview_screen,
       $nsp_widget_vars;

define('nsp_TEXTDOMAIN', 'newstatpress');
define('nsp_PLUGINNAME', 'NewStatPress');
define('nsp_REQUIRED_WP_VERSION','3.5');
define('nsp_NOTICENEWS', TRUE);
define('nsp_TABLENAME', $wpdb->prefix . 'statpress');
define('nsp_BASENAME', dirname(plugin_basename(__FILE__)));
define('nsp_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define('nsp_SERVER_NAME', nsp_GetServerName() );
define('nsp_RATING_URL', 'https://wordpress.org/support/view/plugin-reviews/'.nsp_TEXTDOMAIN );
define('nsp_PLUGIN_URL','http://newstatpress.altervista.org' );
define('nsp_DONATE_URL', 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F5S5PF4QBWU7E' );
define('nsp_SUPPORT_URL','https://wordpress.org/support/plugin/'.nsp_TEXTDOMAIN );

$newstatpress_dir = WP_PLUGIN_DIR . '/' .nsp_BASENAME;

$nsp_option_vars=array( // list of option variable name, with default value associated
                        // (''=>array('name'=>'','value'=>''))
                        'overview'=>array('name'=>'newstatpress_el_overview','value'=>'10'),
                        'top_days'=>array('name'=>'newstatpress_el_top_days','value'=>'5'),
                        'os'=>array('name'=>'newstatpress_el_os','value'=>'10'),
                        'browser'=>array('name'=>'newstatpress_el_browser','value'=>'10'),
                        'feed'=>array('name'=>'newstatpress_el_feed','value'=>'5'),
                        'searchengine'=>array('name'=>'newstatpress_el_searchengine','value'=>'10'),
                        'search'=>array('name'=>'newstatpress_el_search','value'=>'20'),
                        'referrer'=>array('name'=>'newstatpress_el_referrer','value'=>'10'),
                        'languages'=>array('name'=>'newstatpress_el_languages','value'=>'20'),
                        'spiders'=>array('name'=>'newstatpress_el_spiders','value'=>'10'),
                        'pages'=>array('name'=>'newstatpress_el_pages','value'=>'5'),
                        'visitors'=>array('name'=>'newstatpress_el_visitors','value'=>'5'),
                        'daypages'=>array('name'=>'newstatpress_el_daypages','value'=>'5'),
                        'ippages'=>array('name'=>'newstatpress_el_ippages','value'=>'5'),
                        'ip_per_page_newspy'=>array('name'=>'newstatpress_ip_per_page_newspy','value'=>''),
                        'visits_per_ip_newspy'=>array('name'=>'newstatpress_visits_per_ip_newspy','value'=>''),
                        'bot_per_page_spybot'=>array('name'=>'newstatpress_bot_per_page_spybot','value'=>''),
                        'visits_per_bot_spybot'=>array('name'=>'newstatpress_visits_per_bot_spybot','value'=>''),
                        'autodelete'=>array('name'=>'newstatpress_autodelete','value'=>''),
                        'autodelete_spiders'=>array('name'=>'newstatpress_autodelete_spiders','value'=>''),
                        'daysinoverviewgraph'=>array('name'=>'newstatpress_daysinoverviewgraph','value'=>''),
                        'ignore_users'=>array('name'=>'newstatpress_ignore_users','value'=>''),
                        'ignore_ip'=>array('name'=>'newstatpress_ignore_ip','value'=>''),
                        'ignore_permalink'=>array('name'=>'newstatpress_ignore_permalink','value'=>''),
                        'updateint'=>array('name'=>'newstatpress_updateint','value'=>''),
                        'calculation'=>array('name'=>'newstatpress_calculation_method','value'=>'classic'),
                        'menu_cap'=>array('name'=>'newstatpress_mincap','value'=>'read'),
                        'menuoverview_cap'=>array('name'=>'newstatpress_menuoverview_cap','value'=>'switch_themes'),
                        'menudetails_cap'=>array('name'=>'newstatpress_menudetails_cap','value'=>'switch_themes'),
                        'menuvisits_cap'=>array('name'=>'newstatpress_menuvisits_cap','value'=>'switch_themes'),
                        'menusearch_cap'=>array('name'=>'newstatpress_menusearch_cap','value'=>'switch_themes'),
                        'menuoptions_cap'=>array('name'=>'newstatpress_menuoptions_cap','value'=>'edit_users'),
                        'menutools_cap'=>array('name'=>'newstatpress_menutools_cap','value'=>'switch_themes'),
                        'menucredits_cap'=>array('name'=>'newstatpress_menucredits_cap','value'=>'read'),
                        'apikey'=>array('name'=>'newstatpress_apikey','value'=>'read'),
                        'ip2nation'=>array('name'=>'newstatpress_ip2nation','value'=>'none'),
                        'mail_notification'=>array('name'=>'newstatpress_mail_notification','value'=>'disabled'),
                        'mail_notification_freq'=>array('name'=>'newstatpress_mail_notification_freq','value'=>'daily'),
                        'mail_notification_address'=>array('name'=>'newstatpress_mail_notification_emailaddress','value'=>''),
                        'mail_notification_time'=>array('name'=>'newstatpress_mail_notification_time','value'=>''),
                        'mail_notification_info'=>array('name'=>'newstatpress_mail_notification_info','value'=>''),
                        'mail_notification_sender'=>array('name'=>'newstatpress_mail_notification_sender','value'=>'NewsStatPress'),
                        'settings'=>array('name'=>'newstatpress_settings','value'=>''),
                        'stats_offsets'=>array('name'=>'newstatpress_stats_offsets','value'=>'0')
                      );

$nsp_widget_vars=array( // list of widget variables name, with description associated
                       array('visits',__('Today visits', 'newstatpress')),
                       array('yvisits',__('Yesterday visits', 'newstatpress')),
                       array('mvisits',__('Month visits', 'newstatpress')),
                       array('wvisits',__('Week visits', 'newstatpress')),
                       array('totalvisits',__('Total visits', 'newstatpress')),
                       array('totalpageviews',__('Total pages view', 'newstatpress')),
                       array('todaytotalpageviews',__('Total pages view today', 'newstatpress')),
                       array('thistotalvisits',__('This page, total visits', 'newstatpress')),
                       array('alltotalvisits',__('All page, total visits', 'newstatpress')),
                       array('os',__('Visitor Operative System', 'newstatpress')),
                       array('browser',__('Visitor Browser', 'newstatpress')),
                       array('ip',__('Visitor IP address', 'newstatpress')),
                       array('since',__('Date of the first hit', 'newstatpress')),
                       array('visitorsonline',__('Counts all online visitors', 'newstatpress')),
                       array('usersonline',__('Counts logged online visitors', 'newstatpress')),
                       array('monthtotalpageviews',__('Total page view in the month', 'newstatpress')),
                       array('toppost',__('The most viewed Post', 'newstatpress'))
                      );

/**
 * Check to update of the plugin
 * Added by cHab
 *
 *******************************/
function nsp_UpdateCheck() {

  global $_NEWSTATPRESS;
  $active_version = get_option('newstatpress_version', '0' );
  $admin_notices = get_option( 'newstatpress_admin_notices' );

  if( !empty( $admin_notices ) )
    add_action( 'admin_notices', 'nsp_AdminNotices' );

  // check version, update installation date and update notice status
  if (version_compare( $active_version, $_NEWSTATPRESS['version'], '<' )) {
    if (version_compare( $active_version, '1.1.0', '<' ))
      nsp_Activation('old'); // for old installation > 14 days since nsp 1.1.4
      if(nsp_NOTICENEWS) {
        global $current_user;
        $status = get_user_meta( $current_user->ID, 'newstatpress_nag_status', TRUE );
        $status['news'] = FALSE ;
        update_user_meta( $current_user->ID, 'newstatpress_nag_status', $status );
      }
    update_option('newstatpress_version', $_NEWSTATPRESS['version']);
  }

  //check if is compatible with WP Version
  global $wp_version;
  if( version_compare( $wp_version, nsp_REQUIRED_WP_VERSION, '<' ) ) {
    deactivate_plugins( nsp_PLUGIN_BASENAME );
    $notice_text = sprintf( __( 'Plugin %s deactivated. WordPress Version %s required. Please upgrade WordPress to the latest version.', 'newstatpress' ), nsp_PLUGINNAME, nsp_REQUIRED_WP_VERSION );
    $new_admin_notice = array( 'style' => 'error', 'notice' => $notice_text );
    update_option( 'newstatpress_admin_notices', $new_admin_notice );
    add_action( 'admin_notices', 'nsp_AdminNotices' );
    return FALSE;
  }

  nsp_CheckNagNotices();

}
add_action( 'admin_init', 'nsp_UpdateCheck' );

/**
 * Check and Export if capability of user allow that
 * need here due to header change
 * Updated by cHab
 *
 ***************************************************/
function nsp_checkExport() {
  global $nsp_option_vars;
  global $current_user;
  wp_get_current_user();

  if (isset($_GET['newstatpress_action']) && $_GET['newstatpress_action'] == 'exportnow') {
    $tools_capability=get_option('newstatpress_menutools_cap') ;
    if(!$tools_capability) { //default value
      $tools_capability=$nsp_option_vars['menutools_cap']['value'];
    }  
    if ( user_can( $current_user, $tools_capability ) ) {
      require ('includes/nsp_tools.php');
      nsp_ExportNow();
    }
  }
}
add_action('init','nsp_checkExport');

/**
 * Installation time update of the plugin
 * Added by cHab
 *
 *******************************/
register_activation_hook( __FILE__, 'nsp_Activation' );
function nsp_Activation($arg='') {
  global $nsp_option_vars;
  $nsp_settings = get_option($nsp_option_vars['settings']['name']);
  if( empty( $nsp_settings['install_time'] ) ) {
  	$nsp_settings['install_time'] = time();
    if($arg='old')
      $nsp_settings['install_time'] = time()-7776000;
    update_option( 'newstatpress_settings', $nsp_settings );
  }
}

/**
 * Load CSS style, languages files, extra files
 * Added by cHab
 *
 ***********************************************/
 function nsp_RegisterPluginStylesAndScripts() {

   //CSS
   $style_path=plugins_url('./css/style.css', __FILE__);

   wp_register_style('NewStatPressStyles', $style_path);
   wp_enqueue_style('NewStatPressStyles');

   $style_path2=plugins_url('./css/pikaday.css', __FILE__);

   wp_register_style('pikaday', $style_path2);
   wp_enqueue_style('pikaday');

   wp_enqueue_style( 'NewStatPressStyles', get_stylesheet_uri(), array( 'dashicons' ), '1.0' );

   // Load the postbox script that provides the widget style boxes.
   wp_enqueue_script('common');
   wp_enqueue_script('wp-lists');
   wp_enqueue_script('postbox'); //meta box

   // JS and jQuery
   $scripts=array('idTabs'=>plugins_url('./js/jquery.idTabs.min.js', __FILE__),
                  'moment'=>plugins_url('./js/moment.min.js', __FILE__),
                  'pikaday'=>plugins_url('./js/pikaday.js', __FILE__),                  
                  'NewStatPressJs'=>plugins_url('./js/nsp_general.js', __FILE__));
   foreach($scripts as $key=>$sc)
   {
       wp_register_script( $key, $sc );
       
       if ($key=='NewStatPressJs') {
         wp_localize_script( 'NewStatPressJs', 'ExtData', array(
           'Credit' => plugins_url( './includes/json/credit.json', __FILE__ ),
           'Lang' => plugins_url( './includes/json/lang.json', __FILE__ ),
           'Resources' => plugins_url( './includes/json/ressources.json', __FILE__ ),
           'Donation' => plugins_url( './includes/json/donation.json', __FILE__ ),
           'Domain' => plugins_url( './images/domain', __FILE__ ),
         ));       
       }
       
       wp_enqueue_script( $key );
   }   

 }
 add_action( 'admin_enqueue_scripts', 'nsp_RegisterPluginStylesAndScripts' );


 function nsp_load_textdomain() {
   load_plugin_textdomain( 'newstatpress', false, nsp_BASENAME . '/langs' );
 }
 add_action( 'plugins_loaded', 'nsp_load_textdomain' );

 if (is_admin()) { //load dashboard and extra functions
   require ('includes/nsp_functions-extra.php');
   require ('includes/nsp_dashboard.php');

   add_action('wp_dashboard_setup', 'nsp_AddDashBoardWidget' );
 }
 require ('includes/api/variables.php');
 require ('includes/api/external.php');
 require ('includes/nsp_core.php');
 
 // register actions for ajax variables API
 add_action( 'wp_ajax_nsp_variables', 'nsp_variablesAjax' );
 add_action( 'wp_ajax_nopriv_nsp_variables', 'nsp_variablesAjax' ); // need this to serve non logged in users
 
 // register actions for ajax external API
 add_action( 'wp_ajax_nsp_external', 'nsp_externalApiAjaxN' );
 add_action( 'wp_ajax_nopriv_nsp_external', 'nsp_externalApiAjax' ); // need this to serve non logged in users
 
 
/*************************************
 * Add pages for NewStatPress plugin *
 *************************************/
function nsp_BuildPluginMenu() {

  global $nsp_option_vars;
  global $current_user;
  global $nsp_overview_screen;
  wp_get_current_user();


  // Fix capability if it's not defined
  // $capability=get_option('newstatpress_mincap') ;
  // if(!$capability) //default value
    $capability=$nsp_option_vars['menu_cap']['value'];

  $overview_capability=get_option('newstatpress_menuoverview_cap') ;
  if(!$overview_capability) //default value
    $overview_capability=$nsp_option_vars['menuoverview_cap']['value'];

  $details_capability=get_option('newstatpress_menudetails_cap') ;
  if(!$details_capability) //default value
    $details_capability=$nsp_option_vars['menudetails_cap']['value'];

  $visits_capability=get_option('newstatpress_menuvisits_cap') ;
  if(!$visits_capability) //default value
    $visits_capability=$nsp_option_vars['menuvisits_cap']['value'];

  $search_capability=get_option('newstatpress_menusearch_cap') ;
  if(!$search_capability) //default value
    $search_capability=$nsp_option_vars['menusearch_cap']['value'];

  $tools_capability=get_option('newstatpress_menutools_cap') ;
  if(!$tools_capability) //default value
    $tools_capability=$nsp_option_vars['menutools_cap']['value'];

  $options_capability=get_option('newstatpress_menuoptions_cap') ;
  if(!$options_capability) //default value
    $options_capability=$nsp_option_vars['menuoptions_cap']['value'];

  $credits_capability=$nsp_option_vars['menucredits_cap']['value'];

  // Display menu with personalized capabilities if user IS NOT "subscriber"
  if ( user_can( $current_user, "edit_posts" ) ) {
    add_menu_page('NewStatPres', 'NewStatPress', $capability, 'nsp-main', 'nsp_NewStatPressMainC', plugins_url('newstatpress/images/stat.png',nsp_BASENAME));
    $nsp_overview_screen=add_submenu_page('nsp-main', __('Overview','newstatpress'), __('Overview','newstatpress'), $overview_capability, 'nsp-main', 'nsp_NewStatPressMainC');
    add_submenu_page('nsp-main', __('Details','newstatpress'), __('Details','newstatpress'), $details_capability, 'nsp_details', 'nsp_DisplayDetailsC');
    add_submenu_page('nsp-main', __('Visits','newstatpress'), __('Visits','newstatpress'), $visits_capability, 'nsp_visits', 'nsp_DisplayVisitsPageC');
    add_submenu_page('nsp-main', __('Search','newstatpress'), __('Search','newstatpress'), $search_capability, 'nsp_search', 'nsp_DatabaseSearchC');
    add_submenu_page('nsp-main', __('Tools','newstatpress'), __('Tools','newstatpress'), $tools_capability, 'nsp_tools', 'nsp_DisplayToolsPageC');
    add_submenu_page('nsp-main', __('Options','newstatpress'), __('Options','newstatpress'), $options_capability, 'nsp_options', 'nsp_OptionsC');
    add_submenu_page('nsp-main', __('Credits','newstatpress'), __('Credits','newstatpress'), $credits_capability, 'nsp_credits', 'nsp_DisplayCreditsPageC');

    // Add action to load the meta boxes to the overview page.
    add_action('load-' . $nsp_overview_screen, 'nsp_statistics_load_overview_page');
    add_action('admin_footer-'.$nsp_overview_screen,'wptuts_print_script_in_footer');
  }
}
add_action('admin_menu', 'nsp_BuildPluginMenu');

/* Prints script in footer to 'initialises' the meta boxes */
function wptuts_print_script_in_footer() {
  ?>
    <script>jQuery(document).ready(function(){ postboxes.add_postbox_toggles(pagenow);jQuery('.postbox h3').prepend('<a class="togbox">+</a> '); });</script>
  <?php
}


function nsp_statistics_load_overview_page() {
  global $nsp_overview_screen;
  add_meta_box( 'nsp_lasthits_postbox', __('Last hits',nsp_TEXTDOMAIN), 'nsp_generate_overview_lasthits', $nsp_overview_screen, 'normal', null, array( 'widget' => 'lasthits' )  );
  add_meta_box( 'nsp_lastsearchterms_postbox', __('Last search terms',nsp_TEXTDOMAIN), 'nsp_generate_overview_lastsearchterms', $nsp_overview_screen, 'normal', null, array( 'widget' => 'lastsearchterms' )  );
  add_meta_box( 'nsp_lastreferrers_postbox', __('Last referrers',nsp_TEXTDOMAIN), 'nsp_generate_overview_lastreferrers', $nsp_overview_screen, 'normal', null, array( 'widget' => 'lastreferrers' )  );
  add_meta_box( 'nsp_agents_postbox', __('Last agents',nsp_TEXTDOMAIN), 'nsp_generate_overview_agents', $nsp_overview_screen, 'normal', null, array( 'widget' => 'agents' )  );
  add_meta_box( 'nsp_pages_postbox', __('Last pages',nsp_TEXTDOMAIN), 'nsp_generate_overview_pages', $nsp_overview_screen, 'normal', null, array( 'widget' => 'pages' )  );
  add_meta_box( 'nsp_spiders_postbox', __('Last spiders',nsp_TEXTDOMAIN), 'nsp_generate_overview_spiders', $nsp_overview_screen, 'normal', null, array( 'widget' => 'spiders' )  );
}

function nsp_NewStatPressMainC() {
  require ('includes/nsp_overview.php');
  nsp_NewStatPressMain();
}

function nsp_DisplayDetailsC() {
  require ('includes/nsp_details.php');
  nsp_DisplayDetails();
}

function nsp_DisplayCreditsPageC() {
  require ('includes/nsp_credits.php');
  nsp_DisplayCreditsPage();
}

function nsp_OptionsC() {
  require ('includes/nsp_options.php');
  nsp_Options();
}

function nsp_DisplayToolsPageC() {
  require ('includes/nsp_tools.php');
  nsp_DisplayToolsPage();
}

function nsp_DisplayVisitsPageC() {
  require ('includes/nsp_visits.php');
  nsp_DisplayVisitsPage();
}

function nsp_DatabaseSearchC() {
  require ('includes/nsp_search.php');
  nsp_DatabaseSearch();
}




/**
 * Get the url of the plugin
 *
 * @return the url of the plugin
 ********************************/
function nsp_PluginUrl() {
  //Try to use WP API if possible, introduced in WP 2.6
  if (function_exists('plugins_url')) return trailingslashit(plugins_url(basename(dirname(__FILE__))));

  //Try to find manually... can't work if wp-content was renamed or is redirected
  $path = dirname(__FILE__);
  $path = str_replace("\\","/",$path);
  $path = trailingslashit(get_bloginfo('wpurl')) . trailingslashit(substr($path,strpos($path,"wp-content/")));

  return $path;
}

function nsp_GetServerName() {
  $server_name = '';
  if(     !empty( $_SERVER['HTTP_HOST'] ) )             { $server_name = $_SERVER['HTTP_HOST']; }
  elseif( !empty( $_NEWSTATPRESS_ENV['HTTP_HOST'] ) )   { $server_name = $_NEWSTATPRESS_ENV['HTTP_HOST']; }
  elseif( !empty( $_SERVER['SERVER_NAME'] ) )           { $server_name = $_SERVER['SERVER_NAME']; }
  elseif( !empty( $_NEWSTATPRESS_ENV['SERVER_NAME'] ) ) { $server_name = $_NEWSTATPRESS_ENV['SERVER_NAME']; }
  return nsp_CaseTrans( 'lower', $server_name );
}

/***TODO rsfb_strlen
* Convert case using multibyte version if available, if not, use defaults
***/
function nsp_CaseTrans( $type, $string ) {

  switch ($type) {
    case 'upper':
      return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $string, 'UTF-8' ) : strtoupper( $string );
    case 'lower':
      return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string, 'UTF-8' ) : strtolower( $string );
    case 'ucfirst':
       if( function_exists( 'mb_strtoupper' ) && function_exists( 'mb_substr' ) ) {
         $strtmp = mb_strtoupper( mb_substr( $string, 0, 1, 'UTF-8' ), 'UTF-8' ) . mb_substr( $string, 1, NULL, 'UTF-8' );
         /* Added workaround for strange PHP bug in mb_substr() on some servers */
         return rsfb_strlen( $string ) === rsfb_strlen( $strtmp ) ? $strtmp : ucfirst( $string );
       } else { 
           return ucfirst( $string ); 
         }
    case 'ucwords':
      return function_exists( 'mb_convert_case' ) ? mb_convert_case( $string, MB_CASE_TITLE, 'UTF-8' ) : ucwords( $string );
      /***
       * Note differences in results between ucwords() and this.
       * ucwords() will capitalize first characters without altering other characters, whereas this will lowercase everything, but capitalize the first character of each word.
       This works better for our purposes, but be aware of differences.
       ***/
    default:
      return $string;
  }
}


/**
 * Calculate offset_time in second to add to epoch format
 * added by cHab
 *
 * @param $t,$tu
 * @return $offset_time
 ***********************************************************/
function nsp_calculationOffsetTime($t,$tu) {

  list($current_hour, $current_minute) = explode(":", date("H:i",$t));
  list($publishing_hour, $publishing_minutes) = explode(":", $tu);

  if($current_hour>$publishing_hour)
    $plus_hour=24-$current_hour+$publishing_hour;
  else
    $plus_hour=$publishing_hour-$current_hour;

  if($current_minute>$publishing_minutes) {
    $plus_minute=60-$current_minute+$publishing_minutes;
    if($plus_hour==0)
      $plus_hour=23;
    else
      $plus_hour=$plus_hour-1;
  }
  else
    $plus_minute=$publishing_minutes-$current_minute;

  return $offset_time=$plus_hour*60*60+$plus_minute*60;
}

/**
* Parameters for newstatpress email notification
* added by cHab
*
***************************************************/
function nsp_Set_mail_content_type($content_type) {
  return 'text/html';
}

/**
 * Send an email notification with the overview statistics
 * added by cHab
 *
 * @param $arg : type of mail ('' or 'test')
 * @return $email_confirmation
 *************************************/
function nsp_stat_by_email($arg='') {
  global $nsp_option_vars, $support_pluginpage, $author_linkpage;
  $date = date('m/d/Y h:i:s a', time());

  add_filter('wp_mail_content_type','nsp_Set_mail_content_type');

  $name=$nsp_option_vars['mail_notification']['name'];
  $status=get_option($name);
  $name=$nsp_option_vars['mail_notification_freq']['name'];
  $freq=get_option($name);

  $userna = get_option('newstatpress_mail_notification_info');

  //$headers= 'From:NewStatPress';
  $blog_title = get_bloginfo('name');
  $subject=sprintf(__('[%s] Visits statistics','newstatpress'), $blog_title);
  if($arg=='test')
    $subject=sprintf(__('[%s] Visits statistics : test of email address','newstatpress'), $blog_title);

  require_once ('includes/api/nsp_api_dashboard.php');
  $resultH=nsp_ApiDashboard("HTML");

  $name=$nsp_option_vars['mail_notification_address']['name'];
  $email_address=get_option($name);

  $name=$nsp_option_vars['mail_notification_sender']['name'];
  $sender=get_option($name);
  //$sender=get_option($nsp_option_vars['name']);
  if($sender=='') {
    $sender=$nsp_option_vars['mail_notification_sender']['value'];
  } 

  $support_pluginpage="<a href='".nsp_SUPPORT_URL."' target='_blank'>".__('support page','newstatpress')."</a>";
  $author_linkpage="<a href='".nsp_PLUGIN_URL."/?page_id=2' target='_blank'>".__('the author','newstatpress')."</a>";

  $credits_introduction=__('If you have found this plugin useful and you like it, thank you to take a moment to rate it.','newstatpress');
  $credits_introduction.=' '.sprintf(__('You can help to the plugin development by reporting bugs on the %s or by adding/updating translation by contacting directly %s.','newstatpress'), $support_pluginpage, $author_linkpage);
  $credits_introduction.='<br />';
  $credits_introduction.=__('NewStatPress is provided for free and is maintained only on free time, you can also consider a donation to support further work, directly on the plugin website or through the plugin (Credits Page).','newstatpress');

  $warning=__('This option is yet experimental, please report bugs or improvement (see link on the bottom)','newstatpress');
  $advising=__('You receive this email because you have enabled the statistics notification in the NewStatpress plugin (option menu) from your WP website ','newstatpress');
  $message = __('Dear','newstatpress')." $userna, <br /> <br />
             <i>$advising<STRONG>$blog_title</STRONG>.</i>
             <mark>$warning.</mark> <br />
             <br />".
             __('Statistics at','newstatpress')." $date (".__('server time','newstatpress').") from  $blog_title: <br />
             $resultH <br /> <br />"
             .__('Best Regards from','newstatpress')." <i>NewStatPress Team</i>. <br />
             <br />
             <br />
             -- <br />
             $credits_introduction";
  $headers = "From: " . $sender . " <newstatpress@altervista.org> \r\n";
  $email_confirmation = wp_mail($email_address, $subject, $message, $headers);

  remove_filter('wp_mail_content_type','nsp_Set_mail_content_type');

  return $email_confirmation;
}


function nsp_mail_notification_deactivate() {
 wp_clear_scheduled_hook( 'nsp_mail_notification' );
}

//Hook mail publi
add_action( 'nsp_mail_notification', 'nsp_stat_by_email' );




/**
 * Add Settings link to plugins page
 * added by cHab
 *
 */
function nsp_AddSettingsLink( $links, $file ) {
	if ( $file != plugin_basename( __FILE__ ))
 		return $links;

 	$settings_link = '<a href="admin.php?page=nsp_options">' . __( 'Settings', 'newstatpress' ) . '</a>';

 	array_unshift( $links, $settings_link );

 	return $links;
 }
 add_filter( 'plugin_action_links', 'nsp_AddSettingsLink',10,2);



/**TODO useful or not????
 * PHP 4 compatible mb_substr function
 * (taken in statpress-visitors)
 */
function nsp_MySubstr($str, $x, $y = 0) {
  if($y == 0)
    $y = strlen($str) - $x;

  if(function_exists('mb_substr'))
    return mb_substr($str, $x, $y);
  else
    return substr($str, $x, $y);
}




// Not use!!! commented by chab
/**
 * Check if the argument is an IP addresses
 *
 * @param ip the ip to check
 * @return TRUE if it is an ip
 */
// function nsp_CheckIP($ip) {
//   return ( ! preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
// }




/**
 * Decode the given url
 *
 * @param out_url the given url to decode
 * @return the decoded url
 ****************************************/
function nsp_DecodeURL($out_url) {
  $out_url=filter_var($out_url, FILTER_SANITIZE_URL);

  if($out_url == '') { $out_url=__('Page','newstatpress').": Home"; }
  if(substr($out_url,0,4)=="cat=") { $out_url=__('Category','newstatpress').": ".get_cat_name(substr($out_url,4)); }
  if(substr($out_url,0,2)=="m=") { $out_url=__('Calendar','newstatpress').": ".substr($out_url,6,2)."/".substr($out_url,2,4); }
  if(substr($out_url,0,2)=="s=") { $out_url=__('Search','newstatpress').": ".substr($out_url,2); }
  if(substr($out_url,0,2)=="p=") {
    $subOut=substr($out_url,2);
    $post_id_7 = get_post($subOut, ARRAY_A);
    $out_url = $post_id_7['post_title'];
  }
  if(substr($out_url,0,8)=="page_id=") {
    $subOut=substr($out_url,8);
    $post_id_7=get_page($subOut, ARRAY_A);
    $out_url = __('Page','newstatpress').": ".$post_id_7['post_title'];
  }
  return $out_url;
}


function nsp_URL() {
  $urlRequested = (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '' );
  if ( $urlRequested == "" ) { // SEO problem!
    $urlRequested = (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '' );
  }
  if(substr($urlRequested,0,2) == '/?') { $urlRequested=substr($urlRequested,2); }
  if($urlRequested == '/') { $urlRequested=''; }
  
  // sanitize urldecode
  $urlRequested = filter_var($urlRequested, FILTER_SANITIZE_URL);
  //if (!filter_var($urlRequested, FILTER_VALIDATE_URL)) return "";
  
  return $urlRequested;
}


/**
 * Convert data us to default format di Wordpress
 *
 * @param dt: date to convert
 * @return converted data
 ****************************************************/
function nsp_hdate($dt = "00000000") {
  return mysql2date(get_option('date_format'), substr($dt,0,4)."-".substr($dt,4,2)."-".substr($dt,6,2));
}



function newstatpress_hdate($dt = "00000000") {
  return mysql2date(get_option('date_format'), nsp_MySubstr($dt, 0, 4) . "-" . nsp_MySubstr($dt, 4, 2) . "-" . nsp_MySubstr($dt, 6, 2));
}




//---------------------------------------------------------------------------
// GET DATA from visitors Functions
//---------------------------------------------------------------------------


/**TODO clean $accepted
 * Extracts the accepted language from browser headers
 */
function nsp_GetLanguage($accepted){
  if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])){

    // Capture up to the first delimiter (, found in Safari)
    preg_match("/([^,;]*)/", $_SERVER["HTTP_ACCEPT_LANGUAGE"], $array_languages);

    // Fix some codes, the correct syntax is with minus (-) not underscore (_)
    return str_replace( "_", "-", strtolower( $array_languages[0] ) );
  }
  return 'xx';  // Indeterminable language
}

// function nsp_GetLanguage($accepted) {
//   return substr($accepted,0,2);
// }


function nsp_GetQueryPairs($url){
  $parsed_url = parse_url($url);
  $tab=parse_url($url);
  $host = $tab['host'];
  if(key_exists("query",$tab)){
    $query=$tab["query"];
    return explode("&",$query);
  } else {return null;}
}


/**
 * Get OS from the given argument
 *
 * @param arg the argument to parse for OS
 * @return the OS find in configuration file
 *******************************************/
function nsp_GetOs($arg) {
  global $newstatpress_dir;

  $arg=str_replace(" ","",$arg);
  $lines = file($newstatpress_dir.'/def/os.dat');
  foreach($lines as $line_num => $os) {
    list($nome_os,$id_os)=explode("|",$os);
    if(strpos($arg,$id_os)===FALSE) continue;
    return $nome_os;     // fount
  }
  return '';
}

/**
 * Get OS logo from the given argument
 *
 * @param arg the argument to parse for OS
 * @return the OS find in configuration file
 *******************************************/
function nsp_GetOsImg($arg) {
  global $newstatpress_dir;
  $lines = file($newstatpress_dir.'/def/os.dat');
  foreach($lines as $line_num => $os) {
    list($name_os,$id_os,$img_os)=explode("|",$os);
    if(strcmp($name_os,$arg)==0)
    	return $img_os;
  }
  return '';
}

/**
 * Get Browser from the given argument
 *
 * @param arg the argument to parse for Brower
 * @return the Browser find in configuration file
 ************************************************/
function nsp_GetBrowser($arg) {
  global $newstatpress_dir;

  $arg=str_replace(" ","",$arg);
  $lines = file($newstatpress_dir.'/def/browser.dat');
  foreach($lines as $line_num => $browser) {
    list($nome,$id)=explode("|",$browser);
    if(strpos($arg,$id)===FALSE) continue;
    return $nome;     // fount
  }
  return '';
}

/**
 * Get Browser from the given argument
 *
 * @param arg the argument to parse for Brower
 * @return the Browser find in configuration file
 ************************************************/
function nsp_GetBrowserImg($arg) {
  global $newstatpress_dir;
  $lines = file($newstatpress_dir.'/def/browser.dat');
  foreach($lines as $line_num => $browser) {
    list($name_browser,$id,$img_browser)=explode("|",$browser);
    //echo $name_browser;
    if(strcmp($name_browser,$arg)==0) return $img_browser;
  }
  return '';
}

/**
 * Check if the given ip is to ban
 *
 * @param arg the ip to check
 * @return '' id the address is banned
 */
function nsp_CheckBanIP($arg){
  global $newstatpress_dir;

  $lines = file($newstatpress_dir.'/def/banips.dat');
  foreach($lines as $line_num => $banip) {
    if(strpos($arg,rtrim($banip,"\n"))===FALSE) continue;
    return ''; // this is banned
  }
  return $arg;
}

/**
 * Get the search engines
 *
 * @param refferer the url to test
 * @return the search engine present in the url
 */
function nsp_GetSE($referrer = null){
  global $newstatpress_dir;

  $key = null;
  $lines = file($newstatpress_dir.'/def/searchengines.dat');
  foreach($lines as $line_num => $se) {
    list($nome,$url,$key)=explode("|",$se);
    if(strpos($referrer,$url)===FALSE) continue;

    # find if
    $variables = nsp_GetQueryPairs(html_entity_decode($referrer));
    $variables === null ? $i = 0 : $i = count($variables);
    while($i--){
      $tab=explode("=",$variables[$i]);
      if($tab[0] == $key){return ($nome."|".urldecode($tab[1]));}
    }
  }
  return null;
}

/**
 * Get the spider from the given agent
 *
 * @param agent the agent string
 * @return agent the fount agent
 *************************************/
function nsp_GetSpider($agent = null){
  global $newstatpress_dir;

  $agent=str_replace(" ","",$agent);
  $key = null;
  $lines = file($newstatpress_dir.'/def/spider.dat');
  foreach($lines as $line_num => $spider) {
    list($nome,$key)=explode("|",$spider);
    if(strpos($agent,$key)===FALSE) continue;
    # fount
    return $nome;
  }
  return null;
}

/**
 * Get the previous month in 'YYYYMM' format
 *
 * @return the previous month
 */
function nsp_Lastmonth() {
  $ta = getdate(current_time('timestamp'));

  $year = $ta['year'];
  $month = $ta['mon'];

  --$month; // go back 1 month

  if( $month === 0 ): // if this month is Jan
    --$year; // go back a year
    $month = 12; // last month is Dec
  endif;

  // return in format 'YYYYMM'
  return sprintf( $year.'%02d', $month);
}

/**
 * Create or update the table
 *
 * @param action to do: update, create
 *************************************/
 function nsp_BuildPluginSQLTable($action) {

   global $wpdb;
   global $wp_db_version;
   $table_name = nsp_TABLENAME;
   $charset_collate = $wpdb->get_charset_collate();
   $index_list=array(array('Key_name'=>"spider_nation", 'Column_name'=>"(spider, nation)"),
                     array('Key_name'=>"ip_date", 'Column_name'=>"(ip, date)"),
                     array('Key_name'=>"agent", 'Column_name'=>"(agent)"),
                     array('Key_name'=>"search", 'Column_name'=>"(search)"),
                     array('Key_name'=>"referrer", 'Column_name'=>"(referrer)"),
                     array('Key_name'=>"feed_spider_os", 'Column_name'=>"(feed, spider, os)"),
                     array('Key_name'=>"os", 'Column_name'=>"(os)"),
                     array('Key_name'=>"date_feed_spider", 'Column_name'=>"(date, feed, spider)"),
                     array('Key_name'=>"feed_spider_browser", 'Column_name'=>"(feed, spider, browser)"),
                     array('Key_name'=>"browser", 'Column_name'=>"(browser)")
                     );
   // Add by chab
   // IF the table is already created then DROP INDEX for update
   if ($action=='')
     $action='create';

   $sql_createtable = "
    CREATE TABLE ". $table_name . " (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      date int(8),
      time time,
      ip varchar(39),
      urlrequested varchar(250),
      agent varchar(250),
      referrer varchar(512),
      search varchar(250),
      nation varchar(2),
      os varchar(30),
      browser varchar(32),
      searchengine varchar(16),
      spider varchar(32),
      feed varchar(8),
      user varchar(16),
      timestamp timestamp DEFAULT 0,
      UNIQUE KEY id (id)";

   if ($action=='create') {
     foreach ($index_list as $index)
     {
       $Key_name=$index['Key_name'];
       $Column_name=$index['Column_name'];
       $sql_createtable.=", INDEX $Key_name $Column_name";
     }
   }
   elseif ($action=='update') {
       foreach ($index_list as $index)
       {
         $Key_name=$index['Key_name'];
         $Column_name=$index['Column_name'];
         if ($wpdb->query($wpdb->prepare("SHOW INDEXES FROM $table_name WHERE Key_name = %s", $Key_name))=='') {
           $sql_createtable.=",\n INDEX $Key_name $Column_name";
         }
       }
   }
   $sql_createtable.=") $charset_collate;";


  //  echo $sql_createtable;

  if($wp_db_version >= 5540) $page = 'wp-admin/includes/upgrade.php';
  else $page = 'wp-admin/upgrade'.'-functions.php';

  require_once(ABSPATH . $page);
  dbDelta($sql_createtable);
}

/**
 * Get if this is a feed
 *
 * @param url the url to test
 * @return the kind of feed that is found
 *****************************************/
function nsp_IsFeed($url) {
  $tmp=get_bloginfo('rdf_url');
  if ($tmp) {
    if (stristr($url,$tmp) != FALSE) { return 'RDF'; }
  }

  $tmp=get_bloginfo('rss2_url');
  if ($tmp) {
    if (stristr($url,$tmp) != FALSE) { return 'RSS2'; }
  }

  $tmp=get_bloginfo('rss_url');
  if ($tmp) {
    if (stristr($url,$tmp) != FALSE) { return 'RSS'; }
  }

  $tmp=get_bloginfo('atom_url');
  if ($tmp) {
    if (stristr($url,$tmp) != FALSE) { return 'ATOM'; }
  }

  $tmp=get_bloginfo('comments_rss2_url');
  if ($tmp) {
    if (stristr($url,$tmp) != FALSE) { return 'COMMENT'; }
  }

  $tmp=get_bloginfo('comments_atom_url');
  if ($tmp) {
    if (stristr($url,$tmp) != FALSE) { return 'COMMENT'; }
  }

  if (stristr($url,'wp-feed.php') != FALSE) { return 'RSS2'; }
  if (stristr($url,'/feed/') != FALSE) { return 'RSS2'; }
  return '';
}

/**
 * Insert statistic into the database
 *
 ************************************/
function nsp_StatAppend() {

  global $wpdb;
  $table_name = nsp_TABLENAME;
  global $userdata;
  global $_STATPRESS;


	wp_get_current_user();
  $feed='';

  // Time
  $timestamp  = current_time('timestamp');
  $vdate  = gmdate("Ymd",$timestamp);
  $vtime  = gmdate("H:i:s",$timestamp);
  $timestamp = date('Y-m-d H:i:s', $timestamp);

  // IP
  $ipAddress = $_SERVER['REMOTE_ADDR']; // BASIC detection -> to delete if it works
  // $ipAddress = htmlentities(nsp_GetUserIP());

  // Is this IP blacklisted from file?
  if(nsp_CheckBanIP($ipAddress) == '') { return ''; }

  // Is this IP blacklisted from user?
  $to_ignore = get_option('newstatpress_ignore_ip', array());
  foreach($to_ignore as $a_ip_range){
    list ($ip_to_ignore, $mask) = @explode("/", trim($a_ip_range));
    if (empty($mask)) $mask = 32;
    $long_ip_to_ignore = ip2long($ip_to_ignore);
    $long_mask = bindec( str_pad('', $mask, '1') . str_pad('', 32-$mask, '0') );
    $long_masked_user_ip = ip2long($ipAddress) & $long_mask;
    $long_masked_ip_to_ignore = $long_ip_to_ignore & $long_mask;
    if ($long_masked_user_ip == $long_masked_ip_to_ignore) { return ''; }
  }

  if(get_option('newstatpress_cryptip')=='checked') {
    $ipAddress = crypt($ipAddress,nsp_TEXTDOMAIN);
  }

  // URL (requested)
  $urlRequested=nsp_URL();
  if (preg_match("/.ico$/i", $urlRequested)) { return ''; }
  if (preg_match("/favicon.ico/i", $urlRequested)) { return ''; }
  if (preg_match("/.css$/i", $urlRequested)) { return ''; }
  if (preg_match("/.js$/i", $urlRequested)) { return ''; }  
  if (stristr($urlRequested, content_url()) != FALSE) { return ''; }
  if (stristr($urlRequested, admin_url()) != FALSE) { return ''; }
  $urlRequested=esc_sql($urlRequested);

  // Is a given permalink blacklisted?
  $to_ignore = get_option('newstatpress_ignore_permalink', array());
    foreach($to_ignore as $a_filter){
    if (!empty($urlRequested) && strpos($urlRequested, $a_filter) === 0) { return ''; }
  }

  $referrer = (isset($_SERVER['HTTP_REFERER']) ? htmlentities($_SERVER['HTTP_REFERER']) : '');
  $referrer=esc_url($referrer);
  $referrer=esc_sql($referrer);


  $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT']) : '');
  $userAgent=sanitize_text_field($userAgent);
  $userAgent=esc_sql($userAgent);

  $spider=nsp_GetSpider($userAgent);

  if(($spider != '') and (get_option('newstatpress_donotcollectspider')=='checked')) { return ''; }

  # ininitalize to empty
  $searchengine='';
  $search_phrase='';

  if($spider != '') {
    $os=''; $browser='';
  } else {
      // Trap feeds
      $feed=nsp_IsFeed(get_bloginfo('url').$_SERVER['REQUEST_URI']);
      // Get OS and browser
      $os=nsp_GetOs($userAgent);
      $browser=nsp_GetBrowser($userAgent);

     $exp_referrer=nsp_GetSE($referrer);
     if (isset($exp_referrer)) {
       list($searchengine,$search_phrase)=explode("|",$exp_referrer);
     }
    }

  // Country (ip2nation table) or language
  $countrylang="";
  if($wpdb->get_var("SHOW TABLES LIKE 'ip2nation'") == 'ip2nation') {
    $qry = $wpdb->get_row($wpdb->prepare(
         'SELECT *
          FROM ip2nation
          WHERE ip < INET_ATON( %s )
          ORDER BY ip DESC
          LIMIT 0,1'    
          ,$ipAddress
    ));
    if (isset($qry->country)) {
      $countrylang=$qry->country;
    }  
  }

  if($countrylang == '') {
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
      $countrylang=nsp_GetLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
    }  
  }

  // Auto-delete visits if...
  if(get_option('newstatpress_autodelete') != '') {
    $int = filter_var(get_option('newstatpress_autodelete'), FILTER_SANITIZE_NUMBER_INT);
    # secure action
    if ($int>=1) {
      $t=gmdate('Ymd', current_time('timestamp')-86400*$int*30);

      $results =$wpdb->query($wpdb->prepare( 
         "DELETE FROM $table_name
          WHERE date < %s
          ",
          $t
          ));
    }
  }

  // Auto-delete spiders visits if...
  if(get_option('newstatpress_autodelete_spiders') != '') {
    $int = filter_var(get_option('newstatpress_autodelete_spiders'), FILTER_SANITIZE_NUMBER_INT);

    # secure action
    if ($int>=1) {
      $t=gmdate('Ymd', current_time('timestamp')-86400*$int*30);

      $results =$wpdb->query($wpdb->prepare(
         "DELETE FROM $table_name
          WHERE date < %s and
                feed='' and
                spider<>''
         ",
         $t
         ));
    }
  }

  if ((!is_user_logged_in()) OR (get_option('newstatpress_collectloggeduser')=='checked')) {
    if (is_user_logged_in() AND (get_option('newstatpress_collectloggeduser')=='checked')) {
      $current_user = wp_get_current_user();

      // Is a given name to ignore?
      $to_ignore = get_option('newstatpress_ignore_users', array());
      foreach($to_ignore as $a_filter) {
        if ($current_user->user_login == $a_filter) { return ''; }
      }
    }

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
      nsp_BuildPluginSQLTable('create');
    }

    $login = $userdata ? $userdata->user_login : null;
    
    $results = $wpdb->insert( 
      $table_name, 
       array( 
          'date' => $vdate, 
          'time' => $vtime, 
          'ip' => substr($ipAddress, 0, 39),
          'urlrequested' => substr($urlRequested, 0, 250),
          'agent' => substr(strip_tags($userAgent), 0, 250),
          'referrer' => substr($referrer, 0, 512),
          'search' => substr(strip_tags($search_phrase), 0, 250),
          'nation' => substr($countrylang, 0, 2),
          'os' => substr($os, 0, 30),
          'browser' => substr($browser, 0, 32),
          'searchengine' => substr($searchengine, 0, 16),
          'spider' => substr($spider, 0, 32),
          'feed' => substr($feed, 0, 8),
          'user' => substr($login, 0, 16),
          'timestamp' => $timestamp
      ), array( '%s')
    );   
  }
}
add_action('send_headers', 'nsp_StatAppend');

/**
 * Generate the Ajax code for the given variable
 *
 * @param var variable to get
 * @param limit optional limit value for query
 * @param flag optional flag value for checked
 * @param url optional url address
 ************************************************/
function nsp_generateAjaxVar($var, $limit=0, $flag='', $url='') {
  global $newstatpress_dir;

  wp_enqueue_script('wp_ajax_nsp_variables_'.$var, plugins_url('./includes/js/nsp_variables_'.$var.'.js', __FILE__), array('jquery'));
  wp_localize_script('wp_ajax_nsp_variables_'.$var, 'nsp_variablesAjax_'.$var, array(
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    'postCommentNonce' => wp_create_nonce( 'newstatpress-nsp_variables-nonce' ),
    'VAR'     => $var,
    'URL'     => $url,
    'FLAG'    => $flag,
    'LIMIT'   => $limit    
  ));
  
  $res = "<span id=\"".$var."\">_</span>";
  return $res;
}

/**
 * Return the expanded vars into the give code. API to use for users.
 */
function NewStatPress_Print($body='') {
  return nsp_ExpandVarsInsideCode($body);
}

/**
 * Expand vars into the give code
 *
 * @param body the code where to look for variables to expand
 * @return the modified code
 ************************************************************/
function nsp_ExpandVarsInsideCode($body) {
  global $wpdb;
  $table_name = nsp_TABLENAME;

  $vars_list=array('visits',
                   'yvisits',
                   'mvisits',
                   'wvisits',
                   'totalvisits',
                   'totalpageviews',
                   'todaytotalpageviews',
                   'alltotalvisits',
                   'monthtotalpageviews'
                  );

  # look for $vars_list
  foreach($vars_list as $var) {
    if(strpos(strtolower($body),"%$var%") !== FALSE) {
      $body = str_replace("%$var%", nsp_GenerateAjaxVar($var), $body);
    }
  }
  
  # look for %thistotalvisits%
  if(strpos(strtolower($body),"%thistotalvisits%") !== FALSE) {
    $body = str_replace("%thistotalvisits%", nsp_GenerateAjaxVar("thistotalvisits", 0, '', nsp_URL()), $body);
  }

  # look for %since%
  if(strpos(strtolower($body),"%since%") !== FALSE) {
    // not needs prepare
    $qry = $wpdb->get_var(
      "SELECT date
       FROM $table_name
       ORDER BY date
       LIMIT 1;
      ");
    $body = str_replace("%since%", nsp_hdate($qry), $body);
  }

  # look for %os%
  if(strpos(strtolower($body),"%os%") !== FALSE) {
    $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
    $os=nsp_GetOs($userAgent);
    $body = str_replace("%os%", $os, $body);
  }

  # look for %browser%
  if(strpos(strtolower($body),"%browser%") !== FALSE) {
    $browser=nsp_GetBrowser($userAgent);
    $body = str_replace("%browser%", $browser, $body);
  }

  # look for %ip%
  if(strpos(strtolower($body),"%ip%") !== FALSE) {
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $body = str_replace("%ip%", $ipAddress, $body);
  }

  # look for %visitorsonline%
  if(strpos(strtolower($body),"%visitorsonline%") !== FALSE) {
    $act_time = current_time('timestamp');
    $from_time = date('Y-m-d H:i:s', strtotime('-4 minutes', $act_time));
    $to_time = date('Y-m-d H:i:s', $act_time);
    // use prepare
     $qry = $wpdb->get_var($wpdb->prepare(
      "SELECT count(DISTINCT(ip)) AS visitors
       FROM $table_name
       WHERE
         spider='' AND
         feed='' AND
         date = %s AND
         timestamp BETWEEN %s AND %s;
      ", gmdate("Ymd", $act_time), $from_time, $to_time));   
    $body = str_replace("%visitorsonline%", $qry, $body);
  }

  # look for %usersonline%
  if(strpos(strtolower($body),"%usersonline%") !== FALSE) {
    $act_time = current_time('timestamp');
    $from_time = date('Y-m-d H:i:s', strtotime('-4 minutes', $act_time));
    $to_time = date('Y-m-d H:i:s', $act_time);
    // use prepare
    $qry = $wpdb->get_var($wpdb->prepare(
      "SELECT count(DISTINCT(ip)) AS users
       FROM $table_name
       WHERE
         spider='' AND
         feed='' AND
         date = %s AND
         user<>'' AND
         timestamp BETWEEN %s AND %s;
      ", gmdate("Ymd", $act_time), $from_time, $to_time));
    $body = str_replace("%usersonline%", $qry, $body);
  }

  # look for %toppost%
  if(strpos(strtolower($body),"%toppost%") !== FALSE) {
    // not needs prepare
    $qry = $wpdb->get_row(
      "SELECT urlrequested,count(*) AS totale
       FROM $table_name
       WHERE
         spider='' AND
         feed='' AND
         urlrequested LIKE '%p=%'
       GROUP BY urlrequested
       ORDER BY totale DESC
       LIMIT 1;
      ");
    $body = str_replace("%toppost%", nsp_DecodeURL($qry->urlrequested), $body);
  }

  # look for %topbrowser%
  if(strpos(strtolower($body),"%topbrowser%") !== FALSE) {
    // not needs prepare
    $qry = $wpdb->get_row(
       "SELECT browser,count(*) AS totale
        FROM $table_name
        WHERE
          spider='' AND
          feed=''
        GROUP BY browser
        ORDER BY totale DESC
        LIMIT 1;
       ");
    $body = str_replace("%topbrowser%", nsp_DecodeURL($qry->browser), $body);
  }

  # look for %topos%
  if(strpos(strtolower($body),"%topos%") !== FALSE) {
    // not needs prepare
    $qry = $wpdb->get_row(
      "SELECT os,count(*) AS totale
       FROM $table_name
       WHERE
         spider='' AND
         feed=''
       GROUP BY os
       ORDER BY totale DESC
       LIMIT 1;
      ");
    $body = str_replace("%topos%", nsp_DecodeURL($qry->os), $body);
  }

  # look for %topsearch%
  if(strpos(strtolower($body),"%topsearch%") !== FALSE) {
    // not needs prepare
    $qry = $wpdb->get_row(
      "SELECT search, count(*) AS csearch
       FROM $table_name
       WHERE
         search<>''
       GROUP BY search
       ORDER BY csearch DESC
       LIMIT 1;
      ");
    $body = str_replace("%topsearch%", nsp_DecodeURL($qry->search), $body);
  }

  return $body;
}

// TODO : if working, move the contents into the caller instead of this function
/**
 * Get top posts
 *
 * @param limit: the number of post to show
 * @param showcounts: if checked show totals
 * @return result of extraction
 *******************************************/
function nsp_TopPosts($limit=5, $showcounts='checked') {
  return nsp_GenerateAjaxVar("widget_topposts", $limit, $showcounts);
}


/**
 * Build NewsStatPress Widgets: Stat and TopPosts
 *
 ************************************************/
function nsp_WidgetInit($args) {
  if ( !function_exists('wp_register_sidebar_widget') || !function_exists('wp_register_widget_control') ) return;

  // Statistics Widget control
  function nsp_WidgetStats_control() {
    global $nsp_widget_vars;
    $options = get_option('widget_newstatpress');
    if ( !is_array($options) ) $options = array('title'=>'NewStatPress Stats', 'body'=>'Visits today: %visits%');
    if ( isset($_POST['newstatpress-submit']) && $_POST['newstatpress-submit'] ) {
      $options['title'] = sanitize_text_field($_POST['newstatpress-title']);
      $options['body'] = stripslashes($_POST['newstatpress-body']);
      update_option('widget_newstatpress', $options);
    }
    $title = htmlspecialchars($options['title'], ENT_QUOTES);
    $body = htmlspecialchars($options['body'], ENT_QUOTES);

     // the form
    echo "<p>
            <label for='newstatpress-title'>". __('Title:', 'newstatpress') ."</label>
            <input class='widget-title' id='newstatpress-title' name='newstatpress-title' type='text' value='$title' />
          </p>
          <p>
            <label for='newstatpress-body'>". _e('Body:', 'newstatpress') ."</label>
            <textarea class='widget-body' id='newstatpress-body' name='newstatpress-body' type='textarea' placeholder='Example: Month visits: %mvisits%...'>$body</textarea>
          </p>
          <input type='hidden' id='newstatpress-submit' name='newstatpress-submit' value='1' />
          <p>". __('Stats available: ', 'newstatpress') ."<br/ >
          <span class='widget_varslist'>";
          foreach($nsp_widget_vars as $var) {
              echo "<a href='#'>%$var[0]%  <span>"; _e($var[1], 'newstatpress'); echo "</span></a> | ";
          }
    echo "</span></p>";
  }

  function nsp_WidgetStats($args) {
    extract($args);
    $options = get_option('widget_newstatpress');
    $title = $options['title'];
    $body = $options['body'];
    echo $before_widget;
    print($before_title . $title . $after_title);
    print nsp_ExpandVarsInsideCode($body);
    echo $after_widget;
  }
  wp_register_sidebar_widget('NewStatPress', 'NewStatPress Stats', 'nsp_WidgetStats');
  wp_register_widget_control('NewStatPress', array('NewStatPress','widgets'), 'nsp_WidgetStats_control', 300, 210);

  // Top posts Widget control
  function nsp_WidgetTopPosts_control() {
    $options = get_option('widget_newstatpresstopposts');
    if ( !is_array($options) ) {
      $options = array('title'=>'NewStatPress TopPosts', 'howmany'=>'5', 'showcounts'=>'checked');
    }
    if ( isset($_POST['newstatpresstopposts-submit']) && $_POST['newstatpresstopposts-submit'] ) {
      $options['title'] = sanitize_text_field($_POST['newstatpresstopposts-title']);
      $options['howmany'] = filter_var($_POST['newstatpresstopposts-howmany'], FILTER_SANITIZE_NUMBER_INT);
      $options['showcounts'] = sanitize_text_field($_POST['newstatpresstopposts-showcounts']);
      if($options['showcounts'] == "1") {
        $options['showcounts']='checked';
      }
      update_option('widget_newstatpresstopposts', $options);
    }
    $title = htmlspecialchars($options['title'], ENT_QUOTES);
    $howmany = htmlspecialchars($options['howmany'], ENT_QUOTES);
    $showcounts = htmlspecialchars($options['showcounts'], ENT_QUOTES);
    // the form
    echo "<p style='text-align:right;'>
            <label for='newstatpresstopposts-title'>". __('Title','newstatpress') . "
            <input style='width: 250px;' id='newstatpress-title' name='newstatpresstopposts-title' type='text' value=$title />
            </label>
          </p>
          <p style='text-align:right;'>
            <label for='newstatpresstopposts-howmany'>". __('Limit results to','newstatpress') ."
            <input style='width: 100px;' id='newstatpresstopposts-howmany' name='newstatpresstopposts-howmany' type='text' value=$howmany />
            </label>
          </p>";
    echo '<p style="text-align:right;"><label for="newstatpresstopposts-showcounts">' . __('Visits','newstatpress') . ' <input id="newstatpresstopposts-showcounts" name="newstatpresstopposts-showcounts" type=checkbox value="checked" '.$showcounts.' /></label></p>';
    echo '<input type="hidden" id="newstatpress-submitTopPosts" name="newstatpresstopposts-submit" value="1" />';
  }
  function nsp_WidgetTopPosts($args) {
    extract($args);
    $options = get_option('widget_newstatpresstopposts');
    $title = htmlspecialchars($options['title'], ENT_QUOTES);
    $howmany = htmlspecialchars($options['howmany'], ENT_QUOTES);
    $showcounts = htmlspecialchars($options['showcounts'], ENT_QUOTES);
    echo $before_widget;
    print($before_title . $title . $after_title);
    print nsp_TopPosts($howmany,$showcounts);
    echo $after_widget;
  }
  wp_register_sidebar_widget('NewStatPress TopPosts', 'NewStatPress TopPosts', 'nsp_WidgetTopPosts');
  wp_register_widget_control('NewStatPress TopPosts', array('NewStatPress TopPosts','widgets'), 'nsp_WidgetTopPosts_control', 300, 110);
}
add_action('plugins_loaded', 'nsp_WidgetInit');


function nsp_CalculateVariation($month,$lmonth) {

  $target = round($month / (
    (date("d", current_time('timestamp')) - 1 +
    (date("H", current_time('timestamp')) +
    (date("i", current_time('timestamp')) + 1)/ 60.0) / 24.0)) * date("t", current_time('timestamp'))
  );

  $monthchange = null;
  $added = null;

  if($lmonth <> 0) {
    $percent_change = round( 100 * ($month / $lmonth ) - 100,1);
    $percent_target = round( 100 * ($target / $lmonth ) - 100,1);

    if($percent_change >= 0) {
      $percent_change=sprintf("+%'04.1f", $percent_change);
      $monthchange = "<td class='coll'><code style='color:green'>($percent_change%)</code></td>";
    }
    else {
      $percent_change=sprintf("%'05.1f", $percent_change);
      $monthchange = "<td class='coll'><code style='color:red'>($percent_change%)</code></td>";
    }

    if($percent_target >= 0) {
      $percent_target=sprintf("+%'04.1f", $percent_target);
      $added = "<td class='coll'><code style='color:green'>($percent_target%)</code></td>";
    }
    else {
      $percent_target=sprintf("%'05.1f", $percent_target);
      $added = "<td class='coll'><code style='color:red'>($percent_target%)</code></td>";
    }
  }
  else {
    $monthchange = "<td></td>";
    $added = "<td class='coll'></td>";
  }

  $calculated_result=array($monthchange,$target,$added);
  return $calculated_result;
}

function nsp_MakeOverview($print ='dashboard') {

  global $wpdb, $nsp_option_vars;
  $table_name = nsp_TABLENAME;

  $overview_table='';
  global $nsp_option_vars;
  $offsets = get_option($nsp_option_vars['stats_offsets']['name']);

  // $since = NewStatPress_Print('%since%');
  $since = nsp_ExpandVarsInsideCode('%since%');
  $lastmonth = nsp_Lastmonth();
  $thisyear = gmdate('Y', current_time('timestamp'));
  $thismonth = gmdate('Ym', current_time('timestamp'));
  $yesterday = gmdate('Ymd', current_time('timestamp')-86400);
  $today = gmdate('Ymd', current_time('timestamp'));
  $tlm[0]=substr($lastmonth,0,4); $tlm[1]=substr($lastmonth,4,2);

  $thisyearHeader = gmdate('Y', current_time('timestamp'));
  $lastmonthHeader = gmdate('M, Y',gmmktime(0,0,0,$tlm[1],1,$tlm[0]));
  $thismonthHeader = gmdate('M, Y', current_time('timestamp'));
  $yesterdayHeader = gmdate('d M', current_time('timestamp')-86400);
  $todayHeader = gmdate('d M', current_time('timestamp'));

  // build head table overview
  if ($print=='main') {
    //$overview_table.="<div class='wrap'><h2>". __('Overview','newstatpress'). "</h2>";
    $overview_table.="<table class='widefat center nsp'>
              <thead>
              <tr class='sup'>
                <th></th>
                <th>". __('Total since','newstatpress'). "</th>
                <th scope='col'>". __('This year','newstatpress'). "</th>
                <th scope='col'>". __('Last month','newstatpress'). "</th>
                <th scope='col' colspan='2'>". __('This month','newstatpress'). "</th>
                <th scope='col' colspan='2'>". __('Target This month','newstatpress'). "</th>
                <th scope='col'>". __('Yesterday','newstatpress'). "</th>
                <th scope='col'>". __('Today','newstatpress'). "</th>
              </tr>
              <tr class='inf'>
                <th></th>
                <th><span>$since</span></th>
                <th><span>$thisyearHeader</span></th>
                <th><span>$lastmonthHeader</span></th>
                <th colspan='2'><span > $thismonthHeader </span></th>
                <th colspan='2'><span > $thismonthHeader </span></th>
                <th><span>$yesterdayHeader</span></th>
                <th><span>$todayHeader</span></th>
              </tr></thead>
              <tbody class='overview-list'>";
  }
  elseif ($print=='dashboard') {
   $overview_table.="<table class='widefat center nsp'>
                      <thead>
                      <tr class='sup dashboard'>
                      <th></th>
                          <th scope='col'>". __('M-1','newstatpress'). "</th>
                          <th scope='col' colspan='2'>". __('M','newstatpress'). "</th>
                          <th scope='col'>". __('Y','newstatpress'). "</th>
                          <th scope='col'>". __('T','newstatpress'). "</th>
                      </tr>
                      <tr class='inf dashboard'>
                      <th></th>
                          <th><span>$lastmonthHeader</span></th>
                          <th colspan='2'><span > $thismonthHeader </span></th>
                          <th><span>$yesterdayHeader</span></th>
                          <th><span>$todayHeader</span></th>
                      </tr></thead>
                      <tbody class='overview-list'>";
  }

  // build body table overview
  $overview_rows=array('visitors','visitors_feeds','pageview','feeds','spiders');

  foreach ($overview_rows as $row) {

    switch($row) {

      case 'visitors' :
        $row2='DISTINCT ip';
        $row_title=__('Visitors','newstatpress');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed='' AND spider=''";
      break;

      case 'visitors_feeds' :
        $row2='DISTINCT ip';
        $row_title=__('Visitors through Feeds','newstatpress');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed<>'' AND spider='' AND agent<>''";
        break;

      case 'pageview' :
        $row2='date';
        $row_title=__('Pageviews','newstatpress');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed='' AND spider=''";
      break;

      case 'spiders' :
        $row2='date';
        $row_title=__('Spiders','newstatpress');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed='' AND spider<>''";
      break;

      case 'feeds' :
        $row2='date';
        $row_title=__('Pageviews through Feeds','newstatpress');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed<>'' AND spider=''";
      break;
    }

    // query requests
    // not needs prepare
    $qry_total = $wpdb->get_row($sql_QueryTotal);
    // use prepare
    $qry_tyear = $wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date LIKE %s", $thisyear.'%'));

    if (get_option($nsp_option_vars['calculation']['name'])=='sum') {

      // alternative calculation by mouth: sum of unique visitors of each day
      $tot=0;
      $t = getdate(current_time('timestamp'));
      $year = $t['year'];
      $month = sprintf('%02d', $t['mon']);
      $day= $t['mday'];
      $totlm=0;

      for($k=$t['mon'];$k>0;$k--)
      {
        //current month

      }
      for($i=0;$i<$day;$i++)
      {
        // use prepare
        $qry_daylmonth = $wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date LIKE %s", $lastmonth.$i.'%' ));
        $qry_day       = $wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date LIKE %s", $year.$month.$i.'%' ));
        $tot+=$qry_day->$row;
        $totlm+=$qry_daylmonth->$row;

      }
      // echo $totlm." ,";
      $qry_tmonth->$row=$tot;
      $qry_lmonth->$row=$totlm;

    }
    else { // classic
      // use prepare
      $qry_tmonth = $wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date LIKE %s", $thismonth.'%'));
      $qry_lmonth = $wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date LIKE %s", $lastmonth.'%'));
    }

    // use prepare
    $qry_y = $wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date LIKE %s", $yesterday));
    $qry_t = $wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date LIKE %s", $today));

    $calculated_result=nsp_CalculateVariation($qry_tmonth->$row, $qry_lmonth->$row);

    switch($row) {
      case 'visitors' :
        $qry_total->$row=$qry_total->$row+$offsets['alltotalvisits'];
        break;
      case 'visitors_feeds' :
        $qry_total->$row=$qry_total->$row+$offsets['visitorsfeeds'];
        break;
      case 'pageview' :
        $qry_total->$row=$qry_total->$row+$offsets['pageviews'];
        break;
      case 'spiders' :
        $qry_total->$row=$qry_total->$row+$offsets['spy'];
        break;
      case 'feeds' :
        $qry_total->$row=$qry_total->$row+$offsets['pageviewfeeds'];
        break;
    }

    // build full current row
    $overview_table.="<tr><td class='row_title $row'>$row_title</td>";
    if ($print=='main')
      	$overview_table.="<td class='colc'>".$qry_total->$row."</td>\n";
    if ($print=='main')
      $overview_table.="<td class='colc'>".$qry_tyear->$row."</td>\n";
    $overview_table.="<td class='colc'>".$qry_lmonth->$row."</td>\n";
    $overview_table.="<td class='colr'>".$qry_tmonth->$row. $calculated_result[0] ."</td>\n";
    if ($print=='main')
      $overview_table.="<td class='colr'> $calculated_result[1] $calculated_result[2] </td>\n";
    $overview_table.="<td class='colc'>".$qry_y->$row."</td>\n";
    $overview_table.="<td class='colc'>".$qry_t->$row."</td>\n";
    $overview_table.="</tr>";
  }

  if ($print=='dashboard'){
    $overview_table.="</tr></table>";
  }

  if ($print=='main'){
    $overview_table.= "</tr></table>\n";

    // print graph
    //  last "N" days graph  NEW
    $gdays=get_option('newstatpress_daysinoverviewgraph'); if($gdays == 0) { $gdays=20; }
    $start_of_week = get_option('start_of_week');

    $maxxday = 0;
    for($gg=$gdays-1;$gg>=0;$gg--) {

      $date=gmdate('Ymd', current_time('timestamp')-86400*$gg);

      // use prepare
      $qry_visitors  = $wpdb->get_var($wpdb->prepare("SELECT count(DISTINCT ip) AS total FROM $table_name WHERE feed='' AND spider='' AND date = %s", $date));
      $visitors[$gg] = $qry_visitors;

      $qry_pageviews = $wpdb->get_var($wpdb->prepare("SELECT count(date) AS total FROM $table_name WHERE feed='' AND spider='' AND date = %s", $date));
      $pageviews[$gg]= $qry_pageviews;

      $qry_spiders   = $wpdb->get_var($wpdb->prepare("SELECT count(date) AS total FROM $table_name WHERE feed='' AND spider<>'' AND date = %s", $date));
      $spiders[$gg]  = $qry_spiders;

      $qry_feeds     = $wpdb->get_var($wpdb->prepare("SELECT count(date) AS total FROM $table_name WHERE feed<>'' AND spider='' AND date = %s", $date));
      $feeds[$gg]    = $qry_feedsl;

      $total= $visitors[$gg] + $pageviews[$gg] + $spiders[$gg] + $feeds[$gg];
      if ($total > $maxxday) $maxxday= $total;
    }

    if($maxxday == 0) { $maxxday = 1; }
    # Y
    $gd=(90/$gdays).'%';

    $overview_graph="<table class='graph'><tr>";

    for($gg=$gdays-1;$gg>=0;$gg--) {

      $scale_factor=2; //2 : 200px in CSS

      $date=gmdate('Ymd', current_time('timestamp')-86400*$gg);

      $px_visitors = $scale_factor*(round($visitors[ $gg]*100/$maxxday));
      $px_pageviews= $scale_factor*(round($pageviews[$gg]*100/$maxxday));
      $px_spiders  = $scale_factor*(round($spiders[  $gg]*100/$maxxday));
      $px_feeds    = $scale_factor*(round($feeds[    $gg]*100/$maxxday));

      $px_white = $scale_factor*100 - $px_feeds - $px_spiders - $px_pageviews - $px_visitors;

      $overview_graph.="<td width='$gd' valign='bottom'>";

      $overview_graph.="<div class='overview-graph'>
        <div style='border-left:1px; background:#ffffff;width:100%;height:".$px_white."px;'></div>
        <div class='visitors_bar' style='height:".$px_visitors."px;' title='".$visitors[$gg]." ".__('Visitors','newstatpress')."'></div>
        <div class='web_bar' style='height:".$px_pageviews."px;' title='".$pageviews[$gg]." ".__('Pageviews','newstatpress')."'></div>
        <div class='spiders_bar' style='height:".$px_spiders."px;' title='".$spiders[$gg]." ".__('Spiders','newstatpress')."'></div>
        <div class='feeds_bar' style='height:".$px_feeds."px;' title='".$feeds[$gg]." ".__('Feeds','newstatpress')."'></div>
        <div style='background:gray;width:100%;height:1px;'></div>";
        if($start_of_week == gmdate('w',current_time('timestamp')-86400*$gg)) $overview_graph.="<div class='legend-W'>";
        else $overview_graph.="<div class='legend'>";
        $overview_graph.=gmdate('d', current_time('timestamp')-86400*$gg) . ' ' . gmdate('M', current_time('timestamp')-86400*$gg) .     "</div></div></td>\n";
    }
    $overview_graph.="</tr></table>";

    $overview_table=$overview_table.$overview_graph;
  }

  if ($print!=FALSE) print $overview_table;
  else return $overview_table;
}

register_activation_hook(__FILE__,'nsp_BuildPluginSQLTable');
?>
