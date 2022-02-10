<?php

namespace MEC\Books;

use MEC\Singleton;
use MEC\Books\BooksQuery;

class EventBook extends Singleton {

    public function get_tickets_availability( $event_id, $timestamp ){

        $ex = explode(':',$timestamp);
        $timestamp = $ex[0];
		$book = \MEC::getInstance('app.libraries.book');
        return $book->get_tickets_availability( $event_id, $timestamp );
	}

    /**
     * Booking Options
     *
     * @param int $event_id
     * @return array
     */
	public function get_booking_options( $event_id ){

		return (array)get_post_meta( $event_id, 'mec_booking', true);
	}

	/**
     * Total Booking Limit return int | "-1" unlimited
     *
     * @param int $event_id
     * @return int
     */
	public function get_total_booking_limit($event_id){

		$booking_options = $this->get_booking_options($event_id);
		$bookings_limit = isset($booking_options['bookings_limit']) && (int)$booking_options['bookings_limit'] ? (int)$booking_options['bookings_limit'] : -1;
		if(isset($booking_options['bookings_limit_unlimited']) && $booking_options['bookings_limit_unlimited']){

			$bookings_limit = -1;
		}

		return $bookings_limit;
	}

    public function get_user_books( $event_id ){

        if ( ! is_user_logged_in() ) {

			return false;
		}

		$user_data = wp_get_current_user();

		$email = $user_data->user_email;
		if ( empty( $email ) ) {

			return false;
		}

		$books = BooksQuery::getInstance()->get_books_ids(
			array(
				'attendee_email' => $email,
				'event_id'       => $event_id,
			)
		);

        return $books;
    }

    /**
	 * @param int $event_id
	 * @return array
	 */
	public function get_user_books_times_for_event( $event_id ) {

		$books = $this->get_user_books( $event_id );

        if( empty( $books ) || !$books ){

            return $books;
        }

		$books_times = array();
		if ( is_array( $books ) ) {

			foreach ( $books as $book_id ) {

				$books_times[ $book_id ] = get_post_meta( $book_id, 'mec_attention_time_start', true );
			}
		}

		return $books_times;
	}
}
