<?php

if ( ! class_exists( 'Tribe__JSON_LD__Abstract' ) ) {
	return;
}

/**
 * JSON_LD class used to register any other post type that is not an Event and has support for tickets
 *
 * @since 4.7.1
 */
class Tribe__Tickets__JSON_LD__Type extends Tribe__JSON_LD__Abstract {

	/**
	 * Function used to attach the hook into wp_head
	 *
	 * @since 4.7.1
	 */
	public static function hook() {
		add_action( 'wp_head', array( self::instance(), 'markup' ) );
	}

	/**
	 * On PHP 5.2 the child class doesn't get spawned on the Parent one, so we don't have
	 * access to that information on the other side unless we pass it around as a param
	 * so we throw __CLASS__ to the parent::instance() method to be able to spawn new instance
	 * of this class and save on the parent::$instances variable.
	 *
	 * @since 4.7.1
	 *
	 * @return Tribe__Events__JSON_LD__Event
	 */
	public static function instance( $name = null ) {
		return parent::instance( __CLASS__ );
	}

	/**
	 * Function called by the wp_head hook to attach the markup into the page if that's the case, once does the setup
	 * calls the parent method to do the work to generate the script.
	 *
	 * @since 4.7.1
	 *
	 * @param mixed|null $post The ID of the post or array of posts
	 * @param array $args The arguments used to register the data on this type
	 *
	 * @return mixed|void
	 */
	public function markup( $post = null, $args = array() ) {

		// Is not a the single view of a post type
		if ( ! is_singular() ) {
			return;
		}

		$post_type = get_post_type();

		// The post type is the Event type so this is done already not need to be done for this type
		if ( class_exists( 'Tribe__Events__Main' ) && $post_type === Tribe__Events__Main::POSTTYPE ) {
			return;
		}

		// Does this post has tickets?
		if ( ! tribe_events_has_tickets( get_the_ID() ) ) {
			return;
		}

		/**
		 * This will allow you to change the type for the Rich Snippet, by default it will use the type Product for
		 * any Post type or Page. If this is runs in a book post type the filter becomes something like.
		 *
		 * @example tribe_events_json_ld_book_type
		 *
		 * @see http://schema.org/Product
		 *
		 * @see https://developers.google.com/structured-data/rich-snippets/
		 *
		 * @since 4.7.1
		 *
		 * @param string $post_type The name fo the registered post type
		 */
		$this->type = apply_filters( "tribe_tickets_json_ld_{$post_type}_type", 'Product' );

		parent::markup();
	}

	/**
	 * Fetches the JSON-LD data for this type of object
	 *
	 * @param  int|WP_Post|null $post The post/event
	 * @param  array  $args
	 *
	 * @return array
	 */
	public function get_data( $posts = null, $args = array() ) {
		// Fetch the global post object if no posts are provided
		if ( ! is_array( $posts ) && empty( $posts ) ) {
			$posts = array( $GLOBALS['post'] );
		}  else {
			// If we only received a single post object, wrap it in an array
			$posts = ( $posts instanceof WP_Post ) ? array( $posts ) : (array) $posts;
		}

		$return = array();

		foreach ( $posts as $i => $post ) {
			// We may have been passed a post ID - let's ensure we have the post object
			if ( is_numeric( $post ) ) {
				$post = get_post( $post );
			}

			// If we don't have a valid post object, skip to the next item
			if ( ! $post instanceof WP_Post ) {
				continue;
			}

			$data = parent::get_data( $post, $args );

			// If we have an Empty data we just skip
			if ( empty( $data ) ) {
				continue;
			}

			// Fetch first key
			$post_id = key( $data );

			// Fetch first Value
			$data = reset( $data );

			$data = $this->apply_object_data_filter( $data, $args, $post );

			$return[ $post_id ] = $data;
		}

		return $return;
	}
}
