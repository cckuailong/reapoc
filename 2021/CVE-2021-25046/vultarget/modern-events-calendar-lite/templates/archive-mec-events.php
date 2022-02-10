<?php
/** no direct access **/
defined('MECEXEC') or die();

$main = MEC::getInstance('app.libraries.main');
$settings = $main->get_settings();

$title_tag = (isset($settings['archive_title_tag']) and trim($settings['archive_title_tag'])) ? $settings['archive_title_tag'] : 'h1';

/**
 * The Template for displaying events archives
 * 
 * @author Webnus <info@webnus.biz>
 * @package MEC/Templates
 * @version 1.0.0
 */
get_header('mec'); ?>
	
    <section id="<?php echo apply_filters('mec_archive_page_html_id', 'main-content'); ?>" class="<?php echo apply_filters('mec_archive_page_html_class', 'mec-container'); ?>">
        <?php do_action('mec_before_main_content'); ?>

        <?php if(have_posts()): ?>

            <?php do_action('mec_before_events_loop'); ?>

                <?php while(have_posts()): the_post(); $title = apply_filters('mec_archive_title', get_the_title()); ?>

                    <?php if(trim($title)): ?><<?php echo $title_tag; ?>><?php echo $title; ?></<?php echo $title_tag; ?>><?php endif; ?>

                    <?php the_content(); ?>

                <?php break; /** Only one post should be shown **/ endwhile; // end of the loop. ?>

            <?php do_action('mec_after_events_loop'); ?>

        <?php else: ?>

        <p><?php esc_html_e('No event found!', 'modern-events-calendar-lite'); ?></p>

        <?php endif; ?>
    </section>

    <?php do_action('mec_after_main_content'); ?>

<?php get_footer('mec');