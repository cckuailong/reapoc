<?php
/**
 * Block: RSVP
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link  https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.10.8 Updated loading logic for including a renamed template.
 * @since 4.11.0 Added tribe_tickets_order_link_template_already_rendered hook usage to template to prevent duplicate links.
 * @since 5.1.0 Fixed the template loading process.
 *
 * @version 5.1.0
 *
 * @var Tribe__Tickets__Editor__Template $this
 */

$event_id         = $this->get( 'post_id' );
$rsvps            = $this->get( 'active_rsvps' );
$has_active_rsvps = $this->get( 'has_active_rsvps' );
$has_rsvps        = $this->get( 'has_rsvps' );
$all_past         = $this->get( 'all_past' );

// We don't display anything if there is no RSVP
if ( ! $has_rsvps ) {
	return false;
}

/**
 * A flag we can set via filter, e.g. at the end of this method, to ensure this template only shows once.
 *
 * @since 4.5.6
 *
 * @param boolean $already_rendered Whether the order link template has already been rendered.
 *
 * @see Tribe__Tickets__Tickets_View::inject_link_template()
 */
$already_rendered = apply_filters( 'tribe_tickets_order_link_template_already_rendered', false );

// Output order links / view link if we haven't already (for RSVPs).
if ( ! $already_rendered ) {
	$html = $this->template( 'blocks/attendees/order-links', [], false );

	if ( empty( $html ) ) {
		$html = $this->template( 'blocks/attendees/view-link', [], false );;
	}

	echo $html;

	add_filter( 'tribe_tickets_order_link_template_already_rendered', '__return_true' );
}
?>

<div class="tribe-block tribe-block__rsvp">
	<?php if ( $has_active_rsvps ) : ?>
		<?php foreach ( $rsvps as $rsvp ) : ?>
			<div class="tribe-block__rsvp__ticket" data-rsvp-id="<?php echo absint( $rsvp->ID ); ?>">
				<?php $this->template( 'blocks/rsvp/icon' ); ?>
				<?php $this->template( 'blocks/rsvp/content', array( 'ticket' => $rsvp ) ); ?>
			</div>
		<?php endforeach; ?>
	<?php else : ?>
		<div class="tribe-block__rsvp__ticket tribe-block__rsvp__ticket--inactive">
			<?php $this->template( 'blocks/rsvp/icon' ); ?>
			<?php $this->template( 'blocks/rsvp/content-inactive', array( 'all_past' => $all_past ) ); ?>
		</div>
	<?php endif; ?>
	<?php
		/**
		 * Allows filtering of extra classes used on the rsvp-block loader.
		 *
		 * @since  4.11.1
		 *
		 * @param  array $classes The array of classes that will be filtered.
		 */
		$loader_classes = apply_filters( 'tribe_rsvp_block_loader_classes', [ 'tribe-block__rsvp__loading' ] );

		$this->template( 'components/loader', [ 'loader_classes' => $loader_classes ] );
	?>
</div>
