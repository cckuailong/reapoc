<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !defined( 'MEC_CORE_FILE' ) ) {
	define( 'MEC_CORE_FILE', __FILE__ );
}

if ( !defined( 'MEC_CORE_URL' ) ) {
	define('MEC_CORE_URL', plugin_dir_url( __FILE__ ));
}
/**
 * Include Primary Class Plugin
 */
require __DIR__.'/src/Base.php';

\MEC\Base::instance()->init();