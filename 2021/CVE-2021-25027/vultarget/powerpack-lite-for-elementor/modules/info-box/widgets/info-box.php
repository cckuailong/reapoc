<?php
/**
 * Info Box Widget
 *
 * @package PPE
 */

namespace PowerpackElementsLite\Modules\InfoBox\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Helper;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Info Box Widget
 */
class Info_Box extends Powerpack_Widget {

	/**
	 * Retrieve info box widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Info_Box' );
	}

	/**
	 * Retrieve info box widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Info_Box' );
	}

	/**
	 * Retrieve info box widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Info_Box' );
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
		return parent::get_widget_keywords( 'Info_Box' );
	}

	/**
	 * Register info box widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register info box widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.0.3
	 * @access protected
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_icon_controls();
		$this->register_content_content_controls();
		$this->register_content_link_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_info_box_controls();
		$this->register_style_icon_controls();
		$this->register_style_content_controls();
		$this->register_style_button_controls();
	}

	/**
	 * Register Icon Controls in Content tab
	 *
	 * @return void
	 */
	protected function register_content_icon_controls() {
		/**
		 * Content Tab: Icon
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_icon',
			array(
				'label' => __( 'Icon', 'powerpack' ),
			)
		);

		$this->add_control(
			'icon_type',
			array(
				'label'       => esc_html__( 'Type', 'powerpack' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'none'  => array(
						'title' => esc_html__( 'None', 'powerpack' ),
						'icon'  => 'eicon-ban',
					),
					'icon'  => array(
						'title' => esc_html__( 'Icon', 'powerpack' ),
						'icon'  => 'eicon-star',
					),
					'image' => array(
						'title' => esc_html__( 'Image', 'powerpack' ),
						'icon'  => 'eicon-image-bold',
					),
					'text'  => array(
						'title' => esc_html__( 'Text', 'powerpack' ),
						'icon'  => 'eicon-font',
					),
				),
				'default'     => 'icon',
			)
		);

		$this->add_control(
			'selected_icon',
			array(
				'label'            => __( 'Icon', 'powerpack' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'icon_type' => 'icon',
				),
			)
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
			'image',
			array(
				'label'     => __( 'Image', 'powerpack' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array(
					'active' => true,
				),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'icon_type' => 'image',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default'   => 'full',
				'separator' => 'none',
				'condition' => array(
					'icon_type' => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => __( 'Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 30,
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min'  => 5,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', 'rem' ),
				'condition'  => array(
					'icon_type' => 'icon',
				),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'icon_rotation',
			array(
				'label'      => __( 'Rotate', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 360,
						'step' => 1,
					),
				),
				'size_units' => '',
				'condition'  => array(
					'icon_type!' => 'none',
				),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-icon' => 'transform: rotate( {{SIZE}}deg );',
				),
			)
		);

		$this->add_responsive_control(
			'icon_img_width',
			array(
				'label'      => __( 'Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => '',
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min'  => 25,
						'max'  => 600,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 25,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}}.pp-info-box-top .pp-info-box-icon img, {{WRAPPER}}.pp-info-box-left .pp-info-box-icon-wrap, {{WRAPPER}}.pp-info-box-right .pp-info-box-icon-wrap' => 'width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'icon_type' => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'icon_position',
			array(
				'label'              => __( 'Icon', 'powerpack' ) . ' ' . __( 'Position', 'powerpack' ),
				'type'               => Controls_Manager::CHOOSE,
				'default'            => 'top',
				'options'            => array(
					'left'  => array(
						'title' => __( 'Icon on Left', 'powerpack' ),
						'icon'  => 'eicon-h-align-left',
					),
					'top'   => array(
						'title' => __( 'Icon on Top', 'powerpack' ),
						'icon'  => 'eicon-v-align-top',
					),
					'right' => array(
						'title' => __( 'Icon on Right', 'powerpack' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'condition'          => array(
					'icon_type!' => 'none',
				),
				'prefix_class'       => 'pp-info-box%s-',
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'icon_vertical_position',
			array(
				'label'                => __( 'Vertical Align', 'powerpack' ),
				'description'          => __( 'Works in case of left and right icon position', 'powerpack' ),
				'type'                 => Controls_Manager::CHOOSE,
				'default'              => 'top',
				'options'              => array(
					'top'    => array(
						'title' => __( 'Top', 'powerpack' ),
						'icon'  => 'eicon-v-align-top',
					),
					'middle' => array(
						'title' => __( 'Middle', 'powerpack' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'powerpack' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors'            => array(
					'(desktop){{WRAPPER}}.pp-info-box-left .pp-info-box'        => 'align-items: {{VALUE}};',
					'(desktop){{WRAPPER}}.pp-info-box-right .pp-info-box'       => 'align-items: {{VALUE}};',
					'(tablet){{WRAPPER}}.pp-info-box-tablet-left .pp-info-box'  => 'align-items: {{VALUE}};',
					'(tablet){{WRAPPER}}.pp-info-box-tablet-right .pp-info-box' => 'align-items: {{VALUE}};',
					'(mobile){{WRAPPER}}.pp-info-box-mobile-left .pp-info-box'  => 'align-items: {{VALUE}};',
					'(mobile){{WRAPPER}}.pp-info-box-mobile-right .pp-info-box' => 'align-items: {{VALUE}};',
				),
				'selectors_dictionary' => array(
					'top'    => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				),
				'condition'            => array(
					'icon_type' => array( 'icon', 'image', 'text' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Info Box Content Controls in Content tab
	 *
	 * @return void
	 */
	protected function register_content_content_controls() {
		/**
		 * Content Tab: Content
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'powerpack' ),
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Title', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'Title', 'powerpack' ),
			)
		);

		$this->add_control(
			'sub_heading',
			array(
				'label'   => __( 'Subtitle', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'Subtitle', 'powerpack' ),
			)
		);

		$this->add_control(
			'description',
			array(
				'label'   => __( 'Description', 'powerpack' ),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'Enter info box description', 'powerpack' ),
			)
		);

		$this->add_control(
			'divider_title_switch',
			array(
				'label'        => __( 'Title Separator', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'On', 'powerpack' ),
				'label_off'    => __( 'Off', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'title_html_tag',
			array(
				'label'   => __( 'Title HTML Tag', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
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
			'sub_title_html_tag',
			array(
				'label'     => __( 'Subtitle HTML Tag', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h5',
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
					'sub_heading!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Link Controls in Content tab
	 *
	 * @return void
	 */
	protected function register_content_link_controls() {
		/**
		 * Content Tab: Link
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_link',
			array(
				'label' => __( 'Link', 'powerpack' ),
			)
		);

		$this->add_control(
			'link_type',
			array(
				'label'   => __( 'Link Type', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none'   => __( 'None', 'powerpack' ),
					'box'    => __( 'Box', 'powerpack' ),
					'icon'   => __( 'Image/Icon', 'powerpack' ),
					'title'  => __( 'Title', 'powerpack' ),
					'button' => __( 'Button', 'powerpack' ),
				),
			)
		);

		$this->add_control(
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
					'url' => '#',
				),
				'condition'   => array(
					'link_type!' => 'none',
				),
			)
		);

		$this->add_control(
			'button_visible',
			array(
				'label'        => __( 'Show Button', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'link_type' => 'box',
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'      => __( 'Button', 'powerpack' ) . ' ' . __( 'Text', 'powerpack' ),
				'type'       => Controls_Manager::TEXT,
				'dynamic'    => array(
					'active' => true,
				),
				'default'    => __( 'Get Started', 'powerpack' ),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name'     => 'link_type',
							'operator' => '==',
							'value'    => 'button',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name'     => 'link_type',
									'operator' => '==',
									'value'    => 'box',
								),
								array(
									'name'     => 'button_visible',
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
			'select_button_icon',
			array(
				'label'            => __( 'Button', 'powerpack' ) . ' ' . __( 'Icon', 'powerpack' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'button_icon',
				'conditions'       => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name'     => 'link_type',
							'operator' => '==',
							'value'    => 'button',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name'     => 'link_type',
									'operator' => '==',
									'value'    => 'box',
								),
								array(
									'name'     => 'button_visible',
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
			'button_icon_position',
			array(
				'label'     => __( 'Icon Position', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => array(
					'after'  => __( 'After', 'powerpack' ),
					'before' => __( 'Before', 'powerpack' ),
				),
				'conditions'       => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name'     => 'link_type',
									'operator' => '==',
									'value'    => 'button',
								),
								array(
									'name'     => 'select_button_icon[value]',
									'operator' => '!=',
									'value'    => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name'     => 'link_type',
									'operator' => '==',
									'value'    => 'box',
								),
								array(
									'name'     => 'button_visible',
									'operator' => '==',
									'value'    => 'yes',
								),
								array(
									'name'     => 'select_button_icon[value]',
									'operator' => '!=',
									'value'    => '',
								),
							),
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Help Docs Controls in Content tab
	 *
	 * @return void
	 */
	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links( 'Info_Box' );

		if ( ! empty( $help_docs ) ) {

			/**
			 * Content Tab: Help Docs
			 *
			 * @since 1.4.8
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
	 * Register Info Box Controls in Style tab
	 *
	 * @return void
	 */
	protected function register_style_info_box_controls() {
		/**
		 * Style Tab: Info Box
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_box_style',
			array(
				'label' => __( 'Box', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'info_box_min_height',
			array(
				'label'      => __( 'Min Height', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 50,
						'max'  => 1000,
						'step' => 1,
					),
					'vh' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'vh' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-container' => 'min-height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'info_box_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_info_box_style' );

		$this->start_controls_tab(
			'tab_info_box_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'info_box_bg',
				'label'    => __( 'Background', 'powerpack' ),
				'types'    => array( 'none', 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .pp-info-box-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'info_box_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-info-box-container',
			)
		);

		$this->add_control(
			'info_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'info_box_shadow',
				'selector' => '{{WRAPPER}} .pp-info-box-container',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_info_box_hover',
			array(
				'label' => __( 'Hover', 'powerpack' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'info_box_bg_hover',
				'label'    => __( 'Background', 'powerpack' ),
				'types'    => array( 'none', 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .pp-info-box-container:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'info_box_border_hover',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-info-box-container:hover',
			)
		);

		$this->add_control(
			'info_box_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-container:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'info_box_shadow_hover',
				'selector' => '{{WRAPPER}} .pp-info-box-container:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Icon Controls in Style tab
	 *
	 * @return void
	 */
	protected function register_style_icon_controls() {
		/**
		 * Style Tab: Icon
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_box_icon_style',
			array(
				'label'     => __( 'Icon', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'icon_type!' => 'none',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'icon_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'condition' => array(
					'icon_type' => 'text',
				),
				'selector'  => '{{WRAPPER}} .pp-info-box-icon',
			)
		);

		$this->start_controls_tabs( 'tabs_icon_style' );

		$this->start_controls_tab(
			'tab_icon_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_control(
			'icon_color_normal',
			array(
				'label'     => __( 'Icon Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-icon'     => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-info-box-icon svg' => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'icon_type!' => 'image',
				),
			)
		);

		$this->add_control(
			'icon_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-icon' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'icon_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'condition'   => array(
					'icon_type!' => 'none',
				),
				'selector'    => '{{WRAPPER}} .pp-info-box-icon',
			)
		);

		$this->add_control(
			'icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'condition'  => array(
					'icon_type!' => 'none',
				),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-icon, {{WRAPPER}} .pp-info-box-icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label'       => __( 'Margin', 'powerpack' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array( 'px', '%' ),
				'selectors'   => array(
					'{{WRAPPER}} .pp-info-box-icon-wrap' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_icon_hover',
			array(
				'label' => __( 'Hover', 'powerpack' ),
			)
		);

		$this->add_control(
			'icon_color_hover',
			array(
				'label'     => __( 'Icon Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-icon:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-info-box-icon:hover svg' => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'icon_type!' => 'image',
				),
			)
		);

		$this->add_control(
			'icon_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'condition' => array(
					'icon_type!' => 'none',
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-icon:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'icon_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'condition' => array(
					'icon_type!' => 'none',
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-icon:hover .fa' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'hover_animation_icon',
			array(
				'label' => __( 'Icon Animation', 'powerpack' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Title Controls in Style tab
	 *
	 * @return void
	 */
	protected function register_style_content_controls() {
		/**
		 * Style Tab: Title
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_box_content_style',
			array(
				'label' => __( 'Content', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justified', 'powerpack' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_style_heading',
			array(
				'label'     => __( 'Title', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'heading!' => '',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-title' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'heading!' => '',
				),
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label'     => __( 'Hover Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-container:hover .pp-info-box-title' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'heading!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'title_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .pp-info-box-title',
				'condition' => array(
					'heading!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'title_text_shadow',
				'label'     => __( 'Text Shadow', 'powerpack' ),
				'selector'  => '{{WRAPPER}} .pp-info-box-title',
				'condition' => array(
					'heading!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'subtitle_heading',
			array(
				'label'     => __( 'Sub Title', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'sub_heading!' => '',
				),
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'default'   => '',
				'condition' => array(
					'sub_heading!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-subtitle' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'subtitle_color_hover',
			array(
				'label'     => __( 'Hover Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-container:hover .pp-info-box-subtitle' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'sub_heading!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'subtitle_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_2,
				'condition' => array(
					'sub_heading!' => '',
				),
				'selector'  => '{{WRAPPER}} .pp-info-box-subtitle',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'subtitle_text_shadow',
				'label'     => __( 'Text Shadow', 'powerpack' ),
				'selector'  => '{{WRAPPER}} .pp-info-box-subtitle',
				'condition' => array(
					'sub_heading!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'subtitle_margin',
			array(
				'label'      => __( 'Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', '%' ),
				'condition'  => array(
					'sub_heading!' => '',
				),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'divider_title_style_heading',
			array(
				'label'     => __( 'Title Separator', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'divider_title_switch' => 'yes',
				),
			)
		);

		$this->add_control(
			'divider_title_border_type',
			array(
				'label'     => __( 'Border Type', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'none'   => __( 'None', 'powerpack' ),
					'solid'  => __( 'Solid', 'powerpack' ),
					'double' => __( 'Double', 'powerpack' ),
					'dotted' => __( 'Dotted', 'powerpack' ),
					'dashed' => __( 'Dashed', 'powerpack' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-divider' => 'border-bottom-style: {{VALUE}}',
				),
				'condition' => array(
					'divider_title_switch' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'divider_title_width',
			array(
				'label'      => __( 'Border Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 30,
				),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-divider' => 'width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'divider_title_switch' => 'yes',
					'divider_title_border_type!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'divider_title_border_height',
			array(
				'label'      => __( 'Border Height', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 2,
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
					'{{WRAPPER}} .pp-info-box-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'divider_title_switch' => 'yes',
					'divider_title_border_type!' => 'none',
				),
			)
		);

		$this->add_control(
			'divider_title_border_color',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-divider' => 'border-bottom-color: {{VALUE}}',
				),
				'condition' => array(
					'divider_title_switch' => 'yes',
					'divider_title_border_type!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'divider_title_align',
			array(
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-divider-wrap'   => 'display: flex; justify-content: {{VALUE}};',
				),
				'condition' => array(
					'divider_title_switch' => 'yes',
					'divider_title_border_type!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'divider_title_margin',
			array(
				'label'      => __( 'Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'divider_title_switch' => 'yes',
					'divider_title_border_type!' => 'none',
				),
			)
		);

		$this->add_control(
			'description_style_heading',
			array(
				'label'     => __( 'Description', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'description!' => '',
				),
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-description' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'description!' => '',
				),
			)
		);

		$this->add_control(
			'description_color_hover',
			array(
				'label'     => __( 'Hover Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-container:hover .pp-info-box-description' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'description!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'description_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_3,
				'selector'  => '{{WRAPPER}} .pp-info-box-description',
				'condition' => array(
					'description!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'description_text_shadow',
				'label'     => __( 'Text Shadow', 'powerpack' ),
				'selector'  => '{{WRAPPER}} .pp-info-box-description',
				'condition' => array(
					'description!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'description_margin',
			array(
				'label'      => __( 'Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'description!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Button Controls in Style tab
	 *
	 * @return void
	 */
	protected function register_style_button_controls() {
		/**
		 * Style Tab: Button
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_box_button_style',
			array(
				'label'     => __( 'Button', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'link_type' => 'button',
				),
			)
		);

		$this->add_control(
			'button_size',
			array(
				'label'     => __( 'Size', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'md',
				'options'   => array(
					'xs' => __( 'Extra Small', 'powerpack' ),
					'sm' => __( 'Small', 'powerpack' ),
					'md' => __( 'Medium', 'powerpack' ),
					'lg' => __( 'Large', 'powerpack' ),
					'xl' => __( 'Extra Large', 'powerpack' ),
				),
				'condition' => array(
					'link_type' => 'button',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			array(
				'label'     => __( 'Normal', 'powerpack' ),
				'condition' => array(
					'link_type' => 'button',
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
					'{{WRAPPER}} .pp-info-box-button' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-info-box-button .pp-icon svg' => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'link_type' => 'button',
				),
			)
		);

		$this->add_control(
			'button_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_4,
				],
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-info-box-button' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'link_type' => 'button',
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
				'selector'    => '{{WRAPPER}} .pp-info-box-button',
				'condition'   => array(
					'link_type' => 'button',
				),
			)
		);

		$this->add_control(
			'button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-info-box-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'link_type' => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'button_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-info-box-button',
				'condition' => array(
					'link_type' => 'button',
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
					'{{WRAPPER}} .pp-info-box-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'link_type' => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'button_box_shadow',
				'selector'  => '{{WRAPPER}} .pp-info-box-button',
				'condition' => array(
					'link_type' => 'button',
				),
			)
		);

		$this->add_control(
			'info_box_button_icon_heading',
			array(
				'label'     => __( 'Button Icon', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'link_type'                  => 'button',
					'select_button_icon[value]!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_margin',
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
				'condition'   => array(
					'link_type'                  => 'button',
					'select_button_icon[value]!' => '',
				),
				'selectors'   => array(
					'{{WRAPPER}} .pp-info-box .pp-button-icon' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			array(
				'label'     => __( 'Hover', 'powerpack' ),
				'condition' => array(
					'link_type' => 'button',
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
					'{{WRAPPER}} .pp-info-box-button:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-info-box-button:hover .pp-icon svg' => 'fill: {{VALUE}}',
				),
				'condition' => array(
					'link_type' => 'button',
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
					'{{WRAPPER}} .pp-info-box-button:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'link_type' => 'button',
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
					'{{WRAPPER}} .pp-info-box-button:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'link_type' => 'button',
				),
			)
		);

		$this->add_control(
			'button_animation',
			array(
				'label'     => __( 'Animation', 'powerpack' ),
				'type'      => Controls_Manager::HOVER_ANIMATION,
				'condition' => array(
					'link_type' => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'button_box_shadow_hover',
				'selector'  => '{{WRAPPER}} .pp-info-box-button:hover',
				'condition' => array(
					'link_type' => 'button',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render info box icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_infobox_icon() {
		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'icon_text', 'none' );
		$this->add_render_attribute( 'icon_text', 'class', 'pp-icon-text' );

		$this->add_render_attribute( 'icon', 'class', array( 'pp-info-box-icon', 'pp-icon' ) );

		if ( $settings['hover_animation_icon'] ) {
			$this->add_render_attribute( 'icon', 'class', 'elementor-animation-' . $settings['hover_animation_icon'] );
		}

		if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default.
			$settings['icon'] = 'fa fa-star';
		}

		$has_icon = ! empty( $settings['icon'] );

		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		if ( ! $has_icon && ! empty( $settings['selected_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
		$is_new   = ! isset( $settings['icon'] ) && Icons_Manager::is_migration_allowed();
		?>
		<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
			<?php if ( 'icon' === $settings['icon_type'] && $has_icon ) { ?>
				<?php
				if ( $is_new || $migrated ) {
					Icons_Manager::render_icon( $settings['selected_icon'], array( 'aria-hidden' => 'true' ) );
				} elseif ( ! empty( $settings['icon'] ) ) {
					?>
					<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'i' ) ); ?>></i>
					<?php
				}
				?>
				<?php
			} elseif ( 'image' === $settings['icon_type'] ) {

				if ( ! empty( $settings['image']['url'] ) ) {
					echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings, 'image', 'image' ) );
				}
			} elseif ( 'text' === $settings['icon_type'] ) {
				?>
				<span class="pp-icon-text">
					<?php echo wp_kses_post( $settings['icon_text'] ); ?>
				</span>
			<?php } ?>
		</span>
		<?php
	}

	/**
	 * Render info box button output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_infobox_button() {
		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'button_text', 'none' );
		$this->add_render_attribute( 'button_text', 'class', 'pp-button-text' );

		$this->add_render_attribute(
			'info-box-button',
			'class',
			array(
				'pp-info-box-button',
				'elementor-button',
				'elementor-size-' . $settings['button_size'],
			)
		);

		if ( 'button' === $settings['link_type'] ) {
			$button_html_tag = ( 'button' === $settings['link_type'] ) ? 'a' : 'div';
		} elseif ( 'box' === $settings['link_type'] && 'yes' === $settings['button_visible'] ) {
			$button_html_tag = ( 'button' === $settings['link_type'] ) ? 'div' : 'div';
		}

		if ( $settings['button_animation'] ) {
			$this->add_render_attribute( 'info-box-button', 'class', 'elementor-animation-' . $settings['button_animation'] );
		}

		if ( ! isset( $settings['button_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default.
			$settings['button_icon'] = '';
		}

		$has_icon = ! empty( $settings['button_icon'] );

		if ( $has_icon ) {
			$this->add_render_attribute( 'button-icon', 'class', $settings['button_icon'] );
			$this->add_render_attribute( 'button-icon', 'aria-hidden', 'true' );
		}

		if ( ! $has_icon && ! empty( $settings['select_button_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_button_icon'] );
		$is_new   = ! isset( $settings['button_icon'] ) && Icons_Manager::is_migration_allowed();

		if ( 'button' === $settings['link_type'] || ( 'box' === $settings['link_type'] && 'yes' === $settings['button_visible'] ) ) {
			?>
			<?php if ( '' !== $settings['button_text'] || $has_icon ) { ?>
				<div class="pp-info-box-footer">
					<<?php echo esc_html( $button_html_tag ) . ' ' . wp_kses_post( $this->get_render_attribute_string( 'info-box-button' ) ) . wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>
						<?php if ( 'before' === $settings['button_icon_position'] && $has_icon ) { ?>
							<span class='pp-button-icon pp-icon'>
								<?php
								if ( $is_new || $migrated ) {
									Icons_Manager::render_icon( $settings['select_button_icon'], array( 'aria-hidden' => 'true' ) );
								} elseif ( ! empty( $settings['button_icon'] ) ) {
									?>
									<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'button-icon' ) ); ?>></i>
									<?php
								}
								?>
							</span>
						<?php } ?>
						<?php if ( ! empty( $settings['button_text'] ) ) { ?>
							<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'button_text' ) ); ?>>
								<?php echo wp_kses_post( $settings['button_text'] ); ?>
							</span>
						<?php } ?>
						<?php if ( 'after' === $settings['button_icon_position'] && $has_icon ) { ?>
							<span class='pp-button-icon pp-icon'>
								<?php
								if ( $is_new || $migrated ) {
									Icons_Manager::render_icon( $settings['select_button_icon'], array( 'aria-hidden' => 'true' ) );
								} elseif ( ! empty( $settings['button_icon'] ) ) {
									?>
									<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'button-icon' ) ); ?>></i>
									<?php
								}
								?>
							</span>
						<?php } ?>
					</<?php echo esc_html( $button_html_tag ); ?>>
				</div>
			<?php } ?>
			<?php
		}
	}

	/**
	 * Render info box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			array(
				'info-box'           => array(
					'class' => 'pp-info-box',
				),
				'info-box-container' => array(
					'class' => 'pp-info-box-container',
				),
				'title-container'    => array(
					'class' => 'pp-info-box-title-container',
				),
				'heading'            => array(
					'class' => 'pp-info-box-title',
				),
				'sub_heading'        => array(
					'class' => 'pp-info-box-subtitle',
				),
				'description'        => array(
					'class' => 'pp-info-box-description',
				),
			)
		);

		$if_html_tag = 'div';
		$title_container_tag = 'div';

		$this->add_inline_editing_attributes( 'heading', 'none' );
		$this->add_inline_editing_attributes( 'sub_heading', 'none' );
		$this->add_inline_editing_attributes( 'description', 'basic' );

		if ( 'none' !== $settings['link_type'] ) {
			if ( ! empty( $settings['link']['url'] ) ) {
				$this->add_link_attributes( 'link', $settings['link'] );

				if ( 'box' === $settings['link_type'] ) {
					$if_html_tag = 'a';
				} elseif ( 'title' === $settings['link_type'] ) {
					$title_container_tag = 'a';
				}
			}
		}
		?>
		<<?php echo esc_html( $if_html_tag ) . ' ' . wp_kses_post( $this->get_render_attribute_string( 'info-box-container' ) ) . wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'info-box' ) ); ?>>
			<?php if ( 'none' !== $settings['icon_type'] ) { ?>
				<div class="pp-info-box-icon-wrap">
					<?php if ( 'icon' === $settings['link_type'] ) { ?>
						<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>
					<?php } ?>
					<?php
						// Icon.
						$this->render_infobox_icon();
					?>
					<?php if ( 'icon' === $settings['link_type'] ) { ?>
						</a>
					<?php } ?>
				</div>
			<?php } ?>
			<div class="pp-info-box-content">
				<div class="pp-info-box-title-wrap">
					<?php
					if ( ! empty( $settings['heading'] ) ) {
						$title_tag = PP_Helper::validate_html_tag( $settings['title_html_tag'] );
						?>
						<<?php echo esc_html( $title_container_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'title-container' ) ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>>
							<<?php echo esc_html( $title_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'heading' ) ); ?>>
								<?php echo wp_kses_post( $settings['heading'] ); ?>
							</<?php echo esc_html( $title_tag ); ?>>
						</<?php echo esc_html( $title_container_tag ); ?>>
						<?php
					}

					if ( '' !== $settings['sub_heading'] ) {
						$subtitle_tag = PP_Helper::validate_html_tag( $settings['sub_title_html_tag'] );
						?>
						<<?php echo esc_html( $subtitle_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'sub_heading' ) ); ?>>
							<?php echo wp_kses_post( $settings['sub_heading'] ); ?>
						</<?php echo esc_html( $subtitle_tag ); ?>>
						<?php
					}
					?>
				</div>

				<?php if ( 'yes' === $settings['divider_title_switch'] ) { ?>
					<div class="pp-info-box-divider-wrap">
						<div class="pp-info-box-divider"></div>
					</div>
				<?php } ?>

				<?php if ( ! empty( $settings['description'] ) ) { ?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'description' ) ); ?>>
						<?php echo $this->parse_text_editor( $settings['description'] ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php } ?>
				<?php
					// Button.
					$this->render_infobox_button();
				?>
			</div>
		</div>
		</<?php echo esc_attr( $if_html_tag ); ?>>
		<?php
	}

	/**
	 * Render info box widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			var pp_if_html_tag = 'div',
				pp_title_html_tag = 'div',
				pp_button_html_tag = 'div',
				iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
				migrated = elementor.helpers.isIconMigrated( settings, 'selected_icon' ),
				buttonIconHTML = elementor.helpers.renderIcon( view, settings.select_button_icon, { 'aria-hidden': true }, 'i' , 'object' ),
				buttonMigrated = elementor.helpers.isIconMigrated( settings, 'select_button_icon' );

			if ( settings.link.url != '' ) {
				if ( settings.link_type == 'box' ) {
					var pp_if_html_tag = 'a';
				}
				else if ( settings.link_type == 'title' ) {
					var pp_title_html_tag = 'a';
				}
				else if ( settings.link_type == 'button' ) {
					var pp_button_html_tag = 'a';
				}
			}
		#>
		<{{{pp_if_html_tag}}} class="pp-info-box-container" href="{{settings.link.url}}">
			<div class="pp-info-box pp-info-box-{{ settings.icon_position }}">
				<# if ( settings.icon_type != 'none' ) { #>
					<div class="pp-info-box-icon-wrap">
						<# if ( settings.link_type == 'icon' ) { #>
							<a href="{{settings.link.url}}">
						<# } #>
						<span class="pp-info-box-icon pp-icon elementor-animation-{{ settings.hover_animation_icon }}">
							<# if ( settings.icon_type == 'icon' ) { #>
								<# if ( settings.icon || settings.selected_icon ) { #>
								<# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
								{{{ iconHTML.value }}}
								<# } else { #>
									<i class="{{ settings.icon }}" aria-hidden="true"></i>
								<# } #>
								<# } #>
							<# } else if ( settings.icon_type == 'image' ) { #>
								<#
								var image = {
									id: settings.image.id,
									url: settings.image.url,
									size: settings.image_size,
									dimension: settings.image_custom_dimension,
									model: view.getEditModel()
								};
								var image_url = elementor.imagesManager.getImageUrl( image );
								#>
								<img src="{{{ image_url }}}" />
							<# } else if ( settings.icon_type == 'text' ) { #>
								<span class="pp-icon-text elementor-inline-editing" data-elementor-setting-key="icon_text" data-elementor-inline-editing-toolbar="none">
									{{{ settings.icon_text }}}
								</span>
							<# } #>
						</span>
						<# if ( settings.link_type == 'icon' ) { #>
							</a>
						<# } #>
					</div>
				<# } #>
				<div class="pp-info-box-content">
					<div class="pp-info-box-title-wrap">
						<# if ( settings.heading ) { #>
							<{{pp_title_html_tag}} class="pp-info-box-title-container" href="{{settings.link.url}}">
								<{{settings.title_html_tag}} class="pp-info-box-title elementor-inline-editing" data-elementor-setting-key="heading" data-elementor-inline-editing-toolbar="none">
									{{{ settings.heading }}}
								</{{settings.title_html_tag}}>
							</{{pp_title_html_tag}}>
						<# } #>
						<# if ( settings.sub_heading ) { #>
							<{{settings.sub_title_html_tag}} class="pp-info-box-subtitle elementor-inline-editing" data-elementor-setting-key="sub_heading" data-elementor-inline-editing-toolbar="none">
								{{{ settings.sub_heading }}}
							</{{settings.sub_title_html_tag}}>
						<# } #>
					</div>

					<# if ( settings.divider_title_switch == 'yes' ) { #>
						<div class="pp-info-box-divider-wrap">
							<div class="pp-info-box-divider"></div>
						</div>
					<# } #>

					<# if ( settings.description ) { #>
						<div class="pp-info-box-description elementor-inline-editing" data-elementor-setting-key="description" data-elementor-inline-editing-toolbar="basic">
							{{{ settings.description }}}
						</div>
					<# } #>
					<# if ( settings.link_type == 'button' || ( settings.link_type == 'box' && settings.button_visible == 'yes' ) ) { #>
						<# if ( settings.button_text != '' || settings.button_icon != '' ) { #>
							<div class="pp-info-box-footer">
								<{{pp_button_html_tag}} href="{{ settings.link.url }}" class="pp-info-box-button elementor-button elementor-size-{{ settings.button_size }} elementor-animation-{{ settings.button_animation }}">
									<# if ( settings.button_icon_position == 'before' ) { #>
										<# if ( settings.button_icon || settings.select_button_icon.value ) { #>
											<span class="pp-button-icon pp-icon">
												<# if ( buttonIconHTML && buttonIconHTML.rendered && ( ! settings.button_icon || buttonMigrated ) ) { #>
												{{{ buttonIconHTML.value }}}
												<# } else { #>
													<i class="{{ settings.button_icon }}" aria-hidden="true"></i>
												<# } #>
											</span>
										<# } #>
									<# } #>
									<# if ( settings.button_text ) { #>
										<span class="pp-button-text elementor-inline-editing" data-elementor-setting-key="button_text" data-elementor-inline-editing-toolbar="none">
											{{{ settings.button_text }}}
										</span>
									<# } #>
									<# if ( settings.button_icon_position == 'after' ) { #>
										<# if ( settings.button_icon || settings.select_button_icon.value ) { #>
											<span class="pp-button-icon pp-icon">
												<# if ( buttonIconHTML && buttonIconHTML.rendered && ( ! settings.button_icon || buttonMigrated ) ) { #>
													{{{ buttonIconHTML.value }}}
												<# } else { #>
													<i class="{{ settings.button_icon }}" aria-hidden="true"></i>
												<# } #>
											</span>
										<# } #>
									<# } #>
								</{{pp_button_html_tag}}>
							</div>
						<# } #>
					<# } #>
				</div>
			</div>
		</{{{pp_if_html_tag}}}>
		<?php
	}
}
