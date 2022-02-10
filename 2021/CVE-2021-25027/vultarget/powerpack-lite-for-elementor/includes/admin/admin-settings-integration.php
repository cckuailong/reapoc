<?php
use PowerpackElementsLite\Classes\PP_Helper;
use PowerpackElementsLite\Classes\PP_Admin_Settings;

$settings   = PP_Admin_Settings::get_settings();
?>
<h3><?php _e( 'Integration', 'powerpack' ); ?></h3>

<table class="form-table">
	<tr valign="top">
		<th scope="row" valign="top">
			<?php esc_html_e( 'Instagram Access Token', 'powerpack' ); ?>
		</th>
		<td>
			<input id="pp_instagram_access_token" name="pp_instagram_access_token" type="text" class="regular-text" value="<?php echo PP_Admin_Settings::get_option( 'pp_instagram_access_token', true ); ?>" />
		<p class="description">
			<?php // translators: %s: Google API document ?>
			<?php echo sprintf( __( 'To get your Instagram Access Token, read <a href="%s" target="_blank">this document</a>', 'powerpack' ), 'https://powerpackelements.com/docs/create-instagram-access-token-for-instagram-feed-widget/' ); ?>
		</p>
		</td>
	</tr>
</table>
