<?php
// For compatibility with old library (Namespace Alias)
use Stripe\Customer as Stripe_Customer;
use Stripe\Invoice as Stripe_Invoice;
use Stripe\Plan as Stripe_Plan;
use Stripe\Product as Stripe_Product;
use Stripe\Charge as Stripe_Charge;
use Stripe\PaymentIntent as Stripe_PaymentIntent;
use Stripe\SetupIntent as Stripe_SetupIntent;
use Stripe\Source as Stripe_Source;
use Stripe\PaymentMethod as Stripe_PaymentMethod;
use Stripe\Subscription as Stripe_Subscription;
use Stripe\ApplePayDomain as Stripe_ApplePayDomain;
use Stripe\WebhookEndpoint as Stripe_Webhook;
use Stripe\StripeClient as Stripe_Client; // Used for deleting webhook as of 2.4
use Stripe\Account as Stripe_Account;

define( "PMPRO_STRIPE_API_VERSION", "2020-03-02" );

//include pmprogateway
require_once( dirname( __FILE__ ) . "/class.pmprogateway.php" );

//load classes init method
add_action( 'init', array( 'PMProGateway_stripe', 'init' ) );

// loading plugin activation actions
add_action( 'activate_paid-memberships-pro', array( 'PMProGateway_stripe', 'pmpro_activation' ) );
add_action( 'deactivate_paid-memberships-pro', array( 'PMProGateway_stripe', 'pmpro_deactivation' ) );

/**
 * PMProGateway_stripe Class
 *
 * Handles Stripe integration.
 *
 * @since  1.4
 */
class PMProGateway_stripe extends PMProGateway {
	/**
	 * @var bool    Is the Stripe/PHP Library loaded
	 */
	private static $is_loaded = false;

	/**
	 * Stripe Class Constructor
	 *
	 * @since 1.4
	 */
	function __construct( $gateway = null ) {
		$this->gateway             = $gateway;
		$this->gateway_environment = pmpro_getOption( "gateway_environment" );

		if ( true === $this->dependencies() ) {
			$this->loadStripeLibrary();
			Stripe\Stripe::setApiKey( self::get_secretkey() );
			Stripe\Stripe::setAPIVersion( PMPRO_STRIPE_API_VERSION );
			Stripe\Stripe::setAppInfo(
				'WordPress Paid Memberships Pro',
				PMPRO_VERSION,
				'https://www.paidmembershipspro.com',
				'pp_partner_DKlIQ5DD7SFW3A'
			);
			self::$is_loaded = true;
		}

		return $this->gateway;
	}

	/**
	 * Warn if required extensions aren't loaded.
	 *
	 * @return bool
	 * @since 1.8.6.8.1
	 * @since 1.8.13.6 - Add json dependency
	 */
	public static function dependencies() {
		global $msg, $msgt, $pmpro_stripe_error;

		if ( version_compare( PHP_VERSION, '5.3.29', '<' ) ) {

			$pmpro_stripe_error = true;
			$msg                = - 1;
			$msgt               = sprintf( __( "The Stripe Gateway requires PHP 5.3.29 or greater. We recommend upgrading to PHP %s or greater. Ask your host to upgrade.", "paid-memberships-pro" ), PMPRO_PHP_MIN_VERSION );

			if ( ! is_admin() ) {
				pmpro_setMessage( $msgt, "pmpro_error" );
			}

			return false;
		}

		$modules = array( 'curl', 'mbstring', 'json' );

		foreach ( $modules as $module ) {
			if ( ! extension_loaded( $module ) ) {
				$pmpro_stripe_error = true;
				$msg                = - 1;
				$msgt               = sprintf( __( "The %s gateway depends on the %s PHP extension. Please enable it, or ask your hosting provider to enable it.", 'paid-memberships-pro' ), 'Stripe', $module );

				//throw error on checkout page
				if ( ! is_admin() ) {
					pmpro_setMessage( $msgt, 'pmpro_error' );
				}

				return false;
			}
		}

		self::$is_loaded = true;

		return true;
	}

	/**
	 * Load the Stripe API library.
	 *
	 * @since 1.8
	 * Moved into a method in version 1.8 so we only load it when needed.
	 */
	function loadStripeLibrary() {
		//load Stripe library if it hasn't been loaded already (usually by another plugin using Stripe)
		if ( ! class_exists( "Stripe\Stripe" ) ) {
			require_once( PMPRO_DIR . "/includes/lib/Stripe/init.php" );
		}
	}

	/**
	 * Run on WP init
	 *
	 * @since 1.8
	 */
	static function init() {
		//make sure Stripe is a gateway option
		add_filter( 'pmpro_gateways', array( 'PMProGateway_stripe', 'pmpro_gateways' ) );

		//add fields to payment settings
		add_filter( 'pmpro_payment_options', array( 'PMProGateway_stripe', 'pmpro_payment_options' ) );
		add_filter( 'pmpro_payment_option_fields', array(
			'PMProGateway_stripe',
			'pmpro_payment_option_fields'
		), 10, 2 );

		//add some fields to edit user page (Updates)
		add_action( 'pmpro_after_membership_level_profile_fields', array(
			'PMProGateway_stripe',
			'user_profile_fields'
		) );
		add_action( 'profile_update', array( 'PMProGateway_stripe', 'user_profile_fields_save' ) );

		//old global RE showing billing address or not
		global $pmpro_stripe_lite;
		$pmpro_stripe_lite = apply_filters( "pmpro_stripe_lite", ! pmpro_getOption( "stripe_billingaddress" ) );    //default is oposite of the stripe_billingaddress setting

		$gateway = pmpro_getGateway();
		if($gateway == "stripe")
		{
			add_filter( 'pmpro_required_billing_fields', array( 'PMProGateway_stripe', 'pmpro_required_billing_fields' ) );
		}

		//updates cron
		add_action( 'pmpro_cron_stripe_subscription_updates', array(
			'PMProGateway_stripe',
			'pmpro_cron_stripe_subscription_updates'
		) );
		
		//AJAX services for creating/disabling webhooks
		add_action( 'wp_ajax_pmpro_stripe_create_webhook', array( 'PMProGateway_stripe', 'wp_ajax_pmpro_stripe_create_webhook' ) );
		add_action( 'wp_ajax_pmpro_stripe_delete_webhook', array( 'PMProGateway_stripe', 'wp_ajax_pmpro_stripe_delete_webhook' ) );
		add_action( 'wp_ajax_pmpro_stripe_rebuild_webhook', array( 'PMProGateway_stripe', 'wp_ajax_pmpro_stripe_rebuild_webhook' ) );

		/*
            Filter pmpro_next_payment to get actual value
            via the Stripe API. This is disabled by default
            for performance reasons, but you can enable it
            by copying this line into a custom plugin or
            your active theme's functions.php and uncommenting
            it there.
        */
		//add_filter('pmpro_next_payment', array('PMProGateway_stripe', 'pmpro_next_payment'), 10, 3);

		//code to add at checkout if Stripe is the current gateway
		$default_gateway = pmpro_getOption( 'gateway' );
		$current_gateway = pmpro_getGateway();

		// $_REQUEST['review'] here means the PayPal Express review pag
		if ( ( $default_gateway == "stripe" || $current_gateway == "stripe" ) && empty( $_REQUEST['review'] ) )
		{
			add_action( 'pmpro_after_checkout_preheader', array(
				'PMProGateway_stripe',
				'pmpro_checkout_after_preheader'
			) );
			
			add_action( 'pmpro_billing_preheader', array( 'PMProGateway_stripe', 'pmpro_checkout_after_preheader' ) );
			add_filter( 'pmpro_checkout_order', array( 'PMProGateway_stripe', 'pmpro_checkout_order' ) );
			add_filter( 'pmpro_billing_order', array( 'PMProGateway_stripe', 'pmpro_checkout_order' ) );
			add_filter( 'pmpro_include_billing_address_fields', array(
				'PMProGateway_stripe',
				'pmpro_include_billing_address_fields'
			) );
			add_filter( 'pmpro_include_cardtype_field', array(
				'PMProGateway_stripe',
				'pmpro_include_billing_address_fields'
			) );
			add_filter( 'pmpro_include_payment_information_fields', array(
				'PMProGateway_stripe',
				'pmpro_include_payment_information_fields'
			) );

			//make sure we clean up subs we will be cancelling after checkout before processing
			add_action( 'pmpro_checkout_before_processing', array(
				'PMProGateway_stripe',
				'pmpro_checkout_before_processing'
			) );
		}

		add_action( 'pmpro_payment_option_fields', array( 'PMProGateway_stripe', 'pmpro_set_up_apple_pay' ), 10, 2 );
		add_action( 'init', array( 'PMProGateway_stripe', 'clear_saved_subscriptions' ) );

		// Stripe Connect functions.
		add_action( 'admin_init', array( 'PMProGateway_stripe', 'stripe_connect_save_options' ) );
		add_action( 'admin_notices', array( 'PMProGateway_stripe', 'stripe_connect_show_errors' ) );
		add_action( 'admin_notices', array( 'PMProGateway_stripe', 'stripe_connect_deauthorize' ) );
	}

	/**
	 * Clear any saved (preserved) subscription IDs that should have been processed and are now timed out.
	 */
	public static function clear_saved_subscriptions() {

		if ( ! is_user_logged_in() ) {
			return;
		}

		global $current_user;
		$preserve = get_user_meta( $current_user->ID, 'pmpro_stripe_dont_cancel', true );

		// Clean up the subscription timeout values (if applicable)
		if ( ! empty( $preserve ) ) {

			foreach ( $preserve as $sub_id => $timestamp ) {

				// Make sure the ID has "timed out" (more than 3 days since it was last updated/added.
				if ( intval( $timestamp ) >= ( current_time( 'timestamp' ) + ( 3 * DAY_IN_SECONDS ) ) ) {
					unset( $preserve[ $sub_id ] );
				}
			}

			update_user_meta( $current_user->ID, 'pmpro_stripe_dont_cancel', $preserve );
		}
	}

	/**
	 * Make sure Stripe is in the gateways list
	 *
	 * @since 1.8
	 */
	static function pmpro_gateways( $gateways ) {
		if ( empty( $gateways['stripe'] ) ) {
			$gateways['stripe'] = __( 'Stripe', 'paid-memberships-pro' );
		}

		return $gateways;
	}

	/**
	 * Get a list of payment options that the Stripe gateway needs/supports.
	 *
	 * @since 1.8
	 */
	static function getGatewayOptions() {
		$options = array(
			'sslseal',
			'nuclear_HTTPS',
			'gateway_environment',
			'stripe_secretkey',
			'stripe_publishablekey',
			'live_stripe_connect_user_id',
			'live_stripe_connect_secretkey',
			'live_stripe_connect_publishablekey',
			'sandbox_stripe_connect_user_id',
			'sandbox_stripe_connect_secretkey',
			'sandbox_stripe_connect_publishablekey',
			'stripe_webhook',
			'stripe_billingaddress',
			'currency',
			'use_ssl',
			'tax_state',
			'tax_rate',
			'accepted_credit_cards',
			'stripe_payment_request_button',
		);

		return $options;
	}

	/**
	 * Set payment options for payment settings page.
	 *
	 * @since 1.8
	 */
	static function pmpro_payment_options( $options ) {
		//get stripe options
		$stripe_options = self::getGatewayOptions();

		//merge with others.
		$options = array_merge( $stripe_options, $options );

		return $options;
	}

