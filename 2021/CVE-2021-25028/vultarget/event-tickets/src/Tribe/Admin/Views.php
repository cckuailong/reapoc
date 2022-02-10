<?php
/**
 * Class Tribe__Tickets__Admin__Views
 *
 * Hooks view links handler for supported post types edit pages.
 *
 * "Views" are the links on top of a WordPress admin post list.
 * This class does not contain the business logic, it only hooks the classes
 * that will handle the logic.
 *
 * @link https://make.wordpress.org/docs/plugin-developer-handbook/10-plugin-components/custom-list-table-columns/#views
 */
class Tribe__Tickets__Admin__Views extends Tribe__Template {

	/**
	 * Building of the Class template configuration
	 *
	 * @since  4.6.2
	 */
	public function __construct() {
		$this->set_template_origin( Tribe__Tickets__Main::instance() );
		$this->set_template_folder( 'src/admin-views' );

		// Configures this templating class extract variables
		$this->set_template_context_extract( true );
	}

	/**
	 * Hook the necessary Filters and Actions
	 *
	 * @since  4.6
	 *
	 * @return void
	 */
	public function hook() {
		$this->add_view_links( (array) tribe_get_option( 'ticket-enabled-post-types', array() ) );
	}

	/**
	 * Adds the view links on supported post types admin  lists.
	 *
	 * @param array $supported_types A list of the post types that can have tickets.
	 *
	 * @return bool
	 */
	public function add_view_links( array $supported_types = array() ) {
		if ( empty( $supported_types ) ) {
			return true;
		}

		foreach ( $supported_types as $supported_type ) {
			$ticketed_view = new Tribe__Tickets__Admin__Views__Ticketed( $supported_type );
			add_filter( 'views_edit-' . $supported_type, array( $ticketed_view, 'filter_edit_link' ) );
		}

		return true;
	}
}
