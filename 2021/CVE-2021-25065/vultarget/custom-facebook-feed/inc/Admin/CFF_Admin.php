<?php
/**
 * CFF_Admin plugin.
 *
 * Contains everything about the Admin area
 *
 * @since 2.19
 */

namespace CustomFacebookFeed\Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class CFF_Admin{

	/**
	 * Admin constructor
	 *
	 * @since 2.19
	 */
	public function __construct(){
		$this->admin_hook();
		$this->register_assets();
	}


	/**
	 * Admin Hooks + Enqueue
	 *
	 * @since 2.19
	 */
	protected function admin_hook(){
		//Adding Dashboard Menu
		add_action( 'admin_menu', array(  $this, 'register_dashboard_menus' ), 9 );
	}


	/**
	 * Register CFF dashboard Menus.
	 *
	 * @since 2.19
	 */
	public function register_dashboard_menus(){
		$notice = '';
		if ( \cff_main()->cff_error_reporter->are_critical_errors() ) {
			$notice = ' <span class="cff-notice-alert"><span>!</span></span>';
		}

		$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
		$cap = apply_filters( 'cff_settings_pages_capability', $cap );

		$sbi_notifications = new CFF_Notifications();
		$notifications = $sbi_notifications->get();

		$notice_bubble = '';
		if ( empty( $notice ) && ! empty( $notifications ) && is_array( $notifications ) ) {
			$notice_bubble = ' <span class="cff-notice-alert"><span>'.count( $notifications ).'</span></span>';
		}

		add_menu_page(
			'Facebook Feed',
			'Facebook Feed'. $notice . $notice_bubble,
			$cap,
			'cff-top',
			'cff_settings_page'
		);

		add_submenu_page(
			'cff-top',
			__( 'Upgrade to Pro', 'custom-facebook-feed' ),
			__( '<span class="cff_get_pro">Try the Pro Demo</span>', 'custom-facebook-feed' ),
			$cap,
			'https://smashballoondemo.com/?utm_campaign=facebook-free&utm_source=menu-link&utm_medium=upgrade-link',
			''
		);

    	//Show a Instagram plugin menu item if it isn't already installed
		if( !is_plugin_active( 'instagram-feed/instagram-feed.php' ) && !is_plugin_active( 'instagram-feed-pro/instagram-feed.php' )  && current_user_can( 'activate_plugins' ) && current_user_can( 'install_plugins' ) ){
			add_submenu_page(
				'cff-top',
				__( 'Instagram Feed', 'custom-facebook-feed' ),
				'<span class="cff_get_sbi">' . __( 'Instagram Feed', 'custom-facebook-feed' ) . '</span>',
				$cap,
				'admin.php?page=cff-top&tab=more',
				''
			);
		}

	    //Show a Twitter plugin menu item if it isn't already installed
		if( !is_plugin_active( 'custom-twitter-feeds/custom-twitter-feed.php' ) && !is_plugin_active( 'custom-twitter-feeds-pro/custom-twitter-feed.php' )  && current_user_can( 'activate_plugins' ) && current_user_can( 'install_plugins' )  ){
			add_submenu_page(
				'cff-top',
				__( 'Twitter Feed', 'custom-facebook-feed' ),
				'<span class="cff_get_ctf">' . __( 'Twitter Feed', 'custom-facebook-feed' ) . '</span>',
				$cap,
				'admin.php?page=cff-top&tab=more',
				''
			);
		}

    	//Show a YouTube plugin menu item if it isn't already installed
		if( !is_plugin_active( 'feeds-for-youtube/youtube-feed.php' ) && !is_plugin_active( 'youtube-feed-pro/youtube-feed.php' ) && current_user_can( 'activate_plugins' ) && current_user_can( 'install_plugins' )  ){
			add_submenu_page(
				'cff-top',
				__( 'YouTube Feed', 'custom-facebook-feed' ),
				'<span class="cff_get_yt">' . __( 'YouTube Feed', 'custom-facebook-feed' ) . '</span>',
				$cap,
				'admin.php?page=cff-top&tab=more',
				''
			);
		}
	}

