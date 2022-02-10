<?php

namespace Aventura\Wprss\Core\Licensing\Plugin;

// Load Easy Digital Downloads - Software Licensing updater class file
if ( ! class_exists('EDD_SL_Plugin_Updater') ) {
	require_once( WPRSS_INC . 'libraries/EDD_licensing/EDD_SL_Plugin_Updater.php' );
}

/**
 * Updater class, extending the Software Licensing updater and implementing the updater interface.
 */
class Updater extends \EDD_SL_Plugin_Updater implements UpdaterInterface {

}
