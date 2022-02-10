<?php

namespace Aventura\Wprss\Core\Licensing;

use Aventura\Wprss\Core\Licensing\Api\RequestException;
use Aventura\Wprss\Core\Licensing\Api\ResponseException;
use Aventura\Wprss\Core\Licensing\License\Status;
use Aventura\Wprss\Core\Licensing\Plugin\UpdaterException;

/**
 * Manager class for license handling.
 *
 * @version 1.0
 * @since 4.7.8
 */
class Manager {

    // The default updater class
    const DEFAULT_UPDATER_CLASS = '\\Aventura\\Wprss\\Core\\Licensing\\Plugin\\Updater';

    // Name of license keys option in DB
    const DB_LICENSE_KEYS_OPTION_NAME = 'wprss_settings_license_keys';
    // Name of license statuses option in DB
    const DB_LICENSE_STATUSES_OPTION_NAME = 'wprss_settings_license_statuses';
    // Regex pattern for keys in license option
    const DB_LICENSE_KEYS_OPTION_PATTERN = '%s_license_key';
    // Regex pattern for statuses in license option
    const DB_LICENSE_STATUSES_OPTION_PATTERN = '%s_license_%s';

    // Lifetime expiration value
    const EXPIRATION_LIFETIME = 'lifetime';
    // Addon ID suffix for the lifetime-variant licenses
    const LIFETIME_ADDON_ID_SUFFIX = '_lf';
    // The EDD SL item name pattern for lifetime variants
    const LIFETIME_ITEM_NAME_PATTERN = '%s (Lifetime)';

    /**
     * License instance.
     *
     * @var array
     */
    protected $_licenses;

    /**
     * The class to use for updating addons.
     *
     * @var string
     */
    protected $_updaterClass;

    /**
     * The updater instances for the addons.
     *
     * @var array
     */
    protected $_updaterInstances;

    protected $_licenseKeysOptionName;
    protected $_licenseStatusesOptionName;
    protected $_expirationNoticePeriod;
    protected $_defaultAuthorName;

    /**
     * Constructor.
     */
    public function __construct() {
        // Reset the updater instances
        $this->_updaterInstances = array();
        $this->_loadLicenses();
        $this->_construct();
    }

    /**
     * Internal secondary constructor, used by classes that extend this class.
     */
    protected function _construct() {}

    /**
     * Gets the name of the class used to update the addons.
     *
     * @return string
     */
    public function getUpdaterClass() {
        if ( is_null($this->_updaterClass) ) {
            return self::DEFAULT_UPDATER_CLASS;
        }

        return $this->_updaterClass;
    }

    /**
     * Sets the class used to update the addons.
     *
     * @param  string $updaterClass The name of the updater class.
     * @return self
     */
    public function setUpdaterClass( $updaterClass ) {
        $this->_updaterClass = $updaterClass;
        return self;
    }


    /**
     * Get the period before license expiration, for which a notice should be displayed.
     *
     * @return int|string An integer, representing the number of secods before license expiry.
     *  or a string, representing a time offset that can be used with strtotime().
     */
    public function getExpirationNoticePeriod() {
        $period = $this->_expirationNoticePeriod;
        if ( is_numeric( $period ) ) {
            $period = intval( $period );
        }

        return $period;
    }


    /**
     * Set the period before license expiration, for which a notice should be displayed.
     *
     * @param int|string $period An integer, representing the number of secods before license expiry.
     * @return \Aventura\Wprss\Core\Licensing\Manager This instance.
     */
    public function setExpirationNoticePeriod( $period ) {
        $this->_expirationNoticePeriod = trim($period);
        return $this;
    }


    /**
     * Gets the name of the plugin author that is used by default.
     *
     * @return string Nae of the plugin author that is sent to EDD API by default.
     */
    public function getDefaultAuthorName() {
        return $this->_defaultAuthorName;
    }


    /**
     * Sets the name of the plugin author that will be used by default.
     *
     * @param string $name Name of the plugin author that will be sent to EDD API by default.
     * @return \Aventura\Wprss\Core\Licensing\Manager This instance.
     */
    public function setDefaultAuthorName($name) {
        $this->_defaultAuthorName = $name;
        return $this;
    }


