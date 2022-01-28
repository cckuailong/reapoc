<?php
/**
 * Render mapping leads field
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap field-wrap-<?php echo $field['name']; ?> <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>">
	<?php if ( isset( $field['title'] ) && $field['title'] ) : ?>
		<label for="wpcf7-redirect-<?php echo $field['name']; ?>">
			<h3><?php echo esc_html( $field['label'] ); ?></h3>
			&nbsp;
			<?php if ( isset( $field['sub_title'] ) && $field['sub_title'] ) : ?>
				<label for="wpcf7-redirect-<?php echo $field['name']; ?>"><?php echo esc_html( $field['sub_title'] ); ?></label>
				<br/>&nbsp;
			<?php endif; ?>
		</label>
	<?php endif; ?>

	<div class="cf7_row">
		<table class="wp-list-table widefat fixed striped pages wp-list-table-inner">
			<tr>
				<td>
					<strong>
						<?php _e( 'Form fields', 'wpcf7-redirect' ); ?>
					</strong>
				</td>
				<td class="tags-map-api-key">
					<strong>
						<?php _e( 'Field Label', 'wpcf7-redirect' ); ?>
					</strong>
					<?php echo cf7r_tooltip( __( 'The field label', 'wpcf7-redirect' ) ); ?>
				</td>
				<td>
					<strong>
						<?php _e( 'Display on leads list', 'wpcf7-redirect' ); ?>
					</strong>
					<?php echo cf7r_tooltip( __( 'Should this field appear on the leads list', 'wpcf7-redirect' ) ); ?>
				</td>
			</tr>
			<?php
			if ( isset( $field['tags'] ) ) :
				foreach ( $field['tags'] as $mail_tag ) :
					if ( 'lead_id' === $mail_tag->name ) {
						continue;
					}
					$save      = isset( $field['value'][ $mail_tag->name ]['save'] ) ? esc_html( $field['value'][ $mail_tag->name ]['save'] ) : '';
					$tag_value = isset( $field['value'][ $mail_tag->name ]['tag'] ) ? esc_html( $field['value'][ $mail_tag->name ]['tag'] ) : '';
					$appear    = isset( $field['value'][ $mail_tag->name ]['appear'] ) && $field['value'][ $mail_tag->name ]['appear'] ? true : false;
					?>
				<tr>
					<td class="<?php echo $mail_tag->name; ?>"><?php echo $mail_tag->name; ?></td>
					<td class="tags-map-api-key">
						<input type="text" id="sf-<?php echo $mail_tag->name; ?>"
						name="wpcf7-redirect<?php echo $prefix; ?>[<?php echo $field['name']; ?>][<?php echo $mail_tag->name; ?>][tag]"
						class="large-text"
						value="<?php echo $tag_value; ?>" />
					</td>
					<td>
						<input type="checkbox"
						name="wpcf7-redirect<?php echo $prefix; ?>[<?php echo $field['name']; ?>][<?php echo $mail_tag->name; ?>][appear]"
						value="1"
						<?php checked( $appear, 1 ); ?> />
					</td>
				</tr>
					<?php
				endforeach;
			endif;
			?>
		</table>
	</div>
</div>
