<?php

/**
 * The Settings Page
 *
 * @since 4.0
 */

namespace CustomFacebookFeed\Admin;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use CustomFacebookFeed\CFF_View;
use CustomFacebookFeed\CFF_Response;

class CFF_oEmbeds {
    /**
	 * Admin menu page slug.
	 *
	 * @since 4.0
	 *
	 * @var string
	 */
	const SLUG = 'cff-oembeds-manager';

	/**
	 * Initializing the class
	 *
	 * @since 4.0
	 */
	function __construct(){
		$this->init();
	}

    /**
	 * Determining if the user is viewing the our page, if so, party on.
	 *
	 * @since 4.0
	 */
	public function init() {
		if ( ! is_admin() ) {
			return;
		}

		add_action('admin_menu', [$this, 'register_menu']);

		add_action( 'wp_ajax_disable_facebook_oembed', [$this, 'disable_facebook_oembed'] );
		add_action( 'wp_ajax_disable_instagram_oembed', [$this, 'disable_instagram_oembed'] );
	}

	/**
	 * Register Menu.
	 *
	 * @since 4.0
	 */
	function register_menu() {
        $cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
        $cap = apply_filters( 'cff_settings_pages_capability', $cap );

       $oembeds_manager = add_submenu_page(
           'cff-top',
           __( 'oEmbeds', 'custom-facebook-feed' ),
           __( 'oEmbeds', 'custom-facebook-feed' ),
           $cap,
           self::SLUG,
           [$this, 'oembeds_manager'],
           2
       );
       add_action( 'load-' . $oembeds_manager, [$this,'oembeds_enqueue_admin_scripts']);
   }

	/**
	 * Disable Facebook oEmbed
	 *
	 * @since 4.0
	 *
	 * @return CFF_Response
	 */
	public function disable_facebook_oembed () {
		\CustomFacebookFeed\Builder\CFF_Feed_Builder::check_privilege( 'nonce' );

		$oembed_settings = get_option( 'cff_oembed_token', array() );
		$oembed_settings['access_token'] = '';
		$oembed_settings['disabled'] = true;
		update_option( 'cff_oembed_token', $oembed_settings );

		new CFF_Response( true, array(
			'connectionUrl' => $this->get_connection_url()
		) );
	}

	/**
	 * Disable Instagram oEmbed
	 *
	 * @since 4.0
	 *
	 * @return CFF_Response
	 */
	public function disable_instagram_oembed () {
		\CustomFacebookFeed\Builder\CFF_Feed_Builder::check_privilege( 'nonce' );

		$oembed_settings = get_option( 'sbi_oembed_token', array() );
		$oembed_settings['access_token'] = '';
		$oembed_settings['disabled'] = true;
		update_option( 'sbi_oembed_token', $oembed_settings );

		new CFF_Response( true, array(
			'connectionUrl' => $this->get_connection_url()
		) );
	}

   	/**
	 * Enqueue oEmbeds CSS & Script.
	 *
	 * Loads only for oEmbeds page
	 *
	 * @since 4.0
	 */
    public function oembeds_enqueue_admin_scripts(){
        if( ! get_current_screen() ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! 'facebook-feed_page_cff-oembeds-manager' === $screen->id ) {
            return;
		}

		wp_enqueue_style(
			'oembeds-style',
			CFF_PLUGIN_URL . 'admin/assets/css/oembeds.css',
			false,
			CFFVER
		);

		wp_enqueue_script(
			'feed-vue',
			'https://cdn.jsdelivr.net/npm/vue@2.6.12',
			null,
			'2.6.12',
			true
		);

		wp_enqueue_script(
			'oembeds-app',
			CFF_PLUGIN_URL.'admin/assets/js/oembeds.js',
			null,
			CFFVER,
			true
		);

		$cff_oembends = $this->statuses_and_info();
		$cff_oembends['nonce'] = wp_create_nonce( 'cff-admin' );

        wp_localize_script(
			'oembeds-app',
			'cff_oembeds',
			$cff_oembends
		);
    }

