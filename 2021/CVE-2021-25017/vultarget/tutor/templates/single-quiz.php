<?php
/**
 * Template for displaying single quiz
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

$course = tutor_utils()->get_course_by_quiz(get_the_ID());

$enable_spotlight_mode = tutor_utils()->get_option('enable_spotlight_mode');
?>

<?php do_action('tutor_quiz/single/before/wrap'); ?>


    <div class="tutor-single-lesson-wrap <?php echo $enable_spotlight_mode ? "tutor-spotlight-mode" : ""; ?>">

        <div class="tutor-lesson-sidebar">
		    <?php tutor_lessons_sidebar(); ?>
        </div>
        <div id="tutor-single-entry-content" class="tutor-quiz-single-entry-wrap tutor-single-entry-content">
            <input type="hidden" name="tutor_quiz_id" id="tutor_quiz_id" value="<?php the_ID(); ?>">
            <div class="tutor-single-page-top-bar">
                <div class="tutor-topbar-item tutor-hide-sidebar-bar">
                    <a href="javascript:;" class="tutor-lesson-sidebar-hide-bar"><i class="tutor-icon-angle-left"></i> </a>
                    <a href="<?php echo get_the_permalink($course->ID); ?>"  class="tutor-topbar-home-btn">
                        <i class="tutor-icon-home"></i> <?php echo __('Go to Course Home', 'tutor') ; ?>
                    </a>
                </div>
                <div class="tutor-topbar-item tutor-topbar-content-title-wrap">
                    <?php
                    tutor_utils()->get_lesson_type_icon(get_the_ID(), true, true);
                    the_title(); ?>
                </div>

                <div class="tutor-topbar-item tutor-topbar-mark-to-done" style="width: 150px;"></div>
            </div>
            <div class="tutor-quiz-single-wrap ">
                <input type="hidden" name="tutor_quiz_id" id="tutor_quiz_id" value="<?php the_ID(); ?>">

		        <?php
		        if ($course){
			        tutor_single_quiz_top();
			        tutor_single_quiz_content();
			        tutor_single_quiz_body();
		        }else{
			        tutor_single_quiz_no_course_belongs();
		        }
		        ?>
            </div>

        </div>
    </div><!-- .wrap -->

<?php
do_action('tutor_quiz/single/after/wrap');
get_tutor_footer();