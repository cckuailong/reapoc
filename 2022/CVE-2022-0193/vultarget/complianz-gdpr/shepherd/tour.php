<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'you do not have access to this page!' );
}

class cmplz_tour {

	private static $_this;

	public $capability = 'activate_plugins';
	public $url;
	public $version;

	function __construct() {
		if ( isset( self::$_this ) ) {
			wp_die( sprintf( '%s is a singleton class and you cannot create a second instance.',
				get_class( $this ) ) );
		}

		self::$_this = $this;

		$this->url     = cmplz_url . 'shepherd';
		$this->version = cmplz_version;
		add_action( 'wp_ajax_cmplz_cancel_tour',
			array( $this, 'listen_for_cancel_tour' ) );
		add_action( 'admin_init', array( $this, 'restart_tour' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	static function this() {
		return self::$_this;
	}

	public function enqueue_assets( $hook ) {

		if ( get_site_option( 'cmplz_tour_started' ) ) {
			if ( $hook !== 'plugins.php'
			     && ( strpos( $hook, 'complianz' ) === false )
			     && strpos( $hook, 'cmplz' ) === false
			) {
				return;
			}

			wp_register_script( 'cmplz-tether', trailingslashit( $this->url ) . 'tether/tether.min.js', "", $this->version );
			wp_enqueue_script( 'cmplz-tether' );

			wp_register_script( 'cmplz-shepherd',
				trailingslashit( $this->url )
				. 'tether-shepherd/shepherd.min.js', "", $this->version );
			wp_enqueue_script( 'cmplz-shepherd' );

			wp_register_style( 'cmplz-shepherd', trailingslashit( $this->url ) . "css/shepherd-theme-arrows.min.css", "", $this->version );
			wp_enqueue_style( 'cmplz-shepherd' );

			wp_register_style( 'cmplz-shepherd-tour', trailingslashit( $this->url ) . "css/cmplz-tour.min.css", "", $this->version );
			wp_enqueue_style( 'cmplz-shepherd-tour' );

			wp_register_script( 'cmplz-shepherd-tour', trailingslashit( $this->url ) . 'js/cmplz-tour.js', array( 'jquery' ), $this->version );
			wp_enqueue_script( 'cmplz-shepherd-tour' );

			$logo  = '<span class="cmplz-tour-logo"><img class="cmplz-tour-logo" style="width: 70px; height: 70px;" src="' . cmplz_url . 'assets/images/icon-256x256.png"></span>';
			$html  = '<div class="cmplz-tour-logo-text">' . $logo . '<span class="cmplz-tour-text">{content}</span></div>';
			$steps = array(
				array(
					'title'  => __( 'Welcome to Complianz', 'complianz-gdpr' ),
					'text'   => __( "Get ready for privacy legislation around the world. Follow a quick tour or start configuring the plugin!", 'complianz-gdpr' ),
					'link'   => admin_url( "plugins.php" ),
					'attach' => '.cmplz-settings-link',
					'position' => 'right',
				),
				array(
					'title'  => __( 'Dashboard', 'complianz-gdpr' ),
					'text'   => __( "This is your Dashboard. When the Wizard is completed, this will give you an overview of tasks, tools, and documentation.", 'complianz-gdpr' ),
					'link'   => admin_url( "admin.php?page=complianz" ),
					'attach' => '.cmplz-progress .cmplz-grid-title',
					'position' => 'right',
				),
				array(
					'title'  => __( "The Wizard", "complianz-gdpr" ),
					'text'   => __( "This is where everything regarding cookies is configured. We will come back to the Wizard soon.", 'complianz-gdpr' ),
					'link'   => add_query_arg( array( "page" => "cmplz-wizard", "step" => STEP_COOKIES ), admin_url( "admin.php" ) ),
					'attach' => '.cookie_scan .cmplz-label',
					'position' => 'bottom',
				),
				array(
					'title'  => __( 'Cookie Banner', 'complianz-gdpr' ),
					'text'   => __( "Here you can configure and style your cookie banner if the Wizard is completed. An extra tab will be added with region-specific settings.", 'complianz-gdpr' ),
					'link'   => add_query_arg( array( 'page' => 'cmplz-cookiebanner', 'id'   => cmplz_get_default_banner_id() ), admin_url( "admin.php" ) ),
					'attach' => '#general.CMPLZ_COOKIEBANNER .cmplz-settings-title',
					'position' => 'bottom',
				),

				array(
					'title'  => __( "Integrations", "complianz-gdpr" ),
					'text'   => __( "Based on your answers in the Wizard, we will automatically enable integrations with relevant services and plugins. In case you want to block extra scripts, you can add them to the Script Center.", 'complianz-gdpr' ),
					'link'   => add_query_arg(array("page" => 'cmplz-script-center'), admin_url( "admin.php" ) ),
					'attach' => '#services .cmplz-settings-title',
					'position' => 'right',
				),
				array(
					'title'  => __( 'Proof of Consent', 'complianz-gdpr' ),
					'text'   => __( "Complianz tracks changes in your Cookie Notice and Cookie Policy with time-stamped documents. This is your consent registration while respecting the data minimization guidelines and won't store any user data.", 'complianz-gdpr' ),
					'link'   => add_query_arg(array("page" => 'cmplz-proof-of-consent'), admin_url( "admin.php" ) ),
					'attach' => 'input[name=cmplz_generate_snapshot]',
					'position' => 'top',
				),
				array(
					'title'  => __( "Let's start the Wizard", 'complianz-gdpr' ),
					'text'   => __( "You are ready to start the Wizard. For more information, FAQ, and support, please visit Complianz.io.", 'complianz-gdpr' ),
					'attach' => '.field-group.regions',
					'position' => 'bottom',
					'link'   => add_query_arg(array("page" => 'cmplz-wizard'), admin_url( "admin.php" ) ),
				),

			);


			$steps = apply_filters( 'cmplz_shepherd_steps', $steps );
			wp_localize_script( 'cmplz-shepherd-tour', 'cmplz_tour',
				array(
					'ajaxurl'        => admin_url( 'admin-ajax.php' ),
					'html'           => $html,
					'token'          => wp_create_nonce( 'cmplz_tour_nonce' ),
					'nextBtnText'    => __( "Next", "complianz-gdpr" ),
					'backBtnText'    => __( "Previous", "complianz-gdpr" ),
					'configure_text' => __( "Configure", "complianz-gdpr" ),
					'configure_link' => admin_url( "admin.php?page=cmplz-wizard" ),
					'startTour'      => __( "Start tour", "complianz-gdpr" ),
					'endTour'        => __( "End tour", "complianz-gdpr" ),
					'steps'          => $steps,
				) );

		}
	}

	/**
	 *
	 * @since 1.0
	 *
	 * When the tour is cancelled, a post will be sent. Listen for post and update tour cancelled option.
	 *
	 */

	public function listen_for_cancel_tour() {

		if ( ! isset( $_POST['token'] )
		     || ! wp_verify_nonce( $_POST['token'], 'cmplz_tour_nonce' )
		) {
			return;
		}
		update_site_option( 'cmplz_tour_started', false );
		update_site_option( 'cmplz_tour_shown_once', true );
	}


	public function restart_tour() {

		if ( ! isset( $_POST['cmplz_restart_tour'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_POST['cmplz_nonce'] )
		     || ! wp_verify_nonce( $_POST['cmplz_nonce'], 'complianz_save' )
		) {
			return;
		}

		update_site_option( 'cmplz_tour_started', true );

		wp_redirect( admin_url( 'plugins.php' ) );
		exit;
	}

}
