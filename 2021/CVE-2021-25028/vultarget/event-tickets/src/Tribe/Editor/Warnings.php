<?php
/**
 * Conditional Warnings for the Editor.
 */

namespace Tribe\Tickets\Editor;

/**
 * Warnings handling class.
 *
 * @since 5.0.4
 */
class Warnings {

	/**
	 * Hooks actions for showing warnings
	 *
	 * @since 5.0.4
	 */
	public function hook() {
		add_action( 'tribe_events_tickets_new_ticket_buttons', [ $this, 'show_recurring_event_warning_message' ] );
		add_action( 'tribe_events_tickets_new_ticket_buttons', [ $this, 'add_commerce_provider_warning' ] );
	}

	/**
	 * Show the Recurring Event warning message.
	 *
	 * @since 5.0.4
	 *
	 * @param int $post_id Post ID.
	 */
	public function show_recurring_event_warning_message( $post_id ) {
		if ( ! class_exists( 'Tribe__Events__Pro__Main' ) || ! class_exists( 'Tribe__Events__Main' ) ) {
			return;
		}

		if ( ! function_exists( 'tribe_is_recurring_event' ) ) {
			return;
		}

		if ( \Tribe__Events__Main::POSTTYPE != get_post_type( $post_id ) ) {
			return;
		}

		$this->render_notice( $this->get_recurring_event_warning_message(), 'info', '#tribe-recurrence-active', 'checked' );
	}

	/**
	 * Add Provider missing warning for tickets.
	 *
	 * @since 5.0.4
	 */
	public function add_commerce_provider_warning() {
		$available_modules = array_diff_key( \Tribe__Tickets__Tickets::modules(), [ 'Tribe__Tickets__RSVP' => true ] );

		if ( count( $available_modules ) > 0 ) {
			return;
		}

		$this->render_notice( $this->get_commerce_provider_missing_warning_message() );
	}

	/**
	 * Get the Commerce Provider missing warning message.
	 *
	 * @since 5.0.4
	 *
	 * @return string The Commerce Provider missing message.
	 */
	public function get_commerce_provider_missing_warning_message() {
		$kb_url = 'https://evnt.is/1ao5';

		/* translators: %1$s: URL for help link, %2$s: Label for help link. */
		$link = sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( $kb_url ),
			esc_html_x( 'Learn More', 'Helper link in Ticket Editor', 'event-tickets' )
		);

		return wp_kses_post(
			sprintf(
				/* translators: %1$s: link to help article. */
				__( 'There is no payment gateway configured. To create tickets, you\'ll need to enable and configure an ecommerce solution. %1$s', 'event-tickets' ),
				$link
			)
		);
	}

	/**
	 * Get the Recurring Event warning message.
	 *
	 * @since 5.0.4
	 *
	 * @return string The Recurring Event warning message.
	 */
	public function get_recurring_event_warning_message() {
		return __( 'This is a recurring event. If you add tickets, they will only show up on first event in the recurrence series. Please carefully configure your recurring events.', 'event-tickets' );
	}

	/**
	 * Render the notice block.
	 *
	 * @since 5.0.4
	 *
	 * @param string $message Tee message to show.
	 * @param string $type    Type of message.
	 * @param string $depends_on Dependency selector.
	 * @param string $condition Dependency condition like 'checked' | 'not-checked' | 'numeric'.
	 */
	public function render_notice( $message, $type = 'info', $depends_on = '', $condition = '' ) {
		$icon           = 'dashicons-' . $type;
		$has_dependency = empty( $depends_on ) ? '' : 'tribe-dependent';
		$classes        = $type . ' ' . $has_dependency;
		$condition_attr = empty( $condition ) ? '' : 'data-condition-is-' . $condition;
		?>
		<div class="ticket-editor-notice <?php echo esc_attr( $classes ); ?>"
			<?php if ( $depends_on ) { ?>
				data-depends="<?php echo esc_attr( $depends_on ); ?>"
			<?php } ?>
			<?php echo esc_attr( $condition_attr ); ?>
		>
			<span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
			<span class="message"><?php echo wp_kses_post( $message ); ?></span>
		</div>
		<?php
	}
}
