<?php

namespace Aventura\Wprss\Core\Licensing;
use \Aventura\Wprss\Core\Licensing\License\Status;

/**
 * AJAX controller class for licensing AJAX operations.
 */
class AjaxController {

	// Pattern for ajax handler methods
	const AJAX_MANAGE_LICENSE_METHOD_PATTERN = 'handleAjaxLicense%s';

	protected $_manager;
	protected $_settings;

	/**
	 * Constructor.
	 */
	public function __construct() {
	}

    /**
     * Sets the settings controller to be used by this instance.
     *
     * @param \Aventura\Wprss\Core\Licensing\Settings $settings The settings controller.
     * @return \Aventura\Wprss\Core\Licensing\AjaxController This instance.
     */
    public function setSettingsController( Settings $settings ) {
        $this->_settings = $settings;
        return $this;
    }


    /**
     * Gets the settings controller used by this instance.
     *
     * @return \Aventura\Wprss\Core\Licensing\Settings The settings controller used by this instance.
     */
    public function getSettingsController() {
        return $this->_settings;
    }


    /**
     * Sets the license manager to be used by this instance.
     *
     * @param \Aventura\Wprss\Core\Licensing\Manager $manager The license manager to use.
     * @return \Aventura\Wprss\Core\Licensing\AjaxController This instance.
     */
    public function setManager(Manager $manager) {
        $this->_manager = $manager;
        return $this;
    }


    /**
     * Gets the license manager used by this instance.
     *
     * @return \Aventura\Wprss\Core\Licensing\Manager The lisence manager.
     */
    public function getManager() {
        return $this->_manager;
    }

	/**
	 * Echoes an AJAX error response along with activate/deactivate license button HTML markup and then dies.
	 *
	 * @param  string $message The error message to send.
	 * @param  string $addonId Optional addon ID related to the error, used for sending the activate/deactivate license button.
	 */
	protected function _sendErrorResponse( $message, $addonId = '' ) {
		$response = array(
			'error'		=>	$message,
			'html'		=>	$this->getSettingsController()->getActivateLicenseButtonHtml($addonId)
		);

		echo json_encode( $response );
		die();
	}

	/**
	 * Handles AJAX requests for managing licenses (activation, deactivation, etc..)
	 */
	public function handleAjaxManageLicense() {
		// Get data from request
		$nonce = empty( $_GET['nonce'] )? null : sanitize_text_field( $_GET['nonce'] );
		$addon = empty( $_GET['addon'] )? null : sanitize_text_field( $_GET['addon'] );
		$event = empty( $_GET['event'] )? null : sanitize_text_field( $_GET['event'] );
		$licenseKey = empty( $_GET['license'] )? null : sanitize_text_field( $_GET['license'] );

		// If no nonce, stop
		if ( $nonce === null ) $this->_sendErrorResponse( __( 'No nonce', 'wprss' ), $addon );
		// Generate the nonce id
		$nonce_id = sprintf( 'wprss_%s_license_nonce', $addon );
		// Verify the nonce. If verification fails, stop
		if ( ! wp_verify_nonce( $nonce, $nonce_id ) ) {
			$this->_sendErrorResponse( __( 'Bad nonce', 'wprss' ), $addon );
		}

		// Check addon, event and license
		if ( $addon === null ) $this->_sendErrorResponse( __( 'No addon ID', 'wprss' ) );
		if ( $event === null ) $this->_sendErrorResponse( __( 'No event specified', 'wprss' ), $addon );
		if ( $licenseKey === null ) $this->_sendErrorResponse( __( 'No license', 'wprss' ), $addon );

        $settings = $this->getSettingsController();
        $manager = $this->getManager();

		// Check if the license key was obfuscated on the client's end.
		if ( $settings->isLicenseKeyObfuscated( $licenseKey ) ) {
			// If so, use the stored license key since obfuscation signifies that the key was not modified
			// and is equal to the one saved in db
			$licenseKey = $manager->getLicense( $addon );
		} else {
			// Otherwise, update the value in db
			$license = $manager->getLicense( $addon );
			if ( $license === null ) {
				$license = $manager->createLicense( $addon );
			}
			$license->setKey( $licenseKey );
			$manager->saveLicenseKeys();
		}

		// uppercase first letter of event
		$event = ucfirst( $event );
		// Generate method name
		$eventMethod = sprintf( self::AJAX_MANAGE_LICENSE_METHOD_PATTERN, $event );
		// check if the event is handle-able
		if ( ! method_exists( $this, $eventMethod ) ) {
			$this->_sendErrorResponse( __( 'Invalid event specified', 'wprss' ), $addon);
		}

		// Call the appropriate handler method
		$returnValue = call_user_func_array( array( $this, $eventMethod ), array( $addon ) );

		// Prepare the response
		$partialResponse = array(
			'addon'				=>	$addon,
			'html'				=>	$settings->getActivateLicenseButtonHtml( $addon ),
			'licensedAddons'	=>	array_keys( $manager->getLicensesWithStatus( Status::VALID ) )
		);
		// Merge the returned value(s) from the handler method to generate the final resposne
		$response = array_merge( $partialResponse, $returnValue );

		// Return the JSON data.
		echo json_encode( $response );
		die();
	}

	/**
	 * Handles the AJAX request to fetch license information.
	 */
	public function handleAjaxFetchLicense() {
		// If not addon ID in the request, stop
		if ( empty( $_GET['addon']) )
			$this->_sendErrorResponse( __( 'No addon ID', 'wprss' ) );
		// Get and sanitize the addon ID
		$addon = sanitize_text_field( $_GET['addon'] );
		// Get the license information from EDD
		$response = $this->getManager()->checkLicense( $addon, 'ALL' );
		// Send response as JSON
		echo json_encode( $response );
		die();
	}

	/**
	 * Handles the activation AJAX request to activate the license for the given addon.
	 *
	 * @param  string $addonId The addon ID.
	 * @return array           The data to add to the AJAX response, containing the license status after activation.
	 */
	public function handleAjaxLicenseActivate( $addonId ) {
        $manager = $this->getManager();
		return array(
			'validity'	=>	$manager->normalizeLicenseApiStatus( $manager->activateLicense( $addonId ) )
		);
	}

	/**
	 * Handles the deactivation AJAX request to deactivate the license for the given addon.
	 *
	 * @param  string $addonId The addon ID.
	 * @return array           The data to add to the AJAX response, containing the license status after activation.
	 */
	public function handleAjaxLicenseDeactivate( $addonId ) {
        $manager = $this->getManager();
		return array(
			'validity'	=>	$manager->normalizeLicenseApiStatus( $manager->deactivateLicense( $addonId ) )
		);
	}

}
