<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline">

	<label class="wpbs-settings-field-label">
		<strong><?php echo __( 'Languages', 'wp-booking-system' ); ?></strong>
		<br /><br />
		<?php echo __( 'What languages do you wish to use?', 'wp-booking-system' ); ?>
	</label>

	<div class="wpbs-settings-field-inner">
		
		<?php

			$languages = wpbs_get_languages();

			foreach( $languages as $code => $name ) {

				$flag_code = ( $code == 'sv' ? 'se' : ( $code == 'sl' ? 'si' : $code ) );

				echo '<div>';
					echo '<label>';
						echo '<input type="checkbox" name="wpbs_settings[active_languages][]" value="' . esc_attr( $code ) . '" ' . ( ! empty( $settings['active_languages'] ) && in_array( $code, $settings['active_languages'] ) ? 'checked' : '' ) . ' />';
						echo '<img src="' . WPBS_PLUGIN_DIR_URL . 'assets/img/flags/' . esc_attr( $flag_code ) . '.png" />';
						echo esc_html( $name );
					echo '</label>';
				echo '</div>';

			}

		?>

	</div>
	
</div>

<!-- Submit button -->
<input type="submit" class="button-primary" value="<?php echo __( 'Save Settings', 'wp-booking-system' ); ?>" />