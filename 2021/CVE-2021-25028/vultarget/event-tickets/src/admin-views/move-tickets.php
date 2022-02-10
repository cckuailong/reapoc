<?php
/**
 * @var string $title
 * @var array  $attendees
 * @var bool   $multiple_providers
 * @var string $event_name
 * @var string $mode
 */
?>
<div id="tribe-dialog-wrapper">
	<div id="heading">
		<h1> <?php echo esc_html( $title ); ?> </h1>
	</div>

	<div id="main">

		<?php if ( 'ticket_type_only' !== $mode && empty( $attendees ) ): ?>
			<div class="error">
				<p> <?php esc_html_e( 'No attendees specified! Please try again.', 'event-tickets' ); ?> </p>
			</div>
		<?php endif; ?>

		<?php if ( 'ticket_type_only' !== $mode && $multiple_providers ): ?>
			<div class="error">
				<p> <?php esc_html_e( 'You have specified a range of attendees that are managed by different providers. It is not currently possible to move these together.', 'event-tickets' ); ?> </p>
			</div>
		<?php endif; ?>

		<div id="move-where" class="stage">
			<p> <?php printf( _n(
					'You have selected %1$s ticket for %2$s. You can move it to a different ticket type within the same event, or to a different event.',
					'You have selected %1$s tickets for %2$s. You can move them to a different ticket type within the same event, or to a different event.',
					count( $attendees ),
					'event-tickets'
				),
				'<strong>' . count( $attendees ) . '</strong>',
				'<strong>'. esc_html( $event_name ) . '</strong>'
			); ?> </p>

			<p>
				<label for="move-where-this">
					<input type="radio" value="this-post" name="move-where" id="move-where-this"/>
					<?php esc_html_e( 'Move to a different ticket type within the same event', 'event-tickets' ); ?>
				</label>
				<label for="move-where-other">
					<input type="radio" value="other" name="move-where" id="move-where-other"/>
					<?php esc_html_e( 'Move tickets to a different event', 'event-tickets' ); ?>
				</label>
			</p>
		</div>

		<div id="choose-event" class="stage">
			<p>
				<label for="post-type"> <?php esc_html_e( 'You can optionally focus on a specific post type:', 'event-tickets' ); ?> </label>
				<select name="post-type" id="post-type"></select>
			</p>

			<p>
				<label for="search-terms"> <?php esc_html_e( 'You can also enter keywords to help find the target event by title or description:', 'event-tickets' ); ?> </label>
				<input type="text" name="search-terms" id="search-terms" value="" />
			</p>

			<p>
				<label for="post-choice"> <?php esc_html_e( 'Select the post you wish to move the ticket type to:', 'event-tickets' ); ?> </label>
			</p>
			<div id="post-choice" class="select-single-container"></div>

		</div>

		<div id="choose-ticket-type" class="stage">
			<p>
				<label for="ticket-type-choice"> <?php esc_html_e( 'Select the ticket type that the tickets should be transferred to:', 'event-tickets' ); ?></label>
			</p>
			<div id="ticket-type-choice" class="select-single-container"></div>

		</div>

		<div id="processing" style="aligncenter">
			<p>
				<?php echo esc_html_x( 'Please be patient while your request is processed&hellip;', 'move tickets dialog', 'event-tickets' ) ?>
				<img src="<?php echo esc_url( admin_url( 'images/spinner.gif' ) ); ?>" />
			</p>
		</div>

		<div id="back-next">
			<p>
				<a href="#" class="button alignleft" id="back"><?php echo esc_html_x( '&laquo; Back', 'move tickets dialog', 'event-tickets' ); ?></a>
				<a href="#" class="button alignright" id="next" data-final-text="<?php esc_attr_e( 'Finish!', 'event-tickets' ); ?>">
					<?php echo esc_html_x( 'Next &raquo;', 'move tickets dialog', 'event-tickets' ); ?>
				</a>
			</p>
		</div>

	</div>
</div>