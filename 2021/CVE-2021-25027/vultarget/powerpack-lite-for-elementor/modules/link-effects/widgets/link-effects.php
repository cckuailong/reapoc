<?php
namespace PowerpackElementsLite\Modules\LinkEffects\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Link Effects Widget
 */
class Link_Effects extends Powerpack_Widget {

	/**
	 * Retrieve link effects widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Link_Effects' );
	}

	/**
	 * Retrieve link effects widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Link_Effects' );
	}

	/**
	 * Retrieve link effects widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Link_Effects' );
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
		return parent::get_widget_keywords( 'Link_Effects' );
	}

	/**
	 * Register link effects widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register link effects widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.3.2
	 * @access protected
	 */
	protected function register_controls() {

		/*-----------------------------------------------------------------------------------*/
		/*	CONTENT TAB
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Content Tab: Link Effects
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_link_effects',
			[
				'label'                 => __( 'Link Effects', 'powerpack' ),
			]
		);

		$this->add_control(
			'text',
			[
				'label'                 => __( 'Text', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'label_block'           => true,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => __( 'Click Here', 'powerpack' ),
			]
		);

		$this->add_control(
			'secondary_text',
			[
				'label'                 => __( 'Secondary Text', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => __( 'Click Here', 'powerpack' ),
				'condition'             => [
					'effect'    => 'effect-9',
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label'                 => __( 'Link', 'powerpack' ),
				'type'                  => Controls_Manager::URL,
				'dynamic'               => [
					'active'   => true,
				],
				'placeholder'           => 'https://www.your-link.com',
				'default'               => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'effect',
			[
				'label'                 => __( 'Effect', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'effect-1'  => __( 'Bottom Border Slides In', 'powerpack' ),
					'effect-2'  => __( 'Bottom Border Slides Out', 'powerpack' ),
					'effect-3'  => __( 'Brackets', 'powerpack' ),
					'effect-4'  => __( '3D Rolling Cube', 'powerpack' ),
					'effect-5'  => __( 'Same Word Slide In', 'powerpack' ),
					'effect-6'  => __( 'Right Angle Slides Down over Title', 'powerpack' ),
					'effect-7'  => __( 'Second Border Slides Up', 'powerpack' ),
					'effect-8'  => __( 'Border Slight Translate', 'powerpack' ),
					'effect-9'  => __( 'Second Text and Borders', 'powerpack' ),
					'effect-10' => __( 'Push Out', 'powerpack' ),
					'effect-11' => __( 'Text Fill', 'powerpack' ),
					'effect-12' => __( 'Circle', 'powerpack' ),
					'effect-13' => __( 'Three Circles', 'powerpack' ),
					'effect-14' => __( 'Border Switch', 'powerpack' ),
					'effect-15' => __( 'Scale Down', 'powerpack' ),
					'effect-16' => __( 'Fall Down', 'powerpack' ),
					'effect-17' => __( 'Move Up and Push Border', 'powerpack' ),
					'effect-18' => __( 'Cross', 'powerpack' ),
					'effect-19' => __( '3D Side', 'powerpack' ),
					'effect-20' => __( 'Unfold', 'powerpack' ),
					'effect-21' => __( 'Borders Slight Yranslate', 'powerpack' ),
				],
				'default'               => 'effect-1',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'                 => __( 'Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
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
					'justify'   => [
						'title' => __( 'Justified', 'powerpack' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}}'   => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$help_docs = PP_Config::get_widget_help_links( 'Link_Effects' );

		if ( ! empty( $help_docs ) ) {

			/**
			 * Content Tab: Help Docs
			 *
			 * @since 2.4.0
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

		/*-----------------------------------------------------------------------------------*/
		/*	STYLE TAB
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Style Tab: Link Effects
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_style',
			[
				'label'                 => __( 'Link Effects', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} a.pp-link',
			]
		);

		$this->add_responsive_control(
			'divider_title_width',
			[
				'label'                 => __( 'Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'  => 200,
				],
				'range'                 => [
					'px' => [
						'min'   => 1,
						'max'   => 1000,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-link-effect-19' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pp-link-effect-19 span' => 'transform-origin: 50% 50% calc(-{{SIZE}}{{UNIT}}/2)',
				],
				'condition'             => [
					'effect' => 'effect-19',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_link_style' );

		$this->start_controls_tab(
			'tab_link_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'link_color_normal',
			[
				'label'                 => __( 'Link Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} a.pp-link, {{WRAPPER}} .pp-link-effect-10 span, {{WRAPPER}} .pp-link-effect-15:before, {{WRAPPER}} .pp-link-effect-16, {{WRAPPER}} .pp-link-effect-17:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color_normal',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-link-effect-4 span, {{WRAPPER}} .pp-link-effect-10 span, {{WRAPPER}} .pp-link-effect-19 span, {{WRAPPER}} .pp-link-effect-20 span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_border_color',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-link-effect-8:before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .pp-link-effect-11' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .pp-link-effect-1:after, {{WRAPPER}} .pp-link-effect-2:after, {{WRAPPER}} .pp-link-effect-6:before, {{WRAPPER}} .pp-link-effect-6:after, {{WRAPPER}} .pp-link-effect-7:before, {{WRAPPER}} .pp-link-effect-7:after, {{WRAPPER}} .pp-link-effect-14:before, {{WRAPPER}} .pp-link-effect-14:after, {{WRAPPER}} .pp-link-effect-18:before, {{WRAPPER}} .pp-link-effect-18:after' => 'background: {{VALUE}};',
					'{{WRAPPER}} .pp-link-effect-3:before, {{WRAPPER}} .pp-link-effect-3:after' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-link-effect-20 span' => 'box-shadow: inset 0 3px {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_link_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'link_color_hover',
			[
				'label'                 => __( 'Link Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} a.pp-link:hover, {{WRAPPER}} .pp-link-effect-10:before, {{WRAPPER}} .pp-link-effect-11:before, {{WRAPPER}} .pp-link-effect-15, {{WRAPPER}} .pp-link-effect-16:before, {{WRAPPER}} .pp-link-effect-20 span:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color_hover',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-link-effect-4 span:before, {{WRAPPER}} .pp-link-effect-10:before, {{WRAPPER}} .pp-link-effect-19 span:before, {{WRAPPER}} .pp-link-effect-20 span:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-link-effect-8:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .pp-link-effect-11:before' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .pp-link-effect-9:before, {{WRAPPER}} .pp-link-effect-9:after, {{WRAPPER}} .pp-link-effect-14:hover:before, {{WRAPPER}} .pp-link-effect-14:focus:before, {{WRAPPER}} .pp-link-effect-14:hover:after, {{WRAPPER}} .pp-link-effect-14:focus:after, {{WRAPPER}} .pp-link-effect-17:after, {{WRAPPER}} .pp-link-effect-18:hover:before, {{WRAPPER}} .pp-link-effect-18:focus:before, {{WRAPPER}} .pp-link-effect-18:hover:after, {{WRAPPER}} .pp-link-effect-18:focus:after, {{WRAPPER}} .pp-link-effect-21:before, {{WRAPPER}} .pp-link-effect-21:after' => 'background: {{VALUE}};',
					'{{WRAPPER}} .pp-link-effect-17' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-link-effect-13:hover:before, {{WRAPPER}} .pp-link-effect-13:focus:before' => 'color: {{VALUE}}; text-shadow: 10px 0 {{VALUE}}, -10px 0 {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_section();

	}

	/**
	 * Render link effects widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$link_text = '' !== $settings['text'] ? $settings['text'] : '';
		$link_secondary_text = ! empty( $settings['secondary_text'] ) ? $settings['secondary_text'] : '';

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );
		}

		$this->add_render_attribute( 'link', 'class', 'pp-link' );

		if ( $settings['effect'] ) {
			$this->add_render_attribute( 'link', 'class', 'pp-link-' . $settings['effect'] );
		}

		switch ( $settings['effect'] ) {
			case 'effect-4':
			case 'effect-5':
			case 'effect-19':
			case 'effect-20':
				$this->add_render_attribute( 'pp-link-text', 'data-hover', wp_strip_all_tags( $link_text ) );
				break;

			case 'effect-10':
			case 'effect-11':
			case 'effect-15':
			case 'effect-16':
			case 'effect-17':
			case 'effect-18':
				$this->add_render_attribute( 'pp-link-text-2', 'data-hover', wp_strip_all_tags( $link_text ) );
				break;
		}
		?>
		<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'pp-link-text-2' ) ); ?>>
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'pp-link-text' ) ); ?>>
				<?php echo wp_kses_post( $link_text ); ?>
			</span>
			<?php if ( 'effect-9' === $settings['effect'] ) { ?>
				<span><?php echo wp_kses_post( $link_secondary_text ); ?></span>
			<?php } ?>
		</a>
		<?php
	}

	/**
	 * Render link effects widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.4.0
	 * @access protected
	 */
	protected function content_template() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		?>
		<#
		view.addRenderAttribute( 'link', 'class', ['pp-link', 'pp-link-' + settings.effect] );

		var link = settings.link.url ? 'href="' + settings.link.url + '"' : '';

		switch ( settings.effect ) {
			case 'effect-4':
			case 'effect-5':
			case 'effect-19':
			case 'effect-20':
				view.addRenderAttribute( 'pp-link-text', 'data-hover', settings.text );
				break;

			case 'effect-10':
			case 'effect-11':
			case 'effect-15':
			case 'effect-16':
			case 'effect-17':
			case 'effect-18':
				view.addRenderAttribute( 'link', 'data-hover', settings.text );
				break;
		}
		#>
		<a {{{ view.getRenderAttributeString( 'link' ) }}} {{{ link }}}>
			<span {{{ view.getRenderAttributeString( 'pp-link-text' ) }}}>
				{{{ settings.text }}}
			</span>
			<# if ( 'effect-9' === settings.effect ) { #>
				<span>{{{ settings.secondary_text }}}</span>
			<# } #>
		</a>
		<?php
	}
}
