<?php
/**
 * PowerPack Elements Common Widget.
 *
 * @package PowerPack Elements
 */

namespace PowerpackElementsLite\Base;

use Elementor\Widget_Base;
use PowerpackElementsLite\Classes\PP_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Common Widget
 *
 * @since 0.0.1
 */
abstract class Powerpack_Widget extends Widget_Base {

	/**
	 * Widget base constructor.
	 *
	 * Initializing the widget base class.
	 *
	 * @since 1.2.9.4
	 * @access public
	 *
	 * @param array       $data Widget data. Default is an empty array.
	 * @param array|null  $args Optional. Widget default arguments. Default is null.
	 */
	public function __construct( $data = [], $args = null ) {

		parent::__construct( $data, $args );

		add_filter( 'upgrade_powerpack_title', [ $this, 'upgrade_powerpack_title' ], 10, 3 );
		add_filter( 'upgrade_powerpack_message', [ $this, 'upgrade_powerpack_message' ], 10, 3 );
	}

	public function upgrade_powerpack_title() {
		$upgrade_title = __( 'Get PowerPack Pro', 'powerpack' );

		return $upgrade_title;
	}

	public function upgrade_powerpack_message() {
		$upgrade_message = sprintf( __( 'Upgrade to %1$s Pro Version %2$s for 70+ widgets, exciting extensions and advanced features.', 'powerpack' ), '<a href="https://powerpackelements.com/upgrade/?utm_medium=pp-elements-lite&utm_source=pp-widget-upgrade-section&utm_campaign=pp-pro-upgrade" target="_blank" rel="noopener">', '</a>' );

		return $upgrade_message;
	}

	/**
	 * Get categories
	 *
	 * @since 1.0.0
	 */
	public function get_categories() {
		return [ 'powerpack-elements' ];
	}

	/**
	 * Get widget name
	 *
	 * @param string $slug Module class.
	 * @since 2.1.0
	 */
	public function get_widget_name( $slug = '' ) {
		return PP_Helper::get_widget_name( $slug );
	}

	/**
	 * Get widget title
	 *
	 * @param string $slug Module class.
	 * @since 2.1.0
	 */
	public function get_widget_title( $slug = '' ) {
		return PP_Helper::get_widget_title( $slug );
	}

	/**
	 * Get widget title
	 *
	 * @param string $slug Module class.
	 * @since 2.1.0
	 */
	public function get_widget_categories( $slug = '' ) {
		return PP_Helper::get_widget_categories( $slug );
	}

	/**
	 * Get widget title
	 *
	 * @param string $slug Module class.
	 * @since 2.1.0
	 */
	public function get_widget_icon( $slug = '' ) {
		return PP_Helper::get_widget_icon( $slug );
	}

	/**
	 * Get widget title
	 *
	 * @param string $slug Module class.
	 * @since 2.1.0
	 */
	public function get_widget_keywords( $slug = '' ) {
		return PP_Helper::get_widget_keywords( $slug );
	}

