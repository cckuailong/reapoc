<?php
/**
 * Block: Tickets
 * Content Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/content-title.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @version 4.11.0
 *
 */

$ticket = $this->get( 'ticket' );
$post_id = $this->get( 'post_id' );
$is_mini = $this->get( 'is_mini' );
$title_classes = [
	'tribe-common-h7',
	'tribe-common-h6--min-medium',
	'tribe-tickets__item__content__title',
];

$event_title_classes =[
	'tribe-common-b3',
	'tribe-tickets__item__content__subtitle'
];

if ( ! $ticket->show_description() || empty( $ticket->description ) || $is_mini ) {
	$title_classes[] = 'tribe-tickets--no-description';
}
?>
<div <?php tribe_classes( $title_classes ); ?> >
	<?php if ( $is_mini ) : ?>
		<div <?php tribe_classes( $event_title_classes ); ?> >
			<?php echo esc_html( get_the_title( $post_id ) ); ?>
		</div>
	<?php endif; ?>
	<?php echo esc_html( $ticket->name ); ?>
</div>
