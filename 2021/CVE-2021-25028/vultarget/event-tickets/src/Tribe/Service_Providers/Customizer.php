<?php
/**
 * Handles the customizer CSS overrides from TEC
 *
 * @since   4.12.3
 * @package Tribe\Tickets\Service_Providers
 */

namespace Tribe\Tickets\Service_Providers;

use Tribe__Customizer;
use Tribe__Utils__Color;

/**
 * Class Customizer.
 *
 * @since 4.12.3
 */
class Customizer extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.12.3
	 */
	public function register() {
		if ( ! class_exists( 'Tribe__Events__Main' ) ) {
			return;
		}

		add_filter( 'tribe_customizer_css_template', [ $this, 'filter_accent_color_css' ], 100, 1 );
	}

	/**
	 * Handle accent color customizations for Event Tickets.
	 *
	 * @since 4.12.3
	 *
	 * @param string $template The original CSS template.
	 *
	 * @return string $template The resulting CSS template.
	 */
	public function filter_accent_color_css( $template ) {
		$customizer              = Tribe__Customizer::instance();
		$global_elements_section = tribe( 'tec.customizer.global-elements' );
		$settings                = $customizer->get_option( [ $global_elements_section->ID ] );

		if ( $customizer->has_option( $global_elements_section->ID, 'accent_color' ) ) {
			$accent_color     = new Tribe__Utils__Color( $settings['accent_color'] );
			$accent_color_rgb = $accent_color::hexToRgb( $settings['accent_color'] );
			$accent_css_rgb   = $accent_color_rgb['R'] . ',' . $accent_color_rgb['G'] . ',' . $accent_color_rgb['B'];

			$accent_color_hover      = 'rgba(' . $accent_css_rgb . ',0.8)';
			$accent_color_active     = 'rgba(' . $accent_css_rgb . ',0.9)';
			$accent_color_background = 'rgba(' . $accent_css_rgb . ',0.07)';

			$template .= '
				.tribe-common.event-tickets .tribe-tickets__rsvp-actions-success-going-check-icon,
				.tribe-common.event-tickets .tribe-common-c-btn:disabled {
					background-color: <%= global_elements.accent_color %>;
				}
			';

			$template .= '
				.tribe-common.event-tickets .tribe-tickets__rsvp-message {
					background-color: ' . $accent_color_background . ';
				}
			';

			$template .= '
				.tribe-common.event-tickets .tribe-tickets__rsvp-message-link {
					color: <%= global_elements.accent_color %>;
				}
			';

			/**
			 * Overrides from TEC when V2 is not active.
			 */

			// overrides for common base/full/typography/_ctas.pcss.
			$template .= '
				.tribe-common.event-tickets .tribe-common-cta--alt,
				.tribe-common.event-tickets .tribe-common-cta--alt:active,
				.tribe-common.event-tickets .tribe-common-cta--alt:hover,
				.tribe-common.event-tickets .tribe-common-cta--alt:focus,
				.tribe-common.event-tickets .tribe-common-cta--thin-alt,
				.tribe-common.event-tickets .tribe-common-cta--thin-alt:active,
				.tribe-common.event-tickets .tribe-common-cta--thin-alt:focus,
				.tribe-common.event-tickets .tribe-common-cta--thin-alt:hover {
					border-bottom-color: <%= global_elements.accent_color %>;
				}
			';

			$template .= '
				.tribe-common.event-tickets .tribe-common-cta--alt:active,
				.tribe-common.event-tickets .tribe-common-cta--alt:hover,
				.tribe-common.event-tickets .tribe-common-cta--alt:focus,
				.tribe-common.event-tickets .tribe-common-cta--thin-alt:active,
				.tribe-common.event-tickets .tribe-common-cta--thin-alt:hover,
				.tribe-common.event-tickets .tribe-common-cta--thin-alt:focus,
				.tribe-theme-twentyseventeen .tribe-common.event-tickets .tribe-common-cta--alt:hover,
				.tribe-theme-twentyseventeen .tribe-common.event-tickets .tribe-common-cta--alt:focus,
				.tribe-theme-twentyseventeen .tribe-common.event-tickets .tribe-common-cta--thin-alt:hover,
				.tribe-theme-twentyseventeen .tribe-common.event-tickets .tribe-common-cta--thin-alt:focus {
					color: <%= global_elements.accent_color %>;
				}
			';

			// overrides for common components/full/buttons/_solid.pcss.
			$template .= '
				.tribe-common.event-tickets .tribe-common-c-btn,
				.tribe-common.event-tickets a.tribe-common-c-btn {
					background-color: <%= global_elements.accent_color %>;
				}
			';

			$template .= '
				.tribe-common.event-tickets .tribe-common-c-btn:focus,
				.tribe-common.event-tickets .tribe-common-c-btn:hover,
				.tribe-common.event-tickets a.tribe-common-c-btn:focus,
				.tribe-common.event-tickets a.tribe-common-c-btn:hover {
					background-color: ' . $accent_color_hover . ';
				}
			';

			$template .= '
				.tribe-common.event-tickets .tribe-common-c-btn:active,
				.tribe-common.event-tickets a.tribe-common-c-btn:active {
					background-color: ' . $accent_color_active . ';
				}
			';

			$template .= '
				.tribe-common.event-tickets .tribe-common-c-btn:disabled,
				.tribe-common.event-tickets a.tribe-common-c-btn:disabled {
					background-color: <%= global_elements.accent_color %>;
				}
			';

			$template .= '
				.tribe-theme-twentytwenty .tribe-common.event-tickets .tribe-common-c-btn {
					background-color: <%= global_elements.accent_color %>;
				}
			';

			$template .= '
				.tribe-theme-twentyseventeen .tribe-common.event-tickets .tribe-common-c-btn:hover,
				.tribe-theme-twentyseventeen .tribe-common.event-tickets .tribe-common-c-btn:focus,
				.tribe-theme-twentytwenty .tribe-common.event-tickets .tribe-common-c-btn:hover,
				.tribe-theme-twentytwenty .tribe-common.event-tickets .tribe-common-c-btn:focus {
					background-color: ' . $accent_color_hover . ';
				}
			';

			// overrides for common components/full/_loader.pcss.
			$template .= '
				@keyframes tribe-common-c-loader-bounce {
					0% {}
					50% { background-color: <%= global_elements.accent_color %>; }
					100% {}
				}
			';
		}

		return $template;
	}
}
