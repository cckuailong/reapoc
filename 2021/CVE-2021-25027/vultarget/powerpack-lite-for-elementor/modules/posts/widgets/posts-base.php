<?php
namespace PowerpackElementsLite\Modules\Posts\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Posts_Helper;

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Posts Grid Widget
 */
abstract class Posts_Base extends Powerpack_Widget {

	/**
	 * WP_Query
	 *
	 * @var $query
	 */
	protected $query         = null;
	protected $query_filters = null;

	protected $_has_template_content = false;

	/**
	 * Retrieve posts grid widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-group power-pack-admin-icon';
	}

	public function get_script_depends() {
		return array(
			'isotope',
			'imagesloaded',
			'pp-slick',
			'powerpack-frontend-posts',
			'powerpack-pp-posts',
			'powerpack-frontend',
		);
	}

	/**
	 * Register posts grid widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	public function register_query_section_controls( $condition = array(), $widget_type = 'posts', $old_code = '', $advanced_controls = 'no' ) {
		$post_types = PP_Posts_Helper::get_post_types();

		/**
		 * Content Tab: Query
		 */
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'powerpack' ),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'query_type',
			array(
				'label'       => __( 'Query Type', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'custom',
				'label_block' => true,
				'options'     => array(
					'main'   => __( 'Main Query', 'powerpack' ),
					'custom' => __( 'Custom Query', 'powerpack' ),
				),
			)
		);

		$post_types            = PP_Posts_Helper::get_post_types();
		$post_types['related'] = __( 'Related', 'powerpack' );

		$this->add_control(
			'post_type',
			array(
				'label'     => __( 'Post Type', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $post_types,
				'default'   => 'post',
				'condition' => array(
					'query_type' => 'custom',
				),

			)
		);

		foreach ( $post_types as $post_type_slug => $post_type_label ) {

			$taxonomy = PP_Posts_Helper::get_post_taxonomies( $post_type_slug );

			if ( ! empty( $taxonomy ) ) {

				foreach ( $taxonomy as $index => $tax ) {

					$terms = PP_Posts_Helper::get_tax_terms( $index );

					$tax_terms = array();

					if ( ! empty( $terms ) ) {

						foreach ( $terms as $term_index => $term_obj ) {

							$tax_terms[ $term_obj->term_id ] = $term_obj->name;
						}

						$tax_control_key = $index . '_' . $post_type_slug;

						if ( 'yes' === $old_code ) {
							if ( $post_type_slug == 'post' ) {
								if ( $index == 'post_tag' ) {
									$tax_control_key = 'tags';
								} elseif ( $index == 'category' ) {
									$tax_control_key = 'categories';
								}
							}
						}

						// Taxonomy filter type.
						$this->add_control(
							$index . '_' . $post_type_slug . '_filter_type',
							array(
								/* translators: %s Label */
								'label'       => sprintf( __( '%s Filter Type', 'powerpack' ), $tax->label ),
								'type'        => Controls_Manager::SELECT,
								'default'     => 'IN',
								'label_block' => true,
								'options'     => array(
									/* translators: %s label */
									'IN'     => sprintf( __( 'Include %s', 'powerpack' ), $tax->label ),
									/* translators: %s label */
									'NOT IN' => sprintf( __( 'Exclude %s', 'powerpack' ), $tax->label ),
								),
								'separator'   => 'before',
								'condition'   => array(
									'query_type' => 'custom',
									'post_type'  => $post_type_slug,
								),
							)
						);

						$this->add_control(
							$tax_control_key,
							array(
								'label'        => $tax->label,
								'type'         => 'pp-query',
								'post_type'    => $post_type_slug,
								'options'      => array(),
								'label_block'  => true,
								'multiple'     => true,
								'query_type'   => 'terms',
								'object_type'  => $index,
								'include_type' => true,
								'condition'    => array(
									'query_type' => 'custom',
									'post_type'  => $post_type_slug,
								),
							)
						);

					}
				}
			}
		}

		$this->add_control(
			'author_filter_type',
			array(
				'label'       => __( 'Authors Filter Type', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'author__in',
				'label_block' => true,
				'separator'   => 'before',
				'options'     => array(
					'author__in'     => __( 'Include Authors', 'powerpack' ),
					'author__not_in' => __( 'Exclude Authors', 'powerpack' ),
				),
				'condition'   => array(
					'query_type' => 'custom',
					'post_type!' => 'related',
				),
			)
		);

		$this->add_control(
			'authors',
			array(
				'label'       => __( 'Authors', 'powerpack' ),
				'type'        => 'pp-query',
				'label_block' => true,
				'multiple'    => true,
				'query_type'  => 'authors',
				'condition'   => array(
					'query_type' => 'custom',
					'post_type!' => 'related',
				),
			)
		);

		foreach ( $post_types as $post_type_slug => $post_type_label ) {
			$this->add_control(
				$post_type_slug . '_filter_type',
				array(
					/* translators: %s: post type label */
					'label'       => sprintf( __( '%s Filter Type', 'powerpack' ), $post_type_label ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'post__not_in',
					'label_block' => true,
					'separator'   => 'before',
					'options'     => array(
						/* translators: %s: post type label */
						'post__in'     => sprintf( __( 'Include %s', 'powerpack' ), $post_type_label ),
						/* translators: %s: post type label */
						'post__not_in' => sprintf( __( 'Exclude %s', 'powerpack' ), $post_type_label ),
					),
					'condition'   => array(
						'query_type' => 'custom',
						'post_type'  => $post_type_slug,
					),
				)
			);

			$this->add_control(
				$post_type_slug . '_filter',
				array(
					/* translators: %s Label */
					'label'       => $post_type_label,
					'type'        => 'pp-query',
					'default'     => '',
					'multiple'    => true,
					'label_block' => true,
					'query_type'  => 'posts',
					'object_type' => $post_type_slug,
					'condition'   => array(
						'query_type' => 'custom',
						'post_type'  => $post_type_slug,
					),
				)
			);
		}

		$taxonomy   = PP_Posts_Helper::get_post_taxonomies( $post_type_slug );
		$taxonomies = array();
		foreach ( $taxonomy as $index => $tax ) {
			$taxonomies[ $tax->name ] = $tax->label;
		}

		$this->start_controls_tabs(
			'tabs_related',
			array(
				'condition' => array(
					'query_type' => 'custom',
					'post_type'  => 'related',
				),
			)
		);

		$this->start_controls_tab(
			'tab_related_include',
			array(
				'label'     => __( 'Include', 'powerpack' ),
				'condition' => array(
					'query_type' => 'custom',
					'post_type'  => 'related',
				),
			)
		);

		$this->add_control(
			'related_include_by',
			array(
				'label'       => __( 'Include By', 'powerpack' ),
				'type'        => Controls_Manager::SELECT2,
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'options'     => array(
					'terms'   => __( 'Term', 'powerpack' ),
					'authors' => __( 'Author', 'powerpack' ),
				),
				'condition'   => array(
					'query_type' => 'custom',
					'post_type'  => 'related',
				),
			)
		);

		$this->add_control(
			'related_filter_include',
			array(
				'label'       => __( 'Term', 'powerpack' ),
				'type'        => Controls_Manager::SELECT2,
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'options'     => PP_Posts_Helper::get_taxonomies_options(),
				'condition'   => array(
					'query_type'         => 'custom',
					'post_type'          => 'related',
					'related_include_by' => 'terms',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_related_exclude',
			array(
				'label'     => __( 'Exclude', 'powerpack' ),
				'condition' => array(
					'query_type' => 'custom',
					'post_type'  => 'related',
				),
			)
		);

		$this->add_control(
			'related_exclude_by',
			array(
				'label'       => __( 'Exclude By', 'powerpack' ),
				'type'        => Controls_Manager::SELECT2,
				'default'     => '',
				'label_block' => true,
				'multiple'    => true,
				'options'     => array(
					'current_post' => __( 'Current Post', 'powerpack' ),
					'authors'      => __( 'Author', 'powerpack' ),
				),
				'condition'   => array(
					'query_type' => 'custom',
					'post_type'  => 'related',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'related_fallback',
			array(
				'label'       => __( 'Fallback', 'powerpack' ),
				'description' => __( 'Displayed if no relevant results are found.', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'none'   => __( 'None', 'powerpack' ),
					'recent' => __( 'Recent Posts', 'powerpack' ),
				),
				'default'     => 'none',
				'label_block' => false,
				'separator'   => 'before',
				'condition'   => array(
					'query_type' => 'custom',
					'post_type'  => 'related',
				),
			)
		);

		$this->add_control(
			'select_date',
			array(
				'label'       => __( 'Date', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'anytime' => __( 'All', 'powerpack' ),
					'today'   => __( 'Past Day', 'powerpack' ),
					'week'    => __( 'Past Week', 'powerpack' ),
					'month'   => __( 'Past Month', 'powerpack' ),
					'quarter' => __( 'Past Quarter', 'powerpack' ),
					'year'    => __( 'Past Year', 'powerpack' ),
					'exact'   => __( 'Custom', 'powerpack' ),
				),
				'default'     => 'anytime',
				'label_block' => false,
				'multiple'    => false,
				'separator'   => 'before',
				'condition'   => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'date_before',
			array(
				'label'       => __( 'Before', 'powerpack' ),
				'description' => __( 'Setting a ‘Before’ date will show all the posts published until the chosen date (inclusive).', 'powerpack' ),
				'type'        => Controls_Manager::DATE_TIME,
				'label_block' => false,
				'multiple'    => false,
				'placeholder' => __( 'Choose', 'powerpack' ),
				'condition'   => array(
					'query_type'  => 'custom',
					'select_date' => 'exact',
				),
			)
		);

		$this->add_control(
			'date_after',
			array(
				'label'       => __( 'After', 'powerpack' ),
				'description' => __( 'Setting an ‘After’ date will show all the posts published since the chosen date (inclusive).', 'powerpack' ),
				'type'        => Controls_Manager::DATE_TIME,
				'label_block' => false,
				'multiple'    => false,
				'placeholder' => __( 'Choose', 'powerpack' ),
				'condition'   => array(
					'query_type'  => 'custom',
					'select_date' => 'exact',
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'     => __( 'Order', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'DESC' => __( 'Descending', 'powerpack' ),
					'ASC'  => __( 'Ascending', 'powerpack' ),
				),
				'default'   => 'DESC',
				'separator' => 'before',
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => __( 'Order By', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'date'          => __( 'Date', 'powerpack' ),
					'modified'      => __( 'Last Modified Date', 'powerpack' ),
					'rand'          => __( 'Random', 'powerpack' ),
					'comment_count' => __( 'Comment Count', 'powerpack' ),
					'title'         => __( 'Title', 'powerpack' ),
					'ID'            => __( 'Post ID', 'powerpack' ),
					'author'        => __( 'Post Author', 'powerpack' ),
					'menu_order'    => __( 'Menu Order', 'powerpack' ),
					'relevance'     => __( 'Relevance', 'powerpack' ),
				),
				'default'   => 'date',
				'condition' => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'sticky_posts',
			array(
				'label'        => __( 'Sticky Posts', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'separator'    => 'before',
				'condition'    => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'all_sticky_posts',
			array(
				'label'        => __( 'Show Only Sticky Posts', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'query_type'   => 'custom',
					'sticky_posts' => 'yes',
				),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'       => __( 'Offset', 'powerpack' ),
				'description' => __( 'Use this setting to skip this number of initial posts', 'powerpack' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'min'         => 0,
				'condition'   => array(
					'query_type' => 'custom',
					'post_type!' => 'related',
				),
			)
		);

		$this->add_control(
			'exclude_current',
			array(
				'label'        => __( 'Exclude Current Post', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Enable this option to remove current post from the query.', 'powerpack' ),
				'condition'    => array(
					'query_type' => 'custom',
				),
			)
		);

		$this->add_control(
			'query_id',
			array(
				'label'       => __( 'Query ID', 'powerpack' ),
				'description' => __( 'Give your Query a custom unique id to allow server side filtering', 'powerpack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'separator'   => 'before',
			)
		);

		if ( 'yes' === $advanced_controls ) {
			$this->add_control(
				'heading_nothing_found',
				array(
					'label'     => __( 'If Nothing Found!', 'powerpack' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'nothing_found_message',
				array(
					'label'   => __( 'Nothing Found Message', 'powerpack' ),
					'type'    => Controls_Manager::TEXTAREA,
					'rows'    => 3,
					'default' => __( 'It seems we can\'t find what you\'re looking for.', 'powerpack' ),
				)
			);

			$this->add_control(
				'show_search_form',
				array(
					'label'        => __( 'Show Search Form', 'powerpack' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'powerpack' ),
					'label_off'    => __( 'No', 'powerpack' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Get post query arguments.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function query_posts_args( $filter = '', $taxonomy_filter = '', $search = '', $all_posts = '', $paged_args = '', $widget_type = 'posts', $old_code = '', $posts_count_var = '', $posts_count = '' ) {
		$settings  = $this->get_settings_for_display();
		$paged     = ( 'yes' === $paged_args ) ? $this->get_paged() : '';
		$tax_count = 0;

		if ( 'main' === $settings['query_type'] ) {
			$current_query_vars = $GLOBALS['wp_query']->query_vars;
			return apply_filters( "ppe_{$widget_type}_query_args", $current_query_vars, $settings );
		}

		$query_args = array(
			'post_status'         => array( 'publish' ),
			'orderby'             => $settings['orderby'],
			'order'               => $settings['order'],
			'ignore_sticky_posts' => ( 'yes' === $settings['sticky_posts'] ) ? 0 : 1,
			'posts_per_page'      => -1,
		);

		if ( ! $posts_count ) {
			$posts_per_page = ( $posts_count_var ) ? $settings[ $posts_count_var ] : ( isset( $settings['posts_per_page'] ) ? $settings['posts_per_page'] : '' );
		} else {
			$posts_per_page = $posts_count;
		}

		if ( '' === $all_posts ) {
			$query_args['posts_per_page'] = $posts_per_page;
		}

		if ( 'related' === $settings['post_type'] ) {

			$related_terms = $settings['related_filter_include'];
			$post_terms    = wp_get_object_terms( get_the_ID(), $settings['related_filter_include'], array( 'fields' => 'ids' ) );

			// Query Arguments.
			$query_args['post_type'] = get_post_type();

			if ( ! empty( $settings['related_include_by'] ) ) {
				if ( in_array( 'authors', $settings['related_include_by'], true ) ) {
					$query_args['author'] = get_the_author_meta( 'ID' );
				}

				if ( in_array( 'terms', $settings['related_include_by'], true ) ) {
					if ( ! empty( $related_terms ) && ! is_wp_error( $related_terms ) ) {

						foreach ( $related_terms as $index => $tax ) {

							$query_args['tax_query'][] = array(
								'taxonomy' => $tax,
								'field'    => 'term_id',
								'terms'    => $post_terms,
							);

						}
					}
				}
			}

			if ( ! empty( $settings['related_exclude_by'] ) ) {
				if ( in_array( 'current_post', $settings['related_exclude_by'], true ) ) {
					$query_args['post__not_in'] = array( get_the_ID() );
				}

				if ( in_array( 'authors', $settings['related_exclude_by'], true ) ) {
					$query_args['author'] = '-' . get_the_author_meta( 'ID' );
				}
			}

			if ( 'recent' === $settings['related_fallback'] ) {
				$query = $this->get_query();

				if ( ! $query->found_posts ) {
					$query_args = array(
						'post_status'         => array( 'publish' ),
						'post_type'           => get_post_type(),
						'orderby'             => $settings['orderby'],
						'order'               => $settings['order'],
						'ignore_sticky_posts' => ( 'yes' === $settings['sticky_posts'] ) ? 0 : 1,
						'showposts'           => $posts_per_page,
					);
				}
			}
		} else {

			// Query Arguments.
			$query_args['post_type'] = $settings['post_type'];
			if ( 0 < $settings['offset'] ) {

				/**
				 * Offset break the pagination. Using WordPress's work around
				 *
				 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
				 */
				$query_args['offset_to_fix'] = $settings['offset'];
			}
			$query_args['paged'] = $paged;

			// Author Filter.
			if ( ! empty( $settings['authors'] ) ) {
				$query_args[ $settings['author_filter_type'] ] = $settings['authors'];
			}

			// Posts Filter.
			$post_type = $settings['post_type'];

			if ( ! empty( $settings[ $post_type . '_filter' ] ) ) {
				$query_args[ $settings[ $post_type . '_filter_type' ] ] = $settings[ $post_type . '_filter' ];
			}

			// Taxonomy Filter.
			$taxonomy = PP_Posts_Helper::get_post_taxonomies( $post_type );

			$tax_cat_in     = '';
			$tax_cat_not_in = '';
			$tax_tag_in     = '';
			$tax_tag_not_in = '';

			if ( ! empty( $taxonomy ) && ! is_wp_error( $taxonomy ) ) {

				foreach ( $taxonomy as $index => $tax ) {

					$tax_control_key = $index . '_' . $post_type;

					if ( 'yes' === $old_code ) {
						if ( 'post' === $post_type ) {
							if ( 'post_tag' === $index ) {
								$tax_control_key = 'tags';
							} elseif ( 'category' === $index ) {
								$tax_control_key = 'categories';
							}
						}
					}

					if ( ! empty( $settings[ $tax_control_key ] ) ) {

						$operator = $settings[ $index . '_' . $post_type . '_filter_type' ];

						$query_args['tax_query'][] = array(
							'taxonomy' => $index,
							'field'    => 'term_id',
							'terms'    => $settings[ $tax_control_key ],
							'operator' => $operator,
						);

						switch ( $index ) {
							case 'category':
								if ( 'IN' === $operator ) {
									$tax_cat_in = $settings[ $tax_control_key ];
								} elseif ( 'NOT IN' === $operator ) {
									$tax_cat_not_in = $settings[ $tax_control_key ];
								}
								break;

							case 'post_tag':
								if ( 'IN' === $operator ) {
									$tax_tag_in = $settings[ $tax_control_key ];
								} elseif ( 'NOT IN' === $operator ) {
									$tax_tag_not_in = $settings[ $tax_control_key ];
								}
								break;
						}
					}
				}
			}

			if ( '' !== $filter && '*' !== $filter ) {
				// Taxonomy Filter.
				$taxonomy = PP_Posts_Helper::get_post_taxonomies( $post_type );

				$tax_cat_in     = '';
				$tax_cat_not_in = '';
				$tax_tag_in     = '';
				$tax_tag_not_in = '';

				if ( ! empty( $taxonomy ) && ! is_wp_error( $taxonomy ) ) {

					foreach ( $taxonomy as $index => $tax ) {

						$tax_control_key = $index . '_' . $post_type;

						if ( 'yes' === $old_code ) {
							if ( 'post' === $post_type ) {
								if ( 'post_tag' === $index ) {
									$tax_control_key = 'tags';
								} elseif ( 'category' === $index ) {
									$tax_control_key = 'categories';
								}
							}
						}

						if ( ! empty( $settings[ $tax_control_key ] ) ) {

							$operator = $settings[ $index . '_' . $post_type . '_filter_type' ];

							$query_args['tax_query'][] = array(
								'taxonomy' => $index,
								'field'    => 'term_id',
								'terms'    => $settings[ $tax_control_key ],
								'operator' => $operator,
							);

							switch ( $index ) {
								case 'category':
									if ( 'IN' === $operator ) {
										$tax_cat_in = $settings[ $tax_control_key ];
									} elseif ( 'NOT IN' === $operator ) {
										$tax_cat_not_in = $settings[ $tax_control_key ];
									}
									break;

								case 'post_tag':
									if ( 'IN' === $operator ) {
										$tax_tag_in = $settings[ $tax_control_key ];
									} elseif ( 'NOT IN' === $operator ) {
										$tax_tag_not_in = $settings[ $tax_control_key ];
									}
									break;
							}
						}
					}
				}

				$query_args['tax_query'][ $tax_count ]['taxonomy'] = $taxonomy_filter;
				$query_args['tax_query'][ $tax_count ]['field']    = 'slug';
				$query_args['tax_query'][ $tax_count ]['terms']    = $filter;
				$query_args['tax_query'][ $tax_count ]['operator'] = 'IN';

				/* if ( ! empty( $tax_cat_in ) ) {
					$query_args['category__in'] = $tax_cat_in;
				}

				if ( ! empty( $tax_cat_not_in ) ) {
					$query_args['category__not_in'] = $tax_cat_not_in;
				}

				if ( ! empty( $tax_tag_in ) ) {
					$query_args['tag__in'] = $tax_tag_in;
				}

				if ( ! empty( $tax_tag_not_in ) ) {
					$query_args['tag__not_in'] = $tax_tag_not_in;
				} */
			}

			if ( '' !== $search ) {
				$query_args['s'] = $search;
			}
		}

		if ( 'anytime' !== $settings['select_date'] ) {
			$select_date = $settings['select_date'];
			if ( ! empty( $select_date ) ) {
				$date_query = array();
				if ( 'today' === $select_date ) {
					$date_query['after'] = '-1 day';
				} elseif ( 'week' === $select_date ) {
					$date_query['after'] = '-1 week';
				} elseif ( 'month' === $select_date ) {
					$date_query['after'] = '-1 month';
				} elseif ( 'quarter' === $select_date ) {
					$date_query['after'] = '-3 month';
				} elseif ( 'year' === $select_date ) {
					$date_query['after'] = '-1 year';
				} elseif ( 'exact' === $select_date ) {
					$after_date = $settings['date_after'];
					if ( ! empty( $after_date ) ) {
						$date_query['after'] = $after_date;
					}
					$before_date = $settings['date_before'];
					if ( ! empty( $before_date ) ) {
						$date_query['before'] = $before_date;
					}
					$date_query['inclusive'] = true;
				}

				$query_args['date_query'] = $date_query;
			}
		}

		// Sticky Posts Filter.
		if ( 'yes' === $settings['sticky_posts'] && 'yes' === $settings['all_sticky_posts'] ) {
			$post__in = get_option( 'sticky_posts' );

			$query_args['ignore_sticky_posts'] = 1;
			$query_args['post__in'] = $post__in;
		}

		// Exclude current post.
		if ( 'yes' === $settings['exclude_current'] ) {
			if ( is_singular() ) {
				$query_args['post__not_in'] = array( get_queried_object_id() );
			}
		}

		return apply_filters( "ppe_{$widget_type}_query_args", $query_args, $settings );
	}

	/**
	 * pre_get_posts_query_filter
	 *
	 * @param  mixed $wp_query
	 */
	public function pre_get_posts_query_filter( $wp_query ) {
		$settings = $this->get_settings_for_display();

		$query_id = $settings['query_id'];
		/**
		 * Query args.
		 *
		 * It allows developers to alter individual posts widget queries.
		 *
		 * The dynamic portion of the hook name '$query_id', refers to the Query ID.
		 *
		 * @since 1.4.11.3
		 *
		 * @param \WP_Query     $wp_query
		 */
		do_action_deprecated( "pp_query_{$query_id}", [ $wp_query ], '2.3.7', "powerpack/query/{$query_id}" );
		do_action( "powerpack/query/{$query_id}", $wp_query );

	}

	public function query_posts( $filter = '', $taxonomy = '', $search = '', $all_posts = '', $paged_args = '', $widget_type = 'posts', $old_code = '', $posts_count_var = '', $posts_count = '' ) {
		$settings = $this->get_settings_for_display();
		$query_id = $settings['query_id'];

		if ( ! empty( $query_id ) ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts_query_filter' ) );
		}
		$query_args = $this->query_posts_args( $filter, $taxonomy, $search, '', 'yes', $widget_type, $old_code, $posts_count_var, $posts_count );

		$post_type = $settings['post_type'];
		$offset_control = $settings['offset'];

		if ( 'related' !== $post_type && 0 < $offset_control ) {
			add_action( 'pre_get_posts', [ $this, 'fix_query_offset' ], 1 );
			add_filter( 'found_posts', [ $this, 'fix_query_found_posts' ], 1, 2 );
		}

		$this->query = new \WP_Query( $query_args );

		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts_query_filter' ) );
		remove_action( 'pre_get_posts', [ $this, 'fix_query_offset' ], 1 );
		remove_filter( 'found_posts', [ $this, 'fix_query_found_posts' ], 1 );
	}

	public function query_filters_posts( $filter = '', $taxonomy = '', $search = '' ) {
		$settings = $this->get_settings();
		$query_id = $settings['query_id'];

		if ( ! empty( $query_id ) ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts_query_filter' ) );
		}
		$query_filter_args   = $this->query_posts_args( $filter, $taxonomy, $search, 'yes', 'yes' );
		$this->query_filters = new \WP_Query( $query_filter_args );
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts_query_filter' ) );
	}

	/**
	 * Render current query.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	public function get_query() {

		return $this->query;
	}

	/**
	 * Render current query.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	public function get_query_filters() {

		return $this->query_filters;
	}

	/**
	 * Returns the paged number for the query.
	 *
	 * @since 1.7.0
	 * @return int
	 */
	public function get_paged() {
		$settings = $this->get_settings_for_display();

		global $wp_the_query, $paged;

		if ( isset( $settings['_skin'] ) ) {
			$skin_id         = $settings['_skin'];
			$pagination_ajax = $settings[ $skin_id . '_pagination_ajax' ];
			$pagination_type = $settings[ $skin_id . '_pagination_type' ];
		} else {
			$pagination_ajax = '';
			$pagination_type = '';
		}

		if ( 'yes' === $pagination_ajax || 'load_more' === $pagination_type || 'infinite' === $pagination_type ) {
			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'pp-posts-widget-nonce' ) ) {
				if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
					return $_POST['page_number'];
				}
			}

			// Check the 'paged' query var.
			$paged_qv = $wp_the_query->get( 'paged' );

			if ( is_numeric( $paged_qv ) ) {
				return $paged_qv;
			}

			// Check the 'page' query var.
			$page_qv = $wp_the_query->get( 'page' );

			if ( is_numeric( $page_qv ) ) {
				return $page_qv;
			}

			// Check the $paged global?
			if ( is_numeric( $paged ) ) {
				return $paged;
			}

			return 0;
		} else {
			return max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
		}
	}

	public function get_posts_nav_link( $page_limit = null ) {
		if ( ! $page_limit ) {
			$page_limit = $this->query->max_num_pages;
		}

		$return = array();

		$paged = $this->get_paged();

		$link_template     = '<a class="page-numbers %s" href="%s">%s</a>';
		$disabled_template = '<span class="page-numbers %s">%s</span>';

		if ( $paged > 1 ) {
			$next_page = intval( $paged ) - 1;
			if ( $next_page < 1 ) {
				$next_page = 1;
			}

			$return['prev'] = sprintf( $link_template, 'prev', $this->get_wp_link_page( $next_page ), $this->get_settings( 'pagination_prev_label' ) );
		} else {
			$return['prev'] = sprintf( $disabled_template, 'prev', $this->get_settings( 'pagination_prev_label' ) );
		}

		$next_page = intval( $paged ) + 1;

		if ( $next_page <= $page_limit ) {
			$return['next'] = sprintf( $link_template, 'next', $this->get_wp_link_page( $next_page ), $this->get_settings( 'pagination_next_label' ) );
		} else {
			$return['next'] = sprintf( $disabled_template, 'next', $this->get_settings( 'pagination_next_label' ) );
		}

		return $return;
	}

	private function get_wp_link_page( $i ) {
		if ( ! is_singular() || is_front_page() ) {
			return get_pagenum_link( $i );
		}

		// Based on wp-includes/post-template.php:957 `_wp_link_page`.
		global $wp_rewrite;
		$post       = get_post();
		$query_args = array();
		$url        = get_permalink();

		if ( $i > 1 ) {
			if ( '' === get_option( 'permalink_structure' ) || in_array( $post->post_status, array( 'draft', 'pending' ), true ) ) {
				$url = add_query_arg( 'page', $i, $url );
			} elseif ( get_option( 'show_on_front' ) === 'page' && (int) get_option( 'page_on_front' ) === $post->ID ) {
				$url = trailingslashit( $url ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
			} else {
				$url = trailingslashit( $url ) . user_trailingslashit( $i, 'single_paged' );
			}
		}

		if ( is_preview() ) {
			if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) { //phpcs:ignore
				$query_args['preview_id']    = wp_unslash( $_GET['preview_id'] ); //phpcs:ignore
				$query_args['preview_nonce'] = wp_unslash( $_GET['preview_nonce'] ); //phpcs:ignore
			}

			$url = get_preview_post_link( $post, $query_args, $url );
		}

		return $url;
	}

	/**
	 * @param \WP_Query $query
	 *
	 * @since 2.3.6
	 */
	public function fix_query_offset( &$query ) {
		$settings = $this->get_settings_for_display();
		$offset = $settings['offset'];

		if ( $offset && $query->is_paged ) {
			$query->query_vars['offset'] = $offset + ( ( $query->query_vars['paged'] - 1 ) * $query->query_vars['posts_per_page'] );
		} else {
			$query->query_vars['offset'] = $offset;
		}
	}

	/**
	 * @param int       $found_posts
	 * @param \WP_Query $query
	 *
	 * @since 2.3.6
	 *
	 * @return int
	 */
	public function fix_query_found_posts( $found_posts, $query ) {
		$settings = $this->get_settings_for_display();
		$offset = $settings['offset'];

		if ( $offset ) {
			$found_posts -= $offset;
		}

		return $found_posts;
	}
}