	/**
	 * Statuses and info about the current state of oEmbed connection
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function statuses_and_info() {
		$return = array(
			'admin_url' 		=> admin_url(),
			'ajax_handler'		=> admin_url( 'admin-ajax.php' ),
            'supportPageUrl'    => admin_url( 'admin.php?page=cff-support' ),
			'links'				=> \CustomFacebookFeed\Builder\CFF_Feed_Builder::get_links_with_utm(),
			'socialWallLinks'   => \CustomFacebookFeed\Builder\CFF_Feed_Builder::get_social_wall_links(),
			'socialWallActivated' => is_plugin_active( 'social-wall/social-wall.php' ),
			'genericText'       => array(
				'help' => __( 'Help', 'custom-facebook-feed' ),
				'title' => __( 'oEmbeds', 'custom-facebook-feed' ),
				'description' => __( 'Use Smash Balloon to power any Facebook or Instagram oEmbeds across your site. Just click the button below and we\'ll do the rest.                ', 'custom-facebook-feed' ),
				'facebookOEmbeds' => __( 'Facebook oEmbeds are currently not being handled by Smash Balloon', 'custom-facebook-feed' ),
				'facebookOEmbedsEnabled' => __( 'Facebook oEmbeds are turned on', 'custom-facebook-feed' ),
				'instagramOEmbeds' => __( 'Instagram oEmbeds are currently not being handled by Smash Balloon', 'custom-facebook-feed' ),
				'instagramOEmbedsEnabled' => __( 'Instagram oEmbeds are turned on', 'custom-facebook-feed' ),
				'enable' => __( 'Enable', 'custom-facebook-feed' ),
				'disable' => __( 'Disable', 'custom-facebook-feed' ),
				'whatAreOembeds' => __( 'What are oEmbeds?', 'custom-facebook-feed' ),
				'whatElseOembeds' => __( 'What else can the Custom Facebook Feed plugin do?', 'custom-facebook-feed' ),
				'whenYouPaste' => __( 'When you paste a link to a Facebook or Instagram post in WordPress, it automatically displays the post instead of the URL. That is called an oEmbed.', 'custom-facebook-feed' ),
				'dueToRecent' => __( 'Due to recent API changes from Facebook, WordPress cannot automatically embed your posts.', 'custom-facebook-feed' ),
				'however' => __( 'However, we have added this feature to Smash Balloon to make sure your oEmbeds keep working.', 'custom-facebook-feed' ),
				'justEnable' => __( 'Just enable it above, and all your existing and new embeds should work automatically, no other input required.', 'custom-facebook-feed' ),
				'displayACompletely' => __( 'Display a completely customizable Facebook Feed with tons of features', 'custom-facebook-feed' ),
				'createACustom' => __( 'Create a custom styled feed of your Facebook Page or Group posts which integrates seamlessly with your WordPress theme.', 'custom-facebook-feed' ),
            ),
            'images' => array(
                'fbIcon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2.04004C6.5 2.04004 2 6.53004 2 12.06C2 17.06 5.66 21.21 10.44 21.96V14.96H7.9V12.06H10.44V9.85004C10.44 7.34004 11.93 5.96004 14.22 5.96004C15.31 5.96004 16.45 6.15004 16.45 6.15004V8.62004H15.19C13.95 8.62004 13.56 9.39004 13.56 10.18V12.06H16.34L15.89 14.96H13.56V21.96C15.9164 21.5879 18.0622 20.3856 19.6099 18.5701C21.1576 16.7546 22.0054 14.4457 22 12.06C22 6.53004 17.5 2.04004 12 2.04004Z" fill="#006BFA"/></svg>',
                'instaIcon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 6.60938C9 6.60938 6.60938 9.04688 6.60938 12C6.60938 15 9 17.3906 12 17.3906C14.9531 17.3906 17.3906 15 17.3906 12C17.3906 9.04688 14.9531 6.60938 12 6.60938ZM12 15.5156C10.0781 15.5156 8.48438 13.9688 8.48438 12C8.48438 10.0781 10.0312 8.53125 12 8.53125C13.9219 8.53125 15.4688 10.0781 15.4688 12C15.4688 13.9688 13.9219 15.5156 12 15.5156ZM18.8438 6.42188C18.8438 5.71875 18.2812 5.15625 17.5781 5.15625C16.875 5.15625 16.3125 5.71875 16.3125 6.42188C16.3125 7.125 16.875 7.6875 17.5781 7.6875C18.2812 7.6875 18.8438 7.125 18.8438 6.42188ZM22.4062 7.6875C22.3125 6 21.9375 4.5 20.7188 3.28125C19.5 2.0625 18 1.6875 16.3125 1.59375C14.5781 1.5 9.375 1.5 7.64062 1.59375C5.95312 1.6875 4.5 2.0625 3.23438 3.28125C2.01562 4.5 1.64062 6 1.54688 7.6875C1.45312 9.42188 1.45312 14.625 1.54688 16.3594C1.64062 18.0469 2.01562 19.5 3.23438 20.7656C4.5 21.9844 5.95312 22.3594 7.64062 22.4531C9.375 22.5469 14.5781 22.5469 16.3125 22.4531C18 22.3594 19.5 21.9844 20.7188 20.7656C21.9375 19.5 22.3125 18.0469 22.4062 16.3594C22.5 14.625 22.5 9.42188 22.4062 7.6875ZM20.1562 18.1875C19.8281 19.125 19.0781 19.8281 18.1875 20.2031C16.7812 20.7656 13.5 20.625 12 20.625C10.4531 20.625 7.17188 20.7656 5.8125 20.2031C4.875 19.8281 4.17188 19.125 3.79688 18.1875C3.23438 16.8281 3.375 13.5469 3.375 12C3.375 10.5 3.23438 7.21875 3.79688 5.8125C4.17188 4.92188 4.875 4.21875 5.8125 3.84375C7.17188 3.28125 10.4531 3.42188 12 3.42188C13.5 3.42188 16.7812 3.28125 18.1875 3.84375C19.0781 4.17188 19.7812 4.92188 20.1562 5.8125C20.7188 7.21875 20.5781 10.5 20.5781 12C20.5781 13.5469 20.7188 16.8281 20.1562 18.1875Z" fill="url(#paint0_linear)"/><defs><linearGradient id="paint0_linear" x1="8.95781" y1="41.6859" x2="53.1891" y2="-3.46406" gradientUnits="userSpaceOnUse"><stop stop-color="white"/><stop offset="0.147864" stop-color="#F6640E"/><stop offset="0.443974" stop-color="#BA03A7"/><stop offset="0.733337" stop-color="#6A01B9"/><stop offset="1" stop-color="#6B01B9"/></linearGradient></defs></svg>',
                'image1_2x' => CFF_PLUGIN_URL . 'admin/assets/img/oembeds-image-1@2x.png',
                'image2_2x' => CFF_PLUGIN_URL . 'admin/assets/img/oembeds-image-2@2x.png',
                'image3_2x' => CFF_PLUGIN_URL . 'admin/assets/img/oembeds-image-3@2x.png',
                'image4_2x' => CFF_PLUGIN_URL . 'admin/assets/img/oembeds-image-4@2x.png',
			),
			'modal' => array(
				'title' => __( 'Enable Instagram oEmbeds', 'custom-facebook-feed' ),
				'description' => __( 'To enable Instagram oEmbeds our Instagram Feed plugin is required. Click the button below to Install it and enable Instagram oEmbeds.', 'custom-facebook-feed' ),
				'install' => __( 'Install Plugin', 'custom-facebook-feed' ),
				'activate' => __( 'Activate Plugin', 'custom-facebook-feed' ),
				'cancel' => __( 'Cancel', 'custom-facebook-feed' ),
				'instaIcon' => CFF_PLUGIN_URL . 'admin/assets/img/instagram-color-icon.svg',
				'timesIcon' => '<svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.2084 2.14275L12.8572 0.791504L7.50008 6.14859L2.143 0.791504L0.791748 2.14275L6.14883 7.49984L0.791748 12.8569L2.143 14.2082L7.50008 8.85109L12.8572 14.2082L14.2084 12.8569L8.85133 7.49984L14.2084 2.14275Z" fill="#141B38"/></svg>',
				'plusIcon' => '<svg width="13" height="12" viewBox="0 0 13 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.0832 6.83317H7.08317V11.8332H5.4165V6.83317H0.416504V5.1665H5.4165V0.166504H7.08317V5.1665H12.0832V6.83317Z" fill="white"/></svg>'
			),
			'loaderSVG' => '<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/></path></svg>',
			'checkmarkSVG' => '<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>',
		);

		$oembed_token_settings = get_option( 'cff_oembed_token', array() );
		$saved_access_token_data = isset( $oembed_token_settings['access_token'] ) ? $oembed_token_settings['access_token'] : false;
		$newly_retrieved_oembed_connection_data = $this->maybe_connection_data( $saved_access_token_data );
		if ( ! empty( $newly_retrieved_oembed_connection_data['access_token'] ) ) {
			$oembed_token_settings = $newly_retrieved_oembed_connection_data;
			$return['newOembedData'] = $newly_retrieved_oembed_connection_data;

			$encryption = new \CustomFacebookFeed\SB_Facebook_Data_Encryption();
			if ( isset( $newly_retrieved_oembed_connection_data['access_token'] ) && ! $encryption->decrypt( $newly_retrieved_oembed_connection_data['access_token'] ) ) {
				$newly_retrieved_oembed_connection_data['access_token'] = $encryption->encrypt( $newly_retrieved_oembed_connection_data['access_token'] );
			}

			update_option( 'cff_oembed_token', $newly_retrieved_oembed_connection_data );
			update_option( 'sbi_oembed_token', $newly_retrieved_oembed_connection_data );
		} elseif ( ! empty( $newly_retrieved_oembed_connection_data ) ) {
			$return['newOembedData'] = $newly_retrieved_oembed_connection_data;
		}

		$return['connectionURL'] = $this->get_connection_url();
		$return['tokenData'] = $oembed_token_settings;

		$return['facebook'] = array(
			'doingOembeds' => $this->facebook_oembed_enabled()
		);
		$return['instagram'] = [
			'active' => class_exists( '\SB_Instagram_Oembed' ),
			'doingOembeds' => false
		];

		$return['instagram']['installer'] = $this->instagram_installer_info();

		if ( class_exists( '\SB_Instagram_Oembed' ) ) {
			$return['instagram']['doingOembeds'] = \SB_Instagram_Oembed::can_do_oembed();
		}

		return $return;
	}

	/**
	 * Connection URLs are based on the website connecting accounts so that is
	 * configured here and returned
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public static function get_connection_url() {

		$admin_url_state = admin_url( 'admin.php?page=cff-oembeds-manager' );
		//If the admin_url isn't returned correctly then use a fallback
		if( $admin_url_state == '/wp-admin/admin.php?page=cff-oembeds-manager' ){
			$admin_url_state = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		}

		if ( class_exists( '\SB_Instagram_Oembed' ) ) {
			$sbi_oembed_token = \SB_Instagram_Oembed::last_access_token();

			if ( ! empty( $sbi_oembed_token ) ) {
				return add_query_arg( 'transfer', '1', $admin_url_state );
			}
		}

		return 'https://api.smashballoon.com/v2/facebook-login.php?state=' . $admin_url_state;
	}

	/**
	 * Listener for retrieving and storing an access token for oEmbeds
	 *
	 * @param string $saved_access_token_data
	 *
	 * @return array|bool
	 *
	 * @since 4.0
	 */
	public static function maybe_connection_data( $saved_access_token_data ) {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}
		if ( $screen->id !== 'facebook-feed_page_cff-oembeds-manager') {
			return false;
		}
		if ( ! empty( $_GET['transfer'] ) ) {
			if ( class_exists( '\SB_Instagram_Oembed' ) ) {
				$sbi_oembed_token = \SB_Instagram_Oembed::last_access_token();
				$return = get_option( 'sbi_oembed_token', array() );

				$return['access_token'] = $sbi_oembed_token;
				$return['disabled'] = false;
				return $return;
			}
		} if ( isset( $_GET['cff_access_token'] ) ) {
			$access_token = $_GET['cff_access_token'];

			$return = [];
			$valid_new_access_token = ! empty( $access_token ) && strlen( $access_token ) > 20 && $saved_access_token_data !== $access_token ? sanitize_text_field( $_GET['cff_access_token'] ) : false;
			if ( $valid_new_access_token ) {
				$url = esc_url_raw( 'https://graph.facebook.com/me/accounts?limit=500&access_token=' . $valid_new_access_token );
				$pages_data_connection = wp_remote_get( $url );
				$return['access_token'] = $valid_new_access_token;
				$return['disabled'] = false;
				if ( ! is_wp_error( $pages_data_connection ) && isset( $pages_data_connection['body'] ) ) {
					$pages_data = json_decode( $pages_data_connection['body'], true );
					if ( isset( $pages_data['data'][0]['access_token'] ) ) {
						$return['expiration_date'] = 'never';
					} else {
						$return['expiration_date'] = time() + (60 * DAY_IN_SECONDS);
					}
				} else {
					$return['expiration_date'] = 'unknown';
				}
			} else {
				if ( $saved_access_token_data === $access_token ) {
					$return['error'] = 'Not New';
				} else {
					$return['error'] = 'Not Valid';
				}
			}

			return $return;

		}

