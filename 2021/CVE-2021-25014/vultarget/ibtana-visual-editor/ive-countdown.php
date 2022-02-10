<?php

class WP_Ivecountdown {
	var $plugin_options_slug = 'ive-countdown';

	var $options_name = 'WP_TMC_options';

	/**
	 * @var array
	 */

	var $options = array(
		'displayyears'   => 'false',
		'displaymonths'  => 'false',
		'displayweeks'		=> 'false',
		'displayseconds'	=> 'false',
		'yearlabel'		=> 'Years',
		'monthlabel'	=> 'Months',
		'weeklabel'		=> 'Weeks',
		'daylabel'		=> 'Days',
		'hourlabel'		=> 'Hours',
		'minutelabel' => 'Minutes',
		'secondlabel' => 'Seconds',
		'custom_css' 	=> '',
		'backgroundColor' 	=> '#000000',
		'textColor' 	=> '#ffffff',
		'borderColor' 	=> '#ffffff',
		'borderRadius' 	=> 5,
		'borderWidth' 	=> 0,
		'typography' 	=> '',
		'fontWeight' 	=> '400',
		'fontStyle' 	=> 'normal',
		'fontSubset' 	=> '',
		'googleFont' 	=> 'false',
		'loadGoogleFont' 	=> 'true',
		'fontVariant' 	=> '',
		'desklabelFontSize' 	=> 11,
		'tablabelFontSize' 	=> 11,
		'moblabelFontSize' 	=> 8,
		'desknumberFontSize' 	=> 24,
		'tabnumberFontSize' 	=> 24,
		'mobnumberFontSize' 	=> 18,
		'letterSpacing' 	=> 0,
		'marginLR' 	=> 5,
		'marginTB' 	=> 5,
		'deskAlign' 	=> 'left',
		'tabAlign' 	=> 'left',
		'mobAlign' 	=> 'left',
		'tabAlignment' 	=> 'left',
		'blockAlignment' 	=> 'center',
		'deskWidth' => 72,
		'tabWidth' => 72,
		'mobWidth' => 72,
		'deskHeight' => 62,
		'tabHeight' => 62,
		'mobHeight' => 62,
	);


	function __construct() {
		$this->_set_options();

		// add actions
		add_action( 'init', array( $this, 'register_ive_block' ) );
		add_action( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'plugin_actions' ) );
		add_action( 'wp_head', array( $this, 'plugin_head_inject' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'countdown_scripts' ) );
		add_action( 'plugins_loaded', array( $this, 'ive_load_textdomain' ) );

		add_action( 'wp_ajax_iveevents', array( $this, 'iveevents_callback' ) );
		add_action( 'wp_ajax_nopriv_iveevents', array( $this, 'iveevents_callback') );

		//rest endpoints
		add_action( 'rest_api_init',  array( $this, 'rest_ive_endpoints' ) );

