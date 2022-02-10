<?php
 
/**
 * Addon: cashCRED
 * Addon URI: http://codex.mycred.me/chapter-iii/cashcred/
 * Version: 1.0
 */
if ( ! defined( 'myCRED_VERSION' ) ) exit;

define( 'MYCRED_CASHCRED',              __FILE__ );
define( 'MYCRED_CASHCRED_VERSION',      '1.0' );
define( 'MYCRED_CASHCRED_DIR',           myCRED_ADDONS_DIR .   'cash-creds/' );
define( 'MYCRED_CASHCRED_ABSTRACT_DIR',  MYCRED_CASHCRED_DIR . 'abstracts/' );
define( 'MYCRED_CASHCRED_GATEWAYS_DIR',  MYCRED_CASHCRED_DIR . 'gateways/' );
define( 'MYCRED_CASHCRED_MODULES_DIR',   MYCRED_CASHCRED_DIR . 'modules/' );
define( 'MYCRED_CASHCRED_INCLUDES_DIR',  MYCRED_CASHCRED_DIR . 'includes/' );

if ( ! defined( 'MYCRED_CASHCRED_PENDING_COMMENTS' ) )
	define( 'MYCRED_CASHCRED_PENDING_COMMENTS', true );

if ( ! defined( 'MYCRED_CASHCRED_KEY' ) )
	define( 'MYCRED_CASHCRED_KEY', 'cashcred_withdrawal' );

/**
 * Load Dependencies
 */
require_once MYCRED_CASHCRED_ABSTRACT_DIR . 'cashcred-abstract-payment-gateway.php';

require_once MYCRED_CASHCRED_INCLUDES_DIR . 'cashcred-functions.php';
require_once MYCRED_CASHCRED_INCLUDES_DIR . 'cashcred-shortcodes.php';

/**
 * Load Built-in Gateways
 * @since 1.4
 * @version 1.0
 */

require_once MYCRED_CASHCRED_GATEWAYS_DIR . 'bank-transfer.php';
do_action( 'mycred_cashcred_load_gateways' );

/**
 * Load Modules
 * @since 1.7
 * @version 1.0
 */
require_once MYCRED_CASHCRED_MODULES_DIR . 'cashcred-module-core.php';
require_once MYCRED_CASHCRED_MODULES_DIR . 'cashcred-module-withdrawal.php';
