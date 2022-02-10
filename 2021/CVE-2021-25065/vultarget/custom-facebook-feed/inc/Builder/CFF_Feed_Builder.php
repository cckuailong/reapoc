<?php
/**
 * Custom Facebook Feed Builder
 *
 * @since 4.0
 */
namespace CustomFacebookFeed\Builder;
use CustomFacebookFeed\Builder\Tabs\CFF_Styling_Tab;
use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Response;
use CustomFacebookFeed\SB_Facebook_Data_Encryption;
use function DI\value;

class CFF_Feed_Builder {

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 */
	function __construct(){
		$this->init();
	}


	/**
	 * Init the Builder.
	 *
	 * @since 4.0
	*/
	function init(){

		if( is_admin() ){
			add_action('admin_menu', [$this, 'register_menu']);
			// add ajax listeners
			CFF_Feed_Saver_Manager::hooks();
			CFF_Source::hooks();
			CFF_Feed_Builder::hooks();
		}

	}

	/**
	 * Mostly AJAX related hooks
	 *
	 * @since 4.0
	 */
	public static function hooks() {
		add_action( 'wp_ajax_cff_dismiss_onboarding', array( 'CustomFacebookFeed\Builder\CFF_Feed_Builder', 'after_dismiss_onboarding' ) );
		add_action( 'wp_ajax_sb_other_plugins_modal', array( 'CustomFacebookFeed\Builder\CFF_Feed_Builder', 'sb_other_plugins_modal' ) );
	}

	public static function check_privilege( $check_nonce, $action = 'cff-admin' ) {
		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );

		if ( ! current_user_can( $cap ) ) {
			wp_die ( 'You did not do this the right way!' );
		}

		if ( $check_nonce ) {
			$nonce = ! empty( $_POST[ $check_nonce ] ) ? $_POST[ $check_nonce ] : false;

			if ( ! wp_verify_nonce( $nonce, $action ) ) {
				wp_die ( 'You did not do this the right way!' );
			}
		}
	}

	/**
	 * Used to dismiss onboarding using AJAX
	 *
	 * @since 4.0
	 */
	public static function after_dismiss_onboarding() {
		check_ajax_referer( 'cff-admin' , 'nonce');

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );

		if ( current_user_can( $cap ) ) {
			$type = 'newuser';
			if ( isset( $_POST['was_active'] ) ) {
				$type = sanitize_text_field( $_POST['was_active'] );
			}
			CFF_Feed_Builder::update_onboarding_meta( 'dismissed', $type );
		}
		wp_die();
	}

	/**
	 * Used to dismiss onboarding using AJAX
	 *
	 * @since 4.0
	 */
	public static function sb_other_plugins_modal() {
		check_ajax_referer( 'cff_nonce' , 'cff_nonce');

		if ( ! current_user_can( 'activate_plugins' ) || ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error();
		}

		$plugin = isset( $_POST['plugin'] ) ? sanitize_text_field( $_POST['plugin'] ) : '';
		$sb_other_plugins = self::install_plugins_popup();
		$plugin = $sb_other_plugins[ $plugin ];

		// Build the content for modals
		$output = '<div class="cff-fb-source-popup cff-fb-popup-inside cff-install-plugin-modal">
		<div class="cff-fb-popup-cls"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"></path>
		</svg></div>
		<div class="cff-install-plugin-body cff-fb-fs">
		<div class="cff-install-plugin-header">
		<div class="sb-plugin-image">'. $plugin['svgIcon'] .'</div>
		<div class="sb-plugin-name">
		<h3>'. $plugin['name'] .'<span>Free</span></h3>
		<p><span class="sb-author-logo">
		<svg width="13" height="17" viewBox="0 0 13 17" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path fill-rule="evenodd" clip-rule="evenodd" d="M5.72226 4.70098C4.60111 4.19717 3.43332 3.44477 2.34321 3.09454C2.73052 4.01824 3.05742 5.00234 3.3957 5.97507C2.72098 6.48209 1.93286 6.8757 1.17991 7.30453C1.82065 7.93788 2.72809 8.3045 3.45109 8.85558C2.87196 9.57021 1.73414 10.3129 1.45689 10.9606C2.65579 10.8103 4.05285 10.5668 5.16832 10.5174C5.41343 11.7495 5.53984 13.1002 5.88845 14.2288C6.40758 12.7353 6.87695 11.192 7.49488 9.79727C8.44849 10.1917 9.61069 10.6726 10.5416 10.9052C9.88842 9.98881 9.29237 9.01536 8.71356 8.02465C9.57007 7.40396 10.4364 6.79309 11.2617 6.14122C10.0952 6.03375 8.88647 5.96834 7.66107 5.91968C7.46633 4.65567 7.5175 3.14579 7.21791 1.98667C6.76462 2.93671 6.2297 3.80508 5.72226 4.70098ZM6.27621 15.1705C6.12214 15.8299 6.62974 16.1004 6.55318 16.5C6.052 16.3273 5.67498 16.2386 5.00213 16.3338C5.02318 15.8194 5.48587 15.7466 5.3899 15.1151C-1.78016 14.3 -1.79456 1.34382 5.3345 0.546422C14.2483 -0.450627 14.528 14.9414 6.27621 15.1705Z" fill="#E34F0E"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M7.21769 1.98657C7.51728 3.1457 7.46611 4.65557 7.66084 5.91955C8.88625 5.96824 10.0949 6.03362 11.2615 6.14113C10.4362 6.79299 9.56984 7.40386 8.71334 8.02454C9.29215 9.01527 9.8882 9.98869 10.5414 10.9051C9.61046 10.6725 8.44827 10.1916 7.49466 9.79716C6.87673 11.1919 6.40736 12.7352 5.88823 14.2287C5.53962 13.1001 5.41321 11.7494 5.16809 10.5173C4.05262 10.5667 2.65558 10.8102 1.45666 10.9605C1.73392 10.3128 2.87174 9.57012 3.45087 8.85547C2.72786 8.30438 1.82043 7.93778 1.17969 7.30443C1.93264 6.8756 2.72074 6.482 3.39547 5.97494C3.05719 5.00224 2.73031 4.01814 2.34299 3.09445C3.43308 3.44467 4.60089 4.19707 5.72204 4.70088C6.22947 3.80499 6.7644 2.93662 7.21769 1.98657Z" fill="white"></path>
		</svg>
		</span>
		<span class="sb-author-name">'. $plugin['author'] .'</span>
		</p></div></div>
		<div class="cff-install-plugin-content">
		<p>'. $plugin['description'] .'</p>';

		$plugin_install_data = array(
			'step' => 'install',
			'action' => 'cff_install_addon',
			'nonce' => wp_create_nonce('cff-admin'),
			'plugin' => $plugin['plugin'],
			'download_plugin' => $plugin['download_plugin'],
		);

		if ( ! $plugin['installed'] ) {
			$output .= sprintf(
				"<button class='cff-install-plugin-btn cff-btn-orange' id='cff_install_op_btn' data-plugin-atts='%s'>%s</button></div></div></div>",
				json_encode( $plugin_install_data ),
				__('Install', 'custom-facebook-feed')
			);
		}
		if ( $plugin['installed'] && ! $plugin['activated'] ) {
			$plugin_install_data['step'] = 'activate';
			$plugin_install_data['action'] = 'cff_activate_addon';
			$output .= sprintf(
				"<button class='cff-install-plugin-btn cff-btn-orange' id='cff_install_op_btn' data-plugin-atts='%s'>%s</button></div></div></div>",
				json_encode( $plugin_install_data ),
				__('Activate', 'custom-facebook-feed')
			);
		}
		if ( $plugin['installed'] && $plugin['activated'] ) {
			$output .= sprintf(
				"<button class='cff-install-plugin-btn cff-btn-orange' id='cff_install_op_btn' disabled='disabled'>%s</button></div></div></div>",
				__('Plugin installed & activated', 'custom-facebook-feed')
			);
		}

		new CFF_Response( true, array(
			'output' => $output
		) );
	}

	/**
	 * Register Menu.
	 *
	 * @since 4.0
	 */
	function register_menu(){
	 	$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
    	$cap = apply_filters( 'cff_settings_pages_capability', $cap );

		$feed_builder = add_submenu_page(
	        'cff-top',
	        __( 'All Feeds', 'custom-facebook-feed' ),
	        __( 'All Feeds', 'custom-facebook-feed' ),
	        $cap,
	        'cff-feed-builder',
	        [$this, 'feed_builder'],
	        0
	    );
	    add_action( 'load-' . $feed_builder, [$this,'builder_enqueue_admin_scripts']);
	}

	/**
	 * Enqueue Builder CSS & Script.
	 *
	 * Loads only for builder pages
	 *
	 * @since 4.0
	 */
   	public function builder_enqueue_admin_scripts(){
        if(get_current_screen()):
        	$screen = get_current_screen();
        	if ( strpos($screen->id, 'cff-feed-builder')  !== false ) :

				$license_key = null;
				if ( get_option( 'cff_license_key' ) ) {
					$license_key = get_option( 'cff_license_key' );
				}
				$upgrade_url = 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=lite-upgrade-bar';

		        $newly_retrieved_source_connection_data = CFF_Source::maybe_source_connection_data();
		        $active_extensions = $this->get_active_extensions();
		        $installed_plugins = get_plugins();
		        $cff_builder = array(
					'ajax_handler'		=> 	admin_url( 'admin-ajax.php' ),
					'pluginType' 		=> $this->get_plugin_type(),
					'licenseType'		=> CFF_Utils::cff_is_pro_version() ? 'pro' : 'free',
					'builderUrl'		=> admin_url( 'admin.php?page=cff-feed-builder' ),
					'supportPageUrl'    => admin_url( 'admin.php?page=cff-support' ),
					'nonce'				=> wp_create_nonce( 'cff-admin' ),
					'upgradeUrl'		=> $upgrade_url,
					'adminPostURL'		=> 	admin_url( 'post.php' ),
					'widgetsPageURL'	=> 	admin_url( 'widgets.php' ),
					'genericText'       => self::get_generic_text(),
					'welcomeScreen' => array(
						'mainHeading' => __( 'All Feeds', 'custom-facebook-feed' ),
						'createFeed' => __( 'Create your Feed', 'custom-facebook-feed' ),
						'createFeedDescription' => __( 'Select your feed type and connect your Facebook page or group', 'custom-facebook-feed' ),
						'customizeFeed' => __( 'Customize your feed type', 'custom-facebook-feed' ),
						'customizeFeedDescription' => __( 'Choose layouts, color schemes, and more', 'custom-facebook-feed' ),
						'embedFeed' => __( 'Embed your feed', 'custom-facebook-feed' ),
						'embedFeedDescription' => __( 'Easily add the feed anywhere on your website', 'custom-facebook-feed' ),
						'customizeImgPath' => CFF_BUILDER_URL . 'assets/img/welcome-1.png',
						'embedImgPath' => CFF_BUILDER_URL . 'assets/img/welcome-2.png',
					),
			        'selectFeedTypeScreen' => array(
						'mainHeading' => __( 'Create a Facebook Feed', 'custom-facebook-feed' ),
						'feedTypeHeading' => __( 'Select Feed Type', 'custom-facebook-feed' ),
						'advancedHeading' => __( 'Advanced Feeds', 'custom-facebook-feed' ),
						'updateHeading' => __( 'Update Feed Type', 'custom-facebook-feed' ),
			        ),
					'selectSourceScreen' => self::select_source_screen_text(),
					'extensionsPopup' => array(
						'reviews' => array(
							'heading' 		=> __( 'Upgrade to display Facebook Reviews', 'custom-facebook-feed' ),
							'description' 	=> __( 'Add compelling social proof to your site by displaying reviews and recommendations from your Facebook Pages. Easily filter by rating to only show your best reviews.', 'custom-facebook-feed' ),
							'img' 			=> '<svg width="396" height="264" viewBox="0 0 396 264" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0)"><g filter="url(#filter0_d)"><rect x="78" y="28" width="240.612" height="103.119" rx="3.68284" fill="white"/></g><path d="M150.429 40.2761L153.212 46.2674L159.77 47.0622L154.931 51.5598L156.202 58.0423L150.429 54.8307L144.657 58.0423L145.927 51.5598L141.089 47.0622L147.647 46.2674L150.429 40.2761Z" fill="#E34F0E"/><path d="M173.754 40.2761L176.536 46.2674L183.094 47.0622L178.256 51.5598L179.526 58.0423L173.754 54.8307L167.981 58.0423L169.252 51.5598L164.413 47.0622L170.971 46.2674L173.754 40.2761Z" fill="#E34F0E"/><path d="M197.079 40.2761L199.861 46.2674L206.419 47.0622L201.581 51.5598L202.851 58.0423L197.079 54.8307L191.306 58.0423L192.577 51.5598L187.738 47.0622L194.296 46.2674L197.079 40.2761Z" fill="#E34F0E"/><path d="M220.403 40.2761L223.185 46.2674L229.743 47.0622L224.905 51.5598L226.176 58.0423L220.403 54.8307L214.63 58.0423L215.901 51.5598L211.063 47.0622L217.621 46.2674L220.403 40.2761Z" fill="#E34F0E"/><path d="M243.727 40.2761L246.51 46.2674L253.067 47.0622L248.229 51.5598L249.5 58.0423L243.727 54.8307L237.955 58.0423L239.225 51.5598L234.387 47.0622L240.945 46.2674L243.727 40.2761Z" fill="#E34F0E"/><circle cx="107.463" cy="56.2351" r="17.1866" fill="#E8E8EB"/><path d="M143.017 86.1655C146.086 86.1177 155.415 86.0326 168.183 86.0743C184.142 86.1264 189.052 86.2567 211.149 86.1134C233.246 85.97 254.729 86.1394 262.709 86.1394C269.092 86.1394 283.374 86.096 289.716 86.0743" stroke="#DCDDE1" stroke-width="7.36567"/><path d="M143.04 104.195C147.132 104.145 158.876 104.074 173.117 104.195C187.357 104.317 207.694 104.212 216.083 104.145" stroke="#DCDDE1" stroke-width="7.36567"/><g filter="url(#filter1_d)"><rect x="78" y="140.94" width="240.612" height="103.119" rx="3.68284" fill="white"/></g><path d="M150.429 153.216L153.212 159.208L159.77 160.002L154.931 164.5L156.202 170.983L150.429 167.771L144.657 170.983L145.927 164.5L141.089 160.002L147.647 159.208L150.429 153.216Z" fill="#59AB46"/><path d="M173.754 153.216L176.536 159.208L183.094 160.002L178.256 164.5L179.526 170.983L173.754 167.771L167.981 170.983L169.252 164.5L164.413 160.002L170.971 159.208L173.754 153.216Z" fill="#59AB46"/><path d="M197.079 153.216L199.861 159.208L206.419 160.002L201.581 164.5L202.851 170.983L197.079 167.771L191.306 170.983L192.577 164.5L187.738 160.002L194.296 159.208L197.079 153.216Z" fill="#59AB46"/><path d="M220.403 153.216L223.185 159.208L229.743 160.002L224.905 164.5L226.176 170.983L220.403 167.771L214.63 170.983L215.901 164.5L211.063 160.002L217.621 159.208L220.403 153.216Z" fill="#59AB46"/><path d="M243.727 153.216L246.51 159.208L253.067 160.002L248.229 164.5L249.5 170.983L243.727 167.771L237.955 170.983L239.225 164.5L234.387 160.002L240.945 159.208L243.727 153.216Z" fill="#59AB46"/><circle cx="107.463" cy="169.175" r="17.1866" fill="#E8E8EB"/><path d="M143.063 212.209C147.156 212.158 158.9 212.087 173.14 212.209C187.38 212.33 207.718 212.226 216.106 212.158" stroke="#DCDDE1" stroke-width="7.36567"/><path d="M143.063 192.25C152.884 192.191 162.337 192.154 190.327 192.269C201.989 192.315 225.313 192.282 230.224 192.269C235.134 192.256 249.252 192.243 265.211 192.295C277.978 192.337 284.648 192.306 287.308 192.295" stroke="#DCDDE1" stroke-width="7.36567"/><g filter="url(#filter2_d)"><rect x="78" y="253.881" width="240.612" height="103.119" rx="3.68284" fill="white"/></g></g><defs><filter id="filter0_d" x="68.1791" y="23.0896" width="260.254" height="122.761" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feMorphology radius="3.68284" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/><feOffset dy="4.91045"/><feGaussianBlur stdDeviation="6.75187"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter><filter id="filter1_d" x="68.1791" y="136.03" width="260.254" height="122.761" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feMorphology radius="3.68284" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/><feOffset dy="4.91045"/><feGaussianBlur stdDeviation="6.75187"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter><filter id="filter2_d" x="68.1791" y="248.97" width="260.254" height="122.761" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feMorphology radius="3.68284" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/><feOffset dy="4.91045"/><feGaussianBlur stdDeviation="6.75187"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter><clipPath id="clip0"><rect width="396" height="264" fill="white"/></clipPath></defs></svg>',
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'buyUrl' 		=> sprintf('https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=feed-type&utm_medium=reviews-modal&utm_content=see-demo')
						),
						'featuredpost' => array(
							'heading' 		=> __( 'Upgrade to display Single Featured Posts', 'custom-facebook-feed' ),
							'description' 	=> __( 'Easily highlight any single post or event from your Facebook page. Enter the URL of the post and let the plugin do the rest.', 'custom-facebook-feed' ),
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'img' 			=> '<svg width="396" height="264" viewBox="0 0 396 264" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0)"><g filter="url(#filter0_d)"><rect x="100.686" y="67.5616" width="219.616" height="144.658" rx="3.94521" fill="white"/></g><g filter="url(#filter1_d)"><rect x="90.1641" y="55.726" width="219.616" height="144.658" rx="3.94521" fill="white"/></g><g filter="url(#filter2_d)"><rect x="77.0137" y="42.5753" width="219.616" height="144.658" rx="3.94521" fill="white"/></g><g clip-path="url(#clip1)"><rect x="84.9043" y="50.4658" width="203.836" height="128.877" rx="2.63014" fill="#E2F5FF"/><path d="M141.451 179.342H288.739V50.4658H141.451V179.342Z" fill="#86D0F9"/><path d="M178.273 179.342H288.739V101.753H182.219C180.04 101.753 178.273 103.52 178.273 105.699V179.342Z" fill="#8C8F9A"/><mask id="path-7-inside-1" fill="white"><path d="M154.603 156.329C154.603 164.512 151.723 172.435 146.468 178.708C141.213 184.981 133.918 189.205 125.861 190.639C117.804 192.073 109.5 190.626 102.403 186.552C95.3056 182.477 89.8688 176.035 87.0449 168.354L106.307 161.273C107.468 164.43 109.703 167.078 112.621 168.754C115.538 170.429 118.952 171.023 122.264 170.434C125.577 169.844 128.576 168.108 130.736 165.529C132.896 162.95 134.08 159.693 134.08 156.329H154.603Z"/></mask><path d="M154.603 156.329C154.603 164.512 151.723 172.435 146.468 178.708C141.213 184.981 133.918 189.205 125.861 190.639C117.804 192.073 109.5 190.626 102.403 186.552C95.3056 182.477 89.8688 176.035 87.0449 168.354L106.307 161.273C107.468 164.43 109.703 167.078 112.621 168.754C115.538 170.429 118.952 171.023 122.264 170.434C125.577 169.844 128.576 168.108 130.736 165.529C132.896 162.95 134.08 159.693 134.08 156.329H154.603Z" stroke="#59AB46" stroke-width="42.5936" mask="url(#path-7-inside-1)"/></g></g><circle cx="84.2462" cy="55.0685" r="19.0685" fill="#E34F0E"/><path d="M77.1625 61.8904H79.9827V50.7714H77.1625L74.3346 52.6978V55.1327L77.0161 53.3219H77.1625V61.8904ZM82.5256 61.8904H85.5307L87.6805 58.2842H87.7499L89.9151 61.8904H93.0898L89.5761 56.3271V56.2885L93.1206 50.7714H89.9767L87.9502 54.5394H87.8732L85.8312 50.7714H82.5256L85.9237 56.2577V56.3039L82.5256 61.8904Z" fill="white"/><defs><filter id="filter0_d" x="90.165" y="62.3014" width="240.658" height="165.699" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feMorphology radius="3.94521" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/><feOffset dy="5.26027"/><feGaussianBlur stdDeviation="7.23288"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter><filter id="filter1_d" x="79.6435" y="50.4658" width="240.658" height="165.699" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feMorphology radius="3.94521" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/><feOffset dy="5.26027"/><feGaussianBlur stdDeviation="7.23288"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter><filter id="filter2_d" x="66.4931" y="37.3151" width="240.658" height="165.699" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feMorphology radius="3.94521" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/><feOffset dy="5.26027"/><feGaussianBlur stdDeviation="7.23288"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter><clipPath id="clip0"><rect width="264.329" height="190.685" fill="white" transform="translate(66.4922 37.3151)"/></clipPath><clipPath id="clip1"><rect x="84.9043" y="50.4658" width="203.836" height="128.877" rx="2.63014" fill="white"/></clipPath></defs></svg>',
							'buyUrl' 		=> 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=feed-type&utm_medium=featured-post&utm_content=see-demo'
						),
						'singlealbum' => array(
							'heading' 		=> __( 'Upgrade to embed Single Album Feeds', 'custom-facebook-feed' ),
							'description' 	=> __( 'Embed all of the photos from inside any single Facebook album directly into your website, choosing from several attractive layouts.', 'custom-facebook-feed' ),
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'img' 			=> '<svg width="396" height="264" viewBox="0 0 396 264" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0)"><g filter="url(#filter0_d)"><rect x="100.686" y="67.5616" width="219.616" height="144.658" rx="3.94521" fill="white"/></g><g filter="url(#filter1_d)"><rect x="90.1641" y="55.726" width="219.616" height="144.658" rx="3.94521" fill="white"/></g><g filter="url(#filter2_d)"><rect x="77.0137" y="42.5753" width="219.616" height="144.658" rx="3.94521" fill="white"/></g><g clip-path="url(#clip1)"><rect x="84.9043" y="50.4658" width="203.836" height="128.877" rx="2.63014" fill="#E2F5FF"/><path d="M141.451 179.342H288.739V50.4658H141.451V179.342Z" fill="#86D0F9"/><path d="M178.273 179.342H288.739V101.753H182.219C180.04 101.753 178.273 103.52 178.273 105.699V179.342Z" fill="#8C8F9A"/><mask id="path-7-inside-1" fill="white"><path d="M154.603 156.329C154.603 164.512 151.723 172.435 146.468 178.708C141.213 184.981 133.918 189.205 125.861 190.639C117.804 192.073 109.5 190.626 102.403 186.552C95.3056 182.477 89.8688 176.035 87.0449 168.354L106.307 161.273C107.468 164.43 109.703 167.078 112.621 168.754C115.538 170.429 118.952 171.023 122.264 170.434C125.577 169.844 128.576 168.108 130.736 165.529C132.896 162.95 134.08 159.693 134.08 156.329H154.603Z"/></mask><path d="M154.603 156.329C154.603 164.512 151.723 172.435 146.468 178.708C141.213 184.981 133.918 189.205 125.861 190.639C117.804 192.073 109.5 190.626 102.403 186.552C95.3056 182.477 89.8688 176.035 87.0449 168.354L106.307 161.273C107.468 164.43 109.703 167.078 112.621 168.754C115.538 170.429 118.952 171.023 122.264 170.434C125.577 169.844 128.576 168.108 130.736 165.529C132.896 162.95 134.08 159.693 134.08 156.329H154.603Z" stroke="#59AB46" stroke-width="42.5936" mask="url(#path-7-inside-1)"/></g></g><circle cx="84.2462" cy="55.0685" r="19.0685" fill="#E34F0E"/><path d="M77.1625 61.8904H79.9827V50.7714H77.1625L74.3346 52.6978V55.1327L77.0161 53.3219H77.1625V61.8904ZM82.5256 61.8904H85.5307L87.6805 58.2842H87.7499L89.9151 61.8904H93.0898L89.5761 56.3271V56.2885L93.1206 50.7714H89.9767L87.9502 54.5394H87.8732L85.8312 50.7714H82.5256L85.9237 56.2577V56.3039L82.5256 61.8904Z" fill="white"/><defs><filter id="filter0_d" x="90.165" y="62.3014" width="240.658" height="165.699" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feMorphology radius="3.94521" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/><feOffset dy="5.26027"/><feGaussianBlur stdDeviation="7.23288"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter><filter id="filter1_d" x="79.6435" y="50.4658" width="240.658" height="165.699" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feMorphology radius="3.94521" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/><feOffset dy="5.26027"/><feGaussianBlur stdDeviation="7.23288"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter><filter id="filter2_d" x="66.4931" y="37.3151" width="240.658" height="165.699" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/><feMorphology radius="3.94521" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/><feOffset dy="5.26027"/><feGaussianBlur stdDeviation="7.23288"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/></filter><clipPath id="clip0"><rect width="264.329" height="190.685" fill="white" transform="translate(66.4922 37.3151)"/></clipPath><clipPath id="clip1"><rect x="84.9043" y="50.4658" width="203.836" height="128.877" rx="2.63014" fill="white"/></clipPath></defs></svg>',
							'buyUrl' 		=> 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=feed-type&utm_medium=single-album&utm_content=see-demo'
						),
						'carousel' => array(
							'heading' 		=> __( 'Upgrade to get the Carousel layout', 'custom-facebook-feed' ),
							'description' 	=> __( 'The Carousel layout is perfect for when you either want to display a lot of content in a small space or want to catch your visitors attention.', 'custom-facebook-feed' ),
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'img' 			=> '<svg width="397" height="265" viewBox="0 0 397 265" fill="none" xmlns="http://www.w3.org/2000/svg"> <g filter="url(#car_filter0_d)"> <g clip-path="url(#car_clip0)"> <rect x="52.7578" y="54.7651" width="208.244" height="168.003" rx="2.27031" transform="rotate(-5 52.7578 54.7651)" fill="white"/> <g clip-path="url(#car_clip1)"> <rect width="112.002" height="124.647" transform="translate(98.5781 73.4239) rotate(-5)" fill="#DCDDE1"/> <circle cx="112.679" cy="76.2938" r="73.4517" transform="rotate(-5 112.679 76.2938)" fill="#D0D1D7"/> <circle cx="244.582" cy="130.215" r="64.1417" transform="rotate(-5 244.582 130.215)" fill="#86D0F9"/> </g> <g clip-path="url(#car_clip2)"> <rect width="112.002" height="124.647" transform="translate(216.449 63.1114) rotate(-5)" fill="#B6DDAD"/> <circle cx="232.305" cy="183.028" r="46.9609" transform="rotate(-5 232.305 183.028)" fill="#96CE89"/> </g> <g clip-path="url(#car_clip3)"> <rect width="112.002" height="124.647" transform="translate(-20.1992 83.8153) rotate(-5)" fill="#FFDF99"/> <circle cx="128.461" cy="186.877" r="64.1417" transform="rotate(-5 128.461 186.877)" fill="#FFC033"/> </g> </g> </g> <g filter="url(#car_filter1_d)"> <g clip-path="url(#car_clip4)"> <rect x="163.398" y="26.6155" width="208.244" height="168.003" rx="2.27031" transform="rotate(5 163.398 26.6155)" fill="white"/> <g clip-path="url(#car_clip5)"> <rect width="112.002" height="124.647" transform="translate(205.281 52.9472) rotate(5)" fill="#B5E5FF"/> <circle cx="200.072" cy="173.795" r="46.9609" transform="rotate(5 200.072 173.795)" fill="#43A6DB"/> <circle cx="339.206" cy="134.228" r="64.1417" transform="rotate(5 339.206 134.228)" fill="#86D0F9"/> </g> <g clip-path="url(#car_clip6)"> <rect width="112.002" height="124.647" transform="translate(323.152 63.2598) rotate(5)" fill="#B6DDAD"/> <circle cx="317.943" cy="184.108" r="46.9609" transform="rotate(5 317.943 184.108)" fill="#96CE89"/> </g> <g clip-path="url(#car_clip7)"> <rect width="112.002" height="124.647" transform="translate(86.5078 42.5556) rotate(5)" fill="#FCE1D5"/> <circle cx="215.014" cy="169.866" r="64.1417" transform="rotate(5 215.014 169.866)" fill="#F9BBA0"/> </g> </g> </g> <defs> <filter id="car_filter0_d" x="45.846" y="33.6532" width="235.918" height="199.337" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="3.94961"/> <feGaussianBlur stdDeviation="3.45591"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.16 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <filter id="car_filter1_d" x="141.846" y="23.6533" width="235.918" height="199.337" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="3.94961"/> <feGaussianBlur stdDeviation="3.45591"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.16 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <clipPath id="car_clip0"> <rect x="52.7578" y="54.7651" width="208.244" height="168.003" rx="2.27031" transform="rotate(-5 52.7578 54.7651)" fill="white"/> </clipPath> <clipPath id="car_clip1"> <rect width="112.002" height="124.647" fill="white" transform="translate(98.5781 73.4239) rotate(-5)"/> </clipPath> <clipPath id="car_clip2"> <rect width="112.002" height="124.647" fill="white" transform="translate(216.449 63.1114) rotate(-5)"/> </clipPath> <clipPath id="car_clip3"> <rect width="112.002" height="124.647" fill="white" transform="translate(-20.1992 83.8153) rotate(-5)"/> </clipPath> <clipPath id="car_clip4"> <rect x="163.398" y="26.6155" width="208.244" height="168.003" rx="2.27031" transform="rotate(5 163.398 26.6155)" fill="white"/> </clipPath> <clipPath id="car_clip5"> <rect width="112.002" height="124.647" fill="white" transform="translate(205.281 52.9472) rotate(5)"/> </clipPath> <clipPath id="car_clip6"> <rect width="112.002" height="124.647" fill="white" transform="translate(323.152 63.2598) rotate(5)"/> </clipPath> <clipPath id="car_clip7"> <rect width="112.002" height="124.647" fill="white" transform="translate(86.5078 42.5556) rotate(5)"/> </clipPath> </defs> </svg>',
							'buyUrl' 		=> 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=customizer&utm_medium=feed-layout&utm_content=carousel'
						),
						'socialwall' => array(
							//Combine all your social media channels into one Social Wall
							'heading' 		=> __( '<span class="sb-social-wall">Combine all your social media channels into one', 'custom-facebook-feed' ) .' <span>'. __( 'Social Wall', 'custom-facebook-feed' ).'</span></span>',
							'description' 	=> __( '<span class="sb-social-wall">A dash of Instagram, a sprinkle of Facebook, a spoonful of Twitter, and a dollop of YouTube, all in the same feed.</span>', 'custom-facebook-feed' ),
							'popupContentBtn' 	=> '<div class="cff-fb-extpp-lite-btn">' . self::builder_svg_icons()['tag'] . __( 'Lite Feed Users get 50% OFF', 'custom-facebook-feed' ) .'</div>',
							'img' 			=> '<svg width="397" height="264" viewBox="0 0 397 264" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0)"><g filter="url(#filter0_ddd)"><rect x="18.957" y="63" width="113.812" height="129.461" rx="2.8453" fill="white"/></g><g clip-path="url(#clip1)"><path d="M18.957 63H132.769V176.812H18.957V63Z" fill="#0068A0"/><rect x="56.957" y="106" width="105" height="105" rx="9" fill="#005B8C"/></g><path d="M36.0293 165.701C31.4649 165.701 27.7305 169.427 27.7305 174.017C27.7305 178.166 30.7678 181.61 34.7347 182.232V176.423H32.6268V174.017H34.7347V172.183C34.7347 170.1 35.9712 168.954 37.8716 168.954C38.7762 168.954 39.7222 169.112 39.7222 169.112V171.162H38.6766C37.6475 171.162 37.3239 171.801 37.3239 172.456V174.017H39.6309L39.2575 176.423H37.3239V182.232C39.2794 181.924 41.0602 180.926 42.3446 179.419C43.629 177.913 44.3325 175.996 44.3281 174.017C44.3281 169.427 40.5936 165.701 36.0293 165.701Z" fill="#006BFA"/><rect x="53.1016" y="169.699" width="41.2569" height="9.95855" rx="1.42265" fill="#D0D1D7"/><g filter="url(#filter1_ddd)"><rect x="18.957" y="201" width="113.812" height="129.461" rx="2.8453" fill="white"/></g><g clip-path="url(#clip2)"><path d="M18.957 201H132.769V314.812H18.957V201Z" fill="#F37036"/><circle cx="23.957" cy="243" r="59" fill="#E34F0E"/></g><g filter="url(#filter2_ddd)"><rect x="139.957" y="23" width="113.812" height="129.461" rx="2.8453" fill="white"/></g><g clip-path="url(#clip3)"><path d="M139.957 23H253.769V136.812H139.957V23Z" fill="#8C8F9A"/><circle cx="127.457" cy="142.5" r="78.5" fill="#D0D1D7"/></g><path d="M157.026 129.493C154.537 129.493 152.553 131.516 152.553 133.967C152.553 136.456 154.537 138.44 157.026 138.44C159.477 138.44 161.5 136.456 161.5 133.967C161.5 131.516 159.477 129.493 157.026 129.493ZM157.026 136.884C155.431 136.884 154.109 135.601 154.109 133.967C154.109 132.372 155.392 131.088 157.026 131.088C158.621 131.088 159.905 132.372 159.905 133.967C159.905 135.601 158.621 136.884 157.026 136.884ZM162.706 129.338C162.706 128.754 162.239 128.287 161.655 128.287C161.072 128.287 160.605 128.754 160.605 129.338C160.605 129.921 161.072 130.388 161.655 130.388C162.239 130.388 162.706 129.921 162.706 129.338ZM165.662 130.388C165.584 128.987 165.273 127.743 164.262 126.731C163.25 125.72 162.005 125.409 160.605 125.331C159.166 125.253 154.848 125.253 153.408 125.331C152.008 125.409 150.802 125.72 149.752 126.731C148.74 127.743 148.429 128.987 148.351 130.388C148.274 131.827 148.274 136.145 148.351 137.585C148.429 138.985 148.74 140.191 149.752 141.241C150.802 142.253 152.008 142.564 153.408 142.642C154.848 142.719 159.166 142.719 160.605 142.642C162.005 142.564 163.25 142.253 164.262 141.241C165.273 140.191 165.584 138.985 165.662 137.585C165.74 136.145 165.74 131.827 165.662 130.388ZM163.795 139.102C163.523 139.88 162.9 140.463 162.161 140.774C160.994 141.241 158.271 141.124 157.026 141.124C155.742 141.124 153.019 141.241 151.891 140.774C151.113 140.463 150.53 139.88 150.219 139.102C149.752 137.974 149.868 135.25 149.868 133.967C149.868 132.722 149.752 129.999 150.219 128.832C150.53 128.093 151.113 127.509 151.891 127.198C153.019 126.731 155.742 126.848 157.026 126.848C158.271 126.848 160.994 126.731 162.161 127.198C162.9 127.47 163.484 128.093 163.795 128.832C164.262 129.999 164.145 132.722 164.145 133.967C164.145 135.25 164.262 137.974 163.795 139.102Z" fill="url(#paint0_linear)"/><rect x="174.102" y="129.699" width="41.2569" height="9.95855" rx="1.42265" fill="#D0D1D7"/><g filter="url(#filter3_ddd)"><rect x="139.957" y="161" width="114" height="109" rx="2.8453" fill="white"/></g><rect x="148.957" y="194" width="91" height="8" rx="1.42265" fill="#D0D1D7"/><rect x="148.957" y="208" width="51" height="8" rx="1.42265" fill="#D0D1D7"/><path d="M164.366 172.062C163.788 172.324 163.166 172.497 162.521 172.579C163.181 172.182 163.691 171.552 163.931 170.794C163.308 171.169 162.618 171.432 161.891 171.582C161.298 170.937 160.466 170.562 159.521 170.562C157.758 170.562 156.318 172.002 156.318 173.779C156.318 174.034 156.348 174.282 156.401 174.514C153.731 174.379 151.353 173.097 149.771 171.154C149.493 171.627 149.336 172.182 149.336 172.767C149.336 173.884 149.898 174.874 150.768 175.437C150.236 175.437 149.741 175.287 149.306 175.062V175.084C149.306 176.644 150.416 177.949 151.886 178.242C151.414 178.371 150.918 178.389 150.438 178.294C150.642 178.934 151.041 179.493 151.579 179.894C152.117 180.295 152.767 180.517 153.438 180.529C152.301 181.43 150.891 181.916 149.441 181.909C149.186 181.909 148.931 181.894 148.676 181.864C150.101 182.779 151.796 183.312 153.611 183.312C159.521 183.312 162.768 178.407 162.768 174.154C162.768 174.012 162.768 173.877 162.761 173.734C163.391 173.284 163.931 172.714 164.366 172.062Z" fill="#1B90EF"/><g filter="url(#filter4_ddd)"><rect x="260.957" y="63" width="113.812" height="129.461" rx="2.8453" fill="white"/></g><g clip-path="url(#clip4)"><rect x="260.957" y="63" width="113.812" height="113.812" fill="#D72C2C"/><path d="M283.359 103.308L373.461 193.41H208.793L283.359 103.308Z" fill="#DF5757"/></g><path d="M276.37 176.456L280.677 173.967L276.37 171.477V176.456ZM285.963 169.958C286.071 170.348 286.145 170.871 286.195 171.535C286.253 172.199 286.278 172.772 286.278 173.27L286.328 173.967C286.328 175.784 286.195 177.12 285.963 177.975C285.755 178.722 285.274 179.203 284.527 179.411C284.137 179.519 283.423 179.593 282.328 179.643C281.249 179.701 280.262 179.726 279.349 179.726L278.029 179.776C274.552 179.776 272.386 179.643 271.531 179.411C270.784 179.203 270.303 178.722 270.096 177.975C269.988 177.585 269.913 177.062 269.863 176.398C269.805 175.734 269.78 175.162 269.78 174.664L269.73 173.967C269.73 172.149 269.863 170.813 270.096 169.958C270.303 169.212 270.784 168.73 271.531 168.523C271.921 168.415 272.635 168.34 273.73 168.29C274.809 168.232 275.797 168.207 276.71 168.207L278.029 168.158C281.506 168.158 283.672 168.29 284.527 168.523C285.274 168.73 285.755 169.212 285.963 169.958Z" fill="#EB2121"/><rect x="295.102" y="169.699" width="41.2569" height="9.95855" rx="1.42265" fill="#D0D1D7"/><g filter="url(#filter5_ddd)"><rect x="260.957" y="201" width="113.812" height="129.461" rx="2.8453" fill="white"/></g><g clip-path="url(#clip5)"><rect x="260.957" y="201" width="113.812" height="113.812" fill="#59AB46"/><circle cx="374.457" cy="235.5" r="44.5" fill="#468737"/></g><g clip-path="url(#clip6)"><path d="M139.957 228H253.957V296C253.957 296.552 253.509 297 252.957 297H140.957C140.405 297 139.957 296.552 139.957 296V228Z" fill="#0068A0"/><circle cx="227.957" cy="245" r="34" fill="#004D77"/></g></g><defs><filter id="filter0_ddd" x="0.462572" y="53.0414" width="150.801" height="166.45" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8.5359"/><feGaussianBlur stdDeviation="9.24723"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.42265"/><feGaussianBlur stdDeviation="1.42265"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4.26795"/><feGaussianBlur stdDeviation="4.26795"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/></filter><filter id="filter1_ddd" x="0.462572" y="191.041" width="150.801" height="166.45" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8.5359"/><feGaussianBlur stdDeviation="9.24723"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.42265"/><feGaussianBlur stdDeviation="1.42265"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4.26795"/><feGaussianBlur stdDeviation="4.26795"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/></filter><filter id="filter2_ddd" x="121.463" y="13.0414" width="150.801" height="166.45" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8.5359"/><feGaussianBlur stdDeviation="9.24723"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.42265"/><feGaussianBlur stdDeviation="1.42265"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4.26795"/><feGaussianBlur stdDeviation="4.26795"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/></filter><filter id="filter3_ddd" x="121.463" y="151.041" width="150.989" height="145.989" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8.5359"/><feGaussianBlur stdDeviation="9.24723"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.42265"/><feGaussianBlur stdDeviation="1.42265"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4.26795"/><feGaussianBlur stdDeviation="4.26795"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/></filter><filter id="filter4_ddd" x="242.463" y="53.0414" width="150.801" height="166.45" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8.5359"/><feGaussianBlur stdDeviation="9.24723"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.42265"/><feGaussianBlur stdDeviation="1.42265"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4.26795"/><feGaussianBlur stdDeviation="4.26795"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/></filter><filter id="filter5_ddd" x="242.463" y="191.041" width="150.801" height="166.45" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="8.5359"/><feGaussianBlur stdDeviation="9.24723"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="1.42265"/><feGaussianBlur stdDeviation="1.42265"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/><feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4.26795"/><feGaussianBlur stdDeviation="4.26795"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/><feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/><feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/></filter><linearGradient id="paint0_linear" x1="154.502" y1="158.603" x2="191.208" y2="121.133" gradientUnits="userSpaceOnUse"><stop stop-color="white"/><stop offset="0.147864" stop-color="#F6640E"/><stop offset="0.443974" stop-color="#BA03A7"/><stop offset="0.733337" stop-color="#6A01B9"/><stop offset="1" stop-color="#6B01B9"/></linearGradient><clipPath id="clip0"><rect width="396" height="264" fill="white" transform="translate(0.957031)"/></clipPath><clipPath id="clip1"><path d="M18.957 65.3711C18.957 64.0616 20.0186 63 21.3281 63H130.398C131.708 63 132.769 64.0616 132.769 65.3711V156.895H18.957V65.3711Z" fill="white"/></clipPath><clipPath id="clip2"><path d="M18.957 203.371C18.957 202.062 20.0186 201 21.3281 201H130.398C131.708 201 132.769 202.062 132.769 203.371V294.895H18.957V203.371Z" fill="white"/></clipPath><clipPath id="clip3"><path d="M139.957 25.3711C139.957 24.0616 141.019 23 142.328 23H251.398C252.708 23 253.769 24.0616 253.769 25.3711V116.895H139.957V25.3711Z" fill="white"/></clipPath><clipPath id="clip4"><path d="M260.957 65.3711C260.957 64.0616 262.019 63 263.328 63H372.398C373.708 63 374.769 64.0616 374.769 65.3711V156.895H260.957V65.3711Z" fill="white"/></clipPath><clipPath id="clip5"><path d="M260.957 203.371C260.957 202.062 262.019 201 263.328 201H372.398C373.708 201 374.769 202.062 374.769 203.371V294.895H260.957V203.371Z" fill="white"/></clipPath><clipPath id="clip6"><path d="M139.957 228H253.957V296C253.957 296.552 253.509 297 252.957 297H140.957C140.405 297 139.957 296.552 139.957 296V228Z" fill="white"/></clipPath></defs></svg>',
							'buyUrl' 		=> 'https://smashballoon.com/social-wall/demo/?utm_campaign=facebook-free&utm_source=feed-type&utm_medium=social-wall&utm_content=see-demo',
							'bullets'       => [
								'heading' => __( 'Upgrade to the All Access Bundle and get:', 'custom-facebook-feed' ),
								'content' => [
									__( 'Custom Facebook Feed Pro', 'custom-facebook-feed' ),
									__( 'All Pro Facebook Extensions', 'custom-facebook-feed' ),
									__( 'Custom Twitter Feeds Pro', 'custom-facebook-feed' ),
									__( 'Instagram Feed Pro', 'custom-facebook-feed' ),
									__( 'YouTube Feeds Pro', 'custom-facebook-feed' ),
									__( 'Social Wall Pro', 'custom-facebook-feed' ),
								]
							],
						),
						'date_range' => array(
							'heading' 		=> __( 'Upgrade to filter content by Date Range', 'custom-facebook-feed' ),
							'description' 	=> __( 'Filter posts based on a start and end date. Use relative dates (such as "1 month ago" or "now") or absolute dates (such as 01/01/21) to curate your feeds to specific date ranges.', 'custom-facebook-feed' ),
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'img' 			=> '<svg width="397" height="265" viewBox="0 0 397 265" fill="none" xmlns="http://www.w3.org/2000/svg"> <g filter="url(#datepickerfilter0_dd)"> <g clip-path="url(#datepickerclip0)"> <rect x="76.5" y="36.5" width="180" height="211" rx="4.47155" fill="#0068A0"/> <circle cx="71.5" cy="192.5" r="91" fill="#0096CC"/> </g> <rect x="76.6863" y="36.6863" width="179.627" height="210.627" rx="4.28524" stroke="#F3F4F5" stroke-width="0.372629"/> </g> <g filter="url(#datepickerfilter1_dd)"> <rect x="121.344" y="15.4999" width="208.672" height="208.985" rx="4.47155" transform="rotate(6 121.344 15.4999)" fill="white"/> <path d="M147.462 41.9622C148.474 41.0856 149.756 40.7289 151.308 40.8921C152.861 41.0553 154.038 41.6705 154.84 42.7377C155.648 43.8054 155.963 45.1736 155.788 46.8423C155.613 48.5061 155.02 49.7763 154.008 50.6529C152.997 51.5294 151.717 51.8864 150.17 51.7237C148.617 51.5606 147.437 50.9451 146.63 49.8774C145.828 48.8101 145.514 47.4446 145.689 45.7808C145.864 44.1122 146.455 42.8393 147.462 41.9622ZM153.06 43.9075C152.62 43.206 151.972 42.8103 151.116 42.7203C150.26 42.6304 149.541 42.8825 148.961 43.4767C148.385 44.0714 148.039 44.9178 147.924 46.0157C147.809 47.1088 147.971 48.006 148.411 48.7075C148.851 49.4042 149.502 49.7977 150.363 49.8882C151.219 49.9782 151.934 49.7283 152.51 49.1384C153.086 48.5436 153.431 47.6997 153.546 46.6066C153.662 45.5087 153.499 44.609 153.06 43.9075ZM164.051 48.0628L162.085 47.8562C162.066 47.4288 161.935 47.0727 161.691 46.7879C161.452 46.5036 161.12 46.3391 160.694 46.2943C160.167 46.2389 159.729 46.408 159.379 46.8016C159.035 47.1909 158.824 47.7579 158.746 48.5028C158.666 49.2621 158.754 49.8679 159.009 50.3203C159.27 50.7731 159.666 51.0274 160.198 51.0834C160.619 51.1276 160.975 51.0452 161.266 50.8361C161.557 50.6223 161.759 50.3061 161.871 49.8876L163.845 50.095C163.695 51.0083 163.272 51.7047 162.576 52.1842C161.88 52.6636 161.022 52.8497 160.002 52.7425C158.822 52.6184 157.926 52.16 157.315 51.3671C156.704 50.5694 156.465 49.5394 156.598 48.2771C156.729 47.034 157.176 46.0859 157.939 45.4326C158.707 44.7749 159.674 44.5074 160.84 44.6299C161.875 44.7387 162.679 45.1117 163.253 45.749C163.831 46.3869 164.097 47.1581 164.051 48.0628ZM166.14 43.5439L168.258 43.7665L168.07 45.5585L169.506 45.7095L169.339 47.3056L167.902 47.1546L167.511 50.8764C167.449 51.4616 167.723 51.7863 168.333 51.8503C168.531 51.8712 168.704 51.8771 168.852 51.8682L168.688 53.428C168.435 53.4601 168.096 53.4538 167.67 53.4091C166.756 53.313 166.12 53.0799 165.763 52.7099C165.411 52.3403 165.275 51.7758 165.354 51.0165L165.784 46.9319L164.688 46.8168L164.856 45.2207L165.951 45.3359L166.14 43.5439ZM176.213 53.3608C175.446 54.0039 174.471 54.2632 173.286 54.1386C172.101 54.0141 171.2 53.5551 170.584 52.7618C169.968 51.9684 169.727 50.9382 169.86 49.671C169.992 48.4183 170.444 47.4682 171.216 46.8207C171.994 46.169 172.968 45.9046 174.138 46.0276C175.313 46.1511 176.211 46.6122 176.831 47.4109C177.452 48.2048 177.697 49.2281 177.565 50.4808C177.431 51.7528 176.981 52.7128 176.213 53.3608ZM173.456 52.5208C173.978 52.5757 174.414 52.4063 174.763 52.0127C175.113 51.6191 175.328 51.0354 175.409 50.2615C175.49 49.4974 175.4 48.884 175.141 48.4215C174.881 47.959 174.49 47.7004 173.968 47.6454C173.446 47.5905 173.007 47.7621 172.652 48.16C172.302 48.5584 172.087 49.1397 172.007 49.9039C171.926 50.6778 172.015 51.2935 172.275 51.7512C172.535 52.2088 172.928 52.4654 173.456 52.5208ZM182.859 55.1009C182.298 55.0419 181.818 54.8619 181.42 54.5608C181.026 54.2602 180.744 53.8688 180.575 53.3864L180.531 53.3819L180.391 54.7168L178.302 54.4972L179.402 44.0282L181.521 44.2509L181.096 48.2919L181.139 48.2965C181.406 47.8551 181.76 47.528 182.203 47.3154C182.647 47.0979 183.144 47.0182 183.695 47.0761C184.677 47.1793 185.411 47.6159 185.898 48.3859C186.389 49.1563 186.569 50.1679 186.437 51.4206C186.306 52.6684 185.922 53.6208 185.286 54.2776C184.65 54.9297 183.841 55.2041 182.859 55.1009ZM182.792 48.6975C182.279 48.6437 181.841 48.8128 181.477 49.2048C181.113 49.5969 180.894 50.1388 180.822 50.8304C180.748 51.5317 180.846 52.1093 181.116 52.5631C181.392 53.0126 181.788 53.2646 182.306 53.319C182.838 53.3749 183.278 53.2133 183.626 52.8342C183.979 52.4508 184.193 51.9036 184.268 51.1926C184.342 50.4864 184.246 49.9091 183.98 49.4606C183.715 49.0073 183.319 48.753 182.792 48.6975ZM191.291 49.4148C190.831 49.3665 190.43 49.4833 190.088 49.7651C189.75 50.0475 189.536 50.4309 189.446 50.9153L192.747 51.2623C192.775 50.7616 192.656 50.3407 192.388 49.9996C192.126 49.6591 191.76 49.4641 191.291 49.4148ZM192.532 53.5869L194.476 53.7913C194.28 54.5433 193.833 55.1198 193.136 55.5208C192.444 55.9175 191.619 56.0655 190.661 55.9649C189.467 55.8393 188.566 55.3828 187.959 54.5953C187.357 53.8083 187.122 52.7909 187.253 51.543C187.384 50.3 187.826 49.3441 188.581 48.6752C189.337 48.0016 190.288 47.725 191.434 47.8455C192.561 47.9639 193.421 48.4113 194.014 49.1876C194.607 49.9639 194.842 50.9445 194.717 52.1295L194.649 52.7752L189.288 52.2117L189.274 52.3423C189.216 52.8985 189.33 53.3677 189.618 53.75C189.91 54.128 190.325 54.3452 190.862 54.4016C191.249 54.4423 191.592 54.3903 191.891 54.2457C192.19 54.0962 192.404 53.8766 192.532 53.5869ZM195.387 56.2929L196.205 48.5156L198.25 48.7306L198.103 50.1308L198.147 50.1354C198.334 49.6563 198.601 49.3029 198.948 49.0753C199.294 48.8477 199.698 48.758 200.157 48.8063C200.379 48.8297 200.577 48.8774 200.751 48.9494L200.555 50.8139C200.365 50.7109 200.113 50.6428 199.799 50.6098C199.276 50.5549 198.854 50.6645 198.532 50.9387C198.21 51.2129 198.02 51.6233 197.962 52.1698L197.506 56.5156L195.387 56.2929ZM204.442 49.8435C204.546 48.8519 204.989 48.0794 205.771 47.5259C206.558 46.968 207.522 46.7491 208.664 46.8691C209.762 46.9845 210.627 47.3639 211.258 48.0073C211.89 48.6459 212.16 49.4053 212.068 50.2856C212.035 50.5999 211.954 50.8996 211.827 51.1844C211.704 51.465 211.524 51.7418 211.285 52.015C211.051 52.2839 210.818 52.5259 210.585 52.741C210.352 52.9513 210.052 53.2058 209.683 53.5044L206.871 55.6954L206.866 55.739L211.589 56.2354L211.406 57.9766L203.796 57.1767L203.95 55.7039L208.297 52.3171C208.885 51.841 209.292 51.4511 209.52 51.1474C209.752 50.8442 209.888 50.5112 209.926 50.1484C209.967 49.7518 209.845 49.4016 209.559 49.0977C209.273 48.7938 208.891 48.6167 208.412 48.5664C207.904 48.513 207.467 48.6284 207.1 48.9125C206.733 49.1918 206.524 49.5733 206.473 50.057L206.469 50.0932L204.438 49.8797L204.442 49.8435ZM220.047 57.6304C219.194 58.5335 218.109 58.9158 216.794 58.7776C215.478 58.6393 214.495 58.042 213.843 56.9857C213.192 55.9246 212.955 54.5452 213.133 52.8475C213.311 51.1595 213.828 49.8691 214.685 48.9762C215.547 48.079 216.633 47.6993 217.944 47.8371C219.255 47.9749 220.236 48.5695 220.888 49.6209C221.541 50.6724 221.778 52.0421 221.601 53.7301C221.423 55.4229 220.905 56.723 220.047 57.6304ZM215.601 55.8869C215.891 56.5775 216.35 56.9558 216.978 57.0219C217.607 57.088 218.132 56.8155 218.553 56.2045C218.975 55.5887 219.248 54.6883 219.373 53.5033C219.496 52.328 219.413 51.3999 219.123 50.719C218.838 50.0338 218.383 49.6584 217.759 49.5928C217.135 49.5272 216.61 49.7997 216.184 50.4102C215.758 51.0207 215.484 51.9111 215.361 53.0816C215.237 54.2617 215.317 55.1968 215.601 55.8869ZM222.971 51.791C223.075 50.7994 223.518 50.0269 224.3 49.4734C225.087 48.9155 226.052 48.6966 227.193 48.8166C228.291 48.932 229.156 49.3114 229.787 49.9548C230.42 50.5934 230.689 51.3528 230.597 52.2331C230.564 52.5474 230.483 52.8471 230.356 53.1319C230.233 53.4125 230.053 53.6893 229.814 53.9625C229.58 54.2314 229.347 54.4733 229.114 54.6885C228.882 54.8988 228.581 55.1533 228.212 55.4519L225.4 57.6429L225.395 57.6865L230.118 58.1829L229.935 59.9241L222.325 59.1242L222.48 57.6514L226.826 54.2646C227.414 53.7885 227.822 53.3986 228.049 53.0949C228.281 52.7917 228.417 52.4587 228.455 52.0959C228.497 51.6993 228.374 51.3491 228.088 51.0452C227.803 50.7413 227.42 50.5642 226.941 50.5139C226.434 50.4605 225.996 50.5759 225.629 50.86C225.262 51.1393 225.053 51.5208 225.002 52.0045L224.998 52.0407L222.967 51.8272L222.971 51.791ZM238.576 59.5779C237.723 60.4809 236.639 60.8633 235.323 60.7251C234.008 60.5868 233.024 59.9895 232.372 58.9332C231.721 57.8721 231.484 56.4927 231.663 54.795C231.84 53.107 232.357 51.8166 233.214 50.9237C234.076 50.0265 235.162 49.6468 236.473 49.7846C237.784 49.9223 238.765 50.517 239.418 51.5684C240.07 52.6199 240.307 53.9896 240.13 55.6776C239.952 57.3704 239.434 58.6705 238.576 59.5779ZM234.131 57.8344C234.42 58.525 234.879 58.9033 235.508 58.9694C236.136 59.0354 236.661 58.763 237.083 58.152C237.504 57.5362 237.777 56.6357 237.902 55.4508C238.025 54.2755 237.942 53.3474 237.652 52.6665C237.367 51.9813 236.912 51.6059 236.288 51.5403C235.665 51.4747 235.14 51.7472 234.713 52.3577C234.287 52.9681 234.013 53.8586 233.89 55.0291C233.766 56.2092 233.846 57.1443 234.131 57.8344Z" fill="#141B38"/> <path d="M282.968 56.2178L278.32 59.9818L282.084 64.6299" stroke="#141B38" stroke-width="1.49526" stroke-linecap="round" stroke-linejoin="round"/> <path d="M292.334 57.2023L296.098 61.8504L291.45 65.6144" stroke="#141B38" stroke-width="1.49526" stroke-linecap="round" stroke-linejoin="round"/> <g clip-path="url(#datepickerclip1)"> <path d="M142.631 78.2622L141.951 84.7307L142.88 84.8283L143.372 80.1443L143.432 80.1506L144.849 85.0256L145.619 85.1066L148.018 80.6358L148.078 80.6421L147.586 85.3229L148.514 85.4205L149.194 78.9521L148.01 78.8276L145.422 83.6393L145.346 83.6314L143.816 78.3867L142.631 78.2622ZM151.891 85.8744C153.258 86.0181 154.257 85.1108 154.415 83.6106C154.574 82.1008 153.785 81.0057 152.418 80.8619C151.05 80.7182 150.051 81.6255 149.892 83.1352C149.735 84.6355 150.523 85.7306 151.891 85.8744ZM151.977 85.0819C151.083 84.988 150.749 84.164 150.846 83.2323C150.944 82.3037 151.444 81.5579 152.338 81.6519C153.225 81.7452 153.559 82.5786 153.462 83.5072C153.364 84.4389 152.865 85.1752 151.977 85.0819Z" fill="#141B38"/> <path d="M168.142 81.7925L170.157 82.0043L169.566 87.6326L170.538 87.7349L171.13 82.1066L173.148 82.3187L173.236 81.4785L168.23 80.9524L168.142 81.7925ZM176.212 86.297C176.128 87.1217 175.474 87.4489 174.944 87.3932C174.359 87.3317 173.999 86.866 174.069 86.2059L174.381 83.2338L173.437 83.1346L173.113 86.2204C172.986 87.4237 173.58 88.1185 174.512 88.2164C175.242 88.2931 175.778 87.9599 176.057 87.4655L176.107 87.4708L176.019 88.3109L176.948 88.4085L177.458 83.5572L176.51 83.4576L176.212 86.297Z" fill="#141B38"/> <path d="M192.088 89.9999L193.073 90.1034L194.944 85.4015L194.994 85.4068L195.847 90.3949L196.829 90.4981L199.306 84.2186L198.276 84.1104L196.492 88.9907L196.432 88.9843L195.648 83.8342L194.631 83.7273L192.794 88.5988L192.734 88.5925L192 83.4508L190.974 83.3429L192.088 89.9999ZM201.043 91.0401C202.101 91.1513 202.904 90.7088 203.202 89.9418L202.325 89.6867C202.106 90.1268 201.671 90.3174 201.134 90.2609C200.326 90.1759 199.838 89.5945 199.911 88.657L203.36 89.0195L203.395 88.6847C203.579 86.9318 202.603 86.1362 201.503 86.0207C200.152 85.8786 199.153 86.8146 198.996 88.3054C198.838 89.812 199.612 90.8897 201.043 91.0401ZM199.988 87.9498C200.098 87.2653 200.66 86.7208 201.428 86.8015C202.16 86.8785 202.583 87.4722 202.509 88.2147L199.988 87.9498Z" fill="#141B38"/> <path d="M217.07 86.935L219.085 87.1468L218.494 92.7751L219.466 92.8774L220.058 87.2491L222.076 87.4612L222.165 86.6211L217.158 86.0949L217.07 86.935ZM223.762 90.4166C223.844 89.6428 224.38 89.252 225.052 89.3227C225.703 89.3911 226.048 89.8457 225.973 90.5564L225.661 93.5284L226.605 93.6277L226.93 90.5419C227.057 89.3322 226.459 88.6435 225.461 88.5386C224.706 88.4592 224.216 88.7399 223.934 89.2403L223.874 89.234L224.127 86.8273L223.195 86.7294L222.515 93.1978L223.46 93.2971L223.762 90.4166Z" fill="#141B38"/> <path d="M240.134 95.05L241.11 95.1526L241.407 92.3321L244.154 92.6209L244.242 91.7839L241.495 91.4951L241.702 89.5242L244.737 89.8433L244.825 89.0031L240.814 88.5815L240.134 95.05ZM245.209 95.5834L246.153 95.6826L246.465 92.72C246.531 92.0852 247.069 91.6787 247.739 91.749C247.934 91.7696 248.152 91.8276 248.225 91.8577L248.32 90.9544C248.227 90.9318 248.042 90.9027 247.922 90.8901C247.353 90.8303 246.833 91.1014 246.601 91.6039L246.551 91.5986L246.632 90.828L245.719 90.732L245.209 95.5834Z" fill="#141B38"/> <path d="M264.917 92.8328L265.858 92.9318C265.939 91.8928 265.102 91.0448 263.788 90.9067C262.49 90.7703 261.417 91.408 261.3 92.5229C261.205 93.423 261.794 94.0182 262.804 94.4085L263.546 94.6973C264.219 94.9532 264.728 95.2367 264.668 95.8116C264.602 96.4432 263.955 96.7967 263.124 96.7094C262.372 96.6304 261.782 96.2299 261.799 95.5196L260.82 95.4167C260.76 96.5951 261.591 97.4137 263.04 97.566C264.559 97.7257 265.518 97.0186 265.634 95.9226C265.756 94.7572 264.764 94.1963 263.964 93.9078L263.35 93.6772C262.858 93.4946 262.21 93.187 262.279 92.5683C262.336 92.0187 262.881 91.6641 263.677 91.7477C264.42 91.8257 264.911 92.2286 264.917 92.8328ZM267.937 98.0807C268.739 98.165 269.233 97.805 269.452 97.4607L269.489 97.4647L269.42 98.128L270.342 98.2249L270.681 95.0033C270.829 93.5915 269.747 93.1936 268.976 93.1126C268.098 93.0203 267.252 93.289 266.844 94.1402L267.71 94.4356C267.885 94.1059 268.274 93.8115 268.908 93.8782C269.518 93.9423 269.797 94.2942 269.739 94.8437L269.737 94.8658C269.701 95.2101 269.348 95.1666 268.465 95.176C267.535 95.1868 266.569 95.3279 266.453 96.4302C266.352 97.3841 267.014 97.9837 267.937 98.0807ZM268.222 97.3442C267.688 97.2881 267.328 97.0076 267.377 96.5401C267.43 96.0348 267.897 95.9019 268.451 95.8867C268.762 95.8778 269.497 95.8721 269.647 95.7505L269.582 96.3759C269.521 96.9507 269.008 97.4269 268.222 97.3442Z" fill="#141B38"/> <path d="M287.781 95.2358L288.722 95.3348C288.803 94.2958 287.966 93.4478 286.652 93.3097C285.354 93.1733 284.281 93.811 284.164 94.9259C284.069 95.826 284.658 96.4212 285.667 96.8115L286.41 97.1003C287.082 97.3562 287.592 97.6397 287.532 98.2146C287.465 98.8462 286.818 99.1997 285.988 99.1124C285.236 99.0334 284.646 98.6329 284.663 97.9226L283.684 97.8197C283.624 98.9981 284.454 99.8167 285.904 99.969C287.423 100.129 288.382 99.4216 288.497 98.3256C288.62 97.1602 287.628 96.5993 286.828 96.3108L286.214 96.0802C285.722 95.8976 285.074 95.59 285.142 94.9713C285.2 94.4217 285.745 94.0671 286.541 94.1507C287.283 94.2287 287.774 94.6316 287.781 95.2358ZM292.684 98.539C292.601 99.3637 291.947 99.6909 291.416 99.6351C290.832 99.5737 290.472 99.108 290.542 98.4479L290.854 95.4758L289.91 95.3766L289.585 98.4623C289.459 99.6657 290.053 100.36 290.985 100.458C291.715 100.535 292.251 100.202 292.53 99.7074L292.58 99.7127L292.492 100.553L293.42 100.65L293.93 95.7991L292.983 95.6996L292.684 98.539Z" fill="#141B38"/> <path d="M220.75 109.328L219.799 109.228L218.074 110.113L217.976 111.048L219.63 110.2L219.667 110.204L219.091 115.694L220.07 115.797L220.75 109.328Z" fill="#434960"/> <path d="M239.413 117.829L243.671 118.277L243.759 117.44L240.847 117.134L240.852 117.086L242.279 115.876C243.589 114.804 243.987 114.267 244.063 113.544C244.172 112.505 243.409 111.62 242.19 111.492C240.98 111.365 240.014 112.055 239.89 113.236L240.821 113.334C240.891 112.639 241.388 112.231 242.086 112.305C242.742 112.374 243.199 112.831 243.131 113.475C243.071 114.047 242.687 114.421 241.915 115.084L239.488 117.122L239.413 117.829Z" fill="#8C8F9A"/> <path d="M263.194 120.419C264.515 120.557 265.567 119.87 265.677 118.795C265.766 117.978 265.332 117.338 264.487 117.115L264.492 117.065C265.186 116.962 265.681 116.478 265.754 115.751C265.857 114.8 265.163 113.907 263.918 113.776C262.731 113.651 261.701 114.268 261.559 115.316L262.504 115.415C262.588 114.824 263.185 114.523 263.82 114.589C264.48 114.659 264.871 115.105 264.805 115.702C264.742 116.328 264.197 116.686 263.48 116.61L262.933 116.553L262.85 117.349L263.396 117.406C264.293 117.5 264.763 118.01 264.695 118.66C264.629 119.289 264.038 119.658 263.276 119.578C262.575 119.504 262.067 119.087 262.089 118.508L261.097 118.404C261.028 119.463 261.887 120.281 263.194 120.419Z" fill="#8C8F9A"/> <path d="M283.882 121.226L286.949 121.548L286.816 122.812L287.748 122.91L287.88 121.646L288.736 121.736L288.823 120.909L287.967 120.819L288.428 116.441L287.224 116.315L283.965 120.437L283.882 121.226ZM287.042 120.722L284.992 120.506L284.997 120.456L287.329 117.501L287.38 117.506L287.042 120.722Z" fill="#8C8F9A"/> <rect x="183.387" y="120.225" width="90" height="21" rx="10.5" transform="rotate(6 183.387 120.225)" fill="#FCE1D5"/> <circle cx="192.255" cy="131.924" r="10.4336" transform="rotate(6 192.255 131.924)" fill="#E34F0E"/> <path d="M143.149 130.657C144.451 130.794 145.476 129.972 145.605 128.711C145.74 127.461 144.964 126.46 143.785 126.336C143.305 126.286 142.841 126.422 142.562 126.642L142.524 126.638L142.92 124.85L145.664 125.138L145.752 124.301L142.196 123.927L141.49 127.149L142.37 127.375C142.636 127.183 143.088 127.08 143.479 127.121C144.246 127.208 144.742 127.842 144.659 128.631C144.577 129.408 143.978 129.914 143.236 129.836C142.61 129.77 142.156 129.32 142.164 128.759L141.217 128.659C141.146 129.693 141.962 130.532 143.149 130.657Z" fill="#8C8F9A"/> <path d="M167.478 133.214C168.829 133.368 169.828 132.49 169.955 131.248C170.088 130.01 169.282 129.028 168.16 128.911C167.478 128.839 166.853 129.105 166.464 129.617L166.416 129.612C166.574 128.143 167.222 127.33 168.169 127.43C168.791 127.495 169.143 127.915 169.219 128.466L170.183 128.567C170.142 127.529 169.415 126.698 168.259 126.577C166.755 126.419 165.681 127.612 165.445 129.861C165.192 132.242 166.329 133.083 167.478 133.214ZM167.561 132.392C166.81 132.313 166.326 131.624 166.403 130.897C166.482 130.174 167.123 129.603 167.869 129.681C168.608 129.759 169.088 130.413 169.006 131.161C168.929 131.926 168.3 132.47 167.561 132.392Z" fill="#8C8F9A"/> <path d="M190.093 135.501L191.309 135.629L194.632 130.451L194.737 129.449L190.331 128.986L190.228 129.966L193.421 130.301L193.417 130.345L190.093 135.501Z" fill="white"/> <circle cx="261.872" cy="139.241" r="10.4336" transform="rotate(6 261.872 139.241)" fill="#E34F0E"/> <path d="M216.708 138.388C218.053 138.53 219.084 137.865 219.201 136.817C219.28 136.002 218.781 135.256 218.071 135.051L218.075 135.013C218.719 134.927 219.208 134.382 219.288 133.684C219.385 132.695 218.591 131.867 217.406 131.743C216.209 131.617 215.26 132.261 215.163 133.251C215.083 133.945 215.447 134.583 216.073 134.802L216.069 134.84C215.319 134.893 214.675 135.518 214.596 136.333C214.48 137.381 215.35 138.246 216.708 138.388ZM216.792 137.589C215.983 137.504 215.517 137.021 215.591 136.374C215.656 135.701 216.262 135.279 217.026 135.359C217.778 135.438 218.283 135.977 218.219 136.65C218.145 137.297 217.591 137.673 216.792 137.589ZM217.108 134.579C216.455 134.511 216.034 134.048 216.105 133.433C216.162 132.826 216.653 132.472 217.322 132.542C217.979 132.611 218.386 133.059 218.328 133.666C218.257 134.282 217.753 134.647 217.108 134.579Z" fill="#E34F0E"/> <path d="M239.593 134.074C238.236 133.919 237.246 134.802 237.117 136.027C236.99 137.263 237.791 138.247 238.912 138.365C239.597 138.437 240.22 138.164 240.609 137.656L240.659 137.661C240.5 139.148 239.849 139.958 238.902 139.858C238.283 139.793 237.924 139.372 237.856 138.806L236.893 138.705C236.925 139.766 237.656 140.59 238.812 140.711C240.315 140.869 241.392 139.676 241.63 137.412C241.876 135.049 240.744 134.205 239.593 134.074ZM239.509 134.896C240.258 134.975 240.744 135.668 240.669 136.378C240.597 137.099 239.946 137.669 239.207 137.591C238.465 137.513 237.99 136.863 238.066 136.114C238.145 135.362 238.767 134.818 239.509 134.896Z" fill="#E34F0E"/> <path d="M260.257 136.336L259.142 136.219L257.426 137.082L257.313 138.156L258.928 137.349L258.966 137.353L258.406 142.681L259.577 142.804L260.257 136.336ZM263.598 143.351C265.158 143.518 266.219 142.385 266.442 140.262C266.664 138.152 265.854 136.835 264.3 136.671C262.746 136.508 261.683 137.626 261.458 139.738C261.235 141.858 262.037 143.187 263.598 143.351ZM263.702 142.363C262.896 142.278 262.476 141.416 262.642 139.863C262.808 138.322 263.395 137.563 264.197 137.647C265.003 137.732 265.42 138.596 265.261 140.138C265.097 141.692 264.51 142.448 263.702 142.363Z" fill="white"/> <path d="M283.533 138.783L282.583 138.683L280.858 139.568L280.76 140.503L282.414 139.655L282.451 139.659L281.874 145.148L282.854 145.251L283.533 138.783ZM287.737 139.224L286.787 139.124L285.062 140.01L284.964 140.945L286.617 140.097L286.655 140.101L286.078 145.59L287.057 145.693L287.737 139.224Z" fill="#8C8F9A"/> <path d="M139.708 146.522L138.757 146.422L137.032 147.307L136.934 148.242L138.588 147.394L138.626 147.398L138.049 152.887L139.028 152.99L139.708 146.522ZM140.778 153.174L145.035 153.622L145.123 152.785L142.211 152.478L142.216 152.431L143.643 151.221C144.953 150.148 145.351 149.612 145.427 148.889C145.537 147.85 144.774 146.965 143.555 146.837C142.345 146.709 141.378 147.4 141.254 148.581L142.186 148.679C142.256 147.984 142.752 147.576 143.45 147.649C144.107 147.719 144.564 148.175 144.496 148.82C144.436 149.391 144.051 149.766 143.279 150.429L140.852 152.467L140.778 153.174Z" fill="#8C8F9A"/> <path d="M164.026 149.078L163.075 148.978L161.35 149.863L161.252 150.798L162.906 149.95L162.944 149.954L162.367 155.443L163.346 155.546L164.026 149.078ZM167.338 156.055C168.659 156.194 169.711 155.506 169.821 154.432C169.91 153.615 169.476 152.975 168.631 152.752L168.636 152.701C169.33 152.599 169.825 152.114 169.898 151.388C170.001 150.437 169.307 149.543 168.062 149.413C166.875 149.288 165.845 149.905 165.703 150.953L166.648 151.052C166.732 150.461 167.329 150.16 167.964 150.226C168.624 150.296 169.015 150.742 168.949 151.339C168.886 151.965 168.341 152.322 167.624 152.247L167.077 152.19L166.994 152.985L167.54 153.043C168.437 153.137 168.907 153.646 168.839 154.297C168.773 154.926 168.182 155.295 167.42 155.215C166.719 155.141 166.211 154.723 166.233 154.145L165.242 154.04C165.172 155.1 166.031 155.918 167.338 156.055Z" fill="#8C8F9A"/> <path d="M188.258 151.625L187.307 151.525L185.582 152.41L185.484 153.345L187.138 152.497L187.176 152.501L186.599 157.99L187.578 158.093L188.258 151.625ZM189.325 156.999L192.391 157.322L192.259 158.585L193.19 158.683L193.323 157.42L194.179 157.509L194.266 156.682L193.41 156.592L193.87 152.214L192.667 152.088L189.408 156.21L189.325 156.999ZM192.485 156.495L190.435 156.279L190.44 156.229L192.772 153.274L192.823 153.279L192.485 156.495Z" fill="#8C8F9A"/> <path d="M213.424 154.27L212.473 154.17L210.748 155.055L210.65 155.99L212.304 155.142L212.342 155.146L211.765 160.635L212.744 160.738L213.424 154.27ZM216.619 161.235C217.921 161.372 218.946 160.55 219.075 159.29C219.21 158.039 218.434 157.038 217.255 156.914C216.775 156.864 216.311 157 216.032 157.22L215.994 157.216L216.39 155.428L219.134 155.716L219.222 154.879L215.666 154.505L214.96 157.727L215.84 157.953C216.106 157.761 216.558 157.658 216.949 157.699C217.716 157.786 218.212 158.42 218.129 159.209C218.047 159.986 217.448 160.492 216.706 160.414C216.08 160.348 215.626 159.898 215.634 159.337L214.687 159.237C214.616 160.271 215.432 161.11 216.619 161.235Z" fill="#8C8F9A"/> <path d="M235.588 156.599L234.638 156.499L232.913 157.385L232.815 158.32L234.469 157.472L234.506 157.476L233.929 162.965L234.909 163.068L235.588 156.599ZM238.907 163.578C240.258 163.732 241.257 162.854 241.384 161.612C241.518 160.374 240.711 159.392 239.59 159.274C238.907 159.203 238.282 159.469 237.893 159.981L237.846 159.976C238.003 158.507 238.651 157.694 239.598 157.794C240.22 157.859 240.572 158.279 240.649 158.83L241.612 158.931C241.571 157.892 240.844 157.062 239.688 156.941C238.184 156.783 237.111 157.976 236.874 160.225C236.621 162.606 237.759 163.447 238.907 163.578ZM238.99 162.756C238.239 162.677 237.756 161.988 237.832 161.261C237.911 160.538 238.552 159.967 239.298 160.045C240.037 160.123 240.517 160.777 240.435 161.525C240.358 162.29 239.73 162.834 238.99 162.756Z" fill="#8C8F9A"/> <path d="M257.522 158.904L256.572 158.805L254.847 159.69L254.748 160.625L256.402 159.777L256.44 159.781L255.863 165.27L256.842 165.373L257.522 158.904ZM258.81 165.58L259.83 165.687L263.233 160.38L263.324 159.514L259.048 159.065L258.96 159.902L262.226 160.245L262.221 160.292L258.81 165.58Z" fill="#8C8F9A"/> <path d="M280.45 161.314L279.499 161.214L277.774 162.1L277.676 163.035L279.33 162.186L279.368 162.19L278.791 167.68L279.77 167.783L280.45 161.314ZM283.696 168.285C285.041 168.426 286.072 167.762 286.189 166.714C286.268 165.898 285.769 165.153 285.059 164.947L285.063 164.909C285.707 164.824 286.196 164.278 286.275 163.581C286.373 162.591 285.579 161.764 284.394 161.639C283.197 161.514 282.248 162.158 282.151 163.147C282.071 163.841 282.435 164.48 283.06 164.699L283.056 164.737C282.307 164.789 281.663 165.414 281.584 166.23C281.467 167.278 282.338 168.142 283.696 168.285ZM283.78 167.486C282.971 167.401 282.505 166.917 282.579 166.271C282.643 165.597 283.25 165.176 284.014 165.256C284.766 165.335 285.271 165.873 285.207 166.547C285.133 167.194 284.579 167.57 283.78 167.486ZM284.096 164.476C283.442 164.407 283.022 163.944 283.093 163.329C283.15 162.722 283.641 162.368 284.31 162.439C284.967 162.508 285.374 162.956 285.316 163.563C285.245 164.178 284.74 164.543 284.096 164.476Z" fill="#8C8F9A"/> <path d="M137.233 169.117L136.282 169.017L134.557 169.902L134.459 170.837L136.113 169.989L136.151 169.993L135.574 175.483L136.553 175.585L137.233 169.117ZM141.143 169.439C139.786 169.283 138.796 170.166 138.667 171.391C138.541 172.627 139.341 173.611 140.462 173.729C141.147 173.801 141.77 173.528 142.159 173.02L142.209 173.025C142.05 174.512 141.4 175.322 140.452 175.222C139.833 175.157 139.475 174.737 139.407 174.171L138.443 174.069C138.476 175.13 139.206 175.954 140.362 176.075C141.866 176.233 142.943 175.04 143.181 172.776C143.426 170.413 142.295 169.569 141.143 169.439ZM141.06 170.26C141.808 170.339 142.294 171.032 142.22 171.742C142.147 172.463 141.496 173.033 140.757 172.955C140.015 172.877 139.541 172.227 139.616 171.478C139.695 170.727 140.318 170.182 141.06 170.26Z" fill="#8C8F9A"/> <path d="M157.89 177.828L162.148 178.275L162.236 177.439L159.324 177.132L159.329 177.085L160.755 175.875C162.066 174.802 162.464 174.266 162.54 173.543C162.649 172.504 161.886 171.619 160.667 171.491C159.457 171.363 158.491 172.054 158.367 173.235L159.298 173.333C159.368 172.638 159.864 172.23 160.562 172.303C161.219 172.372 161.676 172.829 161.608 173.474C161.548 174.045 161.164 174.42 160.392 175.083L157.965 177.121L157.89 177.828ZM165.585 178.745C167.079 178.902 168.082 177.775 168.305 175.656C168.526 173.552 167.767 172.237 166.286 172.081C164.801 171.925 163.789 173.051 163.564 175.158C163.342 177.274 164.089 178.585 165.585 178.745ZM165.674 177.902C164.799 177.81 164.364 176.877 164.534 175.26C164.706 173.649 165.327 172.817 166.199 172.909C167.067 173 167.505 173.943 167.335 175.554C167.166 177.171 166.546 177.994 165.674 177.902Z" fill="#8C8F9A"/> <path d="M183.117 180.48L187.374 180.927L187.462 180.09L184.55 179.784L184.555 179.737L185.982 178.526C187.292 177.454 187.69 176.918 187.766 176.194C187.876 175.155 187.113 174.27 185.894 174.142C184.684 174.015 183.717 174.705 183.593 175.887L184.525 175.984C184.595 175.289 185.091 174.882 185.789 174.955C186.446 175.024 186.903 175.481 186.835 176.125C186.775 176.697 186.391 177.072 185.618 177.734L183.191 179.772L183.117 180.48ZM191.705 174.842L190.755 174.742L189.03 175.628L188.932 176.563L190.586 175.715L190.623 175.719L190.046 181.208L191.026 181.311L191.705 174.842Z" fill="#8C8F9A"/> <path d="M207.137 183.004L211.395 183.452L211.483 182.615L208.571 182.308L208.576 182.261L210.003 181.051C211.313 179.978 211.711 179.442 211.787 178.719C211.896 177.68 211.133 176.795 209.914 176.667C208.704 176.539 207.738 177.23 207.614 178.411L208.545 178.509C208.615 177.814 209.112 177.406 209.81 177.479C210.466 177.549 210.923 178.005 210.855 178.65C210.795 179.221 210.411 179.596 209.639 180.259L207.212 182.297L207.137 183.004ZM212.592 183.577L216.849 184.025L216.937 183.188L214.025 182.882L214.03 182.834L215.457 181.624C216.767 180.551 217.165 180.015 217.241 179.292C217.351 178.253 216.588 177.368 215.369 177.24C214.159 177.113 213.192 177.803 213.068 178.984L214 179.082C214.07 178.387 214.566 177.979 215.264 178.053C215.921 178.122 216.378 178.579 216.31 179.223C216.25 179.795 215.866 180.169 215.093 180.832L212.666 182.87L212.592 183.577Z" fill="#8C8F9A"/> <path d="M229.418 185.346L233.675 185.793L233.763 184.956L230.851 184.65L230.856 184.603L232.283 183.393C233.593 182.32 233.992 181.784 234.068 181.061C234.177 180.021 233.414 179.137 232.195 179.008C230.985 178.881 230.018 179.572 229.894 180.753L230.826 180.851C230.896 180.156 231.392 179.748 232.09 179.821C232.747 179.89 233.204 180.347 233.136 180.991C233.076 181.563 232.692 181.938 231.919 182.601L229.492 184.638L229.418 185.346ZM237.115 186.244C238.435 186.383 239.488 185.695 239.598 184.621C239.687 183.803 239.253 183.164 238.408 182.941L238.413 182.89C239.107 182.788 239.602 182.303 239.675 181.576C239.778 180.626 239.083 179.732 237.839 179.602C236.651 179.477 235.622 180.094 235.48 181.142L236.424 181.241C236.509 180.65 237.106 180.348 237.741 180.415C238.401 180.485 238.791 180.931 238.725 181.528C238.663 182.153 238.117 182.511 237.401 182.436L236.854 182.378L236.77 183.174L237.317 183.232C238.214 183.326 238.684 183.835 238.616 184.486C238.55 185.115 237.958 185.483 237.197 185.403C236.496 185.33 235.987 184.912 236.01 184.333L235.018 184.229C234.948 185.288 235.808 186.107 237.115 186.244Z" fill="#8C8F9A"/> <path d="M251.307 187.647L255.565 188.094L255.653 187.257L252.741 186.951L252.746 186.904L254.173 185.693C255.483 184.621 255.881 184.085 255.957 183.361C256.066 182.322 255.303 181.437 254.084 181.309C252.874 181.182 251.908 181.872 251.784 183.054L252.715 183.152C252.785 182.456 253.282 182.049 253.98 182.122C254.637 182.191 255.093 182.648 255.025 183.292C254.965 183.864 254.581 184.239 253.809 184.902L251.382 186.939L251.307 187.647ZM256.62 186.928L259.687 187.25L259.554 188.513L260.486 188.611L260.618 187.348L261.474 187.438L261.561 186.61L260.705 186.52L261.166 182.143L259.962 182.016L256.703 186.138L256.62 186.928ZM259.78 186.423L257.73 186.208L257.735 186.157L260.067 183.203L260.118 183.208L259.78 186.423Z" fill="#8C8F9A"/> <path d="M274.181 190.051L278.439 190.498L278.527 189.661L275.615 189.355L275.619 189.308L277.046 188.098C278.357 187.025 278.755 186.489 278.831 185.766C278.94 184.726 278.177 183.842 276.958 183.713C275.748 183.586 274.782 184.277 274.657 185.458L275.589 185.556C275.659 184.861 276.155 184.453 276.853 184.526C277.51 184.595 277.967 185.052 277.899 185.696C277.839 186.268 277.455 186.643 276.683 187.306L274.255 189.343L274.181 190.051ZM281.761 190.937C283.063 191.074 284.088 190.252 284.217 188.992C284.352 187.741 283.576 186.74 282.398 186.616C281.918 186.566 281.453 186.702 281.174 186.922L281.136 186.918L281.532 185.13L284.277 185.418L284.365 184.581L280.808 184.208L280.102 187.429L280.982 187.655C281.248 187.463 281.7 187.36 282.092 187.402C282.858 187.489 283.354 188.122 283.271 188.911C283.189 189.688 282.59 190.194 281.848 190.116C281.222 190.05 280.768 189.6 280.776 189.039L279.829 188.939C279.758 189.973 280.574 190.812 281.761 190.937Z" fill="#8C8F9A"/> <path d="M130.912 197.848L135.17 198.296L135.258 197.459L132.346 197.153L132.351 197.105L133.778 195.895C135.088 194.822 135.486 194.286 135.562 193.563C135.671 192.524 134.908 191.639 133.689 191.511C132.48 191.384 131.513 192.074 131.389 193.255L132.32 193.353C132.39 192.658 132.887 192.25 133.585 192.324C134.242 192.393 134.698 192.849 134.63 193.494C134.57 194.065 134.186 194.44 133.414 195.103L130.987 197.141L130.912 197.848ZM138.616 198.747C139.966 198.902 140.966 198.023 141.093 196.782C141.226 195.544 140.419 194.562 139.298 194.444C138.616 194.372 137.991 194.639 137.602 195.15L137.554 195.145C137.712 193.677 138.359 192.864 139.307 192.963C139.929 193.029 140.281 193.449 140.357 194L141.32 194.101C141.28 193.062 140.552 192.232 139.396 192.111C137.893 191.953 136.819 193.146 136.583 195.395C136.329 197.776 137.467 198.617 138.616 198.747ZM138.699 197.926C137.947 197.847 137.464 197.157 137.541 196.431C137.62 195.708 138.261 195.137 139.006 195.215C139.745 195.293 140.226 195.947 140.144 196.695C140.067 197.46 139.438 198.003 138.699 197.926Z" fill="#8C8F9A"/> <path d="M155.807 200.465L160.065 200.912L160.153 200.075L157.241 199.769L157.246 199.722L158.672 198.512C159.983 197.439 160.381 196.903 160.457 196.18C160.566 195.14 159.803 194.256 158.584 194.127C157.374 194 156.408 194.691 156.283 195.872L157.215 195.97C157.285 195.275 157.781 194.867 158.479 194.94C159.136 195.009 159.593 195.466 159.525 196.11C159.465 196.682 159.081 197.057 158.309 197.72L155.881 199.757L155.807 200.465ZM161.48 201.061L162.5 201.168L165.903 195.861L165.994 194.996L161.717 194.546L161.629 195.383L164.895 195.726L164.89 195.774L161.48 201.061Z" fill="#8C8F9A"/> <path d="M179.848 202.991L184.105 203.439L184.193 202.602L181.281 202.296L181.286 202.249L182.713 201.038C184.023 199.966 184.421 199.429 184.497 198.706C184.607 197.667 183.844 196.782 182.625 196.654C181.415 196.527 180.448 197.217 180.324 198.398L181.256 198.496C181.326 197.801 181.822 197.394 182.52 197.467C183.177 197.536 183.633 197.993 183.566 198.637C183.506 199.209 183.121 199.583 182.349 200.246L179.922 202.284L179.848 202.991ZM187.479 203.883C188.824 204.024 189.855 203.36 189.972 202.312C190.051 201.496 189.551 200.751 188.842 200.545L188.846 200.508C189.49 200.422 189.979 199.876 190.058 199.179C190.156 198.19 189.361 197.362 188.177 197.238C186.98 197.112 186.031 197.756 185.933 198.745C185.854 199.44 186.218 200.078 186.843 200.297L186.839 200.335C186.09 200.387 185.446 201.012 185.367 201.828C185.25 202.876 186.121 203.74 187.479 203.883ZM187.563 203.084C186.754 202.999 186.288 202.516 186.362 201.869C186.426 201.195 187.033 200.774 187.797 200.854C188.549 200.933 189.054 201.472 188.99 202.145C188.915 202.792 188.362 203.168 187.563 203.084ZM187.879 200.074C187.225 200.005 186.804 199.543 186.875 198.927C186.933 198.32 187.424 197.966 188.093 198.037C188.75 198.106 189.156 198.554 189.099 199.161C189.028 199.776 188.523 200.142 187.879 200.074Z" fill="#8C8F9A"/> <path d="M204.658 205.599L208.916 206.047L209.004 205.21L206.092 204.904L206.097 204.856L207.524 203.646C208.834 202.573 209.232 202.037 209.308 201.314C209.417 200.275 208.654 199.39 207.435 199.262C206.226 199.135 205.259 199.825 205.135 201.006L206.067 201.104C206.136 200.409 206.633 200.001 207.331 200.075C207.988 200.144 208.444 200.6 208.376 201.245C208.316 201.816 207.932 202.191 207.16 202.854L204.733 204.892L204.658 205.599ZM212.953 199.842C211.596 199.686 210.606 200.569 210.477 201.794C210.351 203.03 211.151 204.014 212.272 204.132C212.958 204.204 213.58 203.931 213.969 203.423L214.02 203.428C213.86 204.915 213.21 205.725 212.262 205.626C211.643 205.561 211.285 205.14 211.217 204.574L210.253 204.472C210.286 205.533 211.017 206.357 212.173 206.478C213.676 206.636 214.753 205.444 214.991 203.179C215.236 200.816 214.105 199.972 212.953 199.842ZM212.87 200.663C213.618 200.742 214.104 201.435 214.03 202.145C213.957 202.866 213.307 203.436 212.567 203.359C211.825 203.281 211.351 202.63 211.426 201.881C211.505 201.13 212.128 200.585 212.87 200.663Z" fill="#8C8F9A"/> <path d="M229.142 208.262C230.463 208.4 231.515 207.713 231.625 206.639C231.714 205.821 231.28 205.181 230.435 204.958L230.44 204.908C231.134 204.805 231.629 204.321 231.702 203.594C231.805 202.644 231.11 201.75 229.866 201.619C228.678 201.494 227.649 202.111 227.507 203.159L228.451 203.259C228.536 202.667 229.133 202.366 229.768 202.433C230.428 202.502 230.818 202.949 230.752 203.545C230.69 204.171 230.145 204.529 229.428 204.453L228.881 204.396L228.798 205.192L229.344 205.249C230.241 205.344 230.711 205.853 230.643 206.503C230.577 207.132 229.985 207.501 229.224 207.421C228.523 207.347 228.015 206.93 228.037 206.351L227.045 206.247C226.975 207.306 227.835 208.124 229.142 208.262ZM234.873 208.883C236.367 209.04 237.37 207.913 237.593 205.794C237.814 203.69 237.055 202.375 235.573 202.219C234.089 202.063 233.076 203.189 232.852 205.295C232.629 207.412 233.376 208.723 234.873 208.883ZM234.961 208.04C234.087 207.948 233.651 207.014 233.821 205.397C233.994 203.787 234.615 202.955 235.486 203.046C236.355 203.138 236.792 204.081 236.623 205.692C236.453 207.309 235.833 208.132 234.961 208.04Z" fill="#8C8F9A"/> <path d="M252.514 210.718C253.834 210.857 254.886 210.17 254.996 209.095C255.085 208.278 254.651 207.638 253.806 207.415L253.811 207.365C254.505 207.262 255 206.777 255.073 206.051C255.176 205.1 254.482 204.207 253.237 204.076C252.05 203.951 251.02 204.568 250.878 205.616L251.823 205.715C251.907 205.124 252.504 204.823 253.139 204.889C253.799 204.959 254.19 205.405 254.124 206.002C254.061 206.628 253.516 206.985 252.799 206.91L252.252 206.853L252.169 207.649L252.715 207.706C253.612 207.8 254.082 208.31 254.014 208.96C253.948 209.589 253.357 209.958 252.596 209.878C251.894 209.804 251.386 209.386 251.408 208.808L250.417 208.703C250.347 209.763 251.206 210.581 252.514 210.718ZM259.137 204.785L258.187 204.685L256.462 205.571L256.364 206.506L258.017 205.658L258.055 205.662L257.478 211.151L258.458 211.254L259.137 204.785Z" fill="#8C8F9A"/> </g> <rect x="121.51" y="15.7046" width="208.299" height="208.612" rx="4.28524" transform="rotate(6 121.51 15.7046)" stroke="#F3F4F5" stroke-width="0.372629"/> </g> <defs> <filter id="datepickerfilter0_dd" x="65.5" y="29.5" width="202" height="233" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="4"/> <feGaussianBlur stdDeviation="5.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.09 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow" result="shape"/> </filter> <filter id="datepickerfilter1_dd" x="88.5" y="8.49988" width="251.373" height="251.652" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="4"/> <feGaussianBlur stdDeviation="5.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.09 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow" result="shape"/> </filter> <clipPath id="datepickerclip0"> <rect x="76.5" y="36.5" width="180" height="211" rx="4.47155" fill="white"/> </clipPath> <clipPath id="datepickerclip1"> <rect width="155.013" height="124.458" fill="white" transform="translate(141.996 75.6855) rotate(6)"/> </clipPath> </defs> </svg>',
							'buyUrl' 		=> 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=customizer&utm_medium=filters&utm_content=date-range'
						),

						//Fake Extensions
						'lightbox' => array(
							'heading' 		=> __( 'Upgrade to Pro to enable the popup Lightbox', 'custom-facebook-feed' ),
							'description' 	=> __( 'Display photos and videos in your feed and allow visitors to view them in a beautiful full size lightbox, keeping them on your site for longer to discover more of your content.', 'custom-facebook-feed' ),
							'img' 			=> '<svg width="353" height="205" viewBox="0 0 353 205" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M28.6018 102.122L26.2658 100.019L17.3153 109.959L27.2558 118.91L29.3591 116.574L21.7704 109.726L28.6018 102.122Z" fill="#8C8F9A"/> <path d="M325.2 85.8093L323.097 88.1453L330.686 94.9932L323.854 102.597L326.19 104.7L335.141 94.7597L325.2 85.8093Z" fill="#8C8F9A"/> <g clip-path="url(#lightboxclip0)" filter="url(#lightboxfilter0_d)"> <rect width="261.925" height="173.162" transform="translate(40.6836 18.392) rotate(-3)" fill="white"/> <rect x="188.055" y="50.0114" width="93.129" height="5.82056" rx="1.45514" transform="rotate(-3 188.055 50.0114)" fill="#D0D1D7"/> <rect x="188.738" y="63.0895" width="53.8402" height="5.82056" rx="1.45514" transform="rotate(-3 188.738 63.0895)" fill="#D0D1D7"/> <circle cx="195.707" cy="29.2103" r="8.73084" transform="rotate(-3 195.707 29.2103)" fill="#DCDDE1"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M291.128 126.209C291.086 125.406 290.402 124.79 289.599 124.832L199.504 129.553C198.701 129.595 198.085 130.28 198.127 131.083L199.193 151.427C199.235 152.229 199.92 152.846 200.722 152.804L285.292 148.372C285.578 148.357 285.863 148.427 286.11 148.573L290.161 150.978C291.16 151.571 292.418 150.811 292.357 149.65L291.128 126.209ZM199.955 165.958C199.913 165.156 200.529 164.471 201.332 164.429L291.427 159.707C292.229 159.665 292.914 160.282 292.956 161.084L294.022 181.428C294.064 182.231 293.448 182.916 292.645 182.958L208.076 187.39C207.789 187.405 207.513 187.504 207.283 187.676L203.506 190.49C202.574 191.185 201.244 190.56 201.183 189.4L199.955 165.958Z" fill="url(#lightboxpaint0_linear)"/> <g clip-path="url(#lightboxclip1)"> <rect x="40.6836" y="18.3918" width="128.052" height="173.162" transform="rotate(-3 40.6836 18.3918)" fill="#E34F0E"/> <rect x="98.9062" y="45.6586" width="97.4944" height="161.521" rx="14.5514" transform="rotate(2.99107 98.9062 45.6586)" fill="#DCDDE1"/> <circle cx="62.7523" cy="161.492" r="80.0327" transform="rotate(-3 62.7523 161.492)" fill="#0096CC"/> </g> </g> <defs> <filter id="lightboxfilter0_d" x="31.8248" y="0.254482" width="288.346" height="204.35" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feMorphology radius="3.32203" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/> <feOffset dy="4.42938"/> <feGaussianBlur stdDeviation="6.0904"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <linearGradient id="lightboxpaint0_linear" x1="245.186" y1="139.295" x2="247.201" y2="177.739" gradientUnits="userSpaceOnUse"> <stop stop-color="#D0D1D7"/> <stop offset="1" stop-color="#D0D1D7" stop-opacity="0"/> </linearGradient> <clipPath id="lightboxclip0"> <rect width="261.925" height="173.162" fill="white" transform="translate(40.6836 18.392) rotate(-3)"/> </clipPath> <clipPath id="lightboxclip1"> <rect width="128.052" height="173.162" fill="white" transform="translate(40.6836 18.3919) rotate(-3)"/> </clipPath> </defs> </svg>',
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'buyUrl' 		=> 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=customizer&utm_medium=lightbox'
						),

						'advancedFilter' => array(
							'heading' 		=> __( 'Upgrade to Pro to get Advanced Filters', 'custom-facebook-feed' ),
							'description' 	=> __( 'Filter your posts using specific words, hashtags, or phrases to ensure only the content you want is displayed. Choose to hide or show specific types of posts in your timeline feeds.', 'custom-facebook-feed' ),
							'img' 			=> '<svg width="396" height="264" viewBox="0 0 396 264" fill="none" xmlns="http://www.w3.org/2000/svg"> <g filter="url(#advFilerfilter0_d)"> <rect x="139.98" y="62.5992" width="162.17" height="179.401" fill="white"/> </g> <path d="M156.195 184.881C157.547 184.847 161.567 184.786 166.838 184.819C173.426 184.86 173.426 184.912 179.001 184.881C184.575 184.85 185.589 184.789 199.272 184.85C212.955 184.912 210.421 184.912 217.516 184.881C224.611 184.85 225.624 184.835 238.801 184.881C249.342 184.918 261.775 184.913 266.674 184.907" stroke="#DCDDE1" stroke-width="8.10851"/> <path d="M156.195 204.559C157.547 204.53 161.567 204.478 166.838 204.506C173.426 204.542 173.426 204.586 179.001 204.559C184.575 204.533 184.068 204.506 197.751 204.559C211.435 204.613 210.421 204.6 218.023 204.593" stroke="#DCDDE1" stroke-width="8.10851"/> <g clip-path="url(#advFilerclip0)"> <rect x="151.129" y="75.7755" width="139.912" height="88.1801" fill="#8C8F9A"/> <circle cx="157.006" cy="175.713" r="48.2051" fill="#F37036"/> <circle cx="295.743" cy="122.805" r="65.8411" fill="#DCDDE1"/> </g> <circle cx="291.509" cy="68.1738" r="18.7509" fill="#D72C2C"/> <path d="M290.884 65.6399L293.026 67.7751V67.667C293.026 67.1294 292.813 66.6138 292.433 66.2336C292.052 65.8534 291.537 65.6399 290.999 65.6399H290.884ZM287.979 66.1804L289.026 67.2278C288.992 67.3697 288.972 67.5116 288.972 67.667C288.972 68.2046 289.186 68.7202 289.566 69.1004C289.946 69.4806 290.462 69.6941 290.999 69.6941C291.148 69.6941 291.297 69.6739 291.438 69.6401L292.486 70.6874C292.033 70.9104 291.533 71.0455 290.999 71.0455C290.103 71.0455 289.244 70.6896 288.61 70.056C287.977 69.4224 287.621 68.563 287.621 67.667C287.621 67.1332 287.756 66.6332 287.979 66.1804ZM284.242 62.4438L285.783 63.9844L286.087 64.2885C284.972 65.1669 284.093 66.3156 283.566 67.667C284.735 70.6334 287.621 72.7348 290.999 72.7348C292.047 72.7348 293.047 72.5321 293.959 72.1672L294.249 72.451L296.222 74.4241L297.081 73.5659L285.1 61.5856L284.242 62.4438ZM290.999 64.2885C291.895 64.2885 292.755 64.6444 293.388 65.278C294.022 65.9116 294.378 66.771 294.378 67.667C294.378 68.0995 294.29 68.5184 294.134 68.8968L296.114 70.8766C297.128 70.032 297.939 68.9238 298.432 67.667C297.263 64.7006 294.378 62.5992 290.999 62.5992C290.053 62.5992 289.148 62.7681 288.296 63.0722L289.763 64.525C290.148 64.3763 290.56 64.2885 290.999 64.2885Z" fill="white"/> <g filter="url(#advFilerfilter1_d)"> <rect width="162.17" height="179.401" transform="translate(85.7383 41.9814)" fill="white"/> <path d="M101.953 164.263C103.305 164.229 107.325 164.169 112.596 164.201C119.184 164.243 119.184 164.294 124.758 164.263C130.333 164.232 131.346 164.171 145.03 164.232C158.713 164.294 156.179 164.294 163.274 164.263C170.369 164.232 171.382 164.217 184.559 164.263C195.1 164.3 207.533 164.296 212.432 164.289" stroke="#DCDDE1" stroke-width="8.10851"/> <path d="M101.953 183.942C103.305 183.912 107.325 183.86 112.596 183.889C119.184 183.924 119.184 183.968 124.758 183.942C130.333 183.915 129.826 183.889 143.509 183.942C157.192 183.995 156.179 183.982 163.781 183.975" stroke="#DCDDE1" stroke-width="8.10851"/> <g clip-path="url(#advFilerclip1)"> <rect x="96.8867" y="55.1577" width="139.912" height="88.1801" fill="#2C324C"/> <circle cx="125.767" cy="81.4144" r="83.2083" fill="#0068A0"/> <circle cx="256.898" cy="165.92" r="86.4368" fill="#E34F0E"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M165.9 115.972C175.136 115.972 182.623 108.484 182.623 99.2478C182.623 90.0115 175.136 82.524 165.9 82.524C156.663 82.524 149.176 90.0115 149.176 99.2478C149.176 108.484 156.663 115.972 165.9 115.972ZM172.489 99.7547L160.985 93.016V106.493L172.489 99.7547Z" fill="white"/> </g> </g> <circle cx="234.38" cy="40.751" r="18.7509" fill="#0096CC"/> <g clip-path="url(#advFilerclip2)"> <path d="M233.874 38.217C233.337 38.217 232.821 38.4306 232.441 38.8108C232.061 39.1909 231.847 39.7065 231.847 40.2442C231.847 40.7818 232.061 41.2974 232.441 41.6776C232.821 42.0577 233.337 42.2713 233.874 42.2713C234.412 42.2713 234.927 42.0577 235.308 41.6776C235.688 41.2974 235.901 40.7818 235.901 40.2442C235.901 39.7065 235.688 39.1909 235.308 38.8108C234.927 38.4306 234.412 38.217 233.874 38.217ZM233.874 43.6227C232.978 43.6227 232.119 43.2668 231.485 42.6332C230.852 41.9996 230.496 41.1402 230.496 40.2442C230.496 39.3481 230.852 38.4888 231.485 37.8552C232.119 37.2216 232.978 36.8656 233.874 36.8656C234.77 36.8656 235.63 37.2216 236.263 37.8552C236.897 38.4888 237.253 39.3481 237.253 40.2442C237.253 41.1402 236.897 41.9996 236.263 42.6332C235.63 43.2668 234.77 43.6227 233.874 43.6227ZM233.874 35.1763C230.496 35.1763 227.61 37.2778 226.441 40.2442C227.61 43.2105 230.496 45.312 233.874 45.312C237.253 45.312 240.138 43.2105 241.307 40.2442C240.138 37.2778 237.253 35.1763 233.874 35.1763Z" fill="white"/> </g> <defs> <filter id="advFilerfilter0_d" x="131.872" y="58.5449" width="178.387" height="195.618" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feMorphology radius="3.04069" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/> <feOffset dy="4.05426"/> <feGaussianBlur stdDeviation="5.5746"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <filter id="advFilerfilter1_d" x="77.6298" y="37.9272" width="178.387" height="195.618" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feMorphology radius="3.04069" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/> <feOffset dy="4.05426"/> <feGaussianBlur stdDeviation="5.5746"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <clipPath id="advFilerclip0"> <rect width="139.912" height="88.1801" fill="white" transform="translate(151.129 75.7755)"/> </clipPath> <clipPath id="advFilerclip1"> <rect width="139.912" height="88.1801" fill="white" transform="translate(96.8867 55.1577)"/> </clipPath> <clipPath id="advFilerclip2"> <rect width="16.217" height="16.217" fill="white" transform="translate(225.766 32.1356)"/> </clipPath> </defs> </svg>',
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'buyUrl' 		=> 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=customizer&utm_medium=filters&utm_content=advanced-filters'
						),

						'postSettings' => array(
							'heading' 		=> __( 'Upgrade to Pro to get Post Layouts', 'custom-facebook-feed' ),
							'description' 	=> __( 'Display your timeline posts in 3 easy layout options with photos and videos included to make your posts pop, keeping your visitors engaged on your site for longer.', 'custom-facebook-feed' ),
							'img' 			=> '<svg width="396" height="264" viewBox="0 0 396 264" fill="none" xmlns="http://www.w3.org/2000/svg"> <g filter="url(#postLayoutfilter0_d)"> <rect width="169.359" height="187.354" transform="translate(45.7344 25.3549) rotate(-3)" fill="white"/> <path d="M69.332 151.996C70.7395 151.887 74.9292 151.604 80.4276 151.35C87.3007 151.033 87.3035 151.086 93.1155 150.75C98.9276 150.413 99.9813 150.293 114.255 149.61C128.528 148.926 125.886 149.064 133.283 148.645C140.681 148.225 141.737 148.153 155.481 147.481C166.477 146.944 179.443 146.259 184.551 145.984" stroke="#DCDDE1" stroke-width="8.46797"/> <path d="M70.4057 172.519C71.8135 172.414 76.0036 172.14 81.5018 171.882C88.3745 171.559 88.3769 171.605 94.1892 171.272C100.002 170.94 99.4716 170.94 113.745 170.248C128.018 169.555 126.96 169.598 134.887 169.174" stroke="#DCDDE1" stroke-width="8.46797"/> <g clip-path="url(#postLayoutclip0)"> <rect x="58.082" y="38.4871" width="146.115" height="92.0892" transform="rotate(-3 58.082 38.4871)" fill="#8C8F9A"/> <circle cx="89.6383" cy="64.2915" r="86.8971" transform="rotate(-3 89.6383 64.2915)" fill="#0068A0"/> <circle cx="231.01" cy="145.256" r="90.2686" transform="rotate(-3 231.01 145.256)" fill="#E34F0E"/> </g> </g> <rect x="175.465" y="60.8865" width="169.177" height="150.051" rx="3.69355" transform="rotate(10 175.465 60.8865)" fill="#F3F4F5"/> <g filter="url(#postLayoutfilter1_d)"> <rect x="178.789" y="66.0789" width="162.516" height="140.355" transform="rotate(10 178.789 66.0789)" fill="white"/> </g> <path d="M238.203 97.8082C239.182 97.9808 242.094 98.4943 245.912 99.1674C250.684 100.009 250.684 100.009 254.722 100.721C258.76 101.433 259.494 101.562 269.405 103.31C279.316 105.058 277.481 104.734 282.62 105.64C287.759 106.546 288.493 106.676 298.037 108.359C305.673 109.705 314.678 111.293 318.227 111.918" stroke="#DCDDE1" stroke-width="7.3871"/> <path d="M225.375 170.557C226.354 170.73 229.266 171.243 233.084 171.916C237.856 172.758 237.856 172.758 241.894 173.47C245.932 174.182 246.666 174.311 256.577 176.059C266.488 177.806 264.653 177.483 269.792 178.389C274.931 179.295 275.665 179.424 285.209 181.107C292.844 182.454 301.85 184.042 305.399 184.667" stroke="#DCDDE1" stroke-width="7.3871"/> <path d="M235.426 113.57C236.406 113.743 239.324 114.258 243.148 114.932C247.928 115.775 247.928 115.775 251.973 116.488C256.018 117.201 255.65 117.137 265.579 118.887C275.507 120.638 274.772 120.508 280.287 121.481" stroke="#DCDDE1" stroke-width="7.3871"/> <path d="M222.598 186.319C223.578 186.492 226.495 187.006 230.32 187.681C235.1 188.524 235.1 188.524 239.145 189.237C243.19 189.95 242.822 189.885 252.751 191.636C262.679 193.387 261.944 193.257 267.459 194.229" stroke="#DCDDE1" stroke-width="7.3871"/> <g clip-path="url(#postLayoutclip1)"> <rect width="39.3979" height="39.3979" transform="translate(186.992 76.2765) rotate(10)" fill="#86D0F9"/> <circle cx="177.478" cy="109.837" r="26.9121" transform="rotate(10 177.478 109.837)" fill="#0068A0"/> </g> <g clip-path="url(#postLayoutclip2)"> <rect width="39.3979" height="39.3979" transform="translate(174.164 149.025) rotate(10)" fill="#F6966B"/> <circle cx="215.291" cy="179.013" r="26.9121" transform="rotate(10 215.291 179.013)" fill="#F37036"/> </g> <line x1="167.564" y1="129.734" x2="327.611" y2="157.955" stroke="#E8E8EB" stroke-width="1.23118"/> <defs> <filter id="postLayoutfilter0_d" x="37.2664" y="12.2573" width="195.869" height="212.897" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feMorphology radius="3.17549" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/> <feOffset dy="4.23399"/> <feGaussianBlur stdDeviation="5.82173"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <filter id="postLayoutfilter1_d" x="142.038" y="58.2005" width="209.18" height="191.204" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="4.5019"/> <feGaussianBlur stdDeviation="6.19011"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <clipPath id="postLayoutclip0"> <rect width="146.115" height="92.0892" fill="white" transform="translate(58.082 38.4871) rotate(-3)"/> </clipPath> <clipPath id="postLayoutclip1"> <rect width="39.3979" height="39.3979" fill="white" transform="translate(186.992 76.2765) rotate(10)"/> </clipPath> <clipPath id="postLayoutclip2"> <rect width="39.3979" height="39.3979" fill="white" transform="translate(174.164 149.025) rotate(10)"/> </clipPath> </defs> </svg>',
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'buyUrl' 		=> 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=customizer&utm_medium=posts&utm_content=post-layouts'
						),

						'mediaComment' => array(
							'heading' 		=> __( 'Upgrade to Pro to add Media, Likes, & Comments', 'custom-facebook-feed' ),
							'description' 	=> __( 'Display any likes, shares, comments, and reactions in a customizable drop-down box below each timeline post, including comment replies and image attachments.', 'custom-facebook-feed' ),
							'img' 			=> '<svg width="396" height="264" viewBox="0 0 396 264" fill="none" xmlns="http://www.w3.org/2000/svg"> <g filter="url(#shareCommentfilter0_d)"> <rect x="127.551" y="28" width="174.908" height="193.492" rx="1.09318" transform="rotate(5 127.551 28)" fill="white"/> </g> <path d="M133.482 160.909C134.937 160.999 139.262 161.312 144.922 161.843C151.997 162.507 151.992 162.562 157.984 163.053C163.977 163.544 165.072 163.573 179.768 164.925C194.464 166.277 191.741 166.039 199.367 166.673C206.993 167.307 208.084 167.386 222.236 168.674C233.559 169.705 246.918 170.868 252.182 171.322" stroke="#DCDDE1" stroke-width="8.7454"/> <path d="M131.631 182.052C133.086 182.148 137.41 182.47 143.071 182.996C150.146 183.653 150.142 183.701 156.134 184.196C162.126 184.692 161.584 184.616 176.281 185.959C190.977 187.302 189.889 187.193 198.058 187.9" stroke="#DCDDE1" stroke-width="8.7454"/> <g clip-path="url(#shareCommentclip0)"> <rect x="138.293" y="43.2052" width="150.902" height="95.1063" transform="rotate(5 138.293 43.2052)" fill="#B5E5FF"/> <circle cx="135.215" cy="151.135" r="51.9914" transform="rotate(5 135.215 151.135)" fill="#59AB46"/> <circle cx="289.253" cy="107.33" r="71.0127" transform="rotate(5 289.253 107.33)" fill="#DCDDE1"/> </g> <g filter="url(#shareCommentfilter1_d)"> <rect width="159.664" height="188.649" transform="translate(94.207 33.7212)" fill="white"/> <path d="M110.48 153.365C111.803 153.331 115.736 153.272 120.893 153.305C127.339 153.345 127.339 153.395 132.794 153.365C138.248 153.335 139.24 153.274 152.628 153.335C166.016 153.395 163.536 153.395 170.478 153.365C177.42 153.335 178.412 153.32 191.304 153.365C201.618 153.401 213.782 153.397 218.576 153.39" stroke="#DCDDE1" stroke-width="7.9336"/> <path d="M110.48 172.619C111.803 172.59 115.736 172.539 120.893 172.567C127.339 172.602 127.339 172.645 132.794 172.619C138.248 172.593 137.752 172.567 151.14 172.619C164.528 172.671 163.536 172.659 170.974 172.652" stroke="#DCDDE1" stroke-width="7.9336"/> <g clip-path="url(#shareCommentclip1)"> <rect x="105.523" y="46.6133" width="136.894" height="86.2778" fill="#2C324C"/> <circle cx="133.781" cy="72.3034" r="81.4134" fill="#0068A0"/> <circle cx="262.08" cy="154.986" r="84.5722" fill="#E34F0E"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M173.047 106.115C182.084 106.115 189.41 98.7893 189.41 89.7523C189.41 80.7152 182.084 73.3892 173.047 73.3892C164.01 73.3892 156.684 80.7152 156.684 89.7523C156.684 98.7893 164.01 106.115 173.047 106.115ZM179.49 90.2482L168.235 83.6549V96.8415L179.49 90.2482Z" fill="white"/> </g> <path d="M140.397 211.62C140.228 211.62 140.066 211.552 139.946 211.433C139.827 211.313 139.76 211.151 139.76 210.982V209.069H137.209C136.871 209.069 136.546 208.934 136.307 208.695C136.068 208.456 135.934 208.132 135.934 207.793V200.141C135.934 199.803 136.068 199.479 136.307 199.239C136.546 199 136.871 198.866 137.209 198.866H147.412C147.75 198.866 148.075 199 148.314 199.239C148.553 199.479 148.687 199.803 148.687 200.141V207.793C148.687 208.132 148.553 208.456 148.314 208.695C148.075 208.934 147.75 209.069 147.412 209.069H143.522L141.163 211.435C141.035 211.556 140.876 211.62 140.716 211.62H140.397Z" fill="#8C8F9A"/> <g clip-path="url(#shareCommentclip2)"> <path d="M124.088 202.692C124.088 202.305 123.934 201.935 123.661 201.661C123.388 201.388 123.017 201.234 122.63 201.234H118.024L118.724 197.904C118.739 197.831 118.746 197.751 118.746 197.671C118.746 197.372 118.622 197.095 118.425 196.898L117.653 196.133L112.857 200.928C112.588 201.198 112.427 201.562 112.427 201.963V209.251C112.427 209.638 112.581 210.008 112.854 210.282C113.128 210.555 113.498 210.709 113.885 210.709H120.444C121.049 210.709 121.566 210.344 121.785 209.82L123.986 204.682C124.051 204.514 124.088 204.339 124.088 204.15V202.692ZM108.055 210.709H110.97V201.963H108.055V210.709Z" fill="#0096CC"/> </g> </g> <path d="M240.692 203.056L235.591 197.955V200.87C230.489 201.599 228.303 205.243 227.574 208.887C229.396 206.336 231.947 205.17 235.591 205.17V208.158L240.692 203.056Z" fill="#8C8F9A"/> <defs> <filter id="shareCommentfilter0_d" x="103.125" y="24.8115" width="206.229" height="223.122" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="4.3727"/> <feGaussianBlur stdDeviation="3.82611"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.16 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <filter id="shareCommentfilter1_d" x="86.2734" y="29.7544" width="175.531" height="204.516" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feMorphology radius="2.9751" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/> <feOffset dy="3.9668"/> <feGaussianBlur stdDeviation="5.45435"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <clipPath id="shareCommentclip0"> <rect width="150.902" height="95.1063" fill="white" transform="translate(138.293 43.2052) rotate(5)"/> </clipPath> <clipPath id="shareCommentclip1"> <rect width="136.894" height="86.2778" fill="white" transform="translate(105.523 46.6133)"/> </clipPath> <clipPath id="shareCommentclip2"> <rect width="17.4908" height="17.4908" fill="white" transform="translate(107.324 195.404)"/> </clipPath> </defs> </svg>',
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'buyUrl' 		=> 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=customizer&utm_medium=posts&utm_content=advanced-elements'
						),

						'loadMore' => array(
							'heading' 		=> __( 'Upgrade to Pro to add Load More functionality', 'custom-facebook-feed' ),
							'description' 	=> __( 'Add a Load More button at the bottom of each feed to infinitely load more content. Customize the button text, colors, and font to look exactly as you\'d like.', 'custom-facebook-feed' ),
							'img' 			=> '<svg width="396" height="264" viewBox="0 0 396 264" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#loadMoreclip0)"> <path fill-rule="evenodd" clip-rule="evenodd" d="M204.027 90.2973C202.623 90.2973 201.486 91.4347 201.486 92.8378V174.135C201.486 175.538 202.623 176.676 204.027 176.676H301.837C303.24 176.676 304.378 175.538 304.378 174.135V92.8378C304.378 91.4347 303.24 90.2973 301.837 90.2973H204.027ZM87.1616 104.27C85.7585 104.27 84.6211 105.408 84.6211 106.811V188.108C84.6211 189.511 85.7585 190.649 87.1616 190.649H184.972C186.376 190.649 187.513 189.511 187.513 188.108V106.811C187.513 105.408 186.376 104.27 184.972 104.27H87.1616ZM87.1616 204.622C85.7585 204.622 84.6211 205.759 84.6211 207.162V288.459C84.6211 289.863 85.7585 291 87.1616 291H184.972C186.376 291 187.513 289.863 187.513 288.459V207.162C187.513 205.759 186.376 204.622 184.972 204.622H87.1616ZM201.486 193.189C201.486 191.786 202.623 190.649 204.027 190.649H301.837C303.24 190.649 304.378 191.786 304.378 193.189V274.486C304.378 275.89 303.24 277.027 301.837 277.027H204.027C202.623 277.027 201.486 275.89 201.486 274.486V193.189Z" fill="url(#loadMorepaint0_linear)"/> <rect x="117.125" y="68.5811" width="146.224" height="42" rx="6.53282" fill="#0096CC"/> <path d="M145.104 86.227C144.817 86.227 144.625 86.0354 144.625 85.7479V81.9146C144.625 81.6271 144.817 81.4354 145.104 81.4354C145.392 81.4354 145.583 81.6271 145.583 81.9146V85.7479C145.583 86.0354 145.392 86.227 145.104 86.227Z" fill="white" stroke="white" stroke-width="0.887334"/> <path opacity="0.3" d="M145.104 97.7269C144.817 97.7269 144.625 97.5352 144.625 97.2477V93.4144C144.625 93.127 144.817 92.9353 145.104 92.9353C145.392 92.9353 145.583 93.127 145.583 93.4144V97.2477C145.583 97.5352 145.392 97.7269 145.104 97.7269Z" fill="white" stroke="white" stroke-width="0.887334"/> <path opacity="0.3" d="M147.019 86.7062C146.923 86.7062 146.875 86.7062 146.779 86.6583C146.588 86.5146 146.492 86.275 146.636 86.0354L148.552 82.7292C148.696 82.5375 148.936 82.4417 149.175 82.5854C149.367 82.7292 149.463 82.9688 149.319 83.2083L147.402 86.5146C147.306 86.6104 147.163 86.7062 147.019 86.7062Z" fill="white" stroke="white" stroke-width="0.887334"/> <path opacity="0.3" d="M141.269 96.6727C141.173 96.6727 141.125 96.6727 141.029 96.6248C140.838 96.481 140.742 96.2414 140.886 96.0019L142.802 92.6957C142.946 92.504 143.186 92.4082 143.425 92.5519C143.617 92.6957 143.713 92.9352 143.569 93.1748L141.652 96.481C141.556 96.5769 141.413 96.6727 141.269 96.6727Z" fill="white" stroke="white" stroke-width="0.887334"/> <path opacity="0.93" d="M143.186 86.7061C143.042 86.7061 142.898 86.6102 142.802 86.4665L140.886 83.1603C140.742 82.9686 140.838 82.6811 141.029 82.5374C141.221 82.3936 141.509 82.4894 141.652 82.6811L143.569 85.9873C143.713 86.179 143.617 86.4665 143.425 86.6102C143.329 86.7061 143.281 86.7061 143.186 86.7061Z" fill="white" stroke="white" stroke-width="0.887334"/> <path opacity="0.3" d="M148.936 96.6728C148.792 96.6728 148.648 96.5769 148.552 96.4332L146.636 93.127C146.492 92.9353 146.588 92.6478 146.779 92.5041C146.971 92.3603 147.259 92.4561 147.402 92.6478L149.319 95.954C149.463 96.1457 149.367 96.4332 149.175 96.5769C149.079 96.6248 149.031 96.6728 148.936 96.6728Z" fill="white" stroke="white" stroke-width="0.887334"/> <path opacity="0.65" d="M141.273 90.0602H137.44C137.153 90.0602 136.961 89.8686 136.961 89.5811C136.961 89.2936 137.153 89.1019 137.44 89.1019H141.273C141.561 89.1019 141.753 89.2936 141.753 89.5811C141.753 89.8686 141.561 90.0602 141.273 90.0602Z" fill="white" stroke="white" stroke-width="0.887334"/> <path opacity="0.3" d="M152.773 90.0602H148.94C148.653 90.0602 148.461 89.8686 148.461 89.5811C148.461 89.2936 148.653 89.1019 148.94 89.1019H152.773C153.061 89.1019 153.253 89.2936 153.253 89.5811C153.253 89.8686 153.061 90.0602 152.773 90.0602Z" fill="white" stroke="white" stroke-width="0.887334"/> <path opacity="0.86" d="M141.798 88.0959C141.702 88.0959 141.654 88.0959 141.558 88.0479L138.252 86.1313C138.06 85.9876 137.965 85.748 138.108 85.5084C138.252 85.3167 138.492 85.2209 138.731 85.3647L142.037 87.2813C142.229 87.425 142.325 87.6646 142.181 87.9042C142.085 88.0479 141.942 88.0959 141.798 88.0959Z" fill="white" stroke="white" stroke-width="0.887334"/> <path opacity="0.3" d="M151.763 93.8458C151.667 93.8458 151.619 93.8458 151.523 93.7979L148.217 91.8812C148.025 91.7375 147.929 91.4979 148.073 91.2583C148.217 91.0667 148.456 90.9708 148.696 91.1146L152.002 93.0312C152.194 93.175 152.29 93.4146 152.146 93.6541C152.05 93.7979 151.906 93.8458 151.763 93.8458Z" fill="white" stroke="white" stroke-width="0.887334"/> <path opacity="0.44" d="M138.445 93.8456C138.301 93.8456 138.157 93.7498 138.061 93.606C137.918 93.4144 138.013 93.1269 138.205 92.9831L141.511 91.0665C141.703 90.9227 141.991 91.0186 142.134 91.2102C142.278 91.4019 142.182 91.6894 141.991 91.8331L138.684 93.7498C138.636 93.8456 138.541 93.8456 138.445 93.8456Z" fill="white" stroke="white" stroke-width="0.887334"/> <path opacity="0.3" d="M148.413 88.0957C148.27 88.0957 148.126 87.9998 148.03 87.8561C147.886 87.6644 147.982 87.3769 148.174 87.2332L151.48 85.3165C151.672 85.1728 151.959 85.2686 152.103 85.4603C152.247 85.652 152.151 85.9395 151.959 86.0832L148.653 87.9998C148.557 88.0957 148.509 88.0957 148.413 88.0957Z" fill="white" stroke="white" stroke-width="0.887334"/> <path d="M161.738 95.5811H168.926V93.8858H163.754V84.3076H161.738V95.5811ZM174.191 95.753C176.746 95.753 178.301 94.0811 178.301 91.3155V91.2998C178.301 88.5498 176.738 86.878 174.191 86.878C171.652 86.878 170.082 88.5576 170.082 91.2998V91.3155C170.082 94.0811 171.629 95.753 174.191 95.753ZM174.191 94.1748C172.84 94.1748 172.066 93.1201 172.066 91.3233V91.3076C172.066 89.5108 172.84 88.4483 174.191 88.4483C175.535 88.4483 176.309 89.5108 176.309 91.3076V91.3233C176.309 93.1201 175.543 94.1748 174.191 94.1748ZM182.582 95.7217C183.707 95.7217 184.613 95.2373 185.082 94.4092H185.215V95.5811H187.137V89.7451C187.137 87.9405 185.902 86.878 183.707 86.878C181.676 86.878 180.301 87.8311 180.113 89.2686L180.105 89.3389H181.941L181.949 89.3076C182.145 88.7451 182.723 88.4248 183.613 88.4248C184.66 88.4248 185.215 88.8936 185.215 89.7451V90.4795L183.02 90.6123C180.941 90.7373 179.777 91.628 179.777 93.1514V93.167C179.777 94.7217 180.957 95.7217 182.582 95.7217ZM181.699 93.0733V93.0576C181.699 92.3545 182.207 91.9561 183.293 91.8858L185.215 91.7608V92.4483C185.215 93.4639 184.348 94.2295 183.176 94.2295C182.316 94.2295 181.699 93.7998 181.699 93.0733ZM192.551 95.7217C193.754 95.7217 194.676 95.1748 195.145 94.2451H195.277V95.5811H197.23V83.7373H195.277V88.4014H195.145C194.699 87.4795 193.723 86.9014 192.551 86.9014C190.387 86.9014 189.051 88.5889 189.051 91.3076V91.3233C189.051 94.0264 190.41 95.7217 192.551 95.7217ZM193.16 94.0811C191.832 94.0811 191.027 93.042 191.027 91.3233V91.3076C191.027 89.5889 191.84 88.542 193.16 88.542C194.48 88.542 195.309 89.5967 195.309 91.3076V91.3233C195.309 93.0342 194.488 94.0811 193.16 94.0811ZM204.145 95.5811H205.965V87.5655H206.098L209.223 95.5811H210.598L213.723 87.5655H213.863V95.5811H215.676V84.3076H213.363L209.98 92.9795H209.848L206.457 84.3076H204.145V95.5811ZM221.941 95.753C224.496 95.753 226.051 94.0811 226.051 91.3155V91.2998C226.051 88.5498 224.488 86.878 221.941 86.878C219.402 86.878 217.832 88.5576 217.832 91.2998V91.3155C217.832 94.0811 219.379 95.753 221.941 95.753ZM221.941 94.1748C220.59 94.1748 219.816 93.1201 219.816 91.3233V91.3076C219.816 89.5108 220.59 88.4483 221.941 88.4483C223.285 88.4483 224.059 89.5108 224.059 91.3076V91.3233C224.059 93.1201 223.293 94.1748 221.941 94.1748ZM228.004 95.5811H229.949V90.6201C229.949 89.4014 230.801 88.628 232.098 88.628C232.434 88.628 232.754 88.6748 233.066 88.7373V86.9951C232.879 86.9405 232.574 86.9014 232.277 86.9014C231.152 86.9014 230.371 87.4405 230.082 88.3545H229.949V87.042H228.004V95.5811ZM238.098 95.753C240.418 95.753 241.559 94.417 241.832 93.3155L241.855 93.2451H240.004L239.98 93.2998C239.793 93.6905 239.191 94.2217 238.137 94.2217C236.816 94.2217 235.988 93.3311 235.965 91.7998H241.934V91.1592C241.934 88.5811 240.434 86.878 238.004 86.878C235.574 86.878 234.02 88.6358 234.02 91.3233V91.3311C234.02 94.0498 235.559 95.753 238.098 95.753ZM238.027 88.4014C239.113 88.4014 239.902 89.0967 240.035 90.4951H235.98C236.129 89.1358 236.941 88.4014 238.027 88.4014Z" fill="white"/> <path d="M265.034 111.665L264.788 110.272L263.243 110.545L262.997 109.151L259.906 109.696L259.661 108.303L255.025 109.121L254.779 107.728L251.688 108.273L250.706 102.7L249.161 102.972L248.915 101.579L245.824 102.124L246.07 103.517L244.525 103.79L246.735 116.328L245.19 116.601L244.944 115.207L240.308 116.025L241.291 121.597L242.836 121.325L243.328 124.111L244.873 123.839L245.364 126.625L246.91 126.352L247.401 129.139L248.946 128.866L249.683 133.046L266.682 130.048L265.699 124.476L267.244 124.203L266.507 120.024L264.962 120.296L265.699 124.476L264.154 124.748L264.891 128.928L250.983 131.38L250.492 128.594L248.946 128.866L248.455 126.08L246.91 126.352L246.418 123.566L244.873 123.839L244.382 121.052L242.836 121.325L242.099 117.146L245.19 116.601L245.436 117.994L246.981 117.721L247.718 121.901L249.263 121.628L246.07 103.517L249.161 102.972L251.371 115.511L252.917 115.238L251.934 109.666L255.025 109.121L256.007 114.693L257.553 114.421L256.816 110.241L259.906 109.696L260.889 115.269L262.434 114.996L261.697 110.817L263.243 110.545L263.488 111.938L265.034 111.665L266.507 120.024L268.053 119.751L266.579 111.393L265.034 111.665Z" fill="#141B38"/> <path d="M264.964 120.296L266.509 120.024L265.036 111.665L263.49 111.938L263.245 110.545L261.699 110.817L262.436 114.996L260.891 115.269L259.908 109.696L256.818 110.241L257.555 114.421L256.009 114.693L255.027 109.121L251.936 109.666L252.919 115.238L251.373 115.511L249.163 102.972L246.072 103.517L249.265 121.628L247.72 121.901L246.983 117.721L245.438 117.994L245.192 116.601L242.101 117.146L242.838 121.325L244.384 121.052L244.875 123.839L246.42 123.566L246.912 126.352L248.457 126.08L248.948 128.866L250.494 128.594L250.985 131.38L264.893 128.928L264.156 124.748L265.701 124.476L264.964 120.296Z" fill="white"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M259.835 118.328L258.29 118.6L259.448 125.168L260.993 124.895L259.835 118.328ZM256.745 118.873L255.199 119.145L256.358 125.713L257.903 125.44L256.745 118.873ZM252.109 119.69L253.655 119.418L254.813 125.985L253.267 126.258L252.109 119.69Z" fill="#141B38"/> </g> <defs> <linearGradient id="loadMorepaint0_linear" x1="191.324" y1="179.216" x2="194" y2="256" gradientUnits="userSpaceOnUse"> <stop stop-color="#DCDDE1"/> <stop offset="1" stop-color="#DCDDE1" stop-opacity="0"/> </linearGradient> <clipPath id="loadMoreclip0"> <rect width="396" height="264" fill="white"/> </clipPath> </defs> </svg>',
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'buyUrl' 		=> 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=customizer&utm_medium=load-more'
						),

						'photos' => array(
							'heading' 		=> __( 'Upgrade to Pro to<br/>get Photo Feeds', 'custom-facebook-feed' ),
							'description' 	=> __( 'Save time by displaying beautiful photo feeds which pull right from your Facebook Photos page. List, grid, masonry, and carousels, with different layouts for both desktop and mobile, and a full size photo lightbox.', 'custom-facebook-feed' ),
							'img' 			=> '<svg width="396" height="264" viewBox="0 0 396 264" fill="none" xmlns="http://www.w3.org/2000/svg"> <rect x="156" y="45.3642" width="160" height="177" rx="1" transform="rotate(5 156 45.3642)" fill="white"/> <path d="M161.424 166.945C162.755 167.027 166.712 167.314 171.889 167.799C178.361 168.406 178.356 168.457 183.838 168.906C189.32 169.355 190.321 169.381 203.764 170.619C217.208 171.856 214.717 171.638 221.693 172.218C228.669 172.798 229.667 172.87 242.613 174.048C252.971 174.991 265.191 176.055 270.007 176.47" stroke="#DCDDE1" stroke-width="8"/> <path d="M159.733 186.286C161.064 186.373 165.02 186.668 170.198 187.149C176.67 187.75 176.666 187.794 182.147 188.247C187.629 188.7 187.133 188.631 200.577 189.86C214.021 191.088 213.026 190.989 220.498 191.635" stroke="#DCDDE1" stroke-width="8"/> <g clip-path="url(#photosPoupclip0)"> <rect x="165.824" y="59.2734" width="138.04" height="87" transform="rotate(5 165.824 59.2734)" fill="#B5E5FF"/> <circle cx="163.009" cy="158.004" r="47.56" transform="rotate(5 163.009 158.004)" fill="#59AB46"/> <circle cx="303.916" cy="117.932" r="64.96" transform="rotate(5 303.916 117.932)" fill="#0096CC"/> </g> <g filter="url(#photosPoupfilter0_f)"> <rect x="95" y="36" width="154" height="176" rx="1" fill="black" fill-opacity="0.15"/> </g> <rect x="80" y="28" width="160" height="177" rx="1" fill="white"/> <path d="M96 148.645C97.3333 148.611 101.3 148.552 106.5 148.584C113 148.625 113 148.676 118.5 148.645C124 148.615 125 148.554 138.5 148.615C152 148.676 149.5 148.676 156.5 148.645C163.5 148.615 164.5 148.6 177.5 148.645C187.9 148.682 200.167 148.677 205 148.671" stroke="#DCDDE1" stroke-width="8"/> <path d="M96 168.061C97.3333 168.031 101.3 167.98 106.5 168.008C113 168.043 113 168.087 118.5 168.061C124 168.034 123.5 168.008 137 168.061C150.5 168.113 149.5 168.101 157 168.093" stroke="#DCDDE1" stroke-width="8"/> <g clip-path="url(#photosPoupclip1)"> <rect x="91" y="41" width="138.04" height="87" fill="#F9BBA0"/> <circle cx="96.8002" cy="139.6" r="47.56" fill="#F37036"/> <circle cx="233.679" cy="87.4" r="64.96" fill="#59AB46"/> </g> <defs> <filter id="photosPoupfilter0_f" x="81" y="22" width="182" height="204" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/> <feGaussianBlur stdDeviation="7" result="effect1_foregroundBlur"/> </filter> <clipPath id="photosPoupclip0"> <rect width="138.04" height="87" fill="white" transform="translate(165.824 59.2734) rotate(5)"/> </clipPath> <clipPath id="photosPoupclip1"> <rect width="138.04" height="87" fill="white" transform="translate(91 41)"/> </clipPath> </defs> </svg>',
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'buyUrl' 		=> 'https://smashballoondemo.com/photos/?utm_campaign=facebook-free&utm_source=feed-type&utm_medium=photos&utm_content=see-demo'
						),

						'videos' => array(
							'heading' 		=> __( 'Upgrade to Pro to<br/>get Video Feeds', 'custom-facebook-feed' ),
							'description' 	=> __( 'Automatically feed videos from your Facebook Videos page right to your website. List, grid, masonry, and carousel layouts, played in stunning HD with a full size video lightbox.', 'custom-facebook-feed' ),
							'img' 			=> '<svg width="396" height="264" viewBox="0 0 396 264" fill="none" xmlns="http://www.w3.org/2000/svg"> <g filter="url(#videoPopupfilter0_d)"> <rect width="177.175" height="196" transform="translate(109.412 34)" fill="white"/> <path d="M127.131 167.596C128.607 167.558 133 167.493 138.758 167.529C145.956 167.573 145.956 167.629 152.046 167.596C158.137 167.562 159.244 167.495 174.193 167.562C189.142 167.629 186.374 167.629 194.125 167.596C201.877 167.562 202.984 167.545 217.379 167.596C228.896 167.636 242.479 167.631 247.831 167.624" stroke="#DCDDE1" stroke-width="8.85876"/> <path d="M127.131 189.095C128.607 189.063 133 189.006 138.758 189.037C145.956 189.076 145.956 189.124 152.046 189.095C158.137 189.066 157.583 189.037 172.532 189.095C187.481 189.153 186.374 189.14 194.679 189.132" stroke="#DCDDE1" stroke-width="8.85876"/> <g clip-path="url(#videoPopupclip0)"> <rect x="121.594" y="48.3955" width="152.858" height="96.339" fill="#E34F0E"/> <circle cx="153.147" cy="77.0815" r="90.9072" fill="#0096CC"/> <circle cx="296.411" cy="169.406" r="94.4344" fill="#DCDDE1"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M196.994 114.836C207.085 114.836 215.265 106.656 215.265 96.565C215.265 86.4741 207.085 78.2938 196.994 78.2938C186.903 78.2938 178.723 86.4741 178.723 96.565C178.723 106.656 186.903 114.836 196.994 114.836ZM204.192 97.1186L191.624 89.7565V104.481L204.192 97.1186Z" fill="white"/> </g> </g> <defs> <filter id="videoPopupfilter0_d" x="100.553" y="29.5706" width="194.893" height="213.718" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feMorphology radius="3.32203" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/> <feOffset dy="4.42938"/> <feGaussianBlur stdDeviation="6.0904"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <clipPath id="videoPopupclip0"> <rect width="152.858" height="96.339" fill="white" transform="translate(121.594 48.3955)"/> </clipPath> </defs> </svg>',
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'buyUrl' 		=> 'https://smashballoondemo.com/videos/?utm_campaign=facebook-free&utm_source=feed-type&utm_medium=videos&utm_content=see-demo'
						),

						'albums' => array(
							'heading' 		=> __( 'Upgrade to Pro to<br/>get Album Feeds', 'custom-facebook-feed' ),
							'description' 	=> __( 'Display a feed of the albums from your Facebook Photos page. Show photos within each album inside a beautiful album lightbox to increase discovery of your content to your website visitors.', 'custom-facebook-feed' ),
							'img' 			=> '<svg width="396" height="264" viewBox="0 0 396 264" fill="none" xmlns="http://www.w3.org/2000/svg"> <g filter="url(#albmPoupfilter0_d)"> <rect x="91.7383" y="67.1008" width="238.202" height="156.899" rx="4.27907" fill="white"/> </g> <g filter="url(#albmPoupfilter1_d)"> <rect x="80.3262" y="54.2636" width="238.202" height="156.899" rx="4.27907" fill="white"/> </g> <g filter="url(#albmPoupfilter2_d)"> <rect x="66.0625" y="40" width="238.202" height="156.899" rx="4.27907" fill="white"/> </g> <g clip-path="url(#albmPoupclip0)"> <rect x="74.6211" y="48.5581" width="221.085" height="139.783" rx="2.85271" fill="#E2F5FF"/> <path d="M135.953 188.341H295.705V48.5582H135.953V188.341Z" fill="#86D0F9"/> <path d="M175.893 188.341H295.707V104.186H180.172C177.808 104.186 175.893 106.102 175.893 108.465V188.341Z" fill="#E34F0E"/> <mask id="albmPouppath-7-inside-1" fill="white"> <path d="M150.218 163.38C150.218 172.256 147.094 180.849 141.395 187.653C135.695 194.457 127.782 199.038 119.044 200.593C110.305 202.149 101.298 200.579 93.6002 196.16C85.9026 191.741 80.0057 184.754 76.9428 176.423L97.8349 168.742C99.094 172.167 101.518 175.039 104.683 176.856C107.847 178.673 111.55 179.318 115.143 178.679C118.735 178.039 121.988 176.156 124.331 173.359C126.675 170.561 127.959 167.029 127.959 163.38H150.218Z"/> </mask> <path d="M150.218 163.38C150.218 172.256 147.094 180.849 141.395 187.653C135.695 194.457 127.782 199.038 119.044 200.593C110.305 202.149 101.298 200.579 93.6002 196.16C85.9026 191.741 80.0057 184.754 76.9428 176.423L97.8349 168.742C99.094 172.167 101.518 175.039 104.683 176.856C107.847 178.673 111.55 179.318 115.143 178.679C118.735 178.039 121.988 176.156 124.331 173.359C126.675 170.561 127.959 167.029 127.959 163.38H150.218Z" stroke="#59AB46" stroke-width="46.1981" mask="url(#albmPouppath-7-inside-1)"/> </g> <defs> <filter id="albmPoupfilter0_d" x="80.3274" y="61.3954" width="261.023" height="179.721" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feMorphology radius="4.27907" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/> <feOffset dy="5.70543"/> <feGaussianBlur stdDeviation="7.84496"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <filter id="albmPoupfilter1_d" x="68.9153" y="48.5581" width="261.023" height="179.721" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feMorphology radius="4.27907" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/> <feOffset dy="5.70543"/> <feGaussianBlur stdDeviation="7.84496"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <filter id="albmPoupfilter2_d" x="54.6516" y="34.2946" width="261.023" height="179.721" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feMorphology radius="4.27907" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/> <feOffset dy="5.70543"/> <feGaussianBlur stdDeviation="7.84496"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <clipPath id="albmPoupclip0"> <rect x="74.6211" y="48.5581" width="221.085" height="139.783" rx="2.85271" fill="white"/> </clipPath> </defs> </svg>',
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'buyUrl' 		=>'https://smashballoondemo.com/albums/?utm_campaign=facebook-free&utm_source=feed-type&utm_medium=albums&utm_content=see-demo'
						),

						'events' => array(
							'heading' 		=> __( 'Upgrade to Pro to<br/>get Event Feeds', 'custom-facebook-feed' ),
							'description' 	=> __( 'Promote your Facebook events to your website visitors to help boost attendance. Display both upcoming and past events in a list, masonry layout, or carousel.', 'custom-facebook-feed' ),
							'img' 			=> '<svg width="396" height="264" viewBox="0 0 396 264" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#eventPopupclip0)"> <g filter="url(#eventPopupfilter0_d)"> <rect x="77" y="33" width="241.415" height="77.5976" rx="3.69512" fill="white"/> </g> <rect x="87.4694" y="43.4695" width="33.2561" height="34.4878" rx="5.54268" fill="white" stroke="#E8E8EB" stroke-width="1.23171"/> <path d="M100.899 70.7073H103.37L108.084 61.3252V59.4175H100.016V61.3017H105.707V61.4346L100.899 70.7073Z" fill="#434960"/> <path d="M86.8535 49.0122C86.8535 45.6109 89.6108 42.8537 93.0121 42.8537H115.183C118.584 42.8537 121.341 45.6109 121.341 49.0122V55.1707H86.8535V49.0122Z" fill="#E34F0E"/> <path d="M97.653 50.9944C98.869 50.9944 99.5655 50.3268 99.5655 49.1721V45.6466H98.2411V49.1613C98.2411 49.6268 98.0355 49.8721 97.6349 49.8721C97.256 49.8721 97.0251 49.6159 97.0179 49.2082V49.1937H95.744V49.2154C95.7585 50.3124 96.4874 50.9944 97.653 50.9944ZM100.309 50.8537H101.698L102.019 49.7098H103.719L104.04 50.8537H105.429L103.679 45.6466H102.059L100.309 50.8537ZM102.835 46.7941H102.903L103.452 48.7571H102.286L102.835 46.7941ZM106.303 50.8537H107.558V47.8947H107.623L109.803 50.8537H110.853V45.6466H109.597V48.5767H109.532L107.36 45.6466H106.303V50.8537Z" fill="white"/> <path d="M143.512 57.7255C149.054 57.6973 161.248 57.6093 168.146 57.6409C176.768 57.6804 181.079 57.7593 192.164 57.7255C203.25 57.6916 210.024 57.6296 221.725 57.686C233.426 57.7424 245.128 57.7649 253.75 57.7255C262.371 57.686 275.304 57.7875 286.39 57.748" stroke="#DCDDE1" stroke-width="7.39024"/> <path d="M144.129 78.2453C149.877 78.1904 163.959 78.1137 174.306 78.2453C187.239 78.4097 202.019 78.1082 211.873 78.2453" stroke="#DCDDE1" stroke-width="7.39024"/> <g filter="url(#eventPopupfilter1_d)"> <rect x="77" y="120.451" width="241.415" height="77.5976" rx="3.69512" fill="white"/> </g> <path d="M144.744 143.848C149.055 143.805 158.909 143.768 163.836 143.778C169.994 143.789 180.464 143.883 190.317 143.848C200.171 143.813 206.945 143.731 235.275 143.848C257.938 143.941 270.994 143.887 274.689 143.848" stroke="#DCDDE1" stroke-width="7.39024"/> <rect x="87.4694" y="130.921" width="33.2561" height="34.4878" rx="5.54268" fill="white" stroke="#E8E8EB" stroke-width="1.23171"/> <path d="M104.713 158.409C107.347 158.409 109.107 157.025 109.107 154.976V154.961C109.107 153.428 108.012 152.459 106.331 152.303V152.256C107.652 151.982 108.684 151.067 108.684 149.66V149.644C108.684 147.846 107.121 146.626 104.697 146.626C102.328 146.626 100.741 147.94 100.577 149.957L100.569 150.051H102.742L102.75 149.98C102.844 149.042 103.587 148.44 104.697 148.44C105.807 148.44 106.456 149.019 106.456 149.957V149.973C106.456 150.887 105.69 151.513 104.502 151.513H103.243V153.194H104.533C105.901 153.194 106.706 153.788 106.706 154.867V154.883C106.706 155.836 105.917 156.509 104.713 156.509C103.493 156.509 102.688 155.883 102.586 155.008L102.578 154.922H100.319L100.326 155.023C100.483 157.04 102.164 158.409 104.713 158.409Z" fill="#434960"/> <path d="M86.8535 136.463C86.8535 133.062 89.6108 130.305 93.0121 130.305H115.183C118.584 130.305 121.341 133.062 121.341 136.463V142.622H86.8535V136.463Z" fill="#0096CC"/> <path d="M95.2148 138.305H96.3804V134.989H96.4526L97.6361 138.305H98.3903L99.5739 134.989H99.6497V138.305H100.812V133.098H99.3069L98.0511 136.631H97.9826L96.7232 133.098H95.2148V138.305ZM101.692 138.305H103.081L103.403 137.161H105.102L105.423 138.305H106.813L105.062 133.098H103.442L101.692 138.305ZM104.218 134.245H104.287L104.835 136.208H103.67L104.218 134.245ZM107.686 138.305H109.01V136.483H109.696L110.587 138.305H112.074L111.035 136.288C111.598 136.039 111.919 135.458 111.919 134.823V134.815C111.919 133.74 111.229 133.098 109.948 133.098H107.686V138.305ZM109.01 135.577V134.097H109.793C110.27 134.097 110.569 134.393 110.569 134.833V134.841C110.569 135.292 110.277 135.577 109.8 135.577H109.01Z" fill="white"/> <path d="M145.359 164.465C151.107 164.41 165.19 164.333 175.536 164.465C188.469 164.629 203.25 164.328 213.103 164.465" stroke="#DCDDE1" stroke-width="7.39024"/> <g filter="url(#eventPopupfilter2_d)"> <rect x="77" y="207.902" width="241.415" height="77.5976" rx="3.69512" fill="white"/> </g> <rect x="87.4694" y="218.372" width="33.2561" height="34.4878" rx="5.54268" fill="white" stroke="#E8E8EB" stroke-width="1.23171"/> <path d="M98.75 245.61H101.111V234.328H98.7578L95.8415 236.353V238.479L98.6092 236.556H98.75V245.61ZM104.184 245.61H112.323V243.71H107.35V243.53L109.438 241.583C111.494 239.683 112.159 238.62 112.159 237.338V237.314C112.159 235.368 110.532 234.038 108.21 234.038C105.74 234.038 104.051 235.524 104.051 237.69L104.059 237.721H106.24V237.682C106.24 236.611 106.998 235.876 108.101 235.876C109.18 235.876 109.837 236.548 109.837 237.51V237.533C109.837 238.323 109.407 238.894 107.819 240.434L104.184 244.023V245.61Z" fill="#434960"/> <path d="M86.8535 223.915C86.8535 220.513 89.6108 217.756 93.0121 217.756H115.183C118.584 217.756 121.341 220.513 121.341 223.915V230.073H86.8535V223.915Z" fill="#59AB46"/> <path d="M96.3274 225.756H97.7167L98.0379 224.612H99.7375L100.059 225.756H101.448L99.6978 220.549H98.0776L96.3274 225.756ZM98.8534 221.697H98.9219L99.4704 223.66H98.3049L98.8534 221.697ZM102.321 225.756H103.645V224.23H104.562C105.728 224.23 106.478 223.515 106.478 222.4V222.393C106.478 221.278 105.728 220.549 104.562 220.549H102.321V225.756ZM104.245 221.563C104.807 221.563 105.139 221.852 105.139 222.393V222.4C105.139 222.941 104.807 223.234 104.245 223.234H103.645V221.563H104.245ZM107.452 225.756H108.777V223.934H109.462L110.354 225.756H111.84L110.801 223.739C111.364 223.49 111.685 222.909 111.685 222.274V222.267C111.685 221.191 110.996 220.549 109.715 220.549H107.452V225.756ZM108.777 223.028V221.549H109.56C110.036 221.549 110.336 221.844 110.336 222.285V222.292C110.336 222.743 110.043 223.028 109.567 223.028H108.777Z" fill="white"/> <path d="M143.512 229.428C149.054 229.4 161.248 229.312 168.146 229.343C176.768 229.383 181.079 229.462 192.164 229.428C203.25 229.394 210.024 229.332 221.725 229.388C233.426 229.445 245.128 229.467 253.75 229.428C262.371 229.388 275.304 229.49 286.39 229.45" stroke="#DCDDE1" stroke-width="7.39024"/> <path d="M144.129 248.221C149.877 248.166 163.959 248.089 174.306 248.221C187.239 248.385 202.019 248.084 211.873 248.221" stroke="#DCDDE1" stroke-width="7.39024"/> </g> <defs> <filter id="eventPopupfilter0_d" x="67.1463" y="28.0732" width="261.122" height="97.3049" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feMorphology radius="3.69512" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/> <feOffset dy="4.92683"/> <feGaussianBlur stdDeviation="6.77439"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <filter id="eventPopupfilter1_d" x="67.1463" y="115.524" width="261.122" height="97.3049" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feMorphology radius="3.69512" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/> <feOffset dy="4.92683"/> <feGaussianBlur stdDeviation="6.77439"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <filter id="eventPopupfilter2_d" x="67.1463" y="202.976" width="261.122" height="97.3049" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feMorphology radius="3.69512" operator="erode" in="SourceAlpha" result="effect1_dropShadow"/> <feOffset dy="4.92683"/> <feGaussianBlur stdDeviation="6.77439"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.15 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <clipPath id="eventPopupclip0"> <rect width="396" height="264" fill="white"/> </clipPath> </defs> </svg>',
							'bullets'       => [
								'heading' => __( 'And much more!', 'custom-facebook-feed' ),
								'content' => [
									__( 'Display images & videos in posts', 'custom-facebook-feed' ),
									__( 'Show likes, reactions, comments', 'custom-facebook-feed' ),
									__( 'Advanced feed types', 'custom-facebook-feed' ),
									__( 'Filter Posts', 'custom-facebook-feed' ),
									__( 'Popup photo/video lightbox', 'custom-facebook-feed' ),
									__( 'Ability to load more posts', 'custom-facebook-feed' ),
									__( 'Multiple post layout options', 'custom-facebook-feed' ),
									__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
									__( '30 day money back guarantee', 'custom-facebook-feed' ),
								]
							],
							'buyUrl' 		=> 'https://smashballoondemo.com/events/?utm_campaign=facebook-free&utm_source=feed-type&utm_medium=events&utm_content=see-demo'
						),

					),
					'allFeedsScreen' => array(
						'mainHeading' => __( 'All Feeds', 'custom-facebook-feed' ),
						'columns' => array(
							'nameText' => __( 'Name', 'custom-facebook-feed' ),
							'shortcodeText' => __( 'Shortcode', 'custom-facebook-feed' ),
							'instancesText' => __( 'Instances', 'custom-facebook-feed' ),
							'actionsText' => __( 'Actions', 'custom-facebook-feed' ),
						),
						'bulkActions' => __( 'Bulk Actions', 'custom-facebook-feed' ),
						'legacyFeeds' => array(
							'heading' => __( 'Legacy Feeds', 'custom-facebook-feed' ),
							'toolTip' => __( 'What are Legacy Feeds?', 'custom-facebook-feed' ),
							'toolTipExpanded' => array(
								__( 'Legacy feeds are older feeds from before the version 4 update. You can edit settings for these feeds by using the "Settings" button to the right. These settings will apply to all legacy feeds, just like the settings before version 4, and work in the same way that they used to.', 'custom-facebook-feed' ),
								__( 'You can also create a new feed, which will now have it\'s own individual settings. Modifying settings for new feeds will not affect other feeds.', 'custom-facebook-feed' ),
							),
							'toolTipExpandedAction' => array(
								__( 'Legacy feeds represent shortcodes of old feeds found on your website before <br/>the version 4 update.', 'custom-facebook-feed' ),
								__( 'To edit Legacy feed settings, you will need to use the "Settings" button above <br/>or edit their shortcode settings directly. To delete them, simply remove the <br/>shortcode wherever it is being used on your site.', 'custom-facebook-feed' ),
							),
							'show' => __( 'Show Legacy Feeds', 'custom-facebook-feed' ),
							'hide' => __( 'Hide Legacy Feeds', 'custom-facebook-feed' ),
						),
						'socialWallLinks' => CFF_Feed_Builder::get_social_wall_links(),
						'onboarding' => $this->get_onboarding_text()
					),
			        'addFeaturedPostScreen' => array(
				        'mainHeading' => __( 'Add Featured Post', 'custom-facebook-feed' ),
				        'description' => __( 'Add the URL or ID of the post you want to feature', 'custom-facebook-feed' ),
				        'couldNotFetch' => __( 'Could not fetch post preview', 'custom-facebook-feed' ),
				        'URLorID' => __( 'Post URL or ID', 'custom-facebook-feed' ),
				        'unable' => sprintf( __( 'Unable to retrieve post. Please make sure the link is correct. See %shere%s for more help.', 'custom-facebook-feed' ), '<a href="https://smashballoon.com/doc/how-to-use-the-featured-post-extension-to-display-a-specific-facebook-post/?facebook" target="_blank" rel="noopener">', '</a>' ),
				        'unablePreview' => __( 'Unable to retrieve post. Please make sure the link<br/>entered is correct.', 'custom-facebook-feed' ),
				        'preview' => __( 'Post Preview', 'custom-facebook-feed' ),
				        'previewDescription' => __( 'Once you enter a post URL or ID, click next and the preview will show up here', 'custom-facebook-feed' ),
				        'previewHeading' => __( 'Add a Featured Post', 'custom-facebook-feed' ),
				        'previewText' => __( 'To add a featured post, add it\'s URL or ID in the<br/>"Featured Post URL or ID" field on the left sidebar.', 'custom-facebook-feed' ),
			        ),
			        'addVideosPostScreen' => array(
				        'mainHeading' => __( 'Customize Video Feed', 'custom-facebook-feed' ),
				        'description' => __( 'Add the URL or ID of the post you want to feature', 'custom-facebook-feed' ),
				        'sections' => array(
				        	array(
				        		'id' => 'all',
				        		'heading' => __( 'Show all Videos', 'custom-facebook-feed' ),
				        		'description' => __( 'I want to show all the videos from my Facebook page or group "Videos" page', 'custom-facebook-feed' ),
				        	),
				        	array(
				        		'id' => 'playlist',
				        		'heading' => __( 'Show from a specific Playlist', 'custom-facebook-feed' ),
				        		'description' => __( 'I want to show videos from a specific playlist', 'custom-facebook-feed' ),
				        	)
				        ),
				        'inputLabel' => __( 'Add Playlist URL', 'custom-facebook-feed' ),
				        'inputDescription' => __( 'Add your Facebook playlist URL here. It should look something like: https://facebook.com/videos/v1.124959697/', 'custom-facebook-feed' ),
				        'errorMessage' => __( "Couldn't fetch the playlist, please make sure it's a valid URL", 'custom-facebook-feed' ),
			        ),
					'addFeaturedAlbumScreen' => array(
						'mainHeading' => __( 'Choose Album to Embed', 'custom-facebook-feed' ),
						'description' => __( 'Add the URL or ID of the album you want to feature', 'custom-facebook-feed' ),
						'couldNotFetch' => __( 'Could not fetch album preview', 'custom-facebook-feed' ),
						'URLorID' => __( 'Album URL or ID', 'custom-facebook-feed' ),
						'unable' => sprintf( __( 'Unable to retrieve album. Please make sure the link is correct. See %shere%s for more help.', 'custom-facebook-feed' ), '<a href="https://smashballoon.com/doc/how-to-use-the-album-extension-to-display-photos-from-a-specific-facebook-album/?facebook" target="_blank" rel="noopener">', '</a>' ),
				        'unablePreview' => __( 'Unable to retrieve album. Please make sure the link<br/>entered is correct.', 'custom-facebook-feed' ),
						'preview' => __( 'Album Preview', 'custom-facebook-feed' ),
						'previewDescription' => __( 'Once you enter an album URL or ID, click next and the preview will show up here', 'custom-facebook-feed' ),
				        'previewHeading' => __( 'Add an Album', 'custom-facebook-feed' ),
				        'previewText' => __( 'To add a single album, add it\'s URL or ID in the "Album<br/>URL or ID" field on the left sidebar.', 'custom-facebook-feed' ),
					),
					'mainFooterScreen' => array(
						'heading' => sprintf( __( 'Upgrade to the %sAll Access Bundle%s to get all of our Pro Plugins', 'custom-facebook-feed' ), '<strong>', '</strong>' ),
						'description' => __( 'Includes all Smash Balloon plugins for one low price: Instagram, Facebook, Twitter, YouTube, and Social Wall', 'custom-facebook-feed' ),
						'promo' => sprintf( __( '%sBonus%s Lite users get %s50&#37; Off%s automatically applied at checkout', 'custom-facebook-feed' ), '<span class="cff-bld-ft-bns">', '</span>', '<strong>', '</strong>' ),
					),
					'embedPopupScreen' => array(
						'heading' => __( 'Embed Feed', 'custom-facebook-feed' ),
						'description' => __( 'Add the unique shortcode to any page, post, or widget:', 'custom-facebook-feed' ),
						'description_2' => __( 'Or use the built in WordPress block or widget', 'custom-facebook-feed' ),
						'addPage' => __( 'Add to a Page', 'custom-facebook-feed' ),
						'addWidget' => __( 'Add to a Widget', 'custom-facebook-feed' ),
						'selectPage' => __( 'Select Page', 'custom-facebook-feed' ),
					),
					'dialogBoxPopupScreen'  => array(
						'deleteSourceCustomizer' => array(
							'heading' =>  __( 'Delete "#"?', 'custom-facebook-feed' ),
							'description' => __( 'You are going to delete this source. To retrieve it, you will need to add it again. Are you sure you want to continue?', 'custom-facebook-feed' ),
						),
						'deleteSingleFeed' => array(
							'heading' =>  __( 'Delete "#"?', 'custom-facebook-feed' ),
							'description' => __( 'You are going to delete this feed. You will lose all the settings. Are you sure you want to continue?', 'custom-facebook-feed' ),
						),
						'deleteMultipleFeeds' => array(
							'heading' =>  __( 'Delete Feeds?', 'custom-facebook-feed' ),
							'description' => __( 'You are going to delete these feeds. You will lose all the settings. Are you sure you want to continue?', 'custom-facebook-feed' ),
						),
						'backAllToFeed' => array(
							'heading' =>  __( 'Are you Sure?', 'custom-facebook-feed' ),
							'description' => __( 'Are you sure you want to leave this page, all unsaved settings will be lost, please make sure to save before leaving.', 'custom-facebook-feed' ),
						)
					),
					'translatedText' => $this->get_translated_text(),
					'socialShareLink' => $this->get_social_share_link(),
					'dummyLightBoxData' => $this->get_dummy_lightbox_data(),
			        'feedTypes' => $this->get_feed_types(),
			        'advancedFeedTypes' => $this->get_advanced_feed_types($active_extensions),
			        'feeds' => CFF_Feed_Builder::get_feed_list(),
			        'itemsPerPage' => CFF_Db::RESULTS_PER_PAGE,
			        'feedsCount' => CFF_Db::feeds_count(),
			        'sources' => self::get_source_list(),
			        'links' => self::get_links_with_utm(),
					'legacyFeeds' => $this->get_legacy_feed_list(),
			        'activeExtensions' => $active_extensions,
					'pluginsInfo' => [
						'social_wall' => [
							'installed' => isset( $installed_plugins['social-wall/social-wall.php'] ) ? true : false,
							'activated' => is_plugin_active( 'social-wall/social-wall.php' ),
							'settingsPage' => admin_url( 'admin.php?page=sbsw' ),
						]
					],
					'socialInfo' => $this->get_smashballoon_info(),
			        'svgIcons' => $this->builder_svg_icons(),
			        'sourceConnectionURLs' => CFF_Source::get_connection_urls(),
					'installPluginsPopup' => self::install_plugins_popup()
				);

		        if ( $newly_retrieved_source_connection_data ) {
			        $cff_builder['newSourceData'] = $newly_retrieved_source_connection_data;
		        }

			   //Check For Manual Source Popup
			   $cff_builder['newManualSourcePopup'] = (isset($_GET['manualsource']) && $_GET['manualsource'] == 'true') ? true : false;



		        $maybe_feed_customizer_data = CFF_Feed_Saver_Manager::maybe_feed_customizer_data();
		        if( $maybe_feed_customizer_data ){
		        	//Masonry + Isotope + ImagesLoaded Scripts
		        	wp_enqueue_script(
		        		"cff-isotope", 'https://unpkg.com/isotope-layout@3.0.6/dist/isotope.pkgd.min.js',
		        		null,
		        		'3.0.6',
		        		true
		        	);
		        	wp_enqueue_script(
		        		"cff-images-loaded", 'https://unpkg.com/imagesloaded@4.1.4/imagesloaded.pkgd.min.js',
		        		null,
		        		'4.1.4',
		        		true
		        	);

		        	//Check if carousel Plugin is Active
		        	if( $active_extensions['carousel'] == true ){
		        		wp_enqueue_script(
			        		"cff-carousel-js", 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
			        		null,
			        		'2.3.4',
			        		true
			        	);
			        	wp_enqueue_script(
			        		"cff-autoheight", CFF_PLUGIN_URL.'admin/builder/assets/js/owl.autoheight.js',
			        		null,
			        		CFFVER,
			        		true
			        	);
			        	wp_enqueue_style(
				        	'cff-carousel-css',
				        	'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css',
				        	false,
				        	CFFVER
				        );
				        wp_enqueue_style(
				        	'cff-carousel-theme-css',
				        	'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css',
				        	false,
				        	CFFVER
				        );


		        	}

		        	wp_enqueue_style(
			        	'feed-builder-preview-style',
			        	CFF_PLUGIN_URL . 'admin/builder/assets/css/preview.css',
			        	false,
			        	CFFVER
			        );
		        	$cff_builder['customizerFeedData'] 			=  $maybe_feed_customizer_data;
		        	$cff_builder['customizerSidebarBuilder'] 	=  \CustomFacebookFeed\Builder\Tabs\CFF_Builder_Customizer_Tab::get_customizer_tabs();
		        	$cff_builder['wordpressPageLists']	= $this->get_wp_pages();
		        	$cff_builder['facebook_feed_dismiss_lite'] = get_transient( 'facebook_feed_dismiss_lite' );

		        	//Date
		        	global $wp_locale;
		        	wp_enqueue_script(
		        		"cff-date_i18n", CFF_PLUGIN_URL.'admin/builder/assets/js/date_i18n.js',
		        		null,
		        		CFFVER,
		        		true
		        	);

					$monthNames = array_map(
						array(&$wp_locale, 'get_month'),
						range(1, 12)
					);
					$monthNamesShort = array_map(
						array(&$wp_locale, 'get_month_abbrev'),
						$monthNames
					);
					$dayNames = array_map(
						array(&$wp_locale, 'get_weekday'),
						range(0, 6)
					);
					$dayNamesShort = array_map(
						array(&$wp_locale, 'get_weekday_abbrev'),
						$dayNames
					);
					wp_localize_script("cff-date_i18n",
						"DATE_I18N", array(
						  "month_names" => $monthNames,
						  "month_names_short" => $monthNamesShort,
						  "day_names" => $dayNames,
						  "day_names_short" => $dayNamesShort
						)
					);
		        }

		        wp_enqueue_style(
		        	'feed-builder-style',
		        	CFF_PLUGIN_URL . 'admin/builder/assets/css/builder.css',
		        	false,
		        	CFFVER
		        );

		        self::global_enqueue_ressources_scripts();

		        wp_enqueue_script(
		        	'feed-builder-app',
		        	CFF_PLUGIN_URL.'admin/builder/assets/js/builder.js',
		        	null,
		        	CFFVER,
		        	true
		        );


		        // Customize screens
		        $cff_builder['customizeScreens'] = $this->get_customize_screens_text();
		        wp_localize_script(
		        	'feed-builder-app',
		        	'cff_builder',
		        	$cff_builder
		        );


        	endif;
        endif;
	}


	/**
	 * Global JS + CSS Files
	 *
	 * Shared JS + CSS ressources for the admin panel
	 *
	 * @since 4.0
	 */
   public static function global_enqueue_ressources_scripts($is_settings = false){
	   	wp_enqueue_style(
	   		'feed-global-style',
	   		CFF_PLUGIN_URL . 'admin/builder/assets/css/global.css',
	   		false,
	   		CFFVER
	   	);

	   	wp_enqueue_script(
			'feed-builder-vue',
			'https://cdn.jsdelivr.net/npm/vue@2.6.12',
			null,
			"2.6.12",
			true
		);
		wp_enqueue_script(
			'feed-colorpicker-vue',
			CFF_PLUGIN_URL.'admin/builder/assets/js/vue-color.min.js',
			null,
			CFFVER,
			true
		);

	   	wp_enqueue_script(
	   		'feed-builder-ressources',
	   		CFF_PLUGIN_URL.'admin/builder/assets/js/ressources.js',
	   		null,
	   		CFFVER,
	   		true
	   	);

	   	wp_enqueue_script(
	   		'sb-dialog-box',
	   		CFF_PLUGIN_URL.'admin/builder/assets/js/confirm-dialog.js',
	   		null,
	   		CFFVER,
	   		true
	   	);

	   	wp_enqueue_script(
	   		'sb-add-source',
	   		CFF_PLUGIN_URL.'admin/builder/assets/js/add-source.js',
	   		null,
	   		CFFVER,
	   		true
	   	);

	   	wp_enqueue_script(
	   		'install-plugin-popup',
	   		CFF_PLUGIN_URL.'admin/builder/assets/js/install-plugin-popup.js',
	   		null,
	   		CFFVER,
	   		true
	   	);

		$newly_retrieved_source_connection_data = CFF_Source::maybe_source_connection_data();
	   	$cff_source = array(
	   		'sources' => self::get_source_list(),
	   		'sourceConnectionURLs' => CFF_Source::get_connection_urls($is_settings)
	   	);
	   	if ( $newly_retrieved_source_connection_data ) {
	   		$cff_source['newSourceData'] = $newly_retrieved_source_connection_data;
	   	}

	   	wp_localize_script(
	   		'sb-add-source',
	   		'cff_source',
	   		$cff_source
	   	);
	}


	/**
	 * Whether this is the free or Pro version
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function get_plugin_type() {
		if ( function_exists( 'cff_main_pro' ) ) {
			return 'pro';
		}
		return 'free';
	}

	/**
	 * Get WP Pages List
	 *
	 * @return string
	 *
	 * @since 4.0
	 */
	public function get_wp_pages(){
		$pagesList = get_pages();
		$pagesResult = [];
		if(is_array($pagesList)){
			foreach ($pagesList as $page) {
				array_push($pagesResult, ['id' => $page->ID, 'title' => $page->post_title]);
			}
		}
		return $pagesResult;
	}


	/**
	 * Get Generic text
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_generic_text(){
		return array(
			'done' => __( 'Done', 'custom-facebook-feed' ),
			'title' => __( 'Settings', 'custom-facebook-feed' ),
			'dashboard' => __( 'Dashboard', 'custom-facebook-feed' ),
			'addNew' => __( 'Add New', 'custom-facebook-feed' ),
			'addSource' => __( 'Add Source', 'custom-facebook-feed' ),
			'previous' => __( 'Previous', 'custom-facebook-feed' ),
			'next' => __( 'Next', 'custom-facebook-feed' ),
			'finish' => __( 'Finish', 'custom-facebook-feed' ),
			'new' => __( 'New', 'custom-facebook-feed' ),
			'update' => __( 'Update', 'custom-facebook-feed' ),
			'upgrade' => __( 'Upgrade', 'custom-facebook-feed' ),
			'settings' => __( 'Settings', 'custom-facebook-feed' ),
			'back' => __( 'Back', 'custom-facebook-feed' ),
			'backAllFeeds' => __( 'Back to all feeds', 'custom-facebook-feed' ),
			'createFeed' => __( 'Create Feed', 'custom-facebook-feed' ),
			'add' => __( 'Add', 'custom-facebook-feed' ),
			'change' => __( 'Change', 'custom-facebook-feed' ),
			'getExtention' => __( 'Get Extension', 'custom-facebook-feed' ),
			'viewDemo' => __( 'View Demo', 'custom-facebook-feed' ),
			'includes' => __( 'Includes', 'custom-facebook-feed' ),
			'photos' => __( 'Photos', 'custom-facebook-feed' ),
			'photo' => __( 'Photo', 'custom-facebook-feed' ),
			'apply' => __( 'Apply', 'custom-facebook-feed' ),
			'copy' => __( 'Copy', 'custom-facebook-feed' ),
			'edit' => __( 'Edit', 'custom-facebook-feed' ),
			'duplicate' => __( 'Duplicate', 'custom-facebook-feed' ),
			'delete' => __( 'Delete', 'custom-facebook-feed' ),
			'shortcode' => __( 'Shortcode', 'custom-facebook-feed' ),
			'clickViewInstances' => __( 'Click to view Instances', 'custom-facebook-feed' ),
			'usedIn' => __( 'Used in', 'custom-facebook-feed' ),
			'place' => __( 'place', 'custom-facebook-feed' ),
			'places' => __( 'places', 'custom-facebook-feed' ),
			'item' => __( 'Item', 'custom-facebook-feed' ),
			'items' => __( 'Items', 'custom-facebook-feed' ),
			'learnMore' => __( 'Learn More', 'custom-facebook-feed' ),
			'location' => __( 'Location', 'custom-facebook-feed' ),
			'page' => __( 'Page', 'custom-facebook-feed' ),
			'copiedClipboard' => __( 'Copied to Clipboard', 'custom-facebook-feed' ),
			'feedImported' => __( 'Feed imported successfully', 'custom-facebook-feed' ),
			'failedToImportFeed' => __( 'Failed to import feed', 'custom-facebook-feed' ),
			'timeline' => __( 'Timeline', 'custom-facebook-feed' ),
			'help' => __( 'Help', 'custom-facebook-feed' ),
			'admin' => __( 'Admin', 'custom-facebook-feed' ),
			'member' => __( 'Member', 'custom-facebook-feed' ),
			'reset' => __( 'Reset', 'custom-facebook-feed' ),
			'preview' => __( 'Preview', 'custom-facebook-feed' ),
			'name' => __( 'Name', 'custom-facebook-feed' ),
			'id' => __( 'ID', 'custom-facebook-feed' ),
			'token' => __( 'Token', 'custom-facebook-feed' ),
			'confirm' => __( 'Confirm', 'custom-facebook-feed' ),
			'cancel' => __( 'Cancel', 'custom-facebook-feed' ),
			'clearFeedCache' => __( 'Clear Feed Cache', 'custom-facebook-feed' ),
			'saveSettings' => __( 'Save Changes', 'custom-facebook-feed' ),
			'feedName' => __( 'Feed Name', 'custom-facebook-feed' ),
			'shortcodeText' => __( 'Shortcode', 'custom-facebook-feed' ),
			'general' => __( 'General', 'custom-facebook-feed' ),
			'feeds' => __( 'Feeds', 'custom-facebook-feed' ),
			'translation' => __( 'Translation', 'custom-facebook-feed' ),
			'advanced' => __( 'Advanced', 'custom-facebook-feed' ),
			'error' => __( 'Error:', 'custom-facebook-feed' ),
			'errorNotice' => __( 'There was an error when trying to connect to Facebook.', 'custom-facebook-feed' ),
			'errorDirections' => '<a href="https://smashballoon.com/custom-facebook-feed/docs/errors/" target="_blank" rel="noopener">' . __( 'Directions on How to Resolve This Issue', 'custom-facebook-feed' )  . '</a>',
			'errorSource' => __( 'Source Invalid', 'custom-facebook-feed' ),
			'updateRequired' => __( 'Update Required', 'custom-facebook-feed' ),
			'invalid' => __( 'Invalid', 'custom-facebook-feed' ),
			'reconnect' => __( 'Reconnect', 'custom-facebook-feed' ),
			'feed' => __( 'feed', 'custom-facebook-feed' ),
			'sourceNotUsedYet' => __( 'Source is not used yet', 'custom-facebook-feed' ),
			'notification' => array(
				'feedSaved' => array(
					'type' => 'success',
					'text' => __( 'Feed saved successfully', 'custom-facebook-feed' )
				),
				'feedSavedError' => array(
					'type' => 'error',
					'text' => __( 'Error saving Feed', 'custom-facebook-feed' )
				),
				'previewUpdated' => array(
					'type' => 'success',
					'text' => __( 'Preview updated successfully', 'custom-facebook-feed' )
				),
				'carouselLayoutUpdated' => array(
					'type' => 'success',
					'text' => __( 'Carousel updated successfully', 'custom-facebook-feed' )
				),
				'unkownError' => array(
					'type' => 'error',
					'text' => __( 'Unknown error occurred', 'custom-facebook-feed' )
				),
				'cacheCleared' => array(
					'type' => 'success',
					'text' => __( 'Feed cache cleared', 'custom-facebook-feed' )
				),
				'selectSourceError' => array(
					'type' => 'error',
					'text' => __( 'Please select a source for your feed', 'custom-facebook-feed' )
				),
			),
			'install' => __( 'Install', 'custom-facebook-feed' ),
			'installed' => __( 'Installed', 'custom-facebook-feed' ),
			'activate' => __( 'Activate', 'custom-facebook-feed' ),
			'installedAndActivated' => __( 'Installed & Activated', 'custom-facebook-feed' ),
			'free' => __( 'Free', 'custom-facebook-feed' ),
			'invalidLicenseKey' => __( 'Invalid license key', 'custom-facebook-feed' ),
			'licenseActivated' => __( 'License activated', 'custom-facebook-feed' ),
			'licenseDeactivated' => __( 'License Deactivated', 'custom-facebook-feed' ),
			'carouselLayoutUpdated'=> array(
				'type' => 'success',
				'text' => __( 'Carousel Layout updated', 'custom-facebook-feed' )
			),
			'getMoreFeatures' => __( 'Get more features with Custom Facebook Feed Pro', 'custom-facebook-feed' ),
			'liteFeedUsers' => __( 'Lite Feed Users get 50% OFF', 'custom-facebook-feed' ),
			'tryDemo' => __( 'Try Demo', 'custom-facebook-feed' ),
			'seeProDemo' => __( 'See the Pro Demo', 'custom-facebook-feed' ),
			'seeTheDemo' => __( 'See the Demo', 'custom-facebook-feed' ),
			'displayImagesVideos' => __( 'Display images and videos in posts', 'custom-facebook-feed' ),
			'viewLikesShares' => __( 'View likes, shares and comments', 'custom-facebook-feed' ),
			'allFeedTypes' => __( 'All Feed Types: Photos, Albums, Events and more', 'custom-facebook-feed' ),
			'abilityToLoad' => __( 'Ability to “Load More” posts', 'custom-facebook-feed' ),
			'andMuchMore' => __( 'And Much More!', 'custom-facebook-feed' ),
			'cffFreeCTAFeatures' => array(
				__( 'Filter posts', 'custom-facebook-feed' ),
				__( 'Popup photo/video lighbox', 'custom-facebook-feed' ),
				__( '30 day money back guarantee', 'custom-facebook-feed' ),
				__( 'Multiple post layout options', 'custom-facebook-feed' ),
				__( 'Video player (HD, 360, Live)', 'custom-facebook-feed' ),
				__( 'Fast, friendly and effective support', 'custom-facebook-feed' ),
			),
			'ctaShowFeatures' => __( 'Show', 'custom-facebook-feed' ),
			'ctaShowFeatures' => __( 'Show', 'custom-facebook-feed' ),

			'ctaShowFeatures' => __( 'Show Features', 'custom-facebook-feed' ),
			'ctaHideFeatures' => __( 'Hide Features', 'custom-facebook-feed' ),
			'upgradeToPro' => __( 'Upgrade to Pro', 'custom-facebook-feed' ),
			'clickingHere' => __( 'clicking here', 'custom-facebook-feed' ),

			'redirectLoading' => array(
				'heading' =>  __( 'Redirecting to connect.smashballoon.com', 'custom-facebook-feed' ),
				'description' =>  __( 'You will be redirected to our app so you can connect your account in 5 seconds', 'custom-facebook-feed' ),
			),

		);
	}


	/**
	 * Select Source Screen Text
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function select_source_screen_text() {
		return array(
			'mainHeading' => __( 'Select a Source', 'custom-facebook-feed' ),
			'description' => __( 'Sources are Facebook pages or groups your feed will fetch posts, photos, or videos from', 'custom-facebook-feed' ),
			'eventsToolTip' => array(
				__( 'To display events from a Facebook Page<br/>you need to create your own Facebook app.', 'custom-facebook-feed' ),
				__( 'Click "+ Add New" to get started.', 'custom-facebook-feed' )
			),
			'groupsToolTip' => array(
				__( 'Due to Facebook limitations, it\'s not possible to display photo feeds from a Group, only a Page.', 'custom-facebook-feed' )
			),
			'updateHeading' => __( 'Update Source', 'custom-facebook-feed' ),
			'updateDescription' => __( 'Select a source from your connected Facebook Pages and Groups. Or, use "Add New" to connect a new one.', 'custom-facebook-feed' ),
			'updateFooter' => __( 'Add multiple Facebook Pages or Groups to a feed with our Multifeed extension', 'custom-facebook-feed' ),
			'noSources' => __( 'Please add a source in order to display a feed. Go to the "Settings" tab -> "Sources" section -> Click "Add New" to connect a source.', 'custom-facebook-feed' ),

			'modal' => array(
				'addNew' => __( 'Add a New Source', 'custom-facebook-feed' ),
				'selectSourceType' => __( 'Select Source Type', 'custom-facebook-feed' ),
				'connectAccount' => __( 'Connect a Facebook Account', 'custom-facebook-feed' ),
				'connectAccountDescription' => __( 'This does not give us permission to manage your Facebook Pages or Groups, it simply allows the plugin to see a list of them and retrieve their public content from the API.', 'custom-facebook-feed' ),
				'connect' => __( 'Connect', 'custom-facebook-feed' ),
				'enterEventToken' => __( 'Enter Events Access Token', 'custom-facebook-feed' ),
				'enterEventTokenDescription' => sprintf( __( 'Due to restrictions by Facebook, you need to create a Facebook app and then paste that app Access Token here. We have a guide to help you with just that, which you can read %shere%s', 'custom-facebook-feed' ), '<a href="https://smashballoon.com/custom-facebook-feed/page-token/" target="_blank" rel="noopener">', '</a>' ),
				'alreadyHave' => __( 'Already have a Facebook Access Token for your Page or Group?', 'custom-facebook-feed' ),
				'addManuallyLink' => __( 'Add Account Manually', 'custom-facebook-feed' ),
				'selectPage' => __( 'Select a Facebook Page', 'custom-facebook-feed' ),
				'selectGroup' => __( 'Select a Facebook Group', 'custom-facebook-feed' ),
				'showing' => __( 'Showing', 'custom-facebook-feed' ),
				'facebook' => __( 'Facebook', 'custom-facebook-feed' ),
				'pages' => __( 'Pages', 'custom-facebook-feed' ),
				'groups' => __( 'Groups', 'custom-facebook-feed' ),
				'connectedTo' => __( 'connected to', 'custom-facebook-feed' ),
				'addManually' => __( 'Add a Source Manually', 'custom-facebook-feed' ),
				'addSource' => __( 'Add Source', 'custom-facebook-feed' ),
				'sourceType' => __( 'Source Type', 'custom-facebook-feed' ),
				'pageOrGroupID' => __( 'Facebook Page or Group ID', 'custom-facebook-feed' ),
				'fbPageID' => __( 'Facebook Page ID', 'custom-facebook-feed' ),
				'eventAccessToken' => __( 'Event Access Token', 'custom-facebook-feed' ),
				'enterID' => __( 'Enter ID', 'custom-facebook-feed' ),
				'accessToken' => __( 'Facebook Access Token', 'custom-facebook-feed' ),
				'enterToken' => __( 'Enter Token', 'custom-facebook-feed' ),
				'addApp' => __( 'Add Facebook App to your group', 'custom-facebook-feed' ),
				'addAppDetails' => __( 'To get posts from your group, Facebook requires the "Smash Balloon Wordpress" app to be added in your group settings. Just follow the directions here:', 'custom-facebook-feed' ),
				'addAppSteps' => [
					__( 'Go to your group settings page by ', 'custom-facebook-feed' ),
					sprintf( __( 'Search for "Smash Balloon WordPress" and select our app %s(see screenshot)%s', 'custom-facebook-feed' ), '<a href="JavaScript:void(0);" id="cff-group-app-tooltip">', '<img class="cff-group-app-screenshot sb-tr-1" src="' . trailingslashit( CFF_PLUGIN_URL ) . 'admin/assets/img/group-app.png" alt="Thumbnail Layout"></a>'),
					__( 'Click "Add" and you are done.', 'custom-facebook-feed' )
				],
				'reconnectingAppDir' => __( 'If you are reconnecting an existing Group then make sure to follow the directions above to add this new app to your Group settings. The previous app will no longer work. This is required in order for new posts to be retrieved.', 'custom-facebook-feed' ),
				'appMemberInstructions' => sprintf( __( 'To display a feed form this group, Facebook requires the admin to add the Smash Balloon app in the group settings. Please ask an admin to follow the %sdirections here%s to add the app.', 'custom-facebook-feed' ), '<a href="https://smashballoon.com/doc/display-facebook-group-feed/" target="_blank" rel="noopener noreferrer">', '</a>' ) . '<br><br>' . __( 'Once this is done, you will be able to display a feed from this group.', 'custom-facebook-feed' ),
				'notAdmin' => __( 'For groups you are not an administrator of', 'custom-facebook-feed' ),
				'disclaimer' => sprintf( __( 'Please note: There are Facebook limitations to displaying group content which may prevent older posts from being displayed. Please %ssee here%s for more information.', 'custom-facebook-feed' ), '<a href="https://smashballoon.com/doc/facebook-api-change-limits-groups-to-90-days/" target="_blank" rel="noopener noreferrer">', '</a>' ),
				'noGroupTooltip' => __( 'Due to Facebook limitations, it\'s not possible to display photo feeds from a Group, only a Page.', 'custom-facebook-feed' )
			),
			'footer' => array(
				'heading' => __( 'Add feeds for popular social platforms with our other plugins', 'custom-facebook-feed' ),
			),
			'page' => __( 'Page', 'custom-facebook-feed' ),
			'group' => __( 'Group', 'custom-facebook-feed' ),
		);
	}


	/**
	 * For types listed on the top of the select feed type screen
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_feed_types() {
    	$feed_types = array(
		    array(
			    'type' => 'timeline',
			    'title'=> __( 'Timeline', 'custom-facebook-feed' ),
			    'description'=> __( 'Display posts from your Facebook timeline', 'custom-facebook-feed' ),
			    'icon'	=>  'timelineIcon'
		    )
	    );

    	return $feed_types;
	}

	/**
	 * For types listed on the bottom of the select feed type screen
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_advanced_feed_types($active_extensions) {
		$feed_types = array(
			array(
			    'type' => 'photos',
			    'title' => __( 'Photos', 'custom-facebook-feed' ),
			    'description' => __( 'Display photos from your Facebook Photos page', 'custom-facebook-feed' ),
				'extensionActive' => false,
			    'icon'	=>  'photosIcon'
		    ),
		    array(
			    'type' => 'videos',
			    'title' => __( 'Videos', 'custom-facebook-feed' ),
			    'description' => __( 'Display videos from your Facebook Videos page', 'custom-facebook-feed' ),
				'extensionActive' => false,
			    'icon'	=>  'videosIcon'
		    ),
		    array(
			    'type' => 'albums',
			    'title' => __( 'Albums', 'custom-facebook-feed' ),
			    'description' => __( 'Display all albums from your Facebook Photos page', 'custom-facebook-feed' ),
				'extensionActive' => false,
			    'icon'	=>  'albumsIcon'
		    ),
		    array(
			    'type' => 'events',
			    'title' => __( 'Events', 'custom-facebook-feed' ),
			    'description' => __( 'Display events from your Facebook Events page', 'custom-facebook-feed' ),
				'extensionActive' => false,
			    'icon'	=>  'eventsIcon'
		    ),
			array(
				'type' => 'reviews',
				'title'=> __( 'Reviews', 'custom-facebook-feed' ),
				'description'=> __( 'Show reviews or recommendations from your Facebook page', 'custom-facebook-feed' ),
				'extensionActive' => false,
			    'icon'	=>  'reviewsIcon'
			),
			array(
				'type' => 'featuredpost',
				'title' => __( 'Single Featured Post', 'custom-facebook-feed' ),
				'description' => __( 'Display a single post from your Facebook page', 'custom-facebook-feed' ),
				'extensionActive' => false,
			    'icon'	=>  'featuredpostIcon'
			),
			array(
				'type' => 'singlealbum',
				'title' => __( 'Single Album', 'custom-facebook-feed' ),
				'description' => __( 'Display the contents of a single Album from your Facebook page', 'custom-facebook-feed' ),
				'extensionActive' => false,
			    'icon'	=>  'singlealbumIcon'
			),
			array(
				'type' => 'socialwall',
				'title' => __( 'Social Wall', 'custom-facebook-feed' ),
				'description' => __( 'Create a feed which combines sources from multiple social platforms', 'custom-facebook-feed' ),
				'extensionActive' => false,
			    'icon'	=>  'socialwallIcon'
			)
		);

		return $feed_types;
	}

	/**
	 * Returns an associate array of all existing feeds along with their data
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_feed_list( $feeds_args = array() ) {
		$feeds_data = CFF_Db::feeds_query($feeds_args);

		$i = 0;
		foreach ( $feeds_data as $single_feed ) {
			$args = array(
				'feed_id' => '*' . $single_feed['id'],
				'html_location' => array( 'content' ),
			);
			$count = \CustomFacebookFeed\CFF_Feed_Locator::count( $args );

			$content_locations = \CustomFacebookFeed\CFF_Feed_Locator::facebook_feed_locator_query( $args );

			// if this is the last page, add in the header footer and sidebar locations
			if ( count( $content_locations ) < CFF_Db::RESULTS_PER_PAGE ) {

				$args = array(
					'feed_id' => '*' . $single_feed['id'],
					'html_location' => array( 'header', 'footer', 'sidebar' ),
					'group_by' => 'html_location'
				);
				$other_locations = \CustomFacebookFeed\CFF_Feed_Locator::facebook_feed_locator_query( $args );

				$locations = array();

				$combined_locations = array_merge( $other_locations, $content_locations );
			} else {
				$combined_locations = $content_locations;
			}

			foreach ( $combined_locations as $location ) {
				$page_text = get_the_title( $location['post_id'] );
				if ( $location['html_location'] === 'header' ) {
					$html_location = __( 'Header', 'custom-facebook-feed' );
				} elseif ( $location['html_location'] === 'footer' ) {
					$html_location = __( 'Footer', 'custom-facebook-feed' );
				} elseif ( $location['html_location'] === 'sidebar' ) {
					$html_location = __( 'Sidebar', 'custom-facebook-feed' );
				} else {
					$html_location = __( 'Content', 'custom-facebook-feed' );
				}
				$shortcode_atts = json_decode( $location['shortcode_atts'], true );
				$shortcode_atts = is_array( $shortcode_atts ) ? $shortcode_atts : array();

				$full_shortcode_string = '[custom-facebook-feed';
				foreach ( $shortcode_atts as $key => $value ) {
					if ( ! empty( $value ) ) {
						$full_shortcode_string .= ' ' . esc_html( $key ) . '="' . esc_html( $value ) . '"';
					}
				}
				$full_shortcode_string .= ']';

				$locations[] = [
					'link' => esc_url( get_the_permalink( $location['post_id'] ) ),
					'page_text' => $page_text,
					'html_location' => $html_location,
					'shortcode' => $full_shortcode_string
				];
			}
			$feeds_data[ $i ]['instance_count'] = $count;
			$feeds_data[ $i ]['location_summary'] = $locations;
			$feeds_data[ $i ]['settings'] = json_decode( $feeds_data[ $i ]['settings'] );

			$i++;
		}
		return $feeds_data;
	}

	/**
	 * Return legacy feed source name
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_legacy_feed_name( $sources_list, $source_id ) {
		foreach ($sources_list as $source) {
			if($source['account_id'] == $source_id){
				return $source['username'];
			}
		}
		return $source_id;
	}

	/**
	 * Returns an associate array of all existing sources along with their data
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_legacy_feed_list() {
		$cff_statuses = get_option( 'cff_statuses', array() );
		$sources_list = CFF_Feed_Builder::get_source_list();

		#if ( empty( $cff_statuses['support_legacy_shortcode'] ) ) {
		#	return [];
		#}

		$args = array(
			'html_location' => array( 'header', 'footer', 'sidebar', 'content' ),
			'group_by' => 'shortcode_atts',
			'page' => 1
		);
		$feeds_data = \CustomFacebookFeed\CFF_Feed_Locator::legacy_facebook_feed_locator_query( $args );

		if ( empty( $feeds_data ) ) {
			$args = array(
				'html_location' => array( 'header', 'footer', 'sidebar', 'content', 'unknown' ),
				'group_by' => 'shortcode_atts',
				'page' => 1
			);
			$feeds_data = \CustomFacebookFeed\CFF_Feed_Locator::legacy_facebook_feed_locator_query( $args );
		}

		$feed_saver = new CFF_Feed_Saver( 'legacy' );
		$settings = $feed_saver->get_feed_settings();

		$default_type = 'timeline';

		if ( isset( $settings['feedtype'] ) ) {
			$default_type = $settings['feedtype'];

		} elseif ( isset( $settings['type'] ) ) {
			if ( strpos( $settings['type'], ',' ) === false ) {
				$default_type = $settings['type'];
			}
		}
		$i = 0;
		$reindex = false;
		foreach ( $feeds_data as $single_feed ) {
			$args = array(
				'shortcode_atts' => $single_feed['shortcode_atts'],
				'html_location' => array( 'content' ),
			);
			$content_locations = \CustomFacebookFeed\CFF_Feed_Locator::facebook_feed_locator_query( $args );

			$count = \CustomFacebookFeed\CFF_Feed_Locator::count( $args );
			if ( count( $content_locations ) < CFF_Db::RESULTS_PER_PAGE ) {

				$args = array(
					'feed_id' => $single_feed['feed_id'],
					'html_location' => array( 'header', 'footer', 'sidebar' ),
					'group_by' => 'html_location'
				);
				$other_locations = \CustomFacebookFeed\CFF_Feed_Locator::facebook_feed_locator_query( $args );

				$combined_locations = array_merge( $other_locations, $content_locations );
			} else {
				$combined_locations = $content_locations;
			}

			$locations = array();
			foreach ( $combined_locations as $location ) {
				$page_text = get_the_title( $location['post_id'] );
				if ( $location['html_location'] === 'header' ) {
					$html_location = __( 'Header', 'custom-facebook-feed' );
				} elseif ( $location['html_location'] === 'footer' ) {
					$html_location = __( 'Footer', 'custom-facebook-feed' );
				} elseif ( $location['html_location'] === 'sidebar' ) {
					$html_location = __( 'Sidebar', 'custom-facebook-feed' );
				} else {
					$html_location = __( 'Content', 'custom-facebook-feed' );
				}
				$shortcode_atts = json_decode( $location['shortcode_atts'], true );
				$shortcode_atts = is_array( $shortcode_atts ) ? $shortcode_atts : array();

				$full_shortcode_string = '[custom-facebook-feed';
				foreach ( $shortcode_atts as $key => $value ) {
					if ( ! empty( $value ) ) {
						$full_shortcode_string .= ' ' . esc_html( $key ) . '="' . esc_html( $value ) . '"';
					}
				}
				$full_shortcode_string .= ']';

				$locations[] = [
					'link' => esc_url( get_the_permalink( $location['post_id'] ) ),
					'page_text' => $page_text,
					'html_location' => $html_location,
					'shortcode' => $full_shortcode_string
				];
			}
			$shortcode_atts = json_decode( $feeds_data[ $i ]['shortcode_atts'], true );
			$shortcode_atts = is_array( $shortcode_atts ) ? $shortcode_atts : array();

			$full_shortcode_string = '[custom-facebook-feed';
			foreach ( $shortcode_atts as $key => $value ) {
				if ( ! empty( $value ) ) {
					$full_shortcode_string .= ' ' . esc_html( $key ) . '="' . esc_html( $value ) . '"';
				}
			}
			$full_shortcode_string .= ']';

			$feeds_data[ $i ]['shortcode'] = $full_shortcode_string;
			$feeds_data[ $i ]['instance_count'] = $count;
			$feeds_data[ $i ]['location_summary'] = $locations;
			$feeds_data[ $i ]['feed_name'] = self::get_legacy_feed_name($sources_list , $feeds_data[ $i ]['feed_id']);
			$feeds_data[ $i ]['feed_type'] = $default_type;

			if ( isset( $shortcode_atts['feedtype'] ) ) {
				$feeds_data[ $i ]['feed_type'] = $shortcode_atts['feedtype'];

			} elseif ( isset( $shortcode_atts['type'] ) ) {
				if ( strpos( $shortcode_atts['type'], ',' ) === false ) {
					$feeds_data[ $i ]['feed_type'] = $shortcode_atts['type'];
				}
			}

			if ( isset( $feeds_data[ $i ]['id'] ) ) {
				unset( $feeds_data[ $i ]['id'] );
			}

			if ( isset( $feeds_data[ $i ]['html_location'] ) ) {
				unset( $feeds_data[ $i ]['html_location'] );
			}

			if ( isset( $feeds_data[ $i ]['last_update'] ) ) {
				unset( $feeds_data[ $i ]['last_update'] );
			}

			if ( isset( $feeds_data[ $i ]['post_id'] ) ) {
				unset( $feeds_data[ $i ]['post_id'] );
			}

			if ( ! empty( $shortcode_atts['feed'] ) ) {
				$reindex = true;
				unset( $feeds_data[ $i ] );
			}

			if ( isset( $feeds_data[ $i ]['shortcode_atts'] ) ) {
				unset( $feeds_data[ $i ]['shortcode_atts'] );
			}

			$i++;
		}

		if ( $reindex ) {
			$feeds_data = array_values( $feeds_data );
		}

		// if there were no feeds found in the locator table we still want the legacy settings to be available
		// if it appears as though they had used version 3.x or under at some point.
		if ( empty( $feeds_data )
		     && ! is_array( $cff_statuses['support_legacy_shortcode'] )
		     && ( $cff_statuses['support_legacy_shortcode'] ) ) {

			$feeds_data = array(
				array(
					'feed_id' => __( 'Legacy Feed', 'custom-facebook-feed' ) . ' ' . __( '(unknown location)', 'custom-facebook-feed' ),
					'feed_name' => __( 'Legacy Feed', 'custom-facebook-feed' ) . ' ' . __( '(unknown location)', 'custom-facebook-feed' ),
					'shortcode' => '[custom-facebook-feed]',
					'feed_type' => '',
					'instance_count' => false,
					'location_summary' => array()
				)
			);
		}

		return $feeds_data;
	}

	/**
	 * Returns an associate array of all existing sources along with their data
	 *
	 * @param int $page
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_source_list( $page = 1 ) {
		$args['page'] = $page;
		$source_data = CFF_Db::source_query( $args );
	   	$encryption = new SB_Facebook_Data_Encryption();

		$legacy_data = \CustomFacebookFeed\CFF_FB_Settings::get_legacy_settings( array() );

		$legacy_id = ! empty( $legacy_data['id'] ) ? $legacy_data['id'] : '';

		$return = array();
		foreach ( $source_data as $source ) {
			$info = ! empty( $source['info'] ) ? json_decode($encryption->decrypt( $source['info'] ) ) : array();
			$avatar = \CustomFacebookFeed\CFF_Parse::get_avatar( $info );

			$source['avatar_url'] = $avatar;

			$source['needs_update'] = CFF_Source::needs_update( $source, $info );

			if ( $source['account_id'] === $legacy_id ) {
				$source['used_in'] = $source['used_in'] + 1;
				if ( ! isset( $source['instances'] ) ) {
					$source['instances'] = array();
				}
				$source['instances'][] = [
					'id' => 'legacy',
					'feed_name' => __( 'Legacy Feeds', 'custom-facebook-feed' ),
					'settings' => $legacy_data,
					'author' => 1,
					'status' => 'publish',
					'last_modified' => '2021-07-07 19:46:09'
				];
			}

			$return[] = $source;
		}

		return $return;
	}

	/**
	 * Get Links with UTM
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_links_with_utm() {
		$license_key = null;
		if ( get_option('cff_license_key') ) {
			$license_key = get_option('cff_license_key');
		}
		$all_access_bundle = sprintf('https://smashballoon.com/all-access/?license_key=%s&upgrade=true&utm_campaign=facebook-free&utm_source=all-feeds&utm_medium=footer-banner&utm_content=learn-more', $license_key);
		$all_access_bundle_popup = sprintf('https://smashballoon.com/all-access/?license_key=%s&upgrade=true&utm_campaign=facebook-free&utm_source=balloon&utm_medium=all-access', $license_key);
		$sourceCombineCTA = sprintf('https://smashballoon.com/social-wall/?license_key=%s&upgrade=true&utm_campaign=facebook-free&utm_source=customizer&utm_medium=sources&utm_content=social-wall', $license_key);

		return array(
			'allAccessBundle' => $all_access_bundle,
			'popup' => array(
				'allAccessBundle' => $all_access_bundle_popup,
				'fbProfile' => 'https://www.facebook.com/SmashBalloon/',
				'twitterProfile' => 'https://twitter.com/smashballoon',
			),
			'sourceCombineCTA' => $sourceCombineCTA,
			'multifeedCTA' => 'https://smashballoon.com/extensions/multifeed/?utm_campaign=facebook-free&utm_source=customizer&utm_medium=sources&utm_content=multifeed',
			'doc' => 'https://smashballoon.com/docs/facebook/?utm_campaign=facebook-free&utm_source=support&utm_medium=view-documentation-button&utm_content=view-documentation',
			'blog' => 'https://smashballoon.com/blog/?utm_campaign=facebook-free&utm_source=support&utm_medium=view-blog-button&utm_content=view-blog',
			'gettingStarted' => 'https://smashballoon.com/docs/getting-started/?utm_campaign=facebook-free&utm_source=support&utm_medium=getting-started-button&utm_content=getting-started',
		);
	}

	/**
	 * Gets a list of info
	 * Used in multiple places in the feed creator
	 * Other Platforms + Social Links
	 * Upgrade links
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_smashballoon_info() {
		$smash_info = [
			'colorSchemes' => [
				'facebook' => '#006BFA',
				'twitter' => '#1B90EF',
				'instagram' => '#BA03A7',
				'youtube' => '#EB2121',
				'linkedin' => '#007bb6',
				'mail' => '#666',
				'smash' => '#EB2121'
			],
			'upgrade' => [
					'name' => __( 'Upgrade to Pro', 'custom-facebook-feed' ),
					'icon' => 'facebook',
					'link' => 'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=balloon&utm_medium=upgrade'
			],
			'platforms' => [
				[
					'name' => __( 'Twitter Feed Pro', 'custom-facebook-feed' ),
					'icon' => 'twitter',
					'link' => 'https://smashballoon.com/custom-twitter-feeds/?utm_campaign=facebook-free&utm_source=balloon&utm_medium=twitter'
				],
				[
					'name' => __( 'Instagram Feed Pro', 'custom-facebook-feed' ),
					'icon' => 'instagram',
					'link' => 'https://smashballoon.com/instagram-feed/?utm_campaign=facebook-free&utm_source=balloon&utm_medium=instagram'
				],
				[
					'name' => __( 'YouTube Feed Pro', 'custom-facebook-feed' ),
					'icon' => 'youtube',
					'link' => 'https://smashballoon.com/youtube-feed/?utm_campaign=facebook-free&utm_source=balloon&utm_medium=youtube'
				],
				[
					'name' => __( 'Social Wall Plugin', 'custom-facebook-feed' ),
					'icon' => 'smash',
					'link' => 'https://smashballoon.com/social-wall/?utm_campaign=facebook-free&utm_source=balloon&utm_medium=social-wall ',
				]
			],
			'socialProfiles' => [
				'facebook' => 'https://www.facebook.com/SmashBalloon/',
				'twitter' => 'https://twitter.com/smashballoon',
			],
			'morePlatforms' => ['instagram','youtube','twitter']
		];

		return $smash_info;
	}

	/**
	 * Text specific to onboarding. Will return an associative array 'active' => false
	 * if onboarding has been dismissed for the user or there aren't any legacy feeds.
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_onboarding_text() {
		// TODO: return if no legacy feeds
		$cff_statuses_option = get_option( 'cff_statuses', array() );

		if ( ! isset( $cff_statuses_option['legacy_onboarding'] ) ) {
			return array( 'active' => false );
		}

		if ( $cff_statuses_option['legacy_onboarding']['active'] === false
		     || CFF_Feed_Builder::onboarding_status() === 'dismissed' ) {
			return array( 'active' => false );
		}

		$type = $cff_statuses_option['legacy_onboarding']['type'];

		$text = array(
			'active' => true,
			'type' => $type,
			'legacyFeeds' => array(
				'heading' => __( 'Legacy Feed Settings', 'custom-facebook-feed' ),
				'description' => sprintf( __( 'These settings will impact %s legacy feeds on your site. You can learn more about what legacy feeds are and how they differ from new feeds %shere%s.', 'custom-facebook-feed' ), '<span class="cff-fb-count-placeholder"></span>', '<a href="https://smashballoon.com/doc/facebook-legacy-feeds/" target="_blank" rel="noopener">', '</a>' ),
			),
			'getStarted' => __( 'You can now create and customize feeds individually. Click "Add New" to get started.', 'custom-facebook-feed' ),
		);

		if ($type === 'single') {
			$text['tooltips'] = array(
				array(
					'step' => 1,
					'heading' => __( 'How you create a feed has changed', 'custom-facebook-feed' ),
					'p' => __( 'You can now create and customize feeds individually without using shortcode options.', 'custom-facebook-feed' ) . ' ' . __( 'Click "Add New" to get started.', 'custom-facebook-feed' ),
					'pointer' => 'top'
				),
				array(
					'step' => 2,
					'heading' => __( 'Your existing feed is here', 'custom-facebook-feed' ),
					'p' => __( 'You can edit your existing feed from here, and all changes will only apply to this feed.', 'custom-facebook-feed' ),
					'pointer' => 'top'
				)
			);
		} else {
			$text['tooltips'] = array(
				array(
					'step' => 1,
					'heading' => __( 'How you create a feed has changed', 'custom-facebook-feed' ),
					'p' => __( 'You can now create and customize feeds individually without using shortcode options.', 'custom-facebook-feed' ) . ' ' . __( 'Click "Add New" to get started.', 'custom-facebook-feed' ),
					'pointer' => 'top'
				),
				array(
					'step' => 2,
					'heading' => __( 'Your existing feeds are under "Legacy" feeds', 'custom-facebook-feed' ),
					'p' => __( 'You can edit the settings for any existing "legacy" feed (i.e. any feed created prior to this update) here.', 'custom-facebook-feed' ) . ' ' . __( 'This works just like the old settings page and affects all legacy feeds on your site.', 'custom-facebook-feed' )
				),
				array(
					'step' => 3,
					'heading' => __( 'Existing feeds work as normal', 'custom-facebook-feed' ),
					'p' => __( 'You don\'t need to update or change any of your existing feeds. They will continue to work as usual.', 'custom-facebook-feed' ) . ' ' . __( 'This update only affects how new feeds are created and customized.', 'custom-facebook-feed' )
				)
			);
		}

		return $text;
	}

	public function get_customizer_onboarding_text() {

		if ( CFF_Feed_Builder::onboarding_status( 'customizer' ) === 'dismissed' ) {
			return array( 'active' => false );
		}

		$text = array(
			'active' => true,
			'type' => 'customizer',
			'tooltips' => array(
				array(
					'step' => 1,
					'heading' => __( 'Embedding a Feed', 'custom-facebook-feed' ),
					'p' => __( 'After you are done customizing the feed, click here to add it to a page or a widget.', 'custom-facebook-feed' ),
					'pointer' => 'top'
				),
				array(
					'step' => 2,
					'heading' => __( 'Customize', 'custom-facebook-feed' ),
					'p' => __( 'Change your feed layout, color scheme, or customize individual feed sections here.', 'custom-facebook-feed' ),
					'pointer' => 'top'
				),
				array(
					'step' => 3,
					'heading' => __( 'Settings', 'custom-facebook-feed' ),
					'p' => __( 'Update your feed source, filter your posts, or change advanced settings here.', 'custom-facebook-feed' ),
					'pointer' => 'top'
				)
			)
		);

		return $text;
	}

	/**
	 * Text related to the feed customizer
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_customize_screens_text() {
		$text =  [
			'common' => [
				'preview' => __( 'Preview', 'custom-facebook-feed' ),
				'help' => __( 'Help', 'custom-facebook-feed' ),
				'embed' => __( 'Embed', 'custom-facebook-feed' ),
				'save' => __( 'Save', 'custom-facebook-feed' ),
				'sections' => __( 'Sections', 'custom-facebook-feed' ),
				'enable' => __( 'Enable', 'custom-facebook-feed' ),
				'background' => __( 'Background', 'custom-facebook-feed' ),
				'text' => __( 'Text', 'custom-facebook-feed' ),
				'inherit' => __( 'Inherit from Theme', 'custom-facebook-feed' ),
				'size' => __( 'Size', 'custom-facebook-feed' ),
				'color' => __( 'Color', 'custom-facebook-feed' ),
				'height' => __( 'Height', 'custom-facebook-feed' ),
				'placeholder' => __( 'Placeholder', 'custom-facebook-feed' ),
				'select' => __( 'Select', 'custom-facebook-feed' ),
				'enterText' => __( 'Enter Text', 'custom-facebook-feed' ),
				'hoverState' => __( 'Hover State', 'custom-facebook-feed' ),
				'sourceCombine'	=>  __( 'Combine sources from multiple platforms using our Social Wall plugin', 'custom-facebook-feed' ),
			],

			'tabs' => [
				'customize' => __( 'Customize', 'custom-facebook-feed' ),
				'settings' => __( 'Settings', 'custom-facebook-feed' ),
			],
			'overview' => [
				'feedLayout' => __( 'Feed Layout', 'custom-facebook-feed' ),
				'colorScheme' => __( 'Color Scheme', 'custom-facebook-feed' ),
				'header' => __( 'Header', 'custom-facebook-feed' ),
				'posts' => __( 'Posts', 'custom-facebook-feed' ),
				'likeBox' => __( 'Like Box', 'custom-facebook-feed' ),
				'loadMore' => __( 'Load More Button', 'custom-facebook-feed' ),
			],
			'feedLayoutScreen' => [
				'layout' => __( 'Layout', 'custom-facebook-feed' ),
				'list' => __( 'List', 'custom-facebook-feed' ),
				'grid' => __( 'Grid', 'custom-facebook-feed' ),
				'masonry' => __( 'Masonry', 'custom-facebook-feed' ),
				'carousel' => __( 'Carousel', 'custom-facebook-feed' ),
				'feedHeight' => __( 'Feed Height', 'custom-facebook-feed' ),
				'number' => __( 'Number of Posts', 'custom-facebook-feed' ),
				'columns' => __( 'Columns', 'custom-facebook-feed' ),
				'desktop' => __( 'Desktop', 'custom-facebook-feed' ),
				'tablet' => __( 'Tablet', 'custom-facebook-feed' ),
				'mobile' => __( 'Mobile', 'custom-facebook-feed' ),
				'bottomArea' => [
					'heading' => __( 'Tweak Post Styles', 'custom-facebook-feed' ),
					'description' => __( 'Change post background, border radius, shadow etc.', 'custom-facebook-feed' ),
				]
			],
			'colorSchemeScreen' => [
				'scheme' => __( 'Scheme', 'custom-facebook-feed' ),
				'light' => __( 'Light', 'custom-facebook-feed' ),
				'dark' => __( 'Dark', 'custom-facebook-feed' ),
				'custom' => __( 'Custom', 'custom-facebook-feed' ),
				'customPalette' => __( 'Custom Palette', 'custom-facebook-feed' ),
				'background2' => __( 'Background 2', 'custom-facebook-feed' ),
				'text2' => __( 'Text 2', 'custom-facebook-feed' ),
				'link' => __( 'Link', 'custom-facebook-feed' ),
				'bottomArea' => [
					'heading' => __( 'Overrides', 'custom-facebook-feed' ),
					'description' => __( 'Colors that have been overridden from individual post element settings will not change. To change them, you will have to reset overrides.', 'custom-facebook-feed' ),
					'ctaButton' => __( 'Reset Overrides.', 'custom-facebook-feed' ),
				]
			],
			'headerScreen' => [
				'headerType' => __( 'Header Type', 'custom-facebook-feed' ),
				'visual' => __( 'Visual', 'custom-facebook-feed' ),
				'coverPhoto' => __( 'Cover Photo', 'custom-facebook-feed' ),
				'nameAndAvatar' => __( 'Name and avatar', 'custom-facebook-feed' ),
				'about' => __( 'About (bio and Likes)', 'custom-facebook-feed' ),
				'displayOutside' => __( 'Display outside scrollable area', 'custom-facebook-feed' ),
				'icon' => __( 'Icon', 'custom-facebook-feed' ),
				'iconImage' => __( 'Icon Image', 'custom-facebook-feed' ),
				'iconColor' => __( 'Icon Color', 'custom-facebook-feed' ),
			],
			// all Lightbox in common
			// all Load More in common
			'likeBoxScreen' => [
				'small' => __( 'Small', 'custom-facebook-feed' ),
				'large' => __( 'Large', 'custom-facebook-feed' ),
				'coverPhoto' => __( 'Cover Photo', 'custom-facebook-feed' ),
				'customWidth' => __( 'Custom Width', 'custom-facebook-feed' ),
				'defaultSetTo' => __( 'By default, it is set to auto', 'custom-facebook-feed' ),
				'width' => __( 'Width', 'custom-facebook-feed' ),
				'customCTA' => __( 'Custom CTA', 'custom-facebook-feed' ),
				'customCTADescription' => __( 'This toggles the custom CTA like "Show now" and "Contact"', 'custom-facebook-feed' ),
				'showFans' => __( 'Show Fans', 'custom-facebook-feed' ),
				'showFansDescription' => __( 'Show visitors which of their friends follow your page', 'custom-facebook-feed' ),
				'displayOutside' => __( 'Display outside scrollable area', 'custom-facebook-feed' ),
				'displayOutsideDescription' => __( 'Make the like box fixed by moving it outside the scrollable area', 'custom-facebook-feed' ),
			],
			'postsScreen' => [
				'thumbnail' => __( 'Thumbnail', 'custom-facebook-feed' ),
				'half' => __( 'Half width', 'custom-facebook-feed' ),
				'full' => __( 'Full width', 'custom-facebook-feed' ),
				'useFull' => __( 'Use full width layout when post width is less than 500px', 'custom-facebook-feed' ),
				'postStyle' => __( 'Post Style', 'custom-facebook-feed' ),
				'editIndividual' => __( 'Edit Individual Elements', 'custom-facebook-feed' ),
				'individual' => [
					'description' => __( 'Hide or show individual elements of a post or edit their options', 'custom-facebook-feed' ),
					'name' => __( 'Name', 'custom-facebook-feed' ),
					'edit' => __( 'Edit', 'custom-facebook-feed' ),
					'postAuthor' => __( 'Post Author', 'custom-facebook-feed' ),
					'postText' => __( 'Post Text', 'custom-facebook-feed' ),
					'date' => __( 'Date', 'custom-facebook-feed' ),
					'photosVideos' => __( 'Photos/Videos', 'custom-facebook-feed' ),
					'likesShares' => __( 'Likes, Shares and Comments', 'custom-facebook-feed' ),
					'eventTitle' => __( 'Event Title', 'custom-facebook-feed' ),
					'eventDetails' => __( 'Event Details', 'custom-facebook-feed' ),
					'postAction' => __( 'Post Action Links', 'custom-facebook-feed' ),
					'sharedPostText' => __( 'Shared Post Text', 'custom-facebook-feed' ),
					'sharedLinkBox' => __( 'Shared Link Box', 'custom-facebook-feed' ),
					'postTextDescription' => __( 'The main text of the Facebook post', 'custom-facebook-feed' ),
					'maxTextLength' => __( 'Maximum Text Length', 'custom-facebook-feed' ),
					'characters' => __( 'Characters', 'custom-facebook-feed' ),
					'linkText' => __( 'Link text to Facebook post', 'custom-facebook-feed' ),
					'postDateDescription' => __( 'The date of the post', 'custom-facebook-feed' ),
					'format' => __( 'Format', 'custom-facebook-feed' ),
					'custom' => __( 'Custom', 'custom-facebook-feed' ),
					'learnMoreFormats' => '<a href="https://smashballoon.com/doc/date-formatting-reference/" target="_blank" rel="noopener">' . __( 'Learn more about custom formats', 'custom-facebook-feed' ) . '</a>',
					'addTextBefore' => __( 'Add text before date', 'custom-facebook-feed' ),
					'addTextBeforeEG' => __( 'E.g. Posted', 'custom-facebook-feed' ),
					'addTextAfter' => __( 'Add text after date', 'custom-facebook-feed' ),
					'addTextAfterEG' => __( 'E.g. - posted date', 'custom-facebook-feed' ),
					'timezone' => __( 'Timezone', 'custom-facebook-feed' ),
					'tzDescription' => __( 'Timezone settings are global across all feeds. To update it use the global settings.', 'custom-facebook-feed' ),
					'tzCTAText' => __( 'Go to Global Settings', 'custom-facebook-feed' ),
					'photosVideosDescription' => __( 'Any photos or videos in your posts', 'custom-facebook-feed' ),
					'useOnlyOne' => __( 'Use only one image per post', 'custom-facebook-feed' ),
					'postActionLinksDescription' => __( 'The "View on Facebook" and "Share" links at the bottom of each post', 'custom-facebook-feed' ),
					'viewOnFBLink' => __( 'View on Facebook link', 'custom-facebook-feed' ),
					'viewOnFBLinkDescription' => __( 'Toggle "View on Facebook" link below each post', 'custom-facebook-feed' ),
					'customizeText' => __( 'Customize Text', 'custom-facebook-feed' ),
					'shareLink' => __( 'Share Link', 'custom-facebook-feed' ),
					'shareLinkDescription' => __( 'Toggle "Share" link below each post', 'custom-facebook-feed' ),
					'likesSharesDescription' => __( 'The comments box displayed at the bottom of each timeline post', 'custom-facebook-feed' ),
					'iconTheme' => __( 'Icon Theme', 'custom-facebook-feed' ),
					'auto' => __( 'Auto', 'custom-facebook-feed' ),
					'light' => __( 'Light', 'custom-facebook-feed' ),
					'dark' => __( 'Dark', 'custom-facebook-feed' ),
					'expandComments' => __( 'Expand comments box by default', 'custom-facebook-feed' ),
					'hideComment' => __( 'Hide comment avatars', 'custom-facebook-feed' ),
					'showLightbox' => __( 'Show comments in lightbox', 'custom-facebook-feed' ),
					'eventTitleDescription' => __( 'The title of an event', 'custom-facebook-feed' ),
					'eventDetailsDescription' => __( 'The information associated with an event', 'custom-facebook-feed' ),
					'textSize' => __( 'Text Size', 'custom-facebook-feed' ),
					'textColor' => __( 'Text Color', 'custom-facebook-feed' ),
					'sharedLinkBoxDescription' => __( "The link info box that's created when a link is shared in a Facebook post", 'custom-facebook-feed' ),
					'boxStyle' => __( 'Box Style', 'custom-facebook-feed' ),
					'removeBackground' => __( 'Remove background/border', 'custom-facebook-feed' ),
					'linkTitle' => __( 'Link Title', 'custom-facebook-feed' ),
					'linkURL' => __( 'Link URL', 'custom-facebook-feed' ),
					'linkDescription' => __( 'Link Description', 'custom-facebook-feed' ),
					'chars' => __( 'chars', 'custom-facebook-feed' ),
					'sharedPostDescription' => __( 'The description text associated with shared photos, videos, or links', 'custom-facebook-feed' ),
				],
				'postType' => __( 'Post Type', 'custom-facebook-feed' ),
				'boxed' => __( 'boxed', 'custom-facebook-feed' ),
				'regular' => __( 'Regular', 'custom-facebook-feed' ),
				'indvidualProperties' => __( 'Indvidual Properties', 'custom-facebook-feed' ),
				'backgroundColor' => __( 'Background Color', 'custom-facebook-feed' ),
				'borderRadius' => __( 'Border Radius', 'custom-facebook-feed' ),
				'boxShadow' => __( 'Box Shadow', 'custom-facebook-feed' ),
			],
		];

		$text['onboarding'] = $this->get_customizer_onboarding_text();

		return $text;
	}


	/**
	 * Get Social Share Links
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	function get_social_share_link(){
		return [
			'facebook'	=> 'https://www.facebook.com/sharer/sharer.php?u=',
			'twitter' 	=> 'https://twitter.com/intent/tweet?text=',
			'linkedin'	=> 'https://www.linkedin.com/shareArticle?mini=true&amp;url=',
			'mail' 	=> 'mailto:?subject=Facebook&amp;body=',
		];
	}

	/**
	 * Creating a Dummy Lightbox Data
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	function get_dummy_lightbox_data(){
		return [
			'visibility' => 'hidden',
			'image' => CFF_BUILDER_URL . 'assets/img/dummy-lightbox.jpeg',
			'post'	=> [
				'id' => '410484879066269_4042924559155598',
				'updated_time'=> '2021-06-07T22:45:17+0000',
				'from'=> [
					'picture'=> [
						'data'=> [
							'height'=> 50,
							'is_silhouette'=> false,
							'url'=> CFF_BUILDER_URL . 'assets/img/dummy-author.png',
							'width'=> 50
						]
					],
					'id'=> '410484879066269',
					'name'=> 'Smash Balloon',
					'link'=> 'https://www.facebook.com/410484879066269'
				],
				'message'=>'This is example text to show how it is displayed inside the lightbox. This is an example <a>Link</a> inside the lightbox text.',
			    'message_tags'=>[],
			    'status_type'=>'added_photos',
			    'created_time'=>'2021-05-31T14:00:30+0000',
			    "shares"	=> [
			    	"count" => 29
			    ],

			    //HERE COMMENTs
			    'comments' => [
			    	'data'	=> [
			    		[
			    			'created_time' => '2021-06-02T01:25:27+0000',
			    			'from'=> [
			                    'name' => 'John Doe',
			                    'id' => '3933732853410659',
			                    'picture' => [
			                        'data' => [
			                            'url' => CFF_BUILDER_URL . 'assets/img/dummy-author.png',
			                        ]
			                    ],
			                ],
			                'id' => "4042924559155598_4048646911916696",
							'message' => 'Example comment one',
				            'like_count' => 0
			    		],
			    		[
			    			'created_time' => '2021-06-02T01:25:27+0000',
			    			'from'=> [
			                    'name' => 'Jane Parker',
			                    'id' => '3933732853410659',
			                    'picture' => [
			                        'data' => [
			                            'url' => CFF_BUILDER_URL . 'assets/img/dummy-author.png',
			                        ]
			                    ],
			                ],
			                'id' => "4042924559155598_4048646911916696",
							'message' => 'Example comment two',
				            'like_count' => 0
			    		]
			    	],
			    	'summary'=> [
			    		'total_count'=> 14,
			    		'can_comment'=> false,
			    		'order'=> "ranked"
			    	]
			    ],
			    'likes'=> [
			    	'data'=> [],
			    	'summary'=> [
			    		'total_count'=> 14,
			    		'can_like'=> false,
			    		'has_liked'=> false
			    	]
			    ],
			    'privacy'=> [
			    	'allow'=> '',
			    	'deny'=> '',
			    	'description'=> 'Public',
			    	'friends'=> '',
			    	'value'=> 'EVERYONE'
			    ]
			]
		];
	}


	/**
	 * Get Translated text Set in the Settings Page
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	function get_translated_text(){
		$translated_text = \CustomFacebookFeed\CFF_Shortcode::add_translations( array() );

		$text =  [
			'seeMoreText' => __( 'See More', 'custom-facebook-feed' ),
			'seeLessText' => __( 'See Less', 'custom-facebook-feed' ),
			'secondText' => __( 'second', 'custom-facebook-feed' ),
			'secondsText' => __( 'seconds', 'custom-facebook-feed' ),
			'minuteText' => __( 'minute', 'custom-facebook-feed' ),
			'minutesText' => __( 'minutes', 'custom-facebook-feed' ),
			'hourText' => __( 'hour', 'custom-facebook-feed' ),
			'hoursText' => __( 'hours', 'custom-facebook-feed' ),
			'dayText' => __( 'day', 'custom-facebook-feed' ),
			'daysText' => __( 'days', 'custom-facebook-feed' ),
			'weekText' => __( 'week', 'custom-facebook-feed' ),
			'weeksText' => __( 'weeks', 'custom-facebook-feed' ),
			'monthText' => __( 'month', 'custom-facebook-feed' ),
			'monthsText' => __( 'months', 'custom-facebook-feed' ),
			'yearText' => __( 'year', 'custom-facebook-feed' ),
			'yearsText' => __( 'years', 'custom-facebook-feed' ),
			'agoText' => __( 'ago', 'custom-facebook-feed' ),
			'commentonFacebookText' => __( 'Comment on Facebook', 'custom-facebook-feed' ),
			'phototext' => __( 'Photos', 'custom-facebook-feed' ),
			'videotext' => __( 'Videos', 'custom-facebook-feed' ),
		];

		foreach ( $text as $key => $value ) {
			$text[ $key ] = ! empty( $translated_text[ strtolower( $key ) ] ) ? $translated_text[ strtolower( $key ) ] : $value;
		}

		return $text;
	}

	/**
	 * Status of the onboarding sequence for specific user
	 *
	 * @return string|boolean
	 *
	 * @since 4.0
	 */
	public static function onboarding_status( $type = 'newuser' ) {
		$onboarding_statuses = get_user_meta( get_current_user_id(), 'cff_onboarding', true );
		$status = false;
		if ( ! empty( $onboarding_statuses ) ) {
			$statuses = maybe_unserialize( $onboarding_statuses );
			$status = isset( $statuses[ $type ] ) ? $statuses[ $type ] : false;
		}

		return $status;
	}

	/**
	 * Update status of onboarding sequence for specific user
	 *
	 * @return string|boolean
	 *
	 * @since 4.0
	 */
	public static function update_onboarding_meta( $value, $type = 'newuser' ) {
		$onboarding_statuses = get_user_meta( get_current_user_id(), 'cff_onboarding', true );
		if ( ! empty( $onboarding_statuses ) ) {
			$statuses = maybe_unserialize( $onboarding_statuses );
			$statuses[ $type ] = $value;
		} else {
			$statuses = array(
				$type => $value
			);
		}

		$statuses = maybe_serialize( $statuses );

		update_user_meta( get_current_user_id(), 'cff_onboarding', $statuses );
	}

	/**
	 * Checks & Returns the list of Active Extensions
	 *
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_active_extensions() {
		$active_extensions = array(
			'multifeed' => false,
			'date_range' => false,
			'featured_post' => false,
			'album' => false,
			'carousel' => false,
			'reviews' => false,
			//Fake
			'lightbox' => false,
			'advancedFilter'  => false,
			'postSettings'  => false,
			'mediaComment'	=> false,
			'loadMore'	=> false,
		);

		return $active_extensions;
	}

	/**
	 * Plugins information for plugin install modal in all feeds page on select source flow
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public static function install_plugins_popup() {
		// get the WordPress's core list of installed plugins
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$installed_plugins = get_plugins();

		$is_instagram_installed = false;
        $instagram_plugin = 'instagram-feed/instagram-feed.php';
        if ( isset( $installed_plugins['instagram-feed-pro/instagram-feed.php'] ) ) {
            $is_instagram_installed = true;
            $instagram_plugin = 'instagram-feed-pro/instagram-feed.php';
        } else if ( isset( $installed_plugins['instagram-feed/instagram-feed.php'] ) ) {
            $is_instagram_installed = true;
        }

        $is_twitter_installed = false;
        $twitter_plugin = 'custom-twitter-feeds/custom-twitter-feed.php';
        if ( isset( $installed_plugins['custom-twitter-feeds-pro/custom-twitter-feed.php'] ) ) {
            $is_twitter_installed = true;
            $twitter_plugin = 'custom-twitter-feeds-pro/custom-twitter-feed.php';
        } else if ( isset( $installed_plugins['custom-twitter-feeds/custom-twitter-feed.php'] ) ) {
            $is_twitter_installed = true;
        }

        $is_youtube_installed = false;
        $youtube_plugin = 'feeds-for-youtube/youtube-feed.php';
        if ( isset( $installed_plugins['youtube-feed-pro/youtube-feed.php'] ) ) {
            $is_youtube_installed = true;
            $youtube_plugin = 'youtube-feed-pro/youtube-feed.php';
        } else if ( isset( $installed_plugins['feeds-for-youtube/youtube-feed.php'] ) ) {
            $is_youtube_installed = true;
        }

		return array(
			'instagram' => array(
				'displayName' => __( 'Instagram', 'custom-facebook-feed' ),
				'name' => __( 'Instagram Feed', 'custom-facebook-feed' ),
				'author' => __( 'By Smash Balloon', 'custom-facebook-feed' ),
				'description' => __('To display an Instagram feed, our Instagram plugin is required. </br> It provides a clean and beautiful way to add your Instagram posts to your website. Grab your visitors attention and keep them engaged with your site longer.', 'custom-facebook-feed'),
				'dashboard_permalink' => admin_url( 'admin.php?page=sb-instagram-feed' ),
				'svgIcon' => '<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 9.91406C13.5 9.91406 9.91406 13.5703 9.91406 18C9.91406 22.5 13.5 26.0859 18 26.0859C22.4297 26.0859 26.0859 22.5 26.0859 18C26.0859 13.5703 22.4297 9.91406 18 9.91406ZM18 23.2734C15.1172 23.2734 12.7266 20.9531 12.7266 18C12.7266 15.1172 15.0469 12.7969 18 12.7969C20.8828 12.7969 23.2031 15.1172 23.2031 18C23.2031 20.9531 20.8828 23.2734 18 23.2734ZM28.2656 9.63281C28.2656 8.57812 27.4219 7.73438 26.3672 7.73438C25.3125 7.73438 24.4688 8.57812 24.4688 9.63281C24.4688 10.6875 25.3125 11.5312 26.3672 11.5312C27.4219 11.5312 28.2656 10.6875 28.2656 9.63281ZM33.6094 11.5312C33.4688 9 32.9062 6.75 31.0781 4.92188C29.25 3.09375 27 2.53125 24.4688 2.39062C21.8672 2.25 14.0625 2.25 11.4609 2.39062C8.92969 2.53125 6.75 3.09375 4.85156 4.92188C3.02344 6.75 2.46094 9 2.32031 11.5312C2.17969 14.1328 2.17969 21.9375 2.32031 24.5391C2.46094 27.0703 3.02344 29.25 4.85156 31.1484C6.75 32.9766 8.92969 33.5391 11.4609 33.6797C14.0625 33.8203 21.8672 33.8203 24.4688 33.6797C27 33.5391 29.25 32.9766 31.0781 31.1484C32.9062 29.25 33.4688 27.0703 33.6094 24.5391C33.75 21.9375 33.75 14.1328 33.6094 11.5312ZM30.2344 27.2812C29.7422 28.6875 28.6172 29.7422 27.2812 30.3047C25.1719 31.1484 20.25 30.9375 18 30.9375C15.6797 30.9375 10.7578 31.1484 8.71875 30.3047C7.3125 29.7422 6.25781 28.6875 5.69531 27.2812C4.85156 25.2422 5.0625 20.3203 5.0625 18C5.0625 15.75 4.85156 10.8281 5.69531 8.71875C6.25781 7.38281 7.3125 6.32812 8.71875 5.76562C10.7578 4.92188 15.6797 5.13281 18 5.13281C20.25 5.13281 25.1719 4.92188 27.2812 5.76562C28.6172 6.25781 29.6719 7.38281 30.2344 8.71875C31.0781 10.8281 30.8672 15.75 30.8672 18C30.8672 20.3203 31.0781 25.2422 30.2344 27.2812Z" fill="url(#paint0_linear)"/><defs><linearGradient id="paint0_linear" x1="13.4367" y1="62.5289" x2="79.7836" y2="-5.19609" gradientUnits="userSpaceOnUse"><stop stop-color="white"/><stop offset="0.147864" stop-color="#F6640E"/><stop offset="0.443974" stop-color="#BA03A7"/><stop offset="0.733337" stop-color="#6A01B9"/><stop offset="1" stop-color="#6B01B9"/></linearGradient></defs></svg>',
				'installed' => $is_instagram_installed,
				'activated' => is_plugin_active( $instagram_plugin ),
				'plugin' => $instagram_plugin,
				'download_plugin' => 'https://downloads.wordpress.org/plugin/instagram-feed.zip',
			),
			'twitter' => array(
				'displayName' => __( 'Twitter', 'custom-facebook-feed' ),
				'name' => __( 'Twitter Feed', 'custom-facebook-feed' ),
				'author' => __( 'By Smash Balloon', 'custom-facebook-feed' ),
				'description' => __('Custom Twitter Feeds is a highly customizable way to display tweets from your Twitter account. Promote your latest content and update your site content automatically.', 'custom-facebook-feed'),
                'dashboard_permalink' => admin_url( 'admin.php?page=custom-twitter-feeds' ),
				'svgIcon' => '<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M33.6905 9C32.5355 9.525 31.2905 9.87 30.0005 10.035C31.3205 9.24 32.3405 7.98 32.8205 6.465C31.5755 7.215 30.1955 7.74 28.7405 8.04C27.5555 6.75 25.8905 6 24.0005 6C20.4755 6 17.5955 8.88 17.5955 12.435C17.5955 12.945 17.6555 13.44 17.7605 13.905C12.4205 13.635 7.66555 11.07 4.50055 7.185C3.94555 8.13 3.63055 9.24 3.63055 10.41C3.63055 12.645 4.75555 14.625 6.49555 15.75C5.43055 15.75 4.44055 15.45 3.57055 15V15.045C3.57055 18.165 5.79055 20.775 8.73055 21.36C7.78664 21.6183 6.79569 21.6543 5.83555 21.465C6.24296 22.7437 7.04085 23.8626 8.11707 24.6644C9.19329 25.4662 10.4937 25.9105 11.8355 25.935C9.56099 27.7357 6.74154 28.709 3.84055 28.695C3.33055 28.695 2.82055 28.665 2.31055 28.605C5.16055 30.435 8.55055 31.5 12.1805 31.5C24.0005 31.5 30.4955 21.69 30.4955 13.185C30.4955 12.9 30.4955 12.63 30.4805 12.345C31.7405 11.445 32.8205 10.305 33.6905 9Z" fill="#1B90EF"/></svg>',
				'installed' => $is_twitter_installed,
				'activated' => is_plugin_active( $twitter_plugin ),
				'plugin' => $twitter_plugin,
				'download_plugin' => 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip',
			),
			'youtube' => array(
				'displayName' => __( 'YouTube', 'custom-facebook-feed' ),
				'name' => __( 'Feeds for YouTube', 'custom-facebook-feed' ),
				'author' => __( 'By Smash Balloon', 'custom-facebook-feed' ),
				'description' => __( 'To display a YouTube feed, our YouTube plugin is required. It provides a simple yet powerful way to display videos from YouTube on your website, Increasing engagement with your channel while keeping visitors on your website.', 'custom-facebook-feed' ),
				'dashboard_permalink' => admin_url( 'admin.php?page=youtube-feed' ),
				'svgIcon' => '<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 22.5L22.785 18L15 13.5V22.5ZM32.34 10.755C32.535 11.46 32.67 12.405 32.76 13.605C32.865 14.805 32.91 15.84 32.91 16.74L33 18C33 21.285 32.76 23.7 32.34 25.245C31.965 26.595 31.095 27.465 29.745 27.84C29.04 28.035 27.75 28.17 25.77 28.26C23.82 28.365 22.035 28.41 20.385 28.41L18 28.5C11.715 28.5 7.8 28.26 6.255 27.84C4.905 27.465 4.035 26.595 3.66 25.245C3.465 24.54 3.33 23.595 3.24 22.395C3.135 21.195 3.09 20.16 3.09 19.26L3 18C3 14.715 3.24 12.3 3.66 10.755C4.035 9.405 4.905 8.535 6.255 8.16C6.96 7.965 8.25 7.83 10.23 7.74C12.18 7.635 13.965 7.59 15.615 7.59L18 7.5C24.285 7.5 28.2 7.74 29.745 8.16C31.095 8.535 31.965 9.405 32.34 10.755Z" fill="#EB2121"/></svg>',
				'installed' => $is_youtube_installed,
				'activated' => is_plugin_active( $youtube_plugin ),
				'plugin' => $youtube_plugin,
				'download_plugin' => 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip',
			),
		);
	}

	/**
	 * For Other Platforms listed on the footer widget
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function builder_svg_icons() {
		$builder_svg_icons = [
			'youtube' 		=> '<svg viewBox="0 0 16 11" fill="none"><path d="M15.1367 2.16797C14.9727 1.51172 14.4531 0.992188 13.8242 0.828125C12.6484 0.5 8 0.5 8 0.5C8 0.5 3.32422 0.5 2.14844 0.828125C1.51953 0.992188 1 1.51172 0.835938 2.16797C0.507812 3.31641 0.507812 5.77734 0.507812 5.77734C0.507812 5.77734 0.507812 8.21094 0.835938 9.38672C1 10.043 1.51953 10.5352 2.14844 10.6992C3.32422 11 8 11 8 11C8 11 12.6484 11 13.8242 10.6992C14.4531 10.5352 14.9727 10.043 15.1367 9.38672C15.4648 8.21094 15.4648 5.77734 15.4648 5.77734C15.4648 5.77734 15.4648 3.31641 15.1367 2.16797ZM6.46875 7.99219V3.5625L10.3516 5.77734L6.46875 7.99219Z"/></svg>',
			'twitter' 		=> '<svg viewBox="0 0 14 12" fill="none"><path d="M12.5508 2.90625C13.0977 2.49609 13.5898 2.00391 13.9727 1.42969C13.4805 1.64844 12.9062 1.8125 12.332 1.86719C12.9336 1.51172 13.3711 0.964844 13.5898 0.28125C13.043 0.609375 12.4141 0.855469 11.7852 0.992188C11.2383 0.417969 10.5 0.0898438 9.67969 0.0898438C8.09375 0.0898438 6.80859 1.375 6.80859 2.96094C6.80859 3.17969 6.83594 3.39844 6.89062 3.61719C4.51172 3.48047 2.37891 2.33203 0.957031 0.609375C0.710938 1.01953 0.574219 1.51172 0.574219 2.05859C0.574219 3.04297 1.06641 3.91797 1.85938 4.4375C1.39453 4.41016 0.929688 4.30078 0.546875 4.08203V4.10938C0.546875 5.50391 1.53125 6.65234 2.84375 6.92578C2.625 6.98047 2.35156 7.03516 2.10547 7.03516C1.91406 7.03516 1.75 7.00781 1.55859 6.98047C1.91406 8.12891 2.98047 8.94922 4.23828 8.97656C3.25391 9.74219 2.02344 10.207 0.683594 10.207C0.4375 10.207 0.21875 10.1797 0 10.1523C1.25781 10.9727 2.76172 11.4375 4.40234 11.4375C9.67969 11.4375 12.5508 7.08984 12.5508 3.28906C12.5508 3.15234 12.5508 3.04297 12.5508 2.90625Z"/></svg>',
			'instagram' 	=> '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 4.50781C6.5 4.50781 4.50781 6.53906 4.50781 9C4.50781 11.5 6.5 13.4922 9 13.4922C11.4609 13.4922 13.4922 11.5 13.4922 9C13.4922 6.53906 11.4609 4.50781 9 4.50781ZM9 11.9297C7.39844 11.9297 6.07031 10.6406 6.07031 9C6.07031 7.39844 7.35938 6.10938 9 6.10938C10.6016 6.10938 11.8906 7.39844 11.8906 9C11.8906 10.6406 10.6016 11.9297 9 11.9297ZM14.7031 4.35156C14.7031 3.76562 14.2344 3.29688 13.6484 3.29688C13.0625 3.29688 12.5938 3.76562 12.5938 4.35156C12.5938 4.9375 13.0625 5.40625 13.6484 5.40625C14.2344 5.40625 14.7031 4.9375 14.7031 4.35156ZM17.6719 5.40625C17.5938 4 17.2812 2.75 16.2656 1.73438C15.25 0.71875 14 0.40625 12.5938 0.328125C11.1484 0.25 6.8125 0.25 5.36719 0.328125C3.96094 0.40625 2.75 0.71875 1.69531 1.73438C0.679688 2.75 0.367188 4 0.289062 5.40625C0.210938 6.85156 0.210938 11.1875 0.289062 12.6328C0.367188 14.0391 0.679688 15.25 1.69531 16.3047C2.75 17.3203 3.96094 17.6328 5.36719 17.7109C6.8125 17.7891 11.1484 17.7891 12.5938 17.7109C14 17.6328 15.25 17.3203 16.2656 16.3047C17.2812 15.25 17.5938 14.0391 17.6719 12.6328C17.75 11.1875 17.75 6.85156 17.6719 5.40625ZM15.7969 14.1562C15.5234 14.9375 14.8984 15.5234 14.1562 15.8359C12.9844 16.3047 10.25 16.1875 9 16.1875C7.71094 16.1875 4.97656 16.3047 3.84375 15.8359C3.0625 15.5234 2.47656 14.9375 2.16406 14.1562C1.69531 13.0234 1.8125 10.2891 1.8125 9C1.8125 7.75 1.69531 5.01562 2.16406 3.84375C2.47656 3.10156 3.0625 2.51562 3.84375 2.20312C4.97656 1.73438 7.71094 1.85156 9 1.85156C10.25 1.85156 12.9844 1.73438 14.1562 2.20312C14.8984 2.47656 15.4844 3.10156 15.7969 3.84375C16.2656 5.01562 16.1484 7.75 16.1484 9C16.1484 10.2891 16.2656 13.0234 15.7969 14.1562Z" fill="url(#paint0_linear)"/><defs><linearGradient id="paint0_linear" x1="6.46484" y1="33.7383" x2="43.3242" y2="-3.88672" gradientUnits="userSpaceOnUse"><stop stop-color="white"/><stop offset="0.147864" stop-color="#F6640E"/><stop offset="0.443974" stop-color="#BA03A7"/><stop offset="0.733337" stop-color="#6A01B9"/><stop offset="1" stop-color="#6B01B9"/></linearGradient></defs></svg>',
			'facebook' 		=> '<svg viewBox="0 0 12 13" fill="none"><path d="M11.8125 6.5C11.8125 3.28906 9.21094 0.6875 6 0.6875C2.78906 0.6875 0.1875 3.28906 0.1875 6.5C0.1875 9.40625 2.29688 11.8203 5.08594 12.2422V8.1875H3.60938V6.5H5.08594V5.23438C5.08594 3.78125 5.95312 2.96094 7.26562 2.96094C7.92188 2.96094 8.57812 3.07812 8.57812 3.07812V4.50781H7.85156C7.125 4.50781 6.89062 4.95312 6.89062 5.42188V6.5H8.50781L8.25 8.1875H6.89062V12.2422C9.67969 11.8203 11.8125 9.40625 11.8125 6.5Z"/></svg>',
			'smash' 		=> '<svg viewBox="0 0 13 17" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.83932 4.33552C4.69523 3.81557 3.50355 3.03908 2.39114 2.67764C2.78638 3.63091 3.11997 4.64653 3.46516 5.65042C2.77664 6.17368 1.9724 6.57989 1.20404 7.02245C1.85789 7.67608 2.78389 8.05444 3.52169 8.62317C2.93071 9.36069 1.76961 10.1272 1.48669 10.7956C2.71012 10.6406 4.13575 10.3892 5.27404 10.3382C5.52417 11.6097 5.65317 13.0038 6.0089 14.1685C6.53865 12.6272 7.01763 11.0344 7.6482 9.59503C8.62132 10.0021 9.80728 10.4984 10.7572 10.7384C10.0907 9.7927 9.48245 8.78807 8.89181 7.76564C9.76584 7.12506 10.6499 6.49463 11.4921 5.82189C10.3017 5.71097 9.06826 5.64347 7.81778 5.59325C7.61906 4.28876 7.67127 2.73053 7.36556 1.53428C6.903 2.51475 6.35713 3.41093 5.83932 4.33552ZM6.40459 15.1404C6.24738 15.8209 6.76536 16.1 6.68723 16.5124C6.17579 16.3342 5.79106 16.2426 5.10446 16.3409C5.12594 15.81 5.59809 15.7349 5.50015 15.0832C-1.81657 14.2419 -1.83127 0.870844 5.44362 0.0479086C14.5398 -0.981072 14.8252 14.9039 6.40459 15.1404Z"/></svg>',
			'tag' 			=> '<svg viewBox="0 0 18 18"><path d="M16.841 8.65033L9.34102 1.15033C9.02853 0.840392 8.60614 0.666642 8.16602 0.666993H2.33268C1.89066 0.666993 1.46673 0.842587 1.15417 1.15515C0.841611 1.46771 0.666016 1.89163 0.666016 2.33366V8.16699C0.665842 8.38692 0.709196 8.60471 0.79358 8.8078C0.877964 9.01089 1.00171 9.19528 1.15768 9.35033L8.65768 16.8503C8.97017 17.1603 9.39256 17.334 9.83268 17.3337C10.274 17.3318 10.6966 17.155 11.0077 16.842L16.841 11.0087C17.154 10.6975 17.3308 10.275 17.3327 9.83366C17.3329 9.61373 17.2895 9.39595 17.2051 9.19285C17.1207 8.98976 16.997 8.80538 16.841 8.65033ZM9.83268 15.667L2.33268 8.16699V2.33366H8.16602L15.666 9.83366L9.83268 15.667ZM4.41602 3.16699C4.66324 3.16699 4.90492 3.2403 5.11048 3.37766C5.31604 3.51501 5.47626 3.71023 5.57087 3.93864C5.66548 4.16705 5.69023 4.41838 5.642 4.66086C5.59377 4.90333 5.47472 5.12606 5.2999 5.30088C5.12508 5.47569 4.90236 5.59474 4.65988 5.64297C4.4174 5.69121 4.16607 5.66645 3.93766 5.57184C3.70925 5.47723 3.51403 5.31702 3.37668 5.11146C3.23933 4.90589 3.16602 4.66422 3.16602 4.41699C3.16602 4.08547 3.29771 3.76753 3.53213 3.53311C3.76655 3.29869 4.0845 3.16699 4.41602 3.16699Z"/></svg>',
			'copy' 			=> '<svg viewBox="0 0 12 13" fill="none"><path d="M10.25 0.25H4.625C3.9375 0.25 3.375 0.8125 3.375 1.5V9C3.375 9.6875 3.9375 10.25 4.625 10.25H10.25C10.9375 10.25 11.5 9.6875 11.5 9V1.5C11.5 0.8125 10.9375 0.25 10.25 0.25ZM10.25 9H4.625V1.5H10.25V9ZM0.875 8.375V7.125H2.125V8.375H0.875ZM0.875 4.9375H2.125V6.1875H0.875V4.9375ZM5.25 11.5H6.5V12.75H5.25V11.5ZM0.875 10.5625V9.3125H2.125V10.5625H0.875ZM2.125 12.75C1.4375 12.75 0.875 12.1875 0.875 11.5H2.125V12.75ZM4.3125 12.75H3.0625V11.5H4.3125V12.75ZM7.4375 12.75V11.5H8.6875C8.6875 12.1875 8.125 12.75 7.4375 12.75ZM2.125 2.75V4H0.875C0.875 3.3125 1.4375 2.75 2.125 2.75Z"/></svg>',
			'duplicate' 	=> '<svg viewBox="0 0 10 12" fill="none"><path d="M6.99997 0.5H0.999969C0.449969 0.5 -3.05176e-05 0.95 -3.05176e-05 1.5V8.5H0.999969V1.5H6.99997V0.5ZM8.49997 2.5H2.99997C2.44997 2.5 1.99997 2.95 1.99997 3.5V10.5C1.99997 11.05 2.44997 11.5 2.99997 11.5H8.49997C9.04997 11.5 9.49997 11.05 9.49997 10.5V3.5C9.49997 2.95 9.04997 2.5 8.49997 2.5ZM8.49997 10.5H2.99997V3.5H8.49997V10.5Z"/></svg>',
			'edit' 			=> '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.25 9.06241V11.2499H2.4375L8.88917 4.79824L6.70167 2.61074L0.25 9.06241ZM10.9892 2.69824L8.80167 0.510742L7.32583 1.99241L9.51333 4.17991L10.9892 2.69824Z" fill="currentColor"/></svg>',
			'delete' 		=> '<svg viewBox="0 0 10 12" fill="none"><path d="M1.00001 10.6667C1.00001 11.4 1.60001 12 2.33334 12H7.66668C8.40001 12 9.00001 11.4 9.00001 10.6667V2.66667H1.00001V10.6667ZM2.33334 4H7.66668V10.6667H2.33334V4ZM7.33334 0.666667L6.66668 0H3.33334L2.66668 0.666667H0.333344V2H9.66668V0.666667H7.33334Z"/></svg>',
			'checkmark'		=> '<svg width="11" height="9"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.15641 5.65271L9.72487 0.0842487L10.9623 1.32169L4.15641 8.12759L0.444097 4.41528L1.68153 3.17784L4.15641 5.65271Z"/></svg>',
			'checkmarklarge'=> '<svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.08058 8.36133L14.0355 0.406383L15.8033 2.17415L6.08058 11.8969L0.777281 6.59357L2.54505 4.8258L6.08058 8.36133Z" fill="currentColor"></path></svg>',
			'information' 	=> '<svg viewBox="0 0 14 14"><path d="M6.33203 4.99992H7.66536V3.66659H6.33203V4.99992ZM6.9987 12.3333C4.0587 12.3333 1.66536 9.93992 1.66536 6.99992C1.66536 4.05992 4.0587 1.66659 6.9987 1.66659C9.9387 1.66659 12.332 4.05992 12.332 6.99992C12.332 9.93992 9.9387 12.3333 6.9987 12.3333ZM6.9987 0.333252C6.12322 0.333252 5.25631 0.50569 4.44747 0.840722C3.63864 1.17575 2.90371 1.66682 2.28465 2.28587C1.03441 3.53612 0.332031 5.23181 0.332031 6.99992C0.332031 8.76803 1.03441 10.4637 2.28465 11.714C2.90371 12.333 3.63864 12.8241 4.44747 13.1591C5.25631 13.4941 6.12322 13.6666 6.9987 13.6666C8.76681 13.6666 10.4625 12.9642 11.7127 11.714C12.963 10.4637 13.6654 8.76803 13.6654 6.99992C13.6654 6.12444 13.4929 5.25753 13.1579 4.4487C12.8229 3.63986 12.3318 2.90493 11.7127 2.28587C11.0937 1.66682 10.3588 1.17575 9.54992 0.840722C8.74108 0.50569 7.87418 0.333252 6.9987 0.333252ZM6.33203 10.3333H7.66536V6.33325H6.33203V10.3333Z"/></svg>',
			'cog' 			=> '<svg viewBox="0 0 18 18"><path d="M9.00035 11.9167C8.22681 11.9167 7.48494 11.6095 6.93796 11.0625C6.39098 10.5155 6.08369 9.77363 6.08369 9.00008C6.08369 8.22653 6.39098 7.48467 6.93796 6.93769C7.48494 6.39071 8.22681 6.08342 9.00035 6.08342C9.7739 6.08342 10.5158 6.39071 11.0627 6.93769C11.6097 7.48467 11.917 8.22653 11.917 9.00008C11.917 9.77363 11.6097 10.5155 11.0627 11.0625C10.5158 11.6095 9.7739 11.9167 9.00035 11.9167ZM15.192 9.80842C15.2254 9.54175 15.2504 9.27508 15.2504 9.00008C15.2504 8.72508 15.2254 8.45008 15.192 8.16675L16.9504 6.80842C17.1087 6.68342 17.1504 6.45842 17.0504 6.27508L15.3837 3.39175C15.2837 3.20842 15.0587 3.13342 14.8754 3.20842L12.8004 4.04175C12.367 3.71675 11.917 3.43342 11.392 3.22508L11.0837 1.01675C11.0668 0.918597 11.0156 0.829605 10.9394 0.765542C10.8631 0.70148 10.7666 0.666482 10.667 0.66675H7.33369C7.12535 0.66675 6.95035 0.81675 6.91702 1.01675L6.60869 3.22508C6.08369 3.43342 5.63369 3.71675 5.20035 4.04175L3.12535 3.20842C2.94202 3.13342 2.71702 3.20842 2.61702 3.39175L0.950354 6.27508C0.842021 6.45842 0.892021 6.68342 1.05035 6.80842L2.80869 8.16675C2.77535 8.45008 2.75035 8.72508 2.75035 9.00008C2.75035 9.27508 2.77535 9.54175 2.80869 9.80842L1.05035 11.1917C0.892021 11.3167 0.842021 11.5417 0.950354 11.7251L2.61702 14.6084C2.71702 14.7917 2.94202 14.8584 3.12535 14.7917L5.20035 13.9501C5.63369 14.2834 6.08369 14.5667 6.60869 14.7751L6.91702 16.9834C6.95035 17.1834 7.12535 17.3334 7.33369 17.3334H10.667C10.8754 17.3334 11.0504 17.1834 11.0837 16.9834L11.392 14.7751C11.917 14.5584 12.367 14.2834 12.8004 13.9501L14.8754 14.7917C15.0587 14.8584 15.2837 14.7917 15.3837 14.6084L17.0504 11.7251C17.1504 11.5417 17.1087 11.3167 16.9504 11.1917L15.192 9.80842Z"/></svg>',
			'angleUp'		=> '<svg width="8" height="6" viewBox="0 0 8 6" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.94 5.27325L4 2.21992L7.06 5.27325L8 4.33325L4 0.333252L0 4.33325L0.94 5.27325Z" fill="#434960"/></svg>',
			'user_check' 	=> '<svg viewBox="0 0 11 9"><path d="M9.55 4.25L10.25 4.955L6.985 8.25L5.25 6.5L5.95 5.795L6.985 6.835L9.55 4.25ZM4 6.5L5.5 8H0.5V7C0.5 5.895 2.29 5 4.5 5L5.445 5.055L4 6.5ZM4.5 0C5.03043 0 5.53914 0.210714 5.91421 0.585786C6.28929 0.960859 6.5 1.46957 6.5 2C6.5 2.53043 6.28929 3.03914 5.91421 3.41421C5.53914 3.78929 5.03043 4 4.5 4C3.96957 4 3.46086 3.78929 3.08579 3.41421C2.71071 3.03914 2.5 2.53043 2.5 2C2.5 1.46957 2.71071 0.960859 3.08579 0.585786C3.46086 0.210714 3.96957 0 4.5 0Z"/></svg>',
			'users' 		=> '<svg viewBox="0 0 12 8"><path d="M6 0.75C6.46413 0.75 6.90925 0.934375 7.23744 1.26256C7.56563 1.59075 7.75 2.03587 7.75 2.5C7.75 2.96413 7.56563 3.40925 7.23744 3.73744C6.90925 4.06563 6.46413 4.25 6 4.25C5.53587 4.25 5.09075 4.06563 4.76256 3.73744C4.43437 3.40925 4.25 2.96413 4.25 2.5C4.25 2.03587 4.43437 1.59075 4.76256 1.26256C5.09075 0.934375 5.53587 0.75 6 0.75ZM2.5 2C2.78 2 3.04 2.075 3.265 2.21C3.19 2.925 3.4 3.635 3.83 4.19C3.58 4.67 3.08 5 2.5 5C2.10218 5 1.72064 4.84196 1.43934 4.56066C1.15804 4.27936 1 3.89782 1 3.5C1 3.10218 1.15804 2.72064 1.43934 2.43934C1.72064 2.15804 2.10218 2 2.5 2ZM9.5 2C9.89782 2 10.2794 2.15804 10.5607 2.43934C10.842 2.72064 11 3.10218 11 3.5C11 3.89782 10.842 4.27936 10.5607 4.56066C10.2794 4.84196 9.89782 5 9.5 5C8.92 5 8.42 4.67 8.17 4.19C8.60594 3.62721 8.80828 2.9181 8.735 2.21C8.96 2.075 9.22 2 9.5 2ZM2.75 7.125C2.75 6.09 4.205 5.25 6 5.25C7.795 5.25 9.25 6.09 9.25 7.125V8H2.75V7.125ZM0 8V7.25C0 6.555 0.945 5.97 2.225 5.8C1.93 6.14 1.75 6.61 1.75 7.125V8H0ZM12 8H10.25V7.125C10.25 6.61 10.07 6.14 9.775 5.8C11.055 5.97 12 6.555 12 7.25V8Z"/></svg>',
			'info'			=> '<svg viewBox="0 0 14 14"><path d="M6.33203 5.00004H7.66536V3.66671H6.33203V5.00004ZM6.9987 12.3334C4.0587 12.3334 1.66536 9.94004 1.66536 7.00004C1.66536 4.06004 4.0587 1.66671 6.9987 1.66671C9.9387 1.66671 12.332 4.06004 12.332 7.00004C12.332 9.94004 9.9387 12.3334 6.9987 12.3334ZM6.9987 0.333374C6.12322 0.333374 5.25631 0.505812 4.44747 0.840844C3.63864 1.17588 2.90371 1.66694 2.28465 2.286C1.03441 3.53624 0.332031 5.23193 0.332031 7.00004C0.332031 8.76815 1.03441 10.4638 2.28465 11.7141C2.90371 12.3331 3.63864 12.8242 4.44747 13.1592C5.25631 13.4943 6.12322 13.6667 6.9987 13.6667C8.76681 13.6667 10.4625 12.9643 11.7127 11.7141C12.963 10.4638 13.6654 8.76815 13.6654 7.00004C13.6654 6.12456 13.4929 5.25766 13.1579 4.44882C12.8229 3.63998 12.3318 2.90505 11.7127 2.286C11.0937 1.66694 10.3588 1.17588 9.54992 0.840844C8.74108 0.505812 7.87418 0.333374 6.9987 0.333374ZM6.33203 10.3334H7.66536V6.33337H6.33203V10.3334Z"/></svg>',
			'list'			=> '<svg viewBox="0 0 14 12"><path d="M0.332031 7.33341H4.33203V11.3334H0.332031V7.33341ZM9.66537 3.33341H5.66536V4.66675H9.66537V3.33341ZM0.332031 4.66675H4.33203V0.666748H0.332031V4.66675ZM5.66536 0.666748V2.00008H13.6654V0.666748H5.66536ZM5.66536 11.3334H9.66537V10.0001H5.66536V11.3334ZM5.66536 8.66675H13.6654V7.33341H5.66536"/></svg>',
			'grid'			=> '<svg viewBox="0 0 12 12"><path d="M0 5.33333H5.33333V0H0V5.33333ZM0 12H5.33333V6.66667H0V12ZM6.66667 12H12V6.66667H6.66667V12ZM6.66667 0V5.33333H12V0"/></svg>',
			'masonry'		=> '<svg viewBox="0 0 16 16"><rect x="3" y="3" width="4.5" height="5" /><rect x="3" y="9" width="4.5" height="5" /><path d="M8.5 2H13V7H8.5V2Z" /><rect x="8.5" y="8" width="4.5" height="5" /></svg>',
			'carousel'		=> '<svg viewBox="0 0 14 11"><path d="M0.332031 2.00008H2.9987V9.33342H0.332031V2.00008ZM3.66536 10.6667H10.332V0.666748H3.66536V10.6667ZM4.9987 2.00008H8.9987V9.33342H4.9987V2.00008ZM10.9987 2.00008H13.6654V9.33342H10.9987V2.00008Z"/></svg>',
			'desktop'		=> '<svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.0013 9.66659H2.0013V1.66659H14.0013V9.66659ZM14.0013 0.333252H2.0013C1.2613 0.333252 0.667969 0.926585 0.667969 1.66659V9.66659C0.667969 10.0202 0.808445 10.3593 1.05849 10.6094C1.30854 10.8594 1.64768 10.9999 2.0013 10.9999H6.66797V12.3333H5.33464V13.6666H10.668V12.3333H9.33463V10.9999H14.0013C14.3549 10.9999 14.6941 10.8594 14.9441 10.6094C15.1942 10.3593 15.3346 10.0202 15.3346 9.66659V1.66659C15.3346 1.31296 15.1942 0.973825 14.9441 0.723776C14.6941 0.473728 14.3549 0.333252 14.0013 0.333252Z" fill="#141B38"/></svg>',
			'tablet'		=> '<svg width="12" height="16" viewBox="0 0 12 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.0013 2.66659V13.3333H2.0013L2.0013 2.66659H10.0013ZM0.667969 1.99992L0.667969 13.9999C0.667969 14.7399 1.2613 15.3333 2.0013 15.3333H10.0013C10.3549 15.3333 10.6941 15.1928 10.9441 14.9427C11.1942 14.6927 11.3346 14.3535 11.3346 13.9999V1.99992C11.3346 1.6463 11.1942 1.30716 10.9441 1.05711C10.6941 0.807062 10.3549 0.666586 10.0013 0.666586H2.0013C1.64768 0.666586 1.30854 0.807062 1.05849 1.05711C0.808444 1.30716 0.667969 1.6463 0.667969 1.99992Z" fill="#141B38"/></svg>',
			'mobile'		=> '<svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.33203 12.6667H1.66536V3.33341H8.33203V12.6667ZM8.33203 0.666748H1.66536C0.925365 0.666748 0.332031 1.26008 0.332031 2.00008V14.0001C0.332031 14.3537 0.472507 14.6928 0.722555 14.9429C0.972604 15.1929 1.31174 15.3334 1.66536 15.3334H8.33203C8.68565 15.3334 9.02479 15.1929 9.27484 14.9429C9.52489 14.6928 9.66537 14.3537 9.66537 14.0001V2.00008C9.66537 1.64646 9.52489 1.30732 9.27484 1.05727C9.02479 0.807224 8.68565 0.666748 8.33203 0.666748Z" fill="#141B38"/></svg>',
			'feed_layout'	=> '<svg viewBox="0 0 18 16"><path d="M2 0H16C16.5304 0 17.0391 0.210714 17.4142 0.585786C17.7893 0.960859 18 1.46957 18 2V14C18 14.5304 17.7893 15.0391 17.4142 15.4142C17.0391 15.7893 16.5304 16 16 16H2C1.46957 16 0.960859 15.7893 0.585786 15.4142C0.210714 15.0391 0 14.5304 0 14V2C0 1.46957 0.210714 0.960859 0.585786 0.585786C0.960859 0.210714 1.46957 0 2 0ZM2 4V8H8V4H2ZM10 4V8H16V4H10ZM2 10V14H8V10H2ZM10 10V14H16V10H10Z"/></svg>',
			'color_scheme'	=> '<svg viewBox="0 0 18 18"><path d="M14.5 9C14.1022 9 13.7206 8.84196 13.4393 8.56066C13.158 8.27936 13 7.89782 13 7.5C13 7.10218 13.158 6.72064 13.4393 6.43934C13.7206 6.15804 14.1022 6 14.5 6C14.8978 6 15.2794 6.15804 15.5607 6.43934C15.842 6.72064 16 7.10218 16 7.5C16 7.89782 15.842 8.27936 15.5607 8.56066C15.2794 8.84196 14.8978 9 14.5 9ZM11.5 5C11.1022 5 10.7206 4.84196 10.4393 4.56066C10.158 4.27936 10 3.89782 10 3.5C10 3.10218 10.158 2.72064 10.4393 2.43934C10.7206 2.15804 11.1022 2 11.5 2C11.8978 2 12.2794 2.15804 12.5607 2.43934C12.842 2.72064 13 3.10218 13 3.5C13 3.89782 12.842 4.27936 12.5607 4.56066C12.2794 4.84196 11.8978 5 11.5 5ZM6.5 5C6.10218 5 5.72064 4.84196 5.43934 4.56066C5.15804 4.27936 5 3.89782 5 3.5C5 3.10218 5.15804 2.72064 5.43934 2.43934C5.72064 2.15804 6.10218 2 6.5 2C6.89782 2 7.27936 2.15804 7.56066 2.43934C7.84196 2.72064 8 3.10218 8 3.5C8 3.89782 7.84196 4.27936 7.56066 4.56066C7.27936 4.84196 6.89782 5 6.5 5ZM3.5 9C3.10218 9 2.72064 8.84196 2.43934 8.56066C2.15804 8.27936 2 7.89782 2 7.5C2 7.10218 2.15804 6.72064 2.43934 6.43934C2.72064 6.15804 3.10218 6 3.5 6C3.89782 6 4.27936 6.15804 4.56066 6.43934C4.84196 6.72064 5 7.10218 5 7.5C5 7.89782 4.84196 8.27936 4.56066 8.56066C4.27936 8.84196 3.89782 9 3.5 9ZM9 0C6.61305 0 4.32387 0.948211 2.63604 2.63604C0.948211 4.32387 0 6.61305 0 9C0 11.3869 0.948211 13.6761 2.63604 15.364C4.32387 17.0518 6.61305 18 9 18C9.39782 18 9.77936 17.842 10.0607 17.5607C10.342 17.2794 10.5 16.8978 10.5 16.5C10.5 16.11 10.35 15.76 10.11 15.5C9.88 15.23 9.73 14.88 9.73 14.5C9.73 14.1022 9.88804 13.7206 10.1693 13.4393C10.4506 13.158 10.8322 13 11.23 13H13C14.3261 13 15.5979 12.4732 16.5355 11.5355C17.4732 10.5979 18 9.32608 18 8C18 3.58 13.97 0 9 0Z"/></svg>',
			'header'		=> '<svg viewBox="0 0 20 13"><path d="M1.375 0.625C0.960787 0.625 0.625 0.960786 0.625 1.375V11.5H2.875V2.875H17.125V9.625H11.5V11.875H18.625C19.0392 11.875 19.375 11.5392 19.375 11.125V1.375C19.375 0.960786 19.0392 0.625 18.625 0.625H1.375Z"/><path d="M4.375 7C4.16789 7 4 7.16789 4 7.375V12.625C4 12.8321 4.16789 13 4.375 13H9.625C9.83211 13 10 12.8321 10 12.625V7.375C10 7.16789 9.83211 7 9.625 7H4.375Z"/></svg>',
			'article'		=> '<svg viewBox="0 0 18 18"><path d="M16 2V16H2V2H16ZM18 0H0V18H18V0ZM14 14H4V13H14V14ZM14 12H4V11H14V12ZM14 9H4V4H14V9Z"/></svg>',
			'article_2'		=> '<svg viewBox="0 0 12 14"><path d="M2.0013 0.333496C1.64768 0.333496 1.30854 0.473972 1.05849 0.72402C0.808444 0.974069 0.667969 1.31321 0.667969 1.66683V12.3335C0.667969 12.6871 0.808444 13.0263 1.05849 13.2763C1.30854 13.5264 1.64768 13.6668 2.0013 13.6668H10.0013C10.3549 13.6668 10.6941 13.5264 10.9441 13.2763C11.1942 13.0263 11.3346 12.6871 11.3346 12.3335V4.3335L7.33463 0.333496H2.0013ZM2.0013 1.66683H6.66797V5.00016H10.0013V12.3335H2.0013V1.66683ZM3.33464 7.00016V8.3335H8.66797V7.00016H3.33464ZM3.33464 9.66683V11.0002H6.66797V9.66683H3.33464Z"/></svg>',
			'like_box'		=> '<svg viewBox="0 0 18 17"><path d="M17.505 7.91114C17.505 7.48908 17.3373 7.08431 17.0389 6.78587C16.7405 6.48744 16.3357 6.31977 15.9136 6.31977H10.8849L11.6488 2.68351C11.6647 2.60394 11.6727 2.51641 11.6727 2.42889C11.6727 2.10266 11.5374 1.8003 11.3226 1.58547L10.4791 0.75L5.24354 5.98559C4.94914 6.27999 4.77409 6.67783 4.77409 7.11546V15.0723C4.77409 15.4943 4.94175 15.8991 5.24019 16.1975C5.53863 16.496 5.9434 16.6636 6.36546 16.6636H13.5266C14.187 16.6636 14.7519 16.2658 14.9906 15.6929L17.3936 10.0834C17.4652 9.90034 17.505 9.70938 17.505 9.5025V7.91114ZM0 16.6636H3.18273V7.11546H0V16.6636Z"/></svg>',
			'load_more'		=> '<svg viewBox="0 0 24 24"><path d="M20 18.5H4C3.46957 18.5 2.96086 18.2893 2.58579 17.9142C2.21071 17.5391 2 17.0304 2 16.5V7.5C2 6.96957 2.21071 6.46086 2.58579 6.08579C2.96086 5.71071 3.46957 5.5 4 5.5H20C20.5304 5.5 21.0391 5.71071 21.4142 6.08579C21.7893 6.46086 22 6.96957 22 7.5V16.5C22 17.0304 21.7893 17.5391 21.4142 17.9142C21.0391 18.2893 20.5304 18.5 20 18.5ZM4 7.5V16.5H20V7.5H4Z"/><circle cx="7.5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="16.5" cy="12" r="1.5"/></svg>',
			'lightbox'		=> '<svg viewBox="0 0 24 24"><path d="M21 17H7V3H21V17ZM21 1H7C6.46957 1 5.96086 1.21071 5.58579 1.58579C5.21071 1.96086 5 2.46957 5 3V17C5 17.5304 5.21071 18.0391 5.58579 18.4142C5.96086 18.7893 6.46957 19 7 19H21C21.5304 19 22.0391 18.7893 22.4142 18.4142C22.7893 18.0391 23 17.5304 23 17V3C23 2.46957 22.7893 1.96086 22.4142 1.58579C22.0391 1.21071 21.5304 1 21 1ZM3 5H1V21C1 21.5304 1.21071 22.0391 1.58579 22.4142C1.96086 22.7893 2.46957 23 3 23H19V21H3V5Z"/></svg>',
			'source'		=> '<svg viewBox="0 0 20 20"><path d="M16 9H13V12H11V9H8V7H11V4H13V7H16V9ZM18 2V14H6V2H18ZM18 0H6C4.9 0 4 0.9 4 2V14C4 14.5304 4.21071 15.0391 4.58579 15.4142C4.96086 15.7893 5.46957 16 6 16H18C19.11 16 20 15.11 20 14V2C20 1.46957 19.7893 0.960859 19.4142 0.585786C19.0391 0.210714 18.5304 0 18 0ZM2 4H0V18C0 18.5304 0.210714 19.0391 0.585786 19.4142C0.960859 19.7893 1.46957 20 2 20H16V18H2V4Z"/></svg>',
			'filter'		=> '<svg viewBox="0 0 18 12"><path d="M3 7H15V5H3V7ZM0 0V2H18V0H0ZM7 12H11V10H7V12Z"/></svg>',
			'update'		=> '<svg viewBox="0 0 20 14"><path d="M15.832 3.66659L12.4987 6.99992H14.9987C14.9987 8.326 14.4719 9.59777 13.5342 10.5355C12.5965 11.4731 11.3248 11.9999 9.9987 11.9999C9.16536 11.9999 8.35703 11.7916 7.66536 11.4166L6.4487 12.6333C7.50961 13.3085 8.74115 13.6669 9.9987 13.6666C11.7668 13.6666 13.4625 12.9642 14.7127 11.714C15.963 10.4637 16.6654 8.76803 16.6654 6.99992H19.1654L15.832 3.66659ZM4.9987 6.99992C4.9987 5.67384 5.52548 4.40207 6.46316 3.46438C7.40085 2.5267 8.67261 1.99992 9.9987 1.99992C10.832 1.99992 11.6404 2.20825 12.332 2.58325L13.5487 1.36659C12.4878 0.691379 11.2562 0.332902 9.9987 0.333252C8.23059 0.333252 6.53489 1.03563 5.28465 2.28587C4.03441 3.53612 3.33203 5.23181 3.33203 6.99992H0.832031L4.16536 10.3333L7.4987 6.99992"/></svg>',
			'sun'			=> '<svg viewBox="0 0 16 15"><path d="M2.36797 12.36L3.30797 13.3L4.50797 12.1067L3.5613 11.16L2.36797 12.36ZM7.33463 14.9667H8.66797V13H7.33463V14.9667ZM8.0013 3.6667C6.94044 3.6667 5.92302 4.08813 5.17287 4.83827C4.42273 5.58842 4.0013 6.60583 4.0013 7.6667C4.0013 8.72756 4.42273 9.74498 5.17287 10.4951C5.92302 11.2453 6.94044 11.6667 8.0013 11.6667C9.06217 11.6667 10.0796 11.2453 10.8297 10.4951C11.5799 9.74498 12.0013 8.72756 12.0013 7.6667C12.0013 5.45336 10.208 3.6667 8.0013 3.6667ZM13.3346 8.33336H15.3346V7.00003H13.3346V8.33336ZM11.4946 12.1067L12.6946 13.3L13.6346 12.36L12.4413 11.16L11.4946 12.1067ZM13.6346 2.97337L12.6946 2.03337L11.4946 3.2267L12.4413 4.17336L13.6346 2.97337ZM8.66797 0.366699H7.33463V2.33337H8.66797V0.366699ZM2.66797 7.00003H0.667969V8.33336H2.66797V7.00003ZM4.50797 3.2267L3.30797 2.03337L2.36797 2.97337L3.5613 4.17336L4.50797 3.2267Z"/></svg>',
			'moon'			=> '<svg viewBox="0 0 10 10"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.63326 6.88308C9.26754 6.95968 8.88847 6.99996 8.5 6.99996C5.46243 6.99996 3 4.53752 3 1.49996C3 1.11148 3.04028 0.732413 3.11688 0.366699C1.28879 1.11045 0 2.9047 0 4.99996C0 7.76138 2.23858 9.99996 5 9.99996C7.09526 9.99996 8.88951 8.71117 9.63326 6.88308Z"/></svg>',
			'visual'		=> '<svg viewBox="0 0 12 12"><path d="M3.66667 7L5.33333 9L7.66667 6L10.6667 10H1.33333L3.66667 7ZM12 10.6667V1.33333C12 0.979711 11.8595 0.640573 11.6095 0.390524C11.3594 0.140476 11.0203 0 10.6667 0H1.33333C0.979711 0 0.640573 0.140476 0.390524 0.390524C0.140476 0.640573 0 0.979711 0 1.33333V10.6667C0 11.0203 0.140476 11.3594 0.390524 11.6095C0.640573 11.8595 0.979711 12 1.33333 12H10.6667C11.0203 12 11.3594 11.8595 11.6095 11.6095C11.8595 11.3594 12 11.0203 12 10.6667Z" /></svg>',
			'text'			=> '<svg viewBox="0 0 14 12"><path d="M12.332 11.3334H1.66536C1.31174 11.3334 0.972604 11.1929 0.722555 10.9429C0.472507 10.6928 0.332031 10.3537 0.332031 10.0001V2.00008C0.332031 1.64646 0.472507 1.30732 0.722555 1.05727C0.972604 0.807224 1.31174 0.666748 1.66536 0.666748H12.332C12.6857 0.666748 13.0248 0.807224 13.2748 1.05727C13.5249 1.30732 13.6654 1.64646 13.6654 2.00008V10.0001C13.6654 10.3537 13.5249 10.6928 13.2748 10.9429C13.0248 11.1929 12.6857 11.3334 12.332 11.3334ZM1.66536 2.00008V10.0001H12.332V2.00008H1.66536ZM2.9987 4.00008H10.9987V5.33341H2.9987V4.00008ZM2.9987 6.66675H9.66537V8.00008H2.9987V6.66675Z"/></svg>',
			'background'	=> '<svg viewBox="0 0 14 12"><path d="M12.334 11.3334H1.66732C1.3137 11.3334 0.974557 11.1929 0.724509 10.9429C0.47446 10.6928 0.333984 10.3537 0.333984 10.0001V2.00008C0.333984 1.64646 0.47446 1.30732 0.724509 1.05727C0.974557 0.807224 1.3137 0.666748 1.66732 0.666748H12.334C12.6876 0.666748 13.0267 0.807224 13.2768 1.05727C13.5268 1.30732 13.6673 1.64646 13.6673 2.00008V10.0001C13.6673 10.3537 13.5268 10.6928 13.2768 10.9429C13.0267 11.1929 12.6876 11.3334 12.334 11.3334Z"/></svg>',
			'cursor'		=> '<svg viewBox="-96 0 512 512"><path d="m180.777344 512c-2.023438 0-4.03125-.382812-5.949219-1.152344-3.96875-1.578125-7.125-4.691406-8.789063-8.640625l-59.863281-141.84375-71.144531 62.890625c-2.988281 3.070313-8.34375 5.269532-13.890625 5.269532-11.648437 0-21.140625-9.515626-21.140625-21.226563v-386.070313c0-11.710937 9.492188-21.226562 21.140625-21.226562 4.929687 0 9.707031 1.726562 13.761719 5.011719l279.058594 282.96875c4.355468 5.351562 6.039062 10.066406 6.039062 14.972656 0 11.691406-9.492188 21.226563-21.140625 21.226563h-94.785156l57.6875 136.8125c3.410156 8.085937-.320313 17.386718-8.363281 20.886718l-66.242188 28.796875c-2.027344.875-4.203125 1.324219-6.378906 1.324219zm-68.5-194.367188c1.195312 0 2.367187.128907 3.5625.40625 5.011718 1.148438 9.195312 4.628907 11.179687 9.386719l62.226563 147.453125 36.886718-16.042968-60.90625-144.445313c-2.089843-4.929687-1.558593-10.605469 1.40625-15.0625 2.96875-4.457031 7.980469-7.148437 13.335938-7.148437h93.332031l-241.300781-244.671876v335.765626l69.675781-61.628907c2.941407-2.605469 6.738281-4.011719 10.601563-4.011719zm-97.984375 81.300782c-.449219.339844-.851563.703125-1.238281 1.085937zm275.710937-89.8125h.214844zm0 0"/></svg>',
			'link'			=> '<svg viewBox="0 0 14 8"><path d="M1.60065 4.00008C1.60065 2.86008 2.52732 1.93341 3.66732 1.93341H6.33399V0.666748H3.66732C2.78326 0.666748 1.93542 1.01794 1.3103 1.64306C0.685174 2.26818 0.333984 3.11603 0.333984 4.00008C0.333984 4.88414 0.685174 5.73198 1.3103 6.35711C1.93542 6.98223 2.78326 7.33342 3.66732 7.33342H6.33399V6.06675H3.66732C2.52732 6.06675 1.60065 5.14008 1.60065 4.00008ZM4.33398 4.66675H9.66732V3.33342H4.33398V4.66675ZM10.334 0.666748H7.66732V1.93341H10.334C11.474 1.93341 12.4007 2.86008 12.4007 4.00008C12.4007 5.14008 11.474 6.06675 10.334 6.06675H7.66732V7.33342H10.334C11.218 7.33342 12.0659 6.98223 12.691 6.35711C13.3161 5.73198 13.6673 4.88414 13.6673 4.00008C13.6673 3.11603 13.3161 2.26818 12.691 1.64306C12.0659 1.01794 11.218 0.666748 10.334 0.666748Z"/></svg>',
			'thumbnail'		=> '<svg viewBox="0 0 14 12"><path d="M0.332031 7.33333H4.33203V11.3333H0.332031V7.33333ZM9.66537 3.33333H5.66536V4.66666H9.66537V3.33333ZM0.332031 4.66666H4.33203V0.666664H0.332031V4.66666ZM5.66536 0.666664V2H13.6654V0.666664H5.66536ZM5.66536 11.3333H9.66537V10H5.66536V11.3333ZM5.66536 8.66666H13.6654V7.33333H5.66536"/></svg>',
			'halfwidth'		=> '<svg viewBox="0 0 14 8"><path d="M6 0.5H0V7.5H6V0.5Z"/><path d="M14 0.75H7.5V2H14V0.75Z"/><path d="M7.5 3.25H14V4.5H7.5V3.25Z"/><path d="M11 5.75H7.5V7H11V5.75Z"/></svg>',
			'fullwidth'		=> '<svg viewBox="0 0 10 12"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 6.75V0.333328H0V6.75H10Z"/><path d="M0 8.24999H10V9.49999H0V8.24999Z"/><path d="M6 10.75H0V12H6V10.75Z"/></svg>',
			'boxed'			=> '<svg viewBox="0 0 16 16"><path d="M14.1667 12.8905H1.83333C1.47971 12.8905 1.14057 12.75 0.890524 12.5C0.640476 12.25 0.5 11.9108 0.5 11.5572V3.33333C0.5 2.97971 0.640476 2.64057 0.890524 2.39052C1.14057 2.14048 1.47971 2 1.83333 2H14.1667C14.5203 2 14.8594 2.14048 15.1095 2.39052C15.3595 2.64057 15.5 2.97971 15.5 3.33333V11.5572C15.5 11.9108 15.3595 12.25 15.1095 12.5C14.8594 12.75 14.5203 12.8905 14.1667 12.8905ZM1.83333 3.33333V11.5572H14.1667V3.33333H1.83333Z"/><path d="M8 8H11V9H8V8Z"/><path d="M6.5 9.5H3V5.5H6.5V9.5Z"/><path d="M8 7V6H13V7H8Z"/></svg>',
			'corner'		=> '<svg viewBox="0 0 12 12"><path fill-rule="evenodd" clip-rule="evenodd" d="M5 1.5H1.5V10.5H10.5V7C10.5 3.96243 8.03757 1.5 5 1.5ZM0 0V12H12V7C12 3.13401 8.86599 0 5 0H0Z"/></svg>',
			'preview'		=> '<svg viewBox="0 0 16 10"><path d="M8.0013 3C7.47087 3 6.96216 3.21071 6.58709 3.58579C6.21202 3.96086 6.0013 4.46957 6.0013 5C6.0013 5.53043 6.21202 6.03914 6.58709 6.41421C6.96216 6.78929 7.47087 7 8.0013 7C8.53173 7 9.04044 6.78929 9.41551 6.41421C9.79059 6.03914 10.0013 5.53043 10.0013 5C10.0013 4.46957 9.79059 3.96086 9.41551 3.58579C9.04044 3.21071 8.53173 3 8.0013 3ZM8.0013 8.33333C7.11725 8.33333 6.2694 7.98214 5.64428 7.35702C5.01916 6.7319 4.66797 5.88406 4.66797 5C4.66797 4.11595 5.01916 3.2681 5.64428 2.64298C6.2694 2.01786 7.11725 1.66667 8.0013 1.66667C8.88536 1.66667 9.7332 2.01786 10.3583 2.64298C10.9834 3.2681 11.3346 4.11595 11.3346 5C11.3346 5.88406 10.9834 6.7319 10.3583 7.35702C9.7332 7.98214 8.88536 8.33333 8.0013 8.33333ZM8.0013 0C4.66797 0 1.8213 2.07333 0.667969 5C1.8213 7.92667 4.66797 10 8.0013 10C11.3346 10 14.1813 7.92667 15.3346 5C14.1813 2.07333 11.3346 0 8.0013 0Z"/></svg>',
			'flag'			=> '<svg viewBox="0 0 9 9"><path d="M5.53203 1L5.33203 0H0.832031V8.5H1.83203V5H4.63203L4.83203 6H8.33203V1H5.53203Z"/></svg>',
			'copy2'			=> '<svg viewBox="0 0 12 13"><path d="M10.25 0.25H4.625C3.9375 0.25 3.375 0.8125 3.375 1.5V9C3.375 9.6875 3.9375 10.25 4.625 10.25H10.25C10.9375 10.25 11.5 9.6875 11.5 9V1.5C11.5 0.8125 10.9375 0.25 10.25 0.25ZM10.25 9H4.625V1.5H10.25V9ZM0.875 8.375V7.125H2.125V8.375H0.875ZM0.875 4.9375H2.125V6.1875H0.875V4.9375ZM5.25 11.5H6.5V12.75H5.25V11.5ZM0.875 10.5625V9.3125H2.125V10.5625H0.875ZM2.125 12.75C1.4375 12.75 0.875 12.1875 0.875 11.5H2.125V12.75ZM4.3125 12.75H3.0625V11.5H4.3125V12.75ZM7.4375 12.75V11.5H8.6875C8.6875 12.1875 8.125 12.75 7.4375 12.75ZM2.125 2.75V4H0.875C0.875 3.3125 1.4375 2.75 2.125 2.75Z"/></svg>',
			'timelineIcon'	=> '<svg width="187" height="91" viewBox="0 0 187 85" fill="none" xmlns="http://www.w3.org/2000/svg"> <g filter="url(#filter0_ddd)"> <rect x="13.75" y="7.5" width="160" height="64" rx="2" fill="white"/> </g> <rect x="24.75" y="36.5" width="135.109" height="9" fill="#D8DADD"/> <rect x="24.75" y="51.5" width="84.1981" height="9" fill="#D8DADD"/> <circle cx="31.25" cy="22" r="6.5" fill="#D8DADD"/> <defs> <filter id="filter0_ddd" x="0.75" y="0.5" width="186" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> </defs> </svg>',
			'photosIcon'	=> '<svg width="209" height="136" viewBox="0 0 209 136" fill="none"> <g clip-path="url(#clip0_phts)"> <rect x="80.2002" y="44" width="48" height="48" fill="#43A6DB"/> <circle cx="70.7002" cy="78.5" r="40.5" fill="#86D0F9"/> </g> <g clip-path="url(#clip1_phts)"> <rect x="131.2" y="44" width="48" height="48" fill="#B6DDAD"/> <rect x="152.2" y="65" width="33" height="33" fill="#96CE89"/> </g> <g clip-path="url(#clip2_phts)"> <rect x="29.2002" y="44" width="48" height="48" fill="#F6966B"/> <path d="M38.6485 61L76.6485 99H7.2002L38.6485 61Z" fill="#F9BBA0"/> </g> <defs> <clipPath id="clip0_phts"> <rect x="80.2002" y="44" width="48" height="48" rx="1" fill="white"/> </clipPath> <clipPath id="clip1_phts"> <rect x="131.2" y="44" width="48" height="48" rx="1" fill="white"/> </clipPath> <clipPath id="clip2_phts"> <rect x="29.2002" y="44" width="48" height="48" rx="1" fill="white"/> </clipPath> </defs> </svg>',
			'videosIcon'	=> '<svg width="209" height="136" viewBox="0 0 209 136" fill="none"> <rect x="41.6001" y="31" width="126" height="74" fill="#43A6DB"/> <path fill-rule="evenodd" clip-rule="evenodd" d="M104.6 81C111.78 81 117.6 75.1797 117.6 68C117.6 60.8203 111.78 55 104.6 55C97.4204 55 91.6001 60.8203 91.6001 68C91.6001 75.1797 97.4204 81 104.6 81ZM102.348 63.2846C102.015 63.0942 101.6 63.3349 101.6 63.7188V72.2813C101.6 72.6652 102.015 72.9059 102.348 72.7154L109.84 68.4342C110.176 68.2422 110.176 67.7579 109.84 67.5659L102.348 63.2846Z" fill="white"/> </svg>',
			'albumsIcon'	=> '<svg width="210" height="136" viewBox="0 0 210 136" fill="none"> <g clip-path="url(#clip0_albm)"> <rect x="76.1187" y="39.7202" width="57.7627" height="57.7627" fill="#43A6DB"/> <rect x="101.39" y="64.9917" width="39.7119" height="39.7119" fill="#86D0F9"/> </g> <g clip-path="url(#clip1_albm)"> <rect x="70.1016" y="32.5" width="57.7627" height="57.7627" fill="#F9BBA0"/> <path d="M81.4715 52.9575L127.2 98.6863H43.627L81.4715 52.9575Z" fill="#F6966B"/> </g> <defs> <clipPath id="clip0_albm"> <rect x="76.1187" y="39.7202" width="57.7627" height="57.7627" rx="1.20339" fill="white"/> </clipPath> <clipPath id="clip1_albm"> <rect x="70.1016" y="32.5" width="57.7627" height="57.7627" rx="1.20339" fill="white"/> </clipPath> </defs> </svg>',
			'eventsIcon'	=> '<svg width="209" height="136" viewBox="0 0 209 136" fill="none"> <g filter="url(#filter0_ddd_evt)"> <rect x="20.5562" y="39.9375" width="160" height="64" rx="2" fill="white"/> </g> <rect x="31.6001" y="69" width="102" height="9" fill="#D8DADD"/> <rect x="31.6001" y="84" width="64" height="9" fill="#D8DADD"/> <circle cx="38.0562" cy="54.4375" r="6.5" fill="#D8DADD"/> <circle cx="173.744" cy="46.5625" r="14.5" fill="#E34F0E"/> <path d="M169.275 53.5L173.775 50.875L178.275 53.5V42.625C178.275 42.0156 177.759 41.5 177.15 41.5H170.4C169.767 41.5 169.275 42.0156 169.275 42.625V53.5Z" fill="white"/> <defs> <filter id="filter0_ddd_evt" x="7.55615" y="32.9375" width="186" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> </defs> </svg>',
			'reviewsIcon'	=> '<svg width="207" height="129" viewBox="0 0 207 129" fill="none"> <g filter="url(#filter0_ddd_rev)"> <rect x="23.5" y="32.5" width="160" height="64" rx="2" fill="white"/> </g> <path d="M61.0044 42.8004C61.048 42.6917 61.202 42.6917 61.2456 42.8004L62.7757 46.6105C62.7942 46.6568 62.8377 46.6884 62.8875 46.6917L66.9839 46.9695C67.1008 46.9774 67.1484 47.1238 67.0584 47.199L63.9077 49.8315C63.8694 49.8635 63.8528 49.9145 63.8649 49.9629L64.8666 53.9447C64.8952 54.0583 64.7707 54.1488 64.6714 54.0865L61.1941 51.9034C61.1519 51.8769 61.0981 51.8769 61.0559 51.9034L57.5786 54.0865C57.4793 54.1488 57.3548 54.0583 57.3834 53.9447L58.3851 49.9629C58.3972 49.9145 58.3806 49.8635 58.3423 49.8315L55.1916 47.199C55.1016 47.1238 55.1492 46.9774 55.2661 46.9695L59.3625 46.6917C59.4123 46.6884 59.4558 46.6568 59.4743 46.6105L61.0044 42.8004Z" fill="#E34F0E"/> <path d="M76.6045 42.8004C76.6481 42.6917 76.8021 42.6917 76.8457 42.8004L78.3757 46.6105C78.3943 46.6568 78.4378 46.6884 78.4876 46.6917L82.584 46.9695C82.7009 46.9774 82.7485 47.1238 82.6585 47.199L79.5078 49.8315C79.4695 49.8635 79.4529 49.9145 79.465 49.9629L80.4667 53.9447C80.4953 54.0583 80.3708 54.1488 80.2715 54.0865L76.7942 51.9034C76.752 51.8769 76.6982 51.8769 76.656 51.9034L73.1787 54.0865C73.0794 54.1488 72.9549 54.0583 72.9835 53.9447L73.9852 49.9629C73.9973 49.9145 73.9807 49.8635 73.9424 49.8315L70.7917 47.199C70.7017 47.1238 70.7493 46.9774 70.8662 46.9695L74.9626 46.6917C75.0124 46.6884 75.0559 46.6568 75.0744 46.6105L76.6045 42.8004Z" fill="#E34F0E"/> <path d="M92.2046 42.8004C92.2482 42.6917 92.4022 42.6917 92.4458 42.8004L93.9758 46.6105C93.9944 46.6568 94.0379 46.6884 94.0877 46.6917L98.1841 46.9695C98.301 46.9774 98.3486 47.1238 98.2586 47.199L95.1078 49.8315C95.0696 49.8635 95.053 49.9145 95.0651 49.9629L96.0668 53.9447C96.0954 54.0583 95.9709 54.1488 95.8716 54.0865L92.3943 51.9034C92.3521 51.8769 92.2983 51.8769 92.2561 51.9034L88.7788 54.0865C88.6795 54.1488 88.555 54.0583 88.5836 53.9447L89.5853 49.9629C89.5974 49.9145 89.5808 49.8635 89.5425 49.8315L86.3918 47.199C86.3018 47.1238 86.3494 46.9774 86.4663 46.9695L90.5627 46.6917C90.6125 46.6884 90.6559 46.6568 90.6745 46.6105L92.2046 42.8004Z" fill="#E34F0E"/> <path d="M107.804 42.8004C107.848 42.6917 108.002 42.6917 108.045 42.8004L109.575 46.6105C109.594 46.6568 109.638 46.6884 109.687 46.6917L113.784 46.9695C113.901 46.9774 113.948 47.1238 113.858 47.199L110.707 49.8315C110.669 49.8635 110.653 49.9145 110.665 49.9629L111.666 53.9447C111.695 54.0583 111.57 54.1488 111.471 54.0865L107.994 51.9034C107.952 51.8769 107.898 51.8769 107.856 51.9034L104.378 54.0865C104.279 54.1488 104.155 54.0583 104.183 53.9447L105.185 49.9629C105.197 49.9145 105.18 49.8635 105.142 49.8315L101.991 47.199C101.901 47.1238 101.949 46.9774 102.066 46.9695L106.162 46.6917C106.212 46.6884 106.256 46.6568 106.274 46.6105L107.804 42.8004Z" fill="#E34F0E"/> <path d="M123.404 42.8004C123.448 42.6917 123.602 42.6917 123.646 42.8004L125.176 46.6105C125.194 46.6568 125.238 46.6884 125.287 46.6917L129.384 46.9695C129.501 46.9774 129.548 47.1238 129.458 47.199L126.308 49.8315C126.269 49.8635 126.253 49.9145 126.265 49.9629L127.267 53.9447C127.295 54.0583 127.171 54.1488 127.071 54.0865L123.594 51.9034C123.552 51.8769 123.498 51.8769 123.456 51.9034L119.978 54.0865C119.879 54.1488 119.755 54.0583 119.783 53.9447L120.785 49.9629C120.797 49.9145 120.781 49.8635 120.742 49.8315L117.591 47.199C117.502 47.1238 117.549 46.9774 117.666 46.9695L121.762 46.6917C121.812 46.6884 121.856 46.6568 121.874 46.6105L123.404 42.8004Z" fill="#E34F0E"/> <rect x="54.625" y="65.5" width="70" height="7" fill="#D8DADD"/> <rect x="54.625" y="78.5" width="43" height="7" fill="#D8DADD"/> <circle cx="39" cy="49" r="6.5" fill="#D8DADD"/> <defs> <filter id="filter0_ddd_rev" x="10.5" y="25.5" width="186" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> </defs> </svg>',
			'featuredpostIcon'	=> '<svg width="207" height="129" viewBox="0 0 207 129" fill="none"> <g filter="url(#filter0_ddd_ftpst)"> <rect x="21.4282" y="34.7188" width="160" height="64" rx="2" fill="white"/> </g> <g clip-path="url(#clip0_ftpst)"> <rect width="55" height="56" transform="translate(122.228 38.7188)" fill="#43A6DB"/> <circle cx="197.728" cy="101.219" r="55.5" fill="#86D0F9"/> </g> <rect x="32.4282" y="63.7188" width="69" height="9" fill="#D8DADD"/> <rect x="32.4282" y="78.7188" width="43" height="9" fill="#D8DADD"/> <circle cx="38.9282" cy="49.2188" r="6.5" fill="#D8DADD"/> <circle cx="171.072" cy="44.7812" r="15.5" fill="#F37036" stroke="#FEF4EF" stroke-width="2"/> <path d="M173.587 44.7578L173.283 41.9688H174.291C174.595 41.9688 174.853 41.7344 174.853 41.4062V40.2812C174.853 39.9766 174.595 39.7188 174.291 39.7188H167.916C167.587 39.7188 167.353 39.9766 167.353 40.2812V41.4062C167.353 41.7344 167.587 41.9688 167.916 41.9688H168.9L168.595 44.7578C167.47 45.2734 166.603 46.2344 166.603 47.4062C166.603 47.7344 166.837 47.9688 167.166 47.9688H170.353V50.4297C170.353 50.4531 170.353 50.4766 170.353 50.5L170.916 51.625C170.986 51.7656 171.197 51.7656 171.267 51.625L171.83 50.5C171.83 50.4766 171.853 50.4531 171.853 50.4297V47.9688H175.041C175.345 47.9688 175.603 47.7344 175.603 47.4062C175.603 46.2109 174.712 45.2734 173.587 44.7578Z" fill="white"/> <defs> <filter id="filter0_ddd_ftpst" x="8.42822" y="27.7188" width="186" height="90" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="6"/> <feGaussianBlur stdDeviation="6.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="1"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.11 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <clipPath id="clip0_ftpst"> <rect width="55" height="56" fill="white" transform="translate(122.228 38.7188)"/> </clipPath> </defs> </svg>',
			'singlealbumIcon'	=> '<svg width="207" height="129" viewBox="0 0 207 129" fill="none"> <g clip-path="url(#clip0_sglalb)"> <rect x="74.6187" y="36.2202" width="57.7627" height="57.7627" fill="#43A6DB"/> <rect x="99.8896" y="61.4917" width="39.7119" height="39.7119" fill="#86D0F9"/> </g> <g clip-path="url(#clip1_sglalb)"> <rect x="68.6016" y="29" width="57.7627" height="57.7627" fill="#F9BBA0"/> <path d="M79.9715 49.4575L125.7 95.1863H42.127L79.9715 49.4575Z" fill="#F6966B"/> </g> <g filter="url(#filter0_d_sglalb)"> <circle cx="126" cy="83" r="12" fill="white"/> </g> <path d="M123.584 79H122.205L120.217 80.2773V81.6055L122.088 80.4102H122.135V87H123.584V79ZM126.677 81H125.177L126.959 84L125.131 87H126.631L127.888 84.8398L129.158 87H130.646L128.806 84L130.615 81H129.119L127.888 83.2148L126.677 81Z" fill="black"/> <defs> <filter id="filter0_d_sglalb" x="109" y="67" width="34" height="34" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="1"/> <feGaussianBlur stdDeviation="2.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <clipPath id="clip0_sglalb"> <rect x="74.6187" y="36.2202" width="57.7627" height="57.7627" rx="1.20339" fill="white"/> </clipPath> <clipPath id="clip1_sglalb"> <rect x="68.6016" y="29" width="57.7627" height="57.7627" rx="1.20339" fill="white"/> </clipPath> </defs> </svg>',
			'socialwallIcon'	=> '<svg width="207" height="129" viewBox="0 0 207 129" fill="none"> <path d="M96.6875 47.5C96.6875 42.1484 92.3516 37.8125 87 37.8125C81.6484 37.8125 77.3125 42.1484 77.3125 47.5C77.3125 52.3438 80.8281 56.3672 85.4766 57.0703V50.3125H83.0156V47.5H85.4766V45.3906C85.4766 42.9688 86.9219 41.6016 89.1094 41.6016C90.2031 41.6016 91.2969 41.7969 91.2969 41.7969V44.1797H90.0859C88.875 44.1797 88.4844 44.9219 88.4844 45.7031V47.5H91.1797L90.75 50.3125H88.4844V57.0703C93.1328 56.3672 96.6875 52.3438 96.6875 47.5Z" fill="#2A65DB"/> <path d="M128.695 42.3828C128.461 41.4453 127.719 40.7031 126.82 40.4688C125.141 40 118.5 40 118.5 40C118.5 40 111.82 40 110.141 40.4688C109.242 40.7031 108.5 41.4453 108.266 42.3828C107.797 44.0234 107.797 47.5391 107.797 47.5391C107.797 47.5391 107.797 51.0156 108.266 52.6953C108.5 53.6328 109.242 54.3359 110.141 54.5703C111.82 55 118.5 55 118.5 55C118.5 55 125.141 55 126.82 54.5703C127.719 54.3359 128.461 53.6328 128.695 52.6953C129.164 51.0156 129.164 47.5391 129.164 47.5391C129.164 47.5391 129.164 44.0234 128.695 42.3828ZM116.312 50.7031V44.375L121.859 47.5391L116.312 50.7031Z" fill="url(#paint0_linear_sclwl)"/> <path d="M86 78.0078C83.5 78.0078 81.5078 80.0391 81.5078 82.5C81.5078 85 83.5 86.9922 86 86.9922C88.4609 86.9922 90.4922 85 90.4922 82.5C90.4922 80.0391 88.4609 78.0078 86 78.0078ZM86 85.4297C84.3984 85.4297 83.0703 84.1406 83.0703 82.5C83.0703 80.8984 84.3594 79.6094 86 79.6094C87.6016 79.6094 88.8906 80.8984 88.8906 82.5C88.8906 84.1406 87.6016 85.4297 86 85.4297ZM91.7031 77.8516C91.7031 77.2656 91.2344 76.7969 90.6484 76.7969C90.0625 76.7969 89.5938 77.2656 89.5938 77.8516C89.5938 78.4375 90.0625 78.9062 90.6484 78.9062C91.2344 78.9062 91.7031 78.4375 91.7031 77.8516ZM94.6719 78.9062C94.5938 77.5 94.2812 76.25 93.2656 75.2344C92.25 74.2188 91 73.9062 89.5938 73.8281C88.1484 73.75 83.8125 73.75 82.3672 73.8281C80.9609 73.9062 79.75 74.2188 78.6953 75.2344C77.6797 76.25 77.3672 77.5 77.2891 78.9062C77.2109 80.3516 77.2109 84.6875 77.2891 86.1328C77.3672 87.5391 77.6797 88.75 78.6953 89.8047C79.75 90.8203 80.9609 91.1328 82.3672 91.2109C83.8125 91.2891 88.1484 91.2891 89.5938 91.2109C91 91.1328 92.25 90.8203 93.2656 89.8047C94.2812 88.75 94.5938 87.5391 94.6719 86.1328C94.75 84.6875 94.75 80.3516 94.6719 78.9062ZM92.7969 87.6562C92.5234 88.4375 91.8984 89.0234 91.1562 89.3359C89.9844 89.8047 87.25 89.6875 86 89.6875C84.7109 89.6875 81.9766 89.8047 80.8438 89.3359C80.0625 89.0234 79.4766 88.4375 79.1641 87.6562C78.6953 86.5234 78.8125 83.7891 78.8125 82.5C78.8125 81.25 78.6953 78.5156 79.1641 77.3438C79.4766 76.6016 80.0625 76.0156 80.8438 75.7031C81.9766 75.2344 84.7109 75.3516 86 75.3516C87.25 75.3516 89.9844 75.2344 91.1562 75.7031C91.8984 75.9766 92.4844 76.6016 92.7969 77.3438C93.2656 78.5156 93.1484 81.25 93.1484 82.5C93.1484 83.7891 93.2656 86.5234 92.7969 87.6562Z" fill="url(#paint1_linear_swwl)"/> <path d="M127.93 78.4375C128.711 77.8516 129.414 77.1484 129.961 76.3281C129.258 76.6406 128.438 76.875 127.617 76.9531C128.477 76.4453 129.102 75.6641 129.414 74.6875C128.633 75.1562 127.734 75.5078 126.836 75.7031C126.055 74.8828 125 74.4141 123.828 74.4141C121.562 74.4141 119.727 76.25 119.727 78.5156C119.727 78.8281 119.766 79.1406 119.844 79.4531C116.445 79.2578 113.398 77.6172 111.367 75.1562C111.016 75.7422 110.82 76.4453 110.82 77.2266C110.82 78.6328 111.523 79.8828 112.656 80.625C111.992 80.5859 111.328 80.4297 110.781 80.1172V80.1562C110.781 82.1484 112.188 83.7891 114.062 84.1797C113.75 84.2578 113.359 84.3359 113.008 84.3359C112.734 84.3359 112.5 84.2969 112.227 84.2578C112.734 85.8984 114.258 87.0703 116.055 87.1094C114.648 88.2031 112.891 88.8672 110.977 88.8672C110.625 88.8672 110.312 88.8281 110 88.7891C111.797 89.9609 113.945 90.625 116.289 90.625C123.828 90.625 127.93 84.4141 127.93 78.9844C127.93 78.7891 127.93 78.6328 127.93 78.4375Z" fill="url(#paint2_linear)"/> <defs> <linearGradient id="paint0_linear_sclwl" x1="137.667" y1="33.4445" x2="109.486" y2="62.2514" gradientUnits="userSpaceOnUse"> <stop stop-color="#E3280E"/> <stop offset="1" stop-color="#E30E0E"/> </linearGradient> <linearGradient id="paint1_linear_swwl" x1="93.8998" y1="73.3444" x2="78.4998" y2="89.4444" gradientUnits="userSpaceOnUse"> <stop stop-color="#5F0EE3"/> <stop offset="0.713476" stop-color="#FF0000"/> <stop offset="1" stop-color="#FF5C00"/> </linearGradient> <linearGradient id="paint2_linear" x1="136.667" y1="68.4445" x2="108.674" y2="93.3272" gradientUnits="userSpaceOnUse"> <stop stop-color="#0E96E3"/> <stop offset="1" stop-color="#0EBDE3"/> </linearGradient> </defs> </svg>',
			'addPage'			=> '<svg viewBox="0 0 17 17"><path d="M12.1667 9.66667H13.8333V12.1667H16.3333V13.8333H13.8333V16.3333H12.1667V13.8333H9.66667V12.1667H12.1667V9.66667ZM2.16667 0.5H13.8333C14.7583 0.5 15.5 1.24167 15.5 2.16667V8.66667C14.9917 8.375 14.4333 8.16667 13.8333 8.06667V2.16667H2.16667V13.8333H8.06667C8.16667 14.4333 8.375 14.9917 8.66667 15.5H2.16667C1.24167 15.5 0.5 14.7583 0.5 13.8333V2.16667C0.5 1.24167 1.24167 0.5 2.16667 0.5ZM3.83333 3.83333H12.1667V5.5H3.83333V3.83333ZM3.83333 7.16667H12.1667V8.06667C11.4583 8.18333 10.8083 8.45 10.2333 8.83333H3.83333V7.16667ZM3.83333 10.5H8V12.1667H3.83333V10.5Z"/></svg>',
			'addWidget'			=> '<svg viewBox="0 0 15 16"><path d="M0 15.5H6.66667V8.83333H0V15.5ZM1.66667 10.5H5V13.8333H1.66667V10.5ZM0 7.16667H6.66667V0.5H0V7.16667ZM1.66667 2.16667H5V5.5H1.66667V2.16667ZM8.33333 0.5V7.16667H15V0.5H8.33333ZM13.3333 5.5H10V2.16667H13.3333V5.5ZM12.5 11.3333H15V13H12.5V15.5H10.8333V13H8.33333V11.3333H10.8333V8.83333H12.5V11.3333Z"/></svg>',
			'plus'				=> '<svg width="13" height="12" viewBox="0 0 13 12"><path d="M12.3327 6.83332H7.33268V11.8333H5.66602V6.83332H0.666016V5.16666H5.66602V0.166656H7.33268V5.16666H12.3327V6.83332Z"/></svg>',

			'facebookShare'		=> '<svg viewBox="0 0 448 512"><path fill="currentColor" d="M400 32H48A48 48 0 0 0 0 80v352a48 48 0 0 0 48 48h137.25V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.27c-30.81 0-40.42 19.12-40.42 38.73V256h68.78l-11 71.69h-57.78V480H400a48 48 0 0 0 48-48V80a48 48 0 0 0-48-48z"></path></svg>',
			'twitterShare'		=> '<svg viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>',
			'linkedinShare'		=> '<svg viewBox="0 0 448 512"><path fill="currentColor" d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"></path></svg>',
			'mailShare'			=> '<svg viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>',

			'successNotification'			=> '<svg viewBox="0 0 20 20"><path d="M10 0C4.5 0 0 4.5 0 10C0 15.5 4.5 20 10 20C15.5 20 20 15.5 20 10C20 4.5 15.5 0 10 0ZM8 15L3 10L4.41 8.59L8 12.17L15.59 4.58L17 6L8 15Z"/></svg>',
			'errorNotification'				=> '<svg viewBox="0 0 20 20"><path d="M9.99997 0C4.47997 0 -3.05176e-05 4.48 -3.05176e-05 10C-3.05176e-05 15.52 4.47997 20 9.99997 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 9.99997 0ZM11 15H8.99997V13H11V15ZM11 11H8.99997V5H11V11Z"/></svg>',
			'messageNotification'			=> '<svg viewBox="0 0 20 20"><path d="M11.0001 7H9.00012V5H11.0001V7ZM11.0001 15H9.00012V9H11.0001V15ZM10.0001 0C8.6869 0 7.38654 0.258658 6.17329 0.761205C4.96003 1.26375 3.85764 2.00035 2.92905 2.92893C1.05369 4.8043 0.00012207 7.34784 0.00012207 10C0.00012207 12.6522 1.05369 15.1957 2.92905 17.0711C3.85764 17.9997 4.96003 18.7362 6.17329 19.2388C7.38654 19.7413 8.6869 20 10.0001 20C12.6523 20 15.1958 18.9464 17.0712 17.0711C18.9466 15.1957 20.0001 12.6522 20.0001 10C20.0001 8.68678 19.7415 7.38642 19.2389 6.17317C18.7364 4.95991 17.9998 3.85752 17.0712 2.92893C16.1426 2.00035 15.0402 1.26375 13.827 0.761205C12.6137 0.258658 11.3133 0 10.0001 0Z"/></svg>',

			'albumsPreview'				=> '<svg width="63" height="65" viewBox="0 0 63 65" fill="none"><rect x="13.6484" y="10.2842" width="34.7288" height="34.7288" rx="1.44703" fill="#8C8F9A"/> <g filter="url(#filter0_dddalbumsPreview)"><rect x="22.1484" y="5.21962" width="34.7288" height="34.7288" rx="1.44703" transform="rotate(8 22.1484 5.21962)" fill="white"/> </g><path d="M29.0485 23.724L18.9288 28.1468L17.2674 39.9686L51.6582 44.802L52.2623 40.5031L29.0485 23.724Z" fill="#B5E5FF"/> <path d="M44.9106 25.2228L17.7194 36.7445L17.2663 39.9687L51.6571 44.802L53.4696 31.9054L44.9106 25.2228Z" fill="#43A6DB"/> <circle cx="42.9495" cy="18.3718" r="2.89406" transform="rotate(8 42.9495 18.3718)" fill="#43A6DB"/> <g filter="url(#filter1_dddalbumsPreview)"> <rect x="42.4766" y="33.9054" width="16.875" height="16.875" rx="8.4375" fill="white"/> <path d="M54.1953 42.8116H51.3828V45.6241H50.4453V42.8116H47.6328V41.8741H50.4453V39.0616H51.3828V41.8741H54.1953V42.8116Z" fill="#0068A0"/> </g> <defs> <filter id="filter0_dddalbumsPreview" x="0.86108" y="0.342124" width="58.3848" height="57.6613" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dx="-7.23516" dy="4.3411"/> <feGaussianBlur stdDeviation="4.70286"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="2.89406"/> <feGaussianBlur stdDeviation="1.44703"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> <filter id="filter1_dddalbumsPreview" x="25.8357" y="28.8408" width="36.4099" height="35.6864" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dx="-7.23516" dy="4.3411"/> <feGaussianBlur stdDeviation="4.70286"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="2.89406"/> <feGaussianBlur stdDeviation="1.44703"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="effect2_dropShadow" result="effect3_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect3_dropShadow" result="shape"/> </filter> </defs> </svg>',
			'featuredPostPreview'		=> '<svg width="47" height="48" viewBox="0 0 47 48" fill="none"> <g filter="url(#filter0_ddfeaturedpos)"> <rect x="2.09375" y="1.84264" width="34.7288" height="34.7288" rx="1.44703" fill="white"/> </g> <path d="M11.4995 19.2068L2.09375 24.9949L2.09375 36.9329H36.8225V32.5918L11.4995 19.2068Z" fill="#B5E5FF"/> <path d="M27.4168 18.4833L2.09375 33.6772V36.933H36.8225V23.9097L27.4168 18.4833Z" fill="#43A6DB"/> <circle cx="24.523" cy="11.9718" r="2.89406" fill="#43A6DB"/> <g filter="url(#filter1_ddfeaturedpos)"> <rect x="26.0312" y="25.2824" width="16.875" height="16.875" rx="8.4375" fill="white"/> <path d="M37.75 34.1886H34.9375V37.0011H34V34.1886H31.1875V33.2511H34V30.4386H34.9375V33.2511H37.75V34.1886Z" fill="#0068A0"/> </g> <defs> <filter id="filter0_ddfeaturedpos" x="0.09375" y="0.842636" width="40.7288" height="40.7288" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dx="1" dy="2"/> <feGaussianBlur stdDeviation="1.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow" result="shape"/> </filter> <filter id="filter1_ddfeaturedpos" x="24.0312" y="24.2824" width="22.875" height="22.875" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dx="1" dy="2"/> <feGaussianBlur stdDeviation="1.5"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/> <feBlend mode="normal" in2="effect1_dropShadow" result="effect2_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect2_dropShadow" result="shape"/> </filter> </defs> </svg>',
			'issueSinglePreview'		=> 	'<svg width="27" height="18" viewBox="0 0 27 18" fill="none"> <line x1="3.22082" y1="2.84915" x2="8.91471" y2="8.54304" stroke="#8C8F9A" stroke-width="3"/> <path d="M3.10938 8.65422L8.80327 2.96033" stroke="#8C8F9A" stroke-width="3"/> <line x1="18.3107" y1="2.84915" x2="24.0046" y2="8.54304" stroke="#8C8F9A" stroke-width="3"/> <path d="M18.1992 8.65422L23.8931 2.96033" stroke="#8C8F9A" stroke-width="3"/> <line x1="8.64062" y1="16.3863" x2="18.0351" y2="16.3863" stroke="#8C8F9A" stroke-width="3"/> </svg>',
			'playButton'				=> 	'<svg viewBox="0 0 448 512"><path fill="currentColor" d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z"></path></svg>',
			'spinner' 					=> '<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/></path></svg>',
			'rocket' 					=> '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.9411 18.4918L9.58281 15.3001C10.8911 14.8168 12.1161 14.1668 13.2495 13.4085L10.9411 18.4918ZM4.69948 10.4168L1.50781 9.05846L6.59115 6.75013C5.83281 7.88346 5.18281 9.10846 4.69948 10.4168ZM18.0078 1.9918C18.0078 1.9918 13.8828 0.224296 9.16615 4.9418C7.34115 6.7668 6.24948 8.77513 5.54115 10.5335C5.30781 11.1585 5.46615 11.8418 5.92448 12.3085L7.69948 14.0751C8.15781 14.5418 8.84115 14.6918 9.46615 14.4585C11.5649 13.6582 13.4706 12.4228 15.0578 10.8335C19.7745 6.1168 18.0078 1.9918 18.0078 1.9918ZM12.1161 7.88346C11.4661 7.23346 11.4661 6.17513 12.1161 5.52513C12.7661 4.87513 13.8245 4.87513 14.4745 5.52513C15.1161 6.17513 15.1245 7.23346 14.4745 7.88346C13.8245 8.53346 12.7661 8.53346 12.1161 7.88346ZM6.03297 17.5001L8.23281 15.3001C7.94948 15.2251 7.67448 15.1001 7.42448 14.9251L4.85797 17.5001H6.03297ZM2.49963 17.5001H3.67463L6.81615 14.3668L5.63281 13.1918L2.49963 16.3251V17.5001ZM2.49963 15.1418L5.07448 12.5751C4.89948 12.3251 4.77448 12.0585 4.69948 11.7668L2.49963 13.9668V15.1418Z" fill="white"/></svg>',
			'ctaBoxes'					=> array(
				'gallery'	=> '<svg width="57" height="56" viewBox="0 0 57 56" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="28.1951" height="23.9101" transform="translate(2.16699 12.0054) rotate(-5)" fill="#8C8F9A"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.2046 29.2969L18.2047 29.297L7.30116 30.2509L12.231 23.8084L15.7913 27.0796L20.432 20.2572L28.365 28.4079L18.2046 29.2969Z" fill="white"/><circle cx="13.6716" cy="18.5171" r="2.20279" transform="rotate(-5 13.6716 18.5171)" fill="white"/><rect x="22.9921" y="19.3603" width="29.958" height="25.5524" transform="rotate(4 22.9921 19.3603)" fill="#0096CC"/><path d="M32.4971 28.6891C32.52 28.3624 32.8787 28.174 33.1607 28.3405L41.1188 33.0404C41.4314 33.225 41.3991 33.6873 41.0638 33.8266L32.529 37.3732C32.2266 37.4988 31.8977 37.2623 31.9205 36.9356L32.4971 28.6891Z" fill="white"/><rect x="22.9921" y="19.3603" width="29.958" height="25.5524" transform="rotate(4 22.9921 19.3603)" stroke="white" stroke-width="1.76224"/></svg>',
				'like'		=> '<svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 14H41V36H16L7 41V14Z" fill="#8C8F9A"/><path d="M38.7033 21.3195C38.8925 21.1158 39.158 21 39.4361 21H52C52.5523 21 53 21.4477 53 22V45C53 45.5523 52.5523 46 52 46H28C27.4477 46 27 45.5523 27 45V29C27 28.4477 27.4477 28 28 28H32.0639C32.342 28 32.6075 27.8842 32.7967 27.6805L38.7033 21.3195Z" fill="white"/><path d="M48.9225 30.7297C48.9225 30.2373 48.7269 29.765 48.3787 29.4169C48.0305 29.0687 47.5583 28.8731 47.0659 28.8731H41.1991L42.0902 24.6308C42.1088 24.5379 42.1181 24.4358 42.1181 24.3337C42.1181 23.9531 41.9603 23.6004 41.7096 23.3497L40.7257 22.375L34.6175 28.4832C34.274 28.8267 34.0698 29.2908 34.0698 29.8014V39.0843C34.0698 39.5767 34.2654 40.049 34.6136 40.3971C34.9617 40.7453 35.434 40.9409 35.9264 40.9409H44.281C45.0515 40.9409 45.7106 40.4768 45.9891 39.8084L48.7925 33.2639C48.8761 33.0504 48.9225 32.8276 48.9225 32.5863V30.7297ZM28.5 40.9409H32.2132V29.8014H28.5V40.9409Z" fill="#0096CC"/></svg>',
				'feedTypes'	=> CFF_PLUGIN_URL . 'admin/assets/img/feed-types.svg',
				'loadMore'	=> '<svg width="57" height="56" viewBox="0 0 57 56" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="48" height="24" transform="translate(4.33203 13)" fill="#8C8F9A"/><circle cx="18.332" cy="25" r="3" fill="white"/><circle cx="28.332" cy="25" r="3" fill="white"/><circle cx="38.332" cy="25" r="3" fill="white"/><mask id="path-4-outside-1" maskUnits="userSpaceOnUse" x="30.4266" y="24.4708" width="27.0023" height="29.393" fill="black"><rect fill="white" x="30.4266" y="24.4708" width="27.0023" height="29.393"/><path fill-rule="evenodd" clip-rule="evenodd" d="M53.3369 43.6926L40.5179 30.8309L38.9653 48.9235L43.8738 45.2108L45.7 50.2281L49.0165 49.021L47.1904 44.0037L53.3369 43.6926Z"/></mask><path fill-rule="evenodd" clip-rule="evenodd" d="M53.3369 43.6926L40.5179 30.8309L38.9653 48.9235L43.8738 45.2108L45.7 50.2281L49.0165 49.021L47.1904 44.0037L53.3369 43.6926Z" fill="#0096CC"/><path d="M40.5179 30.8309L41.2262 30.1249L39.7057 28.5994L39.5215 30.7454L40.5179 30.8309ZM53.3369 43.6926L53.3875 44.6913L55.6311 44.5778L54.0452 42.9867L53.3369 43.6926ZM38.9653 48.9235L37.9689 48.838L37.7769 51.0762L39.5685 49.721L38.9653 48.9235ZM43.8738 45.2108L44.8135 44.8688L44.3504 43.5965L43.2706 44.4133L43.8738 45.2108ZM45.7 50.2281L44.7603 50.5702L45.1023 51.5099L46.042 51.1678L45.7 50.2281ZM49.0165 49.021L49.3585 49.9607L50.2982 49.6187L49.9562 48.679L49.0165 49.021ZM47.1904 44.0037L47.1398 43.005L45.7876 43.0734L46.2507 44.3457L47.1904 44.0037ZM39.8096 31.5368L52.6287 44.3985L54.0452 42.9867L41.2262 30.1249L39.8096 31.5368ZM39.9616 49.009L41.5142 30.9164L39.5215 30.7454L37.9689 48.838L39.9616 49.009ZM43.2706 44.4133L38.362 48.1259L39.5685 49.721L44.4771 46.0084L43.2706 44.4133ZM46.6397 49.8861L44.8135 44.8688L42.9341 45.5528L44.7603 50.5702L46.6397 49.8861ZM48.6745 48.0813L45.358 49.2884L46.042 51.1678L49.3585 49.9607L48.6745 48.0813ZM46.2507 44.3457L48.0768 49.363L49.9562 48.679L48.1301 43.6617L46.2507 44.3457ZM53.2864 42.6939L47.1398 43.005L47.2409 45.0024L53.3875 44.6913L53.2864 42.6939Z" fill="white" mask="url(#path-4-outside-1)"/></svg>'
			),
			'reviewsIconFree' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#reviewsFreeclip0)"> <g filter="url(#reviewsFreefilter0_d)"> <rect x="3" y="3" width="26.1602" height="26.1602" rx="2" fill="#F37036"/> </g> <path d="M15.5633 9.08746C15.7214 8.69383 16.2786 8.69383 16.4367 9.08746L17.9589 12.8781C18.0262 13.0457 18.1835 13.16 18.3638 13.1722L22.4393 13.4486C22.8625 13.4773 23.0347 14.0073 22.7092 14.2792L19.5744 16.8983C19.4358 17.0141 19.3757 17.1991 19.4198 17.3743L20.4164 21.3357C20.5199 21.7471 20.0691 22.0746 19.7098 21.849L16.2502 19.6771C16.0972 19.581 15.9028 19.581 15.7498 19.6771L12.2902 21.849C11.9309 22.0746 11.4801 21.747 11.5836 21.3357L12.5802 17.3743C12.6243 17.1991 12.5642 17.0141 12.4256 16.8983L9.29084 14.2792C8.96532 14.0073 9.13751 13.4773 9.56073 13.4486L13.6362 13.1722C13.8165 13.16 13.9738 13.0457 14.0411 12.8781L15.5633 9.08746Z" fill="white"/> </g> <defs> <filter id="reviewsFreefilter0_d" x="-3" y="0" width="38.1602" height="38.1602" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"> <feFlood flood-opacity="0" result="BackgroundImageFix"/> <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/> <feOffset dy="3"/> <feGaussianBlur stdDeviation="3"/> <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.04 0"/> <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/> <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/> </filter> <clipPath id="reviewsFreeclip0"> <rect width="32" height="32" fill="white"/> </clipPath> </defs> </svg>',
			'featuredpostIconFree' => '<svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#featuredPostFreeclip0)"> <rect x="0.5" y="13" width="26" height="18" rx="1" fill="#D0D1D7"/> <rect x="3.5" y="22" width="16" height="2" fill="white"/> <rect x="3.5" y="26" width="12" height="2" fill="white"/> <circle cx="22.5" cy="10" r="10" fill="#E34F0E"/> <path d="M24.7828 10.5642L25.649 8.15364L26.4759 8.55698C26.726 8.67892 27.0313 8.58978 27.1626 8.32053L27.6129 7.3974C27.7348 7.14739 27.6264 6.83265 27.3764 6.71071L22.1453 4.15935C21.8761 4.02802 21.5806 4.14578 21.4587 4.39579L21.0084 5.31892C20.8771 5.58817 20.9756 5.87429 21.2449 6.00561L22.0526 6.39957L20.6864 8.56623C19.5569 8.53909 18.4607 8.98054 17.9917 9.94213C17.8604 10.2114 17.9589 10.4975 18.2282 10.6288L20.8437 11.9045L19.8588 13.9239C19.8494 13.9431 19.84 13.9623 19.8307 13.9815L19.842 15.1298C19.8434 15.2733 20.0165 15.3578 20.1305 15.2705L21.0423 14.5725C21.0516 14.5533 21.0803 14.5434 21.0896 14.5242L22.0745 12.5048L24.6901 13.7805C24.9401 13.9024 25.2454 13.8133 25.3768 13.5441C25.8551 12.5632 25.4995 11.4375 24.7828 10.5642Z" fill="white"/> </g> <defs> <clipPath id="featuredPostFreeclip0"> <rect width="32" height="32" fill="white" transform="translate(0.5)"/> </clipPath> </defs> </svg>',
			'singlealbumIconFree' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#singleAlbumFreeclip0)"> <rect x="9.42969" y="6.29321" width="21" height="21" transform="rotate(4 9.42969 6.29321)" fill="#B6DDAD"/> <rect x="17.9531" y="16.0992" width="14.4375" height="14.4375" transform="rotate(4 17.9531 16.0992)" fill="#96CE89"/> </g> <g clip-path="url(#singleAlbumFreeclip1)"> <rect x="1.86523" y="2" width="21" height="21" fill="#F6966B"/> <circle cx="-2.29102" cy="17.0938" r="17.7188" fill="#F9BBA0"/> </g> <circle cx="22" cy="21" r="10" fill="#E34F0E"/> <path d="M18.0259 24.4121H19.6478V18H18.0214L16.3906 19.1109V20.5062L17.9948 19.4308H18.0259V24.4121Z" fill="white"/> <path d="M20.7075 24.4121H22.4405L23.6803 22.3325H23.7202L24.9689 24.4121H26.7997L24.7778 21.2038V21.1816L26.8174 18H25.0089L23.8402 20.1729H23.7913L22.6138 18H20.7119L22.6671 21.1638V21.1905L20.7075 24.4121Z" fill="white"/> <defs> <clipPath id="singleAlbumFreeclip0"> <rect x="9.42969" y="6.29321" width="21" height="21" rx="0.4375" transform="rotate(4 9.42969 6.29321)" fill="white"/> </clipPath> <clipPath id="singleAlbumFreeclip1"> <rect x="1.86523" y="2" width="21" height="21" rx="0.4375" fill="white"/> </clipPath> </defs> </svg>',
			'socialwallIconFree' => '<svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#socialWallFreeclip0)"> <path d="M8.49935 2.19C5.29102 2.19 2.66602 4.80917 2.66602 8.035C2.66602 10.9517 4.80102 13.3725 7.58935 13.81V9.72667H6.10768V8.035H7.58935V6.74584C7.58935 5.28167 8.45852 4.47667 9.79435 4.47667C10.4302 4.47667 11.0952 4.5875 11.0952 4.5875V6.02834H10.3602C9.63685 6.02834 9.40935 6.4775 9.40935 6.93834V8.035H11.031L10.7685 9.72667H9.40935V13.81C10.7839 13.5929 12.0356 12.8916 12.9385 11.8325C13.8413 10.7735 14.3358 9.42663 14.3327 8.035C14.3327 4.80917 11.7077 2.19 8.49935 2.19Z" fill="#006BFA"/> <path d="M8.5 21.3047C7 21.3047 5.80469 22.5234 5.80469 24C5.80469 25.5 7 26.6953 8.5 26.6953C9.97656 26.6953 11.1953 25.5 11.1953 24C11.1953 22.5234 9.97656 21.3047 8.5 21.3047ZM8.5 25.7578C7.53906 25.7578 6.74219 24.9844 6.74219 24C6.74219 23.0391 7.51562 22.2656 8.5 22.2656C9.46094 22.2656 10.2344 23.0391 10.2344 24C10.2344 24.9844 9.46094 25.7578 8.5 25.7578ZM11.9219 21.2109C11.9219 20.8594 11.6406 20.5781 11.2891 20.5781C10.9375 20.5781 10.6562 20.8594 10.6562 21.2109C10.6562 21.5625 10.9375 21.8438 11.2891 21.8438C11.6406 21.8438 11.9219 21.5625 11.9219 21.2109ZM13.7031 21.8438C13.6562 21 13.4688 20.25 12.8594 19.6406C12.25 19.0312 11.5 18.8438 10.6562 18.7969C9.78906 18.75 7.1875 18.75 6.32031 18.7969C5.47656 18.8438 4.75 19.0312 4.11719 19.6406C3.50781 20.25 3.32031 21 3.27344 21.8438C3.22656 22.7109 3.22656 25.3125 3.27344 26.1797C3.32031 27.0234 3.50781 27.75 4.11719 28.3828C4.75 28.9922 5.47656 29.1797 6.32031 29.2266C7.1875 29.2734 9.78906 29.2734 10.6562 29.2266C11.5 29.1797 12.25 28.9922 12.8594 28.3828C13.4688 27.75 13.6562 27.0234 13.7031 26.1797C13.75 25.3125 13.75 22.7109 13.7031 21.8438ZM12.5781 27.0938C12.4141 27.5625 12.0391 27.9141 11.5938 28.1016C10.8906 28.3828 9.25 28.3125 8.5 28.3125C7.72656 28.3125 6.08594 28.3828 5.40625 28.1016C4.9375 27.9141 4.58594 27.5625 4.39844 27.0938C4.11719 26.4141 4.1875 24.7734 4.1875 24C4.1875 23.25 4.11719 21.6094 4.39844 20.9062C4.58594 20.4609 4.9375 20.1094 5.40625 19.9219C6.08594 19.6406 7.72656 19.7109 8.5 19.7109C9.25 19.7109 10.8906 19.6406 11.5938 19.9219C12.0391 20.0859 12.3906 20.4609 12.5781 20.9062C12.8594 21.6094 12.7891 23.25 12.7891 24C12.7891 24.7734 12.8594 26.4141 12.5781 27.0938Z" fill="url(#socialWallFreepaint0_linear)"/> <path d="M30.6018 4.50001C30.1526 4.70418 29.6684 4.83834 29.1668 4.90251C29.6801 4.59334 30.0768 4.10334 30.2634 3.51418C29.7793 3.80584 29.2426 4.01001 28.6768 4.12668C28.2159 3.62501 27.5684 3.33334 26.8334 3.33334C25.4626 3.33334 24.3426 4.45334 24.3426 5.83584C24.3426 6.03418 24.3659 6.22668 24.4068 6.40751C22.3301 6.30251 20.4809 5.30501 19.2501 3.79418C19.0343 4.16168 18.9118 4.59334 18.9118 5.04834C18.9118 5.91751 19.3493 6.68751 20.0259 7.12501C19.6118 7.12501 19.2268 7.00834 18.8884 6.83334V6.85084C18.8884 8.06418 19.7518 9.07918 20.8951 9.30668C20.528 9.40713 20.1427 9.42111 19.7693 9.34751C19.9277 9.84479 20.238 10.2799 20.6565 10.5917C21.0751 10.9035 21.5808 11.0763 22.1026 11.0858C21.2181 11.7861 20.1216 12.1646 18.9934 12.1592C18.7951 12.1592 18.5968 12.1475 18.3984 12.1242C19.5068 12.8358 20.8251 13.25 22.2368 13.25C26.8334 13.25 29.3593 9.43501 29.3593 6.12751C29.3593 6.01668 29.3593 5.91168 29.3534 5.80084C29.8434 5.45084 30.2634 5.00751 30.6018 4.50001Z" fill="#1B90EF"/> <path d="M23.3327 25.75L26.3602 24L23.3327 22.25V25.75ZM30.076 21.1825C30.1518 21.4567 30.2043 21.8242 30.2393 22.2908C30.2802 22.7575 30.2977 23.16 30.2977 23.51L30.3327 24C30.3327 25.2775 30.2393 26.2167 30.076 26.8175C29.9302 27.3425 29.5918 27.6808 29.0668 27.8267C28.7927 27.9025 28.291 27.955 27.521 27.99C26.7627 28.0308 26.0685 28.0483 25.4268 28.0483L24.4993 28.0833C22.0552 28.0833 20.5327 27.99 19.9318 27.8267C19.4068 27.6808 19.0685 27.3425 18.9227 26.8175C18.8468 26.5433 18.7943 26.1758 18.7593 25.7092C18.7185 25.2425 18.701 24.84 18.701 24.49L18.666 24C18.666 22.7225 18.7593 21.7833 18.9227 21.1825C19.0685 20.6575 19.4068 20.3192 19.9318 20.1733C20.206 20.0975 20.7077 20.045 21.4777 20.01C22.236 19.9692 22.9302 19.9517 23.5718 19.9517L24.4993 19.9167C26.9435 19.9167 28.466 20.01 29.0668 20.1733C29.5918 20.3192 29.9302 20.6575 30.076 21.1825Z" fill="#EB2121"/> </g> <defs> <linearGradient id="socialWallFreepaint0_linear" x1="6.97891" y1="38.843" x2="29.0945" y2="16.268" gradientUnits="userSpaceOnUse"> <stop stop-color="white"/> <stop offset="0.147864" stop-color="#F6640E"/> <stop offset="0.443974" stop-color="#BA03A7"/> <stop offset="0.733337" stop-color="#6A01B9"/> <stop offset="1" stop-color="#6B01B9"/> </linearGradient> <clipPath id="socialWallFreeclip0"> <rect width="32" height="32" fill="white" transform="translate(0.5)"/> </clipPath> </defs> </svg>',
			'photosIconFree' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#freePhotosclip0)"> <rect x="2.90625" y="3" width="26.1602" height="26.1602" fill="#F6966B"/> <path d="M8.05548 12.265L28.7657 32.9752H-9.08398L8.05548 12.265Z" fill="#F9BBA0"/> </g> <defs> <clipPath id="freePhotosclip0"> <rect x="2.90625" y="3" width="26.1602" height="26.1602" rx="0.545005" fill="white"/> </clipPath> </defs> </svg>',
			'videosIconFree' => '<svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#videosFreeclip0)"> <path d="M0.25 4H32.25V28H0.25V4Z" fill="#43A6DB"/> <path d="M20.5716 15.576C20.8849 15.7718 20.8849 16.2282 20.5716 16.424L14.015 20.5219C13.682 20.73 13.25 20.4906 13.25 20.0979L13.25 11.9021C13.25 11.5094 13.682 11.27 14.015 11.4781L20.5716 15.576Z" fill="white"/> </g> <defs> <clipPath id="videosFreeclip0"> <rect width="32" height="32" fill="white" transform="translate(0.25)"/> </clipPath> </defs> </svg>',
			'albumsIconFree' => '<svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#albumsFreeclip0)"> <rect x="9.92969" y="6.29321" width="21" height="21" transform="rotate(4 9.92969 6.29321)" fill="#B6DDAD"/> <rect x="18.4531" y="16.0992" width="14.4375" height="14.4375" transform="rotate(4 18.4531 16.0992)" fill="#96CE89"/> </g> <g clip-path="url(#albumsFreeclip1)"> <rect x="2.36523" y="2" width="21" height="21" fill="#F6966B"/> <circle cx="-1.79102" cy="17.0938" r="17.7188" fill="#F9BBA0"/> </g> <defs> <clipPath id="albumsFreeclip0"> <rect x="9.92969" y="6.29321" width="21" height="21" rx="0.4375" transform="rotate(4 9.92969 6.29321)" fill="white"/> </clipPath> <clipPath id="albumsFreeclip1"> <rect x="2.36523" y="2" width="21" height="21" rx="0.4375" fill="white"/> </clipPath> </defs> </svg>',
			'eventsIconFree' => '<svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg"> <rect x="3.75" y="3" width="26.1602" height="26.1602" rx="2" fill="#43A6DB"/> <path d="M11.7852 21.9999L16.25 18.9999L20.7852 21.9999V11.1523C20.7852 10.5305 20.2591 10.0044 19.6373 10.0044L12.8978 9.99994C12.2522 9.99994 11.75 10.526 11.75 11.1478L11.7852 21.9999Z" fill="white"/> </svg>',
		];
		return $builder_svg_icons;
	}

	/**
	 * Color Overrides Manager
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_color_overrides(){
		return [
			//Post Author
			[
				'heading' 		=> __( 'Post Author', 'custom-facebook-feed' ),
				'elements'		=> ['authorcolor'],
				'enableViewAction' 	=> [
					'sections' => [
						'customize',
						'sections',
						'customize_posts',
						'nested_sections',
						'individual_elements',
						'controls',
						0,
						'section'
					],
					'id' => 'post_styling_author'
				],
				'controls' => [
					[
						'heading' => __( 'Text', 'custom-facebook-feed' ),
						'id'	  => 'authorcolor'
					]
				]
			],
			//Post Text
			[
				'heading' => __( 'Post', 'custom-facebook-feed' ),
				'elements'	=> ['textcolor'],
				'enableViewAction' 	=> [
					'sections' => [
						'customize',
						'sections',
						'customize_posts',
						'nested_sections',
						'individual_elements',
						'controls',
						1,
						'section'
					],
					'id' => 'post_styling_text'
				],
				'controls' => [
					[
						'heading' => __( 'Text', 'custom-facebook-feed' ),
						'id'	  => 'textcolor'
					]
				]
			],
			//Post Date
			[
				'heading' => __( 'Post Date', 'custom-facebook-feed' ),
				'elements'	=> ['datecolor'],
				'enableViewAction' 	=> [
					'sections' => [
						'customize',
						'sections',
						'customize_posts',
						'nested_sections',
						'individual_elements',
						'controls',
						2,
						'section'
					],
					'id' => 'post_styling_date'
				],
				'controls' => [
					[
						'heading' => __( 'Text', 'custom-facebook-feed' ),
						'id'	  => 'datecolor'
					]
				]
			],
			//Likes Shares & Comments
			[
				'heading' => __( 'Likes, Shares and Comments', 'custom-facebook-feed' ),
				'elements'	=> ['socialtextcolor','sociallinkcolor','socialbgcolor'],
				'enableViewAction' 	=> [
					'sections' => [
						'customize',
						'sections',
						'customize_posts',
						'nested_sections',
						'individual_elements',
						'controls',
						4,
						'section'
					],
					'id' => 'post_styling_social'
				],
				'controls' => [
					[
						'heading' => __( 'Text', 'custom-facebook-feed' ),
						'id'	  => 'socialtextcolor'
					],
					[
						'heading' => __( 'Link', 'custom-facebook-feed' ),
						'id'	  => 'sociallinkcolor'
					],
					[
						'heading' => __( 'Background', 'custom-facebook-feed' ),
						'id'	  => 'socialbgcolor'
					]
				]
			],
			//Event Title
			[
				'heading' => __( 'Event Title', 'custom-facebook-feed' ),
				'elements'	=> ['eventtitlecolor'],
				'enableViewAction' 	=> [
					'sections' => [
						'customize',
						'sections',
						'customize_posts',
						'nested_sections',
						'individual_elements',
						'controls',
						5,
						'section'
					],
					'id' => 'post_styling_eventtitle'
				],
				'controls' => [
					[
						'heading' => __( 'Text', 'custom-facebook-feed' ),
						'id'	  => 'eventtitlecolor'
					]
				]
			],
			//Event Details
			/*[
				'heading' => __( 'Event Details', 'custom-facebook-feed' ),
				'elements'	=> ['eventdetailscolor'],
				'enableViewAction' 	=> [
					'sections' => [
						'customize',
						'sections',
						'customize_posts',
						'nested_sections',
						'individual_elements',
						'controls',
						6,
						'section'
					],
					'id' => 'post_styling_eventdetails'
				],
				'controls' => [
					[
						'heading' => __( 'Text', 'custom-facebook-feed' ),
						'id'	  => 'eventdetailscolor'
					]
				]
			],*/
			//Link
			[
				'heading' => __( 'Post Action Links', 'custom-facebook-feed' ),
				'elements'	=> ['linkcolor'],
				'enableViewAction' 	=> [
					'sections' => [
						'customize',
						'sections',
						'customize_posts',
						'nested_sections',
						'individual_elements',
						'controls',
						7,
						'section'
					],
					'id' => 'post_styling_link'
				],
				'controls' => [
					[
						'heading' => __( 'Text', 'custom-facebook-feed' ),
						'id'	  => 'linkcolor'
					]
				]
			],
			//Description
			/*
			[
				'heading' => __( 'Shared Post', 'custom-facebook-feed' ),
				'elements'	=> ['desccolor'],
				'controls' => [
					[
						'heading' => __( 'Text', 'custom-facebook-feed' ),
						'id'	  => 'desccolor'
					]
				]
			],*/
			[
				'heading' => __( 'Shared Link Box', 'custom-facebook-feed' ),
				'elements'	=> ['linkbgcolor','linktitlecolor','linkdesccolor','linkurlcolor'],
				'enableViewAction' 	=> [
					'sections' => [
						'customize',
						'sections',
						'customize_posts',
						'nested_sections',
						'individual_elements',
						'controls',
						8,
						'section'
					],
					'id' => 'post_styling_sharedlinks'
				],
				'controls' => [
					[
						'heading' => __( 'Background', 'custom-facebook-feed' ),
						'id'	  => 'linkbgcolor'
					],
					[
						'heading' => __( 'Title', 'custom-facebook-feed' ),
						'id'	  => 'linktitlecolor'
					],
					[
						'heading' => __( 'Description', 'custom-facebook-feed' ),
						'id'	  => 'linkdesccolor'
					],
					[
						'heading' => __( 'Url', 'custom-facebook-feed' ),
						'id'	  => 'linkurlcolor'
					]
				]
			]

		];

	}

	public static function get_social_wall_links() {
		return array(
			'<a href="'. esc_url( admin_url( 'admin.php?page=cff-feed-builder' ) ) . '">' . __( 'All Feeds', 'custom-facebook-feed' ) . '</a>',
			'<a href="'. esc_url( admin_url( 'admin.php?page=cff-settings' ) ) . '">' . __( 'Settings', 'custom-facebook-feed' ) . '</a>',
			'<a href="'. esc_url( admin_url( 'admin.php?page=cff-oembeds-manager' ) ) . '">' . __( 'oEmbeds', 'custom-facebook-feed' ) . '</a>',
			'<a href="'. esc_url( admin_url( 'admin.php?page=cff-extensions-manager' ) ) . '">' . __( 'Extensions', 'custom-facebook-feed' ) . '</a>',
			'<a href="'. esc_url( admin_url( 'admin.php?page=cff-about-us' ) ) . '">' . __( 'About Us', 'custom-facebook-feed' ) . '</a>',
			'<a href="'. esc_url( admin_url( 'admin.php?page=cff-support' ) ) . '">' . __( 'Support', 'custom-facebook-feed' ) . '</a>',
		);
	}

	/**
	 * Feed Builder Wrapper.
	 *
	 * @since 4.0
	 */
	public function feed_builder(){
		include_once CFF_BUILDER_DIR . 'templates/builder.php';
	}

}
