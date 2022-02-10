<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * The Template for displaying events archives
 * 
 * @author Webnus <info@webnus.biz>
 * @package MEC/Templates
 * @version 1.0.0
 */
logitrans_page_header_menu_choice_noplugin();
logitrans_blog_title_final(); ?>
    

        
        <section id="<?php echo apply_filters('mec_archive_page_html_id', 'main-content'); ?>" class="<?php echo apply_filters('mec_archive_page_html_class', 'container'); ?>">
		
		<?php do_action('mec_before_main_content'); ?>		
		
		<?php if(have_posts()): ?>

			<?php do_action('mec_before_events_loop'); ?>

				<?php while(have_posts()): the_post(); $title = apply_filters('mec_archive_title', get_the_title()); ?>
                    
                    <?php if(trim($title)): ?><h1><?php echo $title; ?></h1><?php endif; ?>
                    
					<?php the_content(); ?>

				<?php break; /** Only one post should be shown **/ endwhile; // end of the loop. ?>

			<?php do_action('mec_after_events_loop'); ?>

		<?php endif; ?>
        </section>

    <?php do_action('mec_after_main_content'); ?>

<?php get_footer('mec');