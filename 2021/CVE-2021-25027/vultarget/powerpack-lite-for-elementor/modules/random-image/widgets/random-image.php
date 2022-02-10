<?php
namespace PowerpackElementsLite\Modules\RandomImage\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Modules\RandomImage\Module;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Control_Media;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gallery Slider Widget
 */
class Random_Image extends Powerpack_Widget {

	/**
	 * Retrieve gallery slider widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Random_Image' );
	}

	/**
	 * Retrieve gallery slider widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Random_Image' );
	}

	/**
	 * Retrieve the list of categories the gallery slider widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return parent::get_widget_categories( 'Random_Image' );
	}

	/**
	 * Retrieve gallery slider widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Random_Image' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Random_Image' );
	}

	/**
	 * Retrieve the list of scripts the gallery slider widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [
			'jquery-fancybox',
			'powerpack-frontend',
		];
	}

	/**
	 * Retrieve the list of styles the image slider widget depended on.
	 *
	 * Used to set styles dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_style_depends() {
		return [
			'fancybox',
		];
	}

	/**
	 * Image filters.
	 *
	 * @access public
	 * @param boolean $inherit if inherit option required.
	 * @return array Filters.
	 */
	protected function image_filters( $inherit = false ) {

		$inherit_opt = array();

		if ( $inherit ) {
			$inherit_opt = array(
				'' => __( 'Inherit', 'powerpack' ),
			);
		}

		$pp_image_filters = array(
			'normal'            => __( 'Normal', 'powerpack' ),
			'filter-1977'       => __( '1977', 'powerpack' ),
			'filter-aden'       => __( 'Aden', 'powerpack' ),
			'filter-amaro'      => __( 'Amaro', 'powerpack' ),
			'filter-ashby'      => __( 'Ashby', 'powerpack' ),
			'filter-brannan'    => __( 'Brannan', 'powerpack' ),
			'filter-brooklyn'   => __( 'Brooklyn', 'powerpack' ),
			'filter-charmes'    => __( 'Charmes', 'powerpack' ),
			'filter-clarendon'  => __( 'Clarendon', 'powerpack' ),
			'filter-crema'      => __( 'Crema', 'powerpack' ),
			'filter-dogpatch'   => __( 'Dogpatch', 'powerpack' ),
			'filter-earlybird'  => __( 'Earlybird', 'powerpack' ),
			'filter-gingham'    => __( 'Gingham', 'powerpack' ),
			'filter-ginza'      => __( 'Ginza', 'powerpack' ),
			'filter-hefe'       => __( 'Hefe', 'powerpack' ),
			'filter-helena'     => __( 'Helena', 'powerpack' ),
			'filter-hudson'     => __( 'Hudson', 'powerpack' ),
			'filter-inkwell'    => __( 'Inkwell', 'powerpack' ),
			'filter-juno'       => __( 'Juno', 'powerpack' ),
			'filter-kelvin'     => __( 'Kelvin', 'powerpack' ),
			'filter-lark'       => __( 'Lark', 'powerpack' ),
			'filter-lofi'       => __( 'Lofi', 'powerpack' ),
			'filter-ludwig'     => __( 'Ludwig', 'powerpack' ),
			'filter-maven'      => __( 'Maven', 'powerpack' ),
			'filter-mayfair'    => __( 'Mayfair', 'powerpack' ),
			'filter-moon'       => __( 'Moon', 'powerpack' ),
		);

		return array_merge( $inherit_opt, $pp_image_filters );
	}