	/**
	 * Display fields for Stripe options.
	 *
	 * @since 1.8
	 */
	static function pmpro_payment_option_fields( $values, $gateway ) {

		$stripe = new PMProGateway_stripe();
		$gateway_environment = $stripe->gateway_environment;

		// TODO: Update to have Stripe Connect button and to hide current fields. Currently switching gateways brings fields back.
		if ( self::using_legacy_keys() ) {
			// Check if webhook is enabled or not.
			$webhook = self::does_webhook_exist();

			// Check to see if events are missing.
			if ( is_array( $webhook ) && isset( $webhook['enabled_events'] ) ) {
				$events = self::check_missing_webhook_events( $webhook['enabled_events'] );
				if ( $events ) {
					self::update_webhook_events();
				}
			}

			// Break the country cache in case new credentials were saved.
			delete_transient( 'pmpro_stripe_account_country' );
		}

		$stripe_legacy_key      = $values['stripe_publishablekey'];
		$stripe_legacy_secret   = $values['stripe_secretkey'];
		$stripe_is_legacy_setup = ( self::using_legacy_keys() && ! empty( $stripe_legacy_key ) && ! empty( $stripe_legacy_secret ) );

		// Determine if the gateway is connected in live mode and set var.
		if ( self::has_connect_credentials( 'live' ) || $stripe_is_legacy_setup ) {
			$live_connection_selector = 'pmpro_gateway-mode-connected';
		} else {
			$live_connection_selector = 'pmpro_gateway-mode-not-connected';
		}

		// Determine if the gateway is connected in test mode and set var.
		if ( self::has_connect_credentials( 'sandbox' ) || $stripe_is_legacy_setup ) {
			$test_connection_selector = 'pmpro_gateway-mode-connected';
		} else {
			$test_connection_selector = 'pmpro_gateway-mode-not-connected';
		}
		?>		
		<tr class="pmpro_settings_divider gateway gateway_stripe_live"
		    <?php if ( $gateway != "stripe" || $gateway_environment != "live" ) { ?>style="display: none;"<?php } ?>>
            <td colspan="2">
				<hr />
				<h2>
					<?php esc_html_e( 'Stripe Connect Settings', 'paid-memberships-pro' ); ?>
					<span class="pmpro_gateway-mode pmpro_gateway-mode-live <?php esc_attr_e( $live_connection_selector ); ?>">
						<?php esc_html_e( 'Live Mode:', 'paid-memberships-pro' ); ?>
						<?php if ( self::has_connect_credentials( 'live' ) ) {
							esc_html_e( 'Connected', 'paid-memberships-pro' );
						} elseif( $stripe_is_legacy_setup ) {
							esc_html_e( 'Connected with Legacy Keys', 'paid-memberships-pro' );
						} else {
							esc_html_e( 'Not Connected', 'paid-memberships-pro' );
						} ?>
					</span>
				</h2>
				<?php if ( self::using_legacy_keys() ) { ?>
					<div class="notice notice-large notice-warning inline">
						<p class="pmpro_stripe_webhook_notice">
							<strong><?php esc_html_e( 'Your site is using legacy API keys to authenticate with Stripe.', 'paid-memberships-pro' ); ?></strong><br />
							<?php esc_html_e( 'You can continue to use the legacy API keys or choose to upgrade to our new Stripe Connect solution below.', 'paid-memberships-pro' ); ?><br />
							<?php esc_html_e( 'Use the "Connect with Stripe" button below to securely authenticate with your Stripe account using Stripe Connect. Log in with the current Stripe account used for this site so that existing subscriptions are not affected by the update.', 'paid-memberships-pro' ); ?>
							<a href="https://www.paidmembershipspro.com/gateway/stripe/switch-legacy-to-connect/?utm_source=plugin&utm_medium=pmpro-paymentsettings&utm_campaign=documentation&utm_content=switch-to-connect" target="_blank"><?php esc_html_e( 'Read the documentation on switching to Stripe Connect &raquo;', 'paid-memberships-pro' ); ?></a>
						</p>
					</div>
				<?php } ?>
            </td>
        </tr>
		<tr class="gateway gateway_stripe_live" <?php if ( $gateway != "stripe" || $gateway_environment != "live" ) { ?>style="display: none;"<?php } ?>>
            <th scope="row" valign="top">
                <label><?php esc_html_e( 'Stripe Connection:', 'paid-memberships-pro' ); ?></label>
            </th>
			<td>
				<?php
				$connect_url_base = apply_filters( 'pmpro_stripe_connect_url', 'https://connect.paidmembershipspro.com' );
				if ( self::has_connect_credentials( 'live' ) ) {
					$connect_url = add_query_arg(
						array(
							'action' => 'disconnect',
							'gateway_environment' => 'live',
							'stripe_user_id' => $values['live_stripe_connect_user_id'],
							'return_url' => rawurlencode( admin_url( 'admin.php?page=pmpro-paymentsettings' ) ),
						),
						$connect_url_base
					);
					?>
					<a href="<?php echo esc_url_raw( $connect_url ); ?>" class="pmpro-stripe-connect"><span><?php esc_html_e( 'Disconnect From Stripe', 'paid-memberships-pro' ); ?></span></a>
					<p class="description"><?php esc_html_e( 'This will disconnect your site from Stripe. Users will not be able to complete membership checkout or update their billing information. Existing subscriptions will not be affected at the gateway, but new recurring orders will not be created in this site.', 'paid-memberships-pro' ); ?></p>
					<?php
				} else {
					$connect_url = add_query_arg(
						array(
							'action' => 'authorize',
							'gateway_environment' => 'live',
							'return_url' => rawurlencode( admin_url( 'admin.php?page=pmpro-paymentsettings' ) ),
						),
						$connect_url_base
					);
					?>
					<a href="<?php echo esc_url_raw( $connect_url ); ?>" class="pmpro-stripe-connect"><span><?php esc_html_e( 'Connect with Stripe', 'paid-memberships-pro' ); ?></span></a>
					<?php
				}
				?>
				<p class="description">
					<?php
						if ( pmpro_license_isValid( null, 'plus' ) ) {
							esc_html_e( 'Note: You have a valid license and are not charged additional platform fees for payment processing.', 'paid-memberships-pro');
							echo ' <a href="https://www.paidmembershipspro.com/gateway/stripe/?utm_source=plugin&utm_medium=pmpro-paymentsettings&utm_campaign=gateways&utm_content=stripe-fees#tab-fees" target="_blank">' . esc_html( 'Learn More &raquo;', 'paid-memberships-pro' ) . '</a>';
						} else {
							esc_html_e( 'Note: You are using the free Stripe payment gateway integration. This includes an additional 1% fee for payment processing. This fee is removed by activating a Plus license.', 'paid-memberships-pro');
							echo ' <a href="https://www.paidmembershipspro.com/gateway/stripe/?utm_source=plugin&utm_medium=pmpro-paymentsettings&utm_campaign=gateways&utm_content=stripe-fees#tab-fees" target="_blank">' . esc_html( 'Learn More &raquo;', 'paid-memberships-pro' ) . '</a>';
						}
					?>
				</p>
				<input type='hidden' name='live_stripe_connect_user_id' id='live_stripe_connect_user_id' value='<?php echo esc_attr( $values['live_stripe_connect_user_id'] ) ?>'/>
				<input type='hidden' name='live_stripe_connect_secretkey' id='live_stripe_connect_secretkey' value='<?php echo esc_attr( $values['live_stripe_connect_secretkey'] ) ?>'/>
				<input type='hidden' name='live_stripe_connect_publishablekey' id='live_stripe_connect_publishablekey' value='<?php echo esc_attr( $values['live_stripe_connect_publishablekey'] ) ?>'/>
			</td>
        </tr>
		<tr class="gateway gateway_stripe_live" <?php if ( $gateway != "stripe" || $gateway_environment != "live" ) { ?>style="display: none;"<?php } ?>>
            <th scope="row" valign="top">
                <label><?php esc_html_e( 'Webhook', 'paid-memberships-pro' ); ?>:</label>
            </th>
            <td>
				<?php self::get_last_webhook_date( 'live' ); ?>
				<p class="description"><?php esc_html_e( 'Webhook URL', 'paid-memberships-pro' ); ?>:
				<code><?php echo esc_html( self::get_site_webhook_url() ); ?></code></p>
            </td>
        </tr>
		<tr class="pmpro_settings_divider gateway gateway_stripe_sandbox"
		    <?php if ( $gateway != "stripe" || $gateway_environment != "sandbox" ) { ?>style="display: none;"<?php } ?>>
            <td colspan="2">
				<hr />
				<h2>
					<?php esc_html_e( 'Stripe Connect Settings', 'paid-memberships-pro' ); ?>
					<span class="pmpro_gateway-mode pmpro_gateway-mode-test <?php esc_attr_e( $live_connection_selector ); ?>">
						<?php esc_html_e( 'Test Mode:', 'paid-memberships-pro' ); ?>
						<?php if ( self::has_connect_credentials( 'sandbox' ) ) {
							esc_html_e( 'Connected', 'paid-memberships-pro' );
						} elseif( $stripe_is_legacy_setup ) {
							esc_html_e( 'Connected with Legacy Keys', 'paid-memberships-pro' );
						} else {
							esc_html_e( 'Not Connected', 'paid-memberships-pro' );
						} ?>
					</span>
				</h2>
				<?php if ( self::using_legacy_keys() ) { ?>
					<div class="notice notice-large notice-warning inline">
						<p class="pmpro_stripe_webhook_notice">
							<strong><?php esc_html_e( 'Your site is using legacy API keys to authenticate with Stripe.', 'paid-memberships-pro' ); ?></strong><br />
							<?php esc_html_e( 'You can continue to use the legacy API keys or choose to upgrade to our new Stripe Connect solution below.', 'paid-memberships-pro' ); ?><br />
							<?php esc_html_e( 'Use the "Connect with Stripe" button below to securely authenticate with your Stripe account using Stripe Connect in test mode.', 'paid-memberships-pro' ); ?>
							<a href="https://www.paidmembershipspro.com/gateway/stripe/switch-legacy-to-connect/?utm_source=plugin&utm_medium=pmpro-paymentsettings&utm_campaign=documentation&utm_content=switch-to-connect" target="_blank"><?php esc_html_e( 'Read the documentation on switching to Stripe Connect &raquo;', 'paid-memberships-pro' ); ?></a>
						</p>
					</div>
				<?php } ?>
            </td>
		</tr>
		<tr class="gateway gateway_stripe_sandbox" <?php if ( $gateway != "stripe" || $gateway_environment != "sandbox" ) { ?>style="display: none;"<?php } ?>>
            <th scope="row" valign="top">
                <label> <?php esc_html_e( 'Stripe Connection:', 'paid-memberships-pro' ); ?></label>
            </th>
			<td>
				<?php
				/**
				 * Allow customization of the Stripe Connect URL used by Paid Memberships Pro.
				 *
				 * @since 2.6.0
				 *
				 * @param string $connect_url_base The Stripe Connect URL to be used.
				 */
				$connect_url_base = apply_filters( 'pmpro_stripe_connect_url', 'https://connect.paidmembershipspro.com' );
				if ( self::has_connect_credentials( 'sandbox' ) ) {
					$connect_url = add_query_arg(
						array(
							'action' => 'disconnect',
							'gateway' => 'stripe',
							'gateway_environment' => 'test',
							'stripe_user_id' => $values['sandbox_stripe_connect_user_id'],
							'return_url' => rawurlencode( admin_url( 'admin.php?page=pmpro-paymentsettings' ) ),
						),
						$connect_url_base
					);
					?>
					<a href="<?php echo esc_url( $connect_url ); ?>" class="pmpro-stripe-connect"><span><?php esc_html_e( 'Disconnect From Stripe', 'paid-memberships-pro' ); ?></span></a>
					<p class="description"><?php esc_html_e( 'This will disconnect your site from Stripe in test mode only.', 'paid-memberships-pro' ); ?></p>
					<?php
				} else {
					$connect_url = add_query_arg(
						array(
							'action' => 'authorize',
							'gateway' => 'stripe',
							'gateway_environment' => 'test',
							'return_url' => rawurlencode( admin_url( 'admin.php?page=pmpro-paymentsettings' ) ),
						),
						$connect_url_base
					);
					?>
					<a href="<?php echo esc_url( $connect_url ); ?>" class="pmpro-stripe-connect"><span><?php esc_html_e( 'Connect with Stripe', 'paid-memberships-pro' ); ?></span></a>
					<?php
				}
				?>
				<input type='hidden' name='sandbox_stripe_connect_user_id' id='sandbox_stripe_connect_user_id' value='<?php echo esc_attr( $values['sandbox_stripe_connect_user_id'] ); ?>'/>
				<input type='hidden' name='sandbox_stripe_connect_secretkey' id='sandbox_stripe_connect_secretkey' value='<?php echo esc_attr( $values['sandbox_stripe_connect_secretkey'] ); ?>'/>
				<input type='hidden' name='sandbox_stripe_connect_publishablekey' id='sandbox_stripe_connect_publishablekey' value='<?php echo esc_attr( $values['sandbox_stripe_connect_publishablekey'] ) ?>'/>
			</td>
		</tr>
		<tr class="gateway gateway_stripe_sandbox" <?php if ( $gateway != "stripe" || $gateway_environment != "sandbox" ) { ?>style="display: none;"<?php } ?>>
            <th scope="row" valign="top">
                <label><?php esc_html_e( 'Webhook', 'paid-memberships-pro' ); ?>:</label>
            </th>
            <td>
				<?php self::get_last_webhook_date( 'sandbox' ); ?>
				<p class="description"><?php esc_html_e( 'Webhook URL', 'paid-memberships-pro' ); ?>:
				<code><?php echo esc_html( self::get_site_webhook_url() ); ?></code></p>
            </td>
        </tr>
		<tr class="pmpro_settings_divider gateway gateway_stripe" <?php if ( $gateway != "stripe" ) { ?>style="display: none;"<?php } ?>>		
            <td colspan="2">
				<hr />
				<h2 class="pmpro_stripe_legacy_keys" <?php if( ! self::show_legacy_keys_settings() ) {?>style="display: none;"<?php }?>><?php esc_html_e( 'Stripe API Settings (Legacy)', 'paid-memberships-pro' ); ?></h2>
				<?php if( ! self::show_legacy_keys_settings() ) {?>
				<p>
					<?php esc_html_e( 'Having trouble connecting through the button above or otherwise need to use your own API keys?', 'paid-memberships-pro' );?>
					<a id="pmpro_stripe_legacy_keys_toggle" href="javascript:void(0);"><?php esc_html_e( 'Click here to use the legacy API settings.', 'paid-memberships-pro' );?></a>
				</p>
				<script>
					// Toggle to show the Stripe legacy keys settings.
					jQuery(document).ready(function(){
						jQuery('#pmpro_stripe_legacy_keys_toggle').click(function(e){
							var btn = jQuery('#pmpro_stripe_legacy_keys_toggle');
							var div = btn.closest('.pmpro_settings_divider');							
							btn.parent().remove();
							jQuery('.pmpro_stripe_legacy_keys').show();
							jQuery('.pmpro_stripe_legacy_keys').addClass('gateway_stripe');
							jQuery('#stripe_publishablekey').focus();
						});
					});
				</script>
				<?php } ?>
            </td>
        </tr>
		<tr class="gateway pmpro_stripe_legacy_keys <?php if ( self::show_legacy_keys_settings() ) { echo 'gateway_stripe'; } ?>" <?php if ( $gateway != "stripe" || ! self::show_legacy_keys_settings() ) { ?>style="display: none;"<?php } ?>>
            <th scope="row" valign="top">
                <label for="stripe_publishablekey"><?php _e( 'Publishable Key', 'paid-memberships-pro' ); ?>:</label>
            </th>
            <td>
                <input type="text" id="stripe_publishablekey" name="stripe_publishablekey" value="<?php echo esc_attr( $values['stripe_publishablekey'] ) ?>" class="regular-text code" />
				<?php
				$public_key_prefix = substr( $values['stripe_publishablekey'], 0, 3 );
				if ( ! empty( $values['stripe_publishablekey'] ) && $public_key_prefix != 'pk_' ) {
					?>
                    <p class="pmpro_red"><strong><?php _e( 'Your Publishable Key appears incorrect.', 'paid-memberships-pro' ); ?></strong></p>
					<?php
				}
				?>
            </td>
        </tr>
        <tr class="gateway pmpro_stripe_legacy_keys <?php if ( self::show_legacy_keys_settings() ) { echo 'gateway_stripe'; } ?>" <?php if ( $gateway != "stripe" ||  ! self::show_legacy_keys_settings() ) { ?>style="display: none;"<?php } ?>>
            <th scope="row" valign="top">
                <label for="stripe_secretkey"><?php _e( 'Secret Key', 'paid-memberships-pro' ); ?>:</label>
            </th>
            <td>
                <input type="text" id="stripe_secretkey" name="stripe_secretkey" value="<?php echo esc_attr( $values['stripe_secretkey'] ) ?>" autocomplete="off" class="regular-text code pmpro-admin-secure-key" />
            </td>
        </tr>		
		<tr class="gateway pmpro_stripe_legacy_keys <?php if ( self::show_legacy_keys_settings() ) { echo 'gateway_stripe'; } ?>" <?php if ( $gateway != "stripe" || ! self::show_legacy_keys_settings() ) { ?>style="display: none;"<?php } ?>>
            <th scope="row" valign="top">
                <label><?php esc_html_e( 'Webhook', 'paid-memberships-pro' ); ?>:</label>
            </th>
            <td>
				<?php if ( ! empty( $webhook ) && is_array( $webhook ) && self::show_legacy_keys_settings()) { ?>
				<button type="button" id="pmpro_stripe_create_webhook" class="button button-secondary" style="display: none;"><span class="dashicons dashicons-update-alt"></span> <?php _e( 'Create Webhook' ,'paid-memberships-pro' ); ?></button>
					<?php 
						if ( 'disabled' === $webhook['status'] ) {
							// Check webhook status.
							?>
							<div class="notice error inline">
								<p id="pmpro_stripe_webhook_notice" class="pmpro_stripe_webhook_notice"><?php _e( 'A webhook is set up in Stripe, but it is disabled.', 'paid-memberships-pro' ); ?> <a id="pmpro_stripe_rebuild_webhook" href="#">Rebuild Webhook</a></p>
							</div>
							<?php
						} elseif ( $webhook['api_version'] < PMPRO_STRIPE_API_VERSION ) {
							// Check webhook API version.
							?>
							<div class="notice error inline">
								<p id="pmpro_stripe_webhook_notice" class="pmpro_stripe_webhook_notice"><?php _e( 'A webhook is set up in Stripe, but it is using an old API version.', 'paid-memberships-pro' ); ?> <a id="pmpro_stripe_rebuild_webhook" href="#"><?php _e( 'Rebuild Webhook', 'paid-memberships-pro' ); ?></a></p>
							</div>
							<?php
						} else {
							?>
							<div class="notice notice-success inline">
								<p id="pmpro_stripe_webhook_notice" class="pmpro_stripe_webhook_notice"><?php _e( 'Your webhook is enabled.', 'paid-memberships-pro' ); ?> <a id="pmpro_stripe_delete_webhook" href="#"><?php _e( 'Disable Webhook', 'paid-memberships-pro' ); ?></a></p>
							</div>
							<?php
						}
					} elseif ( self::show_legacy_keys_settings() ) { ?>
						<button type="button" id="pmpro_stripe_create_webhook" class="button button-secondary"><span class="dashicons dashicons-update-alt"></span> <?php _e( 'Create Webhook' ,'paid-memberships-pro' ); ?></button>
						<div class="notice error inline">
							<p id="pmpro_stripe_webhook_notice" class="pmpro_stripe_webhook_notice"><?php _e('A webhook in Stripe is required to process recurring payments, manage failed payments, and synchronize cancellations.', 'paid-memberships-pro' );?></p>
						</div>
						<?php
					}
				?>
				<p class="description"><?php esc_html_e( 'Webhook URL', 'paid-memberships-pro' ); ?>:
				<code><?php echo self::get_site_webhook_url(); ?></code></p>
            </td>
        </tr>
		<tr class="pmpro_settings_divider gateway gateway_stripe" <?php if ( $gateway != "stripe" ) { ?>style="display: none;"<?php } ?>>		
            <td colspan="2">
				<hr />
				<h2><?php esc_html_e( 'Other Stripe Settings', 'paid-memberships-pro' ); ?></h2>				
            </td>
        </tr>
		<tr class="gateway gateway_stripe" <?php if ( $gateway != "stripe" ) { ?>style="display: none;"<?php } ?>>
            <th><?php _e( 'Stripe API Version', 'paid-memberships-pro' ); ?>:</th>
            <td><code><?php echo PMPRO_STRIPE_API_VERSION; ?></code></td>
        </tr>
		<tr class="gateway gateway_stripe" <?php if ( $gateway != "stripe" ) { ?>style="display: none;"<?php } ?>>
            <th scope="row" valign="top">
                <label for="stripe_billingaddress"><?php _e( 'Show Billing Address Fields', 'paid-memberships-pro' ); ?>:</label>
            </th>
            <td>
                <select id="stripe_billingaddress" name="stripe_billingaddress">
                    <option value="0"
					        <?php if ( empty( $values['stripe_billingaddress'] ) ) { ?>selected="selected"<?php } ?>><?php _e( 'No', 'paid-memberships-pro' ); ?></option>
                    <option value="1"
					        <?php if ( ! empty( $values['stripe_billingaddress'] ) ) { ?>selected="selected"<?php } ?>><?php _e( 'Yes', 'paid-memberships-pro' ); ?></option>
                </select>
				<p class="description"><?php _e( "Stripe doesn't require billing address fields. Choose 'No' to hide them on the checkout page.<br /><strong>If No, make sure you disable address verification in the Stripe dashboard settings.</strong>", 'paid-memberships-pro' ); ?></p>
            </td>
        </tr>
		<tr class="gateway gateway_stripe" <?php if ( $gateway != "stripe" ) { ?>style="display: none;"<?php } ?>>
            <th scope="row" valign="top">
				<label for="stripe_payment_request_button"><?php _e( 'Enable Payment Request Button', 'paid-memberships-pro' ); ?>:</label>
            </th>
            <td>
                <select id="stripe_payment_request_button" name="stripe_payment_request_button">
                    <option value="0"
					        <?php if ( empty( $values['stripe_payment_request_button'] ) ) { ?>selected="selected"<?php } ?>><?php _e( 'No', 'paid-memberships-pro' ); ?></option>
                    <option value="1"
					        <?php if ( ! empty( $values['stripe_payment_request_button'] ) ) { ?>selected="selected"<?php } ?>><?php _e( 'Yes', 'paid-memberships-pro' ); ?></option>
                </select>
                <?php
	                $allowed_stripe_payment_button_html = array (
						'a' => array (
							'href' => array(),
							'target' => array(),
							'title' => array(),
						),
					);
				?>
				<p class="description"><?php echo sprintf( wp_kses( __( 'Allow users to pay using Apple Pay, Google Pay, or Microsoft Pay depending on their browser. When enabled, your domain will automatically be registered with Apple and a domain association file will be hosted on your site. <a target="_blank" href="%s" title="More Information about the domain association file for Apple Pay">More Information &raquo;</a>', 'paid-memberships-pro' ), $allowed_stripe_payment_button_html ), 'https://stripe.com/docs/stripe-js/elements/payment-request-button#verifying-your-domain-with-apple-pay' ); ?></p>
					<?php
					if ( ! empty( $values['stripe_payment_request_button'] ) ) {
						// Are there any issues with how the payment request button is set up?
						$payment_request_error = null;
						$allowed_payment_request_error_html = array (
							'a' => array (
								'href' => array(),
								'target' => array(),
								'title' => array(),
							),
						);
						if ( empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off" ) {
							$payment_request_error = sprintf( wp_kses( __( 'This webpage is being served over HTTP, but the Stripe Payment Request Button will only work on pages being served over HTTPS. To resolve this, you must <a target="_blank" href="%s" title="Configuring WordPress to Always Use HTTPS/SSL">set up WordPress to always use HTTPS</a>.', 'paid-memberships-pro' ), $allowed_payment_request_error_html ), 'https://www.paidmembershipspro.com/configuring-wordpress-always-use-httpsssl/?utm_source=plugin&utm_medium=pmpro-paymentsettings&utm_campaign=blog&utm_content=configure-https' );
						} elseif ( self::using_legacy_keys() && substr( $values['stripe_publishablekey'], 0, 8 ) !== "pk_live_" && substr( $values['stripe_publishablekey'], 0, 8 ) !== "pk_test_" ) {
							$payment_request_error = sprintf( wp_kses( __( 'It looks like you are using an older Stripe publishable key. In order to use the Payment Request Button feature, you will need to update your API key, which will be prefixed with "pk_live_" or "pk_test_". <a target="_blank" href="%s" title="Stripe Dashboard API Key Settings">Log in to your Stripe Dashboard to roll your publishable key</a>.', 'paid-memberships-pro' ), $allowed_payment_request_error_html ), 'https://dashboard.stripe.com/account/apikeys' );
						} elseif ( self::using_legacy_keys() && substr( $values['stripe_secretkey'], 0, 8 ) !== "sk_live_" && substr( $values['stripe_secretkey'], 0, 8 ) !== "sk_test_" ) {
							$payment_request_error = sprintf( wp_kses( __( 'It looks like you are using an older Stripe secret key. In order to use the Payment Request Button feature, you will need to update your API key, which will be prefixed with "sk_live_" or "sk_test_". <a target="_blank" href="%s" title="Stripe Dashboard API Key Settings">Log in to your Stripe Dashboard to roll your secret key</a>.', 'paid-memberships-pro' ), $allowed_payment_request_error_html ), 'https://dashboard.stripe.com/account/apikeys' );
						} elseif ( ! $stripe->pmpro_does_apple_pay_domain_exist() ) {
							$payment_request_error = sprintf( wp_kses( __( 'Your domain could not be registered with Apple to enable Apple Pay. Please try <a target="_blank" href="%s" title="Apple Pay Settings Page in Stripe">registering your domain manually from the Apple Pay settings page in Stripe</a>.', 'paid-memberships-pro' ), $allowed_payment_request_error_html ), 'https://dashboard.stripe.com/settings/payments/apple_pay' );
						}
						if ( ! empty( $payment_request_error ) ) {
							?>
							<div class="notice error inline">
								<p id="pmpro_stripe_payment_request_button_notice"><?php echo( $payment_request_error ); ?></p>
							</div>
							<?php
						}
					}
					?>
            </td>
        </tr>
        <?php if ( ! function_exists( 'pmproappe_pmpro_valid_gateways' ) ) {
				$allowed_appe_html = array (
					'a' => array (
						'href' => array(),
						'target' => array(),
						'title' => array(),
					),
				);
				echo '<tr class="gateway gateway_stripe"';
				if ( $gateway != "stripe" ) { 
					echo ' style="display: none;"';
				}
				echo '><th>&nbsp;</th><td><p class="description">' . sprintf( wp_kses( __( 'Optional: Offer PayPal Express as an option at checkout using the <a target="_blank" href="%s" title="Paid Memberships Pro - Add PayPal Express Option at Checkout Add On">Add PayPal Express Add On</a>.', 'paid-memberships-pro' ), $allowed_appe_html ), 'https://www.paidmembershipspro.com/add-ons/pmpro-add-paypal-express-option-checkout/?utm_source=plugin&utm_medium=pmpro-paymentsettings&utm_campaign=add-ons&utm_content=pmpro-add-paypal-express-option-checkout' ) . '</p></td></tr>';
		} ?>
		<?php
	}

