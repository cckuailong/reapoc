<?php
/**
 * Add theme compatibility things here.
 *
 * @todo  This is an implementation to set a body class we can use in the common implementation.
 *
 * @since   4.11.4
 *
 */

class Tribe__Tickets__Theme_Compatibility {
	/**
	 * List of themes which have compatibility.
	 *
	 * @since 4.11.4
	 *
	 * @var   array
	 */
	protected $themes = [
		'avada',
		'divi',
		'enfold',
		'genesis',
		'twentyfifteen',
		'twentysixteen',
		'twentyseventeen',
		'twentynineteen',
		'twentytwenty',
		'twentytwentyone',
	];

	/**
	 * Checks if theme needs a compatibility fix.
	 *
	 * @since  4.11.4
	 *
	 * @return boolean Whether compatibility is required.
	 */
	public function is_compatibility_required() {
		$template   = strtolower( get_template() );
		$stylesheet = strtolower( get_stylesheet() );

		// Prevents empty stylesheet or template.
		if ( empty( $template ) || empty( $stylesheet ) ) {
			return false;
		}

		if ( in_array( $template, $this->get_registered_themes() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add the theme to the body class.
	 *
	 * @since 4.11.4
	 *
	 * @param  array $classes List of body classes.
	 *
	 * @return array $classes List of body classes, modified if compatibility is required.
	 */
	public function filter_body_class( array $classes ) {

		if ( ! $this->is_compatibility_required() ) {
			return $classes;
		}

		return array_merge( $classes, $this->get_body_classes() );
	}

	/**
	 * Fetches the correct class strings for theme and child theme if available.
	 *
	 * @since 4.11.4
	 *
	 * @return array $classes List of body classes with parent and child theme classes included.
	 */
	public function get_body_classes() {
		$classes      = [];
		$child_theme  = strtolower( get_stylesheet() );
		$parent_theme = strtolower( get_template() );

		// Prevents empty stylesheet or template.
		if ( empty( $parent_theme ) || empty( $child_theme ) ) {
			return $classes;
		}

		$classes[] = sanitize_html_class( "tribe-theme-$parent_theme" );

		// if the 2 options are the same, then there is no child theme.
		if ( $child_theme !== $parent_theme ) {
			$classes[] = sanitize_html_class( "tribe-theme-parent-$parent_theme" );
			$classes[] = sanitize_html_class( "tribe-theme-child-$child_theme" );
		}

		return $classes;
	}

	/**
	 * Returns a list of themes registered for compatibility with our Views.
	 *
	 * @since  4.11.4
	 *
	 * @return array An array of the themes registered.
	 */
	public function get_registered_themes() {
		/**
		 * Filters the list of themes that are registered for compatibility.
		 *
		 * @since 4.11.4
		 *
		 * @param array $registered An associative array of views in the shape `[ <slug> => <class> ]`.
		 */
		$registered = apply_filters( 'tribe_tickets_theme_compatibility_registered', $this->themes );

		return (array) $registered;
	}
}
