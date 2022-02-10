<?php
namespace PowerpackElementsLite\Modules\Posts\Skins;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Helper;
use PowerpackElementsLite\Classes\PP_Config;
use PowerpackElementsLite\Modules\Posts\Module;
use PowerpackElementsLite\Classes\PP_Posts_Helper;
use PowerpackElementsLite\Group_Control_Transition;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Skin Base
 */
abstract class Skin_Base extends Elementor_Skin_Base {

	protected function _register_controls_actions() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore 
		add_action( 'elementor/element/pp-posts/section_skin_field/before_section_end', array( $this, 'register_layout_controls' ) );
		add_action( 'elementor/element/pp-posts/section_query/after_section_end', array( $this, 'register_controls' ) );
		add_action( 'elementor/element/pp-posts/section_query/after_section_end', array( $this, 'register_style_sections' ) );
	}

	public function register_style_sections( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_style_controls();
	}

	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_slider_controls();
		$this->register_filter_section_controls();
		$this->register_search_controls();
		$this->register_terms_controls();
		$this->register_image_controls();
		$this->register_title_controls();
		$this->register_excerpt_controls();
		$this->register_meta_controls();
		$this->register_button_controls();
		$this->register_pagination_controls();
		$this->register_content_order();
		$this->register_content_help_docs();
		$this->register_content_upgrade_pro_controls();
	}

	public function register_style_controls() {
		$this->register_style_layout_controls();
		$this->register_style_box_controls();
		$this->register_style_content_controls();
		$this->register_style_image_controls();
		$this->register_style_terms_controls();
		$this->register_style_title_controls();
		$this->register_style_excerpt_controls();
		$this->register_style_meta_controls();
		$this->register_style_button_controls();
		$this->register_style_pagination_controls();
		$this->register_style_arrows_controls();
		$this->register_style_dots_controls();
	}

	public function register_layout_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_layout_content_controls();
	}

	public function register_layout_content_controls() {

		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Layout', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'grid'     => __( 'Grid', 'powerpack' ),
					'masonry'  => __( 'Masonry', 'powerpack' ),
					'carousel' => __( 'Carousel', 'powerpack' ),
				),
				'default' => 'grid',
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'              => __( 'Columns', 'powerpack' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '3',
				'tablet_default'     => '2',
				'mobile_default'     => '1',
				'options'            => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
				),
				'prefix_class'       => 'elementor-grid%s-',
				'render_type'        => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'equal_height',
			array(
				'label'        => __( 'Equal Height', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'prefix_class' => 'pp-equal-height-',
				'render_type'  => 'template',
				'condition'    => array(
					$this->get_control_id( 'layout!' ) => 'masonry',
				),
			)
		);
	}

	public function register_slider_controls() {

		$this->start_controls_section(
			'section_slider_options',
			array(
				'label'     => __( 'Carousel Options', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
				),
			)
		);

		$slides_per_view = range( 1, 10 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->add_responsive_control(
			'slides_to_scroll',
			array(
				'type'               => Controls_Manager::SELECT,
				'label'              => __( 'Slides to Scroll', 'powerpack' ),
				'description'        => __( 'Set how many slides are scrolled per swipe.', 'powerpack' ),
				'options'            => $slides_per_view,
				'default'            => '1',
				'tablet_default'     => '1',
				'mobile_default'     => '1',
				'condition'          => array(
					$this->get_control_id( 'layout' ) => 'carousel',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'animation_speed',
			array(
				'label'              => __( 'Animation Speed', 'powerpack' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 600,
				'frontend_available' => true,
				'condition'          => array(
					$this->get_control_id( 'layout' ) => 'carousel',
				),
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'              => __( 'Arrows', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					$this->get_control_id( 'layout' ) => 'carousel',
				),
			)
		);

		$this->add_control(
			'dots',
			array(
				'label'              => __( 'Dots', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'no',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					$this->get_control_id( 'layout' ) => 'carousel',
				),
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
				'condition'          => array(
					$this->get_control_id( 'layout' ) => 'carousel',
				),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'              => __( 'Autoplay Speed', 'powerpack' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 3000,
				'frontend_available' => true,
				'condition'          => array(
					$this->get_control_id( 'layout' )   => 'carousel',
					$this->get_control_id( 'autoplay' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'              => __( 'Pause on Hover', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					$this->get_control_id( 'layout' )   => 'carousel',
					$this->get_control_id( 'autoplay' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'infinite_loop',
			array(
				'label'              => __( 'Infinite Loop', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					$this->get_control_id( 'layout' ) => 'carousel',
				),
			)
		);

		$this->add_control(
			'adaptive_height',
			array(
				'label'              => __( 'Adaptive Height', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					$this->get_control_id( 'layout' ) => 'carousel',
				),
			)
		);

		$this->add_control(
			'center_mode',
			[
				'label'                 => __( 'Center Mode', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => '',
				'label_on'              => __( 'Yes', 'powerpack' ),
				'label_off'             => __( 'No', 'powerpack' ),
				'return_value'          => 'yes',
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'direction',
			array(
				'label'              => __( 'Direction', 'powerpack' ),
				'type'               => Controls_Manager::CHOOSE,
				'label_block'        => false,
				'toggle'             => false,
				'options'            => array(
					'left'  => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'            => 'left',
				'frontend_available' => true,
				'condition'          => array(
					$this->get_control_id( 'layout' ) => 'carousel',
				),
			)
		);

		$this->end_controls_section();
	}

	public function register_filter_section_controls() {

		$this->start_controls_section(
			'section_filters',
			array(
				'label'     => __( 'Filters', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'post_type!'                       => 'related',
					$this->get_control_id( 'layout!' ) => 'carousel',
				),
			)
		);

		$this->add_control(
			'show_filters',
			array(
				'label'        => __( 'Show Filters', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'post_type!'                       => 'related',
					$this->get_control_id( 'layout!' ) => 'carousel',
				),
			)
		);

		$this->add_control(
			'show_filters_notice',
			array(
				'label'           => '',
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'This feature is available in PowerPack Pro.', 'powerpack' ) . ' ' . apply_filters( 'upgrade_powerpack_message', sprintf( __( 'Upgrade to %1$s Pro Version %2$s for 70+ widgets, exciting extensions and advanced features.', 'powerpack' ), '<a href="#" target="_blank" rel="noopener">', '</a>' ) ),
				'content_classes' => 'upgrade-powerpack-notice elementor-panel-alert elementor-panel-alert-info',
				'condition'    => array(
					'post_type!'                            => 'related',
					$this->get_control_id( 'layout!' )      => 'carousel',
					$this->get_control_id( 'show_filters' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Search Form
	 *
	 * @since 1.4.11.0
	 * @access protected
	 */
	protected function register_search_controls() {

		$this->start_controls_section(
			'section_search_form',
			array(
				'label'     => __( 'Search Form', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'layout' ) => array( 'grid', 'masonry' ),
				),
			)
		);

		$this->add_control(
			'show_ajax_search_form',
			array(
				'label'              => __( 'Show Search Form', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					$this->get_control_id( 'layout' ) => array( 'grid', 'masonry' ),
				),
			)
		);

		$this->add_control(
			'show_ajax_search_form_notice',
			array(
				'label'           => '',
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'This feature is available in PowerPack Pro.', 'powerpack' ) . ' ' . apply_filters( 'upgrade_powerpack_message', sprintf( __( 'Upgrade to %1$s Pro Version %2$s for 70+ widgets, exciting extensions and advanced features.', 'powerpack' ), '<a href="#" target="_blank" rel="noopener">', '</a>' ) ),
				'content_classes' => 'upgrade-powerpack-notice elementor-panel-alert elementor-panel-alert-info',
				'condition'    => array(
					$this->get_control_id( 'layout' )                => array( 'grid', 'masonry' ),
					$this->get_control_id( 'show_ajax_search_form' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_terms_controls() {
		/**
		 * Content Tab: Post Terms
		 */
		$this->start_controls_section(
			'section_terms',
			array(
				'label'     => __( 'Post Terms', 'powerpack' ),
			)
		);

		$this->add_control(
			'post_terms',
			array(
				'label'        => __( 'Show Post Terms', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$post_types = PP_Posts_Helper::get_post_types();

		foreach ( $post_types as $post_type_slug => $post_type_label ) {

			$taxonomy = PP_Posts_Helper::get_post_taxonomies( $post_type_slug );

			if ( ! empty( $taxonomy ) ) {

				$related_tax = array();

				// Get all taxonomy values under the taxonomy.
				foreach ( $taxonomy as $index => $tax ) {

					$terms = get_terms( $index );

					$related_tax[ $index ] = $tax->label;
				}

				// Add control for all taxonomies.
				$this->add_control(
					'tax_badge_' . $post_type_slug,
					array(
						'label'     => __( 'Select Taxonomy', 'powerpack' ),
						'type'      => Controls_Manager::SELECT2,
						'options'   => $related_tax,
						'multiple'  => true,
						'default'   => array_keys( $related_tax )[0],
						'condition' => array(
							'post_type' => $post_type_slug,
							$this->get_control_id( 'post_terms' ) => 'yes',
						),
					)
				);
			}
		}

		$this->add_control(
			'max_terms',
			array(
				'label'       => __( 'Max Terms to Show', 'powerpack' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 1,
				'condition'   => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
				'label_block' => false,
			)
		);

		$this->add_control(
			'post_taxonomy_link',
			array(
				'label'        => __( 'Link to Taxonomy', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'post_terms_separator',
			array(
				'label'     => __( 'Terms Separator', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => ',',
				'selectors' => array(
					'{{WRAPPER}} .pp-post-terms > .pp-post-term:not(:last-child):after' => 'content: "{{UNIT}}";',
				),
				'condition' => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Image
	 */
	protected function register_image_controls() {

		$this->start_controls_section(
			'section_image',
			array(
				'label'     => __( 'Image', 'powerpack' ),
			)
		);

		$this->add_control(
			'show_thumbnail',
			array(
				'label'        => __( 'Show Image', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'thumbnail_link',
			array(
				'label'        => __( 'Link to Post', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'thumbnail_link_target',
			array(
				'label' => __( 'Open in a New Tab', 'powerpack' ),
				'type'  => Controls_Manager::SWITCHER,
				'condition' => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
					$this->get_control_id( 'thumbnail_link' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'thumbnail_custom_height',
			array(
				'label'        => __( 'Custom Height', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'ratio',
				'prefix_class' => 'pp-posts-thumbnail-',
				'condition'    => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'thumbnail_ratio',
			array(
				'label'          => __( 'Image Ratio', 'powerpack' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 1,
				),
				'tablet_default' => array(
					'size' => '',
				),
				'mobile_default' => array(
					'size' => 1,
				),
				'range'          => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.01,
					),
				),
				'selectors'      => array(
					'{{WRAPPER}} .pp-posts-container .pp-post-thumbnail-wrap' => 'padding-bottom: calc( {{SIZE}} * 100% );',
				),
				'condition'      => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
					$this->get_control_id( 'thumbnail_custom_height!' ) => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'thumbnail',
				'label'     => __( 'Image Size', 'powerpack' ),
				'default'   => 'large',
				'exclude'   => array( 'custom' ),
				'condition' => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'thumbnail_location',
			array(
				'label'     => __( 'Image Location', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'inside'  => __( 'Inside Content Container', 'powerpack' ),
					'outside' => __( 'Outside Content Container', 'powerpack' ),
				),
				'default'   => 'outside',
				'condition' => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'fallback_image',
			array(
				'label'       => __( 'Fallback Image', 'powerpack' ),
				'description' => __( 'If a featured image is not available in post, it will display the first image from the post or default image placeholder or a custom image. You can choose None to do not display the fallback image.', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'none'    => __( 'None', 'powerpack' ),
					'default' => __( 'Default', 'powerpack' ),
					'custom'  => __( 'Custom', 'powerpack' ),
				),
				'default'     => 'default',
				'condition'   => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'fallback_image_custom',
			array(
				'label'     => __( 'Fallback Image Custom', 'powerpack' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
					$this->get_control_id( 'fallback_image' ) => 'custom',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Title
	 */
	protected function register_title_controls() {
		$this->start_controls_section(
			'section_post_title',
			array(
				'label'     => __( 'Title', 'powerpack' ),
			)
		);

		$this->add_control(
			'post_title',
			array(
				'label'        => __( 'Post Title', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'post_title_link',
			array(
				'label'        => __( 'Link to Post', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					$this->get_control_id( 'post_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'post_title_link_target',
			array(
				'label' => __( 'Open in a New Tab', 'powerpack' ),
				'type'  => Controls_Manager::SWITCHER,
				'condition' => array(
					$this->get_control_id( 'post_title' ) => 'yes',
					$this->get_control_id( 'post_title_link' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'title_html_tag',
			array(
				'label'     => __( 'HTML Tag', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => array(
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
				'condition' => array(
					$this->get_control_id( 'post_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'post_title_separator',
			array(
				'label'        => __( 'Title Separator', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					$this->get_control_id( 'post_title' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Excerpt
	 */
	protected function register_excerpt_controls() {
		$this->start_controls_section(
			'section_post_excerpt',
			array(
				'label'     => __( 'Content', 'powerpack' ),
			)
		);

		$this->add_control(
			'show_excerpt',
			array(
				'label'        => __( 'Show Content', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'content_type',
			array(
				'label'     => __( 'Content Type', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'excerpt',
				'options'   => array(
					'excerpt' => __( 'Excerpt', 'powerpack' ),
					'content' => __( 'Limited Content', 'powerpack' ),
					'full'    => __( 'Full Content', 'powerpack' ),
				),
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'     => __( 'Excerpt Length', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 20,
				'min'       => 0,
				'step'      => 1,
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
					$this->get_control_id( 'content_type' ) => 'excerpt',
				),
			)
		);

		$this->add_control(
			'content_length',
			array(
				'label'       => __( 'Content Length', 'powerpack' ),
				'title'       => __( 'Words', 'powerpack' ),
				'description' => __( 'Number of words to be displayed from the post content', 'powerpack' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 30,
				'min'         => 0,
				'step'        => 1,
				'condition'   => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
					$this->get_control_id( 'content_type' ) => 'content',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Meta
	 */
	protected function register_meta_controls() {
		$this->start_controls_section(
			'section_post_meta',
			array(
				'label'     => __( 'Meta', 'powerpack' ),
			)
		);

		$this->add_control(
			'post_meta',
			array(
				'label'        => __( 'Post Meta', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'post_meta_separator',
			array(
				'label'     => __( 'Post Meta Separator', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '-',
				'selectors' => array(
					'{{WRAPPER}} .pp-post-meta .pp-meta-separator:not(:last-child):after' => 'content: "{{UNIT}}";',
				),
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_post_author',
			array(
				'label'     => __( 'Post Author', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'show_author',
			array(
				'label'        => __( 'Show Post Author', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'author_link',
			array(
				'label'        => __( 'Link to Author', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					$this->get_control_id( 'post_meta' )   => 'yes',
					$this->get_control_id( 'show_author' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'author_icon',
			array(
				'label'     => __( 'Author Icon', 'powerpack' ),
				'type'      => Controls_Manager::ICON,
				'default'   => '',
				'condition' => array(
					$this->get_control_id( 'post_meta' )   => 'yes',
					$this->get_control_id( 'show_author' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'author_prefix',
			array(
				'label'     => __( 'Prefix', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					$this->get_control_id( 'post_meta' )   => 'yes',
					$this->get_control_id( 'show_author' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_post_date',
			array(
				'label'     => __( 'Post Date', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'show_date',
			array(
				'label'        => __( 'Show Post Date', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'date_link',
			array(
				'label'        => __( 'Link to Post', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
					$this->get_control_id( 'show_date' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'     => __( 'Date Format', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''         => __( 'Published Date', 'powerpack' ),
					'ago'      => __( 'Time Ago', 'powerpack' ),
					'modified' => __( 'Last Modified Date', 'powerpack' ),
					'custom'   => __( 'Custom Format', 'powerpack' ),
					'key'      => __( 'Custom Meta Key', 'powerpack' ),
				),
				'default'   => '',
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
					$this->get_control_id( 'show_date' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'date_custom_format',
			array(
				'label'       => __( 'Custom Format', 'powerpack' ),
				'description' => sprintf( __( 'Refer to PHP date formats <a href="%s">here</a>', 'powerpack' ), 'https://wordpress.org/support/article/formatting-date-and-time/' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => '',
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					$this->get_control_id( 'post_meta' )   => 'yes',
					$this->get_control_id( 'show_date' )   => 'yes',
					$this->get_control_id( 'date_format' ) => 'custom',
				),
			)
		);

		$this->add_control(
			'date_meta_key',
			array(
				'label'       => __( 'Custom Meta Key', 'powerpack' ),
				'description' => __( 'Display the post date stored in custom meta key.', 'powerpack' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => '',
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					$this->get_control_id( 'post_meta' )   => 'yes',
					$this->get_control_id( 'show_date' )   => 'yes',
					$this->get_control_id( 'date_format' ) => 'key',
				),
			)
		);

		$this->add_control(
			'date_icon',
			array(
				'label'     => __( 'Date Icon', 'powerpack' ),
				'type'      => Controls_Manager::ICON,
				'default'   => '',
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
					$this->get_control_id( 'show_date' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'date_prefix',
			array(
				'label'     => __( 'Prefix', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
					$this->get_control_id( 'show_date' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_post_comments',
			array(
				'label'     => __( 'Post Comments', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'show_comments',
			array(
				'label'        => __( 'Show Post Comments', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'comments_icon',
			array(
				'label'     => __( 'Comments Icon', 'powerpack' ),
				'type'      => Controls_Manager::ICON,
				'default'   => '',
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
					$this->get_control_id( 'show_comments' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_button_controls() {

		$this->start_controls_section(
			'section_button',
			array(
				'label' => __( 'Read More Button', 'powerpack' ),
			)
		);

		$this->add_control(
			'show_button',
			array(
				'label'        => __( 'Show Button', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'     => __( 'Button Text', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => __( 'Read More', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'select_button_icon',
			array(
				'label'            => __( 'Button Icon', 'powerpack' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => $this->get_control_id( 'button_icon' ),
				'condition'        => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'button_icon_position',
			array(
				'label'     => __( 'Icon Position', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => array(
					'before' => __( 'Before', 'powerpack' ),
					'after'  => __( 'After', 'powerpack' ),
				),
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
					$this->get_control_id( 'select_button_icon[value]!' ) => '',
				),
			)
		);

		$this->add_control(
			'button_link_target',
			array(
				'label'     => __( 'Open in a New Tab', 'powerpack' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	public function register_pagination_controls() {
		$this->start_controls_section(
			'section_pagination',
			array(
				'label'     => __( 'Pagination', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
				),
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'     => __( 'Pagination', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => array(
					'none'                  => __( 'None', 'powerpack' ),
					'numbers'               => __( 'Numbers', 'powerpack' ),
					'numbers_and_prev_next' => __( 'Numbers', 'powerpack' ) . ' + ' . __( 'Previous/Next', 'powerpack' ),
					'load_more'             => __( 'Load More Button', 'powerpack' ),
					'infinite'              => __( 'Infinite', 'powerpack' ),
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
				),
			)
		);

		$this->add_control(
			'pagination_notice',
			array(
				'label'           => '',
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'This pagination option is available in PowerPack Pro.', 'powerpack' ) . ' ' . apply_filters( 'upgrade_powerpack_message', sprintf( __( 'Upgrade to %1$s Pro Version %2$s for 70+ widgets, exciting extensions and advanced features.', 'powerpack' ), '<a href="#" target="_blank" rel="noopener">', '</a>' ) ),
				'content_classes' => 'upgrade-powerpack-notice elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array(
						'load_more',
						'infinite',
					),
				),
			)
		);

		$this->add_control(
			'pagination_position',
			array(
				'label'     => __( 'Pagination Position', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => array(
					'top'        => __( 'Top', 'powerpack' ),
					'bottom'     => __( 'Bottom', 'powerpack' ),
					'top-bottom' => __( 'Top', 'powerpack' ) . ' + ' . __( 'Bottom', 'powerpack' ),
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array(
						'numbers',
						'numbers_and_prev_next',
					),
				),
			)
		);

		$this->add_control(
			'pagination_ajax',
			array(
				'label'     => __( 'Ajax Pagination', 'powerpack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array(
						'numbers',
						'numbers_and_prev_next',
					),
				),
			)
		);

		$this->add_control(
			'pagination_page_limit',
			array(
				'label'     => __( 'Page Limit', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5,
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array(
						'numbers',
						'numbers_and_prev_next',
					),
				),
			)
		);

		$this->add_control(
			'pagination_numbers_shorten',
			array(
				'label'     => __( 'Shorten', 'powerpack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => '',
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array(
						'numbers',
						'numbers_and_prev_next',
					),
				),
			)
		);

		$this->add_control(
			'pagination_prev_label',
			array(
				'label'     => __( 'Previous Label', 'powerpack' ),
				'default'   => __( '&laquo; Previous', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => 'numbers_and_prev_next',
				),
			)
		);

		$this->add_control(
			'pagination_next_label',
			array(
				'label'     => __( 'Next Label', 'powerpack' ),
				'default'   => __( 'Next &raquo;', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => 'numbers_and_prev_next',
				),
			)
		);

		$this->add_control(
			'pagination_align',
			array(
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-pagination-wrap' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array(
						'numbers',
						'numbers_and_prev_next',
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Order
	 *
	 * @since 1.4.11.0
	 * @access protected
	 */
	protected function register_content_order() {

		$this->start_controls_section(
			'section_order',
			array(
				'label'     => __( 'Order', 'powerpack' ),
			)
		);

		$this->add_control(
			'content_parts_order_heading',
			array(
				'label' => __( 'Content Parts', 'powerpack' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'thumbnail_order',
			array(
				'label'     => __( 'Thumbnail', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'condition' => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
					$this->get_control_id( 'thumbnail_location' ) => 'inside',
				),
			)
		);

		$this->add_control(
			'terms_order',
			array(
				'label'     => __( 'Terms', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'condition' => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'title_order',
			array(
				'label'     => __( 'Title', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'condition' => array(
					$this->get_control_id( 'post_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'meta_order',
			array(
				'label'     => __( 'Meta', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'excerpt_order',
			array(
				'label'     => __( 'Excerpt', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'button_order',
			array(
				'label'     => __( 'Read More Button', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'meta_order_heading',
			array(
				'label'     => __( 'Post Meta', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'author_order',
			array(
				'label'     => __( 'Author', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'condition' => array(
					$this->get_control_id( 'post_meta' )   => 'yes',
					$this->get_control_id( 'show_author' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'date_order',
			array(
				'label'     => __( 'Date', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
					$this->get_control_id( 'show_date' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'comments_order',
			array(
				'label'     => __( 'Comments', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
					$this->get_control_id( 'show_comments' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Help Docs
	 *
	 * @since 2.1.0
	 * @access protected
	 */
	protected function register_content_help_docs() {

		$help_docs = PP_Config::get_widget_help_links( 'Posts' );

		if ( ! empty( $help_docs ) ) {

			/**
			 * Content Tab: Help Docs
			 *
			 * @since 2.1.0
			 * @access protected
			 */
			$this->start_controls_section(
				'section_help_docs',
				array(
					'label' => __( 'Help Docs', 'powerpack' ),
				)
			);

			$hd_counter = 1;
			foreach ( $help_docs as $hd_title => $hd_link ) {
				$this->add_control(
					'help_doc_' . $hd_counter,
					array(
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => sprintf( '%1$s ' . $hd_title . ' %2$s', '<a href="' . $hd_link . '" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'pp-editor-doc-links',
					)
				);

				$hd_counter++;
			}

			$this->end_controls_section();
		}
	}

	/**
	 * Content Tab: Upgrade pro section
	 *
	 * @since 2.1.0
	 * @access protected
	 */
	protected function register_content_upgrade_pro_controls() {
		if ( ! is_pp_elements_active() ) {
			$this->start_controls_section(
				'section_upgrade_powerpack',
				array(
					'label' => apply_filters( 'upgrade_powerpack_title', __( 'Get PowerPack Pro', 'powerpack' ) ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'upgrade_powerpack_notice',
				array(
					'label'           => '',
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => apply_filters( 'upgrade_powerpack_message', sprintf( __( 'Upgrade to %1$s Pro Version %2$s for 70+ widgets, exciting extensions and advanced features.', 'powerpack' ), '<a href="#" target="_blank" rel="noopener">', '</a>' ) ),
					'content_classes' => 'upgrade-powerpack-notice elementor-panel-alert elementor-panel-alert-info',
				)
			);

			$this->end_controls_section();
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Style Tab: Layout
	 */
	protected function register_style_layout_controls() {

		$this->start_controls_section(
			'section_layout_style',
			array(
				'label' => __( 'Layout', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'posts_horizontal_spacing',
			array(
				'label'     => __( 'Column Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'   => array(
					'size' => 25,
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-post-wrap' => 'padding-left: calc( {{SIZE}}{{UNIT}}/2 ); padding-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .pp-posts'     => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				),
			)
		);

		$this->add_responsive_control(
			'posts_vertical_spacing',
			array(
				'label'     => __( 'Row Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'   => array(
					'size' => 25,
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-elementor-grid .pp-grid-item-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Box
	 */
	protected function register_style_box_controls() {
		$this->start_controls_section(
			'section_post_box_style',
			array(
				'label' => __( 'Box', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_post_box_style' );

		$this->start_controls_tab(
			'tab_post_box_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_control(
			'post_box_bg',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-post' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'post_box_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-post',
			)
		);

		$this->add_responsive_control(
			'post_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'post_box_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'post_box_shadow',
				'selector' => '{{WRAPPER}} .pp-post',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_post_box_hover',
			array(
				'label' => __( 'Hover', 'powerpack' ),
			)
		);

		$this->add_control(
			'post_box_bg_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-post:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'post_box_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-post:hover' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'post_box_shadow_hover',
				'selector' => '{{WRAPPER}} .pp-post:hover',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Content Container
	 */
	protected function register_style_content_controls() {
		$this->start_controls_section(
			'section_post_content_style',
			array(
				'label' => __( 'Content Container', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'post_content_align',
			array(
				'label'       => __( 'Alignment', 'powerpack' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'left'   => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'     => '',
				'selectors'   => array(
					'{{WRAPPER}} .pp-post-content' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'post_content_bg',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-post-content' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'post_content_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'post_content_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Image
	 */
	protected function register_style_image_controls() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label'     => __( 'Image', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'img_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-thumbnail, {{WRAPPER}} .pp-post-thumbnail img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'image_spacing',
			array(
				'label'     => __( 'Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'default'   => array(
					'size' => 20,
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-post-thumbnail' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'thumbnail_effects_tabs' );

		$this->start_controls_tab(
			'normal',
			array(
				'label'     => __( 'Normal', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'thumbnail_filters',
				'selector'  => '{{WRAPPER}} .pp-post-thumbnail img',
				'condition' => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Transition::get_type(),
			array(
				'name'      => 'image_transition',
				'selector'  => '{{WRAPPER}} .pp-post-thumbnail img',
				'separator' => '',
				'condition' => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			array(
				'label'     => __( 'Hover', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'      => 'thumbnail_hover_filters',
				'selector'  => '{{WRAPPER}} .pp-post:hover .pp-post-thumbnail img',
				'condition' => array(
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Title
	 */
	protected function register_style_title_controls() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label'     => __( 'Title', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'post_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-post-title, {{WRAPPER}} .pp-post-title a' => 'color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'post_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label'     => __( 'Hover Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-post-title a:hover' => 'color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'post_title' ) => 'yes',
					$this->get_control_id( 'post_title_link' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'title_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'selector'  => '{{WRAPPER}} .pp-post-title',
				'condition' => array(
					$this->get_control_id( 'post_title' ) => 'yes',
				),
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
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'    => array(
					'size' => 10,
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'post_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'title_separator_heading',
			array(
				'label'     => __( 'Separator', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'post_title' ) => 'yes',
					$this->get_control_id( 'post_title_separator' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'title_separator_background',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .pp-post-separator',
				'exclude'   => array(
					'image',
				),
				'condition' => array(
					$this->get_control_id( 'post_title' ) => 'yes',
					$this->get_control_id( 'post_title_separator' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'title_separator_height',
			array(
				'label'      => __( 'Separator Height', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 20,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-separator' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'post_title' ) => 'yes',
					$this->get_control_id( 'post_title_separator' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'title_separator_width',
			array(
				'label'      => __( 'Separator Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'range'      => array(
					'%'  => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
					'px' => array(
						'min'  => 10,
						'max'  => 200,
						'step' => 1,
					),
				),
				'size_units' => array( '%', 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-separator' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'post_title' ) => 'yes',
					$this->get_control_id( 'post_title_separator' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'title_separator_margin_bottom',
			array(
				'label'      => __( 'Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 15,
				),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-separator-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'post_title' ) => 'yes',
					$this->get_control_id( 'post_title_separator' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Post Terms
	 */
	protected function register_style_terms_controls() {
		$this->start_controls_section(
			'section_terms_style',
			array(
				'label'     => __( 'Post Terms', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'terms_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'selector'  => '{{WRAPPER}} .pp-post-terms',
				'condition' => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'terms_margin_bottom',
			array(
				'label'      => __( 'Bottom Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 10,
				),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-terms-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'terms_gap',
			array(
				'label'      => __( 'Terms Gap', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 5,
				),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-terms .pp-post-term:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'terms_style_tabs' );

		$this->start_controls_tab(
			'terms_style_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_control(
			'terms_bg_color',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pp-post-terms' => 'background: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'terms_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-post-terms' => 'color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'terms_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-terms' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'terms_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-terms' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'terms_style_hover',
			array(
				'label' => __( 'Hover', 'powerpack' ),
			)
		);

		$this->add_control(
			'terms_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pp-post-terms:hover' => 'background: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'terms_text_color_hover',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-post-terms a:hover' => 'color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'post_terms' ) => 'yes',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Content
	 */
	protected function register_style_excerpt_controls() {
		$this->start_controls_section(
			'section_excerpt_style',
			array(
				'label'     => __( 'Content', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'excerpt_color',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-post-excerpt' => 'color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'excerpt_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'selector'  => '{{WRAPPER}} .pp-post-excerpt',
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'excerpt_margin_bottom',
			array(
				'label'      => __( 'Bottom Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'    => array(
					'size' => 20,
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Meta
	 */
	protected function register_style_meta_controls() {

		$this->start_controls_section(
			'section_meta_style',
			array(
				'label'     => __( 'Meta', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'meta_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-post-meta' => 'color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'meta_links_color',
			array(
				'label'     => __( 'Links Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-post-meta a' => 'color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'meta_links_color_hover',
			array(
				'label'     => __( 'Links Hover Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-post-meta a:hover' => 'color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'meta_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'selector'  => '{{WRAPPER}} .pp-post-meta',
				'condition' => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'meta_items_spacing',
			array(
				'label'      => __( 'Meta Items Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'    => array(
					'size' => 5,
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-meta .pp-meta-separator:not(:last-child)' => 'margin-left: calc({{SIZE}}{{UNIT}} / 2); margin-right: calc({{SIZE}}{{UNIT}} / 2);',
				),
				'condition'  => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'meta_margin_bottom',
			array(
				'label'      => __( 'Bottom Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'default'    => array(
					'size' => 20,
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-post-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'post_meta' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Style Tab: Button
	 */
	protected function register_style_button_controls() {

		$this->start_controls_section(
			'section_button_style',
			array(
				'label'     => __( 'Read More Button', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'button_size',
			array(
				'label'     => __( 'Size', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'sm',
				'options'   => array(
					'xs' => __( 'Extra Small', 'powerpack' ),
					'sm' => __( 'Small', 'powerpack' ),
					'md' => __( 'Medium', 'powerpack' ),
					'lg' => __( 'Large', 'powerpack' ),
					'xl' => __( 'Extra Large', 'powerpack' ),
				),
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'button_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-posts-button',
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			array(
				'label'     => __( 'Normal', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'button_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-button' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'button_text_color_normal',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-button' => 'color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'button_border_normal',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-posts-button',
				'condition'   => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-posts-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'button_margin',
			array(
				'label'      => __( 'Margin', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-posts-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-posts-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'button_box_shadow',
				'selector'  => '{{WRAPPER}} .pp-posts-button',
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'read_more_button_icon_style_heading',
			array(
				'label'     => __( 'Button Icon', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
					$this->get_control_id( 'select_button_icon[value]!' ) => '',
				),
			)
		);

		$this->add_control(
			'button_icon_spacing',
			array(
				'label'     => __( 'Icon Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
					$this->get_control_id( 'select_button_icon[value]!' ) => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			array(
				'label'     => __( 'Hover', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'button_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-button:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'button_text_color_hover',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-button:hover' => 'color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'button_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-button:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'button_animation',
			array(
				'label'     => __( 'Animation', 'powerpack' ),
				'type'      => Controls_Manager::HOVER_ANIMATION,
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'button_box_shadow_hover',
				'selector'  => '{{WRAPPER}} .pp-posts-button:hover',
				'condition' => array(
					$this->get_control_id( 'show_button' ) => 'yes',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function register_style_arrows_controls() {
		$this->start_controls_section(
			'section_arrows_style',
			array(
				'label'     => __( 'Arrows', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
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
				'condition'          => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
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
					'{{WRAPPER}} .pp-slider-arrow' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_position',
			array(
				'label'      => __( 'Align Arrows', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => -100,
						'max'  => 50,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-arrow-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-arrow-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_arrows_style' );

		$this->start_controls_tab(
			'tab_arrows_normal',
			array(
				'label'     => __( 'Normal', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-slider-arrow' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
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
					'{{WRAPPER}} .pp-slider-arrow' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
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
				'selector'    => '{{WRAPPER}} .pp-slider-arrow',
				'condition'   => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-slider-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrows_hover',
			array(
				'label'     => __( 'Hover', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-slider-arrow:hover' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
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
					'{{WRAPPER}} .pp-slider-arrow:hover' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
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
					'{{WRAPPER}} .pp-slider-arrow:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'arrows_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-slider-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
				'condition'  => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'arrows' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	public function register_style_dots_controls() {
		$this->start_controls_section(
			'section_dots_style',
			array(
				'label'     => __( 'Dots', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'dots_size',
			array(
				'label'      => __( 'Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 2,
						'max'  => 40,
						'step' => 1,
					),
				),
				'size_units' => '',
				'selectors'  => array(
					'{{WRAPPER}} .pp-slick-slider .slick-dots li button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'dots_spacing',
			array(
				'label'      => __( 'Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units' => '',
				'selectors'  => array(
					'{{WRAPPER}} .pp-slick-slider .slick-dots li' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_dots_style' );

		$this->start_controls_tab(
			'tab_dots_normal',
			array(
				'label'     => __( 'Normal', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_color_normal',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-slick-slider .slick-dots li' => 'background: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'dots_border_normal',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-slick-slider .slick-dots li',
				'condition'   => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'dots_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-slick-slider .slick-dots li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'dots_margin',
			array(
				'label'              => __( 'Margin', 'powerpack' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', 'em', '%' ),
				'allowed_dimensions' => 'vertical',
				'placeholder'        => array(
					'top'    => '',
					'right'  => 'auto',
					'bottom' => '',
					'left'   => 'auto',
				),
				'selectors'          => array(
					'{{WRAPPER}} .pp-slick-slider .slick-dots' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'          => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dots_active',
			array(
				'label'     => __( 'Active', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_color_active',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-slick-slider .slick-dots li.slick-active' => 'background: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_border_color_active',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-slick-slider .slick-dots li.slick-active' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dots_hover',
			array(
				'label'     => __( 'Hover', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_color_hover',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-slick-slider .slick-dots li:hover' => 'background: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->add_control(
			'dots_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-slick-slider .slick-dots li:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout' ) => 'carousel',
					$this->get_control_id( 'dots' )   => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function register_style_pagination_controls() {
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => __( 'Pagination', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_responsive_control(
			'pagination_margin_top',
			array(
				'label'     => __( 'Gap between Posts & Pagination', 'powerpack' ),
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
					'{{WRAPPER}} .pp-posts-pagination-top .pp-posts-pagination' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-posts-pagination-bottom .pp-posts-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pagination_typography',
				'selector'  => '{{WRAPPER}} .pp-posts-pagination .page-numbers, {{WRAPPER}} .pp-posts-pagination a',
				'scheme'    => Scheme_Typography::TYPOGRAPHY_2,
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->start_controls_tabs( 'tabs_pagination' );

		$this->start_controls_tab(
			'tab_pagination_normal',
			array(
				'label'     => __( 'Normal', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_control(
			'pagination_link_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-pagination .page-numbers, {{WRAPPER}} .pp-posts-pagination a' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-pagination .page-numbers, {{WRAPPER}} .pp-posts-pagination a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'pagination_link_border_normal',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-posts-pagination .page-numbers, {{WRAPPER}} .pp-posts-pagination a',
				'condition'   => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_responsive_control(
			'pagination_link_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-posts-pagination .page-numbers, {{WRAPPER}} .pp-posts-pagination a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_responsive_control(
			'pagination_link_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-posts-pagination .page-numbers, {{WRAPPER}} .pp-posts-pagination a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'pagination_link_box_shadow',
				'selector'  => '{{WRAPPER}} .pp-posts-pagination .page-numbers, {{WRAPPER}} .pp-posts-pagination a',
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_hover',
			array(
				'label'     => __( 'Hover', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_control(
			'pagination_link_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-pagination a:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_control(
			'pagination_color_hover',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-pagination a:hover' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_control(
			'pagination_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-pagination a:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'pagination_link_box_shadow_hover',
				'selector'  => '{{WRAPPER}} .pp-posts-pagination a:hover',
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_active',
			array(
				'label'     => __( 'Active', 'powerpack' ),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_control(
			'pagination_link_bg_color_active',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-pagination .page-numbers.current' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_control(
			'pagination_color_active',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-pagination .page-numbers.current' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_control(
			'pagination_border_color_active',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pp-posts-pagination .page-numbers.current' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'pagination_link_box_shadow_active',
				'selector'  => '{{WRAPPER}} .pp-posts-pagination .page-numbers.current',
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'pagination_spacing',
			array(
				'label'     => __( 'Space Between', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'separator' => 'before',
				'default'   => array(
					'size' => 10,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'body:not(.rtl) {{WRAPPER}} .pp-posts-pagination .page-numbers:not(:first-child)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'body:not(.rtl) {{WRAPPER}} .pp-posts-pagination .page-numbers:not(:last-child)' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'body.rtl {{WRAPPER}} .pp-posts-pagination .page-numbers:not(:first-child)' => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'body.rtl {{WRAPPER}} .pp-posts-pagination .page-numbers:not(:last-child)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'numbers', 'numbers_and_prev_next' ),
				),
			)
		);

		$this->add_control(
			'heading_loader',
			array(
				'label'     => __( 'Loader', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'load_more', 'infinite' ),
				),
			)
		);

		$this->add_control(
			'loader_color',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pp-loader:after, {{WRAPPER}} .pp-posts-loader:after' => 'border-bottom-color: {{VALUE}}; border-top-color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'load_more', 'infinite' ),
				),
			)
		);

		$this->add_responsive_control(
			'loader_size',
			array(
				'label'      => __( 'Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 10,
						'max'  => 80,
						'step' => 1,
					),
				),
				'default'    => array(
					'size' => 46,
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-posts-loader' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'layout!' ) => 'carousel',
					$this->get_control_id( 'pagination_type' ) => array( 'load_more', 'infinite' ),
				),
			)
		);

		$this->end_controls_section();
	}

	public function get_avatar_size( $size = 'sm' ) {

		if ( 'xs' === $size ) {
			$value = 30;
		} elseif ( 'sm' === $size ) {
			$value = 60;
		} elseif ( 'md' === $size ) {
			$value = 120;
		} elseif ( 'lg' === $size ) {
			$value = 180;
		} elseif ( 'xl' === $size ) {
			$value = 240;
		} else {
			$value = 60;
		}

		return $value;
	}

	/**
	 * Get Masonry classes array.
	 *
	 * Returns the Masonry classes array.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function get_masonry_classes() {
		$settings = $this->parent->get_settings_for_display();

		$post_type = $settings['post_type'];

		$filter_by = $this->get_instance_value( 'tax_' . $post_type . '_filter' );

		$taxonomies = wp_get_post_terms( get_the_ID(), $filter_by );
		$class      = array();

		if ( count( $taxonomies ) > 0 ) {

			foreach ( $taxonomies as $taxonomy ) {

				if ( is_object( $taxonomy ) ) {

					$class[] = $taxonomy->slug;
				}
			}
		}

		return implode( ' ', $class );
	}

	/**
	 * Render post terms output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_terms() {
		$settings   = $this->parent->get_settings_for_display();
		$post_terms = $this->get_instance_value( 'post_terms' );
		$query_type = $settings['query_type'];

		if ( 'yes' !== $post_terms ) {
			return;
		}

		$post_type = $settings['post_type'];

		if ( 'related' === $settings['post_type'] || 'main' === $query_type ) {
			$post_type = get_post_type();
		}

		$taxonomies = $this->get_instance_value( 'tax_badge_' . $post_type );

		$terms = array();

		if ( is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms_tax = wp_get_post_terms( get_the_ID(), $taxonomy );
				$terms     = array_merge( $terms, $terms_tax );
			}
		} else {
			$terms = wp_get_post_terms( get_the_ID(), $taxonomies );
		}

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		}

		$max_terms = $this->get_instance_value( 'max_terms' );

		if ( $max_terms ) {
			$terms = array_slice( $terms, 0, $max_terms );
		}

		$terms = apply_filters( 'ppe_posts_terms', $terms );

		$link_terms = $this->get_instance_value( 'post_taxonomy_link' );

		if ( 'yes' === $link_terms ) {
			$format = '<span class="pp-post-term"><a href="%2$s">%1$s</a></span>';
		} else {
			$format = '<span class="pp-post-term">%1$s</span>';
		}
		?>
		<?php do_action( 'ppe_before_single_post_terms', get_the_ID(), $settings ); ?>
		<div class="pp-post-terms-wrap">
			<span class="pp-post-terms">
				<?php
				foreach ( $terms as $term ) {
					printf( wp_kses_post( $format ), esc_attr( $term->name ), esc_url( get_term_link( (int) $term->term_id ) ) );
				}

				do_action( 'ppe_single_post_terms', get_the_ID(), $settings );
				?>
			</span>
		</div>
		<?php do_action( 'ppe_after_single_post_terms', get_the_ID(), $settings ); ?>
		<?php
	}

	/**
	 * Render post meta output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_meta_item( $item_type = '' ) {
		$skin     = $this->get_id();
		$settings = $this->parent->get_settings_for_display();

		if ( '' === $item_type ) {
			return;
		}

		$show_item   = $this->get_instance_value( 'show_' . $item_type );
		$item_link   = $this->get_instance_value( $item_type . '_link' );
		$item_prefix = $this->get_instance_value( $item_type . '_prefix' );

		if ( 'yes' !== $show_item ) {
			return;
		}

		$item_icon        = $this->get_instance_value( $item_type . '_icon' );
		$select_item_icon = $this->get_instance_value( 'select_' . $item_type . '_icon' );

		$migrated = isset( $settings['__fa4_migrated'][ $skin . '_select_' . $item_type . '_icon' ] );
		$is_new   = empty( $settings[ $skin . '_' . $item_type . '_icon' ] ) && Icons_Manager::is_migration_allowed();
		?>
		<?php do_action( 'ppe_before_single_post_' . $item_type, get_the_ID(), $settings ); ?>
		<span class="pp-post-<?php echo esc_attr( $item_type ); ?>">
			<?php
			if ( $item_icon || $select_item_icon ) {
				if ( $is_new || $migrated ) {
					Icons_Manager::render_icon( $select_item_icon, array( 'class' => 'pp-meta-icon', 'aria-hidden' => 'true' ) );
				} else { ?>
					<span class="pp-meta-icon <?php echo esc_attr( $item_icon ); ?>" aria-hidden="true"></span>
					<?php
				}
			}

			if ( $item_prefix ) {
				?>
				<span class="pp-meta-prefix">
				<?php
					echo esc_attr( $item_prefix );
				?>
				</span>
				<?php
			}
			?>
			<span class="pp-meta-text">
				<?php
				if ( 'author' === $item_type ) {
					echo wp_kses_post( $this->get_post_author( $item_link ) );
				} elseif ( 'date' === $item_type ) {
					if ( PP_Helper::is_tribe_events_post( get_the_ID() ) && function_exists( 'tribe_get_start_date' ) ) {
						$post_date = tribe_get_start_time( get_the_ID(), 'F d, Y' );
					} else {
						$post_date = $this->get_post_date();
					}

					if ( 'yes' === $item_link ) {
						echo '<a href="' . esc_url( get_permalink() ) . '">' . wp_kses_post( $post_date ) . '</a>';
					} else {
						echo wp_kses_post( $post_date );
					}
				} elseif ( 'comments' === $item_type ) {
					echo wp_kses_post( $this->get_post_comments() );
				}
				?>
			</span>
		</span>
		<span class="pp-meta-separator"></span>
		<?php do_action( 'ppe_after_single_post_' . $item_type, get_the_ID(), $settings ); ?>
		<?php
	}

	/**
	 * Get post author
	 *
	 * @access protected
	 */
	protected function get_post_author( $author_link = '' ) {
		if ( 'yes' === $author_link ) {
			return get_the_author_posts_link();
		} else {
			return get_the_author();
		}
	}

	/**
	 * Get post author
	 *
	 * @access protected
	 */
	protected function get_post_comments() {
		/**
		 * Comments Filter
		 *
		 * Filters the output for comments
		 *
		 * @since 1.4.11.0
		 * @param string    $comments       The original text
		 * @param int       get_the_id()    The post ID
		 */
		$comments = get_comments_number_text();
		$comments = apply_filters( 'ppe_posts_comments', $comments, get_the_ID() );
		return $comments;
	}

	/**
	 * Get post date
	 *
	 * @access protected
	 */
	protected function get_post_date( $date_link = '' ) {
		$date_type = $this->get_instance_value( 'date_format' );
		$date_format = $this->get_instance_value( 'date_format_select' );
		$date_custom_format = $this->get_instance_value( 'date_custom_format' );
		$date = '';

		if ( 'custom' === $date_format && $date_custom_format ) {
			$date_format = $date_custom_format;
		}

		if ( 'ago' === $date_type ) {
			$date = sprintf( _x( '%s ago', '%s = human-readable time difference', 'powerpack' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
		} elseif ( 'modified' === $date_type ) {
			$date = get_the_modified_date( $date_format, get_the_ID() );
		} elseif ( 'key' === $date_type ) {
			$date_meta_key = $this->get_instance_value( 'date_meta_key' );
			if ( $date_meta_key ) {
				$date = get_post_meta( get_the_ID(), $date_meta_key, 'true' );
			}
		} else {
			$date = get_the_date( $date_format );
		}

		if ( '' === $date ) {
			$date = get_the_date( $date_format );
		}

		return apply_filters( 'ppe_posts_date', $date, get_the_ID() );
	}

	/**
	 * Render post thumbnail output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function get_post_thumbnail() {
		$settings              = $this->parent->get_settings_for_display();
		$image                 = $this->get_instance_value( 'show_thumbnail' );
		$fallback_image        = $this->get_instance_value( 'fallback_image' );
		$fallback_image_custom = $this->get_instance_value( 'fallback_image_custom' );

		if ( 'yes' !== $image ) {
			return;
		}

		if ( has_post_thumbnail() ) {

			$image_id = get_post_thumbnail_id( get_the_ID() );

			$setting_key              = $this->get_control_id( 'thumbnail' );
			$settings[ $setting_key ] = array(
				'id' => $image_id,
			);
			$thumbnail_html           = Group_Control_Image_Size::get_attachment_image_html( $settings, $setting_key );

		} elseif ( 'default' === $fallback_image ) {

			$thumbnail_url  = Utils::get_placeholder_image_src();
			$thumbnail_html = '<img src="' . $thumbnail_url . '"/>';

		} elseif ( 'custom' === $fallback_image ) {

			$custom_image_id          = $fallback_image_custom['id'];
			$setting_key              = $this->get_control_id( 'thumbnail' );
			$settings[ $setting_key ] = array(
				'id' => $custom_image_id,
			);
			$thumbnail_html           = Group_Control_Image_Size::get_attachment_image_html( $settings, $setting_key );

		}

		if ( empty( $thumbnail_html ) ) {
			return;
		}

		return $thumbnail_html;
	}

	/**
	 * Render post title output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_post_title() {
		$settings = $this->parent->get_settings_for_display();

		$show_post_title      = $this->get_instance_value( 'post_title' );
		$title_tag            = PP_Helper::validate_html_tag( $this->get_instance_value( 'title_html_tag' ) );
		$title_link           = $this->get_instance_value( 'post_title_link' );
		$title_link_key       = 'title-link-' . get_the_ID();
		$title_link_target    = $this->get_instance_value( 'post_title_link_target' );
		$post_title_separator = $this->get_instance_value( 'post_title_separator' );

		if ( 'yes' !== $show_post_title ) {
			return;
		}

		$post_title = get_the_title();
		/**
		 * Post Title Filter
		 *
		 * Filters post title
		 *
		 * @since 1.4.11.0
		 * @param string    $post_title     The original text
		 * @param int       get_the_id()    The post ID
		 */
		$post_title = apply_filters( 'ppe_posts_title', $post_title, get_the_ID() );
		if ( $post_title ) {
			?>
			<?php do_action( 'ppe_before_single_post_title', get_the_ID(), $settings ); ?>
			<<?php echo esc_html( $title_tag ); ?> class="pp-post-title">
				<?php
				if ( 'yes' === $title_link ) {
					$this->parent->add_render_attribute( $title_link_key, 'href', apply_filters( 'ppe_posts_title_link', get_the_permalink(), get_the_ID() ) );

					if ( 'yes' === $title_link_target ) {
						$this->parent->add_render_attribute( $title_link_key, 'target', '_blank' );
					}

					$post_title = '<a ' . $this->parent->get_render_attribute_string( $title_link_key ) . '>' . $post_title . '</a>';
				}

				echo wp_kses_post( $post_title );
				?>
			</<?php echo esc_html( $title_tag ); ?>>
			<?php
			if ( 'yes' === $post_title_separator ) {
				?>
				<div class="pp-post-separator-wrap">
					<div class="pp-post-separator"></div>
				</div>
				<?php
			}
		}

		do_action( 'ppe_after_single_post_title', get_the_ID(), $settings );
	}

	/**
	 * Render post thumbnail output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_post_thumbnail() {
		$settings = $this->parent->get_settings_for_display();

		$image_link        = $this->get_instance_value( 'thumbnail_link' );
		$image_link_key    = 'image-link-' . get_the_ID();
		$image_link_target = $this->get_instance_value( 'thumbnail_link_target' );

		$thumbnail_html = $this->get_post_thumbnail();

		if ( empty( $thumbnail_html ) ) {
			return;
		}

		if ( 'yes' === $image_link ) {
			$this->parent->add_render_attribute( $image_link_key, 'href', apply_filters( 'ppe_posts_image_link', get_the_permalink(), get_the_ID() ) );

			if ( 'yes' === $image_link_target ) {
				$this->parent->add_render_attribute( $image_link_key, 'target', '_blank' );
			}

			$thumbnail_html = '<a ' . $this->parent->get_render_attribute_string( $image_link_key ) . '>' . $thumbnail_html . '</a>';

		}
		do_action( 'ppe_before_single_post_thumbnail', get_the_ID(), $settings );
		?>
		<div class="pp-post-thumbnail">
			<div class="pp-post-thumbnail-wrap">
				<?php echo wp_kses_post( $thumbnail_html ); ?>
			</div>
		</div>
		<?php
		do_action( 'ppe_after_single_post_thumbnail', get_the_ID(), $settings );
	}

	/**
	 * Get post excerpt length.
	 *
	 * Returns the length of post excerpt.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function pp_excerpt_length_filter() {
		return $this->get_instance_value( 'excerpt_length' );
	}

	/**
	 * Get post excerpt end text.
	 *
	 * Returns the string to append to post excerpt.
	 *
	 * @param string $more returns string.
	 * @since 1.7.0
	 * @access public
	 */
	public function pp_excerpt_more_filter( $more ) {
		return ' ...';
	}

	/**
	 * Get post excerpt.
	 *
	 * Returns the post excerpt HTML wrap.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_excerpt() {
		$settings       = $this->parent->get_settings_for_display();
		$show_excerpt   = $this->get_instance_value( 'show_excerpt' );
		$excerpt_length = $this->get_instance_value( 'excerpt_length' );
		$content_type   = $this->get_instance_value( 'content_type' );
		$content_length = $this->get_instance_value( 'content_length' );

		if ( 'yes' !== $show_excerpt ) {
			return;
		}

		if ( 'excerpt' === $content_type && 0 === $excerpt_length ) {
			return;
		}
		?>
		<?php do_action( 'ppe_before_single_post_excerpt', get_the_ID(), $settings ); ?>
		<div class="pp-post-excerpt">
			<?php
			if ( 'full' === $content_type ) {
				the_content();
			} elseif ( 'content' === $content_type ) {
				$more = '...';
				$post_content = wp_trim_words( get_the_content(), $content_length, apply_filters( 'pp_posts_content_limit_more', $more ) );
				echo wp_kses_post( $post_content );
			} else {
				add_filter( 'excerpt_length', array( $this, 'pp_excerpt_length_filter' ), 20 );
				add_filter( 'excerpt_more', array( $this, 'pp_excerpt_more_filter' ), 20 );
				the_excerpt();
				remove_filter( 'excerpt_length', array( $this, 'pp_excerpt_length_filter' ), 20 );
				remove_filter( 'excerpt_more', array( $this, 'pp_excerpt_more_filter' ), 20 );
			}
			?>
		</div>
		<?php do_action( 'ppe_after_single_post_excerpt', get_the_ID(), $settings ); ?>
		<?php
	}

	/**
	 * Render button icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_button_icon() {
		$skin             = $this->get_id();
		$settings         = $this->parent->get_settings_for_display();
		$show_button      = $this->get_instance_value( 'show_button' );

		if ( 'yes' !== $show_button ) {
			return;
		}

		$button_icon          = $this->get_instance_value( 'button_icon' );
		$select_button_icon   = $this->get_instance_value( 'select_button_icon' );
		$button_icon_position = $this->get_instance_value( 'button_icon_position' );
		$button_icon_align    = ( 'before' === $button_icon_position ) ? 'left' : 'right';

		$migrated = isset( $settings['__fa4_migrated'][ $skin . '_select_button_icon' ] );
		$is_new   = empty( $settings[ $skin . '_button_icon' ] ) && Icons_Manager::is_migration_allowed();

		if ( $is_new || $migrated ) { ?>
			<span class="pp-button-icon elementor-button-icon elementor-align-icon-<?php echo esc_attr( $button_icon_align ); ?>">
				<?php Icons_Manager::render_icon( $select_button_icon, array( 'aria-hidden' => 'true' ) ); ?>
			</span>
			<?php
		} else { ?>
			<span class="pp-button-icon elementor-button-icon elementor-align-icon-<?php echo esc_attr( $button_icon_align ); ?>">
				<i class="pp-button-icon <?php echo esc_attr( $button_icon ); ?>" aria-hidden="true"></i>
			</span>
			<?php
		}
	}

	/**
	 * Render button output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_button() {
		$skin             = $this->get_id();
		$settings         = $this->parent->get_settings_for_display();
		$show_button      = $this->get_instance_value( 'show_button' );
		$button_animation = $this->get_instance_value( 'button_animation' );

		if ( 'yes' !== $show_button ) {
			return;
		}

		$button_text          = isset( $settings[ $skin . '_button_text' ] ) ? $settings[ $skin . '_button_text' ] : $this->get_instance_value( 'button_text' );
		$button_icon_position = $this->get_instance_value( 'button_icon_position' );
		$button_size          = $this->get_instance_value( 'button_size' );
		$button_link_target   = $this->get_instance_value( 'button_link_target' );

		$classes = array(
			'pp-posts-button',
			'elementor-button',
			'elementor-size-' . $button_size,
		);

		if ( $button_animation ) {
			$classes[] = 'elementor-animation-' . $button_animation;
		}

		$this->parent->add_render_attribute(
			'button-' . get_the_ID(),
			array(
				'class' => implode( ' ', $classes ),
				'href'  => apply_filters( 'ppe_posts_button_link', get_the_permalink(), get_the_ID() ),
			)
		);

		if ( 'yes' === $button_link_target ) {
			$this->parent->add_render_attribute( 'button-' . get_the_ID(), 'target', '_blank' );
		}

		/**
		 * Button Text Filter
		 *
		 * Filters the text for the button
		 *
		 * @since 1.4.11.0
		 * @param string    $button_text    The original text
		 * @param int       get_the_id()    The post ID
		 */
		$button_text = apply_filters( 'ppe_posts_button_text', $button_text, get_the_ID() );
		?>
		<?php do_action( 'ppe_before_single_post_button', get_the_ID(), $settings ); ?>
		<a <?php echo wp_kses_post( $this->parent->get_render_attribute_string( 'button-' . get_the_ID() ) ); ?>>
			<?php if ( 'before' === $button_icon_position ) {
				$this->render_button_icon();
			} ?>
			<?php if ( $button_text ) { ?>
				<span class="pp-button-text">
					<?php echo wp_kses_post( $button_text ); ?>
				</span>
			<?php } ?>
			<?php if ( 'after' === $button_icon_position ) {
				$this->render_button_icon();
			} ?>
		</a>
		<?php do_action( 'ppe_after_single_post_button', get_the_ID(), $settings ); ?>
		<?php
	}

	/**
	 * Render post body output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function render_ajax_post_body( $filter = '', $taxonomy = '', $search = '' ) {
		ob_start();
		$this->parent->query_posts( $filter, $taxonomy, $search );

		$query       = $this->parent->get_query();
		$total_pages = $query->max_num_pages;

		while ( $query->have_posts() ) {
			$query->the_post();

			$this->render_post_body();
		}

		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Render post body output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function render_ajax_pagination() {
		ob_start();
		$this->render_pagination();
		return ob_get_clean();
	}

	/**
	 * Render post body output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function render_ajax_not_found( $filter = '', $taxonomy = '', $search = '' ) {
		ob_start();
		$this->parent->query_posts( $filter, $taxonomy, $search );

		$query = $this->parent->get_query();

		if ( ! $query->found_posts ) {
			$this->render_search();
		}
		return ob_get_clean();
	}

	/**
	 * Get Pagination.
	 *
	 * Returns the Pagination HTML.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_pagination() {
		$settings  = $this->parent->get_settings_for_display();

		$pagination_type    = $this->get_instance_value( 'pagination_type' );
		$page_limit         = $this->get_instance_value( 'pagination_page_limit' );
		$pagination_shorten = $this->get_instance_value( 'pagination_numbers_shorten' );

		if ( 'none' === $pagination_type ) {
			return;
		}

		// Get current page number.
		$paged = $this->parent->get_paged();

		$query       = $this->parent->get_query();
		$total_pages = $query->max_num_pages;
		$total_pages_pagination = $query->max_num_pages;

		if ( 2 > $total_pages ) {
			return;
		}

		$has_numbers   = in_array( $pagination_type, array( 'numbers', 'numbers_and_prev_next' ) );
		$has_prev_next = ( 'numbers_and_prev_next' === $pagination_type );
		$is_load_more  = ( 'load_more' === $pagination_type );

		$links = array();

		if ( $has_numbers ) {

			$current_page = $paged;
			if ( ! $current_page ) {
				$current_page = 1;
			}

			$paginate_args = array(
				'type'      => 'array',
				'current'   => $current_page,
				'total'     => $total_pages,
				'prev_next' => false,
				'show_all'  => 'yes' !== $pagination_shorten,
			);
		}

		if ( $has_prev_next ) {
			$prev_label = $this->get_instance_value( 'pagination_prev_label' );
			$next_label = $this->get_instance_value( 'pagination_next_label' );

			$paginate_args['prev_next'] = true;

			if ( $prev_label ) {
				$paginate_args['prev_text'] = $prev_label;
			}
			if ( $next_label ) {
				$paginate_args['next_text'] = $next_label;
			}
		}

		if ( $has_numbers || $has_prev_next ) {

			if ( is_singular() && ! is_front_page() && ! is_singular( 'page' ) ) {
				global $wp_rewrite;
				if ( $wp_rewrite->using_permalinks() ) {
					$paginate_args['base']   = trailingslashit( get_permalink() ) . '%_%';
					$paginate_args['format'] = user_trailingslashit( '%#%', 'single_paged' );
				} else {
					$paginate_args['format'] = '?page=%#%';
				}
			}

			$links = paginate_links( $paginate_args );

		}

		if ( ! $is_load_more ) {
			$pagination_ajax = $this->get_instance_value( 'pagination_ajax' );
			$query_type      = $settings['query_type'];
			$pagination_type = 'standard';

			if ( 'yes' === $pagination_ajax && 'main' !== $query_type ) {
				$pagination_type = 'ajax';
			}
			?>
			<nav class="pp-posts-pagination pp-posts-pagination-<?php echo esc_attr( $pagination_type ); ?> elementor-pagination" role="navigation" aria-label="<?php esc_attr_e( 'Pagination', 'powerpack' ); ?>" data-total="<?php echo esc_html( $total_pages_pagination ); ?>">
				<?php echo implode( PHP_EOL, $links ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</nav>
			<?php
		}
	}

	public function get_posts_outer_wrap_classes() {
		$pagination_type = $this->get_instance_value( 'pagination_type' );

		$classes = array(
			'pp-posts-container',
		);

		if ( 'infinite' === $pagination_type ) {
			$classes[] = 'pp-posts-infinite-scroll';
		}

		return apply_filters( 'ppe_posts_outer_wrap_classes', $classes );
	}

	public function get_posts_wrap_classes() {
		$layout = $this->get_instance_value( 'layout' );

		$classes = array(
			'pp-posts',
			'pp-posts-skin-' . $this->get_id(),
		);

		if ( 'carousel' === $layout ) {
			$classes[] = 'pp-posts-carousel';
			$classes[] = 'pp-slick-slider';
		} else {
			$classes[] = 'pp-elementor-grid';
			$classes[] = 'pp-posts-grid';
		}

		return apply_filters( 'ppe_posts_wrap_classes', $classes );
	}

	public function get_item_wrap_classes() {
		$layout = $this->get_instance_value( 'layout' );

		$classes = array( 'pp-post-wrap' );

		if ( 'carousel' === $layout ) {
			$classes[] = 'pp-carousel-item-wrap';
		} else {
			$classes[] = 'pp-grid-item-wrap';
		}

		return implode( ' ', $classes );
	}

	public function get_item_classes() {
		$layout = $this->get_instance_value( 'layout' );

		$classes = array();

		$classes[] = 'pp-post';

		if ( 'carousel' === $layout ) {
			$classes[] = 'pp-carousel-item';
		} else {
			$classes[] = 'pp-grid-item';
		}

		return implode( ' ', $classes );
	}

	public function get_ordered_items( $items ) {

		if ( ! $items ) {
			return;
		}

		$ordered_items = array();

		foreach ( $items as $item ) {
			$order = $this->get_instance_value( $item . '_order' );

			$order = ( $order ) ? $order : 1;

			$ordered_items[ $item ] = $order;
		}

		asort( $ordered_items );

		return $ordered_items;
	}

	/**
	 * Render post meta output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_post_meta() {
		$settings  = $this->parent->get_settings_for_display();
		$post_meta = $this->get_instance_value( 'post_meta' );

		if ( 'yes' === $post_meta ) {
			?>
			<?php do_action( 'ppe_before_single_post_meta', get_the_ID(), $settings ); ?>
			<div class="pp-post-meta">
				<?php
					$meta_items = $this->get_ordered_items( Module::get_meta_items() );

				foreach ( $meta_items as $meta_item => $index ) {
					if ( 'author' === $meta_item ) {
						// Post Author
						$this->render_meta_item( 'author' );
					}

					if ( 'date' === $meta_item ) {
						// Post Date
						$this->render_meta_item( 'date' );
					}

					if ( 'comments' === $meta_item ) {
						// Post Comments
						$this->render_meta_item( 'comments' );
					}
				}
				?>
			</div>
			<?php
			do_action( 'ppe_after_single_post_meta', get_the_ID(), $settings );
		}
	}

	/**
	 * Render post body output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_post_body() {
		$settings = $this->parent->get_settings_for_display();

		$post_terms         = $this->get_instance_value( 'post_terms' );
		$post_meta          = $this->get_instance_value( 'post_meta' );
		$thumbnail_location = $this->get_instance_value( 'thumbnail_location' );

		do_action( 'ppe_before_single_post_wrap', get_the_ID(), $settings );
		?>
		<div <?php post_class( $this->get_item_wrap_classes() ); ?>>
			<?php do_action( 'ppe_before_single_post', get_the_ID(), $settings ); ?>
			<div class="<?php echo esc_attr( $this->get_item_classes() ); ?>">
				<?php
				if ( 'outside' === $thumbnail_location ) {
					$this->render_post_thumbnail();
				}
				?>

				<?php do_action( 'ppe_before_single_post_content', get_the_ID(), $settings ); ?>

				<div class="pp-post-content">
					<?php
						$content_parts = $this->get_ordered_items( Module::get_post_parts() );

					foreach ( $content_parts as $part => $index ) {
						if ( 'thumbnail' === $part ) {
							if ( 'inside' === $thumbnail_location ) {
								$this->render_post_thumbnail();
							}
						}

						if ( 'terms' === $part ) {
							$this->render_terms();
						}

						if ( 'title' === $part ) {
							$this->render_post_title();
						}

						if ( 'meta' === $part ) {
							$this->render_post_meta();
						}

						if ( 'excerpt' === $part ) {
							$this->render_excerpt();
						}

						if ( 'button' === $part ) {
							$this->render_button();
						}
					}
					?>
				</div>

				<?php do_action( 'ppe_after_single_post_content', get_the_ID(), $settings ); ?>
			</div>
			<?php do_action( 'ppe_after_single_post', get_the_ID(), $settings ); ?>
		</div>
		<?php
		do_action( 'ppe_after_single_post_wrap', get_the_ID(), $settings );
	}

	/**
	 * Render Search Form HTML.
	 *
	 * Returns the Search Form HTML.
	 *
	 * @since 1.4.11.0
	 * @access public
	 */
	public function render_search() {
		$settings = $this->parent->get_settings_for_display();
		?>
		<div class="pp-posts-empty">
			<?php if ( $settings['nothing_found_message'] ) { ?>
				<p><?php echo wp_kses_post( $settings['nothing_found_message'] ); ?></p>
			<?php } ?>

			<?php if ( 'yes' === $settings['show_search_form'] ) { ?>
				<?php get_search_form(); ?>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Carousel Settings.
	 *
	 * @access public
	 */
	public function slider_settings() {
		$skin            = $this->get_id();
		$autoplay        = $this->get_instance_value( 'autoplay' );
		$autoplay_speed  = $this->get_instance_value( 'autoplay_speed' );
		$arrows          = $this->get_instance_value( 'arrows' );
		$arrow           = $this->get_instance_value( 'arrow' );
		$select_arrow    = $this->get_instance_value( 'select_arrow' );
		$dots            = $this->get_instance_value( 'dots' );
		$animation_speed = $this->get_instance_value( 'animation_speed' );
		$infinite_loop   = $this->get_instance_value( 'infinite_loop' );
		$pause_on_hover  = $this->get_instance_value( 'pause_on_hover' );
		$adaptive_height = $this->get_instance_value( 'adaptive_height' );
		$direction       = $this->get_instance_value( 'direction' );

		$slides_to_show          = ( $this->get_instance_value( 'columns' ) !== '' ) ? absint( $this->get_instance_value( 'columns' ) ) : 3;
		$slides_to_show_tablet   = ( $this->get_instance_value( 'columns_tablet' ) !== '' ) ? absint( $this->get_instance_value( 'columns_tablet' ) ) : 2;
		$slides_to_show_mobile   = ( $this->get_instance_value( 'columns_mobile' ) !== '' ) ? absint( $this->get_instance_value( 'columns_mobile' ) ) : 2;
		$slides_to_scroll        = ( $this->get_instance_value( 'slides_to_scroll' ) !== '' ) ? absint( $this->get_instance_value( 'slides_to_scroll' ) ) : 1;
		$slides_to_scroll_tablet = ( $this->get_instance_value( 'slides_to_scroll_tablet' ) !== '' ) ? absint( $this->get_instance_value( 'slides_to_scroll_tablet' ) ) : 1;
		$slides_to_scroll_mobile = ( $this->get_instance_value( 'slides_to_scroll_mobile' ) !== '' ) ? absint( $this->get_instance_value( 'slides_to_scroll_mobile' ) ) : 1;

		$slider_options = array(
			'slidesToShow'   => $slides_to_show,
			'slidesToScroll' => $slides_to_scroll,
			'autoplay'       => ( 'yes' === $autoplay ),
			'autoplaySpeed'  => ( $autoplay_speed ) ? $autoplay_speed : 3000,
			'arrows'         => ( 'yes' === $arrows ),
			'dots'           => ( 'yes' === $dots ),
			'speed'          => ( $animation_speed ) ? $animation_speed : 600,
			'infinite'       => ( 'yes' === $infinite_loop ),
			'pauseOnHover'   => ( 'yes' === $pause_on_hover ),
			'adaptiveHeight' => ( 'yes' === $adaptive_height ),
		);

		if ( 'right' === $direction ) {
			$slider_options['rtl'] = true;
		}

		if ( 'yes' === $arrows ) {
			if ( ! isset( $settings[ $skin . '_arrow' ] ) && ! Icons_Manager::is_migration_allowed() ) {
				// add old default.
				$settings[ $skin . '_arrow' ] = 'fa fa-angle-right';
			}

			$has_icon = ! empty( $settings[ $skin . '_arrow' ] );

			if ( ! $has_icon && ! empty( $select_arrow['value'] ) ) {
				$has_icon = true;
			}
			$migrated = isset( $settings['__fa4_migrated'][ $skin . '_select_arrow' ] );
			$is_new   = ! isset( $settings[ $skin . '_arrow' ] ) && Icons_Manager::is_migration_allowed();

			if ( $is_new || $migrated ) {
				$arrow = $select_arrow['value'];
			}

			if ( $arrow ) {
				$pa_next_arrow = $arrow;
				$pa_prev_arrow = str_replace( 'right', 'left', $arrow );
			} else {
				$pa_next_arrow = 'fa fa-angle-right';
				$pa_prev_arrow = 'fa fa-angle-left';
			}

			$slider_options['prevArrow'] = '<div class="pp-slider-arrow pp-arrow pp-arrow-prev"><i class="' . $pa_prev_arrow . '"></i></div>';
			$slider_options['nextArrow'] = '<div class="pp-slider-arrow pp-arrow pp-arrow-next"><i class="' . $pa_next_arrow . '"></i></div>';
		}

		if ( 'yes' === $this->get_instance_value( 'center_mode' ) ) {
			$center_mode = true;

			$slider_options['centerMode'] = $center_mode;
		} else {
			$center_mode = false;
		}

		$slider_options['responsive'] = array(
			array(
				'breakpoint' => 1024,
				'settings'   => array(
					'slidesToShow'   => $slides_to_show_tablet,
					'slidesToScroll' => $slides_to_scroll_tablet,
					'centerMode'     => $center_mode,
				),
			),
			array(
				'breakpoint' => 768,
				'settings'   => array(
					'slidesToShow'   => $slides_to_show_mobile,
					'slidesToScroll' => $slides_to_scroll_mobile,
					'centerMode'     => $center_mode,
				),
			),
		);

		$this->parent->add_render_attribute(
			'posts-wrap',
			array(
				'data-slider-settings' => wp_json_encode( $slider_options ),
			)
		);
	}

	/**
	 * Render posts grid widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function render() {
		$settings = $this->parent->get_settings_for_display();

		$query_type          = $settings['query_type'];
		$layout              = $this->get_instance_value( 'layout' );
		$pagination_type     = $this->get_instance_value( 'pagination_type' );
		$pagination_position = $this->get_instance_value( 'pagination_position' );
		$equal_height        = $this->get_instance_value( 'equal_height' );
		$direction           = $this->get_instance_value( 'direction' );
		$skin                = $this->get_id();
		$posts_outer_wrap    = $this->get_posts_outer_wrap_classes();
		$posts_wrap          = $this->get_posts_wrap_classes();
		$page_id             = '';
		if ( null !== \Elementor\Plugin::$instance->documents->get_current() ) {
			$page_id = \Elementor\Plugin::$instance->documents->get_current()->get_main_id();
		}

		$this->parent->add_render_attribute( 'posts-container', 'class', $posts_outer_wrap );

		$this->parent->add_render_attribute( 'posts-wrap', 'class', $posts_wrap );

		if ( 'carousel' === $layout ) {
			if ( 'yes' === $equal_height ) {
				$this->parent->add_render_attribute( 'posts-wrap', 'data-equal-height', 'yes' );
			}
			if ( 'right' === $direction ) {
				$this->parent->add_render_attribute( 'posts-wrap', 'dir', 'rtl' );
			}
		}

		$this->parent->add_render_attribute(
			'posts-wrap',
			array(
				'data-query-type' => $query_type,
				'data-layout'     => $layout,
				'data-page'       => $page_id,
				'data-skin'       => $skin,
			)
		);

		$this->parent->add_render_attribute( 'post-categories', 'class', 'pp-post-categories' );

		$filter   = '';
		$taxonomy = '';

		$this->parent->query_posts( $filter, $taxonomy );
		$query = $this->parent->get_query();

		if ( 'carousel' === $layout ) {
			$this->slider_settings();
		}

		if ( ! $query->found_posts ) {
			?>
			<div <?php echo wp_kses_post( $this->parent->get_render_attribute_string( 'posts-container' ) ); ?>>
			<?php
			$this->render_search();
			?>
			</div>
			<?php
			return;
		}

		do_action( 'ppe_before_posts_outer_wrap', $settings );
		?>
		<div <?php echo wp_kses_post( $this->parent->get_render_attribute_string( 'posts-container' ) ); ?>>
			<?php
			do_action( 'ppe_before_posts_wrap', $settings );

			$i = 1;

			$total_pages = $query->max_num_pages;
			?>

			<?php if ( 'carousel' !== $layout ) { ?>
				<?php if ( ( 'numbers' === $pagination_type || 'numbers_and_prev_next' === $pagination_type ) && ( 'top' === $pagination_position || 'top-bottom' === $pagination_position ) ) { ?>
				<div class="pp-posts-pagination-wrap pp-posts-pagination-top">
					<?php
						$this->render_pagination();
					?>
				</div>
				<?php } ?>
			<?php } ?>

			<div <?php echo wp_kses_post( $this->parent->get_render_attribute_string( 'posts-wrap' ) ); ?>>
				<?php
				$i = 1;

				if ( $query->have_posts() ) :
					while ( $query->have_posts() ) :
						$query->the_post();

						$this->render_post_body();

						$i++;

					endwhile;
				endif;
				wp_reset_postdata();
				?>
			</div>

			<?php do_action( 'ppe_after_posts_wrap', $settings ); ?>

			<?php if ( 'load_more' === $pagination_type || 'infinite' === $pagination_type ) { ?>
			<div class="pp-posts-loader"></div>
			<?php } ?>

			<?php
			if ( 'load_more' === $pagination_type || 'infinite' === $pagination_type ) {
				$pagination_bottom = true;
			} elseif ( ( 'numbers' === $pagination_type || 'numbers_and_prev_next' === $pagination_type ) && ( '' === $pagination_position || 'bottom' === $pagination_position || 'top-bottom' === $pagination_position ) ) {
				$pagination_bottom = true;
			} else {
				$pagination_bottom = false;
			}
			?>

			<?php if ( 'carousel' !== $layout ) { ?>
				<?php if ( $pagination_bottom ) { ?>
				<div class="pp-posts-pagination-wrap pp-posts-pagination-bottom">
					<?php
						$this->render_pagination();
					?>
				</div>
				<?php } ?>
			<?php } ?>
		</div>

		<?php do_action( 'ppe_after_posts_outer_wrap', $settings ); ?>

		<?php
		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {

			if ( 'masonry' === $layout ) {
				$this->render_editor_script();
			}
		}
	}

	/**
	 * Get masonry script.
	 *
	 * Returns the post masonry script.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function render_editor_script() {
		$settings = $this->parent->get_settings_for_display();
		$layout   = $this->get_instance_value( 'layout' );

		if ( 'masonry' !== $layout ) {
			return;
		}

		$layout = 'masonry';

		?>
		<script type="text/javascript">

			jQuery( document ).ready( function( $ ) {
				$( '.pp-posts-grid' ).each( function() {

					var $node_id 	= '<?php echo esc_attr( $this->parent->get_id() ); ?>',
						$scope 		= $( '[data-id="' + $node_id + '"]' ),
						$selector 	= $(this);

					if ( $selector.closest( $scope ).length < 1 ) {
						return;
					}

					$selector.imagesLoaded( function() {

						$isotopeObj = $selector.isotope({
							layoutMode: '<?php echo esc_attr( $layout ); ?>',
							itemSelector: '.pp-grid-item-wrap',
						});

						$selector.find( '.pp-grid-item-wrap' ).resize( function() {
							$isotopeObj.isotope( 'layout' );
						});
					});
				});
			});

		</script>
		<?php
	}
}
