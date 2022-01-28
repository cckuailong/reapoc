<?php

namespace MEC\Books;

use MEC\PostBase;

class Book extends PostBase {

	/**
	 * Constructor
	 *
	 * @param int|\WP_Post|array $data
	 */
	public function __construct( $data, $load_post = true ) {

		$this->type = 'book';
		parent::__construct( $data, $load_post );
	}

	/**
	 * @return int
	 */
	public function get_event_id() {

		return (int) $this->get_meta( 'mec_event_id' );
	}

	/**
	 * @return string
	 */
	public function get_title() {

		return get_the_title( $this->ID );
	}

	/**
	 * @return array //TODO: Convert to Attendee[]
	 */
	public function get_attendees() {

		return $this->get_meta( 'mec_attendees' );
	}

	/**
	 * @return array
	 */
	public function get_primary_attendees(){

		$attendees = $this->get_attendees();
		return (array)current($attendees);
	}

	/**
	 * @return bool
	 */
	public function is_first_attendee_data_for_all() {

		return (bool)$this->get_meta( 'mec_first_for_all' );
	}

	/**
	 * @return array
	 */
	public function get_fixed_fields() {

		return (array)$this->get_meta( 'mec_fixed_fields' );
	}

	/**
	 * @return string $start_timestamp:$end_timestamp
	 */
	public function get_attention_time( $type = null ) {

		$data = $this->get_meta( 'mec_attention_time' );
		if ( !is_null( $type ) ) {

			$data = explode( ':', $data );
		}

		if ( 'start' === $type ) {

			$data = isset( $data[0] ) ? $data[0] : '';
		} elseif ( 'end' === $type ) {

			$data = isset( $data[1] ) ? $data[1] : '';
		}

		return $data;
	}

	/**
	 * @return int -1 , 0 , 1
	 */
	public function get_confirm_status() {

		return $this->get_meta( 'mec_confirmed' );
	}

	public function get_confirm_status_text() {

		$status = $this->get_confirm_status();

		switch ( $status ) {
			case '-1':

				$text = __('Rejected', 'modern-events-calendar-lite');

				break;
			case '1':

				$text = __('Confirmed', 'modern-events-calendar-lite');

				break;
			case '0':
			default:

				$text = __('Pending', 'modern-events-calendar-lite');

				break;
		}

		return $text;
	}

	/**
	 * @param        $status
	 * @param string $mode manually|automatic
	 *
	 * @return void
	 */
	public function set_confirm_status( $status, $mode = 'manually' ) {

		$text_status = '';
		$status      = strtolower( trim( $status ) );
		switch ( $status ) {
			case 'reject':
			case '-1':
				$status      = -1;
				$text_status = 'rejected';
				break;
			case 'pending':
			case '0':
				$status      = 0;
				$text_status = 'pending';
				break;
			case 'confirm':
			case '1':
				$status      = 1;
				$text_status = 'confirm';
				break;
		}

		if ( in_array( $status, array( -1, 0, 1 ), false ) ) {

			$old_status = $this->get_meta( 'mec_confirmed' );
			$status     = apply_filters( 'mec_' . $this->type . '_confirmed_status_value', $status, $mode, $this->ID, $this );
			if ( $old_status != $status ) {

				$this->set_meta( 'mec_confirmed', $status );
				$this->set_meta( 'mec_status_' . $text_status . '_changed', time() );
				/**
				 * Do Action for send email or ...
				 */
				do_action( 'mec_' . $this->type . '_confirmed', $this->ID, $old_status, $status, $mode );

				return true;
			}
		}
	}

	/**
	 * @return int -1 , 0 , 1
	 */
	public function get_verification_status() {

		return $this->get_meta( 'mec_verified' );
	}

	public function get_verification_status_text() {

		$status = $this->get_verification_status();

		switch ( $status ) {
			case '-1':

				$text = __('Canceled', 'modern-events-calendar-lite');

				break;
			case '1':

				$text = __('Verified', 'modern-events-calendar-lite');

				break;
			case '0':
			default:

				$text = __('Waiting', 'modern-events-calendar-lite');

				break;
		}

		return $text;
	}

	/**
	 * @param        $status
	 * @param string $mode manually|automatic
	 *
	 * @return void
	 */
	public function set_verification_status( $status, $mode = 'manually' ) {

		$text_status = '';
		$status      = strtolower( trim( $status ) );
		switch ( $status ) {
			case 'canceled':
			case '-1':
				$status      = -1;
				$text_status = 'canceled';
				break;
			case 'waiting':
			case '0':
				$status      = 0;
				$text_status = 'waiting';
				break;
			case 'verified':
			case '1':
				$status      = 1;
				$text_status = 'verified';
				break;
		}

		if ( in_array( $status, array( -1, 0, 1 ), false ) ) {

			$old_status = $this->get_meta( 'mec_verified' );
			$status     = apply_filters( 'mec_' . $this->type . '_verified_status_value', $status, $mode, $this->ID, $this );

			if ( $old_status != $status ) {

				$this->set_meta( 'mec_verified', $status );
				$this->set_meta( 'mec_status_' . $text_status . '_changed', time() );
				/**
				 * Do Action for send email or ...
				 */
				do_action( 'mec_' . $this->type . '_verified', $this->ID, $old_status, $status, $mode );

				return true;
			}
		}

	}

	/**
	 * @return int|float|string
	 */
	public function get_price() {

		return $this->get_meta( 'mec_price' );
	}

	/**
	 * @return mixed
	 */
	public function get_location_id() {

		return $this->get_meta( 'mec_booking_location' );
	}

	/**
	 * @return array|\WP_Error|\WP_Term|null
	 */
	public function get_location_term() {

		$location_id = $this->get_location_id();
		$location    = get_term( $location_id, 'mec_location', ARRAY_A );

		return $location;
	}

	/**
	 * @return string 1,2,3
	 */
	public function get_tickets() {

		return $this->get_meta( 'mec_ticket_id' );
	}

	/**
	 * @return array
	 */
	public function get_tickets_ids() {

		$ids     = array();
		$tickets = $this->get_tickets();
		$tickets = explode( ',', trim( $tickets, ', ' ) );

		foreach ( $tickets as $ticket_id ) {

			if ( empty( $ticket_id ) || isset( $ids[ $ticket_id ] ) ) {

				continue;
			}

			$ids[ $ticket_id ] = $ticket_id;
		}

		return $ids;
	}

	public function timestamp($start, $end){
        // Timestamp is already available
        if(isset($start['timestamp']) and isset($end['timestamp']))
        {
            return $start['timestamp'].':'.$end['timestamp'];
        }

        $s_hour = $start['hour'];
        if(strtoupper($start['ampm']) == 'AM' and $s_hour == '0') $s_hour = 12;

        $e_hour = $end['hour'];
        if(strtoupper($end['ampm']) == 'AM' and $e_hour == '0') $e_hour = 12;

        $start_time = $start['date'].' '.sprintf("%02d", $s_hour).':'.sprintf("%02d", $start['minutes']).' '.$start['ampm'];
        $end_time = $end['date'].' '.sprintf("%02d", $e_hour).':'.sprintf("%02d", $end['minutes']).' '.$end['ampm'];

        return strtotime($start_time).':'.strtotime($end_time);
    }

}
