<?php
/**
 * Analytics Class.
 *
 * @package     Analytics
 * @copyright   Copyright (c) 2019, CyberChimps, Inc.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License Version 3
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Analytics.
 */
class Analytics {

	/**
	 * Analytics Instance
	 *
	 * @var Analytics[]
	 */
	private static $_instances = array();

	/**
	 * Statics Loaded.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private static $_statics_loaded = false;

	/**
	 * Module Type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $_module_type;

	/**
	 * Module Id.
	 *
	 * @since 1.0.0
	 *
	 * @var number
	 */
	private $_module_id;

	/**
	 * Plugin BaseName.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $_plugin_basename;

	/**
	 * Plugin URL.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $_plugin_url;

	/**
	 * Current page.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @var string
	 */
	private static $_pagenow;

	/**
	 * Product Version.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $_version = false;

	private $_tracking_obj;

	/**
	 * Product Name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $_product_name;

	const REASON_OTHER                             = 8;
	const REASON_DONT_LIKE_TO_SHARE_MY_INFORMATION = 9;

	/**
	 * Main singleton instance.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @param number      $module_id Module Id.
	 * @param string|bool $slug Slug.
	 * @param string      $product_name Product Name.
	 * @param string      $version Product Version.
	 * @param string      $module_type Module Type.
	 */
	private function __construct( $module_id, $product_name, $version, $module_type, $slug = false, $plugin_basename = '', $plugin_url = '' ) {
		$this->_module_id    = $module_id;
		$this->_slug         = $slug;
		$this->_module_type  = $module_type;
		$this->_product_name = $product_name;
		$this->_version      = $version;
		$this->_tracking_obj = new Analytics_Tracking( $slug );
		if ( '' !== $plugin_basename ) {
			$this->_plugin_basename = $plugin_basename;
		}
		if ( '' !== $plugin_url ) {
			$this->_plugin_url = $plugin_url;
		}

		$this->_blog_id = is_multisite() ? get_current_blog_id() : null;

		$this->register_constructor_hooks();
	}

	/**
	 * Main singleton instance.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @param  number      $module_id Module Id.
	 * @param  string|bool $slug Slug.
	 * @param string      $product_name Product Name.
	 * @param string      $version Product Version.
	 * @param string      $module_type Module Type.
	 *
	 * @return Analytics|false
	 */
	static function instance( $module_id, $product_name, $version, $module_type, $slug = false, $plugin_basename = '', $plugin_url = '' ) {
		if ( empty( $module_id ) ) {
			return false;
		}

		$key = 'm_' . $slug;

		self::$_instances[ $key ] = new Analytics( $module_id, $product_name, $version, $module_type, $slug, $plugin_basename, $plugin_url );

		return self::$_instances[ $key ];
	}

	/**
	 * Load resources.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 */
	private static function _load_required_static() {
		if ( self::$_statics_loaded ) {
			return;
		}

		if ( 0 === did_action( 'plugins_loaded' ) ) {
			add_action( 'plugins_loaded', array( 'Analytics', '_load_textdomain' ), 1 );
		}

		self::$_statics_loaded = true;
	}


	/**
	 * Dynamic initiator
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @param array $plugin_info Plugin Info.
	 *
	 * @throws Analytics_Exception Analytics Exception.
	 */
	public function dynamic_init( array $plugin_info ) {

		if ( $this->should_stop_execution() ) {
			return;
		}
	}

	/**
	 * Register Constructor Hooks.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 */
	private function register_constructor_hooks() {

		if ( is_admin() ) {
			add_action( 'admin_init', array( &$this, '_hook_action_links_and_register_account_hooks' ) );
		}

		add_action( 'wp_ajax_ask-for-review-dismiss', array( &$this, '_ask_for_review_dismiss' ) );
		add_action( 'wp_ajax_ask-for-usage-dismiss', array( &$this, '_ask_for_usage_dismiss' ) );
		add_action( 'wp_ajax_ask-for-usage-optin', array( &$this, '_ask_for_usage_optin' ) );
	}

	/**
	 * Checks if the current user can activate plugins or switch themes.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	function is_user_admin() {

		return ( $this->is_plugin() && current_user_can( is_multisite() ? 'manage_options' : 'activate_plugins' ) )
			|| ( $this->is_theme() && current_user_can( 'switch_themes' ) );
	}

	/**
	 * Checks if the module type is "theme". The other type is "plugin".
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	public function is_theme() {
		return ( ! $this->is_plugin() );
	}

	/**
	 * Get Slug.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @return string Plugin slug.
	 */
	public function get_slug() {

		return $this->_slug;
	}

