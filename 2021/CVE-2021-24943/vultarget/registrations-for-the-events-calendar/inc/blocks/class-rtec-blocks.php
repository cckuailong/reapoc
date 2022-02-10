<?php
/**
 * Registration form block with live preview.
 *
 * @since 2.14
 */
class RTEC_Blocks {

	/**
	 * Indicates if current integration is allowed to load.
	 *
	 * @since 2.14
	 *
	 * @return bool
	 */
	public function allow_load() {
		return function_exists( 'register_block_type' );
	}

	/**
	 * Loads an integration.
	 *
	 * @since 2.14
	 */
	public function load() {
		$this->hooks();
	}

	/**
	 * Integration hooks.
	 *
	 * @since 2.14
	 */
	protected function hooks() {
		add_action( 'init', array( $this, 'register_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		add_filter( 'rtec_event_meta', array( $this, 'block_editor_event_meta_changes' ), 99, 1 );
	}

	/**
	 * Register Registrations for the Events Calendar Gutenberg block on the backend.
	 *
	 * @since 2.14
	 */
	public function register_block() {
		if ( ! class_exists( 'Tribe__Main' ) ) {
			return;
		}

		wp_register_style(
			'rtec-blocks-styles',
			trailingslashit( RTEC_PLUGIN_URL ) . 'css/rtec-blocks.css',
			array( 'wp-edit-blocks' ),
			RTEC_VERSION
		);

		$attributes = array(
			'shortcodeSettings' => array(
				'type' => 'string',
			),
			'eventID' => array(
				'type' => 'string',
			),
			'isTribeEvent' => array(
				'type' => 'boolean',
			),
			'noNewChanges' => array(
				'type' => 'boolean',
			),
			'executed' => array(
				'type' => 'boolean',
			)
		);

		register_block_type(
			'rtec/rtec-form-block',
			array(
				'attributes'      => $attributes,
				'render_callback' => array( $this, 'get_form_html' ),
			)
		);
	}

	/**
	 * Load Registrations for the Events Calendar Gutenberg block scripts.
	 *
	 * @since 2.14
	 */
	public function enqueue_block_editor_assets() {
		if ( ! function_exists( 'tribe_get_start_date' ) ) {
			return;
		}
		rtec_scripts_and_styles( true );

		wp_enqueue_style( 'rtec-blocks-styles' );
		wp_enqueue_script(
			'rtec-form-block',
			trailingslashit( RTEC_PLUGIN_URL ) . 'js/rtec-blocks.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
			RTEC_VERSION,
			true
		);

		$post_type = defined('Tribe__Events__Main::POSTTYPE') ? Tribe__Events__Main::POSTTYPE : 'tribe_events';
		global $rtec_options;
		$args = array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => 50,
			'orderby'        => 'meta_value',
			'order'          => 'ASC'
		);
		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'relation' => 'AND',
				array(
					'key'     => '_EventStartDate',
					'value'   => date( 'Y-m-d H:i', time() + rtec_get_utc_offset() ),
					'compare' => '>=',
					'type'    => 'DATE'
				)
			)
		);

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

		$upcoming_posts = get_posts( $args );
		$upcoming_event_array = array( array(
			'id' => 0,
			'title' => __( 'Click here', 'registrations-for-the-events-calendar' ),
		));
		if ( ! empty( $upcoming_posts ) ) {
			foreach ( $upcoming_posts as $post ) {
				$upcoming_event_array[] = array(
					'id' => $post->ID,
					'title' => $post->post_title . ' (' . tribe_get_start_date( $post->ID, false ) . ')',
				);
			}
		}

		$shortcodeSettings = '';

		$i18n = array(
			'registration'        => esc_html__( 'Registration', 'registrations-for-the-events-calendar' ),
			'addSettings'         => esc_html__( 'Add Settings', 'registrations-for-the-events-calendar' ),
			'shortcodeSettings'   => esc_html__( 'Shortcode Settings', 'registrations-for-the-events-calendar' ),
			'example'             => esc_html__( 'Example', 'registrations-for-the-events-calendar' ),
			'preview'             => esc_html__( 'Apply Changes', 'registrations-for-the-events-calendar' ),
			'whichevent'          => esc_html__( 'Choose an event', 'registrations-for-the-events-calendar' ),
		);

		wp_localize_script(
			'rtec-form-block',
			'rtec_block_editor',
			array(
				'wpnonce'  => wp_create_nonce( 'rtec-blocks' ),
				'canShowFeed' => true,
				'upcoming'            => $upcoming_event_array,
				'shortcodeSettings'    => $shortcodeSettings,
				'i18n'     => $i18n,
			)
		);
	}

	/**
	 * Get form HTML to display in a Registrations for the Events Calendar Gutenberg block.
	 *
	 * @param array $attr Attributes passed by Registrations for the Events Calendar Gutenberg block.
	 *
	 * @since 2.14
	 *
	 * @return string
	 */
	public function get_form_html( $attr ) {

		$return = '';

		$shortcode_settings = isset( $attr['shortcodeSettings'] ) ? $attr['shortcodeSettings'] : '';
		$is_tribe_event = isset( $attr['isTribeEvent'] ) ? $attr['isTribeEvent'] : false;
		$event_id = ! empty( $attr['eventID'] ) ? $attr['eventID'] : false;

		if ( empty( $event_id ) ) {
			global $post;
			$event_id = $post->ID;
		}

		if ( $is_tribe_event ) {
			$shortcode_settings = 'tribe_flag=true ' . $shortcode_settings;
		}

		if ( $event_id ) {
			$shortcode_settings = 'event='.$event_id. ' '. $shortcode_settings;
		}


		$shortcode_settings = str_replace(array( '[rtec-registration-form', ']' ), '', $shortcode_settings );

		$return .= do_shortcode( '[rtec-registration-form '.$shortcode_settings.']' );

		return $return;

	}

	public function block_editor_event_meta_changes( $event_meta ) {
		if ( ! RTEC_Blocks::is_gb_editor() ) {
			return $event_meta;
		}

		$event_meta['registration_deadline'] = time() + 2000;
		$event_meta['registrations_disabled'] = false;

		return $event_meta;
	}

	/**
	 * Checking if is Gutenberg REST API call.
	 *
	 * @since 2.14
	 *
	 * @return bool True if is Gutenberg REST API call.
	 */
	public static function is_gb_editor() {

		// TODO: Find a better way to check if is GB editor API call.
		return defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context']; // phpcs:ignore
	}

}
