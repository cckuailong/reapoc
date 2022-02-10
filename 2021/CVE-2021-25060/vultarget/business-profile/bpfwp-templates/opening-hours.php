<?php
/**
 * Template for displaying the opening hours
 *
 * This template does not perform the Schema.org markup. That is printed as a
 * series of metatags as part of the bpwfwp_print_opening_hours() template
 * function.
 *
 * For full details about theming, see /templates/contact-card.php
 *
 * Happy theming!
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2016, Theme of the Crop
 * @license   GPL-2.0+
 * @since     1.1.0
 */
?>

<div class="bp-opening-hours">
	<span class="bp-title"><?php _e( 'Opening Hours', 'business-profile' ); ?></span>
	<?php foreach ( $data->weekday_hours as $weekday => $times ) : ?>
	<div class="bp-weekday">
		<span class="bp-weekday-name bp-weekday-<?php echo esc_attr( $weekday ); ?>"><?php echo esc_html( $data->weekday_names[$weekday] ); ?></span>
		<span class="bp-times">
		<?php foreach ( $times as $time ) : ?>
			<span class="bp-time"><?php echo esc_html( $time ); ?></span>
		<?php endforeach; ?>
		</span>
	</div>
	<?php endforeach; ?>
</div>
