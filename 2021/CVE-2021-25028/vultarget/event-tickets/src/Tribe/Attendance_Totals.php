<?php
/**
 * Calculates the total number of attendees checked in / deleted for a specific
 * event (irrespective of whether those attendees relate to RSVPs or other tickets).
 *
 * Also has the capability to print this information as HTML, intended for
 * use in the attendee summary screen.
 *
 * Note that the totals are calculated upon instantiation, effectively making
 * the object a snapshot in time. Therefore if attendees are deleted or the check in
 * status is modified later on in the request for one or more attendees it would be
 * necessary to obtain a fresh object of this type to get accurate totals.
 */
class Tribe__Tickets__Attendance_Totals extends Tribe__Tickets__Abstract_Attendance_Totals {
	protected $relative_priority = 50;
	protected $total_checked_in = 0;
	protected $total_not_checked_in = 0;
	protected $total_deleted = 0;

	protected function calculate_totals() {
		$total_attendees = Tribe__Tickets__Tickets::get_event_attendees_count( $this->event_id );

		$this->total_checked_in = Tribe__Tickets__Tickets::get_event_checkedin_attendees_count( $this->event_id );
		$this->total_not_checked_in = $total_attendees - $this->total_checked_in;
		$this->total_deleted = Tribe__Tickets__Attendance::instance( $this->event_id )->get_deleted_attendees_count();
	}

	public function print_totals() {
		$total_deleted_label = esc_html_x( 'Deleted Attendees:', 'attendee summary', 'event-tickets' );

		$total_deleted = $this->get_total_deleted();

		$deleted_list_item = $total_deleted ? "<li> <strong>$total_deleted_label</strong> $total_deleted </li>" : '';

		echo "
			<ul>
				$deleted_list_item
			</ul>
		";
	}

	/**
	 * The total number of checked in attendees for this event.
	 *
	 * @return int
	 */
	public function get_total_checked_in() {
		/**
		 * Returns the total number of checked in attendees for an event.
		 *
		 * @param int $total_checked_in
		 * @param int $original_total_checked_in
		 * @param int $event_id
		 */
		return (int) apply_filters( 'tribe_tickets_get_total_checked_in', $this->total_checked_in, $this->total_checked_in, $this->event_id );
	}

	/**
	 * The total number of attendees who are not yet checked in for this event.
	 *
	 * @return int
	 */
	public function get_total_not_checked_in() {
		/**
		 * Returns the total number of attendees not yet checked in for this event.
		 *
		 * @param int $total_not_checked_in
		 * @param int $original_total_not_checked_in
		 * @param int $event_id
		 */
		return (int) apply_filters( 'tribe_tickets_get_total_not_checked_in', $this->total_not_checked_in, $this->total_not_checked_in, $this->event_id );
	}

	/**
	 * The total number of attendee records for this event that were deleted.
	 *
	 * @return int
	 */
	public function get_total_deleted() {
		/**
		 * Returns the total number of attendee records for this event that were deleted.
		 *
		 * @param int $total_deleted
		 * @param int $original_total_deleted
		 * @param int $event_id
		 */
		return (int) apply_filters( 'tribe_tickets_get_total_attendees_deleted', $this->total_deleted, $this->total_deleted, $this->event_id );
	}
}