    /**
     * Gets a list of registered addons.
     *
     * @return array An assoc array containing addon IDs as array keys and the addon names as array values.
     */
    public function getAddons() {
        return wprss_get_addons();
    }


    /**
     * Gets all of the updater instances.
     *
     * Alias for self#getUpdaterInstance()
     *
     * @see self::getUpdaterInstance
     * @uses self::getUpdaterInstance
     * @return array
     */
    public function getUpdaterInstances() {
        return $this->getUpdaterInstance();
    }


    /**
     * Gets the updater instance for a single addon, or all the updater instances if no param or null is passed.#
     *
     * @param  mixed $addonId The addon ID for which to return the updater instance, or null to return all instances. Default: null
     * @return mixed          An instance of \Aventura\Wprss\Core\Licensing\Plugin\UpdaterInterface if an addon ID is given, or an array of instances is null is given.
     */
    public function getUpdaterInstance( $addonId = null ) {
        return is_null($addonId)
                ? $this->_updaterInstances
                : $this->_getUpdaterInstance($addonId);
    }


    /**
     * Internally used to get a single updater instance for a particular addon.
     *
     * @param  string $addonId The addon ID.
     * @return \Aventura\Wprss\Core\Licensing\Plugin\UpdaterInterface
     */
    protected function _getUpdaterInstance( $addonId ) {
        return $this->hasUpdaterInsantance($addonId)
                ? $this->_updaterInstances[$addonId]
                : null;
    }


    /**
     * Checks if an updater instance exists for a specific addon.
     *
     * @param  string  $addonId The addon ID.
     * @return boolean          True if the updater instance exists, false if not.
     */
    public function hasUpdaterInsantance($addonId) {
        return isset($this->_updaterInstances[$addonId]);
    }


    /**
     * Sets the updater instance for an addon.
     *
     * @param string $addonId  The addon ID.
     * @param \Aventura\Wprss\Core\Licensing\Plugin\UpdaterInterface The updater instance.
     */
    protected function _setUpdaterInstance($addonId, $instance) {
        $this->_updaterInstances[ $addonId ] = $instance;
        return $this;
    }


    /**
     * Creates a new license.
     *
     * This does not save the license to the database. You will need to call Manager::saveLicenses() to save to db.
     *
     * @param  string  $addonId The addon ID
     * @return License          The created license
     */
    public function createLicense( $addonId ) {
        return $this->_licenses[ $addonId ] = new License( array('addon_code' => $addonId) );
    }


    /**
     * Gets all license key settings.
     *
     * @return array
     */
    public function getLicenses() {
        return $this->_licenses;
    }


    /**
     * Gets the license key for a specific addon.
     *
     * @param  string $addonId The addon id.
     * @return License
     */
    public function getLicense( $addonId ) {
        if ( ! $this->licenseExists( $addonId ) ) {
            return null;
        }
        return $this->_licenses[ $addonId ];
    }


    /**
     * Checks if an addon license key
     *
     * @param  string  $addonId The addon id
     * @return boolean          True if the license key for the given addon id exists, false if not.
     */
    public function licenseExists( $addonId ) {
        return isset( $this->_licenses[ $addonId ] );
    }


    /**
     * Gets all licenses with the given status.
     *
     * @param  mixed  $status    The status to search for, or an array of statuses.
     * @param  boolean $negation If true, the method will search for licenses that do NOT have the given status.
     *                           If false, the method will search for licenses with the given status.
     *                           Default: false
     * @return array             An array of matching licenses. Array keys contain the addon IDs and array values contain the license objects.
     */
    public function getLicensesWithStatus( $status, $negation = false ) {
        $licenses = array();
        foreach ( $this->_licenses as $_addonId => $_license ) {
            $_isStatus = is_array($status)
                    ? in_array($_license->getStatus())
                    : $_license->getStatus() === $status;
            if ( $_isStatus xor $negation === true ) {
                $licenses[ $_addonId ] = $_license;
            }
        }
        return $licenses;
    }


    /**
     * Checks if a license with the given status exists, stopping at the first match.
     *
     * @param  mixed  $status    The status to search for, or an array of statuses.
     * @param  boolean $negation If true, the method will search for licenses that do NOT have the given status.
     *                           If false, the method will search for licenses with the given status.
     *                           Default: false
     * @return boolean           True if a license with or without (depending on $negation) the given status exists, false otherwise.
     */
    public function licenseWithStatusExists( $status, $negation = false ) {
        return count( $this->getLicensesWithStatus( $status, $negation ) ) > 0;
    }


