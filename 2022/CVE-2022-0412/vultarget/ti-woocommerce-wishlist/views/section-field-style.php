<?php
/**
 * The Template for displaying admin section field for style this plugin.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="col-lg-4">
	<?php echo $label; // WPCS: xss ok. ?>
	<?php echo $field; // WPCS: xss ok. ?>
</div>
