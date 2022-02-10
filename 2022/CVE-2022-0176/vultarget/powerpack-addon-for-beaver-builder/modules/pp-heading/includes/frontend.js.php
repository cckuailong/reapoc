/**
 * $module An instance of your module class.
 * $id The module's ID.
 * $settings The module's settings.
*/

(function($) {

    if($(window).width() <= 768 && $(window).width() >= 481 ) {
        $('.fl-node-<?php echo $id; ?> .pp-heading-separator, .fl-node-<?php echo $id; ?> .pp-heading').removeClass('pp-<?php echo $settings->heading_alignment; ?>');
        $('.fl-node-<?php echo $id; ?> .pp-heading-separator, .fl-node-<?php echo $id; ?> .pp-heading').addClass('pp-tablet-<?php echo $settings->heading_alignment_medium; ?>');
    }

    if( $(window).width() <= 480 ) {
        $('.fl-node-<?php echo $id; ?> .pp-heading-separator, .fl-node-<?php echo $id; ?> .pp-heading').removeClass('pp-<?php echo $settings->heading_alignment; ?>');
        $('.fl-node-<?php echo $id; ?> .pp-heading-separator, .fl-node-<?php echo $id; ?> .pp-heading').addClass('pp-mobile-<?php echo $settings->heading_alignment_responsive; ?>');
    }

})(jQuery);