    /**
     * Gets inactive licenses.
     *
     * @uses self::getLicensesWithStatus
     * @return array
     */
    public function getInactiveLicenses() {
        $licenses = array();
        foreach ( $this->_licenses as $_addonId => $_license ) {
            if ($_license->isInactive()) {
                $licenses[ $_addonId ] = $_license;
            }
        }
        return $licenses;
    }


    /**
     * Gets the licenses that are soon to be expired.
     *
     * @uses self::_calculateSteTimestamp To calculate the minimum date for classifying licenses as "soon-to-expire".
     *
     * @return array An assoc array with addon IDs as array keys and License instances as array values.
     */
    public function getExpiringLicenses() {
        // Calculate soon-to-expiry (ste) date
        $ste = $this->getSteTimestamp();
        // Prepare the list
        $expiringLicences = array();
        // Iterate all licenses
        foreach ( $this->_licenses as $_addonId => $_license ) {
            if ($this->isLicenseExpiring($_addonId)) {
                $expiringLicences[ $_addonId ] = $_license;
            }
        }
        return $expiringLicences;
    }


    /**
     * Checks if a license is about to expire, according to the expiration period.
     *
     * @param  string  $addonId The ID of the addon whose license is to be checked for expiry.
     * @return boolean          True if the addon's license is about to expire, false if the addon license does not exist or is not about to expire.
     */
    public function isLicenseExpiring( $addonId ) {
        if (!$this->licenseExists($addonId)) {
            return false;
        }
        $license = $this->getLicense($addonId);
        // Get expiry
        $expires = $license->getExpiry();
        if ($expires === static::EXPIRATION_LIFETIME) {
            return false;
        }
        // Split using space and get first part only (date only)
        $parts = explode( ' ', $expires );
        $dateOnly = strtotime( $parts[0] );
        // Check if the expiry date is zero, or is within the expiration notice period
        return $dateOnly == 0 || $dateOnly < $this->getSteTimestamp();
    }


    /**
     * Checks if there are licenses that will soon expire.
     *
     * @uses self::_calculateSteTimestamp To calculate the minimum date for classifying licenses as "soon-to-expire".
     *
     * @return bool True if there are licenses that will soon expire, false otherwise.
     */
    public function expiringLicensesExist() {
        return count( $this->getExpiringLicenses() ) > 0;
    }


    /**
     * Activates an add-on's license.
     *
     * @uses self::sendDualApiRequest() Sends the request with $action set as 'activate_license'.
     *
     * @param  string $addonId The ID of the addon.
     * @param  string $return  What to return from the response.
     * @return mixed
     */
    public function activateLicense( $addonId, $return = 'license') {
        return $this->sendDualApiRequest( $addonId, 'activate_license', $return );
    }


    /**
     * Deactivates an add-on's license.
     *
     * @uses self::sendDualApiRequest() Sends the request with $action set as 'deactivate_license'.
     *
     * @param  string $addonId The ID of the addon.
     * @param  string $return  What to return from the response.
     * @return mixed
     */
    public function deactivateLicense( $addonId, $return = 'license') {
        return $this->sendDualApiRequest( $addonId, 'deactivate_license', $return );
    }


    /**
     * Checks an add-on's license's status with the server.
     *
     * @uses self::sendDualApiRequest() Sends the request with $action set as 'check_license'.
     *
     * @param  string $addonId The ID of the addon.
     * @param  string $return  What to return from the response.
     * @return mixed
     */
    public function checkLicense( $addonId, $return = 'license') {
        return $this->sendDualApiRequest( $addonId, 'check_license', $return );
    }

    /**
     * Calls the EDD Software Licensing API for a specified addon, falling back to the lifetime variant if required.
     *
     * @uses self::sendApiRequest To send the individual requests.
     *
     * @param string $addonId The ID of the addon
     * @param string $action  The action to perform on the license.
     * @param string $return  What to return from the response. If 'ALL', the entire license status object is returned,
     *                        Otherwise, the property with name $return will be returned, or null if it doesn't exist.
     * @return mixed
     */
    public function sendDualApiRequest( $addonId, $action = 'check_license', $return = 'license' ) {
        $data   = $this->sendApiRequest( $addonId, $action, $return );
        $status = is_object( $data ) ? $data->license : $data;

        return ( in_array($status, array('invalid', 'item_name_mismatch', 'failed')) )
            ? $this->sendApiRequest( $addonId . static::LIFETIME_ADDON_ID_SUFFIX, $action, $return )
            : $data;
    }

