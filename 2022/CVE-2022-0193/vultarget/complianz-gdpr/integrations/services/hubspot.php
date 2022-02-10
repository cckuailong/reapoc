<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_hs_tracking_script' );
function cmplz_hs_tracking_script( $tags ) {
	$tags[] = 'track.hubspot.com';
	$tags[] = 'js.hs-analytics.net';

	return $tags;
}

/**
 * Sync the Complianz banner with the hubspot banner by clicking on the apropriate buttonn when consent is given.
 */
function cmplz_hubspot_clicker() {
	if (cmplz_get_value('block_hubspot_service') === 'yes') {
		?>
		<script>
			jQuery(document).ready(function ($) {
				$(document).on("cmplzEnableScripts", cmplzHubspotScriptHandler);
				function cmplzHubspotScriptHandler(consentData) {
					var hubspotAcceptBtn = document.getElementById("hs-eu-confirmation-button");
					var hubspotDeclinetBtn = document.getElementById("hs-eu-decline-button");

					$('#hs-eu-confirmation-button').remove();
					if (consentData.detail === 'marketing') {
						if ( hubspotAcceptBtn != null ) {
							hubspotAcceptBtn.click();
						}
					} else {
						if ( hubspotDeclinetBtn != null ) {
							hubspotDeclinetBtn.click();
						}
					}
				}
			})
		</script>
		<?php
	}
}
add_action( 'wp_footer', 'cmplz_hubspot_clicker' );

/**
 * Add custom hubspot css
 */
function cmplz_hubspot_css() {
	if (cmplz_get_value('block_hubspot_service') === 'yes'){ ?>
		<style>div#hs-eu-cookie-confirmation {display: none;}</style>
	<?php
	}
}
add_action( 'wp_footer', 'cmplz_hubspot_css' );
