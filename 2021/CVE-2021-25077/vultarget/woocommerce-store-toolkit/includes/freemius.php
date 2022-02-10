<?php
if( !function_exists( 'wst_fs' ) ) {
	// Create a helper function for easy SDK access.
	function wst_fs() {

		global $wst_fs;

		if( !isset( $wst_fs ) ) {
			// Include Freemius SDK.
			require_once( WOO_ST_PATH . '/freemius/start.php' );
			$wst_fs = fs_dynamic_init( array(
				'id'                  => '8306',
				'slug'                => 'woocommerce-store-toolkit',
				'type'                => 'plugin',
				'public_key'          => 'pk_dd82e56cfbe18dabba1ecf3a603a2',
				'is_premium'          => false,
				'has_addons'          => false,
				'has_paid_plans'      => false,
				'menu'                => array(
				'slug'           => 'woo_st',
				'account'        => false,
				'contact'        => false,
				'support'        => false,
				'parent'         => array(
				'slug' => 'woocommerce',
			),
			),
			) );
		}

		return $wst_fs;
	}

	// Init Freemius.
	wst_fs();
	// Signal that SDK was initiated.
	do_action( 'wst_fs_loaded' );
}

function wst_fs_connect_message_on_update( $message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link ) {

	return sprintf(
	__( 'Hey %1$s' ) . ',<br>' .
	__( 'Help us improve %2$s. If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, %2$s will still work just fine.', 'woocommerce-store-toolkit' ),
	$user_first_name,
	'<b>' . $plugin_title . '</b>',
	'<b>' . $user_login . '</b>',
	$site_link,
	$freemius_link
	);

}
wst_fs()->add_filter( 'connect_message_on_update', 'wst_fs_connect_message_on_update', 10, 6 );