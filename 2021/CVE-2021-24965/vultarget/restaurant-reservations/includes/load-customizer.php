<?php defined( 'ABSPATH' ) || exit;
/**
 * Functions to manage the live email preview in the customizer
 */

if ( isset( $_GET['etfrtb_designer'] ) ) {
	add_filter( 'customize_loaded_components', 'etfrtb_customize_init_blank_state' );
	add_action( 'customize_controls_enqueue_scripts', 'etfrtb_customize_control_assets' );
	add_action( 'customize_controls_init', 'etfrtb_customize_inject_url_param' );
	add_action( 'customize_register_email_designer', 'etfrtb_customize_register' );
	add_action( 'customize_preview_init', 'etfrtb_customize_preview_init' );
}

/**
 * Initialize the callbacks to turn the Customizer into a "blank slate"
 *
 * This is the first of a series of callbacks which remove all registered
 * panels, sections and controls from the Customizer. This is only done when
 * the customizer is loaded with a special query arg from the notifications
 * settings screen.
 *
 * @see https://github.com/xwp/wp-customizer-blank-slate
 * @param $components Components that have been loaded
 * @since 0.1
 */
if ( ! function_exists('etfrtb_customize_init_blank_state') ) {
function etfrtb_customize_init_blank_state( $components ) {
	global $rtb_controller;

	if ( ! $rtb_controller->permissions->check_permission( 'templates' ) ) {
		return $components;
	}

	// Reset the customize register actions
	add_action( 'wp_loaded', 'etfrtb_customize_reset_register', 1 );

	// Remove all registered components
	$components = array();
	return $components;
}
}

/**
 * Prevent other constructs from being registered and register only those we
 * want registered in our instance of the Customizer
 *
 * @since 0.1
 */
if ( ! function_exists('etfrtb_customize_reset_register') ) {
function etfrtb_customize_reset_register() {

	global $wp_customize;

	// Prevent anything from hooking in to register controls
	remove_all_actions( 'customize_register' );

	$wp_customize->register_panel_type( 'WP_Customize_Panel' );
	$wp_customize->register_section_type( 'WP_Customize_Section' );
	$wp_customize->register_control_type( 'WP_Customize_Color_Control' );
	$wp_customize->register_control_type( 'WP_Customize_Image_Control' );
	$wp_customize->register_control_type( 'WP_Customize_Media_Control' );

	// Register our Customizer controls
	do_action( 'customize_register_email_designer', $wp_customize );
}
}

/**
 * Register customizer controls to manage the email designer
 *
 * @since 0.1
 */
