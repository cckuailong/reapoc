<?php
namespace PowerpackElementsLite\Modules\ImageComparison\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Image Comparison Widget
 */
class Image_Comparison extends Powerpack_Widget {

	/**
	 * Retrieve image comparison widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Image_Comparison' );
	}

	/**
	 * Retrieve image comparison widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Image_Comparison' );
	}

	/**
	 * Retrieve image comparison widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Image_Comparison' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.3.4
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Image_Comparison' );
	}

	/**
	 * Retrieve the list of scripts the image comparison widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'jquery-event-move',
			'pp-twentytwenty',
			'imagesloaded',
			'powerpack-frontend',
		);
	}

	/**
	 * Retrieve the list of styles the image comparison widget depended on.
	 *
	 * Used to set style dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_style_depends() {
		return array(
			'pp-twentytwenty',
		);
	}

	/**
	 * Register image comparison widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register image comparison widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.3.2
	 * @access protected
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_before_image_controls();
		$this->register_content_after_image_controls();
		$this->register_content_settings_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_overlay_controls();
		$this->register_style_handle_controls();
		$this->register_style_divider_controls();
		$this->register_style_label_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_content_before_image_controls() {
		/**
		 * Content Tab: Before Image
		 */
		$this->start_controls_section(
			'section_before_image',
			array(
				'label' => __( 'Before Image', 'powerpack' ),
			)
		);

		$this->add_control(
			'before_label',
			array(
				'label'   => __( 'Label', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'Before', 'powerpack' ),
			)
		);