	/**
	 * Keep Watch.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	private function should_stop_execution() {

		if ( $this->is_activation_mode() ) {
			if ( ! is_admin() ) {
				/**
				 * If in activation mode, don't execute Analytics outside of the
				 * admin dashboard.
				 *
				 * @author CyberChimps
				 * @since  1.0.0
				 */
				return true;
			}

			if ( self::is_ajax()
			) {
				/**
				 * During activation, if running in AJAX mode, unless there's a sticky
				 * connectivity issue notice, don't run Analytics.
				 *
				 * @author CyberChimps
				 * @since  1.0.0
				 */
				return true;
			}
		}

		return false;
	}

	/**
	 * Is plugin in activation mode.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @param bool $and_on And ON.
	 *
	 * @return bool
	 */
	public function is_activation_mode( $and_on = true ) {
		return true;
	}

	/**
	 * Check if a real user is visiting the admin dashboard.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	public function is_user_in_admin() {
		return is_admin();
	}

	/**
	 * Get Option
	 *
	 * @param string[] $options Options.
	 * @param string   $key Key.
	 * @param mixed    $default Default.
	 *
	 * @return bool
	 */
	private function get_option( &$options, $key, $default = false ) {
		return ! empty( $options[ $key ] ) ? $options[ $key ] : $default;
	}

	/**
	 * Get Boolean Option.
	 *
	 * @param array  $options Options.
	 * @param string $key Key.
	 * @param bool   $default Default.
	 * @return bool|mixed
	 */
	private function get_bool_option( &$options, $key, $default = false ) {
		return isset( $options[ $key ] ) && is_bool( $options[ $key ] ) ? $options[ $key ] : $default;
	}
	/**
	 * Displays a confirmation and feedback dialog box when the user clicks on the "Deactivate" link on the plugins
	 * page.
	 *
	 * @author CyberChimps
	 *
	 * @since  1.0.0
	 */
	public function _add_deactivation_feedback_dialog_box() {

		$show_deactivation_feedback_form = true;

		$vars = array(
			'id'           => $this->_module_id,
			'slug'         => $this->_slug,
			'product_name' => $this->_product_name,
			'version'      => $this->_version,
			'module_type'  => $this->_module_type,
		);

		if ( $show_deactivation_feedback_form ) {
			$user_type = 'long-term';

			$uninstall_reasons = $this->_get_uninstall_reasons( $user_type );

			$vars['reasons'] = $uninstall_reasons;
		}
		$uninstall_confirmation_message = apply_filters( 'uninstall_confirmation_message', '' );

		$vars['show_deactivation_feedback_form'] = $show_deactivation_feedback_form;
		$vars['uninstall_confirmation_message']  = $uninstall_confirmation_message;

		/**
		 * Load the HTML template for the deactivation feedback dialog box.
		 */
		as_require_template( 'forms/deactivation/form.php', $vars );
	}

	/**
	 * Get Uninstall Reasons.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @param string $user_type User Type.
	 *
	 * @return array The uninstall reasons for the specified user type.
	 */
	public function _get_uninstall_reasons( $user_type = 'long-term' ) {

		$params                = array();
		$params['module_type'] = $this->_module_type;

		if ( false === get_transient( $this->_module_type . '_uninstall_reasons' ) ) {
			$request = array(
				'method'  => 'GET',
				'body'    => $params,
				'timeout' => 30,
			);

			$url      = esc_url( WP_STAT__ADDRESS . '/wp-json/uninstall/reason/theme/' );
			$response = wp_remote_get( $url, $request );

			if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
				$uninstall_reasons = $this->get_default_uninstall_reasons( $params );
			} else {
				$uninstall_reasons = json_decode( $response['body'], true );
			}

			set_transient( $this->_module_type . '_uninstall_reasons', $uninstall_reasons, 60 * 60 * 24 );
		} else {

			$uninstall_reasons = get_transient( $this->_module_type . '_uninstall_reasons' );
		}
		return $uninstall_reasons;
	}

	/**
	 * Get Default uninstall reasons
	 *
	 * @param array $params Parameters
	 * @return array
	 */
	public function get_default_uninstall_reasons( $params = array() ) {
		$module_type = $params['module_type'];

		$deactivation_reasons = array(
			array(
				'id'                => 1,
				'text'              => "I couldn't understand how to make it work",
				'input_type'        => '',
				'input_placeholder' => '',
			),
			array(
				'id'                => 2,
				'text'              => sprintf( "The %s is great, but I need specific feature that you don't support", $module_type ),
				'input_type'        => 'textarea',
				'input_placeholder' => 'What feature?',
			),
			array(
				'id'                => 3,
				'text'              => sprintf( 'The %s is not working', $module_type ),
				'input_type'        => 'textarea',
				'input_placeholder' => "Kindly share what didn't work so we can fix it for future users...",
			),
			array(
				'id'                => 4,
				'text'              => "It's not what I was looking for",
				'input_type'        => 'textarea',
				'input_placeholder' => "What you've been looking for?",
			),
			array(
				'id'                => 5,
				'text'              => sprintf( "The %s didn't work as expected", $module_type ),
				'input_type'        => 'textarea',
				'input_placeholder' => 'What did you expect?',
			),
			array(
				'id'                => 6,
				'text'              => sprintf( 'I found a better %s', $module_type ),
				'input_type'        => 'textfield',
				'input_placeholder' => sprintf( "What's the %s's name?", $module_type ),
			),
			array(
				'id'                => 7,
				'text'              => sprintf( "It's a temporary %s switch. I'm just debugging an issue.", $module_type ),
				'input_type'        => '',
				'input_placeholder' => '',
			),
			array(
				'id'                => 8,
				'text'              => 'Other',
				'input_type'        => 'textfield',
				'input_placeholder' => '',
			),
		);
		return $deactivation_reasons;
	}

	/**
	 * Checks if the plugin's type is "plugin". The other type is "theme".
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	public function is_plugin() {
		return ( WP_STAT__MODULE_TYPE_PLUGIN === $this->_module_type );
	}

	/**
	 * Hook Action links.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 */
	public function _hook_action_links_and_register_account_hooks() {
		if ( self::is_plugins_page() && $this->is_plugin() ) {
			$this->hook_plugin_action_links();
		}

		$this->_register_account_hooks();
	}

	/**
	 * Hook to plugin action links filter.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 */
	private function hook_plugin_action_links() {

		// Add action link to settings page.
		add_filter(
			'plugin_action_links_' . $this->_plugin_basename,
			array(
				&$this,
				'_modify_plugin_action_links_hook',
			),
			WP_CYB__DEFAULT_PRIORITY,
			2
		);
		add_filter(
			'network_admin_plugin_action_links_' . $this->_plugin_basename,
			array(
				&$this,
				'_modify_plugin_action_links_hook',
			),
			WP_CYB__DEFAULT_PRIORITY,
			2
		);
	}

	/**
	 * Modify plugin's deactivate action links collection.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @param array $links
	 * @param       $file
	 *
	 * @return array
	 */
	function _modify_plugin_action_links_hook( $links, $file ) {

		$passed_deactivate = false;
		$deactivate_link   = '';
		$before_deactivate = array();
		$after_deactivate  = array();
		foreach ( $links as $key => $link ) {
			if ( 'deactivate' === $key ) {
				$deactivate_link   = $link;
				$passed_deactivate = true;
				continue;
			}

			if ( ! $passed_deactivate ) {
				$before_deactivate[ $key ] = $link;
			} else {
				$after_deactivate[ $key ] = $link;
			}
		}

		if ( ! empty( $deactivate_link ) ) {
			/**
			 * HTML to identify the correct plugin
			 *
			 * @since 1.0.0
			 */
			$deactivate_link .= '<i class="as-module-slug" data-module-slug="' . $this->_slug . '"></i>';

			// Append deactivation link.
			$before_deactivate['deactivate'] = $deactivate_link;
		}

		return array_merge( $before_deactivate, $after_deactivate );
	}

	/**
	 * Register Account Hooks.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 */
	private function _register_account_hooks() {
		if ( ! is_admin() ) {
			return;
		}
		add_action( 'admin_notices', array( &$this, '_ask_for_review_notice' ) );
		add_action( 'admin_notices', array( &$this, '_ask_for_usage_notice' ) );
		/**
		 * Show deactivation form on themes.php page.
		 *
		 * @since 1.0.0
		 */
		add_action( 'wp_ajax_submit_uninstall_reason', array( &$this, '_submit_uninstall_reason_action' ) );

		if ( ( $this->is_theme() && self::is_themes_page() ||
			( $this->is_plugin() && self::is_plugins_page() ) )
		) {
			add_action( 'admin_footer', array( &$this, '_add_deactivation_feedback_dialog_box' ) );
		}
	}

	/**
	 * Dismiss review notice.
	 */
	public function _ask_for_review_dismiss() {
		check_ajax_referer( 'ask_for_review', 'security' );
		if ( isset( $_POST['slug'] ) ) {
			$slug = $_POST['slug'] ? sanitize_text_field( $_POST['slug'] ) : '';
			update_option( $slug . '-ask-for-review-dismissed', true );
		}
		wp_send_json_success();
	}

	/**
	 * Dismiss review notice.
	 */
	public function _ask_for_usage_dismiss() {
		check_ajax_referer( 'ask_for_usage', 'security' );
		if ( isset( $_POST['slug'] ) ) {
			$slug = $_POST['slug'] ? sanitize_text_field( $_POST['slug'] ) : '';
			update_option( $slug . '-ask-for-usage-dismissed', true );
			update_option( $slug . '-ask-for-usage-optin', false );
		}
		wp_send_json_success();
	}

	public function _ask_for_usage_optin() {
		check_ajax_referer( 'ask_for_usage', 'security' );
		if ( isset( $_POST['slug'] ) ) {
			$slug = $_POST['slug'] ? sanitize_text_field( $_POST['slug'] ) : '';
			update_option( $slug . '-ask-for-usage-dismissed', true );
			update_option( $slug . '-ask-for-usage-optin', true );
		}
		wp_send_json_success();
	}

	/**
	 * Notice to be displayed for Review.
	 */
	public function _ask_for_review_notice() {
		if ( false === get_option( $this->_slug . '-setup' ) ) {
			update_option( $this->_slug . '-setup', true );
			set_transient( $this->_slug . '-ask-for-review-flag', true, MONTH_IN_SECONDS );
		} else {
			if ( false === get_transient( $this->_slug . '-ask-for-review-flag' ) && false === get_option( $this->_slug . '-ask-for-review-dismissed' ) ) {
				$vars = array(
					'id'           => $this->_module_id,
					'slug'         => $this->_slug,
					'product_name' => $this->_product_name,
					'version'      => $this->_version,
					'module_type'  => $this->_module_type,
					'plugin_url'   => $this->_plugin_url,
				);

				as_require_template( 'forms/review/form.php', $vars );
			}
		}
	}

	/**
	 * Notice to be displayed for Review.
	 */
	public function _ask_for_usage_notice() {
		if ( false === get_option( $this->_slug . '-usage-setup' ) ) {
			update_option( $this->_slug . '-usage-setup', true );
			set_transient( $this->_slug . '-ask-for-usage-flag', true, 120 );
		} else {
			if ( false === get_transient( $this->_slug . '-ask-for-usage-flag' ) && false === get_option( $this->_slug . '-ask-for-usage-dismissed' ) ) {
				$vars = array(
					'id'           => $this->_module_id,
					'slug'         => $this->_slug,
					'product_name' => $this->_product_name,
					'version'      => $this->_version,
					'module_type'  => $this->_module_type,
					'plugin_url'   => $this->_plugin_url,
				);

				as_require_template( 'forms/usage/form.php', $vars );
			}
		}
	}

	/**
	 * Called after the user has submitted his reason for deactivating the plugin.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 */
	public function _submit_uninstall_reason_action() {

		check_ajax_referer( 'uninstall_reason', 'security' );

		$deactivation_reason = as_request_get( 'deactivation_reason' );

		$reason_info = trim( as_request_get( 'reason_info', '' ) );
		if ( ! empty( $reason_info ) ) {
			$reason_info = substr( $reason_info, 0, 128 );
		}

		$reason = array(
			'deactivation_reason' => $deactivation_reason,
			'info'                => $reason_info,
			'is_anonymous'        => as_request_get_bool( 'is_anonymous' ),
		);

		$slug = as_request_get( 'slug' );
		if ( '' !== $slug ) {
			$slug_obj = self::$_instances[ 'm_' . $slug ];
			$slug_obj->_uninstall_plugin_event( false, $reason );
		} else {
			$this->_uninstall_plugin_event( false, $reason );
		}

		// Print '1' for successful operation.
		echo 1;
		exit;
	}

	/**
	 * Plugin uninstall hook.
	 *
	 * @author CyberChimps
	 * @since  1.0..
	 *
	 * @param bool  $check_user User have plugins activation privileges.
	 * @param arrat $reason Reasons.
	 */
	public function _uninstall_plugin_event( $check_user = true, $reason = array() ) {

		if ( $check_user && ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$params = array();

		$current_user = wp_get_current_user();

		if ( isset( $reason ) ) {
			$params['deactivation_reason'] = $reason['deactivation_reason'];
			$params['reason_info']         = $reason['info'];
			$params['platform_version']    = get_bloginfo( 'version' );
			if ( $reason['is_anonymous'] || 'yes' === get_option( 'allow_user_data_' . $this->get_slug(), 'no' ) ) {
				$params['user_nickname'] = $current_user->user_nicename;
				$params['user_email']    = $current_user->user_email;
				$params['site_url']      = get_site_url();
			} else {
				$params['user_nickname'] = '';
				$params['user_email']    = '';
				$params['site_url']      = '';
			}
			$params['slug']         = $this->_slug;
			$params['product_name'] = $this->_product_name;
			$params['version']      = $this->_version;
			$params['module_type']  = $this->_module_type;
		}

		$request = array(
			'method'  => 'POST',
			'body'    => $params,
			'timeout' => 30,
		);

		// Send anonymous uninstall event only if user submitted a feedback.
		if ( isset( $reason ) ) {
			$url      = esc_url( WP_STAT__ADDRESS . '/wp-json/action/submit/uninstall/reason/' );
			$response = wp_remote_post( $url, $request );
		}

	}

	/**
	 * Get current page or the referer if executing a WP AJAX request.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public static function get_current_page() {
		if ( ! isset( self::$_pagenow ) ) {
			global $pagenow;
			if ( empty( $pagenow ) && is_admin() && is_multisite() ) {
				/**
				 * It appears that `$pagenow` is not yet initialized in some network admin pages when this method
				 * is called, so initialize it here using some pieces of code from `wp-includes/vars.php`.
				 *
				 * @author CyberChimps
				 * @since 1.0.0
				 */
				if ( is_network_admin() ) {
					preg_match( '#/wp-admin/network/?(.*?)$#i', $_SERVER['PHP_SELF'], $self_matches );
				} elseif ( is_user_admin() ) {
					preg_match( '#/wp-admin/user/?(.*?)$#i', $_SERVER['PHP_SELF'], $self_matches );
				} else {
					preg_match( '#/wp-admin/?(.*?)$#i', $_SERVER['PHP_SELF'], $self_matches );
				}

				$pagenow = $self_matches[1];
				$pagenow = trim( $pagenow, '/' );
				$pagenow = preg_replace( '#\?.*?$#', '', $pagenow );
				if ( '' === $pagenow || 'index' === $pagenow || 'index.php' === $pagenow ) {
					$pagenow = 'index.php';
				} else {
					preg_match( '#(.*?)(/|$)#', $pagenow, $self_matches );
					$pagenow = strtolower( $self_matches[1] );
					if ( '.php' !== substr( $pagenow, -4, 4 ) ) {
						$pagenow .= '.php'; // for Options +Multiviews: /wp-admin/themes/index.php (themes.php is queried).
					}
				}
			}

			self::$_pagenow = $pagenow;

			if ( self::is_ajax() &&
				'admin-ajax.php' === $pagenow
			) {
				$referer = as_get_raw_referer();

				if ( is_string( $referer ) ) {
					$parts = explode( '?', $referer );

					self::$_pagenow = basename( $parts[0] );
				}
			}
		}

		return self::$_pagenow;
	}

	/**
	 * Check is ajax request
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @return bool Is running in AJAX call.
	 *
	 * @link   http://wordpress.stackexchange.com/questions/70676/how-to-check-if-i-am-in-admin-ajax
	 */
	public static function is_ajax() {
		return ( defined( 'DOING_AJAX' ) && DOING_AJAX );
	}

	/**
	 * Helper method to check if user in the themes page.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	public static function is_themes_page() {
		return ( 'themes.php' === self::get_current_page() );
	}

	/**
	 * Helper method to check if user in the plugins page.
	 *
	 * @author CyberChimps
	 * @since  1.0.0
	 *
	 * @return bool
	 */
	static function is_plugins_page() {
		return ( 'plugins.php' === self::get_current_page() );
	}
}