	/**
	 * AJAX callback to create webhooks.
	 */
	static function wp_ajax_pmpro_stripe_create_webhook() {
		// TODO: Try to get secretkey from Stripe Connect before sending legacy key.
		$secretkey = sanitize_text_field( $_REQUEST['secretkey'] );
		
		$stripe = new PMProGateway_stripe();
		Stripe\Stripe::setApiKey( $secretkey );
		
		$r = $stripe::update_webhook_events();
		
		if ( empty( $r ) ) {
			$r = array(
				'success' => false,
				'notice' => 'error',
				'message' => __( 'Webhook creation failed. You might already have a webhook set up.', 'paid-memberships-pro' ),
				'response' => $r
			);
		} else {
			if ( is_wp_error( $r ) ) {
				$r = array(
					'success' => false,
					'notice' => 'error',
					'message' => $r->get_error_message(),
					'response' => $r
				);
			} else {
				$r = array(
					'success' => true,
					'notice' => 'notice-success',
					'message' => __( 'Your webhook is enabled.', 'paid-memberships-pro' ),
					'response' => $r
				);
			}
		}
		
		echo json_encode( $r );
		
		exit;
	}
	
	/**
	 * AJAX callback to disable webhooks.
	 */
	static function wp_ajax_pmpro_stripe_delete_webhook() {
		// TODO: Try to get secretkey from Stripe Connect before sending legacy key.
		$secretkey = sanitize_text_field( $_REQUEST['secretkey'] );
		
		$stripe = new PMProGateway_stripe();
		Stripe\Stripe::setApiKey( $secretkey );
		
		$webhook = self::does_webhook_exist();

		if ( empty( $webhook ) ) {
			$r = array(
				'success' => true,
				'notice' => 'error',
				'message' => __( 'A webhook in Stripe is required to process recurring payments, manage failed payments, and synchronize cancellations.', 'paid-memberships-pro' )
			);
		} else {
			$r = $stripe::delete_webhook( $webhook, $secretkey );
			
			if ( is_wp_error( $r ) ) {
				$r = array(
					'success' => false,
					'notice' => 'error',
					'message' => $r->get_error_message(),
					'response' => $r
				);
			} else {
				if ( ! empty( $r['deleted'] ) && $r['deleted'] == true ) {
					$r = array(
						'success' => true,
						'notice' => 'error',
						'message' => __( 'A webhook in Stripe is required to process recurring payments, manage failed payments, and synchronize cancellations.', 'paid-memberships-pro' ),
						'response' => $r
					);
				} else {
					$r = array(
						'success' => false,
						'notice' => 'error',
						'message' => __( 'There was an error deleting the webhook.', 'paid-memberships-pro' ),
						'response' => $r
					);
				}
			}
		}

		echo json_encode( $r );
		
		exit;
	}

	/**
	 * AJAX callback to disable webhooks.
	 */
	static function wp_ajax_pmpro_stripe_rebuild_webhook() {
		$secretkey = sanitize_text_field( $_REQUEST['secretkey'] );
		
		$stripe = new PMProGateway_stripe();
		Stripe\Stripe::setApiKey( $secretkey );
		
		$webhook = self::does_webhook_exist();

		if ( empty( $webhook ) ) {
			$r = array(
				'success' => true,
				'notice' => 'error',
				'message' => __( 'A webhook in Stripe is required to process recurring payments, manage failed payments, and synchronize cancellations.', 'paid-memberships-pro' )
			);
		} else {
			$r = $stripe::delete_webhook( $webhook, $secretkey );
			
			if ( is_wp_error( $r ) ) {
				$r = array(
					'success' => false,
					'notice' => 'error',
					'message' => $r->get_error_message(),
					'response' => $r
				);
			} else {
				if ( ! empty( $r['deleted'] ) && $r['deleted'] == true ) {
					// Deletion succeeded. Rebuild...
					self::does_webhook_exist( true ); // Break the webhook cache.
					$r = $stripe::update_webhook_events();
		
					if ( empty( $r ) ) {
						$r = array(
							'success' => false,
							'notice' => 'error',
							'message' => $r,
							'message' => __( 'Webhook creation failed. Please refresh and try again.', 'paid-memberships-pro' ),
							'response' => $r
						);
					} else {
						if ( is_wp_error( $r ) ) {
							$r = array(
								'success' => false,
								'notice' => 'error',
								'message' => $r->get_error_message(),
								'response' => $r
							);
						} else {
							$r = array(
								'success' => true,
								'notice' => 'notice-success',
								'message' => __( 'Your webhook is enabled.', 'paid-memberships-pro' ),
								'response' => $r
							);
						}
					}
				} else {
					$r = array(
						'success' => false,
						'notice' => 'error',
						'message' => __( 'There was an error deleting the webhook.', 'paid-memberships-pro' ),
						'response' => $r
					);
				}
			}
		}

		echo json_encode( $r );
		
		exit;
	}

	/**
	 * Code added to checkout preheader.
	 *
	 * @since 1.8
	 */
	static function pmpro_checkout_after_preheader( $order ) {
		global $gateway, $pmpro_level, $current_user, $pmpro_requirebilling, $pmpro_pages, $pmpro_currency;

		$default_gateway = pmpro_getOption( "gateway" );

		if ( $gateway == "stripe" || $default_gateway == "stripe" ) {
			//stripe js library
			wp_enqueue_script( "stripe", "https://js.stripe.com/v3/", array(), null );

			if ( ! function_exists( 'pmpro_stripe_javascript' ) ) {
				$localize_vars = array(
					'publishableKey' => self::get_publishablekey(),
					'user_id'        => self::get_connect_user_id(),
					'verifyAddress'  => apply_filters( 'pmpro_stripe_verify_address', pmpro_getOption( 'stripe_billingaddress' ) ),
					'ajaxUrl'        => admin_url( "admin-ajax.php" ),
					'msgAuthenticationValidated' => __( 'Verification steps confirmed. Your payment is processing.', 'paid-memberships-pro' ),
					'pmpro_require_billing' => $pmpro_requirebilling,
					'restUrl' => get_rest_url(),
					'siteName' => get_bloginfo( 'name' ),
					'updatePaymentRequestButton' => apply_filters( 'pmpro_stripe_update_payment_request_button', true ),
					'currency' => strtolower( $pmpro_currency ),
					'accountCountry' => self::get_account_country(),
				);

				if ( ! empty( $order ) ) {
					if ( ! empty( $order->Gateway->payment_intent ) ) {
						$localize_vars['paymentIntent'] = $order->Gateway->payment_intent;
					}
					if ( ! empty( $order->Gateway->setup_intent ) ) {
						$localize_vars['setupIntent']  = $order->Gateway->setup_intent;
						$localize_vars['subscription'] = $order->Gateway->subscription;
					}
				}

				wp_register_script( 'pmpro_stripe',
					plugins_url( 'js/pmpro-stripe.js', PMPRO_BASE_FILE ),
					array( 'jquery' ),
					PMPRO_VERSION );
				wp_localize_script( 'pmpro_stripe', 'pmproStripe', $localize_vars );
				wp_enqueue_script( 'pmpro_stripe' );
			}
		}
	}

	/**
	 * Don't require the CVV.
	 * Don't require address fields if they are set to hide.
	 */
	static function pmpro_required_billing_fields( $fields ) {
		global $pmpro_stripe_lite, $current_user, $bemail, $bconfirmemail;

		//CVV is not required if set that way at Stripe. The Stripe JS will require it if it is required.
		unset( $fields['CVV'] );

		//if using stripe lite, remove some fields from the required array
		if ( $pmpro_stripe_lite ) {
			//some fields to remove
			$remove = array(
				'bfirstname',
				'blastname',
				'baddress1',
				'bcity',
				'bstate',
				'bzipcode',
				'bphone',
				'bcountry',
				'CardType'
			);
			//if a user is logged in, don't require bemail either
			if ( ! empty( $current_user->user_email ) ) {
				$remove[]      = 'bemail';
				$bemail        = $current_user->user_email;
				$bconfirmemail = $bemail;
			}
			//remove the fields
			foreach ( $remove as $field ) {
				unset( $fields[ $field ] );
			}
		}

		return $fields;
	}

	/**
	 * Get available webhooks
	 * 
	 * @since 2.4
	 */
	static function get_webhooks( $limit = 10 ) {		
		if ( ! class_exists( 'Stripe\WebhookEndpoint' ) ) {
			// Load Stripe library.
			new PMProGateway_stripe();
			if ( ! class_exists( 'Stripe\WebhookEndpoint' ) ) {
				// Couldn't load library.
				return false;
			}
		}

		try {
			$webhooks = Stripe_Webhook::all( [ 'limit' => apply_filters( 'pmpro_stripe_webhook_retrieve_limit', $limit ) ] );
		} catch (\Throwable $th) {
			$webhooks = $th->getMessage();
		} catch (\Exception $e) {
			$webhooks = $e->getMessage();
		}
		
		return $webhooks;
	}

	/**
	 * Get current webhook URL for website to compare.
	 * 
	 * @since 2.4
	 */
	static function get_site_webhook_url() {
		return admin_url( 'admin-ajax.php' ) . '?action=stripe_webhook';
	}

	/**
	 * List of current enabled events required for PMPro to work.
	 * 
	 * @since 2.4
	 */
	static function webhook_events() {
		return apply_filters( 'pmpro_stripe_webhook_events', array(
			'invoice.payment_succeeded',
			'invoice.payment_action_required',
			'customer.subscription.deleted',
			'charge.failed'
		) );
	}

	/**
	 * Create webhook with relevant events
	 * 
	 * @since 2.4
	 */
	static function create_webhook() {
		try {
			$create = Stripe_Webhook::create([
				'url' => self::get_site_webhook_url(),
				'enabled_events' => self::webhook_events(),
				'api_version' => PMPRO_STRIPE_API_VERSION,
			]);

			if ( $create ) {
				return $create->id;
			}
		} catch (\Throwable $th) {
			//throw $th;
			return new WP_Error( 'error', $th->getMessage() );
		} catch (\Exception $e) {
			//throw $th;
			return new WP_Error( 'error', $e->getMessage() );
		}
		
	}

	/**
	 * See if a webhook is registered with Stripe.
	 * 
	 * @since 2.4
	 */
	static function does_webhook_exist( $force = false ) {
		static $cached_webhook = null;
		if ( ! empty( $cached_webhook ) && ! $force ) {
			return $cached_webhook;
		}

		$webhooks = self::get_webhooks();
		
		$webhook_id = false;
		if ( ! empty( $webhooks ) && ! empty( $webhooks['data'] ) ) {

			$pmpro_webhook_url = self::get_site_webhook_url();

			foreach( $webhooks as $webhook ) {
				if ( $webhook->url == $pmpro_webhook_url ) {
					$webhook_id = $webhook->id;
					$webhook_events = $webhook->enabled_events;
					$webhook_api_version = $webhook->api_version;
					$webhook_status = $webhook->status;
					continue;
				}
			}
		} else {
			$webhook_id = false; // make sure it's false if none are found.
		}

		if ( $webhook_id ) {
			$webhook_data = array();
			$webhook_data['webhook_id'] = $webhook_id;
			$webhook_data['enabled_events'] = $webhook_events;
			$webhook_data['api_version'] = $webhook_api_version;
			$webhook_data['status'] = $webhook_status;
			$cached_webhook = $webhook_data;
		} else {
			$cached_webhook = false;
		}
		return $cached_webhook;
	}

	/**
	 * Get a list of events that are missing between the created existing webhook and required webhook events for Paid Memberships Pro.
	 * 
	 * @since 2.4
	 */
	static function check_missing_webhook_events( $webhook_events ) {

		// Get required events
		$pmpro_webhook_events = self::webhook_events();

		// No missing events if webhook event is "All Events" selected.
		if ( is_array( $webhook_events ) && $webhook_events[0] === '*' ) {
			return false;
		} 

		if ( count( array_diff( $pmpro_webhook_events, $webhook_events ) ) ) {
			$events = array_unique( array_merge( $pmpro_webhook_events, $webhook_events ) );
			// Force reset of indexes for Stripe.
			$events = array_values( $events );
		} else {
			$events = false;
		}

		return $events;
	}

	/**
	 * Update required webhook enabled events.
	 * 
	 * @since 2.4
	 */
	static function update_webhook_events() {

		// Also checks database to see if it's been saved.
		$webhook = self::does_webhook_exist();

		if ( empty( $webhook ) ) {
			$create = self::create_webhook();
			return $create;
		}

		// Bail if no enabled events for a webhook are passed through.
		if ( ! isset( $webhook['enabled_events'] ) ) {
			return;
		}
		
		$events = self::check_missing_webhook_events( $webhook['enabled_events'] );
		
		if ( $events ) {

			try {
				$update = Stripe_Webhook::update( 
					$webhook['webhook_id'],
					['enabled_events' => $events ]
				);
	
				if ( $update ) {
					return $update;
				}
			} catch (\Throwable $th) {
				//throw $th;
				return new WP_Error( 'error', $th->getMessage() );
			} catch (\Exception $e) {
				//throw $th;
				return new WP_Error( 'error', $e->getMessage() );
			}
				
		}
		
	}

	/**
	 * Delete an existing webhook.
	 * 
	 * @since 2.4
	 */
	static function delete_webhook( $webhook_id, $secretkey = false ) {
		if ( empty( $secretkey ) ) {
			$secretkey = self::get_secretkey();
		}
		if ( is_array( $webhook_id ) ) {
			$webhook_id = $webhook_id['webhook_id'];
		}
		
		try {
			$stripe = new Stripe_Client( $secretkey );
			$delete = $stripe->webhookEndpoints->delete( $webhook_id, [] );
		} catch (\Throwable $th) {
			return new WP_Error( 'error', $th->getMessage() );
		} catch (\Exception $e) {
			return new WP_Error( 'error', $e->getMessage() );
		}

		return $delete;
	}

	/**
	 * Filtering orders at checkout.
	 *
	 * @since 1.8
	 */
	static function pmpro_checkout_order( $morder ) {

		// Create a code for the order.
		if ( empty( $morder->code ) ) {
			$morder->code = $morder->getRandomCode();
		}

		// Add the PaymentIntent ID to the order.
		if ( ! empty ( $_REQUEST['payment_intent_id'] ) ) {
			$morder->payment_intent_id = sanitize_text_field( $_REQUEST['payment_intent_id'] );
		}

		// Add the SetupIntent ID to the order.
		if ( ! empty ( $_REQUEST['setup_intent_id'] ) ) {
			$morder->setup_intent_id = sanitize_text_field( $_REQUEST['setup_intent_id'] );
		}

		// Add the Subscription ID to the order.
		if ( ! empty ( $_REQUEST['subscription_id'] ) ) {
			$morder->subscription_transaction_id = sanitize_text_field( $_REQUEST['subscription_id'] );
		}

		// Add the PaymentMethod ID to the order.
		if ( ! empty ( $_REQUEST['payment_method_id'] ) ) {
			$morder->payment_method_id = sanitize_text_field( $_REQUEST['payment_method_id'] );
		}

		// Add the Customer ID to the order.
		if ( empty( $morder->customer_id ) ) {

		}
		if ( ! empty ( $_REQUEST['customer_id'] ) ) {
			$morder->customer_id = sanitize_text_field( $_REQUEST['customer_id'] );
		}

		//stripe lite code to get name from other sources if available
		global $pmpro_stripe_lite, $current_user;
		if ( ! empty( $pmpro_stripe_lite ) && empty( $morder->FirstName ) && empty( $morder->LastName ) ) {
			if ( ! empty( $current_user->ID ) ) {
				$morder->FirstName = get_user_meta( $current_user->ID, "first_name", true );
				$morder->LastName  = get_user_meta( $current_user->ID, "last_name", true );
			} elseif ( ! empty( $_REQUEST['first_name'] ) && ! empty( $_REQUEST['last_name'] ) ) {
				$morder->FirstName = sanitize_text_field( $_REQUEST['first_name'] );
				$morder->LastName  = sanitize_text_field( $_REQUEST['last_name'] );
			}
		}

		return $morder;
	}

