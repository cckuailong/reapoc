<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'VILLATHEME_ADMIN_SHOW_MESSAGE' ) ) {
	class VILLATHEME_ADMIN_SHOW_MESSAGE {
		protected static $instance = null;

		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 1 );
		}

		public function admin_enqueue_scripts() {
			wp_enqueue_style( 'villatheme-admin-show-message', VI_WOO_ORDERS_TRACKING_CSS . 'show-message.css', '', VI_WOO_ORDERS_TRACKING_VERSION );
			wp_enqueue_script( 'villatheme-admin-show-message', VI_WOO_ORDERS_TRACKING_JS . 'show-message.js', array( 'jquery' ), VI_WOO_ORDERS_TRACKING_VERSION );
		}

		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}