		$this->add_control(
			'before_image',
			array(
				'label'   => __( 'Image', 'powerpack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
				),
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_after_image_controls() {
		/**
		 * Content Tab: After Image
		 */
		$this->start_controls_section(
			'section_after_image',
			array(
				'label' => __( 'After Image', 'powerpack' ),
			)
		);

		$this->add_control(
			'after_label',
			array(
				'label'   => __( 'Label', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'After', 'powerpack' ),
			)
		);

		$this->add_control(
			'after_image',
			array(
				'label'   => __( 'Image', 'powerpack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
				),
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_settings_controls() {
		/**
		 * Content Tab: Settings
		 */
		$this->start_controls_section(
			'section_member_box_settings',
			array(
				'label' => __( 'Settings', 'powerpack' ),
			)
		);

		$this->add_control(
			'visible_ratio',
			array(
				'label'      => __( 'Visible Ratio', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'size_units' => '',
			)
		);

		$this->add_control(
			'orientation',
			array(
				'label'   => __( 'Orientation', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'vertical'   => __( 'Vertical', 'powerpack' ),
					'horizontal' => __( 'Horizontal', 'powerpack' ),
				),
			)
		);

		$this->add_control(
			'move_slider',
			array(
				'label'   => __( 'Move Slider', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'drag',
				'options' => array(
					'drag'        => __( 'Drag', 'powerpack' ),
					'mouse_move'  => __( 'Mouse Move', 'powerpack' ),
					'mouse_click' => __( 'Mouse Click', 'powerpack' ),
				),
			)
		);

		$this->add_control(
			'overlay',
			array(
				'label'        => __( 'Overlay', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Show', 'powerpack' ),
				'label_off'    => __( 'Hide', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links( 'Image_Comparison' );

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

	protected function register_style_overlay_controls() {
		/**
		 * Style Tab: Overlay
		 */
		$this->start_controls_section(
			'section_overlay_style',
			[
				'label'             => __( 'Overlay', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
				'condition'         => [
					'overlay'  => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_overlay_style' );

		$this->start_controls_tab(
			'tab_overlay_normal',
			[
				'label'             => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'              => 'overlay_background',
				'types'             => [ 'classic', 'gradient' ],
				'selector'          => '{{WRAPPER}} .twentytwenty-overlay',
				'condition'         => [
					'overlay'  => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_overlay_hover',
			[
				'label'             => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'              => 'overlay_background_hover',
				'types'             => [ 'classic', 'gradient' ],
				'selector'          => '{{WRAPPER}} .twentytwenty-overlay:hover',
				'condition'         => [
					'overlay'  => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_style_handle_controls() {
		/**
		 * Style Tab: Handle
		 */
		$this->start_controls_section(
			'section_handle_style',
			[
				'label'             => __( 'Handle', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_handle_style' );

		$this->start_controls_tab(
			'tab_handle_normal',
			[
				'label'             => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'handle_icon_color',
			[
				'label'             => __( 'Icon Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-left-arrow' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}} .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'              => 'handle_background',
				'types'             => [ 'classic', 'gradient' ],
				'selector'          => '{{WRAPPER}} .twentytwenty-handle',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'handle_border',
				'label'             => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .twentytwenty-handle',
				'separator'         => 'before',
			]
		);

		$this->add_control(
			'handle_border_radius',
			[
				'label'             => __( 'Border Radius', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-handle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'handle_box_shadow',
				'selector'              => '{{WRAPPER}} .twentytwenty-handle',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_handle_hover',
			[
				'label'             => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'handle_icon_color_hover',
			[
				'label'             => __( 'Icon Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-handle:hover .twentytwenty-left-arrow' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}} .twentytwenty-handle:hover .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'              => 'handle_background_hover',
				'types'             => [ 'classic', 'gradient' ],
				'selector'          => '{{WRAPPER}} .twentytwenty-handle:hover',
			]
		);

		$this->add_control(
			'handle_border_color_hover',
			[
				'label'             => __( 'Border Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-handle:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_style_divider_controls() {
		/**
		 * Style Tab: Divider
		 */
		$this->start_controls_section(
			'section_divider_style',
			[
				'label'             => __( 'Divider', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'             => __( 'Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:after, {{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-vertical .twentytwenty-handle:after' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'divider_width',
			[
				'label'             => __( 'Width', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'default'           => [
					'size' => 3,
					'unit' => 'px',
				],
				'size_units'        => [ 'px', '%' ],
				'range'             => [
					'px' => [
						'max' => 20,
					],
				],
				'tablet_default'    => [
					'unit' => 'px',
				],
				'mobile_default'    => [
					'unit' => 'px',
				],
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:before, {{WRAPPER}} .twentytwenty-horizontal .twentytwenty-handle:after' => 'width: {{SIZE}}{{UNIT}}; margin-left: calc(-{{SIZE}}{{UNIT}}/2);',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_label_controls() {
		/**
		 * Style Tab: Label
		 */
		$this->start_controls_section(
			'section_label_style',
			[
				'label'             => __( 'Label', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_horizontal_position',
			[
				'label'                 => __( 'Position', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'default'               => 'top',
				'options'               => [
					'top'          => [
						'title'    => __( 'Top', 'powerpack' ),
						'icon'     => 'eicon-v-align-top',
					],
					'middle'       => [
						'title'    => __( 'Middle', 'powerpack' ),
						'icon'     => 'eicon-v-align-middle',
					],
					'bottom'       => [
						'title'    => __( 'Bottom', 'powerpack' ),
						'icon'     => 'eicon-v-align-bottom',
					],
				],
				'prefix_class'          => 'pp-ic-label-horizontal-',
				'condition'             => [
					'orientation'  => 'horizontal',
				],
			]
		);

		$this->add_control(
			'label_vertical_position',
			[
				'label'                 => __( 'Position', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
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
				],
				'default'               => 'center',
				'prefix_class'  => 'pp-ic-label-vertical-',
				'condition'             => [
					'orientation'  => 'vertical',
				],
			]
		);

		$this->add_responsive_control(
			'label_align',
			[
				'label'             => __( 'Align', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'size_units'        => [ 'px', '%' ],
				'range'             => [
					'px' => [
						'max' => 200,
					],
				],
				'selectors'         => [
					'{{WRAPPER}}.pp-ic-label-horizontal-top .twentytwenty-horizontal .twentytwenty-before-label:before,
                    {{WRAPPER}}.pp-ic-label-horizontal-top .twentytwenty-horizontal .twentytwenty-after-label:before' => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-before-label:before' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .twentytwenty-horizontal .twentytwenty-after-label:before' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-ic-label-horizontal-bottom .twentytwenty-horizontal .twentytwenty-before-label:before,
                    {{WRAPPER}}.pp-ic-label-horizontal-bottom .twentytwenty-horizontal .twentytwenty-after-label:before' => 'bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .twentytwenty-vertical .twentytwenty-before-label:before' => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .twentytwenty-vertical .twentytwenty-after-label:before' => 'bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-ic-label-vertical-left .twentytwenty-vertical .twentytwenty-before-label:before,
                    {{WRAPPER}}.pp-ic-label-vertical-left .twentytwenty-vertical .twentytwenty-after-label:before' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-ic-label-vertical-right .twentytwenty-vertical .twentytwenty-before-label:before,
                    {{WRAPPER}}.pp-ic-label-vertical-right .twentytwenty-vertical .twentytwenty-after-label:before' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_label_style' );

		$this->start_controls_tab(
			'tab_label_before',
			[
				'label'             => __( 'Before', 'powerpack' ),
			]
		);

		$this->add_control(
			'label_text_color_before',
			[
				'label'             => __( 'Text Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-before-label:before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'label_bg_color_before',
			[
				'label'             => __( 'Background Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-before-label:before' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'label_border',
				'label'             => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .twentytwenty-before-label:before',
			]
		);

		$this->add_control(
			'label_border_radius',
			[
				'label'             => __( 'Border Radius', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-before-label:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_label_after',
			[
				'label'             => __( 'After', 'powerpack' ),
			]
		);

		$this->add_control(
			'label_text_color_after',
			[
				'label'             => __( 'Text Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-after-label:before' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'label_bg_color_after',
			[
				'label'             => __( 'Background Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-after-label:before' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'label_border_after',
				'label'             => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .twentytwenty-after-label:before',
			]
		);

		$this->add_control(
			'label_border_radius_after',
			[
				'label'             => __( 'Border Radius', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-after-label:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'label_typography',
				'label'             => __( 'Typography', 'powerpack' ),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .twentytwenty-before-label:before, {{WRAPPER}} .twentytwenty-after-label:before',
				'separator'         => 'before',
			]
		);

		$this->add_responsive_control(
			'label_padding',
			[
				'label'             => __( 'Padding', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .twentytwenty-before-label:before, {{WRAPPER}} .twentytwenty-after-label:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'         => 'before',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render image comparison widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$widget_options = [
			'visible_ratio'      => ( $settings['visible_ratio']['size'] ) ? $settings['visible_ratio']['size'] : '0.5',
			'orientation'        => ( $settings['orientation'] ) ? $settings['orientation'] : 'vertical',
			'before_label'       => ( $settings['before_label'] ) ? esc_attr( $settings['before_label'] ) : '',
			'after_label'        => ( $settings['after_label'] ) ? esc_attr( $settings['after_label'] ) : '',
			'slider_on_hover'    => 'mouse_move' === $settings['move_slider'] ? true : false,
			'slider_with_handle' => 'drag' === $settings['move_slider'] ? true : false,
			'slider_with_click'  => 'mouse_click' === $settings['move_slider'] ? true : false,
			'no_overlay'         => ( 'yes' === $settings['overlay'] ) ? false : true,
		];

		$this->add_render_attribute( 'image-comparison', [
			'class'         => 'pp-image-comparison',
			'id'            => 'pp-image-comparison-' . esc_attr( $this->get_id() ),
			'data-settings' => wp_json_encode( $widget_options ),
		] );
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'image-comparison' ) ); ?>>
			<?php
			if ( '' !== $settings['before_image']['url'] ) :

				$this->add_render_attribute( 'before-image', 'src', $settings['before_image']['url'] );
				$this->add_render_attribute( 'before-image', 'alt', Control_Media::get_image_alt( $settings['before_image'] ) );
				$this->add_render_attribute( 'before-image', 'title', Control_Media::get_image_title( $settings['before_image'] ) );
				?>
				<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'before-image' ) ); ?> />
				<?php
				endif;

			if ( '' !== $settings['after_image']['url'] ) :

				$this->add_render_attribute( 'after-image', 'src', $settings['after_image']['url'] );
				$this->add_render_attribute( 'after-image', 'alt', Control_Media::get_image_alt( $settings['after_image'] ) );
				$this->add_render_attribute( 'after-image', 'title', Control_Media::get_image_title( $settings['after_image'] ) );
				?>
				<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'after-image' ) ); ?> />
				<?php
				endif;
			?>
		</div>
		<?php
	}

	/**
	 * Render image comparison widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.3.2
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			var visible_ratio       = ( settings.visible_ratio.size != '' ) ? settings.visible_ratio.size : '0.5';
			var slider_on_hover     = ( settings.move_slider == 'mouse_move' ) ? true : false;
			var slider_with_handle  = ( settings.move_slider == 'drag' ) ? true : false;
			var slider_with_click   = ( settings.move_slider == 'mouse_click' ) ? true : false;
			var no_overlay          = ( settings.overlay == 'yes' ) ? false : true;
		#>
		<div class="pp-image-comparison" data-settings='{ "visible_ratio":{{ visible_ratio }},"orientation":"{{ settings.orientation }}","before_label":"{{ settings.before_label }}","after_label":"{{ settings.after_label }}","slider_on_hover":{{ slider_on_hover }},"slider_with_handle":{{ slider_with_handle }},"slider_with_click":{{ slider_with_click }},"no_overlay":{{ no_overlay }} }'>
			<# if ( settings.before_image.url != '' ) { #>
				<img src="{{ settings.before_image.url }}">
			<# } #>

			<# if ( settings.after_image.url != '' ) { #>
				<img src="{{ settings.after_image.url }}">
			<# } #>
		</div>
		<?php
	}
}
