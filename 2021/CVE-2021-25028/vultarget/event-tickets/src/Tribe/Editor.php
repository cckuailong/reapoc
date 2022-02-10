<?php

/**
 * Class Tribe__Tickets__Editor
 *
 * @since 4.9
 */
class Tribe__Tickets__Editor extends Tribe__Editor {

	/**
	 * Variable used as a flag to detect if we should flush the tickets blocks
	 *
	 * @since 4.9.2
	 *
	 * @var string
	 */
	public $meta_key_flush_flag = '_tribe_tickets_flush_blocks';

	/**
	 * Hooks actions from the editor into the correct places
	 *
	 * @since 4.9
	 *
	 * @return bool
	 */
	public function hook() {
		// Add Rest API support
		add_filter( 'tribe_tickets_register_ticket_post_type_args', array( $this, 'add_rest_support' ) );

		// Update Post content to use correct child blocks for tickets
		add_filter( 'tribe_blocks_editor_update_classic_content', array( $this, 'update_tickets_block_with_childs' ), 10, 3 );

		// Add RSVP and tickets blocks
		add_action( 'admin_init', array( $this, 'add_tickets_block_in_editor' ) );

		add_filter( 'tribe_events_editor_default_classic_template', array( $this, 'filter_default_template_classic_blocks' ), 15 );

		add_action( 'tribe_events_tickets_post_capacity', tribe_callback( 'tickets.admin.views', 'template', 'editor/button-view-orders' ) );
		add_action( 'tribe_events_tickets_metabox_edit_main', array( $this, 'filter_get_price_fields' ), 10, 2 );
		add_action( 'tribe_events_tickets_capacity', tribe_callback( 'tickets.admin.views', 'template', 'editor/total-capacity' ) );
		add_action( 'tribe_events_tickets_ticket_table_add_header_column', tribe_callback( 'tickets.admin.views', 'template', 'editor/column-head-price' ) );
		add_action( 'tribe_events_tickets_ticket_table_add_tbody_column', array( $this, 'add_column_content_price' ), 10, 2 );

		// Maybe add flag from classic editor
		add_action( 'load-post.php', array( $this, 'flush_blocks' ), 0 );
		add_action( 'tribe_tickets_update_blocks_from_classic_editor', array( $this, 'update_blocks' ) );
	}

	/**
	 * Adds the ticket block into the editor
	 *
	 * @since 4.9
	 *
	 *
	 * @param array $template Array of all the templates used by default
	 * @param string $post_type The current post type
	 *
	 * @return array
	 */
	public function add_tickets_block_in_editor() {
		// Post types where the block shouldn't be displayed by default
		if ( ! class_exists( 'Tribe__Events__Main' ) ) {
			return;
		}

		foreach ( $this->get_enabled_post_types() as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			if ( ! $post_type_object || $post_type !== Tribe__Events__Main::POSTTYPE ) {
				continue;
			}

			$template = isset( $post_type_object->template )
				? (array) $post_type_object->template
				: array();

			$template[] = array( 'tribe/tickets' );
			$template[] = array( 'tribe/rsvp' );

			$post_type_object->template = $template;
		}
	}

	/**
	 * Filters and adds the ticket block into the default classic blocks
	 *
	 * @since 4.9
	 *
	 * @param  array $template
	 *
	 * @return array
	 */
	public function filter_default_template_classic_blocks( $template = array() ) {
		$template[] = array( 'tribe/tickets' );
		$template[] = array( 'tribe/rsvp' );
		$template[] = array( 'tribe/attendees' );
		return $template;
	}

	/**
	 * Check if current admin page is post type `tribe_events`
	 *
	 * @since 4.9
	 *
	 * @param  mixed $post_type
	 *
	 * @return bool
	 */
	public function current_type_support_tickets( $post_type = null ) {
		$post_types = $this->get_enabled_post_types();

		if ( ! is_null( $post_type ) ) {
			return in_array( $post_type, $post_types, true );
		}

		$is_valid_type = false;
		foreach ( $this->get_enabled_post_types() as $post_type ) {
			$is_valid_type = Tribe__Admin__Helpers::instance()->is_post_type_screen( $post_type );
			// Don't operate on following types as current type is valid
			if ( $is_valid_type ) {
				return $is_valid_type;
			}
		}
		return $is_valid_type;
	}

	/**
	 * Making sure we have correct post content for tickets blocks after going into Gutenberg
	 *
	 * @since 4.9
	 *
	 * @param  string  $content Content that will be updated
	 * @param  WP_Post $post    Which post we will migrate
	 * @param  array   $blocks  Which blocks we are updating with
	 *
	 * @return bool
	 */
	public function update_tickets_block_with_childs( $content, $post, $blocks ) {
		$search = '<!-- wp:tribe/tickets  /-->';

		// Do we have a tickets blocks already setup? (we should)
		if ( false === strpos( $content, $search ) ) {
			return $content;
		}

		$replace = $this->get_tickets_as_blocks( $post->ID );
		// Do the actual replace for tickets blocks
		$content = str_replace( $search, implode( "\n\r", $replace ), $content );

		return $content;
	}

	/**
	 * Return an array with all the tickets as a group of items in an array as strings as blocks
	 * formats ready to be inserted into the post_content of a post
	 *
	 * @since 4.9.2
	 *
	 * @param $post_id
	 *
	 * @return array
	 */
	public function get_tickets_as_blocks( $post_id ) {
		$tickets = Tribe__Tickets__Tickets::get_all_event_tickets( $post_id );
		$blocks[] = '<!-- wp:tribe/tickets --><div class="wp-block-tribe-tickets">';

		foreach ( $tickets as $key => $ticket ) {
			// Skip RSVP items
			if ( 'Tribe__Tickets__RSVP' === $ticket->provider_class ) {
				continue;
			}
			// Insert into the replace a single Child ticket
			$blocks[] = '<!-- wp:tribe/tickets-item {"hasBeenCreated":true,"ticketId":' . $ticket->ID . '} --><div class="wp-block-tribe-tickets-item"></div><!-- /wp:tribe/tickets-item -->';
		}

		$blocks[] = '</div><!-- /wp:tribe/tickets -->';

		return $blocks;
	}

