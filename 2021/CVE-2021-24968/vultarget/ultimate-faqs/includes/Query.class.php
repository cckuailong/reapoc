<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewdufaqQuery' ) ) {
/**
 * Class to handle common queries used to pull faqs from
 * the database.
 *
 * Bookings can be retrieved with specific date ranges, common
 * date params (today/upcoming), etc. This class is intended for
 * the base plugin as well as extensions or custom projects which
 * need a stable mechanism for reliably retrieving faqs data.
 *
 * Queries return an array of ewdufaqReview objects.
 *
 * @since 2.0.0
 */
class ewdufaqQuery {

	/**
	 * Bookings
	 *
	 * Array of faqs retrieved after get_faqs() is called
	 *
	 * @since 2.0.0
	 */
	public $faqs = array();

	/**
	 * Query args
	 *
	 * Passed to WP_Query
	 * http://codex.wordpress.org/Class_Reference/WP_Query
	 *
	 * @since 2.0.0
	 */
	public $args = array();

	/**
	 * Query context
	 *
	 * Defines the context in which the query is run.
	 * Useful for hooking into the right query without
	 * tampering with others.
	 *
	 * @since 2.0.0
	 */
	public $context;

	/**
	 * Instantiate the query with an array of arguments
	 *
	 * This supports all WP_Query args as well as several
	 * short-hand arguments for common needs. Short-hands
	 * include:
	 *
	 * date_range string today|upcoming|dates
	 * start_date string don't get faqs before this
	 * end_date string don't get faqs after this
	 *
	 * @see ewdufaqQuery::prepare_args()
	 * @param args array Options to tailor the query
	 * @param context string Context for the query, used
	 *		in filters
	 * @since 2.0.0
	 */
	public function __construct( $args = array(), $context = '' ) {

		global $ewd_ufaq_controller;

		$defaults = array(
			'post_type'			=> EWD_UFAQ_FAQ_POST_TYPE,
			'posts_per_page'	=> -1,
			'post_status'		=> 'publish',
			'order'				=> 'DESC',
			'paged'				=> 1,
		);

		$this->args = wp_parse_args( $args, $defaults );

		$this->context = $context;

	}

	/**
	 * Parse the args array and convert custom arguments
	 * for use by WP_Query
	 *
	 * @since 2.0.0
	 */
	public function prepare_args() {
		global $ewd_ufaq_controller;

		$args = $this->args;

		$this->include_children = ( ! empty( $args['include_children'] ) and strtolower( $args['include_children'] ) != 'yes' ) ? false : true;

		if ( ! empty( $args['search_string'] ) ) { $args['s'] = $args['search_string']; }

		if ( ! empty( $args['post_count'] ) ) { $args['posts_per_page'] = $args['post_count']; }
		
		if ( ! empty( $args['faq_page'] ) ) { $args['paged'] = $args['faq_page']; }

		if ( ! empty( $args['post__in'] ) ) { $args['post__in'] = is_array( $args['post__in'] ) ? $args['post__in'] : json_decode( str_replace( array( '&lsqb;', '&rsqb;' ), array( '[', ']' ) ) ); }

		if ( ! empty( $args['post__in_string'] ) ) { $args['post__in'] = ! empty( $args['post__in'] ) ? array_merge( $args['post__in'], explode( ',', $args['post__in_string'] ) ) : explode( ',', $args['post__in_string'] ); }

		$tax_query = array();

		if ( ! empty( $args['include_category'] ) ) {

			$include_category_array = explode( ',', $args['include_category'] );
				
			$tax_query[] = array( 
				'taxonomy' 			=> EWD_UFAQ_FAQ_CATEGORY_TAXONOMY,
				'field' 			=> 'slug',
				'terms' 			=> $include_category_array,
				'include_children' 	=> $this->include_children
			);
		}

		if ( ! empty( $args['exclude_category'] ) ) {

			$exclude_category_array = explode( ',', $args['exclude_category'] );
				
			$tax_query[] = array( 
				'taxonomy' 			=> EWD_UFAQ_FAQ_CATEGORY_TAXONOMY,
				'field' 			=> 'slug',
				'operator'			=> 'NOT IN',
				'terms' 			=> $exclude_category_array,
				'include_children' 	=> $this->include_children
			);
		}

		if ( ! empty( $args['include_category_ids'] ) ) {

			$include_category_ids_array = explode( ',', $args['include_category_ids'] );
				
			$tax_query[] = array( 
				'taxonomy' 			=> EWD_UFAQ_FAQ_CATEGORY_TAXONOMY,
				'field' 			=> 'term_id',
				'terms' 			=> $include_category_ids_array,
				'include_children' 	=> $this->include_children
			);
		}

		if ( ! empty( $args['exclude_category_ids'] ) ) {

			$exclude_category_ids_array = explode( ',', $args['exclude_category_ids'] );
				
			$tax_query[] = array( 
				'taxonomy' 			=> EWD_UFAQ_FAQ_CATEGORY_TAXONOMY,
				'field' 			=> 'term_id',
				'operator'			=> 'NOT IN',
				'terms' 			=> $exclude_category_ids_array,
				'include_children' 	=> $this->include_children
			);
		}

		if ( ! empty( $args['include_tag'] ) ) {

			$include_tag_ids_array = explode( ',', $args['include_tag'] );
				
			$tax_query[] = array( 
				'taxonomy' 	=> EWD_UFAQ_FAQ_TAG_TAXONOMY,
				'field' 	=> 'slug',
				'terms' 	=> $include_tag_ids_array
			);
		}
		
		if ( ! empty( $tax_query ) ) { $args['tax_query'] = $tax_query; }

		/*$meta_query = array();

		if ( ! empty( $args['product_name'] ) ) { 

			$meta_query[] = array(
				'key' => 'EWD_UFAQ_Product_Name',
				'value' => $args['product_name'],
				'compare' => '=',
			); 
		}

		if ( ! empty( $meta_query ) ) { $args['meta_query'] = $meta_query; }*/

		$orderby = array();

		if ( empty( $args['product_name'] ) and $ewd_ufaq_controller->settings->get_setting( 'group-by-product' ) ) {

			$orderby['meta_value'] = $ewd_ufaq_controller->settings->get_setting( 'group-by-product-order' );
		}

		if ( ! empty( $args['orderby'] ) ) {

			if ( $args['orderby'] == 'rating' or $args['orderby'] == 'popular' ) { $orderby['meta_value_num'] = $args['order']; }
			elseif ( $args['orderby'] == 'date' ) { $orderby['date'] = $args['order']; }
			elseif ( $args['orderby'] == 'title' ) { $orderby['title'] = $args['order']; }
			elseif ( $args['orderby'] == 'set_order' ) { $orderby['meta_value_num'] = $args['order']; }
		}

		if ( $args['orderby'] == 'rating' ) { $args['meta_key'] = 'FAQ_Total_Score'; }
		elseif ( $args['orderby'] == 'popular' ) { $args['meta_key'] = 'ufaq_view_count'; }
		elseif ( $args['orderby'] == 'set_order' ) { $args['meta_key'] = 'ufaq_order'; }
		
		if ( ! empty( $orderby ) ) { $args['orderby'] = $orderby; }

		$this->args = $args;

		return $this->args;
	}