		return false;
	}

	/**
	 * Check if Facebook oEmbed is enabled or not
	 *
	 * @return bool
	 *
	 * @since 4.0
	 */
	public function facebook_oembed_enabled() {
		$cff_oembed_token = get_option( 'cff_oembed_token' );
		if ( isset( $cff_oembed_token['access_token'] ) && isset( $cff_oembed_token['disabled'] ) && ! $cff_oembed_token['disabled'] ) {
			return true;
		}
		return false;
	}

	/**
	 * Determines what action for Instagram should be done in the following order
	 * and returns data used in the common "addon" installer
	 *
	 * Free or Pro active, do nothing
	 * Pro installed but not active, activate Pro
	 * Free installed but not active, activate Free
	 * Nothing installed, install and activate Free
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function instagram_installer_info() {
		$all_plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins' );

		if ( in_array( 'instagram-feed/instagram-feed.php', $active_plugins, true )
			|| in_array( 'instagram-feed-pro/instagram-feed.php', $active_plugins, true ) ) {
			return [
				'nextStep' => 'none',
				'plugin' => 'none',
				'action' => 'none'
			];
		}

		foreach ( $all_plugins as $plugin ) {
			if ( strpos( $plugin['Name'], 'Instagram Feed Pro' ) !== false ) {
				return [
					'nextStep' => 'pro_activate',
					'plugin' => 'instagram-feed-pro/instagram-feed.php',
					'action' => 'cff_activate_addon'
				];
			}
			if ( strpos( $plugin['Name'], 'Instagram Feed' ) !== false ) {
				return [
					'nextStep' => 'free_activate',
					'plugin' => 'instagram-feed/instagram-feed.php',
					'action' => 'cff_activate_addon'
				];
			}
		}

		return [
			'nextStep' => 'free_install',
			'plugin' => 'https://downloads.wordpress.org/plugin/instagram-feed.zip',
			'action' => 'cff_install_addon'
		];
	}

   	/**
	 * oEmbeds Manager Page View Template
	 *
	 * @since 4.0
	 */
	public function oembeds_manager(){
		return CFF_View::render( 'oembeds.index' );
	}
}
