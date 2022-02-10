<?php
/**
 * Methods for handling multiple locations
 *
 * @package   RestaurantReservations
 * @copyright Copyright (c) 2016, Theme of the Crop
 * @license   GPL-2.0+
 * @since     1.6
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'rtbMultipleLocations', false ) ) {
	/**
	 * Class to handle custom post type and post meta fields
	 *
	 * @since 1.6
	 */
	class rtbMultipleLocations {

		/**
		 * Post type slug where locations can be found
		 *
		 * @since 1.6
		 */
		public $post_type = false;

		/**
		 * Taxonomy to use when assigning bookings to locations
		 *
		 * @since 1.6
		 */
		public $location_taxonomy = 'rtb_location';

		/**
		 * Set the loading hook
		 *
		 * @since 1.6
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'load' ), 100 );
		}

		/**
		 * Load locations support
		 *
		 * @since 1.6
		 */
		public function load() {

			/**
			 * Allow third-party plugins to enable multiple locations
			 *
			 * Expects a post type slug pointing to the locations or false if
			 * multiple locations are not enabled.
			 *
			 * @since 1.6
			 */
			$this->post_type = apply_filters( 'rtb_set_locations_post_type', false );

			if ( !$this->post_type ) {
				return;
			}

			$this->hooks();
		}

		/**
		 * Set up hooks
		 *
		 * @since 1.6
		 */
		public function hooks() {
			add_action( 'init',                                       array( $this, 'register_taxonomy' ), 1000 ); // after custom post types declared (hopefully!)
			add_action( 'save_post_' . $this->post_type,              array( $this, 'save_location' ), 10, 3 );
			add_action( 'before_delete_post',                         array( $this, 'delete_location' ) );
			add_action( 'rtb_booking_form_fields',                    array( $this, 'add_location_field' ), 10, 3 );
			add_action( 'rtb_pre_validate_booking_submission',        array( $this, 'validate_location' ) );
			add_action( 'rtb_insert_booking',                         array( $this, 'save_booking_location' ) );
			add_action( 'rtb_update_booking',                         array( $this, 'save_booking_location' ) );
			add_action( 'rtb_booking_load_post_data',                 array( $this, 'load_booking_location' ), 10, 2 );
			add_filter( 'rtb_query_args',                             array( $this, 'modify_query' ), 10, 2 );
			add_filter( 'rtb_bookings_all_table_columns',             array( $this, 'add_location_column' ) );
			add_filter( 'rtb_bookings_table_column',                  array( $this, 'print_location_column' ), 10, 3 );
			add_action( 'edit_form_after_title',                      array( $this, 'add_meta_nonce' ) );
			add_action( 'add_meta_boxes',                             array( $this, 'add_meta_boxes' ) );
			add_filter( 'the_content',                                array( $this, 'append_to_content' ) );
			add_filter( 'rtb_notification_email_to_email',            array( $this, 'notification_to_email' ), 10, 2 );
			add_filter( 'rtb_notification_email_from_email',          array( $this, 'notification_from_email' ), 10, 2 );
			add_filter( 'rtb_notification_email_from_name',           array( $this, 'notification_from_name' ), 10, 2 );
			add_filter( 'rtb_notification_template_tags',             array( $this, 'notification_template_tags' ), 10, 2 );
			add_filter( 'rtb_notification_template_tag_descriptions', array( $this, 'notification_template_tag_descriptions' ) );
			add_action( 'admin_init',                                 array( $this, 'fix_autodraft_term_error' ) );
			add_filter( 'rtb_settings_page',                          array( $this, 'maybe_add_location_settings' ) );
			add_action( 'admin_init',                                 array( $this, 'remove_location_select_setting' ) ); 
		}

		/**
		 * Register the location taxonomy
		 *
		 * @since 1.6
		 */
		public function register_taxonomy() {

			$args = array(
				'label'        => _x( 'Location', 'Name for grouping bookings', 'restaurant-reservations' ),
				'hierarchical' => false,
		        'public'       => true,
				'rewrite'      => false,
			);

			/**
			 * Allow third-party plugins to modify the location taxonomy
			 * arguments.
			 *
			 * @since 1.6
			 */
			$args = apply_filters( 'rtb_locations_args', $args );

			register_taxonomy( $this->location_taxonomy, RTB_BOOKING_POST_TYPE, $args );
		}

		/**
		 * Generate taxonomy terms linked to locations and keep them sync'd
		 * with any changes
		 *
		 * @since 1.6
		 */
		public function save_location( $post_id, $post, $update ) {

			if (
					$post->post_status === 'auto-draft' ||
					( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
					!current_user_can( 'edit_post', $post_id ) ||
					!isset( $_POST['rtb_location_meta_nonce'] ) ||
					!wp_verify_nonce( $_POST['rtb_location_meta_nonce'], 'rtb_location_meta' )
				) {
				return $post_id;
			}

			$term_id = get_post_meta( $post_id, $this->location_taxonomy, true );

			// Create a new term for this location
			if ( !$term_id ) {

				$term = wp_insert_term(
					sanitize_text_field( $post->post_title ),
					$this->location_taxonomy
				);

				if ( !is_a( $term, 'WP_Error' ) ) {
					update_post_meta( $post_id, $this->location_taxonomy, $term['term_id'] );
					$term_id = $term['term_id'];
				}

			// Update the term for this location
			} else {
				wp_update_term(
					$term_id,
					$this->location_taxonomy,
					array(
						'name' => sanitize_text_field( $post->post_title ),
						'slug' => sanitize_text_field( $post->post_name ),
					)
				);
			}

			if ( !empty( $_POST['rtb_append_booking_form'] ) ) {
				update_post_meta( $post_id, 'rtb_append_booking_form', true );
			} else {
				delete_post_meta( $post_id, 'rtb_append_booking_form' );
			}

			if ( $term_id ) {

				if ( !empty( $_POST['rtb_reply_to_name'] ) ) {
					$reply_to_name = sanitize_text_field( $_POST['rtb_reply_to_name'] );
					update_term_meta( $term_id, 'rtb_reply_to_name', $reply_to_name );
				} else {
					delete_term_meta( $term_id, 'rtb_reply_to_name' );
				}

				if ( !empty( $_POST['rtb_reply_to_address'] ) ) {
					$reply_to_address = sanitize_email( $_POST['rtb_reply_to_address'] );
					update_term_meta( $term_id, 'rtb_reply_to_address', $reply_to_address );
				} else {
					delete_term_meta( $term_id, 'rtb_reply_to_address' );
				}

				if ( !empty( $_POST['rtb_admin_email_address'] ) ) {
					$email = sanitize_text_field( $_POST['rtb_admin_email_address'] );
					update_term_meta( $term_id, 'rtb_admin_email_address', $email );
				} else {
					delete_term_meta( $term_id, 'rtb_admin_email_address' );
				}
			}

			return $post_id;
		}

		/**
		 * Delete taxonomy terms linked to locations when a location is deleted
		 *
		 * Only does this when no bookings are associated with that term.
		 * Otherwise it may be important to keep the bookings grouped for
		 * historical data.
		 *
		 * @since 1.6
		 */
		public function delete_location( $post_id ) {

			if ( !current_user_can( 'delete_posts' ) ) {
				return $post_id;
			}

			$term_id = get_post_meta( $post_id, $this->location_taxonomy, true );

			$term = get_term( $term_id, $this->location_taxonomy );

			if ( !$term || is_a( $term, 'WP_Error' ) ) {
				return;
			}

			$query = new rtbQuery( array( 'location' => $term_id ), 'delete-location-term-check' );
			$query->prepare_args();
			$query->get_bookings();

			// Don't delete taxonomy terms if there are bookings assigned to
			// this location, so the booking associations can remain as
			// historical data.
			if ( count( $query->bookings ) ) {
				add_term_meta( $term_id, 'rtb_location_removed', true );
			} else {
				wp_delete_term( $term_id, $this->location_taxonomy );
			}

		}

		/**
		 * Get location term id from location post id
		 *
		 * Transforms a location post id into its associated term id. If the
		 * id doesn't match a location post, it will check if the received id
		 * matches a term id and return it if so. Between versions 1.6 and
		 * and 1.6.1, only term ids were accepted as shortcodes, and this
		 * provides a backwards-compatible fallback.
		 *
		 * @param $location_id int The location id (post or term)
		 * @return int The location term id. Default: 0
		 */
		public function get_location_term_id( $location_id ) {

			$location_id = absint( $location_id );
			$term_id = 0;

			if ( get_post_type( $location_id ) === $this->post_type ) {
				$term_id = get_post_meta( $location_id, $this->location_taxonomy, true );
			} elseif ( term_exists( $location_id, $this->location_taxonomy ) ) {
				$term_id = $location_id;
			}

			return $term_id;
		}


		/**
		 * Add the location selection field to the booking form
		 *
		 * @since 1.6
		 */
		public function add_location_field( $fields, $request = null, $args = array() ) {

			// If the location is specified, don't add a field.
			// A hidden field is added automatically in rtb_print_booking_form()
			if ( !empty( $args['location'] ) ) {
				$args['location'] = $this->get_location_term_id( $args['location'] );
				if ( !empty( $args['location'] ) ) {
					return $fields;
				}
			}

			if ( $request === null ) {
				global $rtb_controller;
				$request = $rtb_controller->request;
			}

			// Select a fieldset in which to place the field
			$placement = false;
			if ( isset( $fields['reservation'] ) && isset( $fields['reservation']['fields'] ) ) {
				$placement = &$fields['reservation']['fields'];
			} else {
				$key = key( reset( $fields  ) );
				if ( isset( $fields[$key]['fields'] ) ) {
					$placement = &$fields[$key]['fields'];
				}
			}

			// If we couldn't find any working fieldset, then something odd is
			// going on. Just pretend we were never here.
			if ( $placement === false ) {
				return $fields;
			}

			$placement = array_merge(
				array(
					'location' => array(
						'title'			=> __( 'Location', 'restaurant-reservations' ),
						'request_input'	=> empty( $request->location ) ? '' : $request->location,
						'callback'		=> 'rtb_print_form_select_field',
						'callback_args'	=> array(
							'options'	=> $this->get_location_options(),
						),
						'empty_option'	=> true,
						'required'		=> true,
					)
				),
				$placement
			);

			return $fields;
		}

		/**
		 * Retrieve a key/value array of location terms and names
		 *
		 * @param bool $active_only Whether or not to retrieve only currently
		 *  active locations. Default: true - don't retrieve locations that
		 *  have been removed
		 * @since 1.6
		 */
		public function get_location_options( $active_only = true ) {

			$terms = get_terms(
				array(
					'taxonomy'   => $this->location_taxonomy,
					'hide_empty' => false,
				)
			);

			$options = array();
			foreach( $terms as $term ) {
				$archived = get_term_meta( $term->term_id, 'rtb_location_removed', true );
				if ( !$active_only || !$archived ) {
					$options[$term->term_id] = $term->name;
				}
			}

			return $options;
		}

		/**
		 * Validate location in post data
		 *
		 * @since 1.6
		 */
		public function validate_location( $booking ) {

			$booking->location = empty( $_POST['rtb-location'] ) ? '' : absint( $_POST['rtb-location'] );
			if ( empty( $booking->location ) ) {
				$booking->validation_errors[] = array(
					'field'			=> 'location',
					'post_variable'	=> $booking->location,
					'message'	=> __( 'Please select a location for your booking.', 'restaurant-reservations' ),
				);

			} elseif ( !term_exists( $booking->location, $this->location_taxonomy ) ) {
				$booking->validation_errors[] = array(
					'field'			=> 'location',
					'post_variable'	=> $booking->location,
					'message'	=> __( 'The location you selected is not valid. Please select another location.', 'restaurant-reservations' ),
				);
			}
		}

		/**
		 * Save the booking location when the booking is created or updated.
		 *
		 * @since 1.6
		 */
		public function save_booking_location( $booking ) {

			if ( !empty( $booking->location ) ) {
				wp_set_object_terms( $booking->ID, $booking->location, $this->location_taxonomy );
			}
		}

		/**
		 * Load the booking location when teh booking is loaded
		 *
		 * @since 1.6
		 */
		public function load_booking_location( $booking, $post ) {

			$terms = wp_get_object_terms( $booking->ID, $this->location_taxonomy, array( 'fields' => 'ids' ) );

			if ( is_a( $terms, 'WP_Error' ) ) {
				return;
			}

			$booking->location = current( $terms );
		}

		/**
		 * Add location column to the list table
		 *
		 * @since 1.6
		 */
		public function add_location_column( $columns ) {

			$first = array_splice( $columns, 0, 2 );
			$first['location'] = __( 'Location', 'restaurant-reservations' );

			return array_merge( $first, $columns );
		}

		/**
		 * Print the value in the location column for the list table
		 *
		 * @since 1.6
		 */
		public function print_location_column( $value, $booking, $column_name ) {

			if ( $column_name !== 'location' ) {
				return $value;
			}

			$terms = wp_get_object_terms( $booking->ID, $this->location_taxonomy );

			if ( empty( $terms ) || is_a( $terms, 'WP_Error' ) ) {
				return '';
			}

			$location = current( $terms );

			return $location->name;
		}

		/**
		 * Modify queries to add location taxonomy parameters
		 *
		 * @param array $args Array of arguments passed to rtbQuery
		 * @since 1.6
		 */
		public function modify_query( $args, $context = '' ) {

			global $rtb_controller;

			if ( !empty( $args['location'] ) && !empty( $rtb_controller->locations->post_type ) ) {

				if ( !is_array( $args['location'] ) ) {
					$args['location'] = array( $args['location'] );
				}

				$args['tax_query'] = array(
					array(
						'taxonomy' => $rtb_controller->locations->location_taxonomy,
						'field'    => 'term_id',
						'terms'    => $args['location'],

					)
				);
			}

			return $args;
		}

		/**
		 * Add meta box to the location post editing screen
		 *
		 * @since 1.6
		 */
		public function add_meta_boxes() {

			$meta_boxes = array(

				// Metabox to enter schema type
				array(
					'id'        => 'rtb_location',
					'title'     => __( 'Reservations', 'restaurant-reservations' ),
					'callback'  => array( $this, 'print_location_metabox' ),
					'post_type' => $this->post_type,
					'context'   => 'side',
					'priority'  => 'default',
				),
			);

			// Create filter so addons can modify the metaboxes
			$meta_boxes = apply_filters( 'rtb_location_metaboxes', $meta_boxes );

			// Create the metaboxes
			foreach ( $meta_boxes as $meta_box ) {
				add_meta_box(
					$meta_box['id'],
					$meta_box['title'],
					$meta_box['callback'],
					$meta_box['post_type'],
					$meta_box['context'],
					$meta_box['priority']
				);
			}
		}

		/**
		 * Output a hidden nonce field to secure the saving of term meta
		 *
		 * @since 1.6
		 */
		public function add_meta_nonce() {
			global $post;
			if ( $post->post_type == $this->post_type ) {
				wp_nonce_field( 'rtb_location_meta', 'rtb_location_meta_nonce' );
			}
		}

		/**
		 * Print metabox on location post editing screen
		 *
		 * @since 1.6
		 */
		public function print_location_metabox( $post ) {

			global $rtb_controller;

			$notification_email = '';
			$reply_to_name = '';
			$reply_to_address = '';
			$term_id = get_post_meta( $post->ID, $this->location_taxonomy, true );
			$admin_email_option = $rtb_controller->settings->get_setting( 'admin-email-option' );
			if ( $term_id ) {
				$reply_to_name = get_term_meta( $term_id, 'rtb_reply_to_name', true );
				$reply_to_address = get_term_meta( $term_id, 'rtb_reply_to_address', true );
				if ( $admin_email_option ) {
					$notification_email = get_term_meta( $term_id, 'rtb_admin_email_address', true );
				}
			}

			$append_booking_form = get_post_meta( $post->ID, 'rtb_append_booking_form', true );

			?>

			<style type="text/css">.rtb-location-meta-input + .rtb-location-meta-input { margin-top: 2em; }</style>

			<div class="rtb-location-meta-input rtb-location-meta-append-form">
				<label>
					<input type="checkbox" name="rtb_append_booking_form" value="1"<?php if ( $append_booking_form ) : ?> checked="checked"<?php endif; ?>>
					<?php esc_html_e( "Automatically add the booking form to this page.", 'restaurant-reservations' ); ?>
				</label>
			</div>

			<div class="rtb-location-meta-input rtb-location-meta-reply-to-name">
					<label for="rtb_reply_to_name">
						<?php esc_html_e( 'Reply-To Name', 'restaurant-reservations' ); ?>
					</label>
					<input type="text" name="rtb_reply_to_name" id="rtb_reply_to_name" value="<?php esc_attr_e( $reply_to_name ); ?>" placeholder="<?php esc_attr_e( $rtb_controller->settings->get_setting( 'reply-to-name' ) ); ?>">
					<p class="description">
						<?php esc_html_e( 'The name which should appear in the Reply-To field of a user notification email.', 'restaurant-reservations' ); ?>
					</p>
			</div>

			<div class="rtb-location-meta-input rtb-location-meta-reply-to-address">
					<label for="rtb_reply_to_address">
						<?php esc_html_e( 'Reply-To Email Address', 'restaurant-reservations' ); ?>
					</label>
					<input type="text" name="rtb_reply_to_address" id="rtb_reply_to_address" value="<?php esc_attr_e( $reply_to_address ); ?>" placeholder="<?php esc_attr_e( $rtb_controller->settings->get_setting( 'reply-to-address' ) ); ?>">
					<p class="description">
						<?php esc_html_e( 'The email address which should appear in the Reply-To field of a user notification email.', 'restaurant-reservations' ); ?>
					</p>
			</div>

			<?php if ( $admin_email_option ) : ?>
				<div class="rtb-location-meta-input rtb-location-meta-admin-email">
						<label for="rtb_admin_email_address">
							<?php esc_html_e( 'Admin Notification Email Address', 'restaurant-reservations' ); ?>
						</label>
						<input type="text" name="rtb_admin_email_address" id="rtb_admin_email_address" value="<?php esc_attr_e( $notification_email ); ?>" placeholder="<?php esc_attr_e( $rtb_controller->settings->get_setting( 'admin-email-address' ) ); ?>">
						<p class="description">
							<?php esc_html_e( 'The email address where admin notifications for bookings at this location should be sent.', 'restaurant-reservations' ); ?>
						</p>
				</div>
			<?php endif; ?>

			<?php
		}

		/**
		 * Append booking form to a location's `post_content`
		 * @since 0.0.1
		 */
		public function append_to_content( $content ) {

			if ( !is_main_query() || !in_the_loop() || post_password_required() ) {
				return $content;
			}

			global $post;

			$append_booking_form = get_post_meta( $post->ID, 'rtb_append_booking_form', true );

			if ( !$append_booking_form ) {
				return $content;
			}

			$term_id = get_post_meta( $post->ID, $this->location_taxonomy, true );

			if ( empty( $term_id ) ) {
				return $content;
			}

			return $content . do_shortcode( '[booking-form location=' . absint( $term_id ) .']' );
		}

		/**
		 * Modify the notification email recipient for each location
		 *
		 * @since 1.6
		 */
		public function notification_to_email( $email, $notification ) {

			if ( $notification->target == 'user' || empty( $notification->booking->location ) ) {
				return $email;
			}

			$val = get_term_meta( $notification->booking->location, 'rtb_admin_email_address', true );
			$email = empty( $val ) ? $email : $val;

			return $email;
		}

		/**
		 * Modify the notification email sender address for each location
		 *
		 * @since 1.6
		 */
		public function notification_from_email( $email, $notification ) {

			if ( $notification->target != 'user' || empty( $notification->booking->location ) ) {
				return $email;
			}

			$val = get_term_meta( $notification->booking->location, 'rtb_reply_to_address', true );
			$email = empty( $val ) ? $email : $val;

			return $email;
		}

		/**
		 * Modify the notification email sender name for each location
		 *
		 * @since 1.6
		 */
		public function notification_from_name( $name, $notification ) {

			if ( $notification->target != 'user' || empty( $notification->booking->location ) ) {
				return $name;
			}

			$val = get_term_meta( $notification->booking->location, 'rtb_reply_to_name', true );
			$name = empty( $val ) ? $name : $val;

			return $name;
		}

		/**
		 * Add a location template tag for notifications
		 *
		 * @since 1.6.1
		 */
		public function notification_template_tags( $template_tags, $notification ) {

			$term = empty( $notification->booking->location ) ? null : get_term( $notification->booking->location, $this->location_taxonomy );
			$location_name = is_null( $term ) || is_wp_error( $term ) ? '' : $term->name;

			$table_number = empty( $notification->booking->table ) ? '' : $notification->booking->table;
			$table_number = is_array($table_number) ? implode(',', $table_number ) : $table_number;

			return array_merge(
				array(
					'{location}' => $location_name,
					'{table}' => $table_number,
				),
				$template_tags
			);
		}

		/**
		 * Add a description for the location template tag
		 *
		 * @since 1.6.1
		 */
		public function notification_template_tag_descriptions( $descriptions ) {
			return array_merge(
				array( '{location}' => __( 'Location for which this booking was made.', 'restaurant-reservations' ) ),
				$descriptions
			);
		}

		/**
		 * Removes Auto-Draft locations that were added due to a bug in v1.7
		 *
		 * Version 1.7 introduced a bug which caused a location term to be
		 * created if the location Add New page was loaded. This term
		 * corresponded to an auto-draft post object and will be removed when
		 * that object is removed. This provides a one-time fix in v1.7.1
		 *
		 * @see https://github.com/NateWr/restaurant-reservations/issues/91
		 * @see https://developer.wordpress.org/reference/functions/wp_delete_auto_drafts/
		 * @since 1.7.1
		 */
		public function fix_autodraft_term_error() {

			if ( get_option( 'rtb_autodraft_terms_fixed', false ) ) {
				return;
			}

			global $wpdb;

			if ( !$wpdb ) {
				return;
			}

			$old_posts = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_status = 'auto-draft' AND post_type = '$this->post_type';" );
			foreach ( (array) $old_posts as $delete ) {
				// Force delete.
				wp_delete_post( $delete, true );
			}

			// Set the `rtb_location_removed` term meta on any terms that are
			// no longer attached to posts
			global $wp_version;
			if ( version_compare( $wp_version, '3.9', '>=' ) ) {
				$live_terms = $wpdb->get_col( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key='$this->location_taxonomy';" );
				$all_terms = get_terms( array(
					'taxonomy' => $this->location_taxonomy,
					'hide_empty' => false,
					'meta_query' => array(
						array(
							'compare' => 'NOT EXISTS',
							'key' => 'rtb_location_removed',
						)
					)
				) );
				if ( is_array( $all_terms ) ) {
					foreach( $all_terms as $term ) {
						if ( !in_array( $term->term_id, $live_terms ) ) {
							$query = new rtbQuery( array( 'location' => $term->term_id ), 'delete-location-term-check' );
							$query->prepare_args();
							$query->get_bookings();

							// Don't delete taxonomy terms if there are bookings assigned to
							// this location, so the booking associations can remain as
							// historical data.
							if ( count( $query->bookings ) ) {
								add_term_meta( $term->term_id, 'rtb_location_removed', true );
							} else {
								wp_delete_term( $term->term_id, $this->location_taxonomy );
							}
						}
					}
				}
			}

			update_option( 'rtb_autodraft_terms_fixed', true );
		}

		/**
		 * If multiple locations exist, adds a select box to the settings page which
		 * allows a user to select whether certain settings are global or location-specific.
		 * Also adds in location-specific settings for a number of different settings, and
		 * makes all settings conditional on the value of the select box.
		 *
		 * @since 2.3.6
		 */
		public function maybe_add_location_settings( $sap ) {
			global $rtb_controller;

			$args = array(
				'taxonomy'   => $this->location_taxonomy,
				'hide_empty' => false,
			);

			$terms = get_terms( $args );

			if ( ! $this->do_locations_exist() ) { return $sap; }

			foreach ( $sap->pages['rtb-settings']->sections as $key => $section ) {

				foreach ( $section->settings as $setting_key => $setting ) {

					$sap->pages['rtb-settings']->sections[ $key ]->settings[ $setting_key ]->conditional_on = 'location-select';
					$sap->pages['rtb-settings']->sections[ $key ]->settings[ $setting_key ]->conditional_on_value = false;

					$sap->pages['rtb-settings']->sections[ $key ]->settings[ $setting_key ]->set_conditional_display();
				}
			}


			$location_options = array(
				''		=> __( 'Global', 'restaurant-reservations' ),
			);

			foreach ( $terms as $term ) {

				$location_options[ $term->slug ] = $term->term_id . ' - '.$term->name;
			}

			// Schedule location-specific options
			$sap->add_section(
				'rtb-settings',
				array(
					'id'            => 'rtb-schedule-location-select',
					'title'         => __( 'Select Schedule Location', 'restaurant-reservations' ),
					'tab'	        => 'rtb-schedule-tab',
					'rank'          => 2,
				)
			);

			$sap->add_setting(
				'rtb-settings',
				'rtb-schedule-location-select',
				'select',
				array(
					'id'            => 'location-select',
					'title'         => __( 'Schedule Location', 'restaurant-reservations' ),
					'description'   => __( 'Select which location the schedule will apply to. If a specific location doesn\'t have a schedule set, then it will fall back to the global schedule when booking.', 'restaurant-reservations' ),
					'blank_option'	=> false,
					'options'       => $location_options,
				)
			);

			// Translateable strings for scheduler components
			$scheduler_strings = array(
				'add_rule'			=> __( 'Add new scheduling rule', 'restaurant-reservations' ),
				'weekly'			=> _x( 'Weekly', 'Format of a scheduling rule', 'restaurant-reservations' ),
				'monthly'			=> _x( 'Monthly', 'Format of a scheduling rule', 'restaurant-reservations' ),
				'date'				=> _x( 'Date', 'Format of a scheduling rule', 'restaurant-reservations' ),
				'weekdays'			=> _x( 'Days of the week', 'Label for selecting days of the week in a scheduling rule', 'restaurant-reservations' ),
				'month_weeks'		=> _x( 'Weeks of the month', 'Label for selecting weeks of the month in a scheduling rule', 'restaurant-reservations' ),
				'date_label'		=> _x( 'Date', 'Label to select a date for a scheduling rule', 'restaurant-reservations' ),
				'time_label'		=> _x( 'Time', 'Label to select a time slot for a scheduling rule', 'restaurant-reservations' ),
				'allday'			=> _x( 'All day', 'Label to set a scheduling rule to last all day', 'restaurant-reservations' ),
				'start'				=> _x( 'Start', 'Label for the starting time of a scheduling rule', 'restaurant-reservations' ),
				'end'				=> _x( 'End', 'Label for the ending time of a scheduling rule', 'restaurant-reservations' ),
				'set_time_prompt'	=> _x( 'All day long. Want to %sset a time slot%s?', 'Prompt displayed when a scheduling rule is set without any time restrictions', 'restaurant-reservations' ),
				'toggle'			=> _x( 'Open and close this rule', 'Toggle a scheduling rule open and closed', 'restaurant-reservations' ),
				'delete'			=> _x( 'Delete rule', 'Delete a scheduling rule', 'restaurant-reservations' ),
				'delete_schedule'	=> __( 'Delete scheduling rule', 'restaurant-reservations' ),
				'never'				=> _x( 'Never', 'Brief default description of a scheduling rule when no weekdays or weeks are included in the rule', 'restaurant-reservations' ),
				'weekly_always'	=> _x( 'Every day', 'Brief default description of a scheduling rule when all the weekdays/weeks are included in the rule', 'restaurant-reservations' ),
				'monthly_weekdays'	=> _x( '%s on the %s week of the month', 'Brief default description of a scheduling rule when some weekdays are included on only some weeks of the month. %s should be left alone and will be replaced by a comma-separated list of days and weeks in the following format: M, T, W on the first, second week of the month', 'restaurant-reservations' ),
				'monthly_weeks'		=> _x( '%s week of the month', 'Brief default description of a scheduling rule when some weeks of the month are included but all or no weekdays are selected. %s should be left alone and will be replaced by a comma-separated list of weeks in the following format: First, second week of the month', 'restaurant-reservations' ),
				'all_day'			=> _x( 'All day', 'Brief default description of a scheduling rule when no times are set', 'restaurant-reservations' ),
				'before'			=> _x( 'Ends at', 'Brief default description of a scheduling rule when an end time is set but no start time. If the end time is 6pm, it will read: Ends at 6pm', 'restaurant-reservations' ),
				'after'				=> _x( 'Starts at', 'Brief default description of a scheduling rule when a start time is set but no end time. If the start time is 6pm, it will read: Starts at 6pm', 'restaurant-reservations' ),
				'separator'			=> _x( '&mdash;', 'Separator between times of a scheduling rule', 'restaurant-reservations' ),
			);

			foreach ( $terms as $term ) {

				$sap->add_setting(
					'rtb-settings',
					'rtb-schedule',
					'scheduler',
					array(
						'id'					=> $term->slug . '-schedule-open',
						'title'					=> __( 'Schedule', 'restaurant-reservations' ),
						'description'			=> __( 'Define the weekly schedule during which you accept bookings.', 'restaurant-reservations' ),
						'weekdays'				=> array(
							'monday'				=> _x( 'Mo', 'Monday abbreviation', 'restaurant-reservations' ),
							'tuesday'				=> _x( 'Tu', 'Tuesday abbreviation', 'restaurant-reservations' ),
							'wednesday'				=> _x( 'We', 'Wednesday abbreviation', 'restaurant-reservations' ),
							'thursday'				=> _x( 'Th', 'Thursday abbreviation', 'restaurant-reservations' ),
							'friday'				=> _x( 'Fr', 'Friday abbreviation', 'restaurant-reservations' ),
							'saturday'				=> _x( 'Sa', 'Saturday abbreviation', 'restaurant-reservations' ),
							'sunday'				=> _x( 'Su', 'Sunday abbreviation', 'restaurant-reservations' )
						),
						'time_format'			=> $rtb_controller->settings->get_setting( 'time-format' ),
						'date_format'			=> $rtb_controller->settings->get_setting( 'date-format' ),
						'disable_weeks'			=> true,
						'disable_date'			=> true,
						'strings' 				=> $scheduler_strings,
						'conditional_on' 		=> 'location-select',
						'conditional_on_value'	=> $term->slug,
					)
				);

				$sap->add_setting(
					'rtb-settings',
					'rtb-schedule',
					'scheduler',
					array(
						'id'					=> $term->slug . '-schedule-closed',
						'title'					=> __( 'Exceptions', 'restaurant-reservations' ),
						'description'			=> __( "Define special opening hours for holidays, events or other needs. Leave the time empty if you're closed all day.", 'restaurant-reservations' ),
						'time_format'			=> esc_attr( $rtb_controller->settings->get_setting( 'time-format' ) ),
						'date_format'			=> esc_attr( $rtb_controller->settings->get_setting( 'date-format' ) ),
						'disable_weekdays'		=> true,
						'disable_weeks'			=> true,
						'strings' 				=> $scheduler_strings,
						'conditional_on' 		=> 'location-select',
						'conditional_on_value'	=> $term->slug,
					)
				);
			}

			// Restriction location-specific options
			$sap->add_section(
				'rtb-settings',
				array(
					'id'            => 'rtb-restrictions-location-select',
					'title'         => __( 'Select Seat Restrictions Location', 'restaurant-reservations' ),
					'tab'	        => 'rtb-premium',
					'rank'          => 11,
				)
			);

			$sap->add_setting(
				'rtb-settings',
				'rtb-restrictions-location-select',
				'select',
				array(
					'id'            => 'location-select',
					'title'         => __( 'Seat Restrictions Location', 'restaurant-reservations' ),
					'description'   => __( 'Select which location the restrictions will apply to. If a specific location doesn\'t have restrictions set, then the global total number will be used as a fall-back.', 'restaurant-reservations' ),
					'blank_option'	=> false,
					'options'       => $location_options,
				)
			);

			$max_reservation_options = array();
			$max_reservations_upper_limit = apply_filters( 'rtb-max-reservations-upper-limit', 100 );

			for ( $i = 1; $i <= $max_reservations_upper_limit; $i++ ) {

				$max_reservation_options[$i] = $i;
			}

			$max_people_options = array();
			$max_people_upper_limit = apply_filters( 'rtb-max-people-upper-limit', 400 );

			for ( $i = 1; $i <= $max_people_upper_limit; $i++ ) {

				$max_people_options[$i] = $i;
			}

			foreach ( $terms as $term ) {

				$sap->add_setting(
					'rtb-settings',
					'rtb-seat-assignments',
					'select',
					array(
						'id'					=> $term->slug . '-rtb-max-tables-count',
						'title'					=> __( 'Max Reservations', 'restaurant-reservations' ),
						'description'			=> __( 'How many reservations, if enabled, should be allowed at the same time at this location? Set dining block length to change how long a meal typically lasts.', 'restaurant-reservations' ),
						'options'				=> $max_reservation_options,
						'conditional_on' 		=> 'location-select',
						'conditional_on_value'	=> $term->slug,
					)
				);
		
				$sap->add_setting(
					'rtb-settings',
					'rtb-seat-assignments',
					'select',
					array(
						'id'      				=> $term->slug . '-rtb-max-people-count',
						'title'     			=> __( 'Max People', 'restaurant-reservations' ),
						'description'     		=> __( 'How many people, if enabled, should be allowed to be present at this restaurant location at the same time? Set dining block length to change how long a meal typically lasts. May not work correctly if max reservations is set.', 'restaurant-reservations' ),
						'options'				=> $max_people_options,
						'conditional_on' 		=> 'location-select',
						'conditional_on_value'	=> $term->slug,
					)
				);
			}

			return $sap;
		}

		/**
		 * Blank out the location setting, so that it's always set to 'Global'
		 * on page load, except for immediately after saving a setting.
		 *
		 * @since 2.3.6
		 */
		public function remove_location_select_setting() {
			global $rtb_controller;

			$rtb_controller->settings->set_setting( 'location-select', null );

			$rtb_controller->settings->save_settings();
		}

		/**
		 * Returns true if locations have been created, false otherwise
		 *
		 * @since 2.3.6
		 */
		public function do_locations_exist() {

			$args = array(
				'taxonomy'   => $this->location_taxonomy,
				'hide_empty' => false,
			);

			$terms = get_terms( $args );

			return ( ! empty( $terms ) and ! is_wp_error( $terms ) );
		}
	}
}