    /**
     * Calls the EDD Software Licensing API to perform licensing tasks on the addon's store server.
     *
     * @param string $addonId The ID of the addon
     * @param string $action  The action to perform on the license.
     * @param string $return  What to return from the response. If 'ALL', the entire license status object is returned,
     *                        Otherwise, the property with name $return will be returned, or null if it doesn't exist.
     * @return mixed
     */
    public function sendApiRequest( $addonId, $action = 'check_license', $return = 'license' ) {
        $lfSuffix = static::LIFETIME_ADDON_ID_SUFFIX;
        $lfSuffixLen = strlen($lfSuffix);

        // Check if a lifetime license (ends with lifetime suffix)
        $isLifetime = (substr($addonId, -$lfSuffixLen) === $lfSuffix);
        // Get the "real" addon ID (without the lifetime suffix)
        $addonRid = $isLifetime
            ? substr($addonId, 0, -$lfSuffixLen)
            : $addonId;

        // Get the license for the addon
        $license = $this->getLicense( $addonRid );
        // Use blank license if addon license does not exist
        if ( $license === null ) {
            $license = new License();
        }

        // Uppercase the addon ID
        $addonUid = strtoupper( $addonRid );

        // Prepare constants names
        $itemNameConstant = sprintf( 'WPRSS_%s_SL_ITEM_NAME', $addonUid );

        // Check for existence of constants
        if ( !defined($itemNameConstant) ) {
            return null;
        }

        // Get constant values
        $itemName = constant( $itemNameConstant );
        // Correct item name for lifetime variants
        $itemName = ($isLifetime)
            ? sprintf(static::LIFETIME_ITEM_NAME_PATTERN, $itemName)
            : $itemName;

        $requestData = [
            'edd_action' => $action,
            'license'    => $license,
            'item_name'  => $itemName,
        ];

        $storeUrl = apply_filters('wpra/licensing/store_url', WPRSS_SL_STORE_URL, $addonId, $action, $license);
        $requestData = apply_filters('wpra/licensing/request', $requestData, $addonId, $action, $license);

        try {
            $licenseData = $this->api($storeUrl, $requestData);
        }
        catch ( RequestException $e ) {
            wprss_log( sprintf( 'Could not retrieve licensing data from "%1$s": %2$s', $storeUrl, $e->getMessage() ), __FUNCTION__, WPRSS_LOG_LEVEL_WARNING );
            return $license->getStatus();
        }
        catch ( ResponseException $e ) {
            wprss_log( sprintf( 'Received invalid licensing data from "%1$s": %2$s', $storeUrl, $e->getMessage() ), __FUNCTION__, WPRSS_LOG_LEVEL_WARNING );
            return $license->getStatus();
        }

        // Update the DB option
        $license->setStatus( $licenseData->license );
        if (isset($licenseData->expires)) {
            $license->setExpiry( $licenseData->expires );
        }
        $this->saveLicenseStatuses();

        // Return the data
        if ( strtoupper( $return ) === 'ALL' ) {
            return $licenseData;
        } else {
            return isset( $licenseData->{$return} ) ? $licenseData->{$return} : null;
        }
    }


