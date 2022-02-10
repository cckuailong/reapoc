<?php
/**
 * Template for displaying single lesson
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

get_tutor_header();

global $post;
$currentPost = $post;

$enable_spotlight_mode = tutor_utils()->get_option('enable_spotlight_mode');
?>

<?php do_action('tutor_lesson/single/before/wrap'); ?>
    <div class="tutor-single-lesson-wrap <?php echo $enable_spotlight_mode ? "tutor-spotlight-mode" : ""; ?>">
        <div class="tutor-lesson-sidebar">
			<?php tutor_lessons_sidebar(); ?>
        </div>
        <div id="tutor-single-entry-content" class="tutor-lesson-content tutor-single-entry-content tutor-single-entry-content-<?php the_ID(); ?>">
		    <?php tutor_lesson_content(); ?>
        </div>
    </div>
<?php do_action('tutor_lesson/single/after/wrap');

get_tutor_footer();