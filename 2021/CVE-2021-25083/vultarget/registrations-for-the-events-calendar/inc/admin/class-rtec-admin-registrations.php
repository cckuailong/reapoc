<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class RTEC_Admin_Registrations
 */
class RTEC_Admin_Registrations {

	/**
	 * @var
	 */
	private $tab;

	/**
	 * @var array
	 */
	private $settings = array();

	/**
	 * @var int
	 */
	private $posts_per_page = 10;

	/**
	 * @var array
	 */
	private $ids_on_page = array();

	private $events_user_is_attending = array();

	private $current_user;

	private $current_user_is_author;

	/**
	 * @param $tab
	 * @param array $settings
	 */
	public function build_admin_registrations( $tab, $settings = array() ) {
		$this->tab = $tab;
		$this->settings = $settings;
		$this->current_user = wp_get_current_user();

		$this->current_user_is_author = in_array( 'author', (array) $this->current_user->roles );
		$capability = 'edit_posts';

		if ( $this->tab === 'my-registrations' && current_user_can( $capability ) && isset( $_POST['rtec_email'] ) && is_email( $_POST['rtec_email'] ) ) {
			$db            = new RTEC_Db_Admin();
			$event_id_args['where'] = array(
				array( 'email', sanitize_text_field( $_POST['rtec_email'] ), '=', 'string' ),
				array( 'status', '"x"', '!=', 'string' )
			);
			$this->events_user_is_attending     = $db->get_event_ids( $event_id_args, $arrange = 'DESC' );
		} else {
			$this->events_user_is_attending = array(0);
		}
	}

	/**
	 * @param $id
	 */
	public function add_event_id_on_page( $id )
	{
		$this->ids_on_page[] = $id;
	}

	/**
	 * @return array
	 */
	public function get_ids_on_page()
	{
		return $this->ids_on_page;
	}

	public function get_settings() {
	    return $this->settings;
    }

	/**
	 * @return array
	 */
	public function get_events( $full = false )
	{
		global $rtec_options;
		$settings = $this->settings;

		if ( $settings['qtype'] === 'all' ) {
			$args = array(
				'posts_per_page' => $this->posts_per_page,
				'start_date' => '2000-10-01 00:01',
				'offset' => $settings['off']
			);
		} elseif ( $settings['qtype'] === 'start' ) {
			$args = array(
				'posts_per_page' => $this->posts_per_page,
				'start_date' => $settings['start'],
				'offset' => $settings['off']
			);
		} elseif ( $settings['qtype'] === 'past' ) {
			$args = array(
				'posts_per_page' => $this->posts_per_page,
				'end_date'     => date( 'Y-m-d H:i', time() + rtec_get_utc_offset() ),
				'order'         => 'DESC',
				'offset'         => $settings['off']
			);
		} elseif ( $settings['qtype'] === 'cur' ) {
			$post_type = defined('Tribe__Events__Main::POSTTYPE') ? Tribe__Events__Main::POSTTYPE : 'tribe_events';
			$args = array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => 50,
				'orderby'        => 'meta_value',
				'order'          => 'ASC'
			);
			if ( $this->settings['with'] === 'with' ) {
				$args['meta_query'] = array(
					'relation' => 'AND',
					array(
						'relation' => 'AND',
						array(
							'key'     => '_EventStartDate',
							'value'   => date( 'Y-m-d H:i', time() + rtec_get_utc_offset() ),
							'compare' => '<=',
							'type'    => 'DATE'
						),
						array(
							'key'     => '_EventEndDate',
							'value'   => date( 'Y-m-d H:i', time() + rtec_get_utc_offset() ),
							'compare' => '>=',
							'type'    => 'DATE'
						)
					)
				);
			} else {
				$args['meta_query'] = array(
					'relation' => 'AND',
					array(
						'key'     => '_EventStartDate',
						'value'   => date( 'Y-m-d H:i', time() + rtec_get_utc_offset() ),
						'compare' => '<=',
						'type'    => 'DATE'
					),
					array(
						'key'     => '_EventEndDate',
						'value'   => date( 'Y-m-d H:i', time() + rtec_get_utc_offset() ),
						'compare' => '>=',
						'type'    => 'DATE'
					)
				);
			}

			if ( $this->settings['with'] === 'with' ) {
				if ( isset( $rtec_options['disable_by_default'] ) && $rtec_options['disable_by_default'] === true ) {
					$args['meta_query'][] = array(
						'key' => '_RTECregistrationsDisabled',
						'value' => '0',
						'compare' => '='
					);
				} else {
					$args['meta_query'][] = array(
						'relation' => 'OR',
						array(
							'key' => '_RTECregistrationsDisabled',
							'compare' => 'NOT EXISTS'
						),
						array(
							'key' => '_RTECregistrationsDisabled',
							'value' => '1',
							'compare' => '!='
						)
					);
				}
			}

		}  elseif ( $settings['qtype'] === 'hid' ) {

			$post_type = defined( 'Tribe__Events__Main::POSTTYPE' ) ? Tribe__Events__Main::POSTTYPE : 'tribe_events';
			$args      = array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'orderby'        => 'meta_value',
				'order'          => 'ASC'
			);

