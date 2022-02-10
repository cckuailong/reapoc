<?php
namespace PowerpackElementsLite\Modules\Buttons\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Buttons Widget
 */
class Buttons extends Powerpack_Widget {

	/**
	 * Retrieve buttons widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Buttons' );
	}

	/**
	 * Retrieve buttons widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Buttons' );
	}

	/**
	 * Retrieve buttons widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Buttons' );
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
		return parent::get_widget_keywords( 'Buttons' );
	}

	/**
	 * Retrieve the list of scripts the advanced menu widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [
			'pp-tooltipster',
			'powerpack-frontend',
		];
	}
	/**
	 * Register buttons widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register buttons widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.1.3
	 * @access protected
	 */
	protected function register_controls() {

		/*-----------------------------------------------------------------------------------*/
		/*	CONTENT TAB
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Content Tab: Buttons
		 */
		$this->start_controls_section(
			'section_list',
			[
				'label'                 => __( 'Buttons', 'powerpack' ),
			]
		);
		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'buttons_tabs' );

			$repeater->start_controls_tab(
				'button_general',
				[
					'label' => __( 'Content', 'powerpack' ),
				]
			);

			$repeater->add_control(
				'text',
				[
					'label'             => __( 'Text', 'powerpack' ),
					'type'              => Controls_Manager::TEXT,
					'default'           => __( 'Button #1', 'powerpack' ),
					'placeholder'       => __( 'Button #1', 'powerpack' ),
					'dynamic'           => [
						'active' => true,
					],
				]
			);
			$repeater->add_control(
				'pp_icon_type',
				[
					'label'             => __( 'Icon Type', 'powerpack' ),
					'type'              => Controls_Manager::CHOOSE,
					'label_block'       => false,
					'toggle'            => false,
					'default'           => 'icon',
					'options'           => [
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
				]
			);
			$repeater->add_control(
				'selected_icon',
				[
					'label'             => __( 'Icon', 'powerpack' ),
					'type'              => Controls_Manager::ICONS,
					'label_block'       => true,
					'default'           => [
						'value' => 'fas fa-check',
						'library' => 'fa-solid',
					],
					'fa4compatibility'  => 'button_icon',
					'condition'         => [
						'pp_icon_type' => 'icon',
					],
				]
			);
			$repeater->add_control(
				'icon_img',
				[
					'label'             => __( 'Image', 'powerpack' ),
					'label_block'       => true,
					'type'              => Controls_Manager::MEDIA,
					'default'           => [
						'url' => Utils::get_placeholder_image_src(),
					],
					'dynamic'           => [
						'active'  => true,
					],
					'condition'         => [
						'pp_icon_type' => 'image',
					],
				]
			);
			$repeater->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'      => 'icon_img',
					'label'     => __( 'Image Size', 'powerpack' ),
					'default'   => 'full',
					'condition' => array(
						'pp_icon_type' => 'image',
					),
				)
			);
			$repeater->add_control(
				'icon_text',
				[
					'label'             => __( 'Icon Text', 'powerpack' ),
					'label_block'       => false,
					'type'              => Controls_Manager::TEXT,
					'default'           => __( '1', 'powerpack' ),
					'dynamic'           => [
						'active'  => true,
					],
					'condition'         => [
						'pp_icon_type' => 'text',
					],
				]
			);

			$repeater->add_control(
				'has_tooltip',
				[
					'label'         => __( 'Enable Tooltip', 'powerpack' ),
					'type'          => Controls_Manager::SWITCHER,
					'default'       => 'no',
					'yes'       => __( 'Yes', 'powerpack' ),
					'no'        => __( 'No', 'powerpack' ),
				]
			);

			$repeater->add_control(
				'tooltip_content',
				[
					'label'         => __( 'Tooltip Content', 'powerpack' ),
					'type'          => Controls_Manager::TEXTAREA,
					'default'       => __( 'I am a tooltip for a button', 'powerpack' ),
					'placeholder'   => __( 'I am a tooltip for a button', 'powerpack' ),
					'rows'          => 5,
					'condition'     => [
						'has_tooltip'   => 'yes',
					],
				]
			);

			$repeater->add_control(
				'link',
				[
					'label'             => __( 'Link', 'powerpack' ),
					'type'              => Controls_Manager::URL,
					'dynamic'           => [
						'active'  => true,
					],
					'label_block'       => true,
					'placeholder'       => __( 'http://your-link.com', 'powerpack' ),
				]
			);
			$repeater->add_control(
				'css_id',
				[
					'label'       => __( 'CSS ID', 'powerpack' ),
					'title'       => __( 'Add your custom ID WITHOUT the # key. e.g: my-id', 'powerpack' ),
					'label_block' => false,
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => [
						'active'  => true,
					],
				]
			);
			$repeater->add_control(
				'css_classes',
				[
					'label'       => __( 'CSS Classes', 'powerpack' ),
					'title'       => __( 'Add your custom class WITHOUT the dot. e.g: my-class', 'powerpack' ),
					'label_block' => false,
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => [
						'active'  => true,
					],
				]
			);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab(
				'button_layout_tab',
				[
					'label' => __( 'Layout', 'powerpack' ),
				]
			);

			$repeater->add_control(
				'single_button_size',
				[
					'label'                 => __( 'Button Size', 'powerpack' ),
					'type'                  => Controls_Manager::SELECT,
					'default'               => 'default',
					'options'               => [
						'default' => __( 'Default', 'powerpack' ),
						'xs' => __( 'Extra Small', 'powerpack' ),
						'sm' => __( 'Small', 'powerpack' ),
						'md' => __( 'Medium', 'powerpack' ),
						'lg' => __( 'Large', 'powerpack' ),
						'xl' => __( 'Extra Large', 'powerpack' ),
						'custom' => __( 'Custom', 'powerpack' ),
					],
				]
			);

			$repeater->add_responsive_control(
				'single_button_width',
				[
					'label'                 => __( 'Button Width', 'powerpack' ),
					'type'                  => Controls_Manager::SLIDER,
					'size_units'            => [ 'px', '%' ],
					'range'                 => [
						'px' => [
							'min'   => 10,
							'max'   => 800,
							'step'  => 1,
						],
					],
					'selectors'     => [
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-button' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition'         => [
						'single_button_size' => 'custom',
					],
				]
			);

			$repeater->add_responsive_control(
				'single_button_padding',
				[
					'label'                 => __( 'Padding', 'powerpack' ),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => [ 'px', 'em', '%' ],
					'selectors'             => [
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$repeater->end_controls_tab();

			$repeater->start_controls_tab(
				'button_style_tabs',
				[
					'label' => __( 'Style', 'powerpack' ),
				]
			);

			$repeater->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                  => 'single_title_typography',
					'label'                 => __( 'Button Typography', 'powerpack' ),
					'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
					'selector'              => '{{WRAPPER}} {{CURRENT_ITEM}}.pp-button .pp-button-title',
				]
			);

			$repeater->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				[
					'name'     => 'text_shadow',
					'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.pp-button .pp-button-title',
				]
			);

			$repeater->add_responsive_control(
				'single_icon_size',
				[
					'label'                 => __( 'Icon Size', 'powerpack' ),
					'type'                  => Controls_Manager::SLIDER,
					'range'                 => [
						'px' => [
							'min'   => 5,
							'max'   => 100,
							'step'  => 1,
						],
					],
					'selectors'     => [
						'{{WRAPPER}} {{CURRENT_ITEM}} span.pp-button-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .pp-button-icon-image img' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition'         => [
						'pp_icon_type!' => 'none',
					],
				]
			);

			$repeater->add_control(
				'single_normal_options',
				[
					'label'     => __( 'Normal', 'powerpack' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'after',
				]
			);
			$repeater->add_control(
				'single_button_bg_color',
				[
					'label'                 => __( 'Background Color', 'powerpack' ),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-button' => 'background: {{VALUE}};',
					],
				]
			);
			$repeater->add_control(
				'single_text_color',
				[
					'label'                 => __( 'Text Color', 'powerpack' ),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-button' => 'color: {{VALUE}};',
					],
				]
			);
			$repeater->add_control(
				'single_icon_color',
				[
					'label'                 => __( 'Icon Color', 'powerpack' ),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-button .pp-buttons-icon-wrapper span' => 'color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-button .pp-buttons-icon-wrapper .pp-icon svg' => 'fill: {{VALUE}};',
					],
				]
			);
			$repeater->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'                  => 'single_button_border',
					'label'                 => __( 'Border', 'powerpack' ),
					'placeholder'           => '1px',
					'default'               => '1px',
					'selector'              => '{{WRAPPER}} {{CURRENT_ITEM}}.pp-button',
				]
			);
			$repeater->add_control(
				'single_button_border_radius',
				[
					'label'                 => __( 'Border Radius', 'powerpack' ),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => [ 'px', '%' ],
					'selectors'             => [
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$repeater->add_control(
				'single_hover_options',
				[
					'label' => __( 'Hover', 'powerpack' ),
					'type'  => Controls_Manager::HEADING,
					'separator' => 'after',
				]
			);

			$repeater->add_control(
				'single_button_bg_color_hover',
				[
					'label'                 => __( 'Background Color', 'powerpack' ),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-button:hover' => 'background: {{VALUE}};',
					],
				]
			);

			$repeater->add_control(
				'single_text_color_hover',
				[
					'label'                 => __( 'Text Color', 'powerpack' ),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-button:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$repeater->add_control(
				'single_icon_color_hover',
				[
					'label'                 => __( 'Icon Color', 'powerpack' ),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-button:hover .pp-buttons-icon-wrapper span' => 'color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-button:hover .pp-buttons-icon-wrapper .pp-icon svg' => 'fill: {{VALUE}};',
					],
				]
			);

			$repeater->add_control(
				'single_border_color_hover',
				[
					'label'                 => __( 'Border Color', 'powerpack' ),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-button:hover' => 'border-color: {{VALUE}};',
					],
				]
			);

			$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'buttons',
			[
				'label'       => __( 'Buttons', 'powerpack' ),
				'type'        => Controls_Manager::REPEATER,
				'show_label'  => true,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ text }}}',
				'default'     => [
					[
						'text' => __( 'Button #1', 'powerpack' ),
					],
					[
						'text' => __( 'Button #2', 'powerpack' ),
					],
				],
			]
		);
		$this->end_controls_section();

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

		/**
		 * Style Tab: Layout
		 */
		$this->start_controls_section(
			'button_layout',
			[
				'label' => __( 'Layout', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'button_size',
			[
				'label'                 => __( 'Buttons Size', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'sm',
				'options'               => [
					'xs' => __( 'Extra Small', 'powerpack' ),
					'sm' => __( 'Small', 'powerpack' ),
					'md' => __( 'Medium', 'powerpack' ),
					'lg' => __( 'Large', 'powerpack' ),
					'xl' => __( 'Extra Large', 'powerpack' ),
				],
			]
		);
		$this->add_responsive_control(
			'button_spacing',
			[
				'label'                 => __( 'Buttons Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 10,
				],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-buttons-group .pp-button:not(:last-child)'  => 'margin-right: {{SIZE}}{{UNIT}};',
					'(desktop){{WRAPPER}}.pp-buttons-stack-desktop .pp-buttons-group .pp-button:not(:last-child)'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'(tablet){{WRAPPER}}.pp-buttons-stack-tablet .pp-buttons-group .pp-button:not(:last-child)'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}}.pp-buttons-stack-mobile .pp-buttons-group .pp-button:not(:last-child)'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_align',
			[
				'label'                 => __( 'Vertical Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'middle',
				'options'               => [
					'top'           => [
						'title'     => __( 'Top', 'powerpack' ),
						'icon'      => 'eicon-v-align-top',
					],
					'middle'        => [
						'title'     => __( 'Middle', 'powerpack' ),
						'icon'      => 'eicon-v-align-middle',
					],
					'bottom'        => [
						'title'     => __( 'Bottom', 'powerpack' ),
						'icon'      => 'eicon-v-align-bottom',
					],
					'stretch'       => [
						'title'     => __( 'Stretch', 'powerpack' ),
						'icon'      => 'eicon-v-align-stretch',
					],
				],
				'prefix_class'          => 'pp-buttons-valign%s-',
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label'                 => __( 'Horizontal Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'left',
				'options'               => [
					'left'          => [
						'title'     => __( 'Left', 'powerpack' ),
						'icon'      => 'eicon-h-align-left',
					],
					'center'        => [
						'title'     => __( 'Center', 'powerpack' ),
						'icon'      => 'eicon-h-align-center',
					],
					'right'         => [
						'title'     => __( 'Right', 'powerpack' ),
						'icon'      => 'eicon-h-align-right',
					],
					'stretch'       => [
						'title'     => __( 'Stretch', 'powerpack' ),
						'icon'      => 'eicon-h-align-stretch',
					],
				],
				'prefix_class'          => 'pp-buttons-halign%s-',
			]
		);

		$this->add_responsive_control(
			'content_align',
			[
				'label'                 => __( 'Content Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'left',
				'options'               => [
					'left'          => [
						'title'     => __( 'Left', 'powerpack' ),
						'icon'      => 'eicon-h-align-left',
					],
					'center'        => [
						'title'     => __( 'Center', 'powerpack' ),
						'icon'      => 'eicon-h-align-center',
					],
					'right'         => [
						'title'     => __( 'Right', 'powerpack' ),
						'icon'      => 'eicon-h-align-right',
					],
					'stretch'       => [
						'title'     => __( 'Stretch', 'powerpack' ),
						'icon'      => 'eicon-h-align-stretch',
					],
				],
				'selectors_dictionary'  => [
					'left'         => 'flex-start',
					'center'       => 'center',
					'right'        => 'flex-end',
					'stretch'      => 'stretch',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-button .pp-button-content-wrapper'   => 'justify-content: {{VALUE}};',
				],
				'condition'             => [
					'button_align' => 'stretch',
				],
			]
		);

		$this->add_control(
			'stack_on',
			[
				'label'                 => __( 'Stack on', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'none',
				'description'           => __( 'Choose a breakpoint where the buttons will stack.', 'powerpack' ),
				'options'               => [
					'none'    => __( 'None', 'powerpack' ),
					'desktop' => __( 'Desktop', 'powerpack' ),
					'tablet'  => __( 'Tablet', 'powerpack' ),
					'mobile'  => __( 'Mobile', 'powerpack' ),
				],
				'prefix_class'          => 'pp-buttons-stack-',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Styling
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_box_button_style',
			[
				'label'                 => __( 'Styling', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'button_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} .pp-button',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'text_shadow',
				'selector' => '{{WRAPPER}} .pp-button',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

			$this->add_control(
				'button_bg_color_normal',
				[
					'label'                 => __( 'Background Color', 'powerpack' ),
					'type'                  => Controls_Manager::COLOR,
					'scheme'    => [
						'type'  => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_4,
					],
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .pp-button' => 'background: {{VALUE}};',
					],
				]
			);
			$this->add_control(
				'button_text_color_normal',
				[
					'label'                 => __( 'Text Color', 'powerpack' ),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '#ffffff',
					'selectors'             => [
						'{{WRAPPER}} .pp-button' => 'color: {{VALUE}}',
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
					'selector'              => '{{WRAPPER}} .pp-button',
				]
			);
			$this->add_responsive_control(
				'button_border_radius',
				[
					'label'                 => __( 'Border Radius', 'powerpack' ),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => [ 'px', '%' ],
					'selectors'             => [
						'{{WRAPPER}} .pp-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'                  => 'button_box_shadow',
					'selector'              => '{{WRAPPER}} .pp-button',
				]
			);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

			$this->add_control(
				'button_bg_color_hover',
				[
					'label'                 => __( 'Background Color', 'powerpack' ),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .pp-button:hover' => 'background: {{VALUE}};',
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
						'{{WRAPPER}} .pp-button:hover' => 'color: {{VALUE}}',
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
						'{{WRAPPER}} .pp-button:hover' => 'border-color: {{VALUE}}',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'                  => 'button_box_shadow_hover',
					'selector'              => '{{WRAPPER}} .pp-button:hover',
				]
			);
			$this->add_control(
				'button_animation',
				[
					'label'                 => __( 'Animation', 'powerpack' ),
					'type'                  => Controls_Manager::HOVER_ANIMATION,
				]
			);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Icon
		 */
		$this->start_controls_section(
			'section_icon_style',
			[
				'label'                 => __( 'Icon', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'icon_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-button-icon-number',
			]
		);
		$this->add_responsive_control(
			'icon_position',
			[
				'label'             => __( 'Icon Position', 'powerpack' ),
				'type'              => Controls_Manager::SELECT,
				'default'           => 'before',
				'options'           => [
					'after'     => __( 'After', 'powerpack' ),
					'before'    => __( 'Before', 'powerpack' ),
					'top'       => __( 'Top', 'powerpack' ),
					'bottom'    => __( 'Bottom', 'powerpack' ),
				],
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'range'                 => [
					'px' => [
						'min' => 6,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-button-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-button-icon-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 8,
				],
				'range'                 => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-icon-before .pp-buttons-icon-wrapper' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-icon-after .pp-buttons-icon-wrapper' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-icon-top .pp-buttons-icon-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-icon-bottom .pp-buttons-icon-wrapper' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_icon_style' );

			$this->start_controls_tab(
				'tab_icon_normal',
				[
					'label'                 => __( 'Normal', 'powerpack' ),
				]
			);
			$this->add_control(
				'icon_color',
				[
					'label'                 => __( 'Color', 'powerpack' ),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .pp-buttons-icon-wrapper span' => 'color: {{VALUE}};',
						'{{WRAPPER}} .pp-buttons-icon-wrapper .pp-icon svg' => 'fill: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_icon_hover',
				[
					'label'                 => __( 'Hover', 'powerpack' ),
				]
			);

			$this->add_control(
				'icon_color_hover',
				[
					'label'                 => __( 'Color', 'powerpack' ),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .pp-button:hover .pp-buttons-icon-wrapper .pp-button-icon' => 'color: {{VALUE}};',
						'{{WRAPPER}} .pp-button:hover .pp-buttons-icon-wrapper .pp-icon svg' => 'fill: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Tooltip
		 * -------------------------------------------------
		 */

		$this->start_controls_section(
			'section_tooltip_style',
			[
				'label'     => __( 'Tooltip', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

			$this->add_responsive_control(
				'tooltips_position',
				[
					'label'     => __( 'Tooltip Position', 'powerpack' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'above',
					'options'   => [
						'above'     => __( 'Above', 'powerpack' ),
						'below'     => __( 'Below', 'powerpack' ),
						'left'      => __( 'Left', 'powerpack' ),
						'right'     => __( 'Right', 'powerpack' ),
					],
				]
			);

			$this->add_control(
				'tooltips_align',
				[
					'label'     => __( 'Text Align', 'powerpack' ),
					'type'      => Controls_Manager::CHOOSE,
					'default'   => ' center',
					'options'   => [
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
					'selectors' => [
						'.pp-tooltip-{{ID}}' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'tooltips_background_color',
				[
					'label'     => __( 'Background Color', 'powerpack' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#000000',
					'selectors' => [
						'.pp-tooltip.pp-tooltip-{{ID}} .tooltipster-box' => 'background-color: {{VALUE}};',
						'.pp-tooltip.pp-tooltip-{{ID}}.tooltipster-top .tooltipster-arrow-background' => 'border-top-color: {{VALUE}};',
						'.pp-tooltip.pp-tooltip-{{ID}}.tooltipster-bottom .tooltipster-arrow-background' => 'border-bottom-color: {{VALUE}};',
						'.pp-tooltip.pp-tooltip-{{ID}}.tooltipster-left .tooltipster-arrow-background' => 'border-left-color: {{VALUE}};',
						'.pp-tooltip.pp-tooltip-{{ID}}.tooltipster-right .tooltipster-arrow-background' => 'border-right-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'tooltips_color',
				[
					'label'     => __( 'Color', 'powerpack' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffffff',
					'selectors' => [
						'.pp-tooltip.pp-tooltip-{{ID}} .pp-tooltip-content' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'      => 'tooltips_typography',
					'scheme'    => Scheme_Typography::TYPOGRAPHY_3,
					'separator' => 'after',
					'selector'  => '.pp-tooltip.pp-tooltip-{{ID}} .pp-tooltip-content',
				]
			);

			$this->add_responsive_control(
				'tooltips_border_radius',
				[
					'label'      => __( 'Border Radius', 'powerpack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'.pp-tooltip.pp-tooltip-{{ID}} .tooltipster-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'tooltips_padding',
				[
					'label'      => __( 'Padding', 'powerpack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'.pp-tooltip.pp-tooltip-{{ID}} .tooltipster-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'      => 'tooltips_box_shadow',
					'selector'  => '.pp-tooltip.pp-tooltip-{{ID}} .tooltipster-box',
					'separator' => '',
				]
			);

		$this->end_controls_section();
	}

	/**
	 * Render buttons widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$fallback_defaults = [
			'fa fa-check',
			'fa fa-times',
			'fa fa-dot-circle-o',
		];

		// Button Animation
		$button_animation = '';
		if ( $settings['button_animation'] ) {
			$button_animation = 'elementor-animation-' . $settings['button_animation'];
		}

		$i = 1;
		?>
		<div class="pp-buttons-group">
			<?php foreach ( $settings['buttons'] as $index => $item ) : ?>
				<?php
				$button_key          = $this->get_repeater_setting_key( 'button', 'buttons', $index );
				$tooltip_content_key = $this->get_repeater_setting_key( 'tooltip_content', 'buttons', $index );
				$tooltip_content_id  = $this->get_id() . '-' . $item['_id'];
				$content_inner_key   = $this->get_repeater_setting_key( 'content', 'buttons', $index );

				// Button Size
				$button_size = ( 'default' !== $item['single_button_size'] ) ? $item['single_button_size'] : $settings['button_size'];

				// Link
				if ( ! empty( $item['link']['url'] ) ) {
					$this->add_link_attributes( $button_key, $item['link'] );
				}

				// Icon Position
				$icon_position = '';
				if ( isset( $settings['icon_position'] ) && $settings['icon_position'] ) {
					$icon_position = 'pp-icon-' . $settings['icon_position'];
				}
				if ( isset( $settings['icon_position_tablet'] ) && $settings['icon_position_tablet'] ) {
					$icon_position .= ' pp-icon-' . $settings['icon_position_tablet'] . '-tablet';
				}
				if ( isset( $settings['icon_position_mobile'] ) && $settings['icon_position_mobile'] ) {
					$icon_position .= ' pp-icon-' . $settings['icon_position_mobile'] . '-mobile';
				}

				$this->add_render_attribute( $button_key, 'class', [
					'pp-button',
					'elementor-button',
					'elementor-size-' . $button_size,
					'elementor-repeater-item-' . $item['_id'],
					$button_animation,
				] );

				// CSS ID
				if ( $item['css_id'] ) {
					$this->add_render_attribute( $button_key, 'id', $item['css_id'] );
				}

				// Custom Class
				if ( $item['css_classes'] ) {
					$this->add_render_attribute( $button_key, 'class', $item['css_classes'] );
				}

				// ToolTip
				if ( 'yes' === $item['has_tooltip'] && ! empty( $item['tooltip_content'] ) ) {
					$ttip_position = $this->get_tooltip_position( $settings['tooltips_position'] );
					$ttip_position_tablet = $this->get_tooltip_position( $settings['tooltips_position_tablet'] );
					$ttip_position_mobile = $this->get_tooltip_position( $settings['tooltips_position_mobile'] );

					$this->add_render_attribute(
						$tooltip_content_key,
						array(
							'class' => [ 'pp-tooltip-content', 'pp-tooltip-content-' . $this->get_id() ],
							'id'    => 'pp-tooltip-content-' . $tooltip_content_id,
						)
					);

					if ( isset( $settings['tooltips_position_tablet'] ) && $settings['tooltips_position_tablet'] ) {
						$ttip_tablet = $ttip_position_tablet;
					} else {
						$ttip_tablet = $ttip_position;
					};

					if ( isset( $settings['tooltips_position_mobile'] ) && $settings['tooltips_position_mobile'] ) {
						$ttip_mobile = $ttip_position_mobile;
					} else {
						$ttip_mobile = $ttip_position;
					};

					$this->add_render_attribute(
						$button_key,
						[
							'data-tooltip'                 => 'yes',
							'data-tooltip-position'        => $ttip_position,
							'data-tooltip-position-tablet' => $ttip_tablet,
							'data-tooltip-position-mobile' => $ttip_mobile,
							'data-tooltip-content'         => '#pp-tooltip-content-' . $tooltip_content_id,
						]
					);
				}

				$this->add_render_attribute( $content_inner_key, 'class', [
					'pp-button-content-inner',
					$icon_position,
				] );
				?>
				<a <?php echo wp_kses_post( $this->get_render_attribute_string( $button_key ) ); ?>>
					<div class="pp-button-content-wrapper">
						<span <?php echo wp_kses_post( $this->get_render_attribute_string( $content_inner_key ) ); ?>>
							<?php
							if ( 'none' !== $item['pp_icon_type'] ) {
								$icon_key = 'icon_' . $i;
								$icon_wrap = 'pp-buttons-icon-wrapper';
								$this->add_render_attribute( $icon_key, 'class', $icon_wrap );
								$migration_allowed = Icons_Manager::is_migration_allowed();
								?>
								<span <?php echo wp_kses_post( $this->get_render_attribute_string( $icon_key ) ); ?>>
									<?php
									if ( 'icon' === $item['pp_icon_type'] ) {
										// add old default
										if ( ! isset( $item['icon'] ) && ! $migration_allowed ) {
											$item['icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-check';
										}

										$migrated = isset( $item['__fa4_migrated']['selected_icon'] );
										$is_new = ! isset( $item['icon'] ) && $migration_allowed;

										if ( ! empty( $item['icon'] ) || ( ! empty( $item['selected_icon']['value'] ) && $is_new ) ) { ?>
											<span class="pp-button-icon pp-icon">
												<?php
												if ( $is_new || $migrated ) {
													Icons_Manager::render_icon( $item['selected_icon'], [
														'class' => 'pp-button-icon',
														'aria-hidden' => 'true',
													] );
												} else { ?>
													<i class="pp-button-icon <?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></i>
													<?php
												}
												?>
											</span>
											<?php
										}
									} elseif ( 'image' === $item['pp_icon_type'] ) { ?>
										<span class="pp-button-icon-image">
											<?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $item, 'icon_img', 'icon_img' ) ); ?>
										</span>
										<?php
									} elseif ( 'text' === $item['pp_icon_type'] ) { ?>
										<span class="pp-button-icon pp-button-icon-number">
											<?php echo esc_attr( $item['icon_text'] ); ?>
										</span>
										<?php
									}
									?>
								</span>
								<?php
							}
							if ( $item['text'] ) { ?>
								<?php
								$text_key = $this->get_repeater_setting_key( 'text', 'buttons', $index );
								$this->add_render_attribute( $text_key, 'class', 'pp-button-title' );
								$this->add_inline_editing_attributes( $text_key, 'none' ); ?>

								<span <?php echo wp_kses_post( $this->get_render_attribute_string( $text_key ) ); ?>>
									<?php echo wp_kses_post( $item['text'] ); ?>
								</span>
							<?php } ?>
						</span>
					</div>
				</a>
				<?php if ( 'yes' === $item['has_tooltip'] && $item['tooltip_content'] ) { ?>
					<div class="pp-tooltip-container">
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( $tooltip_content_key ) ); ?>>
							<?php echo wp_kses_post( $item['tooltip_content'] ); ?>
						</div>
					</div>
				<?php } ?>
				<?php $i++;
			endforeach; ?>
		</div><?php
	}

	protected function get_tooltip_position( $tt_position ) {
		switch ( $tt_position ) {
			case 'above':
				$tt_position = 'top';
				break;

			case 'below':
				$tt_position = 'bottom';
				break;

			case 'left':
				$tt_position = 'left';
				break;

			case 'right':
				$tt_position = 'right';
				break;

			default:
				$tt_position = 'top';
				break;
		}

		return $tt_position;
	}

	/**
	 * Render buttons widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.1.3
	 * @access protected
	 */
	protected function content_template() {
		?>
		<div class="pp-buttons-group">
			<#
			var i = 1;

			function get_tooltip_position( $tt_position ) {
				switch ( $tt_position ) {
					case 'above':
						$tt_position = 'top';
						break;

					case 'below':
						$tt_position = 'bottom';
						break;

					case 'left':
						$tt_position = 'left';
						break;

					case 'right':
						$tt_position = 'right';
						break;

					default:
						$tt_position = 'top';
						break;
				}

				return $tt_position;
			}
			#>
			<# _.each( settings.buttons, function( item, index ) { #>
				<#
				var content_inner_key = 'content-inner_' + i,
					tooltipContentId = view.$el.data('id') + '-' + item._id;
					buttonSize = '',
					iconPosition = '',
					iconsHTML = {},
					migrated = {};

				var button_key = view.getRepeaterSettingKey( 'button', 'buttons', index ),
					tooltipContentKey = view.getRepeaterSettingKey( 'tooltip_content', 'buttons', index );

				if ( item.single_button_size != 'default' ) {
					buttonSize = item.single_button_size;
				} else {
					buttonSize = settings.button_size;
				}

				if ( settings.icon_position ) {
					iconPosition = 'pp-icon-' + settings.icon_position;
				}

				if ( settings.icon_position_tablet ) {
					iconPosition += ' pp-icon-' + settings.icon_position_tablet + '-tablet';
				}

				if ( settings.icon_position_mobile ) {
					iconPosition += ' pp-icon-' + settings.icon_position_mobile + '-mobile';
				}

				view.addRenderAttribute(
					button_key,
					{
						'id': item.css_id,
						'class': [
							'pp-button',
							'elementor-button',
							'elementor-size-' + buttonSize,
							'elementor-repeater-item-' + item._id,
							'elementor-animation-' + settings.button_animation,
							item.css_classes,
						],
					}
				);

				view.addRenderAttribute(
					tooltipContentKey,
					{
						'class': [ 'pp-tooltip-content', 'pp-tooltip-content-' + tooltipContentId ],
						'id': 'pp-tooltip-content-' + tooltipContentId,
					}
				);

				if ( item.has_tooltip == 'yes' && item.tooltip_content != '' ) {
					var ttip_tablet;
					var ttip_mobile;
				   
					if ( settings.tooltips_position_tablet ) {
						ttip_tablet = settings.tooltips_position_tablet;
					} else { 
						ttip_tablet = settings.tooltips_position;
					};
					if ( settings.tooltips_position_mobile ) {
						ttip_mobile = settings.tooltips_position_mobile;
					} else { 
						ttip_mobile = settings.tooltips_position;
					};
				   
					view.addRenderAttribute(
						button_key,
						{
							'data-tooltip': 'yes',
							'data-tooltip-position': get_tooltip_position( settings.tooltips_position ),
							'data-tooltip-position-tablet': get_tooltip_position( ttip_tablet ),
							'data-tooltip-position-mobile': get_tooltip_position( ttip_mobile ),
							'data-tooltip-content': '#pp-tooltip-content-' + tooltipContentId,
						}
					);
				}

				if ( item.link.url != '' ) {
					view.addRenderAttribute( button_key, 'href', item.link.url );

					if ( item.link.is_external ) {
						view.addRenderAttribute( button_key, 'target', '_blank' );
					}

					if ( item.link.nofollow ) {
						view.addRenderAttribute( button_key, 'rel', 'nofollow' );
					}
				}

				view.addRenderAttribute(
					content_inner_key,
					{
						'class': [
							'pp-button-content-inner',
							iconPosition,
						],
					}
				);
				#>
				<a {{{ view.getRenderAttributeString( button_key ) }}}>
					<div class="pp-button-content-wrapper">
						<span {{{ view.getRenderAttributeString( content_inner_key ) }}}>
							<# if ( item.pp_icon_type != 'none' ) { #>
								<#
									var icon_key = 'icon_' + i;
							   
									view.addRenderAttribute( icon_key, 'class', 'pp-buttons-icon-wrapper' );
								#>
								<span {{{ view.getRenderAttributeString( icon_key ) }}}>
									<# if ( item.pp_icon_type == 'icon' ) { #>
										<# if ( item.button_icon || item.selected_icon.value ) { #>
											<span class="pp-button-icon pp-icon">
											<#
												iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.selected_icon, { 'aria-hidden': true }, 'i', 'object' );
												migrated[ index ] = elementor.helpers.isIconMigrated( item, 'selected_icon' );
												if ( iconsHTML[ index ] && iconsHTML[ index ].rendered && ( ! item.button_icon || migrated[ index ] ) ) { #>
													{{{ iconsHTML[ index ].value }}}
												<# } else { #>
													<i class="{{ item.button_icon }}" aria-hidden="true"></i>
												<# }
											#>
											</span>
										<# } #>
									<# } else if ( item.pp_icon_type == 'image' ) { #>
										<span class="pp-button-icon-image">
											<#
											var image = {
												id: item.icon_img.id,
												url: item.icon_img.url,
												size: item.icon_img_size,
												dimension: item.icon_img_custom_dimension,
												model: view.getEditModel()
											};
											var image_url = elementor.imagesManager.getImageUrl( image );
											#>
											<img src="{{{ image_url }}}">
										</span>
									<# } else if ( item.pp_icon_type == 'text' ) { #>
										<span class="pp-button-icon pp-button-icon-number">
											{{{ item.icon_text }}}
										</span>
									<# } #>
								</span>
							<# } #>

							<# if ( item.text != '' ) { #>
								<#
									var text_key = 'text_' + i;
							   
									view.addRenderAttribute( text_key, 'class', 'pp-button-title' );
								   
									view.addInlineEditingAttributes( text_key, 'none' );
								#>

								<span {{{ view.getRenderAttributeString( text_key ) }}}>
									{{{ item.text }}}
								</span>
							<# } #>
						</span>
					</div>
				</a>
				<# if ( 'yes' === item.has_tooltip && item.tooltip_content ) { #>
					<div class="pp-tooltip-container">
						<div {{{ view.getRenderAttributeString( tooltipContentKey ) }}}>
							{{ item.tooltip_content }}
						</div>
					</div>
				<# } #>
			<# i++ } ); #>
		</div>
		<?php
	}
}