	/**
	 * Register Assets
	 *
	 * @since 2.19
	 */
	public function register_assets(){
		add_action( 'admin_enqueue_scripts' , array( $this, 'enqueue_styles_assets' ) );
		add_action( 'admin_enqueue_scripts' , array( $this, 'enqueue_scripts_assets' ) );
	}



	/**
	 * Enqueue & Register Styles
	 *
	 * @since 2.19
	 */
	public function enqueue_styles_assets(){
		wp_register_style(
			'custom_wp_admin_css',
			CFF_PLUGIN_URL . 'admin/assets/css/cff-admin-style.css',
			false,
			CFFVER
		);
        wp_enqueue_style( 'custom_wp_admin_css' );
        wp_enqueue_style(
        	'cff-font-awesome',
        	'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css',
        	array(),
        	'4.5.0'
        );
        wp_enqueue_style( 'wp-color-picker' );
	}


	/**
	 * Enqueue & Register Scripts
	 *
	 * @since 2.19
	 */
	public function enqueue_scripts_assets(){
	    //Declare color-picker as a dependency
	    wp_enqueue_script(
	    	'cff_admin_script',
	    	CFF_PLUGIN_URL . 'admin/assets/js/cff-admin-scripts.js',
	    	false,
	    	CFFVER
	    );

		wp_localize_script( 'cff_admin_script', 'cffA', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'cff_nonce' => wp_create_nonce( 'cff_nonce' )
			)
		);
		$strings = array(
			'addon_activate'                  => esc_html__( 'Activate', 'custom-facebook-feed' ),
			'addon_activated'                 => esc_html__( 'Activated', 'custom-facebook-feed' ),
			'addon_active'                    => esc_html__( 'Active', 'custom-facebook-feed' ),
			'addon_deactivate'                => esc_html__( 'Deactivate', 'custom-facebook-feed' ),
			'addon_inactive'                  => esc_html__( 'Inactive', 'custom-facebook-feed' ),
			'addon_install'                   => esc_html__( 'Install Addon', 'custom-facebook-feed' ),
			'addon_error'                     => esc_html__( 'Could not install addon. Please download from smashballoon.com and install manually.', 'custom-facebook-feed' ),
			'plugin_error'                    => esc_html__( 'Could not install a plugin. Please download from WordPress.org and install manually.', 'custom-facebook-feed' ),
			'addon_search'                    => esc_html__( 'Searching Addons', 'custom-facebook-feed' ),
			'ajax_url'                        => admin_url( 'admin-ajax.php' ),
			'cancel'                          => esc_html__( 'Cancel', 'custom-facebook-feed' ),
			'close'                           => esc_html__( 'Close', 'custom-facebook-feed' ),
			'nonce'                           => wp_create_nonce( 'cff-admin' ),
			'almost_done'                     => esc_html__( 'Almost Done', 'custom-facebook-feed' ),
			'oops'                            => esc_html__( 'Oops!', 'custom-facebook-feed' ),
			'ok'                              => esc_html__( 'OK', 'custom-facebook-feed' ),
			'plugin_install_activate_btn'     => esc_html__( 'Install and Activate', 'custom-facebook-feed' ),
			'plugin_install_activate_confirm' => esc_html__( 'needs to be installed and activated to import its forms. Would you like us to install and activate it for you?', 'custom-facebook-feed' ),
			'plugin_activate_btn'             => esc_html__( 'Activate', 'custom-facebook-feed' ),
		);
		$strings = apply_filters( 'cff_admin_strings', $strings );

		wp_localize_script(
			'cff_admin_script',
			'cff_admin',
			$strings
		);
	    if( !wp_script_is('jquery-ui-draggable') ) {
	        wp_enqueue_script(
	            array(
	            'jquery',
	            'jquery-ui-core',
	            'jquery-ui-draggable'
	            )
	        );
	    }
	    wp_enqueue_script(
	        array(
	        'hoverIntent',
	        'wp-color-picker'
	        )
	    );
	}





}
