<?php
namespace PowerpackElementsLite\Modules\Headings\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Helper;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Dual Heading Widget
 */
class Dual_Heading extends Powerpack_Widget {

	/**
	 * Retrieve dual heading widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Dual_Heading' );
	}

	/**
	 * Retrieve dual heading widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Dual_Heading' );
	}

	/**
	 * Retrieve dual heading widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Dual_Heading' );
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
		return parent::get_widget_keywords( 'Dual_Heading' );
	}

	/**
	 * Register dual heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register dual heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.3.2
	 * @access protected
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_heading_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_first_section_controls();
		$this->register_style_second_section_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_content_heading_controls() {
		/**
		 * Content Tab: Dual Heading
		 */
		$this->start_controls_section(
			'section_dual_heading',
			[
				'label'                 => __( 'Dual Heading', 'powerpack' ),
			]
		);

		$this->add_control(
			'first_text',
			[
				'label'                 => __( 'First Part', 'powerpack' ),
				'type'                  => Controls_Manager::TEXTAREA,
				'dynamic'               => [
					'active'   => true,
				],
				'label_block'           => true,
				'rows'                  => 3,
				'default'               => __( 'Our', 'powerpack' ),
			]
		);

		$this->add_control(
			'second_text',
			[
				'label'                 => __( 'Second Part', 'powerpack' ),
				'type'                  => Controls_Manager::TEXTAREA,
				'dynamic'               => [
					'active'   => true,
				],
				'label_block'           => true,
				'rows'                  => 3,
				'default'               => __( 'Services', 'powerpack' ),
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
				'label_block'           => true,
				'placeholder'           => 'https://www.your-link.com',
			]
		);

		$this->add_control(
			'heading_html_tag',
			[
				'label'                 => __( 'HTML Tag', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'label_block'           => false,
				'default'               => 'h2',
				'options'               => [
					'h1'     => __( 'H1', 'powerpack' ),
					'h2'     => __( 'H2', 'powerpack' ),
					'h3'     => __( 'H3', 'powerpack' ),
					'h4'     => __( 'H4', 'powerpack' ),
					'h5'     => __( 'H5', 'powerpack' ),
					'h6'     => __( 'H6', 'powerpack' ),
					'div'    => __( 'div', 'powerpack' ),
					'span'   => __( 'span', 'powerpack' ),
					'p'      => __( 'p', 'powerpack' ),
				],
			]
		);

		$this->add_control(
			'second_part_display',
			[
				'label'                 => __( 'Second Part Display', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'label_block'           => false,
				'default'               => 'inline-block',
				'options'               => [
					'inline-block'  => __( 'Inline', 'powerpack' ),
					'block'         => __( 'Block', 'powerpack' ),
				],
				'prefix_class'          => 'pp-dual-heading-',
				'selectors'             => [
					'{{WRAPPER}} .pp-second-text' => 'display: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'                 => __( 'Alignment', 'powerpack' ),
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
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}}'   => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links( 'Dual_Heading' );

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

	protected function register_style_first_section_controls() {
		/**
		 * Style Tab: First Part
		 */
		$this->start_controls_section(
			'first_section_style',
			[
				'label'                 => __( 'First Part', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'first_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'scheme'                => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-first-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'first_part_bg',
				'label'                 => __( 'Background', 'powerpack' ),
				'types'                 => [ 'none', 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .pp-first-text',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'first_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_1,
				'selector'              => '{{WRAPPER}} .pp-first-text',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'first_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-first-text',
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'first_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-first-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'first_text_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-first-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'                  => 'first_text_shadow',
				'selector'              => '{{WRAPPER}} .pp-first-text',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'first_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-first-text',
				'separator'             => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_second_section_controls() {
		/**
		 * Style Tab: Second Part
		 */
		$this->start_controls_section(
			'second_section_style',
			[
				'label'                 => __( 'Second Part', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'second_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'scheme'                => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-second-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'second_part_bg',
				'label'                 => __( 'Background', 'powerpack' ),
				'types'                 => [ 'none', 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .pp-second-text',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'second_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_1,
				'selector'              => '{{WRAPPER}} .pp-second-text',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'second_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-second-text',
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'second_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-second-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'second_text_margin',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ '%', 'px' ],
				'default'               => [
					'size' => 0,
					'unit' => 'px',
				],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}}.pp-dual-heading-inline-block .pp-second-text' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-dual-heading-block .pp-second-text' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'second_text_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-second-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'                  => 'second_text_shadow',
				'selector'              => '{{WRAPPER}} .pp-second-text',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'second_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-second-text',
				'separator'             => 'before',
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render dual heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'dual-heading', 'class', 'pp-dual-heading' );
		$this->add_inline_editing_attributes( 'first_text', 'basic' );
		$this->add_render_attribute( 'first_text', 'class', 'pp-first-text' );
		$this->add_inline_editing_attributes( 'second_text', 'basic' );
		$this->add_render_attribute( 'second_text', 'class', 'pp-second-text' );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'dual-heading-link', $settings['link'] );
		}

		if ( $settings['first_text'] || $settings['second_text'] ) {
			$heading_html_tag = PP_Helper::validate_html_tag( $settings['heading_html_tag'] );
			?>
			<<?php echo esc_html( $heading_html_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( 'dual-heading' ) ); ?>>
				<?php
				if ( ! empty( $settings['link']['url'] ) ) { ?>
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'dual-heading-link' ) ); ?>>
					<?php
				}

				if ( $settings['first_text'] ) {
					?>
					<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'first_text' ) ); ?>>
						<?php echo $this->parse_text_editor( $settings['first_text'] ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<?php
				}
				if ( $settings['second_text'] ) {
					?>
					<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'second_text' ) ); ?>>
						<?php echo $this->parse_text_editor( $settings['second_text'] ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<?php
				}

				if ( ! empty( $settings['link']['url'] ) ) { ?>
					</a>
				<?php } ?>
			</<?php echo esc_html( $heading_html_tag ); ?>>
			<?php
		}
	}

	/**
	 * Render dual heading widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		?>
		<{{{settings.heading_html_tag}}} class="pp-dual-heading">
			<# if ( settings.link.url ) { #><a href="{{settings.link.url}}"><# } #>
				<#
				if ( settings.first_text != '' ) {
					var first_text = settings.first_text;

					view.addRenderAttribute( 'first_text', 'class', 'pp-first-text' );

					view.addInlineEditingAttributes( 'first_text' );

					var first_text_html = '<span' + ' ' + view.getRenderAttributeString( 'first_text' ) + '>' + first_text + '</span>';

					print( first_text_html );
				}

				if ( settings.second_text != '' ) {
					var second_text = settings.second_text;

					view.addRenderAttribute( 'second_text', 'class', 'pp-second-text' );

					view.addInlineEditingAttributes( 'second_text' );

					var second_text_html = '<span' + ' ' + view.getRenderAttributeString( 'second_text' ) + '>' + second_text + '</span>';

					print( second_text_html );
				}
				#>
			<# if ( settings.link.url ) { #></a><# } #>
		</{{{settings.heading_html_tag}}}>
		<?php
	}
}
