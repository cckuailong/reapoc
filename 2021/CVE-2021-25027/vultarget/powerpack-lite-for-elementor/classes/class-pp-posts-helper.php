<?php
namespace PowerpackElementsLite\Classes;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class PP_Posts_Helper.
 */
class PP_Posts_Helper {

	protected static $post_types = array();
	protected static $post_tax   = array();
	protected static $tax_terms  = array();
	protected static $taxonomies = array();

	/**
	 * Get Post Categories.
	 *
	 * @since 1.4.2
	 * @access public
	 */
	public static function get_post_categories() {

		$options = array();

		$terms = get_terms(
			array(
				'taxonomy'   => 'category',
				'hide_empty' => true,
			)
		);

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
		}

		return $options;
	}

	/**
	 * Get Post Types.
	 *
	 * @since 1.4.2
	 * @access public
	 */
	public static function get_post_types() {
		if ( ! empty( self::$post_types ) ) {
			return self::$post_types;
		}

		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);

		$options = array();

		foreach ( $post_types as $post_type ) {
			$options[ $post_type->name ] = $post_type->label;
		}

		self::$post_types = $options;

		return $options;
	}

	/**
	 * Get All Posts.
	 *
	 * @since 1.4.2
	 * @access public
	 */
	public static function get_all_posts() {

		$post_list = get_posts(
			array(
				'post_type'      => 'post',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'posts_per_page' => -1,
			)
		);

		$posts = array();

		if ( ! empty( $post_list ) && ! is_wp_error( $post_list ) ) {
			foreach ( $post_list as $post ) {
				$posts[ $post->ID ] = $post->post_title;
			}
		}

		return $posts;
	}

	/**
	 * Get All Posts by Post Type.
	 *
	 * @since 1.4.2
	 * @param string $post_type Post type.
	 * @access public
	 */
	public static function get_all_posts_by_type( $post_type ) {

		$post_list = get_posts(
			array(
				'post_type'      => $post_type,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'posts_per_page' => -1,
			)
		);

		$posts = array();

		if ( ! empty( $post_list ) && ! is_wp_error( $post_list ) ) {
			foreach ( $post_list as $post ) {
				$posts[ $post->ID ] = $post->post_title;
			}
		}

		return $posts;
	}

	/**
	 * Get Post Taxonomies.
	 *
	 * @since 1.4.2
	 * @param string $post_type Post type.
	 * @access public
	 */
	public static function get_post_taxonomies( $post_type ) {
		$data       = array();
		$taxonomies = array();

		if ( ! empty( self::$post_tax ) ) {
			if ( isset( self::$post_tax[ $post_type ] ) ) {
				$data = self::$post_tax[ $post_type ];
			}
		}

		if ( empty( $data ) ) {
			$taxonomies = get_object_taxonomies( $post_type, 'objects' );

			foreach ( $taxonomies as $tax_slug => $tax ) {

				if ( ! $tax->public || ! $tax->show_ui ) {
					continue;
				}

				$data[ $tax_slug ] = $tax;
			}

			self::$post_tax[ $post_type ] = $data;
		}

		return apply_filters( 'pp_post_loop_taxonomies', $data, $taxonomies, $post_type );
	}

	public static function get_tax_terms( $taxonomy ) {
		$terms = array();

		if ( ! empty( self::$tax_terms ) ) {
			if ( isset( self::$tax_terms[ $taxonomy ] ) ) {
				$terms = self::$tax_terms[ $taxonomy ];
			}
		}

		if ( empty( $terms ) ) {
			$terms                        = get_terms( $taxonomy );
			self::$tax_terms[ $taxonomy ] = $terms;
		}

		return $terms;
	}

	/**
	 * Get list of users.
	 *
	 * @uses   get_users()
	 * @link   https://codex.wordpress.org/Function_Reference/get_users
	 * @since 1.4.2
	 * @return array $user_list data for all users.
	 */
	public static function get_users() {

		$users     = get_users();
		$user_list = array();

		if ( empty( $users ) ) {
			return $user_list;
		}

		foreach ( $users as $user ) {
			$user_list[ $user->ID ] = $user->display_name;
		}

		return $user_list;
	}

	/**
	 * Get Post Tags.
	 *
	 * @since 1.4.2
	 * @access public
	 */
	public static function get_post_tags() {

		$options = array();

		$tags = get_tags();

		foreach ( $tags as $tag ) {
			$options[ $tag->term_id ] = $tag->name;
		}

		return $options;
	}

	/**
	 * Get custom excerpt.
	 *
	 * @since 1.4.2
	 * @param int $limit excerpt length.
	 * @access public
	 */
	public static function custom_excerpt( $limit = '' ) {

		$excerpt = explode( ' ', get_the_excerpt(), $limit );

		if ( count( $excerpt ) >= $limit ) {
			array_pop( $excerpt );
			$excerpt = implode( ' ', $excerpt ) . '...';
		} else {
			$excerpt = implode( ' ', $excerpt );
		}

		$excerpt = preg_replace( '`[[^]]*]`', '', $excerpt );

		return $excerpt;
	}

	/**
	 * Get all available taxonomies
	 *
	 * @since 1.4.7
	 */
	public static function get_taxonomies_options() {
		if ( ! empty( self::$taxonomies ) ) {
			return self::$taxonomies;
		}

		$options = array();

		$taxonomies = get_taxonomies(
			array(
				'show_in_nav_menus' => true,
			),
			'objects'
		);

		if ( empty( $taxonomies ) ) {
			$options[''] = __( 'No taxonomies found', 'powerpack' );
			return $options;
		}

		foreach ( $taxonomies as $taxonomy ) {
			$options[ $taxonomy->name ] = $taxonomy->label;
		}

		self::$taxonomies = $options;

		return $options;
	}
}