		// the shortcode
		add_shortcode('ive', array( $this, 'ive_shortcode') );
	}

	function ive_load_textdomain() {
		load_plugin_textdomain( 'ive-countdown' );
	}

	/**
 	* Register ive block
 	*/
	function register_ive_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}

		wp_register_script(
			'wp-blocks',
			plugin_dir_url(__FILE__) . 'block.js',
			array(
				'wp-blocks',
				'wp-element',
				'wp-components',
				'wp-i18n',
				'wp-editor',
			),
			'0.2',
			true
		);

		$ive_options = array(
			'displayyears' => $this->options['displayyears'],
			'displaymonths' => $this->options['displaymonths'],
			'displayweeks' => $this->options['displayweeks'],
			'displayseconds' => $this->options['displayseconds'],
			'yearlabel' => $this->options['yearlabel'],
			'monthlabel' => $this->options['monthlabel'],
			'weeklabel' => $this->options['weeklabel'],
			'daylabel' => $this->options['daylabel'],
			'hourlabel' => $this->options['hourlabel'],
			'minutelabel' => $this->options['minutelabel'],
			'secondlabel' => $this->options['secondlabel'],
			'backgroundColor' => $this->options['backgroundColor'],
			'textColor' => $this->options['textColor'],
			'borderColor' => $this->options['borderColor'],
			'borderRadius' => $this->options['borderRadius'],
			'borderWidth' => $this->options['borderWidth'],
			'desknumberFontSize' => $this->options['desknumberFontSize'],
			'tabnumberFontSize' => $this->options['tabnumberFontSize'],
			'mobnumberFontSize' => $this->options['mobnumberFontSize'],
			'desklabelFontSize' => $this->options['desklabelFontSize'],
			'tablabelFontSize' => $this->options['tablabelFontSize'],
			'moblabelFontSize' => $this->options['moblabelFontSize'],
			'typography' => $this->options['typography'],
			'fontWeight' => $this->options['fontWeight'],
			'googleFont' => $this->options['googleFont'],
			'loadGoogleFont' => $this->options['loadGoogleFont'],
			'fontVariant' => $this->options['fontVariant'],
			'fontStyle' => $this->options['fontStyle'],
			'fontSubset' => $this->options['fontSubset'],
			'letterSpacing' => $this->options['letterSpacing'],
			'marginLR' => $this->options['marginLR'],
			'marginTB' => $this->options['marginTB'],
			'deskAlign' => $this->options['deskAlign'],
			'tabAlign' => $this->options['tabAlign'],
			'mobAlign' => $this->options['mobAlign'],
			'tabAlignment' => $this->options['tabAlignment'],
			'blockAlignment' => $this->options['blockAlignment'],
			'deskWidth' => $this->options['deskWidth'],
			'tabWidth' => $this->options['tabWidth'],
			'mobWidth' => $this->options['mobWidth'],
			'deskHeight' => $this->options['deskHeight'],
			'tabHeight' => $this->options['tabHeight'],
			'mobHeight' => $this->options['mobHeight'],
		);

		//pass default options to js
		wp_localize_script('wp-blocks', 'ive_options', $ive_options );

		register_block_type( 'ive/countdown', array(
			'editor_script' => 'wp-blocks',
			'render_callback' => [$this, 'ive_countdown_callback'],
			'attributes' => array(
														'content' => array('type' => 'string'),
														'id' => array ('type' => 'string'),
														'style' => array ('type' => 'string', 'default' => 'suzuki'),
														't' => array ('type' => 'number'),
														'timestr' => array ('type' => 'string'),
														'launchtarget' => array ('type' => 'string'),
													  'secs' => array ('type' => 'string', 'default' => '00'),
														'displayyears' => array ('type' => 'boolean', 'default' => $this->options['displayyears']),
														'displaymonths' => array ('type' => 'boolean', 'default' => $this->options['displaymonths']),
														'displayweeks' => array ('type' => 'boolean', 'default' => $this->options['displayweeks']),
														'displayseconds' => array ('type' => 'boolean', 'default' => $this->options['displayseconds']),
														'yearlabel' => array ('type' => 'string', 'default' => $this->options['yearlabel']),
														'monthlabel' => array ('type' => 'string', 'default' => $this->options['monthlabel']),
														'weeklabel' => array ('type' => 'string', 'default' => $this->options['weeklabel']),
														'daylabel' => array ('type' => 'string', 'default' => $this->options['daylabel']),
														'hourlabel' => array ('type' => 'string', 'default' => $this->options['hourlabel']),
														'minutelabel' => array ('type' => 'string', 'default' => $this->options['minutelabel']),
														'secondlabel' => array ('type' => 'string', 'default' => $this->options['secondlabel']),
														'backgroundColor' => array ('type' => 'string', 'default' => $this->options['backgroundColor']),
														'textColor' => array ('type' => 'string', 'default' => $this->options['textColor']),
														'borderColor' => array ('type' => 'string', 'default' => $this->options['borderColor']),
														'borderRadius' => array ('type' => 'number', 'default' => $this->options['borderRadius']),
														'borderWidth' => array ('type' => 'number', 'default' => $this->options['borderWidth']),
														'typography' => array ('type' => 'string', 'default' => $this->options['typography']),
														'fontWeight' => array ('type' => 'number', 'default' => $this->options['fontWeight']),
														'googleFont' => array ('type' => 'boolean', 'default' => $this->options['googleFont']),
														'loadGoogleFont' => array ('type' => 'boolean', 'default' => $this->options['loadGoogleFont']),
														'fontVariant' => array ('type' => 'string', 'default' => $this->options['fontVariant']),
														'fontStyle' => array ('type' => 'string', 'default' => $this->options['fontStyle']),
														'fontSubset' => array ('type' => 'string', 'default' => $this->options['fontSubset']),
														'desklabelFontSize' => array ('type' => 'number', 'default' => $this->options['desklabelFontSize']),
														'tablabelFontSize' => array ('type' => 'number', 'default' => $this->options['tablabelFontSize']),
														'moblabelFontSize' => array ('type' => 'number', 'default' => $this->options['moblabelFontSize']),
														'desknumberFontSize' => array ('type' => 'number', 'default' => $this->options['desknumberFontSize']),
														'tabnumberFontSize' => array ('type' => 'number', 'default' => $this->options['tabnumberFontSize']),
														'mobnumberFontSize' => array ('type' => 'number', 'default' => $this->options['mobnumberFontSize']),
														'letterSpacing' => array ('type' => 'number', 'default' => $this->options['letterSpacing']),
														'marginLR' => array ('type' => 'number', 'default' => $this->options['marginLR']),
														'marginTB' => array ('type' => 'number', 'default' => $this->options['marginTB']),
														'deskAlign' => array ('type' => 'string', 'default' => $this->options['deskAlign']),
														'tabAlign' => array ('type' => 'string', 'default' => $this->options['tabAlign']),
														'mobAlign' => array ('type' => 'string', 'default' => $this->options['mobAlign']),
														'tabAlignment' => array ('type' => 'string', 'default' => $this->options['tabAlignment']),
														'blockAlignment' => array ('type' => 'string', 'default' => $this->options['blockAlignment']),
														'deskWidth' => array ('type' => 'number', 'default' => $this->options['deskWidth']),
														'tabWidth' => array ('type' => 'number', 'default' => $this->options['tabWidth']),
														'mobWidth' => array ('type' => 'number', 'default' => $this->options['mobWidth']),
														'deskHeight' => array ('type' => 'number', 'default' => $this->options['deskHeight']),
														'tabHeight' => array ('type' => 'number', 'default' => $this->options['tabHeight']),
														'mobHeight' => array ('type' => 'number', 'default' => $this->options['mobHeight']),
											)
		) );
	}

	//rest  endpoints: /wp-json/ive/v1/now
	function rest_ive_endpoints() {
    $namespace = 'ive/v1';
    register_rest_route( $namespace, '/now', array(
			'methods'   => 'GET',
			'callback'  => array( $this, 'rest_now_handler'),
			'permission_callback' => '__return_true'
    ));
	}

	function rest_now_handler( ) {
		$now = new DateTime( null, $this->get_wp_timezone() );
		$result = new WP_REST_Response( $now, 200 );
		$result->set_headers(array('Cache-Control' => 'no-cache'));
		return $result;
	}

	// Add link to options page from plugin list
	function plugin_actions($links) {
		$new_links = array();
		$new_links[] = '<a href="options-general.php?page='.$this->plugin_options_slug.'">' . __('Settings', 'ive-countdown') . '</a>';
		return array_merge($new_links, $links);
	}

	//plugin header inject
	function plugin_head_inject(){
		// custom css
		if( !empty( $this->options['custom_css'] ) ){
			echo "<style>\n";
			echo $this->options['custom_css'];
			echo "\n</style>\n";
		}
	}

	//load front-end countdown scripts
	function countdown_scripts() {

		$post = get_post();
    if ( !is_object( $post ) ) {
      return false;
    }

		$is_ive_countdown_block_exists = false;

		if ( has_blocks( $post->post_content ) ) {
			$blocks = parse_blocks( $post->post_content );

			foreach ( $blocks as $key => $block_single ) {
				$block_single_blockName  = $block_single['blockName'];
				$block_single_blockName_arr = explode( '/', $block_single_blockName );
				if ( ( $block_single_blockName_arr[0] === 'ive' ) && ( 'countdown' === $block_single_blockName_arr[1] ) ) {
					$is_ive_countdown_block_exists = true;
					break;
				}
			}
		}

		if ( !$is_ive_countdown_block_exists ) {
			return;
		}

		if (function_exists( 'is_checkout' )) {
			if (is_checkout()) {
				return;
			}
		}
		$plugin_url = plugins_url() .'/'. dirname( plugin_basename(__FILE__) );

		//tCountdown script
		wp_register_script( 'countdown-script', $plugin_url.'/js/jquery.ive-countdown.min.js', array ('jquery'), '2.4.5', 'true' );
		// callback for t(-) events
		$response = array( 'now' => date( 'n/j/Y H:i:s', strtotime(current_time('mysql'))));
		wp_localize_script( 'countdown-script', 'iveCountAjax', array(
			'ajaxurl' 		 =>	admin_url( 'admin-ajax.php' ),
			'api_nonce'		 => wp_create_nonce( 'wp_rest' ),
			'api_url'		 => site_url('/wp-json/ive/v1/'),
			'countdownNonce' => wp_create_nonce( 'tountajax-countdownonce-nonce' ),
			'ivenow'         => json_encode($response)
		));
		wp_enqueue_script('countdown-script');
	}


	/**
	 * Admin options page
	 */
	function options_page() {


	}

	function _set_options() {
		// set options
		$saved_options = get_option( $this->options_name );

		// set all options
		if ( ! empty( $saved_options ) ) {
				foreach ( $this->options AS $key => $option ) {
						if(isset($saved_options[ $key ])){
							  if(is_array($saved_options[ $key ])){
									$this->options[ $key ] = ( empty( $saved_options[ $key ][0] ) ) ? '' : $saved_options[ $key ][0];
								}
								else{
									$this->options[ $key ] = ( empty( $saved_options[ $key ] ) ) ? '' : $saved_options[ $key ];
								}
						}
				}
		}
	}

	function get_wp_timezone() {
	    $tzstring = get_option( 'timezone_string' );
	    $offset   = get_option( 'gmt_offset' );

			if( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ){
	        $offset_st = $offset > 0 ? "-$offset" : '+'.absint( $offset );
	        $tzstring  = 'Etc/GMT'.$offset_st;
	    }

	    if( empty( $tzstring ) ){
	        $tzstring = 'UTC';
	    }

	    $timezone = new DateTimeZone( $tzstring );
	    return $timezone;
	}

	function ive_countdown_callback($atts) {

		if(empty($atts['content'])){
			$atts['content'] = '';
		}
		if(empty($atts['launchtarget'])){
			$atts['launchtarget'] = 'countdown';
		}
		$style = $atts['style'];
		$timestamp = new DateTime( null, $this->get_wp_timezone() );

		/*
		$timezone = get_option('timezone_string');
		if(!empty($atts['timezone'])){
			$timezone = $atts['timezone'];
		}
		*/

		if(!empty($atts['timestr'])){
			$t = $atts['timestr'];
		}
		else if(!empty($atts['t'])){
			$timestamp->setTimestamp($atts['t']);
			$t = $timestamp->format('Y-m-d H:i').':'.$atts['secs'];
		}
		else{
			$t = '+ 1 day';
		}

		$display_str = '';
		if($atts['displayyears'] === true){
			$display_str .= 'displayyears="true"';
		}
		if($atts['displaymonths'] === true){
			$display_str .= ' displaymonths="true"';
		}
		if($atts['displayweeks'] === true){
			$display_str .= ' displayweeks="true"';
		}
		if($atts['displayseconds'] === true){
			$display_str .= ' displayseconds="true"';
		}
		//var_dump($display_str);

		return do_shortcode('[ive t="'.$t.'" style="'.$style.'" '.$display_str.' years="'.$atts['yearlabel'].'" months="'.$atts['monthlabel'].'" weeks="'.$atts['weeklabel'].'" days="'.$atts['daylabel'].'" hours="'.$atts['hourlabel'].'" minutes="'.$atts['minutelabel'].'" seconds="'.$atts['secondlabel'].'" launchtarget="'.$atts['launchtarget'].'" backgroundcolor="'.$atts['backgroundColor'].'" textcolor="'.$atts['textColor'].'" bordercolor="'.$atts['borderColor'].'" borderradius="'.$atts['borderRadius'].'" borderwidth="'.$atts['borderWidth'].'" desklabelfontsize="'.$atts['desklabelFontSize'].'" typography="'.$atts['typography'].'" fontweight="'.$atts['fontWeight'].'" fontstyle="'.$atts['fontStyle'].'" desknumberfontsize="'.$atts['desknumberFontSize'].'" letterspacing="'.$atts['letterSpacing'].'" marginlr="'.$atts['marginLR'].'" margintb="'.$atts['marginTB'].'" deskalign="'.$atts['deskAlign'].'" mobalign="'.$atts['mobAlign'].'" tabalign="'.$atts['tabAlign'].'" tabalignment="'.$atts['tabAlignment'].'" blockalignment="'.$atts['blockAlignment'].'" tablabelfontsize="'.$atts['tablabelFontSize'].'" tabnumberfontsize="'.$atts['tabnumberFontSize'].'" moblabelfontsize="'.$atts['moblabelFontSize'].'" mobnumberfontsize="'.$atts['mobnumberFontSize'].'"  deskwidth="'.$atts['deskWidth'].'" deskheight="'.$atts['deskHeight'].'" tabwidth="'.$atts['tabWidth'].'"  tabheight="'.$atts['tabHeight'].'"  mobwidth="'.$atts['mobWidth'].'" mobheight="'.$atts['mobHeight'].'"]'.$atts['content'].'[/ive]');
	}

	//the shortcode
	function ive_shortcode($atts, $content=null) {
		//find a random number, if no id was assigned
		$ran = uniqid();
	    extract(shortcode_atts(array(
			'id' => $ran,
			't' => '',
			//'timezone' => get_option('timezone_string'),
			'years' => $this->options['yearlabel'],
			'months' => $this->options['monthlabel'],
			'weeks' => $this->options['weeklabel'],
			'days' => $this->options['daylabel'],
			'hours' => $this->options['hourlabel'],
			'minutes' => $this->options['minutelabel'],
			'seconds' => $this->options['secondlabel'],
			'displayyears' => $this->options['displayyears'],
			'displaymonths' => $this->options['displaymonths'],
			'displayweeks' => $this->options['displayweeks'],
			'displayseconds' => $this->options['displayseconds'],
			'backgroundcolor' => $this->options['backgroundColor'],
			'textcolor' => $this->options['textColor'],
			'bordercolor' => $this->options['borderColor'],
			'borderradius' => $this->options['borderRadius'],
			'borderwidth' => $this->options['borderWidth'],
			'typography' => $this->options['typography'],
			'fontstyle' => $this->options['fontStyle'],
			'fontweight' => $this->options['fontWeight'],
			'desklabelfontsize' => $this->options['desklabelFontSize'],
			'tablabelfontsize' => $this->options['tablabelFontSize'],
			'moblabelfontsize' => $this->options['moblabelFontSize'],
			'desknumberfontsize' => $this->options['desknumberFontSize'],
			'tabnumberfontsize' => $this->options['tabnumberFontSize'],
			'mobnumberfontsize' => $this->options['mobnumberFontSize'],
			'marginlr' => $this->options['marginLR'],
			'margintb' => $this->options['marginTB'],
			'deskalign' => $this->options['deskAlign'],
			'tabalign' => $this->options['tabAlign'],
			'mobalign' => $this->options['mobAlign'],
			'letterspacing' => $this->options['letterSpacing'],
			'tabalignment' => $this->options['tabAlignment'],
			'blockalignment' => $this->options['blockAlignment'],
			'deskwidth' => $this->options['deskWidth'],
			'tabwidth' => $this->options['deskWidth'],
			'mobwidth' => $this->options['deskWidth'],
			'deskheight' => $this->options['deskHeight'],
			'tabheight' => $this->options['deskHeight'],
			'mobheight' => $this->options['deskHeight'],
			'style' => 'suzuki',
			'before' => '',
			'after' => '',
			'width' => 'auto',
			'height' => 'auto',
			'launchwidth' => 'auto',
			'launchheight' => 'auto',
			'launchtarget' => 'countdown',
			'event_id' => '',
			'debug' => '',
		), $atts, 'ive' ));

		if(empty($t)){
			return;
		}

		//insert some style into your life
		$style_file_url = plugins_url('/css/'.$style.'/style.css', __FILE__);

		if ( file_exists( __DIR__ .'/css/'.$style.'/style.css' ) ) {
			if (! wp_style_is( 'countdown-'.$style.'-css', 'registered' )) {
				wp_register_style( 'countdown-'.$style.'-css', $style_file_url, array(), '2.0');
			}
			wp_enqueue_style( 'countdown-'.$style.'-css' );
		}

		//$now = new DateTime( );
		$now = new DateTime( null, $this->get_wp_timezone() );

		// deal with this_year and this_easter
		if(stristr($t, '%') != FALSE){
			$scode = array('%this_year%', '%this_easter%');
			$swap = array(date('Y'), date('Y-m-d', easter_date(date('Y'))));

			if( strtotime( str_replace($scode, $swap, $t)) <  strtotime("now") ){
				$swap = array(date('Y', strtotime('+1 year')), date('Y-m-d', easter_date(date('Y', strtotime('+1 year')))));
			}
			$t = str_replace($scode, $swap, $t);
		}


		$target = new DateTime( $t, $this->get_wp_timezone() );
		$tomorrow_target = $target;

		$diffSecs = $target->getTimestamp() - $now->getTimestamp();

		$day = $target->format('d');
		$month = $target->format('m');
		$year = $target->format('Y');
		$hour = $target->format('H');
		$min = $target->format('i');
		$sec = $target->format('s');

		// interval
		$interval = $now->diff($target);

		// next digits
		$pop_day = new DateInterval('P1D');

		$tomorrow_target->sub($pop_day);
		$tomorrow_interval = $now->diff($tomorrow_target);

		// countdown digits
		$date_arr = array(
			'secs' => $interval->s,
			'mins' => $interval->i,
			'hours' => $interval->h,
			'days' => $interval->d,
			'months' => $interval->m,
			'years' => $interval->y,
	 	);

		$current_hours = $interval->h;
		$current_minutes = $interval->i;

		$next_arr = array(
			'next_day' => $tomorrow_interval->d
		);

		if($interval->m != $tomorrow_interval->m){
			$next_arr['next_month'] = $tomorrow_interval->m;
		}

		if($interval->y != $tomorrow_interval->y){
			$next_arr['next_year'] = $tomorrow_interval->y;
		}

	  // deal with display years
		if(!empty($interval->y) && $displayyears != 'true'){
			// if no months, calculate everyting with total days
			if($displaymonths != 'true'){
				$date_arr['days'] = $interval->days;
				$next_arr['next_day'] = $tomorrow_interval->days;
			}
			// add years to months.
			else{
				$date_arr['months'] = $date_arr['months'] + ($interval->y * 12);
				if(isset($next_arr['next_month'])){
					$next_arr['next_month'] = $next_arr['next_month'] + ($tomorrow_interval->y * 12);
				}
				else{
					$next_arr['next_month'] = ($tomorrow_interval->y * 12);
				}

			}
		}

		// deal with display months
		if(!empty($date_arr['months']) && $displaymonths != 'true'){
			if(!empty($interval->y) && $displayyears == 'true'){
				$pop_years = new DateInterval('P'.$interval->y.'Y');
				$adjusted_target = $target->sub($pop_years);
				$interval = $now->diff($adjusted_target);

				if(!empty($next_arr['next_year'])){
					$pop_tomorrow_time = new DateInterval('P'.$interval->y.'Y1D');
					$adjusted_tomorrow = $target->sub($pop_tomorrow_time);
					$tomorrow_interval = $now->diff($adjusted_tomorrow);
				}
			}
			$date_arr['days'] = $interval->days;
			$next_arr['next_day'] = $tomorrow_interval->days;
		}

		//but what if months where empty, but next day we have months...
		else if(!empty($next_arr['next_month']) && $displaymonths != 'true'){
			$next_arr['next_day'] = $tomorrow_interval->days;
		}

		// adjust days if weeks are being displayed
		if($displayweeks == 'true'){
			$date_arr['weeks'] = (int) floor( $date_arr['days'] / 7 );
			$date_arr['daysT-'] = (int) floor($date_arr['days'] %7);

			$next_week = (int) floor( $next_arr['next_day'] / 7 );
			if($date_arr['weeks'] != $next_week ){
				$next_arr['next_week'] = $next_week;
			}
			$next_arr['next_day'] = (int) floor($next_arr['next_day'] %7);
		}

		if( $diffSecs <= 0 && $launchtarget != 'countup'){
			$date_arr = array(
				'secs' => 0,
				'mins' => 0,
				'hours' => 0,
				'days' => 0,
				'weeks' => 0,
				'months' => 0,
				'years' => 0,
		 	);
		}

		// break numbers into digit elements
		foreach ($date_arr as $i => $d) {
			if($i == 'days' && $next_arr['next_day'] > 99){
				if($d > 9){
					$d = sprintf("%02d", $d);
				}
				if($d < 10){
					$d = sprintf("%03d", $d);
				}
			}
			//single digits get a padding zero
			else if($d < 10){
				$d = sprintf("%02d", $d);
			}
			$date_arr[$i] = array_map('intval', str_split($d));
		}


		if(is_numeric($width)){
			$width .= 'px';
		}
		if(is_numeric($height)){
			$height .= 'px';
		}
		$fonttypography = str_replace(" ","+",$typography);
		$fontfamilyname = ($fonttypography !== '') ? $fonttypography : 'Open+Sans';

		$ive = '<div id="'.$id.'-countdown" class="ive-title countdown_main_'.$id.' align'.$blockalignment.' ive_countdown" style="width:'.$width.'; height:'.$height.';">';
		$ive .= '<div class="'.$style.'-countdown">';
		$ive .= '<div id="'.$id.'-tophtml" class="'.$style.'-tophtml">';
	    if($before){
	        $ive .=  htmlspecialchars_decode($before);
	    }
		$ive .=  '</div>';
		$ive .= '<style>';
		$ive .= '@import url("https://fonts.googleapis.com/css2?family='.$fontfamilyname.':wght@'.$fontweight.'&display=swap");';
		$ive .= '
		.countdown-style-'.$id.'{
			background: '.$backgroundcolor.' !important;
			color: '.$textcolor.' !important;
			border: '.$borderwidth.'px solid '.$bordercolor.' !important;
			border-radius: '.$borderradius.'px !important;
			margin: '.$margintb.'px '.$marginlr.'px !important;
		}
		.countdown-title-color-'.$id.'{
			color: '.$textcolor.' !important;
			font-family: '.$fonttypography.' !important;
			font-weight: '.$fontweight.' !important;
			font-style: '.$fontstyle.' !important;
			letter-spacing: '.$letterspacing.'px !important;
		}
		@media screen and (max-width: 767px){
			.countdown-title-color-'.$id.'{
				text-align: '.$mobalign.' !important;
				font-size:'.$moblabelfontsize.'px !important;
			}
			.countdown-number-fontsize-'.$id.'{
				font-size:'.$mobnumberfontsize.'px !important;
			}
		}
		@media screen and (min-width: 768px) and (max-width: 1023px){
			.countdown-title-color-'.$id.'{
				text-align: '.$tabalign.' !important;
				font-size:'.$tablabelfontsize.'px !important;
			}
			.countdown-number-fontsize-'.$id.'{
				font-size:'.$tabnumberfontsize.'px !important;
			}
		}
		@media screen and (min-width: 1024px){
			.countdown_main_'.$id.'{
				justify-content:'.$tabalignment.';
				display:flex;
			}
			.countdown-title-color-'.$id.'{
				text-align: '.$deskalign.' !important;
				font-size:'.$desklabelfontsize.'px !important;
			}
			.countdown-width-height'.$id.'{
				width:'.$deskwidth.'px !important;
				height:'.$deskheight.'px !important;
			}
			.countdown-number-fontsize-'.$id.'{
				font-size:'.$desknumberfontsize.'px !important;
			}
		}
		';
		$ive .= '</style>';
		//drop in the dashboard
		$ive .=  '<div id="'.$id.'-dashboard" class="'.$style.'-dashboard">';

		if(!empty($date_arr['years']) && $displayyears == 'true'){
			$class = $style.'-dash '.$style.'-years_dash';
			$next_year = '';
			if(isset($next_arr['next_year'])){
				$next_year = 'data-next="'.$next_arr['next_year'].'"';
			}
			$ive .=  '<div class="countdown-width-height'.$id.' countdown-style-'.$id.' '.$class.'" '.$next_year.'"><div class="countdown-title-color-'.$id.' '.$style.'-dash_title">'.$years.'</div>';
			foreach( $date_arr['years'] AS $digit ){
				$ive .=  '<div class="countdown-number-fontsize-'.$id.' '.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
			}
			$ive .= '</div>';
		}

		if(!empty($date_arr['months']) && $displaymonths == 'true'){
			$class = $style.'-dash '.$style.'-months_dash';
			$next_month = '';
			if(isset($next_arr['next_month'])){
				$next_month = 'data-next="'.$next_arr['next_month'].'"';
			}
			$ive .=  '<div class="countdown-width-height'.$id.' countdown-style-'.$id.' '.$class.'" '.$next_month.'><div class="countdown-title-color-'.$id.' '.$style.'-dash_title">'.$months.'</div>';
			foreach( $date_arr['months'] AS $digit ){
				$ive .=  '<div class="countdown-number-fontsize-'.$id.' '.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
			}
			$ive .= '</div>';
		}

		if($displayweeks == 'true'){
			$wclass = $style.'-dash '.$style.'-weeks_dash';
			$next_week = '';
			if(isset($next_arr['next_week'])){
				$next_week = 'data-next="'.$next_arr['next_week'].'"';
			}
			$ive .=  '<div class="countdown-width-height'.$id.' countdown-style-'.$id.' '.$wclass.'" '.$next_week.'><div class="countdown-title-color-'.$id.' '.$style.'-dash_title">'.$weeks.'</div>';
			foreach( $date_arr['weeks'] AS $digit ){
				$ive .=  '<div class="countdown-number-fontsize-'.$id.' '.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
			}
			$ive .= '</div>';
		}

		$dclass = $style.'-dash '.$style.'-days_dash';
		$ive .= '<div class="countdown-width-height'.$id.' countdown-style-'.$id.' '.$dclass.'" data-next="'.$next_arr['next_day'].'"><div class="countdown-title-color-'.$id.' '.$style.'-dash_title">'.$days.'</div>';
		foreach( $date_arr['days'] AS $digit ){
			$ive .=  '<div class="countdown-number-fontsize-'.$id.' '.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
		}
		$ive .= '</div>';

		$ive .= '<div class="countdown-width-height'.$id.' countdown-style-'.$id.' '.$style.'-dash '.$style.'-hours_dash" data-current="'.$current_hours.'">';
			$ive .= '<div class="countdown-title-color-'.$id.' '.$style.'-dash_title">'.$hours.'</div>';
			foreach( $date_arr['hours'] AS $digit ){
				$ive .=  '<div class="countdown-number-fontsize-'.$id.' '.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
			}
		$ive .= '</div>';

		$ive .= '<div class="countdown-width-height'.$id.' countdown-style-'.$id.' '.$style.'-dash '.$style.'-minutes_dash" data-current="'.$current_minutes.'">';
			$ive .= '<div class="countdown-title-color-'.$id.' '.$style.'-dash_title">'.$minutes.'</div>';
			foreach( $date_arr['mins'] AS $digit ){
				$ive .=  '<div class="countdown-number-fontsize-'.$id.' '.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
			}
		$ive .= '</div>';

		if($displayseconds == 'true'){
				$ive .= '<div class="countdown-width-height'.$id.' countdown-style-'.$id.' '.$style.'-dash '.$style.'-seconds_dash">';
					$ive .= '<div class="countdown-title-color-'.$id.' '.$style.'-dash_title">'.$seconds.'</div>';
					foreach( $date_arr['secs'] AS $digit ){
						$ive .=  '<div class="countdown-number-fontsize-'.$id.' '.$style.'-digit" data-digit="'.$digit.'">'.$digit.'</div>';
					}
				$ive .= '</div>';
		}

		$ive .= '</div>'; //close the dashboard

		$ive .= '<div id="'.$id.'-bothtml" class="'.$style.'-bothtml">';
		if($after){
			$ive .= htmlspecialchars_decode($after);
		}
		$ive .= '</div></div></div>';

		$lt = date( 'n/j/Y H:i:s', strtotime(current_time('mysql')) );

		if(is_numeric($launchwidth)){
			$launchwidth .= 'px';
		}
		if(is_numeric($launchheight)){
			$launchheight .= 'px';
		}

		$content = json_encode(do_shortcode($content));
		$content = str_replace(array('\r\n', '\r', '\n<p>', '\n', '""'), '', $content);

		$ive .= "<script language='javascript' type='text/javascript'>
			jQuery(document).ready(function($) {
				$('#".$id."-dashboard').iveCountDown({
					targetDate: {
						'day': 	".$day.",
						'month': ".$month.",
						'year': ".$year.",
						'hour': ".$hour.",
						'min': 	".$min.",
						'sec': 	".$sec.",
						'localtime': '".$lt."'
					},
					style: '".$style."',
					id: '".$id."',
					event_id: '".$event_id."',
					debug: '".$debug."',
					launchtarget: '".$launchtarget."'";

		if(!empty($content)){
			$ive .= ", onComplete: function() {
								$('#".$id."-".$launchtarget."').css({'width' : '".$launchwidth."', 'height' : '".$launchheight."'});
								$('#".$id."-".$launchtarget."').html(".$content.");
							}";
		}
		$ive .= "});
			});
		</script>";

		if(!empty($debug)){
			$ive .= '<pre>Now: ' . $now->format('Y-m-d H:i:s e');
			$formated_target = new DateTime($t, $this->get_wp_timezone());
			$ive .= '<br/>Target: ' . $formated_target->format('Y-m-d H:i:s e');

			$interval = $formated_target->diff($now);
			$ive .= '<br/>Time to Target: ' . $interval->format('%a days %h hours %i mins. %s secs.');

			//rest now values
			$ive .= '<br/>Browser Now (JS): <span id="'.$id.'-jsnow">loading...</span>';
			$ive .= '<br/>Timezone Delta: <span id="'.$id.'-jstzone">loading...</span>';
			$ive .= '<br/>Rest Now (JS): <span id="'.$id.'-jstime">loading...</span>';
			$ive .= '<br/>Time to Target: <span id="'.$id.'-jsdiff">loading...</span>';
			$ive .= '<br/>Time Cached: <span id="'.$id.'-jscached">loading...</span>';

			$ive .= '</pre>';
		}

		return $ive;
	}

	function iveevents_callback() {
	    if ( ! wp_verify_nonce( $_POST['countdownNonce'], 'tountajax-countdownonce-nonce' ) ) {
			die ( 'Busted!');
		}

		esc_attr( WP_IveibtanaEvents::iveEvents( sanitize_text_field( $_POST['event_id'] ) ) );
		wp_die();
	}

}
$WP_Ivecountdown = new WP_Ivecountdown;

?>
