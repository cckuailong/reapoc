<?php
namespace CustomFacebookFeed;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CFF_SiteHealth {

	/**
	 * Indicates if current integration is allowed to load.
	 *
	 * @since 1.5.5
	 *
	 * @return bool
	 */
	public function allow_load() {

		global $wp_version;

		return version_compare( $wp_version, '5.2', '>=' );
	}

	/**
	 * Loads an integration.
	 *
	 * @since 1.5.5
	 */
	public function load() {

		$this->hooks();
	}

	/**
	 * Integration hooks.
	 *
	 * @since 1.5.5
	 */
	protected function hooks() {
		add_filter( 'site_status_tests', array( $this, 'add_tests' ) );
	}

	/**
	 * Add MonsterInsights WP Site Health tests.
	 *
	 * @param array $tests The current filters array.
	 *
	 * @return array
	 */
	public function add_tests( $tests ) {
		$tests['direct']['cff_test_check_errors'] = array(
			'label' => __( 'Custom Facebook Feed Errors', 'custom-facebook-feed' ),
			'test'  => array( $this, 'test_check_errors' )
		);

		return $tests;
	}

	/**
	 * Checks if there are Instagram API Errors
	 */
	public function test_check_errors() {
		$result = array(
			'label'       => __( 'Custom Facebook Feed has no critical errors', 'custom-facebook-feed' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Custom Facebook Feed', 'custom-facebook-feed' ),
				'color' => 'blue',
			),
			'description' => __( 'No critical errors have been detected.', 'custom-facebook-feed' ),
			'test'        => 'cff_test_check_errors',
		);

		


		if ( \cff_main()->cff_error_reporter->are_critical_errors() ) {
			$link = admin_url( 'admin.php?page=cff-settings' );
			$result['status'] = 'critical';
			$result['label'] = __( 'Your Custom Facebook Feed is experiencing an error.', 'custom-facebook-feed' );
			$result['description'] = sprintf( __( 'A critical issue has been detected with your Custom Facebook Feed. Visit the %sCustom Facebook Feed settings page%s to fix the issue.', 'custom-facebook-feed' ), '<a href="' . esc_url( $link ) . '">', '</a>' );
		}


		return $result;
	}
}