<?php
if ( ! defined( 'MYCRED_PURCHASE' ) ) exit;

/**
 * myCRED_buyCRED_Module class
 * @since 0.1
 * @version 1.4.1
 */
if ( ! class_exists( 'myCRED_buyCRED_Reward' ) ) :
	class myCRED_buyCRED_Reward {

		// Instnace
		protected static $_instance = NULL;

		/**
		 * Construct
		 */
		function __construct() {

			add_action( 'mycred_admin_enqueue',  array( $this, 'register_assets' ) );
			add_filter( 'mycred_setup_hooks',    array( $this, 'register_buycred_reward_hook' ), 10, 2 );
			add_action( 'mycred_load_hooks',     array( $this, 'load_buycred_reward_hook' ) );
			add_filter( 'mycred_all_references', array( $this, 'register_buycred_reward_refrence' ) );

		}

		/**
		 * Setup Instance
		 * @since 1.7
		 * @version 1.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Register Assets
		 * @since 1.8
		 * @version 1.0
		 */
		public function register_assets() {

			wp_enqueue_style( 'buycred-admin-style', plugins_url( 'assets/css/admin-style.css', MYCRED_PURCHASE ), array(), MYCRED_PURCHASE_VERSION, 'all' );
			wp_enqueue_script( 'buycred-admin-script', plugins_url( 'assets/js/admin-script.js', MYCRED_PURCHASE ), array( 'jquery' ), MYCRED_PURCHASE_VERSION, 'all' );

		}

		public function load_buycred_reward_hook() {
			require_once MYCRED_BUYCRED_INCLUDES_DIR . 'buycred-reward-hook.php';
		}

		public function register_buycred_reward_hook( $installed ) {

			$installed['buycred_reward'] = array(
				'title'       => __('Reward for Buying %plural%', 'mycred'),
				'description' => __('Adds a myCred hook for buyCred reward.', 'mycred'),
				'callback'    => array('myCRED_buyCRED_Reward_Hook')
			);

			return $installed;
		}


		public function register_buycred_reward_refrence( $list ) {

			$list['buycred_reward']  = __('Reward for buyCRED Purchase', 'mycred');
			return $list;
		}

	}
endif;

function mycred_buycred_reward_init() {
	return myCRED_buyCRED_Reward::instance();
}
mycred_buycred_reward_init();