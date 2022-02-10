<?php
/**
 * Class Tribe__Tickets__Commerce__Orders_Tabbed_View
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__Orders_Tabbed_View {

	/**
	 * @var string
	 */
	public $active_tab_slug;

	/**
	 * @var array A map that binds requested pages to tabs.
	 */
	protected $tab_map = [
		'tickets-attendees' => 'tribe-tickets-attendance-report',
	];

	/**
	 * Renders the tabbed view for the current post.
	 *
	 * @since 4.7
	 * @since 4.12.1 Added Post ID to page title.
	 */
	public function render() {
		$post_id = Tribe__Utils__Array::get( $_GET, 'event_id', Tribe__Utils__Array::get( $_GET, 'post_id', false ), false );

		if ( empty( $post_id ) || ! $post = get_post( $post_id ) ) {
			return;
		}

		$view_title = sprintf(
			// Translators: %1$s: the post/event title, %2$d: the post/event ID.
			_x( 'Attendees for: %1$s [#%2$d]', 'attendees report screen heading', 'event-tickets' ),
			get_the_title( $post_id ),
			$post_id
		);

		/**
		 * Whether we should display the "Attendees for: %s [#%d]" view title.
		 *
		 * @since 5.0.1
		 *
		 * @param boolean $show_title Whether to show the view title.
		 * @param int     $post_id    The post ID.
		 */
		$show_title = apply_filters( 'tribe_tickets_attendees_show_view_title', true, $post_id );

		if ( ! $show_title ) {
			$view_title = '';
		}

		$tabbed_view = new Tribe__Tabbed_View();
		$tabbed_view->set_label( $view_title );
		$query_string = empty( $_SERVER['QUERY_STRING'] ) ? '' : '?' . $_SERVER['QUERY_STRING'];
		$request_uri  = 'edit.php' . $query_string;
		$tabbed_view->set_url( remove_query_arg( 'tab', $request_uri ) );

		$tab_map = $this->get_tab_map();

		// try to set the active tab from the requested page
		$active_string = empty( $_SERVER['QUERY_STRING'] ) ? '' : $_SERVER['QUERY_STRING'];
		wp_parse_str( $active_string, $query_args );
		if ( ! empty( $query_args['page'] ) && isset( $tab_map[ $query_args['page'] ] ) ) {
			$active = $tab_map[ $query_args['page'] ];
			$tabbed_view->set_active( $active );
		}

		/**
		 * Fires before the tabbed view renders to allow for additional tabs registration before the default tabs are added.
		 *
		 * Note that the tabbed view will not render if only a tab is registered; tabs registered during this action will
		 * appear right (after) the default ones.
		 *
		 * @since 4.7
		 *
		 * @param Tribe__Tabbed_View $tabbed_view The tabbed view that is rendering.
		 * @param WP_Post            $post        The post orders should be shown for.
		 * @param string|null        $active      The currently active tab, use the `tribe_tickets_orders_tabbed_view_tab_map` filter
		 *                                        to add tabs registered here to the map that will allow them to be activated.
		 */
		do_action( 'tribe_tickets_orders_tabbed_view_register_tab_right', $tabbed_view, $post );

		// Register the Attendees tab.
		$attendees_report = new Tribe__Tickets__Tabbed_View__Attendee_Report_Tab( $tabbed_view );

		/** @var Tribe__Tickets__Attendees $attendees */
		$attendees = tribe( 'tickets.attendees' );

		$attendees_report->set_url( $attendees->get_report_link( $post ) );
		$tabbed_view->register( $attendees_report );

		/**
		 * Fires before the tabbed view renders to allow for additional tabs registration after the default tabs are added.
		 *
		 * Note that the tabbed view will not render if only a tab is registered; tabs registered during this action will
		 * appear left (before) the default ones.
		 *
		 * @since 4.7
		 *
		 * @param Tribe__Tabbed_View $tabbed_view The tabbed view that is rendering.
		 * @param WP_Post            $post        The post orders should be shown for.
		 * @param string|null        $active      The currently active tab, use the `tribe_tickets_orders_tabbed_view_tab_map` filter
		 *                                        to add tabs registered here to the map that will allow them to be activated.
		 */
		do_action( 'tribe_tickets_orders_tabbed_view_register_tab_left', $tabbed_view, $post );

		// if there is only one tab registered then do not show the tabbed view
		if ( count( $tabbed_view->get() ) <= 1 ) {
			return;
		}

		if ( null !== $this->active_tab_slug ) {
			$tabbed_view->set_active( $this->active_tab_slug );
		}

		echo $tabbed_view->render();
	}

	/**
	 * Returns the attendee and orders tabbed view tabs to map the tab request slug to
	 * the registered tabs.
	 *
	 * @since 4.7
	 *
	 * @return array $tab_map An associative array in the [ <query_var> => <tab_slug> ] format.
	 *
	 */
	protected function get_tab_map() {
		/**
		 * Filters the attendee and orders tabbed view tabs to map the tab request slug to
		 * the registered tabs.
		 *
		 * The map will relate the GET query variable to the registered tab slugs.
		 *
		 * @since 4.7
		 *
		 * @param array $tab_map An associative array in the [ <query_var> => <tab_slug> ] format.
		 *
		 */
		$tab_map = apply_filters( 'tribe_tickets_orders_tabbed_view_tab_map', $this->tab_map );

		return $tab_map;
	}

	/**
	 * Sets the currently active tab slug.
	 *
	 * @since 4.7
	 *
	 * @param string $tab_slug
	 */
	public function set_active( $tab_slug ) {
		$this->active_tab_slug = $tab_slug;
	}
}
