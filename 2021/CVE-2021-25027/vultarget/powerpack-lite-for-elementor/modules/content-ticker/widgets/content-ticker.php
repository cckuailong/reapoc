<?php
namespace PowerpackElementsLite\Modules\ContentTicker\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Helper;
use PowerpackElementsLite\Classes\PP_Config;
use PowerpackElementsLite\Classes\PP_Posts_Helper;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Content Ticker Widget
 */
class Content_Ticker extends Powerpack_Widget {

	/**
	 * Retrieve content ticker widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Content_Ticker' );
	}

	/**
	 * Retrieve content ticker widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Content_Ticker' );
	}

	/**
	 * Retrieve content ticker widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Content_Ticker' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.4.13.1
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Content_Ticker' );
	}

	/**
	 * Retrieve the list of scripts the content ticker widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'swiper',
			'powerpack-frontend',
		);
	}

	/**
	 * Register content ticker widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register content ticker widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.0.3
	 * @access protected
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_general_controls();
		$this->register_content_meta_controls();
		$this->register_content_ticker_items_controls();
		$this->register_query_section_controls();
		$this->register_content_heading_controls();
		$this->register_content_ticker_settings_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_heading_controls();
		$this->register_style_content_controls();
		$this->register_style_image_controls();
		$this->register_style_arrows_controls();
	}

	/**
	 * Register Content Ticker General Controls.
	 *
	 * @access protected
	 */
	protected function register_content_general_controls() {
		/**
		 * Content Tab: General
		 */
		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'General', 'powerpack' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => __( 'Source', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'custom' => __( 'Custom', 'powerpack' ),
					'posts'  => __( 'Posts', 'powerpack' ),
				),
				'default' => 'posts',
			)
		);

		$this->add_control(
			'link_type',
			array(
				'label'   => __( 'Link Type', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''      => __( 'None', 'powerpack' ),
					'title' => __( 'Title', 'powerpack' ),
					'image' => __( 'Image', 'powerpack' ),
					'both'  => __( 'Title + Image', 'powerpack' ),
				),
				'default' => '',
			)
		);

		$this->add_control(
			'posts_count',
			array(
				'label'     => __( 'Posts Count', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 4,
				'condition' => array(
					'source' => 'posts',
				),
			)
		);

		$this->add_control(
			'post_image',
			array(
				'label'        => __( 'Post Image', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'show',
				'label_on'     => __( 'Show', 'powerpack' ),
				'label_off'    => __( 'Hide', 'powerpack' ),
				'return_value' => 'show',
				'condition'    => array(
					'source' => 'posts',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'image_size',
				'label'     => __( 'Image Size', 'powerpack' ),
				'default'   => 'medium_large',
				'condition' => array(
					'source'     => 'posts',
					'post_image' => 'show',
				),
			)
		);

		$this->add_control(
			'title_html_tag',
			array(
				'label'   => __( 'Title HTML Tag', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => array(
					'h1'   => __( 'H1', 'powerpack' ),
					'h2'   => __( 'H2', 'powerpack' ),
					'h3'   => __( 'H3', 'powerpack' ),
					'h4'   => __( 'H4', 'powerpack' ),
					'h5'   => __( 'H5', 'powerpack' ),
					'h6'   => __( 'H6', 'powerpack' ),
					'div'  => __( 'div', 'powerpack' ),
					'span' => __( 'span', 'powerpack' ),
					'p'    => __( 'p', 'powerpack' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Content Ticker Post Meta Controls.
	 *
	 * @access protected
	 */
	protected function register_content_meta_controls() {
		/**
		 * Content Tab: Post Meta
		 */
		$this->start_controls_section(
			'section_post_meta',
			array(
				'label'     => __( 'Post Meta', 'powerpack' ),
				'condition' => array(
					'source' => 'posts',
				),
			)
		);

		$this->add_control(
			'post_meta',
			array(
				'label'              => __( 'Post Meta', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'label_on'           => __( 'Show', 'powerpack' ),
				'label_off'          => __( 'Hide', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					'source' => 'posts',
				),
			)
		);

		$this->add_control(
			'post_date',
			array(
				'label'              => __( 'Date', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'label_on'           => __( 'Show', 'powerpack' ),
				'label_off'          => __( 'Hide', 'powerpack' ),
				'return_value'       => 'yes',
				'condition'          => array(
					'source'    => 'posts',
					'post_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			'post_time',
			array(
				'label'              => __( 'Time', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'label_on'           => __( 'Show', 'powerpack' ),
				'label_off'          => __( 'Hide', 'powerpack' ),
				'return_value'       => 'yes',
				'condition'          => array(
					'source'    => 'posts',
					'post_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			'datetime_separator',
			array(
				'label'       => __( 'Date Time Separator', 'powerpack' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => 'at',
				'condition'          => array(
					'source'    => 'posts',
					'post_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			'datetime_icon',
			array(
				'label'            => __( 'Date Time Icon', 'powerpack' ),
				'type'             => Controls_Manager::ICONS,
				'label_block'      => false,
				'skin'             => 'inline',
				'default'          => array(
					'value'   => 'fas fa-calendar-alt',
					'library' => 'fa-solid',
				),
				'recommended'     => array(
					'fa-regular' => array(
						'calendar',
						'calendar-alt',
						'calendar-check',
						'calendar-day',
						'clock',
					),
					'fa-solid'   => array(
						'calendar',
						'calendar-alt',
						'calendar-check',
						'clock',
					),
				),
				'conditions'       => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'source',
							'operator' => '==',
							'value'    => 'posts',
						),
						array(
							'name'     => 'post_meta',
							'operator' => '==',
							'value'    => 'yes',
						),
						array(
							'relation' => 'or',
							'terms'    => array(
								array(
									'name'     => 'post_date',
									'operator' => '==',
									'value'    => 'yes',
								),
								array(
									'name'     => 'post_time',
									'operator' => '==',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'post_author',
			array(
				'label'              => __( 'Author', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					'source'    => 'posts',
					'post_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			'select_author_icon',
			array(
				'label'            => __( 'Author Icon', 'powerpack' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'author_icon',
				'label_block'      => false,
				'skin'             => 'inline',
				'default'          => array(
					'value'   => 'fas fa-user',
					'library' => 'fa-solid',
				),
				'recommended'     => array(
					'fa-regular' => array(
						'user',
						'user-circle',
					),
					'fa-solid'   => array(
						'user',
						'user-alt',
						'user-check',
						'user-circle',
						'user-graduate',
						'user-md',
						'user-nurse',
						'user-secret',
						'user-tie',
					),
				),
				'condition'        => array(
					'source'      => 'posts',
					'post_author' => 'yes',
					'post_meta'   => 'yes',
				),
			)
		);

		$this->add_control(
			'post_category',
			array(
				'label'              => __( 'Category', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					'source'    => 'posts',
					'post_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			'select_category_icon',
			array(
				'label'            => __( 'Category Icon', 'powerpack' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'category_icon',
				'label_block'      => false,
				'skin'             => 'inline',
				'default'          => array(
					'value'   => 'fas fa-folder-open',
					'library' => 'fa-solid',
				),
				'recommended'     => array(
					'fa-regular' => array(
						'folder',
						'folder-open',
					),
					'fa-solid'   => array(
						'folder',
						'folder-open',
						'tag',
					),
				),
				'condition'        => array(
					'source'        => 'posts',
					'post_category' => 'yes',
					'post_meta'     => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Content Ticker Items Controls.
	 *
	 * @access protected
	 */
	protected function register_content_ticker_items_controls() {
		/**
		 * Content Tab: Ticker Items
		 */
		$this->start_controls_section(
			'section_ticker_items',
			array(
				'label'     => __( 'Ticker Items', 'powerpack' ),
				'condition' => array(
					'source' => 'custom',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'ticker_items_tabs' );

		$repeater->start_controls_tab( 'tab_ticker_items_content', array( 'label' => __( 'Content', 'powerpack' ) ) );

			$repeater->add_control(
				'ticker_title',
				array(
					'label'       => __( 'Title', 'powerpack' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => false,
					'default'     => '',
				)
			);

			$repeater->add_control(
				'link',
				array(
					'label'       => __( 'Link', 'powerpack' ),
					'type'        => Controls_Manager::URL,
					'dynamic'     => array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::URL_CATEGORY,
						),
					),
					'placeholder' => 'https://www.your-link.com',
					'default'     => array(
						'url' => '',
					),
				)
			);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'tab_ticker_items_image', array( 'label' => __( 'Image', 'powerpack' ) ) );

		$repeater->add_control(
			'ticker_image',
			array(
				'label'        => __( 'Show Image', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label'      => __( 'Choose Image', 'powerpack' ),
				'type'       => \Elementor\Controls_Manager::MEDIA,
				'default'    => array(
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				),
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'ticker_image',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'       => 'image',
				'exclude'    => array( 'custom' ),
				'include'    => array(),
				'default'    => 'large',
				'conditions' => array(
					'terms' => array(
						array(
							'name'     => 'ticker_image',
							'operator' => '==',
							'value'    => 'yes',
						),
					),
				),
			)
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'items',
			array(
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'ticker_title' => __( 'Content Ticker Item 1', 'powerpack' ),
					),
					array(
						'ticker_title' => __( 'Content Ticker Item 2', 'powerpack' ),
					),
					array(
						'ticker_title' => __( 'Content Ticker Item 3', 'powerpack' ),
					),
					array(
						'ticker_title' => __( 'Content Ticker Item 4', 'powerpack' ),
					),
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ ticker_title }}}',
				'condition'   => array(
					'source' => 'custom',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Content Query Controls.
	 *
	 * @access protected
	 */
	protected function register_query_section_controls() {
		/**
		 * Content Tab: Query
		 */
		$this->start_controls_section(
			'section_post_query',
			array(
				'label'     => __( 'Query', 'powerpack' ),
				'condition' => array(
					'source' => 'posts',
				),
			)
		);

		$this->add_control(
			'post_type',
			array(
				'label'     => __( 'Post Type', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => PP_Posts_Helper::get_post_types(),
				'default'   => 'post',
				'condition' => array(
					'source' => 'posts',
				),
			)
		);

		$post_types = PP_Posts_Helper::get_post_types();

		foreach ( $post_types as $post_type_slug => $post_type_label ) {

			$taxonomy = PP_Posts_Helper::get_post_taxonomies( $post_type_slug );

			if ( ! empty( $taxonomy ) ) {

				foreach ( $taxonomy as $index => $tax ) {

					$terms = get_terms( $index );

					$tax_terms = array();

					if ( ! empty( $terms ) ) {

						foreach ( $terms as $term_index => $term_obj ) {

							$tax_terms[ $term_obj->term_id ] = $term_obj->name;
						}

						if ( 'post_tag' === $index ) {
							$tax_control_key = 'tags';
						} elseif ( 'category' === $index ) {
							$tax_control_key = 'categories';
						} else {
							$tax_control_key = $index . '_' . $post_type_slug;
						}

						// Taxonomy filter type
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
									'source'    => 'posts',
									'post_type' => $post_type_slug,
								),
							)
						);

						// Add control for all taxonomies.
						/*
						$this->add_control(
							$tax_control_key,
							[
								'label'       => $tax->label,
								'type'        => Controls_Manager::SELECT2,
								'multiple'    => true,
								'default'     => '',
								'label_block' => true,
								'options'     => $tax_terms,
								'condition'   => [
									'source'    => 'posts',
									'post_type' => $post_type_slug,
								],
							]
						);*/

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
									'source'    => 'posts',
									'post_type' => $post_type_slug,
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
					'source' => 'posts',
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
					'source' => 'posts',
				),
			)
		);

		$post_types = PP_Posts_Helper::get_post_types();

		foreach ( $post_types as $post_type_slug => $post_type_label ) {

			$posts_all = PP_Posts_Helper::get_all_posts_by_type( $post_type_slug );

			if ( 'post' === $post_type_slug ) {
				$posts_control_key = 'exclude_posts';
			} else {
				$posts_control_key = $post_type_slug . '_filter';
			}

			$this->add_control(
				$post_type_slug . '_filter_type',
				array(
					'label'       => sprintf( __( '%s Filter Type', 'powerpack' ), $post_type_label ),
					'type'        => Controls_Manager::SELECT,
					'default'     => 'post__not_in',
					'label_block' => true,
					'separator'   => 'before',
					'options'     => array(
						'post__in'     => sprintf( __( 'Include %s', 'powerpack' ), $post_type_label ),
						'post__not_in' => sprintf( __( 'Exclude %s', 'powerpack' ), $post_type_label ),
					),
					'condition'   => array(
						'source'    => 'posts',
						'post_type' => $post_type_slug,
					),
				)
			);

			// $this->add_control(
			// $posts_control_key,
			// [
			// * translators: %s Label */
			// 'label'       => $post_type_label,
			// 'type'        => Controls_Manager::SELECT2,
			// 'default'     => '',
			// 'multiple'     => true,
			// 'label_block' => true,
			// 'options'     => $posts_all,
			// 'condition'   => [
			// 'source'    => 'posts',
			// 'post_type' => $post_type_slug,
			// ],
			// ]
			// );

			$this->add_control(
				$posts_control_key,
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
						'source'    => 'posts',
						'post_type' => $post_type_slug,
					),
				)
			);
		}

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
					'source' => 'posts',
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
					'source'      => 'posts',
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
					'source'      => 'posts',
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
					'source' => 'posts',
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
				),
				'default'   => 'date',
				'condition' => array(
					'source' => 'posts',
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
					'source' => 'posts',
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
					'source'       => 'posts',
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
				'separator'   => 'before',
				'condition'   => array(
					'source' => 'posts',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Content Ticker Heading Controls.
	 *
	 * @access protected
	 */
	protected function register_content_heading_controls() {
		/**
		 * Content Tab: Heading
		 */
		$this->start_controls_section(
			'section_heading',
			array(
				'label' => __( 'Header', 'powerpack' ),
			)
		);

		$this->add_control(
			'show_heading',
			array(
				'label'        => __( 'Show Heading', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'     => __( 'Heading Text', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Trending Now', 'powerpack' ),
				'condition' => array(
					'show_heading' => 'yes',
				),
			)
		);

		$this->add_control(
			'selected_icon',
			array(
				'label'            => __( 'Icon', 'powerpack' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'heading_icon',
				'default'          => array(
					'value'   => 'fas fa-bolt',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'show_heading' => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_icon_position',
			array(
				'label'       => __( 'Icon Position', 'powerpack' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'toggle'      => false,
				'default'     => 'left',
				'options'     => array(
					'left'  => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
			)
		);

		$this->add_control(
			'heading_arrow',
			array(
				'label'        => __( 'Arrow', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'show_heading' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Content Ticker Settings Controls.
	 *
	 * @access protected
	 */
	protected function register_content_ticker_settings_controls() {
		/**
		 * Content Tab: Ticker Settings
		 */
		$this->start_controls_section(
			'section_additional_options',
			array(
				'label' => __( 'Ticker Settings', 'powerpack' ),
			)
		);

		$this->add_control(
			'ticker_effect',
			array(
				'label'       => __( 'Effect', 'powerpack' ),
				'description' => __( 'Sets transition effect', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'fade',
				'options'     => array(
					'slide' => __( 'Slide', 'powerpack' ),
					'fade'  => __( 'Fade', 'powerpack' ),
				),
			)
		);

		$this->add_control(
			'slider_speed',
			array(
				'label'       => __( 'Slider Speed', 'powerpack' ),
				'description' => __( 'Duration of transition between slides (in ms)', 'powerpack' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => array( 'size' => 400 ),
				'range'       => array(
					'px' => array(
						'min'  => 100,
						'max'  => 3000,
						'step' => 1,
					),
				),
				'size_units'  => '',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'              => __( 'Autoplay', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'pause_on_interaction',
			array(
				'label'        => __( 'Pause on Interaction', 'powerpack' ),
				'description'  => __( 'Disables autoplay completely on first interaction with the carousel.', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'              => __( 'Autoplay Speed', 'powerpack' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 3000,
				'min'                => 500,
				'max'                => 5000,
				'step'               => 1,
				'frontend_available' => true,
				'condition'          => array(
					'autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'              => __( 'Infinite Loop', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'grab_cursor',
			array(
				'label'              => __( 'Grab Cursor', 'powerpack' ),
				'description'        => __( 'Shows grab cursor when you hover over the slider', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'navigation_heading',
			array(
				'label'     => __( 'Navigation', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'        => __( 'Arrows', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links( 'Content_Ticker' );
		if ( ! empty( $help_docs ) ) {
			/**
			 * Content Tab: Docs Links
			 *
			 * @since 2.4.1
			 * @access protected
			 */
			$this->start_controls_section(
				'section_help_docs',
				[
					'label' => __( 'Help Docs', 'powerpack' ),
				]
			);

			$hd_counter = 1;
			foreach ( $help_docs as $hd_title => $hd_link ) {
				$this->add_control(
					'help_doc_' . $hd_counter,
					[
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => sprintf( '%1$s ' . $hd_title . ' %2$s', '<a href="' . $hd_link . '" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'pp-editor-doc-links',
					]
				);

				$hd_counter++;
			}

			$this->end_controls_section();
		}
	}

	/**
	 * Register Content Ticker Heading Controls in Style Tabs.
	 *
	 * @access protected
	 */
	protected function register_style_heading_controls() {
		/**
		 * Style Tab: Heading
		 */
		$this->start_controls_section(
			'section_heading_style',
			array(
				'label'     => __( 'Header', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_heading' => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_alignment',
			array(
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-text-lign-left',
					),
					'center' => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-text-lign-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-text-lign-right',
					),
				),
				'default'   => 'left',
				'prefix_class' => 'pp-content-ticker-heading-',
			)
		);

		$this->add_responsive_control(
			'heading_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-content-ticker-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_bg',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-heading' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-content-ticker-heading:after' => 'border-left-color: {{VALUE}}',
				),
				'condition' => array(
					'show_heading' => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-heading' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-content-ticker-heading .pp-icon svg' => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'show_heading' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'heading_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-content-ticker-heading',
				'condition'   => array(
					'show_heading' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'selector' => '{{WRAPPER}} .pp-content-ticker-heading',
			)
		);

		$this->add_responsive_control(
			'heading_width',
			array(
				'label'      => __( 'Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 20,
						'max'  => 500,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-content-ticker-heading' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'show_heading' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Content Ticker Content Controls in Style Tabs.
	 *
	 * @access protected
	 */
	protected function register_style_content_controls() {
		/**
		 * Style Tab: Content
		 */
		$this->start_controls_section(
			'section_content_ticker_style',
			array(
				'label' => __( 'Content', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-content-ticker-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'content_tabs' );

		$this->start_controls_tab( 'tab_content_normal', array( 'label' => __( 'Normal', 'powerpack' ) ) );

		$this->add_control(
			'content_bg',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-container' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'content_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-content-ticker-container',
			)
		);

		$this->add_control(
			'content_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-content-ticker-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_title',
			array(
				'label'     => __( 'Title', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'title_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-item-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'selector' => '{{WRAPPER}} .pp-content-ticker-item-title',
			)
		);

		$this->add_responsive_control(
			'title_margin_bottom',
			array(
				'label'      => __( 'Bottom Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-content-ticker-item-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_meta',
			array(
				'label'     => __( 'Post Meta', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'source'    => 'posts',
					'post_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-meta' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-content-ticker-meta .pp-icon svg' => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'source'    => 'posts',
					'post_meta' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'meta_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'selector'  => '{{WRAPPER}} .pp-content-ticker-meta',
				'condition' => array(
					'source'    => 'posts',
					'post_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			'meta_items_spacing',
			array(
				'label'     => __( 'Items Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => '',
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-meta > span:not(:last-child)'   => 'margin-right: {{SIZE}}px;',
				),
				'condition' => array(
					'source'    => 'posts',
					'post_meta' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'tab_content_hover', array( 'label' => __( 'Hover', 'powerpack' ) ) );

		$this->add_control(
			'content_bg_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-container:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'content_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-container:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'content_title_color_hover',
			array(
				'label'     => __( 'Title Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-item-title:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'meta_color_hover',
			array(
				'label'     => __( 'Post Meta Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-meta > span:hover' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'source'    => 'posts',
					'post_meta' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Content Ticker Image Controls in Style Tabs.
	 *
	 * @access protected
	 */
	protected function register_style_image_controls() {
		/**
		 * Style Tab: Image
		 */
		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => __( 'Image', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'image_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-content-ticker-image',
			)
		);

		$this->add_control(
			'image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-content-ticker-image, {{WRAPPER}} .pp-content-ticker-image:after, {{WRAPPER}} .pp-content-ticker-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => __( 'Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-content-ticker-image' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_margin',
			array(
				'label'       => __( 'Margin', 'powerpack' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array( 'px', '%' ),
				'placeholder' => array(
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				),
				'selectors'   => array(
					'{{WRAPPER}} .pp-content-ticker-image' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Content Ticker Arrows Controls in Style Tabs.
	 *
	 * @access protected
	 */
	protected function register_style_arrows_controls() {
		/**
		 * Style Tab: Arrows
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_arrows_style',
			array(
				'label'     => __( 'Arrows', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'arrows' => 'yes',
				),
			)
		);

		$this->add_control(
			'select_arrow',
			array(
				'label'                  => __( 'Choose Arrow', 'powerpack' ),
				'type'                   => Controls_Manager::ICONS,
				'fa4compatibility'       => 'arrow',
				'label_block'            => false,
				'default'                => array(
					'value'   => 'fas fa-angle-right',
					'library' => 'fa-solid',
				),
				'skin'                   => 'inline',
				'exclude_inline_options' => 'svg',
				'recommended'            => array(
					'fa-regular' => array(
						'arrow-alt-circle-right',
						'caret-square-right',
						'hand-point-right',
					),
					'fa-solid'   => array(
						'angle-right',
						'angle-double-right',
						'chevron-right',
						'chevron-circle-right',
						'arrow-right',
						'long-arrow-alt-right',
						'caret-right',
						'caret-square-right',
						'arrow-circle-right',
						'arrow-alt-circle-right',
						'toggle-right',
						'hand-point-right',
					),
				),
			)
		);

		$this->add_responsive_control(
			'arrows_size',
			array(
				'label'      => __( 'Arrows Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => '22' ),
				'range'      => array(
					'px' => array(
						'min'  => 15,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-content-ticker-navigation .swiper-button-next, {{WRAPPER}} .pp-content-ticker-navigation .swiper-button-prev' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_arrows_style' );

		$this->start_controls_tab(
			'tab_arrows_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_control(
			'arrows_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-navigation .swiper-button-next, {{WRAPPER}} .pp-content-ticker-navigation .swiper-button-prev' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrows_color_normal',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-navigation .swiper-button-next, {{WRAPPER}} .pp-content-ticker-navigation .swiper-button-prev' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'arrows_border_normal',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-content-ticker-navigation .swiper-button-next, {{WRAPPER}} .pp-content-ticker-navigation .swiper-button-prev',
			)
		);

		$this->add_control(
			'arrows_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-content-ticker-navigation .swiper-button-next, {{WRAPPER}} .pp-content-ticker-navigation .swiper-button-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrows_hover',
			array(
				'label' => __( 'Hover', 'powerpack' ),
			)
		);

		$this->add_control(
			'arrows_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-navigation .swiper-button-next:hover, {{WRAPPER}} .pp-content-ticker-navigation .swiper-button-prev:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrows_color_hover',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-navigation .swiper-button-next:hover, {{WRAPPER}} .pp-content-ticker-navigation .swiper-button-prev:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrows_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-content-ticker-navigation .swiper-button-next:hover, {{WRAPPER}} .pp-content-ticker-navigation .swiper-button-prev:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'arrows_spacing',
			array(
				'label'      => __( 'Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => '' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-content-ticker-navigation .swiper-button-prev' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->add_responsive_control(
			'arrows_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-content-ticker-navigation .swiper-button-next, {{WRAPPER}} .pp-content-ticker-navigation .swiper-button-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Slider Settings.
	 *
	 * @access public
	 */
	public function slider_settings() {
		$settings = $this->get_settings();

		$slider_options = array(
			'direction'     => 'horizontal',
			'speed'         => ( '' !== $settings['slider_speed']['size'] ) ? $settings['slider_speed']['size'] : 400,
			'effect'        => ( $settings['ticker_effect'] ) ? $settings['ticker_effect'] : 'fade',
			'slidesPerView' => 1,
			'grabCursor'    => ( 'yes' === $settings['grab_cursor'] ),
			'autoHeight'    => false,
			'loop'          => ( 'yes' === $settings['loop'] ),
		);

		$slider_options['fadeEffect'] = array(
			'crossFade' => true,
		);

		if ( 'yes' === $settings['autoplay'] && ! empty( $settings['autoplay_speed'] ) ) {
			$autoplay_speed = $settings['autoplay_speed'];
		} else {
			$autoplay_speed = 999999;
		}

		$slider_options['autoplay'] = array(
			'delay' => $autoplay_speed,
		);

		if ( 'yes' === $settings['arrows'] ) {
			$slider_options['navigation'] = array(
				'nextEl' => '.swiper-button-next-' . esc_attr( $this->get_id() ),
				'prevEl' => '.swiper-button-prev-' . esc_attr( $this->get_id() ),
			);
		}

		$this->add_render_attribute(
			'content-ticker',
			array(
				'data-slider-settings' => wp_json_encode( $slider_options ),
			)
		);
	}

	/**
	 * Render content ticker widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();

		$this->add_render_attribute( 'content-ticker-container', 'class', 'pp-content-ticker-container' );

		if ( 'yes' === $settings['show_heading'] && 'yes' === $settings['heading_arrow'] ) {
			$this->add_render_attribute( 'content-ticker-container', 'class', 'pp-content-ticker-heading-arrow' );
		}

		$this->add_render_attribute( 'content-ticker', 'class', array(
			'pp-content-ticker',
			'pp-swiper-slider',
		) );

		$this->slider_settings();

		$this->add_render_attribute( 'content-ticker-wrap', 'class', array(
			'pp-content-ticker-wrap',
			'swiper-container-wrap',
		) );

		if ( ! isset( $settings['heading_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['heading_icon'] = 'fa fa-bolt';
		}

		$has_icon = ! empty( $settings['heading_icon'] );

		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['heading_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		if ( ! $has_icon && ! empty( $settings['selected_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
		$is_new   = ! isset( $settings['heading_icon'] ) && Icons_Manager::is_migration_allowed();
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'content-ticker-container' ) ); ?>>
			<?php if ( 'yes' === $settings['show_heading'] && $settings['heading'] ) { ?>
				<div class="pp-content-ticker-heading">
					<?php if ( $has_icon ) { ?>
						<?php
							$this->add_render_attribute(
								'heading-icon',
								'class',
								array(
									'pp-content-ticker-heading-icon',
									'pp-icon',
								)
							);

						if ( 'right' === $settings['heading_icon_position'] ) {
							$this->add_render_attribute( 'heading-icon', 'class', 'pp-content-ticker-heading-icon-' . $settings['heading_icon_position'] );
						}
						?>
						<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'heading-icon' ) ); ?>>
							<?php
							if ( $is_new || $migrated ) {
								Icons_Manager::render_icon( $settings['selected_icon'], array( 'aria-hidden' => 'true' ) );
							} elseif ( ! empty( $settings['heading_icon'] ) ) {
								?>
								<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'i' ) ); ?>></i>
								<?php
							}
							?>
						</span>
					<?php } ?>
					<span class="pp-content-ticker-heading-text">
						<?php echo wp_kses_post( $settings['heading'] ); ?>
					</span>
				</div>
			<?php } ?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'content-ticker-wrap' ) ); ?>>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'content-ticker' ) ); ?>>
					<div class="swiper-wrapper">
						<?php
						if ( 'posts' === $settings['source'] ) {
							$this->render_source_posts();
						} elseif ( 'custom' === $settings['source'] ) {
							$this->render_source_custom();
						}
						?>
					</div>
				</div>
			</div>
			<div class="pp-content-ticker-navigation">
				<?php
					$this->render_arrows();
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render content ticker arrows output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_arrows() {
		$settings = $this->get_settings_for_display();

		$migration_allowed = Icons_Manager::is_migration_allowed();

		if ( ! isset( $settings['arrow'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default.
			$settings['arrow'] = 'fa fa-angle-right';
		}

		$has_icon = ! empty( $settings['arrow'] );

		if ( ! $has_icon && ! empty( $settings['select_arrow']['value'] ) ) {
			$has_icon = true;
		}

		$migrated = isset( $settings['__fa4_migrated']['select_arrow'] );
		$is_new = ! isset( $settings['arrow'] ) && $migration_allowed;

		if ( 'yes' === $settings['arrows'] ) {
			?>
			<?php
			if ( $has_icon ) {
				if ( $is_new || $migrated ) {
					$next_arrow = str_replace( 'left', 'right', $settings['select_arrow']['value'] );
					$prev_arrow = str_replace( 'right', 'left', $settings['select_arrow']['value'] );
				} else {
					$next_arrow = $settings['arrow'];
					$prev_arrow = str_replace( 'right', 'left', $settings['arrow'] );
				}
			} else {
				$next_arrow = 'fa fa-angle-right';
				$prev_arrow = 'fa fa-angle-left';
			}
			?>

			<?php if ( ! empty( $settings['arrow'] ) || ( ! empty( $settings['select_arrow']['value'] ) && $is_new ) ) { ?>
				<!-- Add Arrows -->
				<div class="swiper-button-prev swiper-button-prev-<?php echo esc_attr( $this->get_id() ); ?>">
					<i aria-hidden="true" class="<?php echo esc_attr( $prev_arrow ); ?>"></i>
				</div>
				<div class="swiper-button-next swiper-button-next-<?php echo esc_attr( $this->get_id() ); ?>">
					<i aria-hidden="true" class="<?php echo esc_attr( $next_arrow ); ?>"></i>
				</div>
			<?php } ?>
			<?php
		}
	}

	/**
	 * Render custom content output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_source_custom() {
		$settings = $this->get_settings_for_display();

		$i = 1;

		foreach ( $settings['items'] as $index => $item ) {
			$item_key  = $this->get_repeater_setting_key( 'item', 'items', $index );
			$link_key  = $this->get_repeater_setting_key( 'link', 'items', $index );

			$this->add_render_attribute(
				$item_key,
				'class',
				array(
					'pp-content-ticker-item',
					'swiper-slide',
					'elementor-repeater-item-' . esc_attr( $item['_id'] ),
				)
			);

			if ( '' !== $settings['link_type'] ) {
				$this->add_link_attributes( $link_key, $item['link'] );
			}
			?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( $item_key ) ); ?>>
				<div class="pp-content-ticker-content">
					<?php if ( 'yes' === $item['ticker_image'] && '' !== $item['image']['url'] ) { ?>
						<div class="pp-content-ticker-image">
							<?php
							if ( ( 'image' === $settings['link_type'] || 'both' === $settings['link_type'] ) && '' !== $item['link']['url'] ) {
								?>
								<a <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>>
									<?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $item ) ); ?>
								</a>
								<?php
							} else {
								echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $item ) );
							}
							?>
						</div>
					<?php } ?>
					<?php
					if ( '' !== $item['ticker_title'] ) {
						$title_tag = PP_Helper::validate_html_tag( $settings['title_html_tag'] );
						?>
						<<?php echo esc_html( $title_tag ); ?> class="pp-content-ticker-item-title">
						<?php
						if ( ( 'title' === $settings['link_type'] || 'both' === $settings['link_type'] ) && $item['link']['url'] ) {
							printf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( $link_key ), $item['ticker_title'] );
						} else {
							echo wp_kses_post( $item['ticker_title'] );
						}
						?>
						</<?php echo esc_html( $title_tag ); ?>>
						<?php
					}
					?>
				</div>
			</div>
			<?php
			$i++;
		}
	}

	/**
	 * Render posts output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_source_posts() {
		$settings = $this->get_settings();

		$i = 1;

		// Author Icon
		if ( ! isset( $settings['author_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['author_icon'] = 'fa fa-user';
		}

		$has_author_icon = ! empty( $settings['author_icon'] );

		if ( $has_author_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['author_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		if ( ! $has_author_icon && ! empty( $settings['select_author_icon']['value'] ) ) {
			$has_author_icon = true;
		}
		$migrated_author_icon = isset( $settings['__fa4_migrated']['select_author_icon'] );
		$is_new_author_icon   = ! isset( $settings['author_icon'] ) && Icons_Manager::is_migration_allowed();

		// Category Icon
		if ( ! isset( $settings['category_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['category_icon'] = 'fa fa-folder-open';
		}

		$has_category_icon = ! empty( $settings['category_icon'] );

		if ( $has_category_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['category_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		if ( ! $has_category_icon && ! empty( $settings['select_category_icon']['value'] ) ) {
			$has_category_icon = true;
		}
		$migrated_category_icon = isset( $settings['__fa4_migrated']['select_category_icon'] );
		$is_new_category_icon   = ! isset( $settings['category_icon'] ) && Icons_Manager::is_migration_allowed();

		// Query Arguments
		$args        = $this->get_posts_query_arguments();
		$posts_query = new \WP_Query( $args );

		if ( $posts_query->have_posts() ) :
			while ( $posts_query->have_posts() ) :
				$posts_query->the_post();

				$item_key = 'content-ticker-item' . $i;

				if ( has_post_thumbnail() ) {
					$image_id     = get_post_thumbnail_id( get_the_ID() );
					$thumb_url = Group_Control_Image_Size::get_attachment_image_src( $image_id, 'image_size', $settings );
					$image_alt    = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				} else {
					$thumb_url = '';
					$image_alt    = '';
				}

				$this->add_render_attribute(
					$item_key,
					'class',
					array(
						'pp-content-ticker-item',
						'swiper-slide',
						'pp-content-ticker-item-' . intval( $i ),
					)
				);
				?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( $item_key ) ); ?>>
					<div class="pp-content-ticker-content">
						<?php if ( 'show' === $settings['post_image'] && '' !== $thumb_url ) { ?>
							<div class="pp-content-ticker-image">
								<?php
								if ( 'image' === $settings['link_type'] || 'both' === $settings['link_type'] ) {
									?>
									<a href="<?php echo get_permalink(); ?>">
										<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php wp_kses_post( $image_alt ); ?>">
									</a>
									<?php
								} else {
									?>
									<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php wp_kses_post( $image_alt ); ?>">
									<?php
								}
								?>
							</div>
						<?php } ?>
						<div class="pp-content-ticker-item-title-wrap">
							<?php
							$title_tag = PP_Helper::validate_html_tag( $settings['title_html_tag'] );
							?>
							<<?php echo esc_html( $title_tag ); ?> class="pp-content-ticker-item-title">
							<?php
							if ( 'title' === $settings['link_type'] || 'both' === $settings['link_type'] ) {
								printf( '<a href="%1$s">%2$s</a>', get_permalink(), get_the_title() );
							} else {
								the_title();
							}
							?>
							</<?php echo esc_html( $title_tag ); ?>>
							<?php
							if ( 'yes' === $settings['post_meta'] ) { ?>
								<div class="pp-content-ticker-meta">
									<?php if ( 'yes' === $settings['post_date'] || 'yes' === $settings['post_time'] ) { ?>
										<span class="pp-content-ticker-item-datetime">
											<?php if ( ! empty( $settings['datetime_icon']['value'] ) ) { ?>
												<span class="pp-content-ticker-meta-icon pp-icon">
													<?php Icons_Manager::render_icon( $settings['datetime_icon'], array( 'aria-hidden' => 'true' ) ); ?>
												</span>
											<?php } ?>
											<?php
											if ( 'yes' === $settings['post_date'] ) {
												the_date();
											}
											if ( 'yes' === $settings['post_date'] && 'yes' === $settings['post_time'] ) {
												echo ' ' . $settings['datetime_separator'] . ' ';
											}
											if ( 'yes' === $settings['post_time'] ) {
												the_time();
											}
											?>
										</span>
									<?php } ?>
									<?php if ( 'yes' === $settings['post_author'] ) { ?>
										<span class="pp-content-author">
											<?php if ( $has_author_icon ) { ?>
												<span class="pp-content-ticker-meta-icon pp-icon">
													<?php
													if ( $is_new_author_icon || $migrated_author_icon ) {
														Icons_Manager::render_icon( $settings['select_author_icon'], array( 'aria-hidden' => 'true' ) );
													} elseif ( ! empty( $settings['author_icon'] ) ) {
														?>
														<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'i' ) ); ?>></i>
														<?php
													}
													?>
												</span>
											<?php } ?>
											<span class="pp-content-ticker-meta-text">
												<?php echo get_the_author(); ?>
											</span>
										</span>
									<?php } ?>  
									<?php if ( 'yes' === $settings['post_category'] ) { ?>
										<span class="pp-post-category">
												<?php if ( $has_category_icon ) { ?>
												<span class="pp-content-ticker-meta-icon pp-icon">
													<?php
													if ( $is_new_author_icon || $migrated_author_icon ) {
														Icons_Manager::render_icon( $settings['select_category_icon'], array( 'aria-hidden' => 'true' ) );
													} elseif ( ! empty( $settings['category_icon'] ) ) {
														?>
														<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'i' ) ); ?>></i>
														<?php
													}
													?>
												</span>
											<?php } ?>
											<span class="pp-content-ticker-meta-text">
												<?php
												$category = get_the_category();
												if ( $category ) {
													echo esc_attr( $category[0]->name );
												}
												?>
											</span>
										</span>
									<?php } ?>  
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php
				$i++;
			endwhile;
		endif;
		wp_reset_postdata();
	}

	/**
	 * Get post query arguments.
	 *
	 * @access protected
	 */
	protected function get_posts_query_arguments() {
		$settings    = $this->get_settings();
		$posts_count = absint( $settings['posts_count'] );

		// Query Arguments
		$args = array(
			'post_status'         => array( 'publish' ),
			'post_type'           => $settings['post_type'],
			'orderby'             => $settings['orderby'],
			'order'               => $settings['order'],
			'offset'              => $settings['offset'],
			'ignore_sticky_posts' => ( 'yes' === $settings['sticky_posts'] ) ? 0 : 1,
			'showposts'           => $posts_count,
		);

		// Author Filter
		if ( ! empty( $settings['authors'] ) ) {
			$args[ $settings['author_filter_type'] ] = $settings['authors'];
		}

		// Posts Filter
		$post_type = $settings['post_type'];

		if ( 'post' === $post_type ) {
			$posts_control_key = 'exclude_posts';
		} else {
			$posts_control_key = $post_type . '_filter';
		}

		if ( ! empty( $settings[ $posts_control_key ] ) ) {
			$args[ $settings[ $post_type . '_filter_type' ] ] = $settings[ $posts_control_key ];
		}

		// Taxonomy Filter
		$taxonomy = PP_Posts_Helper::get_post_taxonomies( $post_type );

		if ( ! empty( $taxonomy ) && ! is_wp_error( $taxonomy ) ) {

			foreach ( $taxonomy as $index => $tax ) {

				if ( 'post_tag' === $index ) {
					$tax_control_key = 'tags';
				} elseif ( 'category' === $index ) {
					$tax_control_key = 'categories';
				} else {
					$tax_control_key = $index . '_' . $post_type;
				}

				if ( ! empty( $settings[ $tax_control_key ] ) ) {

					$operator = $settings[ $index . '_' . $post_type . '_filter_type' ];

					$args['tax_query'][] = array(
						'taxonomy' => $index,
						'field'    => 'term_id',
						'terms'    => $settings[ $tax_control_key ],
						'operator' => $operator,
					);
				}
			}
		}

		if ( 'anytime' !== $settings['select_date'] ) {
			$select_date = $settings['select_date'];
			if ( ! empty( $select_date ) ) {
				$date_query = [];
				switch ( $select_date ) {
					case 'today':
						$date_query['after'] = '-1 day';
						break;

					case 'week':
						$date_query['after'] = '-1 week';
						break;

					case 'month':
						$date_query['after'] = '-1 month';
						break;

					case 'quarter':
						$date_query['after'] = '-3 month';
						break;

					case 'year':
						$date_query['after'] = '-1 year';
						break;

					case 'exact':
						$after_date = $settings['date_after'];
						if ( ! empty( $after_date ) ) {
							$date_query['after'] = $after_date;
						}
						$before_date = $settings['date_before'];
						if ( ! empty( $before_date ) ) {
							$date_query['before'] = $before_date;
						}
						$date_query['inclusive'] = true;
						break;
				}

				$args['date_query'] = $date_query;
			}
		}

		// Sticky Posts Filter
		if ( 'yes' === $settings['sticky_posts'] && 'yes' === $settings['all_sticky_posts'] ) {
			$post__in = get_option( 'sticky_posts' );

			$args['post__in'] = $post__in;
		}

		return $args;
	}
}
