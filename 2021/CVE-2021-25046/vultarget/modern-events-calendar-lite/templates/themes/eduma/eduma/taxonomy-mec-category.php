<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * The Template for displaying mec-category taxonomy events
 * 
 * @author Webnus <info@webnus.biz>
 * @package MEC/Templates
 * @version 1.0.0
 */
 ?>
        
        <style type="text/css">
			.theiaStickySidebar{display:none !important;}
        </style>
        <section id="<?php echo apply_filters('mec_category_page_html_id', 'main-content'); ?>" class="<?php echo apply_filters('mec_category_page_html_class', 'container'); ?>">

              <?php do_action('mec_before_events_loop'); ?>

                     <?php $MEC = MEC::instance(); echo $MEC->category(); ?>

              <?php do_action('mec_after_events_loop'); ?>

        </section>
