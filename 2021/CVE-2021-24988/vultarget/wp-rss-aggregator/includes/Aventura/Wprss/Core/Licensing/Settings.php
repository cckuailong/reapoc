<?php

namespace Aventura\Wprss\Core\Licensing;
use WPRSS_MBString;

/**
 * The licensing settings class.
 *
 * Manages registraion, rendering and validation of the licensing settings as well as handling AJAX requests
 * from the client. This class uses the Manager class internally to retrieve, update and manage licenses.
 */
class Settings {

    /**
     * What to print in place of license code chars.
     * This must not be a symbol that is considered to be a valid license key char.
     *
     * @since 4.6.10
     */
    const LICENSE_KEY_MASK_CHAR = '•';

    /**
     * How many characters of the license code to print as is.
     * Use negative value to indicate that characters at the end of the key are excluded.
     *
     * @since 4.6.10
     */
    const LICENSE_KEY_MASK_EXCLUDE_AMOUNT = -4;

    /**
     * The Licensing Manager instance.
     *
     * @var Manager
     */
    protected $_manager;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->_setManager( wprss_licensing_get_manager() );
        // Only load notices if on admin side
        if ( is_main_site() && is_admin() ) {
            $this->_initNotices();
        }
    }

    /**
     * Sets the license manager for this settings controller to use.
     *
     * @param \Aventura\Wprss\Core\Licensing\Manager $manager An instance of the license manager.
     * @return \Aventura\Wprss\Core\Licensing\Settings This instance.
     */
    protected function _setManager(Manager $manager) {
        $this->_manager = $manager;
        return $this;
    }

    /**
     * Gets the license manager for this settings controller.
     *
     * @return \Aventura\Wprss\Core\Licensing\Manager The license manager used by this settings controller.
     */
    public function getManager() {
        return $this->_manager;
    }

    /**
     * Initializes the admin notices.
     *
     * @return \Aventura\Wprss\Core\Licensing\Settings
     */
    protected function _initNotices() {
        $factory = wprss_core_container()->get(sprintf('%sfactory', WPRSS_SERVICE_ID_PREFIX));
        $noticesComponent = wprss()->getAdminAjaxNotices();

        foreach ( $this->getManager()->getAddons() as $_addonId => $_addonName ) {
            $_year = date('Y');
            $emptyLicenseNotice = $factory->make(
                sprintf('%saddon_empty_license', WPRSS_NOTICE_SERVICE_ID_PREFIX),
                [
                    'addon_id'    => $_addonId,
                    'addon_name'  => $_addonName,
                    'settings'    => $this
                ]
            );
            $noticesComponent->addNotice($emptyLicenseNotice);

            $inactiveLicenseNotice = $factory->make(
                sprintf('%saddon_inactive_license', WPRSS_NOTICE_SERVICE_ID_PREFIX),
                [
                    'addon_id'    => $_addonId,
                    'addon_name'  => $_addonName,
                    'settings'    => $this
                ]
            );
            $noticesComponent->addNotice($inactiveLicenseNotice);

            $expiringLicenseNotice = $factory->make(
                sprintf('%saddon_expiring_license', WPRSS_NOTICE_SERVICE_ID_PREFIX),
                [
                    'addon_id'    => $_addonId,
                    'addon_name'  => $_addonName,
                    'settings'    => $this,
                    'year'        => $_year
                ]
            );
            $noticesComponent->addNotice($expiringLicenseNotice);
        }

        return $this;
    }

    /**
     * Condition callback for the "invalid license notice".
     *
     * @return boolean True if the notice is to be shown, false if not.
     */
    public function emptyLicenseKeyNoticeCondition( $args ) {
        if (!current_user_can('manage_options')) {
            return false;
        }

        if ( ! isset( $args['addon'] ) ) {
            return false;
        }

        if (!is_main_site()) {
            return false;
        }

        $license = $this->getManager()->getLicense( $args['addon'] );

        return is_null($license) || strlen( $license->getKey() ) === 0;
    }


    /**
     * Condition callback for the "inactive saved license" notice.
     *
     * @return boolean True if the notice is to be shown, false if not.
     */
    public function savedInactiveLicenseNoticeCondition( $args ) {
        if (!current_user_can('manage_options')) {
            return false;
        }

        if ( ! isset( $args['addon'] ) ) {
            return false;
        }
        $license = $this->getManager()->getLicense( $args['addon'] );

        return $license !== null && strlen( $license->getKey() ) > 0 && ! $license->isValid();
    }


    /**
     * Condition callback for the "soon to expire license" notice.
     *
     * @return boolean True if the notice is to be shown, false if not.
     */
    public function soonToExpireLicenseNoticeCondition( $args ) {
        if (!current_user_can('manage_options')) {
            return false;
        }

        if (!isset($args['addon'])) {
            return false;
        }

        $manager = $this->getManager();
        $license = $manager->getLicense($args['addon']);

        return $license && $license->isValid() && $manager->isLicenseExpiring($args['addon']);
    }


    /**
     * Registers the WordPress settings.
     */
    public function registerSettings() {
        // Iterate all addon IDs and register a settings section with 2 fields for each.
        foreach ($this->getManager()->getAddons() as $_addonId => $_addonName) {
            // Settings Section
            add_settings_section(
                sprintf('wprss_settings_%s_licenses_section', $_addonId),
                sprintf('%s %s', $_addonName, __('License', 'wprss')),
                '__return_empty_string',
                'wprss_settings_license_keys'
            );
            // License key field
            add_settings_field(
                sprintf('wprss_settings_%s_license', $_addonId),
                __('License key', 'wprss'),
                [$this, 'renderLicenseKeyField'],
                'wprss_settings_license_keys',
                sprintf('wprss_settings_%s_licenses_section', $_addonId),
                [$_addonId]
            );
            // Activate license button
            add_settings_field(
                sprintf('wprss_settings_%s_activate_license', $_addonId),
                __('Activate license', 'wprss'),
                [$this, 'renderActivateLicenseButton'],
                'wprss_settings_license_keys',
                sprintf('wprss_settings_%s_licenses_section', $_addonId),
                [$_addonId]
            );
        }

        return $this;
    }

    /**
     * Renders the license field for a particular add-on.
     *
     * @since 4.4.5
     */
    public function renderLicenseKeyField( $args ) {
        if (count($args) < 1) {
            return;
        }
        // Addon ID is the first arg
        $addonId = $args[0];
        // Get the addon's license
        $license = $this->getManager()->getLicense( $addonId );
        // Mask it - if the license exists
        $displayedKey = $license !== null
            ? self::obfuscateLicenseKey( $license->getKey() )
            : '';

        printf(
            '<input id="wprss-%s-license-key" name="wprss_settings_license_keys[%s_license_key]" class="wprss-license-input" type="text" value="%s" style="width: 300px;" />',
            esc_attr($addonId),
            esc_attr($addonId),
            esc_attr($displayedKey)
        );

        printf(
            '<label class="description" for="wprss-%s-license-key">%s</label>',
             esc_attr($addonId),
            __('Enter your license key', 'wprss')
        );
    }


    /**
     * Masks a license key.
     *
     * @param  string  $licenseKey        The license key to mask
     * @param  string  $maskChar          The masking character(s)
     * @param  integer $maskExcludeAmount The amount of characyers to exclude from the mask. If negative, the exluded characters will begin from the end of the string
     * @return string                     The masked license key
     */
    public static function obfuscateLicenseKey( $licenseKey, $maskChar = self::LICENSE_KEY_MASK_CHAR, $maskExcludeAmount = self::LICENSE_KEY_MASK_EXCLUDE_AMOUNT ) {
        // Pre-calculate license key length
        $licenseKeyLength = strlen( $licenseKey );
        // In case the mask exclude amount is greater than the license key length
        $actualMaskExcludeAmount = abs( $maskExcludeAmount ) > ( $licenseKeyLength - 1 )
                ? ( $licenseKeyLength - 1 ) * ( $maskExcludeAmount < 0 ? -1 : 1 ) // Making sure to preserve position of mask
                : $maskExcludeAmount;
        // How many chars to mask. Always at least one char will be masked.
        $maskLength = $licenseKeyLength - abs( $actualMaskExcludeAmount );
        // Create the mask
        $mask = $maskLength > 0 ? str_repeat( $maskChar, $maskLength ) : '';
        // The starting index: if negative mask exclude amount, start from the back. otherwise start from 0
        $startIndex = $actualMaskExcludeAmount < 0 ? $maskLength : 0;
        // Extract the excluded characters
        $excludedChars = WPRSS_MBString::mb_substr( $licenseKey, $startIndex, abs( $actualMaskExcludeAmount ) );
        // Generate the displayed key and return it
        return sprintf( $actualMaskExcludeAmount > 0 ? '%1$s%2$s' : '%2$s%1$s', $excludedChars, $mask );
    }

    /**
     * Determines whether or not the license key in question is obfuscated.
     *
     * This is achieved by searching for the mask character in the key. Because the
     * mask character cannot be a valid license character, the presence of at least
     * one such character indicates that the key is obfuscated.
     *
     * @param string $key The license key in question.
     * @param string $maskChar The masking character(s).
     * @return bool Whether or not this key is obfuscated.
     */
    public function isLicenseKeyObfuscated( $key, $maskChar = self::LICENSE_KEY_MASK_CHAR ) {
        return WPRSS_MBString::mb_strpos( $key, $maskChar ) !== false;
    }


    /**
     * Invalidates the key if it is obfuscated, causing the saved version to be used.
     * This meant that the new key will not be saved, as it is considered then to be unchanged.
     *
     * @since 4.6.10
     * @param bool $is_valid Indicates whether the key is currently considered to be valid.
     * @param string $key The license key in question
     * @return bool Whether or not the key is still to be considered valid.
     */
    public function validateLicenseKeyForSave( $is_valid, $key ) {
        return !$this->isLicenseKeyObfuscated($key) && $is_valid;
    }

    /**
     * Renders the activate/deactivate license button for a particular add-on.
     *
     * @since 4.4.5
     */
    public function renderActivateLicenseButton( $args ) {
        $addonId = $args[0];
        $manager = $this->getManager();
        $data = $manager->checkLicense( $addonId, 'ALL' );
        $data = empty($data) ? 'invalid' : $data;
        $status = is_string( $data ) ? $data : $data->license;
        if ( $status === 'site_inactive' ) $status = 'inactive';
        if ( $status === 'item_name_mismatch' ) $status = 'invalid';

        $valid = $status == 'valid';
        $btnText = $valid
            ? __('Deactivate license', 'wprss')
            : __('Activate license');
        $btnName = "wprss_{$addonId}_license_" . ( $valid? 'deactivate' : 'activate' );
        $btnClass = "button-" . ( $valid ? 'deactivate' : 'activate' ) . "-license";
        wp_nonce_field( "wprss_{$addonId}_license_nonce", "wprss_{$addonId}_license_nonce", false );

        $icon = '';
        if ( $status === 'valid' ) {
            $icon = '<i class="fa fa-check"></i>';
        } elseif( $status === 'invalid' || $status === 'expired' ) {
            $icon = '<i class="fa fa-times"></i>';
        } elseif( $status === 'inactive' ) {
            $icon = '<i class="fa fa-warning"></i>';
        }

        printf(
            '<input type="button" class="%s button-process-license button-secondary" name="%s" value="%s" />',
            esc_attr($btnClass),
            esc_attr($btnName),
            esc_attr($btnText)
        );

        printf(
            '<span class="wprss-license-status-text">
                <strong>%s</strong>
                <span class="wprss-license-icon-%s">%s</span>
            </span>',
            __('Status', 'wprss'),
            esc_attr($status),
            $icon
        );

        $license = $manager->getLicense($addonId);

        if ($license !== null && !$license->isInvalid() && ($licenseKey = $license->getKey()) && !empty($licenseKey)) {
            if (!is_object($data)) {
                printf(
                    '<p><small>%</small></p>',
                    __(
                        'Failed to get license information. This is a temporary problem. Check your internet connection and try again later.',
                        'wprss'
                    )
                );
            } else {
                $currentActivations = $data->site_count;
                $activationsLeft = $data->activations_left;
                $activationsLimit = $data->license_limit;
                $expires = $data->expires;
                $expiresSpace = strpos($expires, ' ');
                // if expiry has space, get only first word
                $expires = ($expiresSpace !== false)
                    ? substr($expires, 0, $expiresSpace)
                    : $expires;
                $expires = trim($expires);
                // change lifetime expiry to never
                $expires = ($expires === Manager::EXPIRATION_LIFETIME)
                    ? __('never', 'wprss')
                    : $expires;

                if (!empty($data->payment_id) && !empty($data->license_limit)) {
                    echo '<p><small>';

                    if ($status !== 'valid' && $activationsLeft === 0) {
                        $accountUrl = sprintf(
                            'https://www.wprssaggregator.com/account/?action=manage_licenses&payment_id=%s',
                            urlencode($data->payment_id)
                        );
                        printf(
                            '<a href="%s">%s</a>',
                            esc_attr($accountUrl),
                            __(
                                'No activations left. Click here to manage the sites you\'ve activated licenses on.',
                                'wprss'
                            )
                        );

                        echo '<br/>';
                    }

                    if (!empty($expires) && $expires !== 'never' && strtotime($expires) < strtotime("+2 weeks")) {
                        $renewalUrl = sprintf(
                            '%s/checkout/?edd_license_key=%s',
                            WPRSS_SL_STORE_URL,
                            urlencode($licenseKey)
                        );

                        printf(
                            '<a href="%s">%s</a><br/>',
                            esc_attr($renewalUrl),
                            __('Renew your license to continue receiving updates and support.', 'wprss')
                        );

                        echo '<br/>';
                    }

                    printf('<strong>%s</strong> ', __('Activations:', 'wprss'));
                    printf(
                        '%s/%s (%s)',
                        $currentActivations,
                        $activationsLimit,
                        sprintf(_x('%s left', 'Number of license activations remaining', 'wprss'), $activationsLeft)
                    );

                    echo '<br/>';

                    if (!empty($expires)) {
                        printf('<strong>%s</strong> ', __('Expires:', 'wprss'));
                        printf('<code>%s</code>', esc_html($expires));
                        echo '<br/>';
                    }

                    printf('<strong>%s</strong> ', __('Registered to:', 'wprss'));
                    printf('%s (<code>%s</code>)', $data->customer_name, $data->customer_email);

                    echo '</small></p>';
                }
            }
        }
    }

    /**
     * Handles the activation/deactivation process
     *
     * @since 1.0
     */
    public function handleLicenseStatusChange() {
        $manager = $this->getManager();
        $addons = $manager->getAddons();

        // Get for each registered addon
        foreach( $addons as $id => $name ) {
            // listen for our activate button to be clicked
            if(  isset( $_POST["wprss_{$id}_license_activate"] ) || isset( $_POST["wprss_{$id}_license_deactivate"] ) ) {
                // run a quick security check
                if ( ! check_admin_referer( "wprss_{$id}_license_nonce", "wprss_{$id}_license_nonce" ) )
                    continue; // get out if we didn't click the Activate/Deactivate button
            }

            // retrieve the license
            $license = $manager->getLicense( $id );

            // If the license is not saved in DB, but is included in POST
            if ( $license == '' && ! empty( $_POST['wprss_settings_license_keys'][$id.'_license_key'] ) ) {
                // Use the license given in POST
                $license->setKey( $_POST['wprss_settings_license_keys'][ $id.'_license_key' ] );
            }

            // Prepare the action to take
            if ( isset( $_POST["wprss_{$id}_license_activate"] ) ) {
                $manager->activateLicense( $id );
            }
            elseif ( isset( $_POST["wprss_{$id}_license_deactivate"] ) ) {
                $manager->deactivateLicense( $id );
            }
        }

        return $this;
    }

    /**
     * Gets the HTML markup for the activate license button.
     *
     * @param  string $addonId The ID of the addon for which the button will be related to.
     * @return string          The HTML markup of the button.
     */
    public function getActivateLicenseButtonHtml( $addonId ) {
        ob_start();
        $this->renderActivateLicenseButton( array( $addonId ) );
        return ob_get_clean();
    }

}
