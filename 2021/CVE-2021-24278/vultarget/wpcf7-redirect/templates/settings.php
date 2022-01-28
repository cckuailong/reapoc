<?php
/**
 * Displays the settings panel
 */

defined( 'ABSPATH' ) || exit;

wp_nonce_field( 'wpcf7_redirect_page_metaboxes', 'wpcf7_redirect_page_metaboxes_nonce' );

do_action( 'before_redirect_settings_tab_title', $this->cf7_post );
?>

<fieldset>
	<div class="fields-wrap field-wrap-page-id">
		<div class="tab-wrap">
			<div class="wpcf7r-tab-wrap-inner">
				<div data-tab-inner>
					<div class="qs-row">
						<?php include( 'default-settings.php' ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</fieldset>

<?php
do_action( 'after_redirect_settings_tab_form', $this->cf7_post );
