<?php

/**
 * Plugin Upgrade Routine
 *
 * @since 2.2
 */
class WPUF_Upgrades {

    /**
     * The upgrades
     *
     * @var array
     */
    private static $upgrades = [
        '2.1.9' => 'upgrades/upgrade-2.1.9.php',
        '2.6.0' => 'upgrades/upgrade-2.6.0.php',
        '2.7.0' => 'upgrades/upgrade-2.7.0.php',
        '2.8.0' => 'upgrades/upgrade-2.8.0.php',
        '2.8.2' => 'upgrades/upgrade-2.8.2.php',
        '2.8.5' => 'upgrades/upgrade-2.8.5.php',
        '2.9.2' => 'upgrades/upgrade-2.9.2.php',
    ];

    /**
     * Get the plugin version
     *
     * @return string
     */
    public function get_version() {
        return get_option( 'wpuf_version' );
    }

    /**
     * Check if the plugin needs any update
     *
     * @return bool
     */
    public function needs_update() {

        // may be it's the first install
        if ( !$this->get_version() ) {
            return false;
        }
        //check if current version is greater then installed version and any update key is available
        if ( version_compare( $this->get_version(), WPUF_VERSION, '<' ) && in_array( WPUF_VERSION, array_keys( self::$upgrades ) ) ) {
            return true;
        }

        return false;
    }

    /**
     * Perform all the necessary upgrade routines
     *
     * @return void
     */
    public function perform_updates() {
        $installed_version = $this->get_version();
        $path              = trailingslashit( __DIR__ );

        foreach ( self::$upgrades as $version => $file ) {
            if ( version_compare( $installed_version, $version, '<' ) ) {
                include $path . $file;
                update_option( 'wpuf_version', $version );
            }
        }

        update_option( 'wpuf_version', WPUF_VERSION );
    }
}
