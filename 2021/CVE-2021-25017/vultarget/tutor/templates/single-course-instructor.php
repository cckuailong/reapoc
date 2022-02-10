<?php
/**
 * Template for displaying single course for instructor
 *
 * @since v.1.6.7
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.6.7
 */

get_header();

do_action('tutor_course/single/instructor/before/wrap');
?>

<div <?php tutor_post_class('tutor-full-width-course-top tutor-course-top-info tutor-page-wrap'); ?>>
    <div class="tutor-container">
        <div class="tutor-row">
            <div class="tutor-col-8 tutor-col-md-100">
                <?php do_action('tutor_course/single/instructor/before/inner-wrap'); ?>
                <?php tutor_course_lead_info(); ?>
                <?php tutor_course_content(); ?>
                <?php tutor_course_benefits_html(); ?>
                <?php tutor_course_enrolled_nav(); ?>
                <?php tutor_course_topics(); ?>
                <?php tutor_course_instructors_html(); ?>
                <?php tutor_course_target_reviews_html(); ?>
		        <?php do_action('tutor_course/single/instructor/after/inner-wrap'); ?>
            </div>
            <div class="tutor-col-4">
                <div class="tutor-single-course-sidebar">
                    <?php do_action('tutor_course/single/instructor/before/sidebar'); ?>
                    <?php tutor_course_enroll_box(); ?>
                    <?php tutor_course_requirements_html(); ?>
                    <?php tutor_course_tags_html(); ?>
                    <?php tutor_course_target_audience_html(); ?>
                    <?php do_action('tutor_course/single/instructor/after/sidebar'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php do_action('tutor_course/single/instructor/after/wrap'); ?>

<?php
get_footer();