    /**
     * Low-level method for sending API requests to an EDD license server.
     *
     * Will normalize parameters for the request as needed, and return the decoded response.
     *
     * @param string $storeUrl The URL, to which to send the request. The EDD API endpoint.
     * @param array $params Params of the request. 'license', 'edd_action' and
     *  'item_name' are required for a successful call. 'license' can also be
     *  an instance of {@link Aventura\Wprss\Core\Licensing\License}.
     * @return object License data as properties of an stdClass instance.
     * @throws RequestException If request fails or a response is not received.
     * @throws ResponseException If the response is empty or cannot be decoded.
     */
    public function api($storeUrl, $params) {
        $defaultParams = array(
            'edd_action'            => null,
            'license'               => null,
            'item_name'             => null,
            'url'                   => network_home_url(),
            'time'                  => time(),
        );

        // Prepare params
        $params = array_merge($defaultParams, $params);
        array_walk($params, array($this, 'sanitizeApiParams'));
        if ( $params['license'] instanceof License ) $params['license'] = $params['license']->getKey();
        if ( $params['license'] ) $params['license'] = sanitize_text_field ( $params['license']);
        if ( $params['item_name'] ) $params['item_name'] = sanitize_text_field ( $params['item_name']);

        // Send the request to the API
        $response = wp_remote_post( add_query_arg( $params, $storeUrl ) );

        // Request failed
        if ( is_wp_error( $response ) ) {
            throw new RequestException( $response->get_error_message() );
        }

        $body = wp_remote_retrieve_body( $response );
        if ( empty( $body ) ) {
            throw new ResponseException( 'Response body is empty' );
        }

        if ( ($licenseData = json_decode( $body )) === null ) {
            throw new ResponseException( sprintf( 'Response body could not be decoded: %1$s', $body ) );
        }

        return $licenseData;
    }


    /**
     * Sanitizes the API params, prior to sending them.
     * 
     * @param   mixed $value The param value
     * @param  string $key   The param ID
     * @return  mixed        The sanitized param value.
     */
    public function sanitizeApiParams( &$value, $key ) {
        $value = is_string($value)? urlencode($value) : $value;
    }


    /**
     * Creates an updater instance for an addon.
     *
     * @param  string $id       The ID of the addon.
     * @param  string $itemName The name of the addon as registered in EDD on our servers.
     * @param  string $version  The current version of the addon.
     * @param  string $path     The path to the addon's main file.
     * @param  string $storeUrl The URL of the server that handles the licensing and serves the updates.
     * @return boolean True if the updater was initialized, false on failure due to an invalid license.
     */
    public function initUpdaterInstance($id, $itemName, $version, $path, $storeUrl = WPRSS_SL_STORE_URL) {
        // Generate the lifetime-variant item ID and name
        $lfId = sprintf('%s%s', $id, static::LIFETIME_ADDON_ID_SUFFIX);
        $lfItemName = sprintf('%s%s', $itemName, static::LIFETIME_ADDON_ID_SUFFIX);

        // Prepare the data
        $license = $this->getLicense( $id );
        // If the addon doesn't have a license or the license is not valid, do not set the updater.
        // Returns false to indicate this failure.
        if ( $license === null || $license->getStatus() !== Status::VALID ) {
            return false;
        }

        try {
            // Create an updater
            $updater = $this->newUpdater($storeUrl, $path, array(
                'version'   =>	$version,				// current version number
                'license'   =>	$license,				// license key (used get_option above to retrieve from DB)
                'item_name' =>	$itemName,				// name of this plugin
            ));

            // Create an updater for the lifetime-variant of the license
            $lfUpdater = $this->newUpdater($storeUrl, $path, array(
                'version'   =>	$version,
                'license'   =>	$license,
                'item_name' =>	$lfItemName,
            ));

            // Register the updater
            $this->_setUpdaterInstance($id, $updater);
            // Register the updater for the lifetime variant
            $this->_setUpdaterInstance($lfId, $lfUpdater);

            // Return true to indicate success
            return true;
        } catch ( UpdaterException $e ) {
            wprss_log( sprintf( 'Could not create new updater:: %1$s', $e->getMessage() ), __FUNCTION__, WPRSS_LOG_LEVEL_WARNING );
            return false;
        }
    }


    /**
     * Creates a new instance of the updater class, as configured.
     *
     * Guaranteed to return an updater.
     *
     * @see getUpdaterClass()
     * @see Plugin\UpdaterInterface::__construct();
     * @param string $url Endpoint URL.
     * @param string $path Plugin file.
     * @param array $params Params to requests.
     * @return \Aventura\Wprss\Core\Licensing\Plugin\UpdaterInterface
     * @throws \Aventura\Wprss\Core\Licensing\Plugin\Updater\InstanceException If the updater instance class is not a valid updater class.
     */
    public function newUpdater($url, $path, $params = array()) {
        // Get the updater class
        $updaterClass = $this->getUpdaterClass();

        $defaultParams = array(
            'author'            => $this->getDefaultAuthorName(),
            'license'           => null,
        );
        $params = array_merge($defaultParams, $params);
        if ( $params['license'] instanceof License ) $params['license'] = $params['license']->getKey();

        $updater = new $updaterClass($url, $path, $params);
        if ( !($updater instanceof Plugin\UpdaterInterface) ) {
            throw new UpdaterException(sprintf('Could not create updater instance: class "%1$s" is not a valid updater', get_class($updater)));
        }

        return $updater;
    }


