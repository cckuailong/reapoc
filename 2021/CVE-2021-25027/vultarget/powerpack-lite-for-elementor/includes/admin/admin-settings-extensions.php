<?php
$extensions         = pp_elements_lite_get_extensions();
$enabled_extensions = pp_elements_lite_get_enabled_extensions();
?>
<div class="pp-settings-section">
	<div class="pp-settings-section-header">
		<h3 class="pp-settings-section-title"><?php _e( 'Extensions', 'powerpack' ); ?></h3>
	</div>
	<div class="pp-settings-section-content">
		<table class="form-table pp-settings-elements-grid">
			<?php
			foreach ( $extensions as $extension_name => $extension_title ) :
				$extension_enabled = false;

				if ( is_array( $enabled_extensions ) && ( in_array( $extension_name, $enabled_extensions ) ) || isset( $enabled_extensions[ $extension_name ] ) ) {
					$extension_enabled = true;
				}
				if ( ! is_array( $enabled_extensions ) && 'disabled' != $enabled_extensions ) {
					$extension_enabled = true;
				}
				?>
			<tr valign="top">
				<th>
					<label for="<?php echo $extension_name; ?>">
						<?php echo $extension_title; ?>
					</label>
				</th>
				<td>
					<label class="pp-admin-field-toggle">
						<input
							id="<?php echo $extension_name; ?>"
							name="pp_enabled_extensions[]"
							type="checkbox"
							value="<?php echo $extension_name; ?>"
							<?php echo $extension_enabled ? ' checked="checked"' : ''; ?>
						/>
						<span class="pp-admin-field-toggle-slider" aria-hidden="true"></span>
					</label>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>

<?php wp_nonce_field( 'pp-extensions-settings', 'pp-extensions-settings-nonce' ); ?>
