<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

final class CMPLZ_Forminator_Addon extends Forminator_Addon_Abstract {

	private static $_instance = null;
	/**
	 * Use this trait to mark this addon as PRO
	 */
	protected $_slug = 'complianz';
	protected $_version = cmplz_version;
	protected $_min_forminator_version = '1.1';
	protected $_short_title = 'Complianz';
	protected $_title = 'Complianz Privacy Suite';
	protected $_url = 'https://complianz.io';
	protected $_full_path = __FILE__;
	protected $_icon;
	protected $_icon_x2;
	protected $_image = '';
	protected $_image_x2 = '';

	public function __construct() {
		$this->_icon = cmplz_url . 'assets/images/icon-logo.svg';
		$this->_icon_x2 = cmplz_url . 'assets/images/icon-256x256.png';
		// late init to allow translation
		$this->_description
			= __( 'Integrate Forminator with Complianz Privacy Suite',
			"complianz-gdpr" );
		$this->_activation_error_message
			= __( 'Sorry but we failed to activate the Complianz integration',
			"complianz-gdpr" );
		$this->_deactivation_error_message
			= __( 'Sorry but we failed to deactivate the Complianz integration, please try again',
			"complianz-gdpr" );

		$this->_update_settings_error_message = __(
			'Sorry, we failed to update settings, please check your form and try again',
			"complianz-gdpr"
		);
	}

	/**
	 * @return self|null
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Flag for check if and addon connected (global settings suchs as api key complete)
	 *
	 * @return bool
	 */
	public function is_connected() {
		$fields = get_option( 'complianz_options_integrations' );
		if ( ! isset( $fields["forminator"] ) ) {
			return true;
		}

		if ( isset( $fields["forminator"] ) && $fields["forminator"] == 1 ) {
			return true;
		}

		return false;

	}

//    public function setup_connect() {
//        cmplz_update_option( 'integrations','forminator',1);
//    }

	/**
	 * Flag for check if and addon connected to a form(form settings suchs as list name completed)
	 *
	 * @param $form_id
	 *
	 * @return bool
	 */
	public function is_form_connected( $form_id ) {
		return false;
	}
}
