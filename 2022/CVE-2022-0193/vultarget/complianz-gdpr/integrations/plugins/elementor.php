<?php
defined( 'ABSPATH' ) or die();

/**
 * Add a notice about the legal hub
 * @param array $warnings
 *
 * @return array
 */
function cmplz_elementor_legal_hub($warnings){
	$warnings['elementor_hub']  = array(
			'conditions' => array('_true_'),
			'plus_one' => false,
			'include_in_progress' => false,
			'open' => sprintf(__( 'Download the Legal Hub for Elementor or design it yourself!', 'complianz-gdpr' ).cmplz_read_more("https://complianz.io/creating-the-legal-hub/") ),
		);
	return $warnings;
}
add_filter( 'cmplz_warning_types', 'cmplz_elementor_legal_hub' );


function cmplz_elementor_initDomContentLoaded() {
	if ( cmplz_uses_thirdparty('youtube') ) {
		if(!wp_script_is('jquery', 'done')) {
			wp_enqueue_script('jquery');
		}
		ob_start();
		?>
		<script>
			jQuery(document).ready(function ($) {
				$(document).on("cmplzRunAfterAllScripts", cmplz_elementor_fire_initOnReadyComponents);
				function cmplz_elementor_fire_initOnReadyComponents() {
					var blockedContentContainers = [];
					$('[data-cmplz-elementor-settings]').each(function (i, obj) {
						if ( $(this).hasClass('cmplz-activated') ) return;
						$(this).addClass('cmplz-activated' );
						$(this).data('settings', $(this).data('cmplz-elementor-settings'));

						var blockedContentContainer = $(this);
						blockedContentContainer.animate({"background-image": "url('')"}, 400, function () {
							//remove the added classes
							var cssIndex = blockedContentContainer.data('placeholderClassIndex');
							blockedContentContainer.removeClass('cmplz-blocked-content-container');
							blockedContentContainer.removeClass('cmplz-placeholder-' + cssIndex);
						});
						blockedContentContainers.push(blockedContentContainer);
					});

					for (var key in blockedContentContainers) {
						console.log(blockedContentContainers[key]);
						if (blockedContentContainers.hasOwnProperty(key) && blockedContentContainers[key] !== undefined ) {
							elementorFrontend.elementsHandler.runReadyTrigger( blockedContentContainers[key] );
						}
					}
				}
			})
		</script>
		<?php
		$script = ob_get_clean();
		$script = str_replace(array('<script>', '</script>'), '', $script);
		wp_add_inline_script( 'jquery', $script);
	}
}
add_action( 'wp_enqueue_scripts', 'cmplz_elementor_initDomContentLoaded' );

/**
 * Filter cookie blocker output
 */
function cmplz_elementor_cookieblocker( $output ){
	if ( cmplz_uses_thirdparty('youtube') ) {
		$iframe_pattern = '/elementor-widget-video.*?data-settings=.*?youtube_url.*?&quot;:&quot;(.*?)&quot;/is';
		if ( preg_match_all( $iframe_pattern, $output, $matches, PREG_PATTERN_ORDER ) ) {
			foreach ( $matches[0] as $key => $total_match ) {
				$placeholder = '';
				if ( cmplz_use_placeholder('youtube') && isset($matches[1][$key]) ) {
					$youtube_url = $matches[1][0];
					$placeholder = 'data-placeholder-image="'.cmplz_placeholder( false, $youtube_url ).'" ';
				}

				$new_match = str_replace('data-settings', $placeholder.'data-cmplz-elementor-settings', $total_match);
				$new_match = str_replace('elementor-widget-video', 'elementor-widget-video cmplz-placeholder-element', $new_match);
				$output = str_replace($total_match, $new_match, $output);
			}

		}
	}

	return $output;
}
add_filter('cmplz_cookie_blocker_output', 'cmplz_elementor_cookieblocker');
