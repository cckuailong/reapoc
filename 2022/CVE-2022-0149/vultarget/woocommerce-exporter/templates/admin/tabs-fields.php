<h3><?php _e( 'Field Editor', 'woocommerce-exporter' ); ?></h3>
<p><?php _e( 'Customise the field labels for this export type by filling in the fields, an empty field label will revert to the default Store Exporter field label at export time.', 'woocommerce-exporter' ); ?></p>
<?php if( $fields ) { ?>
<form method="post" id="postform">
	<table class="form-table">
		<tbody>
	<?php foreach( $fields as $field ) { ?>
		<?php if( isset( $field['name'] ) ) { ?>
			<tr>
				<th scope="row"><label for="<?php echo $field['name']; ?>"><?php echo $field['name']; ?></label></th>
				<td>
					<input type="text" name="fields[<?php echo $field['name']; ?>]" title="<?php echo $field['name']; ?>" placeholder="<?php echo $field['label']; ?>" value="<?php if( isset( $labels[$field['name']] ) ) { echo $labels[$field['name']]; } ?>" class="regular-text all-options" />
				</td>
			</tr>
		<?php } ?>
	<?php } ?>
		</tbody>
	</table>
	<!-- .form-table -->

	<p class="submit">
		<input type="submit" value="<?php _e( 'Save Changes', 'woocommerce-exporter' ); ?> " class="button-primary" />
	</p>
	<input type="hidden" name="action" value="save-fields" />
	<?php wp_nonce_field( 'save_fields', 'woo_ce_save_fields' ); ?>
	<input type="hidden" name="type" value="<?php echo esc_attr( $export_type ); ?>" />

</form>
<?php } ?>