	/**
	 * Add a placeholder for the widget in the elementor editor
	 *
	 * @access public
	 * @since 1.3.0
	 *
	 * @return void
	 */
	public function render_editor_placeholder( $args = array() ) {

		if ( ! \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			return;
		}

		$defaults = [
			'title' => $this->get_title() . ' - ' . __( 'ID', 'powerpack' ) . ' ' . $this->get_id(),
			'body'  => __( 'This is a placeholder for this widget and is visible only in the editor.', 'powerpack' ),
		];

		$args = wp_parse_args( $args, $defaults );

		$this->add_render_attribute([
			'placeholder' => [
				'class' => 'pp-editor-placeholder',
			],
			'placeholder-title' => [
				'class' => 'pp-editor-placeholder-title',
			],
			'placeholder-content' => [
				'class' => 'pp-editor-placeholder-content',
			],
		]);

		?><div <?php echo wp_kses_post( $this->get_render_attribute_string( 'placeholder' ) ); ?>>
			<h4 <?php echo wp_kses_post( $this->get_render_attribute_string( 'placeholder-title' ) ); ?>>
				<?php echo esc_html( $args['title'] ); ?>
			</h4>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'placeholder-content' ) ); ?>>
				<?php echo esc_html( $args['body'] ); ?>
			</div>
		</div><?php
	}

	/**
	 * Get swiper slider settings
	 *
	 * @access public
	 * @since 2.3.2
	 */
	public function get_swiper_slider_settings( $settings, $new = true ) {
		$pagination = ( $new ) ? $settings['pagination'] : $settings['dots'];

		$effect = ( isset( $settings['carousel_effect'] ) && ( $settings['carousel_effect'] ) ) ? $settings['carousel_effect'] : 'slide';

		$slider_options = [
			'direction'         => 'horizontal',
			'effect'            => $effect,
			'speed'             => ( '' !== $settings['slider_speed']['size'] ) ? $settings['slider_speed']['size'] : 400,
			'slidesPerView'     => ( '' !== $settings['items']['size'] ) ? absint( $settings['items']['size'] ) : 3,
			'spaceBetween'      => ( '' !== $settings['margin']['size'] ) ? absint( $settings['margin']['size'] ) : 10,
			'grabCursor'        => ( 'yes' === $settings['grab_cursor'] ),
			'autoHeight'        => true,
			'loop'              => ( 'yes' === $settings['infinite_loop'] ),
		];

		$autoplay_speed = 999999;

		if ( 'yes' === $settings['autoplay'] ) {
			if ( isset( $settings['autoplay_speed']['size'] ) ) {
				$autoplay_speed = $settings['autoplay_speed']['size'];
			} elseif ( $settings['autoplay_speed'] ) {
				$autoplay_speed = $settings['autoplay_speed'];
			}
		}

		$slider_options['autoplay'] = [
			'delay'                  => $autoplay_speed,
			'disableOnInteraction'   => ( 'yes' === $settings['pause_on_interaction'] ),
		];

		if ( 'yes' === $pagination ) {
			$slider_options['pagination'] = [
				'el'                 => '.swiper-pagination-' . esc_attr( $this->get_id() ),
				'type'               => $settings['pagination_type'],
				'clickable'          => true,
			];
		}

		if ( 'yes' === $settings['arrows'] ) {
			$slider_options['navigation'] = [
				'nextEl'             => '.swiper-button-next-' . esc_attr( $this->get_id() ),
				'prevEl'             => '.swiper-button-prev-' . esc_attr( $this->get_id() ),
			];
		}

		$elementor_bp_lg = get_option( 'elementor_viewport_lg' );
		$elementor_bp_md = get_option( 'elementor_viewport_md' );
		$bp_desktop      = ! empty( $elementor_bp_lg ) ? $elementor_bp_lg : 1025;
		$bp_tablet       = ! empty( $elementor_bp_md ) ? $elementor_bp_md : 768;
		$bp_mobile       = 320;

		$slider_options['breakpoints'] = [
			$bp_desktop   => [
				'slidesPerView'      => ( '' !== $settings['items']['size'] ) ? absint( $settings['items']['size'] ) : 3,
				'spaceBetween'       => ( '' !== $settings['margin']['size'] ) ? absint( $settings['margin']['size'] ) : 10,
			],
			$bp_tablet   => [
				'slidesPerView'      => ( '' !== $settings['items_tablet']['size'] ) ? absint( $settings['items_tablet']['size'] ) : 2,
				'spaceBetween'       => ( '' !== $settings['margin_tablet']['size'] ) ? absint( $settings['margin_tablet']['size'] ) : 10,
			],
			$bp_mobile   => [
				'slidesPerView'      => ( '' !== $settings['items_mobile']['size'] ) ? absint( $settings['items_mobile']['size'] ) : 1,
				'spaceBetween'       => ( '' !== $settings['margin_mobile']['size'] ) ? absint( $settings['margin_mobile']['size'] ) : 10,
			],
		];

		return $slider_options;
	}



	/**
	 * Get swiper slider settings for content_template function
	 *
	 * @access public
	 * @since 2.3.2
	 */
	public function get_swiper_slider_settings_js() {
		$elementor_bp_tablet    = get_option( 'elementor_viewport_lg' );
		$elementor_bp_mobile    = get_option( 'elementor_viewport_md' );
		$elementor_bp_lg        = get_option( 'elementor_viewport_lg' );
		$elementor_bp_md        = get_option( 'elementor_viewport_md' );
		$bp_desktop             = ! empty( $elementor_bp_lg ) ? $elementor_bp_lg : 1025;
		$bp_tablet              = ! empty( $elementor_bp_md ) ? $elementor_bp_md : 768;
		$bp_mobile              = 320;
		?>
		<#
			function get_slider_settings( settings ) {
		   
				if (typeof settings.effect !== 'undefined') {
					var $effect = settings.effect;
				} else {
					var $effect = 'slide';
				}

				var $items          = ( settings.items.size !== '' || settings.items.size !== undefined ) ? settings.items.size : 3,
					$items_tablet   = ( settings.items_tablet.size !== '' || settings.items_tablet.size !== undefined ) ? settings.items_tablet.size : 2,
					$items_mobile   = ( settings.items_mobile.size !== '' || settings.items_mobile.size !== undefined ) ? settings.items_mobile.size : 1,
					$margin         = ( settings.margin.size !== '' || settings.margin.size !== undefined ) ? settings.margin.size : 10,
					$margin_tablet  = ( settings.margin_tablet.size !== '' || settings.margin_tablet.size !== undefined ) ? settings.margin_tablet.size : 10,
					$margin_mobile  = ( settings.margin_mobile.size !== '' || settings.margin_mobile.size !== undefined ) ? settings.margin_mobile.size : 10,
					$autoplay       = ( settings.autoplay == 'yes' && settings.autoplay_speed.size != '' ) ? settings.autoplay_speed.size : 999999;

				return {
					direction:              "horizontal",
					speed:                  ( settings.slider_speed.size !== '' || settings.slider_speed.size !== undefined ) ? settings.slider_speed.size : 400,
					effect:                 $effect,
					slidesPerView:          $items,
					spaceBetween:           $margin,
					grabCursor:             ( settings.grab_cursor === 'yes' ) ? true : false,
					autoHeight:             true,
					loop:                   ( settings.infinite_loop === 'yes' ),
					autoplay: {
						delay: $autoplay,
						disableOnInteraction: ( settings.disableOnInteraction === 'yes' ),
					},
					pagination: {
						el: '.swiper-pagination',
						type: settings.pagination_type,
						clickable: true,
					},
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev',
					},
					breakpoints: {
						<?php echo esc_attr( $bp_desktop ); ?>: {
							slidesPerView:  $items,
							spaceBetween:   $margin
						},
						<?php echo esc_attr( $bp_tablet ); ?>: {
							slidesPerView:  $items_tablet,
							spaceBetween:   $margin_tablet
						},
						<?php echo esc_attr( $bp_mobile ); ?>: {
							slidesPerView:  $items_mobile,
							spaceBetween:   $margin_mobile
						}
					}
				};
			};
		#>
		<?php
	}
}