    /**
     * Normalizes the license status received by the API into the license statuses that we use locally in our code.
     *
     * @param  string $status The status to normalize.
     * @return string         The normalized status.
     */
    public function normalizeLicenseApiStatus( $status ) {
        if ( $status === 'site_inactive' ) $status = 'inactive';
        if ( $status === 'item_name_mismatch' ) $status = 'invalid';
        return $status;
    }


    /**
     * Loads the licenses from db and prepares the internal licenses array
     */
    protected function _loadLicenses() {
        $this->_licenses = array();
        $options = self::_normalizeLicenseOptions( $this->getLicenseKeysDbOption(), $this->getLicenseStatusesDbOption() );
        foreach ( $options as $addonId => $_data ) {
            $this->_licenses[ $addonId ] = new License( $_data );
        }

        return $this;
    }


    /**
     * Saves the licenses and their statuses to the db.
     */
    public function saveLicenses() {
        $this->saveLicenseKeys();
        $this->saveLicenseStatuses();

        return $this;
    }


    /**
     * Saves the license keys to the db.
     */
    public function saveLicenseKeys() {
        $keys = array();
        foreach ( $this->_licenses as $_addonId => $_license ) {
            $_key = sprintf( self::DB_LICENSE_KEYS_OPTION_PATTERN, $_addonId );
            $keys[ $_key ] = $_license->getKey();
        }
        static::_updateOption( self::DB_LICENSE_KEYS_OPTION_NAME, $keys );

        return $this;
    }


    /**
     * Saves the license statuses (and expirations) to the db.
     */
    public function saveLicenseStatuses() {
        $statuses = array();
        foreach ( $this->_licenses as $_addonId => $_license ) {
            $_status = sprintf( self::DB_LICENSE_STATUSES_OPTION_PATTERN, $_addonId, 'status' );
            $_expires = sprintf( self::DB_LICENSE_STATUSES_OPTION_PATTERN, $_addonId, 'expires' );
            $statuses[ $_status ] = $_license->getStatus();
            $statuses[ $_expires ] = $_license->getExpiry();
        }
        static::_updateOption( self::DB_LICENSE_STATUSES_OPTION_NAME, $statuses );

        return $this;
    }


    /**
     * Retrieves the licenses keys db option.
     *
     * @return array
     */
    public function getLicenseKeysDbOption() {
        return self::_getOption( $this->getLicenseKeysOptionName(), array() );
    }


    /**
     * Retrieves the licenses statuses db option.
     *
     * @return array
     */
    public function getLicenseStatusesDbOption() {
        return self::_getOption( $this->getLicenseStatusesOptionName(), array() );
    }


    /**
     * @param string $optionName The name of the option where license keys should be stored.
     * @return \Aventura\Wprss\Core\Licensing\Manager This instance.
     */
    public function setLicenseKeysOptionName($optionName) {
        $this->_licenseKeysOptionName = $optionName;
        return $this;
    }


    /**
     * @return string The name of the option where license keys are stored.
     */
    public function getLicenseKeysOptionName() {
        if ( is_null( $this->_licenseKeysOptionName ) ) {
            return self::DB_LICENSE_KEYS_OPTION_NAME;
        }

        return $this->_licenseKeysOptionName;
    }


    /**
     * @param string $optionName The name of the option where license statuses should be stored.
     * @return \Aventura\Wprss\Core\Licensing\Manager This instance.
     */
    public function setLicenseStatusesOptionName($optionName) {
        $this->_licenseStatusesOptionName = $optionName;
        return $this;
    }


    /**
     * @return string The name of the option where license statuses are stored.
     */
    public function getLicenseStatusesOptionName() {
        if ( is_null( $this->_licenseStatusesOptionName ) ) {
            return self::DB_LICENSE_STATUSES_OPTION_NAME;
        }

        return $this->_licenseStatusesOptionName;
    }

