<?php
/**
 * The Template for displaying admin header this plugin.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

?>
<div class="<?php echo esc_attr(sprintf('%s-header', self::$_name)); ?> tinv-wishlist-clearfix">
	<div class="row">
		<div class="col-lg-7">
			<div class="tinwl-logo-title">
				<div class="tinvwl-table auto-width">
					<div class="tinwl-logo tinvwl-cell-3">
						<div class="tinvwl-table">
							<div class="tinvwl-cell">
								<i class="logo_heart"></i>
							</div>
							<div class="tinvwl-cell">
								<h2><?php _e('Wishlist', 'ti-woocommerce-wishlist'); ?></h2>
							</div>
						</div>
					</div>
					<div class="tinvwl-cell">
						<h1 class="tinvwl-title"><?php echo esc_html($_name); ?></h1>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-5">
			<div class="tinvwl-status-panel status-panel"><?php
				foreach ($status_panel as $item) {
					echo $item; // WPCS: xss ok.
				}
				?></div>
		</div>
	</div>

</div>
