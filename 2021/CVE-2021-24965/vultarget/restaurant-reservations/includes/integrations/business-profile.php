<?php if ( !defined( 'ABSPATH' ) ) exit;
/**
 * Business Profile: Enable multi-location support in Restaurant Reservations
 * when locations are activated in Business Profile
 *
 * @param string $post_type The post type to use for locations
 * @since 1.6
 */
function rtb_bp_maybe_enable_bp_locations( $post_type ) {

	// Don't override a post type that's already been set
	if ( $post_type ) {
		return $post_type;
	}

	global $bpfwp_controller;
	if ( isset( $bpfwp_controller ) && isset( $bpfwp_controller->cpts ) && $bpfwp_controller->settings->get_setting( 'multiple-locations' ) ) {
		return $bpfwp_controller->cpts->location_cpt_slug;
	}

	return $post_type;
}
add_filter( 'rtb_set_locations_post_type', 'rtb_bp_maybe_enable_bp_locations' );

/**
 * Business Profile: Add a default display setting for the booking link
 *
 * @param array $defaults Array of display settings
 * @since 1.6
 */
function rtb_bp_booking_link_default( $defaults ) {

	$defaults['show_booking_link'] = true;

	return $defaults;
}
add_filter( 'bpfwp_default_display_settings','rtb_bp_booking_link_default' );

/**
 * Business Profile: Add callback to print the booking link in contact cards
 *
 * @param array $data Key/value list of callbacks for printing card details
 * @since 1.6
 */
function rtb_bp_add_booking_link_callback( $data ) {

	global $rtb_controller;
	$booking_page = $rtb_controller->settings->get_setting( 'booking-page' );

	if ( !empty( $booking_page ) ) {

		// Place the link at the end of other short links if they're
		// displayed
		if ( isset( $data['contact'] ) ) {
			$pos = array_search( 'contact', array_keys( $data ) );
		} elseif ( isset( $data['phone'] ) ) {
			$pos = array_search( 'phone', array_keys( $data ) );
		} elseif ( isset( $data['address'] ) ) {
			$pos = array_search( 'address', array_keys( $data ) );
		}

		if ( !empty( $pos ) ) {
			$a = array_slice( $data, 0, $pos );
			$b = array_slice( $data, $pos );
			$data = array_merge( $a, array( 'booking_page' => 'rtb_bp_print_booking_link' ), $b );
		} else {
			// If no short links are being displayed, just add it to the bottom.
			$data['booking_page'] = 'rtb_bp_print_booking_link';
		}
	}

	return $data;
}
add_filter( 'bpwfwp_component_callbacks', 'rtb_bp_add_booking_link_callback' );

/**
 * Print the booking link
 *
 * @param bool|int $location Optional location post ID being displayed
 * @since 1.6
 */
function rtb_bp_print_booking_link( $location = false ) {

	global $rtb_controller;

	$booking_page = $rtb_controller->settings->get_setting( 'booking-page'  );

	if ( $location && get_post_meta( $location, 'rtb_append_booking_form', true ) ) {
		$booking_page = $location;
	}

	$schema_type = 'Organization';
	if ( function_exists( 'bpfwp_setting' ) ) {
		$schema_type = bpfwp_setting( 'schema-type', $location );
	}

	if ( bpfwp_get_display( 'show_booking_link' ) ) :
		?>
		<div class="bp-booking">
			<a href="<?php echo esc_url( get_permalink( $booking_page ) ); ?>"<?php if ( rtb_bp_is_schema_type_compatible( $schema_type ) ) : ?> itemprop="acceptsReservations"<?php endif; ?>><?php _e( 'Book a table', 'restaurant-reservations' ); ?></a>
		</div>
		<?php
	endif;

	if ( rtb_bp_is_schema_type_compatible( $schema_type ) ) : ?>
		<meta itemprop="acceptsReservations" content="<?php echo esc_url( get_permalink( $booking_page ) ); ?>">
		<?php
	endif;
}

/**
 * Business Profile: Add an option to the contact card widget to show/hide the
 * booking link
 *
 * @param array $toggles Key/value list of show/hide checkbox toggles
 * @since 1.6
 */
function rtb_bp_add_booking_link_widget_option( $toggles ) {

	// Place the option below the contact option
	$pos = array_search( 'show_contact', array_keys( $toggles ) );

	if ( ! empty( $pos ) ) {
		$a = array_slice( $toggles, 0, $pos );
		$b = array_slice( $toggles, $pos );
		$toggles = array_merge( $a, array( 'show_booking_link' => __( 'Show book a table link', 'restaurant-reservations' ) ) , $b );
	} else {
		// If no short links are being displayed, just add it to the bottom.
		$toggles['show_booking_link'] = __( 'Show book a table link', 'restaurant-reservations' );
	}

	return $toggles;
}
add_filter( 'bpfwp_widget_display_toggles', 'rtb_bp_add_booking_link_widget_option' );

