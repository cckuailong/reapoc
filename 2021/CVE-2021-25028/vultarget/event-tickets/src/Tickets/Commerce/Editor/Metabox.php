<?php

namespace TEC\Tickets\Commerce\Editor;

use TEC\Tickets\Commerce\Module;
use Tribe__Tickets__Main as Tickets_Plugin;


/**
 * Class Metabox.
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Editor
 */
class Metabox {


	/**
	 * Add the sku field in the admin's new/edit ticket metabox
	 *
	 * @since 4.7
	 *
	 * @param     $post_id   int id of the event post
	 * @param int $ticket_id (null) id of the ticket
	 *
	 * @return void
	 */
	public function do_metabox_sku_options( $post_id, $ticket_id = null ) {
		$sku = '';

		/** @var \Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler = tribe( 'tickets.handler' );
		$provider_obj    = tribe( Module::class );

		$is_correct_provider = $tickets_handler->is_correct_provider( $post_id, $provider_obj );

		if ( ! empty( $ticket_id ) ) {
			$ticket              = tribe( Module::class )->get_ticket( $post_id, $ticket_id );
			$is_correct_provider = $tickets_handler->is_correct_provider( $ticket_id, $provider_obj );

			if ( ! empty( $ticket ) ) {
				$sku = get_post_meta( $ticket_id, '_sku', true );
			}
		}

		// Bail when we are not dealing with this provider
		if ( ! $is_correct_provider ) {
			return;
		}

		$path = Tickets_Plugin::instance()->plugin_path;

		include $path . 'src/admin-views/commerce/metabox/sku.php';
	}

	/**
	 * Renders the advanced fields in the new/edit ticket form.
	 * Using the method, providers can add as many fields as
	 * they want, specific to their implementation.
	 *
	 * @since 5.1.9
	 *
	 * @param int $post_id
	 * @param int $ticket_id
	 */
	public function include_metabox_advanced_options( $post_id, $ticket_id ) {
		$provider = __CLASS__;

		echo '<div id="' . sanitize_html_class( $provider ) . '_advanced" class="tribe-dependent" data-depends="#provider_TEC_Tickets_Commerce_Module_radio" data-condition-is-checked>';

		if ( ! tribe_is_frontend() ) {
			$this->do_metabox_sku_options( $post_id, $ticket_id );
		}

		/**
		 * Allows for the insertion of additional content into the ticket edit form - advanced section
		 *
		 * @since 4.6
		 *
		 * @param int Post ID
		 * @param string the provider class name
		 * @param int $ticket_id The ticket ID.
		 */
		do_action( 'tribe_events_tickets_metabox_edit_ajax_advanced', $post_id, $provider, $ticket_id );

		echo '</div>';
	}


	/**
	 * Renders the advanced fields in the new/edit ticket form.
	 * Using the method, providers can add as many fields as
	 * they want, specific to their implementation.
	 *
	 * @since 5.2.0
	 *
	 * @param int $post_id
	 * @param int $ticket_id
	 *
	 * @return mixed
	 */
	public function do_metabox_capacity_options( $post_id, $ticket_id ) {
		/** @var \Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler = tribe( 'tickets.handler' );
		$provider        = tribe( Module::class );

		$is_correct_provider = $tickets_handler->is_correct_provider( $post_id, $provider );

		$url               = '';
		$stock             = '';
		$global_stock_mode = $tickets_handler->get_default_capacity_mode();
		$global_stock_cap  = 0;
		$ticket_capacity   = null;
		$post_capacity     = null;

		$stock_object = new \Tribe__Tickets__Global_Stock( $post_id );

		if ( $stock_object->is_enabled() ) {
			$post_capacity = tribe_tickets_get_capacity( $post_id );
		}

		if ( ! empty( $ticket_id ) ) {
			$ticket              = tribe( Module::class )->get_ticket( $post_id, $ticket_id );
			$is_correct_provider = $tickets_handler->is_correct_provider( $ticket_id, $provider );

			if ( ! empty( $ticket ) ) {
				$stock             = $ticket->managing_stock() ? $ticket->stock() : '';
				$ticket_capacity   = tribe_tickets_get_capacity( $ticket->ID );
				$global_stock_mode = ( method_exists( $ticket, 'global_stock_mode' ) ) ? $ticket->global_stock_mode() : '';
				$global_stock_cap  = ( method_exists( $ticket, 'global_stock_cap' ) ) ? $ticket->global_stock_cap() : 0;
			}
		}

		// Bail when we are not dealing with this provider
		if ( ! $is_correct_provider ) {
			return;
		}

		$file = \Tribe__Tickets__Main::instance()->plugin_path . 'src/admin-views/commerce/metabox/capacity.php';

		/**
		 * Filters the absolute path to the file containing the metabox capacity HTML.
		 *
		 * @since 5.2.0
		 *
		 * @param string     $file The absolute path to the file containing the metabox capacity HTML
		 * @param int|string $ticket_capacity
		 * @param int|string $post_capacity
		 */
		$file = apply_filters( 'tec_tickets_commerce_metabox_capacity_file', $file, $ticket_capacity, $post_capacity );

		if ( file_exists( $file ) ) {
			include $file;
		}
	}
}