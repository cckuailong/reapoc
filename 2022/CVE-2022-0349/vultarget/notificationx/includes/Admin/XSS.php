<?php

/**
 * Extension Factory
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Admin;

use NotificationX\GetInstance;

class XSS {
    use GetInstance;

    public function __construct() {
        add_filter( 'nx_settings', [ $this, 'save_settings' ] );
        add_filter( 'nx_settings_tab_miscellaneous', [ $this, 'settings_tab_help' ] );
    }

    public function save_settings( $settings ) {
        if ( isset( $settings['xss_code'] ) ) {
            unset( $settings['xss_code'] );
        }
        return $settings;
    }


    public function settings_tab_help( $tabs ) {

        $tabs['fields']['xss_settings'] = array(
            'name'     => 'xss_settings',
            'type'     => 'section',
            'label'    => __( 'Cross Domain Notice', 'notificationx' ),
            'priority' => 30,
            'fields'   => array(
                'xss_code' => array(
                    'name'         => 'xss_code',
                    'type'         => 'codeviewer',
                    'label'        => __( 'Cross Domain Notice', 'notificationx' ),
                    'button_text'  => __( 'Click to Copy', 'notificationx' ),
                    'success_text' => __( 'Cross Domain Notice code has been copied to Clipboard.', 'notificationx' ),
                    'is_pro'       => true,
                    'copyOnClick'  => true,
                    'readOnly'     => true,
                    'help'         => sprintf( __( 'Show your Notification Alerts in another website using <a target="_blank" href="%s">Cross Domain Notice</a>.', 'notificationx' ), 'https://notificationx.com/docs/notificationx-cross-domain-notice/' ),
                    'default'      => apply_filters( 'nx_settings_xss_code_default', "<div id='notificationx-frontend'></div>\n<script>....</script>\n<script src='....../crossSite.js'></script>" ),
                    'priority'     => 1,
                ),
            ),
        );

        return $tabs;
    }
}
