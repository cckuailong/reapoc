<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DLM_Product_Error_Handler {

	/**
	 * @var DLM_Product_Error_Handler
	 */
	private static $instance = null;

	/**
	 * @var array<string>
	 */
	private $errors = array();
	
	/**
	 * Private constructor
	 */
	private function __construct() {
		$this->setup();
	}

	/**
	 * Singleton get method
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return DLM_Product_Error_Handler
	 */
	public static function get() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function setup() {
		$this->load();
		add_action( 'admin_notices', array( $this, 'display' ) );
		add_action( 'shutdown', array( $this, 'store' ) );
	}

	/**
	 * Add message to error array
	 *
	 * @param string $message
	 */
	public function add( $message ) {
		$this->errors[] = $message;
	}

	/**
	 * Load error messages
	 */
	public function load() {
		$this->errors = get_option( 'dlm_product_errors', array() );
	}

	/**
	 * Store error messags
	 */
	public function store() {
		update_option( 'dlm_product_errors', $this->errors );
	}

	/**
	 * Display errors
	 */
	public function display() {
		if ( ! empty( $this->errors ) ) {
			foreach ( $this->errors as $key => $error ) {
				?>
				<div class="error">
					<p><?php echo wp_kses_post( $error ); ?></p>
				</div>
				<?php
				// unset error
				unset( $this->errors[ $key ] );
			}
		}
	}


}