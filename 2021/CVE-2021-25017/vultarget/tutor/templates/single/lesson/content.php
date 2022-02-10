<?php
/**
 * Display the content
 *
 * @since v.1.0.0
 * @author themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

$jsonData = array();
$jsonData['post_id'] = get_the_ID();
$jsonData['best_watch_time'] = 0;
$jsonData['autoload_next_course_content'] = (bool) get_tutor_option('autoload_next_course_content');

$best_watch_time = tutor_utils()->get_lesson_reading_info(get_the_ID(), 0, 'video_best_watched_time');
if ($best_watch_time > 0){
	$jsonData['best_watch_time'] = $best_watch_time;
}
?>

<?php do_action('tutor_lesson/single/before/content'); ?>

<div class="tutor-single-page-top-bar">
    <div class="tutor-topbar-item tutor-hide-sidebar-bar">
        <a href="javascript:;" class="tutor-lesson-sidebar-hide-bar"><i class="tutor-icon-angle-left"></i> </a>
        <?php $course_id = tutor_utils()->get_course_id_by('lesson', get_the_ID()); ?>
        <a href="<?php echo get_the_permalink($course_id); ?>" class="tutor-topbar-home-btn">
            <i class="tutor-icon-home"></i> <?php echo __('Go to Course Home', 'tutor') ; ?>
        </a>
    </div>
    <div class="tutor-topbar-item tutor-topbar-content-title-wrap">
        <?php
        tutor_utils()->get_lesson_type_icon(get_the_ID(), true, true);
        the_title(); ?>
    </div>

    <div class="tutor-topbar-item tutor-topbar-mark-to-done">
        <?php tutor_lesson_mark_complete_html(); ?>
    </div>

</div>


<div class="tutor-lesson-content-area">

    <input type="hidden" id="tutor_video_tracking_information" value="<?php echo esc_attr(json_encode($jsonData)); ?>">
	<?php tutor_lesson_video(); ?>
	<?php the_content(); ?>
	<?php get_tutor_posts_attachments(); ?>
	<?php tutor_next_previous_pagination(); ?>
</div>

<?php do_action('tutor_lesson/single/after/content'); ?>