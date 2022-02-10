<?php
/**
 * The Template for displaying admin notice message this plugin.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="notice notice-<?php echo esc_attr( $_status ); ?>"><p><?php echo $_message; // WPCS: xss ok. ?></p></div>