	/**
	 * Code to run after checkout
	 *
	 * @since 1.8
	 */
	static function pmpro_after_checkout( $user_id, $morder ) {
		global $gateway;

		if ( $gateway == "stripe" ) {
			if ( self::$is_loaded && ! empty( $morder ) && ! empty( $morder->Gateway ) && ! empty( $morder->Gateway->customer ) && ! empty( $morder->Gateway->customer->id ) ) {
				update_user_meta( $user_id, "pmpro_stripe_customerid", $morder->Gateway->customer->id );
			}
		}
	}

	/**
	 * Check settings if billing address should be shown.
	 * @since 1.8
	 */
	static function pmpro_include_billing_address_fields( $include ) {
		//check settings RE showing billing address
		if ( ! pmpro_getOption( "stripe_billingaddress" ) ) {
			$include = false;
		}

		return $include;
	}

	/**
	 * Use our own payment fields at checkout. (Remove the name attributes.)
	 * @since 1.8
	 */
	static function pmpro_include_payment_information_fields( $include ) {
		//global vars
		global $pmpro_requirebilling, $pmpro_show_discount_code, $discount_code, $CardType, $AccountNumber, $ExpirationMonth, $ExpirationYear;

		//get accepted credit cards
		$pmpro_accepted_credit_cards        = pmpro_getOption( "accepted_credit_cards" );
		$pmpro_accepted_credit_cards        = explode( ",", $pmpro_accepted_credit_cards );
		$pmpro_accepted_credit_cards_string = pmpro_implodeToEnglish( $pmpro_accepted_credit_cards );

		//include ours
		?>
        <div id="pmpro_payment_information_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_payment_information_fields' ); ?>"
		     <?php if ( ! $pmpro_requirebilling || apply_filters( "pmpro_hide_payment_information_fields", false ) ) { ?>style="display: none;"<?php } ?>>
            <h3>
                <span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php _e( 'Payment Information', 'paid-memberships-pro' ); ?></span>
                <span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-msg' ); ?>"><?php printf( __( 'We Accept %s', 'paid-memberships-pro' ), $pmpro_accepted_credit_cards_string ); ?></span>
            </h3>
			<?php $sslseal = pmpro_getOption( "sslseal" ); ?>
			<?php if ( ! empty( $sslseal ) ) { ?>
            <div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields-display-seal' ); ?>">
				<?php } ?>
		<?php
			if ( pmpro_getOption( 'stripe_payment_request_button' ) ) { ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-payment-request-button', 'pmpro_checkout-field-payment-request-button' ); ?>">
					<div id="payment-request-button"><!-- Alternate payment method will be inserted here. --></div>
					<h4 class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-credit-card', 'pmpro_payment-credit-card' ); ?>"><?php esc_html_e( 'Pay with Credit Card', 'paid-memberships-pro' ); ?></h4>
				</div>
				<?php
			}
		?>
                <div class="pmpro_checkout-fields<?php if ( ! empty( $sslseal ) ) { ?> pmpro_checkout-fields-leftcol<?php } ?>">
					<?php
					$pmpro_include_cardtype_field = apply_filters( 'pmpro_include_cardtype_field', false );
					if ( $pmpro_include_cardtype_field ) { ?>
                        <div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-card-type', 'pmpro_payment-card-type' ); ?>">
                            <label for="CardType"><?php _e( 'Card Type', 'paid-memberships-pro' ); ?></label>
                            <select id="CardType" class="<?php echo pmpro_get_element_class( 'CardType' ); ?>">
								<?php foreach ( $pmpro_accepted_credit_cards as $cc ) { ?>
                                    <option value="<?php echo $cc ?>"
									        <?php if ( $CardType == $cc ) { ?>selected="selected"<?php } ?>><?php echo $cc ?></option>
								<?php } ?>
                            </select>
                        </div>
					<?php } else { ?>
                        <input type="hidden" id="CardType" name="CardType"
                               value="<?php echo esc_attr( $CardType ); ?>"/>
					<?php } ?>
                    <div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-account-number', 'pmpro_payment-account-number' ); ?>">
                        <label for="AccountNumber"><?php _e( 'Card Number', 'paid-memberships-pro' ); ?></label>
                        <div id="AccountNumber"></div>
                    </div>
                    <div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-expiration', 'pmpro_payment-expiration' ); ?>">
                        <label for="Expiry"><?php _e( 'Expiration Date', 'paid-memberships-pro' ); ?></label>
                        <div id="Expiry"></div>
                    </div>
					<?php
					$pmpro_show_cvv = apply_filters( "pmpro_show_cvv", true );
					if ( $pmpro_show_cvv ) { ?>
                        <div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-cvv', 'pmpro_payment-cvv' ); ?>">
                            <label for="CVV"><?php _e( 'CVC', 'paid-memberships-pro' ); ?></label>
                            <div id="CVV"></div>
                        </div>
					<?php } ?>
					<?php if ( $pmpro_show_discount_code ) { ?>
                        <div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-discount-code', 'pmpro_payment-discount-code' ); ?>">
                            <label for="discount_code"><?php _e( 'Discount Code', 'paid-memberships-pro' ); ?></label>
                            <input class="<?php echo pmpro_get_element_class( 'input pmpro_alter_price', 'discount_code' ); ?>"
                                   id="discount_code" name="discount_code" type="text" size="10"
                                   value="<?php echo esc_attr( $discount_code ) ?>"/>
                            <input type="button" id="discount_code_button" name="discount_code_button"
                                   value="<?php _e( 'Apply', 'paid-memberships-pro' ); ?>"/>
                            <p id="discount_code_message" class="<?php echo pmpro_get_element_class( 'pmpro_message' ); ?>" style="display: none;"></p>
                        </div>
					<?php } ?>
                </div> <!-- end pmpro_checkout-fields -->
				<?php if ( ! empty( $sslseal ) ) { ?>
                <div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields-rightcol pmpro_sslseal', 'pmpro_sslseal' ); ?>"><?php echo stripslashes( $sslseal ); ?></div>
            </div> <!-- end pmpro_checkout-fields-display-seal -->
		<?php } ?>
        </div> <!-- end pmpro_payment_information_fields -->
		<?php

		//don't include the default
		return false;
	}