if ( ! function_exists('etfrtb_customize_register') ) {
function etfrtb_customize_register( $wp_customize ) {
	global $rtb_controller;

	if ( ! $rtb_controller->permissions->check_permission( 'templates' ) ) { return; }

	etfrtb_customize_register_settings( $wp_customize );

	$template_selection = array( '0' => __( 'No Email Template', 'email-templates-for-rtb' ) );
	foreach( $rtb_controller->email_templates->template_options as $file => $template ) {
		$template_selection[$file] = $template['title'];
	}

	$wp_customize->add_section(
		'etfrtb_style',
		array(
			'title' => __( 'Logo & Colors', 'email-templates-for-rtb' ),
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'etfrtb_logo',
			array(
				'section'   => 'etfrtb_style',
				'label'     => __( 'Logo', 'email-templates-for-rtb' ),
				'settings'  => 'etfrtb_logo',
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'etfrtb_color_primary',
			array(
				'section'  => 'etfrtb_style',
				'label'    => __( 'Primary Color', 'email-templates-for-rtb' ),
				'settings' => 'etfrtb_color_primary',
			)
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'etfrtb_color_primary_text',
			array(
				'section'  => 'etfrtb_style',
				'label'    => __( 'Primary Text Color', 'email-templates-for-rtb' ),
				'description'    => __( 'Some templates display text on a background of the Primary Color. Adjust the text color in these cases to make sure it can be read easily.', 'email-templates-for-rtb' ),
				'settings' => 'etfrtb_color_primary_text',
			)
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'etfrtb_color_button',
			array(
				'section'  => 'etfrtb_style',
				'label'    => __( 'Button Color', 'email-templates-for-rtb' ),
				'description'    => __( 'Some emails include a button. Select a background color for these buttons.', 'email-templates-for-rtb' ),
				'settings' => 'etfrtb_color_button',
			)
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'etfrtb_color_button_text',
			array(
				'section'  => 'etfrtb_style',
				'label'    => __( 'Button Text Color', 'email-templates-for-rtb' ),
				'description'    => __( 'Some templates include a button. Select a text color for these buttons', 'email-templates-for-rtb' ),
				'settings' => 'etfrtb_color_button_text',
			)
		)
	);

	$wp_customize->add_control(
		'etfrtb_acknowledgement',
		array(
			'section' => 'etfrtb_style',
			'label' => __( 'Email Acknowledgement', 'email-templates-for-rtb' ),
			'settings' => 'etfrtb_acknowledgement',
			'description' => __( 'Display a brief acknowledgement of why the user is receiving this message at the bottom of the email.', 'email-templates-for-rtb' ),
		)
	);

	// Initial booking request email for admin
	$wp_customize->add_section(
		'etfrtb-content-booking-admin',
		array(
			'title' => __( 'Admin Notification Email', 'email-templates-for-rtb' ),
			'description' => __( 'The email sent to the admin when a new booking is made.', 'email-templates-for-rtb' ),
		)
	);

	$wp_customize->add_control(
		'etfrtb_booking_admin_template',
		array(
			'section' => 'etfrtb-content-booking-admin',
			'settings' => 'etfrtb_booking_admin_template',
			'type' => 'select',
			'label' => __( 'Template', 'email-templates-for-rtb' ),
			'choices' => $template_selection,
		)
	);

	$wp_customize->add_control(
		'etfrtb_booking_admin_headline',
		array(
			'section' => 'etfrtb-content-booking-admin',
			'label' => __( 'Lead Sentence', 'email-templates-for-rtb' ),
			'settings' => 'etfrtb_booking_admin_headline',
			'description' => 'Add an attention-grabbing headline to this email.',
		)
	);

	$wp_customize->add_control(
		'etfrtb_booking_admin_footer_message',
		array(
			'section' => 'etfrtb-content-booking-admin',
			'label' => __( 'Footer Message', 'email-templates-for-rtb' ),
			'settings' => 'etfrtb_booking_admin_footer_message',
			'description' => 'Add a short message to the footer.',
		)
	);

	// Initial booking request email for user
	$wp_customize->add_section(
		'etfrtb-content-booking-user',
		array(
			'title' => __( 'New Request Email', 'email-templates-for-rtb' ),
			'description' => __( 'The email a user receives when they make an initial booking request.', 'email-templates-for-rtb' ),
		)
	);

	$wp_customize->add_control(
		'etfrtb_booking_user_template',
		array(
			'section' => 'etfrtb-content-booking-user',
			'settings' => 'etfrtb_booking_user_template',
			'type' => 'select',
			'label' => __( 'Template', 'email-templates-for-rtb' ),
			'choices' => $template_selection,
		)
	);

	$wp_customize->add_control(
		'etfrtb_booking_user_headline',
		array(
			'section' => 'etfrtb-content-booking-user',
			'label' => __( 'Lead Sentence', 'email-templates-for-rtb' ),
			'settings' => 'etfrtb_booking_user_headline',
			'description' => 'Add an attention-grabbing headline to this email.',
		)
	);

	$wp_customize->add_control(
		'etfrtb_booking_user_footer_message',
		array(
			'section' => 'etfrtb-content-booking-user',
			'label' => __( 'Footer Message', 'email-templates-for-rtb' ),
			'settings' => 'etfrtb_booking_user_footer_message',
			'description' => 'Add a short message to the footer.',
		)
	);

	// Booking confirmed email
	$wp_customize->add_section(
		'etfrtb-content-confirmed-user',
		array(
			'title' => __( 'Confirmed Email', 'email-templates-for-rtb' ),
			'description' => __( 'The email a user receives when their booking is confirmed.', 'email-templates-for-rtb' ),
		)
	);

	$wp_customize->add_control(
		'etfrtb_confirmed_user_template',
		array(
			'section' => 'etfrtb-content-confirmed-user',
			'settings' => 'etfrtb_confirmed_user_template',
			'type' => 'select',
			'label' => __( 'Template', 'email-templates-for-rtb' ),
			'choices' => $template_selection,
		)
	);

	$wp_customize->add_control(
		'etfrtb_confirmed_user_headline',
		array(
			'section' => 'etfrtb-content-confirmed-user',
			'label' => __( 'Lead Sentence', 'email-templates-for-rtb' ),
			'settings' => 'etfrtb_confirmed_user_headline',
			'description' => 'Add an attention-grabbing headline to this email.',
		)
	);

	$wp_customize->add_control(
		'etfrtb_confirmed_user_footer_message',
		array(
			'section' => 'etfrtb-content-confirmed-user',
			'label' => __( 'Footer Message', 'email-templates-for-rtb' ),
			'settings' => 'etfrtb_confirmed_user_footer_message',
			'description' => 'Add a short message to the footer.',
		)
	);

	// Rejected email
	$wp_customize->add_section(
		'etfrtb-content-rejected-user',
		array(
			'title' => __( 'Rejected Email', 'email-templates-for-rtb' ),
			'description' => __( 'The email a user receives when their booking has been rejected.', 'email-templates-for-rtb' ),
		)
	);

	$wp_customize->add_control(
		'etfrtb_rejected_user_template',
		array(
			'section' => 'etfrtb-content-rejected-user',
			'settings' => 'etfrtb_rejected_user_template',
			'type' => 'select',
			'label' => __( 'Template', 'email-templates-for-rtb' ),
			'choices' => $template_selection,
		)
	);

	$wp_customize->add_control(
		'etfrtb_rejected_user_headline',
		array(
			'section' => 'etfrtb-content-rejected-user',
			'label' => __( 'Lead Sentence', 'email-templates-for-rtb' ),
			'settings' => 'etfrtb_rejected_user_headline',
			'description' => 'Add an attention-grabbing headline to this email.',
		)
	);

	$wp_customize->add_control(
		'etfrtb_rejected_user_book_again',
		array(
			'section' => 'etfrtb-content-rejected-user',
			'label' => __( 'Book Again Label', 'email-templates-for-rtb' ),
			'settings' => 'etfrtb_rejected_user_book_again',
			'description' => 'Add a label to display a button encouraging customers to book for a different time.',
		)
	);

	$wp_customize->add_control(
		'etfrtb_rejected_user_footer_message',
		array(
			'section' => 'etfrtb-content-rejected-user',
			'label' => __( 'Footer Message', 'email-templates-for-rtb' ),
			'settings' => 'etfrtb_rejected_user_footer_message',
			'description' => 'Add a short message to the footer.',
		)
	);

	// Admin update email
	$wp_customize->add_section(
		'etfrtb-content-admin-notice',
		array(
			'title' => __( 'Admin Update', 'email-templates-for-rtb' ),
			'description' => sprintf(
				__( 'The email a user receives when an admin sends them a custom email message from the %sbookings panel%s.', 'email-templates-for-rtb' ),
				'<a href="' . admin_url( '?page=rtb-bookings' ) . '">',
				'</a>'
			),
		)
	);

	$wp_customize->add_control(
		'etfrtb_admin_notice_template',
		array(
			'section' => 'etfrtb-content-admin-notice',
			'settings' => 'etfrtb_admin_notice_template',
			'type' => 'select',
			'label' => __( 'Template', 'email-templates-for-rtb' ),
			'choices' => $template_selection,
		)
	);

	$wp_customize->add_control(
		'etfrtb_admin_notice_headline',
		array(
			'section' => 'etfrtb-content-admin-notice',
			'label' => __( 'Lead Sentence', 'email-templates-for-rtb' ),
			'settings' => 'etfrtb_admin_notice_headline',
			'description' => 'Add an attention-grabbing headline to this email.',
		)
	);

	$wp_customize->add_control(
		'etfrtb_admin_notice_footer_message',
		array(
			'section' => 'etfrtb-content-admin-notice',
			'label' => __( 'Footer Message', 'email-templates-for-rtb' ),
			'settings' => 'etfrtb_admin_notice_footer_message',
			'description' => 'Add a short message to the footer.',
		)
	);

	// User reminder email
	$wp_customize->add_section(
		'etfrtb-content-reminder-user',
		array(
			'title' => __( 'User Reminder', 'email-templates-for-rtb' ),
			'description' => __( 'The email a user receives as a reminder of the reservation.', 'email-templates-for-rtb' ),
		)
	);

	$wp_customize->add_control(
		'etfrtb_reminder_user_template',
		array(
			'section' => 'etfrtb-content-reminder-user',
			'settings' => 'etfrtb_reminder_user_template',
			'label' => __( 'Template', 'email-templates-for-rtb' ),
			'type' => 'select',
			'choices' => $template_selection,
		)
	);

	$wp_customize->add_control(
		'etfrtb_reminder_user_headline',
		array(
			'section' => 'etfrtb-content-reminder-user',
			'settings' => 'etfrtb_reminder_user_headline',
			'label' => __( 'Lead Sentence', 'email-templates-for-rtb' ),
			'description' => 'Add an attention-grabbing headline to this email.',
		)
	);

	$wp_customize->add_control(
		'etfrtb_reminder_user_footer_message',
		array(
			'section' => 'etfrtb-content-reminder-user',
			'settings' => 'etfrtb_reminder_user_footer_message',
			'label' => __( 'Footer Message', 'email-templates-for-rtb' ),
			'description' => 'Add a short message to the footer.',
		)
	);

	// User late email
	$wp_customize->add_section(
		'etfrtb-content-late-user',
		array(
			'title' => __( 'User Late', 'email-templates-for-rtb' ),
			'description' => __( 'The email a user receives when they are late for their reservation.', 'email-templates-for-rtb' ),
		)
	);

	$wp_customize->add_control(
		'etfrtb_late_user_template',
		array(
			'section' => 'etfrtb-content-late-user',
			'settings' => 'etfrtb_late_user_template',
			'label' => __( 'Template', 'email-templates-for-rtb' ),
			'type' => 'select',
			'choices' => $template_selection,
		)
	);

	$wp_customize->add_control(
		'etfrtb_late_user_headline',
		array(
			'section' => 'etfrtb-content-late-user',
			'settings' => 'etfrtb_late_user_headline',
			'label' => __( 'Lead Sentence', 'email-templates-for-rtb' ),
			'description' => 'Add an attention-grabbing headline to this email.',
		)
	);

	$wp_customize->add_control(
		'etfrtb_late_user_footer_message',
		array(
			'section' => 'etfrtb-content-late-user',
			'settings' => 'etfrtb_late_user_footer_message',
			'label' => __( 'Footer Message', 'email-templates-for-rtb' ),
			'description' => 'Add a short message to the footer.',
		)
	);
}
}

/**
 * Register the customizer settings
 *
 * The settings must be registered both in and out of the customizer's "blank
 * slate" mode. When the customizer is saved, the request is processed outside
 * of the "blank slate" mode. If the setting is not registered there, the value
 * will not be saved.
 *
 * @since 0.1
 */
if ( ! function_exists('etfrtb_customize_register_settings') ) {
function etfrtb_customize_register_settings( $wp_customize ) {

	global $rtb_controller;

	$wp_customize->add_setting(
		'etfrtb_logo',
		array(
			'default' => get_theme_mod( 'custom_logo' ),
			'sanitize_callback' => 'absint',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_color_primary',
		array(
			'default' => '#66BB7F',
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_color_primary_text',
		array(
			'default' => '#FFFFFF',
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_color_button',
		array(
			'default' => '#66BB7F',
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_color_button_text',
		array(
			'default' => '#FFFFFF',
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_acknowledgement',
		array(
			'default' => __( 'This message was sent by {site_link} on {current_time}. You are receiving this email because we received a booking request from this email address.', 'email-templates-for-rtb' ),
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	// Initial booking admin notification
	$wp_customize->add_setting(
		'etfrtb_booking_admin_headline',
		array(
			'default' => $rtb_controller->settings->get_setting( 'subject-booking-admin' ),
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_booking_admin_template',
		array(
			'default' => 'conversations.php',
			'sanitize_callback' => 'sanitize_file_name',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_booking_admin_footer_message',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	// Initial booking user notification
	$wp_customize->add_setting(
		'etfrtb_booking_user_headline',
		array(
			'default' => $rtb_controller->settings->get_setting( 'subject-booking-user' ),
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_booking_user_template',
		array(
			'default' => 'conversations.php',
			'sanitize_callback' => 'sanitize_file_name',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_booking_user_footer_message',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	// Confirmed booking user notification
	$wp_customize->add_setting(
		'etfrtb_confirmed_user_headline',
		array(
			'default' => $rtb_controller->settings->get_setting( 'subject-confirmed-user' ),
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_confirmed_user_template',
		array(
			'default' => 'conversations.php',
			'sanitize_callback' => 'sanitize_file_name',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_confirmed_user_footer_message',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	// Rejected email
	$wp_customize->add_setting(
		'etfrtb_rejected_user_template',
		array(
			'default' => 'conversations.php',
			'sanitize_callback' => 'sanitize_file_name',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_rejected_user_headline',
		array(
			'default' => $rtb_controller->settings->get_setting( 'subject-rejected-user' ),
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_rejected_user_book_again',
		array(
			'default' => __( 'Book Another Time', 'email-templates-for-rtb' ),
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_rejected_user_footer_message',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	// Admin update email
	$wp_customize->add_setting(
		'etfrtb_admin_notice_template',
		array(
			'default' => 'conversations.php',
			'sanitize_callback' => 'sanitize_file_name',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_admin_notice_headline',
		array(
			'default' => $rtb_controller->settings->get_setting( 'subject-admin-notice' ),
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_admin_notice_footer_message',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	// User Reminder email
	$wp_customize->add_setting(
		'etfrtb_reminder_user_template',
		array(
			'default' => 'conversations.php',
			'sanitize_callback' => 'sanitize_file_name',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_reminder_user_headline',
		array(
			'default' => $rtb_controller->settings->get_setting( 'subject-reminder-user' ),
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_reminder_user_footer_message',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	// User Late email
	$wp_customize->add_setting(
		'etfrtb_late_user_template',
		array(
			'default' => 'conversations.php',
			'sanitize_callback' => 'sanitize_file_name',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_late_user_headline',
		array(
			'default' => $rtb_controller->settings->get_setting( 'subject-late-user' ),
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);

	$wp_customize->add_setting(
		'etfrtb_late_user_footer_message',
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field',
			'capability' => 'manage_options',
			'type' => 'option',
			'autoload' => false,
		)
	);
}
}
add_action( 'customize_register', 'etfrtb_customize_register_settings' );

/**
 * Inject the query param into the preview URL
 *
 * @since 0.1
 */
if ( ! function_exists('etfrtb_customize_inject_url_param') ) {
function etfrtb_customize_inject_url_param() {
	global $wp_customize;
	global $rtb_controller;

	if ( ! $rtb_controller->permissions->check_permission( 'templates' ) ) { return; }

	$wp_customize->set_preview_url(
		add_query_arg(
			array( 'etfrtb_designer' => '1' ),
			$wp_customize->get_preview_url()
		)
	);
}
}

/**
 * Load wp-util dependency missing in WP versions prior to 4.7
 *
 * @since 0.1
 */
if ( ! function_exists('etfrtb_customize_control_assets') ) {
function etfrtb_customize_control_assets() {
	global $rtb_controller;

	if ( ! $rtb_controller->permissions->check_permission( 'templates' ) ) { return; }


	$min = SCRIPT_DEBUG ? '.min' : '';

	wp_enqueue_script( 'wp-util' );
	wp_enqueue_script( 'etfrtb-customizer-control', RTB_PLUGIN_URL . '/assets/js/customizer-control' . $min . '.js', array( 'customize-controls' ) );
}
}

/**
 * Initialize the customizer preview window
 *
 * @since 0.1
 */
if ( ! function_exists('etfrtb_customize_preview_init') ) {
function etfrtb_customize_preview_init() {
	global $rtb_controller;

	if ( ! $rtb_controller->permissions->check_permission( 'templates' ) ) { return; }
	
	add_rewrite_endpoint( 'etfrtb_designer', EP_NONE);
	add_rewrite_endpoint( 'etfrtb_designer_template', EP_NONE);
	add_rewrite_endpoint( 'etfrtb_designer_email', EP_NONE);
	add_filter( 'template_include', 'etfrtb_customize_preview_load_email_designer' );
}
}

/**
 * Load the email designer in the customizer preview
 *
 * @param string $template
 * @since 0.1
 */
if ( ! function_exists('etfrtb_customize_preview_load_email_designer') ) {
function etfrtb_customize_preview_load_email_designer( $template ) {

	global $wp_query;

	if ( !isset( $wp_query->query['etfrtb_designer'] ) ) {
		return $template;
	}

	$email = !isset( $wp_query->query['etfrtb_designer_email'] ) ? 'booking-user' : $wp_query->query['etfrtb_designer_email'];

	$designer = new etfrtbDesigner();
	$designer->setup( $email );

	echo $designer->render();
	exit();
}
}