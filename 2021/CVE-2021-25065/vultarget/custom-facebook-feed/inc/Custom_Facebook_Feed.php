<?php
/**
 * CustomFacebookFeed plugin.
 *
 * The main Custom_Facebook_Feed class that runs the plugins & registers all the ressources.
 *
 * @since 2.19
 */

namespace CustomFacebookFeed;
use CustomFacebookFeed\SB_Facebook_Data_Manager;
use CustomFacebookFeed\Admin\CFF_Admin;
use CustomFacebookFeed\Admin\CFF_About;
use CustomFacebookFeed\Admin\CFF_New_User;
use CustomFacebookFeed\Admin\CFF_Notifications;
use CustomFacebookFeed\Admin\CFF_Tracking;
use CustomFacebookFeed\Builder\CFF_Feed_Builder;
use CustomFacebookFeed\Admin\CFF_Global_Settings;
use CustomFacebookFeed\Admin\CFF_oEmbeds;
use CustomFacebookFeed\Admin\CFF_Extensions;
use CustomFacebookFeed\Admin\CFF_About_Us;
use CustomFacebookFeed\Admin\CFF_Support;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



final class Custom_Facebook_Feed{

	/**
	 * Instance
	 *
	 * @since 2.19
	 * @access private
	 * @static
	 * @var Custom_Facebook_Feed
	 */
	private static $instance;


	/**
	 * CFF_Admin.
	 *
	 * Admin admin panel.
	 *
	 * @since 2.19
	 * @access public
	 *
	 * @var CFF_Admin
	 */
	public $cff_admin;


	/**
	 * CFF_About.
	 *
	 * About page panel.
	 *
	 * @since 2.19
	 * @access public
	 *
	 * @var CFF_About
	 */
	public $cff_about;

	/**
	 * CFF_Error_Reporter.
	 *
	 * Error Reporter panel.
	 *
	 * @since 2.19
	 * @access public
	 *
	 * @var CFF_Error_Reporter
	 */
	public $cff_error_reporter;

	/**
	 * cff_blocks.
	 *
	 * Blocks.
	 *
	 * @since 2.19
	 * @access public
	 *
	 * @var cff_blocks
	 */
	public $cff_blocks;

	/**
	 * CFF_Notifications.
	 *
	 * Notifications System.
	 *
	 * @since 2.19
	 * @access public
	 *
	 * @var CFF_Notifications
	 */
	public $cff_notifications;

	/**
	 * CFF_New_User.
	 *
	 * New User.
	 *
	 * @since 2.19
	 * @access public
	 *
	 * @var CFF_New_User
	 */
	public $cff_newuser;

	/**
	 * CFF_Oembed.
	 *
	 * Oembed Element.
	 *
	 * @since 2.19
	 * @access public
	 *
	 * @var CFF_Oembed
	 */
	public $cff_oembed;

	/**
	 * CFF_Tracking.
	 *
	 * Tracking System.
	 *
	 * @since 2.19
	 * @access public
	 *
	 * @var CFF_Tracking
	 */
	public $cff_tracking;

	/**
	 * CFF_Shortcode.
	 *
	 * Shortcode Class.
	 *
	 * @since 2.19
	 * @access public
	 *
	 * @var CFF_Shortcode
	 */
	public $cff_shortcode;

	/**
	 * CFF_SiteHealth.
	 *
	 *
	 * @since 2.19
	 * @access public
	 *
	 * @var CFF_SiteHealth
	 */
	public $cff_sitehealth;


	/**
	 * CFF_Feed_Builder.
	 *
	 * Feed Builder.
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @var CFF_Feed_Builder
	 */
	public $cff_feed_builder;

	/**
	 * CFF_Global_Settings.
	 *
	 * Global Settings.
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @var CFF_Global_Settings
	 */
	public $cff_global_settings;

	/**
	 * CFF_oEmbeds.
	 *
	 * oEmbeds Page.
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @var CFF_oEmbeds
	 */
	public $cff_oembeds;

