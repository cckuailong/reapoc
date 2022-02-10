<?php

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

if( ! function_exists( 'wp_dropdown_posts' ) ) {

	/**
	 * Create dropdown HTML content of posts
	 *
	 * The content can either be displayed, which it is by default or retrieved by
	 * setting the 'echo' argument. The 'include' and 'exclude' arguments do not
	 * need to be used; all published posts will be displayed in that case.
	 *
	 * Supports all WP_Query arguments
	 * @see https://codex.wordpress.org/Class_Reference/WP_Query
	 *
	 * The available arguments are as follows:
	 *
	 * @author Myles McNamara
	 * @website https://smyl.es
	 * @updated March 29, 2016
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $args {
	 *     Optional. Array or string of arguments to generate a drop-down of posts.
	 *     {@see WP_Query for additional available arguments.
	 *
	 *     @type string       $show_option_all         Text to show as the drop-down default (all).
	 *                                                 Default empty.
	 *     @type string       $show_option_none        Text to show as the drop-down default when no
	 *                                                 posts were found. Default empty.
	 *     @type int|string   $option_none_value       Value to use for $show_option_non when no posts
	 *                                                 were found. Default -1.
	 *     @type array|string $show_callback           Function or method to filter display value (label)
	 *
	 *     @type string       $orderby                 Field to order found posts by.
	 *                                                 Default 'post_title'.
	 *     @type string       $order                   Whether to order posts in ascending or descending
	 *                                                 order. Accepts 'ASC' (ascending) or 'DESC' (descending).
	 *                                                 Default 'ASC'.
	 *     @type array|string $include                 Array or comma-separated list of post IDs to include.
	 *                                                 Default empty.
	 *     @type array|string $exclude                 Array or comma-separated list of post IDs to exclude.
	 *                                                 Default empty.
	 *     @type bool|int     $multi                   Whether to skip the ID attribute on the 'select' element.
	 *                                                 Accepts 1|true or 0|false. Default 0|false.
	 *     @type string       $show                    Post table column to display. If the selected item is empty
	 *                                                 then the Post ID will be displayed in parentheses.
	 *                                                 Accepts post fields. Default 'post_title'.
	 *     @type int|bool     $echo                    Whether to echo or return the drop-down. Accepts 1|true (echo)
	 *                                                 or 0|false (return). Default 1|true.
	 *     @type int          $selected                Which post ID should be selected. Default 0.
	 *     @type string       $select_name             Name attribute of select element. Default 'post_id'.
	 *     @type string       $id                      ID attribute of the select element. Default is the value of $select_name.
	 *     @type string       $class                   Class attribute of the select element. Default empty.
	 *     @type array|string $post_status             Post status' to include, default publish
	 *     @type string       $who                     Which type of posts to query. Accepts only an empty string or
	 *                                                 'authors'. Default empty.
	 * }
	 * @return string String of HTML content.
	 */
	function wp_dropdown_posts( $args = '' ) {

		$defaults = array(
			'selected'              => FALSE,
			'pagination'            => FALSE,
			'posts_per_page'        => - 1,
			'post_status'           => 'publish',
			'cache_results'         => TRUE,
			'cache_post_meta_cache' => TRUE,
			'echo'                  => 1,
			'select_name'           => 'post_id',
			'id'                    => '',
			'class'                 => '',
			'show'                  => 'post_title',
			'show_callback'         => NULL,
			'show_option_all'       => NULL,
			'show_option_none'      => NULL,
			'option_none_value'     => '',
			'multi'                 => FALSE,
			'value_field'           => 'ID',
			'order'                 => 'ASC',
			'orderby'               => 'post_title',
		);

		$r = wp_parse_args( $args, $defaults );

		$posts  = get_posts( $r );
		$output = '';

		$show = $r['show'];

		if( ! empty($posts) ) {

			$name = esc_attr( $r['select_name'] );

			if( $r['multi'] && ! $r['id'] ) {
				$id = '';
			} else {
				$id = $r['id'] ? " id='" . esc_attr( $r['id'] ) . "'" : " id='$name'";
			}

			$output = "<select name='{$name}'{$id} class='" . esc_attr( $r['class'] ) . "'>\n";

			if( $r['show_option_all'] ) {
				$output .= "\t<option value='0'>{$r['show_option_all']}</option>\n";
			}

			if( $r['show_option_none'] ) {
				$_selected = selected( $r['show_option_none'], $r['selected'], FALSE );
				$output .= "\t<option value='" . esc_attr( $r['option_none_value'] ) . "'$_selected>{$r['show_option_none']}</option>\n";
			}

			foreach( (array) $posts as $post ) {

				$value   = ! isset($r['value_field']) || ! isset($post->{$r['value_field']}) ? $post->ID : $post->{$r['value_field']};
				$_selected = selected( $value, $r['selected'], FALSE );

				$display = ! empty($post->$show) ? $post->$show : sprintf( __( '#%d (no title)' ), $post->ID );

				if( $r['show_callback'] ) $display = call_user_func( $r['show_callback'], $display, $post->ID );

				$output .= "\t<option value='{$value}'{$_selected}>" . esc_html( $display ) . "</option>\n";
			}

			$output .= "</select>";
		}

		/**
		 * Filter the HTML output of a list of pages as a drop down.
		 *
		 * @since 1.0.0
		 *
		 * @param string $output HTML output for drop down list of posts.
		 * @param array  $r      The parsed arguments array.
		 * @param array  $posts  List of WP_Post objects returned by `get_posts()`
		 */
		$html = apply_filters( 'wp_dropdown_posts', $output, $r, $posts );

		if( $r['echo'] ) {
			echo $html;
		}

		return $html;
	}

}
