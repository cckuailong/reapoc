<?php

/**
 * The functionality that includes PMPro data within Site Health information.
 *
 * @since 2.6.2
 */
class PMPro_Site_Health {

	/**
	 * The current object instance.
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Initialize class object and use it for future init calls.
	 *
	 * @since 2.6.2
	 *
	 * @return self The class object.
	 */
	public static function init() {
		if ( ! is_object( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add hooks needed for functionality.
	 *
	 * @since 2.6.2
	 */
	public function hook() {
		add_filter( 'debug_information', [ $this, 'debug_information' ] );
	}

	/**
	 * Remove hooks needed for functionality.
	 *
	 * @since 2.6.2
	 */
	public function unhook() {
		remove_filter( 'debug_information', [ $this, 'debug_information' ] );
	}

	/**
	 * Add our data to Site Health information.
	 *
	 * @since 2.6.2
	 *
	 * @param array $info The Site Health information.
	 *
	 * @return array The updated Site Health information.
	 */
	public function debug_information( $info ) {
		$info['pmpro'] = [
			'label'       => 'Paid Memberships Pro',
			'description' => __( 'This debug information for your Paid Memberships Pro installation can assist you in getting support.', 'paid-memberships-pro' ),
			'fields'      => [
				'pmpro-cron-jobs'            => [
					'label' => __( 'Cron Job Status', 'paid-memberships-pro' ),
					'value' => self::get_cron_jobs(),
				],
				'pmpro-gateway'              => [
					'label' => __( 'Payment Gateway', 'paid-memberships-pro' ),
					'value' => self::get_gateway(),
				],
				'pmpro-gateway-env'          => [
					'label' => __( 'Payment Gateway Environment', 'paid-memberships-pro' ),
					'value' => self::get_gateway_env(),
				],
				'pmpro-orders'               => [
					'label' => __( 'Orders', 'paid-memberships-pro' ),
					'value' => self::get_orders(),
				],
				'pmpro-discount-codes'       => [
					'label' => __( 'Discount Codes', 'paid-memberships-pro' ),
					'value' => self::get_discount_codes(),
				],
				'pmpro-membership-levels'    => [
					'label' => __( 'Membership Levels', 'paid-memberships-pro' ),
					'value' => self::get_levels(),
				],
				'pmpro-custom-templates'     => [
					'label' => __( 'Custom Templates', 'paid-memberships-pro' ),
					'value' => self::get_custom_templates(),
				],
				'pmpro-getfile-usage'        => [
					'label' => __( 'getfile.php Usage', 'paid-memberships-pro' ),
					'value' => self::get_getfile_usage(),
				],
				'pmpro-htaccess-cache-usage' => [
					'label' => __( '.htaccess Cache Usage', 'paid-memberships-pro' ),
					'value' => self::get_htaccess_cache_usage(),
				],
			],
		];

		// Automatically add information about constants set.
		$info['pmpro']['fields'] = array_merge( $info['pmpro']['fields'], self::get_constants() );

		return $info;
	}

	/**
	 * Gets the level information.
	 *
	 * @since 2.6.2
	 *
	 * @return string The level information.
	 */
	public function get_levels() {
		$membership_levels = pmpro_getAllLevels( true );

		if ( ! $membership_levels ) {
			return __( 'No Levels Found', 'paid-memberships-pro' );
		}

		return wp_json_encode( $membership_levels, JSON_PRETTY_PRINT );
	}

	/**
	 * Get the discount code information.
	 *
	 * @since 2.6.2
	 *
	 * @return string The discount code information.
	 */
	public function get_discount_codes() {
		global $wpdb;

		$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `$wpdb->pmpro_discount_codes`" );

		// translators: %d: The total count of discount codes.
		return sprintf( _n( '%d discount code', '%d discount codes', $count, 'paid-memberships-pro' ), $count );
	}

	/**
	 * Get the order information.
	 *
	 * @since 2.6.2
	 *
	 * @return string The order information.
	 */
	public function get_orders() {
		global $wpdb;

		$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `$wpdb->pmpro_membership_orders`" );

		// translators: %d: The total count of orders.
		return sprintf( _n( '%d order', '%d orders', $count, 'paid-memberships-pro' ), $count );
	}

	/**
	 * Get the payment gateway information.
	 *
	 * @since 2.6.2
	 *
	 * @return string The payment gateway information.
	 */
	public function get_gateway() {
		$gateway  = pmpro_getOption( 'gateway' );
		$gateways = pmpro_gateways();

		// Check if gateway is registered.
		if ( ! isset( $gateways[ $gateway ] ) ) {
			// translators: %s: The gateway name that is not registered.
			return sprintf( __( '%s (gateway not registered)', 'paid-memberships-pro' ), $gateway );
		}

		$gateway_text = $gateways[ $gateway ];

		// Custom Stripe gateway information.
		if ( 'stripe' === $gateway ) {
			$stripe = new PMProGateway_stripe();

			$legacy  = $stripe->using_legacy_keys();
			$connect = $stripe->has_connect_credentials();

			if ( $legacy ) {
				$gateway_text .= ' (' . __( 'Legacy Keys', 'paid-memberships-pro' ) . ')';
			}

			if ( $connect ) {
				$gateway_text .= ' (' . __( 'Stripe Connect', 'paid-memberships-pro' ) . ')';
			}
		}

		return $gateway_text;
	}

	/**
	 * Get the payment gateway environment information.
	 *
	 * @since 2.6.2
	 *
	 * @return string The payment gateway environment information.
	 */
	public function get_gateway_env() {
		$environment  = pmpro_getOption( 'gateway_environment' );
		$environments = [
			'sandbox' => __( 'Sandbox/Testing', 'paid-memberships-pro' ),
			'live'    => __( 'Live/Production', 'paid-memberships-pro' ),
		];

		// Check if environment is registered.
		if ( ! isset( $environments[ $environment ] ) ) {
			// translators: %s: The environment name that is not registered.
			return sprintf( __( '%s (environment not registered)', 'paid-memberships-pro' ), $environment );
		}

		return $environments[ $environment ];
	}

	/**
	 * Get the custom template information.
	 *
	 * @since 2.6.2
	 *
	 * @return string The custom template information.
	 */
	public function get_custom_templates() {
		$parent_theme_path = get_template_directory() . '/paid-memberships-pro/';
		$child_theme_path  = get_stylesheet_directory() . '/paid-memberships-pro/';

		$parent_theme_templates = $this->get_custom_templates_from_path( $parent_theme_path );
		$child_theme_templates  = null;

		if ( $parent_theme_path !== $child_theme_path ) {
			$child_theme_templates = $this->get_custom_templates_from_path( $child_theme_path );
		}

		if ( is_wp_error( $parent_theme_templates ) ) {
			return $parent_theme_templates->get_error_message();
		}

		$templates = $parent_theme_templates;

		if ( null !== $child_theme_templates ) {
			if ( is_wp_error( $child_theme_templates ) ) {
				$child_theme_templates = $child_theme_templates->get_error_message();
			}

			$templates = [
				'parent' => $parent_theme_templates,
				'child'  => $child_theme_templates,
			];
		}

		return wp_json_encode( $templates, JSON_PRETTY_PRINT );
	}

	private function get_custom_templates_from_path( $path ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		/**
		 * @var $wp_filesystem WP_Filesystem_Base
		 */
		global $wp_filesystem;

		WP_Filesystem();

		if ( ! $wp_filesystem ) {
			return new WP_Error( 'access-denied', __( 'Unable to verify', 'paid-memberships-pro' ) );
		}

		$override_path = get_stylesheet_directory() . '/paid-memberships-pro/';
		$override_list = false;

		if ( ! $wp_filesystem->is_dir( $path ) ) {
			return new WP_Error( 'path-not-found', __( 'No template overrides', 'paid-membership-pro' ) );
		}

		$override_list = $wp_filesystem->dirlist( $path );

		if ( ! $override_list ) {
			return new WP_Error( 'path-empty', __( 'Empty override folder -- no template overrides', 'paid-membership-pro' ) );
		}

		$templates = [];

		foreach ( $override_list as $template => $info ) {
			$last_modified = $info['lastmod'] . ' ' . $info['time'];

			if ( isset( $info['lastmodunix'] ) ) {
				$last_modified = date( 'Y-m-d H:i:s', $info['lastmodunix'] );
			}

			$templates[ $template ] = [
				'last_updated' => $last_modified,
				'path'         => str_replace( ABSPATH, '', $path ) . $template,
			];
		}

		return $templates;
	}

	/**
	 * Get the cron job information.
	 *
	 * @since 2.6.2
	 *
	 * @return string The cron job information.
	 */
	public function get_cron_jobs() {
		$crons = _get_cron_array();

		$cron_times = [];

		// These are our crons.
		$expected_crons = [
			'pmpro_cron_expire_memberships',
			'pmpro_cron_expiration_warnings',
			'pmpro_cron_credit_card_expiring_warnings',
			'pmpro_cron_admin_activity_email',
		];

		$gateway = pmpro_getOption( 'gateway' );

		if ( 'stripe' === $gateway ) {
			$expected_crons[] = 'pmpro_cron_stripe_subscription_updates';
		}

		// Find any of our crons and when their next run is.
		if ( $crons ) {
			foreach ( $crons as $time => $cron ) {
				$keys    = array_keys( $cron );
				$matches = array_intersect( $expected_crons, $keys );

				foreach ( $matches as $cron_hook ) {
					$cron_times[ $cron_hook ] = date( 'Y-m-d H:i:s', $time );
				}
			}
		}

		$missing_crons = array_diff( $expected_crons, array_keys( $cron_times ) );

		$cron_information = [];

		foreach ( $missing_crons as $cron_hook ) {
			$cron_information[] = $cron_hook . ' (' . __( 'missing', 'paid-memberships-pro' ) . ')';
		}

		// Build the information of what crons are missing and what crons are going to run.
		foreach ( $cron_times as $cron_hook => $next_run ) {
			$cron_information[] = $cron_hook . ' (' . $next_run . ')';
		}

		return implode( " | \n", $cron_information );
	}

	/**
	 * Get the .htaccess services/getfile.php usage information.
	 *
	 * @since 2.6.4
	 *
	 * @return string The .htaccess services/getfile.php usage information.
	 */
	public function get_getfile_usage() {
		if ( ! defined( 'PMPRO_GETFILE_ENABLED' ) ) {
			return __( 'PMPRO_GETFILE_ENABLED is not set', 'paid-memberships-pro' );
		}

		if ( ! PMPRO_GETFILE_ENABLED ) {
			return __( 'PMPRO_GETFILE_ENABLED is off', 'paid-memberships-pro' );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';

		/**
		 * @var $wp_filesystem WP_Filesystem_Base
		 */
		global $wp_filesystem;

		WP_Filesystem();

		if ( ! $wp_filesystem ) {
			return __( 'Unable to access .htaccess file', 'paid-memberships-pro' );
		}

		if ( ! $wp_filesystem->exists( ABSPATH . '/.htaccess' ) ) {
			return __( 'Off - No .htaccess file', 'paid-memberships-pro' );
		}

		$htaccess_contents = $wp_filesystem->get_contents( ABSPATH . '/.htaccess' );

		if ( false === strpos( $htaccess_contents, '/services/getfile.php' ) ) {
			return __( 'Off', 'paid-memberships-pro' );
		}

		return __( 'On - .htaccess contains services/getfile.php usage', 'paid-memberships-pro' );
	}

	/**
	 * Get the .htaccess cache usage information.
	 *
	 * @since 2.6.4
	 *
	 * @return string The .htaccess cache usage information.
	 */
	public function get_htaccess_cache_usage() {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		/**
		 * @var $wp_filesystem WP_Filesystem_Base
		 */
		global $wp_filesystem;

		WP_Filesystem();

		if ( ! $wp_filesystem ) {
			return __( 'Unable to access .htaccess file', 'paid-memberships-pro' );
		}

		if ( ! $wp_filesystem->exists( ABSPATH . '/.htaccess' ) ) {
			return __( 'Off - No .htaccess file', 'paid-memberships-pro' );
		}

		$htaccess_contents = $wp_filesystem->get_contents( ABSPATH . '/.htaccess' );

		if ( false !== strpos( $htaccess_contents, 'ExpiresByType text/html' ) ) {
			return __( 'On - Browser cache enabled for HTML (ExpiresByType text/html), this may interfere with Content Restriction after Login. Remove that line from your .htaccess to resolve this problem.', 'paid-memberships-pro' );
		} elseif ( false !== strpos( $htaccess_contents, 'ExpiresDefault' ) ) {
			return __( 'On - Browser cache enabled for HTML (ExpiresDefault), this may interfere with Content Restriction after Login. Remove that line from your .htaccess to resolve this problem.', 'paid-memberships-pro' );
		}

		return __( 'Off', 'paid-memberships-pro' );
	}

	/**
	 * Get the constants site health information.
	 *
	 * @since 2.6.4
	 *
	 * @return array The constants site health information.
	 */
	public function get_constants() {
		$constants = [
			'PMPRO_CRON_LIMIT'                => __( 'Cron Limit', 'paid-memberships-pro' ),
			'PMPRO_DEFAULT_LEVEL'             => __( 'Default Membership Level', 'paid-memberships-pro' ),
			'PMPRO_USE_SESSIONS'              => __( 'Use Sessions', 'paid-memberships-pro' ),
		];

		$gateway_specific_constants = [
			'authorizenet' => [
				'PMPRO_AUTHNET_SILENT_POST_DEBUG' => __( 'Authorize.net Silent Post Debug Mode', 'paid-memberships-pro' ),
			],
			'braintree' => [
				'PMPRO_BRAINTREE_WEBHOOK_DEBUG'   => __( 'Braintree Webhook Debug Mode', 'paid-memberships-pro' ),
			],
			'paypal' => [
				'PMPRO_IPN_DEBUG'                 => __( 'PayPal IPN Debug Mode', 'paid-memberships-pro' ),
			],
			'paypalexpress' => [
				'PMPRO_IPN_DEBUG'                 => __( 'PayPal IPN Debug Mode', 'paid-memberships-pro' ),
			],
			'paypalstandard' => [
				'PMPRO_IPN_DEBUG'                 => __( 'PayPal IPN Debug Mode', 'paid-memberships-pro' ),
			],
			'stripe' => [
				'PMPRO_STRIPE_WEBHOOK_DELAY'      => __( 'Stripe Webhook Delay', 'paid-memberships-pro' ),
				'PMPRO_STRIPE_WEBHOOK_DEBUG'      => __( 'Stripe Webhook Debug Mode', 'paid-memberships-pro' ),
			],
			'twocheckout' => [
				'PMPRO_INS_DEBUG'                 => __( '2Checkout INS Debug Mode', 'paid-memberships-pro' ),
			],
		];

		$gateway = pmpro_getOption( 'gateway' );

		if ( $gateway && isset( $gateway_specific_constants[ $gateway ] ) ) {
			$constants = array_merge( $constants, $gateway_specific_constants[ $gateway ] );
		}

		/**
		 * Allow filtering the supported Site Health constants by other add ons.
		 *
		 * @since 2.6.4
		 *
		 * @param array  $constants The list of constants to show in Site Health.
		 * @param string $gateway   The current payment gateway.
		 */
		$constants = apply_filters( 'pmpro_site_health_constants', $constants, $gateway );

		// Get and format constant information.
		$constants_formatted = [];

		foreach ( $constants as $constant => $label ) {
			// Only get site health info for constants that are set.
			if ( ! defined( $constant ) ) {
				continue;
			}

			$constants_formatted[ 'pmpro-constants-' . $constant ] = [
				'label' => $label . ' (' . $constant . ')',
				'value' => var_export( constant( $constant ), true ),
			];
		}

		return $constants_formatted;
	}

}
