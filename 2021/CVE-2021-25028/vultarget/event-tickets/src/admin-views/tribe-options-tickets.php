<?php
/**
 * Filter to allow users to add/alter ignored post types
 *
 * @since 4.7
 * @since 4.10.2 Update tooltip text for Confirmation email sender address and allow it to be saved as empty
 * @since 4.10.9 Use function for text.
 *
 * @version 4.10.10
 */
$post_types_to_ignore = apply_filters( 'tribe_tickets_settings_post_type_ignore_list', [
	'attachment',
] );

$all_post_type_objects = get_post_types( [ 'public' => true ], 'objects' );
$all_post_types        = [];

foreach ( $all_post_type_objects as $post_type => $post_type_object ) {
	$should_ignore = false;

	foreach ( $post_types_to_ignore as $ignore ) {
		if ( preg_match( '/' . preg_quote( $ignore, '/' ) . '/', $post_type ) ) {
			$should_ignore = true;
			break;
		}
	}

	if ( $should_ignore ) {
		continue;
	}

	$all_post_types[ $post_type ] = $post_type_object->labels->singular_name;
}

$all_post_types = apply_filters( 'tribe_tickets_settings_post_types', $all_post_types );

$options = get_option( Tribe__Main::OPTIONNAME, [] );

/**
 * List of ticketing solutions that support login requirements (ie, disabling or
 * enabling the ticket form according to whether a user is logged in or not).
 *
 * @param array $ticket_systems
 */
$ticket_addons = apply_filters( 'tribe_tickets_settings_systems_supporting_login_requirements', [
	'event-tickets_rsvp' => sprintf( _x( 'Require users to log in before they %s', 'login requirement setting', 'event-tickets' ), tribe_get_rsvp_label_singular( 'require_login_to_rsvp_setting' ) ),
	'event-tickets_all'  => sprintf( _x( 'Require users to log in before they purchase %s', 'login requirement setting', 'event-tickets' ), tribe_get_ticket_label_plural_lowercase( 'require_login_to_purchase_setting' ) ),
]
);

$tickets_fields = [
	'tribe-form-content-start'  => [
		'type' => 'html',
		'html' => '<div class="tribe-settings-form-wrap">',
	],
	'tickets-title'             => [
		'type' => 'html',
		'html' => '<h3>' . esc_html( sprintf( _x( '%s Settings', 'tickets fields settings title', 'event-tickets' ), tribe_get_ticket_label_singular( 'tickets_fields_settings_title' ) ) ) . '</h3>',
	],
	'ticket-enabled-post-types' => [
		'type'            => 'checkbox_list',
		'label'           => esc_html(
			sprintf(
				// Translators: %s: dynamic "tickets" text.
				_x(
					'Post types that can have %s',
					'tickets fields settings enabled post types',
					'event-tickets'
				),
				tribe_get_ticket_label_plural_lowercase( 'tickets_fields_settings_enabled_post_types' )
			)
		),
		// only set the default to tribe_events if the ticket-enabled-post-types index has never been saved
		'default'         => array_key_exists( 'ticket-enabled-post-types', $options ) ? false : 'tribe_events',
		'options'         => $all_post_types,
		'can_be_empty'    => true,
		'validation_type' => 'options_multi',
	],
];

$tec_fields = [];

/**
 * If The Events Calendar is active let's add an option to control the position
 * of the ticket forms in the events view.
 */