    /**
     * Retrieves the value for an option from the database.
     *
     * @since 4.17.5
     *
     * @param string $name The name of the option to retrieve.
     * @param mixed $default Optional default value if the option does not exist.
     *
     * @return mixed The value for hte option, or the given $default if the option does not exist.
     */
    protected static function _getOption($name, $default = null) {
        return static::_performOnMainSite(function () use ($name, $default) {
            return get_option($name, $default);
        });
    }

    /**
     * Updates an option in the database.
     *
     * @since 4.17.5
     *
     * @param string $name The name of the option.
     * @param mixed $value The value to set to the option.
     *
     * @return mixed True on success, false on failure.
     */
    protected static function _updateOption($name, $value) {
        return static::_performOnMainSite(function () use ($name, $value) {
            return update_option($name, $value);
        });
    }

    /**
     * Runs a function on the main site of a WordPress multi site installation.
     *
     * If the current installation is not a multi-site, the function will run normally.
     *
     * @since 4.17.5
     *
     * @param callable $function The function to run on the main site.
     *
     * @return mixed The return value of the function.
     */
    protected static function _performOnMainSite(callable $function) {
        $mustSwitch = !is_main_site();

        if ($mustSwitch) {
            switch_to_blog(get_main_site_id());
        }

        $value = $function();

        if ($mustSwitch) {
            restore_current_blog();
        }

        return $value;
    }


    /**
     * Normalizes the given db options into a format that the Manager can use to compile a list of License instances.
     *
     * @return array
     */
    protected static function _normalizeLicenseOptions( $keys, $statuses ) {
        // Prepare result array
        $normalized = array();
        // Prepare regex pattern outside of iterations
        $licenseKeysOptionPattern = self::_formatStringToDbOptionPattern( self::DB_LICENSE_KEYS_OPTION_PATTERN );
        $licenseStatusesOptionPattern = self::_formatStringToDbOptionPattern( self::DB_LICENSE_STATUSES_OPTION_PATTERN );

        // Prepare the license keys into the normalized array
        foreach ( $keys as $_key => $_value ) {
            // Regex match for pattern of array keys
            preg_match( $licenseKeysOptionPattern, $_key, $_matches );
            if ( count( $_matches ) < 2 ) continue;
            // Addon id is the first match (excluding whole string match at $_matches[0])
            $_addonId = $_matches[1];
            // check if entry for add-on exists in normalized array, otherwise create it
            if ( ! isset( $normalized[ $_addonId ] ) )
                $normalized[ $_addonId ] = array();
            // Insert the license key inot the normalized array
            $normalized[ $_addonId ]['key'] = $_value;
            $normalized[ $_addonId ]['addon_code'] = $_addonId;
        }
        // Now iterate and insert the statuses
        foreach ( $statuses as $_key => $_value ) {
            // Regex match for pattern of array keys
            preg_match( $licenseStatusesOptionPattern, $_key, $_matches );
            // continue to next iteration if there are no matches
            if ( count( $_matches ) < 3 ) continue;
            // The addon ID: first match
            $_addonId = $_matches[1];
            // Property name: second match
            $_property = $_matches[2];
            // if entry for add-on doesn't exist in normalized array, continue
            if ( ! isset( $normalized[ $_addonId ] ) ) continue;
            // Add the property to the normalized array for the addon's entry
            $normalized[ $_addonId ][ $_property ] = $_value;
        }

        return $normalized;
    }


    /**
     * Converts the given format string into a regex pattern, replacing all instances of '%s' with
     * '([^_]+)'. The pattern can be used by PHP regex functions to match db license options.
     *
     * @param  string $formatString
     * @return string
     */
    protected static function _formatStringToDbOptionPattern( $formatString ) {
        return sprintf( '/\\A%s\\Z/', str_replace( '%s', '([^_\s]+)', $formatString ) );
    }


    /**
     * Calculates the "soon-to-expire" timestamp.
     *
     * @return integer The timestamp for a future date, for which addons whose license's expiry lies between this date and the present are considered "soon-to-expire".
     */
    public function getSteTimestamp() {
        $period = $this->getExpirationNoticePeriod();
        if ( is_null( $period ) ) {
            return null;
        }

        return is_numeric( $period )
                ? time() + $period
                : strtotime( sprintf( '+%s', $period ) );
    }

}
