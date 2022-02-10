<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Orders__Report
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Orders__Report {

	/**
	 * Slug of the admin page for orders
	 *
	 * @var string
	 */
	public static $orders_slug = 'tpp-orders';

	/**
	 * @var string
	 */
	public static $tab_slug = 'tribe-tickets-paypal-orders-report';

	/**
	 * @var string The menu slug of the orders page
	 */
	public $orders_page;

	/**
	 * @var Tribe__Tickets__Commerce__PayPal__Orders__Table
	 */
	public $orders_table;

	/**
	 * Returns the link to the "Orders" report for this post.
	 *
	 * @since 4.7
	 *
	 * @param WP_Post $post
	 *
	 * @return string The absolute URL.
	 */
	public static function get_tickets_report_link( $post ) {
		$url = add_query_arg( [
			'post_type' => $post->post_type,
			'page'      => self::$orders_slug,
			'post_id'   => $post->ID,
		], admin_url( 'edit.php' ) );

		return $url;
	}

	/**
	 * Hooks the actions and filter required by the class.
	 *
	 * @since 4.7
	 */
	public function hook() {
		/** @var \Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		// if Tribe Commerce is not active, then disable report page
		if ( ! $paypal->is_active() ) {
			return;
		}

		add_filter( 'post_row_actions', [ $this, 'add_orders_row_action' ], 10, 2 );
		add_action( 'tribe_tickets_attendees_page_inside', [ $this, 'render_tabbed_view' ] );
		add_action( 'admin_menu', [ $this, 'register_orders_page' ] );

		// register the tabbed view
		$paypal_tabbed_view = new Tribe__Tickets__Commerce__PayPal__Orders__Tabbed_View();
		$paypal_tabbed_view->register();
	}

	/**
	 * Adds order related actions to the available row actions for the post.
	 *
	 * @since 4.7
	 *
	 * @param array $actions
	 * @param       $post
	 *
	 * @return array
	 */
	public function add_orders_row_action( array $actions, $post ) {
		$post_id = Tribe__Main::post_id_helper( $post );
		$post    = get_post( $post_id );

		// only if tickets are active on this post type
		if ( ! in_array( $post->post_type, Tribe__Tickets__Main::instance()->post_types(), true ) ) {
			return $actions;
		}

		/** @var \Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		if ( ! $paypal->post_has_tickets( $post ) ) {
			return $actions;
		}

		$url         = $paypal->get_event_reports_link( $post->ID, true );
		$post_labels = get_post_type_labels( get_post_type_object( $post->post_type ) );
		$post_type   = strtolower( $post_labels->singular_name );

		$actions['tickets_orders'] = sprintf(
			'<a title="%s" href="%s">%s</a>',
			sprintf( esc_html__( 'See PayPal purchases for this %s', 'event-tickets' ), $post_type ),
			esc_url( $url ),
			esc_html__( 'PayPal Orders', 'event-tickets' )
		);

		return $actions;
	}

	/**
	 * Renders the tabbed view header before the report.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Attendees $attendees
	 */
	public function render_tabbed_view( Tribe__Tickets__Attendees $attendees ) {
		$post = $attendees->get_post();

		if ( ! $post ) {
			return;
		}

		/** @var \Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		$has_tickets = $paypal->get_attendees_count( $post->ID );
		if ( ! $has_tickets ) {
			return;
		}

		$tabbed_view = new Tribe__Tickets__Commerce__PayPal__Orders__Tabbed_View();
		$tabbed_view->register();
	}

	/**
	 * Registers the PayPal orders page as a plugin options page.
	 *
	 * @since 4.7
	 */
	public function register_orders_page() {
		$candidate_post_id = Tribe__Utils__Array::get( $_GET, 'post_id', Tribe__Utils__Array::get( $_GET, 'event_id', 0 ) );

		if ( ( $post_id = absint( $candidate_post_id ) ) != $candidate_post_id ) {
			return;
		}

		$cap     = 'edit_posts';
		if ( ! current_user_can( 'edit_posts' ) && $post_id ) {
			$post = get_post( $post_id );

			if ( $post instanceof WP_Post && get_current_user_id() === (int) $post->post_author ) {
				$cap = 'read';
			}
		}

		$page_title        = __( 'PayPal Orders', 'event-tickets' );
		$this->orders_page = add_submenu_page(
			null,
			$page_title,
			$page_title,
			$cap,
			self::$orders_slug,
			[ $this, 'orders_page_inside' ]
		);

		/** @var Tribe__Tickets__Attendees $attendees */
		$attendees = tribe( 'tickets.attendees' );

		add_filter( 'tribe_filter_attendee_page_slug', [ $this, 'add_attendee_resources_page_slug' ] );
		add_action( 'admin_enqueue_scripts', [ $attendees, 'enqueue_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $attendees, 'load_pointers' ] );
		add_action( 'load-' . $this->orders_page, [ $this, 'attendees_page_screen_setup' ] );
	}

	/**
	 * Filter the page slugs that the attendee resources will load to add the order page
	 *
	 * @since 4.7
	 *
	 * @param $slugs
	 *
	 * @return array
	 */
	public function add_attendee_resources_page_slug( $slugs ) {
		$slugs[] = $this->orders_page;

		return $slugs;
	}

	/**
	 * Sets up the attendees page screen.
	 *
	 * @since 4.7
	 */
	public function attendees_page_screen_setup() {
		$this->orders_table = new Tribe__Tickets__Commerce__PayPal__Orders__Table();
		wp_enqueue_script( 'jquery-ui-dialog' );

		add_filter( 'admin_title', [ $this, 'orders_admin_title' ] );
	}

	/**
	 * Sets the browser title for the Orders admin page.
	 *
	 * @since 4.7
	 *
	 * @param $admin_title
	 *
	 *
	 * @return string
	 */
	public function orders_admin_title( $admin_title ) {
		if ( ! empty( $_GET['post_id'] ) ) {
			$event       = get_post( $_GET['post_id'] );
			$admin_title = sprintf( esc_html_x( '%s - PayPal Orders', 'Browser title', 'event-tickets' ), $event->post_title );
		}

		return $admin_title;
	}

	/**
	 * Renders the order page
	 *
	 * @since 4.7
	 */
	public function orders_page_inside() {
		$post_id = Tribe__Utils__Array::get( $_GET, 'event_id', Tribe__Utils__Array::get( $_GET, 'post_id', 0 ) );
		$post    = get_post( $post_id );

		// Build and render the tabbed view from Event Tickets and set this as the active tab
		$tabbed_view = new Tribe__Tickets__Commerce__Orders_Tabbed_View();
		$tabbed_view->set_active( self::$tab_slug );
		$tabbed_view->render();

		$author = get_user_by( 'id', $post->post_author );

		$tickets = Tribe__Tickets__Tickets::get_event_tickets( $post_id );

		/** @var \Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		/** @var \Tribe__Tickets__Commerce__PayPal__Orders__Sales $sales */
		$sales = tribe( 'tickets.commerce.paypal.orders.sales' );

		$paypal_tickets = array_filter( $tickets, [ $paypal, 'is_paypal_ticket' ] );
		$ticket_ids     = Tribe__Utils__Array::get( $_GET, 'product_ids', false );

		if ( false !== $ticket_ids ) {
			$ticket_ids = array_map( 'absint', explode( ',', $ticket_ids ) );
			$filtered   = [];
			/** @var \Tribe__Tickets__Ticket_Object $paypal_ticket */
			foreach ( $paypal_tickets as $paypal_ticket ) {
				if ( in_array( $paypal_ticket->ID, $ticket_ids ) ) {
					$filtered[] = $paypal_ticket;
				}
			}
			$paypal_tickets = $filtered;
		}

		/** @var Tribe__Tickets__Status__Manager $status_mgr */
		$status_mgr = tribe( 'tickets.status' );

		$total_sold          = $sales->get_sales_for_tickets( $tickets );
		$order_overview      = $status_mgr->get_providers_status_classes( 'tpp' );
		$complete_statuses   = (array) $status_mgr->get_statuses_by_action( 'count_completed', 'tpp' );
		$incomplete_statuses = (array) $status_mgr->get_statuses_by_action( 'count_incomplete', 'tpp' );
		$tickets_sold        = [];

		//update ticket item counts by order status
		foreach ( $paypal_tickets as $ticket ) {

			// Only Display if a PayPal Ticket otherwise kick out
			if ( 'Tribe__Tickets__Commerce__PayPal__Main' != $ticket->provider_class ) {
				continue;
			}

			if ( empty( $tickets_sold[ $ticket->name ] ) ) {
				$tickets_sold[ $ticket->name ] = [
					'ticket'     => $ticket,
					'has_stock'  => ! $ticket->stock(),
					'sku'        => get_post_meta( $ticket->ID, '_sku', true ),
					'sold'       => 0,
					'pending'    => 0,
					'completed'  => 0,
					'refunded'   => 0,
					'incomplete' => 0,
				];
			}

			//update ticket item counts by order status
			$orders = $sales->get_all_orders_by_product_id( $ticket->ID );
			foreach ( $orders as $key => $order ) {

				if ( $order->get_status_label() && $order->get_item_quantity() ) {

					if ( in_array( $order->get_status(), $complete_statuses, true ) ) {
						$tickets_sold[ $ticket->name ]['completed'] += $order->get_item_quantity();
					}

					if ( in_array( $order->get_status(), $incomplete_statuses, true ) ) {
						$tickets_sold[ $ticket->name ]['incomplete'] += $order->get_item_quantity();
					}

					$order_overview->statuses[ $order->get_status_label() ]->add_qty( $order->get_item_quantity() );
					$order_overview->statuses[ $order->get_status_label() ]->add_line_total( $order->get_sub_total() );
					$order_overview->add_qty( $order->get_item_quantity() );
					$order_overview->add_line_total( $order->get_sub_total() );

				}
			}
		}

		$post_type_object = get_post_type_object( $post->post_type );
		$post_singular_label = $post_type_object->labels->singular_name;

		// Render the table buffering its output; it will be used in the template below
		$this->orders_table->prepare_items();

		ob_start();
		$this->orders_table->search_box( __( 'Search Orders', 'event-tickets' ), 'tpp-orders' );
		$this->orders_table->display();
		$table = ob_get_clean();

		include Tribe__Tickets__Main::instance()->plugin_path . 'src/admin-views/tpp-orders.php';
	}

}
