<?php

namespace Tribe\Tickets\Admin\Manager;

use tad_DI52_ServiceProvider;

/**
 * Class Manager
 *
 * @package Tribe\Tickets\Admin\Manager
 *
 * @since   5.1.0
 */
class Service_Provider extends tad_DI52_ServiceProvider {
	/**
	 * Register the provider singletons.
	 *
	 * @since 5.1.0
	 */
	public function register() {
		$this->container->singleton( 'tickets.admin.manager', self::class );

		$this->hooks();
	}

	/**
	 * Add actions and filters.
	 *
	 * @since 5.1.0
	 */
	protected function hooks() {

		add_action( 'wp_before_admin_bar_render', [ $this, 'add_attendees_view_button' ], 20 );

		if ( ! is_admin() ) {
			return;
		}

		// Handle AJAX.
		add_action( 'wp_ajax_nopriv_tribe_tickets_admin_manager', [ $this, 'ajax_handle_admin_manager' ] );
		add_action( 'wp_ajax_tribe_tickets_admin_manager', [ $this, 'ajax_handle_admin_manager' ] );
	}

	/**
	 * Handle response
	 *
	 * @since 5.1.0
	 */
	public function ajax_handle_admin_manager() {
		// @todo Look at adding capability checks of some sort based on a filter that provides capability context for the specific request.
		$response = [
			'html' => '',
		];

		if ( ! check_ajax_referer( 'tribe_tickets_admin_manager_nonce', 'nonce', false ) ) {
			$response['html'] = $this->render_error( __( 'Insecure request.', 'event-tickets' ) );

			wp_send_json_error( $response );
		}

		/*
		 * Get the request vars.
		 *
		 * Note to future developers: Using tribe_get_request_vars() here was removing non-string values (like arrays).
		 */
		$vars = $_REQUEST;

		/**
		 * Filter the admin manager request.
		 *
		 * @since 5.1.0
		 *
		 * @param string|\WP_Error $render_response The render response HTML content or WP_Error with list of errors.
		 * @param array            $vars            The request variables.
		 */
		$render_response = apply_filters( 'tribe_tickets_admin_manager_request', '', $vars );

		if ( is_string( $render_response ) && '' !== $render_response ) {
			// Return the HTML if it's a string.
			$response['html'] = $render_response;

			wp_send_json_success( $response );
		} elseif ( is_wp_error( $render_response ) ) {
			$response['html'] = $this->render_error( $render_response->get_error_messages() );

			wp_send_json_error( $response );
		}

		$response['html'] = $this->render_error( __( 'Something happened here.', 'event-tickets' ) );

		wp_send_json_error( $response );
	}

	/**
	 * Handle error rendering.
	 *
	 * @since 5.1.0
	 *
	 * @param string|array $error_message The error message(s).
	 *
	 * @return string The error template HTML.
	 */
	public function render_error( $error_message ) {

		// @todo @juanfra Re-check how we're going to deal with admin views. Ideally we should follow
		// the same model we do for FE, like the following:

		// // Set required template globals.
		// $args = [
		// 	'error_message' => $error_message,
		// ];

		// /** @var \Tribe__Tickets__Editor__Template $template */
		// $template = tribe( 'tickets.editor.template' );

		// // Add the rendering attributes into global context.
		// $template->add_template_globals( $args );

		// return $template->template( 'path/to/template/error', $args, false );

		return $error_message;
	}

	/**
	 * Add the Attendee Report nav button to WP Admin Nav bar.
	 *
	 * @since 5.1.3
	 */
	public function add_attendees_view_button() {

		global $wp_admin_bar;

		// Check user permission.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		// Get list of supported post types for Tickets.
		$supported_post_types = (array) tribe_get_option( 'ticket-enabled-post-types', [] );

		// Only show the view button for admin edit or singular frontend view.
		if (
			! ( is_admin() && 'edit' === tribe_get_request_var( 'action' ) )
			&& ! is_singular( $supported_post_types )
		) {
			return;
		}

		$post    = get_post();
		$post_id = $post ? $post->ID : 0;

		// If no valid post is found, bail out.
		if ( 0 === $post_id ) {
			return;
		}

		// Make sure we have tickets on this Post Type / Event.
		$tickets = \Tribe__Tickets__Tickets::get_all_event_tickets( $post_id );

		if ( empty( $tickets ) ) {
			return;
		}

		/** @var \Tribe__Tickets__Attendees $tickets_attendees */
		$tickets_attendees = tribe( 'tickets.attendees' );

		$url = $tickets_attendees->get_report_link( $post );

		// Add the Nav button node to nav menu.
		$wp_admin_bar->add_menu(
			[
				'id'    => 'event-tickets-attendees',
				'title' => '<i class="ab-icon dashicons dashicons-groups"></i> ' . esc_html__( 'Attendees', 'event-tickets' ),
				'href'  => $url,
				'meta' => [
					'title' => __( 'Manage attendees', 'event-tickets' ),
				],
			]
		);
	}
}