	/**
	 * Return the supported post types for tickets
	 *
	 * @return array
	 */
	public function get_enabled_post_types() {
		return (array) tribe_get_option( 'ticket-enabled-post-types', array() );
	}

	/**
	 * Add the event tickets category into the block categories
	 *
	 * @since 4.9
	 *
	 * @param $categories
	 * @param $post
	 * @return array
	 */
	public function block_categories( $categories ) {
		if ( ! $this->current_type_support_tickets() ) {
			return $categories;
		}

		return array_merge(
			$categories,
			array(
				array(
					'slug' => 'tribe-tickets',
					'title' => __( 'Tickets Blocks', 'event-tickets' ),
				),
			)
		);
	}

	/**
	 * Prints and returns the Price fields
	 *
	 * @since 4.9
	 *
	 * @param  int  $post_id    Post ID
	 * @param  int  $ticket_id  Ticket ID
	 *
	 * @return string
	 */
	public function filter_get_price_fields( $post_id, $ticket_id ) {
		$provider = Tribe__Tickets__Tickets::get_event_ticket_provider_object( $post_id );

		$context = [
			'post_id'   => $post_id,
			'ticket_id' => $ticket_id,
			'provider'  => $provider,
		];

		return tribe( 'tickets.admin.views' )->template( 'editor/fieldset/price', $context );
	}

	/**
	 * Prints and returns the Body for the Price Column
	 *
	 * @since 4.9
	 *
	 * @param  Tribe__Tickets__Ticket_Object $ticket        Ticket object
	 * @param  mixed                         $provider_obj  The ticket provider object
	 *
	 * @return string
	 */
	public function add_column_content_price( $ticket, $provider_obj ) {
		/** @var Tribe__Tickets__Admin__Views $admin_views */
		$admin_views = tribe( 'tickets.admin.views' );

		$context = [
			'ticket'       => $ticket,
			'provider_obj' => $provider_obj,
		];

		return $admin_views->template( 'editor/column-body-price', $context );
	}

	/**
	 * Function called by `load-post.php` action when the editor screen is loaded, insert a new meta
	 * field value as a flag into the post / event if the classic editor is displayed and has blocks
	 * in case it has blocks to trigger a new action `tribe_tickets_update_blocks_from_classic_editor`
	 *
	 *
	 * @since 4.9.2
	 *
	 * @return bool
	 */
	public function flush_blocks() {
		/** @var Tribe__Editor $editor */
		$editor = tribe( 'editor' );

		// Bail because we dont have access to any of the classes we need for Blocks Editor.
		if ( ! $editor->should_load_blocks() ) {
			return false;
		}

		$post_id = absint( tribe_get_request_var( 'post' ) );

		if (
			empty( $post_id )
			|| ! is_numeric( $post_id )
			|| ! tribe_events_has_tickets( $post_id )
		) {
			return false;
		}

		/** @var Tribe__Tickets__Editor__Template__Overwrite $template_overwrite */
		$template_overwrite = tribe( 'tickets.editor.template.overwrite' );

		// Set meta key only if is classic editor and bail.
		if ( $template_overwrite->has_classic_editor( $post_id ) ) {
			update_post_meta( $post_id, $this->meta_key_flush_flag, 1 );

			return false;
		}

		/**
		 * Don't process the first time a post is converted into blocks which means has_blocks, will
		 * return false the first time this is called in the post as the conversion from classic into
		 * blocks has not done yet, as this is process by 'tribe_blocks_editor_flag_post_classic_editor'
		 * so we only care if we already have blocks so we can search and remove old tickets blocks to
		 * replace with the new schema of tickets.
		 */
		if ( ! has_blocks( $post_id ) ) {
			return false;
		}

		$has_meta_flush = metadata_exists( 'post', $post_id, $this->meta_key_flush_flag );
		if ( $has_meta_flush ) {
			/**
			 * Fire an action to update the blocks associated with a post
			 *
			 * @since 4.9.2
			 */
			do_action( 'tribe_tickets_update_blocks_from_classic_editor', $post_id );

			return delete_post_meta( $post_id, $this->meta_key_flush_flag );
		}

		return false;
	}

	/**
	 * Update tickets blocks when the action:  `tribe_tickets_update_blocks_from_classic_editor`
	 * is fired. When this action is fired couple of actions happens:
	 *
	 * 1. Remove all the inner blocks and replaces with just the name of the tickets block
	 * 2. Replace the placeholder ticket block with all the tickets associated with the Event / POST as blocks
	 * 3. Update the content of the post
	 *
	 * @since 4.9.2
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function update_blocks( $post_id ) {
		$post = get_post( $post_id );

		if ( ! ( $post instanceof WP_Post ) ) {
			return false;
		}

		/** @var Tribe__Editor__Utils $editor_utils */
		$editor_utils = tribe( 'editor.utils' );
		$block_name   = $editor_utils->to_tribe_block_name( 'tickets' );
		// Replace all the inner blocks with a general block name with no tickets inside.
		$editor_utils->remove_inner_blocks( $post_id, $block_name, "<!-- $block_name  /-->" );

		$content      = get_post_field( 'post_content', $post_id );
		$post_content = $this->update_tickets_block_with_childs( $content, $post, array() );

		return wp_update_post( array(
			'ID'           => $post->ID,
			'post_content' => $post_content,
		) );
	}
}
