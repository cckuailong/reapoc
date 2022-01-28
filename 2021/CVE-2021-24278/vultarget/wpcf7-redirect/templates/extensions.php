<?php
/**
 * Displays the extensions panel
 */

defined( 'ABSPATH' ) || exit;

wp_nonce_field( 'wpcf7_redirect_page_extensions', 'wpcf7_redirect_page_extensions_nonce' );

do_action( 'before_extensions_settings_tab_title', $this );
?>

<fieldset>
	<div class="fields-wrap field-wrap-page-id">
		<div class="tab-wrap">
			<div class="wpcf7r-tab-wrap-inner">
				<div data-tab-inner>
					<?php include( 'extensions/extensions-table.php' ); ?>
				</div>
			</div>
		</div>
	</div>
</fieldset>

<?php
do_action( 'after_extensions_settings_tab_title', $this );
