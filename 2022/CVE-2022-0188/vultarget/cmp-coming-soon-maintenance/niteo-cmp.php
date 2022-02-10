<?php
/*
 Plugin Name: 		CMP - Coming Soon & Maintenance Plugin
 Plugin URI: 		https://wordpress.org/plugins/cmp-coming-soon-maintenance/
 Description:       Display customizable landing page for Coming Soon, Maintenance & Under Construction page.
 Version:           4.0.18
 Author:            NiteoThemes
 Author URI:        https://www.niteothemes.com
 Text Domain:       cmp-coming-soon-maintenance
 Domain Path:		/languages
 License:           GPL-2.0+
 License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CMP_Coming_Soon_and_Maintenance' ) ) :

	/**
	 * Main CMP Coming Soon and Maintenance class.
	 *
	 * @since 2.8
	 */ 
	class CMP_Coming_Soon_and_Maintenance {

		/**
		 * CMP_Coming_Soon_and_Maintenance The one true CMP_Coming_Soon_and_Maintenance
		 *
		 * @var string $instance
		 */
		private static $instance;

		/**
		 * Main CMP_Coming_Soon_and_Maintenance Instance.
		 *
		 * Insures that only one instance of CMP_Coming_Soon_and_Maintenance exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * Thanks Rich Tabor for teaching things about Singleton
		 * @since 2.8
		 * @static
		 * @static var array $instance
		 * @uses CMP_Coming_Soon_and_Maintenance::init() Initiate actions and filters.
		 * @uses CMP_Coming_Soon_and_Maintenance::cmp_assets_suffix() Include .min version based on CMP_DEBUG
		 * @uses CMP_Coming_Soon_and_Maintenance::load_textdomain() load the language files.
		 * @return object|CMP_Coming_Soon_and_Maintenance
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CMP_Coming_Soon_and_Maintenance ) ) {
				self::$instance = new CMP_Coming_Soon_and_Maintenance();
				self::$instance->constants();
				self::$instance->init();
				self::$instance->cmp_assets_suffix();
			}

			return self::$instance;
		}

		// define constants
		private function constants() {
			$this->define( 'CMP_VERSION', '4.0.18' );
			$this->define( 'CMP_DEBUG', false );
			$this->define( 'CMP_AUTHOR', 'NiteoThemes' );
			$this->define( 'CMP_AUTHOR_HOMEPAGE', 'https://niteothemes.com' );
			$this->define( 'CMP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			$this->define( 'CMP_PREMIUM_THEMES_DIR', plugin_dir_path( __DIR__ ) . 'cmp-premium-themes/' );
			$this->define( 'CMP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'CMP_UPDATE_URL', 'https://niteothemes.com/updates/' );
			CMP_DEBUG === TRUE
				? $this->define( 'CMP_LICENSE_SERVER_URL', 'https://niteothemes.com/dev2/' )
				: $this->define( 'CMP_LICENSE_SERVER_URL', 'https://niteothemes.com/' );
		}

		/**
		 * Inits and hooks
		 */
		public function init() {
			do_action( 'cmp_plugin_loaded');
			add_action( 'init', array( $this, 'load_textdomain' ));
			add_action( 'plugins_loaded', array( $this, 'cmp_update_process' ), 0 );
			add_action( 'template_redirect', array( $this, 'cmp_displayPage' ), 1 );
			add_action( 'admin_init', array( $this, 'cmp_adminInit' ) ) ;
			add_action( 'admin_menu', array( $this, 'cmp_adminMenu' ) );
			add_action( 'admin_notices', array( $this, 'cmp_admin_notice' ) );
			add_action( 'wp_before_admin_bar_render', array( $this, 'cmp_admin_bar' ) );
			add_action( 'admin_enqueue_scripts', array( $this,'cmp_add_topbar_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this,'cmp_add_admin_style' ) ); 
			add_action( 'wp_enqueue_scripts', array( $this,'cmp_add_topbar_scripts' ) );
			add_action( 'wp_ajax_cmp_get_post_detail', array( $this, 'cmp_get_post_detail' ) );
			add_action( 'wp_ajax_nopriv_cmp_get_post_detail', array( $this, 'cmp_get_post_detail' ) );
			add_action( 'wp_ajax_cmp_check_update', array( $this, 'cmp_check_update' ) );
			add_action( 'wp_ajax_cmp_ajax_dismiss_activation_notice', array( $this, 'cmp_ajax_dismiss_activation_notice' ) );
			add_action( 'wp_ajax_niteo_themeinfo', array( $this, 'niteo_themeinfo' ) );
			add_action( 'wp_ajax_niteo_unsplash', array( $this, 'niteo_unsplash' ) );
			add_action( 'wp_ajax_niteo_export_csv', array( $this, 'niteo_export_csv' ) );
			add_action( 'wp_ajax_cmp_theme_update_install', array( $this, 'cmp_theme_update_install' ) );
			add_action( 'wp_ajax_cmp_toggle_activation', array( $this, 'cmp_ajax_toggle_activation') );
			add_action( 'wp_ajax_nopriv_niteo_subscribe', array( $this, 'niteo_subscribe' ) );
			add_action( 'wp_ajax_niteo_subscribe', array( $this, 'niteo_subscribe' ) );
			add_action( 'wp_ajax_cmp_mailchimp_list_ajax', array( $this, 'cmp_mailchimp_list_ajax' ) );
			add_action( 'wp_ajax_cmp_ajax_upload_font', array( $this, 'cmp_ajax_upload_font' ) );
			add_action( 'wp_ajax_cmp_ajax_export_settings', array( $this, 'cmp_ajax_export_settings' ) );
			add_action( 'wp_ajax_cmp_ajax_import_settings', array( $this, 'cmp_ajax_import_settings' ) );
			add_action( 'wp_ajax_nopriv_cmp_disable_comingsoon_ajax', array( $this, 'cmp_disable_comingsoon_ajax' ) );
			add_action( 'admin_head', array( $this, 'cmp_admin_css') );
			add_action( 'after_setup_theme', array( $this, 'cmp_create_translation'), 10 );
			add_action( 'after_setup_theme', array( $this, 'cmp_register_wpml_strings'), 20 );
			add_filter( 'upload_mimes', array( $this, 'cmp_allow_font_mimes' ));
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this,'add_action_links' ) );			
			
			register_activation_hook( __FILE__, array( $this, 'cmp_activate' ) );
			register_deactivation_hook( __FILE__, array( $this, 'cmp_deactivate' ) );
			
			require_once( dirname( __FILE__) . '/inc/class-cmp-render_html.php' );
			
			if ( $this->cmp_active() === '1' && get_option('niteoCS_rest_api_status', '1') !== '1' ) {
				add_filter( 'rest_authentication_errors', array( $this, 'restrict_rest_api'), 0, 1 );
			}

			add_action( 'init', array( $this, 'jetpack_stats_compatibility' ) );
		}


		/**
		 * Define constant if not already set.
		 *
		 * @param  string|string $name Name of the definition.
		 * @param  string|bool   $value Default value.
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Admin Init - register and enqueue scripts nad styles
		 */
		public function cmp_adminInit() {
			// include feedback class
			require_once( 'inc/class-cmp-feedback.php' );

			// ini render-settings class
			require_once('inc/class-cmp-render_settings.php');

			$this->render_settings = new cmp_render_settings();

			if ( current_user_can('administrator') ) {

				wp_register_style( 'cmp-style',  plugins_url('/css/cmp-settings-style'.CMP_ASSET_SUFFIX.'.css', __FILE__),'', CMP_VERSION );
				wp_register_style( 'cmp-font-awesome',  plugins_url('/css/font-awesome.min.css', __FILE__) );
				wp_register_style( 'countdown_flatpicker_css',  plugins_url('/css/flatpickr.min.css', __FILE__) );
				wp_register_style( 'animate-css',  plugins_url('/css/animate'.CMP_ASSET_SUFFIX.'.css', __FILE__) );
				wp_register_script( 'webfont', 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js', array(), '1.6.26', true );
				wp_register_script( 'cmp-select2-js',  plugins_url('/js/external/select2.min.js', __FILE__) );
				wp_register_script( 'cmp-typography', plugins_url('/js/typography'.CMP_ASSET_SUFFIX.'.js', __FILE__), array('cmp-select2-js' ), CMP_VERSION );
				wp_register_script( 'cmp_settings_js',  plugins_url('/js/settings'.CMP_ASSET_SUFFIX.'.js', __FILE__), array('webfont', 'cmp-select2-js'), CMP_VERSION );
				wp_register_script( 'cmp_advanced_js',  plugins_url('/js/cmp-advanced'.CMP_ASSET_SUFFIX.'.js', __FILE__), array(), CMP_VERSION );
				wp_register_script( 'cmp-editor-translation',  plugins_url('/js/cmp-editor-translation'.CMP_ASSET_SUFFIX.'.js', __FILE__), array(), CMP_VERSION, true );
				wp_register_script( 'countdown_flatpicker_js',  plugins_url('/js/external/flatpickr.min.js', __FILE__) );
				
			}
			
		}

		/**
		 * Add CMP updater proceess
		 *
		 * @since 2.8.7
		 */
		public function cmp_update_process() {
			require_once('inc/cmp-update-process.php');
		}

		/**
		 * create translation if it's has not been created 
		 *
		 * @since 3.7.5
		 */

		public function cmp_create_translation() {
			// Populate translation list, if not yet created
			if ( !get_option('niteoCS_translation') ) {
				$translation = array(
					0 => array('id' => 0, 'name' => 'Counter Seconds Label', 'string' => __('Seconds', 'cmp-coming-soon-maintenance'), 'translation' => __('Seconds', 'cmp-coming-soon-maintenance') ),
					1 => array('id' => 1, 'name' => 'Counter Minutes Label', 'string' => __('Minutes', 'cmp-coming-soon-maintenance'), 'translation' => __('Minutes', 'cmp-coming-soon-maintenance') ),
					2 => array('id' => 2, 'name' => 'Counter Hours Label', 'string' => __('Hours', 'cmp-coming-soon-maintenance'), 'translation' => __('Hours', 'cmp-coming-soon-maintenance') ),
					3 => array('id' => 3, 'name' => 'Counter Days Label', 'string' => __('Days', 'cmp-coming-soon-maintenance'), 'translation' => __('Days', 'cmp-coming-soon-maintenance') ),
					4 => array('id' => 4, 'name' => 'Subscribe Form Placeholder', 'string' => __('Insert your email address.', 'cmp-coming-soon-maintenance'), 'translation' => __('Insert your email address.', 'cmp-coming-soon-maintenance') ),
					5 => array('id' => 5, 'name' => 'Subscribe Response Duplicate', 'string' => __('Oops! This email address is already on our list.', 'cmp-coming-soon-maintenance'), 'translation' => __('Oops! This email address is already on our list.', 'cmp-coming-soon-maintenance') ),
					6 => array('id' => 6, 'name' => 'Subscribe Response Not Valid', 'string' => __('Oops! We need a valid email address. Please try again.', 'cmp-coming-soon-maintenance'), 'translation' => __('Oops! We need a valid email address. Please try again.', 'cmp-coming-soon-maintenance') ),
					7 => array('id' => 7, 'name' => 'Subscribe Response Thanks', 'string' => __('Thank you! Your sign up request was successful.', 'cmp-coming-soon-maintenance'), 'translation' => __('Thank you! Your sign up request was successful.', 'cmp-coming-soon-maintenance') ),
					8 => array('id' => 8, 'name' => 'Subscribe Submit Button Label', 'string' => __('Submit', 'cmp-coming-soon-maintenance'), 'translation' => __('Submit', 'cmp-coming-soon-maintenance') ),
					9 => array('id' => 9, 'name' => 'CMP Eclipse Theme: Scroll Text', 'string' => __('Scroll', 'cmp-coming-soon-maintenance'), 'translation' => __('Scroll', 'cmp-coming-soon-maintenance') ),
					10 => array('id' => 10, 'name' => 'Subscribe Form First Name Placeholder', 'string' => __('First Name', 'cmp-coming-soon-maintenance'), 'translation' => __('First Name', 'cmp-coming-soon-maintenance') ),
					11 => array('id' => 11, 'name' => 'Subscribe Form Last Name Placeholder', 'string' => __('Last Name', 'cmp-coming-soon-maintenance'), 'translation' => __('Last Name', 'cmp-coming-soon-maintenance') ),
					12 => array('id' => 12, 'name' => 'Subscribe', 'string' => __('Subscribe', 'cmp-coming-soon-maintenance'), 'translation' => __('Subscribe', 'cmp-coming-soon-maintenance') ),
					13 => array('id' => 13, 'name' => 'Subscribe GDPR Checkbox', 'string' => __('You must agree with our Terms and Conditions.', 'cmp-coming-soon-maintenance'), 'translation' =>  __('You must agree with our Terms and Conditions.', 'cmp-coming-soon-maintenance') ),
					14 => array('id' => 14, 'name' => 'Subscribe Missing Email', 'string' => __('Oops! Email is empty.', 'cmp-coming-soon-maintenance'), 'translation' =>  __('Oops! Email is empty.', 'cmp-coming-soon-maintenance') ),
				);

				update_option('niteoCS_translation', wp_json_encode( $translation ));
			}
		}

		/**
		 * Register CMP strings to Polylang / WPML
		 *
		 * @since 3.7.5
		 */
		public function cmp_register_wpml_strings() {

			if ( $this->translation_active() ) {

				$themeslug = $this->cmp_selectedTheme();
				$translation = json_decode( get_option('niteoCS_translation'), true );
				$overlay_status = get_option('niteoCS_overlay_text[status]', '0');

				$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Title', stripslashes( get_option('niteoCS_body_title', 'SOMETHING IS HAPPENING!') ) );
				$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Copyright', stripslashes( get_option('niteoCS_copyright', 'Made by <a href="https://niteothemes.com">NiteoThemes</a> with love.') ) );
				$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Subscribe GDPR Message', stripslashes( get_option('niteoCS_subscribe_label', '') ) );
				$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Social Icons Title', stripslashes( get_option('niteoCS_soc_title', 'GET SOCIAL WITH US') ) );
				$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Counter Title', stripslashes( get_option('niteoCS_counter_heading', 'STAY TUNED, WE ARE LAUNCHING SOON...') ) );
				$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Extended Footer Title', stripslashes( get_option('niteoCS_contact_title', 'Quick Contacts') ) );
				$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Extended Footer Content', stripslashes( get_option('niteoCS_contact_content', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.') ) );
				$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Contact Form Title', stripslashes( get_option('niteoCS_contact_form_label', 'Get in Touch') ) );	
				$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'SEO Title', stripslashes( get_option('niteoCS_title', get_bloginfo('name').' Coming soon!') ) );	
				$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'SEO Description', stripslashes( get_option('niteoCS_descr', 'Just Another Coming Soon Page') ) );
				$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Subscribe Popup Form Title', stripslashes( get_option('niteoCS_subscribe_popup_title', get_option('niteoCS_subscribe_title', 'SUBSCRIBE US') ) ) );
				$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Popup Subscribe GDPR Message',  stripslashes(get_option('niteoCS_subscribe_label_popup') ) );
				
				foreach ( $translation as $translate ) {
					$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', $translate['name'], stripslashes( $translate['translation'] ) );
				}

				if ( $overlay_status == '1' && in_array( $themeslug, $this->cmp_overlay_text_themes() ) ) {
					$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Overlay Title', stripslashes( get_option('niteoCS_overlay_text[heading]', 'NEW WEBSITE ON THE WAY!') ) );
					$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Overlay Content', stripslashes( get_option('niteoCS_overlay_text[paragraph]', '') ) );
					$this->cmp_register_string( 'CMP - Coming Soon & Maintenance', 'Overlay Button Text', stripslashes( get_option('niteoCS_overlay_text[button_text]', 'Call to Action!') ) );
				}

				// register strings from themes
				if ( file_exists( $this->cmp_theme_dir( $themeslug ).$themeslug.'/translation-strings.php' ) && function_exists('icl_register_string') ) {
					require_once $this->cmp_theme_dir( $themeslug ).$themeslug.'/translation-strings.php';
				}
			}
		}

		public function cmp_register_string( $group, $name, $string, $multiline = false ) {

			if ( function_exists('pll_register_string') ) {
				pll_register_string( $name, $string, $group, $multiline );
				
			} else if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				do_action( 'wpml_register_single_string', $group, $name, $string );
			}
		}

		/**
		 * Translates given string
		 *
		 * @since 3.7.5
		 * @return string
		 */
		public function cmp_wpml_translate_string( $translation, $name ) {
			
			if ( defined( 'ICL_SITEPRESS_VERSION' ) && version_compare( ICL_SITEPRESS_VERSION, '3.2', '>=' ) ) {
				$translation = apply_filters( 'wpml_translate_single_string', $translation , 'CMP - Coming Soon & Maintenance', $name );
			// WPML and Polylang compatibility
			} elseif ( function_exists( 'icl_t' ) ) {
				$translation = icl_t( 'CMP - Coming Soon & Maintenance', $name, $translation );
			}

			return $translation;
		}

		/**
		 * Translates CMP saved option niteoCS_translation by Polylang / WPML
		 *
		 * @since 3.7.5
		 * @return string
		 */
		public function cmp_wpml_niteoCS_translation() {
			
			$translation = json_decode( get_option('niteoCS_translation'), true );

			if ( defined( 'ICL_SITEPRESS_VERSION' ) && version_compare( ICL_SITEPRESS_VERSION, '3.2', '>=' ) ) {
				foreach ( $translation as $key => $translate ) {
					$translation[$key]['translation'] = apply_filters( 'wpml_translate_single_string', $translate['translation'], 'CMP - Coming Soon & Maintenance', $translate['name'] );
				}
			// WPML and Polylang compatibility
			} elseif ( function_exists( 'icl_t' ) ) {
				foreach ( $translation as $key => $translate ) {
					$translation[$key]['translation'] = icl_t( 'CMP - Coming Soon & Maintenance', $translate['name'], $translate['translation'] );
				}
			}

			return $translation;
		}

		/**
		 * returns current language
		 *
		 * @since 3.7.5
		 * @return string
		 */
		public function cmp_get_current_lang( $type = 'slug' ) {
			$lang = null;

			if ( function_exists('pll_current_language') ) {
				$lang = pll_current_language( $type );

			} else if ( defined('ICL_LANGUAGE_CODE') ) {
				$lang = $type === 'slug' ? ICL_LANGUAGE_CODE : ICL_LANGUAGE_NAME;
			}
			
			return $lang;
		}

		/**
		 * returns configured languages slug
		 *
		 * @since 3.7.5
		 * @return array
		 */
		public function cmp_get_language_list() {
			$langs = array();

			if ( function_exists('pll_languages_list') ) {
				$langs = pll_languages_list();

			} else if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				$langs = apply_filters( 'wpml_active_languages', NULL, array( 'skip_missing' => 0 ) );
				$langs = array_keys( $langs );
			}

			return $langs;
		}

		/**
		 * returns default language slug
		 *
		 * @since 3.7.5
		 * @return string
		 */
		public function cmp_get_default_language() {
			$default = null;

			if ( function_exists('pll_default_language') ) {
				$default = pll_default_language();

			} else if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				global $sitepress;
				$default = $sitepress->get_default_language();
			}

			return $default;
		}

		/**
		 * get translated object by id
		 *
		 * @since 3.7.5
		 * @return object
		 */
		public function cmp_get_translated_id( $id ) {

			if ( function_exists('pll_get_post') ) {
				$id = pll_get_post( $id );

			} else if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
				$id = apply_filters( 'wpml_object_id', $id );
			}

			return $id;
		}

		/**
		 * Define Bundled CMP themes and return them in array
		 *
		 * @since 2.8.3
		 * @return array
		 */
		public function cmp_themes_bundled() {
			return array('construct', 'countdown', 'hardwork');
		}

		public function cmp_theme_supports( $themeslug, $supports ) {

			if ( file_exists($this->cmp_theme_dir( $themeslug ).$themeslug.'/'.$themeslug . '-defaults.php') ) {
				require $this->cmp_theme_dir( $themeslug ).$themeslug.'/'.$themeslug . '-defaults.php';
				
				foreach  ($theme_supports as $key => $value ) {

					if ( $key === $supports ) {
						return $value;
					}
				}
			}

			return false;
		}

		/**
		 * Define CMP Themes Using Font Animation styles
		 *
		 * @since 2.8.3
		 * @return array
		 */
		public function cmp_font_animation_themes() {
			return array( 'hardwork_premium', 'fifty', 'orbit', 'stylo', 'apollo', 'vega', 'pluto' );
		}

		/**
		 * Define CMP Themes Using CF7
		 *
		 * @since 2.8.5
		 * @return array
		 */
		public function cmp_cf7_themes() {
			return array( 'stylo', 'agency' );
		}

		/**
		 * Define CMP Themes supporting Overlay text
		 *
		 * @since 2.8.5
		 * @return array
		 */
		public function cmp_overlay_text_themes() {
			return array( 'agency' );
		}

		/**
		 * Define CMP themes supporting 3rd party page builders
		 *
		 * @since 3.7.3
		 * @return array
		 */
		public function cmp_builder_themes() {
			return array( 'divi', 'elementor', 'oxygen_builder' );
		}

		/**
		 * Check if WPML or Polylang is activated
		 *
		 * @since 3.7.9
		 * @return array
		 */
		public function translation_active() {
			return function_exists('pll_languages_list') || defined('ICL_SITEPRESS_VERSION');
		}
		
		/**
		 * returns array list of premium themes => manually defined
		 *
		 * @return array
		 */
		public function cmp_premium_themes() {
			
			$premium_themes = array();
			array_push( $premium_themes, array('name' => 'loki', 'url' => 'https://niteothemes.com/downloads/cmp-loki-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=loki', 'price' => '12') );
			array_push( $premium_themes, array('name' => 'orion', 'url' => 'https://niteothemes.com/downloads/cmp-orion-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=orion', 'price' => '12') );
			array_push( $premium_themes, array('name' => 'titan', 'url' => 'https://niteothemes.com/downloads/cmp-titan-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=titan', 'price' => '12') );
			array_push( $premium_themes, array('name' => 'saturn', 'url' => 'https://niteothemes.com/downloads/cmp-saturn-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=saturn', 'price' => '12') );
			array_push( $premium_themes, array('name' => 'mercury', 'url' => 'https://niteothemes.com/downloads/cmp-mercury-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=mercury', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'fifty', 'url' => 'https://niteothemes.com/downloads/cmp-fifty-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=fifty', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'atlas', 'url' => 'https://niteothemes.com/downloads/cmp-atlas-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=atlas', 'price' => '12') );
			array_push( $premium_themes, array('name' => 'scout', 'url' => 'https://niteothemes.com/downloads/cmp-scout-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=scout', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'mosaic', 'url' => 'https://niteothemes.com/downloads/cmp-mosaic-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=mosaic', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'libra', 'url' => 'https://niteothemes.com/downloads/cmp-libra-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=libra', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'delta', 'url' => 'https://niteothemes.com/downloads/cmp-delta-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=delta', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'headliner', 'url' => 'https://niteothemes.com/downloads/cmp-headliner-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=headliner', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'elementor', 'url' => 'https://niteothemes.com/downloads/cmp-elementor-addon/?utm_source=cmp&utm_medium=referral&utm_campaign=elementor', 'price' => '29') );
			array_push( $premium_themes, array('name' => 'divi', 'url' => 'https://niteothemes.com/downloads/cmp-divi-addon/?utm_source=cmp&utm_medium=referral&utm_campaign=divi', 'price' => '29') );
			array_push( $premium_themes, array('name' => 'timex', 'url' => 'https://niteothemes.com/downloads/cmp-timex-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=timex', 'price' => '15') );
			array_push( $premium_themes, array('name' => 'thor', 'url' => 'https://niteothemes.com/downloads/cmp-thor-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=thor', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'hardwork_premium', 'url' => 'https://niteothemes.com/downloads/cmp-hardwork-premium/?utm_source=cmp&utm_medium=referral&utm_campaign=hardwork_premium', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'tempie', 'url' => 'https://niteothemes.com/downloads/cmp-tempie-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=tempie', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'stylo', 'url' => 'https://niteothemes.com/downloads/cmp-stylo-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=stylo', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'apollo', 'url' => 'https://niteothemes.com/downloads/cmp-apollo-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=apollo', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'ares', 'url' => 'https://niteothemes.com/downloads/cmp-ares-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=ares', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'juno', 'url' => 'https://niteothemes.com/downloads/cmp-juno-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=juno', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'pluto', 'url' => 'https://niteothemes.com/downloads/cmp-pluto-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=pluto', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'agency', 'url' => 'https://niteothemes.com/downloads/cmp-agency-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=agency', 'price' => '15') );
			array_push( $premium_themes, array('name' => 'vega', 'url' => 'https://niteothemes.com/downloads/cmp-vega-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=vega', 'price' => '10') );			
			array_push( $premium_themes, array('name' => 'element', 'url' => 'https://niteothemes.com/downloads/cmp-element-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=element', 'price' => '10') );			
			array_push( $premium_themes, array('name' => 'postery', 'url' => 'https://niteothemes.com/downloads/cmp-postery/?utm_source=cmp&utm_medium=referral&utm_campaign=postery', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'frame', 'url' => 'https://niteothemes.com/downloads/cmp-frame-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=frame', 'price' => '10') );
			array_push( $premium_themes, array('name' => 'eclipse', 'url' => 'https://niteothemes.com/downloads/cmp-eclipse-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=eclipse', 'price' =>'0') );
			array_push( $premium_themes, array('name' => 'orbit', 'url' => 'https://niteothemes.com/downloads/cmp-orbit-theme/?utm_source=cmp&utm_medium=referral&utm_campaign=orbit', 'price' => '0') );

			return $premium_themes;
		}
		/**
		 * Returns CMP Premium themes from PREMIUM tHEMES DIR
		 *
		 * @since 2.8.3
		 * @return array
		 */
		public function cmp_premium_themes_installed() {
			$premium_themes = array();
			
			if ( file_exists( CMP_PREMIUM_THEMES_DIR ) ) {
				$premium_themes = glob( CMP_PREMIUM_THEMES_DIR . '*', GLOB_ONLYDIR );
				$premium_themes = array_map( 'basename', $premium_themes );
			}
			
		   	return $premium_themes;
		}

		/**
		 * Merge and return bundled themes with premium installed themes
		 *
		 * @since 2.8.3
		 * @return array
		 */
		public function cmp_themes_available() {
		   	return array_merge( $this->cmp_themes_bundled(), $this->cmp_premium_themes_installed() );
		}

		/**
		 * Enqueue admin scripts and styles
		 */
		public function cmp_add_topbar_scripts() {

			// return of user is not logged in
			if ( !is_user_logged_in() ) {
				return;
			}

			// return if Top Bar Icon is disabled
			if ( get_option('niteoCS_topbar_icon', '1') == '0' ) {
				return;
			}

			wp_register_style( 'cmp-admin-head-style',  plugins_url('/css/cmp-admin-head.css', __FILE__), array(), CMP_VERSION);
			wp_register_script( 'cmp_admin_script',  plugins_url('/js/cmp-admin-head.js', __FILE__), array('jquery'), CMP_VERSION);

			$roles_topbar = json_decode( get_option('niteoCS_roles_topbar', '[]'), true );

			// push WP administrator to roles array, since it is default
			array_push( $roles_topbar, 'administrator' );

			

			// get current user
			$current_user = wp_get_current_user();
			// check for roles array length
			if ( count( $current_user->roles ) > 0 ) {
				// enqueue topbar script and style only, if current user is allowed to display topbar, or is admin
				foreach  ( $current_user->roles as $role ) {
					if ( in_array( $role, $roles_topbar ) ) {
						wp_enqueue_style( 'cmp-admin-head-style' );
						wp_enqueue_script( 'cmp_admin_script' );
						wp_localize_script( 'cmp_admin_script', 'cmp_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
						break;
					}
				};

			// this one is for broken wp admin, where current user does not have any roles
			} else {
				wp_enqueue_script( 'cmp_admin_script' );
				wp_enqueue_style( 'cmp-admin-head-style' );
			}

		}

		/**
		 * Add CMP admin style for its settings pages
		 *
		 * @since 2.8.9
		 */
		public function cmp_add_admin_style($hook) {
			// return of user is not logged in
			if ( !is_user_logged_in() ) {
				return;
			}

			$prefix = sanitize_title( __( 'CMP Settings', 'cmp-coming-soon-maintenance' ) );

			$cmp_pages = array(
				'toplevel_page_cmp-settings',
				$prefix.'_page_cmp-advanced',
				$prefix.'_page_cmp-subscribers',
				$prefix.'_page_cmp-translate',
				$prefix.'_page_cmp-upload-theme',
				$prefix.'_page_cmp-themes-manager',
				$prefix.'_page_cmp-addons',
			);

			if ( in_array( $hook, $cmp_pages )  ) {
				wp_enqueue_style( 'cmp-style' );
			}
		}

		/**
		 * Set Plugin assets minified or full path based on DEBUG mode
		 *
		 * @since 2.8.1
		 */
		public function cmp_assets_suffix() {

			// If there's no debug mode, use the minified assets supplied.
			if ( ! defined( 'CMP_DEBUG' ) ) {
				define( 'CMP_ASSET_SUFFIX', '.min' );
				return;
			}

			if ( true === CMP_DEBUG ) {
				define( 'CMP_ASSET_SUFFIX', null );
			} else {
				define( 'CMP_ASSET_SUFFIX', '.min' );
			}
		}


		/**
		 * Register CMP menus pages
		 */
		public function cmp_adminMenu() {
			/* Register our plugin page */
			$page = add_menu_page(__('CMP Settings', 'cmp-coming-soon-maintenance'), __('CMP Settings', 'cmp-coming-soon-maintenance'), 'manage_options', 'cmp-settings', array($this, 'cmp_settings_page'), plugins_url('/img/cmp.png', __FILE__));
			add_submenu_page('cmp-settings', __('CMP Basic Setup', 'cmp-coming-soon-maintenance'), __('CMP Basic Setup', 'cmp-coming-soon-maintenance'), 'manage_options', 'cmp-settings' );
			add_submenu_page('cmp-settings', __('CMP Advanced Setup', 'cmp-coming-soon-maintenance'), __('CMP Advanced Setup', 'cmp-coming-soon-maintenance'), 'manage_options', 'cmp-advanced', array($this, 'cmp_advanced_page') );
			add_submenu_page('cmp-settings', __('CMP Subscribers', 'cmp-coming-soon-maintenance'), __('CMP Subscribers', 'cmp-coming-soon-maintenance'), 'manage_options', 'cmp-subscribers', array($this, 'cmp_subs_page') );
			add_submenu_page('cmp-settings', __('CMP Translation', 'cmp-coming-soon-maintenance'), __('CMP Translation', 'cmp-coming-soon-maintenance'), 'manage_options', 'cmp-translate', array($this, 'cmp_translate_page') );
			add_submenu_page('cmp-settings', __('Upload CMP Theme', 'cmp-coming-soon-maintenance'), __('Upload CMP Theme', 'cmp-coming-soon-maintenance'), 'manage_options', 'cmp-upload-theme', array($this, 'cmp_upload_page') );
			/* Using registered $page handle to hook script load */
			add_action('admin_print_scripts-'.$page, array($this, 'cmp_admin_scripts'));

		}

		/**
		 * enqueue styles and scripts when navigated to CMP Settings page
		 */
		public function cmp_admin_scripts() {
			wp_enqueue_media();
			if ( function_exists( 'wp_enqueue_code_editor' ) ) {
				wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
			}
			wp_localize_script( 'cmp-typography', 'fonts', array( 'google' => $this->cmp_get_google_fonts() ) );
			wp_enqueue_script( 'cmp_settings_js' );
			wp_enqueue_script( 'cmp-typography' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'webfont' );
			wp_enqueue_script( 'cmp-select2-js' );

			if ( $this->translation_active() ) {
				$langs = $this->cmp_get_language_list();
				$default_lang = $this->cmp_get_default_language();
				wp_localize_script( 'cmp-editor-translation', 'translation', array( 'langs' => $langs, 'default' => $default_lang ) );
				wp_enqueue_script( 'cmp-editor-translation' );
			}

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'cmp-font-awesome' );
		
			if ( in_array( $this->cmp_selectedTheme(), $this->cmp_font_animation_themes() ) ) {
				wp_enqueue_style('animate-css');
			}
		}

		function cmp_admin_css() { ?>
			<style type="text/css" media="screen">
			#toplevel_page_cmp-settings img {
				max-width: 14px!important;
				padding-top: 5px!important;
			}
			</style>
			<?php 
		  }

		/**
		 * Render CMP Settings Page
		 */
		public function cmp_settings_page() {

			$this->cmp_check_update( $this->cmp_selectedTheme() );
			require_once ('cmp-settings.php');
		}

		/**
		 * Render CMP Advanced Settings Sub Page
		 */
		public function cmp_advanced_page() {
			wp_enqueue_script( 'cmp-select2-js' );
			wp_enqueue_script( 'cmp_advanced_js' );
			wp_enqueue_style( 'cmp-font-awesome' );
			require_once ('cmp-advanced.php');
		}

		/**
		 * Render CMP Subscribers Sub Page
		 */
		public function cmp_subs_page() {
			require_once ('cmp-subscribers.php');
		}

		/**
		 * Render CMP Translation Sub Page
		 */
		public function cmp_translate_page() {
			require_once ('cmp-translate.php');
		}

		/**
		 * Render CMP Upload new Theme Sub Page
		 */
		public function cmp_upload_page() {
			require_once ('cmp-upload.php');
		}

		/**
		 * Load text domain
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'cmp-coming-soon-maintenance', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Return CMP active status 0 or 1
		 * Default is 0 after CMP plugin installation 
		 * @since 3.1.2
		 * @return string
		 */
		public function cmp_active() {
			return get_option('niteoCS_status', '0') === '0' ? '0': '1';
		}

		/**
		 * Return CMP Mode status
		 *
		 * @since 3.1.2
		 * @return string
		 */
		public function cmp_mode() {
			return get_option( 'niteoCS_activation', '2' );

		}

		/**
		 * returns bundled plugin`s assets URL for Premium Themes
		 *
		 * @since 2.6
		 * @access public
		 * @param string
		 * @return URL string
		 */
		public function cmp_asset_url( $filepath ) {
			return  plugins_url( $filepath, __FILE__ );
		}

		/**
		 * Overrides wp login page, Loggout current user and wp redirect, if cmp is enabled
		 *
		 * @access public
		 */
		// public function cmp_admin_override() {
		// 	// if admin is logged in or CMP is disabled, or not on page filter
		// 	if ( $this->cmp_active() === '0' || $this->cmp_roles_filter() || !$this->cmp_page_filter() ) {
		// 		return;
		// 	}

		// 	wp_logout();
		// 	wp_redirect( get_bloginfo('url') );
		// 	exit();
		// }

		// function to display CMP landing page
		public function cmp_displayPage() {

			$theme = $this->cmp_selectedTheme();

			// register html class for rendering of HTML elements in Themes
			$html = new CMP_Coming_Soon_and_Maintenance_Render_HTML();

			// check if preview is set
			if ( isset($_GET['cmp_preview']) && $_GET['cmp_preview'] == 'true' )  {
				$theme = isset( $_GET['cmp_theme'] ) ? $_GET['cmp_theme'] : $theme;

				if ( !in_array($theme, $this->cmp_themes_available()) ) {
					$theme = $this->cmp_selectedTheme();
				}
				
			    //  finally render theme preview cmp_preview=true
			    if ( file_exists( $this->cmp_theme_dir( $theme ).$theme.'/'.$theme.'-theme.php') ) {
					require_once ( $this->cmp_theme_dir( $theme ).$theme.'/'.$theme.'-theme.php' );
					die();
				}
			}

			// return if CMP is disabled, user bypass by role or page is whitelisted
			if ( $this->cmp_active() === '0' || $this->cmp_roles_filter() || !$this->cmp_page_filter() ) {
				return;
			}

			// bypass CMP and set cookie for user defined period of time, if bypass is enabled,  bypass ID is set, and match CMP bypass settings
			if ( isset( $_GET['cmp_bypass'] ) && $_GET['cmp_bypass'] == get_option('niteoCS_bypass_id', md5( get_home_url() )) && get_option('niteoCS_bypass', '0') == '1' ) {
				nocache_headers();
				header('Cache-Control: max-age=0; private');
				setcookie('cmp_bypass', get_option('niteoCS_bypass_id', md5( get_home_url() ) ), time() + get_option('niteoCS_bypass_expire', '172800'));
				// exit CMP
				return;
			}

			// if bypass Cookie is set, return
			if ( isset($_COOKIE['cmp_bypass']) && $_COOKIE['cmp_bypass'] == get_option('niteoCS_bypass_id', md5( get_home_url() ) ) && get_option('niteoCS_bypass', '0') == '1' ) {
				// exit CMP
				return;
			}

			// if CMP in redirect mode with 0 timeout, die early with redirect
			if ( $this->cmp_mode() == 3 && get_option('niteoCS_redirect_time') == 0 ) {

				$redirect_url = get_option('niteoCS_URL_redirect');
				// redirect to URL
				if ( $redirect_url != '' ) {
  					header('Location: '.esc_url( $redirect_url ));
					die();
				}
			}


			// check for mailpoptin ajax
			if ( isset($_GET['mailoptin-ajax']) ) {

				$campaign_id = get_option('niteoCS_mailoptin_selected');
				$campaign = MailOptin\Core\Repositories\OptinCampaignsRepository::get_optin_campaign_by_id($campaign_id);

				if ( isset($_POST['optin_data']) && $_POST['optin_data']['optin_uuid'] == $campaign['uuid'] ) {
					return;
				}
				
			}

			// if themes with countdown timer
			if ( $this->cmp_theme_supports( $theme, 'counter' ) ) {
				
				// if counter is enabled - default yes
				if ( get_option('niteoCS_counter', '1') == '1' ) {

					// if countdown date is set - default 24 hours
					if ( get_option('niteoCS_counter_date' ) && get_option('niteoCS_counter_date' ) != '' ) {
						
						// if timer < timestamp do pre-set action
						if ( get_option('niteoCS_counter_date' ) < time() ) {

							$action = get_option('niteoCS_countdown_action', 'no-action');

							// send notification email if email transient is not set
							if ( get_option('niteoCS_countdown_notification', '1') == '1' && get_option( 'niteoCS_counter_email', false ) !== 'sent' ) {

								switch ( $action ) {
									case 'no-action':
										$message = __('Counter expired but it is set to make no action - you should login to your Wordpress Admin and adjust the expired timer or disable Coming Soon / Maintenance Mode.', 'cmp-coming-soon-maintenance');
										break;
									case 'hide':
										$message = __('Counter expired and and it is hidden on your website per settings.', 'cmp-coming-soon-maintenance');
										break;

									case 'disable-cmp':
										$message = __('Counter expired and Coming soon / Maintanance mode was disabled.', 'cmp-coming-soon-maintenance');
										break;

									case 'redirect':
										$message = __('Counter expired and your Website is redirected to external URL per settings.', 'cmp-coming-soon-maintenance');
										break;

									default:
										$message = '';
										break;
								}

								$to = get_option('niteoCS_countdown_email_address', get_option( 'admin_email' ));
								$subject = 'Countdown timer just expired on your Coming Soon Page - ' . get_site_url();
								$body = $message . ' This is auto generated message from CMP - Coming Soon & Maintenance Plugin installed on ' . get_site_url();
								$headers = array('Content-Type: text/plain; charset=UTF-8');
								// send email
								wp_mail( $to, $subject, $body, $headers );

								// set email option
								update_option( 'niteoCS_counter_email', 'sent' );

							}

							
							// if action set to redirect
							if ( $action == 'redirect' ) {
								$redirect_url = esc_url( get_option('niteoCS_countdown_redirect') );
								header('Location: '.$redirect_url);
								die();
							}

						}
					}
				}
			}

			// if maintanance mode send correct 503 headers
			if ( $this->cmp_mode() == '1' ) {
				header('HTTP/1.1 503 Service Temporarily Unavailable');
				header('Status: 503 Service Temporarily Unavailable');
				header('Retry-After: 86400'); // retry in a day
			}

			// send no-cache headers if set in Settings
			if ( get_option('niteoCS_seo_nocache', '1') == '1' ){
				nocache_headers();
				header('Cache-Control: no-cache; private');
			}

			// set cookie for WPML AJAX translation
			if ( defined('ICL_LANGUAGE_CODE') ) {
				setcookie('wp-wpml_current_language', ICL_LANGUAGE_CODE, time()+3600, '/');
				setcookie('_icl_current_language', ICL_LANGUAGE_CODE, time()+3600, '/');
			}

			// finally render selected CMP theme
			if ( file_exists( $this->cmp_theme_dir( $theme ).$theme.'/'.$theme.'-theme.php') ) {
				require_once ( $this->cmp_theme_dir( $theme ).$theme.'/'.$theme.'-theme.php' );
				die();
			}

		}

	    // function to toggle CMP activation for admin menu icon
	    public function cmp_ajax_toggle_activation() {
			// check for ajax payoload
			if ( isset( $_POST['payload'] ) && $_POST['payload'] == 'toggle_cmp_status' ) {

				// verify nonce
				check_ajax_referer( 'cmp-coming-soon-ajax-secret', 'security' );
				// verify user rights
		    	if ( !$this->cmp_user_can_admin_bar_activation() ) {
			    	echo 'Current user cannot toggle CMP activation';
			    	wp_die();
			    	return;
		    	}

		    	if ( $this->cmp_active() === '0' ) {
					update_option('niteoCS_status', '1');
					$this->cmp_send_notification('on');

		    	} else {
					update_option('niteoCS_status', '0');
					$this->cmp_send_notification('off');
		    	}

		    	$this->cmp_purge_cache();

		    	echo 'success';
		    	wp_die();
		    	return;
		    }
	    }

		// return selected theme, defaults to hardwork
		public function cmp_selectedTheme() {
			return get_option('niteoCS_theme', 'hardwork');
		}


		// return installed theme dir path
		public function cmp_theme_dir( $slug ) {
			if ( in_array( $slug, $this->cmp_themes_bundled() ) ) {
				return CMP_PLUGIN_DIR . 'themes/';

			} else {
				return CMP_PREMIUM_THEMES_DIR;
			}
		}

		// return installed theme URL
		public function cmp_themeURL( $slug ) {
			if ( in_array( $slug, $this->cmp_themes_bundled() ) ) {
				return plugins_url( '/themes/', __FILE__ );

			} else {
				return plugins_url( '/cmp-premium-themes/');
				
			}
		}

		// display admin topbar notice
	    public function cmp_admin_bar() {

			$prefix = sanitize_title( __( 'CMP Settings', 'cmp-coming-soon-maintenance' ) );

			if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				if ( current_user_can('publish_pages') && function_exists('get_current_screen') && is_admin() && get_current_screen()->id == $prefix.'_page_cmp-advanced' && wp_verify_nonce( $_POST['save_options_field'], 'save_options' ) ) {
			    	// check POST if user wants to enable or disable the topbar from settings
					if ( isset( $_POST['niteoCS_topbar_icon'] ) && is_numeric( $_POST['niteoCS_topbar_icon'] ) ) {
						update_option('niteoCS_topbar_icon', sanitize_text_field( $_POST['niteoCS_topbar_icon'] ));
					}

					if ( isset( $_POST['niteoCS_topbar_version'] ) ) {
						update_option('niteoCS_topbar_version', sanitize_text_field( $_POST['niteoCS_topbar_version'] ));
					}
				}
			}
			

	    	if ( !$this->cmp_user_can_admin_bar_activation() ) {
	    		return false;
	    	}

			// CMP PLUGIN ACTIVATION AND STATUS CHANGE SETTINGS!
			if ( $_SERVER['REQUEST_METHOD'] == 'POST' && ( function_exists('get_current_screen') && is_admin() && get_current_screen()->id == 'toplevel_page_cmp-settings' ) ) {

				// verify nonce and user rights
				if ( !wp_verify_nonce($_POST['save_options_field'], 'save_options') || !current_user_can('publish_pages') ) {
					die('Sorry, but this request is invalid');
				} 

				if ( isset($_POST['activate']) && is_numeric($_POST['activate']) ) {
					update_option('niteoCS_activation', sanitize_text_field($_POST['activate']));
				}

				if ( isset($_POST['cmp_status']) ) {
					update_option('niteoCS_status', $this->sanitize_checkbox($_POST['cmp_status']));
					$this->cmp_send_notification('on');

				} else if ( !isset($_POST['submit_theme']) ) {
					update_option('niteoCS_status', '0');
					$this->cmp_send_notification('off');
				}
			}

	    	// create nonce for ajax request
			$ajax_nonce = wp_create_nonce( 'cmp-coming-soon-ajax-secret' );

	        global $wp_admin_bar;

			$class = '';
			$msg = '';
			$icon = '<img src="'.plugins_url('/img/cmp.png', __FILE__).'" alt="CMP Logo" class="cmp-logo" style="max-width:20px;">';

			$topbar_version = get_option('niteoCS_topbar_version', 'cmp-topbar-full');

			if ( $topbar_version === 'cmp-topbar-full' ) {
				switch ( $this->cmp_mode() ) {
					case '1':
						$msg = __('Maintenance Mode:', 'cmp-coming-soon-maintenance');
						$class = ' maintenance';
						break;
					case '2':
						$msg = __('Coming Soon Mode:', 'cmp-coming-soon-maintenance');
						$class = ' coming-soon';
						break;
					case '3':
						$msg = __('Redirect Mode:', 'cmp-coming-soon-maintenance');
						$class = ' redirect';
						break;
					default:
						break;
				}

				$msg = '<span class="cmp-status-msg ab-label">'.$msg.'</span>';
			}

			
	        $topbar = $icon.$msg.'<div class="toggle-wrapper"><div id="cmp-status-menubar" class="toggle-checkbox"></div><div id="cmp-toggle-adminbar" class="status-' . esc_attr( $this->cmp_active() ) . '" data-security="'. esc_attr( $ajax_nonce ).'"><span class="toggle_handler"></span></div></div>';

	    	//Add the main siteadmin menu item
	        $wp_admin_bar->add_menu( array(
	            'id'     => 'cmp-admin-notice',
	            'href' => admin_url().'admin.php?page=cmp-settings',
	            'parent' => 'top-secondary',
	            'title'  => $topbar,
	            'meta'   => array( 'class' => 'cmp-notice'.$class ),
	        ) );

	        // Display CMP Settings in topbar only for administrator
			if ( current_user_can( 'administrator' ) ) {
			    $wp_admin_bar->add_node( array(
			    	'id'     => 'cmp-basic-settings',
			    	'title'  => __('CMP Basic Settings', 'cmp-coming-soon-maintenance'),
			    	'href'   => admin_url('admin.php?page=cmp-settings'),
			    	'parent' => 'cmp-admin-notice'
				));
				
			    $wp_admin_bar->add_node( array(
			    	'id'     => 'cmp-advanced-settings',
			    	'title'  => __('CMP Advanced Settings', 'cmp-coming-soon-maintenance'),
			    	'href'   => admin_url('admin.php?page=cmp-advanced'),
			    	'parent' => 'cmp-admin-notice'
			    ));
			}

		    $wp_admin_bar->add_node( array(
		    	'id'    => 'cmp-preview',
		    	'title' => __('CMP Preview', 'cmp-coming-soon-maintenance'),
		    	'href'  => get_site_url().'/?cmp_preview=true',
		    	'parent'=> 'cmp-admin-notice',
		    	'meta' => array('target' => '_blank' )
		    ));

	    }

		public function cmp_activate() {

			if ( get_option('niteoCS_archive') ) {
				//get all the options back from the archive
				$options = get_option('niteoCS_archive');
				// update options
				foreach ($options as $option) {
					update_option($option['name'], $option['value']);
				}

				update_option( 'niteoCS_activation_notice', false );

				// delete archive
				delete_option('niteoCS_archive');
			}
		}

		// archive plugin stuff when plugin is deactivated
		public function cmp_deactivate() {
			//get all the options. store them in an array
			$options = array();

			global $wpdb;
			$saved_options = $wpdb->get_results( "SELECT * FROM $wpdb->options WHERE option_name LIKE 'niteoCS_%'", OBJECT );
			$i = 0;
			foreach ($saved_options as $option) {
				$options[$i] = array('name' => $option->option_name, 'value' => get_option( $option->option_name) );
				$i++;
			}

			//store the options all in one record, in case we ever reactivate the plugin
			update_option('niteoCS_archive', $options);

			//delete the separate ones
			foreach ( $options as $option ) {
				delete_option($option['name']);
			}

			$this->cmp_purge_cache();
		}

		/**
		 * Difference between Premium Themes installed and Premium Themes available sets in cmp_premium_themes() function.
		 *
		 * @since 2.2
		 * @access public
		 * @return array
		 */
		public function cmp_downloadable_themes() {
			$downloadable_themes = array();

			foreach ( $this->cmp_premium_themes() as $premium ) {
				if ( !in_array($premium['name'], $this->cmp_premium_themes_installed()) ) {
					array_push( $downloadable_themes, $premium );
				} 
			}

			return $downloadable_themes;
		}

		/**
		 * Disable CMP Coming Soon Mode via AJAX
		 *
		 * @since 3.7.6
		 * @access public
		 * @return JSON
		 */
		public function cmp_disable_comingsoon_ajax() {

			$theme = $this->cmp_selectedTheme();

			if ( !in_array( $theme, $this->cmp_builder_themes() ) ) {
				check_ajax_referer( 'cmp-coming-soon-maintenance-nonce', 'security' );
			}

			$result = array( 'message' => 'error');

			if ( get_option('niteoCS_countdown_action', 'no-action') !== 'disable-cmp' ) {
				echo json_encode( $result );
				wp_die();
			}

			if ( !empty( $_REQUEST['status'] ) && $_REQUEST['status'] === 'disable-cmp' && get_option('niteoCS_counter_date' ) < time() ) {
				update_option('niteoCS_status', '0');
				$this->cmp_purge_cache();
				$result = array( 'message' => 'success');	
			}
			
			echo json_encode( $result );

			wp_die();
		}

		// theme updates function
		public function cmp_check_update( $theme_slug ) {

			$ajax = false;
			// check for ajax 
			if ( isset( $_POST['theme_slug'] ) ) {
				// verify nonce
				check_ajax_referer( 'cmp-coming-soon-ajax-secret', 'security' );
				// verify user rights
				if( !current_user_can('publish_pages') ) {
					die('Sorry, but this request is invalid');
				}

				// sanitize array
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

				if ( !empty( $_POST['theme_slug'] ) ) {
					$theme_slug = $_POST['theme_slug'];
					$ajax   = true;
				}
		    }

			if ( !in_array( $theme_slug, $this->cmp_premium_themes_installed() ) ) {
				return;
			}

			// check for current theme version
			$remote_version = '';
			$current_version = '';

			if ( CMP_DEBUG === TRUE ) {
				delete_transient( $theme_slug.'_updatecheck' );
			}

			// always check if update check transient is set or ajax request
			if ( false === ( $updatecheck_transient = get_transient( $theme_slug.'_updatecheck' ) ) || $ajax === TRUE ) {

				$current_version = $this->cmp_theme_version($theme_slug);
				// get remote version from  remote server
				$request = wp_remote_post( CMP_UPDATE_URL .'?action=get_metadata&slug='.$theme_slug, array('body' => array('action' => 'version')) );
				
				// if no error, retrivee body
			    if ( !is_wp_error( $request ) ) {

			    	// decode to json
			        $remote_version = json_decode( $request['body'], true );

			        // get remove version key
			        if ( isset($remote_version['version']) ) {

			        	$remote_version = $remote_version['version'];

			        	// if remote version is bigger than current, display info about new version
			        	if ( (float)$remote_version > (float)$current_version ) {

			        		$title = ucwords(str_replace('_', ' ', $theme_slug));

			        		// create nonce
			        		$ajax_nonce = wp_create_nonce( 'cmp-coming-soon-ajax-secret' );

			        		// if admin screen is not in updating theme
			        		if (!isset($_GET['theme']) || (isset($_GET['theme']) && $_GET['theme'] != $theme_slug)) {

			        			$transient = '<div class="notice notice-warning"><p class="message">'.sprintf(__('There is a <b>recommended</b> update of <b>CMP Theme: %s</b> available:', 'cmp-coming-soon-maintenance'), $title).' <a href="'.admin_url().'options-general.php?page=cmp-settings&action=update-cmp-theme&theme='.esc_attr($theme_slug).'&type=premium" class="cmp update-theme" data-type="premium" data-security="'.esc_attr($ajax_nonce).'" data-slug="'.esc_attr($theme_slug).'" data-name="'.esc_attr($title).'" data-remote_url="' . esc_url( CMP_UPDATE_URL ) . '" data-new_ver="' . esc_attr( $remote_version ) . '">'.sprintf(__(' click to update to %s version from NiteoThemes server now','cmp-coming-soon-maintenance'), esc_attr( $remote_version )).'!</a></div>';

			        			// set transient with 12 hour expire
			        			set_transient( $theme_slug.'_updatecheck', $transient, 60*60*12 );

			        			// die early if this is ajax request with status = true
			        			if ( $ajax ) {
									wp_die($remote_version);
									return;
								}


			        			echo $transient;
			        		}

			        	} else {
			        		// die early if this is ajax request with status = false
			        		if ( $ajax ) {
								wp_die('false');
								return;
							}
			        		// set transient no update available with 12 hours expire
			        		set_transient( $theme_slug.'_updatecheck', '', 60*60*12 );
			        	}

			        }
			    }

			// empty transient means theme was updated in last 24 hours
			} else if ( $updatecheck_transient != '' ) {

				echo $updatecheck_transient;
			}

    		if ( $ajax ) {
				wp_die('false');
			}

			return;
		}

		public function cmp_theme_upload($uploadedfile) {

			if ( !current_user_can('administrator') ) {
				return false;
			}

			// allow zip file to upload
			add_filter('upload_mimes', array( $this, 'cmp_allow_mimes') );

		    // load PHP WP FILE 
			if ( ! function_exists( 'wp_handle_upload' ) ) {
			    require_once realpath('../../../wp-admin/includes/file.php');
			}

			$filename		= $uploadedfile['name'];
			/* You can use wp_check_filetype() public function to check the
			 file type and go on wit the upload or stop it.*/
			$filetype = wp_check_filetype( $filename );

			if ( $filetype['ext'] == 'zip' ) {
				// Upload file
				$movefile = wp_handle_upload( $uploadedfile, array('test_form' => FALSE) );

				if ( $movefile && !isset( $movefile['error'] ) ) {

					WP_Filesystem();
			        $source_path		= $movefile['file'];
			        $theme_name			= str_replace('.zip', '', $filename);
					$destination_path	= CMP_PREMIUM_THEMES_DIR;

					// create new theme DIR
					if ( wp_mkdir_p( $destination_path ) ) {
						// Unzip FILE into that DIR
						$unzipfile = unzip_file( $source_path, $destination_path);
						   
						   if ( $unzipfile ) {
						   		// delete FILE
								wp_delete_file( $source_path );
								
								if ( $theme_name == 'pluto' ) {
									delete_option('niteoCS_banner');
								}
								echo '<div class="notice notice-success is-dismissible"><p class="message">'.ucwords(str_replace('_', ' ', $theme_name)).' '.__(' theme was successfully installed!', 'cmp-coming-soon-maintenance').'</p></div>';
								return;

						   } else {
						   		echo '<div class="notice notice-error is-dismissible"><p>'.__('There was an error unzipping the file!', 'cmp-coming-soon-maintenance').'</p></div>';   
						   		return;
						   }

					} else {
						echo '<div class="notice notice-error is-dismissible"><p>'.__('Error creating Theme subdirectory!', 'cmp-coming-soon-maintenance').'</p></div>';   
						return;
					}

				} else {
				    /**
				     * Error generated by _wp_handle_upload()
				     * @see _wp_handle_upload() in wp-admin/includes/file.php
				     */
				    echo '<div class="notice notice-error is-dismissible"><p>'.$movefile['error'].'</p></div>'; 
				    return;
				}
			} else {
				echo '<div class="notice notice-error is-dismissible"><p>'.__('Unable to upload new Theme file .', 'cmp-coming-soon-maintenance'). strtoupper($filetype['ext']) .__(' file extension is not supported. Please upload ZIP file containing CMP Theme.', 'cmp-coming-soon-maintenance').'</p></div>';  
				return;
			}

			add_filter('upload_mimes', array($this, 'cmp_remove_mimes'));
			return;
		}

		public function cmp_theme_update_install( $file ) {
			$ajax = false;
			// check for ajax 
			if ( isset( $_POST['file'] ) ) {
				// verify nonce
				check_ajax_referer( 'cmp-coming-soon-ajax-secret', 'security' );
				// verify user rights
				if( !current_user_can('publish_pages') ) {
					die('Sorry, but this request is invalid');
				}

				// sanitize array
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

				if ( !empty( $_POST['file'] ) ) {
					$file = $_POST['file'];
					$ajax   = true;
				}
		    }

		    // load PHP WP FILE 
			if ( ! empty( $file ) ) {
				// Download file to temp location.
				$file['tmp_name'] = download_url( $file['url'] );
				//WARNING: The file is not automatically deleted, The script must unlink() the file.

				// If error storing temporarily, return the error.
				if ( !is_wp_error( $file['tmp_name'] ) ) {
					WP_Filesystem();

					// create new theme DIR
					if ( wp_mkdir_p( CMP_PREMIUM_THEMES_DIR ) ) {
						// Unzip FILE into that DIR
						$unzipfile = unzip_file( $file['tmp_name'], CMP_PREMIUM_THEMES_DIR );

						if ( !is_wp_error( $unzipfile ) ) {
							// delete tmp FILE
							wp_delete_file( $file['tmp_name'] );

							// set transient no update available with 24 hours expire
							set_transient( $file['name'] . '_updatecheck', '', 60*60*24 );

							// die
							if ( $ajax ) {
								wp_die('success');
								return;

							} else {
								echo '<div class="notice notice-success is-dismissible"><p>CMP '. ucwords( str_replace( '_', ' ', $file['name'] ) ) .' '. __( 'Theme has been updated to latest version!', 'cmp-coming-soon-maintenance' ).'</p></div>';
								return;
							}

						} else {
								echo '<div class="notice notice-error is-dismissible"><p>'.__('There was an error unzipping the file due to error: ', 'cmp-coming-soon-maintenance') . $unzipfile->get_error_message().'</p></div>';

							if ( $ajax ) {
								wp_die('error');
								return;
							} 
						}

					} else {
						echo '<div class="notice notice-error is-dismissible"><p>'.__('Error creating Theme subdirectory!', 'cmp-coming-soon-maintenance').'</p></div>';   
						if ( $ajax ) {
							wp_die('error');
							return;
						}
					}

				} else {
					echo '<div class="notice notice-error is-dismissible"><p>'.__('Error during updating Theme files:', 'cmp-coming-soon-maintenance').' '.$file['tmp_name']->get_error_message().'</p></div>'; 
					if ( $ajax === true ) {
						wp_die('error');
						return;
					} 
				}
			} else {

				echo '<div class="notice notice-error is-dismissible"><p>'.__('General Error during updating Theme files.', 'cmp-coming-soon-maintenance').'</p></div>';  
				if ( $ajax === true ) {
					wp_die('error');
					return;
				} 
			}

			return;
		}


		// build unsplash api
		public function cmp_unsplash_api ( $query ) {

			$api_url = 'https://api.unsplash.com/'.$query.'&client_id=41f043163758cf2e898e8a868bc142c20bc3f5966e7abac4779ee684088092ab' ;
			
			if ( function_exists( 'wp_remote_get' ) ) {

				$response = wp_remote_get( $api_url );

				if ( !is_object( $response ) && isset( $response['body'] ) ) {

					$body = $response['body'];
					$data = array( 'response' => $response['response']['code'], 'body' => $body );

				} else {
					$data = array( 'response' => 'Unplash API', 'body' => 'Not responding after 5000ms' );
				}

			} else {
			    $data = array( 'response' => '500', 'body' => 'You have neither cUrl installed nor allow_url_fopen activated. Ask your server hosting provider to allow on of those options.' );
			}

			return $data;
		}

		// prepare unsplash url and get unsplash photo via cmp_unsplash_api()
		public function niteo_unsplash( $params ) {
			$ajax = false;

			// check for ajax 
			if ( isset( $_POST['params'] ) ) {
				// verify nonce
				check_ajax_referer( 'cmp-coming-soon-ajax-secret', 'security' );
				// verify user rights
				if( !current_user_can('publish_pages') ) {
					die('Sorry, but this request is invalid');
				}

				// sanitize array
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

				if ( !empty( $_POST['params'] ) ) {
					$params = $_POST['params'];
					$ajax   = true;
				}
		    }

		    array_key_exists ('feed', $params) 			? $feed 		= $params['feed'] 		: $feed = '';
		    array_key_exists ('url', $params)			? $url 			= $params['url'] 		: $url = '';
		    array_key_exists ('feat', $params)			? $feat 		= $params['feat'] 		: $feat = '';
		    array_key_exists ('custom_str', $params)	? $custom_str 	= $params['custom_str'] : $custom_str = '';
		    array_key_exists ('count', $params)			? $count 		= $params['count'] 		: $count = '1';

			switch ( $feed ) {
				// specific unsplash photo by url/id
				case '0':
					$id = '';
					// check if $query contains unsplash.com url
					if ( strpos( $url, 'unsplash.com' ) !== false ) {
						$parts = parse_url( $url );
						// check for photo parameter in URL
						if ( isset($parts['query'])) {
							parse_str($parts['query'], $query);
						    $id = $query['photo'];
						}
					    // if no ID found, get last part of URL containing ID
					    if ( $id == '' ) {

							$pathFragments = explode('/', $parts['path']);
							$id = end($pathFragments);
					    }

					// $query is ID
					} else {
						$id = $url;
					}

					// prepare query for single image
					$api_query = 'photos/'.$id.'?';
					break;

				// random from user
				case '1':

					if ( $custom_str[0] == '@' ) {
						$custom_str = substr($custom_str, 1);
					}

					// prepare query for random photo from collection
					$api_query = 'photos/random/?username='.$custom_str.'&count='.$count;
					break;

				// random from collection
				case '2':
					if ( is_numeric( $url ) ) {
						$collection = $url;
					} else {
						$collection = filter_var($url, FILTER_SANITIZE_NUMBER_INT);
						$collection = str_replace('-', '', $collection );
					}

					// prepare query for random photo from collection
					$api_query = 'photos/random/?collections='.$collection.'&count='.$count;
					break;

				// random photo
				case '3':

					// featured
					if ( $feat == '0' || $feat == '') {
						$featured = 'false';
					} else {
						$featured = 'true';
					}

					// category
					$search = str_replace(' ', ',', $url);

					if ( $search !== '' ) {
						$search = 'query='.$search.'&';
					}
					// prepare query for random photo
					$api_query = 'photos/random/?orientation=landscape&featured='.$featured.'&'.$search.'count='.$count;
					break;

				default:
					$api_query = 'photos/random/?orientation=landscape&count='.$count;
					break;
			}

			$unsplash_img = $this->cmp_unsplash_api( $api_query );

			if ( $ajax === true ) {
				echo json_encode($unsplash_img);
				wp_die();

			} else {
				return $unsplash_img;
			}
		}
		
		// check value in multidimensional array
		public function niteo_in_array_r($needle, $haystack, $strict = false) {
		    foreach ( $haystack as $item ) {
		        if ( ( $strict ? $item === $needle : $item == $needle ) || ( is_array( $item ) && $this->niteo_in_array_r( $needle, $item, $strict ) ) ) {
		            return true;
		        }
		    }

		    return false;
		}
		/**
		 * Recaptcha integration
		 *
		 * @since 3.6.16
		 * @access public
		 * @return boolean
		 */
		public function is_human( $token ) {

			$this->define( 'RECAPTCHA_SECRET', get_option('niteoCS_recaptcha_secret') );

			$request = array(
				'body' => array(
					'secret' => RECAPTCHA_SECRET,
					'response' => $token,
				),
			);

			$response = wp_remote_post( esc_url_raw( 'https://www.google.com/recaptcha/api/siteverify' ), $request );
			$response_body = wp_remote_retrieve_body( $response );
			$recaptcha = json_decode( $response_body, true );

			if ( $recaptcha['success'] === true & $recaptcha['score'] > 0.50 ) {
				return true;
			}

			return false;

		}

		// save subscribe function
		// $check must be true, to avoid duplicated requests after update to 2.1
		public function niteo_subscribe( $check ) {

			$subscribe_method = get_option('niteoCS_subscribe_method', 'cmp');
			$response = '';

			// get translation lists
	        if ( get_option('niteoCS_translation') ) {
	            $translation    		= json_decode( get_option('niteoCS_translation'), TRUE );
	            $response_ok    		= $this->cmp_wpml_translate_string( $translation[7]['translation'], 'Subscribe Response Thanks' );
	            $response_duplicate 	= $this->cmp_wpml_translate_string( $translation[5]['translation'], 'Subscribe Response Duplicate' );
	            $response_invalid 		= $this->cmp_wpml_translate_string( $translation[6]['translation'], 'Subscribe Response Not Valid' );
	        }

			$ajax = ( isset( $_POST['ajax'] ) && $_POST['ajax'] == TRUE ) ? TRUE : FALSE;

			if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['form_honeypot'] ) && $_POST['form_honeypot'] === '' && isset( $_POST['email'] ) ) :
				
				if  ( $ajax ) {
					check_ajax_referer( 'cmp-subscribe-action', 'security' );
				}

				// check recatpcha score if integration is enabled
				if ( get_option( 'niteoCS_recaptcha_status', '1' ) === '1' && !empty(get_option('niteoCS_recaptcha_site', ''))) {
					if ( !$this->is_human( sanitize_text_field( $_POST['token'] ) ) ) {
						echo json_encode( array( 'status' => '0', 'message' => 'Sorry, robots not allowed.') );
						wp_die();
					}
				}

	        	if ( filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL ) ) {
	        		// email already passed is_email, no need to sanitize
	        		$email = sanitize_email( $_POST['email'] );

	        		// sanitize all inputs
	        		$ip_address = ( isset( $_POST['lastname'] ) ) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '';
	        		$firstname = ( isset( $_POST['firstname'] ) ) ? sanitize_text_field( $_POST['firstname'] ) : '';
	        		$lastname = ( isset( $_POST['lastname'] ) ) ? sanitize_text_field( $_POST['lastname'] ) : '';
					$timestamp = time();

	        		switch ( $subscribe_method ) {
	        			// default custom CMP method
	        			case 'cmp':
							// get subscribe list 
							$subscribe_list = get_option('niteoCS_subscribers_list');

							// if no subscribe list yet, create first item and insert it into DB
							if ( !$subscribe_list ) {
								$new_list = array();
								$new_email = array( 'id' => '0', 'timestamp' => $timestamp, 'email' => $email, 'ip_address' => $ip_address, 'firstname' => $firstname, 'lastname' => $lastname );
								array_push( $new_list, $new_email );
								update_option( 'niteoCS_subscribers_list', $new_list );
								$response = array( 'status' => '1', 'message' => $response_ok);

							} else {
								// check if email don`t already exists
								if ( !$this->niteo_in_array_r( $email, $subscribe_list, true ) ) {
									$count = count( $subscribe_list );
									$new_email = array( 'id' => $count, 'timestamp' => $timestamp, 'email' => $email, 'ip_address' => $ip_address, 'firstname' => $firstname, 'lastname' => $lastname );
									array_push( $subscribe_list, $new_email );
									update_option('niteoCS_subscribers_list', $subscribe_list);
									$response = array( 'status' => '1', 'message' => $response_ok);
									// sent notif email
									if ( get_option('niteoCS_subscribe_notification', '0') ) {
										$subscribe_notif_email = get_option('niteoCS_subscribe_email_address', get_option( 'admin_email' ));
										$subject = sprintf(__('You have a new Subscriber on %s!', 'cmp-coming-soon-maintenance'), get_site_url());
										$body = __('This is auto generated message from CMP - Coming Soon & Maintenance WordPress Plugin. You can disable these emails under CMP Advanced Settings > Email Notifications.', 'cmp-coming-soon-maintenance');
										$headers = array('Content-Type: text/plain; charset=UTF-8');
										wp_mail( $subscribe_notif_email, $subject, $body, $headers );
									}

								// if email exists return duplicate response
								} else {
									$response = array( 'status' => '0', 'message' => $response_duplicate);
								}
							}
	        				break;

	        			// mailchimp API call
	        			case 'mailchimp':
							$api_key 	= esc_attr( get_option('niteoCS_mailchimp_apikey') );
							$list_id 	= esc_attr( get_option('niteoCS_mailchimp_list_selected') );
							$double_opt = get_option( 'niteoCS_mailchimp[double-opt]', '0' );
							$status 	= ( $double_opt == '1') ? 'pending' : 'subscribed'; // subscribed, cleaned, pending
							
							$args = array(
								'method' => 'PUT',
							 	'headers' => array(
									'Authorization' => 'Basic ' . base64_encode( 'user:'. $api_key )
								),
								'body' => json_encode(array(
							    	'email_address' 	=> $email,
									'status'        	=> $status,
									'merge_fields' 		=> array(
									    'FNAME'		=> $firstname,
									    'LNAME'		=> $lastname
									)
								))
							);

							$mailchimp = wp_remote_post( 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/'. $list_id .'/members/' . md5(strtolower($email)), $args );
							
							if ( !is_wp_error( $mailchimp ) ) {

								$body = json_decode( $mailchimp['body'] );

								if ( $mailchimp['response']['code'] == 200 && $body->status == $status ) {
									$response = array( 'status' => '1', 'message' => $response_ok);

								} else {
									$response = array( 'status' => '0', 'message' => 'Error ' . $mailchimp['response']['code'] . ' ' . $body->title . ': ' . $body->detail);
								}

							} else {
								$error = $mailchimp->get_error_message();
								$response = array( 'status' => '0', 'message' => $error);
							}

							break;

						// MailPoet integration
						case 'mailpoet':
							$response = array( 'status' => '0', 'message' => __('Something went wrong please try again later.', 'cmp-coming-soon-maintenance') );
							$mailpoet_list = get_option('niteoCS_mailpoet_list_selected');
							$list_ids = array($mailpoet_list);
							$firstname = $firstname === '' ? null : $firstname;
							$lastname = $lastname === '' ? null : $lastname;
							$subscriber = array(
								'email' => $email,
								'first_name' => $firstname,
								'last_name' => $lastname
							);

							if ( class_exists(\MailPoet\API\API::class) ) {
								// Get MailPoet API instance
								$mailpoet_api = \MailPoet\API\API::MP('v1');

								// Check if subscriber exists. If subscriber doesn't exist an exception is thrown
								try {
									$subscribed = $mailpoet_api->getSubscriber( $subscriber['email'] );
								} catch (\Exception $e) {}

								try {
									if (!$subscribed) {
										// Subscriber doesn't exist let's create one
										$mailpoet_api->addSubscriber( $subscriber, $list_ids );
										$response = array( 'status' => '1', 'message' => $response_ok );

									} else {
										// In case subscriber exists just add him to new lists
										$mailpoet_api->subscribeToLists( $subscriber['email'], $list_ids );
										$response = array( 'status' => '1', 'message' => $response_ok );

									}
								} catch (\Exception $e) {
									$error_message = $e->getMessage(); 
									$response = array( 'status' => '0', 'message' => $error_message );
								}

							}

						break;

						// Mailster integration
						case 'mailster':
							$response = array( 'status' => '0', 'message' => __('Something went wrong please try again later.', 'cmp-coming-soon-maintenance') );
							$mailster_list_id = get_option('niteoCS_mailster_list_selected');

							if ( function_exists( 'mailster' ) ){
								// define to overwrite existing users
								$overwrite = true;

								// add with double opt in
								$double_opt_in = true;

								$subscriber = array(
									'email' => $email,
									'firstname' => $firstname,
									'lastname' => $lastname,
									'status' => get_option( 'niteoCS_mailster_double_opt', '1' ) ? 0 :1
								);

								// add a new subscriber and $overwrite it if exists
								$subscriber_id = mailster( 'subscribers' )->add( $subscriber, $overwrite );

								// if result isn't a WP_error assign the lists
								if ( ! is_wp_error( $subscriber_id ) ) {
									mailster( 'subscribers' )->assign_lists( $subscriber_id, $mailster_list_id );
									$response = array( 'status' => '1', 'message' => $response_ok);

								} else {
									$response = array( 'status' => '0', 'message' => $subscriber_id->get_error_message() );
								}
							}

						break;

	        			default:
	        				break;
	        		}

				// if not email, set response invalid
				} else {
					$response = array( 'status' => '0', 'message' => $response_invalid);
				}
			endif;

			if ( $ajax === TRUE ) {
				echo json_encode( $response );
				wp_die();

			} else {
				return ( $response == '' ) ? $response : json_encode( $response );
			}
			
		}

		public function niteo_export_csv() {

			if ( !current_user_can('publish_pages') ) {
				die('Sorry, but this request is invalid');
			}

			check_ajax_referer( 'cmp-coming-soon-ajax-secret', 'security' );

			// load subscribers array
			$subscribers = get_option('niteoCS_subscribers_list');

			if( !empty($subscribers) ) {
				$filename = 'cmp-subscribers-export-' . date('Y-m-d') . '.csv';

				header('Content-Type: text/csv');
				header('Content-Disposition: attachment;filename='.$filename);

				$fp = fopen('php://output', 'w');

				fputcsv($fp, array(
					__('ID','cmp-coming-soon-maintenance'),
					__('Date','cmp-coming-soon-maintenance'),
					__('Email','cmp-coming-soon-maintenance'),
					__('Firstname','cmp-coming-soon-maintenance'),
					__('Lastname','cmp-coming-soon-maintenance'),
					__('Fullname', 'cmp-coming-soon-maintenance')
					)
				);
				foreach ( $subscribers as $key => $value ) {

					if ( isset( $value['ip_address'] ) ) {
						unset($subscribers[$key]['ip_address']);
					}

					if ( isset( $value['timestamp'] ) )	{	
						$format="Y-m-d H:i:s";
						$subscribers[$key]['timestamp'] = date_i18n($format, $subscribers[$key]['timestamp']);
					}

					$subscribers[$key]['Name'] = '';

					if ( $value['firstname'] !== '' || $value['lastname'] !== '' ) {
						$subscribers[$key]['Name'] = $value['firstname'] . ' ' . $value['lastname'];
					}

					$subscribers[$key]['Name'] = trim($subscribers[$key]['Name']);

				}

				foreach ( $subscribers as $key => $value ) {
					fputcsv($fp, $value, $delimiter = ',', $enclosure = '"' );
				}

				fclose($fp);
			}
			die();
		}

		public function cmp_allow_mimes( $mimes = array() ) {
			// add your own extension here - as many as you like
			$mimes['zip'] = 'application/zip'; 
			 
			return $mimes;
		}

		public function cmp_allow_font_mimes( $mimes = array() ) {
			// add your own extension here - as many as you like
			$mimes['woff']  = 'application/x-font-woff';
			$mimes['woff2'] = 'application/x-font-woff2';
			$mimes['ttf']   = 'application/x-font-ttf';
			$mimes['eot']   = 'application/vnd.ms-fontobject';
			$mimes['otf']   = 'font/otf';
			if ( current_user_can('administrator') ) {
				$mimes['svg']   = 'image/svg+xml';
			}
			return $mimes;
		}

	    public function cmp_admin_notice() {
			global $pagenow;

			// display save messages
			if ( isset($_GET['page']) && ($_GET['page'] == 'cmp-settings' || $_GET['page'] == 'cmp-translate' || $_GET['page'] == 'cmp-advanced') ) {
				if (isset($_GET['status']) && $_GET['status'] == 'settings-saved') {
					$status 	= 'success';
					$message 	= __('CMP Settings Saved', 'cmp-coming-soon-maintenance');

					echo '<div class="notice notice-'.$status.' is-dismissible"><p>'.$message.'.</p></div>';
				}
			}

			// display activation notice
			if ( !get_option( 'niteoCS_activation_notice' ) ) {
				if ( ( $pagenow == 'plugins.php') ) {
					// load the notices view
					require_once( dirname( __FILE__) . '/inc/cmp-activation-notice.php' );
				}
			}
			return;
	    }

		// convert hex to rgba
		public function hex2rgba ( $hex, $opacity = null ) {
			list( $red, $green, $blue ) = sscanf( $hex, '#%02x%02x%02x' );

			if ( $opacity === null ) {
				return $red . ',' . $green . ',' . $blue;
			}

			$rgba = 'rgba(' . $red . ',' . $green . ',' . $blue . ',' . $opacity.')'; 

			return $rgba;
		}

		// convert hex to hsl css
		public function hex2hsl( $hex, $opacity ) {

			if ( $hex[0] != '#' ) {
				$rgba = explode( ',', $hex);
				$rgba[3] = str_replace(')', '', $rgba[3]);
				$rgba[3] = $rgba[3] - ( $opacity / 100 );
				$rgba = $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . $rgba[3] . ')';
				return $rgba;
			}

			list( $red, $green, $blue ) = sscanf( $hex, '#%02x%02x%02x' );

			$r = $red / 255.0;
			$g = $green / 255.0;
			$b = $blue / 255.0;
			$H = 0;
			$S = 0;
			$V = 0;

			$min = min( $r, $g, $b );
			$max = max( $r, $g, $b );
			$delta = ( $max - $min );

			$L = ( $max + $min ) / 2.0;

			if( $delta == 0 ) {
				$H = 0;
				$S = 0;
			} else {
				$S = $L > 0.5 ? $delta / ( 2 - $max - $min ) : $delta / ( $max + $min );

				$dR = ( ( ( $max - $r ) / 6) + ( $delta / 2  ) ) / $delta;
				$dG = ( ( ( $max - $g ) / 6) + ( $delta / 2  ) ) / $delta;
				$dB = ( ( ( $max - $b ) / 6) + ( $delta / 2  ) ) / $delta;

				if ( $r == $max )
					$H = $dB - $dG;
				else if( $g == $max )
					$H = ( 1/3 ) + $dR - $dB;
				else
					$H = ( 2/3 ) + $dG - $dR;

				if ( $H < 0 )
					$H += 1;
				if ( $H > 1 )
					$H -= 1;
			}

			$HSL = array( 'hue' => round( ($H*360), 0 ), 'saturation'=> round( ($S*100), 0 ), 'luminosity' => round( ( $L*100 ), 0) );

			// if color is white {
			if ( $HSL['hue'] == 0 && $HSL['saturation'] == 0) {
				$requested_lumi = $HSL['luminosity'] + $opacity;
			} else {
				$requested_lumi = $HSL['luminosity'] - $opacity;
			}
		
			$requested_lumi = (int)round($requested_lumi);

			if ( $requested_lumi > 90 ) {
				
				$requested_lumi = 90;
			}

			$HSL = 'hsl( '. $HSL['hue'] .', '.( $HSL['saturation']) .'%, '. $requested_lumi . '%)';
			return $HSL;
		}

		// check if mobile 
		public function isMobile() {
			if ( isset($_SERVER["HTTP_USER_AGENT"]) ) {
				return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);

			} else {
				return false;
			}
		}

		// sanitize function
		public function sanitize_checkbox( $input ) {
			return ( ( isset( $input ) && true == $input ) ? '1' : '0' );
		}

		// sanitize function
		public function niteo_sanitize_html( $html ) {

			if ( !current_user_can( 'unfiltered_html' ) ) {
	           	$allowed = wp_kses_allowed_html( 'post' );
	           	$html = wp_kses( $html, $allowed );
	        }

		    return $html;
		}

		// public function to sort social icons 
		public function sort_social($a, $b){
		    if ( $a['hidden'] == $b['hidden'] ) {
		        if( $a['order'] == $b['order'] ) {
		            return 0;
		        }
		        return $a['order'] < $b['order'] ? -1 : 1;
		    } else {
		         return $a['hidden'] > $b['hidden'] ? 1 : -1;
		    }
		 }

		 // public function to shift multidimensional array 
		public function customShift($array, $name){

		    foreach($array as $key => $val){     // loop all elements
		        if($val['name'] == $name){             // check for id $id
		            unset($array[$key]);         // unset the $array with id $id
		            array_unshift($array, $val); // unshift the array with $val to push in the beginning of array
		            return $array;               // return new $array
		        }
		    }
		}

		public function get_youtube_img( $youtube_url ) {
			$yt_id = $this->get_youtube_id( $youtube_url );

			$youtube_image = 'http://img.youtube.com/vi/' . $yt_id . '/hqdefault.jpg';

			return $youtube_image;
		}


		public function get_youtube_id( $youtube_url ) {
			$youtube = preg_match('/.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/', $youtube_url, $url);

			return $url[7];
		}

		public function cmp_get_pages( $post_status = 'publish,draft', $builder = false) {
			$meta_query = array();
			$pages = array();

			// meta_query for pages built by elementor
			if ( $builder === 'elementor' ) {
				$meta_query = array(
					'key' => '_elementor_edit_mode',
					'compare' => 'EXISTS'
				);
			}
			// meta_query for pages built by Divi
			if ( $builder === 'divi' ) {
				$meta_query = array(
					'key' => '_et_pb_use_builder',
					'compare' => 'EXISTS'
				);
			}

			$args = array(
				'post_type' => 'page',
				'post_status' => $post_status,
				'posts_per_page' => -1,
				'meta_query' => array($meta_query)
			);

			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {
			 
				foreach( $the_query->posts as $post ) {
					// check for gutenberg pages
					if ( $builder === 'gutenberg' && !has_blocks($post->ID) ) {
						continue;
					}

					$post_info = array('id' => $post->ID, 'name' => $post->post_title);
					array_push($pages, $post_info);
				}
				wp_reset_postdata();
			}

			return $pages;
		}

		// send json data for theme info overlay AJAX request
		public function niteo_themeinfo( ) {

			// check for ajax 
			if ( isset( $_POST['theme_slug'] ) ) {
				// verify nonce
				check_ajax_referer( 'cmp-coming-soon-ajax-secret', 'security' );
				// verify user rights
				if( !current_user_can('publish_pages') ) {
					die('Sorry, but this request is invalid');
				}


				// sanitize  $post
				$theme_slug = sanitize_text_field( $_POST['theme_slug'] );
				$data = array( 'result' => 'true', 'author_homepage' => CMP_AUTHOR_HOMEPAGE, 'author' => CMP_AUTHOR );
				
				if ( !empty( $theme_slug ) ) {
					$headers  = array('Theme Name', 'Description');
					$theme_info = get_file_data(plugin_dir_path( __FILE__ ).'/themes/'. $theme_slug. '.txt', $headers, '');

					$screenshots = array_map( 'basename', glob( plugin_dir_path( __FILE__ ) . 'img/thumbnails/'.$theme_slug.'/*' ) );
					
					foreach ( $screenshots as $key => $screenshot ) {
						$screenshots[$key] = plugins_url('img/thumbnails/'.$theme_slug.'/'.$screenshot, __FILE__ );
					}

				 	$data['name'] = $theme_info[0];
				 	$data['description'] = $theme_info[1];
					$data['screenshots'] = $screenshots;		
				}

				echo json_encode ($data);
				wp_die();
		    }
		}

	    // legacy function for premium themes redirect
	    public function niteo_redirect() {
	        return;
	    }

	    // returns array of google fonts from /inc/webfonts.php 
	    public function cmp_get_google_fonts() {
	    	$fonts = include_once wp_normalize_path( dirname( __FILE__ ) . '/inc/webfonts.php' );
			$google_fonts = json_decode( $fonts, true);

	    	return $google_fonts;
	    }

	    public function cmp_google_variant_title( $variant ) {

			switch( $variant ) {
			    case '100':
			        return 'Thin 100';
			        break;
			    case '100italic':
			        return 'Thin 100 Italic';
			        break;
			    case '200':
			        return 'Extra-light 200';
			        break;
			    case '200italic':
			        return 'Extra-light 200 Italic';
			        break;
			    case '300':
			        return 'Light 300';
			        break;
			    case '300italic':
			        return 'Light 300 Italic';
			        break;
				case '400':
				case 'regular':
			        return 'Regular 400';
			        break;
				case '400italic':
				case 'italic':
			        return 'Regular 400 Italic';
			        break;
			    case '500':
			        return 'Medium 500';
			        break;
			    case '500italic':
			        return 'Meidum 500 Italic';
			        break;
			    case '600':
			        return 'Semi-Bold 600';
			        break;
			    case '600italic':
			        return 'Semi-Bold 600 Italic';
			        break;
			    case '700':
			        return 'Bold 700';
			        break;
			    case '700italic':
			        return 'Bold 700 Italic';
			        break;
			    case '800':
			        return 'Extra-Bold 800';
			        break;
			    case '800italic':
			        return 'Extra-Bold Italic';
			        break;
			    case '900':
			        return 'Black 900';
			        break;
			    case '900italic':
			        return 'Black 900 Italic';
			        break;
				case 'Not Applicable':
					return 'Not Applicable';
					break;
			    default:
			        break;
			}
		}

		// returns true if current page should display CMP page
		// since 2.2
		public function cmp_page_filter() {
			global $wp;

			$uri = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : '';
			
			$current_url = trailingslashit( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://"  . $uri );

			$custom_login_url = get_option('niteoCS_custom_login_url', '');

			// return early if login page or ajax call
			if ( fnmatch( '*wp-login.php*', $current_url ) || wp_doing_ajax() ) {
				return false;
			}

			// return early if custom login page
			if ( $custom_login_url !== '' && fnmatch('*'.$custom_login_url.'*', $current_url) ) {
				return false;
			}

			// WPS HIDE login integration
			if ( defined('WPS_HIDE_LOGIN_BASENAME') ) {
				if ( fnmatch('*'.get_option( 'whl_page' ), $current_url) ) {
					return false;
				}
			}

			// allow / block RSS
			if ( get_option('niteoCS_rss_status', '1') && is_feed() ) {
				return false;
			}

			// get current page IDs
			if ( is_front_page() && is_home() ) {
				// default homepage
			  	$page_id = '-1';
			} elseif ( is_front_page() ) {
			  	// static homepage
				$page_id = '-1';
			} elseif ( is_home() ) {
				// posts page
			  	$page_id = get_option( 'page_for_posts' );
			} else {
			  	//everyting else
			  	$page_id = get_the_ID();
			}

			// check whitelist or blacklist and return true/false
			switch ( get_option('niteoCS_page_filter', '0') ) {
				// disabled return true
				case '0':
					return true;
					break;

				// whitelist
				case '1':
					$whitelist = json_decode( get_option('niteoCS_page_whitelist', '[]'), true );
					if ( !empty( $whitelist ) && in_array( $page_id, $whitelist ) ) {
						return true;
					}

					$page_wl_custom	= json_decode(get_option('niteoCS_page_whitelist_custom', '[]'), true);
					if ( !empty( $page_wl_custom ) ) {
						foreach ( $page_wl_custom as $url ) {
							if ( fnmatch( $url, $current_url ) ) {
								return true;
							}
						}
					}

					return false;
					break;

				// blacklist
				case '2':
					$blacklist = json_decode( get_option('niteoCS_page_blacklist', '[]'), true );

					if ( !empty( $blacklist ) && in_array( $page_id, $blacklist ) ) {
						return false;
					}

					$page_bl_custom	= json_decode(get_option('niteoCS_page_blacklist_custom', '[]'), true);

					if ( !empty( $page_bl_custom ) ) {
						foreach ($page_bl_custom as $url ) {
							if ( fnmatch( $url, $current_url ) ) {
								return false;
							}
						}
					}

					return true;
					break;

				default:
					return true;
					break;
			}

			return true;
		}

		// returns true if logged in user meet CMP roles filter
		// since 2.2
		public function cmp_roles_filter() {
			$roles = json_decode( get_option('niteoCS_roles', '[]'), true );
			// push WP administrator to roles array, since it is default
			array_push( $roles, 'administrator' );

			$current_user = wp_get_current_user();

			foreach ( $current_user->roles as $role ) {
				if ( in_array( $role, $roles ) ) {
					return true;
				}
			};

			return false;
		}


		public function add_action_links( $links ) {
			 $settings = array(
			 	'<a href="' . admin_url( 'admin.php?page=cmp-settings' ) . '">CMP Settings</a>',
			 );
			return array_merge( $settings, $links );
		}


		// returns version of selected CMP theme
		public function cmp_theme_version( $theme_slug ) {

			$version = CMP_VERSION;

			// if premium theme style.css exists get its version
			if ( in_array( $theme_slug, $this->cmp_premium_themes_installed() ) ) {
				if ( file_exists( CMP_PREMIUM_THEMES_DIR . $theme_slug . '/style.css' ) ) {
					$version = get_file_data( CMP_PREMIUM_THEMES_DIR . $theme_slug . '/style.css', array('Version'), '' );
				}

			} 

			// if we have local version of theme and not in updating theme
			if ( is_array( $version ) ) {
				$version =  $version[0];
			}

			return $version;
		}

		/**
		 * Connect to Mailchimp via API and retrieve Mailchimp lists
		 *
		 * @since 2.6
		 * @access public
		 * @return Object
		 */
		public function cmp_mailchimp_list_ajax( $apikey ) {

			// check for ajax 
			if ( isset( $_POST['params'] ) ) {
				// verify nonce
				check_ajax_referer( 'cmp-coming-soon-ajax-secret', 'security' );
				// verify user rights
				if( !current_user_can('publish_pages') ) {
					die('Sorry, but this request is invalid');
				}

				// sanitize array
				$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

				// check params
				if ( !empty( $_POST['params'] ) ) {
					$params = $_POST['params'];
				}

				$api_key = $params['apikey'];

				$dc = substr( $api_key,strpos($api_key,'-') + 1 ); // datacenter, it is the part of your api key - us5, us8 etc

				$args = array(
				 	'headers' => array(
						'Authorization' => 'Basic ' . base64_encode( 'user:'. $api_key )
					)
				);


				// retrieve response from mailchimp
				$response = wp_remote_get( 'https://'.$dc.'.api.mailchimp.com/3.0/lists/', $args );
				
				// if we have it, create new array with lists id and name, else push error messages into array
				if ( !is_wp_error( $response ) ) {
					$lists_array = array();

					$body = json_decode( $response['body'], true);

					if ( $response['response']['code'] == 200 ) { 
						$lists_array['response'] = 200;
						$i = 0;
						foreach ( $body['lists'] as $list ) {
							$lists_array['lists'][$i]['id'] = $list['id'];
							$lists_array['lists'][$i]['name'] = $list['name'];
							$i++;
						}

					} else {
						$lists_array['response'] = $response['response']['code'];
						$lists_array['message'] = $body['title'] . ': ' . $body['detail'];
					}

				} else {
					$lists_array['response'] = '500';
					$lists_array['message'] = $response->get_error_message();
				}

				// json encode response
				$lists_json = json_encode( $lists_array );

				// save it
				update_option('niteoCS_mailchimp_lists', $lists_json);

				// delete selected old mailchimp list because we do not want it
				delete_option('niteoCS_mailchimp_list_selected');

				// echo ajax result
				echo $lists_json;
				wp_die();

		    }

		}

		/**
		 * Returns Background image URL
		 *
		 * @since 2.8.4
		 * @access public
		 * @return string
		 */
		public function cmp_get_background_img_for_seo() {
			
			$themeslug = $this->cmp_selectedTheme();
			$default_img = '';

			$background_type = get_option('niteoCS_banner', '2');

			if ( file_exists( $this->cmp_theme_dir( $this->cmp_selectedTheme() ).$this->cmp_selectedTheme().'/img/'.$this->cmp_selectedTheme().'_banner_large.jpg' ) ) {
				$default_img = $this->cmp_themeURL( $this->cmp_selectedTheme() ).$this->cmp_selectedTheme().'/img/'.$this->cmp_selectedTheme().'_banner_large.jpg';
			} elseif ( file_exists( $this->cmp_theme_dir( $this->cmp_selectedTheme() ).$this->cmp_selectedTheme().'/img/'.$this->cmp_selectedTheme().'_banner_large.png' ) ) {
				$default_img = $this->cmp_themeURL( $this->cmp_selectedTheme() ).$this->cmp_selectedTheme().'/img/'.$this->cmp_selectedTheme().'_banner_large.png';
			}

			switch ( $background_type ) {
				// custom img
				case '0':
	                $banner_id = get_option('niteoCS_banner_id');
	                
	                if ( $banner_id != '' ) {
	                    $banner_ids = explode(',', $banner_id);
	                    $image_url = wp_get_attachment_image_src( $banner_ids[0], 'large');

	                    if ( isset( $image_url[0] ) ) {
	                        $image_url = $image_url[0];
	                    }

	                } else {
	                    // send default image
	                    $image_url = $default_img;
	                }

	                break;
	            // unsplash
	            case '1':
	                $unplash_feed   = get_option('niteoCS_unsplash_feed', '3');

	                switch ( $unplash_feed ) {
	                    // specific photo from id
	                    case '0':
	                        $params = array('feed' => '0', 'url' => get_option('niteoCS_unsplash_0', '') );
	                        $unsplash = $this->niteo_unsplash(  $params );
	                        break;

	                    // random from user
	                    case '1':
	                        $params = array('feed' => '1', 'custom_str' => get_option('niteoCS_unsplash_1', '') );
	                        $unsplash = $this->niteo_unsplash(  $params );
	                        break;

	                    // random from collection
	                    case '2':
	                        $params = array('feed' => '2', 'url' => get_option('niteoCS_unsplash_2', '') );
	                        $unsplash = $this->niteo_unsplash(  $params );
	                        break;

	                    // random photo
	                    case '3':
	                        $params = array('feed' => '3', 'url' => get_option('niteoCS_unsplash_3', ''), 'feat' => get_option('niteoCS_unsplash_feat', '0') );
	                        $unsplash = $this->niteo_unsplash(  $params );
	                        break;
	                    default:
	                        break;
	                }

	                // get raw url from response
	                if ( isset( $unsplash['response'] ) && $unsplash['response'] == '200' ) {
	                    $body = json_decode ($unsplash['body'], true );

	                    if ( isset( $body[0] ) ) {
	                        foreach ( $body as $item ) {
	                            $unsplash_url = $item['urls']['raw'];
	                        }
	                    } else {
	                        $unsplash_url = $body['urls']['raw'];
	                    } 

	                    $image_url = $unsplash_url . '?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&fit=crop&w=1200&h=630';
	                } 

	                break;
	            // default image
	            case '2':
	                $image_url = $default_img;
	                break;

	            case '3':
	                // Pattern
	                $niteoCS_banner_pattern = get_option('niteoCS_banner_pattern', 'sakura');

	                if ( $niteoCS_banner_pattern != 'custom' ) {
	                    $image_url =  CMP_PLUGIN_URL.'img/patterns/'.esc_attr($niteoCS_banner_pattern).'.png';   

	                } else {
	                    $image_url = get_option('niteoCS_banner_pattern_custom');
	                    $image_url = wp_get_attachment_image_src( $image_url, 'large' );
	                    if ( isset($image_url[0]) ){
	                        $image_url = $image_url[0];
	                    }
	                }

	                break;

	            case '5':
                    $image_url   = wp_get_attachment_image_src( get_option('niteoCS_video_thumb'), 'large' );

                    if ( !empty( $image_url ) ) {
                        $image_url = $image_url[0];       
                    }

	                break;

	            case '6':
	            case '4':
	            default:
	            	$image_url = '';
	                break;
	        }

            return $image_url;
		}


		/**
		 * Purge cache for popular caching plugins
		 *
		 * @since 2.8.5
		 * @access public
		 * @return string
		 */
		public function cmp_purge_cache() {
			// W3 Total Cache
			if ( function_exists('w3tc_flush_all') )  {
				w3tc_flush_all();
			}

	        // wp super cache
	        if ( function_exists( 'wp_cache_clear_cache' ) ) {
				wp_cache_clear_cache();
	        }

			// Clear Cachify Cache
			if ( has_action('cachify_flush_cache') ) {
				do_action('cachify_flush_cache');
			}
	
	        // endurance cache
	        if ( class_exists( 'Endurance_Page_Cache' ) && method_exists('Endurance_Page_Cache','purge_all'))  {
				$epc = new Endurance_Page_Cache;
				$epc->purge_all();
	        }

	        // SG Optimizer 
	        if ( class_exists( 'SG_CachePress_Supercacher' ) && method_exists( 'SG_CachePress_Supercacher', 'purge_cache' ) ) {
				SG_CachePress_Supercacher::purge_cache( true );
	        }

	        // WP Fastest Cache
	        if ( isset( $GLOBALS['wp_fastest_cache'] ) && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache' ) ) {
				$GLOBALS['wp_fastest_cache']->deleteCache( true );
	        }

	        // Swift Performance
	        if ( is_callable( array( 'Swift_Performance_Cache', 'clear_all_cache' ) ) ) {
				Swift_Performance_Cache::clear_all_cache();
	        }

	        // WP Rocket 
	        if ( function_exists( 'rocket_clean_domain' ) ) {
	        	rocket_clean_domain();
			}

			// wp-optimize
			if ( class_exists('WP_Optimize') ) {
				if (!class_exists('WP_Optimize_Cache_Commands')) include_once(WPO_PLUGIN_MAIN_PATH . 'cache/class-cache-commands.php');
				$cache_commands = new WP_Optimize_Cache_Commands();
				$cache_commands->purge_page_cache();
			}

			// Clear Litespeed cache
			if ( class_exists('LiteSpeed_Cache_API') && method_exists( 'LiteSpeed_Cache_API', 'purge_all' ) ) {
				LiteSpeed_Cache_API::purge_all();
			}
		}

		/**
		 * Checks if user defined role has permission to access and toggle CMP from Admin Bar Icon
		 *
		 * @since 2.8.5
		 * @access public
		 * @return boolean
		 */
		public function cmp_user_can_admin_bar_activation() {

			// return if Top Bar Icon is disabled
			if ( get_option('niteoCS_topbar_icon', '1') == '0' ) {
				return false;
			}
	    	
	    	require_once(ABSPATH . 'wp-admin/includes/screen.php');

			// get defined roles for admin topbar access
			$roles_topbar = json_decode( get_option('niteoCS_roles_topbar', '[]'), true );

			// push WP administrator to roles array, since it is default
			array_push( $roles_topbar, 'administrator' );

			// get current user
			$current_user = wp_get_current_user();

			// if current user can access topbar, return true
			foreach  ( $current_user->roles as $role ) {
				if ( in_array( $role, $roles_topbar ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Returns full post content via AJAX
		 *
		 * @since 3.0
		 * @access public
		 * @return array
		 */
		public function cmp_get_post_detail() {
			$id = isset($_POST['id']) ? esc_attr($_POST['id']) : '';
			$size = $this->isMobile ? 'large' : 'large';

			$post = array(
				'img' 	=> '',
				'date' 	=> '',
				'title' => 'Post is not published or is Password protected',
				'body' 	=> '',
				'url' 	=> '',
			);

			if ( get_post_status( $id ) === 'publish' && !post_password_required( $id ) ) {

				$post =  array(
					'img' 	=> get_the_post_thumbnail( $id, $size ),
					'date' 	=> get_the_date( 'F j, Y', $id ),
					'title' => get_the_title( $id ),
					'body' 	=> apply_filters( 'the_content', get_post_field('post_content', $id) ),
					'url' 	=> get_the_permalink( $id ),
				);
			}

			wp_send_json( $post );

		}

		/**
		 * Dismiss activation notice
		 *
		 * @since 3.4.3
		 * @access public
		 * @return void
		 */
		function cmp_ajax_dismiss_activation_notice() {
			check_ajax_referer( 'cmp-coming-soon-maintenance-nonce', 'nonce' );
			// user has dismissed the welcome notice
			update_option( 'niteoCS_activation_notice', true );
			wp_die();
			exit;
		}

		/**
		 * Upload custom Fonts to CMP
		 *
		 * @since 3.5
		 * @access public
		 * @return string
		 */
		public function cmp_ajax_upload_font() {
			// verify nonce
			check_ajax_referer( 'cmp-coming-soon-ajax-secret', 'security' );

			// verify user rights
			if( !current_user_can('publish_pages') ) {
				die('Sorry, but this request is invalid');
			}
	

			if ( isset($_POST['payload']) ) {

				$payload = json_decode( stripslashes($_POST['payload']), true );			
				$action = $payload['action'];

				if ( $action === 'upload_font' ) {
					
					$new_fonts = $payload['files'];

					// delete_option('niteoCS_custom_fonts');

					if ( get_option('niteoCS_custom_fonts') ) {

						$old_fonts = json_decode( get_option('niteoCS_custom_fonts'), true );

						$i = 0;

						foreach ( $old_fonts as $old_font ) {

							foreach ( $new_fonts as $new_font ) {
								if ( $old_font['id'] === $new_font['id'] ) {

									$old_fonts[$i]['urls'] = ( is_array($old_font['urls']) ) ? array_unique(array_merge( $old_font['urls'], $new_font['urls'] )) : $new_font['urls'];
									$old_fonts[$i]['ids'] = ( is_array($old_font['ids']) ) ? array_unique(array_merge( $old_font['ids'], $new_font['ids'] )) : $new_font['ids'];

								} else if ( !$this->niteo_in_array_r($new_font['id'], $old_fonts) ) {
									array_push($old_fonts, $new_font);
								}
							}

							$i++;	
						}

						$new_fonts = $old_fonts;
					} 

					update_option( 'niteoCS_custom_fonts', json_encode( $new_fonts ) );

				}

			}

			// echo confirmation
			echo 'success';
			wp_die();
		}

		/**
		 * Export function of CMP Settings to JSON
		 *
		 * @since 3.5.3
		 * @access public
		 * @return string
		 */
		public function cmp_export_settings() {
			
			$options = array();
			// add default cmp identifier
			$options[0] = 'CMP_EXPORT';

			global $wpdb;
			$saved_options = $wpdb->get_results( "SELECT * FROM $wpdb->options WHERE option_name LIKE 'niteoCS_%'", OBJECT );
			$i = 1;

			foreach ($saved_options as $option) {

				$option_name = $option->option_name;
				$option_value = get_option( $option_name );

				$img_settings = array( 'niteoCS_banner_id', 'niteoCS_logo_id', 'niteoCS_seo_img_id', 'niteoCS_favicon_id', 'niteoCS_subs_img_id', 'niteoCS_subs_img_popup_id' );

				if ( in_array($option_name, $img_settings) && $option_value && $option_value != '' ) {
					$option_value = $this->cmp_get_img_urls($option_value);
				}

				if ( $option_name === 'niteoCS_posts' ) {
					$option_value = '[]';
				}


				$options[$i] = array( $option_name => $option_value );
				$i++;
			}

			return json_encode($options);
		}

		/**
		 * Helper function to retrieve full img URL, comma separated, by ID
		 *
		 * @since 3.5.3
		 * @access public
		 * @return string
		 */

		 public function cmp_get_img_urls( $ids ) {

			$id_array = explode(',', $ids);
			$i = 1;
			$urls = '';

			foreach ($id_array as $id) {
				$sep = ($i < count($id_array)) ? ',' : '';
				$image_url = wp_get_attachment_image_src( $id, 'full');

				if ( isset( $image_url[0] ) ) {
					$image_url = $image_url[0];
				}

				$urls .= $image_url . $sep;
				
				$i++;
			} 
			
			return $urls;
		 }

		/**
		 * export settings to JSON via AJAX 
		 *
		 * @since 3.5.3
		 * @access public
		 * @return string
		 */
		 public function cmp_ajax_export_settings() {
			// verify nonce
			check_ajax_referer( 'cmp-coming-soon-ajax-secret', 'security' );

			// verify user rights
			if( !current_user_can('publish_pages') ) {
				die('Sorry, but this request is invalid');
			}

			$settings = $this->cmp_export_settings();

			$replace = array('https://', 'http://');
			$home_url = str_replace($replace, '', get_home_url());

			if ( !empty($settings) ) {
				$filename =  $home_url. '-cmp-settings-' . date('Y-m-d') . '.json';

				header('Content-Type: application/json');
				header('Content-Disposition: attachment;filename=' . $filename);

				$fp = fopen('php://output', 'w');

				fwrite($fp , $settings);
				fclose($fp);
			}
			die();
		}

		/**
		 * import settings function
		 *
		 * @since 3.5.3
		 * @access public
		 * @return string
		 */
		public function cmp_ajax_import_settings( ) {

			check_ajax_referer( 'cmp-coming-soon-ajax-secret', 'security' );

			// verify user rights
			if( !current_user_can('publish_pages') ) {
				die('Sorry, but this request is invalid');
			}

			$settings = json_decode( stripslashes($_POST['json']), true );

			$result = array(
				'result' => 'success',
				'message' => __('All done!', 'cmp-coming-soon-maintenance')
			);

			if ( json_last_error() == JSON_ERROR_NONE ) {
				if ( $settings[0] === 'CMP_EXPORT' ) {
					// remove first value used for JSON CMP Settings check
					unset($settings[0]);

					// delete all current CMP Settings
					global $wpdb;
					$saved_options = $wpdb->get_results( "SELECT * FROM $wpdb->options WHERE option_name LIKE 'niteoCS_%'", OBJECT );
					foreach ($saved_options as $option) {
						delete_option($option->option_name);
					}
					
					// import cmp settings from JSON structure
					foreach ( $settings as $setting ) {
	
						$img_settings = array( 'niteoCS_banner_id', 'niteoCS_logo_id', 'niteoCS_seo_img_id', 'niteoCS_favicon_id', 'niteoCS_subs_img_id', 'niteoCS_subs_img_popup_id' );
	
						$name = key($setting);
						$value = $setting[$name];
	
						if ( in_array($name, $img_settings) ) {
	
							$urls = explode(',', $value);
	
							if (is_array($urls)) {
								foreach ( $urls as $url ) {
									$value = $this->cmp_insert_attachment_from_url($url);
									$value .= ',' . $value;
								}
							}
						}
		
						update_option( $name, $value );
					}
				} else {
					$result = array(
						'result' => 'error',
						'message' =>  __('JSON file is valid but it does not contain CMP Settings.', 'cmp-coming-soon-maintenance')
					);
				}
			} else {
				$result = array(
					'result' => 'error',
					'message' =>  __('Please insert valid JSON file and try again.', 'cmp-coming-soon-maintenance')
				);

			}

			echo json_encode($result);
			wp_die();

		}

		/**
		 * Insert an attachment from an URL address.
		 * by https://gist.github.com/m1r0/f22d5237ee93bcccb0d9
		 *
		 * @param  String $url
		 * @param  Int    $parent_post_id
		 * @return Int    Attachment ID
		 */
		private function cmp_insert_attachment_from_url($url, $parent_post_id = null) {

			if( !class_exists( 'WP_Http' ) ) {
				include_once( ABSPATH . WPINC . '/class-http.php' );
			}
				
			$http = new WP_Http();

			$response = $http->request( $url );

			if( is_wp_error( $response ) || $response['response']['code'] != 200 ) {
				return false;
			}

			$upload = wp_upload_bits( basename($url), null, $response['body'] );

			if( !empty( $upload['error'] ) ) {
				return false;
			}

			$file_path = $upload['file'];
			$file_name = basename( $file_path );
			$file_type = wp_check_filetype( $file_name, null );
			$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
			$wp_upload_dir = wp_upload_dir();
			$post_info = array(
				'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
				'post_mime_type' => $file_type['type'],
				'post_title'     => $attachment_title,
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			// Create the attachment
			$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );
			// Include image.php
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			// Define attachment metadata
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
			// Assign metadata to attachment
			wp_update_attachment_metadata( $attach_id,  $attach_data );

			return $attach_id;
		}


		/**
		 * Send email notification to specify user
		 *
		 * @param  String $message
		 */
		private function cmp_send_notification( $status ) {
			// send notification email to admin, if enabled
			if ( get_option('niteoCS_mode_change_notification', '0') == '1' ) {
				switch ( $this->cmp_mode() ) {
					case '1':
						$mode = __('Maintenance','cmp-coming-soon-maintenance');
						break;
					case '2':
						$mode = __('Coming Soon','cmp-coming-soon-maintenance');
						break;
					case '3':
						$mode = __('Redirect','cmp-coming-soon-maintenance');
						break;
					default:
						break;
				}
				$current_user = wp_get_current_user();
				$email = get_option('niteoCS_mode_change_email_address', get_option( 'admin_email' ));
				$subject = $mode . ' mode on your website - ' . get_site_url() . ' - was just turned ' . $status . ' by ' . $current_user->user_login;
				$body = 'This is auto generated message from CMP - Coming Soon & Maintenance WordPress Plugin. You can disable these messages under CMP Advanced Settings > Email Notifications.';
				$headers = array('Content-Type: text/plain; charset=UTF-8');
				// send email
				wp_mail( $email, $subject, $body, $headers );
			}

			return;
		}

		/**
		 * Display Admin notices
		 *
		 * @since 3.7.3
		 */
		public function cmp_display_admin_notice( $type, $dismisable, $message ) {

			echo '<div class="notice notice-'.$type.' '.$dismisable.'"><p class="message">'.$message.'</p></div>';
		}

		/**
		 * Disable REST API if required
		 */
		function restrict_rest_api( $result ) {
			if ( true === $result || is_wp_error( $result ) ) {
				return $result;
			}
		
			// No authentication has been performed yet.
			// Return an error if user is not logged in.
			if ( ! is_user_logged_in() ) {
				return new WP_Error(
					'rest_disabled',
					__( 'JSON API is disabled by CMP – Coming Soon & Maintenance Plugin.' ),
					array( 'status' => 401 )
				);
			}
	
		}

		function jetpack_stats_compatibility() {
			if ( function_exists( 'stats_footer' ) ) {
				add_action( 'cmp_footer', 'stats_footer', 101 );
			}
		}

	}


endif;

/*
 * @since 2.8.1
 * @return object|CMP_Coming_Soon_and_Maintenance instance.
 */
function cmp_coming_soon_and_maintenance() {
	return CMP_Coming_Soon_and_Maintenance::instance();
}

// Get the things running
cmp_coming_soon_and_maintenance();

register_uninstall_hook( __FILE__, 'cmp_plugin_delete' );

// And here goes the uninstallation function:
function cmp_plugin_delete() {
    delete_option('niteoCS_archive');
}