/**
 * Business Profile: Check if a given schema type supports the
 *`acceptsReservations` param
 *
 * Only `FoodEstablishment` and child schemas of that type are allowed to use
 * the `acceptsReservations` parameter.
 *
 * @param string $type Schema type. See: https://schema.org/docs/full.html
 * @since 1.6
 */
function rtb_bp_is_schema_type_compatible( $type ) {

	$food_schema_types = rtb_bp_food_schema_types();

	$allowed_schema_types = array_keys( $food_schema_types );
	$allowed_schema_types[] = 'FoodEstablishment';

	return in_array( $type, $allowed_schema_types );
}

/**
 * Business Profile: Add all FoodEstablishment sub-types to the list of
 * available schema
 *
 * @param array $schema_types Key/value with id/label of schema types
 * @since 1.6
 */
function rtb_bp_schema_types( $schema_types ) {

	$pos = array_search( 'FoodEstablishment', array_keys( $schema_types ) ) + 1;

	// Do nothing if no Food Establishment has been found
	if ( empty( $pos ) ) {
		return $schema_types;
	}

	$a = array_slice( $schema_types, 0, $pos );
	$b = array_slice( $schema_types, $pos );
	$schema_types = array_merge( $a, rtb_bp_food_schema_types(), $b );

	return $schema_types;
}
add_filter( 'bp_schema_types', 'rtb_bp_schema_types' );

/**
 * Business Profile: Get an array of all FoodEstablishment sub-types with
 * labels
 *
 * @since 1.6
 */
function rtb_bp_food_schema_types() {

	return array(
		'Bakery' => __( '--- Bakery', 'restaurant-reservations' ),
		'BarOrPub' => __( '--- Bar or Pub', 'restaurant-reservations' ),
		'Brewery' => __( '--- Brewery', 'restaurant-reservations' ),
		'CafeOrCoffeeShop' => __( '--- Cafe or Coffee Shop', 'restaurant-reservations' ),
		'FastFoodRestaurant' => __( '--- FastFoodRestaurant', 'restaurant-reservations' ),
		'IceCreamShop' => __( '--- Ice Cream Shop', 'restaurant-reservations' ),
		'Restaurant' => __( '--- Restaurant', 'restaurant-reservations' ),
		'Winery' => __( '--- Winery', 'restaurant-reservations' ),
	);
}


/**
* Email template functions that are dependent on Business Profile
*
* @since 2.0.0
*/

/**
 * Add Contact Card details to the designer
 *
 * @param stdClass $designer Handler which populates the email template
 * @param rtbBooking $booking Associated booking
 * @since 0.1
 */
function etfrtb_bp_designer_setup( $designer ) {
	global $rtb_controller;

	// Get Business Profile details if that plugin is active
	if ( !function_exists( 'bpfwp_setting' ) or ! $rtb_controller->permissions->check_permission( 'templates' ) ) {
		return;
	}

	$location = false;
	if ( is_a( $designer->get( 'notification' ), 'rtbNotification' )  &&
			is_a( $designer->get( 'notification')->booking, 'rtbBooking' ) &&
			isset( $designer->get( 'notification')->booking->location ) ) {
		$location = $designer->get( 'notification')->booking->location;
	}

	$designer->set( 'address', bpfwp_setting( 'address', $location ) );
	$designer->set( 'phone', bpfwp_setting( 'phone', $location ) );
	$designer->set( 'contact-email', bpfwp_setting( 'contact-email', $location ) );
	$designer->set( 'contact-page', bpfwp_setting( 'contact-page', $location ) );
	$designer->set( 'bpfwp_setting', bpfwp_setting( 'bpfwp_setting', $location ) );
	if ( $location ) {
		$designer->set( 'location_name', get_the_title( $location ) );
		$designer->set( 'location_url', get_permalink( $location ) );
	}

	switch( $designer->email_type ) {

		case 'booking-admin' :
			$designer->set( 'show_contact', get_option( 'etfrtb_booking_admin_footer_contact', 1 ) );
			break;

		case 'booking-user' :
			$designer->set( 'show_contact', get_option( 'etfrtb_booking_user_footer_contact', 1 ) );
			break;

		case 'confirmed-user' :
			$designer->set( 'show_contact', get_option( 'etfrtb_confirmed_user_footer_contact', 1 ) );
			break;

		case 'rejected-user' :
			$designer->set( 'show_contact', get_option( 'etfrtb_rejected_user_footer_contact', 1 ) );
			break;

		case 'admin-notice' :
			$designer->set( 'show_contact', get_option( 'etfrtb_admin_notice_footer_contact', 1 ) );
			break;

		case 'reminder-user' :
			$designer->set( 'show_contact', get_option( 'etfrtb_reminder_user_footer_contact', 1 ) );
			break;

		case 'late-user' :
			$designer->set( 'show_contact', get_option( 'etfrtb_late_user_footer_contact', 1 ) );
			break;
	}
}
add_filter( 'etfrtb_designer_setup', 'etfrtb_bp_designer_setup' );