  	/**
   	 * CFF_About_Us.
	 *
	 * About Us Page.
	 *
	 * @since 4.0
	 * @access public
   	 * @var CFF_About_Us
	 */
	public $cff_about_us;

	/**
	 * CFF_Support.
	 *
	 * Support Page.
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @var CFF_Support
	 */
	public $cff_support;

	/**
	 * CFF_Tooltip_Wizard.
	 *
	 * GB Blocks Pages.
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @var CFF_Tooltip_Wizard
	 */
	public $cff_tooltip_wizard;

	/**
	 * CFF_Elementor_Base.
	 *
	 * Elementor Base.
	 *
	 * @since 4.0
	 * @access public
	 *
	 * @var CFF_Elementor_Base
	 */
	public $cff_elementor_base;

	/**
	 * Custom_Facebook_Feed Instance.
	 *
	 * Just one instance of the Custom_Facebook_Feed class
	 *
	 * @since 2.19
	 * @access public
	 * @static
	 *
	 * @return Custom_Facebook_Feed
	 */
	public static function instance() {
		if ( null === self::$instance) {
			self::$instance = new self();

			if( !class_exists('CFF_Utils') ) include CFF_PLUGIN_DIR. 'inc/CFF_Utils.php';


			add_action( 'plugins_loaded', [ self::$instance, 'load_textdomain' ], 10 );
			add_action( 'init', [ self::$instance, 'init' ], 0 );



			add_action( 'wp_loaded', [ self::$instance, 'cff_check_for_db_updates' ] );

			add_action( 'wp_footer', [ self::$instance, 'cff_js' ] );

            add_filter( 'cron_schedules', [ self::$instance, 'cff_cron_custom_interval' ] );
            add_filter('widget_text', 'do_shortcode');

            add_action('wp_ajax_feed_locator', [self::$instance, 'cff_feed_locator']);
			add_action('wp_ajax_nopriv_feed_locator', [self::$instance, 'cff_feed_locator']);

			register_activation_hook( CFF_FILE, [ self::$instance, 'cff_activate' ] );
			register_deactivation_hook( CFF_FILE, [ self::$instance, 'cff_deactivate' ] );
			register_uninstall_hook( CFF_FILE, array('CustomFacebookFeed\Custom_Facebook_Feed','cff_uninstall'));


		}
		return self::$instance;
	}

	/**
 	 * Load Custom_Facebook_Feed textdomain.
 	 *
 	 * @since 2.19
 	 *
 	 * @return void
	 * @access public
 	*/
	public function load_textdomain(){
		load_plugin_textdomain( 'custom-facebook-feed' );
	}


	/**
	 * Init.
	 *
	 * Initialize Custom_Facebook_Feed plugin.
	 *
	 * @since 2.19
	 * @access public
	 */
	public function init() {
		//Load Composer Autoload
		require CFF_PLUGIN_DIR . 'vendor/autoload.php';
		$this->cff_tracking 				= new CFF_Tracking();
		$this->cff_oembed 					= new CFF_Oembed();
		$this->cff_error_reporter			= new CFF_Error_Reporter();
		$this->cff_admin 					= new CFF_Admin();
		$this->cff_blocks 					= new CFF_Blocks();
		$this->cff_shortcode				= new CFF_Shortcode();
		$this->cff_feed_builder				= new CFF_Feed_Builder();
		$this->cff_global_settings			= new CFF_Global_Settings();
		$this->cff_oembeds					= new CFF_oEmbeds();
		$this->cff_about_us					= new CFF_About_Us();
		$this->cff_support					= new CFF_Support();
		$this->cff_elementor_base			= CFF_Elementor_Base::instance();


		$this->cff_ppca_check_notice_dismiss();
		$this->register_assets();
		$this->group_posts_process();

		$this->detect_custom_code();

		if ( $this->cff_blocks->allow_load() ) {
			$this->cff_blocks->load();
		}


		if ( is_admin() ) {
			$this->cff_about		= new CFF_About();
			$this->cff_tooltip_wizard		    = new Builder\CFF_Tooltip_Wizard();
			if ( version_compare( PHP_VERSION,  '5.3.0' ) >= 0 && version_compare( get_bloginfo('version'), '4.6' , '>' ) ) {
				$this->cff_notifications = new CFF_Notifications();
				$this->cff_notifications->init();

				$this->cff_newuser = new CFF_New_User();
				$this->cff_newuser->init();

				require_once trailingslashit( CFF_PLUGIN_DIR ) . 'admin/addon-functions.php';
				$this->cff_sitehealth = new CFF_SiteHealth();
				if ( $this->cff_sitehealth->allow_load() ) {
					$this->cff_sitehealth->load();
				}
			}
		}
	}

