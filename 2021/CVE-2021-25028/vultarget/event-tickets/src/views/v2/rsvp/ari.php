<?php
/**
 * Block: RSVP
 * ARI
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.12.3
 * @since 5.0.0 Added form information to implement the ARi dynamic functionality.
 *
 * @version 5.0.0
 */

?>
<form
	class="tribe-tickets__rsvp-ar tribe-common-g-row tribe-common-g-row--gutters"
	name="tribe-tickets-rsvp-form-ari"
	data-rsvp-id="<?php echo esc_attr( $rsvp->ID ); ?>"
>
	<div class="tribe-tickets__rsvp-ar-sidebar-wrapper tribe-common-g-col">
		<?php $this->template( 'v2/rsvp/ari/sidebar', [ 'rsvp' => $rsvp ] ); ?>
	</div>

	<div class="tribe-tickets__rsvp-ar-form-wrapper tribe-common-g-col">
		<?php $this->template( 'v2/rsvp/ari/form', [ 'rsvp' => $rsvp ] ); ?>
	</div>
</form>
