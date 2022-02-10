<?php
namespace PowerpackElementsLite\Modules\Flipbox\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Helper;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Flipbox Widget
 */
class Flipbox extends Powerpack_Widget {

	/**
	 * Retrieve flipbox widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Flipbox' );
	}

	/**
	 * Retrieve flipbox widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Flipbox' );
	}

	/**
	 * Retrieve flipbox widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Flipbox' );
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
		return parent::get_widget_keywords( 'Flipbox' );
	}

	/**
	 * Retrieve the list of scripts the logo carousel widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'powerpack-frontend',
		);
	}

	/**
	 * Register flipbox widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore 
		$this->register_controls();
	}

	/**
	 * Register flipbox widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.3.2
	 * @access protected
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_front_controls();
		$this->register_content_back_controls();
		$this->register_content_settings_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_front_controls();
		$this->register_style_back_controls();
		$this->register_style_button_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_content_front_controls() {
		/**
		 * Content Tab: Front
		 */
		$this->start_controls_section(
			'section_front',
			[
				'label'                 => esc_html__( 'Front', 'powerpack' ),
			]
		);

		$this->add_control(
			'icon_type',
			[
				'label'                 => esc_html__( 'Icon Type', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'none'  => [
						'title' => esc_html__( 'None', 'powerpack' ),
						'icon'  => 'eicon-ban',
					],
					'icon'  => [
						'title' => esc_html__( 'Icon', 'powerpack' ),
						'icon'  => 'eicon-star',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'powerpack' ),
						'icon'  => 'eicon-image-bold',
					],
					'text'  => [
						'title' => esc_html__( 'Text', 'powerpack' ),
						'icon'  => 'eicon-font',
					],
				],
				'default'               => 'icon',
			]
		);

		$this->add_control(
			'icon_image',
			[
				'label'                 => esc_html__( 'Choose Image', 'powerpack' ),
				'type'                  => Controls_Manager::MEDIA,
				'default'               => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition'             => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'select_icon',
			[
				'label'                 => __( 'Icon', 'powerpack' ),
				'type'                  => Controls_Manager::ICONS,
				'fa4compatibility'      => 'icon',
				'default'               => [
					'value'     => 'fas fa-star',
					'library'   => 'fa-solid',
				],
				'condition'             => [
					'icon_type'     => 'icon',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'thumbnail',
				'default'               => 'full',
				'condition'             => [
					'icon_type'         => 'image',
					'icon_image[url]!'  => '',
				],
			]
		);

		$this->add_control(
			'icon_text',
			array(
				'label'     => __( 'Icon Text', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => '1',
				'condition' => array(
					'icon_type' => 'text',
				),
			)
		);

		$this->add_control(
			'title_front',
			[
				'label'                 => esc_html__( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'label_block'           => true,
				'default'               => esc_html__( 'This is the heading', 'powerpack' ),
				'separator'             => 'before',
			]
		);
		$this->add_control(
			'description_front',
			[
				'label'                 => esc_html__( 'Description', 'powerpack' ),
				'type'                  => Controls_Manager::TEXTAREA,
				'label_block'           => true,
				'default'               => __( 'This is the front content. Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'powerpack' ),
			]
		);

		$this->add_control(
			'title_html_tag_front',
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

	protected function register_content_back_controls() {
		/**
		 * Content Tab: Back
		 */
		$this->start_controls_section(
			'section_back',
			[
				'label'                 => esc_html__( 'Back', 'powerpack' ),
			]
		);

		$this->add_control(
			'icon_type_back',
			[
				'label'                 => esc_html__( 'Icon Type', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'none'  => [
						'title' => esc_html__( 'None', 'powerpack' ),
						'icon'  => 'eicon-ban',
					],
					'icon'  => [
						'title' => esc_html__( 'Icon', 'powerpack' ),
						'icon'  => 'eicon-star',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'powerpack' ),
						'icon'  => 'eicon-image-bold',
					],
					'text'  => [
						'title' => esc_html__( 'Text', 'powerpack' ),
						'icon'  => 'eicon-font',
					],
				],
				'default'               => 'icon',
			]
		);

		$this->add_control(
			'icon_image_back',
			[
				'label'                 => esc_html__( 'Flipbox Image', 'powerpack' ),
				'type'                  => Controls_Manager::MEDIA,
				'default'               => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition'             => [
					'icon_type_back'    => 'image',
				],
			]
		);

		$this->add_control(
			'select_icon_back',
			[
				'label'                 => __( 'Icon', 'powerpack' ),
				'type'                  => Controls_Manager::ICONS,
				'fa4compatibility'      => 'icon_back',
				'default'               => [
					'value'     => 'far fa-snowflake',
					'library'   => 'fa-regular',
				],
				'condition'             => [
					'icon_type_back'     => 'icon',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'thumbnail_back',
				'default'               => 'full',
				'condition'             => [
					'icon_type_back'        => 'image',
					'icon_image_back[url]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_text_back',
			array(
				'label'     => __( 'Icon Text', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => '1',
				'condition' => array(
					'icon_type_back' => 'text',
				),
			)
		);

		$this->add_control(
			'title_back',
			[
				'label'                 => esc_html__( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'label_block'           => true,
				'default'               => esc_html__( 'This is the heading', 'powerpack' ),
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'description_back',
			[
				'label'                 => esc_html__( 'Description', 'powerpack' ),
				'type'                  => Controls_Manager::TEXTAREA,
				'label_block'           => true,
				'default'               => __( 'This is the front content. Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'powerpack' ),
			]
		);

		$this->add_control(
			'title_html_tag_back',
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

		$this->add_control(
			'link_type',
			[
				'label'                 => __( 'Link Type', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'none',
				'options'               => [
					'none'      => __( 'None', 'powerpack' ),
					'title'     => __( 'Title', 'powerpack' ),
					'button'    => __( 'Button', 'powerpack' ),
					'box'       => __( 'Box', 'powerpack' ),
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label'                 => __( 'Link', 'powerpack' ),
				'type'                  => Controls_Manager::URL,
				'dynamic'               => [
					'active'        => true,
					'categories'    => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder'           => 'https://www.your-link.com',
				'default'               => [
					'url' => '#',
				],
				'condition'             => [
					'link_type!'   => 'none',
				],
			]
		);

		$this->add_control(
			'flipbox_button_text',
			[
				'label'                 => __( 'Button Text', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => __( 'Get Started', 'powerpack' ),
				'condition'             => [
					'link_type'   => 'button',
				],
			]
		);

		$this->add_control(
			'select_button_icon',
			[
				'label'                 => __( 'Button Icon', 'powerpack' ),
				'type'                  => Controls_Manager::ICONS,
				'fa4compatibility'      => 'button_icon',
				'condition'             => [
					'link_type'   => 'button',
				],
			]
		);

		$this->add_control(
			'button_icon_position',
			[
				'label'                 => __( 'Icon Position', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'after',
				'options'               => [
					'after'     => __( 'After', 'powerpack' ),
					'before'    => __( 'Before', 'powerpack' ),
				],
				'condition'             => [
					'link_type'     => 'button',
					'select_button_icon[value]!'  => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_content_settings_controls() {
		/**
		 * Content Tab: Settings
		 */
		$this->start_controls_section(
			'section_settings',
			[
				'label'                 => esc_html__( 'Settings', 'powerpack' ),
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'                 => __( 'Height', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', 'vh' ],
				'range'                 => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-container' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%' ],
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-back, {{WRAPPER}} .pp-flipbox-front' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'flip_effect',
			[
				'label'                 => esc_html__( 'Flip Effect', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'flip',
				'label_block'           => false,
				'options'               => [
					'flip'     => esc_html__( 'Flip', 'powerpack' ),
					'slide'    => esc_html__( 'Slide', 'powerpack' ),
					'push'     => esc_html__( 'Push', 'powerpack' ),
					'zoom-in'  => esc_html__( 'Zoom In', 'powerpack' ),
					'zoom-out' => esc_html__( 'Zoom Out', 'powerpack' ),
					'fade'     => esc_html__( 'Fade', 'powerpack' ),
				],
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'flip_direction',
			[
				'label'                 => esc_html__( 'Flip Direction', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'left',
				'label_block'           => false,
				'options'               => [
					'left'     => esc_html__( 'Left', 'powerpack' ),
					'right'    => esc_html__( 'Right', 'powerpack' ),
					'up'       => esc_html__( 'Top', 'powerpack' ),
					'down'     => esc_html__( 'Bottom', 'powerpack' ),
				],
				'condition'             => [
					'flip_effect!' => [
						'fade',
						'zoom-in',
						'zoom-out',
					],
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links( 'Flipbox' );

		if ( ! empty( $help_docs ) ) {

			/**
			 * Content Tab: Help Docs
			 *
			 * @since 1.4.8
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

	protected function register_style_front_controls() {
		/**
		 * Style Tab: Front
		 */
		$this->start_controls_section(
			'section_front_style',
			[
				'label'                 => esc_html__( 'Front', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'padding_front',
			[
				'label'                 => esc_html__( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-front .pp-flipbox-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_alignment_front',
			[
				'label'                 => esc_html__( 'Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'left' => [
						'title'   => esc_html__( 'Left', 'powerpack' ),
						'icon'    => 'eicon-text-align-left',
					],
					'center' => [
						'title'   => esc_html__( 'Center', 'powerpack' ),
						'icon'    => 'eicon-text-align-center',
					],
					'right' => [
						'title'   => esc_html__( 'Right', 'powerpack' ),
						'icon'    => 'eicon-text-align-right',
					],
				],
				'default'               => 'center',
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-front .pp-flipbox-overlay' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_position_front',
			[
				'label'                 => __( 'Vertical Position', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top' => [
						'title' => __( 'Top', 'powerpack' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'powerpack' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'powerpack' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary'  => [
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-front .pp-flipbox-overlay' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'background_front',
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .pp-flipbox-front',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'border_front',
				'label'                 => esc_html__( 'Border Style', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-flipbox-front',
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'overlay_front',
			[
				'label'                 => esc_html__( 'Overlay', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'overlay_front',
				'types'                 => [ 'classic', 'gradient' ],
				'exclude'               => [ 'image' ],
				'selector'              => '{{WRAPPER}} .pp-flipbox-front .pp-flipbox-overlay',
			]
		);

		$this->add_control(
			'image_style_heading_front',
			[
				'label'                 => esc_html__( 'Image', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing_front',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-icon-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'image_size_front',
			[
				'label'                 => esc_html__( 'Size (%)', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-icon-image > img' => 'width: {{SIZE}}%;',
				],
				'condition'             => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'icon_style_heading_front',
			[
				'label'                 => esc_html__( 'Icon', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'icon_type' => [ 'icon', 'text' ],
				],
			]
		);

		$this->add_control(
			'icon_color_front',
			[
				'label'                 => esc_html__( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#ffffff',
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-icon-image, {{WRAPPER}} .pp-flipbox-icon-image i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-flipbox-icon-image svg' => 'fill: {{VALUE}};',
				],
				'condition'             => [
					'icon_type' => [ 'icon', 'text' ],
				],
			]
		);

		$this->add_responsive_control(
			'icon_size_front',
			[
				'label'                 => __( 'Icon Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 40,
					'unit' => 'px',
				],
				'range'                 => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-icon-image, {{WRAPPER}} .pp-flipbox-icon-image i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type' => 'icon',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'icon_typography_front',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-flipbox-icon-image .pp-icon-text',
				'condition' => array(
					'icon_type' => 'text',
				),
			)
		);

		$this->add_responsive_control(
			'icon_spacing_front',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-icon-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type' => [ 'icon', 'text' ],
				],
			]
		);

		$this->add_control(
			'title_heading_front',
			[
				'label'                 => esc_html__( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'title_front!' => '',
				],
			]
		);

		$this->add_control(
			'title_color_front',
			[
				'label'                 => esc_html__( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#fff',
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-front .pp-flipbox-heading' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'title_front!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'title_typography_front',
				'selector'              => '{{WRAPPER}} .pp-flipbox-front .pp-flipbox-heading',
				'condition'             => [
					'title_front!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing_front',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-front .pp-flipbox-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'title_front!' => '',
				],
			]
		);

		$this->add_control(
			'description_heading_front',
			[
				'label'                 => esc_html__( 'Description', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'description_front!' => '',
				],
			]
		);

		$this->add_control(
			'description_color_front',
			[
				'label'                 => esc_html__( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#fff',
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-front .pp-flipbox-content' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'description_front!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'description_typography_front',
				'selector'              => '{{WRAPPER}} .pp-flipbox-front .pp-flipbox-content',
				'condition'             => [
					'description_front!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_back_controls() {
		/**
		 * Style Tab: Back
		 */
		$this->start_controls_section(
			'section_back_style',
			[
				'label'                 => esc_html__( 'Back', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'padding_back',
			[
				'label'                 => esc_html__( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-back .pp-flipbox-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_alignment_back',
			[
				'label'                 => esc_html__( 'Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'left' => [
						'title'   => esc_html__( 'Left', 'powerpack' ),
						'icon'    => 'eicon-text-align-left',
					],
					'center' => [
						'title'   => esc_html__( 'Center', 'powerpack' ),
						'icon'    => 'eicon-text-align-center',
					],
					'right' => [
						'title'   => esc_html__( 'Right', 'powerpack' ),
						'icon'    => 'eicon-text-align-right',
					],
				],
				'default'               => 'center',
				'selectors' => [
					'{{WRAPPER}} .pp-flipbox-back .pp-flipbox-overlay' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_position_back',
			[
				'label'                 => __( 'Vertical Position', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top' => [
						'title' => __( 'Top', 'powerpack' ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'powerpack' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'powerpack' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary'  => [
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-back .pp-flipbox-overlay' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'background_back',
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .pp-flipbox-back',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'border_back',
				'label'                 => esc_html__( 'Border Style', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-flipbox-back',
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'overlay_back',
			[
				'label'                 => esc_html__( 'Overlay', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'overlay_back',
				'types'                 => [ 'classic', 'gradient' ],
				'exclude'               => [ 'image' ],
				'selector'              => '{{WRAPPER}} .pp-flipbox-back .pp-flipbox-overlay',
			]
		);

		$this->add_control(
			'image_style_heading_back',
			[
				'label'                 => esc_html__( 'Image', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'icon_type_back'    => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing_back',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-icon-image-back' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type_back'    => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'image_size_back',
			[
				'label'                 => esc_html__( 'Size (%)', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-icon-image-back > img' => 'width: {{SIZE}}%;',
				],
				'condition'             => [
					'icon_type_back'    => 'image',
				],
			]
		);

		$this->add_control(
			'icon_style_heading_back',
			[
				'label'                 => esc_html__( 'Icon', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'icon_type_back'    => [ 'icon', 'text' ],
				],
			]
		);

		$this->add_control(
			'icon_color_back',
			[
				'label'                 => esc_html__( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#ffffff',
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-icon-image-back, {{WRAPPER}} .pp-flipbox-icon-image-back i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-flipbox-icon-image-back svg' => 'fill: {{VALUE}};',
				],
				'condition'             => [
					'icon_type_back'    => [ 'icon', 'text' ],
				],
			]
		);

		$this->add_responsive_control(
			'icon_size_back',
			[
				'label'                 => __( 'Icon Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 40,
					'unit' => 'px',
				],
				'range'                 => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-icon-image-back, {{WRAPPER}} .pp-flipbox-icon-image-back i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type_back'    => 'icon',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'icon_typography_back',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-flipbox-icon-image-back .pp-icon-text',
				'condition' => array(
					'icon_type' => 'text',
				),
			)
		);

		$this->add_responsive_control(
			'icon_spacing_back',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-icon-image-back' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_type_back'    => [ 'icon', 'text' ],
				],
			]
		);

		$this->add_control(
			'title_heading_back',
			[
				'label'                 => esc_html__( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'title_back!' => '',
				],
			]
		);

		$this->add_control(
			'title_color_back',
			[
				'label'                 => esc_html__( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#fff',
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-back .pp-flipbox-heading' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'title_back!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'title_typography_back',
				'selector'              => '{{WRAPPER}} .pp-flipbox-back .pp-flipbox-heading',
				'condition'             => [
					'title_back!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing_back',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-back .pp-flipbox-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'title_back!' => '',
				],
			]
		);

		$this->add_control(
			'description_heading_back',
			[
				'label'                 => esc_html__( 'Description', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'description_back!' => '',
				],
			]
		);

		$this->add_control(
			'description_color_back',
			[
				'label'                 => esc_html__( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-back .pp-flipbox-content' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'description_back!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'description_typography_back',
				'selector'              => '{{WRAPPER}} .pp-flipbox-back .pp-flipbox-content',
				'condition'             => [
					'description_back!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_button_controls() {
		/**
		 * Style Tab: Button
		 * ------------------
		 */
		$this->start_controls_section(
			'section_info_box_button_style',
			[
				'label'                 => __( 'Button', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'md',
				'options'               => [
					'xs' => __( 'Extra Small', 'powerpack' ),
					'sm' => __( 'Small', 'powerpack' ),
					'md' => __( 'Medium', 'powerpack' ),
					'lg' => __( 'Large', 'powerpack' ),
					'xl' => __( 'Extra Large', 'powerpack' ),
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'button_spacing',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 15,
				],
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-button' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_bg_color_normal',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-button' => 'background-color: {{VALUE}}',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_text_color_normal',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-button' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-flipbox-button .pp-button-icon svg' => 'fill: {{VALUE}}',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'button_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-flipbox-button',
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'button_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} .pp-flipbox-button',
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-flipbox-button',
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'info_box_button_icon_heading',
			[
				'label'                 => __( 'Button Icon', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'link_type'    => 'button',
					'select_button_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'button_icon_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'       => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'condition'             => [
					'link_type'    => 'button',
					'select_button_icon[value]!' => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-info-box .pp-button-icon' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-button:hover' => 'background-color: {{VALUE}}',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-button:hover' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-flipbox-button:hover' => 'border-color: {{VALUE}}',
				],
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_control(
			'button_animation',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::HOVER_ANIMATION,
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-flipbox-button:hover',
				'condition'             => [
					'link_type'    => 'button',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render_front() {
		$settings = $this->get_settings_for_display();

		$title_tag_front = PP_Helper::validate_html_tag( $settings['title_html_tag_front'] );

		$this->add_render_attribute( 'icon-front', 'class', 'pp-flipbox-icon-image' );

		if ( 'icon' === $settings['icon_type'] ) {
			$this->add_render_attribute( 'icon-front', 'class', 'pp-icon' );
		}

		if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['icon'] = 'fa fa-star';
		}

		$has_icon = ! empty( $settings['icon'] );

		if ( $has_icon ) {
			$this->add_render_attribute( 'front-i', 'class', $settings['icon'] );
			$this->add_render_attribute( 'front-i', 'aria-hidden', 'true' );
		}

		if ( ! $has_icon && ! empty( $settings['select_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_icon'] );
		$is_new = ! isset( $settings['icon'] ) && Icons_Manager::is_migration_allowed();
		?>
		<div class="pp-flipbox-front">
			<div class="pp-flipbox-overlay">
				<div class="pp-flipbox-inner">
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-front' ) ); ?>>
						<?php if ( 'icon' === $settings['icon_type'] && $has_icon ) { ?>
							<?php
							if ( $is_new || $migrated ) {
								Icons_Manager::render_icon( $settings['select_icon'], [ 'aria-hidden' => 'true' ] );
							} elseif ( ! empty( $settings['icon'] ) ) {
								?><i <?php echo wp_kses_post( $this->get_render_attribute_string( 'front-i' ) ); ?>></i><?php
							}
							?>
						<?php } elseif ( 'image' === $settings['icon_type'] ) { ?>
							<?php
								$flipbox_image = $settings['icon_image'];
								$flipbox_image_url = Group_Control_Image_Size::get_attachment_image_src( $flipbox_image['id'], 'thumbnail', $settings );
								$flipbox_image_url = ( empty( $flipbox_image_url ) ) ? $flipbox_image['url'] : $flipbox_image_url;
							?>
							<?php if ( $flipbox_image_url ) { ?>
								<img src="<?php echo esc_url( $flipbox_image_url ); ?>" alt="">
							<?php } ?>
						<?php } elseif ( 'text' === $settings['icon_type'] ) { ?>
							<span class="pp-icon-text">
								<?php echo wp_kses_post( $settings['icon_text'] ); ?>
							</span>
						<?php } ?>
					</div>

					<<?php echo esc_html( $title_tag_front ); ?> class="pp-flipbox-heading">
						<?php echo wp_kses_post( $settings['title_front'], 'powerpack' ); ?>
					</<?php echo esc_html( $title_tag_front ); ?>>

					<div class="pp-flipbox-content">
						<?php echo wp_kses_post( $settings['description_front'], 'powerpack' ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_back() {
		$settings = $this->get_settings_for_display();

		$title_tag = PP_Helper::validate_html_tag( $settings['title_html_tag_back'] );

		$this->add_render_attribute( 'title-container', 'class', 'pp-flipbox-heading' );

		if ( 'none' !== $settings['icon_type_back'] ) {

			$this->add_render_attribute( 'icon-back', 'class', 'pp-flipbox-icon-image-back' );

			if ( ! isset( $settings['icon_back'] ) && ! Icons_Manager::is_migration_allowed() ) {
				// add old default
				$settings['icon'] = 'fa fa-snowflake-o';
			}

			$has_icon_back = ! empty( $settings['icon_back'] );

			if ( $has_icon_back ) {
				$this->add_render_attribute( 'back-i', 'class', $settings['icon_back'] );
				$this->add_render_attribute( 'back-i', 'aria-hidden', 'true' );
			}

			if ( ! $has_icon_back && ! empty( $settings['select_icon_back']['value'] ) ) {
				$has_icon_back = true;
			}
			$migrated_icon_back = isset( $settings['__fa4_migrated']['select_icon_back'] );
			$is_new_icon_back = ! isset( $settings['icon_back'] ) && Icons_Manager::is_migration_allowed();

			if ( 'image' === $settings['icon_type_back'] ) {
				$flipbox_image_back = $settings['icon_image_back'];
				$flipbox_back_image_url = Group_Control_Image_Size::get_attachment_image_src( $flipbox_image_back['id'], 'thumbnail_back', $settings );
				$flipbox_back_image_url = ( empty( $flipbox_back_image_url ) ) ? $flipbox_image_back['url'] : $flipbox_back_image_url;

				$this->add_render_attribute(
					'icon-image-back',
					[
						'src'   => $flipbox_back_image_url,
						'alt'   => 'flipbox-image',
					]
				);
			} elseif ( 'icon' === $settings['icon_type_back'] ) {
				$this->add_render_attribute( 'icon-back', 'class', 'pp-icon' );
			}
		}

		if ( 'none' !== $settings['link_type'] ) {
			if ( ! empty( $settings['link']['url'] ) ) {
				if ( 'title' === $settings['link_type'] ) {
					$title_tag = 'a';

					$this->add_render_attribute( 'title-container', 'class', 'pp-flipbox-linked-title' );

					$this->add_link_attributes( 'title-container', $settings['link'] );

				} elseif ( 'button' === $settings['link_type'] ) {

					$this->add_render_attribute( 'button', 'class', [ 'elementor-button', 'pp-flipbox-button', 'elementor-size-' . $settings['button_size'] ] );

					$this->add_link_attributes( 'button', $settings['link'] );

				}
			}
		}
		?>
		<div class="pp-flipbox-back">
			<?php
			if ( 'box' === $settings['link_type'] && $settings['link']['url'] ) {
				$this->add_render_attribute( 'box-link', 'class', 'pp-flipbox-box-link' );

				$this->add_link_attributes( 'box-link', $settings['link'] );
				?>
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'box-link' ) ); ?>></a>
			<?php } ?>
			<div class="pp-flipbox-overlay">
				<div class="pp-flipbox-inner">
					<?php if ( 'none' !== $settings['icon_type_back'] ) { ?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-back' ) ); ?>>
							<?php if ( 'image' === $settings['icon_type_back'] ) { ?>
								<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-image-back' ) ); ?>>
							<?php } elseif ( 'icon' === $settings['icon_type_back'] && $has_icon_back ) { ?>
								<?php
								if ( $is_new_icon_back || $migrated_icon_back ) {
									Icons_Manager::render_icon( $settings['select_icon_back'], [ 'aria-hidden' => 'true' ] );
								} elseif ( ! empty( $settings['icon_back'] ) ) {
									?><i <?php echo wp_kses_post( $this->get_render_attribute_string( 'back-i' ) ); ?>></i><?php
								}
								?>
							<?php } elseif ( 'text' === $settings['icon_type_back'] ) { ?>
								<span class="pp-icon-text">
									<?php echo wp_kses_post( $settings['icon_text_back'] ); ?>
								</span>
							<?php } ?>
						</div>
					<?php } ?>

					<?php if ( $settings['title_back'] ) { ?>
						<<?php echo esc_html( $title_tag ),' ', wp_kses_post( $this->get_render_attribute_string( 'title-container' ) ); ?>>
							<?php echo wp_kses_post( $settings['title_back'], 'powerpack' ); ?>
						</<?php echo esc_html( $title_tag ); ?>>
					<?php } ?>

					<div class="pp-flipbox-content">
						<?php echo wp_kses_post( $settings['description_back'], 'powerpack' ); ?>
					</div>

					<?php if ( 'button' === $settings['link_type'] && ! empty( $settings['flipbox_button_text'] ) ) : ?>
						<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'button' ) ); ?>>
							<?php if ( 'before' === $settings['button_icon_position'] ) : ?>
								<?php $this->render_button_icon(); ?>
							<?php endif; ?>
							<?php echo esc_attr( $settings['flipbox_button_text'] ); ?>
							<?php if ( 'after' === $settings['button_icon_position'] ) : ?>
								<?php $this->render_button_icon(); ?>
							<?php endif; ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_button_icon() {
		$settings = $this->get_settings_for_display();

		if ( ! isset( $settings['button_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['icon'] = '';
		}

		$has_button_icon = ! empty( $settings['button_icon'] );

		if ( $has_button_icon ) {
			$this->add_render_attribute( 'button-i', 'class', $settings['button_icon'] );
			$this->add_render_attribute( 'button-i', 'aria-hidden', 'true' );
		}

		if ( ! $has_button_icon && ! empty( $settings['select_button_icon']['value'] ) ) {
			$has_button_icon = true;
		}

		$migrated_button_icon = isset( $settings['__fa4_migrated']['select_button_icon'] );
		$is_new_button_icon = ! isset( $settings['button_icon'] ) && Icons_Manager::is_migration_allowed();

		if ( $has_button_icon ) { ?>
			<span class="pp-button-icon">
				<?php
				if ( $is_new_button_icon || $migrated_button_icon ) {
					Icons_Manager::render_icon( $settings['select_button_icon'], [ 'aria-hidden' => 'true' ] );
				} elseif ( ! empty( $settings['button_icon'] ) ) {
					?><i <?php echo wp_kses_post( $this->get_render_attribute_string( 'button-i' ) ); ?>></i><?php
				}
				?>
			</span>
			<?php
		}
	}

	/**
	 * Render flipbox widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$flipbox_if_html_tag = 'div';

		$this->add_render_attribute(
			[
				'flipbox-card' => [
					'class' => [
						'pp-flipbox-flip-card',
					],
				],
				'flipbox-container' => [
					'class' => [
						'pp-flipbox-container',
						'pp-animate-' . esc_attr( $settings['flip_effect'] ),
						'pp-direction-' . esc_attr( $settings['flip_direction'] ),
					],
				],
			]
		);
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'flipbox-container' ) ); ?>>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'flipbox-card' ) ); ?>>
				<?php
					// Front
					$this->render_front();

					// Back
					$this->render_back();
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render flipbox widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.4.1
	 * @access protected
	 */
	protected function content_template() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		?>
		<#
			view.addRenderAttribute( 'flipbox-card', {
				'class': [
					'pp-flipbox-flip-card'
				],
			} );

			view.addRenderAttribute( 'flipbox-container', {
				'class': [
					'pp-flipbox-container',
					'pp-animate-' + settings.flip_effect,
					'pp-direction-' + settings.flip_direction
				],
			} );

			function render_button_icon() {
				var buttonIconHTML = elementor.helpers.renderIcon( view, settings.select_button_icon, { 'aria-hidden': true }, 'i' , 'object' ),
					buttonMigrated = elementor.helpers.isIconMigrated( settings, 'select_button_icon' );

				if ( settings.button_icon || settings.select_button_icon ) { #>
					<span class="pp-button-icon">
					<#
					if ( buttonIconHTML && buttonIconHTML.rendered && ( ! settings.button_icon || buttonMigrated ) ) { #>
						{{{ buttonIconHTML.value }}}
					<# } else if ( settings.button_icon ) { #>
						<i class="{{ settings.button_icon }}" aria-hidden="true"></i>
					<# } #>
					</span>
					<#
				}
			}

			function render_front() {
				view.addRenderAttribute( 'icon-front', 'class', 'pp-flipbox-icon-image' );

				var iconHTML = elementor.helpers.renderIcon( view, settings.select_icon, { 'aria-hidden': true }, 'i' , 'object' ),
					migrated = elementor.helpers.isIconMigrated( settings, 'select_icon' );

				if ( 'icon' === settings.icon_type ) {
					view.addRenderAttribute( 'icon-front', 'class', 'pp-icon' );
				}
				#>
				<div class="pp-flipbox-front">
					<div class="pp-flipbox-overlay">
						<div class="pp-flipbox-inner">
							<div {{{ view.getRenderAttributeString( 'icon-front' ) }}}>
								<#
								if ( 'icon' === settings.icon_type ) {
									if ( settings.icon || settings.select_icon ) {
										if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
											{{{ iconHTML.value }}}
										<# } else { #>
											<i class="{{ settings.icon }}" aria-hidden="true"></i>
										<# }
									}
								} else if ( 'image' === settings.icon_type ) {
									var flipbox_image = {
										id: settings.icon_image.id,
										url: settings.icon_image.url,
										size: settings.thumbnail_size,
										dimension: settings.thumbnail_custom_dimension,
										model: view.getEditModel()
									};
									var flipbox_image_url = elementor.imagesManager.getImageUrl( flipbox_image );

									if ( flipbox_image_url ) { #>
										<img src="{{{ flipbox_image_url }}}" />
									<# }
								} else if ( 'text' === settings.icon_type ) { #>
									<span class="pp-icon-text">
										{{{ settings.icon_text }}}
									</span>
								<# } #>
							</div>

							<{{ settings.title_html_tag_front }} class="pp-flipbox-heading">
								{{{ settings.title_front }}}
							</{{ settings.title_html_tag_front }}>

							<div class="pp-flipbox-content">
								{{{ settings.description_front }}}
							</div>
						</div>
					</div>
				</div>
				<#
			}

			function render_back() {
				var pp_title_html_tag = settings.title_html_tag_back;

				view.addRenderAttribute( 'title-container', 'class', 'pp-flipbox-heading' );

				view.addRenderAttribute( 'icon-back', 'class', 'pp-flipbox-icon-image' );

				var iconHTML = elementor.helpers.renderIcon( view, settings.select_icon_back, { 'aria-hidden': true }, 'i' , 'object' ),
					migrated = elementor.helpers.isIconMigrated( settings, 'select_icon_back' );

				if ( 'icon' === settings.icon_type_back ) {
					view.addRenderAttribute( 'icon-back', 'class', 'pp-icon' );
				}

				if ( 'none' !== settings.link_type ) {
					if ( settings.link.url ) {
						if ( 'title' === settings.link_type ) {

							var pp_title_html_tag = 'a';

							view.addRenderAttribute( 'title-container', 'class', 'pp-flipbox-linked-title' );
							view.addRenderAttribute( 'title-container', 'href', settings.link.url );

						} else if ( 'button' === settings.link_type ) {

							view.addRenderAttribute( 'button', {
								'class': [
									'elementor-button',
									'pp-flipbox-button',
									'elementor-size-' + settings.button_size,
								],
								'href': [
									settings.link.url,
								],
							} );

						}
					}
				}
				#>
				<div class="pp-flipbox-back">
					<#
					if ( 'box' === settings.link_type && settings.link.url ) {
						view.addRenderAttribute( 'box-link', 'class', 'pp-flipbox-box-link' );
						view.addRenderAttribute( 'box-link', 'href', settings.link.url );
						#>
						<a <{{{ view.getRenderAttributeString( 'box-link' ) }}}></a>
					<# } #>
					<div class="pp-flipbox-overlay">
						<div class="pp-flipbox-inner">
							<# if ( 'none' !== settings.icon_type_back ) { #>
								<div {{{ view.getRenderAttributeString( 'icon-back' ) }}}>
									<#
									if ( 'icon' === settings.icon_type_back ) {
										if ( settings.icon_back || settings.select_icon_back ) {
											if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
												{{{ iconHTML.value }}}
											<# } else { #>
												<i class="{{ settings.icon_back }}" aria-hidden="true"></i>
											<# }
										}
									} else if ( 'image' === settings.icon_type_back ) {
										var flipbox_image = {
											id: settings.icon_image_back.id,
											url: settings.icon_image_back.url,
											size: settings.thumbnail_back_size,
											dimension: settings.thumbnail_back_custom_dimension,
											model: view.getEditModel()
										};
										var flipbox_image_url = elementor.imagesManager.getImageUrl( flipbox_image );

										if ( flipbox_image_url ) { #>
											<img src="{{{ flipbox_image_url }}}" />
										<# }
									} else if ( 'text' === settings.icon_type_back ) { #>
										<span class="pp-icon-text">
											{{{ settings.icon_text_back }}}
										</span>
									<# } #>
								</div>
							<# } #>

							<{{ pp_title_html_tag }} {{{ view.getRenderAttributeString( 'title-container' ) }}}>
								{{{ settings.title_back }}}
							</{{ pp_title_html_tag }}>

							<div class="pp-flipbox-content">
								{{{ settings.description_back }}}
							</div>

							<# if ( 'button' === settings.link_type && '' != settings.flipbox_button_text ) { #>
								<a {{{ view.getRenderAttributeString( 'button' ) }}}>
									<#
									if ( 'before' === settings.button_icon_position ) {
										render_button_icon();
									}
									#>

									{{{ settings.flipbox_button_text }}}

									<#
									if ( 'after' === settings.button_icon_position ) {
										render_button_icon();
									}
									#>
								</a>
							<# } #>
						</div>
					</div>
				</div>
				<#
			}
		#>
		<div {{{ view.getRenderAttributeString( 'flipbox-container' ) }}}>
			<div {{{ view.getRenderAttributeString( 'flipbox-card' ) }}}>
				<#
					render_front();

					render_back();
				#>
			</div>
		</div>
		<?php
	}
}
