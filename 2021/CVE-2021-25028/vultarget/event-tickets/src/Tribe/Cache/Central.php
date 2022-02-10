<?php


class Tribe__Tickets__Cache__Central {

	/**
	 * @var self
	 */
	protected static $instance;

	/**
	 * @var Tribe__Tickets__Cache__Cache_Interface
	 */
	protected $cache;

	/**
	 *  The class singleton constructor.
	 *
	 * @return Tribe__Tickets__Cache__Central
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Tribe__Tickets__Cache__Central constructor.
	 *
	 * @param Tribe__Tickets__Cache__Cache_Interface|null $cache An injectable cache object instance.
	 */
	public function __construct( Tribe__Tickets__Cache__Cache_Interface $cache = null ) {
		$this->cache = $cache ? $cache : new Tribe__Tickets__Cache__Transient_Cache();
		$this->cache->set_expiration_time( 60 );
	}

	/**
	 * Hooks the class to relevant filters.
	 */
	public function hook() {
		/**
		 * Reset the caches when a ticket is created or updated.
		 * This is convenient as all commerce providers and the RSVP provider will call it.
		 */
		add_action( 'event_tickets_after_save_ticket', array( $this->cache, 'reset_all' ) );

		/**
		 * Reset the caches when a ticket is deleted.
		 * This is convenient as all commerce providers and the RSVP provider will call it.
		 */
		add_action( 'tribe_tickets_ticket_deleted', array( $this->cache, 'reset_all' ) );

		/**
		 * Reset the caches when event options are updated.
		 * Chances are that among the updated settings there is the tickets supporting types one:
		 * from the filter we cannot know for sure. This makes the cache less prone to break
		 * if the tickets framework iterates and adds more settings that might trigger a cache
		 * reset.
		 */
		add_filter( 'tribe-events-save-options', array( $this, 'reset_all_filter_passthru' ) );

		/**
		 * Reset the caches when an event is updated.
		 * Past events, even those that do have tickets assigned, will be listed among those
		 * that have no tickets assigned. So if an event transits from future to past or viceversa
		 * the caches should be updated.
		 */
		add_action( 'tribe_events_event_save', array( $this->cache, 'reset_all' ) );

		/**
		 * Reset the caches when a post is deleted.
		 */
		add_action( 'delete_post', array( $this->cache, 'reset_all' ) );

		add_action( 'trash_post', array( $this->cache, 'reset_all' ) );
		add_action( 'untrashed_post', array( $this->cache, 'reset_all' ) );
	}

	/**
	 * Use a filter as an action to reset all caches.
	 *
	 * @param mixed $value
	 *
	 * @return mixed The original value.
	 */
	public function reset_all_filter_passthru( $value ) {
		$this->cache->reset_all();

		return $value;
	}

	/**
	 * Returns an instance of the currently used cache.
	 *
	 * @return null|Tribe__Tickets__Cache__Cache_Interface|Tribe__Tickets__Cache__Transient_Cache
	 */
	public function get_cache() {
		return $this->cache;
	}
}
