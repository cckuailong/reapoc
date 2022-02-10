<?php
/**
 * Methods for our location custom post types.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2016, Theme of the Crop
 * @license   GPL-2.0+
 * @since     1.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpCustomPostTypes', false ) ) :

	/**
	 * Class to handle custom post type and post meta fields
	 *
	 * @since 1.1
	 */
	class bpfwpCustomPostTypes {

		/**
		 * Whether to load location CPTs or not
		 *
		 * @since  2.0
		 * @access public
		 * @var    boolean
		 */
		public $run_locations = false;

		/**
		 * Location post type slug
		 *
		 * @since  1.1
		 * @access public
		 * @var    string
		 */
		public $location_cpt_slug = 'location';

		/**
		 * Location post type slug
		 *
		 * @since  1.1
		 * @access public
		 * @var    string
		 */
		public $schema_cpt_slug = 'schema';

		/**
		 * Register hooks
		 *
		 * @since  1.1
		 * @access public
		 * @return void
		 */
		public function run( $run_locations = false) {
			$this->run_locations = $run_locations;

			add_action( 'init',                  array( $this, 'load_cpts' ) );
			add_action( 'add_meta_boxes',        array( $this, 'add_meta_boxes' ) );
			add_action( 'edit_form_after_title', array( $this, 'add_meta_nonce' ) );
			add_action( 'current_screen',        array( $this, 'maybe_flush_rewrite_rules' ) );
			add_action( 'the_content',           array( $this, 'append_to_content' ) );

			add_action( 'save_post_' . $this->location_cpt_slug,	array( $this, 'save_location_meta' ) );
			add_action( 'save_post_' . $this->schema_cpt_slug,		array( $this, 'save_schema_meta' ) );

			add_action( 'wp_ajax_bpfwp_get_schema_fields', array( $this, 'get_schema_fields' ) );
		}

		/**
		 * Register custom post types
		 *
		 * @since  1.1
		 * @access public
		 * @return void
		 */
		public function load_cpts() {
			
			// Define the location custom post type.
			$args = array(
				'labels' => array(
					'name'               => __( 'Locations',                   'business-profile' ),
					'singular_name'      => __( 'Location',                    'business-profile' ),
					'menu_name'          => __( 'Locations',                   'business-profile' ),
					'name_admin_bar'     => __( 'Locations',                   'business-profile' ),
					'add_new'            => __( 'Add New',                 	   'business-profile' ),
					'add_new_item'       => __( 'Add New Location',            'business-profile' ),
					'edit_item'          => __( 'Edit Location',               'business-profile' ),
					'new_item'           => __( 'New Location',                'business-profile' ),
					'view_item'          => __( 'View Location',               'business-profile' ),
					'view_items'         => __( 'View Locations',              'business-profile' ),
					'search_items'       => __( 'Search Locations',            'business-profile' ),
					'not_found'          => __( 'No locations found',          'business-profile' ),
					'not_found_in_trash' => __( 'No locations found in trash', 'business-profile' ),
					'all_items'          => __( 'Locations',               'business-profile' ),
				),
				'public'       => true,
				'show_in_menu' => 'bpfwp-business-profile',
				'show_in_rest' => true,
				'has_archive'  => true,
				'supports'     => array( 'title', 'editor', 'thumbnail' ),
			);

			$this->location_cpt_slug = apply_filters( 'bpfwp_location_cpt_slug', $this->location_cpt_slug );

			// Create filter so addons can modify the arguments.
			$args = apply_filters( 'bpfwp_location_cpt_args', $args );

			// Register the post type.
			if ( $this->run_locations ) { register_post_type( $this->location_cpt_slug, $args ); }

			// Define the schema custom post type.
			$args = array(
				'labels' => array(
					'name'               => __( 'Schemas',                   'business-profile' ),
					'singular_name'      => __( 'Schema',                    'business-profile' ),
					'menu_name'          => __( 'Schemas',                   'business-profile' ),
					'name_admin_bar'     => __( 'Schemas',                   'business-profile' ),
					'add_new'            => __( 'Add New',                 	   'business-profile' ),
					'add_new_item'       => __( 'Add New Schema',            'business-profile' ),
					'edit_item'          => __( 'Edit Schema',               'business-profile' ),
					'new_item'           => __( 'New Schema',                'business-profile' ),
					'view_item'          => __( 'View Schema',               'business-profile' ),
					'view_items'         => __( 'View Schemas',              'business-profile' ),
					'search_items'       => __( 'Search Schemas',            'business-profile' ),
					'not_found'          => __( 'No schemas found',          'business-profile' ),
					'not_found_in_trash' => __( 'No schemas found in trash', 'business-profile' ),
					'all_items'          => __( 'Schemas',               'business-profile' ),
				),
				'public'       => true,
				'show_ui'	   => true,
				'show_in_menu' => 'bpfwp-business-profile',
				'show_in_rest' => true,
				'has_archive'  => true,
				'supports'     => array( 'title' ),
			);

			$this->schema_cpt_slug = apply_filters( 'bpfwp_schema_cpt_slug', $this->schema_cpt_slug );

			// Create filter so addons can modify the arguments.
			$args = apply_filters( 'bpfwp_schema_cpt_args', $args );

			// Register the post type.
			register_post_type( $this->schema_cpt_slug, $args );
		}

		/**
		 * Flush the rewrite rules
		 *
		 * This should only be called on plugin activation.
		 *
		 * @since  1.1
		 * @access public
		 * @return void
		 */
		public function flush_rewrite_rules() {

			// Load CPTs before flushing, as recommended in the Codex.
			$this->load_cpts();

			flush_rewrite_rules();
		}

		/**
		 * Maybe flush the rewrite rules if the multiple locations option has
		 * been turned on.
		 *
		 * Should only be run on the Business Profile settings page
		 *
		 * @since  1.1
		 * @access public
		 * @param  string $current_screen The current admin screen slug.
		 * @return void
		 */
		public function maybe_flush_rewrite_rules( $current_screen ) {

			global $admin_page_hooks;
			if ( empty( $admin_page_hooks['bpfwp-locations'] ) || $current_screen->base !== $admin_page_hooks['bpfwp-locations'] . '_page_bpfwp-settings' ) {
				return;
			}

			if ( ! bpfwp_setting( 'multiple-locations' ) ) {
				return;
			}

			$rules = get_option( 'rewrite_rules' );
			if ( ! array_key_exists( $this->location_cpt_slug . '/?$', $rules ) ) {
				$this->flush_rewrite_rules();
			}
		}

		/**
		 * Add meta boxes when adding/editing locations
		 *
		 * @since  1.1
		 * @access public
		 * @return void
		 */
		public function add_meta_boxes() {

			$meta_boxes = array(

				// Metabox to enter schema type.
				array(
					'id'        => 'bpfwp_schema_metabox',
					'title'     => __( 'Schema Type', 'business-profile' ),
					'callback'  => array( $this, 'print_schema_metabox' ),
					'post_type' => $this->location_cpt_slug,
					'context'   => 'side',
					'priority'  => 'default',
				),

				// Metabox to enter phone number, contact email address and
				// select a contact page.
				array(
					'id'        => 'bpfwp_contact_metabox',
					'title'     => __( 'Contact Details', 'business-profile' ),
					'callback'  => array( $this, 'print_contact_metabox' ),
					'post_type' => $this->location_cpt_slug,
					'context'   => 'side',
					'priority'  => 'default',
				),

				// Metabox to enter opening hours.
				array(
					'id'        => 'bpfwp_opening_hours_metabox',
					'title'     => __( 'Opening Hours', 'business-profile' ),
					'callback'  => array( $this, 'print_opening_hours_metabox' ),
					'post_type' => $this->location_cpt_slug,
					'context'   => 'normal',
					'priority'  => 'default',
				),

				// Metabox to enter exceptions.
				array(
					'id'        => 'bpfwp_exceptions_metabox',
					'title'     => __( 'Exceptions', 'business-profile' ),
					'callback'  => array( $this, 'print_exceptions_metabox' ),
					'post_type' => $this->location_cpt_slug,
					'context'   => 'normal',
					'priority'  => 'default',
				),

				// Metabox to create Schema for specific post types, categories, etc.
				array(
					'id'        => 'bpfwp_schema_targeting_information',
					'title'     => __( 'Schema Details', 'business-profile' ),
					'callback'  => array( $this, 'print_schema_details_metabox' ),
					'post_type' => $this->schema_cpt_slug,
					'context'   => 'normal',
					'priority'  => 'high',
				),

			);

			// Create filter so addons can modify the metaboxes.
			$meta_boxes = apply_filters( 'bpfwp_meta_boxes', $meta_boxes );

			// Create the metaboxes.
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
		 * Output a hidden nonce field to secure the saving of post meta
		 *
		 * @since  1.1
		 * @access public
		 * @return void
		 */
		public function add_meta_nonce() {
			global $post;

			if ( $post->post_type === $this->location_cpt_slug ) {
				wp_nonce_field( 'bpfwp_location_meta', 'bpfwp_location_meta_nonce' );
			}

			if ( $post->post_type === $this->schema_cpt_slug ) {
				wp_nonce_field( 'bpfwp_schema_meta', 'bpfwp_schema_meta_nonce' );
			}
		}

		/**
		 * Output the metabox HTML to select a schema type
		 *
		 * @since  1.1
		 * @access public
		 * @param  WP_Post $post The current post object.
		 * @return void
		 */
		public function print_schema_metabox( $post ) {

			global $bpfwp_controller;
			$schema_types = $bpfwp_controller->settings->get_schema_types();
			$selected = bpfwp_setting( 'schema-type', $post->ID );

			// Fall back to general setting.
			if ( empty( $selected ) ) {
				$selected = bpfwp_setting( 'schema-type' );
			}
			?>

			<div class="bpfwp-meta-input bpfwp-meta-schema-type">
				<label for="bpfwp_schema-type">
					<?php esc_html_e( 'Schema type', 'business-profile' ); ?>
				</label>
				<select name="schema_type" id="bpfwp_schema-type" aria-describedby="bpfwp_schema-type_description">
					<?php foreach ( $schema_types as $key => $label ) : ?>
						<option value="<?php esc_attr_e( $key ); ?>"<?php if ( $selected === $key ) : ?> selected<?php endif; ?>>
							<?php esc_attr_e( $label ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<p class="description" id="bpfwp_schema-type_description">
					<?php esc_html_e( 'Select the option that best describes your business to improve how search engines understand your website.', 'business-profile' ); ?>
					<a href="http://schema.org/" target="_blank">Schema.org</a>
				</p>
			</div>

			<?php
		}

		/**
		 * Output the metabox HTML to enter a phone number,
		 * contact email address and select a contact page.
		 *
		 * @since  1.1
		 * @access public
		 * @param  WP_Post $post The current post object.
		 * @return void
		 */
		public function print_contact_metabox( $post ) {

			global $bpfwp_controller;

			// Address mimics HTML markup from Simple Admin Pages component.
			wp_enqueue_script( 'bpfwp-admin-location-address', BPFWP_PLUGIN_URL . '/lib/simple-admin-pages/js/address.js', array( 'jquery' ), BPFWP_VERSION );
			wp_localize_script(
				'bpfwp-admin-location-address',
				'sap_address',
				array(
					'api_key' => $bpfwp_controller->settings->get_setting( 'google-maps-api-key' ),
					'strings' => array(
						'no-setting'     => __( 'No map coordinates set.', 'business-profile' ),
						'sep-lat-lon'    => _x( ', ', 'separates latitude and longitude', 'business-profile' ),
						'retrieving'     => __( 'Requesting new coordinates', 'business-profile' ),
						'select'         => __( 'Select a match below', 'business-profile' ),
						'view'           => __( 'View', 'business-profile' ),
						'result_error'   => __( 'Error', 'business-profile' ),
						'result_invalid' => __( 'Invalid request. Be sure to fill out the address field before retrieving coordinates.', 'business-profile' ),
						'result_denied'  => __( 'Request denied.', 'business-profile' ),
						'result_limit'   => __( 'Request denied because you are over your request quota.', 'business-profile' ),
						'result_empty'   => __( 'Nothing was found at that address.', 'business-profile' ),
					),
				)
			);
			?>

			<div class="bpfwp-meta-input bpfwp-meta-geo_address sap-address">
				<textarea name="geo_address" id="bpfwp_address"><?php echo esc_textarea( get_post_meta( $post->ID, 'geo_address', true ) ); ?></textarea>
				<p class="sap-map-coords-wrapper">
					<span class="dashicons dashicons-location-alt"></span>
					<span class="sap-map-coords">
						<?php
						$geo_latitude = get_post_meta( $post->ID, 'geo_latitude', true );
						$geo_longitude = get_post_meta( $post->ID, 'geo_longitude', true );
						if ( empty( $geo_latitude ) || empty( $geo_longitude ) ) :
							esc_html_e( 'No map coordinates set.', 'business-profile' );
						else : ?>
							<?php echo esc_textarea( get_post_meta( $post->ID, 'geo_latitude', true ) ) . esc_html_x( ', ', 'separates latitude and longitude', 'business-profile' ) . esc_textarea ( get_post_meta( $post->ID, 'geo_longitude', true ) ); ?>
							<a href="//maps.google.com/maps?q=<?php echo esc_attr( get_post_meta( $post->ID, 'geo_latitude', true ) ) . ',' . esc_attr( get_post_meta( $post->ID, 'geo_longitude', true ) ); ?>" class="sap-view-coords" target="_blank"><?php esc_html_e( 'View', 'business-profile' ); ?></a>
						<?php
						endif; ?>
					</span>
				</p>
				<p class="sap-coords-action-wrapper">
					<a href="#" class="sap-get-coords">
						<?php esc_html_e( 'Retrieve map coordinates', 'business-profile' ); ?>
					</a>
					<?php echo esc_html_x( ' | ', 'separator between admin action links in address component', 'business-profile' ); ?>
					<a href="#" class="sap-remove-coords">
						<?php esc_html_e( 'Remove map coordinates', 'business-profile' ); ?>
					</a>
				</p>
				<input type="hidden" class="lat" name="geo_latitude" value="<?php echo esc_attr( get_post_meta( $post->ID, 'geo_latitude', true ) ); ?>">
				<input type="hidden" class="lon" name="geo_longitude" value="<?php echo esc_attr( get_post_meta( $post->ID, 'geo_longitude', true ) ); ?>">
			</div>

			<?php
				// Get an array of all pages with sane limits.
				$pages = array();
				$query = new WP_Query( array(
					'post_type'              => array( 'page' ),
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
					'posts_per_page'         => 500,
				) );
				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->next_post();
						$pages[ $query->post->ID ] = $query->post->post_title;
					}
				}
				wp_reset_postdata();
			?>

			<div class="bpfwp-meta-input bpfwp-meta-contact-page">
				<label for="bpfwp_contact-page">
					<?php esc_html_e( 'Contact Page', 'business-profile' ); ?>
				</label>
				<select name="contact_post" id="bpfwp_contact-page">
					<option></option>
					<?php foreach ( $pages as $id => $title ) : ?>
						<option value="<?php echo absint( $id ); ?>"<?php if ( get_post_meta( $post->ID, 'contact_post', true ) == $id ) : ?> selected<?php endif; ?>>
							<?php esc_attr_e( $title ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="bpfwp-meta-input bpfwp-meta-contact-email">
				<label for="bpfwp_contact-email">
					<?php esc_html_e( 'Email Address (optional)', 'business-profile' ); ?>
				</label>
				<input type="email" name="contact_email" id="bpfwp_contact-email" value="<?php esc_attr_e( get_post_meta( $post->ID, 'contact_email', true ) ); ?>">
			</div>

			<div class="bpfwp-meta-input bpfwp-meta-phone">
				<label for="bpfwp_phone">
					<?php esc_html_e( 'Phone Number', 'business-profile' ); ?>
				</label>
				<input type="tel" name="phone" id="bpfwp_phone" value="<?php esc_attr_e( get_post_meta( $post->ID, 'phone', true ) ); ?>">
			</div>

			<div class="bpfwp-meta-input bpfwp-meta-clickphone">
				<label for="bpfwp_clickphone">
					<?php esc_html_e( 'Click-to-Call Phone Number', 'business-profile' ); ?>
				</label>
				<input type="tel" name="clickphone" id="bpfwp_clickphone" value="<?php esc_attr_e( get_post_meta( $post->ID, 'clickphone', true ) ); ?>">
			</div>

			<?php
		}

		/**
		 * Output the metabox HTML to define opening hours
		 *
		 * @since  1.1
		 * @access public
		 * @param  WP_Post $post The current post object.
		 * @return void
		 */
		public function print_opening_hours_metabox( $post ) {

			$scheduler = $this->get_scheduler_meta_object( get_post_meta( $post->ID, 'opening_hours', true ) );

			// Load required scripts and styles.
			wp_enqueue_style( 'bpfwp-admin-location-sap', BPFWP_PLUGIN_URL . '/lib/simple-admin-pages/css/admin.css', array(), BPFWP_VERSION );
			foreach ( $scheduler->styles as $handle => $style ) {
				wp_enqueue_style( $handle, BPFWP_PLUGIN_URL . '/lib/simple-admin-pages/' . $style['path'], $style['dependencies'], $style['version'], $style['media'] );
			}
			foreach ( $scheduler->scripts as $handle => $script ) {
				wp_enqueue_script( $handle, BPFWP_PLUGIN_URL . '/lib/simple-admin-pages/' . $script['path'], $script['dependencies'], $script['version'], $script['footer'] );
			}
			?>

			<div class="bpfwp-meta-input bpfwp-meta-opening-hours">
				<?php $scheduler->display_setting(); ?>
			</div>

			<?php
		}


		/**
		 * Output the metabox HTML to define exceptions
		 *
		 * @since  1.1
		 * @access public
		 * @param  WP_Post $post The current post object.
		 * @return void
		 */
		public function print_exceptions_metabox( $post ) {

			$exceptions = $this->get_exceptions_meta_object( get_post_meta( $post->ID, 'exceptions', true ) );
			?>

			<div class="bpfwp-meta-input bpfwp-meta-exceptions">
				<?php $exceptions->display_setting(); ?>
			</div>

			<?php
		}

		/**
		 * Output the metabox HTML to customize a new schema post
		 *
		 * @since  2.0.0
		 * @access public
		 * @param  WP_Post $post The current post object.
		 * @return void
		 */
		public function print_schema_details_metabox( $post ) {
			global $bpfwp_controller;

			$post_is_set = isset($bpfwp_controller->schemas->schema_cpts[$post->ID]);

			$post_types = get_post_types( array( 'public' => true ), 'objects' );
			$posts = get_posts( array( 'numberposts' => 1000 ) );
			$pages = get_pages();
			$post_categories = get_categories();
			$taxonomies = get_taxonomies( array(), 'objects' );
			$page_templates = get_page_templates();

			$organization_schema_types = $bpfwp_controller->schemas->get_schema_organization_types();
			$rich_results_schema_types = $bpfwp_controller->schemas->get_schema_rich_results_types();
			
			$schema_fields = $post_is_set ? $bpfwp_controller->schemas->schema_cpts[$post->ID]->schema_class->fields : array();

			// Add in the schema selector script and pass post_type, post, page, etc. data to javascript
			wp_enqueue_script( 'bpfwp-admin-schema-selector', BPFWP_PLUGIN_URL . '/assets/js/admin-schema-selector.js', array( 'jquery' ), BPFWP_VERSION );
			wp_localize_script(
				'bpfwp-admin-schema-selector',
				'schema_option_data',
				array(
					'post_types' => $post_types,
					'posts' => $posts,
					'pages' => $pages,
					'post_categories' => $post_categories,
					'taxonomies' => $taxonomies,
					'page_templates' => $page_templates 
				)
			);

			$selected_target_type = $post_is_set ? $bpfwp_controller->schemas->schema_cpts[$post->ID]->target_type : '';
			$selected_target_value = $post_is_set ? $bpfwp_controller->schemas->schema_cpts[$post->ID]->target_value : '';
			$selected_schema = $post_is_set ? $bpfwp_controller->schemas->schema_cpts[$post->ID]->schema_type : '';
			$field_defaults = $post_is_set ? $bpfwp_controller->schemas->schema_cpts[$post->ID]->field_defaults : array();
			$default_display = $post_is_set ? $bpfwp_controller->schemas->schema_cpts[$post->ID]->default_display : false;

			$this->field_id = 0;

			?>

			<div class="bpfwp-meta-input bpfwp-meta-post_type">
				<label for="schema_target_type">
					<?php esc_html_e( 'Specify Target', 'business-profile' ); ?>
				</label>
				<select name="schema_target_type" class="no-margin">
					<option value='post_type' <?php if ( $selected_target_type == 'post_type' ) : ?> selected<?php endif; ?>><?php _e( 'Post Type', 'business-profile' ); ?></option>
					<option value='post' <?php if ( $selected_target_type == 'post' ) : ?> selected<?php endif; ?>><?php _e( 'Post', 'business-profile' ); ?></option>
					<option value='page' <?php if ( $selected_target_type == 'page' ) : ?> selected<?php endif; ?>><?php _e( 'Page', 'business-profile' ); ?></option>
					<?php // @to-do: add in the three target types below ?>
					<!-- <option value='post_category' <?php if ( $selected_target_type == 'post_category' ) : ?> selected<?php endif; ?>><?php _e( 'Post Category', 'business-profile' ); ?></option>
					<option value='taxonomy' <?php if ( $selected_target_type == 'taxonomy' ) : ?> selected<?php endif; ?>><?php _e( 'Taxonomy', 'business-profile' ); ?></option>
					<option value='page_template' <?php if ( $selected_target_type == 'page_template' ) : ?> selected<?php endif; ?>><?php _e( 'Page Template', 'business-profile' ); ?></option> -->
					<option value='global' <?php if ( $selected_target_type == 'global' ) : ?> selected<?php endif; ?>><?php _e( 'Global', 'business-profile' ); ?></option>
				</select>
				<select name="schema_target_value" class="no-margin">
					<?php 
					if ( $selected_target_type == 'post_type' or ! $selected_target_type ) {
						foreach ( $post_types as $post_type ) { ?>
							<option value='<?php echo esc_attr( $post_type->name ); ?>'<?php if ( $selected_target_value == $post_type->name ) : ?> selected<?php endif; ?>><?php echo esc_html( $post_type->label ); ?></option>
					<?php }
					}
					elseif ( $selected_target_type == 'post' ) {
						foreach ( $posts as $post ) { ?>
							<option value='<?php echo esc_attr( $post->ID ); ?>'<?php if ( $selected_target_value == $post->ID ) : ?> selected<?php endif; ?>><?php echo esc_html( $post->post_title ); ?></option>
					<?php }
					}
					elseif ( $selected_target_type == 'page' ) {
						foreach ( $pages as $page ) { ?>
							<option value='<?php echo esc_attr( $page->ID ); ?>'<?php if ( $selected_target_value == $page->ID ) : ?> selected<?php endif; ?>><?php echo esc_html( $page->post_title ); ?></option>
					<?php }
					} ?>
				</select>
			</div>

			<div class="bpfwp-meta-input bpfwp-meta-schema_type">
				<label for="schema_type">
					<?php esc_html_e( 'Schema Type', 'business-profile' ); ?>
				</label>
				<select name="schema_type">
					<option></option>
					<optgroup label="Organization Types">
						<?php foreach ( $organization_schema_types as $schema_slug => $schema_name ) : ?>
							<option value="<?php echo esc_attr( $schema_slug ); ?>"<?php if ( $selected_schema == $schema_slug ) : ?> selected<?php endif; ?>>
								<?php esc_attr_e( $schema_name ); ?>
							</option>
						<?php endforeach; ?>
					</optgroup>
					<optgroup label="Rich Results Types">
						<?php foreach ( $rich_results_schema_types as $schema_slug => $schema_name ) : ?>
							<option value="<?php echo esc_attr( $schema_slug ); ?>"<?php if ( $selected_schema == $schema_slug ) : ?> selected<?php endif; ?>>
								<?php esc_attr_e( $schema_name ); ?>
							</option>
						<?php endforeach; ?>
					</optgroup>
				</select>
			</div>

			<div class="bpfwp-schema-defaults-helper-background bpfwp-hidden"></div>
			<div class="bpfwp-schema-defaults-helper-box bpfwp-hidden">
				<div class="bpfwp-schema-defaults-helper-box-inside">
					<h3><?php esc_html_e( 'Available Default Values', 'business-profile' ); ?></h3>
					<?php $this->print_helper_box_select(); ?>
					<div class='bpfwp-schema-defaults-helper-box-exit'>x</div>
				</div>
			</div>

			<div class="bpfwp-meta-input bpfwp-meta-field_defaults">
				<label for="field_defaults" class="default-label">
					<?php esc_html_e( 'Defaults', 'business-profile' ); ?>
				</label>
				<br><br>
				<?php foreach ( $schema_fields as $field ) { ?>
					<?php $this->get_callback_input( $field, $field_defaults ); ?>
				<?php } ?>
			</div>

			<div class="bpfwp-meta-input bpfwp-default_display">
				<label for="default_display">
					<?php esc_html_e( 'Display For All Matching Items', 'business-profile' ); ?>
				</label>
				<input type="checkbox" name="default_display" <?php if ( $default_display ) : ?> checked<?php endif; ?>>
			</div>

			<?php
		}

		/**
		 * Outputs an input to allow editing of a field's default value
		 *
		 * @since  2.0.0
		 * @access public
		 * @param  bpfwpSchemaField $field The schema field.
		 * @param  array $field_defaults The default values for this Schema CPT.
		 * @return void
		 */
		public function get_callback_input( $field, $field_defaults, $field_prefix = '' ) {
			global $bpfwp_controller;

			if ( $field->input == 'SchemaField' ) {
				echo '<label for="field_defaults" class="bold-label">' . esc_html( $field->name ) . '</label>';
				echo '<div class="bpfwp-clear"></div>';
				echo '<div>';
				$field_prefix .= '_' . $field->slug;
				foreach ( $field->children as $child_field ) { $this->get_callback_input( $child_field, $field_defaults, $field_prefix ); }
				echo '</div>';
			}
			else {
				echo '<label for="field_defaults">' . esc_html( $field->name ) . '</label>';
				//echo '<div class="bpfwp-clear"></div>';
				echo '<input type="text" class="bpfwp-schema-defaults-field" name="field_defaults[' . esc_attr( $field_prefix ) . '_' . esc_attr( $field->slug ) .']" value="' . ( isset($field_defaults[$field_prefix . '_' . $field->slug]) ? esc_attr( $field_defaults[$field_prefix . '_' . $field->slug] ) : "" ) . '" placeholder="' . esc_attr( $field->callback ) . '" data-field_id="' . esc_attr( $this->field_id ) . '">';
				if ( $bpfwp_controller->settings->get_setting( 'schema-default-helpers' ) ) { echo '<span class="bpfwp-schema-defaults-helper dashicons dashicons-arrow-down-alt2" data-field_id="' . esc_attr( $this->field_id ) . '"></span>'; }
				echo '<div class="bpfwp-clear"></div>';

				$this->field_id++;
			}
		}

		/**
		 * Get a modified Scheduler object from the Simple Admin Pages library
		 *
		 * This modified scheduler is used to display and sanitize a scheduler
		 * component on the location post editing screen.
		 *
		 * @since  1.1
		 * @access public
		 * @see    lib/simple-admin-pages/classes/AdminPageSetting.Scheduler.class.php
		 * @param  string $values Optional values to be set.
		 * @return bpfwpSAPSchedulerMeta $scheduler An instance of the scheduler class.
		 */
		public function get_scheduler_meta_object( $values = null ) {

			require_once BPFWP_PLUGIN_DIR . '/includes/class-sap-scheduler-meta.php';
			$scheduler = new bpfwpSAPSchedulerMeta(
				array(
					'page'          => 'dummy_page', // Required but not used.
					'id'            => 'opening_hours',
					'title'         => __( 'Opening Hours', 'business-profile' ),
					'description'   => __( 'Define your weekly opening hours by adding scheduling rules.', 'business-profile' ),
					'weekdays'      => array(
						'monday'    => _x( 'Mo', 'Monday abbreviation', 'business-profile' ),
						'tuesday'   => _x( 'Tu', 'Tuesday abbreviation', 'business-profile' ),
						'wednesday' => _x( 'We', 'Wednesday abbreviation', 'business-profile' ),
						'thursday'  => _x( 'Th', 'Thursday abbreviation', 'business-profile' ),
						'friday'    => _x( 'Fr', 'Friday abbreviation', 'business-profile' ),
						'saturday'  => _x( 'Sa', 'Saturday abbreviation', 'business-profile' ),
						'sunday'    => _x( 'Su', 'Sunday abbreviation', 'business-profile' ),
					),
					'time_format'   => _x( 'h:i A', 'Time format displayed in the opening hours setting panel in your admin area. Must match formatting rules at http://amsul.ca/pickadate.js/time.htm#formats', 'business-profile' ),
					'date_format'   => _x( 'mmmm d, yyyy', 'Date format displayed in the opening hours setting panel in your admin area. Must match formatting rules at http://amsul.ca/pickadate.js/date.htm#formatting-rules', 'business-profile' ),
					'disable_weeks' => true,
					'disable_date'  => true,
					'strings'       => array(
						'add_rule'         => __( 'Add another opening time', 'business-profile' ),
						'weekly'           => _x( 'Weekly', 'Format of a scheduling rule', 'business-profile' ),
						'monthly'          => _x( 'Monthly', 'Format of a scheduling rule', 'business-profile' ),
						'date'             => _x( 'Date', 'Format of a scheduling rule', 'business-profile' ),
						'weekdays'         => _x( 'Days of the week', 'Label for selecting days of the week in a scheduling rule', 'business-profile' ),
						'month_weeks'      => _x( 'Weeks of the month', 'Label for selecting weeks of the month in a scheduling rule', 'business-profile' ),
						'date_label'       => _x( 'Date', 'Label to select a date for a scheduling rule', 'business-profile' ),
						'time_label'       => _x( 'Time', 'Label to select a time slot for a scheduling rule', 'business-profile' ),
						'allday'           => _x( 'All day', 'Label to set a scheduling rule to last all day', 'business-profile' ),
						'start'            => _x( 'Start', 'Label for the starting time of a scheduling rule', 'business-profile' ),
						'end'              => _x( 'End', 'Label for the ending time of a scheduling rule', 'business-profile' ),
						'set_time_prompt'  => _x( 'All day long. Want to %sset a time slot%s?', 'Prompt displayed when a scheduling rule is set without any time restrictions', 'business-profile' ),
						'toggle'           => _x( 'Open and close this rule', 'Toggle a scheduling rule open and closed', 'business-profile' ),
						'delete'           => _x( 'Delete rule', 'Delete a scheduling rule', 'business-profile' ),
						'delete_schedule'  => __( 'Delete scheduling rule', 'business-profile' ),
						'never'            => _x( 'Never', 'Brief default description of a scheduling rule when no weekdays or weeks are included in the rule', 'business-profile' ),
						'weekly_always'    => _x( 'Every day', 'Brief default description of a scheduling rule when all the weekdays/weeks are included in the rule', 'business-profile' ),
						'monthly_weekdays' => _x( '%s on the %s week of the month', 'Brief default description of a scheduling rule when some weekdays are included on only some weeks of the month. %s should be left alone and will be replaced by a comma-separated list of days and weeks in the following format: M, T, W on the first, second week of the month', 'business-profile' ),
						'monthly_weeks'    => _x( '%s week of the month', 'Brief default description of a scheduling rule when some weeks of the month are included but all or no weekdays are selected. %s should be left alone and will be replaced by a comma-separated list of weeks in the following format: First, second week of the month', 'business-profile' ),
						'all_day'          => _x( 'All day', 'Brief default description of a scheduling rule when no times are set', 'business-profile' ),
						'before'           => _x( 'Ends at', 'Brief default description of a scheduling rule when an end time is set but no start time. If the end time is 6pm, it will read: Ends at 6pm', 'business-profile' ),
						'after'            => _x( 'Starts at', 'Brief default description of a scheduling rule when a start time is set but no end time. If the start time is 6pm, it will read: Starts at 6pm', 'business-profile' ),
						'separator'        => _x( '&mdash;', 'Separator between times of a scheduling rule', 'business-profile' ),
					),
				)
			);

			if ( ! empty( $values ) ) {
				$scheduler->set_value( $values );
			}

			return $scheduler;

		}

		public function get_exceptions_meta_object( $values = null ) {

			require_once BPFWP_PLUGIN_DIR . '/includes/class-sap-scheduler-meta.php';
			$exceptions = new bpfwpSAPSchedulerMeta(
				array(
					'page'          	=> 'dummy_page', // Required but not used.
					'id'            	=> 'exceptions',
					'title'         	=> __( 'Exceptions', 'business-profile' ),
					'description'		=> __( "Define special opening hours for holidays, events or other needs. Leave the time empty if you're closed all day.", 'business-profile' ),
					'time_format'   	=> _x( 'h:i A', 'Time format displayed in the opening hours setting panel in your admin area. Must match formatting rules at http://amsul.ca/pickadate.js/time.htm#formats', 'business-profile' ),
					'date_format'		=> _x( 'mmmm d, yyyy', 'Date format displayed in the opening hours setting panel in your admin area. Must match formatting rules at http://amsul.ca/pickadate.js/date.htm#formatting-rules', 'business-profile' ),
					'disable_weekdays'	=> true,
					'disable_weeks'		=> true,
					'strings'       => array(
						'add_rule'         => __( 'Add another exception', 'business-profile' ),
						'weekly'           => _x( 'Weekly', 'Format of a scheduling rule', 'business-profile' ),
						'monthly'          => _x( 'Monthly', 'Format of a scheduling rule', 'business-profile' ),
						'date'             => _x( 'Date', 'Format of a scheduling rule', 'business-profile' ),
						'weekdays'         => _x( 'Days of the week', 'Label for selecting days of the week in a scheduling rule', 'business-profile' ),
						'month_weeks'      => _x( 'Weeks of the month', 'Label for selecting weeks of the month in a scheduling rule', 'business-profile' ),
						'date_label'       => _x( 'Date', 'Label to select a date for a scheduling rule', 'business-profile' ),
						'time_label'       => _x( 'Time', 'Label to select a time slot for a scheduling rule', 'business-profile' ),
						'allday'           => _x( 'All day', 'Label to set a scheduling rule to last all day', 'business-profile' ),
						'start'            => _x( 'Start', 'Label for the starting time of a scheduling rule', 'business-profile' ),
						'end'              => _x( 'End', 'Label for the ending time of a scheduling rule', 'business-profile' ),
						'set_time_prompt'  => _x( 'All day long. Want to %sset a time slot%s?', 'Prompt displayed when a scheduling rule is set without any time restrictions', 'business-profile' ),
						'toggle'           => _x( 'Open and close this rule', 'Toggle a scheduling rule open and closed', 'business-profile' ),
						'delete'           => _x( 'Delete rule', 'Delete a scheduling rule', 'business-profile' ),
						'delete_schedule'  => __( 'Delete scheduling rule', 'business-profile' ),
						'never'            => _x( 'Never', 'Brief default description of a scheduling rule when no weekdays or weeks are included in the rule', 'business-profile' ),
						'weekly_always'    => _x( 'Every day', 'Brief default description of a scheduling rule when all the weekdays/weeks are included in the rule', 'business-profile' ),
						'monthly_weekdays' => _x( '%s on the %s week of the month', 'Brief default description of a scheduling rule when some weekdays are included on only some weeks of the month. %s should be left alone and will be replaced by a comma-separated list of days and weeks in the following format: M, T, W on the first, second week of the month', 'business-profile' ),
						'monthly_weeks'    => _x( '%s week of the month', 'Brief default description of a scheduling rule when some weeks of the month are included but all or no weekdays are selected. %s should be left alone and will be replaced by a comma-separated list of weeks in the following format: First, second week of the month', 'business-profile' ),
						'all_day'          => _x( 'Closed all day', 'Brief default description of a scheduling exception when no times are set', 'business-profile' ),
						'before'           => _x( 'Ends at', 'Brief default description of a scheduling rule when an end time is set but no start time. If the end time is 6pm, it will read: Ends at 6pm', 'business-profile' ),
						'after'            => _x( 'Starts at', 'Brief default description of a scheduling rule when a start time is set but no end time. If the start time is 6pm, it will read: Starts at 6pm', 'business-profile' ),
						'separator'        => _x( '&mdash;', 'Separator between times of a scheduling rule', 'business-profile' ),
					),
				)
			);

			if ( ! empty( $values ) ) {
				$exceptions->set_value( $values );
			}

			return $exceptions;

		}

		/**
		 * Sanitize and save the location post meta
		 *
		 * The actual sanitization and validation should be
		 * performed in a bpfwpLocation object which will
		 * handle all the location data, and perform loading
		 * and saving.
		 *
		 * @since  1.1
		 * @access public
		 * @param  int $post_id The current post ID.
		 * @return int $post_id The current post ID.
		 */
		public function save_location_meta( $post_id ) {
			if ( ! isset( $_POST['bpfwp_location_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bpfwp_location_meta_nonce'] ), 'bpfwp_location_meta' ) ) { // Input var okay.
				return $post_id;
			}


			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			$post_meta = array(
				'schema_type'   	=> 'sanitize_text_field',
				'geo_address'   	=> 'wp_kses_post',
				'geo_latitude'  	=> 'sanitize_text_field',
				'geo_longitude' 	=> 'sanitize_text_field',
				'phone'         	=> 'sanitize_text_field',
				'clickphone'		=> 'sanitize_text_field',
				'contact_post'  	=> 'absint',
				'contact_email' 	=> 'sanitize_email',
				'opening_hours' 	=> array( $this, 'sanitize_opening_hours' ),
				'exceptions' 		=> array( $this, 'sanitize_exceptions' ),
			);

			foreach ( $post_meta as $key => $sanitizer ) {

				if ( ! isset( $_POST[ $key ] ) ) { // Input var okay.
					$_POST[ $key ] = '';
				}

				$cur = get_post_meta( $post_id, $key, true );
				$new = call_user_func( $sanitizer, wp_unslash( $_POST[ $key ] ) ); // Input var okay.

				if ( $new !== $cur ) {
					update_post_meta( $post_id, $key, $new );
				}
			}

			return $post_id;
		}

		/**
		 * Sanitize and save the schema post meta
		 *
		 *
		 * @since  2.0.0
		 * @access public
		 * @param  int $post_id The current post ID.
		 * @return int $post_id The current post ID.
		 */
		public function save_schema_meta( $post_id ) {
			if ( ! isset( $_POST['bpfwp_schema_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bpfwp_schema_meta_nonce'] ), 'bpfwp_schema_meta' ) ) { // Input var okay.
				return $post_id;
			}


			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			$post_meta = array(
				'schema_target_type'   	=> 'sanitize_text_field',
				'schema_target_value'   => 'sanitize_text_field',
				'schema_type'  			=> 'sanitize_text_field',
				'field_defaults' 		=> 'sanitize_text_field',
				'default_display'		=> 'sanitize_text_field'
			);

			$post_meta_array = array();
			foreach ( $post_meta as $key => $sanitizer ) {

				if ( ! isset( $_POST[ $key ] ) ) { // Input var okay.
					$_POST[ $key ] = '';
				}

				if ( is_array($_POST[ $key ]) ) { $value = array_map( $sanitizer, $_POST[ $key ] ); }
				else { $value = call_user_func( $sanitizer, wp_unslash( $_POST[ $key ] ) ); } // Input var okay.

				$post_meta_array[$key] = $value;
			}

			update_post_meta( $post_id, 'bpfwp-schema-data', $post_meta_array );

			return $post_id;
		}

		/**
		 * Sanitize opening hours
		 *
		 * This is a wrapper for the sanitization callback in the Scheduler
		 * component of Simple Admin Pages
		 *
		 * @since 1.1
		 * @access public
		 * @see    lib/simple-admin-pages/classes/AdminPageSetting.Scheduler.class.php
		 * @param  array $values Raw values for the opening hours.
		 * @return array $values Sanitized values for the opening hours.
		 */
		public function sanitize_opening_hours( $values ) {
			$scheduler = $this->get_scheduler_meta_object( $values );
			return $scheduler->sanitize_callback_wrapper( $values );
		}

		/**
		 * Sanitize exceptions
		 *
		 * This is a wrapper for the sanitization callback in the Scheduler
		 * component of Simple Admin Pages
		 *
		 * @since 1.1
		 * @access public
		 * @see    lib/simple-admin-pages/classes/AdminPageSetting.Scheduler.class.php
		 * @param  array $values Raw values for the exceptions.
		 * @return array $values Sanitized values for the exceptions.
		 */
		public function sanitize_exceptions( $values ) {
			$exceptions = $this->get_exceptions_meta_object( $values );
			return $exceptions->sanitize_callback_wrapper( $values );
		}

		/**
		 * Automatically append a contact card to `the_content` on location
		 * single pages
		 *
		 * @since  1.1
		 * @access public
		 * @param  string $content The current WordPress content.
		 * @return string $content The modified WordPress content.
		 */
		public function append_to_content( $content ) {

			if ( ! is_main_query() || ! in_the_loop() || post_password_required() ) {
				return $content;
			}

			global $bpfwp_controller;

			if ( $bpfwp_controller->get_theme_support( 'disable_append_to_content' ) ) {
				return $content;
			}

			global $post;

			if ( ! $post instanceof WP_Post || $post->post_type !== $bpfwp_controller->cpts->location_cpt_slug ) {
				return $content;
			}

			return $content . '[contact-card location=' . $post->ID . ' show_name=0]';
		}

		/**
		 * Returns the schema fields for a particular Schema CPT
		 *
		 * @since  2.0.0
		 * @access public
		 */
		public function get_schema_fields() {

		}

		/**
		 * Prints out the different functions, options and metas that can be used as default values for the various schemas
		 *
		 * @since  2.0.0
		 * @access public
		 */
		public function print_helper_box_select() { 

			?>
			<div class="bpfwp-schema-defaults-helper-container">
				<h4><?php esc_html_e( 'Functions', 'business-profile' ); ?></h4>
					<div class="bpfwp-schema-defaults-helper-functions-container">
						<?php $this->print_helper_options( 'function', $this->get_helper_function_options() ); ?>
					</div>
				<h4><?php esc_html_e( 'Options', 'business-profile' ); ?></h4>
					<div class="bpfwp-schema-defaults-helper-options-container">
						<?php $this->print_helper_options( 'option', $this->get_helper_option_options() ); ?>
					</div>
				<h4><?php esc_html_e( 'Meta', 'business-profile' ); ?></h4>
					<div class="bpfwp-schema-defaults-helper-metas-container">
						<?php $this->print_helper_options( 'meta', $this->get_helper_meta_options() ); ?>
					</div>
			</div>

			<?php
		}

		/**
		 * Prints out the supplied options that can be used as default values for the various schemas
		 *
		 * @since  2.0.0
		 * @access public
		 * @param  array $options The functions, options or metas that should be displayed.
		 */
		public function print_helper_options( $operation, $options ) { 
			
			foreach ($options as $option) { ?>
			<div class="bpfwp-schema-defaults-helper-option" data-helper_value="<?php echo esc_attr( $operation . ' ' . $option['value'] ); ?>">
				<div class="bpfwp-schema-defaults-helper-option-name"><?php echo esc_html( $option['display_name'] ); ?></div>
				<div class="bpfwp-schema-defaults-helper-option-description"><?php echo esc_html( $option['description'] ); ?></div>
			</div>
			<div class="bpfwp-clear"></div>

			<?php } 
		}

		/**
		 * Returns out the different functions that can be used as default values for the various schemas
		 *
		 * @since  2.0.0
		 * @access public
		 * @return array $helper_functions The available functions that can be selected.
		 */
		public function get_helper_function_options() { 

			$helper_functions = array(
				array(
					'section'		=> 'default',
					'display_name'	=> 'Title',
					'value'			=> 'get_the_title',
					'description'	=> 'Gets the title of the post'
				),
				array(
					'section'		=> 'default',
					'display_name'	=> 'Excerpt',
					'value'			=> 'get_the_excerpt',
					'description'	=> 'Gets the post excerpt'
				),
				array(
					'section'		=> 'default',
					'display_name'	=> 'Post Date',
					'value'			=> 'get_the_date',
					'description'	=> 'Gets the date the post was written'
				),
				array(
					'section'		=> 'default',
					'display_name'	=> 'Modified Date',
					'value'			=> 'get_the_modified_date',
					'description'	=> 'Gets the date the post was last modified'
				), 
				array(
					'section'		=> 'default',
					'display_name'	=> 'Post Date/Time',
					'value'			=> 'get_post_datetime',
					'description'	=> 'Gets the date-time the post was written'
				),
				array(
					'section'		=> 'default',
					'display_name'	=> 'Content',
					'value'			=> 'get_the_content',
					'description'	=> 'Gets the entire content of a post'
				),
				array(
					'section'		=> 'bp_default',
					'display_name'	=> 'Image URL',
					'value'			=> 'bpfwp_get_post_image_url',
					'description'	=> 'Gets the URL of the featured image of a post'
				),
				array(
					'section'		=> 'bp_default',
					'display_name'	=> 'Logo URL',
					'value'			=> 'bpfwp_get_site_logo_url',
					'description'	=> 'Gets the URL of the logo for the site'
				),
				array(
					'section'		=> 'bp_default',
					'display_name'	=> 'Logo Width',
					'value'			=> 'bpfwp_get_site_logo_width',
					'description'	=> 'Gets the height of the site\'s logo in pixels'
				),
				array(
					'section'		=> 'bp_default',
					'display_name'	=> 'Logo Height',
					'value'			=> 'bpfwp_get_site_logo_height',
					'description'	=> 'Gets the width of the site\'s logo in pixels'
				),
				array(
					'section'		=> 'woocommerce',
					'display_name'	=> 'WC New Review Rating',
					'value'			=> 'bpfwp_wc_get_most_recent_review_rating',
					'description'	=> 'Gets the most recent rating for a WooCommerce product'
				),
				array(
					'section'		=> 'woocommerce',
					'display_name'	=> 'WC New Review Body',
					'value'			=> 'bpfwp_wc_get_most_recent_review_body',
					'description'	=> 'Gets body of the most recent WooCommerce product review'
				),
				array(
					'section'		=> 'woocommerce',
					'display_name'	=> 'WC New Review Author',
					'value'			=> 'bpfwp_wc_get_most_recent_review_author',
					'description'	=> 'Gets display name of the most recent WooCommerce product review author'
				)
			);

			return apply_filters( 'bpfwp-helper-function-options', $helper_functions );
		}

		/**
		 * Returns out the different options that can be used as default values for the various schemas
		 *
		 * @since  2.0.0
		 * @access public
		 * @return array $helper_options The available options that can be selected.
		 */
		public function get_helper_option_options() { 

			$helper_options = array(
				array(
					'section'		=> 'default',
					'display_name'	=> 'Blog Name',
					'value'			=> 'blogname',
					'description'	=> 'Gets the name of the website set via the Settings menu'
				),
				array(
					'section'		=> 'default',
					'display_name'	=> 'Description',
					'value'			=> 'blogdescription',
					'description'	=> 'Gets the description of the website set via the Settings menu'
				),
				array(
					'section'		=> 'default',
					'display_name'	=> 'Site URL',
					'value'			=> 'siteurl',
					'description'	=> 'Gets the main URL for the website'
				),
				array(
					'section'		=> 'default',
					'display_name'	=> 'Admin EmailL',
					'value'			=> 'admin_email',
					'description'	=> 'Gets the administrator\'s email address'
				)
			);

			return apply_filters( 'bpfwp-helper-option-options', $helper_options );
		}

		/**
		 * Returns out the different metas that can be used as default values for the various schemas
		 *
		 * @since  2.0.0
		 * @access public
		 * @return array $helper_metas The available metas that can be selected.
		 */
		public function get_helper_meta_options() { 

			$helper_metas = array(
				array(
					'section'		=> 'woocommerce',
					'display_name'	=> 'Product SKU',
					'value'			=> '_sku',
					'description'	=> 'Gets the SKU of a WooCommerce product'
				),
				array(
					'section'		=> 'woocommerce',
					'display_name'	=> 'WC Average Rating',
					'value'			=> '_wc_average_rating',
					'description'	=> 'Gets the average rating for a WooCommerce product\'s reviews'
				),
				array(
					'section'		=> 'woocommerce',
					'display_name'	=> 'WC Review Count',
					'value'			=> '_wc_review_count',
					'description'	=> 'Gets the total number of reviews for a WooCommerce product'
				),
				array(
					'section'		=> 'woocommerce',
					'display_name'	=> 'Price',
					'value'			=> '_price',
					'description'	=> 'Gets the price of a WooCommerce product'
				),
				array(
					'section'		=> 'woocommerce',
					'display_name'	=> 'Sale Price Valid Until',
					'value'			=> '_sale_price_dates_to',
					'description'	=> 'Gets the end date for the sale price of a WooCommerce product'
				),
				array(
					'section'		=> 'woocommerce',
					'display_name'	=> 'Stock Status',
					'value'			=> '_stock_status',
					'description'	=> 'Gets the inventory status of a WooCommerce product'
				)
			);

			return apply_filters( 'bpfwp-helper-meta-options', $helper_metas );
		}
	}
endif;
