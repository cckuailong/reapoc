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
$als        = analytics( $VARS['id'], $VARS['product_name'], $VARS['version'], $VARS['module_type'], $VARS['slug'], );
$slug       = $als->get_slug();
$name       = $VARS['product_name'];
$img_url    = $VARS['plugin_url'] . 'icon.png';
$review_url = 'https://wordpress.org/support/plugin/' . $slug . '/reviews/?filter=5#new-post';
$params     = array(
	'nonces'   => array(
		'ask_for_review' => wp_create_nonce( 'ask_for_review' ),
	),
	'ajax_url' => admin_url( 'admin-ajax.php' ),
);

as_enqueue_local_style( 'as_ask_for_review', '/admin/ask-for-review.css' );
as_enqueue_local_script( 'as_ask_for_review', '/admin/ask-for-review.js' );
wp_localize_script( 'as_ask_for_review', 'ask_for_review', $params );

?>
<div class="notice ask-for-review-notice is-dismissible">
	<input type="hidden" name="plugin_slug" value="<?php echo esc_attr( $slug ); ?>">
	<div class="notice-image">
		<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $name ); ?>" />
	</div>
	<div class="notice-content">
		<div class="notice-heading">
			<p>Thanks for using the <?php echo esc_attr( $name ); ?>. Can you please do us a favor and give us a 5-star rating?</p>
		</div>
		<div class="notice-buttons">
			<a href="<?php echo esc_url( $review_url ); ?>" class="button button-primary" target="_blank">Submit Review</a>
		</div>
	</div>
</div>
