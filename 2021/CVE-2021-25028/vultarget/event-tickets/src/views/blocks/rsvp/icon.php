<?php
/**
 * Block: RSVP
 * Icon
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/icon.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9.3
 * @since 4.10.9 Uses new functions to get singular and plural texts.
 *
 * @version 4.10.9
 */

?>
<div class="tribe-block__rsvp__icon">
	<?php $this->template( 'blocks/rsvp/icon-svg' ); ?>
	<?php echo esc_html( tribe_get_rsvp_label_singular( basename( __FILE__ ) ) ); ?>
</div>