if ( class_exists( 'Tribe__Events__Main' ) ) {
	$ticket_form_location_options = [
		'tribe_events_single_event_after_the_meta'     => __( 'Below the event details [default]', 'event-tickets' ),
		'tribe_events_single_event_before_the_meta'    => __( 'Above the event details', 'event-tickets' ),
		'tribe_events_single_event_after_the_content'  => __( 'Below the event description', 'event-tickets' ),
		'tribe_events_single_event_before_the_content' => __( 'Above the event description', 'event-tickets' ),
	];

	$tec_fields = [
		'ticket-rsvp-form-location'             => [
		'type'            => 'dropdown',
		'label'           => esc_html( sprintf( _x( 'Location of %s form', 'form location setting', 'event-tickets' ), tribe_get_rsvp_label_singular( 'form_location_setting' ) ) ),
		'tooltip'         => esc_html__( 'This setting only impacts events made with the classic editor.', 'event-tickets' ),
		'options'         => $ticket_form_location_options,
		'validation_type' => 'options',
		'parent_option'   => Tribe__Events__Main::OPTIONNAME,
		'default'         => reset( $ticket_form_location_options ),
		],
		'ticket-commerce-form-location'         => [
		'type'            => 'dropdown',
		'label'           => esc_html( sprintf( _x( 'Location of %s form', 'form location setting', 'event-tickets' ), tribe_get_ticket_label_plural( 'form_location_setting' ) ) ),
		'tooltip'         => esc_html__( 'This setting only impacts events made with the classic editor.', 'event-tickets' ),
		'options'         => $ticket_form_location_options,
		'validation_type' => 'options',
		'parent_option'   => Tribe__Events__Main::OPTIONNAME,
		'default'         => reset( $ticket_form_location_options ),
		],
		'ticket-display-tickets-left-threshold' => [
		'type'            => 'text',
		'label'           => esc_html( sprintf( _x( 'Display # %s left threshold', 'tickets remaining threshold label', 'event-tickets' ), tribe_get_ticket_label_plural_lowercase( 'remaining_threshold_setting_label' ) ) ),
		'tooltip'         => esc_html( sprintf( _x( 'If this number is less than the number of %1$s left for sale on your event, this will prevent the "# of %1$s left" text from showing on your website. You can leave this blank if you would like to always show the text.', 'tickets remaining threshold tooltip', 'event-tickets' ), tribe_get_ticket_label_plural_lowercase( 'remaining_threshold_setting_tooltip' ) ) ),
		'validation_type' => 'int',
		'size'            => 'small',
		'can_be_empty'    => true,
		'parent_option'   => Tribe__Events__Main::OPTIONNAME,
		],
	];
}

$authentication_fields = [
	'ticket-authentication-requirements-heading' => [
		'type' => 'html',
		'html' => '<h3>' . __( 'Login Requirements', 'event-tickets' ) . '</h3>',
	],
	'ticket-authentication-requirements-advice'  => [
		'type' => 'html',
		'html' => '<p>' . sprintf(
				_x( 'You can require that users log into your site before they are able to %1$s (or buy %2$s). Please review your WordPress Membership option (via the %3$sGeneral Settings admin screen%4$s) before adjusting this setting.',
				'ticket authentication requirements',
				'event-tickets'
				),
			tribe_get_rsvp_label_singular( 'authentication_requirements_advice' ),
			tribe_get_ticket_label_plural_lowercase( 'authentication_requirements_advice' ),
			'<a href="' . esc_url( get_admin_url( null, 'options-general.php' ) ) . '" target="_blank">',
			'</a>'
			) . '</p>',
	],
	'ticket-authentication-requirements'         => [
		'type'            => 'checkbox_list',
		'options'         => $ticket_addons,
		'validation_type' => 'options_multi',
		'can_be_empty'    => true,
	],
];

$ticket_fields_end = [
	'tribe-form-content-end' => [
		'type' => 'html',
		'html' => '</div>',
	],
];

$commerce_fields = [];

/**
 * Hide Legacy PayPal settings for new installations.
 */
if ( tec_tribe_commerce_is_available() ) {
	$commerce_fields = include 'tribe-commerce-settings.php';
}

/**
 * Allows for the specific filtering of the commerce fields.
 *
 * @since 5.1.6
 *
 * @param array $commerce_fields Which fields are already present from legacy.
 */
$commerce_fields = (array) apply_filters( 'tec_tickets_commerce_settings', $commerce_fields );

$tickets_fields = array_merge(
	$tickets_fields,
	$tec_fields,
	$authentication_fields,
	$commerce_fields,
	$ticket_fields_end
);

/**
 * Filters the fields to be registered in the Events > Settings > Tickets tab.
 *
 * A field definition is one suitable to be consumed by the `Tribe__Settings_Tab` class.
 *
 * @see Tribe__Settings_Tab
 * @see Tribe__Field
 *
 * @param array $tickets_fields An associative array of fields definitions to register.
 */
$tickets_fields = apply_filters( 'tribe_tickets_settings_tab_fields', $tickets_fields );

$tickets_tab = [
	'priority' => 20,
	'fields' => $tickets_fields,
];
