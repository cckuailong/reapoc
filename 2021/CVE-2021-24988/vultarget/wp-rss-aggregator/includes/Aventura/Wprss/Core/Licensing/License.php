<?php

namespace Aventura\Wprss\Core\Licensing;
use \Aventura\Wprss\Core\Licensing\License\Status;

/**
 * This class represents a single license object.
 *
 * IMPORTANT!
 * 	This version is still untested
 *
 * @version 1.0-alpha
 * @since 4.7.8
 */
class License {

	// Default values for license properties
	const KEY_DEFAULT			=	false;
	const STATUS_DEFAULT		=	Status::INVALID;
	const EXPIRY_DEFAULT		=	null;

	/**
	 * License key.
	 *
	 * @var string
	 */
	protected $_key;

	/**
	 * License status.
	 *
	 * @see Aventura\Wprss\Licensing\License\Status;
	 * @var string
	 */
	protected $_status;

	/**
	 * License expiry date.
	 *
	 * @var integer
	 */
	protected $_expiry;

    /** @var string Code of the add-on this license belongs to. */
    protected $_addonCode;

	/**
	 * Constructs a new instance, using the given params or an array of properties if only the first param is given.
	 *
	 * @param string  $key     The license key, or an array containing the license data. Default: array()
	 * @param string  $status  The license status. Default: null
	 * @param integer $expiry  The expiry date of this license. Default: null
	 * @see Aventura\Wprss\Licensing\License\Status
	 */
	public function __construct( $key = array(), $status = null, $expiry = null, $addonCode = null ) {
		// If first arg is an array,
		if ( is_array( $key ) ) {
			// Get values from the appropriate keys
			$data = array_merge( self::getDefaultSettings(), $key );
			$key = $data['key'];
			$status = $data['status'];
			$expiry = $data['expires'];
            $addonCode = $data['addon_code'];
		}

		$this
                // Set fields
                ->setKey( $key )
                ->setStatus( $status )
                ->setExpiry( $expiry )
                ->setAddonCode( $addonCode )
                // Call secondary constructor
                ->_construct();
	}

	/**
	 * Internal secondary constructor, for use when class is extended.
	 */
	protected function _construct() {}

	/**
	 * Gets the license key.
	 *
	 * @return string
	 */
	public function getKey() {
		return $this->_key;
	}

	/**
	 * Sets the license key.
	 *
	 * @param  string $key The license key.
	 * @return self
	 */
	public function setKey( $key ) {
		$this->_key = $this->_sanitizeKey( $key );
		return $this;
	}

    /**
     * Sanitizes a license key string.
     *
     * @since 4.11
     *
     * @param string $key
     * @return string
     */
    protected function _sanitizeKey($key) {
        return trim( $key );
    }

	/**
	 * Gets the license status.
	 *
	 * @see Aventura\Wprss\Licensing\License\Status
	 * @return string
	 */
	public function getStatus() {
		return $this->_status;
	}

	/**
	 * Sets the license status.
	 *
	 * @see Aventura\Wprss\Licensing\License\Status
	 *
	 * @param  string $status The license status.
	 * @return self
	 */
	public function setStatus( $status ) {
		$this->_status = $status;
		return $this;
	}

	/**
	 * Gets the license expiry date.
	 *
	 * @return integer
	 */
	public function getExpiry() {
		return $this->_expiry;
	}

	/**
	 * Sets the license expiry date.
	 *
	 * @param integer $expiry The license expiry date
	 */
	public function setExpiry( $expiry ) {
		$this->_expiry = $expiry;
		return $this;
	}

    /**
     * Set the code of the add-on that this license belongs to.
     *
     * @param string $code Code of the addon that this license belongs to.
     * @return \Aventura\Wprss\Core\Licensing\License This instance.
     */
    public function setAddonCode($code) {
        $this->_addonCode = $code;
        return $this;
    }

    /**
     * Get the code of the add-on that this license belongs to.
     *
     * @return string Code of the addon that this license belongs to.
     */
    public function getAddonCode() {
        return $this->_addonCode;
    }

    /**
     * Checks if the license status is valid.
     * 
     * @return boolean True if the status is valid, false otherwise.
     */
    public function isValid() {
    	return $this->getStatus() === Status::VALID;
    }

    /**
     * Checks if the license is invalid.
     * 
     * @return boolean True if the license is invalid, false if otherwise.
     */
    public function isInvalid() {
    	return $this->getStatus() === Status::INVALID;
    }

    /**
     * Checks if the license is expired.
     * 
     * @return boolean True if the license is expired, false if otherwise.
     */
    public function isExpired() {
    	return $this->getStatus() === Status::EXPIRED;
    }

    /**
     * Alias method for License::isValid(). Checks if the license is active.
     *
     * @uses Aventura\Wprss\Core\Licensing\License::isValid()
     * @return boolean True if the license is active, false if otherwise.
     */
    public function isActive() {
    	return $this->isValid();
    }

    /**
     * Checks if the license is inactive.
     * 
     * @return boolean True if the license is inactive, false if otherwise.
     */
    public function isInactive() {
    	return $this->getStatus() === Status::INACTIVE || $this->getStatus() === Status::SITE_INACTIVE;
    }

	/**
	 * Gets the default values for all properties of the license.
	 *
	 * @return array
	 */
	public static function getDefaultSettings() {
		return array(
			'key'		=>	'',
			'status'	=>	Status::INVALID,
			'expires'	=>	null,
            'addon_code'=>  null,
		);
	}

}