	/**
	 * Fields shown on edit user page
	 *
	 * @since 1.8
	 */
	static function user_profile_fields( $user ) {
		global $wpdb, $current_user, $pmpro_currency_symbol;

		$cycles        = array(
			__( 'Day(s)', 'paid-memberships-pro' )   => 'Day',
			__( 'Week(s)', 'paid-memberships-pro' )  => 'Week',
			__( 'Month(s)', 'paid-memberships-pro' ) => 'Month',
			__( 'Year(s)', 'paid-memberships-pro' )  => 'Year'
		);
		$current_year  = date_i18n( "Y" );
		$current_month = date_i18n( "m" );

		//make sure the current user has privileges
		$membership_level_capability = apply_filters( "pmpro_edit_member_capability", "manage_options" );
		if ( ! current_user_can( $membership_level_capability ) ) {
			return false;
		}

		//more privelges they should have
		$show_membership_level = apply_filters( "pmpro_profile_show_membership_level", true, $user );
		if ( ! $show_membership_level ) {
			return false;
		}

		//check that user has a current subscription at Stripe
		$last_order = new MemberOrder();
		$last_order->getLastMemberOrder( $user->ID );

		//assume no sub to start
		$sub = false;

		//check that gateway is Stripe
		if ( $last_order->gateway == "stripe" && self::$is_loaded ) {
			//is there a customer?
			$sub = $last_order->Gateway->getSubscription( $last_order );
		}

		$customer_id = $user->pmpro_stripe_customerid;

		if ( empty( $sub ) ) {
			//make sure we delete stripe updates
			update_user_meta( $user->ID, "pmpro_stripe_updates", array() );

			//if the last order has a sub id, let the admin know there is no sub at Stripe
			if ( ! empty( $last_order ) && $last_order->gateway == "stripe" && ! empty( $last_order->subscription_transaction_id ) && strpos( $last_order->subscription_transaction_id, "sub_" ) !== false ) {
				?>
                <p><?php printf( __( '%1$sNote:%2$s Subscription %3$s%4$s%5$s could not be found at Stripe. It may have been deleted.', 'paid-memberships-pro' ), '<strong>', '</strong>', '<strong>', esc_attr( $last_order->subscription_transaction_id ), '</strong>' ); ?></p>
				<?php
			}
		} elseif ( true === self::$is_loaded ) {
			?>
            <h3><?php _e( "Subscription Updates", 'paid-memberships-pro' ); ?></h3>
            <p>
				<?php
				if ( empty( $_REQUEST['user_id'] ) ) {
					_e( "Subscription updates, allow you to change the member's subscription values at predefined times. Be sure to click Update Profile after making changes.", 'paid-memberships-pro' );
				} else {
					_e( "Subscription updates, allow you to change the member's subscription values at predefined times. Be sure to click Update User after making changes.", 'paid-memberships-pro' );
				}
				?>
            </p>
            <table class="form-table">
                <tr>
                    <th><label for="membership_level"><?php _e( "Update", 'paid-memberships-pro' ); ?></label></th>
                    <td id="updates_td">
						<?php
						$old_updates = $user->pmpro_stripe_updates;
						if ( is_array( $old_updates ) ) {
							$updates = array_merge(
								array(
									array(
										'template'       => true,
										'when'           => 'now',
										'date_month'     => '',
										'date_day'       => '',
										'date_year'      => '',
										'billing_amount' => '',
										'cycle_number'   => '',
										'cycle_period'   => 'Month'
									)
								),
								$old_updates
							);
						} else {
							$updates = array(
								array(
									'template'       => true,
									'when'           => 'now',
									'date_month'     => '',
									'date_day'       => '',
									'date_year'      => '',
									'billing_amount' => '',
									'cycle_number'   => '',
									'cycle_period'   => 'Month'
								)
							);
						}

						foreach ( $updates as $update ) {
							?>
                            <div class="updates_update"
							     <?php if ( ! empty( $update['template'] ) ) { ?>style="display: none;"<?php } ?>>
                                <select class="updates_when" name="updates_when[]">
                                    <option value="now" <?php selected( $update['when'], "now" ); ?>>Now</option>
                                    <option value="payment" <?php selected( $update['when'], "payment" ); ?>>After
                                        Next Payment
                                    </option>
                                    <option value="date" <?php selected( $update['when'], "date" ); ?>>On Date
                                    </option>
                                </select>
                                <span class="updates_date"
								      <?php if ( $update['when'] != "date" ) { ?>style="display: none;"<?php } ?>>
								<select name="updates_date_month[]">
									<?php
									for ( $i = 1; $i < 13; $i ++ ) {
										?>
                                        <option value="<?php echo str_pad( $i, 2, "0", STR_PAD_LEFT ); ?>"
										        <?php if ( ! empty( $update['date_month'] ) && $update['date_month'] == $i ) { ?>selected="selected"<?php } ?>>
											<?php echo date_i18n( "M", strtotime( $i . "/15/" . $current_year ) ); ?>
										</option>
										<?php
									}
									?>
								</select>
								<input name="updates_date_day[]" type="text" size="2"
                                       value="<?php if ( ! empty( $update['date_day'] ) ) {
									       echo esc_attr( $update['date_day'] );
								       } ?>"/>
								<input name="updates_date_year[]" type="text" size="4"
                                       value="<?php if ( ! empty( $update['date_year'] ) ) {
									       echo esc_attr( $update['date_year'] );
								       } ?>"/>
							</span>
                                <span class="updates_billing"
								      <?php if ( $update['when'] == "now" ) { ?>style="display: none;"<?php } ?>>
								<?php echo $pmpro_currency_symbol ?><input name="updates_billing_amount[]" type="text"
                                                                           size="10"
                                                                           value="<?php echo esc_attr( $update['billing_amount'] ); ?>"/>
								<small><?php _e( 'per', 'paid-memberships-pro' ); ?></small>
								<input name="updates_cycle_number[]" type="text" size="5"
                                       value="<?php echo esc_attr( $update['cycle_number'] ); ?>"/>
								<select name="updates_cycle_period[]">
								  <?php
								  foreach ( $cycles as $name => $value ) {
									  echo "<option value='$value'";
									  if ( ! empty( $update['cycle_period'] ) && $update['cycle_period'] == $value ) {
										  echo " selected='selected'";
									  }
									  echo ">$name</option>";
								  }
								  ?>
								</select>
							</span>
                                <span>
								<a class="updates_remove" href="javascript:void(0);">Remove</a>
							</span>
                            </div>
							<?php
						}
						?>
                        <p><a id="updates_new_update" href="javascript:void(0);">+ New Update</a></p>
                    </td>
                </tr>
            </table>
            <script>
                <!--
                jQuery(document).ready(function () {
                    //function to update dropdowns/etc based on when field
                    function updateSubscriptionUpdateFields(when) {
                        if (jQuery(when).val() == 'date')
                            jQuery(when).parent().children('.updates_date').show();
                        else
                            jQuery(when).parent().children('.updates_date').hide();

                        if (jQuery(when).val() == 'no')
                            jQuery(when).parent().children('.updates_billing').hide();
                        else
                            jQuery(when).parent().children('.updates_billing').show();
                    }

                    //and update on page load
                    jQuery('.updates_when').each(function () {
                        if (jQuery(this).parent().css('display') != 'none') updateSubscriptionUpdateFields(this);
                    });

                    //add a new update when clicking to
                    var num_updates_divs = <?php echo count( $updates );?>;
                    jQuery('#updates_new_update').click(function () {
                        //get updates
                        updates = jQuery('.updates_update').toArray();

                        //clone the first one
                        new_div = jQuery(updates[0]).clone();

                        //append
                        new_div.insertBefore('#updates_new_update');

                        //update events
                        addUpdateEvents()

                        //unhide it
                        new_div.show();
                        updateSubscriptionUpdateFields(new_div.children('.updates_when'));
                    });

                    function addUpdateEvents() {
                        //update when when changes
                        jQuery('.updates_when').change(function () {
                            updateSubscriptionUpdateFields(this);
                        });

                        //remove updates when clicking
                        jQuery('.updates_remove').click(function () {
                            jQuery(this).parent().parent().remove();
                        });
                    }

                    addUpdateEvents();
                });
                -->
            </script>
			<?php
		}
	}

	/**
	 * Process fields from the edit user page
	 *
	 * @since 1.8
	 */
	static function user_profile_fields_save( $user_id ) {
		global $wpdb;

		//check capabilities
		$membership_level_capability = apply_filters( "pmpro_edit_member_capability", "manage_options" );
		if ( ! current_user_can( $membership_level_capability ) ) {
			return false;
		}

		//make sure some value was passed
		if ( ! isset( $_POST['updates_when'] ) || ! is_array( $_POST['updates_when'] ) ) {
			return;
		}

		//vars
		$updates             = array();
		$next_on_date_update = "";

		//build array of updates (we skip the first because it's the template field for the JavaScript
		for ( $i = 1; $i < count( $_POST['updates_when'] ); $i ++ ) {
			$update = array();

			//all updates have these values
			$update['when']           = pmpro_sanitize_with_safelist( $_POST['updates_when'][ $i ], array(
				'now',
				'payment',
				'date'
			) );
			$update['billing_amount'] = sanitize_text_field( $_POST['updates_billing_amount'][ $i ] );
			$update['cycle_number']   = intval( $_POST['updates_cycle_number'][ $i ] );
			$update['cycle_period']   = sanitize_text_field( $_POST['updates_cycle_period'][ $i ] );

			//these values only for on date updates
			if ( $_POST['updates_when'][ $i ] == "date" ) {
				$update['date_month'] = str_pad( intval( $_POST['updates_date_month'][ $i ] ), 2, "0", STR_PAD_LEFT );
				$update['date_day']   = str_pad( intval( $_POST['updates_date_day'][ $i ] ), 2, "0", STR_PAD_LEFT );
				$update['date_year']  = intval( $_POST['updates_date_year'][ $i ] );
			}

			//make sure the update is valid
			if ( empty( $update['cycle_number'] ) ) {
				continue;
			}

			//if when is now, update the subscription
			if ( $update['when'] == "now" ) {
				self::updateSubscription( $update, $user_id );

				continue;
			} elseif ( $update['when'] == 'date' ) {
				if ( ! empty( $next_on_date_update ) ) {
					$next_on_date_update = min( $next_on_date_update, $update['date_year'] . "-" . $update['date_month'] . "-" . $update['date_day'] );
				} else {
					$next_on_date_update = $update['date_year'] . "-" . $update['date_month'] . "-" . $update['date_day'];
				}
			}

			//add to array
			$updates[] = $update;
		}

		//save in user meta
		update_user_meta( $user_id, "pmpro_stripe_updates", $updates );

		//save date of next on-date update to make it easier to query for these in cron job
		update_user_meta( $user_id, "pmpro_stripe_next_on_date_update", $next_on_date_update );
	}

	/**
	 * Cron activation for subscription updates.
	 *
	 * @since 1.8
	 */
	static function pmpro_activation() {
		pmpro_maybe_schedule_event( time(), 'daily', 'pmpro_cron_stripe_subscription_updates' );
	}

	/**
	 * Cron deactivation for subscription updates.
	 *
	 * @since 1.8
	 */
	static function pmpro_deactivation() {
		wp_clear_scheduled_hook( 'pmpro_cron_stripe_subscription_updates' );
	}

	/**
	 * Cron job for subscription updates.
	 *
	 * @since 1.8
	 */
	static function pmpro_cron_stripe_subscription_updates() {
		global $wpdb;

		//get all updates for today (or before today)
		$sqlQuery = "SELECT *
					 FROM $wpdb->usermeta
					 WHERE meta_key = 'pmpro_stripe_next_on_date_update'
						AND meta_value IS NOT NULL
						AND meta_value <> ''
						AND meta_value < '" . date_i18n( "Y-m-d", strtotime( "+1 day", current_time( 'timestamp' ) ) ) . "'";
		$updates  = $wpdb->get_results( $sqlQuery );

		if ( ! empty( $updates ) ) {
			//loop through
			foreach ( $updates as $update ) {
				//pull values from update
				$user_id = $update->user_id;

				$user = get_userdata( $user_id );

				//if user is missing, delete the update info and continue
				if ( empty( $user ) || empty( $user->ID ) ) {
					delete_user_meta( $user_id, "pmpro_stripe_updates" );
					delete_user_meta( $user_id, "pmpro_stripe_next_on_date_update" );

					continue;
				}

				$user_updates        = $user->pmpro_stripe_updates;
				$next_on_date_update = "";

				//loop through updates looking for updates happening today or earlier
				if ( ! empty( $user_updates ) ) {
					foreach ( $user_updates as $key => $ud ) {
						if ( $ud['when'] == 'date' &&
						     $ud['date_year'] . "-" . $ud['date_month'] . "-" . $ud['date_day'] <= date_i18n( "Y-m-d", current_time( 'timestamp' ) )
						) {
							self::updateSubscription( $ud, $user_id );

							//remove update from list
							unset( $user_updates[ $key ] );
						} elseif ( $ud['when'] == 'date' ) {
							//this is an on date update for the future, update the next on date update
							if ( ! empty( $next_on_date_update ) ) {
								$next_on_date_update = min( $next_on_date_update, $ud['date_year'] . "-" . $ud['date_month'] . "-" . $ud['date_day'] );
							} else {
								$next_on_date_update = $ud['date_year'] . "-" . $ud['date_month'] . "-" . $ud['date_day'];
							}
						}
					}
				}

				//save updates in case we removed some
				update_user_meta( $user_id, "pmpro_stripe_updates", $user_updates );

				//save date of next on-date update to make it easier to query for these in cron job
				update_user_meta( $user_id, "pmpro_stripe_next_on_date_update", $next_on_date_update );
			}
		}
	}

	/**
	 * Before processing a checkout, check for pending invoices we want to clean up.
	 * This prevents double billing issues in cases where Stripe has pending invoices
	 * because of an expired credit card/etc and a user checks out to renew their subscription
	 * instead of updating their billing information via the billing info page.
	 */
	static function pmpro_checkout_before_processing() {
		global $wpdb, $current_user;

		// we're only worried about cases where the user is logged in
		if ( ! is_user_logged_in() ) {
			return;
		}

		// make sure we're checking out with Stripe
		$current_gateway = pmpro_getGateway();
		if ( $current_gateway != 'stripe' ) {
			return;
		}

		//check the $pmpro_cancel_previous_subscriptions filter
		//this is used in add ons like Gift Memberships to stop PMPro from cancelling old memberships
		$pmpro_cancel_previous_subscriptions = true;
		$pmpro_cancel_previous_subscriptions = apply_filters( 'pmpro_cancel_previous_subscriptions', $pmpro_cancel_previous_subscriptions );
		if ( ! $pmpro_cancel_previous_subscriptions ) {
			return;
		}

		//get user and membership level
		$membership_level = pmpro_getMembershipLevelForUser( $current_user->ID );

		//no level, then probably no subscription at Stripe anymore
		if ( empty( $membership_level ) ) {
			return;
		}

		/**
		 * Filter which levels to cancel at the gateway.
		 * MMPU will set this to all levels that are going to be cancelled during this checkout.
		 * Others may want to display this by add_filter('pmpro_stripe_levels_to_cancel_before_checkout', __return_false);
		 */
		$levels_to_cancel = apply_filters( 'pmpro_stripe_levels_to_cancel_before_checkout', array( $membership_level->id ), $current_user );

		foreach ( $levels_to_cancel as $level_to_cancel ) {
			//get the last order for this user/level
			$last_order = new MemberOrder();
			$last_order->getLastMemberOrder( $current_user->ID, 'success', $level_to_cancel, 'stripe' );

			//so let's cancel the user's susbcription
			if ( ! empty( $last_order ) && ! empty( $last_order->subscription_transaction_id ) ) {
				$subscription = $last_order->Gateway->getSubscription( $last_order );
				if ( ! empty( $subscription ) ) {
					$last_order->Gateway->cancelSubscriptionAtGateway( $subscription, true );

					//Stripe was probably going to cancel this subscription 7 days past the payment failure (maybe just one hour, use a filter for sure)
					$memberships_users_row = $wpdb->get_row( "SELECT * FROM $wpdb->pmpro_memberships_users WHERE user_id = '" . $current_user->ID . "' AND membership_id = '" . $level_to_cancel . "' AND status = 'active' LIMIT 1" );

					if ( ! empty( $memberships_users_row ) && ( empty( $memberships_users_row->enddate ) || $memberships_users_row->enddate == '0000-00-00 00:00:00' ) ) {
						/**
						 * Filter graced period days when canceling existing subscriptions at checkout.
						 *
						 * @param int $days Grace period defaults to 3 days
						 * @param object $membership Membership row from pmpro_memberships_users including membership_id, user_id, and enddate
						 *
						 * @since 1.9.4
						 *
						 */
						$days_grace  = apply_filters( 'pmpro_stripe_days_grace_when_canceling_existing_subscriptions_at_checkout', 3, $memberships_users_row );
						$new_enddate = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) + 3600 * 24 * $days_grace );
						$wpdb->update( $wpdb->pmpro_memberships_users, array( 'enddate' => $new_enddate ), array(
							'user_id'       => $current_user->ID,
							'membership_id' => $level_to_cancel,
							'status'        => 'active'
						), array( '%s' ), array( '%d', '%d', '%s' ) );
					}
				}
			}
		}
	}

	/**
	 * Process checkout and decide if a charge and or subscribe is needed
	 * Updated in v2.1 to work with Stripe v3 payment intents.
	 * @since 1.4
	 */
	function process( &$order ) {
		$steps = array(
			'set_customer',
			'set_payment_method',
			'attach_payment_method_to_customer',
			'process_charges',
			'process_subscriptions',
		);

		foreach ( $steps as $key => $step ) {
			do_action( "pmpro_process_order_before_{$step}", $order );
			$this->$step( $order );
			do_action( "pmpro_process_order_after_{$step}", $order );
			if ( ! empty( $order->error ) ) {
				return false;
			}
		}

		$this->clean_up( $order );
		$order->status = 'success';
		$order->saveOrder();

		return true;
	}

	/**
	 * Make a one-time charge with Stripe
	 *
	 * @since 1.4
	 */
	function charge( &$order ) {
		global $pmpro_currency, $pmpro_currencies;
		$currency_unit_multiplier = 100; //ie 100 cents per USD

		//account for zero-decimal currencies like the Japanese Yen
		if ( is_array( $pmpro_currencies[ $pmpro_currency ] ) && isset( $pmpro_currencies[ $pmpro_currency ]['decimals'] ) && $pmpro_currencies[ $pmpro_currency ]['decimals'] == 0 ) {
			$currency_unit_multiplier = 1;
		}

		//create a code for the order
		if ( empty( $order->code ) ) {
			$order->code = $order->getRandomCode();
		}

		//what amount to charge?
		$amount = $order->InitialPayment;

		//tax
		$order->subtotal = $amount;
		$tax             = $order->getTax( true );
		$amount          = pmpro_round_price( (float) $order->subtotal + (float) $tax );

		//create a customer
		$result = $this->getCustomer( $order );

		if ( empty( $result ) ) {
			//failed to create customer
			return false;
		}

		//charge
		try {
			$params = array(
					"amount"      => $amount * $currency_unit_multiplier, # amount in cents, again
					"currency"    => strtolower( $pmpro_currency ),
					"customer"    => $this->customer->id,
					"description" => apply_filters( 'pmpro_stripe_order_description', "Order #" . $order->code . ", " . trim( $order->FirstName . " " . $order->LastName ) . " (" . $order->Email . ")", $order )
				);
			$params   = self::add_application_fee_amount( $params  );
			/**
			 * Filter params used to create the Stripe charge.
			 *
			 * @since 2.4.4
			 *
		 	 * @param array  $params 	Array of params sent to Stripe.
			 * @param object $order		Order object for this checkout.
			 */
			$params = apply_filters( 'pmpro_stripe_charge_params', $params, $order );
			$response = Stripe_Charge::create( $params );
		} catch ( \Throwable $e ) {
			//$order->status = "error";
			$order->errorcode  = true;
			$order->error      = "Error: " . $e->getMessage();
			$order->shorterror = $order->error;

			return false;
		} catch ( \Exception $e ) {
			//$order->status = "error";
			$order->errorcode  = true;
			$order->error      = "Error: " . $e->getMessage();
			$order->shorterror = $order->error;

			return false;
		}

		if ( empty( $response["failure_message"] ) ) {
			//successful charge
			$order->payment_transaction_id = $response["id"];
			$order->updateStatus( "success" );
			$order->saveOrder();

			return true;
		} else {
			//$order->status = "error";
			$order->errorcode  = true;
			$order->error      = $response['failure_message'];
			$order->shorterror = $response['failure_message'];

			return false;
		}
	}

	/**
	 * Get a Stripe customer object.
	 *
	 * If $this->customer is set, it returns it.
	 * It first checks if the order has a subscription_transaction_id. If so, that's the customer id.
	 * If not, it checks for a user_id on the order and searches for a customer id in the user meta.
	 * If a customer id is found, it checks for a customer through the Stripe API.
	 * If a customer is found and there is a stripeToken on the order passed, it will update the customer.
	 * If no customer is found and there is a stripeToken on the order passed, it will create a customer.
	 *
	 * @return Stripe_Customer|false
	 * @since 1.4
	 */
	function getCustomer( &$order = false, $force = false ) {
		global $current_user;

		//already have it?
		if ( ! empty( $this->customer ) && ! $force ) {
			return $this->customer;
		}

		// Is it already on the order?
		if ( ! empty( $order->customer_id ) ) {
			$customer_id = $order->customer_id;
		}

		//figure out user_id and user
		if ( ! empty( $order->user_id ) ) {
			$user_id = $order->user_id;
		}

		//if no id passed, check the current user
		if ( empty( $user_id ) && ! empty( $current_user->ID ) ) {
			$user_id = $current_user->ID;
		}

		if ( ! empty( $user_id ) ) {
			$user = get_userdata( $user_id );
		} else {
			$user = null;
		}

		//transaction id?
		if ( ! empty( $order->subscription_transaction_id ) && strpos( $order->subscription_transaction_id, "cus_" ) !== false ) {
			$customer_id = $order->subscription_transaction_id;
		} else {
			//try based on user id
			if ( ! empty( $user_id ) ) {
				$customer_id = get_user_meta( $user_id, "pmpro_stripe_customerid", true );
			}

			//look up by transaction id
			if ( empty( $customer_id ) && ! empty( $user_id ) ) {//user id from this order or the user's last stripe order
				if ( ! empty( $order->payment_transaction_id ) ) {
					$payment_transaction_id = $order->payment_transaction_id;
				} else {
					//find the user's last stripe order
					$last_order = new MemberOrder();
					$last_order->getLastMemberOrder( $user_id, array(
						'success',
						'cancelled'
					), null, 'stripe', $order->Gateway->gateway_environment );
					if ( ! empty( $last_order->payment_transaction_id ) ) {
						$payment_transaction_id = $last_order->payment_transaction_id;
					}
				}

				//we have a transaction id to look up
				if ( ! empty( $payment_transaction_id ) ) {
					if ( strpos( $payment_transaction_id, "ch_" ) !== false ) {
						//charge, look it up
						try {
							$charge = Stripe_Charge::retrieve( $payment_transaction_id );
						} catch ( \Throwable $e ) {
							$order->error = sprintf( __( 'Error: %s', 'paid-memberships-pro' ), $e->getMessage() );

							return false;
						} catch ( \Exception $e ) {
							$order->error = sprintf( __( 'Error: %s', 'paid-memberships-pro' ), $e->getMessage() );

							return false;
						}

						if ( ! empty( $charge ) && ! empty( $charge->customer ) ) {
							$customer_id = $charge->customer;
						}
					} else if ( strpos( $payment_transaction_id, "in_" ) !== false ) {
						//invoice look it up
						try {
							$invoice = Stripe_Invoice::retrieve( $payment_transaction_id );
						} catch ( \Throwable $e ) {
							$order->error = sprintf( __( 'Error: %s', 'paid-memberships-pro' ), $e->getMessage() );

							return false;
						} catch ( \Exception $e ) {
							$order->error = sprintf( __( 'Error: %s', 'paid-memberships-pro' ), $e->getMessage() );

							return false;
						}

						if ( ! empty( $invoice ) && ! empty( $invoice->customer ) ) {
							$customer_id = $invoice->customer;
						}
					}
				}

				//if we found it, save to user meta for future reference
				if ( ! empty( $customer_id ) ) {
					update_user_meta( $user_id, "pmpro_stripe_customerid", $customer_id );
				}
			}
		}

		//get name and email values from order in case we update
		if ( ! empty( $order->FirstName ) && ! empty( $order->LastName ) ) {
			$name = trim( $order->FirstName . " " . $order->LastName );
		} elseif ( ! empty( $order->FirstName ) ) {
			$name = $order->FirstName;
		} elseif ( ! empty( $order->LastName ) ) {
			$name = $order->LastName;
		}

		if ( empty( $name ) && ! empty( $user->ID ) ) {
			$name = trim( $user->first_name . " " . $user->last_name );

			//still empty?
			if ( empty( $name ) ) {
				$name = $user->user_login;
			}
		} elseif ( empty( $name ) ) {
			$name = "No Name";
		}

		if ( ! empty( $order->Email ) ) {
			$email = $order->Email;
		} else {
			$email = "";
		}

		if ( empty( $email ) && ! empty( $user->ID ) && ! empty( $user->user_email ) ) {
			$email = $user->user_email;
		} elseif ( empty( $email ) ) {
			$email = "No Email";
		}

		//check for an existing stripe customer
		if ( ! empty( $customer_id ) ) {
			try {
				$this->customer = Stripe_Customer::retrieve( $customer_id );
				// Update description.
				if ( ! empty( $order->payment_method_id ) ) {
					$this->customer->description = $name . " (" . $email . ")";
					$this->customer->email       = $email;
					$this->customer->save();
				}

				if ( ! empty( $user_id ) ) {
					//user logged in/etc
					update_user_meta( $user_id, "pmpro_stripe_customerid", $this->customer->id );
				} else {
					//user not registered yet, queue it up
					global $pmpro_stripe_customer_id;
					$pmpro_stripe_customer_id = $this->customer->id;
					if ( ! function_exists( 'pmpro_user_register_stripe_customerid' ) ) {
						function pmpro_user_register_stripe_customerid( $user_id ) {
							global $pmpro_stripe_customer_id;
							update_user_meta( $user_id, "pmpro_stripe_customerid", $pmpro_stripe_customer_id );
						}

						add_action( "user_register", "pmpro_user_register_stripe_customerid" );
					}
				}

				return $this->customer;
			} catch ( \Throwable $e ) {
				//assume no customer found
			} catch ( \Exception $e ) {
				//assume no customer found
			}
		}

		//no customer id, create one
		if ( ! empty( $order->payment_method_id ) ) {
			try {
				$this->customer = Stripe_Customer::create( array(
					"description" => $name . " (" . $email . ")",
					"email"       => $order->Email,
				) );
			} catch ( \Stripe\Error $e ) {
				$order->error      = __( "Error creating customer record with Stripe:", 'paid-memberships-pro' ) . " " . $e->getMessage();
				$order->shorterror = $order->error;

				return false;
			} catch ( \Throwable $e ) {
				$order->error      = __( "Error creating customer record with Stripe:", 'paid-memberships-pro' ) . " " . $e->getMessage();
				$order->shorterror = $order->error;

				return false;
			} catch ( \Exception $e ) {
				$order->error      = __( "Error creating customer record with Stripe:", 'paid-memberships-pro' ) . " " . $e->getMessage();
				$order->shorterror = $order->error;

				return false;
			}

			if ( ! empty( $user_id ) ) {
				//user logged in/etc
				update_user_meta( $user_id, "pmpro_stripe_customerid", $this->customer->id );
			} else {
				//user not registered yet, queue it up
				global $pmpro_stripe_customer_id;
				$pmpro_stripe_customer_id = $this->customer->id;
				if ( ! function_exists( 'pmpro_user_register_stripe_customerid' ) ) {
					function pmpro_user_register_stripe_customerid( $user_id ) {
						global $pmpro_stripe_customer_id;
						update_user_meta( $user_id, "pmpro_stripe_customerid", $pmpro_stripe_customer_id );
					}

					add_action( "user_register", "pmpro_user_register_stripe_customerid" );
				}
			}

			return apply_filters( 'pmpro_stripe_create_customer', $this->customer );
		}

		return false;
	}

	/**
	 * Get a Stripe subscription from a PMPro order
	 *
	 * @since 1.8
	 */
	function getSubscription( &$order ) {
		global $wpdb;

		//no order?
		if ( empty( $order ) || empty( $order->code ) ) {
			return false;
		}

		$result = $this->getCustomer( $order, true );    //force so we don't get a cached sub for someone else

		//no customer?
		if ( empty( $result ) ) {
			return false;
		}

		//no subscriptions?
		if ( empty( $this->customer->subscriptions ) ) {
			return false;
		}

		//is there a subscription transaction id pointing to a sub?
		if ( ! empty( $order->subscription_transaction_id ) && strpos( $order->subscription_transaction_id, "sub_" ) !== false ) {
			try {
				$sub = $this->customer->subscriptions->retrieve( $order->subscription_transaction_id );
			} catch ( \Throwable $e ) {
				$order->error      = __( "Error getting subscription with Stripe:", 'paid-memberships-pro' ) . $e->getMessage();
				$order->shorterror = $order->error;

				return false;
			} catch ( \Exception $e ) {
				$order->error      = __( "Error getting subscription with Stripe:", 'paid-memberships-pro' ) . $e->getMessage();
				$order->shorterror = $order->error;

				return false;
			}

			return $sub;
		}

		//find subscription based on customer id and order/plan id
		$subscriptions = $this->customer->subscriptions->all();

		//no subscriptions
		if ( empty( $subscriptions ) || empty( $subscriptions->data ) ) {
			return false;
		}

		//we really want to test against the order codes of all orders with the same subscription_transaction_id (customer id)
		$codes = $wpdb->get_col( "SELECT code FROM $wpdb->pmpro_membership_orders WHERE user_id = '" . esc_sql( $order->user_id ) . "' AND subscription_transaction_id = '" . esc_sql( $order->subscription_transaction_id ) . "' AND status NOT IN('refunded', 'review', 'token', 'error')" );

		//find the one for this order
		foreach ( $subscriptions->data as $sub ) {
			if ( in_array( $sub->plan->id, $codes ) ) {
				return $sub;
			}
		}

		//didn't find anything yet
		return false;
	}

	/**
	 * Get subscription status from the Gateway.
	 *
	 * @since 2.3
	 */
	function getSubscriptionStatus( &$order ) {
		$subscription = $this->getSubscription( $order );
		
		if ( ! empty( $subscription ) ) {
			return $subscription->status;
		} else {
			return false;
		}
	}

	/**
	 * Create a new subscription with Stripe.
	 *
	 * This function is not run as a part of the PMPro Checkout Process.
	 * See method create_setup_intent().
	 *
	 * @since 1.4
	 */
	function subscribe( &$order, $checkout = true ) {
		global $pmpro_currency, $pmpro_currencies;

		$currency_unit_multiplier = 100; //ie 100 cents per USD

		//account for zero-decimal currencies like the Japanese Yen
		if ( is_array( $pmpro_currencies[ $pmpro_currency ] ) && isset( $pmpro_currencies[ $pmpro_currency ]['decimals'] ) && $pmpro_currencies[ $pmpro_currency ]['decimals'] == 0 ) {
			$currency_unit_multiplier = 1;
		}

		//create a code for the order
		if ( empty( $order->code ) ) {
			$order->code = $order->getRandomCode();
		}

		//filter order before subscription. use with care.
		$order = apply_filters( "pmpro_subscribe_order", $order, $this );

		//figure out the user
		if ( ! empty( $order->user_id ) ) {
			$user_id = $order->user_id;
		} else {
			global $current_user;
			$user_id = $current_user->ID;
		}

		//set up customer

		$result = $this->getCustomer( $order );
		if ( empty( $result ) ) {
			return false;    //error retrieving customer
		}

		// set subscription id to custom id

		$order->subscription_transaction_id = $this->customer['id'];    //transaction id is the customer id, we save it in user meta later too

		//figure out the amounts
		$amount     = $order->PaymentAmount;
		$amount_tax = $order->getTaxForPrice( $amount );
		$amount     = pmpro_round_price( (float) $amount + (float) $amount_tax );

		/*
            There are two parts to the trial. Part 1 is simply the delay until the first payment
            since we are doing the first payment as a separate transaction.
            The second part is the actual "trial" set by the admin.

            Stripe only supports Year or Month for billing periods, but we account for Days and Weeks just in case.
        */
		//figure out the trial length (first payment handled by initial charge)
		if ( $order->BillingPeriod == "Year" ) {
			$trial_period_days = $order->BillingFrequency * 365;    //annual
		} elseif ( $order->BillingPeriod == "Day" ) {
			$trial_period_days = $order->BillingFrequency * 1;        //daily
		} elseif ( $order->BillingPeriod == "Week" ) {
			$trial_period_days = $order->BillingFrequency * 7;        //weekly
		} else {
			$trial_period_days = $order->BillingFrequency * 30;    //assume monthly
		}

		//convert to a profile start date
		$order->ProfileStartDate = date_i18n( "Y-m-d\TH:i:s", strtotime( "+ " . $trial_period_days . " Day", current_time( "timestamp" ) ) );

		//filter the start date
		$order->ProfileStartDate = apply_filters( "pmpro_profile_start_date", $order->ProfileStartDate, $order );

		//convert back to days
		$trial_period_days = ceil( abs( strtotime( date_i18n( "Y-m-d" ), current_time( "timestamp" ) ) - strtotime( $order->ProfileStartDate, current_time( "timestamp" ) ) ) / 86400 );

		//for free trials, just push the start date of the subscription back
		if ( ! empty( $order->TrialBillingCycles ) && $order->TrialAmount == 0 ) {
			$trialOccurrences = (int) $order->TrialBillingCycles;
			if ( $order->BillingPeriod == "Year" ) {
				$trial_period_days = $trial_period_days + ( 365 * $order->BillingFrequency * $trialOccurrences );    //annual
			} elseif ( $order->BillingPeriod == "Day" ) {
				$trial_period_days = $trial_period_days + ( 1 * $order->BillingFrequency * $trialOccurrences );        //daily
			} elseif ( $order->BillingPeriod == "Week" ) {
				$trial_period_days = $trial_period_days + ( 7 * $order->BillingFrequency * $trialOccurrences );    //weekly
			} else {
				$trial_period_days = $trial_period_days + ( 30 * $order->BillingFrequency * $trialOccurrences );    //assume monthly
			}
		} elseif ( ! empty( $order->TrialBillingCycles ) ) {
			/*
                Let's set the subscription to the trial and give the user an "update" to change the sub later to full price (since v2.0)

                This will force TrialBillingCycles > 1 to act as if they were 1
            */
			$new_user_updates   = array();
			$new_user_updates[] = array(
				'when'           => 'payment',
				'billing_amount' => $order->PaymentAmount,
				'cycle_period'   => $order->BillingPeriod,
				'cycle_number'   => $order->BillingFrequency
			);

			//now amount to equal the trial #s
			$amount     = $order->TrialAmount;
			$amount_tax = $order->getTaxForPrice( $amount );
			$amount     = pmpro_round_price( (float) $amount + (float) $amount_tax );
		}

		// Save $trial_period_days to order for now too.
		$order->TrialPeriodDays = $trial_period_days;

		//create a plan
		try {
			$plan = array(
				"amount"                 => $amount * $currency_unit_multiplier,
				"interval_count"         => $order->BillingFrequency,
				"interval"               => strtolower( $order->BillingPeriod ),
				"trial_period_days"      => $trial_period_days,
				'product'                => array( 'name' => $order->membership_name . " for order " . $order->code ),
				"currency"               => strtolower( $pmpro_currency ),
				"id"                     => $order->code
			);
			$plan = self::add_application_fee_amount( $plan );
			$plan = Stripe_Plan::create( apply_filters( 'pmpro_stripe_create_plan_array', $plan ) );
		} catch ( \Throwable $e ) {
			$order->error      = __( "Error creating plan with Stripe:", 'paid-memberships-pro' ) . $e->getMessage();
			$order->shorterror = $order->error;

			return false;
		} catch ( \Exception $e ) {
			$order->error      = __( "Error creating plan with Stripe:", 'paid-memberships-pro' ) . $e->getMessage();
			$order->shorterror = $order->error;

			return false;
		}

		// before subscribing, let's clear out the updates so we don't trigger any during sub
		if ( ! empty( $user_id ) ) {
			$old_user_updates = get_user_meta( $user_id, "pmpro_stripe_updates", true );
			update_user_meta( $user_id, "pmpro_stripe_updates", array() );
		}


		if ( empty( $order->subscription_transaction_id ) && ! empty( $this->customer['id'] ) ) {
			$order->subscription_transaction_id = $this->customer['id'];
		}

		// subscribe to the plan
		try {
			$subscription = array( "plan" => $order->code );
			$result       = $this->create_subscription( $order );
		} catch ( \Throwable $e ) {
			//try to delete the plan
			$plan->delete();

			//give the user any old updates back
			if ( ! empty( $user_id ) ) {
				update_user_meta( $user_id, "pmpro_stripe_updates", $old_user_updates );
			}

			//return error
			$order->error      = __( "Error subscribing customer to plan with Stripe:", 'paid-memberships-pro' ) . $e->getMessage();
			$order->shorterror = $order->error;

			return false;
		} catch ( \Exception $e ) {
			//try to delete the plan
			$plan->delete();

			//give the user any old updates back
			if ( ! empty( $user_id ) ) {
				update_user_meta( $user_id, "pmpro_stripe_updates", $old_user_updates );
			}

			//return error
			$order->error      = __( "Error subscribing customer to plan with Stripe:", 'paid-memberships-pro' ) . $e->getMessage();
			$order->shorterror = $order->error;

			return false;
		}

		// delete the plan
		$plan = Stripe_Plan::retrieve( $order->code );
		$plan->delete();

		//if we got this far, we're all good
		$order->status                      = "success";
		$order->subscription_transaction_id = $result['id'];

		//save new updates if this is at checkout
		if ( $checkout ) {
			//empty out updates unless set above
			if ( empty( $new_user_updates ) ) {
				$new_user_updates = array();
			}

			//update user meta
			if ( ! empty( $user_id ) ) {
				update_user_meta( $user_id, "pmpro_stripe_updates", $new_user_updates );
			} else {
				//need to remember the user updates to save later
				global $pmpro_stripe_updates;
				$pmpro_stripe_updates = $new_user_updates;
				function pmpro_user_register_stripe_updates( $user_id ) {
					global $pmpro_stripe_updates;
					update_user_meta( $user_id, "pmpro_stripe_updates", $pmpro_stripe_updates );
				}

				add_action( "user_register", "pmpro_user_register_stripe_updates" );
			}
		} else {
			//give them their old updates back
			update_user_meta( $user_id, "pmpro_stripe_updates", $old_user_updates );
		}

		return true;
	}

	/**
	 * Helper method to save the subscription ID to make sure the membership doesn't get cancelled by the webhook
	 */
	static function ignoreCancelWebhookForThisSubscription( $subscription_id, $user_id = null ) {
		if ( empty( $user_id ) ) {
			global $current_user;
			$user_id = $current_user->ID;
		}

		$preserve = get_user_meta( $user_id, 'pmpro_stripe_dont_cancel', true );

		// No previous values found, init the array
		if ( empty( $preserve ) ) {
			$preserve = array();
		}

		// Store or update the subscription ID timestamp (for cleanup)
		$preserve[ $subscription_id ] = current_time( 'timestamp' );

		update_user_meta( $user_id, 'pmpro_stripe_dont_cancel', $preserve );
	}

	/**
	 * Helper method to process a Stripe subscription update
	 */
	static function updateSubscription( $update, $user_id ) {
		global $wpdb;

		//get level for user
		$user_level = pmpro_getMembershipLevelForUser( $user_id );

		//get current plan at Stripe to get payment date
		$last_order = new MemberOrder();
		$last_order->getLastMemberOrder( $user_id );
		$last_order->setGateway( 'stripe' );
		$last_order->Gateway->getCustomer( $last_order );

		$subscription = $last_order->Gateway->getSubscription( $last_order );

		if ( ! empty( $subscription ) ) {
			$end_timestamp = $subscription->current_period_end;

			//cancel the old subscription
			if ( ! $last_order->Gateway->cancelSubscriptionAtGateway( $subscription, true ) ) {
				//throw error and halt save
				if ( ! function_exists( 'pmpro_stripe_user_profile_fields_save_error' ) ) {
					//throw error and halt save
					function pmpro_stripe_user_profile_fields_save_error( $errors, $update, $user ) {
						$errors->add( 'pmpro_stripe_updates', __( 'Could not cancel the old subscription. Updates have not been processed.', 'paid-memberships-pro' ) );
					}

					add_filter( 'user_profile_update_errors', 'pmpro_stripe_user_profile_fields_save_error', 10, 3 );
				}

				//stop processing updates
				return;
			}
		}

		//if we didn't get an end date, let's set one one cycle out
		if ( empty( $end_timestamp ) ) {
			$end_timestamp = strtotime( "+" . $update['cycle_number'] . " " . $update['cycle_period'], current_time( 'timestamp' ) );
		}

		//build order object
		$update_order = new MemberOrder();
		$update_order->setGateway( 'stripe' );
		$update_order->code             = $update_order->getRandomCode();
		$update_order->user_id          = $user_id;
		$update_order->membership_id    = $user_level->id;
		$update_order->membership_name  = $user_level->name;
		$update_order->InitialPayment   = 0;
		$update_order->PaymentAmount    = $update['billing_amount'];
		$update_order->ProfileStartDate = date_i18n( "Y-m-d", $end_timestamp );
		$update_order->BillingPeriod    = $update['cycle_period'];
		$update_order->BillingFrequency = $update['cycle_number'];
		$update_order->getMembershipLevel();

		//need filter to reset ProfileStartDate
		$profile_start_date = $update_order->ProfileStartDate;
		add_filter( 'pmpro_profile_start_date', function ( $startdate, $order ) use ( $profile_start_date ) {
			return "{$profile_start_date}T0:0:0";
		}, 10, 2 );

		//update subscription
		$update_order->Gateway->set_customer( $update_order, true );
		$update_order->Gateway->process_subscriptions( $update_order );

		//update membership
		$sqlQuery = "UPDATE $wpdb->pmpro_memberships_users
						SET billing_amount = '" . esc_sql( $update['billing_amount'] ) . "',
							cycle_number = '" . esc_sql( $update['cycle_number'] ) . "',
							cycle_period = '" . esc_sql( $update['cycle_period'] ) . "',
							trial_amount = '',
							trial_limit = ''
						WHERE user_id = '" . esc_sql( $user_id ) . "'
							AND membership_id = '" . esc_sql( $last_order->membership_id ) . "'
							AND status = 'active'
						LIMIT 1";

		$wpdb->query( $sqlQuery );

		//save order so we know which plan to look for at stripe (order code = plan id)
		$update_order->Gateway->clean_up( $update_order );
		$update_order->status = "success";
		$update_order->saveOrder();
	}

	/**
	 * Helper method to update the customer info via getCustomer
	 *
	 * @since 1.4
	 */
	function update( &$order ) {

		$steps = array(
			'set_customer',
			'set_payment_method',
			'attach_payment_method_to_customer',
			'update_payment_method_for_subscriptions',
		);

		foreach ( $steps as $key => $step ) {
			do_action( "pmpro_update_billing_before_{$step}", $order );
			$this->$step( $order );
			do_action( "pmpro_update_billing_after_{$step}", $order );
			if ( ! empty( $order->error ) ) {
				return false;
			}
		}

		return true;

	}
	
	/**
	 * Update the payment method for a subscription.
	 */
	function update_payment_method_for_subscriptions( &$order ) {
		// get customer
		$this->getCustomer( $order );
		
		if ( empty( $this->customer ) ) {
			return false;
		}
		
		// get all subscriptions
		if ( ! empty( $this->customer->subscriptions ) ) {
			$subscriptions = $this->customer->subscriptions->all();
		}
		
		foreach( $subscriptions as $subscription ) {
			// check if cancelled or expired
			if ( in_array( $subscription->status, array( 'canceled', 'incomplete', 'incomplete_expired' ) ) ) {
				continue;
			}
			
			// check if we have a related order for it
			$one_order = new MemberOrder();
			$one_order->getLastMemberOrderBySubscriptionTransactionID( $subscription->id );
			if ( empty( $one_order ) || empty( $one_order->id ) ) {
				continue;
			}
			
			// update the payment method
			$subscription->default_payment_method = $this->customer->invoice_settings->default_payment_method;
			$subscription->save();
		}
	}

	/**
	 * Cancel a subscription at Stripe
	 *
	 * @since 1.4
	 */
	function cancel( &$order, $update_status = true ) {
		global $pmpro_stripe_event;

		//no matter what happens below, we're going to cancel the order in our system
		if ( $update_status ) {
			$order->updateStatus( "cancelled" );
		}

		//require a subscription id
		if ( empty( $order->subscription_transaction_id ) ) {
			return false;
		}

		//find the customer
		$result = $this->getCustomer( $order );

		if ( ! empty( $result ) ) {
			//find subscription with this order code
			$subscription = $this->getSubscription( $order );

			if ( ! empty( $subscription )
			     && ( empty( $pmpro_stripe_event ) || empty( $pmpro_stripe_event->type ) || $pmpro_stripe_event->type != 'customer.subscription.deleted' ) ) {
				if ( $this->cancelSubscriptionAtGateway( $subscription ) ) {
					//we're okay, going to return true later
				} else {
					$order->error      = __( "Could not cancel old subscription.", 'paid-memberships-pro' );
					$order->shorterror = $order->error;

					return false;
				}
			}

			/*
                Clear updates for this user. (But not if checking out, we would have already done that.)
            */
			if ( empty( $_REQUEST['submit-checkout'] ) ) {
				update_user_meta( $order->user_id, "pmpro_stripe_updates", array() );
			}

			return true;
		} else {
			$order->error      = __( "Could not find the customer.", 'paid-memberships-pro' );
			$order->shorterror = $order->error;

			return false;    //no customer found
		}
	}

	/**
	 * Helper method to cancel a subscription at Stripe and also clear up any upaid invoices.
	 *
	 * @since 1.8
	 */
	function cancelSubscriptionAtGateway( $subscription, $preserve_local_membership = false ) {
		// Check if a valid sub.
		if ( empty( $subscription ) || empty( $subscription->id ) ) {
			return false;
		}

		// If this is already cancelled, return true.
		if ( ! empty( $subscription->canceled_at ) ) {
			return true;
		}

		// Make sure we get the customer for this subscription.
		$order = new MemberOrder();
		$order->getLastMemberOrderBySubscriptionTransactionID( $subscription->id );

		// No order?
		if ( empty( $order ) ) {
			//lets cancel anyway, but this is suspicious
			$r = $subscription->cancel();

			return true;
		}

		// Okay have an order, so get customer so we can cancel invoices too
		$this->getCustomer( $order );

		// Get open invoices.
		$invoices = Stripe_Invoice::all(['customer' => $this->customer->id, 'status' => 'open']);

		// Found it, cancel it.
		try {
			// Find any open invoices for this subscription and forgive them.
			if ( ! empty( $invoices ) ) {
				foreach ( $invoices->data as $invoice ) {
					if ( 'open' == $invoice->status && $invoice->subscription == $subscription->id ) {
						$invoice->voidInvoice();
					}
				}
			}

			// Sometimes we don't want to cancel the local membership when Stripe sends its webhook.
			if ( $preserve_local_membership ) {
				self::ignoreCancelWebhookForThisSubscription( $subscription->id, $order->user_id );
			}

			// Cancel
			$r = $subscription->cancel();

			return true;
		} catch ( \Throwable $e ) {
			return false;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Filter pmpro_next_payment to get date via API if possible
	 *
	 * @since 1.8.6
	 */
	static function pmpro_next_payment( $timestamp, $user_id, $order_status ) {
		//find the last order for this user
		if ( ! empty( $user_id ) ) {
			//get last order
			$order = new MemberOrder();
			$order->getLastMemberOrder( $user_id, $order_status );

			//check if this is a Stripe order with a subscription transaction id
			if ( ! empty( $order->id ) && ! empty( $order->subscription_transaction_id ) && $order->gateway == "stripe" ) {
				//get the subscription and return the current_period end or false
				$subscription = $order->Gateway->getSubscription( $order );

				if ( ! empty( $subscription ) ) {
					$customer = $order->Gateway->getCustomer();
					if ( ! $customer->delinquent && ! empty ( $subscription->current_period_end ) ) {
						$offset = get_option( 'gmt_offset' );						
						$timestamp = $subscription->current_period_end + ( $offset * 3600 );
					} elseif ( $customer->delinquent && ! empty( $subscription->current_period_start ) ) {
						$offset = get_option( 'gmt_offset' );						
						$timestamp = $subscription->current_period_start + ( $offset * 3600 );
					} else {
						$timestamp = null;  // shouldn't really get here
					}
				}
			}
		}

		return $timestamp;
	}

	/**
	 * Refund a payment or invoice
	 *
	 * @param object &$order Related PMPro order object.
	 * @param string $transaction_id Payment or Invoice id to void.
	 *
	 * @return bool                     True or false if the void worked
	 */
	function void( &$order, $transaction_id = null ) {
		//stripe doesn't differentiate between voids and refunds, so let's just pass on to the refund function
		return $this->refund( $order, $transaction_id );
	}

	/**
	 * Refund a payment or invoice
	 *
	 * @param object &$order Related PMPro order object.
	 * @param string $transaction_id Payment or invoice id to void.
	 *
	 * @return bool                   True or false if the refund worked.
	 */
	function refund( &$order, $transaction_id = null ) {
		//default to using the payment id from the order
		if ( empty( $transaction_id ) && ! empty( $order->payment_transaction_id ) ) {
			$transaction_id = $order->payment_transaction_id;
		}

		//need a transaction id
		if ( empty( $transaction_id ) ) {
			return false;
		}

		//if an invoice ID is passed, get the charge/payment id
		if ( strpos( $transaction_id, "in_" ) !== false ) {
			$invoice = Stripe_Invoice::retrieve( $transaction_id );

			if ( ! empty( $invoice ) && ! empty( $invoice->charge ) ) {
				$transaction_id = $invoice->charge;
			}
		}

		//get the charge
		try {
			$charge = Stripe_Charge::retrieve( $transaction_id );
		} catch ( \Throwable $e ) {
			$charge = false;
		} catch ( \Exception $e ) {
			$charge = false;
		}

		//can't find the charge?
		if ( empty( $charge ) ) {
			$order->status     = "error";
			$order->errorcode  = "";
			$order->error      = "";
			$order->shorterror = "";

			return false;
		}

		//attempt refund
		try {
			$refund = $charge->refund();
		} catch ( \Throwable $e ) {
			$order->errorcode  = true;
			$order->error      = __( "Error: ", 'paid-memberships-pro' ) . $e->getMessage();
			$order->shorterror = $order->error;

			return false;
		} catch ( \Exception $e ) {
			$order->errorcode  = true;
			$order->error      = __( "Error: ", 'paid-memberships-pro' ) . $e->getMessage();
			$order->shorterror = $order->error;

			return false;
		}

		if ( $refund->status == "succeeded" ) {
			$order->status = "refunded";
			$order->saveOrder();

			return true;
		} else {
			$order->status     = "error";
			$order->errorcode  = true;
			$order->error      = sprintf( __( "Error: Unkown error while refunding charge #%s", 'paid-memberships-pro' ), $transaction_id );
			$order->shorterror = $order->error;

			return false;
		}
	}

	function set_payment_method( &$order, $force = false ) {
		if ( ! empty( $this->payment_method ) && ! $force ) {
			return true;
		}

		$payment_method = $this->get_payment_method( $order );

		if ( empty( $payment_method ) ) {
			return false;
		}

		$this->payment_method = $payment_method;

		return true;
	}

	function get_payment_method( &$order ) {

		if ( ! empty( $order->payment_method_id ) ) {
			try {
				$payment_method = Stripe_PaymentMethod::retrieve( $order->payment_method_id );
			} catch ( Stripe\Error\Base $e ) {
				$order->error = $e->getMessage();
				return false;
			} catch ( \Throwable $e ) {
				$order->error = $e->getMessage();
				return false;
			} catch ( \Exception $e ) {
				$order->error = $e->getMessage();
				return false;
			}
		}

		if ( empty( $payment_method ) ) {
			return false;
		}

		return $payment_method;
	}

	function set_customer( &$order, $force = false ) {
		if ( ! empty( $this->customer ) && ! $force ) {
			return true;
		}
		$this->getCustomer( $order );
	}

	function attach_payment_method_to_customer( &$order ) {

		if ( ! empty( $this->customer->invoice_settings->default_payment_method ) &&
             $this->customer->invoice_settings->default_payment_method === $this->payment_method->id ) {
			return true;
		}

		try {
			$this->payment_method->attach( [ 'customer' => $this->customer->id ] );
			$this->customer->invoice_settings->default_payment_method = $this->payment_method->id;
			$this->customer->save();
		} catch ( Stripe\Error\Base $e ) {
			$order->error = $e->getMessage();
			return false;
		} catch ( \Throwable $e ) {
			$order->error = $e->getMessage();
			return false;
		} catch ( \Exception $e ) {
			$order->error = $e->getMessage();
			return false;
		}

		return true;
	}

	function process_charges( &$order ) {

		if ( 0 == floatval( $order->InitialPayment ) ) {
			return true;
		}

		$this->set_payment_intent( $order );
		$this->confirm_payment_intent( $order );

		if ( ! empty( $order->error ) ) {
			$order->error = $order->error;

			return false;
		}

		return true;
	}

	function set_payment_intent( &$order, $force = false ) {

		if ( ! empty( $this->payment_intent ) && ! $force ) {
			return true;
		}

		$payment_intent = $this->get_payment_intent( $order );

		if ( empty( $payment_intent ) ) {
			return false;
		}

		$this->payment_intent = $payment_intent;

		return true;
	}

	function get_payment_intent( &$order ) {

		if ( ! empty( $order->payment_intent_id ) ) {
			try {
				$payment_intent = Stripe_PaymentIntent::retrieve( $order->payment_intent_id );
			} catch ( Stripe\Error\Base $e ) {
				$order->error = $e->getMessage();
				return false;
			} catch ( \Throwable $e ) {
				$order->error = $e->getMessage();
				return false;
			} catch ( \Exception $e ) {
				$order->error = $e->getMessage();
				return false;
			}
		}

		if ( empty( $payment_intent ) ) {
			$payment_intent = $this->create_payment_intent( $order );
		}

		if ( empty( $payment_intent ) ) {
			return false;
		}

		return $payment_intent;
	}

	function create_payment_intent( &$order ) {

		global $pmpro_currencies, $pmpro_currency;

		// Account for zero-decimal currencies like the Japanese Yen
		$currency_unit_multiplier = 100; //ie 100 cents per USD
		if ( is_array( $pmpro_currencies[ $pmpro_currency ] ) && isset( $pmpro_currencies[ $pmpro_currency ]['decimals'] ) && $pmpro_currencies[ $pmpro_currency ]['decimals'] == 0 ) {
			$currency_unit_multiplier = 1;
		}

		$amount          = $order->InitialPayment;
		$order->subtotal = $amount;
		$tax             = $order->getTax( true );

		$amount = pmpro_round_price( (float) $order->subtotal + (float) $tax );

		$params = array(
			'customer'               => $this->customer->id,
			'payment_method'         => $this->payment_method->id,
			'amount'                 => $amount * $currency_unit_multiplier,
			'currency'               => $pmpro_currency,
			'confirmation_method'    => 'manual',
			'description'            => apply_filters( 'pmpro_stripe_order_description', "Order #" . $order->code . ", " . trim( $order->FirstName . " " . $order->LastName ) . " (" . $order->Email . ")", $order ),
			'setup_future_usage'     => 'off_session',
		);
		$params = self::add_application_fee_amount( $params );

		/**
		 * Filter params used to create the payment intent.
		 *
		 * @since 2.4.1
		 *
	 	 * @param array  $params 	Array of params sent to Stripe.
		 * @param object $order		Order object for this checkout.
		 */
		$params = apply_filters( 'pmpro_stripe_payment_intent_params', $params, $order );

		try {
			$payment_intent = Stripe_PaymentIntent::create( $params );
		} catch ( Stripe\Error\Base $e ) {
			$order->error = $e->getMessage();
			return false;
		} catch ( \Throwable $e ) {
			$order->error = $e->getMessage();
			return false;
		} catch ( \Exception $e ) {
			$order->error = $e->getMessage();
			return false;
		}

		return $payment_intent;
	}

	function process_subscriptions( &$order ) {

		if ( ! pmpro_isLevelRecurring( $order->membership_level ) ) {
			return true;
		}

		//before subscribing, let's clear out the updates so we don't trigger any during sub
		if ( ! empty( $user_id ) ) {
			$old_user_updates = get_user_meta( $user_id, "pmpro_stripe_updates", true );
			update_user_meta( $user_id, "pmpro_stripe_updates", array() );
		}

		$this->set_setup_intent( $order );
		$this->confirm_setup_intent( $order );

		if ( ! empty( $order->error ) ) {
			$order->error = $order->error;

			//give the user any old updates back
			if ( ! empty( $user_id ) ) {
				update_user_meta( $user_id, "pmpro_stripe_updates", $old_user_updates );
			}

			return false;
		}

		//save new updates if this is at checkout
		//empty out updates unless set above
		if ( empty( $new_user_updates ) ) {
			$new_user_updates = array();
		}

		//update user meta
		if ( ! empty( $user_id ) ) {
			update_user_meta( $user_id, "pmpro_stripe_updates", $new_user_updates );
		} else {
			//need to remember the user updates to save later
			global $pmpro_stripe_updates;
			$pmpro_stripe_updates = $new_user_updates;
			
			if( ! function_exists( 'pmpro_user_register_stripe_updates' ) ) {
				function pmpro_user_register_stripe_updates( $user_id ) {
					global $pmpro_stripe_updates;
					update_user_meta( $user_id, 'pmpro_stripe_updates', $pmpro_stripe_updates );
				}
				add_action( 'user_register', 'pmpro_user_register_stripe_updates' );
			}
		}

		return true;
	}

	function create_plan( &$order ) {

		global $pmpro_currencies, $pmpro_currency;
		
		//figure out the amounts
		$amount     = $order->PaymentAmount;
		$amount_tax = $order->getTaxForPrice( $amount );
		$amount     = pmpro_round_price( (float) $amount + (float) $amount_tax );

		// Account for zero-decimal currencies like the Japanese Yen
		$currency_unit_multiplier = 100; //ie 100 cents per USD
		if ( is_array( $pmpro_currencies[ $pmpro_currency ] ) && isset( $pmpro_currencies[ $pmpro_currency ]['decimals'] ) && $pmpro_currencies[ $pmpro_currency ]['decimals'] == 0 ) {
			$currency_unit_multiplier = 1;
		}

		/*
		Figure out the trial length (first payment handled by initial charge)

		There are two parts to the trial. Part 1 is simply the delay until the first payment
        since we are doing the first payment as a separate transaction.
        The second part is the actual "trial" set by the admin.

        Stripe only supports Year or Month for billing periods, but we account for Days and Weeks just in case.
        */
		if ( $order->BillingPeriod == "Year" ) {
			$trial_period_days = $order->BillingFrequency * 365;    //annual
		} elseif ( $order->BillingPeriod == "Day" ) {
			$trial_period_days = $order->BillingFrequency * 1;        //daily
		} elseif ( $order->BillingPeriod == "Week" ) {
			$trial_period_days = $order->BillingFrequency * 7;        //weekly
		} else {
			$trial_period_days = $order->BillingFrequency * 30;    //assume monthly
		}

		//convert to a profile start date
		$order->ProfileStartDate = date_i18n( "Y-m-d\TH:i:s", strtotime( "+ " . $trial_period_days . " Day", current_time( "timestamp" ) ) );

		//filter the start date
		$order->ProfileStartDate = apply_filters( "pmpro_profile_start_date", $order->ProfileStartDate, $order );

		//convert back to days
		$trial_period_days = ceil( abs( strtotime( date_i18n( "Y-m-d" ), current_time( "timestamp" ) ) - strtotime( $order->ProfileStartDate, current_time( "timestamp" ) ) ) / 86400 );

		//for free trials, just push the start date of the subscription back
		if ( ! empty( $order->TrialBillingCycles ) && $order->TrialAmount == 0 ) {
			$trialOccurrences = (int) $order->TrialBillingCycles;
			if ( $order->BillingPeriod == "Year" ) {
				$trial_period_days = $trial_period_days + ( 365 * $order->BillingFrequency * $trialOccurrences );    //annual
			} elseif ( $order->BillingPeriod == "Day" ) {
				$trial_period_days = $trial_period_days + ( 1 * $order->BillingFrequency * $trialOccurrences );        //daily
			} elseif ( $order->BillingPeriod == "Week" ) {
				$trial_period_days = $trial_period_days + ( 7 * $order->BillingFrequency * $trialOccurrences );    //weekly
			} else {
				$trial_period_days = $trial_period_days + ( 30 * $order->BillingFrequency * $trialOccurrences );    //assume monthly
			}
		} elseif ( ! empty( $order->TrialBillingCycles ) ) {

		}

		// Save $trial_period_days to order for now too.
		$order->TrialPeriodDays = $trial_period_days;

		//create a plan
		try {
			$plan = array(
				"amount"                 => $amount * $currency_unit_multiplier,
				"interval_count"         => $order->BillingFrequency,
				"interval"               => strtolower( $order->BillingPeriod ),
				"trial_period_days"      => $trial_period_days,
				'product'                => array( 'name' => $order->membership_name . " for order " . $order->code ),
				"currency"               => strtolower( $pmpro_currency ),
				"id"                     => $order->code
			);
			$order->plan = Stripe_Plan::create( apply_filters( 'pmpro_stripe_create_plan_array', $plan ) );
		} catch ( Stripe\Error\Base $e ) {
			$order->error = $e->getMessage();

			return false;
		} catch ( \Throwable $e ) {
			$order->error = $e->getMessage();

			return false;
		} catch ( \Exception $e ) {
			$order->error = $e->getMessage();

			return false;
		}

		return $order->plan;
	}

	function create_subscription( &$order ) {

		//subscribe to the plan
		try {
			$params              = array(
				'customer'               => $this->customer->id,
				'default_payment_method' => $this->payment_method,
				'items'                  => array(
					array( 'plan' => $order->code ),
				),
				'trial_period_days'      => $order->TrialPeriodDays,
				'expand'                 => array(
					'pending_setup_intent.payment_method',
				),
			);
			if ( ! self::using_legacy_keys() ) {
				$params['application_fee_percent'] = self::get_application_fee_percentage();
			}
			$order->subscription = Stripe_Subscription::create( apply_filters( 'pmpro_stripe_create_subscription_array', $params ) );
		} catch ( Stripe\Error\Base $e ) {
			$order->error = $e->getMessage();
			return false;
		} catch ( \Throwable $e ) {
			$order->error = $e->getMessage();
			return false;
		} catch ( \Exception $e ) {
			$order->error = $e->getMessage();
			return false;
		}

		return $order->subscription;

	}

	function delete_plan( &$order ) {
		try {
			// Delete the product first while we have a reference to it...
			if ( ( ! empty( $order->plan->product ) ) && ( ! $this->archive_product( $order ) ) ) {
				return false;
			}
			// Then delete the plan.
			$order->plan->delete();
		} catch ( Stripe\Error\Base $e ) {
			$order->error = $e->getMessage();

			return false;
		} catch ( \Throwable $e ) {
			$order->error = $e->getMessage();

			return false;
		} catch ( \Exception $e ) {
			$order->error = $e->getMessage();

			return false;
		}

		return true;
	}

	function archive_product( &$order ) {
		try {
			$product = Stripe_Product::update( $order->plan->product, array( 'active' => false ) );
		} catch ( Stripe\Error\Base $e ) {
			$order->error = $e->getMessage();
			return false;
		} catch ( \Throwable $e ) {
			$order->error = $e->getMessage();
			return false;
		} catch ( \Exception $e ) {
			$order->error = $e->getMessage();
			return false;
		}

		return true;
	}

	function get_setup_intent( &$order ) {

		if ( ! empty( $order->setup_intent_id ) ) {
			try {
				$setup_intent = Stripe_SetupIntent::retrieve( $order->setup_intent_id );
			} catch ( Stripe\Error\Base $e ) {
				$order->error = $e->getMessage();
				return false;
			} catch ( \Throwable $e ) {
				$order->error = $e->getMessage();
				return false;
			} catch ( \Exception $e ) {
				$order->error = $e->getMessage();
				return false;
			}
		}

		if ( empty( $setup_intent ) ) {
			$setup_intent = $this->create_setup_intent( $order );
		}

		if ( empty( $setup_intent ) ) {
			return false;
		}

		return $setup_intent;
	}

	function set_setup_intent( &$order, $force = false ) {

		if ( ! empty( $this->setup_intent ) && ! $force ) {
			return true;
		}

		$setup_intent = $this->get_setup_intent( $order );

		if ( empty( $setup_intent ) ) {
			return false;
		}

		$this->setup_intent = $setup_intent;

		return true;
	}

	function create_setup_intent( &$order ) {

		$this->create_plan( $order );
		$this->subscription = $this->create_subscription( $order );
		$this->delete_plan( $order );

		if ( ! empty( $order->error ) || empty( $this->subscription->pending_setup_intent ) ) {
			return false;
		}

		return $this->subscription->pending_setup_intent;
	}

	function confirm_payment_intent( &$order ) {

		try {
			$params = array(
				'expand' => array(
					'payment_method',
				),
			);
			$this->payment_intent->confirm( $params );
		} catch ( Stripe\Error\Base $e ) {
			$order->error = $e->getMessage();
			return false;
		} catch ( \Throwable $e ) {
			$order->error = $e->getMessage();
			return false;
		} catch ( \Exception $e ) {
			$order->error = $e->getMessage();
			return false;
		}

		if ( 'requires_action' == $this->payment_intent->status ) {
			$order->errorcode = true;
			$order->error = __( 'Customer authentication is required to complete this transaction. Please complete the verification steps issued by your payment provider.', 'paid-memberships-pro' );
			$order->error_type = 'pmpro_alert';

			return false;
		}

		return true;
	}

	function confirm_setup_intent( &$order ) {

		if ( empty( $this->setup_intent ) ) {
			return true;
		}

		if ( 'requires_action' === $this->setup_intent->status ) {
			$order->errorcode = true;
			$order->error     = __( 'Customer authentication is required to finish setting up your subscription. Please complete the verification steps issued by your payment provider.', 'paid-memberships-pro' );

			return false;
		}

	}

	/**
 	 * Get available Apple Pay domains.
 	 */
	function pmpro_get_apple_pay_domains( $limit = 10 ) {
		try {
			$apple_pay_domains = Stripe_ApplePayDomain::all( [ 'limit' => apply_filters( 'pmpro_stripe_apple_pay_domain_retrieve_limit', $limit ) ] );
		} catch (\Throwable $th) {
			$apple_pay_domains = array();
	   	}

		return $apple_pay_domains;
	}

	/**
 	 * Register domain with Apple Pay.
 	 * 
 	 * @since 2.4
 	 */
	function pmpro_create_apple_pay_domain() {
		try {
			$create = Stripe_ApplePayDomain::create([
				'domain_name' => $_SERVER['HTTP_HOST'],
			]);
		} catch (\Throwable $th) {
			//throw $th;
			return false;
		}
		return $create;
	}

	/**
 	 * See if domain is registered with Apple Pay.
 	 * 
 	 * @since 2.4
 	 */
	function pmpro_does_apple_pay_domain_exist() {
		$apple_pay_domains = $this->pmpro_get_apple_pay_domains();

		if ( empty( $apple_pay_domains ) ) {
			return false;
		}

		foreach( $apple_pay_domains as $apple_pay_domain ) {
			if ( $apple_pay_domain->domain_name === $_SERVER['HTTP_HOST'] ) {
				return true;
			}
		}
		return false;
   }

	public static function pmpro_set_up_apple_pay( $payment_option_values, $gateway  ) {
		// Check that we just saved Stripe settings.
		if ( $gateway != 'stripe' || empty( $_REQUEST['savesettings'] ) ) {
			return;
		}

		// Check that payment request button is enabled.
		if ( empty( $payment_option_values['stripe_payment_request_button'] ) ) {
			// We don't want to unregister domain or remove file in case
			// other plugins are using it.
			return;	
		}
	
		// Make sure that Apple Pay is set up.
		// TODO: Apple Pay API functions don't seem to work with
		//       test API keys. Need to figure this out.
		$stripe = new PMProGateway_stripe();
		if ( ! $stripe->pmpro_does_apple_pay_domain_exist() ) {
			// 1. Make sure domain association file available.
			flush_rewrite_rules();
			// 2. Register Domain with Apple.
			$stripe->pmpro_create_apple_pay_domain();
		}
   }

   function get_account() {
		try {
			$account = Stripe_Account::retrieve();
		} catch ( Stripe\Error\Base $e ) {
			return false;
		} catch ( \Throwable $e ) {
			return false;
		} catch ( \Exception $e ) {
			return false;
		}

		if ( empty( $account ) ) {
			return false;
		}

		return $account;
	}

	static function get_account_country() {
		$account_country = get_transient( 'pmpro_stripe_account_country' );
		if ( empty( $account_country ) ) {
			$stripe = new PMProGateway_stripe();
			$account = $stripe->get_account();
			if ( ! empty( $account ) && ! empty( $account->country ) ) {
				$account_country = $account->country;
				set_transient( 'pmpro_stripe_account_country', $account_country );
			}
		}
		return $account_country ?: 'US';
	}

	function clean_up( &$order ) {
		if ( ! empty( $this->payment_intent ) && 'succeeded' == $this->payment_intent->status ) {
			$order->payment_transaction_id = $this->payment_intent->charges->data[0]->id;
		}

		if ( empty( $order->subscription_transaction_id ) && ! empty( $this->subscription ) ) {
			$order->subscription_transaction_id = $this->subscription->id;
		}
	}

	/**
	 * Get percentage of Stripe payment to charge as application fee.
	 *
	 * @return int percentage to charge for application fee.
	 */
	static function get_application_fee_percentage() {
		$application_fee_percentage = pmpro_license_isValid( null, 'plus' ) ? 0 : 1;
		$application_fee_percentage = apply_filters( 'pmpro_set_application_fee_percentage', $application_fee_percentage );
		return round( floatval( $application_fee_percentage ), 2 );
	}

	/**
	 * Add application fee to params to be sent to Stripe.
	 *
	 * @param  array $params to be sent to Stripe.
	 * @param  bool  $add_percent true if percentage should be added, false if actual amount.
	 * @return array params with application fee if applicable.
	 */
	static function add_application_fee_amount( $params ) {
		if ( empty( $params['amount'] ) || self::using_legacy_keys() ) {
			return $params;
		}
		$amount = $params['amount'];
		$application_fee = $amount * ( self::get_application_fee_percentage() / 100 );
		$application_fee = floor( $application_fee );
		if ( ! empty( $application_fee ) ) {
			$params['application_fee_amount'] = intval( $application_fee );
		}
		return $params;
	}

	/**
	 * This function is used to save the parameters returned after successfull connection of Stripe account.
	 *
	 * @return void
	 */
	static function stripe_connect_save_options() {
		// Is user have permission to edit give setting.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Be sure only to connect when param present.
		if ( ! isset( $_REQUEST['pmpro_stripe_connected'] ) || ! isset( $_REQUEST['pmpro_stripe_connected_environment'] ) ) {
			return false;
		}
		
		// Change current gateway to Stripe
		pmpro_setOption( 'gateway', 'stripe' );
		pmpro_setOption( 'gateway_environment', $_REQUEST['pmpro_stripe_connected_environment'] );

		$error = '';
		if (
			'false' === $_REQUEST['pmpro_stripe_connected']
			&& isset( $_REQUEST['error_message'] )
		) {
			$error = $_REQUEST['error_message'];
		} elseif (
			'false' === $_REQUEST['pmpro_stripe_connected']
			|| ! isset( $_REQUEST['pmpro_stripe_publishable_key'] )
			|| ! isset( $_REQUEST['pmpro_stripe_user_id'] )
			|| ! isset( $_REQUEST['pmpro_stripe_access_token'] )
		) {
			$error = __( 'Invalid response from the Stripe Connect server.', 'paid-memberships-pro' );
		} else {
			// Update keys.
			if ( $_REQUEST['pmpro_stripe_connected_environment'] === 'live' ) {
				// Update live keys.
				pmpro_setOption( 'live_stripe_connect_user_id', $_REQUEST['pmpro_stripe_user_id'] );
				pmpro_setOption( 'live_stripe_connect_secretkey', $_REQUEST['pmpro_stripe_access_token'] );
				pmpro_setOption( 'live_stripe_connect_publishablekey', $_REQUEST['pmpro_stripe_publishable_key'] );
			} else {
				// Update sandbox keys.
				pmpro_setOption( 'sandbox_stripe_connect_user_id', $_REQUEST['pmpro_stripe_user_id'] );
				pmpro_setOption( 'sandbox_stripe_connect_secretkey', $_REQUEST['pmpro_stripe_access_token'] );
				pmpro_setOption( 'sandbox_stripe_connect_publishablekey', $_REQUEST['pmpro_stripe_publishable_key'] );
			}
			

			// Delete option for user API key.
			delete_option( 'pmpro_stripe_secretkey' );
			delete_option( 'pmpro_stripe_publishablekey' );

			wp_redirect( admin_url( 'admin.php?page=pmpro-paymentsettings' ) );
			exit;
		}

		if ( ! empty( $error ) ) {
			global $pmpro_stripe_error;
			$pmpro_stripe_error = sprintf(
				/* translators: %s Error Message */
				__( '<strong>Error:</strong> PMPro could not connect to the Stripe API. Reason: %s', 'paid-memberships-pro' ),
				esc_html( $error )
			);
		}
	}

	static function stripe_connect_show_errors() {
		global $pmpro_stripe_error;
		if ( ! empty( $pmpro_stripe_error ) ) {
			$class   = 'notice notice-error pmpro-stripe-connect-message';
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $pmpro_stripe_error );
		}
	}

	/**
	 * Disconnects user from the Stripe Connected App.
	 */
	static function stripe_connect_deauthorize() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Be sure only to deauthorize when param present.
		if ( ! isset( $_REQUEST['pmpro_stripe_disconnected'] ) || ! isset( $_REQUEST['pmpro_stripe_disconnected_environment'] ) ) {
			return false;
		}

		// Show message if NOT disconnected.
		if (
			'false' === $_REQUEST['pmpro_stripe_disconnected']
			&& isset( $_REQUEST['error_code'] )
		) {

			$class   = 'notice notice-warning pmpro-stripe-disconnect-message';
			$message = sprintf(
				/* translators: %s Error Message */
				__( '<strong>Error:</strong> PMPro could not disconnect from the Stripe API. Reason: %s', 'paid-memberships-pro' ),
				esc_html( $_REQUEST['error_message'] )
			);

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );

		}

		if ( $_REQUEST['pmpro_stripe_disconnected_environment'] === 'live' ) {
			// Delete live keys.
			delete_option( 'pmpro_live_stripe_connect_user_id' );
			delete_option( 'pmpro_live_stripe_connect_secretkey' );
			delete_option( 'pmpro_live_stripe_connect_publishablekey' );
		} else {
			// Delete sandbox keys.
			delete_option( 'pmpro_sandbox_stripe_connect_user_id' );
			delete_option( 'pmpro_sandbox_stripe_connect_secretkey' );
			delete_option( 'pmpro_sandbox_stripe_connect_publishablekey' );
		}
	}

	/**
	 * Should we show the legacy key fields on the payment settings page.
	 * We should if the site is using legacy keys already or
	 * if a filter has been set.
	 * @since 2.6
	 */
	public static function show_legacy_keys_settings() {
		$r = self::using_legacy_keys();
		$r = apply_filters( 'pmpro_stripe_show_legacy_keys_settings', $r );
		return $r;
	}

	/**
	 * Determine whether the site is using legacy Stripe keys.
	 *
	 * @return bool Whether the site is using legacy Stripe keys.
	 */
	public static function using_legacy_keys() {
		$r = ! empty( pmpro_getOption( 'stripe_secretkey' ) ) && ! empty( pmpro_getOption( 'stripe_publishablekey' ) );		
		return $r;
	}

	/**
	 * Determine whether the site has Stripe Connect credentials set based on gateway environment.
	 *
	 * @param null|string $gateway_environment The gateway environment to use, default uses the current saved setting.
	 *
	 * @return bool Whether the site has Stripe Connect credentials set.
	 */
	public static function has_connect_credentials( $gateway_environment = null ) {
		if ( empty( $gateway_environment ) ) {
			$gateway_engvironemnt = pmpro_getOption( 'pmpro_gateway_environment' );
		}

		if ( $gateway_environment === 'live' ) {
			// Return whether Stripe is connected for live gateway environment.
			return ( 
				pmpro_getOption( 'live_stripe_connect_user_id' ) &&
				pmpro_getOption( 'live_stripe_connect_secretkey' ) &&
				pmpro_getOption( 'live_stripe_connect_publishablekey' )
			);
		} else {
			// Return whether Stripe is connected for sandbox gateway environment.
			return ( 
				pmpro_getOption( 'sandbox_stripe_connect_user_id' ) &&
				pmpro_getOption( 'sandbox_stripe_connect_secretkey' ) &&
				pmpro_getOption( 'sandbox_stripe_connect_publishablekey' )
			);
		}
	}

	/**
	 * Get the Stripe secret key based on gateway environment.
	 *
	 * @return The Stripe secret key.
	 */
	public static function get_secretkey() {
		$secretkey = '';
		if ( self::using_legacy_keys() ) {
			$secretkey = pmpro_getOption( 'stripe_secretkey' ); 
		} else {
			$secretkey = pmpro_getOption( 'gateway_environment' ) === 'live'
				? pmpro_getOption( 'live_stripe_connect_secretkey' )
				: pmpro_getOption( 'sandbox_stripe_connect_secretkey' );
		}
		return $secretkey;
	}

	static function get_publishablekey() {
		$publishablekey = '';
		if ( self::using_legacy_keys() ) {
			$publishablekey = pmpro_getOption( 'stripe_publishablekey' ); 
		} else {
			$publishablekey = pmpro_getOption( 'gateway_environment' ) === 'live'
				? pmpro_getOption( 'live_stripe_connect_publishablekey' )
				: pmpro_getOption( 'sandbox_stripe_connect_publishablekey' );
		}
		return $publishablekey;
	}

	/**
	 * Get the Stripe Connect User ID based on gateway environment.
	 *
	 * @since 2.6.0
	 *
	 * @return string The Stripe Connect User ID.
	 */
	public static function get_connect_user_id() {
		return pmpro_getOption( 'gateway_environment' ) === 'live'
			? pmpro_getOption( 'live_stripe_connect_user_id' )
			: pmpro_getOption( 'sandbox_stripe_connect_user_id' );
	}

	/**
	 * Determine whether the webhook is working by checking for Stripe orders with invalid transaction IDs.
	 *
	 * @param string|null $gateway_environment to check webhooks for. Defaults to set gateway environment.
	 *
	 * @return bool Whether the webhook is working.
	 */
	public static function webhook_is_working( $gateway_environment = null ) {
		global $wpdb;

		if ( empty( $gateway_environment ) ) {
			$gateway_engvironemnt = pmpro_getOption( 'pmpro_gateway_environment' );
		}

		$last_webhook = get_option( 'pmpro_stripe_last_webhook_received_' . $gateway_environment );

		if ( empty( $last_webhook ) ) {
			// Probably never got a webhook event.
			$last_webhook_safe = date( 'Y-m-d H:i:s', strtotime( '-5 years' ) );
		} else {
			// In case recurring order made after webhook event received.
			$last_webhook_safe  = date( 'Y-m-d H:i:s', strtotime( $last_webhook . ' +5 minutes' ) );
		}

		$hour_before_now = date( 'Y-m-d H:i:s', strtotime( '-1 hour' ) );

		$num_problem_orders = $wpdb->get_var(
			$wpdb->prepare(
				"
					SELECT COUNT(*)
					FROM `{$wpdb->pmpro_membership_orders}`
					WHERE
						`gateway` = 'stripe'
						AND `gateway_environment` = %s
						AND `subscription_transaction_id` <> '' 
						AND `subscription_transaction_id` IS NOT NULL
						AND `timestamp` > %s
						AND `timestamp` < %s
				",
				$gateway_environment,
				$last_webhook_safe,
				$hour_before_now
			)
		);

		return ( empty( $num_problem_orders ) );
	}
	
	/**
	 * Get the date the last webhook was processed.
	 * @param environment The gateway environment (live or sandbox) to check for.
	 * @returns HTML with the date of the last webhook or an error message.
	 * @since 2.6
	 */
	public static function get_last_webhook_date( $environment = 'live' ) {	
		$last_webhook = get_option( 'pmpro_stripe_last_webhook_received_' . $environment );
		if ( ! empty( $last_webhook ) ) {
			echo '<p>' . esc_html__( 'Last webhook received at', 'paid-memberships-pro' ) . ': ' . esc_html( $last_webhook ) . ' GMT.</p>';
		} else {
			echo '<p>' . esc_html__( 'No webhooks have been received.', 'paid-memberships-pro' ) . '</p>';
		}
		if ( ! self::webhook_is_working( $environment ) ) {
			echo '<div class="notice error inline"><p>';
			echo esc_html__( 'Your webhook may not be working correctly.', 'paid-memberships-pro' );
			echo ' <a target="_blank" href="https://www.paidmembershipspro.com/gateway/stripe/?utm_source=plugin&utm_medium=pmpro-paymentsettings&utm_campaign=gateways&utm_content=stripe-webhook#tab-gateway-setup">';
			echo esc_html__( 'Click here for info on setting up your webhook with Stripe.', 'paid-memberships-pro' );
			echo '</a>';
			echo '</p></div>';
		}
	}
}
