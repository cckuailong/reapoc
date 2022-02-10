<?php
/**
 * PowerPack Image Hotspots Widget
 *
 * @package PPE
 */

namespace PowerpackElementsLite\Modules\Hotspots\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography as Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Image Hotspots Widget
 */
class Hotspots extends Powerpack_Widget {

	/**
	 * Retrieve image hotspots widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Hotspots' );
	}

	/**
	 * Retrieve image hotspots widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Hotspots' );
	}

	/**
	 * Retrieve image hotspots widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Hotspots' );
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
		return parent::get_widget_keywords( 'Hotspots' );
	}

	/**
	 * Retrieve the list of scripts the image hotspots widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'pp-tooltipster',
			'powerpack-frontend',
		);
	}

	/**
	 * Register image hotspots widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * Remove this after Elementor v3.4.0
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register image hotspots widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.1.4
	 * @access protected
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_image_controls();
		$this->register_content_hotspots_controls();
		$this->register_content_tooltip_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_image_controls();
		$this->register_style_hotspot_controls();
		$this->register_style_tooltip_controls();
	}

	protected function register_content_image_controls() {
		/**
		 * Content Tab: Image
		 */
		$this->start_controls_section(
			'section_image',
			array(
				'label' => __( 'Image', 'powerpack' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'   => __( 'Choose Image', 'powerpack' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'image',
				'label'   => __( 'Image Size', 'powerpack' ),
				'default' => 'full',
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_hotspots_controls() {
		/**
		 * Content Tab: Hotspots
		 */
		$this->start_controls_section(
			'section_hotspots',
			array(
				'label' => __( 'Hotspots', 'powerpack' ),
			)
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'hot_spots_tabs' );

		$repeater->start_controls_tab( 'tab_content', array( 'label' => __( 'General', 'powerpack' ) ) );

			$repeater->add_control(
				'hotspot_admin_label',
				array(
					'label'       => __( 'Admin Label', 'powerpack' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => false,
					'default'     => '',
				)
			);

			$repeater->add_control(
				'hotspot_type',
				array(
					'label'   => __( 'Type', 'powerpack' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'icon',
					'options' => array(
						'icon'  => __( 'Icon', 'powerpack' ),
						'text'  => __( 'Text', 'powerpack' ),
						'blank' => __( 'Blank', 'powerpack' ),
					),
				)
			);

			$repeater->add_control(
				'selected_icon',
				array(
					'label'            => __( 'Icon', 'powerpack' ),
					'type'             => Controls_Manager::ICONS,
					'label_block'      => false,
					'default'          => array(
						'value'   => 'fas fa-plus',
						'library' => 'fa-solid',
					),
					'fa4compatibility' => 'hotspot_icon',
					'skin'             => 'inline',
					'conditions'       => array(
						'terms' => array(
							array(
								'name'     => 'hotspot_type',
								'operator' => '==',
								'value'    => 'icon',
							),
						),
					),
				)
			);

			$repeater->add_control(
				'hotspot_text',
				array(
					'label'       => __( 'Text', 'powerpack' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => false,
					'default'     => '#',
					'conditions'  => array(
						'terms' => array(
							array(
								'name'     => 'hotspot_type',
								'operator' => '==',
								'value'    => 'text',
							),
						),
					),
				)
			);

			$repeater->add_control(
				'left_position',
				array(
					'label'     => __( 'Left Position (%)', 'powerpack' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 0.1,
						),
					),
					'default'   => [
						'unit' => 'px',
						'size' => 20,
					],
					'separator' => 'before',
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}}' => 'left: {{SIZE}}%;',
					),
				)
			);

			$repeater->add_control(
				'top_position',
				array(
					'label'     => __( 'Top Position (%)', 'powerpack' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 0.1,
						),
					),
					'default'   => [
						'unit' => 'px',
						'size' => 20,
					],
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}}' => 'top: {{SIZE}}%;',
					),
				)
			);

			$repeater->add_control(
				'hotspot_link',
				array(
					'label'       => __( 'Link', 'powerpack' ),
					'description' => __( 'Works only when tolltips\' Trigger is set to Hover or if tooltip is disabled.', 'powerpack' ),
					'type'        => Controls_Manager::URL,
					'dynamic'     => array(
						'active' => true,
					),
					'placeholder' => 'https://www.your-link.com',
					'default'     => array(
						'url' => '#',
					),
					'separator'   => 'before',
				)
			);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'tab_position', array( 'label' => __( 'Tooltip', 'powerpack' ) ) );

			$repeater->add_control(
				'tooltip',
				array(
					'label'        => __( 'Tooltip', 'powerpack' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => '',
					'label_on'     => __( 'Show', 'powerpack' ),
					'label_off'    => __( 'Hide', 'powerpack' ),
					'return_value' => 'yes',
				)
			);

			$repeater->add_control(
				'tooltip_position_local',
				array(
					'label'      => __( 'Tooltip Position', 'powerpack' ),
					'type'       => Controls_Manager::SELECT,
					'default'    => 'global',
					'options'    => array(
						'global'       => __( 'Global', 'powerpack' ),
						'top'          => __( 'Top', 'powerpack' ),
						'bottom'       => __( 'Bottom', 'powerpack' ),
						'left'         => __( 'Left', 'powerpack' ),
						'right'        => __( 'Right', 'powerpack' ),
					),
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'tooltip',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$repeater->add_control(
				'tooltip_content',
				array(
					'label'      => __( 'Tooltip Content', 'powerpack' ),
					'type'       => Controls_Manager::WYSIWYG,
					'default'    => __( 'Tooltip Content', 'powerpack' ),
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'tooltip',
								'operator' => '==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab( 'tab_style', array( 'label' => __( 'Style', 'powerpack' ) ) );

			$repeater->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'hotspot_typography',
					'label'     => __( 'Typography', 'powerpack' ),
					'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}}.pp-hot-spot-wrap',
					'condition' => array(
						'hotspot_type' => 'text',
					),
				)
			);

			$repeater->add_control(
				'hotspot_color_single',
				array(
					'label'     => __( 'Color', 'powerpack' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-hot-spot-wrap, {{WRAPPER}} {{CURRENT_ITEM}} .pp-hot-spot-inner, {{WRAPPER}} {{CURRENT_ITEM}} .pp-hot-spot-inner:before' => 'color: {{VALUE}}',
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-hot-spot-wrap .pp-icon svg' => 'fill: {{VALUE}}',
					),
					'condition' => array(
						'hotspot_type!' => 'blank',
					),
				)
			);

			$repeater->add_control(
				'hotspot_bg_color_single',
				array(
					'label'     => __( 'Background Color', 'powerpack' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-hot-spot-wrap, {{WRAPPER}} {{CURRENT_ITEM}} .pp-hot-spot-inner, {{WRAPPER}} {{CURRENT_ITEM}} .pp-hot-spot-inner:before' => 'background-color: {{VALUE}}',
					),
				)
			);

			$repeater->add_control(
				'hotspot_border_color_single',
				array(
					'label'     => __( 'Border Color', 'powerpack' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}}.pp-hot-spot-wrap' => 'border-color: {{VALUE}}',
					),
				)
			);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'hot_spots',
			array(
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'hotspot_admin_label' => __( 'Hotspot #1', 'powerpack' ),
						'hotspot_text'        => __( '1', 'powerpack' ),
						'selected_icon'       => 'fa fa-plus',
						'left_position'       => 20,
						'top_position'        => 30,
					),
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ hotspot_admin_label }}}',
			)
		);

		$this->add_control(
			'hotspot_pulse',
			array(
				'label'        => __( 'Glow Effect', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'separator'    => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_tooltip_controls() {
		/**
		 * Content Tab: Tooltip Settings
		 */
		$this->start_controls_section(
			'section_tooltip',
			array(
				'label' => __( 'Tooltip Settings', 'powerpack' ),
			)
		);

		$this->add_control(
			'tooltip_always_open',
			array(
				'label'              => __( 'Always Open?', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'no',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'tooltip_trigger',
			array(
				'label'              => __( 'Trigger', 'powerpack' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'hover',
				'options'            => array(
					'hover' => __( 'Hover', 'powerpack' ),
					'click' => __( 'Click', 'powerpack' ),
				),
				'frontend_available' => true,
				'condition' => array(
					'tooltip_always_open!' => 'yes',
				),
			)
		);

		$this->add_control(
			'tooltip_size',
			array(
				'label'   => __( 'Size', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'powerpack' ),
					'tiny'    => __( 'Tiny', 'powerpack' ),
					'small'   => __( 'Small', 'powerpack' ),
					'large'   => __( 'Large', 'powerpack' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'tooltip_position',
			array(
				'label'   => __( 'Global Position', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'          => __( 'Top', 'powerpack' ),
					'bottom'       => __( 'Bottom', 'powerpack' ),
					'left'         => __( 'Left', 'powerpack' ),
					'right'        => __( 'Right', 'powerpack' ),
				),
			)
		);

		$this->add_control(
			'distance',
			array(
				'label'       => __( 'Distance', 'powerpack' ),
				'description' => __( 'The distance between the hotspot and the tooltip.', 'powerpack' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => array(
					'size' => '',
				),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'tooltip_arrow',
			array(
				'label'              => __( 'Show Arrow', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'tooltip_animation',
			array(
				'label'   => __( 'Animation', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => array(
					'fade'  => __( 'Fade', 'powerpack' ),
					'fall'  => __( 'Fall', 'powerpack' ),
					'grow'  => __( 'Grow', 'powerpack' ),
					'slide' => __( 'Slide', 'powerpack' ),
					'swing' => __( 'Swing', 'powerpack' ),
				),
				'frontend_available' => true,
			)
		);

		$tooltip_animations = array(
			''                  => __( 'Default', 'powerpack' ),
			'bounce'            => __( 'Bounce', 'powerpack' ),
			'flash'             => __( 'Flash', 'powerpack' ),
			'pulse'             => __( 'Pulse', 'powerpack' ),
			'rubberBand'        => __( 'rubberBand', 'powerpack' ),
			'shake'             => __( 'Shake', 'powerpack' ),
			'swing'             => __( 'Swing', 'powerpack' ),
			'tada'              => __( 'Tada', 'powerpack' ),
			'wobble'            => __( 'Wobble', 'powerpack' ),
			'bounceIn'          => __( 'bounceIn', 'powerpack' ),
			'bounceInDown'      => __( 'bounceInDown', 'powerpack' ),
			'bounceInLeft'      => __( 'bounceInLeft', 'powerpack' ),
			'bounceInRight'     => __( 'bounceInRight', 'powerpack' ),
			'bounceInUp'        => __( 'bounceInUp', 'powerpack' ),
			'bounceOut'         => __( 'bounceOut', 'powerpack' ),
			'bounceOutDown'     => __( 'bounceOutDown', 'powerpack' ),
			'bounceOutLeft'     => __( 'bounceOutLeft', 'powerpack' ),
			'bounceOutRight'    => __( 'bounceOutRight', 'powerpack' ),
			'bounceOutUp'       => __( 'bounceOutUp', 'powerpack' ),
			'fadeIn'            => __( 'fadeIn', 'powerpack' ),
			'fadeInDown'        => __( 'fadeInDown', 'powerpack' ),
			'fadeInDownBig'     => __( 'fadeInDownBig', 'powerpack' ),
			'fadeInLeft'        => __( 'fadeInLeft', 'powerpack' ),
			'fadeInLeftBig'     => __( 'fadeInLeftBig', 'powerpack' ),
			'fadeInRight'       => __( 'fadeInRight', 'powerpack' ),
			'fadeInRightBig'    => __( 'fadeInRightBig', 'powerpack' ),
			'fadeInUp'          => __( 'fadeInUp', 'powerpack' ),
			'fadeInUpBig'       => __( 'fadeInUpBig', 'powerpack' ),
			'fadeOut'           => __( 'fadeOut', 'powerpack' ),
			'fadeOutDown'       => __( 'fadeOutDown', 'powerpack' ),
			'fadeOutDownBig'    => __( 'fadeOutDownBig', 'powerpack' ),
			'fadeOutLeft'       => __( 'fadeOutLeft', 'powerpack' ),
			'fadeOutLeftBig'    => __( 'fadeOutLeftBig', 'powerpack' ),
			'fadeOutRight'      => __( 'fadeOutRight', 'powerpack' ),
			'fadeOutRightBig'   => __( 'fadeOutRightBig', 'powerpack' ),
			'fadeOutUp'         => __( 'fadeOutUp', 'powerpack' ),
			'fadeOutUpBig'      => __( 'fadeOutUpBig', 'powerpack' ),
			'flip'              => __( 'flip', 'powerpack' ),
			'flipInX'           => __( 'flipInX', 'powerpack' ),
			'flipInY'           => __( 'flipInY', 'powerpack' ),
			'flipOutX'          => __( 'flipOutX', 'powerpack' ),
			'flipOutY'          => __( 'flipOutY', 'powerpack' ),
			'lightSpeedIn'      => __( 'lightSpeedIn', 'powerpack' ),
			'lightSpeedOut'     => __( 'lightSpeedOut', 'powerpack' ),
			'rotateIn'          => __( 'rotateIn', 'powerpack' ),
			'rotateInDownLeft'  => __( 'rotateInDownLeft', 'powerpack' ),
			'rotateInDownLeft'  => __( 'rotateInDownRight', 'powerpack' ),
			'rotateInUpLeft'    => __( 'rotateInUpLeft', 'powerpack' ),
			'rotateInUpRight'   => __( 'rotateInUpRight', 'powerpack' ),
			'rotateOut'         => __( 'rotateOut', 'powerpack' ),
			'rotateOutDownLeft' => __( 'rotateOutDownLeft', 'powerpack' ),
			'rotateOutDownLeft' => __( 'rotateOutDownRight', 'powerpack' ),
			'rotateOutUpLeft'   => __( 'rotateOutUpLeft', 'powerpack' ),
			'rotateOutUpRight'  => __( 'rotateOutUpRight', 'powerpack' ),
			'hinge'             => __( 'Hinge', 'powerpack' ),
			'rollIn'            => __( 'rollIn', 'powerpack' ),
			'rollOut'           => __( 'rollOut', 'powerpack' ),
			'zoomIn'            => __( 'zoomIn', 'powerpack' ),
			'zoomInDown'        => __( 'zoomInDown', 'powerpack' ),
			'zoomInLeft'        => __( 'zoomInLeft', 'powerpack' ),
			'zoomInRight'       => __( 'zoomInRight', 'powerpack' ),
			'zoomInUp'          => __( 'zoomInUp', 'powerpack' ),
			'zoomOut'           => __( 'zoomOut', 'powerpack' ),
			'zoomOutDown'       => __( 'zoomOutDown', 'powerpack' ),
			'zoomOutLeft'       => __( 'zoomOutLeft', 'powerpack' ),
			'zoomOutRight'      => __( 'zoomOutRight', 'powerpack' ),
			'zoomOutUp'         => __( 'zoomOutUp', 'powerpack' ),
		);

		/* $this->add_control(
			'tooltip_animation_in',
			array(
				'label'   => __( 'Animation In', 'powerpack' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => '',
				'options' => $tooltip_animations,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'tooltip_animation_out',
			array(
				'label'   => __( 'Animation Out', 'powerpack' ),
				'type'    => Controls_Manager::SELECT2,
				'default' => '',
				'options' => $tooltip_animations,
				'frontend_available' => true,
			)
		); */

		$this->add_control(
			'tooltip_zindex',
			array(
				'label'              => __( 'Z-Index', 'powerpack' ),
				'description'        => __( 'Increase the z-index value if you are unable to see the tooltip. For example: 99, 999, 9999 ', 'powerpack' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 99,
				'min'                => -9999999,
				'step'               => 1,
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links( 'Hotspots' );

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

		$this->add_responsive_control(
			'image_width',
			array(
				'label'      => __( 'Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 1200,
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
					'{{WRAPPER}} .pp-hot-spot-image' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_align',
			array(
				'label'        => __( 'Alignment', 'powerpack' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'prefix_class' => 'pp-hotspot-img-align%s-',
				'selectors'    => array(
					'{{WRAPPER}} .pp-hot-spot-image' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'image_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-hot-spot-image img',
			)
		);

		$this->add_control(
			'image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-hot-spot-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'image_box_shadow',
				'selector'  => '{{WRAPPER}} .pp-hot-spot-image img',
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_hotspot_controls() {
		/**
		 * Style Tab: Hotspot
		 */
		$this->start_controls_section(
			'section_hotspots_style',
			array(
				'label' => __( 'Hotspot', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'hotspot_icon_size',
			array(
				'label'      => __( 'Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array( 'size' => '14' ),
				'range'      => array(
					'px' => array(
						'min'  => 6,
						'max'  => 40,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-hot-spot-wrap' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'hotspots_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'selector'  => '{{WRAPPER}} .pp-hot-spot-wrap',
			)
		);

		$this->add_control(
			'icon_color_normal',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array(
					'{{WRAPPER}} .pp-hot-spot-wrap, {{WRAPPER}} .pp-hot-spot-inner, {{WRAPPER}} .pp-hot-spot-inner:before' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-hot-spot-wrap .pp-icon svg' => 'fill: {{VALUE}}',
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
					'{{WRAPPER}} .pp-hot-spot-wrap, {{WRAPPER}} .pp-hot-spot-inner, {{WRAPPER}} .pp-hot-spot-inner:before, {{WRAPPER}} .pp-hotspot-icon-wrap' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'icon_border_normal',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-hot-spot-wrap',
			)
		);

		$this->add_control(
			'icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-hot-spot-wrap, {{WRAPPER}} .pp-hot-spot-inner, {{WRAPPER}} .pp-hot-spot-inner:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pp-hot-spot-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'icon_box_shadow',
				'selector'  => '{{WRAPPER}} .pp-hot-spot-wrap',
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_tooltip_controls() {
		/**
		 * Style Tab: Tooltip
		 */
		$this->start_controls_section(
			'section_tooltips_style',
			array(
				'label' => __( 'Tooltip', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'tooltip_bg_color',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.pp-tooltip.pp-tooltip-{{ID}} .tooltipster-box' => 'background-color: {{VALUE}};',
					'.pp-tooltip.pp-tooltip-{{ID}}.tooltipster-top .tooltipster-arrow-background' => 'border-top-color: {{VALUE}};',
					'.pp-tooltip.pp-tooltip-{{ID}}.tooltipster-bottom .tooltipster-arrow-background' => 'border-bottom-color: {{VALUE}};',
					'.pp-tooltip.pp-tooltip-{{ID}}.tooltipster-left .tooltipster-arrow-background' => 'border-left-color: {{VALUE}};',
					'.pp-tooltip.pp-tooltip-{{ID}}.tooltipster-right .tooltipster-arrow-background' => 'border-right-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tooltip_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.pp-tooltip.pp-tooltip-{{ID}} .pp-tooltip-content' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tooltip_width',
			array(
				'label'     => __( 'Width', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 100,
						'max'  => 400,
						'step' => 1,
					),
				),
				'frontend_available' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tooltip_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '.pp-tooltip.pp-tooltip-{{ID}} .pp-tooltip-content',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'tooltip_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.pp-tooltip.pp-tooltip-{{ID}} .tooltipster-box',
			)
		);

		$this->add_control(
			'tooltip_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'.pp-tooltip.pp-tooltip-{{ID}} .tooltipster-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'.pp-tooltip.pp-tooltip-{{ID}} .tooltipster-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'tooltip_box_shadow',
				'selector' => '.pp-tooltip.pp-tooltip-{{ID}} .tooltipster-box',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings          = $this->get_settings_for_display();
		$fallback_defaults = array(
			'fa fa-check',
			'fa fa-times',
			'fa fa-dot-circle-o',
		);

		if ( empty( $settings['image']['url'] ) ) {
			return;
		}
		?>
		<div class="pp-image-hotspots">
			<div class="pp-hot-spot-image">
				<?php
				foreach ( $settings['hot_spots'] as $index => $item ) :

					$hotspot_tag         = 'span';
					$hotspot_key         = $this->get_repeater_setting_key( 'hotspot', 'hot_spots', $index );
					$tooltip_content_key = $this->get_repeater_setting_key( 'tooltip_content', 'hot_spots', $index );
					$tooltip_content_id  = $this->get_id() . '-' . $item['_id'];
					$hotspot_inner_key   = $this->get_repeater_setting_key( 'hotspot-inner', 'hot_spots', $index );
					$link_key            = $this->get_repeater_setting_key( 'link', 'hot_spots', $index );

					$this->add_render_attribute(
						$hotspot_key,
						'class',
						array(
							'pp-hot-spot-wrap',
							'elementor-repeater-item-' . esc_attr( $item['_id'] ),
						)
					);

					if ( 'yes' === $item['tooltip'] && $item['tooltip_content'] ) {
						if ( 'global' !== $item['tooltip_position_local'] ) {
							$tooltip_position = $item['tooltip_position_local'];
						} else {
							$tooltip_position = $settings['tooltip_position'];
						}

						$this->add_render_attribute(
							$tooltip_content_key,
							array(
								'class' => [ 'pp-tooltip-content', 'pp-tooltip-content-' . $this->get_id() ],
								'id'    => 'pp-tooltip-content-' . $tooltip_content_id,
							)
						);

						$this->add_render_attribute(
							$hotspot_key,
							array(
								'class'                 => 'pp-hot-spot-tooptip',
								'data-tooltip'          => 'yes',
								'data-tooltip-position' => $tooltip_position,
								'data-tooltip-content'  => '#pp-tooltip-content-' . $tooltip_content_id,
							)
						);

						if ( $settings['tooltip_width'] ) {
							$this->add_render_attribute( $hotspot_key, 'data-tooltip-width', $settings['tooltip_width']['size'] );
						}
					}

					$this->add_render_attribute( $hotspot_inner_key, 'class', 'pp-hot-spot-inner' );

					if ( 'yes' === $settings['hotspot_pulse'] ) {
						$this->add_render_attribute( $hotspot_inner_key, 'class', 'hotspot-animation' );
					}

					$migration_allowed = Icons_Manager::is_migration_allowed();

					// add old default
					if ( ! isset( $item['hotspot_icon'] ) && ! $migration_allowed ) {
						$item['hotspot_icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-plus';
					}

					$migrated = isset( $item['__fa4_migrated']['selected_icon'] );
					$is_new   = ! isset( $item['hotspot_icon'] ) && $migration_allowed;
					?>
					<?php
					if ( $item['hotspot_link']['url'] ) {
						if ( 'yes' !== $item['tooltip'] || ( 'yes' === $item['tooltip'] && 'hover' === $settings['tooltip_trigger'] ) ) {

							$hotspot_tag = 'a';

							$this->add_link_attributes( $hotspot_key, $item['hotspot_link'] );

						}
					}

					?>
					<<?php echo esc_html( $hotspot_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( $hotspot_key ) ); ?>>
						<span <?php echo wp_kses_post( $this->get_render_attribute_string( $hotspot_inner_key ) ); ?>>
							<span class="pp-hotspot-icon-wrap">
							<?php
							if ( 'icon' === $item['hotspot_type'] ) {
								if ( ! empty( $item['hotspot_icon'] ) || ( ! empty( $item['selected_icon']['value'] ) && $is_new ) ) {
									?>
									<span class="pp-hotspot-icon pp-icon">
										<?php
										if ( $is_new || $migrated ) {
											Icons_Manager::render_icon( $item['selected_icon'], array( 'aria-hidden' => 'true' ) );
										} else {
											?>
											<i class="<?php echo esc_attr( $item['hotspot_icon'] ); ?>" aria-hidden="true"></i>
											<?php
										}
										?>
									</span>
									<?php
								}
							} elseif ( 'text' === $item['hotspot_type'] ) { ?>
								<span class="pp-hotspot-icon-wrap">
									<span class="pp-hotspot-text">
										<?php echo esc_attr( $item['hotspot_text'] ); ?>
									</span>
								</span>
								<?php
							}
							?>
							</span>
						</span>
					</<?php echo esc_html( $hotspot_tag ); ?>>
					<?php if ( 'yes' === $item['tooltip'] && $item['tooltip_content'] ) { ?>
						<div class="pp-tooltip-container">
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( $tooltip_content_key ) ); ?>>
								<?php echo wp_kses_post( $item['tooltip_content'] ); ?>
							</div>
						</div>
					<?php } ?>
				<?php endforeach; ?>

				<?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings ) ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render image hotspots widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.0.3
	 * @access protected
	 */
	protected function content_template() {
		?>
		<#
			var i = 1;
		#>
		<div class="pp-image-hotspots">
			<div class="pp-hot-spot-image">
				<# _.each( settings.hot_spots, function( item, index ) {
				   
					var hotspotTag 			= 'span',
						tooltipContentId    = view.$el.data('id') + '-' + item._id;
						hotspotAnimation	= ( settings.hotspot_pulse == 'yes' ) ? 'hotspot-animation' : '',
						ttPosition			= '',
						iconsHTML			= {},
						migrated			= {};

					var hotspotKey 			= view.getRepeaterSettingKey( 'hotspot', 'hot_spots', index ),
						tooltipContentKey   = view.getRepeaterSettingKey( 'tooltip_content', 'hot_spots', index );

					view.addRenderAttribute(
						hotspotKey,
						{
							'class': [
								'pp-hot-spot-wrap',
								'elementor-repeater-item-' + item._id
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

					if ( item.tooltip_position_local != 'global' ) {
						ttPosition = item.tooltip_position_local;
					} else {
						ttPosition = settings.tooltip_position;
					}

					if ( item.tooltip == 'yes' ) {
						view.addRenderAttribute(
							hotspotKey,
							{
								'class': 'pp-hot-spot-tooptip',
								'data-tooltip': 'yes',
								'data-tooltip-position': ttPosition,
								'data-tooltip-content': '#pp-tooltip-content-' + tooltipContentId,
							}
						);
					}
					#>
					<#
						if ( item.hotspot_link.url ) {
							if ( item.tooltip != 'yes' || ( item.tooltip == 'yes' && settings.tooltip_trigger == 'hover' ) ) {
								hotspotTag = 'a';

								if ( item.hotspot_link.is_external ) {
									view.addRenderAttribute( hotspotKey, 'target', '_blank' );
								}

								if ( item.hotspot_link.nofollow ) {
									view.addRenderAttribute( hotspotKey, 'rel', 'nofollow' );
								}
							}
						}
					#>
					<{{ hotspotTag }} {{{ view.getRenderAttributeString( hotspotKey ) }}}>
						<span class="pp-hot-spot-inner {{ hotspotAnimation }}">
							<# if ( item.hotspot_type == 'icon' ) { #>
								<span class="pp-hotspot-icon-wrap">
									<span class="pp-hotspot-icon pp-icon">
										<#
											iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.selected_icon, { 'aria-hidden': true }, 'i', 'object' );
											migrated[ index ] = elementor.helpers.isIconMigrated( item, 'selected_icon' );
											if ( iconsHTML[ index ] && iconsHTML[ index ].rendered && ( ! item.hotspot_icon || migrated[ index ] ) ) { #>
												{{{ iconsHTML[ index ].value }}}
											<# } else { #>
												<i class="{{ item.hotspot_icon }}" aria-hidden="true"></i>
											<# }
										#>
									</span>
								</span>
							<# } else if ( item.hotspot_type == 'text' ) { #>
								<span class="pp-hotspot-icon-wrap">
									<span class="pp-hotspot-icon">{{ item.hotspot_text }}</span>
								</span>
							<# } #>
						</span>
					</{{ hotspotTag }}>
					<# if ( 'yes' === item.tooltip && item.tooltip_content ) { #>
						<div class="pp-tooltip-container">
							<div {{{ view.getRenderAttributeString( tooltipContentKey ) }}}>
								{{ item.tooltip_content }}
							</div>
						</div>
					<# } #>
				<# i++ } ); #>

				<# if ( settings.image.url != '' ) { #>
					<#
					var image = {
						id: settings.image.id,
						url: settings.image.url,
						size: settings.thumbnail_size,
						dimension: settings.thumbnail_custom_dimension,
						model: view.getEditModel()
					};
					var image_url = elementor.imagesManager.getImageUrl( image );
					#>
					<img src="{{{ image_url }}}" />
				<# } #>
			</div>
		</div>
		<?php
	}
}
