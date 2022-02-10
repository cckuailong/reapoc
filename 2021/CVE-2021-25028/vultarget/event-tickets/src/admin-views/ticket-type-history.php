<?php
/**
 * @var Tribe__Post_History $history
 * @var string $provider
 */
$entry_list = $history->get_entries();
	if ( ! empty( $entry_list ) ) : ?>
	<div class="input_block ticket_advanced ticket_advanced_<?php echo esc_attr( $provider ); ?> history">

		<label for="ticket_history" class="ticket_form_label ticket_form_left"> <?php
			echo esc_html(
				sprintf(
					_x( '%s history:', 'ticket type history', 'event-tickets' ), tribe_get_ticket_label_singular( 'ticket_type_history' )
				)
			); ?>
		</label>

		<div class="ticket_form_right">
			<a href="#" class="toggle-history">
				<span><?php esc_html_e( 'Click to view the history', 'event-tickets' ); ?></span>
				<span><?php esc_html_e( 'Click to hide history', 'event-tickets' ); ?></span>
			</a>
			<ul>
				<?php foreach ( $entry_list as $entry ) : ?>
					<li>
						<span class="date"><?php echo esc_html( $entry->datetime ); ?> </span>
						<span class="details"><?php echo wp_kses_post( $entry->message ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php endif;
