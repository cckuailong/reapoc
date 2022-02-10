<?php
/**
 * Addon: buyCRED
 * Addon URI: http://codex.mycred.me/chapter-iii/buycred/
 * Version: 1.6
 */
if ( ! defined( 'myCRED_VERSION' ) ) exit;

define( 'MYCRED_PURCHASE',              __FILE__ );
define( 'MYCRED_PURCHASE_VERSION',      '1.6' );
define( 'MYCRED_PURCHASE_DIR',          myCRED_ADDONS_DIR . 'buy-creds/' );
define( 'MYCRED_BUYCRED_ABSTRACT_DIR',  MYCRED_PURCHASE_DIR . 'abstracts/' );
define( 'MYCRED_BUYCRED_GATEWAYS_DIR',  MYCRED_PURCHASE_DIR . 'gateways/' );
define( 'MYCRED_BUYCRED_MODULES_DIR',   MYCRED_PURCHASE_DIR . 'modules/' );
define( 'MYCRED_BUYCRED_INCLUDES_DIR',  MYCRED_PURCHASE_DIR . 'includes/' );
define( 'MYCRED_BUYCRED_TEMPLATES_DIR', MYCRED_PURCHASE_DIR . 'templates/' );

if ( ! defined( 'MYCRED_BUY_PENDING_COMMENTS' ) )
	define( 'MYCRED_BUY_PENDING_COMMENTS', true );

if ( ! defined( 'MYCRED_BUY_KEY' ) )
	define( 'MYCRED_BUY_KEY', 'buycred_payment' );

/**
 * Load Dependencies
 */
require_once MYCRED_BUYCRED_ABSTRACT_DIR . 'mycred-abstract-payment-gateway.php';

require_once MYCRED_BUYCRED_INCLUDES_DIR . 'buycred-functions.php';
require_once MYCRED_BUYCRED_INCLUDES_DIR . 'buycred-shortcodes.php';
require_once MYCRED_BUYCRED_INCLUDES_DIR . 'buycred-reward.php';

/**
 * Load Built-in Gateways
 * @since 1.4
 * @version 1.0
 */
require_once MYCRED_BUYCRED_GATEWAYS_DIR . 'paypal-standard.php';
require_once MYCRED_BUYCRED_GATEWAYS_DIR . 'bitpay.php';
require_once MYCRED_BUYCRED_GATEWAYS_DIR . 'netbilling.php';
require_once MYCRED_BUYCRED_GATEWAYS_DIR . 'skrill.php';
require_once MYCRED_BUYCRED_GATEWAYS_DIR . 'bank-transfer.php';

do_action( 'mycred_buycred_load_gateways' );

/**
 * Load Modules
 * @since 1.7
 * @version 1.0
 */
require_once MYCRED_BUYCRED_MODULES_DIR . 'buycred-module-core.php';
require_once MYCRED_BUYCRED_MODULES_DIR . 'buycred-module-pending.php';
