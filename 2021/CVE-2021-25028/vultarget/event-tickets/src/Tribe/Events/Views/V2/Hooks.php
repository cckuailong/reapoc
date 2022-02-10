<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * remove_filter( 'some_filter', [ tribe( Tribe\Tickets\Events\Views\V2\Hooks::class ), 'some_filtering_method' ] );
 * remove_filter( 'some_filter', [ tribe( 'tickets.views.v2.hooks' ), 'some_filtering_method' ] );
 *
 * To remove an action:
 * remove_action( 'some_action', [ tribe( Tribe\Tickets\Events\Views\V2\Hooks::class ), 'some_method' ] );
 * remove_action( 'some_action', [ tribe( 'tickets.views.v2.hooks' ), 'some_method' ] );
 *
 * @since 4.10.9
 *
 * @package Tribe\Tickets\Events\Views\V2
 */

namespace Tribe\Tickets\Events\Views\V2;

use Tribe\Tickets\Events\Views\V2\Models\Tickets;
use Tribe__Tickets__Main as Plugin;
use Tribe__Template;

/**
 * Class Hooks.
 *
 * @since 4.10.9
 *
 * @package Tribe\Tickets\Events\Views\V2
 */
class Hooks extends \tad_DI52_ServiceProvider {
	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.10.9
	 */
	public function register() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Filters the list of folders TEC will look up to find templates to add the ones defined by Tickets.
	 *
	 * @since 4.10.9
	 *
	 * @param array           $folders  The current list of folders that will be searched template files.
	 * @param Tribe__Template $template Which template instance we are dealing with.
	 *
	 * @return array The filtered list of folders that will be searched for the templates.
	 */
	public function filter_template_path_list( array $folders, Tribe__Template $template ) {
		/** @var Plugin $main */
		$main = tribe( 'tickets.main' );

		$path = (array) rtrim( $main->plugin_path, '/' );

		// Pick up if the folder needs to be added to the public template path.
		$folder = $template->get_template_folder();

		if ( ! empty( $folder ) ) {
			$path = array_merge( $path, $folder );
		}

		$folders['event-tickets'] = [
			'id'        => 'event-tickets',
			'namespace' => $main->template_namespace,
			'priority'  => 17,
			'path'      => implode( DIRECTORY_SEPARATOR, $path ),
		];

		return $folders;
	}

	/**
	 * Includes Tickets into the path namespace mapping, allowing for a better namespacing when loading files.
	 *
	 * @since 4.11.2
	 *
	 * @param array            $namespace_map Indexed array containing the namespace as the key and path to `strpos`.
	 * @param string           $path          Path we will do the `strpos` to validate a given namespace.
	 * @param Tribe__Template  $template      Current instance of the template class.
	 *
	 * @return array  Namespace map after adding Pro to the list.
	 */
	public function filter_add_template_origin_namespace( $namespace_map, $path, $template ) {
		/** @var Plugin $main */
		$main = tribe( 'tickets.main' );
		$namespace_map[ $main->template_namespace ] = $main->plugin_path;
		return $namespace_map;
	}

	/**
	 * Add tickets data to the event object.
	 *
	 * @since 4.10.9
	 *
	 * @param array    $props An associative array of all the properties that will be set on the "decorated" post
	 *                        object.
	 * @param \WP_Post $post  The post object handled by the class.
	 *
	 * @return array The model properties. This value might be cached.
	 */
	public function add_tickets_data( $props, $event ) {
		$props['tickets'] = new Tickets( $event->ID );

		return $props;
	}

	/**
	 * Adds the actions required by each Tickets Views v2 component.
	 *
	 * @since 4.10.9
	 */
	protected function add_actions() {
		// silence is golden
	}

	/**
	 * Adds the filters required by each Tickets Views v2 component.
	 *
	 * @since 4.10.9
	 */
	protected function add_filters() {
		add_filter( 'tribe_template_path_list', [ $this, 'filter_template_path_list' ], 15, 2 );
		add_filter( 'tribe_template_origin_namespace_map', [ $this, 'filter_add_template_origin_namespace' ], 15, 3 );
		add_filter( 'tribe_post_type_events_properties', [ $this, 'add_tickets_data' ], 20, 2 );
	}
}
