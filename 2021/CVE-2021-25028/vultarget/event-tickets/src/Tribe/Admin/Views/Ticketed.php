<?php


/**
 * Class Tribe__Tickets__Admin__Views__Ticketed
 *
 * Adds ticket status related views to the post edit screens.
 */
class Tribe__Tickets__Admin__Views__Ticketed {

	/**
	 * @var string
	 */
	protected $post_type;

	/**
	 * Tribe__Tickets__Admin__Views__Ticketed constructor.
	 *
	 * @param string $post_type
	 */
	public function __construct( $post_type = 'post' ) {
		$this->post_type = $post_type;
	}

	/**
	 * Filters the views for this post type to add the ticket status related ones.
	 *
	 * @param array $views An array of views for this post type.
	 *
	 * @return array
	 */
	public function filter_edit_link( array $views = [] ) {
		/** @var Tribe__Tickets__Cache__Cache_Interface $cache */
		$cache = tribe( 'tickets.cache' );

		$cache->include_past( true );

		$ticketed_query_var       = Tribe__Tickets__Query::$has_tickets;
		$ticketed_query_var_value = get_query_var( $ticketed_query_var );

		$ticketed_args  = [
			'post_type'         => $this->post_type,
			$ticketed_query_var => '1',
			'post_status'       => 'any',
			'paged'             => 1,
		];
		$ticketed_url   = add_query_arg( $ticketed_args );
		$ticketed_label = __( 'Ticketed', 'event-tickets' );
		$ticketed_count = count( $cache->posts_with_ticket_types( [ $this->post_type ], true ) );
		$ticketed_class = '1' === $ticketed_query_var_value ? 'class="current"' : '';

		$views['tickets-ticketed'] = sprintf( '<a href="%s" %s>%s</a> (%d)', $ticketed_url, $ticketed_class, $ticketed_label, $ticketed_count );

		$unticketed_args  = [
			'post_type'         => $this->post_type,
			$ticketed_query_var => '0',
			'post_status'       => 'any',
			'paged'             => 1,
		];
		$unticketed_url   = add_query_arg( $unticketed_args );
		$unticketed_label = __( 'Unticketed', 'event-tickets' );
		$unticketed_count = count( $cache->posts_without_ticket_types( [ $this->post_type ], true ) );
		$unticketed_class = '0' === $ticketed_query_var_value ? 'class="current"' : '';

		$views['tickets-unticketed'] = sprintf( '<a href="%s" %s>%s</a> (%d)', $unticketed_url, $unticketed_class, $unticketed_label,
			$unticketed_count );

		$cache->include_past( false );

		return $views;
	}
}