	/**
	 * Register gallery slider widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		/* Content Tab */
		$this->register_content_gallery_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_image_controls();
		$this->register_style_caption_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_content_gallery_controls() {
		/**
		 * Content Tab: Gallery
		 */
		$this->start_controls_section(
			'section_images',
			[
				'label' => __( 'Images', 'powerpack' ),
			]
		);

		$this->add_control(
			'wp_gallery',
			[
				'label'     => __( 'Add Images', 'powerpack' ),
				'type'      => Controls_Manager::GALLERY,
				'dynamic'   => [
					'active' => true,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image', // Actually its `image_size`.
				'label'     => __( 'Image Size', 'powerpack' ),
				'default'   => 'full',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'  => [
						'title'     => __( 'Left', 'powerpack' ),
						'icon'      => 'eicon-text-align-left',
					],
					'center'    => [
						'title'     => __( 'Center', 'powerpack' ),
						'icon'      => 'eicon-text-align-center',
					],
					'right'     => [
						'title'     => __( 'Right', 'powerpack' ),
						'icon'      => 'eicon-text-align-right',
					],
				],
				'selectors'     => [
					'{{WRAPPER}} .pp-random-image-wrap' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'caption',
			[
				'label'     => __( 'Caption', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''            => __( 'None', 'powerpack' ),
					'title'       => __( 'Title', 'powerpack' ),
					'caption'     => __( 'Caption', 'powerpack' ),
					'description' => __( 'Description', 'powerpack' ),
				],
			]
		);

		$this->add_control(
			'caption_position',
			array(
				'label'     => __( 'Caption Position', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'below_image',
				'options'   => array(
					'over_image'  => __( 'Over Image', 'powerpack' ),
					'below_image' => __( 'Below Image', 'powerpack' ),
				),
				'condition' => array(
					'caption!' => '',
				),
			)
		);

		$this->add_control(
			'link_to',
			[
				'label' => __( 'Link to', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => __( 'None', 'powerpack' ),
					'file' => __( 'Media File', 'powerpack' ),
					'custom' => __( 'Custom URL', 'powerpack' ),
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __( 'Link', 'powerpack' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'powerpack' ),
				'condition' => [
					'link_to' => 'custom',
				],
				'show_label' => false,
			]
		);

		$this->add_control(
			'open_lightbox',
			[
				'label' => __( 'Lightbox', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'powerpack' ),
					'yes' => __( 'Yes', 'powerpack' ),
					'no' => __( 'No', 'powerpack' ),
				],
				'condition' => [
					'link_to' => 'file',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links( 'Random_Image' );

		if ( ! empty( $help_docs ) ) {

			/**
			 * Content Tab: Help Docs
			 *
			 * @since 2.3.0
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

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_style_image_controls() {
		/**
		 * Style Tab: Image
		 */
		$this->start_controls_section(
			'section_image',
			[
				'label'                 => __( 'Image', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => __( 'Width', 'powerpack' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pp-random-image' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'space',
			[
				'label' => __( 'Max Width', 'powerpack' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pp-random-image' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => __( 'Height', 'powerpack' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'tablet_default' => [
					'unit' => 'px',
				],
				'mobile_default' => [
					'unit' => 'px',
				],
				'size_units' => [ 'px', 'vh' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pp-random-image' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'object-fit',
			[
				'label' => __( 'Object Fit', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'height[size]!' => '',
				],
				'options' => [
					'' => __( 'Default', 'powerpack' ),
					'fill' => __( 'Fill', 'powerpack' ),
					'cover' => __( 'Cover', 'powerpack' ),
					'contain' => __( 'Contain', 'powerpack' ),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pp-random-image' => 'object-fit: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'separator_panel_style',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( 'image_effects' );

		$this->start_controls_tab( 'normal',
			[
				'label' => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'opacity',
			[
				'label' => __( 'Opacity', 'powerpack' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pp-random-image' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .pp-random-image',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'opacity_hover',
			[
				'label' => __( 'Opacity', 'powerpack' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pp-random-image-wrap:hover .pp-random-image' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .pp-random-image-wrap:hover .pp-random-image',
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => __( 'Transition Duration', 'powerpack' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pp-random-image' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Hover Animation', 'powerpack' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .pp-random-image',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'powerpack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pp-random-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .pp-random-image',
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_caption_controls() {
		/**
		 * Style Tab: Caption
		 */
		$this->start_controls_section(
			'section_caption_style',
			[
				'label'                 => __( 'Caption', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'caption_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-random-image-caption',
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_control(
			'caption_vertical_align',
			[
				'label'                 => __( 'Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'                => false,
				'default'               => 'bottom',
				'options'               => [
					'top'          => [
						'title'    => __( 'Top', 'powerpack' ),
						'icon'     => 'eicon-v-align-top',
					],
					'middle'       => [
						'title'    => __( 'Center', 'powerpack' ),
						'icon'     => 'eicon-v-align-middle',
					],
					'bottom'       => [
						'title'    => __( 'Bottom', 'powerpack' ),
						'icon'     => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary'  => [
					'top'          => 'flex-start',
					'middle'       => 'center',
					'bottom'       => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-media-content'   => 'justify-content: {{VALUE}};',
				],
				'condition'             => [
					'caption!'         => '',
					'caption_position' => 'over_image',
				],
			]
		);

		$this->add_control(
			'caption_horizontal_align',
			[
				'label'                 => __( 'Horizontal Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'                => false,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'           => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'            => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-h-align-right',
					],
					'justify'          => [
						'title' => __( 'Justify', 'powerpack' ),
						'icon'  => 'eicon-h-align-stretch',
					],
				],
				'default'               => 'left',
				'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'center'   => 'center',
					'right'    => 'flex-end',
					'justify'  => 'stretch',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-media-content' => 'align-items: {{VALUE}};',
				],
				'condition'             => [
					'caption!'         => '',
					'caption_position' => 'over_image',
				],
			]
		);

		$this->add_control(
			'caption_text_align',
			[
				'label'                 => __( 'Text Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'    => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'     => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'               => 'center',
				'selectors'             => [
					'{{WRAPPER}} .pp-media-content' => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'caption!'                 => '',
					'caption_horizontal_align' => 'justify',
				],
				'conditions'        => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => 'caption',
							'operator' => '!=',
							'value' => '',
						],
						[
							'relation' => 'or',
							'terms' => [
								[
									'relation' => 'and',
									'terms' => [
										[
											'name' => 'caption_position',
											'operator' => '==',
											'value' => 'over_image',
										],
										[
											'name' => 'caption_horizontal_align',
											'operator' => '==',
											'value' => 'justify',
										],
									],
								],
								[
									'name' => 'caption_position',
									'operator' => '==',
									'value' => 'below_image',
								],
							],
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'caption_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-random-image-caption' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_responsive_control(
			'caption_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-random-image-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_control(
			'caption_hover_effect',
			[
				'label'                 => __( 'Hover Effect', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => '',
				'options'               => [
					''                  => __( 'None', 'powerpack' ),
					'fade-in'           => __( 'Fade In', 'powerpack' ),
					'fade-out'          => __( 'Fade Out', 'powerpack' ),
					'fade-from-top'     => __( 'Fade From Top', 'powerpack' ),
					'fade-from-bottom'  => __( 'Fade From Bottom', 'powerpack' ),
					'fade-from-left'    => __( 'Fade From Left', 'powerpack' ),
					'fade-from-right'   => __( 'Fade From Right', 'powerpack' ),
					'slide-from-top'    => __( 'Slide From Top', 'powerpack' ),
					'slide-from-bottom' => __( 'Slide From Bottom', 'powerpack' ),
					'slide-from-left'   => __( 'Slide From Left', 'powerpack' ),
					'slide-from-right'  => __( 'Slide From Right', 'powerpack' ),
					'fade-to-top'       => __( 'Fade To Top', 'powerpack' ),
					'fade-to-bottom'    => __( 'Fade To Bottom', 'powerpack' ),
					'fade-to-left'      => __( 'Fade To Left', 'powerpack' ),
					'fade-to-right'     => __( 'Fade To Right', 'powerpack' ),
					'slide-to-top'      => __( 'Slide To Top', 'powerpack' ),
					'slide-to-bottom'   => __( 'Slide To Bottom', 'powerpack' ),
					'slide-to-left'     => __( 'Slide To Left', 'powerpack' ),
					'slide-to-right'    => __( 'Slide To Right', 'powerpack' ),
				],
				'prefix_class'          => 'pp-caption-hover-effect-',
				'condition'             => [
					'caption!'         => '',
					'caption_position' => 'over_image',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_caption_style' );

		$this->start_controls_tab(
			'tab_caption_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_control(
			'caption_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-random-image-caption' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_control(
			'caption_bg_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-random-image-caption' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'caption_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-random-image-caption',
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_control(
			'caption_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-random-image-caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'                  => 'caption_text_shadow',
				'label'                 => __( 'Text Shadow', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-random-image-caption',
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_control(
			'caption_opacity_normal',
			[
				'label'                 => __( 'Opacity', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 0,
						'max'   => 1,
						'step'  => 0.01,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-random-image-caption' => 'opacity: {{SIZE}};',
				],
				'condition'             => [
					'caption!'         => '',
					'caption_position' => 'over_image',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_caption_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_control(
			'caption_color_hover',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-random-image-wrap:hover .pp-random-image-caption' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_control(
			'caption_bg_color_hover',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-random-image-wrap:hover .pp-random-image-caption' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_control(
			'caption_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-random-image-wrap:hover .pp-random-image-caption' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'                  => 'caption_text_shadow_hover',
				'label'                 => __( 'Text Shadow', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-random-image-wrap:hover .pp-random-image-caption',
				'condition'             => [
					'caption!'   => '',
				],
			]
		);

		$this->add_control(
			'caption_opacity_hover',
			[
				'label'                 => __( 'Opacity', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 0,
						'max'   => 1,
						'step'  => 0.01,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-random-image-wrap:hover .pp-random-image-caption' => 'opacity: {{SIZE}};',
				],
				'condition'             => [
					'caption!'         => '',
					'caption_position' => 'over_image',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! $settings['wp_gallery'] ) {
			$placeholder = sprintf( 'Click here to edit the "%1$s" settings and choose some images.', esc_attr( $this->get_title() ) );

			echo esc_attr( $this->render_editor_placeholder(
				array(
					'body'  => $placeholder,
				)
			) );
			return;
		}

		$count       = count( $settings['wp_gallery'] );
		$index       = ( $count > 1 ) ? wp_rand( 0, $count - 1 ) : 0;
		$id          = $settings['wp_gallery'][ $index ]['id'];
		$has_caption = '' !== $settings['caption'];
		$link        = '';
		$attachment  = get_post( $id );

		$this->add_render_attribute( [
			'wrapper' => [
				'class' => 'pp-random-image-wrap',
			],
			'figure' => [
				'class' => [
					'pp-image',
					'wp-caption',
					'pp-random-image-figure',
				],
			],
			'image' => [
				'class' => 'elementor-image pp-random-image',
				'src' => Group_Control_Image_Size::get_attachment_image_src( $id, 'image', $settings ),
				'alt' => esc_attr( Control_Media::get_image_alt( $id ) ),
			],
			'caption' => [
				'class' => [
					'widget-image-caption',
					'wp-caption-text',
					'pp-random-image-caption',
					'pp-gallery-image-caption',
				],
			],
		] );

		if ( '' !== $settings['hover_animation'] ) {
			$this->add_render_attribute( 'image', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}

		if ( 'none' !== $settings['link_to'] ) {
			if ( 'file' === $settings['link_to'] ) {
				$link = $settings['wp_gallery'][ $index ];
				$this->add_render_attribute( 'link', [
					'class' => 'pp-random-image-link',
					'data-elementor-open-lightbox' => $settings['open_lightbox'],
				] );

				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
					$this->add_render_attribute( 'link', [
						'class' => 'elementor-clickable',
					] );
				}

				$this->add_render_attribute( 'link', 'href', $link['url'] );
			} elseif ( 'custom' === $settings['link_to'] ) {
				$link = $settings['link'];

				$this->add_link_attributes( 'link', $link );
			}
		}
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrapper' ) ); ?>>
			<?php if ( $has_caption ) { ?>
			<figure <?php echo wp_kses_post( $this->get_render_attribute_string( 'figure' ) ); ?>>
			<?php } ?>

			<?php
			$image_html = '<img ' . $this->get_render_attribute_string( 'image' ) . '/>';

			if ( $link ) {
				if ( 'over_image' === $settings['caption_position'] ) {
					$image_html = '<a ' . $this->get_render_attribute_string( 'link' ) . '></a>' . $image_html;
				} else {
					$image_html = '<a ' . $this->get_render_attribute_string( 'link' ) . '>' . $image_html . '</a>';
				}
			}

			echo wp_kses_post( $image_html );
			?>
			<?php if ( $has_caption ) { ?>
				<?php if ( 'over_image' === $settings['caption_position'] ) { ?>
				<div class="pp-gallery-image-content pp-media-content">
				<?php } ?>
				<figcaption <?php echo wp_kses_post( $this->get_render_attribute_string( 'caption' ) ); ?>>
					<?php echo wp_kses_post( $this->render_image_caption( $attachment ) ); ?>
				</figcaption>
				<?php if ( 'over_image' === $settings['caption_position'] ) { ?>
				</div>
				<?php } ?>
			</figure>
			<?php } ?>
		</div>
		<?php
	}

	protected function render_image_caption( $id ) {
		$settings = $this->get_settings_for_display();

		if ( '' === $settings['caption'] ) {
			return '';
		}

		$caption_type = $settings['caption'];

		$caption = Module::get_image_caption( $id, $caption_type );

		if ( '' === $caption ) {
			return '';
		}

		ob_start();

		echo wp_kses_post( $caption );

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	protected function render_link_icon() {
		$settings = $this->get_settings_for_display();

		if ( ! isset( $settings['link_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['link_icon'] = '';
		}

		$has_link_icon = ! empty( $settings['link_icon'] );

		if ( $has_link_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['link_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		if ( ! $has_link_icon && ! empty( $settings['select_link_icon']['value'] ) ) {
			$has_link_icon = true;
		}
		$migrated_link_icon = isset( $settings['__fa4_migrated']['select_link_icon'] );
		$is_new_link_icon = ! isset( $settings['link_icon'] ) && Icons_Manager::is_migration_allowed();

		if ( ! $has_link_icon ) {
			return '';
		}

		ob_start();
		?>
		<?php if ( $has_link_icon ) { ?>
		<div class="pp-gallery-image-icon-wrap pp-media-content">
			<span class="pp-gallery-image-icon pp-icon">
				<?php
				if ( $is_new_link_icon || $migrated_link_icon ) {
					Icons_Manager::render_icon( $settings['select_link_icon'], [ 'aria-hidden' => 'true' ] );
				} elseif ( ! empty( $settings['link_icon'] ) ) {
					?><i <?php echo wp_kses_post( $this->get_render_attribute_string( 'i' ) ); ?>></i><?php
				}
				?>
			</span>
		</div>
			<?php
		}
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	protected function render_image_overlay( $count ) {
		$pp_overlay_key = $this->get_repeater_setting_key( 'overlay', 'gallery_images', $count );

		$this->add_render_attribute( $pp_overlay_key, 'class', [
			'pp-image-overlay',
			'pp-media-overlay',
		] );

		return '<div ' . $this->get_render_attribute_string( $pp_overlay_key ) . '></div>';
	}
}