	/**
 	 * Launch the Group Posts Cache Process
 	 *
 	 *
 	 * @return void
	 * @access public
 	*/
	function group_posts_process(){
		$cff_cron_schedule = 'hourly';
		$cff_cache_time = get_option( 'cff_cache_time' );
		$cff_cache_time_unit = get_option( 'cff_cache_time_unit' );
		if( $cff_cache_time_unit == 'hours' && $cff_cache_time > 5 ) $cff_cron_schedule = 'twicedaily';
		if( $cff_cache_time_unit == 'days' ) $cff_cron_schedule = 'daily';
		CFF_Group_Posts::group_schedule_event(time(), $cff_cron_schedule);
	}

	/**
	 * Register Assets
	 *
	 * @since 2.19
	 */
	public function register_assets(){
		add_action( 'wp_enqueue_scripts' , array( $this, 'enqueue_styles_assets' ) );
		add_action( 'wp_enqueue_scripts' , array( $this, 'enqueue_scripts_assets' ) );
	}


	/**
	 * Enqueue & Register Styles
	 *
	 * @since 2.19
	 */
	public function enqueue_styles_assets(){
		//Minify files?
	    $options = get_option('cff_style_settings');
	    isset($options[ 'cff_minify' ]) ? $cff_minify = $options[ 'cff_minify' ] : $cff_minify = '';
	    $cff_minify ? $cff_min = '.min' : $cff_min = '';

	    // Respects SSL, Style.css is relative to the current file
	    wp_register_style(
	    	'cff',
	    	CFF_PLUGIN_URL . 'assets/css/cff-style'.$cff_min.'.css' ,
	    	array(),
	    	CFFVER
	    );

        $options['cff_enqueue_with_shortcode'] = isset( $options['cff_enqueue_with_shortcode'] ) ? $options['cff_enqueue_with_shortcode'] : false;
        if ( isset( $options['cff_enqueue_with_shortcode'] ) && !$options['cff_enqueue_with_shortcode'] ) {
            wp_enqueue_style( 'cff' );
        }

	    $options = get_option('cff_style_settings');

	    if ( CFF_GDPR_Integrations::doing_gdpr( $options ) ) {
		    $options[ 'cff_font_source' ] = 'local';
	    }
	    if( !isset( $options[ 'cff_font_source' ] ) ){
	        wp_enqueue_style( 'sb-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
	    } else {

	        if( $options[ 'cff_font_source' ] == 'none' ){
	            //Do nothing
	        } else if( $options[ 'cff_font_source' ] == 'local' ){
	            wp_enqueue_style(
	            	'sb-font-awesome',
	    			CFF_PLUGIN_URL . 'assets/css/font-awesome.min.css',
	            	array(),
	            	'4.7.0'
	            );
	        } else {
	            wp_enqueue_style( 'sb-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
	        }

	    }
	}


	/**
	 * Enqueue & Register Scripts
	 *
	 *
	 * @since 2.19
	 * @access public
	 */
	public function enqueue_scripts_assets(){
		//Minify files?
	    $options = get_option('cff_style_settings');
	    isset($options[ 'cff_minify' ]) ? $cff_minify = $options[ 'cff_minify' ] : $cff_minify = '';
	    $cff_minify ? $cff_min = '.min' : $cff_min = '';

	    //Register the script to make it available
	    wp_register_script(
	    	'cffscripts',
	    	CFF_PLUGIN_URL . 'assets/js/cff-scripts'.$cff_min.'.js' ,
	    	array('jquery'),
	    	CFFVER,
	    	true
	    );
	    $options['cff_enqueue_with_shortcode'] = isset( $options['cff_enqueue_with_shortcode'] ) ? $options['cff_enqueue_with_shortcode'] : false;
        if ( isset( $options['cff_enqueue_with_shortcode'] ) && !$options['cff_enqueue_with_shortcode'] ) {
            wp_enqueue_script( 'cffscripts' );
        }
	}


	/**
	 * DB Update Checker.
	 *
	 * Check for the db updates
	 *
	 * @since 2.19
	 * @access public
	 */
	function cff_check_for_db_updates(){
	    $db_ver = get_option( 'cff_db_version', 0 );
	    if ( (float) $db_ver < 1.0 ) {
	        global $wp_roles;
	        $wp_roles->add_cap( 'administrator', 'manage_custom_facebook_feed_options' );
	        $cff_statuses_option = get_option( 'cff_statuses', array() );
	        if ( ! isset( $cff_statuses_option['first_install'] ) ) {
	            $options_set = get_option( 'cff_page_id', false );
	            if ( $options_set ) {
	                $cff_statuses_option['first_install'] = 'from_update';
	            } else {
	                $cff_statuses_option['first_install'] = time();
	            }
	            $cff_rating_notice_option = get_option( 'cff_rating_notice', false );
	            if ( $cff_rating_notice_option === 'dismissed' ) {
	                $cff_statuses_option['rating_notice_dismissed'] = time();
	            }
	            $cff_rating_notice_waiting = get_transient( 'custom_facebook_rating_notice_waiting' );
	            if ( $cff_rating_notice_waiting === false
	                 && $cff_rating_notice_option === false ) {
	                $time = 2 * WEEK_IN_SECONDS;
	                set_transient( 'custom_facebook_rating_notice_waiting', 'waiting', $time );
	                update_option( 'cff_rating_notice', 'pending', false );
	            }
	            update_option( 'cff_statuses', $cff_statuses_option, false );
	        }
	        update_option( 'cff_db_version', CFF_DBVERSION );
	    }
		if ( (float) $db_ver < 1.1 ) {
			if ( ! wp_next_scheduled( 'cff_feed_issue_email' ) ) {
				$timestamp = strtotime( 'next monday' );
				$timestamp = $timestamp + (3600 * 24 * 7);
				$six_am_local = $timestamp + CFF_Utils::cff_get_utc_offset() + (6*60*60);
				wp_schedule_event( $six_am_local, 'cffweekly', 'cff_feed_issue_email' );
			}
			update_option( 'cff_db_version', CFF_DBVERSION );
		}
		if ( (float) $db_ver < 1.2 ) {
			if ( ! wp_next_scheduled( 'cff_notification_update' ) ) {
				$timestamp = strtotime( 'next monday' );
				$timestamp = $timestamp + (3600 * 24 * 7);
				$six_am_local = $timestamp + CFF_Utils::cff_get_utc_offset() + (6*60*60);

				wp_schedule_event( $six_am_local, 'cffweekly', 'cff_notification_update' );
			}
			update_option( 'cff_db_version', CFF_DBVERSION );
		}

		if ( (float) $db_ver < 1.3 ) {
			CFF_Feed_Locator::create_table();
			update_option( 'cff_db_version', CFF_DBVERSION );
		}

		if ( (float) $db_ver < 1.4 ) {
			Builder\CFF_Db::create_tables();
			update_option( 'cff_db_version', CFF_DBVERSION );
		}

		//\CustomFacebookFeed\Builder\CFF_Db::reset_tables();\CustomFacebookFeed\Builder\CFF_Db::reset_db_update();die();

		/**
		 * for 4.0 update
		 */
		if ( (float) $db_ver < 2.1 ) {
			$options 		= get_option( 'cff_style_settings', array() );
			$legacy_at = get_option( 'cff_access_token' );
			$options_support_legacy = false; // in case the locator table doesn't have any feeds in it but someone might be using legacy feeds
			if ( ! empty( $legacy_at ) ) {
				$options_support_legacy = true;
				\CustomFacebookFeed\Builder\CFF_Feed_Saver::set_legacy_feed_settings();
			}

			\CustomFacebookFeed\Builder\CFF_Db::create_tables();
			update_option( 'cff_db_version', CFF_DBVERSION );

			// are there existing feeds to toggle legacy onboarding?
			$cff_statuses_option = get_option( 'cff_statuses', array() );
			$cff_statuses_option['legacy_onboarding'] = array(
				'active' => false,
				'type'=> 'single'
			);

			\CustomFacebookFeed\Builder\CFF_Source::set_legacy_source_queue();
			if ( \CustomFacebookFeed\Builder\CFF_Source::should_do_source_updates() ) {
				\CustomFacebookFeed\Builder\CFF_Source::batch_process_legacy_source_queue();
			}

			\CustomFacebookFeed\Builder\CFF_Source::update_source_from_legacy_settings();

			// how many legacy feeds?
			$args = array(
				'html_location' => array( 'header', 'footer', 'sidebar', 'content', 'unknown' ),
				'group_by' => 'shortcode_atts',
				'page' => 1
			);
			$feeds_data = \CustomFacebookFeed\CFF_Feed_Locator::legacy_facebook_feed_locator_query( $args );
			$num_legacy = count( $feeds_data );

			$cff_statuses_option['support_legacy_shortcode'] = false;

			if ( $num_legacy > 0 ) {

				if ( $num_legacy > 1 ) {
					$cff_statuses_option['legacy_onboarding'] = array(
						'active' => true,
						'type'=> 'multiple'
					);
					$cff_statuses_option['support_legacy_shortcode'] = true;
				} else {
					$cff_statuses_option['legacy_onboarding'] = array(
						'active' => true,
						'type'=> 'single'
					);

					$shortcode_atts = $feeds_data[0]['shortcode_atts'] != '[""]' ? json_decode( $feeds_data[0]['shortcode_atts'], true ) : [];
					$shortcode_atts = is_array( $shortcode_atts ) ? $shortcode_atts : array();

					$cff_statuses_option['support_legacy_shortcode'] = $shortcode_atts;

					$settings_data = \CustomFacebookFeed\Builder\CFF_Post_Set::legacy_to_builder_convert( $shortcode_atts );

					if ( isset( $settings_data['id'] ) ) {
						$source_ids = explode( ',', str_replace( ' ', '', $settings_data['id'] ) );
					} else {
						$source_ids = (array)get_option( 'cff_page_id', array() );
					}

					$source_list = \CustomFacebookFeed\Builder\CFF_Feed_Builder::get_source_list();

					$supported_sources = array();
					$feed_name = 'Existing Feed';
					foreach ( $source_list as $source ) {
						if ( in_array( $source['account_id'], $source_ids, true ) ) {
							$supported_sources[] = $source['account_id'];
							$feed_name = $source['username'];
						}
					}

					$feed_saver = new \CustomFacebookFeed\Builder\CFF_Feed_Saver( false );
					$feed_saver->set_data( $settings_data );

					$feed_saver->set_feed_name( $feed_name );

					$new_feed_id = $feed_saver->update_or_insert();

					$args = array(
						'new_feed_id' => $new_feed_id,
						'legacy_feed_id' => $feeds_data[0]['feed_id'],
					);

					CFF_Feed_Locator::update_legacy_to_builder( $args );
				}
			} elseif ( $num_legacy === 0 && $options_support_legacy ) {
				$cff_statuses_option['support_legacy_shortcode'] = true;
			}

			update_option( 'cff_statuses', $cff_statuses_option );
		}

		if ( (float) $db_ver < 2.2 ) {
			$manager = new SB_Facebook_Data_Manager();
			$manager->update_db_for_dpa();
			update_option( 'cff_db_version', CFF_DBVERSION );
		}

		if ( version_compare( $db_ver, '2.4', '<' ) ) {
			update_option( 'cff_db_version', CFF_DBVERSION );

			$groups = \CustomFacebookFeed\Builder\CFF_Db::source_query( array( 'type' => 'group' ) );

			$cff_statuses_option                       = get_option( 'cff_statuses', array() );
			$cff_statuses_option['groups_need_update'] = false;

			if ( empty( $groups ) ) {
				update_option( 'cff_statuses', $cff_statuses_option, false );
			} else {
				$encryption         = new \CustomFacebookFeed\SB_Facebook_Data_Encryption();
				$groups_need_update = false;
				foreach ( $groups as $source ) {
					$info   = ! empty( $source['info'] ) ? json_decode( $encryption->decrypt( $source['info'] ) ) : array();
					if ( \CustomFacebookFeed\Builder\CFF_Source::needs_update( $source, $info ) ) {
						$groups_need_update = true;
					}
				}
				$cff_statuses_option['groups_need_update'] = $groups_need_update;
				update_option( 'cff_statuses', $cff_statuses_option, false );
			}
		}
	}


	/**
	 * Activate
	 *
	 * CFF activation action.
	 *
	 * @since 2.19
	 * @access public
	 */
	function cff_activate() {
	    $options = get_option( 'cff_style_settings' );

		//Run cron twice daily when plugin is first activated for new users
		if ( ! wp_next_scheduled( 'cff_cron_job' ) ) {
			wp_schedule_event( time(), 'twicedaily', 'cff_cron_job' );
		}
		if ( ! wp_next_scheduled( 'cff_feed_issue_email' ) ) {
			CFF_Utils::cff_schedule_report_email();
		}
		// set usage tracking to false if fresh install.
		$usage_tracking = get_option( 'cff_usage_tracking', false );

		if ( ! is_array( $usage_tracking ) ) {
			$usage_tracking = array(
				'enabled' => false,
				'last_send' => 0
			);
			update_option( 'cff_usage_tracking', $usage_tracking, false );
		}

		if ( ! wp_next_scheduled( 'cff_notification_update' ) ) {
			$timestamp = strtotime( 'next monday' );
			$timestamp = $timestamp + (3600 * 24 * 7);
			$six_am_local = $timestamp + CFF_Utils::cff_get_utc_offset() + (6*60*60);
			wp_schedule_event( $six_am_local, 'cffweekly', 'cff_notification_update' );
		}

	    if ( ! empty( $options ) ) {
	    	return;
	    }

	    //Show all post types
	    $options[ 'cff_show_links_type' ] = true;
	    $options[ 'cff_show_event_type' ] = true;
	    $options[ 'cff_show_video_type' ] = true;
	    $options[ 'cff_show_photos_type' ] = true;
	    $options[ 'cff_show_status_type' ] = true;
	    $options[ 'cff_show_albums_type' ] = true;
	    $options[ 'cff_show_author' ] = true;
	    $options[ 'cff_show_text' ] = true;
	    $options[ 'cff_show_desc' ] = true;
	    $options[ 'cff_show_shared_links' ] = true;
	    $options[ 'cff_show_date' ] = true;
	    $options[ 'cff_show_media' ] = true;
	    $options[ 'cff_show_media_link' ] = true;
	    $options[ 'cff_show_event_title' ] = true;
	    $options[ 'cff_show_event_details' ] = true;
	    $options[ 'cff_show_meta' ] = true;
	    $options[ 'cff_show_link' ] = true;
	    $options[ 'cff_show_like_box' ] = true;
	    $options[ 'cff_show_facebook_link' ] = true;
	    $options[ 'cff_show_facebook_share' ] = true;
	    $options[ 'cff_event_title_link' ] = true;

	    update_option( 'cff_style_settings', $options );

	    get_option('cff_show_access_token');
	    update_option( 'cff_show_access_token', true );

	}


	/**
	 * Deactivate
	 *
	 * CFF deactivation action.
	 *
	 * @since 2.19
	 * @access public
	 */
	function cff_deactivate() {
	    wp_clear_scheduled_hook( 'cff_cron_job');
		wp_clear_scheduled_hook( 'cff_notification_update');
		wp_clear_scheduled_hook( 'cff_feed_issue_email' );
		wp_clear_scheduled_hook( 'cff_usage_tracking_cron' );
	}


	/**
	 * Uninstall
	 *
	 * CFF uninstallation action.
	 *
	 * @since 2.19
	 * @access public
	 */
	public static function cff_uninstall(){
	    if ( ! current_user_can( 'activate_plugins' ) ){
	        return;
	    }
	    //If the user is preserving the settings then don't delete them
	    $cff_preserve_settings = get_option( 'cff_preserve_settings' );
	    if ( ! empty( $cff_preserve_settings ) ){
		    return;
	    }

	    //Settings
	    delete_option( 'cff_show_access_token' );
	    delete_option( 'cff_access_token' );
	    delete_option( 'cff_page_id' );
	    delete_option( 'cff_num_show' );
	    delete_option( 'cff_post_limit' );
	    delete_option( 'cff_show_others' );
	    delete_option( 'cff_cache_time' );
	    delete_option( 'cff_cache_time_unit' );
	    delete_option( 'cff_locale' );
	    delete_option( 'cff_ajax' );
	    delete_option( 'cff_preserve_settings' );
	    //Style & Layout
	    delete_option( 'cff_title_length' );
	    delete_option( 'cff_body_length' );
	    delete_option('cff_style_settings');

		delete_option( 'cff_usage_tracking_config' );
		delete_option( 'cff_usage_tracking' );

		delete_option( 'cff_statuses' );
		delete_option( 'cff_rating_notice' );
		delete_option( 'cff_review_consent' );
		delete_option( 'cff_db_version' );
		delete_option( 'cff_newuser_notifications' );
		delete_option( 'cff_notifications' );

		delete_option( 'cff_legacy_feed_settings' );
		delete_option( 'cff_theme_styles' );
		delete_option( 'cff_caching_type' );
		delete_option( 'cff_oembed_token' );

		global $wp_roles;
		$wp_roles->remove_cap( 'administrator', 'manage_custom_facebook_feed_options' );

		global $wpdb;
		$locator_table_name = $wpdb->prefix . CFF_FEED_LOCATOR;
		$wpdb->query( "DROP TABLE IF EXISTS $locator_table_name" );

		$feeds_table_name = $wpdb->prefix . 'cff_feeds';
		$wpdb->query( "DROP TABLE IF EXISTS $feeds_table_name" );

		$feed_caches_table_name = $wpdb->prefix . 'cff_feed_caches';
		$wpdb->query( "DROP TABLE IF EXISTS $feed_caches_table_name" );

		$sources_table_name = $wpdb->prefix . 'cff_sources';
		$wpdb->query( "DROP TABLE IF EXISTS $sources_table_name" );

		$table_name = esc_sql( $wpdb->prefix . "postmeta" );
		$result = $wpdb->query("
		    DELETE
		    FROM $table_name
		    WHERE meta_key = '_cff_oembed_done_checking';");

		$usermeta_table_name = $wpdb->prefix . "usermeta";
		$result = $wpdb->query( "
	        DELETE
	        FROM $usermeta_table_name
	        WHERE meta_key LIKE ('cff\_%')
	        " );
	}



	/**
	 * Custom CSS
	 *
	 * Adding custom CSS
	 *
	 * @since 2.19
	 * @access public
	 * @deprecated
	 */
	function cff_custom_css() {

	}


	/**
	 * Custom JS
	 *
	 * Adding custom JS
	 *
	 * @since 2.19
	 * @access public
	 */
	function cff_js() {
	    $options = get_option('cff_style_settings');

	    //Link hashtags?
	    isset($options[ 'cff_link_hashtags' ]) ? $cff_link_hashtags = $options[ 'cff_link_hashtags' ] : $cff_link_hashtags = 'true';
	    ($cff_link_hashtags == 'true' || $cff_link_hashtags == 'on') ? $cff_link_hashtags = 'true' : $cff_link_hashtags = 'false';

	    //If linking the post text then don't link the hashtags
	    isset($options[ 'cff_title_link' ]) ? $cff_title_link = $options[ 'cff_title_link' ] : $cff_title_link = false;
	    ($cff_title_link == 'true' || $cff_title_link == 'on') ? $cff_title_link = true : $cff_title_link = false;
	    if ($cff_title_link) $cff_link_hashtags = 'false';

	    echo '<!-- Custom Facebook Feed JS -->';
	    echo "\r\n";
	    echo '<script type="text/javascript">';
	    echo 'var cffajaxurl = "' . admin_url('admin-ajax.php') . '";';
	    echo "\r\n";
	    echo 'var cfflinkhashtags = "' . $cff_link_hashtags . '";';
	    echo "\r\n";
	    echo '</script>';
	    echo "\r\n";
	}


	/**
	 * Notice Dismiss
	 *
	 * PPCA Check Notice Dismiss
	 *
	 * @since 2.19
	 * @access public
	 */
	function cff_ppca_check_notice_dismiss() {
	    global $current_user;
		$user_id = $current_user->ID;
	    if ( isset($_GET['cff_ppca_check_notice_dismiss']) && '0' == $_GET['cff_ppca_check_notice_dismiss'] ) {
	    	add_user_meta($user_id, 'cff_ppca_check_notice_dismiss', 'true', true);
	    }
	}


	/**
	 * Cron Custom Interval
	 *
	 * Cron Job Custom Interval
	 *
	 * @since 2.19
	 * @access public
	 */
	function cff_cron_custom_interval( $schedules ) {
		$schedules['cffweekly'] = array(
			'interval' => 3600 * 24 * 7,
			'display'  => __( 'Weekly' )
		);
		return $schedules;
	}

	/**
	 * Feed Locator Ajax Call
	 *
	 *
	 * @since 2.19
	 * @access public
	 */
	function cff_feed_locator(){

			$feed_locator_data_array = isset($_POST['feedLocatorData']) && !empty($_POST['feedLocatorData']) && is_array($_POST['feedLocatorData']) ? $_POST['feedLocatorData'] : false;
		  	if($feed_locator_data_array != false):
		  		foreach ($feed_locator_data_array as $single_feed_locator) {
				    $can_do_background_tasks = false;

				    $cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
				    $cap = apply_filters( 'cff_settings_pages_capability', $cap );
				    if ( current_user_can( $cap ) ) {
					    $can_do_background_tasks = true;
				    } else {
					    $nonce = isset( $_POST['locator_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['locator_nonce'] ) ) : '';
					    if ( isset( $single_feed_locator['postID'] ) && wp_verify_nonce( $nonce, esc_attr( 'cff-locator-nonce-' . $single_feed_locator['postID'] ) ) ) {
						    $can_do_background_tasks = true;
					    }
				    }

					if( $can_do_background_tasks ){
			  			$feed_details = array(
							'feed_id' => $single_feed_locator['feedID'],
							'atts' =>  $single_feed_locator['shortCodeAtts'],
							'location' => array(
								'post_id' => $single_feed_locator['postID'],
								'html' => $single_feed_locator['location']
							)
						);
						$locator = new CFF_Feed_Locator( $feed_details );
						$locator->add_or_update_entry();
					}
		  		}
		  	endif;
	    die();
	}

	/**
	 * Detect Custom CSS Code
	 *
	 *
	 * @since ??
	 * @access public
	 */
	public function detect_custom_code(){
		//$cff_options = get_option( 'cff_style_settings' );
		//if( !empty( $cff_options[ 'cff_custom_css' ]) ){
		//	$core_custom_css = wp_get_custom_css();
		//	\WP_Customize_Custom_CSS_Setting
		//}


	}
}


