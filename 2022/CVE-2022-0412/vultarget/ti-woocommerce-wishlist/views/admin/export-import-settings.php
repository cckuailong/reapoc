<?php
/**
 * The Template for displaying Export/Import settings content.
 *
 * @since             1.17.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<section class="tinvwl-panel w-shadow w-bg">
	<div class="container-fluid">

		<div class="row">
			<div style="text-align: left; padding:0 45px 20px;">
				<h3 style="padding: 28px 0;
    margin-bottom: 30px;
    border-bottom: 2px solid rgba(219,219,219,0.522);"><?php _e( 'You can import and export your TI WooCommerce Wishlist plugin settings here. ', 'ti-woocommerce-wishlist' ); ?></h3>
				<p style="font-size: 1.1em"><?php _e( 'This allows you to either backup the data, or to move your settings to a new WordPress instance.', 'ti-woocommerce-wishlist' ); ?></p>
			</div>
		</div>
	</div>
</section>
<section class="tinvwl-panel w-shadow w-bg">
	<div class="container-fluid">
		<div class="row">
			<div style="text-align: left; padding:0 45px 30px;">
				<h2 style="padding: 28px 0;
    margin-bottom: 30px;
    border-bottom: 2px solid rgba(219,219,219,0.522);"><?php _e( 'Import settings', 'ti-woocommerce-wishlist' ); ?></h2>
				<script>
					jQuery(document).ready(function ($) {
						$('.js-tinvwl-upload-toggle').click(function () {
							$('#js-tinvwl-upload-file').toggle();
							$('#js-tinvwl-paste-json').toggle();
						});
					});

				</script>
				<form action="options.php" method="post" enctype="multipart/form-data">
					<input type="hidden" name="action" value="tinvwl_import_settings"/>
					<input type="hidden" name="tinvwl_import_nonce"
						   value="<?php echo esc_attr( wp_create_nonce( 'tinvwl_import' ) ); ?>"/>
					<div id="js-tinvwl-upload-file">
						<p><?php _e( 'Please upload the exported json file or', 'ti-woocommerce-wishlist' ); ?>
							<span style="cursor: pointer;color: #ff5739;text-decoration: underline;"
								  class="js-tinvwl-upload-toggle"><?php _e( 'paste the entire json', 'ti-woocommerce-wishlist' ); ?></span>.
						</p>
						<div style="margin-bottom: 20px;"><input type="file" name="settings-file"/></div>
					</div>
					<div id="js-tinvwl-paste-json" style="display:none;">
						<p><?php _e( 'Please paste the exported json file or', 'ti-woocommerce-wishlist' ); ?>
							<span style="cursor: pointer;color: #ff5739;text-decoration: underline;"
								  class="js-tinvwl-upload-toggle"><?php _e( 'upload the exported file', 'ti-woocommerce-wishlist' ); ?></span>.
						</p>
						<div style="margin-bottom: 20px;"><textarea name="settings-json"
																	style="width: 100%;height: 200px;"></textarea></div>
					</div>

					<div>
						<input type="submit" name="setup" class="tinvwl-btn"
							   value="<?php _e( 'Import', 'ti-woocommerce-wishlist' ); ?>"/>
					</div>

				</form>
			</div>
		</div>
	</div>
</section>
<section class="tinvwl-panel w-shadow w-bg">
	<div class="container-fluid">
		<div class="row">
			<div style="text-align: left; padding:0 45px 30px;">
				<h2 style="padding: 28px 0;
    margin-bottom: 30px;
    border-bottom: 2px solid rgba(219,219,219,0.522);"><?php _e( 'Export settings', 'ti-woocommerce-wishlist' ); ?></h2>
				<form action="options.php" method="post">
					<input type="hidden" name="action" value="tinvwl_export_settings"/>
					<input type="hidden" name="tinvwl_import_nonce"
						   value="<?php echo esc_attr( wp_create_nonce( 'tinvwl_import' ) ); ?>"/>
					<p><?php _e( 'Download the entire plugin configuration.', 'ti-woocommerce-wishlist' ); ?></p>

					<div>
						<input type="submit" name="setup" class="tinvwl-btn"
							   value="<?php _e( 'Export', 'ti-woocommerce-wishlist' ); ?>"/>
					</div>

				</form>
			</div>
		</div>
	</div>
</section>
