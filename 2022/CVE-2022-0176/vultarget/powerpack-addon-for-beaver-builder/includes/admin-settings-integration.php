<h3><?php _e('Integration', 'bb-powerpack-lite'); ?></h3>
<p><?php echo __( 'Facebook App ID is required only if you want to use Facebook Comments Module. All other Facebook Modules can be used without a Facebook App ID. Note that this option will not work on local sites and on domains that don\'t have public access.', 'bb-powerpack-lite' ); ?></p>

<table class="form-table">
	<tr align="top">
		<th scope="row" valign="top">
			<label for="bb_powerpack_fb_app_id"><?php esc_html_e('Facebook App ID', 'bb-powerpack-lite'); ?></label>
		</th>
		<td>
			<input id="bb_powerpack_fb_app_id" name="bb_powerpack_fb_app_id" type="text" class="regular-text" value="<?php echo BB_PowerPack_Admin_Settings::get_option('bb_powerpack_fb_app_id', true); ?>" />
			<p class="description">
				<?php // translators: %s: Facebook App Setting link ?>
				<?php echo sprintf( __( 'To get your Facebook App ID, you need to <a href="%s" target="_blank">register and configure</a> an app. Once registered, add the domain to your <a href="%s" target="_blank">App Domains</a>', 'bb-powerpack-lite' ), 'https://developers.facebook.com/docs/apps/register/', pp_get_fb_app_settings_url() ); ?>
			</p>
		</td>
	</tr>
</table>

<?php submit_button(); ?>