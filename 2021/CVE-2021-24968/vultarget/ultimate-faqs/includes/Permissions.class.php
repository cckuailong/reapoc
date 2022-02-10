<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewdufaqPermissions' ) ) {
/**
 * Class to handle plugin permissions for Ultimate FAQs
 *
 * @since 2.0.0
 */
class ewdufaqPermissions {

	private $plugin_permissions;
	private $permission_level;

	public function __construct() {

		$this->plugin_permissions = array(
			'styling' 		=> 2,
			'premium' 		=> 2,
			'fields' 		=> 2,
			'search' 		=> 2,
			'woocommerce'	=> 2,
			'submit-faq'	=> 2,
			'ordering' 		=> 2,
			'labelling'		=> 2,
			'import' 		=> 2,
			'export' 		=> 2
		);
	}

	public function set_permissions() {
		global $ewd_otp_controller;

		if ( is_array( get_option( 'ewd-ufaq-permission-level' ) ) ) { return; }

		if ( ! empty( get_option( 'ewd-ufaq-permission-level' ) ) ) { 

			update_option( 'ewd-ufaq-permission-level', array( get_option( 'ewd-ufaq-permission-level' ) ) );

			return;
		}

		$this->permission_level = get_option( 'EWD_UFAQ_Full_Version' ) == 'Yes' ? 2 : 1;

		update_option( 'ewd-ufaq-permission-level', array( $this->permission_level ) );
	}

	public function get_permission_level() {

		if ( ! is_array( get_option( 'ewd-ufaq-permission-level' ) ) ) { $this->set_permissions(); }

		$permissions_array = get_option( 'ewd-ufaq-permission-level' );

		$this->permission_level = is_array( $permissions_array ) ? reset( $permissions_array ) : $permissions_array;
	}

	public function check_permission( $permission_type = '' ) {

		if ( ! $this->permission_level ) { $this->get_permission_level(); }
		
		return ( array_key_exists( $permission_type, $this->plugin_permissions ) ? ( $this->permission_level >= $this->plugin_permissions[$permission_type] ? true : false ) : false );
	}

	public function update_permissions() {

		$this->permission_level = get_option( 'ewd-ufaq-permission-level' );
	}
}

}