<?php
/**
 * @package     Analytics
 * @copyright   Copyright (c) 2019, CyberChimps, Inc.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License Version 3
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var array $VARS
 */
$als       = analytics( $VARS['id'], $VARS['product_name'], $VARS['version'], $VARS['module_type'], $VARS['slug'] );
$slug      = $als->get_slug();
$name      = $VARS['product_name'];
$img_url   = $VARS['plugin_url'] . 'icon.png';
$usage_url = 'https://wordpress.org/support/plugin/' . $slug . '/reviews/?filter=5#new-post';
$params    = array(
	'nonces'   => array(
		'ask_for_usage' => wp_create_nonce( 'ask_for_usage' ),
	),
	'ajax_url' => admin_url( 'admin-ajax.php' ),
);

as_enqueue_local_style( 'as_ask_for_usage', '/admin/ask-for-usage.css' );
as_enqueue_local_script( 'as_ask_for_usage', '/admin/ask-for-usage.js' );
wp_localize_script( 'as_ask_for_usage', 'ask_for_usage', $params );

?>
<div class="notice ask-for-usage-notice is-dismissible">
	<input type="hidden" name="plugin_slug" value="<?php echo esc_attr( $slug ); ?>">
	<div class="notice-image">
		<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" />
	</div>
	<div class="notice-content">
		<div class="notice-heading">
			<h2>Help us improve <?php echo esc_attr( $name ); ?></h2>
			<p>Sending us anonymous usage statistics helps us keep improving your <?php echo esc_attr( $name ); ?> experience. Would you like to share this information with us?</p>
		</div>
		<div class="notice-buttons">
			<button class="ask-for-usage-optin button button-primary">Sure, I'll share the information</button>
			<button class="ask-for-usage-optout button button-secondary">No thanks, I don't want to share this information</button>
		</div>
	</div>
</div>
