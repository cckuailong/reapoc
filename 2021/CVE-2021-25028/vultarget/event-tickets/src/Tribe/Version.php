<?php
/**
 * Handling of Ticket Versioning
 *
 * @since  4.6
 */
class Tribe__Tickets__Version {

	/**
	 * Prior to this version we didn't have Versions for Tickets
	 *
	 * @since  4.6
	 *
	 * @var    string
	 */
	public $legacy = '4.5.6';

	/**
	 * Post meta key for the ticket version
	 *
	 * @since  4.6
	 *
	 * @var    string
	 */
	public $meta_key = '_tribe_ticket_version';

	/**
	 * Checks if the Post meta exists
	 *
	 * @since  4.6
	 *
	 * @param  int|WP_Post  $ticket  Which ticket
	 *
	 * @return bool
	 */
	public function exists( $ticket ) {
		if ( ! $ticket instanceof WP_Post ) {
			$ticket = get_post( $ticket );
		}

		if ( ! $ticket instanceof WP_Post ) {
			return false;
		}

		return metadata_exists( 'post', $ticket->ID, $this->meta_key );
	}

	/**
	 * Updates ticket version to a given string
	 * Will default to current version
	 *
	 * @since  4.6
	 *
	 * @param  int|WP_Post  $ticket   Which ticket
	 * @param  null|string  $version  Version to update to (optional)
	 *
	 * @return bool
	 */
	public function update( $ticket, $version = null ) {
		if ( ! $ticket instanceof WP_Post ) {
			$ticket = get_post( $ticket );
		}

		if ( ! $ticket instanceof WP_Post ) {
			return false;
		}

		if ( empty( $version ) ) {
			$version = Tribe__Tickets__Main::VERSION;
		}

		return update_post_meta( $ticket->ID, $this->meta_key, $version );
	}

	/**
	 * Fetches the ticket version number
	 *
	 * Assumes legacy version when Non Existent meta
	 * Assumes current version when Meta is Empty
	 *
	 * @since  4.6
	 *
	 * @param  int|WP_Post  $ticket  Which ticket
	 *
	 * @return bool
	 */
	public function get( $ticket ) {
		if ( ! $ticket instanceof WP_Post ) {
			$ticket = get_post( $ticket );
		}

		if ( ! $ticket instanceof WP_Post ) {
			return false;
		}

		// It means it was a legacy ticket, set it to the one before
		if ( ! $this->exists( $ticket ) ) {
			$version = $this->legacy;
		} else {
			$version = get_post_meta( $ticket->ID, $this->meta_key, true );
		}

		// Defaults to current version
		if ( empty( $version ) ) {
			$version = Tribe__Tickets__Main::VERSION;
		}

		return $version;
	}

	/**
	 * Version compare a ticket version to a given string
	 *
	 * @since  4.6
	 *
	 * @param  int|WP_Post  $ticket   Which ticket
	 * @param  null|string  $version  Version to compare to
	 * @param  string       $compare  What operator is passed to `version_compare()` (optional)
	 *
	 * @return bool
	 */
	public function compare( $ticket, $version, $compare = '>' ) {
		$ticket_version = $this->get( $ticket );

		return version_compare( $ticket_version, $version, $compare );
	}

	/**
	 * Checks if a given ticket is from a legacy version
	 *
	 * @since  4.6
	 *
	 * @param  int|WP_Post  $ticket  Which ticket
	 *
	 * @return bool
	 */
	public function is_legacy( $ticket ) {
		return $this->compare( $ticket, $this->legacy, '<=' );
	}

	/**
	 * Checks if a given ticket was not updated on the latest version
	 *
	 * @since  4.6
	 *
	 * @param  int|WP_Post  $ticket  Which ticket
	 *
	 * @return bool
	 */
	public function is_outdated( $ticket ) {
		return $this->compare( $ticket, Tribe__Tickets__Main::VERSION, '<' );
	}

	/**
	 * Will remove or add actions for Version Control
	 *
	 * @since  4.6
	 *
	 * @param  boolean  $add  Should add the Actions when false will remove actions
	 *
	 * @return void
	 */
	public function hook( $add = true ) {
		if ( $add ) {
			add_action( 'tribe_tickets_ticket_add', array( $this, 'update' ), 10, 1 );
		} else {
			remove_action( 'tribe_tickets_ticket_add', array( $this, 'update' ), 10 );
		}
	}
}