	/**
	 * Parse $_REQUEST args and store in $this->args
	 *
	 * @since 2.0.0
	 */
	public function parse_request_args() {

		$args = array();

		if ( isset( $_REQUEST['faq_page'] ) ) { 

			$args['faq_page'] = intval( $_REQUEST['faq_page'] ); 
		}

		if ( isset( $_REQUEST['search_string'] ) ) { 

			$args['search_string'] = sanitize_text_field( stripslashes( $_REQUEST['search_string'] ) ); 
		}

		if ( isset( $_REQUEST['post_count'] ) ) { 

			$args['post_count'] = sanitize_text_field( $_REQUEST['post_count'] ); 
		}

		if ( isset( $_REQUEST['orderby'] ) ) { 

			$args['orderby'] = sanitize_text_field( $_REQUEST['orderby'] ); 
		}

		if ( isset( $_REQUEST['order'] ) ) { 

			$args['order'] = sanitize_text_field( $_REQUEST['order'] ); 
		}

		if ( isset( $_REQUEST['include_category'] ) ) { 

			$args['include_category'] = sanitize_text_field( $_REQUEST['include_category'] ); 
		}

		if ( isset( $_REQUEST['exclude_category'] ) ) { 

			$args['exclude_category'] = sanitize_text_field( $_REQUEST['exclude_category'] ); 
		}

		if ( get_query_var( 'ufaq_category_slug' ) ) { 

			$args['include_category'] = get_query_var( 'ufaq_category_slug' ); 
		}

		if ( isset( $_REQUEST['include_tag'] ) ) { 

			$args['include_tag'] = sanitize_text_field( $_REQUEST['include_tag'] ); 
		}

		if ( get_query_var( 'ufaq_tag_slug' ) ) { 

			$args['include_tag'] = get_query_var( 'ufaq_tag_slug' ); 
		}

		$this->args = array_merge( $this->args, $args ); 
	}

	/**
	 * Retrieve query results
	 *
	 * @since 2.0.0
	 */
	public function get_faqs() {

		$faqs = array();

		$args = apply_filters( 'ewd_ufaq_query_args', $this->args, $this->context ); 
		
		$query = new WP_Query( $args );
		
		if ( $query->have_posts() ) {

			while( $query->have_posts() ) {
				$query->the_post();

				$faq = new ewdufaqFaq();
				if ( $faq->load_post( $query->post ) ) {
					$faqs[] = $faq;
				}
			}
		}

		$this->found_faqs = $query->found_posts;

		$this->current_page = $this->args['paged'];
		
		$this->max_page = $query->max_num_pages;
		
		$this->faqs = $faqs;

		wp_reset_query();

		return $this->faqs;
	}
}
} // endif
