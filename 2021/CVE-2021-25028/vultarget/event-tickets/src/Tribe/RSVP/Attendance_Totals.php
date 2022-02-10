<?php
/**
 * Calculates RSVP attendance totals for a specified event (ie, how many
 * are going, not going, etc).
 *
 * Also has the capability to print this information as HTML, intended for
 * use in the attendee summary screen.
 *
 * Note that the totals are calculated upon instantiation, effectively making
 * the object a snapshot in time. Therefore if the status of RSVPs is modified
 * or if RSVPs are added/deleted later in the request, it would be necessary
 * to obtain a new object of this type to get accurate results.
 */
class Tribe__Tickets__RSVP__Attendance_Totals extends Tribe__Tickets__Abstract_Attendance_Totals {
	protected $total_rsvps = 0;
	protected $total_going = 0;
	protected $total_not_going = 0;
	protected $has_rsvp_enabled = false;

	/**
	 * Calculate total RSVP attendance for the current event.
	 */
	protected function calculate_totals() {
		$rsvp = Tribe__Tickets__RSVP::get_instance();

		$this->total_going     = $rsvp->get_attendees_count_going( $this->event_id );
		$this->total_not_going = $rsvp->get_attendees_count_not_going( $this->event_id );
		$this->total_rsvps     = $this->total_going + $this->total_not_going;

		$rsvp_tickets = $rsvp->get_tickets( $this->event_id );

		$this->has_rsvp_enabled = ! empty( $rsvp_tickets );
	}

	/**
	 * Prints an HTML (unordered) list of attendance totals.
	 */
	public function print_totals() {
		// Skip output if there are no RSVP attendees going/not going AND if there are no current RSVP tickets.
		if (
			false === $this->has_rsvp_enabled
			&& 0 === $this->get_total_rsvps()
		) {
			return;
		}

		// Note this now uses the `attendees-totals-list` template, so the array values don't quite logically line up
		$args = [
			'total_sold_label'        => esc_html( sprintf( _x( 'Total %s:', 'attendee summary', 'event-tickets' ), tribe_get_rsvp_label_plural( 'total_sold_label' ) ) ),
			'total_complete_label'    => _x( 'Going:', 'attendee summary', 'event-tickets' ),
			'total_cancelled_label'   => _x( 'Not Going:', 'attendee summary', 'event-tickets' ),
			'total_sold'              => $this->get_total_rsvps(),
			'total_complete'          => $this->get_total_going(),
			'total_cancelled'         => $this->get_total_not_going(),
			'total_refunded'          => 0,
			'total_sold_tooltip'      => '',
			'total_completed_tooltip' => '',
			'total_cancelled_tooltip' => '',
			'total_refunded_tooltip'  => '',
		];

		$html = tribe( 'tickets.admin.views' )->template( 'attendees-totals-list', $args, false );

		/**
		 * Filters the HTML that should be printed to display RSVP attendance lines.
		 *
		 * @param string $html The default HTML code displaying going and not going data.
		 */
		$html = apply_filters( 'tribe_tickets_rsvp_print_totals_html', $html );

		echo $html;
	}

	/**
	 * The total number of RSVPs received for this event.
	 *
	 * @return int
	 */
	public function get_total_rsvps() {
		/**
		 * Returns the total RSVP count for an event.
		 *
		 * @param int $total_rsvps
		 * @param int $original_total_rsvps
		 * @param int $event_id
		 */
		return (int) apply_filters( 'tribe_tickets_rsvp_get_total_rsvps', $this->total_rsvps, $this->total_rsvps, $this->event_id );
	}

	/**
	 * The total number of RSVPs for this event that indicate they are
	 * going.
	 *
	 * @return int
	 */
	public function get_total_going() {
		/**
		 * Returns the total going count for an event.
		 *
		 * @param int $total_going
		 * @param int $original_total_going
		 * @param int $event_id
		 */
		return (int) apply_filters( 'tribe_tickets_rsvp_get_total_going', $this->total_going, $this->total_going, $this->event_id );
	}

	/**
	 * The total number of RSVPs for this event that indicate they are
	 * not going.
	 *
	 * @return int
	 */
	public function get_total_not_going() {
		/**
		 * Returns the total not going count for an event.
		 *
		 * @param int $total_not_going
		 * @param int $original_total_not_going
		 * @param int $event_id
		 */
		return (int) apply_filters( 'tribe_tickets_rsvp_get_total_not_going', $this->total_not_going, $this->total_not_going, $this->event_id );
	}
}