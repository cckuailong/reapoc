<?php
/**
 * Custom Facebook Feed : Credit
 * Show a credit message to Smashballoon
 *
 * @version 2.19 Custom Facebook Feed by Smash Balloon
 *
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
use CustomFacebookFeed\CFF_Utils;

$cff_show_credit = CFF_Utils::check_if_on( $atts['credit'] );

if($cff_show_credit) :
?>

<p class="cff-credit">
	<a href="https://smashballoon.com/custom-facebook-feed/" target="_blank" style="color: #<?php echo $cff_posttext_link_color ?>" title="<?php echo esc_attr('Smash Balloon Custom Facebook Feed WordPress Plugin') ?>">
		<img src="<?php echo CFF_PLUGIN_URL ?>/assets/img/smashballoon-tiny.png" alt="<?php echo esc_attr('Smash Balloon Custom Facebook Feed WordPress Plugin') ?>" />
		The Custom Facebook Feed plugin
	</a>
</p>
<?php
endif;