			$compare = '>=';

			$start_date = date( 'Y-m-d H:i', time() - ( 30 * 24 * 60 * 60 ) + rtec_get_utc_offset() );

			if ( $this->settings['with'] === 'with' ) {
				$args['meta_query'] = array(
					'relation' => 'AND',
					array(
						'key'     => '_EventStartDate',
						'value'   => $start_date,
						'compare' => $compare,
						'type'    => 'DATE'
					),
					array(
						'key'     => '_EventHideFromUpcoming',
						'value'   => 'yes',
						'compare' => '='
					)
				);
			} else {
				$args['meta_query'] = array(
					'relation' => 'AND',
					array(
						'key'     => '_EventStartDate',
						'value'   => $start_date,
						'compare' => $compare,
						'type'    => 'DATE'
					),
					array(
						'key'     => '_EventHideFromUpcoming',
						'value'   => 'yes',
						'compare' => '='
					)
				);
			}

			if ( $this->settings['with'] === 'with' ) {
				if ( isset( $rtec_options['disable_by_default'] ) && $rtec_options['disable_by_default'] === true ) {
					$args['meta_query'][] = array(
						'key' => '_RTECregistrationsDisabled',
						'value' => '0',
						'compare' => '='
					);
				} else {
					$args['meta_query'][] = array(
						'relation' => 'OR',
						array(
							'key' => '_RTECregistrationsDisabled',
							'compare' => 'NOT EXISTS'
						),
						array(
							'key' => '_RTECregistrationsDisabled',
							'value' => '1',
							'compare' => '!='
						)
					);
				}
			}

		} else {
			$args = array(
				'posts_per_page' => $this->posts_per_page,
				'start_date' => date( 'Y-m-d H:i' ),
				'offset' => $settings['off']
			);
		}

		if ( $this->tab === 'my-registrations' ) {
			$event_ids = $this->events_user_is_attending;
			$posts_per_page = $full ? 100 : $this->posts_per_page;
			if ( ! empty ( $event_ids ) ) {
				if ( $settings['qtype'] === 'all' ) {
					$start_date = '2000-10-01 00:01';
				} else {
					$start_date = date( 'Y-m-d H:i', (time() + rtec_get_utc_offset() - 6 *  HOUR_IN_SECONDS) );
				}
				$args = array(
					'posts_per_page' => $posts_per_page,
					'start_date' => $start_date,
					'offset' => $settings['off'],
					'post__in' => $event_ids,
				);
			} else {
				$args = false;
			}

		}

		if ( $this->settings['with'] === 'with' ) {
			if ( isset( $rtec_options['disable_by_default'] ) && $rtec_options['disable_by_default'] === true ) {
				$args['meta_query'][] = array(
					'key' => '_RTECregistrationsDisabled',
					'value' => '0',
					'compare' => '='
				);
			} else {
				$args['meta_query'][] = array(
					'relation' => 'OR',
					array(
						'key' => '_RTECregistrationsDisabled',
						'compare' => 'NOT EXISTS'
					),
					array(
						'key' => '_RTECregistrationsDisabled',
						'value' => '1',
						'compare' => '!='
					)
				);
			}
		}

		$args = apply_filters( 'rtec_registration_overview_query_args', $args, $this->settings );

		if ( isset( $args['post_type'] ) ) {
			return get_posts( $args );
		}

		return rtec_get_events( $args );
	}

	/**
	 *
	 */
	public function the_registrations_overview()
	{
		add_action( 'rtec_registrations_tab_after_the_title', array( $this, 'the_toolbar' ) );
		if ( $this->settings['v'] === 'list' ) {
			add_action( 'rtec_registrations_tab_events', array( $this, 'the_events_list' ) );
			add_action( 'rtec_registrations_tab_list_table_body', array( $this, 'the_events_list_table_body' ) );
		} else {
			add_action( 'rtec_registrations_tab_events', array( $this, 'the_events_overview' ) );
			add_action( 'rtec_registrations_tab_event_meta', array( $this, 'the_event_meta' ), 10, 1 );
			add_action( 'rtec_registrations_tab_hidden_event_options', array( $this, 'the_hidden_event_options' ), 10, 1 );
		}

		add_action( 'rtec_registrations_tab_pagination', array( $this, 'the_pagination' ) );
		add_action( 'rtec_registrations_tab_events_loaded', array( $this, 'update_status_for_event_ids' ), 10, 1 );
	}

	/**
	 *
	 */
	public function the_registrations_detailed_view()
	{
		add_action( 'rtec_registrations_tab_event_meta', array( $this, 'the_event_meta' ), 10, 1 );
		add_action( 'rtec_registrations_tab_events_loaded', array( $this, 'update_status_for_event_ids' ), 10, 1 );
	}

	/**
	 *
	 */
	public function the_toolbar() {
		require_once RTEC_PLUGIN_DIR . 'inc/admin/templates/partials/registrations-toolbar.php';
	}

	/**
	 *
	 */
	public function the_events_list() {
		require_once RTEC_PLUGIN_DIR . 'inc/admin/templates/partials/registrations-list-view.php';
	}

	/**
	 *
	 */
	public function the_events_list_table_body() {
		$events = $this->get_events();
		$settings = $this->settings;

		foreach ( $events as $event ) {
			$this->add_event_id_on_page( $event->ID );

			$event_obj = new RTEC_Admin_Event();
			$event_obj->build_admin_event( $event->ID, 'list', '' );
			$event_meta = $event_obj->event_meta;
			$venue = $event_meta['venue_title'];
			$row_class = 'class="rtec-highlight"';
			$num_registered = $event_obj->event_meta['max_registrations'];

			if ( rtec_should_show( $settings['with'], $event_meta['registrations_disabled'] ) ) {
				include RTEC_PLUGIN_DIR . 'inc/admin/templates/partials/registrations-list-table-body.php';
			}

		}

	}

	/**
	 *
	 */
	public function the_events_overview() {
		$settings = $this->settings;
		$events   = $this->get_events();

		$should_show_create_event_prompt = (empty( $events ) && $settings['qtype'] === 'upcoming' && $settings['with'] === 'with');

		if ( $should_show_create_event_prompt ) {
			?>
			<div class="rtec-notice">
				<p><?php echo sprintf( __( "Looks like you there weren't any upcoming events allowing registration found. %sCreate an event%s to get started!", 'registrations-for-the-events-calendar' ), '<a href="' . admin_url( 'post-new.php?post_type=tribe_events' ) .'" class="button button-primary">', '</a>'); ?></p>
			</div>
			<?php
			$args = array(
				'posts_per_page' => $this->posts_per_page,
				'start_date' => '2000-10-01 00:01',
				'offset' => 0
			);
			$args = apply_filters( 'rtec_registration_overview_query_args', $args, $this->settings );

			$events = rtec_get_events( $args );
			if ( ! empty( $events ) ) :
			?>
                <p><?php _e( "Here are some events that didn't fit your filters:", 'registrations-for-the-events-calendar' ); ?></p>
            <?php
            endif;

		}

		if ( ! empty( $events ) ) {
			foreach ( $events as $event ) {
				$event_meta = rtec_get_event_meta( $event->ID );
				$this->add_event_id_on_page( $event->ID );

				if ( rtec_should_show( $settings['with'], $event_meta['registrations_disabled'] ) ) {
					$event_obj = new RTEC_Admin_Event();
					$event_obj->build_admin_event( $event->ID, 'grid', '' );
					if ( ! empty( $event_obj->mvt_fields ) ) {
						echo '<div class="rtec-single-mvt-pair-wrapper rtec-clear">';
					}
					include RTEC_PLUGIN_DIR . 'inc/admin/templates/partials/registrations-overview-view.php';
				}

			}
        }


	}

	/**
	 * @param $event_obj
	 */
	public function the_event_meta( $event_obj ) {
		include RTEC_PLUGIN_DIR . 'inc/admin/templates/partials/registrations-event-meta.php';
	}

	/**
	 * @param $event_obj
	 */
	public function the_hidden_event_options( $event_obj ) {
		//include RTEC_PLUGIN_DIR . 'inc/admin/templates/partials/registrations-hidden-event-options.php';
	}

	/**
	 *
	 */
	public function the_pagination() {
		require_once RTEC_PLUGIN_DIR . 'inc/admin/templates/partials/registrations-pagination.php';
	}

	public function get_view_type_for_user() {
		$meta = get_user_meta( get_current_user_id(), 'rtec_registrations_view_type', true );

		if ( $meta === 'list' ) {
			return 'list';
		} else {
			return 'grid';
		}
	}

	public function update_view_type_for_user( $get_v ) {
		$update = 'grid';

		if ( $get_v === 'list' ) {
			$update = 'list';
		}

		update_user_meta( get_current_user_id(), 'rtec_registrations_view_type', $update );
	}

	/**
	 * @param $var
	 * @param $value
	 */
	public function the_toolbar_href( $var, $value ) {
		$href = RTEC_ADMIN_URL;
		$settings = $this->settings;
		$settings['tab'] = $this->tab;
		$settings[ $var ] = $value;
		$query_args_array = $settings;

		$href = add_query_arg( $query_args_array, $href );

		echo $href;
	}

	/**
	 * @param $context
	 */
	public function the_pagination_href( $context ) {
		$href = RTEC_ADMIN_URL;
		$settings = $this->settings;
		$settings['tab'] = $this->tab;

		if ( $context === 'back' ) {
			$settings['off'] = (int)$this->settings['off'] - $this->posts_per_page;
		} else {
			$settings['off'] = (int)$this->settings['off'] + $this->posts_per_page;
		}

		$query_args_array = $settings;

		$href = add_query_arg( $query_args_array, $href );

		echo $href;
	}

	/**
	 * @param $id
	 * @param string $mvt
	 */
	public function the_detailed_view_href( $id, $mvt = '' ) {
		$href = RTEC_ADMIN_URL;
		$settings = $this->settings;
		$settings['tab'] = 'single';
		$settings['id'] = $id;

		$query_args_array = $settings;

		$href = add_query_arg( $query_args_array, $href );

		echo $href;
	}

	/**
	 * @return bool
	 */
	public function out_of_posts() {

		if ( count( $this->get_ids_on_page() ) < $this->posts_per_page ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * @param $ids_on_page
	 */
	public function update_status_for_event_ids( $ids_on_page ) {

		if ( ! empty( $ids_on_page ) ) {
			$rtec = RTEC();
			$db = $rtec->db_frontend->instance();

			$db->update_statuses( $ids_on_page );
		}

	}

	/**
	 * @param string $status
	 * @param bool $is_user
	 *
	 * @return string
	 */
	public function get_registrant_tr_classes( $status = 'c', $is_user = false ) {

		$classes = '';
		switch( $status ) {
			case 'c' :
				$classes .= '';
				break;
			case 'p' :
				$classes .= ' rtec-unconfirmed';
				break;
			case 'n' :
				$classes .= '';
				break;
			default :
				$classes .= '';
		}

		if ( $is_user ) {
			$classes .= ' rtec-is-user';;
		}

		return $classes;
	}

	/**
	 * @param string $status
	 * @param bool $is_user
	 *
	 * @return string
	 */
	public function get_registrant_icons( $status = 'c', $is_user = false ) {

		$html = '';
		switch( $status ) {
			case 'c' :
				$html .= '';
				break;
			case 'p' :
				$html .= '<span class="rtec-notice-new rtec-unconfirmed"><i class="fa fa-flag" aria-hidden="true"></i></span>';
				break;
			case 'n' :
				$html .= '<span class="rtec-notice-new"><i class="fa fa-tag" aria-hidden="true"></i></span>';
				break;
			default :
				$html .= '';
		}

		if ( $is_user && $status !== 'n' ) {
			$html .= '<div class="rtec-status-icon-wrap">';
			$html .= '<span class="rtec-notice-new rtec-is-user"><i class="fa fa-user" aria-hidden="true"></i></span>';
			$html .= '<span class="rtec-status-explanation">' . __( 'Logged-in user', 'registrations-for-the-events-calendar' ) . '</span>';
			$html .= '</div>';
		}

		return $html;
	}
}