/**
 * Add Customizer Controls
 *
 * @since 0.1
 */
function etfrtb_bp_customize_register( $wp_customize ) {
	global $rtb_controller;

	// Get Business Profile details if that plugin is active
	if ( !function_exists( 'bpfwp_setting' ) or ! $rtb_controller->permissions->check_permission( 'templates' ) ) {
		return;
	}

	etfrtb_bp_customize_register_settings( $wp_customize );

	$footer_contact_settings = array(
		'type' => 'radio',
		'label' => __( 'Footer Contact Details', 'restaurant-reservations' ),
		'choices' => array(
			'1' => __( 'Yes', 'restaurant-reservations' ),
			'0' => __( 'No', 'restaurant-reservations' ),
		),
		'description' => 'Display your address and phone number in the footer?',
	);

	$wp_customize->add_control(
		'etfrtb_booking_admin_footer_contact',
		array_merge(
			$footer_contact_settings,
			array(
				'section' => 'etfrtb-content-booking-admin',
				'settings' => 'etfrtb_booking_admin_footer_contact',
			)
		)
	);

	$wp_customize->add_control(
		'etfrtb_booking_user_footer_contact',
		array_merge(
			$footer_contact_settings,
			array(
				'section' => 'etfrtb-content-booking-user',
				'settings' => 'etfrtb_booking_user_footer_contact',
			)
		)
	);

	$wp_customize->add_control(
		'etfrtb_confirmed_user_footer_contact',
		array_merge(
			$footer_contact_settings,
			array(
				'section' => 'etfrtb-content-confirmed-user',
				'settings' => 'etfrtb_confirmed_user_footer_contact',
			)
		)
	);

	$wp_customize->add_control(
		'etfrtb_rejected_user_footer_contact',
		array_merge(
			$footer_contact_settings,
			array(
				'section' => 'etfrtb-content-rejected-user',
				'settings' => 'etfrtb_rejected_user_footer_contact',
			)
		)
	);

	$wp_customize->add_control(
		'etfrtb_admin_notice_footer_contact',
		array_merge(
			$footer_contact_settings,
			array(
				'section' => 'etfrtb-content-admin-notice',
				'settings' => 'etfrtb_admin_notice_footer_contact',
			)
		)
	);

	$wp_customize->add_control(
		'etfrtb_reminder_user_footer_contact',
		array_merge(
			$footer_contact_settings,
			array(
				'section' => 'etfrtb-content-reminder-user',
				'settings' => 'etfrtb_reminder_user_footer_contact',
			)
		)
	);

	$wp_customize->add_control(
		'etfrtb_late_user_footer_contact',
		array_merge(
			$footer_contact_settings,
			array(
				'section' => 'etfrtb-content-late-user',
				'settings' => 'etfrtb_late_user_footer_contact',
			)
		)
	);
}
add_action( 'customize_register_email_designer' , 'etfrtb_bp_customize_register', 20 );

/**
 * Register Customizer Settings
 *
 * This is loaded with every customizer instance to ensure that the settings
 * are available when the instance is saved
 *
 * @since 0.1
 */
function etfrtb_bp_customize_register_settings( $wp_customize ) {
	global $rtb_controller;

	// Get Business Profile details if that plugin is active
	if ( !function_exists( 'bpfwp_setting' ) or ! $rtb_controller->permissions->check_permission( 'templates' ) ) {
		return;
	}

	$footer_contact_settings = array(
		'default' => 1,
		'sanitize_callback' => 'sanitize_text_field',
		'capability' => 'manage_options',
		'type' => 'option',
		'autoload' => false,
	);

	$wp_customize->add_setting( 'etfrtb_booking_admin_footer_contact', $footer_contact_settings );
	$wp_customize->add_setting( 'etfrtb_booking_user_footer_contact', $footer_contact_settings );
	$wp_customize->add_setting( 'etfrtb_confirmed_user_footer_contact', $footer_contact_settings );
	$wp_customize->add_setting( 'etfrtb_rejected_user_footer_contact', $footer_contact_settings );
	$wp_customize->add_setting( 'etfrtb_admin_notice_footer_contact', $footer_contact_settings );
	$wp_customize->add_setting( 'etfrtb_reminder_user_footer_contact', $footer_contact_settings );
	$wp_customize->add_setting( 'etfrtb_late_user_footer_contact', $footer_contact_settings );
}
add_action( 'customize_register', 'etfrtb_bp_customize_register_settings' );