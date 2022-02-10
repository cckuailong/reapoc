<?php


/**
 * Class Tribe__Tickets__Admin__Columns
 *
 * Hooks additional admin columns for supported post types edit pages.
 *
 * This class does not contain the business logic, it only hooks the classes
 * that will handle the logic.
 *
 * @link https://make.wordpress.org/docs/plugin-developer-handbook/10-plugin-components/custom-list-table-columns/#how-do-i-add-custom-list-table-columns
 */
class Tribe__Tickets__Admin__Columns {

	/**
	 * An array of ticket column objects by supported post type.
	 *
	 * @var Tribe__Tickets__Admin__Columns__Tickets[]
	 */
	protected $columns = array();

	/**
	 * Hooks the actions and filters needed to perform this class operations.
	 */
	public function hook() {
		$this->add_custom_columns( (array) tribe_get_option( 'ticket-enabled-post-types', array() ) );
	}

	protected function add_custom_columns( $supported_types ) {
		if ( empty( $supported_types ) ) {
			return true;
		}

		foreach ( $supported_types as $supported_type ) {
			$tickets_column                   = new Tribe__Tickets__Admin__Columns__Tickets( $supported_type );

			add_filter( "manage_{$supported_type}_posts_columns", array( $tickets_column, 'filter_manage_post_columns' ) );
			add_action( "manage_{$supported_type}_posts_custom_column", array( $tickets_column, 'render_column' ), 10, 2 );

			// store the column for later use
			$this->columns[ $supported_type ] = $tickets_column;
		}

		return true;
	}

	/**
	 * Returns the column associated with a post type.
	 *
	 * @param string $post_type The post type slug, e.g. 'post', 'page' or 'tribe_events'.
	 *
	 * @return Tribe__Tickets__Admin__Columns__Tickets|null Either the column object associated with the
	 *                                                      post type or `null` if no column object is
	 *                                                      associated with the post type.
	 */
	public function get_column_for_type( $post_type ) {
		return isset( $this->columns[ $post_type ] ) ? $this->columns[ $post_type ] : null;
	}
}
