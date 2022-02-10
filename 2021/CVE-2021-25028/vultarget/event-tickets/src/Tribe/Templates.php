<?php

class Tribe__Tickets__Templates extends Tribe__Templates {
	/**
	 * Loads theme files in appropriate hierarchy: 1) child theme,
	 * 2) parent template, 3) plugin resources. will look in the events/
	 * directory in a theme and the views/ directory in the plugin
	 *
	 * @param string $template template file to search for
	 * @param array  $args     additional arguments to affect the template path
	 *                         - namespace
	 *                         - plugin_path
	 *                         - disable_view_check - bypass the check to see if the view is enabled
	 *
	 * @return string
	 **/
	public static function get_template_hierarchy( $template, $args = array() ) {
		if ( ! is_array( $args ) ) {
			$passed        = func_get_args();
			$args          = array();
			$backwards_map = array( 'namespace', 'plugin_path', 'disable_view_check' );
			$count = count( $passed );

			if ( $count > 1 ) {
				for ( $i = 1; $i < $count; $i ++ ) {
					$args[ $backwards_map[ $i - 1 ] ] = $passed[ $i ];
				}
			}
		}

		$args = wp_parse_args(
			$args, array(
				'namespace'          => '/',
				'plugin_path'        => '',
				'disable_view_check' => false,
			)
		);

		$namespace = $args['namespace'];
		$plugin_path = $args['plugin_path'];
		$disable_view_check = $args['disable_view_check'];

		// append .php to file name
		if ( substr( $template, - 4 ) != '.php' ) {
			$template .= '.php';
		}

		/**
		 * Allow base path for templates to be filtered
		 *
		 * @var array
		 */
		$template_base_paths = apply_filters( 'tribe_tickets_template_paths', ( array ) Tribe__Tickets__Main::instance()->plugin_path );

		// backwards compatibility if $plugin_path arg is used
		if ( $plugin_path && ! in_array( $plugin_path, $template_base_paths ) ) {
			array_unshift( $template_base_paths, $plugin_path );
		}

		// ensure that addon plugins look in the right override folder in theme
		$namespace = ! empty( $namespace ) ? trailingslashit( $namespace ) : $namespace;

		$file = false;

		/* potential scenarios:

		- the user has no template overrides
			-> we can just look in our plugin dirs, for the specific path requested, don't need to worry about the namespace
		- the user created template overrides without the namespace, which reference non-overrides without the namespace and, their own other overrides without the namespace
			-> we need to look in their theme for the specific path requested
			-> if not found, we need to look in our plugin views for the file by adding the namespace
		- the user has template overrides using the namespace
			-> we should look in the theme dir, then the plugin dir for the specific path requested, don't need to worry about the namespace

		*/

		// check if there are overrides at all
		if ( locate_template( array( 'tribe-events/' ) ) ) {
			$overrides_exist = true;
		} else {
			$overrides_exist = false;
		}

		if ( $overrides_exist ) {
			// check the theme for specific file requested
			$file = locate_template( array( 'tribe-events/' . $template ), false, false );
		}

		// if the theme file wasn't found, check our plugins views dirs
		if ( ! $file ) {

			foreach ( $template_base_paths as $template_base_path ) {

				// make sure directories are trailingslashed
				$template_base_path = ! empty( $template_base_path ) ? trailingslashit( $template_base_path ) : $template_base_path;

				$file = $template_base_path . 'src/views/' . $template;

				/**
				 * Filter the template file path before inclusion
				 *
				 * @var string File path
				 * @var string Template filename
				 */
				$file = apply_filters( 'tribe_tickets_template', $file, $template );

				// return the first one found
				if ( file_exists( $file ) ) {
					break;
				} else {
					$file = false;
				}
			}
		}

		/**
		 * Filter the template file path before inclusion for the specific requested template
		 *
		 * @var string File path
		 */
		return apply_filters( 'tribe_tickets_template_' . $template, $file );
	}
}
