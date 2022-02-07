<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class DLM_Widget_Manager {

	/**
	 * Setup the actions
	 */
	public function setup() {
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	/**
	 * Register the widgets
	 */
	public function register_widgets() {
		register_widget( 'DLM_Widget_Downloads' );
	}

}