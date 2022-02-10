<?php
/**
 * Custom Facebook Feed Item : Date Template
 * Displays the item date
 *
 * @version 2.19 Custom Facebook Feed by Smash Balloon
 *
 */

use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\CFF_Shortcode_Display;

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$cff_date_styles = $this_class->get_style_attribute( 'date' );
?>

<p class="cff-date" <?php echo $cff_date_styles ?>><?php
		echo CFF_Shortcode_Display::get_date( $options, $atts, $news );
		if($cff_date_position == 'below' || (!$cff_show_author && $cff_date_position == 'author') ):
	?>
	<span class="cff-date-dot">&nbsp;&middot;&nbsp;&nbsp;</span>
	<?php endif; ?>
</p>