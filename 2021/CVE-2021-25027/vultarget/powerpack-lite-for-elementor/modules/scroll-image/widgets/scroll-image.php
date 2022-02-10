<?php
namespace PowerpackElementsLite\Modules\ScrollImage\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Config;
use PowerpackElementsLite\Modules\ScrollImage\Module;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Embed;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Scroll Image Widget
 */
class Scroll_Image extends Powerpack_Widget {

	/**
	 * Retrieve showcase widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'pp-scroll-image';
	}

	/**
	 * Retrieve showcase widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Scroll Image', 'powerpack' );
	}

	/**
	 * Retrieve the list of categories the showcase widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'power-pack' );
	}

	/**
	 * Retrieve showcase widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'ppicon-scroll-image power-pack-admin-icon';
	}

	/**
	 * Retrieve the list of scripts the showcase widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'imagesloaded',
			'powerpack-frontend',
		);
	}

	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		/**
		 * Content Tab: Image
		 */
		$this->start_controls_section(
			'image_settings',
			array(
				'label' => __( 'Image', 'powerpack' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label'       => __( 'Image', 'powerpack' ),
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'label_block' => true,
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

		$this->add_responsive_control(
			'image_height',
			array(
				'label'      => __( 'Image Height', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'vh' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 300,
				),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 800,
					),
					'em' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pp-image-scroll-container' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => __( 'URL', 'powerpack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => 'https://powerpackelements.com/',
				'label_block' => true,
			)
		);

		$this->add_control(
			'icon_heading',
			array(
				'label'     => __( 'Icon', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'selected_icon',
			array(
				'label'            => __( 'Cover', 'powerpack' ) . ' ' . __( 'Icon', 'powerpack' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
			)
		);

		$this->add_control(
			'icon_size',
			array(
				'label'      => __( 'Icon Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default'    => array(
					'size' => 30,
				),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pp-image-scroll-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'selected_icon[value]!' => '',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Content Tab: Settings
		 */
		$this->start_controls_section(
			'settings',
			array(
				'label' => __( 'Settings', 'powerpack' ),
			)
		);

		$this->add_control(
			'trigger_type',
			array(
				'label'              => __( 'Trigger', 'powerpack' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'hover'  => __( 'Hover', 'powerpack' ),
					'scroll' => __( 'Mouse Scroll', 'powerpack' ),
				),
				'default'            => 'hover',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'duration_speed',
			array(
				'label'     => __( 'Scroll Speed', 'powerpack' ),
				'title'     => __( 'In seconds', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3,
				'selectors' => array(
					'{{WRAPPER}} .pp-image-scroll-container .pp-image-scroll-image img'   => 'transition: all {{Value}}s; -webkit-transition: all {{Value}}s;',
				),
				'condition' => array(
					'trigger_type' => 'hover',
				),
			)
		);

		$this->add_control(
			'direction_type',
			array(
				'label'              => __( 'Scroll Direction', 'powerpack' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => array(
					'horizontal' => __( 'Horizontal', 'powerpack' ),
					'vertical'   => __( 'Vertical', 'powerpack' ),
				),
				'default'            => 'vertical',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'reverse',
			array(
				'label'              => __( 'Reverse Direction', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => array(
					'trigger_type' => 'hover',
				),
			)
		);

		$this->end_controls_section();

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

		$this->add_control(
			'help_doc_1',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				/* translators: %1$s doc link */
				'raw'             => sprintf( __( '%1$s Watch Video Overview %2$s', 'powerpack' ), '<a href="https://www.youtube.com/watch?v=eduATa8FPpU&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj" target="_blank" rel="noopener">', '</a>' ),
				'content_classes' => 'pp-editor-doc-links',
			)
		);

		$this->end_controls_section();

		if ( ! is_pp_elements_active() ) {
			/**
			 * Content Tab: Upgrade PowerPack
			 *
			 * @since 1.2.9.4
			 */
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

		/*
		-----------------------------------------------------------------------------------*/
		/*
		STYLE TAB
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Style Tab: Image
		 */
		$this->start_controls_section(
			'image_style',
			array(
				'label' => __( 'Image', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Icon Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pp-image-scroll-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-image-scroll-icon svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'selected_icon[value]!' => '',
				),
			)
		);

		$this->start_controls_tabs( 'image_style_tabs' );

		$this->start_controls_tab(
			'image_style_tab_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .pp-image-scroll-wrap',
			)
		);

		$this->add_control(
			'image_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-image-scroll-wrap, {{WRAPPER}} .pp-container-scroll' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_box_shadow',
				'selector' => '{{WRAPPER}} .pp-image-scroll-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .pp-image-scroll-container .pp-image-scroll-image img',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'image_style_tab_hover',
			array(
				'label' => __( 'Hover', 'powerpack' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_box_shadow_hover',
				'selector' => '{{WRAPPER}} .pp-image-scroll-wrap:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .pp-image-scroll-container .pp-image-scroll-image img:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Overlay
		 */
		$this->start_controls_section(
			'overlay_style',
			array(
				'label' => __( 'Overlay', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'overlay',
			array(
				'label'     => __( 'Overlay', 'powerpack' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'powerpack' ),
				'label_off' => __( 'Hide', 'powerpack' ),

			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'overlay_background',
				'types'     => array( 'classic', 'gradient' ),
				'selector'  => '{{WRAPPER}} .pp-image-scroll-overlay',
				'exclude'   => array(
					'image',
				),
				'condition' => array(
					'overlay' => 'yes',
				),
			)
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( empty( $settings['image']['url'] ) ) {
			return;
		}

		$link_url = $settings['link']['url'];

		if ( '' !== $settings['link']['url'] ) {
			$this->add_render_attribute( 'link', 'class', 'pp-image-scroll-link pp-media-content' );

			$this->add_link_attributes( 'link', $settings['link'] );
		}

		$this->add_render_attribute( 'icon', 'class', [
			'pp-image-scroll-icon',
			'pp-icon',
			'pp-mouse-scroll-' . $settings['direction_type'],
		] );

		if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['icon'] = 'fa fa-star';
		}

		$has_icon = ! empty( $settings['icon'] );

		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		$icon_attributes = $this->get_render_attribute_string( 'icon' );

		if ( ! $has_icon && ! empty( $settings['selected_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
		$is_new = ! isset( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

		$this->add_render_attribute( [
			'container' => [
				'class' => 'pp-image-scroll-container',
			],
			'direction_type' => [
				'class' => [ 'pp-image-scroll-image', 'pp-image-scroll-' . $settings['direction_type'] ],
			],
		] );
		?>
		<div class="pp-image-scroll-wrap">
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'container' ) ); ?>>
				<?php if ( ! empty( $settings['icon'] ) || ( ! empty( $settings['selected_icon']['value'] ) && $is_new ) ) { ?>
					<div class="pp-image-scroll-content">
						<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>>
							<?php
							if ( $is_new || $migrated ) {
								Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
							} elseif ( ! empty( $settings['icon'] ) ) {
								?><i <?php echo wp_kses_post( $this->get_render_attribute_string( 'i' ) ); ?>></i><?php
							}
							?>
						</span>
					</div>
				<?php } ?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'direction_type' ) ); ?>>
					<?php if ( 'yes' === $settings['overlay'] ) { ?>
						<div class="pp-image-scroll-overlay pp-media-overlay">
					<?php } ?>
					<?php if ( ! empty( $link_url ) ) { ?>
							<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'link' ) ); ?>></a>
					<?php } ?>
					<?php if ( 'yes' === $settings['overlay'] ) { ?>
						</div> 
					<?php } ?>

					<?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings ) ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<#
			var direction = settings.direction_type,
				reverse = settings.reverse,
				url,
				iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
				migrated = elementor.helpers.isIconMigrated( settings, 'selected_icon' );

			if ( settings.icon || settings.selected_icon.value ) {

				view.addRenderAttribute( 'icon', 'class', [
					'pp-image-scroll-icon',
					'pp-icon',
					'pp-mouse-scroll-' + settings.direction_type,
				] );
			}

			if ( settings.link.url ) {
				view.addRenderAttribute( 'link', 'class', 'pp-image-scroll-link' );
				url = settings.link.url;
				view.addRenderAttribute( 'link', 'href',  url );
			}

			view.addRenderAttribute( 'container', 'class', 'pp-image-scroll-container' );

			view.addRenderAttribute( 'direction_type', 'class', 'pp-image-scroll-image pp-image-scroll-' + direction );
		#>

		<div class="pp-image-scroll-wrap">
			<div {{{ view.getRenderAttributeString('container') }}}>
				<# if ( settings.icon || settings.selected_icon ) { #>
					<div class="pp-image-scroll-content">   
						<span {{{ view.getRenderAttributeString('icon') }}}>
							<# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
							{{{ iconHTML.value }}}
							<# } else { #>
								<i class="{{ settings.icon }}" aria-hidden="true"></i>
							<# } #>
						</span>
					</div>
				<# } #>
				<div {{{ view.getRenderAttributeString('direction_type') }}}>
					<# if( 'yes' == settings.overlay ) { #>
						<div class="pp-image-scroll-overlay">
					<# }
					if ( settings.link.url ) { #>
						<a {{{ view.getRenderAttributeString('link') }}}></a>
					<# }
					if( 'yes' == settings.overlay ) { #>
						</div> 
					<# } #>

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
				</div>
			</div>
		</div>
		<?php
	}

}
