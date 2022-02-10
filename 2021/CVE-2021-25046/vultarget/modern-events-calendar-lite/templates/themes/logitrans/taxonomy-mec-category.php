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
logitrans_page_header_menu_choice_noplugin();
logitrans_blog_title_final(); ?>
            
		<section id="<?php echo apply_filters('mec_category_page_html_id', 'main-content'); ?>" class="<?php echo apply_filters('mec_category_page_html_class', 'container'); ?>">
		
		<?php do_action('mec_before_main_content'); ?>

			<?php do_action('mec_before_events_loop'); ?>

				<?php $MEC = MEC::instance(); echo $MEC->category(); ?>

			<?php do_action('mec_after_events_loop'); ?>

        </section>

    <?php do_action('mec_after_main_content'); ?>

<?php get_footer('mec');