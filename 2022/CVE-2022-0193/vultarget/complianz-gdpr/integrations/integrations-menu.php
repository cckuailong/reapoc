<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_action( 'cmplz_integrations_menu', 'cmplz_add_integrations_menu' );
function cmplz_add_integrations_menu() {
	add_submenu_page(
		'complianz',
		__( 'Integrations', 'complianz-gdpr' ),
		__( 'Integrations', 'complianz-gdpr' ),
		'manage_options',
		"cmplz-script-center",
		'cmplz_integrations_page'
	);
}


/**
 * Show the integrations page
 *
 */

function cmplz_integrations_page() {
	$grid_items = array(
			'services' => array(
					'page' => 'integrations',
					'name' => 'services',
					'header' => __('Services', 'complianz-gdpr'),
					'class' => 'big',
					'index' => '11',
					'controls' => '',
			),
			'plugins' => array(
					'page' => 'integrations',
					'name' => 'plugins',
					'header' => __('Plugins', 'complianz-gdpr'),
					'class' => 'big',
					'index' => '12',
					'controls' => '',
			),
			'custom-scripts' => array(
					'page' => 'integrations',
					'name' => 'custom-scripts',
					'header' => __('Script Center', 'complianz-gdpr'),
					'class' => 'big',

					'index' => '13',
					'controls' => '',
			),
	);

	echo cmplz_grid_container_settings(__( "Settings", 'complianz-gdpr' ), $grid_items);
}


/**
 * Handle saving of integrations services
 */

function process_integrations_services_save() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_POST['cmplz_save_integrations_type_plugins'] ) || isset($_POST["cmplz_save_integrations_type_services"]) ) {
		if ( ! isset( $_POST['cmplz_nonce'] ) || ! wp_verify_nonce( $_POST['cmplz_nonce'], 'complianz_save' ) ) {
			return;
		}

		if ( isset($_POST["cmplz_save_integrations_type_services"])    ) {

			$thirdparty_services = COMPLIANZ::$config->thirdparty_services;
			unset( $thirdparty_services['google-fonts'] );

			$active_services = cmplz_get_value( 'thirdparty_services_on_site' );
			foreach ( $thirdparty_services as $service => $label ) {
				if ( isset( $_POST[ 'cmplz_' . $service ] ) && $_POST[ 'cmplz_' . $service ] == 1 ) {
					$active_services[ $service ] = 1;
					$service_obj                 = new CMPLZ_SERVICE();
					$service_obj->add( $label, COMPLIANZ::$cookie_admin->get_supported_languages(), false, 'utility' );
				} else {
					$active_services[ $service ] = 0;
				}
			}

			cmplz_update_option( 'wizard', 'thirdparty_services_on_site', $active_services );
			$socialmedia        = COMPLIANZ::$config->thirdparty_socialmedia;
			$active_socialmedia = cmplz_get_value( 'socialmedia_on_site' );
			foreach ( $socialmedia as $service => $label ) {
				if ( isset( $_POST[ 'cmplz_' . $service ] )
				     && $_POST[ 'cmplz_' . $service ] == 1
				) {
					$active_socialmedia[ $service ] = 1;
					$service_obj                    = new CMPLZ_SERVICE();
					$service_obj->add( $label,
						COMPLIANZ::$cookie_admin->get_supported_languages(),
						false, 'social' );
				} else {
					$active_socialmedia[ $service ] = 0;
				}
			}

			cmplz_update_option( 'wizard', 'socialmedia_on_site', $active_socialmedia );

			if ( isset($_POST['cmplz_advertising']) & $_POST['cmplz_advertising'] == 1 ) {
				cmplz_update_option( 'wizard', 'uses_ad_cookies', 'yes' );
			} else {
				cmplz_update_option( 'wizard', 'uses_ad_cookies', 'no' );
			}
		}

		$disabled_placeholders = get_option( 'cmplz_disabled_placeholders', array() );

		foreach ( $_POST as $post_key => $value ) {
			if ( strpos( $post_key, 'cmplz_placeholder' ) !== false ) {
				$plugin = str_replace( array( 'cmplz_placeholder_' ), array( '' ), $post_key );

				if ( intval( $_POST[ $post_key ] ) == 1 ) {
					$key = array_search( $plugin, $disabled_placeholders );
					if ( $key !== false ) {
						unset( $disabled_placeholders[ $key ] );
					}
				} elseif ( intval( $_POST[ $post_key ] ) == 0 ) {
					if ( ! in_array( $plugin, $disabled_placeholders ) ) {
						$disabled_placeholders[] = $plugin;
					}
				}
			}
		}
		update_option( 'cmplz_disabled_placeholders', $disabled_placeholders );
	}
}

add_action( 'plugins_loaded', 'process_integrations_services_save' );
